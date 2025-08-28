<?php
session_start();
header('Content-Type: application/json');

try {
    $db = new PDO('mysql:host=localhost;dbname=clinic_management_system;charset=utf8', 'root', '');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_patient'])) {
        // Check Student ID format
        $student_id = trim($_POST['student_id']);
        $format_pattern = '/^SCC-\d{2}-\d{7}$/';
        if (!preg_match($format_pattern, $student_id)) {
            echo json_encode(['success' => false, 'message' => 'Invalid Student ID format. Use format: SCC-00-0000000']);
            exit;
        }
        
        // Double-check if student ID already exists
        $checkStmt = $db->prepare('SELECT COUNT(*) FROM imported_patients WHERE student_id = ?');
        $checkStmt->execute([$student_id]);
        $exists = $checkStmt->fetchColumn() > 0;
        
        if ($exists) {
            echo json_encode(['success' => false, 'message' => 'Student ID already exists. Please use a different ID.']);
            exit;
        }
        
        $stmt = $db->prepare('INSERT INTO imported_patients (
            student_id, name, dob, gender, address, email, parent_email, parent_phone, 
            contact_number, religion, citizenship, course_program, civil_status, 
            password, year_level, guardian_name, guardian_contact, 
            emergency_contact_name, emergency_contact_number
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
        
        $stmt->execute([
            $_POST['student_id'],
            $_POST['name'],
            $_POST['dob'],
            $_POST['gender'],
            $_POST['address'] ?? '',
            $_POST['email'] ?? '',
            $_POST['parent_email'] ?? '',
            $_POST['parent_phone'] ?? '',
            $_POST['contact_number'] ?? '',
            $_POST['religion'] ?? '',
            $_POST['citizenship'] ?? '',
            $_POST['course_program'] ?? '',
            $_POST['civil_status'] ?? '',
            password_hash($_POST['password'], PASSWORD_DEFAULT),
            $_POST['year_level'] ?? '',
            $_POST['guardian_name'] ?? '',
            $_POST['guardian_contact'] ?? '',
            $_POST['emergency_contact_name'] ?? '',
            $_POST['emergency_contact_number'] ?? ''
        ]);
        
        echo json_encode(['success' => true, 'message' => 'Patient added successfully!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid request']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>
