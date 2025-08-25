<?php
header('Content-Type: application/json');

try {
    // Database connection
    $db = new PDO('mysql:host=localhost;dbname=clinic_management_system;charset=utf8', 'root', '');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Check if required data is provided
    if (!isset($_POST['referral_id']) || !isset($_POST['referral_to'])) {
        echo json_encode([
            'success' => false,
            'message' => 'Missing required data'
        ]);
        exit;
    }
    
    $referralId = $_POST['referral_id'];
    $referralTo = trim($_POST['referral_to']);
    
    // Validate referral_to is not empty
    if (empty($referralTo)) {
        echo json_encode([
            'success' => false,
            'message' => 'Referral destination cannot be empty'
        ]);
        exit;
    }
    
    // Check if the referral_to column exists, if not add it
    $stmt = $db->query("SHOW COLUMNS FROM medication_referrals LIKE 'referral_to'");
    if ($stmt->rowCount() == 0) {
        // Add referral_to column if it doesn't exist
        $db->exec("ALTER TABLE medication_referrals ADD COLUMN referral_to VARCHAR(255) DEFAULT NULL");
    }
    
    // Update the referral record with the referral destination
    $stmt = $db->prepare("
        UPDATE medication_referrals 
        SET referral_to = :referral_to 
        WHERE id = :referral_id
    ");
    
    $result = $stmt->execute([
        ':referral_to' => $referralTo,
        ':referral_id' => $referralId
    ]);
    
    if ($result && $stmt->rowCount() > 0) {
        echo json_encode([
            'success' => true,
            'message' => 'Referral destination saved successfully'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to update referral record or record not found'
        ]);
    }
    
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?>
