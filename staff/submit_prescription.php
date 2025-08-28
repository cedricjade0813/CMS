<?php
// submit_prescription.php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../phpmailer/src/PHPMailer.php';
require_once __DIR__ . '/../phpmailer/src/SMTP.php';
require_once __DIR__ . '/../phpmailer/src/Exception.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

try {
    $db = new PDO('mysql:host=localhost;dbname=clinic_management_system;charset=utf8', 'root', '');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database connection failed: ' . $e->getMessage()]);
    exit;
}

$patient_id = isset($_POST['patient_id']) ? $_POST['patient_id'] : null;
$patient_name = isset($_POST['patient_name']) ? $_POST['patient_name'] : null;
$medicines = isset($_POST['medicines']) ? $_POST['medicines'] : null;
$reason = isset($_POST['reason']) ? $_POST['reason'] : null;
$notes = isset($_POST['notes']) ? $_POST['notes'] : null;
$prescribed_by = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Staff';
$patient_email = isset($_POST['patient_email']) ? $_POST['patient_email'] : null;
$parent_email = isset($_POST['parent_email']) ? $_POST['parent_email'] : null;

// If no medicines, send notes to patient email and exit
$medsArr = json_decode($medicines, true);
if (is_array($medsArr) && count($medsArr) === 0 && $patient_email) {
    require_once __DIR__ . '/../mail.php';
    $to = $patient_email;
    $toName = $patient_name ? $patient_name : 'Patient';
    $subject = 'Clinic Visit Notes';
    $body = 'Dear ' . htmlspecialchars($toName) . ',<br><br>';
    $body .= $notes ? nl2br(htmlspecialchars($notes)) : 'No additional notes.';
    $body .= '<br><br>Thank you,<br>Clinic Management';
    $fromEmail = 'jaynujangad03@gmail.com';
    $fromName = 'Clinic Management';
    $sent = sendMail($to, $toName, $subject, $body, $fromEmail, $fromName);
    echo json_encode(['success' => $sent, 'message' => $sent ? 'No prescription. Notes sent to patient email.' : 'Failed to send email to patient.']);
    exit;
}

// Normalize patient_id: if non-numeric (visitor), set to NULL so FK/constraints aren't violated
if ($patient_id !== null && !ctype_digit((string)$patient_id)) {
    $patient_id = null;
} else if ($patient_id !== null) {
    $patient_id = (int)$patient_id;
}

if ($patient_name && $medicines) {
    try {
        // Insert directly into prescriptions table (Issue Medication History)
        $stmt = $db->prepare('INSERT INTO prescriptions (patient_id, patient_name, prescribed_by, prescription_date, medicines, reason, notes, patient_email, parent_email) VALUES (?, ?, ?, NOW(), ?, ?, ?, ?, ?)');
        // Bind patient_id with correct type (NULL or INT)
        if ($patient_id === null) {
            $stmt->bindValue(1, null, PDO::PARAM_NULL);
        } else {
            $stmt->bindValue(1, $patient_id, PDO::PARAM_INT);
        }
        $stmt->bindValue(2, $patient_name);
        $stmt->bindValue(3, $prescribed_by);
        $stmt->bindValue(4, $medicines);
        $stmt->bindValue(5, $reason);
        $stmt->bindValue(6, $notes);
        $stmt->bindValue(7, $patient_email);
        $stmt->bindValue(8, $parent_email);
        $stmt->execute();
    } catch (PDOException $e) {
        // If NOT NULL constraint on patient_id prevents NULL, retry with 0 for visitors
        if ($patient_id === null) {
            try {
                $stmt = $db->prepare('INSERT INTO prescriptions (patient_id, patient_name, prescribed_by, prescription_date, medicines, reason, notes, patient_email, parent_email) VALUES (0, ?, ?, NOW(), ?, ?, ?, ?, ?)');
                $stmt->bindValue(1, $patient_name);
                $stmt->bindValue(2, $prescribed_by);
                $stmt->bindValue(3, $medicines);
                $stmt->bindValue(4, $reason);
                $stmt->bindValue(5, $notes);
                $stmt->bindValue(6, $patient_email);
                $stmt->bindValue(7, $parent_email);
                $stmt->execute();
            } catch (PDOException $e2) {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Insert failed: ' . $e2->getMessage()]);
                exit;
            }
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Insert failed: ' . $e->getMessage()]);
            exit;
        }
    }
    
    // Record visit for parent alerts tracking (students only to satisfy FK)
    try {
        if ($patient_id !== null) {
            // Ensure this patient exists to satisfy FK
            $checkPatient = $db->prepare("SELECT id FROM imported_patients WHERE id = ?");
            $checkPatient->execute([$patient_id]);
            if ($checkPatient->fetch()) {
                $visitStmt = $db->prepare("
                    INSERT INTO clinic_visits (patient_id, patient_name, visit_date, visit_type, visit_reason, staff_member) 
                    VALUES (?, ?, CURDATE(), 'prescription', ?, ?)
                ");
                $visitStmt->execute([$patient_id, $patient_name, $reason, $prescribed_by]);
                error_log("Parent Alerts: Recorded prescription visit for patient: $patient_name");
            } else {
                error_log("Parent Alerts: Skipped visit record for non-existent patient_id: $patient_id");
            }
        } else {
            // Visitor: skip clinic_visits to avoid FK violation
            error_log("Parent Alerts: Skipped visit record for visitor: $patient_name");
        }
    } catch (Exception $e) {
        error_log("Parent Alerts: Error recording prescription visit: " . $e->getMessage());
        // Don't fail the main operation if this fails
    }
    
    // Deduct from inventory immediately
    $meds = json_decode($medicines, true);
    if (is_array($meds)) {
        foreach ($meds as $med) {
            $medName = $med['medicine'] ?? '';
            $qty = (int)($med['quantity'] ?? 0);
            if ($medName && $qty > 0) {
                $upd = $db->prepare('UPDATE medicines SET quantity = GREATEST(quantity - ?, 0) WHERE name = ?');
                $upd->execute([$qty, $medName]);
            }
        }
    }
    
    // Log action
    $user_email = isset($_SESSION['user_email']) ? $_SESSION['user_email'] : $prescribed_by;
    $logDb = new PDO('mysql:host=localhost;dbname=clinic_management_system;charset=utf8', 'root', '');
    $logDb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $logDb->prepare('CREATE TABLE IF NOT EXISTS logs (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT,
        user_email VARCHAR(255),
        action VARCHAR(255),
        timestamp DATETIME DEFAULT CURRENT_TIMESTAMP
    )')->execute();
    $logDb->prepare('INSERT INTO logs (user_email, action) VALUES (?, ?)')->execute([
        $user_email,
        'Issued prescription for patient: ' . $patient_name
    ]);
    
    echo json_encode(['success' => true, 'message' => 'Prescription issued successfully and added to medication history.']);
} else {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Missing required data.']);
}
?>
