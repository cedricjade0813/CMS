<?php
header('Content-Type: application/json');
try {
    $db = new PDO('mysql:host=localhost;dbname=clinic_management_system;charset=utf8', 'root', '');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'DB connect failed']);
    exit;
}

$full_name = isset($_POST['full_name']) ? trim($_POST['full_name']) : '';
$age = isset($_POST['age']) ? (int)$_POST['age'] : 0;
$gender = isset($_POST['gender']) ? trim($_POST['gender']) : '';
$address = isset($_POST['address']) ? trim($_POST['address']) : '';
$contact = isset($_POST['contact']) ? trim($_POST['contact']) : '';
$emergency_contact = isset($_POST['emergency_contact']) ? trim($_POST['emergency_contact']) : '';

if ($full_name === '' || $age <= 0 || $gender === '' || $contact === '') {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

try {
    $db->prepare('CREATE TABLE IF NOT EXISTS visitor (
        id INT AUTO_INCREMENT PRIMARY KEY,
        visitor_id INT NOT NULL UNIQUE,
        full_name VARCHAR(255) NOT NULL,
        age INT NOT NULL,
        gender VARCHAR(16) NOT NULL,
        address TEXT,
        contact VARCHAR(64) NOT NULL,
        emergency_contact VARCHAR(64),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )')->execute();

    $stmt = $db->prepare('INSERT INTO visitor (full_name, age, gender, address, contact, emergency_contact) VALUES (?, ?, ?, ?, ?, ?)');
    $stmt->execute([$full_name, $age, $gender, $address, $contact, $emergency_contact]);
    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Save failed']);
}
?>


