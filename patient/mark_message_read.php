<?php
session_start();
header('Content-Type: application/json');

// Check if patient is logged in
if (!isset($_SESSION['student_row_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $message_id = isset($_POST['message_id']) ? (int)$_POST['message_id'] : 0;
    $patient_id = $_SESSION['student_row_id'];
    
    if ($message_id > 0) {
        try {
            $db = new PDO('mysql:host=localhost;dbname=clinic_management_system;charset=utf8', 'root', '');
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Mark message as read (only if it belongs to this patient)
            $stmt = $db->prepare('UPDATE messages SET is_read = TRUE WHERE id = ? AND recipient_id = ?');
            $result = $stmt->execute([$message_id, $patient_id]);
            
            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Message marked as read']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Message not found or access denied']);
            }
            
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid message ID']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>
