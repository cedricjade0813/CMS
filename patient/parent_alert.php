<?php
// filepath: c:/xampp/htdocs/CMS/patient/parent_alert.php
include '../includep/header.php';
$student_id = $_SESSION['student_row_id'];

$conn = new mysqli('localhost', 'root', '', 'clinic_management_system');
if ($conn->connect_errno) {
    die('Database connection failed: ' . $conn->connect_error);
}

// Get start and end of current week (Monday to Sunday)
$startOfWeek = date('Y-m-d', strtotime('monday this week'));
$endOfWeek = date('Y-m-d', strtotime('sunday this week'));

// Query: count visits per symptom for this student in the current week
$sql = "SELECT reason, COUNT(*) as visit_count, MIN(date) as first_date, MAX(date) as last_date
        FROM appointments
        WHERE student_id = ? AND date BETWEEN ? AND ?
        GROUP BY reason
        HAVING visit_count >= 3
        ORDER BY visit_count DESC, reason ASC";
$stmt = $conn->prepare($sql);
$stmt->bind_param('iss', $student_id, $startOfWeek, $endOfWeek);
$stmt->execute();
$result = $stmt->get_result();
$alerts = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
$conn->close();
?>
<main class="flex-1 overflow-y-auto bg-gray-50 p-6 ml-16 md:ml-64 mt-[56px]">
    <h2 class="text-2xl font-bold mb-6 text-gray-800">Parent Notification Alert</h2>
    <?php if (!empty($alerts)): ?>
        <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-800 p-4 rounded mb-6 flex items-center gap-2">
            <i class="ri-error-warning-line text-2xl"></i>
            <span>You have visited the clinic 3 or more times this week for the same symptom. Please contact your parent/guardian and let them know about your health status.</span>
        </div>
        <div class="bg-white rounded shadow p-6 mb-8 max-w-xl">
            <h3 class="text-lg font-semibold mb-4">Symptoms with Frequent Visits</h3>
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left font-semibold text-gray-600">Symptom</th>
                        <th class="px-4 py-2 text-left font-semibold text-gray-600">Visits</th>
                        <th class="px-4 py-2 text-left font-semibold text-gray-600">First Visit</th>
                        <th class="px-4 py-2 text-left font-semibold text-gray-600">Last Visit</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($alerts as $row): ?>
                    <tr>
                        <td class="px-4 py-2"><?php echo htmlspecialchars($row['reason']); ?></td>
                        <td class="px-4 py-2"><?php echo (int)$row['visit_count']; ?></td>
                        <td class="px-4 py-2"><?php echo htmlspecialchars($row['first_date']); ?></td>
                        <td class="px-4 py-2"><?php echo htmlspecialchars($row['last_date']); ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="bg-green-100 border-l-4 border-green-500 text-green-800 p-4 rounded mb-6 flex items-center gap-2">
            <i class="ri-checkbox-circle-line text-2xl"></i>
            <span>No symptoms with 3 or more visits this week. Stay healthy!</span>
        </div>
    <?php endif; ?>
</main>
<?php include '../includep/footer.php'; ?>
