<?php
header('Content-Type: application/json');

try {
    $db = new PDO('mysql:host=localhost;dbname=clinic_management_system;charset=utf8', 'root', '');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    $visitor_name = $_POST['visitor_name'] ?? '';
    $visitor_id = $_POST['visitor_id'] ?? '';

    if (empty($visitor_name) && empty($visitor_id)) {
        throw new Exception('Visitor name or ID is required');
    }

    $response = [
        'vital_signs' => [],
        'medication_referrals' => []
    ];

    // Fetch vital signs for visitors
    $vitalQuery = "SELECT * FROM vital_signs WHERE ";
    $params = [];
    if (!empty($visitor_id)) {
        $vitalQuery .= "visitor_id = ?";
        $params[] = $visitor_id;
    } else {
        $vitalQuery .= "visitor_name = ?";
        $params[] = $visitor_name;
    }
    $vitalQuery .= " ORDER BY vital_date DESC, created_at DESC LIMIT 10";
    $vitalStmt = $db->prepare($vitalQuery);
    $vitalStmt->execute($params);
    $response['vital_signs'] = $vitalStmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch medication referrals for visitors
    $refQuery = "SELECT * FROM medication_referrals WHERE ";
    $refParams = [];
    if (!empty($visitor_id)) {
        $refQuery .= "visitor_id = ?";
        $refParams[] = $visitor_id;
    } else {
        $refQuery .= "visitor_name = ?";
        $refParams[] = $visitor_name;
    }
    $refQuery .= " ORDER BY created_at DESC LIMIT 10";
    $refStmt = $db->prepare($refQuery);
    $refStmt->execute($refParams);
    $response['medication_referrals'] = $refStmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($response);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
