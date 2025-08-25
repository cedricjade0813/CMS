<?php
include '../includes/header.php';
// Database connection
$db = new PDO('mysql:host=localhost;dbname=clinic_management_system;charset=utf8', 'root', '');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Total Visits Today: count of prescriptions issued today (match admin dashboard logic)
$visitsToday = 0;
try {
    $visitsToday = $db->query("SELECT COUNT(*) FROM prescriptions WHERE DATE(prescription_date) = CURDATE()")->fetchColumn();
} catch (Exception $e) {}

// Appointments Today: count of appointments submitted by patients (approved, for today)
$appointmentsToday = 0;
$appointmentsTodayList = [];
try {
    // Use the correct date column (date or appointment_date) and status 'approved', for today
    $stmt = $db->prepare("SELECT date, time, reason, email FROM appointments WHERE status = 'approved' AND DATE(date) = CURDATE() ORDER BY time ASC");
    $stmt->execute();
    $appointmentsTodayList = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $appointmentsToday = count($appointmentsTodayList);
} catch (Exception $e) {}

// Total Visits This Week: count of prescriptions issued from Monday to Sunday this week
$totalVisitsWeek = 0;
try {
    $startOfWeek = date('Y-m-d', strtotime('monday this week'));
    $endOfWeek = date('Y-m-d', strtotime('sunday this week'));
    $stmt = $db->prepare('SELECT COUNT(*) FROM prescriptions WHERE prescription_date BETWEEN ? AND ?');
    $stmt->execute([$startOfWeek, $endOfWeek]);
    $totalVisitsWeek = $stmt->fetchColumn();
} catch (Exception $e) {}

// Fetch meds issued this week (sum of all medicines dispensed in prescriptions this week)
$medsIssuedWeek = 0;
try {
    $startOfWeek = date('Y-m-d', strtotime('monday this week'));
    $endOfWeek = date('Y-m-d', strtotime('sunday this week'));
    $stmt = $db->prepare('SELECT medicines FROM prescriptions WHERE prescription_date BETWEEN ? AND ?');
    $stmt->execute([$startOfWeek, $endOfWeek]);
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $medList = json_decode($row['medicines'], true);
        if (is_array($medList)) {
            $medsIssuedWeek += count($medList);
        }
    }
} catch (Exception $e) {}

// Fetch low stock medicines
$lowStockMeds = [];
try {
    $stmt = $db->query('SELECT name FROM medicines WHERE quantity <= 20');
    $lowStockMeds = $stmt->fetchAll(PDO::FETCH_COLUMN);
} catch (Exception $e) {}

// Fetch frequent illnesses (top 5 diagnoses in prescriptions this week)
$illnessLabels = [];
$illnessCounts = [];
try {
    $startOfWeek = date('Y-m-d', strtotime('monday this week'));
    $endOfWeek = date('Y-m-d', strtotime('sunday this week'));
    $stmt = $db->prepare('SELECT diagnosis, COUNT(*) as cnt FROM prescriptions WHERE prescription_date BETWEEN ? AND ? GROUP BY diagnosis ORDER BY cnt DESC LIMIT 5');
    $stmt->execute([$startOfWeek, $endOfWeek]);
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $illnessLabels[] = $row['diagnosis'];
        $illnessCounts[] = (int)$row['cnt'];
    }
} catch (Exception $e) {}
?>

<main class="flex-1 overflow-y-auto bg-gray-50 p-6 ml-16 md:ml-64 mt-[56px]">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Dashboard</h2>
        <!-- Quick Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded shadow p-6 flex flex-col items-center justify-center">
                <div
                    class="w-12 h-12 flex items-center justify-center bg-primary bg-opacity-10 rounded-full text-primary mb-2">
                    <i class="ri-user-heart-line ri-xl"></i>
                </div>
                <span class="text-3xl font-bold text-gray-800 mb-1"><?php echo $visitsToday; ?></span>
                <span class="text-sm font-medium text-gray-500 mb-2">Total Visits Today</span>
            </div>
            <div class="bg-white rounded shadow p-6 flex flex-col items-center justify-center">
                <div class="w-12 h-12 flex items-center justify-center bg-blue-100 rounded-full text-blue-600 mb-2">
                    <i class="ri-calendar-check-line ri-xl"></i>
                </div>
                <span class="text-3xl font-bold text-gray-800 mb-1"><?php echo $appointmentsToday; ?></span>
                <span class="text-sm font-medium text-gray-500 mb-2">Appointments Today</span>
            </div>
            <div class="bg-white rounded shadow p-6 flex flex-col items-center justify-center">
                <div class="w-12 h-12 flex items-center justify-center bg-green-100 rounded-full text-green-600 mb-2">
                    <i class="ri-bar-chart-2-line ri-xl"></i>
                </div>
                <span class="text-3xl font-bold text-gray-800 mb-1"><?php echo $totalVisitsWeek; ?></span>
                <span class="text-sm font-medium text-gray-500 mb-2">Total Visits This Week</span>
            </div>
        </div>
        
        <!-- Dashboard Main Cards Layout -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <!-- Left: Today's Approved Appointments (Square Card) -->
            <?php if ($appointmentsToday > 0): ?>
            <div class="bg-white rounded shadow p-6 flex flex-col items-start justify-start h-[340px] min-h-[300px] max-h-[400px] min-w-[280px] max-w-full">
                <div class="flex items-center mb-4">
                    <div class="w-10 h-10 flex items-center justify-center bg-blue-100 rounded-full text-blue-600 mr-3">
                        <i class="ri-calendar-check-line ri-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800">Today's Approved Appointments</h3>
                        <p class="text-xs text-gray-500">All appointments approved for today</p>
                    </div>
                </div>
                <div class="w-full flex-1 overflow-y-auto pr-2" style="max-height: 220px;">
                    <ul class="divide-y divide-gray-100 w-full">
                        <?php foreach ($appointmentsTodayList as $appt): ?>
                        <li class="py-3 flex flex-col md:flex-row md:items-center md:gap-4">
                            <span class="inline-flex items-center gap-1 w-24 text-blue-800 font-semibold text-sm">
                                <i class="ri-time-line text-blue-400"></i>
                                <?php echo htmlspecialchars($appt['time']); ?>
                            </span>
                            <span class="inline-block flex-1 text-gray-800 text-xs md:text-sm">
                                <i class="ri-stethoscope-line text-blue-300 mr-1"></i>
                                <?php echo htmlspecialchars($appt['reason']); ?>
                            </span>
                            <span class="inline-flex items-center gap-1 text-xs text-blue-600 mt-1 md:mt-0">
                                <i class="ri-mail-line text-blue-400"></i>
                                <?php echo htmlspecialchars($appt['email']); ?>
                            </span>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
            <?php else: ?>
            <div class="bg-white rounded shadow p-6 flex items-center justify-center h-[340px] min-h-[300px] max-h-[400px] min-w-[280px] max-w-full">
                <div class="text-center w-full">
                    <div class="w-10 h-10 flex items-center justify-center bg-blue-100 rounded-full text-blue-600 mx-auto mb-2">
                        <i class="ri-calendar-check-line ri-xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-1">No Appointments Today</h3>
                    <p class="text-xs text-gray-500">No approved appointments for today.</p>
                </div>
            </div>
            <?php endif; ?>

            <!-- Right: Stacked Meds Issued and Stock Alert -->
            <div class="flex flex-col gap-6 h-[340px] min-h-[300px] max-h-[400px] min-w-[280px] max-w-full">
                <!-- Meds Issued Quick Stat (Top) -->
                <div class="bg-white rounded shadow p-6 flex flex-col items-center justify-center h-1/2 min-h-[140px]">
                    <div class="w-12 h-12 flex items-center justify-center bg-orange-100 rounded-full text-orange-600 mb-2">
                        <i class="ri-capsule-line ri-xl"></i>
                    </div>
                    <span class="text-3xl font-bold text-gray-800 mb-1"><?php echo $medsIssuedWeek; ?></span>
                    <span class="text-sm font-medium text-gray-500 mb-2">Meds Issued This Week</span>
                </div>
                <!-- Medicine Stock Alert (Bottom) -->
                <div class="bg-red-50 border-l-4 border-red-400 p-6 rounded flex items-center h-1/2 min-h-[140px]">
                    <div class="w-10 h-10 flex items-center justify-center bg-red-100 rounded-full text-red-600 mr-4">
                        <i class="ri-alert-line ri-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-red-800">Medicine Stock Alert</p>
                        <p class="text-xs text-red-600">
                            <?php
                            if (!empty($lowStockMeds)) {
                                // Fetch medicine quantities for low stock
                                $lowStockDetails = [];
                                $stmt = $db->query('SELECT name, quantity FROM medicines WHERE quantity <= 20');
                                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                    $lowStockDetails[] = $row;
                                }
                                $emptyMeds = array_filter($lowStockDetails, function($med) { return $med['quantity'] == 0; });
                                $lowMeds = array_filter($lowStockDetails, function($med) { return $med['quantity'] > 0; });
                                if (!empty($emptyMeds)) {
                                    $names = array_map(function($med) { return $med['name']; }, $emptyMeds);
                                    echo implode(', ', $names) . ' ' . (count($names) === 1 ? 'is' : 'are') . ' empty.';
                                }
                                if (!empty($lowMeds)) {
                                    if (!empty($emptyMeds)) echo ' '; // space between messages
                                    $names = array_map(function($med) { return $med['name']; }, $lowMeds);
                                    echo implode(', ', $names) . ' ' . (count($names) === 1 ? 'is' : 'are') . ' running low.';
                                }
                            } else {
                                echo 'No medicines are running low.';
                            }
                            ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Main Cards Layout -->
        
        <!-- Bar Chart: Frequent Illnesses -->
        <div class="bg-white rounded shadow p-6 mb-8">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-800">Frequent Illnesses</h3>
            </div>
            <div id="illnessBarChart" class="w-full h-[300px]"></div>
        </div>

</main>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Bar Chart: Frequent Illnesses (live data)
        const illnessBarChart = echarts.init(document.getElementById('illnessBarChart'));
        const illnessBarOption = {
            tooltip: { trigger: 'axis' },
            grid: { left: '3%', right: '4%', bottom: '3%', top: '3%', containLabel: true },
            xAxis: {
                type: 'category',
                data: <?= json_encode($illnessLabels) ?>,
                axisLine: { lineStyle: { color: '#e5e7eb' } },
                axisLabel: { color: '#6b7280' }
            },
            yAxis: {
                type: 'value',
                axisLine: { show: false },
                axisLabel: { color: '#6b7280' },
                splitLine: { lineStyle: { color: '#f3f4f6' } }
            },
            series: [{
                name: 'Cases',
                type: 'bar',
                barWidth: '40%',
                itemStyle: { color: 'rgba(87, 181, 231, 1)', borderRadius: [6, 6, 0, 0] },
                data: <?= json_encode($illnessCounts) ?>
            }]
        };
        illnessBarChart.setOption(illnessBarOption);
        window.addEventListener('resize', function () {
            illnessBarChart.resize();
        });
    });
</script>

<?php
include '../includes/footer.php';
?>