<?php
// staff/parent_alerts.php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
// Handle email notification (AJAX POST) before any output or includes
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $db = new PDO('mysql:host=localhost;dbname=clinic_management_system;charset=utf8', 'root', '');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_POST['action'] === 'send_alert' && isset($_POST['parent_email'], $_POST['patient_name'], $_POST['visit_count'], $_POST['patient_id'])) {
        // PHPMailer logic (simple contact form style)
    require_once __DIR__ . '/../phpmailer/src/Exception.php';
    require_once __DIR__ . '/../phpmailer/src/PHPMailer.php';
    require_once __DIR__ . '/../phpmailer/src/SMTP.php';

        $patientId = (int)$_POST['patient_id'];
        $parentEmail = trim($_POST['parent_email']);
        $patientName = $_POST['patient_name'];
        $visitCount = (int)$_POST['visit_count'];

        // Get current week dates
        $startOfWeek = date('Y-m-d', strtotime('monday this week'));
        $endOfWeek = date('Y-m-d', strtotime('sunday this week'));

        // Always fetch the latest parent email from prescriptions for this patient
        $latestPrescriptionStmt = $db->prepare("SELECT parent_email FROM prescriptions WHERE patient_id = ? ORDER BY prescription_date DESC LIMIT 1");
        $latestPrescriptionStmt->execute([$patientId]);
        $latestPrescription = $latestPrescriptionStmt->fetch(PDO::FETCH_ASSOC);
        if ($latestPrescription && !empty($latestPrescription['parent_email'])) {
            $parentEmail = trim($latestPrescription['parent_email']);
        }

        $subject = "Clinic Medication Alert for $patientName";
        $body = "<strong>Dear Parent/Guardian,</strong><br><br>Your child, <strong>$patientName</strong>, has received medication from the clinic <strong>$visitCount times</strong> this week.<br><br>Please check up on your child's health and contact the clinic if you have any concerns.<br><br>Best regards,<br>Clinic Management Team";

        $mail = new PHPMailer(true);
        $success = false;
        $errorMsg = '';
        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'cedricjade13@gmail.com';
            $mail->Password   = 'brkegvmjmefjqlza';
            $mail->SMTPSecure = 'ssl';
            $mail->Port       = 465;

            $mail->setFrom('cedricjade13@gmail.com', 'Clinic Management');
            // Send to parent email if valid, else fallback
            if (filter_var($parentEmail, FILTER_VALIDATE_EMAIL)) {
                $mail->addAddress($parentEmail);
            } else {
                $mail->addAddress('cedricjade13@gmail.com');
            }
            $mail->addReplyTo('cedricjade13@gmail.com', 'Clinic Management');

            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $body;

            $mail->send();
            $success = true;
        } catch (Exception $e) {
            $errorMsg = $mail->ErrorInfo;
        }
        header('Content-Type: application/json');
        if ($success) {
                // Log alert to parent_alerts table
                $insertAlertStmt = $db->prepare("INSERT INTO parent_alerts (patient_id, patient_name, parent_email, visit_count, week_start_date, week_end_date, alert_sent_at, alert_status, email_content, sent_by) VALUES (?, ?, ?, ?, ?, ?, NOW(), 'sent', ?, ?)");
                $insertAlertStmt->execute([
                    $patientId,
                    $patientName,
                    $parentEmail,
                    $visitCount,
                    $startOfWeek,
                    $endOfWeek,
                    $body,
                    'staff'
                ]);
            echo json_encode(['success' => true, 'message' => 'Message was sent successfully!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to send message: ' . $errorMsg]);
        }
        exit;
    }

    if ($_POST['action'] === 'refresh_data') {
    // Refresh clinic visits data
    // Removed CALL sync_clinic_visits() since procedure does not exist
    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'message' => 'Data refreshed successfully!']);
    exit;
    }
}

include '../includes/header.php';

try {
    $db = new PDO('mysql:host=localhost;dbname=clinic_management_system;charset=utf8', 'root', '');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Create clinic visits tables if they don't exist
    $createTablesSQL = [
        // Create prescriptions table if it doesn't exist
        "CREATE TABLE IF NOT EXISTS prescriptions (
            id INT AUTO_INCREMENT PRIMARY KEY,
            patient_id INT NOT NULL,
            patient_name VARCHAR(255) NOT NULL,
            prescription_date DATETIME DEFAULT CURRENT_TIMESTAMP,
            medicines TEXT NOT NULL,
            reason VARCHAR(500) DEFAULT NULL,
            prescribed_by VARCHAR(255) DEFAULT NULL,
            status ENUM('pending', 'issued', 'completed') DEFAULT 'pending',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_patient_date (patient_id, prescription_date),
            INDEX idx_prescription_date (prescription_date),
            INDEX idx_patient_name (patient_name),
            INDEX idx_status (status)
        )",

        // Create clinic_visits table
        "CREATE TABLE IF NOT EXISTS clinic_visits (
            id INT AUTO_INCREMENT PRIMARY KEY,
            patient_id INT NULL,
            patient_name VARCHAR(255) NOT NULL,
            visit_date DATE NOT NULL,
            visit_time TIME DEFAULT NULL,
            visit_reason VARCHAR(500) DEFAULT NULL,
            visit_type ENUM('appointment', 'prescription', 'walk_in', 'emergency') DEFAULT 'appointment',
            staff_member VARCHAR(255) DEFAULT NULL,
            notes TEXT DEFAULT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_patient_date (patient_id, visit_date),
            INDEX idx_visit_date (visit_date),
            INDEX idx_patient_id (patient_id),
            INDEX idx_patient_name (patient_name)
        )",

        // Create parent_alerts table
        "CREATE TABLE IF NOT EXISTS parent_alerts (
            id INT AUTO_INCREMENT PRIMARY KEY,
            patient_id INT NULL,
            patient_name VARCHAR(255) NOT NULL,
            parent_email VARCHAR(255) NOT NULL,
            visit_count INT NOT NULL,
            week_start_date DATE NOT NULL,
            week_end_date DATE NOT NULL,
            alert_sent_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            alert_status ENUM('pending', 'sent', 'failed') DEFAULT 'pending',
            email_content TEXT DEFAULT NULL,
            sent_by VARCHAR(255) DEFAULT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_patient_name_week (patient_name, week_start_date),
            INDEX idx_alert_date (alert_sent_at),
            INDEX idx_status (alert_status),
            UNIQUE KEY unique_name_week (patient_name, week_start_date)
        )",

        // Create weekly_visit_summary table
        "CREATE TABLE IF NOT EXISTS weekly_visit_summary (
            id INT AUTO_INCREMENT PRIMARY KEY,
            patient_id INT NULL,
            patient_name VARCHAR(255) NOT NULL,
            week_start_date DATE NOT NULL,
            week_end_date DATE NOT NULL,
            total_visits INT DEFAULT 0,
            visit_types JSON DEFAULT NULL,
            last_visit_date DATE DEFAULT NULL,
            needs_alert BOOLEAN DEFAULT FALSE,
            alert_sent BOOLEAN DEFAULT FALSE,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            UNIQUE KEY unique_patient_week (patient_name, week_start_date),
            INDEX idx_needs_alert (needs_alert),
            INDEX idx_week_dates (week_start_date, week_end_date),
            INDEX idx_patient_name (patient_name)
        )",

        // Add parent email columns
        "ALTER TABLE imported_patients ADD COLUMN IF NOT EXISTS parent_email VARCHAR(255) DEFAULT NULL AFTER email",
        "ALTER TABLE imported_patients ADD COLUMN IF NOT EXISTS parent_phone VARCHAR(20) DEFAULT NULL AFTER parent_email"
    ];

    foreach ($createTablesSQL as $sql) {
        try {
            $db->exec($sql);
        } catch (Exception $e) {
            // Ignore errors for already existing tables/columns
        }
    }

    // Sync recent data first
    // Removed CALL sync_clinic_visits() since procedure does not exist
} catch (Exception $e) {
    error_log("Database setup error: " . $e->getMessage());
}

// Get current week dates
$startOfWeek = date('Y-m-d', strtotime('monday this week'));
$endOfWeek = date('Y-m-d', strtotime('sunday this week'));

// Function to record prescription visits (to be called from submit_prescription.php)
function recordPrescriptionVisit($db, $patient_id, $patient_name, $medicines, $reason = null, $prescribed_by = null)
{
    try {
        // Insert into prescriptions table
        $stmt = $db->prepare("
            INSERT INTO prescriptions (patient_id, patient_name, medicines, reason, prescribed_by) 
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute([$patient_id, $patient_name, $medicines, $reason, $prescribed_by]);

        // Also insert into clinic_visits table for tracking
        $visitStmt = $db->prepare("
            INSERT INTO clinic_visits (patient_id, patient_name, visit_date, visit_type, visit_reason, staff_member) 
            VALUES (?, ?, CURDATE(), 'prescription', ?, ?)
        ");
        $visitStmt->execute([$patient_id, $patient_name, $reason, $prescribed_by]);

        error_log("Parent Alerts: Recorded prescription visit for patient: $patient_name");
        return true;
    } catch (Exception $e) {
        error_log("Parent Alerts: Error recording prescription visit: " . $e->getMessage());
        return false;
    }
}

// Get current week dates
$startOfWeek = date('Y-m-d', strtotime('monday this week'));
$endOfWeek = date('Y-m-d', strtotime('sunday this week'));

// Check if parent_email column exists, if not add it
try {
    $db->exec("ALTER TABLE imported_patients ADD COLUMN IF NOT EXISTS parent_email VARCHAR(255) DEFAULT NULL AFTER email");
    $db->exec("ALTER TABLE imported_patients ADD COLUMN IF NOT EXISTS parent_phone VARCHAR(20) DEFAULT NULL AFTER parent_email");
} catch (Exception $e) {
    // Column might already exist or other error, continue
}

// Try to get patients using prescription history (Issue Medication History logic)
$alerts = [];
$alertHistory = [];

try {
    // Use the working simple query and then fetch additional data as needed
    $sql = "
        SELECT 
            MIN(p.patient_id) as patient_id,
            p.patient_name,
            COUNT(*) as visit_count,
            MIN(DATE(p.prescription_date)) as first_visit_this_week,
            MAX(DATE(p.prescription_date)) as last_visit_this_week,
            GROUP_CONCAT(
                CONCAT(DATE(p.prescription_date), ': ', COALESCE(p.reason, 'Medication issued'))
                ORDER BY p.prescription_date SEPARATOR '<br>'
            ) as visit_details
        FROM prescriptions p
        WHERE DATE(p.prescription_date) BETWEEN ? AND ?
        GROUP BY p.patient_name
        HAVING COUNT(*) >= 3
        ORDER BY visit_count DESC, p.patient_name ASC
    ";

    $stmt = $db->prepare($sql);
    $stmt->execute([$startOfWeek, $endOfWeek]);
    $allPrescriptionVisits = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Now add the missing fields (parent_email, parent_phone, alert info) for each result
    foreach ($allPrescriptionVisits as &$visit) {
        // Initialize defaults
        $visit['parent_email'] = $visit['patient_name'] . '@example.com';
        $visit['parent_phone'] = '';
        $visit['last_alert_sent'] = null;
        $visit['alert_status'] = null;
        $visit['alert_already_sent'] = false;

        // Get parent email from the latest prescription entry for this patient name
        // This handles cases where there might be duplicate names by getting the most recent entry
        $latestPrescriptionStmt = $db->prepare("
            SELECT p.patient_id, p.prescription_date, p.parent_email, p.patient_email
            FROM prescriptions p
            WHERE p.patient_name = ?
            ORDER BY p.prescription_date DESC
            LIMIT 1
        ");
        $latestPrescriptionStmt->execute([$visit['patient_name']]);
        $latestPrescription = $latestPrescriptionStmt->fetch(PDO::FETCH_ASSOC);

        if ($latestPrescription) {
            // Get parent email directly from the latest prescription
            if (!empty($latestPrescription['parent_email'])) {
                $visit['parent_email'] = $latestPrescription['parent_email'];
            } elseif (!empty($latestPrescription['patient_email'])) {
                // Fallback to patient email if parent email is empty
                $visit['parent_email'] = $latestPrescription['patient_email'];
            }
        }

            // Check for existing alerts and compare visit_count
            $alertStmt = $db->prepare("SELECT alert_sent_at, alert_status, id, visit_count FROM parent_alerts WHERE patient_name = ? AND week_start_date = ? AND alert_status = 'sent' ORDER BY alert_sent_at DESC LIMIT 1");
            $alertStmt->execute([$visit['patient_name'], $startOfWeek]);
            $alertInfo = $alertStmt->fetch(PDO::FETCH_ASSOC);

            if ($alertInfo) {
                $visit['last_alert_sent'] = $alertInfo['alert_sent_at'];
                $visit['alert_status'] = $alertInfo['alert_status'];
                // Only mark as sent if visit_count matches
                if ((int)$alertInfo['visit_count'] === (int)$visit['visit_count']) {
                    $visit['alert_already_sent'] = true;
                } else {
                    $visit['alert_already_sent'] = false;
                }
            }
    }
    unset($visit); // Break the reference

    // Add debugging to see what we found
    error_log("Parent Alerts Debug - Main query found " . count($allPrescriptionVisits) . " patients with prescriptions this week");
    if (!empty($allPrescriptionVisits)) {
        foreach ($allPrescriptionVisits as $visit) {
            error_log("Main Query Result: " . $visit['patient_name'] . " - Visits: " . $visit['visit_count']);
        }
    } else {
        error_log("Main Query returned no results - checking query execution");
        $errorInfo = $stmt->errorInfo();
        if ($errorInfo[0] !== '00000') {
            error_log("SQL Error: " . $errorInfo[2]);
        }
        error_log("Parameters: startOfWeek=" . $startOfWeek . ", endOfWeek=" . $endOfWeek);
    }

    // No need to filter again since main query already filters for 3+ visits
    $alerts = $allPrescriptionVisits;

    error_log("Parent Alerts Debug - Found " . count($alerts) . " patients with 3+ visits");

    // Get alert history for this week
    $historyStmt = $db->prepare("
        SELECT patient_name, parent_email, visit_count, alert_sent_at, alert_status 
        FROM parent_alerts 
        WHERE week_start_date = ? AND alert_status = 'sent'
        ORDER BY alert_sent_at DESC
    ");
    $historyStmt->execute([$startOfWeek]);
    $alertHistory = $historyStmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    error_log("Error fetching prescription data: " . $e->getMessage());
    $alerts = [];
    $alertHistory = [];
}

// Pagination for Students with 3+ Medication Visits
$visits_per_page = 10;
$visits_page = isset($_GET['visits_page']) ? (int)$_GET['visits_page'] : 1;
$visits_page = max($visits_page, 1);
$visits_offset = ($visits_page - 1) * $visits_per_page;
$total_visits_records = count($alerts);
$total_visits_pages = ceil($total_visits_records / $visits_per_page);
$alerts_paginated = array_slice($alerts, $visits_offset, $visits_per_page);
?>

<style>
  html, body {
    scrollbar-width: none; /* Firefox */
    -ms-overflow-style: none; /* Internet Explorer 10+ */
  }
  html::-webkit-scrollbar,
  body::-webkit-scrollbar {
    display: none; /* Safari and Chrome */
  }
</style>

<main class="flex-1 overflow-y-auto bg-gray-50 p-6 ml-16 md:ml-64 mt-[56px]">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Parent Alert Notifications</h2>
    
    </div>

    <!-- Current Week Summary -->
    <div class="bg-white rounded shadow p-6 mb-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-800">Current Week Summary</h3>
            <span class="text-sm text-gray-600">
                <?php echo date('M j', strtotime($startOfWeek)) . ' - ' . date('M j, Y', strtotime($endOfWeek)); ?>
            </span>
        </div>
        <div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <div class="text-2xl font-bold text-yellow-800"><?php echo count($alerts); ?></div>
                    <div class="text-sm text-yellow-600">Students with 3+ medication visits</div>
                </div>
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="text-2xl font-bold text-blue-800"><?php echo count(array_filter($alerts, fn($a) => !$a['alert_already_sent'])); ?></div>
                    <div class="text-sm text-blue-600">Pending alerts</div>
                </div>
                <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                    <div class="text-2xl font-bold text-green-800"><?php echo count($alertHistory); ?></div>
                    <div class="text-sm text-green-600">Alerts sent this week</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Students Requiring Parent Alerts -->


    <!-- Students Requiring Parent Alerts -->
    <div class="bg-white rounded shadow p-6 mb-6">
        <h3 class="text-lg font-semibold mb-4">Students with 3+ Medication Visits This Week</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left font-semibold text-gray-600">Student Name</th>
                        <th class="px-4 py-2 text-center font-semibold text-gray-600">Visit Count</th>
                        <th class="px-4 py-2 text-left font-semibold text-gray-600">Visit Period</th>
                        <th class="px-4 py-2 text-left font-semibold text-gray-600">Parent Email</th>
                        <th class="px-4 py-2 text-center font-semibold text-gray-600">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($alerts_paginated)): ?>
                        <?php foreach ($alerts_paginated as $alert): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-2 flex items-center gap-2">
                                    <?php echo htmlspecialchars($alert['patient_name']); ?>
                                </td>
                                <td class="px-4 py-2 text-center">
                                    <span class="inline-block px-2 py-1 rounded bg-red-100 text-red-800 text-xs">
                                        <?php echo (int)$alert['visit_count']; ?> visits
                                    </span>
                                </td>
                                <td class="px-4 py-2 text-sm">
                                    <?php echo date('M j', strtotime($alert['first_visit_this_week'])) . ' - ' . date('M j', strtotime($alert['last_visit_this_week'])); ?>
                                </td>
                                <td class="px-4 py-2">
                                    <?php echo htmlspecialchars($alert['parent_email'] ?? 'No email'); ?>
                                </td>
                                <td class="px-4 py-2 text-center">
                                    <?php if (isset($alert['alert_already_sent']) && $alert['alert_already_sent']): ?>
                                        <span class="inline-block px-2 py-1 rounded bg-green-100 text-green-800 text-xs">Alert Sent</span>
                                        <div class="text-xs text-gray-500 mt-1">
                                            <?php echo isset($alert['last_alert_sent']) ? date('M j, g:i A', strtotime($alert['last_alert_sent'])) : 'Unknown time'; ?>
                                        </div>
                                    <?php else: ?>
                                        <button onclick="sendAlert(<?php echo $alert['patient_id']; ?>, '<?php echo htmlspecialchars($alert['patient_name'], ENT_QUOTES); ?>', '<?php echo htmlspecialchars($alert['parent_email'] ?? '', ENT_QUOTES); ?>', <?php echo $alert['visit_count']; ?>)"
                                            class="bg-blue-500 text-white px-3 py-1 rounded text-xs hover:bg-blue-600 transition-colors">
                                            Send Alert
                                        </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="5" class="px-4 py-8 text-center text-gray-400">No students with 3+ medication visits this week. Great news!</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <!-- Pagination and Records Info for Students with 3+ Medication Visits -->
        <?php if ($total_visits_records > 0): ?>
        <div class="flex justify-between items-center mt-6">
            <div class="text-sm text-gray-600">
                <?php 
                $visits_start = $visits_offset + 1;
                $visits_end = min($visits_offset + $visits_per_page, $total_visits_records);
                ?>
                Showing <?php echo $visits_start; ?> to <?php echo $visits_end; ?> of <?php echo $total_visits_records; ?> entries
            </div>
            <?php if ($total_visits_pages > 1): ?>
            <nav class="flex justify-end items-center -space-x-px" aria-label="Pagination">
                <!-- Previous Button -->
                <?php if ($visits_page > 1): ?>
                    <a href="?visits_page=<?php echo $visits_page - 1; ?>" class="min-h-9.5 min-w-9.5 py-2 px-2.5 inline-flex justify-center items-center gap-x-1.5 text-sm first:rounded-s-lg last:rounded-e-lg border border-gray-200 text-gray-800 hover:bg-gray-100 focus:outline-hidden focus:bg-gray-100" aria-label="Previous">
                        <svg class="shrink-0 size-3.5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m15 18-6-6 6-6"></path>
                        </svg>
                        <span class="sr-only">Previous</span>
                    </a>
                <?php else: ?>
                    <button type="button" disabled class="min-h-9.5 min-w-9.5 py-2 px-2.5 inline-flex justify-center items-center gap-x-1.5 text-sm first:rounded-s-lg last:rounded-e-lg border border-gray-200 text-gray-800 disabled:opacity-50 disabled:pointer-events-none" aria-label="Previous">
                        <svg class="shrink-0 size-3.5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m15 18-6-6 6-6"></path>
                        </svg>
                        <span class="sr-only">Previous</span>
                    </button>
                <?php endif; ?>
                <!-- Page Numbers -->
                <?php
                $visits_start_page = max(1, $visits_page - 2);
                $visits_end_page = min($total_visits_pages, $visits_page + 2);
                if ($visits_start_page > 1): ?>
                    <a href="?visits_page=1" class="min-h-9.5 min-w-9.5 flex justify-center items-center border border-gray-200 text-gray-800 hover:bg-gray-100 py-2 px-3 text-sm first:rounded-s-lg last:rounded-e-lg focus:outline-hidden focus:bg-gray-100">1</a>
                    <?php if ($visits_start_page > 2): ?>
                        <span class="min-h-9.5 min-w-9.5 flex justify-center items-center border border-gray-200 text-gray-800 py-2 px-3 text-sm">...</span>
                    <?php endif; ?>
                <?php endif; ?>
                <?php for ($i = $visits_start_page; $i <= $visits_end_page; $i++): ?>
                    <?php if ($i == $visits_page): ?>
                        <button type="button" class="min-h-9.5 min-w-9.5 flex justify-center items-center bg-gray-200 text-gray-800 border border-gray-200 py-2 px-3 text-sm first:rounded-s-lg last:rounded-e-lg focus:outline-hidden focus:bg-gray-300" aria-current="page"><?php echo $i; ?></button>
                    <?php else: ?>
                        <a href="?visits_page=<?php echo $i; ?>" class="min-h-9.5 min-w-9.5 flex justify-center items-center border border-gray-200 text-gray-800 hover:bg-gray-100 py-2 px-3 text-sm first:rounded-s-lg last:rounded-e-lg focus:outline-hidden focus:bg-gray-100"><?php echo $i; ?></a>
                    <?php endif; ?>
                <?php endfor; ?>
                <?php if ($visits_end_page < $total_visits_pages): ?>
                    <?php if ($visits_end_page < $total_visits_pages - 1): ?>
                        <span class="min-h-9.5 min-w-9.5 flex justify-center items-center border border-gray-200 text-gray-800 py-2 px-3 text-sm">...</span>
                    <?php endif; ?>
                    <a href="?visits_page=<?php echo $total_visits_pages; ?>" class="min-h-9.5 min-w-9.5 flex justify-center items-center border border-gray-200 text-gray-800 hover:bg-gray-100 py-2 px-3 text-sm first:rounded-s-lg last:rounded-e-lg focus:outline-hidden focus:bg-gray-100"><?php echo $total_visits_pages; ?></a>
                <?php endif; ?>
                <!-- Next Button -->
                <?php if ($visits_page < $total_visits_pages): ?>
                    <a href="?visits_page=<?php echo $visits_page + 1; ?>" class="min-h-9.5 min-w-9.5 py-2 px-2.5 inline-flex justify-center items-center gap-x-1.5 text-sm first:rounded-s-lg last:rounded-e-lg border border-gray-200 text-gray-800 hover:bg-gray-100 focus:outline-hidden focus:bg-gray-100" aria-label="Next">
                        <span class="sr-only">Next</span>
                        <svg class="shrink-0 size-3.5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m9 18 6-6-6-6"></path>
                        </svg>
                    </a>
                <?php else: ?>
                    <button type="button" disabled class="min-h-9.5 min-w-9.5 py-2 px-2.5 inline-flex justify-center items-center gap-x-1.5 text-sm first:rounded-s-lg last:rounded-e-lg border border-gray-200 text-gray-800 disabled:opacity-50 disabled:pointer-events-none" aria-label="Next">
                        <span class="sr-only">Next</span>
                        <svg class="shrink-0 size-3.5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m9 18 6-6-6-6"></path>
                        </svg>
                    </button>
                <?php endif; ?>
            </nav>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>

</main>

<!-- Visit Details Modal -->
<div id="visitDetailsModal" class="fixed inset-0 bg-black bg-opacity-30 flex items-center justify-center z-50 hidden">
    <div class="w-full max-w-lg mx-4 flex flex-col bg-white border border-gray-200 shadow-2xl rounded-xl pointer-events-auto">
        <div class="flex justify-between items-center py-3 px-4 border-b border-gray-200">
            <h3 id="detailsModalTitle" class="font-bold text-gray-800">Visit Details</h3>
            <button id="closeDetailsModal" type="button" class="size-8 inline-flex justify-center items-center gap-x-2 rounded-full border border-transparent bg-gray-100 text-gray-800 hover:bg-gray-200">
                <span class="sr-only">Close</span>
                <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M18 6 6 18"></path>
                    <path d="m6 6 12 12"></path>
                </svg>
            </button>
        </div>
        <div class="p-4 overflow-y-auto">
            <div id="visitDetailsContent" class="text-sm text-gray-700">
                <!-- Visit details will be populated here -->
            </div>
        </div>
        <div class="flex justify-end items-center gap-x-2 py-3 px-4 border-t border-gray-200">
            <button id="closeDetailsModalBtn" type="button" class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-white text-gray-800 hover:bg-gray-50">
                Close
            </button>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const notifyButtons = document.querySelectorAll('.notifyBtn');
        const viewDetailsButtons = document.querySelectorAll('.viewDetailsBtn');
        const visitDetailsModal = document.getElementById('visitDetailsModal');
        const closeDetailsModal = document.getElementById('closeDetailsModal');
        const closeDetailsModalBtn = document.getElementById('closeDetailsModalBtn');

        // View details modal functionality
        function showVisitDetails(visitDetails, patientName) {
            const modalTitle = document.getElementById('detailsModalTitle');
            const modalContent = document.getElementById('visitDetailsContent');

            modalTitle.textContent = `Visit Details - ${patientName}`;

            // visitDetails is HTML with <br> separators, not JSON
            if (visitDetails && typeof visitDetails === 'string') {
                modalContent.innerHTML = `<div class='text-sm text-gray-700 leading-relaxed'>${visitDetails}</div>`;
            } else {
                modalContent.innerHTML = '<div class="text-gray-600">No visit details available.</div>';
            }

            visitDetailsModal.classList.remove('hidden');
        }

        function hideVisitDetails() {
            visitDetailsModal.classList.add('hidden');
        }

        viewDetailsButtons.forEach(button => {
            button.addEventListener('click', function() {
                const visitDetails = this.getAttribute('data-visit-details');
                const patientName = this.getAttribute('data-patient-name');
                showVisitDetails(visitDetails, patientName);
            });
        });

        closeDetailsModal?.addEventListener('click', hideVisitDetails);
        closeDetailsModalBtn?.addEventListener('click', hideVisitDetails);

        visitDetailsModal?.addEventListener('click', function(e) {
            if (e.target === this) {
                hideVisitDetails();
            }
        });

        // Notify parent functionality
        notifyButtons.forEach(button => {
            button.addEventListener('click', function() {
                const patientId = this.getAttribute('data-patient-id');
                const parentEmail = this.getAttribute('data-parent-email');
                const patientName = this.getAttribute('data-patient-name');
                const visitCount = this.getAttribute('data-visit-count');

                // Disable button and show loading state
                this.disabled = true;
                this.innerHTML = '<i class="ri-loader-4-line mr-1 animate-spin"></i>Sending...';

                fetch('parent_alerts.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `action=send_alert&patient_id=${encodeURIComponent(patientId)}&parent_email=${encodeURIComponent(parentEmail)}&patient_name=${encodeURIComponent(patientName)}&visit_count=${encodeURIComponent(visitCount)}`
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Update button to show success
                            this.innerHTML = '<i class="ri-check-line mr-1"></i>Sent';
                            this.className = 'bg-green-600 text-white px-3 py-1 rounded text-xs cursor-not-allowed';

                            // Update status in the table
                            const row = this.closest('tr');
                            const statusCell = row.querySelector('td:nth-child(5)');
                            if (statusCell) {
                                statusCell.innerHTML = '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800"><i class="ri-check-line mr-1"></i>Sent</span>';
                            }

                            // Show success message
                            showNotification('Alert sent successfully!', 'success');
                        } else {
                            // Re-enable button on error
                            this.disabled = false;
                            this.innerHTML = '<i class="ri-mail-send-line mr-1"></i>Send Alert';
                            showNotification(data.message || 'Failed to send alert', 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        this.disabled = false;
                        this.innerHTML = '<i class="ri-mail-send-line mr-1"></i>Send Alert';
                        showNotification('Network error occurred', 'error');
                    });
            });
        });

        // Define sendAlert function for Send Alert button
        window.sendAlert = function(patientId, patientName, parentEmail, visitCount) {
            // Optionally, you can show a modal to confirm or edit the email before sending
            // For now, send directly as in the previous notifyBtn logic
            const button = event.target;
            button.disabled = true;
            button.innerHTML = '<i class="ri-loader-4-line mr-1 animate-spin"></i>Sending...';

            fetch('parent_alerts.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=send_alert&patient_id=${encodeURIComponent(patientId)}&parent_email=${encodeURIComponent(parentEmail)}&patient_name=${encodeURIComponent(patientName)}&visit_count=${encodeURIComponent(visitCount)}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    button.innerHTML = '<i class="ri-check-line mr-1"></i>Sent';
                    button.className = 'bg-green-600 text-white px-3 py-1 rounded text-xs cursor-not-allowed';
                    // Update status in the table
                    const row = button.closest('tr');
                    const statusCell = row.querySelector('td:nth-child(5)');
                    if (statusCell) {
                        statusCell.innerHTML = '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800"><i class="ri-check-line mr-1"></i>Sent</span>';
                    }
                    showNotification('Alert sent successfully!', 'success');
                } else {
                    button.disabled = false;
                    button.innerHTML = '<i class="ri-mail-send-line mr-1"></i>Send Alert';
                    showNotification(data.message || 'Failed to send alert', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                button.disabled = false;
                button.innerHTML = '<i class="ri-mail-send-line mr-1"></i>Send Alert';
                showNotification('Network error occurred', 'error');
            });
        };

        function showNotification(message, type) {
            // Create notification element
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 z-50 px-4 py-3 rounded-lg text-sm font-medium ${
            type === 'success' 
                ? 'bg-green-100 text-green-800 border border-green-200' 
                : 'bg-red-100 text-red-800 border border-red-200'
        }`;
            notification.innerHTML = `
            <div class="flex items-center">
                <i class="ri-${type === 'success' ? 'check' : 'error-warning'}-line mr-2"></i>
                ${message}
            </div>
        `;

            document.body.appendChild(notification);

            // Remove notification after 3 seconds
            setTimeout(() => {
                notification.remove();
            }, 3000);
        }

        // Refresh table function
        function refreshTable() {
            // Show loading state
            const refreshBtn = document.querySelector('button[onclick="refreshTable()"]');
            if (refreshBtn) {
                refreshBtn.innerHTML = '<i class="ri-loader-4-line mr-2 animate-spin"></i>Refreshing...';
                refreshBtn.disabled = true;
            }

            // Reload the page to refresh data
            window.location.reload();
        }
    });
</script>

<?php include '../includes/footer.php'; ?>