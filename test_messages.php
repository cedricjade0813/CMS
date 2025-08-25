<?php
// Test script to add sample messages to all patients
try {
    $db = new PDO('mysql:host=localhost;dbname=clinic_management_system;charset=utf8', 'root', '');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create messages table if not exists
    $db->exec("CREATE TABLE IF NOT EXISTS messages (
        id INT AUTO_INCREMENT PRIMARY KEY,
        sender_id INT NOT NULL,
        sender_name VARCHAR(255) NOT NULL,
        sender_role VARCHAR(50) NOT NULL,
        recipient_id INT NOT NULL,
        recipient_name VARCHAR(255) NOT NULL,
        subject VARCHAR(255) NOT NULL,
        message TEXT NOT NULL,
        is_read BOOLEAN DEFAULT FALSE,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_recipient (recipient_id),
        INDEX idx_sender (sender_id),
        INDEX idx_created_at (created_at)
    )");
    
    // Get all patients
    $patients = $db->query('SELECT id, name FROM imported_patients')->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($patients)) {
        echo "No patients found in the database. Please import patients first.\n";
        exit;
    }
    
    echo "Found " . count($patients) . " patients in the database.\n";
    
    // Sample messages to send to ALL patients
    $sample_messages = [
        [
            'subject' => 'Important Health Advisory',
            'message' => 'Dear Students, This is an important health advisory for all students. Please maintain good hygiene practices, wash your hands frequently, and stay home if you feel unwell. If you experience any symptoms, please contact the clinic immediately.'
        ],
        [
            'subject' => 'Clinic Schedule Update',
            'message' => 'Attention all students: The clinic will have modified hours next week due to staff training. We will be open from 8:00 AM to 3:00 PM only. Emergency services will still be available during regular hours.'
        ],
        [
            'subject' => 'Annual Health Check Reminder',
            'message' => 'Hello everyone! This is a reminder that annual health checks are now available. Please schedule your appointment through the clinic management system. This is mandatory for all students.'
        ]
    ];
    
    $total_inserted = 0;
    foreach ($sample_messages as $msg) {
        $inserted = 0;
        echo "\nSending: " . $msg['subject'] . "\n";
        
        foreach ($patients as $patient) {
            try {
                $stmt = $db->prepare('INSERT INTO messages (sender_id, sender_name, sender_role, recipient_id, recipient_name, subject, message) VALUES (?, ?, ?, ?, ?, ?, ?)');
                $stmt->execute([
                    1, // sender_id
                    'Dr. Smith', // sender_name
                    'doctor', // sender_role
                    $patient['id'], // recipient_id
                    $patient['name'], // recipient_name
                    $msg['subject'],
                    $msg['message']
                ]);
                $inserted++;
                $total_inserted++;
            } catch (PDOException $e) {
                echo "Error inserting message for " . $patient['name'] . ": " . $e->getMessage() . "\n";
            }
        }
        echo "Sent to " . $inserted . " patients.\n";
    }
    
    echo "\n=== SUMMARY ===\n";
    echo "Total messages inserted: $total_inserted\n";
    echo "Messages sent to " . count($patients) . " patients\n";
    echo "You can now test the messaging system by logging in as any patient.\n";
    echo "\nTo test the new 'Send to All Patients' feature:\n";
    echo "1. Log in as staff\n";
    echo "2. Go to staff/messages.php\n";
    echo "3. Select 'All Patients' option\n";
    echo "4. Enter subject and message\n";
    echo "5. Click 'Send Message'\n";
    
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>
