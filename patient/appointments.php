<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
include '../includep/header.php';

// Database connection
try {
    $db = new PDO('mysql:host=localhost;dbname=clinic_management_system;charset=utf8', 'root', '');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create doctor_schedules table if not exists
    $db->exec("CREATE TABLE IF NOT EXISTS doctor_schedules (
        id INT AUTO_INCREMENT PRIMARY KEY,
        doctor_name VARCHAR(255) NOT NULL,
        schedule_date DATE NOT NULL,
        schedule_time VARCHAR(100) NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY unique_schedule (doctor_name, schedule_date, schedule_time)
    )");
    
    // Create appointments table if not exists
    $db->exec("CREATE TABLE IF NOT EXISTS appointments (
        id INT AUTO_INCREMENT PRIMARY KEY,
        student_id INT NOT NULL,
        date DATE NOT NULL,
        time VARCHAR(100) NOT NULL,
        reason VARCHAR(255) NOT NULL,
        status ENUM('pending', 'approved', 'declined', 'rescheduled', 'confirmed') DEFAULT 'pending',
        email VARCHAR(255),
        parent_email VARCHAR(255),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_student (student_id),
        INDEX idx_date (date),
        INDEX idx_status (status)
    )");
    
    // Get logged-in student ID
    $student_id = $_SESSION['student_row_id'] ?? null;
    
    // Handle appointment booking
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['date'], $_POST['time'], $_POST['reason'], $_POST['email'], $_POST['parent_email'])) {
        $date = $_POST['date'];
        $time = $_POST['time'];
        $reason = $_POST['reason'];
        $email = $_POST['email'];
        $parent_email = $_POST['parent_email'];
        
        // Check if appointment already exists for this date and time
        $check_stmt = $db->prepare('SELECT id FROM appointments WHERE date = ? AND time = ? AND status != "declined"');
        $check_stmt->execute([$date, $time]);
        
        if ($check_stmt->fetch()) {
            $error_message = "This time slot is already booked. Please choose a different time.";
        } else {
            // Insert new appointment
            $insert_stmt = $db->prepare('INSERT INTO appointments (student_id, date, time, reason, status, email, parent_email) VALUES (?, ?, ?, ?, ?, ?, ?)');
            $status = 'pending';
            $result = $insert_stmt->execute([$student_id, $date, $time, $reason, $status, $email, $parent_email]);
            
            if ($result) {
                // Log the successful insertion
                error_log("Appointment booked successfully for student_id: $student_id, date: $date, time: $time");
            } else {
                error_log("Failed to book appointment for student_id: $student_id");
            }
            
            // Notify staff of new appointment
            $patient_name = '';
            $name_stmt = $db->prepare('SELECT name FROM imported_patients WHERE id = ? LIMIT 1');
            $name_stmt->execute([$student_id]);
            $patient = $name_stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($patient) {
                $patient_name = $patient['name'];
                $notif_msg = "A new appointment has been booked by <b>" . htmlspecialchars($patient_name) . "</b> for <b>" . htmlspecialchars($date) . "</b> at <b>" . htmlspecialchars($time) . "</b> (" . htmlspecialchars($reason) . ").";
                $notif_type = 'appointment';
                $notif_stmt = $db->prepare('INSERT INTO notifications (student_id, message, type, created_at) VALUES (NULL, ?, ?, NOW())');
                $notif_stmt->execute([$notif_msg, $notif_type]);
            }
            
            $success_message = "Appointment booked successfully! Please wait for staff approval.";
        }
    }
    
    // Fetch doctor schedules (only future dates)
    $doctor_schedules = [];
    try {
        $schedule_stmt = $db->prepare('SELECT doctor_name, schedule_date, schedule_time FROM doctor_schedules WHERE schedule_date >= CURDATE() ORDER BY schedule_date ASC, schedule_time ASC');
        $schedule_stmt->execute();
        $doctor_schedules = $schedule_stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        // Ignore errors for schedules
    }
    
    // Fetch appointments for this student
    $appointments = [];
    if ($student_id) {
        $appt_stmt = $db->prepare('SELECT date, time, reason, status, email, parent_email FROM appointments WHERE student_id = ? ORDER BY date DESC, time DESC');
        $appt_stmt->execute([$student_id]);
        $appointments = $appt_stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Fetch all booked appointments (to filter out unavailable slots)
    $booked_appointments = [];
    try {
        $booked_stmt = $db->prepare('SELECT date, time FROM appointments WHERE status != "declined" ORDER BY date ASC, time ASC');
        $booked_stmt->execute();
        $booked_appointments = $booked_stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        // Ignore errors for booked appointments
    }
    
} catch (PDOException $e) {
    $error_message = "Database connection failed: " . $e->getMessage();
    $doctor_schedules = [];
    $appointments = [];
}

// Separate appointments for pending and approved
$pending_appts = array_filter($appointments, function($appt) {
    return $appt['status'] === 'pending';
});

$approved_appts = array_filter($appointments, function($appt) {
    return in_array($appt['status'], ['approved', 'confirmed', 'declined', 'rescheduled']);
});

// Pagination for My Pending and Approved Appointments (exact logic from history.php)
$pending_records_per_page = 10;
$pending_page = isset($_GET['pending_page']) ? (int)$_GET['pending_page'] : 1;
$pending_page = max($pending_page, 1);
$pending_offset = ($pending_page - 1) * $pending_records_per_page;
$pending_total = count($pending_appts);
$pending_total_pages = ceil($pending_total / $pending_records_per_page);
$pending_display = array_slice($pending_appts, $pending_offset, $pending_records_per_page);

$approved_records_per_page = 10;
$approved_page = isset($_GET['approved_page']) ? (int)$_GET['approved_page'] : 1;
$approved_page = max($approved_page, 1);
$approved_offset = ($approved_page - 1) * $approved_records_per_page;
$approved_total = count($approved_appts);
$approved_total_pages = ceil($approved_total / $approved_records_per_page);
$approved_display = array_slice($approved_appts, $approved_offset, $approved_records_per_page);
?>

<main class="flex-1 overflow-y-auto bg-gray-50 p-6 ml-16 md:ml-64 mt-[56px]">
        <h2 class="text-2xl font-bold mb-6 text-gray-800">My Appointments</h2>
        
        <?php if (isset($success_message)): ?>
            <?php showSuccessModal(htmlspecialchars($success_message), 'Appointment Booked'); ?>
        <?php endif; ?>
        
        <?php if (isset($error_message)): ?>
            <?php showErrorModal(htmlspecialchars($error_message), 'Error'); ?>
        <?php endif; ?>
        
        <!-- Doctor's Availability Calendar -->
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
        
        <!-- Book Appointment Modal -->
        <div id="bookApptModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40 hidden">
            <div class="bg-white rounded shadow-lg p-8 max-w-xl w-full relative">
                <button id="closeModalBtn" class="absolute top-2 right-2 text-gray-400 hover:text-gray-700 text-2xl">&times;</button>
                <h3 class="text-lg font-semibold mb-4">Book an Appointment</h3>
                <form id="bookApptForm" method="POST" autocomplete="off">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                        <input type="date" name="date" id="modalDate" class="w-full border border-gray-300 rounded px-3 py-2 text-sm" required readonly />
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Time</label>
                        <select name="time" id="modalTime" class="w-full border border-gray-300 rounded px-3 py-2 text-sm" required>
                            <option value="" selected disabled>Select time</option>
                        </select>
                    </div>
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Reason</label>
                        <input type="text" name="reason" class="w-full border border-gray-300 rounded px-3 py-2 text-sm" placeholder="Enter reason" required />
                    </div>
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Your Email Address</label>
                        <input type="email" name="email" class="w-full border border-gray-300 rounded px-3 py-2 text-sm" placeholder="Enter your email address" required />
                    </div>
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Parent's Email Address</label>
                        <input type="email" name="parent_email" class="w-full border border-gray-300 rounded px-3 py-2 text-sm" placeholder="Enter your parent's email address" required />
                    </div>
                    <button type="submit" id="bookApptBtn" class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700 transition-colors">Book Appointment</button>
                </form>
            </div>
        </div>
        

        
        <!-- My Appointments Table: Pending -->
        <div class="bg-white rounded shadow p-6 mb-8">
            <h3 class="text-lg font-semibold mb-4">My Pending Appointments</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left font-semibold text-gray-600">Date</th>
                            <th class="px-4 py-2 text-left font-semibold text-gray-600">Time</th>
                            <th class="px-4 py-2 text-left font-semibold text-gray-600">Reason</th>
                            <th class="px-4 py-2 text-left font-semibold text-gray-600">Email</th>
                            <th class="px-4 py-2 text-left font-semibold text-gray-600">Status</th>
                            <th class="px-4 py-2 text-center font-semibold text-gray-600">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (!empty($pending_display)) {
                            foreach ($pending_display as $appt) {
                                echo "<tr class='hover:bg-blue-50 transition'>";
                                echo "<td class='px-4 py-2'>" . htmlspecialchars($appt['date']) . "</td>";
                                echo "<td class='px-4 py-2'>" . htmlspecialchars($appt['time']) . "</td>";
                                echo "<td class='px-4 py-2'>" . htmlspecialchars($appt['reason']) . "</td>";
                                echo "<td class='px-4 py-2'>" . htmlspecialchars($appt['email']) . "</td>";
                                echo "<td class='px-4 py-2'><span class='inline-block px-2 py-1 rounded bg-yellow-100 text-yellow-800 text-xs'>Pending</span></td>";
                                echo "<td class='px-4 py-2 text-center'><button class='cancelBtn px-2 py-1 text-xs bg-red-500 text-white rounded hover:bg-red-600 mr-1' data-date='" . htmlspecialchars($appt['date']) . "' data-time='" . htmlspecialchars($appt['time']) . "' data-reason='" . htmlspecialchars($appt['reason']) . "'>Cancel</button></td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='6' class='px-4 py-2 text-center text-gray-400'>No pending appointments found.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            <?php if ($pending_total > 0): ?>
            <div class="flex justify-between items-center mt-6">
                <div class="text-sm text-gray-600">
                    <?php 
                    $pending_start = $pending_offset + 1;
                    $pending_end = min($pending_offset + $pending_records_per_page, $pending_total);
                    ?>
                    Showing <?php echo $pending_start; ?> to <?php echo $pending_end; ?> of <?php echo $pending_total; ?> entries
                </div>
                <?php if ($pending_total_pages > 1): ?>
                <nav class="flex justify-end items-center -space-x-px" aria-label="Pagination">
                    <!-- Previous Button -->
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
                    <!-- Page Numbers -->
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
                    <!-- Next Button -->
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
        
        <!-- My Appointments Table: Approved -->
        <div class="bg-white rounded shadow p-6">
            <h3 class="text-lg font-semibold mb-4">My Approved Appointments</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left font-semibold text-gray-600">Date</th>
                            <th class="px-4 py-2 text-left font-semibold text-gray-600">Time</th>
                            <th class="px-4 py-2 text-left font-semibold text-gray-600">Reason</th>
                            <th class="px-4 py-2 text-left font-semibold text-gray-600">Email</th>
                            <th class="px-4 py-2 text-left font-semibold text-gray-600">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (!empty($approved_display)) {
                            foreach ($approved_display as $appt) {
                                echo "<tr class='hover:bg-green-50 transition'>";
                                echo "<td class='px-4 py-2'>" . htmlspecialchars($appt['date']) . "</td>";
                                echo "<td class='px-4 py-2'>" . htmlspecialchars($appt['time']) . "</td>";
                                echo "<td class='px-4 py-2'>" . htmlspecialchars($appt['reason']) . "</td>";
                                echo "<td class='px-4 py-2'>" . htmlspecialchars($appt['email']) . "</td>";
                                echo "<td class='px-4 py-2'>";
                                if ($appt['status'] === 'approved' || $appt['status'] === 'confirmed') {
                                    echo "<span class='inline-block px-2 py-1 rounded bg-green-100 text-green-800 text-xs'>Approved</span>";
                                    echo "<span class='block text-xs text-green-700 mt-1'>Please wait for this day and go to the clinic.</span>";
                                } elseif ($appt['status'] === 'declined') {
                                    echo "<span class='inline-block px-2 py-1 rounded bg-red-100 text-red-800 text-xs'>Declined</span>";
                                } elseif ($appt['status'] === 'rescheduled') {
                                    echo "<span class='inline-block px-2 py-1 rounded bg-blue-100 text-blue-800 text-xs'>Rescheduled</span>";
                                    echo "<span class='block text-xs text-blue-700 mt-1'>Please wait for this day and go to the clinic.</span>";
                                } else {
                                    echo "<span class='inline-block px-2 py-1 rounded bg-gray-100 text-gray-800 text-xs'>" . htmlspecialchars(ucfirst($appt['status'])) . "</span>";
                                }
                                echo "</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='5' class='px-4 py-2 text-center text-gray-400'>No done appointments found.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            <?php if ($approved_total > 0): ?>
            <div class="flex justify-between items-center mt-6">
                <div class="text-sm text-gray-600">
                    <?php 
                    $approved_start = $approved_offset + 1;
                    $approved_end = min($approved_offset + $approved_records_per_page, $approved_total);
                    ?>
                    Showing <?php echo $approved_start; ?> to <?php echo $approved_end; ?> of <?php echo $approved_total; ?> entries
                </div>
                <?php if ($approved_total_pages > 1): ?>
                <nav class="flex justify-end items-center -space-x-px" aria-label="Pagination">
                    <!-- Previous Button -->
                    <?php if ($approved_page > 1): ?>
                        <a href="?approved_page=<?php echo $approved_page - 1; ?>" class="min-h-9.5 min-w-9.5 py-2 px-2.5 inline-flex justify-center items-center gap-x-1.5 text-sm first:rounded-s-lg last:rounded-e-lg border border-gray-200 text-gray-800 hover:bg-gray-100 focus:outline-hidden focus:bg-gray-100" aria-label="Previous">
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
                    $approved_start_page = max(1, $approved_page - 2);
                    $approved_end_page = min($approved_total_pages, $approved_page + 2);
                    if ($approved_start_page > 1): ?>
                        <a href="?approved_page=1" class="min-h-9.5 min-w-9.5 flex justify-center items-center border border-gray-200 text-gray-800 hover:bg-gray-100 py-2 px-3 text-sm first:rounded-s-lg last:rounded-e-lg focus:outline-hidden focus:bg-gray-100">1</a>
                        <?php if ($approved_start_page > 2): ?>
                            <span class="min-h-9.5 min-w-9.5 flex justify-center items-center border border-gray-200 text-gray-800 py-2 px-3 text-sm">...</span>
                        <?php endif; ?>
                    <?php endif; ?>
                    <?php for ($i = $approved_start_page; $i <= $approved_end_page; $i++): ?>
                        <?php if ($i == $approved_page): ?>
                            <button type="button" class="min-h-9.5 min-w-9.5 flex justify-center items-center bg-gray-200 text-gray-800 border border-gray-200 py-2 px-3 text-sm first:rounded-s-lg last:rounded-e-lg focus:outline-hidden focus:bg-gray-300" aria-current="page"><?php echo $i; ?></button>
                        <?php else: ?>
                            <a href="?approved_page=<?php echo $i; ?>" class="min-h-9.5 min-w-9.5 flex justify-center items-center border border-gray-200 text-gray-800 hover:bg-gray-100 py-2 px-3 text-sm first:rounded-s-lg last:rounded-e-lg focus:outline-hidden focus:bg-gray-100"><?php echo $i; ?></a>
                        <?php endif; ?>
                    <?php endfor; ?>
                    <?php if ($approved_end_page < $approved_total_pages): ?>
                        <?php if ($approved_end_page < $approved_total_pages - 1): ?>
                            <span class="min-h-9.5 min-w-9.5 flex justify-center items-center border border-gray-200 text-gray-800 py-2 px-3 text-sm">...</span>
                        <?php endif; ?>
                        <a href="?approved_page=<?php echo $approved_total_pages; ?>" class="min-h-9.5 min-w-9.5 flex justify-center items-center border border-gray-200 text-gray-800 hover:bg-gray-100 py-2 px-3 text-sm first:rounded-s-lg last:rounded-e-lg focus:outline-hidden focus:bg-gray-100"><?php echo $approved_total_pages; ?></a>
                    <?php endif; ?>
                    <!-- Next Button -->
                    <?php if ($approved_page < $approved_total_pages): ?>
                        <a href="?approved_page=<?php echo $approved_page + 1; ?>" class="min-h-9.5 min-w-9.5 py-2 px-2.5 inline-flex justify-center items-center gap-x-1.5 text-sm first:rounded-s-lg last:rounded-e-lg border border-gray-200 text-gray-800 hover:bg-gray-100 focus:outline-hidden focus:bg-gray-100" aria-label="Next">
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

<script>
// Doctor schedules data for calendar
const doctorSchedules = <?php echo json_encode($doctor_schedules); ?>;
const bookedAppointments = <?php echo json_encode($booked_appointments); ?>;

// Function to convert 24-hour format to 12-hour format for display
function convertTimeFormat(timeRange) {
    const parts = timeRange.split('-');
    if (parts.length === 2) {
        const startTime = parts[0].trim();
        const endTime = parts[1].trim();
        
        // Convert start time
        const startHour = parseInt(startTime.split(':')[0]);
        const startMinute = startTime.split(':')[1];
        const startAMPM = startHour >= 12 ? 'PM' : 'AM';
        const startDisplayHour = startHour === 0 ? 12 : (startHour > 12 ? startHour - 12 : startHour);
        
        // Convert end time
        const endHour = parseInt(endTime.split(':')[0]);
        const endMinute = endTime.split(':')[1];
        const endAMPM = endHour >= 12 ? 'PM' : 'AM';
        const endDisplayHour = endHour === 0 ? 12 : (endHour > 12 ? endHour - 12 : endHour);
        
        return `${startDisplayHour}:${startMinute} ${startAMPM} - ${endDisplayHour}:${endMinute} ${endAMPM}`;
    }
    return timeRange; // Return original if format is unexpected
}

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

// Cancel appointment functionality
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.cancelBtn').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            if (btn.disabled) return;
            
            const date = this.dataset.date;
            const time = this.dataset.time;
            const reason = this.dataset.reason;
            
            showConfirmModal('Are you sure you want to cancel this appointment?', function() {
                // Okay pressed: proceed with cancel
                fetch('profile_cancel_appointment.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        date,
                        time,
                        reason
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        // Update status cell in the table
                        const statusCell = btn.closest('tr').querySelector('td:nth-child(5) span');
                        if (statusCell) {
                            statusCell.textContent = 'Cancelled';
                            statusCell.className = 'inline-block px-2 py-1 rounded bg-red-100 text-red-800 text-xs';
                        }
                        btn.disabled = true;
                        btn.classList.add('opacity-50', 'cursor-not-allowed');
                        showSuccessModal('Appointment cancelled successfully!', 'Success');
                    } else {
                        showErrorModal('Failed to cancel appointment.', 'Error');
                    }
                });
            });
        });
    });
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
            // Count available slots for this doctor and date
            const dateStr = `${year}-${(month + 1).toString().padStart(2, '0')}-${d.toString().padStart(2, '0')}`;
            const doctorScheduleEntry = doctorSchedules.find(schedule => 
                schedule.doctor_name === doctorSchedule.doctor_name && 
                schedule.schedule_date === dateStr
            );
            
            // Generate 10 slots from the doctor schedule
            let allSlots = [];
            if (doctorScheduleEntry) {
                const timeRange = doctorScheduleEntry.schedule_time;
                const timeParts = timeRange.split('-');
                if (timeParts.length === 2) {
                    const startTime = timeParts[0].trim();
                    const endTime = timeParts[1].trim();
                    
                    // Parse start time
                    const startHour = parseInt(startTime.split(':')[0]);
                    const startMinute = parseInt(startTime.split(':')[1]);
                    
                    // Generate 10 slots of 30 minutes each
                    for (let i = 0; i < 10; i++) {
                        const slotStartHour = startHour + Math.floor((startMinute + i * 30) / 60);
                        const slotStartMinute = (startMinute + i * 30) % 60;
                        const slotEndHour = startHour + Math.floor((startMinute + (i + 1) * 30) / 60);
                        const slotEndMinute = (startMinute + (i + 1) * 30) % 60;
                        
                        const slotStart = `${slotStartHour.toString().padStart(2, '0')}:${slotStartMinute.toString().padStart(2, '0')}`;
                        const slotEnd = `${slotEndHour.toString().padStart(2, '0')}:${slotEndMinute.toString().padStart(2, '0')}`;
                        
                        allSlots.push({
                            schedule_time: `${slotStart}-${slotEnd}`,
                            doctor_name: doctorSchedule.doctor_name,
                            schedule_date: dateStr
                        });
                    }
                }
            }
            
            // Filter out booked slots
            const availableSlots = allSlots.filter(slot => {
                const isBooked = bookedAppointments.some(booked => 
                    booked.date === dateStr && booked.time === slot.schedule_time
                );
                return !isBooked;
            });
            
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
                
                // Count available slots for this doctor and date
                const dateStr = `${year}-${(month + 1).toString().padStart(2, '0')}-${d.toString().padStart(2, '0')}`;
                const doctorScheduleEntry = doctorSchedules.find(schedule => 
                    schedule.doctor_name === doctorSchedule.doctor_name && 
                    schedule.schedule_date === dateStr
                );
                
                // Generate 10 slots from the doctor schedule
                let allSlots = [];
                if (doctorScheduleEntry) {
                    const timeRange = doctorScheduleEntry.schedule_time;
                    const timeParts = timeRange.split('-');
                    if (timeParts.length === 2) {
                        const startTime = timeParts[0].trim();
                        const endTime = timeParts[1].trim();
                        
                        // Parse start time
                        const startHour = parseInt(startTime.split(':')[0]);
                        const startMinute = parseInt(startTime.split(':')[1]);
                        
                        // Generate 10 slots of 30 minutes each
                        for (let i = 0; i < 10; i++) {
                            const slotStartHour = startHour + Math.floor((startMinute + i * 30) / 60);
                            const slotStartMinute = (startMinute + i * 30) % 60;
                            const slotEndHour = startHour + Math.floor((startMinute + (i + 1) * 30) / 60);
                            const slotEndMinute = (startMinute + (i + 1) * 30) % 60;
                            
                            const slotStart = `${slotStartHour.toString().padStart(2, '0')}:${slotStartMinute.toString().padStart(2, '0')}`;
                            const slotEnd = `${slotEndHour.toString().padStart(2, '0')}:${slotEndMinute.toString().padStart(2, '0')}`;
                            
                            allSlots.push({
                                schedule_time: `${slotStart}-${slotEnd}`,
                                doctor_name: doctorSchedule.doctor_name,
                                schedule_date: dateStr
                            });
                        }
                    }
                }
                
                // Filter out booked slots
                const availableSlots = allSlots.filter(slot => {
                    const isBooked = bookedAppointments.some(booked => 
                        booked.date === dateStr && booked.time === slot.schedule_time
                    );
                    return !isBooked;
                });
                
                // Convert time format for display
                const timeRange = doctorSchedule.schedule_time;
                const displayTime = convertTimeFormat(timeRange);
                
                popup.innerHTML = `<b>${doctorSchedule.doctor_name}</b><br>Available: <span class='text-blue-600'>${displayTime}</span><br>Slots: <span class='text-green-600'>${availableSlots.length} appointment slots</span>`;
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
            
            // Add click event to open modal and prefill date/time
            div.addEventListener('click', function() {
                // Prevent booking for past dates
                const today = new Date();
                today.setHours(0, 0, 0, 0);
                const clickedDate = new Date(year, month, d);
                if (clickedDate < today) {
                    showErrorModal('You cannot book an appointment for a past date.', 'Error');
                    return;
                }
                
                const modal = document.getElementById('bookApptModal');
                modal.classList.remove('hidden');
                
                // Set date in modal
                const modalDate = document.getElementById('modalDate');
                const modalTime = document.getElementById('modalTime');
                
                // Format date as yyyy-mm-dd
                const mm = (month + 1).toString().padStart(2, '0');
                const dd = d.toString().padStart(2, '0');
                modalDate.value = `${year}-${mm}-${dd}`;
                
                // Set time automatically based on doctor schedule
                modalTime.innerHTML = '';
                
                // Get doctor schedule for this date
                const dateStr = `${year}-${(month + 1).toString().padStart(2, '0')}-${d.toString().padStart(2, '0')}`;
                const doctorScheduleEntry = doctorSchedules.find(schedule => 
                    schedule.doctor_name === doctorSchedule.doctor_name && 
                    schedule.schedule_date === dateStr
                );
                
                // Generate 10 slots from the doctor schedule
                let allSlots = [];
                if (doctorScheduleEntry) {
                    const timeRange = doctorScheduleEntry.schedule_time;
                    const timeParts = timeRange.split('-');
                    if (timeParts.length === 2) {
                        const startTime = timeParts[0].trim();
                        const endTime = timeParts[1].trim();
                        
                        // Parse start time
                        const startHour = parseInt(startTime.split(':')[0]);
                        const startMinute = parseInt(startTime.split(':')[1]);
                        
                        // Generate 10 slots of 30 minutes each
                        for (let i = 0; i < 10; i++) {
                            const slotStartHour = startHour + Math.floor((startMinute + i * 30) / 60);
                            const slotStartMinute = (startMinute + i * 30) % 60;
                            const slotEndHour = startHour + Math.floor((startMinute + (i + 1) * 30) / 60);
                            const slotEndMinute = (startMinute + (i + 1) * 30) % 60;
                            
                            const slotStart = `${slotStartHour.toString().padStart(2, '0')}:${slotStartMinute.toString().padStart(2, '0')}`;
                            const slotEnd = `${slotEndHour.toString().padStart(2, '0')}:${slotEndMinute.toString().padStart(2, '0')}`;
                            
                            allSlots.push({
                                schedule_time: `${slotStart}-${slotEnd}`,
                                doctor_name: doctorSchedule.doctor_name,
                                schedule_date: dateStr
                            });
                        }
                    }
                }
                
                // Filter out booked slots
                const availableSlots = allSlots.filter(slot => {
                    const isBooked = bookedAppointments.some(booked => 
                        booked.date === dateStr && booked.time === slot.schedule_time
                    );
                    return !isBooked;
                });
                
                console.log('Available slots for', dateStr, ':', availableSlots);
                
                // Auto-display the first available slot
                if (availableSlots.length > 0) {
                    const firstSlot = availableSlots[0];
                    
                    // Create and add the option
                    const option = document.createElement('option');
                    option.value = firstSlot.schedule_time;
                    option.textContent = convertTimeFormat(firstSlot.schedule_time);
                    option.selected = true;
                    modalTime.appendChild(option);
                    
                    console.log('Auto-displayed time slot:', firstSlot.schedule_time);
                } else {
                    // If no slots available, show message
                    const option = document.createElement('option');
                    option.value = '';
                    option.textContent = 'No available slots';
                    option.disabled = true;
                    option.selected = true;
                    modalTime.appendChild(option);
                }
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
    
    // Set month label
    document.getElementById('calendarMonth').textContent = monthNames[month] + ' ' + year;
}

// Function to reset the booking form
function resetBookingForm() {
    const form = document.getElementById('bookApptForm');
    const modalTime = document.getElementById('modalTime');
    const modalDate = document.getElementById('modalDate');
    
    // Reset form fields
    form.reset();
    
    // Clear time dropdown
    modalTime.innerHTML = '';
    
    // Clear date
    modalDate.value = '';
}

// Modal open/close logic
document.getElementById('closeModalBtn').addEventListener('click', function() {
    document.getElementById('bookApptModal').classList.add('hidden');
    resetBookingForm();
});

// Close modal on outside click
window.addEventListener('click', function(e) {
    const modal = document.getElementById('bookApptModal');
    if (e.target === modal) {
        modal.classList.add('hidden');
        resetBookingForm();
    }
});

// Navigation buttons
document.getElementById('prevMonthBtn').addEventListener('click', function() {
    currentMonth--;
    if (currentMonth < 0) {
        currentMonth = 11;
        currentYear--;
    }
    renderCalendar(currentMonth, currentYear);
});

document.getElementById('nextMonthBtn').addEventListener('click', function() {
    currentMonth++;
    if (currentMonth > 11) {
        currentMonth = 0;
        currentYear++;
    }
    renderCalendar(currentMonth, currentYear);
});

// Handle form submission
document.getElementById('bookApptForm').addEventListener('submit', function(e) {
    // Form will submit normally to the same page
    // The PHP will process it and redirect back with success/error message
});

// Initialize calendar
renderCalendar(currentMonth, currentYear);
</script>

<?php
include '../includep/footer.php';
?>