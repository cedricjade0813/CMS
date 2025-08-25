<?php
include '../includep/header.php';
?>

<main class="flex-1 overflow-y-auto bg-gray-50 p-6 ml-16 md:ml-64 mt-[56px]">
        <h2 class="text-2xl font-bold mb-6 text-gray-800">Student Dashboard</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Dashboard Quick Stats -->
            <div class="bg-white rounded-xl shadow-lg p-8 flex flex-col gap-6 border border-gray-100">
                <h3 class="text-lg font-semibold mb-4 flex items-center gap-2"><i class="ri-bar-chart-2-line text-primary"></i> Quick Stats</h3>
                <div class="flex flex-col gap-4">
                    <div class="flex items-center gap-3">
                        <span class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-blue-100 text-blue-600"><i class="ri-calendar-check-line"></i></span>
                        <div>
                            <div class="text-lg font-bold">
                                <?php
                                // Count total appointments directly from the database for accuracy
                                $conn_count = new mysqli('localhost', 'root', '', 'clinic_management_system');
                                $student_id = $_SESSION['student_row_id'];
                                $total_appointments = 0;
                                if (!$conn_count->connect_errno) {
                                    $stmt = $conn_count->prepare('SELECT COUNT(*) FROM appointments WHERE student_id = ?');
                                    $stmt->bind_param('i', $student_id);
                                    $stmt->execute();
                                    $stmt->bind_result($total_appointments);
                                    $stmt->fetch();
                                    $stmt->close();
                                    $conn_count->close();
                                }
                                echo $total_appointments;
                                ?>
                            </div>
                            <div class="text-xs text-gray-500">Appointments</div>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-yellow-100 text-yellow-600"><i class="ri-time-line"></i></span>
                        <div>
                            <div class="text-lg font-bold">
                                <?php
                                // Count pending appointments directly from the database for accuracy
                                $conn_pending = new mysqli('localhost', 'root', '', 'clinic_management_system');
                                $student_id = $_SESSION['student_row_id'];
                                $pending_count = 0;
                                if (!$conn_pending->connect_errno) {
                                    $stmt = $conn_pending->prepare('SELECT COUNT(*) FROM appointments WHERE student_id = ? AND status = "pending"');
                                    $stmt->bind_param('i', $student_id);
                                    $stmt->execute();
                                    $stmt->bind_result($pending_count);
                                    $stmt->fetch();
                                    $stmt->close();
                                    $conn_pending->close();
                                }
                                echo $pending_count;
                                ?>
                            </div>
                            <div class="text-xs text-gray-500">Pending</div>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-green-100 text-green-600"><i class="ri-checkbox-circle-line"></i></span>
                        <div>
                            <div class="text-lg font-bold">
                                <?php
                                // Count confirmed/approved appointments directly from the database for accuracy
                                $conn_confirmed = new mysqli('localhost', 'root', '', 'clinic_management_system');
                                $student_id = $_SESSION['student_row_id'];
                                $confirmed_count = 0;
                                if (!$conn_confirmed->connect_errno) {
                                    $stmt = $conn_confirmed->prepare('SELECT COUNT(*) FROM appointments WHERE student_id = ? AND (status = "approved" OR status = "confirmed")');
                                    $stmt->bind_param('i', $student_id);
                                    $stmt->execute();
                                    $stmt->bind_result($confirmed_count);
                                    $stmt->fetch();
                                    $stmt->close();
                                    $conn_confirmed->close();
                                }
                                echo $confirmed_count;
                                ?>
                            </div>
                            <div class="text-xs text-gray-500">Confirmed</div>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-red-100 text-red-600"><i class="ri-close-circle-line"></i></span>
                        <div>
                            <div class="text-lg font-bold">
                                <?php
                                // Count cancelled appointments directly from the database for accuracy
                                $conn_cancelled = new mysqli('localhost', 'root', '', 'clinic_management_system');
                                $student_id = $_SESSION['student_row_id'];
                                $cancelled_count = 0;
                                if (!$conn_cancelled->connect_errno) {
                                    $stmt = $conn_cancelled->prepare('SELECT COUNT(*) FROM appointments WHERE student_id = ? AND status = "cancelled"');
                                    $stmt->bind_param('i', $student_id);
                                    $stmt->execute();
                                    $stmt->bind_result($cancelled_count);
                                    $stmt->fetch();
                                    $stmt->close();
                                    $conn_cancelled->close();
                                }
                                echo $cancelled_count;
                                ?>
                            </div>
                            <div class="text-xs text-gray-500">Cancelled</div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Quick Actions Card -->
            <div class="bg-white rounded-xl shadow-lg p-8 flex flex-col gap-6 border border-gray-100">
                <h3 class="text-lg font-semibold mb-4 flex items-center gap-2"><i class="ri-flashlight-line text-primary"></i> Quick Actions</h3>
                <a href="appointments.php" class="w-full flex items-center gap-2 px-4 py-3 bg-primary text-white rounded-lg hover:bg-primary/90 transition shadow text-center justify-center"><i class="ri-calendar-event-line"></i> My Appointments</a>
                <a href="history.php" class="w-full flex items-center gap-2 px-4 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition shadow text-center justify-center"><i class="ri-history-line"></i> Medical History</a>
                <a href="notifications.php" class="w-full flex items-center gap-2 px-4 py-3 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition shadow text-center justify-center"><i class="ri-notification-3-line"></i> Notifications</a>
            </div>
        </div>
        <!-- Recent Appointments Table -->
        <div class="mt-12 bg-white rounded-xl shadow-lg p-8 border border-gray-100">
            <h3 class="text-lg font-semibold mb-6 flex items-center gap-2"><i class="ri-calendar-check-line text-primary"></i> Recent Appointments</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reason</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php if (!empty($appointments)) {
                            foreach ($appointments as $appt) { ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"><?php echo htmlspecialchars($appt['date']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"><?php echo htmlspecialchars($appt['time']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"><?php echo htmlspecialchars($appt['reason']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <?php if ($appt['status'] == 'approved') { ?>
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Approved</span>
                                        <?php } elseif ($appt['status'] == 'pending') { ?>
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                                        <?php } elseif ($appt['status'] == 'cancelled') { ?>
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Cancelled</span>
                                        <?php } else { ?>
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800"><?php echo htmlspecialchars(ucfirst($appt['status'])); ?></span>
                                        <?php } ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                                        <?php if ($appt['status'] == 'pending') { ?>
                                            <button class="cancelBtn px-2 py-1 text-xs bg-red-500 text-white rounded hover:bg-red-600 mr-1">Cancel</button>
                                        <?php } else { ?>
                                            <button class="cancelBtn px-2 py-1 text-xs bg-red-200 text-white rounded opacity-50 cursor-not-allowed mr-1" disabled>Cancel</button>
                                        <?php } ?>
                                    </td>
                                </tr>
                            <?php }
                        } else { ?>
                            <tr>
                                <td colspan="5" class="px-4 py-2 text-center text-gray-400">No appointments found.</td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
</main>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.cancelBtn').forEach(function(btn, idx) {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                if (confirm('Are you sure you want to cancel this appointment?')) {
                    // Find the appointment row and get date/time/reason as unique keys
                    const row = btn.closest('tr');
                    const date = row.children[0].textContent.trim();
                    const time = row.children[1].textContent.trim();
                    const reason = row.children[2].textContent.trim();
                    // Send AJAX request to cancel
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
                                row.querySelector('td:nth-child(4) span').textContent = 'Cancelled';
                                row.querySelector('td:nth-child(4) span').className = 'inline-block px-2 py-1 rounded bg-red-100 text-red-800 text-xs';
                                btn.disabled = true;
                                btn.classList.add('opacity-50', 'cursor-not-allowed');
                            } else {
                                showErrorModal('Failed to cancel appointment.', 'Error');
                            }
                        });
                }
            });
        });
    });
</script>
<?php include '../includep/footer.php'; ?>