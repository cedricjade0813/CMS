<?php
header('Content-Type: application/json');
try {
    $db = new PDO('mysql:host=localhost;dbname=clinic_management_system;charset=utf8', 'root', '');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = $_POST['id'] ?? '';
        if ($id) {
            // Get medicine name for log
            $medName = '';
            $stmt = $db->prepare('SELECT name FROM medicines WHERE id=?');
            $stmt->execute([$id]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($row) $medName = $row['name'];
            $stmt = $db->prepare('DELETE FROM medicines WHERE id=?');
            $stmt->execute([$id]);
            // Log action
            session_start();
            $user_email = isset($_SESSION['user_email']) ? $_SESSION['user_email'] : 'Unknown';
            $logDb = new PDO('mysql:host=localhost;dbname=clinic_management_system;charset=utf8', 'root', '');
            $logDb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $logDb->prepare('CREATE TABLE IF NOT EXISTS logs (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT,
                user_email VARCHAR(255),
                action VARCHAR(255),
                timestamp DATETIME DEFAULT CURRENT_TIMESTAMP
            )')->execute();
            $logDb->prepare('INSERT INTO logs (user_email, action) VALUES (?, ?)')->execute([
                $user_email,
                'Deleted medicine: ' . $medName
            ]);
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'ID required.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid request.']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
