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
    $subjective = $_POST['subjective'] ?? '';
    $objective = $_POST['objective'] ?? '';
    $assessment = $_POST['assessment'] ?? '';
    $plan = $_POST['plan'] ?? '';
    $intervention = $_POST['intervention'] ?? '';
    $evaluation = $_POST['evaluation'] ?? '';
    
    // Get recorded_by from session or set default
    $recorded_by = $_SESSION['username'] ?? 'Staff';
    
    // Validate required fields
    if (empty($patient_id) || empty($patient_name)) {
        throw new Exception('Patient ID and name are required');
    }
    
    // Insert medication referral into database
    $stmt = $db->prepare("INSERT INTO medication_referrals 
        (patient_id, patient_name, subjective, objective, assessment, plan, intervention, evaluation, recorded_by) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    $stmt->execute([
        $patient_id,
        $patient_name,
        $subjective,
        $objective,
        $assessment,
        $plan,
        $intervention,
        $evaluation,
        $recorded_by
    ]);
    
    echo json_encode(['success' => true, 'message' => 'Medication referral saved successfully']);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
