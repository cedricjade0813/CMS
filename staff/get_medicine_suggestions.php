<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

try {
    $db = new PDO('mysql:host=localhost;dbname=clinic_management_system;charset=utf8', 'root', '');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $query = isset($_GET['q']) ? trim($_GET['q']) : '';
    
    if (empty($query)) {
        echo json_encode([]);
        exit;
    }
    
    // Get unique medicine names that match the query (case-insensitive)
    $stmt = $db->prepare("SELECT DISTINCT name FROM medicines WHERE name LIKE ? ORDER BY name ASC LIMIT 10");
    $stmt->execute(['%' . $query . '%']);
    $medicines = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo json_encode($medicines);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>
