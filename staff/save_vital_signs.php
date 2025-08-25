<?php
session_start();
header('Content-Type: application/json');

try {
    $db = new PDO('mysql:host=localhost;dbname=clinic_management_system;charset=utf8', 'root', '');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }
    
    $patient_id = $_POST['patient_id'] ?? '';
    $patient_name = $_POST['patient_name'] ?? '';
    $vital_date = $_POST['vital_date'] ?? '';
    $weight = $_POST['weight'] ?? null;
    $height = $_POST['height'] ?? null;
    $body_temp = $_POST['body_temp'] ?? null;
    $resp_rate = $_POST['resp_rate'] ?? null;
    $pulse = $_POST['pulse'] ?? null;
    $blood_pressure = $_POST['blood_pressure'] ?? null;
    $oxygen_sat = $_POST['oxygen_sat'] ?? null;
    $remarks = $_POST['remarks'] ?? '';
    
    // Get recorded_by from session or set default
    $recorded_by = $_SESSION['username'] ?? 'Staff';
    
    // Validate required fields
    if (empty($patient_id) || empty($patient_name) || empty($vital_date)) {
        throw new Exception('Patient ID, name, and date are required');
    }
    
    // Check if a record already exists for this patient on this date
    $checkStmt = $db->prepare("SELECT id FROM vital_signs WHERE patient_id = ? AND vital_date = ?");
    $checkStmt->execute([$patient_id, $vital_date]);
    $existingRecord = $checkStmt->fetch();
    
    if ($existingRecord) {
        // Update existing record
        $stmt = $db->prepare("UPDATE vital_signs SET 
            patient_name = ?, 
            weight = ?, 
            height = ?, 
            body_temp = ?, 
            resp_rate = ?, 
            pulse = ?, 
            blood_pressure = ?,
            oxygen_sat = ?, 
            remarks = ?, 
            recorded_by = ?
            WHERE patient_id = ? AND vital_date = ?");
        
        $stmt->execute([
            $patient_name,
            $weight ?: null,
            $height ?: null,
            $body_temp ?: null,
            $resp_rate ?: null,
            $pulse ?: null,
            $blood_pressure ?: null,
            $oxygen_sat ?: null,
            $remarks,
            $recorded_by,
            $patient_id,
            $vital_date
        ]);
        
        $message = 'Vital signs updated successfully';
    } else {
        // Insert new record
        $stmt = $db->prepare("INSERT INTO vital_signs 
            (patient_id, patient_name, vital_date, weight, height, body_temp, resp_rate, pulse, blood_pressure, oxygen_sat, remarks, recorded_by) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
        $stmt->execute([
            $patient_id,
            $patient_name,
            $vital_date,
            $weight ?: null,
            $height ?: null,
            $body_temp ?: null,
            $resp_rate ?: null,
            $pulse ?: null,
            $blood_pressure ?: null,
            $oxygen_sat ?: null,
            $remarks,
            $recorded_by
        ]);
        
        $message = 'Vital signs saved successfully';
    }
    
    echo json_encode(['success' => true, 'message' => $message]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
