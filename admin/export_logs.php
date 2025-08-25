<?php
// Get filter parameters
$userFilter = isset($_GET['user']) ? $_GET['user'] : 'all';
$dateFilter = isset($_GET['date']) ? $_GET['date'] : '';
$searchFilter = isset($_GET['search']) ? $_GET['search'] : '';

try {
    $db = new PDO('mysql:host=localhost;dbname=clinic_management_system;charset=utf8', 'root', '');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Build WHERE clause for filters
    $whereConditions = [];
    $params = [];
    
    if ($userFilter !== 'all') {
        $whereConditions[] = "user_email = ?";
        $params[] = $userFilter;
    }
    
    if ($dateFilter) {
        $whereConditions[] = "DATE(timestamp) = ?";
        $params[] = $dateFilter;
    }
    
    if ($searchFilter) {
        $whereConditions[] = "(action LIKE ? OR user_email LIKE ?)";
        $params[] = '%' . $searchFilter . '%';
        $params[] = '%' . $searchFilter . '%';
    }
    
    $whereClause = !empty($whereConditions) ? 'WHERE ' . implode(' AND ', $whereConditions) : '';
    
    // Get all logs matching filters
    $logsQuery = "SELECT * FROM logs $whereClause ORDER BY timestamp DESC";
    $logsStmt = $db->prepare($logsQuery);
    $logsStmt->execute($params);
    $logs = $logsStmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Build user map: email => username
    $userMap = [];
    try {
        $userRows = $db->query('SELECT email, username FROM users')->fetchAll(PDO::FETCH_ASSOC);
        foreach ($userRows as $u) {
            $userMap[$u['email']] = $u['username'];
        }
    } catch (Exception $e) {}
    
    // Set headers for CSV download
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="system_logs_' . date('Ymd_His') . '.csv"');
    header('Pragma: no-cache');
    header('Expires: 0');
    
    // Open output stream
    $output = fopen('php://output', 'w');
    
    // Write CSV header
    fputcsv($output, ['User', 'Action', 'Timestamp']);
    
    // Write data rows
    foreach ($logs as $log) {
        $user = $log['user_email'];
        $username = isset($userMap[$user]) ? $userMap[$user] : ($user ? $user : 'System');
        
        fputcsv($output, [
            $username,
            $log['action'],
            $log['timestamp']
        ]);
    }
    
    fclose($output);
    exit;
    
} catch (PDOException $e) {
    // Error handling
    header('Content-Type: text/plain');
    echo 'Error exporting logs: ' . $e->getMessage();
    exit;
}
?>
