<?php
header('Content-Type: application/json');

try {
    $db = new PDO('mysql:host=localhost;dbname=clinic_management_system;charset=utf8', 'root', '');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }
    
    $patient_name = $_POST['patient_name'] ?? '';
    $patient_id = $_POST['patient_id'] ?? '';
    
    if (empty($patient_name) && empty($patient_id)) {
        throw new Exception('Patient name or ID is required');
    }
    
    $response = [
        'vital_signs' => [],
        'medication_referrals' => []
    ];
    
    // Fetch vital signs
    $vitalQuery = "SELECT * FROM vital_signs WHERE ";
    $params = [];
    
    if (!empty($patient_id)) {
        $vitalQuery .= "patient_id = ?";
        $params[] = $patient_id;
    } else {
        $vitalQuery .= "patient_name = ?";
        $params[] = $patient_name;
    }
    
    $vitalQuery .= " ORDER BY vital_date DESC, created_at DESC LIMIT 10";
    
    $vitalStmt = $db->prepare($vitalQuery);
    $vitalStmt->execute($params);
    $response['vital_signs'] = $vitalStmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Fetch medication referrals
    $referralQuery = "SELECT * FROM medication_referrals WHERE ";
    $referralParams = [];
    
    if (!empty($patient_id)) {
        $referralQuery .= "patient_id = ?";
        $referralParams[] = $patient_id;
    } else {
        $referralQuery .= "patient_name = ?";
        $referralParams[] = $patient_name;
    }
    
    $referralQuery .= " ORDER BY created_at DESC LIMIT 10";
    
    $referralStmt = $db->prepare($referralQuery);
    $referralStmt->execute($referralParams);
    $response['medication_referrals'] = $referralStmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($response);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>
