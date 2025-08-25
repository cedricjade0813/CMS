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
                        <?php if (!empty($appointments)): ?>
                            <?php foreach ($appointments as $appt): ?>
                                <?php if ($appt['status'] === 'pending'): ?>
                                    <tr>
                                        <td class="px-4 py-2"><?php echo htmlspecialchars($appt['date']); ?></td>
                                        <td class="px-4 py-2"><?php echo htmlspecialchars($appt['time']); ?></td>
                                        <td class="px-4 py-2"><?php echo htmlspecialchars($appt['reason']); ?></td>
                                        <td class="px-4 py-2"><?php echo htmlspecialchars($appt['email']); ?></td>
                                        <td class="px-4 py-2">
                                            <span class="inline-block px-2 py-1 rounded bg-yellow-100 text-yellow-800 text-xs">Pending</span>
                                        </td>
                                        <td class="px-4 py-2 text-center">
                                            <button class="cancelBtn px-2 py-1 text-xs bg-red-500 text-white rounded hover:bg-red-600 mr-1" 
                                                    data-date="<?php echo htmlspecialchars($appt['date']); ?>"
                                                    data-time="<?php echo htmlspecialchars($appt['time']); ?>"
                                                    data-reason="<?php echo htmlspecialchars($appt['reason']); ?>">Cancel</button>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="px-4 py-2 text-center text-gray-400">No pending appointments found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- My Appointments Table: Done (Approved/Declined/Rescheduled) -->
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
                        <?php if (!empty($appointments)): ?>
                            <?php foreach ($appointments as $appt): ?>
                                <?php if ($appt['status'] === 'approved' || $appt['status'] === 'confirmed' || $appt['status'] === 'declined' || $appt['status'] === 'rescheduled'): ?>
                                    <tr>
                                        <td class="px-4 py-2"><?php echo htmlspecialchars($appt['date']); ?></td>
                                        <td class="px-4 py-2"><?php echo htmlspecialchars($appt['time']); ?></td>
                                        <td class="px-4 py-2"><?php echo htmlspecialchars($appt['reason']); ?></td>
                                        <td class="px-4 py-2"><?php echo htmlspecialchars($appt['email']); ?></td>
                                        <td class="px-4 py-2">
                                            <?php if ($appt['status'] === 'approved' || $appt['status'] === 'confirmed'): ?>
                                                <span class="inline-block px-2 py-1 rounded bg-green-100 text-green-800 text-xs">Approved</span>
                                                <span class="block text-xs text-green-700 mt-1">Please wait for this day and go to the clinic.</span>
                                            <?php elseif ($appt['status'] === 'declined'): ?>
                                                <span class="inline-block px-2 py-1 rounded bg-red-100 text-red-800 text-xs">Declined</span>
                                            <?php elseif ($appt['status'] === 'rescheduled'): ?>
                                                <span class="inline-block px-2 py-1 rounded bg-blue-100 text-blue-800 text-xs">Rescheduled</span>
                                                <span class="block text-xs text-blue-700 mt-1">Please wait for this day and go to the clinic.</span>
                                            <?php else: ?>
                                                <span class="inline-block px-2 py-1 rounded bg-gray-100 text-gray-800 text-xs"><?php echo htmlspecialchars(ucfirst($appt['status'])); ?></span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="px-4 py-2 text-center text-gray-400">No done appointments found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
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