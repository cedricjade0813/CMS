<?php
// add_medicine.php
header('Content-Type: application/json');

try {
    $db = new PDO('mysql:host=localhost;dbname=clinic_management_system;charset=utf8', 'root', '');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database connection failed: ' . $e->getMessage()]);
    exit;
}

// Ensure medicines table has created_at column
$db->exec("ALTER TABLE medicines ADD COLUMN IF NOT EXISTS created_at DATETIME DEFAULT CURRENT_TIMESTAMP");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $dosage = $_POST['dosage'] ?? '';
    $quantity = $_POST['quantity'] ?? 0;
    $expiry = $_POST['expiry'] ?? '';
    if ($name && $dosage && $quantity && $expiry) {
        $stmt = $db->prepare('INSERT INTO medicines (name, dosage, quantity, expiry, created_at) VALUES (?, ?, ?, ?, NOW())');
        $stmt->execute([$name, $dosage, $quantity, $expiry]);
        // Log action
        session_start();
        // Use the same logic as in includes/header.php for staff: prefer $_SESSION['username'] (staff username), then $_SESSION['user_name'] (full name), then $_SESSION['user_email'], then 'Unknown'
        if (isset($_SESSION['username'])) {
            $user_name = $_SESSION['username'];
        } elseif (isset($_SESSION['user_name'])) {
            $user_name = $_SESSION['user_name'];
        } elseif (isset($_SESSION['user_email'])) {
            $user_name = $_SESSION['user_email'];
        } else {
            $user_name = 'Unknown';
        }
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
            $user_name,
            'Added medicine: ' . $name
        ]);
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'All fields required.']);
    }
    exit;
}
?>
