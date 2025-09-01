<?php
session_start();
header('Content-Type: application/json');

try {
    $db = new PDO('mysql:host=localhost;dbname=clinic_management_system;charset=utf8', 'root', '');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    $entity_type = $_POST['entity_type'] ?? '';

    // Common vitals
    $vital_date = $_POST['vital_date'] ?? '';
    $weight = $_POST['weight'] ?? null;
    $height = $_POST['height'] ?? null;
    $body_temp = $_POST['body_temp'] ?? null;
    $resp_rate = $_POST['resp_rate'] ?? null;
    $pulse = $_POST['pulse'] ?? null;
    $blood_pressure = $_POST['blood_pressure'] ?? null;
    $oxygen_sat = $_POST['oxygen_sat'] ?? null;
    $remarks = $_POST['remarks'] ?? '';

    // recorded_by from session or default
    $recorded_by = $_SESSION['username'] ?? 'Staff';

    if ($entity_type === 'visitor' || !empty($_POST['visitor_id'])) {
        // ...existing visitor logic...
        $visitor_id = $_POST['visitor_id'] ?? '';
        $visitor_name = $_POST['visitor_name'] ?? '';
        if (empty($visitor_id) || empty($visitor_name) || empty($vital_date)) {
            throw new Exception('Visitor ID, name, and date are required');
        }
        $checkStmt = $db->prepare("SELECT id FROM vital_signs WHERE visitor_id = ? AND vital_date = ?");
        $checkStmt->execute([$visitor_id, $vital_date]);
        $existingRecord = $checkStmt->fetch();
        if ($existingRecord) {
            $stmt = $db->prepare("UPDATE vital_signs SET visitor_name = ?, weight = ?, height = ?, body_temp = ?, resp_rate = ?, pulse = ?, blood_pressure = ?, oxygen_sat = ?, remarks = ?, recorded_by = ? WHERE visitor_id = ? AND vital_date = ?");
            $stmt->execute([
                $visitor_name,
                $weight ?: null,
                $height ?: null,
                $body_temp ?: null,
                $resp_rate ?: null,
                $pulse ?: null,
                $blood_pressure ?: null,
                $oxygen_sat ?: null,
                $remarks,
                $recorded_by,
                $visitor_id,
                $vital_date
            ]);
            $message = 'Visitor vital signs updated successfully';
        } else {
            $stmt = $db->prepare("INSERT INTO vital_signs (visitor_id, visitor_name, vital_date, weight, height, body_temp, resp_rate, pulse, blood_pressure, oxygen_sat, remarks, recorded_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $visitor_id,
                $visitor_name,
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
            $message = 'Visitor vital signs saved successfully';
        }
    } else if ($entity_type === 'faculty' || !empty($_POST['faculty_id'])) {
        // Faculty flow
        $faculty_id = $_POST['faculty_id'] ?? '';
        $faculty_name = $_POST['faculty_name'] ?? '';
        if (empty($faculty_id) || empty($faculty_name) || empty($vital_date)) {
            throw new Exception('Faculty ID, name, and date are required');
        }
        $checkStmt = $db->prepare("SELECT id FROM vital_signs WHERE faculty_id = ? AND vital_date = ?");
        $checkStmt->execute([$faculty_id, $vital_date]);
        $existingRecord = $checkStmt->fetch();
        if ($existingRecord) {
            $stmt = $db->prepare("UPDATE vital_signs SET faculty_name = ?, weight = ?, height = ?, body_temp = ?, resp_rate = ?, pulse = ?, blood_pressure = ?, oxygen_sat = ?, remarks = ?, recorded_by = ? WHERE faculty_id = ? AND vital_date = ?");
            $stmt->execute([
                $faculty_name,
                $weight ?: null,
                $height ?: null,
                $body_temp ?: null,
                $resp_rate ?: null,
                $pulse ?: null,
                $blood_pressure ?: null,
                $oxygen_sat ?: null,
                $remarks,
                $recorded_by,
                $faculty_id,
                $vital_date
            ]);
            $message = 'Faculty vital signs updated successfully';
        } else {
            $stmt = $db->prepare("INSERT INTO vital_signs (faculty_id, faculty_name, vital_date, weight, height, body_temp, resp_rate, pulse, blood_pressure, oxygen_sat, remarks, recorded_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $faculty_id,
                $faculty_name,
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
            $message = 'Faculty vital signs saved successfully';
        }
    } else {
        // Patient flow (existing behavior)
        $patient_id = $_POST['patient_id'] ?? '';
        $patient_name = $_POST['patient_name'] ?? '';
        if (empty($patient_id) || empty($patient_name) || empty($vital_date)) {
            throw new Exception('Patient ID, name, and date are required');
        }
        $checkStmt = $db->prepare("SELECT id FROM vital_signs WHERE patient_id = ? AND vital_date = ?");
        $checkStmt->execute([$patient_id, $vital_date]);
        $existingRecord = $checkStmt->fetch();
        if ($existingRecord) {
            $stmt = $db->prepare("UPDATE vital_signs SET patient_name = ?, weight = ?, height = ?, body_temp = ?, resp_rate = ?, pulse = ?, blood_pressure = ?, oxygen_sat = ?, remarks = ?, recorded_by = ? WHERE patient_id = ? AND vital_date = ?");
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
            $stmt = $db->prepare("INSERT INTO vital_signs (patient_id, patient_name, vital_date, weight, height, body_temp, resp_rate, pulse, blood_pressure, oxygen_sat, remarks, recorded_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
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
    }

    echo json_encode(['success' => true, 'message' => $message]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
