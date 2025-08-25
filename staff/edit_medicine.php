<?php
header('Content-Type: application/json');
try {
    $db = new PDO('mysql:host=localhost;dbname=clinic_management_system;charset=utf8', 'root', '');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = $_POST['id'] ?? '';
        $name = $_POST['name'] ?? '';
        $dosage = $_POST['dosage'] ?? '';
        $quantity = $_POST['quantity'] ?? '';
        $expiry = $_POST['expiry'] ?? '';
        if ($id && $name && $dosage && $quantity && $expiry) {
            $stmt = $db->prepare('UPDATE medicines SET name=?, dosage=?, quantity=?, expiry=? WHERE id=?');
            $stmt->execute([$name, $dosage, $quantity, $expiry, $id]);
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
                'Edited medicine: ' . $name
            ]);
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'All fields required.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid request.']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
