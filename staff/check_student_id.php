<?php
session_start();
header('Content-Type: application/json');

try {
    $db = new PDO('mysql:host=localhost;dbname=clinic_management_system;charset=utf8', 'root', '');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['student_id'])) {
        $student_id = trim($_POST['student_id']);
        if (empty($student_id)) {
            echo json_encode(['exists' => false, 'valid_format' => false, 'message' => '']);
            exit;
        }
        // Accept SCC-00-0000000 or SCC-00-00000000
        $format_pattern = '/^SCC-\d{2}-\d{7,8}$/';
        $valid_format = preg_match($format_pattern, $student_id);
        if (!$valid_format) {
            echo json_encode(['exists' => false, 'valid_format' => false, 'message' => 'Invalid format. Use format: SCC-00-0000000 or SCC-00-00000000']);
            exit;
        }
        
        $stmt = $db->prepare('SELECT COUNT(*) FROM imported_patients WHERE student_id = ?');
        $stmt->execute([$student_id]);
        $count = $stmt->fetchColumn();
        
        if ($count > 0) {
            echo json_encode(['exists' => true, 'valid_format' => true, 'message' => 'This Student ID already exists. Please use a different ID.']);
        } else {
            echo json_encode(['exists' => false, 'valid_format' => true, 'message' => 'Student ID is available.']);
        }
    } else {
        echo json_encode(['exists' => false, 'valid_format' => false, 'message' => 'Invalid request']);
    }
} catch (PDOException $e) {
    echo json_encode(['exists' => false, 'valid_format' => false, 'message' => 'Database error: ' . $e->getMessage()]);
} catch (Exception $e) {
    echo json_encode(['exists' => false, 'valid_format' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>
