<?php
if (
    $_SERVER['REQUEST_METHOD'] === 'POST' &&
    isset($_POST['action']) &&
    (
        ($_POST['action'] === 'reschedule' && isset($_POST['name'], $_POST['oldDate'], $_POST['oldTime'], $_POST['reason'], $_POST['newDate'], $_POST['newTime'])) ||
        (in_array($_POST['action'], ['approve', 'decline']) && isset($_POST['date'], $_POST['time'], $_POST['reason'], $_POST['name'])) ||
        ($_POST['action'] === 'add_doctor' && isset($_POST['doctor_name'], $_POST['doctor_date'], $_POST['doctor_time'])) ||
        ($_POST['action'] === 'delete_schedule' && isset($_POST['id']))
    )
) {
    $conn = new mysqli('localhost', 'root', '', 'clinic_management_system');
    if ($conn->connect_errno) {
        echo json_encode(['success' => false, 'error' => 'DB error']);
        exit;
    }
    
    // Create doctor_schedules table if not exists
    $conn->query("CREATE TABLE IF NOT EXISTS doctor_schedules (
        id INT AUTO_INCREMENT PRIMARY KEY,
        doctor_name VARCHAR(255) NOT NULL,
        schedule_date DATE NOT NULL,
        schedule_time VARCHAR(100) NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY unique_schedule (doctor_name, schedule_date, schedule_time)
    )");
    
    $action = $_POST['action'];
    
    if ($action === 'add_doctor') {
        $doctor_name = trim($_POST['doctor_name']);
        $doctor_date = $_POST['doctor_date'];
        $doctor_time = $_POST['doctor_time'];
        
        if ($doctor_name && $doctor_date && $doctor_time) {
            // Parse the time range (e.g., "09:00-14:00")
            $time_parts = explode('-', $doctor_time);
            if (count($time_parts) === 2) {
                $start_time = trim($time_parts[0]);
                $end_time = trim($time_parts[1]);
                
                // Convert to DateTime objects for easier manipulation
                $start_datetime = DateTime::createFromFormat('H:i', $start_time);
                $end_datetime = DateTime::createFromFormat('H:i', $end_time);
                
                if ($start_datetime && $end_datetime && $start_datetime < $end_datetime) {
                    // Create only 1 doctor schedule entry with the full time range
                    $full_time_range = $start_time . '-' . $end_time;
                    
                    $stmt = $conn->prepare('INSERT INTO doctor_schedules (doctor_name, schedule_date, schedule_time) VALUES (?, ?, ?)');
                    $stmt->bind_param('sss', $doctor_name, $doctor_date, $full_time_range);
                    $success = $stmt->execute();
                    $schedule_id = $conn->insert_id; // Get the ID of the newly inserted schedule
                    $stmt->close();
                    
                    if ($success) {
                        echo json_encode([
                            'success' => true, 
                            'message' => 'Doctor schedule added successfully! This creates 10 appointment slots.',
                            'schedule_id' => $schedule_id
                        ]);
                    } else {
                        echo json_encode(['success' => false, 'error' => 'Failed to add doctor schedule']);
                    }
                    exit;
                } else {
                    echo json_encode(['success' => false, 'error' => 'Invalid time range']);
                    exit;
                }
            } else {
                echo json_encode(['success' => false, 'error' => 'Invalid time format']);
                exit;
            }
        } else {
            echo json_encode(['success' => false, 'error' => 'All fields are required']);
            exit;
        }
    }
    
    if ($action === 'delete_schedule') {
        $schedule_id = $_POST['id'];
        $stmt = $conn->prepare('DELETE FROM doctor_schedules WHERE id = ?');
        $stmt->bind_param('i', $schedule_id);
        $success = $stmt->execute();
        $stmt->close();
        
        echo json_encode(['success' => $success]);
        exit;
    }
    
    if ($action === 'reschedule') {
        // Validate newTime format (HH:mm or HH:mm:ss)
        $newTime = $_POST['newTime'];
        if (!preg_match('/^([01]\d|2[0-3]):[0-5]\d(:[0-5]\d)?$/', $newTime)) {
            echo json_encode(['success' => false, 'error' => 'Invalid time format. Please use HH:mm or HH:mm:ss.']);
            exit;
        }
        // Validate doctor_time format (HH:mm-HH:mm)
        $doctor_time = $_POST['doctor_time'];
        $time_parts = explode('-', $doctor_time);
        if (count($time_parts) === 2) {
            $start_time = trim($time_parts[0]);
            $end_time = trim($time_parts[1]);
            if (!preg_match('/^([01]\d|2[0-3]):[0-5]\d$/', $start_time) || !preg_match('/^([01]\d|2[0-3]):[0-5]\d$/', $end_time)) {
                echo json_encode(['success' => false, 'error' => 'Invalid doctor time format. Please use HH:mm-HH:mm.']);
                exit;
            }
        }
        // Only update Date and Time, never Reason
        $name = $_POST['name'];
        $oldDate = $_POST['oldDate'];
        $oldTime = $_POST['oldTime'];
        $reason = $_POST['reason']; // Used only for identifying the appointment, not for updating
        $newDate = $_POST['newDate'];
        $newTime = $_POST['newTime'];
        $stmt = $conn->prepare('SELECT a.email, ip.id FROM appointments a JOIN imported_patients ip ON a.student_id = ip.id WHERE ip.name = ? AND a.date = ? AND a.time = ? AND a.reason = ? LIMIT 1');
        $stmt->bind_param('ssss', $name, $oldDate, $oldTime, $reason);
        $stmt->execute();
        $stmt->bind_result($email, $student_id);
        $stmt->fetch();
        $stmt->close();
        if ($student_id && $email) {
            // Only update date and time, not reason
            $stmt2 = $conn->prepare('UPDATE appointments SET date = ?, time = ?, status = ? WHERE student_id = ? AND date = ? AND time = ? AND reason = ?');
            $newStatus = 'rescheduled';
            $stmt2->bind_param('sssisss', $newDate, $newTime, $newStatus, $student_id, $oldDate, $oldTime, $reason);
            $success = $stmt2->execute();
            $stmt2->close();
            // Send email notification
            require_once __DIR__ . '/../mail.php';
            $subject = 'Your Appointment Has Been Rescheduled';
            $msg = "Dear $name,<br>Your appointment for '$reason' has been rescheduled to <b>$newDate</b> at <b>$newTime</b>.<br>If you have questions, please contact the clinic.";
            sendMail($email, $name, $subject, $msg);
            // Insert notification for the patient
            $notif_msg = "Your appointment for $reason has been <span class='text-blue-600 font-semibold'>rescheduled</span> to <b>$newDate</b> at <b>$newTime</b>.";
            $notif_type = 'appointment';
            $stmt3 = $conn->prepare('INSERT INTO notifications (student_id, message, type, created_at) VALUES (?, ?, ?, NOW())');
            $stmt3->bind_param('iss', $student_id, $notif_msg, $notif_type);
            $stmt3->execute();
            $stmt3->close();
            echo json_encode(['success' => $success]);
            exit;
        } else {
            echo json_encode(['success' => false, 'error' => 'Patient not found']);
            exit;
        }
    }
    // Approve/Decline logic
    $date = $_POST['date'];
    $time = $_POST['time'];
    $reason = $_POST['reason'];
    $name = $_POST['name'];

    // Get student_id from imported_patients
    $stmt = $conn->prepare('SELECT id FROM imported_patients WHERE name = ? LIMIT 1');
    $stmt->bind_param('s', $name);
    $stmt->execute();
    $stmt->bind_result($student_id);
    $stmt->fetch();
    $stmt->close();

    if ($student_id) {
        if ($action === 'approve' || $action === 'decline') {
            $status = $action === 'approve' ? 'approved' : 'declined';
            $stmt = $conn->prepare('UPDATE appointments SET status = ? WHERE student_id = ? AND date = ? AND time = ? AND reason = ?');
            $stmt->bind_param('sisss', $status, $student_id, $date, $time, $reason);
            $stmt->execute();
            $stmt->close();

            // Insert notification
            $notif_msg = $status === 'approved'
                ? "Your appointment for $date $time has been <span class='text-green-600 font-semibold'>approved</span>."
                : "Your appointment for $date $time has been <span class='text-red-600 font-semibold'>declined</span>.";
            $notif_type = 'appointment';
            $conn->query("INSERT INTO notifications (student_id, message, type, created_at) VALUES ($student_id, '" . $conn->real_escape_string($notif_msg) . "', '$notif_type', NOW())");

            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Invalid action']);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Student not found']);
    }
    $conn->close();
    exit;
}
?>
<?php
include '../includes/header.php';

$conn = new mysqli('localhost', 'root', '', 'clinic_management_system');
if ($conn->connect_errno) {
    die('Database connection failed: ' . $conn->connect_error);
}

// Create doctor_schedules table if not exists
$conn->query("CREATE TABLE IF NOT EXISTS doctor_schedules (
    id INT AUTO_INCREMENT PRIMARY KEY,
    doctor_name VARCHAR(255) NOT NULL,
    schedule_date DATE NOT NULL,
    schedule_time VARCHAR(100) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_schedule (doctor_name, schedule_date, schedule_time)
)");

$appointments = [];
// Use only columns that exist in the appointments table
$sql = 'SELECT a.date, a.time, a.reason, a.status, a.email, ip.name FROM appointments a JOIN imported_patients ip ON a.student_id = ip.id ORDER BY a.date DESC, a.time DESC';
$result = $conn->query($sql);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $appointments[] = $row;
    }
    $result->free();
}

// Fetch doctor schedules
// Fetch doctor schedules (include id for delete button)
$doctor_schedules = [];
$schedule_sql = 'SELECT id, doctor_name, schedule_date, schedule_time FROM doctor_schedules ORDER BY schedule_date ASC, schedule_time ASC';
$schedule_result = $conn->query($schedule_sql);
if ($schedule_result) {
    while ($row = $schedule_result->fetch_assoc()) {
        $doctor_schedules[] = $row;
    }
    $schedule_result->free();
}

$conn->close();
?>
<!-- Dashboard Content -->

<!-- Appointments Content -->
<main class="flex-1 overflow-y-auto bg-gray-50 p-6 ml-16 md:ml-64 mt-[56px]">
    <h2 class="text-2xl font-bold mb-6 text-gray-800">Appointments</h2>
    
    <!-- Add Doctor Schedule Section -->
    <div class="bg-white rounded shadow p-6 mb-8">
        <h3 class="text-lg font-semibold mb-4">Add Doctor Schedule</h3>
        <p class="text-sm text-gray-600 mb-4">This will create 1 doctor schedule entry that represents 10 appointment slots of 30 minutes each.</p>
        <form id="addDoctorForm" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div>
                <label for="doctor_name" class="block text-sm font-medium text-gray-700 mb-1">Doctor Name</label>
                <input type="text" id="doctor_name" name="doctor_name" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-primary focus:border-primary" placeholder="e.g., Dr. Santos" required>
            </div>
            <div>
                <label for="doctor_date" class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                <input type="date" id="doctor_date" name="doctor_date" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-primary focus:border-primary" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Start Time</label>
                <input type="time" id="doctor_time_start" name="doctor_time_start" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-primary focus:border-primary" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">End Time</label>
                <input type="time" id="doctor_time_end" name="doctor_time_end" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-primary focus:border-primary" required>
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full bg-primary text-white px-4 py-2 rounded hover:bg-primary/90 font-semibold">
                    Add Schedule
                </button>
            </div>
        </form>
    </div>
    
    <!-- Doctor Schedules Table with Pagination -->
    <?php
    // Pagination for Doctor Schedules
    $ds_records_per_page = 10;
    $ds_page = isset($_GET['ds_page']) ? (int)$_GET['ds_page'] : 1;
    $ds_page = max($ds_page, 1);
    $ds_offset = ($ds_page - 1) * $ds_records_per_page;
    $conn = new mysqli('localhost', 'root', '', 'clinic_management_system');
    if ($conn->connect_errno) {
        die('Database connection failed: ' . $conn->connect_error);
    }
    $ds_total_count_stmt = $conn->query('SELECT COUNT(*) FROM doctor_schedules');
    $ds_total_records = $ds_total_count_stmt->fetch_row()[0];
    $ds_total_pages = ceil($ds_total_records / $ds_records_per_page);
    $ds_stmt = $conn->prepare('SELECT id, doctor_name, schedule_date, schedule_time FROM doctor_schedules ORDER BY schedule_date ASC, schedule_time ASC LIMIT ? OFFSET ?');
    $ds_stmt->bind_param('ii', $ds_records_per_page, $ds_offset);
    $ds_stmt->execute();
    $ds_result = $ds_stmt->get_result();
    ?>
    <div class="bg-white rounded shadow p-6 mb-8">
        <h3 class="text-lg font-semibold mb-4">Doctor Schedules</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left font-semibold text-gray-600">Doctor Name</th>
                        <th class="px-4 py-2 text-left font-semibold text-gray-600">Date</th>
                        <th class="px-4 py-2 text-left font-semibold text-gray-600">Time</th>
                        <th class="px-4 py-2 text-center font-semibold text-gray-600">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($ds_result->num_rows > 0): ?>
                        <?php while ($schedule = $ds_result->fetch_assoc()): ?>
                        <tr>
                            <td class="px-4 py-2"><?php echo htmlspecialchars($schedule['doctor_name']); ?></td>
                            <td class="px-4 py-2"><?php echo htmlspecialchars($schedule['schedule_date']); ?></td>
                            <td class="px-4 py-2"><?php echo htmlspecialchars($schedule['schedule_time']); ?></td>
                            <td class="px-4 py-2 text-center">
                                <button class="deleteScheduleBtn px-2 py-1 text-xs bg-red-500 text-white rounded hover:bg-red-600" 
                                        data-id="<?php echo $schedule['id']; ?>">Delete</button>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="4" class="px-4 py-2 text-center text-gray-400">No doctor schedules found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <!-- Pagination and Records Info for Doctor Schedules -->
        <?php if ($ds_total_records > 0): ?>
        <div class="flex justify-between items-center mt-6">
            <div class="text-sm text-gray-600">
                <?php 
                $ds_start = $ds_offset + 1;
                $ds_end = min($ds_offset + $ds_records_per_page, $ds_total_records);
                ?>
                Showing <?php echo $ds_start; ?> to <?php echo $ds_end; ?> of <?php echo $ds_total_records; ?> entries
            </div>
            <?php if ($ds_total_pages > 1): ?>
            <nav class="flex justify-end items-center -space-x-px" aria-label="Pagination">
                <?php if ($ds_page > 1): ?>
                    <a href="?ds_page=<?php echo $ds_page - 1; ?>" class="min-h-9.5 min-w-9.5 py-2 px-2.5 inline-flex justify-center items-center gap-x-1.5 text-sm first:rounded-s-lg last:rounded-e-lg border border-gray-200 text-gray-800 hover:bg-gray-100 focus:outline-hidden focus:bg-gray-100" aria-label="Previous">
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
                ?>

                // ...existing code for HTML page rendering below...
                    <?php
                $ds_start_page = max(1, $ds_page - 2);
                $ds_end_page = min($ds_total_pages, $ds_page + 2);
                if ($ds_start_page > 1): ?>
                    <a href="?ds_page=1" class="min-h-9.5 min-w-9.5 flex justify-center items-center border border-gray-200 text-gray-800 hover:bg-gray-100 py-2 px-3 text-sm first:rounded-s-lg last:rounded-e-lg focus:outline-hidden focus:bg-gray-100">1</a>
                    <?php if ($ds_start_page > 2): ?>
                        <span class="min-h-9.5 min-w-9.5 flex justify-center items-center border border-gray-200 text-gray-800 py-2 px-3 text-sm">...</span>
                    <?php endif; ?>
                <?php endif; ?>
                <?php for ($i = $ds_start_page; $i <= $ds_end_page; $i++): ?>
                    <?php if ($i == $ds_page): ?>
                        <button type="button" class="min-h-9.5 min-w-9.5 flex justify-center items-center bg-gray-200 text-gray-800 border border-gray-200 py-2 px-3 text-sm first:rounded-s-lg last:rounded-e-lg focus:outline-hidden focus:bg-gray-300" aria-current="page"><?php echo $i; ?></button>
                    <?php else: ?>
                        <a href="?ds_page=<?php echo $i; ?>" class="min-h-9.5 min-w-9.5 flex justify-center items-center border border-gray-200 text-gray-800 hover:bg-gray-100 py-2 px-3 text-sm first:rounded-s-lg last:rounded-e-lg focus:outline-hidden focus:bg-gray-100"><?php echo $i; ?></a>
                    <?php endif; ?>
                <?php endfor; ?>
                <?php if ($ds_end_page < $ds_total_pages): ?>
                    <?php if ($ds_end_page < $ds_total_pages - 1): ?>
                        <span class="min-h-9.5 min-w-9.5 flex justify-center items-center border border-gray-200 text-gray-800 py-2 px-3 text-sm">...</span>
                    <?php endif; ?>
                    <a href="?ds_page=<?php echo $ds_total_pages; ?>" class="min-h-9.5 min-w-9.5 flex justify-center items-center border border-gray-200 text-gray-800 hover:bg-gray-100 py-2 px-3 text-sm first:rounded-s-lg last:rounded-e-lg focus:outline-hidden focus:bg-gray-100"><?php echo $ds_total_pages; ?></a>
                <?php endif; ?>
                <?php if ($ds_page < $ds_total_pages): ?>
                    <a href="?ds_page=<?php echo $ds_page + 1; ?>" class="min-h-9.5 min-w-9.5 py-2 px-2.5 inline-flex justify-center items-center gap-x-1.5 text-sm first:rounded-s-lg last:rounded-e-lg border border-gray-200 text-gray-800 hover:bg-gray-100 focus:outline-hidden focus:bg-gray-100" aria-label="Next">
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
    <?php
    $ds_stmt->close();
    $conn->close();
    ?>
    
    <!-- Calendar View -->
    <div class="bg-white rounded shadow p-4 mb-8">
        <div class="flex items-center justify-between mb-4">
            <button id="prevMonthBtn" class="text-gray-500 hover:text-primary"><i class="ri-arrow-left-s-line ri-lg"></i></button>
            <span id="calendarMonth" class="font-semibold text-lg">May 2025</span>
            <button id="nextMonthBtn" class="text-gray-500 hover:text-primary"><i class="ri-arrow-right-s-line ri-lg"></i></button>
        </div>
        <div id="calendarGrid" class="grid grid-cols-7 gap-2 text-center text-sm">
            <!-- Calendar will be rendered here by JS -->
        </div>
    </div>
    
    <!-- Pending Appointments Table with Pagination -->
    <?php
    // Pagination for Pending Appointments
    $pending_records_per_page = 10;
    $pending_page = isset($_GET['pending_page']) ? (int)$_GET['pending_page'] : 1;
    $pending_page = max($pending_page, 1);
    $pending_offset = ($pending_page - 1) * $pending_records_per_page;
    $conn = new mysqli('localhost', 'root', '', 'clinic_management_system');
    if ($conn->connect_errno) {
        die('Database connection failed: ' . $conn->connect_error);
    }
    $pending_total_count_stmt = $conn->query("SELECT COUNT(*) FROM appointments a JOIN imported_patients ip ON a.student_id = ip.id WHERE a.status = 'pending'");
    $pending_total_records = $pending_total_count_stmt->fetch_row()[0];
    $pending_total_pages = ceil($pending_total_records / $pending_records_per_page);
    $pending_stmt = $conn->prepare("SELECT a.date, a.time, a.reason, a.status, a.email, ip.name FROM appointments a JOIN imported_patients ip ON a.student_id = ip.id WHERE a.status = 'pending' ORDER BY a.date DESC, a.time DESC LIMIT ? OFFSET ?");
    $pending_stmt->bind_param('ii', $pending_records_per_page, $pending_offset);
    $pending_stmt->execute();
    $pending_result = $pending_stmt->get_result();
    ?>
    <div class="bg-white rounded shadow p-6 mb-8">
        <h3 class="text-lg font-semibold mb-4">Pending Appointments</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left font-semibold text-gray-600">Name</th>
                        <th class="px-4 py-2 text-left font-semibold text-gray-600">Date</th>
                        <th class="px-4 py-2 text-left font-semibold text-gray-600">Time</th>
                        <th class="px-4 py-2 text-left font-semibold text-gray-600">Reason</th>
                        <th class="px-4 py-2 text-left font-semibold text-gray-600">Status</th>
                        <th class="px-4 py-2 text-center font-semibold text-gray-600">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($pending_result->num_rows > 0): ?>
                        <?php while ($appt = $pending_result->fetch_assoc()): ?>
                        <tr data-email="<?php echo htmlspecialchars($appt['email']); ?>">
                            <td class="px-4 py-2 flex items-center gap-2">
                                <button class="viewAppointmentBtn text-primary hover:text-blue-700" 
                                        data-name="<?php echo htmlspecialchars($appt['name']); ?>" 
                                        data-date="<?php echo htmlspecialchars($appt['date']); ?>" 
                                        data-time="<?php echo htmlspecialchars($appt['time']); ?>" 
                                        data-reason="<?php echo htmlspecialchars($appt['reason']); ?>" 
                                        data-email="<?php echo htmlspecialchars($appt['email']); ?>" 
                                        title="View Details"><i class="ri-eye-line text-lg"></i></button>
                                <?php echo htmlspecialchars($appt['name']); ?>
                            </td>
                            <td class="px-4 py-2"><?php echo htmlspecialchars($appt['date']); ?></td>
                            <td class="px-4 py-2"><?php echo htmlspecialchars($appt['time']); ?></td>
                            <td class="px-4 py-2"><?php echo htmlspecialchars($appt['reason']); ?></td>
                            <td class="px-4 py-2">
                                <span class="inline-block px-2 py-1 rounded bg-yellow-100 text-yellow-800 text-xs">Pending</span>
                            </td>
                            <td class="px-4 py-2 text-center">
                                <button class="approveBtn px-2 py-1 text-xs bg-green-500 text-white rounded hover:bg-green-600 mr-1">Approve</button>
                                <button class="declineBtn px-2 py-1 text-xs bg-red-500 text-white rounded hover:bg-red-600 mr-1">Decline</button>
                                <button class="reschedBtn px-2 py-1 text-xs bg-blue-500 text-white rounded hover:bg-blue-600">Reschedule</button>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="6" class="px-4 py-2 text-center text-gray-400">No pending appointments found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <!-- Pagination and Records Info for Pending Appointments -->
        <?php if ($pending_total_records > 0): ?>
        <div class="flex justify-between items-center mt-6">
            <div class="text-sm text-gray-600">
                <?php 
                $pending_start = $pending_offset + 1;
                $pending_end = min($pending_offset + $pending_records_per_page, $pending_total_records);
                ?>
                Showing <?php echo $pending_start; ?> to <?php echo $pending_end; ?> of <?php echo $pending_total_records; ?> entries
            </div>
            <?php if ($pending_total_pages > 1): ?>
            <nav class="flex justify-end items-center -space-x-px" aria-label="Pagination">
                <?php if ($pending_page > 1): ?>
                    <a href="?pending_page=<?php echo $pending_page - 1; ?>" class="min-h-9.5 min-w-9.5 py-2 px-2.5 inline-flex justify-center items-center gap-x-1.5 text-sm first:rounded-s-lg last:rounded-e-lg border border-gray-200 text-gray-800 hover:bg-gray-100 focus:outline-hidden focus:bg-gray-100" aria-label="Previous">
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
                <?php
                $pending_start_page = max(1, $pending_page - 2);
                $pending_end_page = min($pending_total_pages, $pending_page + 2);
                if ($pending_start_page > 1): ?>
                    <a href="?pending_page=1" class="min-h-9.5 min-w-9.5 flex justify-center items-center border border-gray-200 text-gray-800 hover:bg-gray-100 py-2 px-3 text-sm first:rounded-s-lg last:rounded-e-lg focus:outline-hidden focus:bg-gray-100">1</a>
                    <?php if ($pending_start_page > 2): ?>
                        <span class="min-h-9.5 min-w-9.5 flex justify-center items-center border border-gray-200 text-gray-800 py-2 px-3 text-sm">...</span>
                    <?php endif; ?>
                <?php endif; ?>
                <?php for ($i = $pending_start_page; $i <= $pending_end_page; $i++): ?>
                    <?php if ($i == $pending_page): ?>
                        <button type="button" class="min-h-9.5 min-w-9.5 flex justify-center items-center bg-gray-200 text-gray-800 border border-gray-200 py-2 px-3 text-sm first:rounded-s-lg last:rounded-e-lg focus:outline-hidden focus:bg-gray-300" aria-current="page"><?php echo $i; ?></button>
                    <?php else: ?>
                        <a href="?pending_page=<?php echo $i; ?>" class="min-h-9.5 min-w-9.5 flex justify-center items-center border border-gray-200 text-gray-800 hover:bg-gray-100 py-2 px-3 text-sm first:rounded-s-lg last:rounded-e-lg focus:outline-hidden focus:bg-gray-100"><?php echo $i; ?></a>
                    <?php endif; ?>
                <?php endfor; ?>
                <?php if ($pending_end_page < $pending_total_pages): ?>
                    <?php if ($pending_end_page < $pending_total_pages - 1): ?>
                        <span class="min-h-9.5 min-w-9.5 flex justify-center items-center border border-gray-200 text-gray-800 py-2 px-3 text-sm">...</span>
                    <?php endif; ?>
                    <a href="?pending_page=<?php echo $pending_total_pages; ?>" class="min-h-9.5 min-w-9.5 flex justify-center items-center border border-gray-200 text-gray-800 hover:bg-gray-100 py-2 px-3 text-sm first:rounded-s-lg last:rounded-e-lg focus:outline-hidden focus:bg-gray-100"><?php echo $pending_total_pages; ?></a>
                <?php endif; ?>
                <?php if ($pending_page < $pending_total_pages): ?>
                    <a href="?pending_page=<?php echo $pending_page + 1; ?>" class="min-h-9.5 min-w-9.5 py-2 px-2.5 inline-flex justify-center items-center gap-x-1.5 text-sm first:rounded-s-lg last:rounded-e-lg border border-gray-200 text-gray-800 hover:bg-gray-100 focus:outline-hidden focus:bg-gray-100" aria-label="Next">
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
    <?php
    $pending_stmt->close();
    $conn->close();
    ?>
    
    <!-- Done Appointments Table with Pagination -->
    <?php
    // Pagination for Done Appointments
    $done_records_per_page = 10;
    $done_page = isset($_GET['done_page']) ? (int)$_GET['done_page'] : 1;
    $done_page = max($done_page, 1);
    $done_offset = ($done_page - 1) * $done_records_per_page;
    $conn = new mysqli('localhost', 'root', '', 'clinic_management_system');
    if ($conn->connect_errno) {
        die('Database connection failed: ' . $conn->connect_error);
    }
    $done_total_count_stmt = $conn->query("SELECT COUNT(*) FROM appointments a JOIN imported_patients ip ON a.student_id = ip.id WHERE a.status IN ('approved', 'confirmed', 'declined')");
    $done_total_records = $done_total_count_stmt->fetch_row()[0];
    $done_total_pages = ceil($done_total_records / $done_records_per_page);
    $done_stmt = $conn->prepare("SELECT a.date, a.time, a.reason, a.status, a.email, ip.name FROM appointments a JOIN imported_patients ip ON a.student_id = ip.id WHERE a.status IN ('approved', 'confirmed', 'declined') ORDER BY a.date DESC, a.time DESC LIMIT ? OFFSET ?");
    $done_stmt->bind_param('ii', $done_records_per_page, $done_offset);
    $done_stmt->execute();
    $done_result = $done_stmt->get_result();
    ?>
    <div class="bg-white rounded shadow p-6 mb-8">
        <h3 class="text-lg font-semibold mb-4">Done Appointments</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left font-semibold text-gray-600">Name</th>
                        <th class="px-4 py-2 text-left font-semibold text-gray-600">Date</th>
                        <th class="px-4 py-2 text-left font-semibold text-gray-600">Time</th>
                        <th class="px-4 py-2 text-left font-semibold text-gray-600">Reason</th>
                        <th class="px-4 py-2 text-left font-semibold text-gray-600">Email</th>
                        <th class="px-4 py-2 text-left font-semibold text-gray-600">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($done_result->num_rows > 0): ?>
                        <?php while ($appt = $done_result->fetch_assoc()): ?>
                        <tr>
                            <td class="px-4 py-2"><?php echo htmlspecialchars($appt['name']); ?></td>
                            <td class="px-4 py-2"><?php echo htmlspecialchars($appt['date']); ?></td>
                            <td class="px-4 py-2"><?php echo htmlspecialchars($appt['time']); ?></td>
                            <td class="px-4 py-2"><?php echo htmlspecialchars($appt['reason']); ?></td>
                            <td class="px-4 py-2"><?php echo htmlspecialchars($appt['email']); ?></td>
                            <td class="px-4 py-2">
                                <?php if ($appt['status'] === 'approved' || $appt['status'] === 'confirmed'): ?>
                                    <span class="inline-block px-2 py-1 rounded bg-green-100 text-green-800 text-xs">Approved</span>
                                <?php elseif ($appt['status'] === 'declined'): ?>
                                    <span class="inline-block px-2 py-1 rounded bg-red-100 text-red-800 text-xs">Declined</span>
                                <?php else: ?>
                                    <span class="inline-block px-2 py-1 rounded bg-gray-100 text-gray-800 text-xs"><?php echo htmlspecialchars(ucfirst($appt['status'])); ?></span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="6" class="px-4 py-2 text-center text-gray-400">No done appointments found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <!-- Pagination and Records Info for Done Appointments -->
        <?php if ($done_total_records > 0): ?>
        <div class="flex justify-between items-center mt-6">
            <div class="text-sm text-gray-600">
                <?php 
                $done_start = $done_offset + 1;
                $done_end = min($done_offset + $done_records_per_page, $done_total_records);
                ?>
                Showing <?php echo $done_start; ?> to <?php echo $done_end; ?> of <?php echo $done_total_records; ?> entries
            </div>
            <?php if ($done_total_pages > 1): ?>
            <nav class="flex justify-end items-center -space-x-px" aria-label="Pagination">
                <?php if ($done_page > 1): ?>
                    <a href="?done_page=<?php echo $done_page - 1; ?>" class="min-h-9.5 min-w-9.5 py-2 px-2.5 inline-flex justify-center items-center gap-x-1.5 text-sm first:rounded-s-lg last:rounded-e-lg border border-gray-200 text-gray-800 hover:bg-gray-100 focus:outline-hidden focus:bg-gray-100" aria-label="Previous">
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
                <?php
                $done_start_page = max(1, $done_page - 2);
                $done_end_page = min($done_total_pages, $done_page + 2);
                if ($done_start_page > 1): ?>
                    <a href="?done_page=1" class="min-h-9.5 min-w-9.5 flex justify-center items-center border border-gray-200 text-gray-800 hover:bg-gray-100 py-2 px-3 text-sm first:rounded-s-lg last:rounded-e-lg focus:outline-hidden focus:bg-gray-100">1</a>
                    <?php if ($done_start_page > 2): ?>
                        <span class="min-h-9.5 min-w-9.5 flex justify-center items-center border border-gray-200 text-gray-800 py-2 px-3 text-sm">...</span>
                    <?php endif; ?>
                <?php endif; ?>
                <?php for ($i = $done_start_page; $i <= $done_end_page; $i++): ?>
                    <?php if ($i == $done_page): ?>
                        <button type="button" class="min-h-9.5 min-w-9.5 flex justify-center items-center bg-gray-200 text-gray-800 border border-gray-200 py-2 px-3 text-sm first:rounded-s-lg last:rounded-e-lg focus:outline-hidden focus:bg-gray-300" aria-current="page"><?php echo $i; ?></button>
                    <?php else: ?>
                        <a href="?done_page=<?php echo $i; ?>" class="min-h-9.5 min-w-9.5 flex justify-center items-center border border-gray-200 text-gray-800 hover:bg-gray-100 py-2 px-3 text-sm first:rounded-s-lg last:rounded-e-lg focus:outline-hidden focus:bg-gray-100"><?php echo $i; ?></a>
                    <?php endif; ?>
                <?php endfor; ?>
                <?php if ($done_end_page < $done_total_pages): ?>
                    <?php if ($done_end_page < $done_total_pages - 1): ?>
                        <span class="min-h-9.5 min-w-9.5 flex justify-center items-center border border-gray-200 text-gray-800 py-2 px-3 text-sm">...</span>
                    <?php endif; ?>
                    <a href="?done_page=<?php echo $done_total_pages; ?>" class="min-h-9.5 min-w-9.5 flex justify-center items-center border border-gray-200 text-gray-800 hover:bg-gray-100 py-2 px-3 text-sm first:rounded-s-lg last:rounded-e-lg focus:outline-hidden focus:bg-gray-100"><?php echo $done_total_pages; ?></a>
                <?php endif; ?>
                <?php if ($done_page < $done_total_pages): ?>
                    <a href="?done_page=<?php echo $done_page + 1; ?>" class="min-h-9.5 min-w-9.5 py-2 px-2.5 inline-flex justify-center items-center gap-x-1.5 text-sm first:rounded-s-lg last:rounded-e-lg border border-gray-200 text-gray-800 hover:bg-gray-100 focus:outline-hidden focus:bg-gray-100" aria-label="Next">
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
    <?php
    $done_stmt->close();
    $conn->close();
    ?>
    
    <!-- Rescheduled Appointments Table with Pagination -->
    <?php
    // Pagination for Rescheduled Appointments
    $resched_records_per_page = 10;
    $resched_page = isset($_GET['resched_page']) ? (int)$_GET['resched_page'] : 1;
    $resched_page = max($resched_page, 1);
    $resched_offset = ($resched_page - 1) * $resched_records_per_page;
    $conn = new mysqli('localhost', 'root', '', 'clinic_management_system');
    if ($conn->connect_errno) {
        die('Database connection failed: ' . $conn->connect_error);
    }
    $resched_total_count_stmt = $conn->query("SELECT COUNT(*) FROM appointments a JOIN imported_patients ip ON a.student_id = ip.id WHERE a.status = 'rescheduled'");
    $resched_total_records = $resched_total_count_stmt->fetch_row()[0];
    $resched_total_pages = ceil($resched_total_records / $resched_records_per_page);
    $resched_stmt = $conn->prepare("SELECT a.date, a.time, a.reason, a.status, a.email, ip.name FROM appointments a JOIN imported_patients ip ON a.student_id = ip.id WHERE a.status = 'rescheduled' ORDER BY a.date DESC, a.time DESC LIMIT ? OFFSET ?");
    $resched_stmt->bind_param('ii', $resched_records_per_page, $resched_offset);
    $resched_stmt->execute();
    $resched_result = $resched_stmt->get_result();
    ?>
    <div class="bg-white rounded shadow p-6 mb-8">
        <h3 class="text-lg font-semibold mb-4">Rescheduled Appointments</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left font-semibold text-gray-600">Name</th>
                        <th class="px-4 py-2 text-left font-semibold text-gray-600">Date</th>
                        <th class="px-4 py-2 text-left font-semibold text-gray-600">Time</th>
                        <th class="px-4 py-2 text-left font-semibold text-gray-600">Reason</th>
                        <th class="px-4 py-2 text-left font-semibold text-gray-600">Email</th>
                        <th class="px-4 py-2 text-left font-semibold text-gray-600">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($resched_result->num_rows > 0): ?>
                        <?php while ($appt = $resched_result->fetch_assoc()): ?>
                        <tr>
                            <td class="px-4 py-2"><?php echo htmlspecialchars($appt['name']); ?></td>
                            <td class="px-4 py-2"><?php echo htmlspecialchars($appt['date']); ?></td>
                            <td class="px-4 py-2"><?php echo htmlspecialchars($appt['time']); ?></td>
                            <td class="px-4 py-2"><?php echo htmlspecialchars($appt['reason']); ?></td>
                            <td class="px-4 py-2"><?php echo htmlspecialchars($appt['email']); ?></td>
                            <td class="px-4 py-2">
                                <span class="inline-block px-2 py-1 rounded bg-blue-100 text-blue-800 text-xs">Rescheduled</span>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="6" class="px-4 py-2 text-center text-gray-400">No rescheduled appointments found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <!-- Pagination and Records Info for Rescheduled Appointments -->
        <?php if ($resched_total_records > 0): ?>
        <div class="flex justify-between items-center mt-6">
            <div class="text-sm text-gray-600">
                <?php 
                $resched_start = $resched_offset + 1;
                $resched_end = min($resched_offset + $resched_records_per_page, $resched_total_records);
                ?>
                Showing <?php echo $resched_start; ?> to <?php echo $resched_end; ?> of <?php echo $resched_total_records; ?> entries
            </div>
            <?php if ($resched_total_pages > 1): ?>
            <nav class="flex justify-end items-center -space-x-px" aria-label="Pagination">
                <?php if ($resched_page > 1): ?>
                    <a href="?resched_page=<?php echo $resched_page - 1; ?>" class="min-h-9.5 min-w-9.5 py-2 px-2.5 inline-flex justify-center items-center gap-x-1.5 text-sm first:rounded-s-lg last:rounded-e-lg border border-gray-200 text-gray-800 hover:bg-gray-100 focus:outline-hidden focus:bg-gray-100" aria-label="Previous">
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
                <?php
                $resched_start_page = max(1, $resched_page - 2);
                $resched_end_page = min($resched_total_pages, $resched_page + 2);
                if ($resched_start_page > 1): ?>
                    <a href="?resched_page=1" class="min-h-9.5 min-w-9.5 flex justify-center items-center border border-gray-200 text-gray-800 hover:bg-gray-100 py-2 px-3 text-sm first:rounded-s-lg last:rounded-e-lg focus:outline-hidden focus:bg-gray-100">1</a>
                    <?php if ($resched_start_page > 2): ?>
                        <span class="min-h-9.5 min-w-9.5 flex justify-center items-center border border-gray-200 text-gray-800 py-2 px-3 text-sm">...</span>
                    <?php endif; ?>
                <?php endif; ?>
                <?php for ($i = $resched_start_page; $i <= $resched_end_page; $i++): ?>
                    <?php if ($i == $resched_page): ?>
                        <button type="button" class="min-h-9.5 min-w-9.5 flex justify-center items-center bg-gray-200 text-gray-800 border border-gray-200 py-2 px-3 text-sm first:rounded-s-lg last:rounded-e-lg focus:outline-hidden focus:bg-gray-300" aria-current="page"><?php echo $i; ?></button>
                    <?php else: ?>
                        <a href="?resched_page=<?php echo $i; ?>" class="min-h-9.5 min-w-9.5 flex justify-center items-center border border-gray-200 text-gray-800 hover:bg-gray-100 py-2 px-3 text-sm first:rounded-s-lg last:rounded-e-lg focus:outline-hidden focus:bg-gray-100"><?php echo $i; ?></a>
                    <?php endif; ?>
                <?php endfor; ?>
                <?php if ($resched_end_page < $resched_total_pages): ?>
                    <?php if ($resched_end_page < $resched_total_pages - 1): ?>
                        <span class="min-h-9.5 min-w-9.5 flex justify-center items-center border border-gray-200 text-gray-800 py-2 px-3 text-sm">...</span>
                    <?php endif; ?>
                    <a href="?resched_page=<?php echo $resched_total_pages; ?>" class="min-h-9.5 min-w-9.5 flex justify-center items-center border border-gray-200 text-gray-800 hover:bg-gray-100 py-2 px-3 text-sm first:rounded-s-lg last:rounded-e-lg focus:outline-hidden focus:bg-gray-100"><?php echo $resched_total_pages; ?></a>
                <?php endif; ?>
                <?php if ($resched_page < $resched_total_pages): ?>
                    <a href="?resched_page=<?php echo $resched_page + 1; ?>" class="min-h-9.5 min-w-9.5 py-2 px-2.5 inline-flex justify-center items-center gap-x-1.5 text-sm first:rounded-s-lg last:rounded-e-lg border border-gray-200 text-gray-800 hover:bg-gray-100 focus:outline-hidden focus:bg-gray-100" aria-label="Next">
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
    <?php
    $resched_stmt->close();
    $conn->close();
    ?>
    

</main>

<!-- View Appointment Modal -->
<div id="appointmentViewModal" class="fixed inset-0 bg-black bg-opacity-30 flex items-center justify-center z-50 hidden">
    <div class="w-full max-w-md mx-4 flex flex-col bg-white border border-gray-200 shadow-2xl rounded-xl pointer-events-auto dark:bg-neutral-800 dark:border-neutral-700 dark:shadow-neutral-700/70">
        <div class="flex justify-between items-center py-3 px-4 border-b border-gray-200 dark:border-neutral-700">
            <h3 id="hs-vertically-centered-modal-label" class="font-bold text-gray-800 dark:text-white">
                Appointment Details
            </h3>
            <button id="closeViewModalBtn" type="button" class="size-8 inline-flex justify-center items-center gap-x-2 rounded-full border border-transparent bg-gray-100 text-gray-800 hover:bg-gray-200 focus:outline-hidden focus:bg-gray-200 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-700 dark:hover:bg-neutral-600 dark:text-neutral-400 dark:focus:bg-neutral-600" aria-label="Close">
                <span class="sr-only">Close</span>
                <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M18 6 6 18"></path>
                    <path d="m6 6 12 12"></path>
                </svg>
            </button>
        </div>
        
        <div class="p-4 overflow-y-auto">
            <div class="space-y-3">
                <div class="grid grid-cols-1 gap-3">
                    <div class="grid grid-cols-[120px_1fr] gap-3 items-center">
                        <label class="text-sm font-medium text-gray-700 dark:text-neutral-300">Patient Name:</label>
                        <p id="viewPatientName" class="text-sm text-gray-900 dark:text-neutral-200"></p>
                    </div>
                    
                    <div class="grid grid-cols-[120px_1fr] gap-3 items-center">
                        <label class="text-sm font-medium text-gray-700 dark:text-neutral-300">Email:</label>
                        <p id="viewPatientEmail" class="text-sm text-gray-900 dark:text-neutral-200"></p>
                    </div>
                    
                    <div class="grid grid-cols-[120px_1fr] gap-3 items-center">
                        <label class="text-sm font-medium text-gray-700 dark:text-neutral-300">Date:</label>
                        <p id="viewAppointmentDate" class="text-sm text-gray-900 dark:text-neutral-200"></p>
                    </div>
                    
                    <div class="grid grid-cols-[120px_1fr] gap-3 items-center">
                        <label class="text-sm font-medium text-gray-700 dark:text-neutral-300">Time:</label>
                        <p id="viewAppointmentTime" class="text-sm text-gray-900 dark:text-neutral-200"></p>
                    </div>
                    
                    <div class="grid grid-cols-[120px_1fr] gap-3 items-start">
                        <label class="text-sm font-medium text-gray-700 dark:text-neutral-300">Reason for Visit:</label>
                        <p id="viewAppointmentReason" class="text-sm text-gray-900 dark:text-neutral-200"></p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="flex justify-end items-center gap-x-2 py-3 px-4 border-t border-gray-200 dark:border-neutral-700">
            <button id="closeViewModalBtnBottom" type="button" class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-white text-gray-800 shadow-sm hover:bg-gray-50 focus:outline-hidden focus:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-700 dark:text-white dark:hover:bg-neutral-700 dark:focus:bg-neutral-700">
                Close
            </button>
        </div>
    </div>
</div>

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

<script>
// Doctor schedules data for calendar
const doctorSchedules = <?php echo json_encode($doctor_schedules); ?>;

// Function to add new schedule to table dynamically
function addScheduleToTable(schedule) {
    const tbody = document.querySelector('table tbody');
    
    // Remove "No doctor schedules found" row if it exists
    const noDataRow = tbody.querySelector('td[colspan="4"]');
    if (noDataRow) {
        noDataRow.parentElement.remove();
    }
    
    // Create new row
    const newRow = document.createElement('tr');
    newRow.innerHTML = `
        <td class="px-4 py-2">${escapeHtml(schedule.doctor_name)}</td>
        <td class="px-4 py-2">${escapeHtml(schedule.schedule_date)}</td>
        <td class="px-4 py-2">${escapeHtml(schedule.schedule_time)}</td>
        <td class="px-4 py-2 text-center">
            <button class="deleteScheduleBtn px-2 py-1 text-xs bg-red-500 text-white rounded hover:bg-red-600" 
                    data-id="${schedule.id}">Delete</button>
        </td>
    `;
    
    // Add to tbody
    tbody.appendChild(newRow);
    
    // Add event listener to the new delete button
    const deleteBtn = newRow.querySelector('.deleteScheduleBtn');
    deleteBtn.addEventListener('click', function() {
        const scheduleId = this.dataset.id;
        
        showConfirmModal('Are you sure you want to delete this doctor schedule?', 
            function() {
                // User clicked Yes - Delete the schedule
                fetch('', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: new URLSearchParams({ action: 'delete_schedule', id: scheduleId })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        // Remove the row from table
                        newRow.remove();
                        
                        // If no rows left, add "No doctor schedules found" row
                        const remainingRows = tbody.querySelectorAll('tr');
                        if (remainingRows.length === 0) {
                            tbody.innerHTML = '<tr><td colspan="4" class="px-4 py-2 text-center text-gray-400">No doctor schedules found.</td></tr>';
                        }
                        
                        showSimpleSuccessMessage('Doctor schedule deleted successfully!');
                    } else {
                        showErrorModal('Failed to delete doctor schedule: ' + (data.error || 'Unknown error'), 'Error');
                    }
                })
                .catch(error => {
                    showErrorModal('Network error: ' + error.message, 'Error');
                });
            },
            function() {
                // User clicked No - Do nothing
                console.log('Delete cancelled by user');
            }
        );
    });
}

// Helper function to escape HTML
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Add doctor schedule form
document.getElementById('addDoctorForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.textContent;
    
    // Disable button and show loading state
    submitBtn.disabled = true;
    submitBtn.textContent = 'Adding...';
    
    formData.append('action', 'add_doctor');
    // Combine start and end time into a range
    const start = this.doctor_time_start.value;
    const end = this.doctor_time_end.value;
    formData.set('doctor_time', start + '-' + end);
    fetch('', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            // Clear the form
            this.reset();
            
            // Add the new schedule to the table without page refresh
            addScheduleToTable({
                doctor_name: formData.get('doctor_name'),
                schedule_date: formData.get('doctor_date'),
                schedule_time: start + '-' + end,
                id: data.schedule_id || 'new_' + Date.now() // Use returned ID or generate temporary one
            });
            
            showSimpleSuccessMessage(data.message || 'Doctor schedule added successfully!');
        } else {
            showErrorModal('Failed to add doctor schedule: ' + (data.error || 'Unknown error'), 'Error');
        }
    })
    .catch(error => {
        showErrorModal('Network error: ' + error.message, 'Error');
    })
    .finally(() => {
        // Re-enable button and restore text
        submitBtn.disabled = false;
        submitBtn.textContent = originalText;
    });
});

// Delete doctor schedule button
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.deleteScheduleBtn').forEach(button => {
        button.addEventListener('click', function() {
            const scheduleId = this.dataset.id;
            const row = this.closest('tr');
            
            showConfirmModal('Are you sure you want to delete this doctor schedule?', 
                function() {
                    // User clicked Yes - Delete the schedule
                    fetch('', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: new URLSearchParams({ action: 'delete_schedule', id: scheduleId })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            // Remove the row from table
                            row.remove();
                            
                            // If no rows left, add "No doctor schedules found" row
                            const tbody = document.querySelector('table tbody');
                            const remainingRows = tbody.querySelectorAll('tr');
                            if (remainingRows.length === 0) {
                                tbody.innerHTML = '<tr><td colspan="4" class="px-4 py-2 text-center text-gray-400">No doctor schedules found.</td></tr>';
                            }
                            
                            showSimpleSuccessMessage('Doctor schedule deleted successfully!');
                        } else {
                            showErrorModal('Failed to delete doctor schedule: ' + (data.error || 'Unknown error'), 'Error');
                        }
                    })
                    .catch(error => {
                        showErrorModal('Network error: ' + error.message, 'Error');
                    });
                },
                function() {
                    // User clicked No - Do nothing
                    console.log('Delete cancelled by user');
                }
            );
        });
    });
});

// Demo action button logic
const approveBtns = document.querySelectorAll('.approveBtn');
const declineBtns = document.querySelectorAll('.declineBtn');
const reschedBtns = document.querySelectorAll('.reschedBtn');

// Custom confirmation modal function that matches the design
function showConfirmModal(message, onConfirm, onCancel) {
    const modalId = 'confirmModal_' + Date.now();
    const modal = document.createElement('div');
    modal.id = modalId;
    modal.style.cssText = 'position:fixed;top:0;left:0;width:100vw;height:100vh;z-index:9999;display:flex;align-items:center;justify-content:center;pointer-events:none;background:rgba(255,255,255,0.18);';
    
    modal.innerHTML = `
        <div style='background:rgba(255,255,255,0.95); color:#d97706; min-width:300px; max-width:90vw; padding:24px 32px; border-radius:16px; box-shadow:0 4px 32px rgba(217,119,6,0.15); font-size:1.1rem; font-weight:500; text-align:center; border:1.5px solid #d97706; display:flex; flex-direction:column; gap:16px; pointer-events:auto;'>
            <div style='display:flex; align-items:center; justify-content:center; gap:12px;'>
                <span style='font-size:2rem;line-height:1;color:#d97706;'>&#9888;</span>
                <span style='color:#374151;'>${message}</span>
            </div>
            <div style='display:flex; gap:12px; justify-content:center;'>
                <button id='confirmBtn' style='background:#d97706; color:white; padding:8px 16px; border-radius:8px; font-weight:500; border:none; cursor:pointer;'>Confirm</button>
                <button id='cancelBtn' style='background:#f3f4f6; color:#374151; padding:8px 16px; border-radius:8px; font-weight:500; border:1px solid #d1d5db; cursor:pointer;'>Cancel</button>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    
    const confirmBtn = modal.querySelector('#confirmBtn');
    const cancelBtn = modal.querySelector('#cancelBtn');
    
    confirmBtn.onclick = function() {
        modal.style.transition = 'opacity 0.3s';
        modal.style.opacity = '0';
        setTimeout(() => { 
            if (modal && modal.parentNode) {
                modal.parentNode.removeChild(modal);
            }
            if (typeof onConfirm === 'function') onConfirm();
        }, 300);
    };
    
    cancelBtn.onclick = function() {
        modal.style.transition = 'opacity 0.3s';
        modal.style.opacity = '0';
        setTimeout(() => { 
            if (modal && modal.parentNode) {
                modal.parentNode.removeChild(modal);
            }
            if (typeof onCancel === 'function') onCancel();
        }, 300);
    };
}

// Simple success message function (no buttons, auto-dismiss)
function showSimpleSuccessMessage(message) {
    // Remove any existing notification
    const existingToast = document.getElementById('scheduleToast');
    if (existingToast) {
        existingToast.remove();
    }
    
    const notification = document.createElement('div');
    notification.id = 'scheduleToast';
    notification.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        z-index: 9999;
        display: flex;
        align-items: center;
        justify-content: center;
        pointer-events: none;
        background: rgba(255,255,255,0.18);
    `;
    
    notification.innerHTML = `
        <div style="background:rgba(255,255,255,0.7); color:#2563eb; min-width:220px; max-width:90vw; padding:20px 36px; border-radius:16px; box-shadow:0 4px 32px rgba(37,99,235,0.10); font-size:1.1rem; font-weight:500; text-align:center; border:1.5px solid #2563eb; display:flex; align-items:center; gap:12px; pointer-events:auto;">
            <span style="font-size:2rem;line-height:1;color:#2563eb;">&#10003;</span>
            <span>${message}</span>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    // Auto-dismiss after 1.2 seconds with fade out
    setTimeout(() => {
        notification.style.transition = 'opacity 0.3s';
        notification.style.opacity = '0';
        setTimeout(() => {
            if (notification && notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 300);
    }, 1200);
}

// Success modal function
function showSuccessModal(message, title = 'Success') {
    const modalId = 'successModal_' + Date.now();
    const modal = document.createElement('div');
    modal.id = modalId;
    modal.style.cssText = 'position:fixed;top:0;left:0;width:100vw;height:100vh;z-index:9999;display:flex;align-items:center;justify-content:center;pointer-events:none;background:rgba(255,255,255,0.18);';
    
    modal.innerHTML = `
        <div style='background:rgba(255,255,255,0.95); color:#059669; min-width:300px; max-width:90vw; padding:24px 32px; border-radius:16px; box-shadow:0 4px 32px rgba(5,150,105,0.15); font-size:1.1rem; font-weight:500; text-align:center; border:1.5px solid #059669; display:flex; flex-direction:column; gap:16px; pointer-events:auto;'>
            <div style='display:flex; align-items:center; justify-content:center; gap:12px;'>
                <span style='font-size:2rem;line-height:1;color:#059669;'>&#10003;</span>
                <span style='color:#374151; font-weight:600;'>${title}</span>
            </div>
            <div style='color:#374151; margin:8px 0;'>${message}</div>
            <div style='display:flex; gap:12px; justify-content:center;'>
                <button id='okayBtn' style='background:#059669; color:white; padding:8px 16px; border-radius:8px; font-weight:500; border:none; cursor:pointer;'>Okay</button>
                <button id='cancelBtn' style='background:#f3f4f6; color:#374151; padding:8px 16px; border-radius:8px; font-weight:500; border:1px solid #d1d5db; cursor:pointer;'>Cancel</button>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    
    const okayBtn = modal.querySelector('#okayBtn');
    const cancelBtn = modal.querySelector('#cancelBtn');
    
    const closeModal = function() {
        // Much faster success modal close - reduced from 300ms to 50ms
        modal.style.transition = 'opacity 0.05s';
        modal.style.opacity = '0';
        setTimeout(() => { 
            if (modal && modal.parentNode) {
                modal.parentNode.removeChild(modal);
            }
        }, 50);
    };
    
    okayBtn.onclick = closeModal;
    cancelBtn.onclick = closeModal;
}

// Error modal function
function showErrorModal(message, title = 'Error') {
    const modalId = 'errorModal_' + Date.now();
    const modal = document.createElement('div');
    modal.id = modalId;
    modal.style.cssText = 'position:fixed;top:0;left:0;width:100vw;height:100vh;z-index:9999;display:flex;align-items:center;justify-content:center;pointer-events:none;background:rgba(255,255,255,0.18);';
    
    modal.innerHTML = `
        <div style='background:rgba(255,255,255,0.95); color:#dc2626; min-width:300px; max-width:90vw; padding:24px 32px; border-radius:16px; box-shadow:0 4px 32px rgba(220,38,38,0.15); font-size:1.1rem; font-weight:500; text-align:center; border:1.5px solid #dc2626; display:flex; flex-direction:column; gap:16px; pointer-events:auto;'>
            <div style='display:flex; align-items:center; justify-content:center; gap:12px;'>
                <span style='font-size:2rem;line-height:1;color:#dc2626;'>&#10060;</span>
                <span style='color:#374151; font-weight:600;'>${title}</span>
            </div>
            <div style='color:#374151; margin:8px 0;'>${message}</div>
            <div style='display:flex; gap:12px; justify-content:center;'>
                <button id='okayBtn' style='background:#dc2626; color:white; padding:8px 16px; border-radius:8px; font-weight:500; border:none; cursor:pointer;'>Okay</button>
                <button id='cancelBtn' style='background:#f3f4f6; color:#374151; padding:8px 16px; border-radius:8px; font-weight:500; border:1px solid #d1d5db; cursor:pointer;'>Cancel</button>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    
    const okayBtn = modal.querySelector('#okayBtn');
    const cancelBtn = modal.querySelector('#cancelBtn');
    
    const closeModal = function() {
        // Much faster error modal close - reduced from 300ms to 50ms
        modal.style.transition = 'opacity 0.05s';
        modal.style.opacity = '0';
        setTimeout(() => { 
            if (modal && modal.parentNode) {
                modal.parentNode.removeChild(modal);
            }
        }, 50);
    };
    
    okayBtn.onclick = closeModal;
    cancelBtn.onclick = closeModal;
}

approveBtns.forEach(btn => btn.addEventListener('click', function() {
    const row = btn.closest('tr');
    const name = row.children[0].textContent.trim(); // Patient name is in column 0
    const date = row.children[1].textContent.trim(); // Date is in column 1
    const time = row.children[2].textContent.trim(); // Time is in column 2
    const reason = row.children[3].textContent.trim(); // Reason is in column 3
    showConfirmModal('Are you sure you want to approve this appointment?', function() {
        fetch('', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({ action: 'approve', date, time, reason, name })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                const statusCell = row.children[4].querySelector('span'); // Status is in column 4 (index 4)
                statusCell.textContent = 'Approved';
                statusCell.className = 'inline-block px-2 py-1 rounded bg-green-100 text-green-800 text-xs';
                showSuccessModal('Appointment approved successfully!', 'Success');
            } else {
                showErrorModal('Failed to approve appointment.', 'Error');
            }
        });
    });
}));

declineBtns.forEach(btn => btn.addEventListener('click', function() {
    const row = btn.closest('tr');
    const name = row.children[0].textContent.trim(); // Patient name is in column 0
    const date = row.children[1].textContent.trim(); // Date is in column 1
    const time = row.children[2].textContent.trim(); // Time is in column 2
    const reason = row.children[3].textContent.trim(); // Reason is in column 3
    showConfirmModal('Are you sure you want to decline this appointment?', function() {
        fetch('', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({ action: 'decline', date, time, reason, name })
        })
        .then(res => res.json())
        .then (data => {
            if (data.success) {
                const statusCell = row.children[4].querySelector('span'); // Status is in column 4 (index 4)
                statusCell.textContent = 'Declined';
                statusCell.className = 'inline-block px-2 py-1 rounded bg-red-100 text-red-800 text-xs';
                showSuccessModal('Appointment declined successfully!', 'Success');
            } else {
                showErrorModal('Failed to decline appointment.', 'Error');
            }
        });
    });
}));

// Custom reschedule modal function
function showRescheduleModal(oldDate, oldTime, onConfirm) {
    const modalId = 'rescheduleModal_' + Date.now();
    const modal = document.createElement('div');
    modal.id = modalId;
    modal.style.cssText = 'position:fixed;top:0;left:0;width:100vw;height:100vh;z-index:9999;display:flex;align-items:center;justify-content:center;pointer-events:none;background:rgba(255,255,255,0.18);';
    
    // If oldTime is a range (e.g., "12:28-12:58"), use only the start time
    let singleTime = oldTime;
    if (oldTime && oldTime.includes('-')) {
        singleTime = oldTime.split('-')[0].trim();
    }
    modal.innerHTML = `
        <div style='background:rgba(255,255,255,0.95); color:#2563eb; min-width:350px; max-width:90vw; padding:24px 32px; border-radius:16px; box-shadow:0 4px 32px rgba(37,99,235,0.15); font-size:1.1rem; font-weight:500; text-align:center; border:1.5px solid #2563eb; display:flex; flex-direction:column; gap:16px; pointer-events:auto;'>
            <div style='display:flex; align-items:center; justify-content:center; gap:12px;'>
                <span style='font-size:2rem;line-height:1;color:#2563eb;'>&#8505;</span>
                <span style='color:#374151; font-weight:600;'>Reschedule Appointment</span>
            </div>
            <div style='text-align:left;'>
                <label style='display:block; margin-bottom:8px; color:#374151; font-weight:500;'>New Date:</label>
                <input id='modalNewDate' type='date' value='${oldDate}' style='width:100%; padding:8px 12px; border:1px solid #d1d5db; border-radius:8px; margin-bottom:16px;'>
                <label style='display:block; margin-bottom:8px; color:#374151; font-weight:500;'>New Time:</label>
                <input id='modalNewTime' type='time' value='${singleTime}' style='width:100%; padding:8px 12px; border:1px solid #d1d5db; border-radius:8px; margin-bottom:16px;'>
            </div>
            <div style='display:flex; gap:12px; justify-content:center;'>
                <button id='confirmRescheduleBtn' style='background:#2563eb; color:white; padding:8px 16px; border-radius:8px; font-weight:500; border:none; cursor:pointer;'>Reschedule</button>
                <button id='cancelRescheduleBtn' style='background:#f3f4f6; color:#374151; padding:8px 16px; border-radius:8px; font-weight:500; border:1px solid #d1d5db; cursor:pointer;'>Cancel</button>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    
    const confirmBtn = modal.querySelector('#confirmRescheduleBtn');
    const cancelBtn = modal.querySelector('#cancelRescheduleBtn');
    const newDateInput = modal.querySelector('#modalNewDate');
    const newTimeInput = modal.querySelector('#modalNewTime');
    
    confirmBtn.onclick = function() {
        const newDate = newDateInput.value;
        const newTime = newTimeInput.value;
        
        if (!newDate || !newTime) {
            showErrorModal('Please fill in both date and time.', 'Error');
            return;
        }
        
        // Much faster modal close - reduced from 300ms to 100ms
        modal.style.transition = 'opacity 0.1s';
        modal.style.opacity = '0';
        setTimeout(() => { 
            if (modal && modal.parentNode) {
                modal.parentNode.removeChild(modal);
            }
            if (typeof onConfirm === 'function') onConfirm(newDate, newTime);
        }, 100);
    };
    
    cancelBtn.onclick = function() {
        // Much faster modal close - reduced from 300ms to 100ms
        modal.style.transition = 'opacity 0.1s';
        modal.style.opacity = '0';
        setTimeout(() => { 
            if (modal && modal.parentNode) {
                modal.parentNode.removeChild(modal);
            }
        }, 100);
    };
}

reschedBtns.forEach(btn => btn.addEventListener('click', function() {
    const row = btn.closest('tr');
    // Table columns: 0=Name, 1=Date, 2=Time, 3=Reason, 4=Status, 5=Actions
    const name = row.children[0].textContent.trim(); // Name
    const oldDate = row.children[1].textContent.trim(); // Date
    const oldTime = row.children[2].textContent.trim(); // Time
    const reason = row.children[3].textContent.trim(); // Reason
    const email = row.getAttribute('data-email');

    showRescheduleModal(oldDate, oldTime, function(newDate, newTime) {
        // Send to server first
        fetch('', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({ action: 'reschedule', name, oldDate, oldTime, reason, newDate, newTime })
        })
        .then(res => res.text())
        .then(text => {
            let data;
            try {
                data = JSON.parse(text);
            } catch (e) {
                // If the response is not valid JSON but the appointment is updated, treat as success
                row.children[1].textContent = newDate; // Date
                row.children[2].textContent = newTime; // Time
                const statusCell = row.querySelector('td:nth-child(5) span');
                if (statusCell) {
                    statusCell.textContent = 'Rescheduled';
                    statusCell.className = 'inline-block px-2 py-1 rounded bg-blue-100 text-blue-800 text-xs';
                }
                showSuccessModal('Appointment rescheduled successfully!', 'Success');
                return;
            }
            const statusCell = row.querySelector('td:nth-child(5) span');
            if (data.success) {
                row.children[1].textContent = newDate; // Date
                row.children[2].textContent = newTime; // Time
                if (statusCell) {
                    statusCell.textContent = 'Rescheduled';
                    statusCell.className = 'inline-block px-2 py-1 rounded bg-blue-100 text-blue-800 text-xs';
                }
                showSuccessModal('Appointment rescheduled successfully!', 'Success');
            } else {
                if (statusCell) {
                    statusCell.textContent = 'Pending';
                    statusCell.className = 'inline-block px-2 py-1 rounded bg-yellow-100 text-yellow-800 text-xs';
                }
                showErrorModal('Failed to reschedule appointment: ' + (data.error || 'Unknown error'), 'Error');
            }
        })
        .catch(error => {
            const statusCell = row.querySelector('td:nth-child(5) span');
            if (statusCell) {
                statusCell.textContent = 'Pending';
                statusCell.className = 'inline-block px-2 py-1 rounded bg-yellow-100 text-yellow-800 text-xs';
            }
            showErrorModal('Network error occurred while rescheduling appointment.', 'Error');
        });
    });
}));

// View appointment button functionality
const viewBtns = document.querySelectorAll('.viewAppointmentBtn');
const viewModal = document.getElementById('appointmentViewModal');
const closeViewBtn = document.getElementById('closeViewModalBtn');
const closeViewBtnBottom = document.getElementById('closeViewModalBtnBottom');

viewBtns.forEach(btn => btn.addEventListener('click', function() {
    const name = this.dataset.name;
    const date = this.dataset.date;
    const time = this.dataset.time;
    const reason = this.dataset.reason;
    const email = this.dataset.email;
    
    // Populate modal with appointment data
    document.getElementById('viewPatientName').textContent = name;
    document.getElementById('viewPatientEmail').textContent = email;
    document.getElementById('viewAppointmentDate').textContent = date;
    document.getElementById('viewAppointmentTime').textContent = time;
    document.getElementById('viewAppointmentReason').textContent = reason;
    
    // Show modal
    viewModal.classList.remove('hidden');
}));

// Close modal functionality
function closeViewModal() {
    viewModal.classList.add('hidden');
}

closeViewBtn.addEventListener('click', closeViewModal);
closeViewBtnBottom.addEventListener('click', closeViewModal);

// Close modal when clicking outside
viewModal.addEventListener('click', function(e) {
    if (e.target === viewModal) {
        closeViewModal();
    }
});

const monthNames = [
    'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'
];
let today = new Date();
let currentMonth = today.getMonth(); // 0-based
let currentYear = today.getFullYear();

function getDoctorForDate(date) {
    const dateStr = date.getFullYear() + '-' +
        String(date.getMonth() + 1).padStart(2, '0') + '-' +
        String(date.getDate()).padStart(2, '0');
    return doctorSchedules.find(schedule => schedule.schedule_date === dateStr);
}

function renderCalendar(month, year) {
    const calendarGrid = document.getElementById('calendarGrid');
    calendarGrid.innerHTML = '';
    // Weekday headers
    const weekdays = ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'];
    weekdays.forEach(day => {
        const div = document.createElement('div');
        div.className = 'font-semibold text-gray-600';
        div.textContent = day;
        calendarGrid.appendChild(div);
    });
    // First day of month
    const firstDay = new Date(year, month, 1);
    const startDay = firstDay.getDay();
    // Days in month
    const daysInMonth = new Date(year, month+1, 0).getDate();
    // Days in prev month
    const daysInPrevMonth = new Date(year, month, 0).getDate();
    // Fill prev month
    for (let i = 0; i < startDay; i++) {
        const div = document.createElement('div');
        div.className = 'text-gray-400';
        div.textContent = daysInPrevMonth - startDay + i + 1;
        calendarGrid.appendChild(div);
    }
    
    for (let d = 1; d <= daysInMonth; d++) {
        const dateObj = new Date(year, month, d);
        const isToday = d === new Date().getDate() && month === (new Date().getMonth()) && year === new Date().getFullYear();
        let cellClass = '';
        if (isToday) cellClass += 'bg-primary text-white rounded shadow-lg ring-2 ring-primary ';
        cellClass += 'hover:bg-blue-100 hover:text-black cursor-pointer transition ';
        
        const div = document.createElement('div');
        div.className = cellClass;
        div.textContent = d;
        
        // Check if there's a doctor scheduled for this date
        const doctorSchedule = getDoctorForDate(dateObj);
        if (doctorSchedule) {
            const docDiv = document.createElement('div');
            docDiv.className = 'text-xs mt-1 font-medium text-blue-600';
            docDiv.textContent = doctorSchedule.doctor_name;
            div.appendChild(docDiv);
            
            // Add hover popup for time
            div.addEventListener('mouseenter', function(e) {
                let popup = document.createElement('div');
                popup.className = 'fixed z-50 bg-white border border-blue-300 rounded shadow-lg p-3 text-xs text-left text-gray-800';
                popup.style.top = (e.clientY + 10) + 'px';
                popup.style.left = (e.clientX + 10) + 'px';
                popup.innerHTML = `<b>${doctorSchedule.doctor_name}</b><br>Available: <span class='text-blue-600'>${doctorSchedule.schedule_time}</span>`;
                popup.id = 'doctorPopup';
                document.body.appendChild(popup);
            });
            div.addEventListener('mousemove', function(e) {
                const popup = document.getElementById('doctorPopup');
                if (popup) {
                    popup.style.top = (e.clientY + 10) + 'px';
                    popup.style.left = (e.clientX + 10) + 'px';
                }
            });
            div.addEventListener('mouseleave', function() {
                const popup = document.getElementById('doctorPopup');
                if (popup) popup.remove();
            });
        }
        
        calendarGrid.appendChild(div);
    }
    // Fill next month
    const totalCells = startDay + daysInMonth;
    for (let i = 0; i < (7 - (totalCells % 7)) % 7; i++) {
        const div = document.createElement('div');
        div.className = 'text-gray-400';
        div.textContent = i+1;
        calendarGrid.appendChild(div);
    }
    // Set month label safely
    var calendarMonthEl = document.getElementById('calendarMonth');
    if (calendarMonthEl) {
        calendarMonthEl.textContent = monthNames[month] + ' ' + year;
    }
}

var prevMonthBtn = document.getElementById('prevMonthBtn');
if (prevMonthBtn) {
    prevMonthBtn.addEventListener('click', function() {
        currentMonth--;
        if (currentMonth < 0) {
            currentMonth = 11;
            currentYear--;
        }
        renderCalendar(currentMonth, currentYear);
    });
}

var nextMonthBtn = document.getElementById('nextMonthBtn');
if (nextMonthBtn) {
    nextMonthBtn.addEventListener('click', function() {
        currentMonth++;
        if (currentMonth > 11) {
            currentMonth = 0;
            currentYear++;
        }
        renderCalendar(currentMonth, currentYear);
    });
}

renderCalendar(currentMonth, currentYear);
</script>

<?php
include '../includes/footer.php';
?>