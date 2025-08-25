<?php
// staff/parent_alerts.php
// Handle email notification (AJAX POST) before any output or includes
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $db = new PDO('mysql:host=localhost;dbname=clinic_management_system;charset=utf8', 'root', '');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_POST['action'] === 'send_alert' && isset($_POST['parent_email'], $_POST['patient_name'], $_POST['visit_count'], $_POST['patient_id'])) {
        require_once '../mail.php';

        $patientId = (int)$_POST['patient_id'];
        $parentEmail = $_POST['parent_email'];
        $patientName = $_POST['patient_name'];
        $visitCount = (int)$_POST['visit_count'];

        // Get current week dates
        $startOfWeek = date('Y-m-d', strtotime('monday this week'));
        $endOfWeek = date('Y-m-d', strtotime('sunday this week'));

        $subject = "Clinic Medication Alert for $patientName";
        $body = "
            <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
                <h2 style='color: #2563eb;'>Clinic Medication Visit Alert</h2>
                <p>Dear Parent/Guardian,</p>
                <p>We are writing to inform you that your child, <strong>$patientName</strong>, has received medication from the clinic <strong>$visitCount times</strong> this week (Monday to Sunday).</p>
                <div style='background-color: #f3f4f6; padding: 15px; border-radius: 8px; margin: 20px 0;'>
                    <h3 style='margin-top: 0; color: #374151;'>Medication Visit Details This Week:</h3>
                    <p style='margin-bottom: 0;'>$visitDetails</p>
                </div>
                <p>We recommend that you:</p>
                <ul>
                    <li>Check up on your child's health and wellbeing</li>
                    <li>Contact the clinic if you have any concerns about the frequent medication needs</li>
                    <li>Consider scheduling a consultation to discuss any ongoing health issues</li>
                    <li>Review if there are any patterns or triggers that might be causing frequent visits</li>
                </ul>
                <p>Multiple medication visits in a week may indicate an underlying health concern that should be addressed.</p>
                <p>If you have any questions or concerns, please don't hesitate to contact us.</p>
                <p style='margin-top: 30px;'>
                    Best regards,<br>
                    <strong>Clinic Management Team</strong>
                </p>
            </div>
        ";

        $sent = sendMail($parentEmail, 'Parent/Guardian', $subject, $body, 'jaynujangad03@gmail.com', 'Clinic Management');

        // Log the alert attempt (using patient name as primary key)
        $alertStatus = $sent ? 'sent' : 'failed';
        $stmt = $db->prepare("
            INSERT INTO parent_alerts (patient_id, patient_name, parent_email, visit_count, week_start_date, week_end_date, alert_status, email_content, sent_by)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE
                alert_status = VALUES(alert_status),
                email_content = VALUES(email_content),
                sent_by = VALUES(sent_by),
                alert_sent_at = CURRENT_TIMESTAMP
        ");
        $stmt->execute([$patientId, $patientName, $parentEmail, $visitCount, $startOfWeek, $endOfWeek, $alertStatus, $body, $_SESSION['username'] ?? 'Staff']);

        // Update weekly summary to mark alert as sent
        if ($sent) {
            $stmt = $db->prepare("
                UPDATE weekly_visit_summary 
                SET alert_sent = TRUE, updated_at = CURRENT_TIMESTAMP 
                WHERE patient_id = ? AND week_start_date = ?
            ");
            $stmt->execute([$patientId, $startOfWeek]);
        }

        header('Content-Type: application/json');
        echo json_encode(['success' => $sent, 'message' => $sent ? 'Alert sent successfully!' : 'Failed to send alert.']);
        exit;
    }

    if ($_POST['action'] === 'refresh_data') {
        // Refresh clinic visits data
        $stmt = $db->prepare("CALL sync_clinic_visits()");
        $stmt->execute();

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
    try {
        $db->exec("CALL sync_clinic_visits()");
    } catch (Exception $e) {
        // Ignore if procedure doesn't exist yet
    }
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

        // Check for existing alerts
        $alertStmt = $db->prepare("SELECT alert_sent_at, alert_status, id FROM parent_alerts WHERE patient_name = ? AND week_start_date = ? AND alert_status = 'sent'");
        $alertStmt->execute([$visit['patient_name'], $startOfWeek]);
        $alertInfo = $alertStmt->fetch(PDO::FETCH_ASSOC);

        if ($alertInfo) {
            $visit['last_alert_sent'] = $alertInfo['alert_sent_at'];
            $visit['alert_status'] = $alertInfo['alert_status'];
            $visit['alert_already_sent'] = true;
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
?>

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
                    <?php if (!empty($allPrescriptionVisits)): ?>
                        <?php foreach ($allPrescriptionVisits as $alert): ?>
                            <tr class="bg-yellow-50">
                                <td class="px-4 py-2 font-medium"><?php echo htmlspecialchars($alert['patient_name']); ?></td>
                                <td class="px-4 py-2 text-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        <?php echo (int)$alert['visit_count']; ?> visits
                                    </span>
                                </td>
                                <td class="px-4 py-2 text-sm">
                                    <?php echo date('M j', strtotime($alert['first_visit_this_week'])) . ' - ' . date('M j', strtotime($alert['last_visit_this_week'])); ?>
                                </td>
                                <td class="px-4 py-2"><?php echo htmlspecialchars($alert['parent_email'] ?? 'No email'); ?></td>
                                <td class="px-4 py-2 text-center">
                                    <?php if (isset($alert['alert_already_sent']) && $alert['alert_already_sent']): ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Alert Sent
                                        </span>
                                        <div class="text-xs text-gray-500 mt-1">
                                            <?php echo isset($alert['last_alert_sent']) ? date('M j, g:i A', strtotime($alert['last_alert_sent'])) : 'Unknown time'; ?>
                                        </div>
                                    <?php else: ?>
                                        <button onclick="sendAlert(<?php echo $alert['patient_id']; ?>, '<?php echo htmlspecialchars($alert['patient_name'], ENT_QUOTES); ?>', '<?php echo htmlspecialchars($alert['parent_email'] ?? '', ENT_QUOTES); ?>', <?php echo $alert['visit_count']; ?>)"
                                            class="bg-blue-600 text-white px-3 py-1 rounded text-xs hover:bg-blue-700 transition-colors">
                                            Send Alert
                                        </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-gray-400">
                                <div>No students with 3+ medication visits this week. Great news!</div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Alert History for This Week -->
    <?php if (!empty($alertHistory)): ?>
        <div class="bg-white rounded shadow p-6">
            <h3 class="text-lg font-semibold mb-4">Alert History This Week</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left font-semibold text-gray-600">Patient Name</th>
                            <th class="px-4 py-2 text-left font-semibold text-gray-600">Parent Email</th>
                            <th class="px-4 py-2 text-center font-semibold text-gray-600">Visit Count</th>
                            <th class="px-4 py-2 text-left font-semibold text-gray-600">Sent At</th>
                            <th class="px-4 py-2 text-center font-semibold text-gray-600">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($alertHistory as $history): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-2"><?php echo htmlspecialchars($history['patient_name']); ?></td>
                                <td class="px-4 py-2"><?php echo htmlspecialchars($history['parent_email']); ?></td>
                                <td class="px-4 py-2 text-center"><?php echo (int)$history['visit_count']; ?></td>
                                <td class="px-4 py-2"><?php echo date('M j, Y g:i A', strtotime($history['alert_sent_at'])); ?></td>
                                <td class="px-4 py-2 text-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                <?php echo $history['alert_status'] === 'sent' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                                        <?php echo ucfirst($history['alert_status']); ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>
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

            try {
                const details = JSON.parse(visitDetails);
                let contentHtml = '<div class="space-y-3">';

                details.forEach((visit, index) => {
                    contentHtml += `
                    <div class="border-l-4 border-blue-500 pl-4 py-2">
                        <div class="flex justify-between items-start mb-1">
                            <span class="font-medium text-gray-900">Visit ${index + 1}</span>
                            <span class="text-xs text-gray-500">${visit.visit_date}</span>
                        </div>
                        <div class="text-sm text-gray-600 space-y-1">
                            <div><strong>Time:</strong> ${visit.visit_time}</div>
                            ${visit.purpose ? `<div><strong>Purpose:</strong> ${visit.purpose}</div>` : ''}
                            ${visit.notes ? `<div><strong>Notes:</strong> ${visit.notes}</div>` : ''}
                        </div>
                    </div>
                `;
                });

                contentHtml += '</div>';
                modalContent.innerHTML = contentHtml;
            } catch (e) {
                modalContent.innerHTML = '<div class="text-gray-600">Unable to parse visit details.</div>';
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