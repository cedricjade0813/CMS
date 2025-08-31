<?php
include '../includes/header.php';
// Database connection
$db = new PDO('mysql:host=localhost;dbname=clinic_management_system;charset=utf8', 'root', '');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

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

// Build data for line chart of frequent reasons across time ranges (daily/weekly/monthly)
$topReasons = [];
$topReasonsDisplay = [];
$dailyLabels = [];
$weeklyLabels = [];
$monthlyLabels = [];
$dailySeries = [];
$weeklySeries = [];
$monthlySeries = [];

try {
    // Top 5 reasons over last 12 months
    $stmt = $db->prepare("SELECT norm_reason, COUNT(*) cnt FROM (
        SELECT COALESCE(NULLIF(LOWER(TRIM(reason)), ''), 'unspecified') AS norm_reason
        FROM prescriptions
        WHERE prescription_date >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
    ) t GROUP BY norm_reason ORDER BY cnt DESC LIMIT 5");
    $stmt->execute();
    $topReasons = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
    // Build display map (capitalize first letter, rest lowercase)
    foreach ($topReasons as $r) {
        $topReasonsDisplay[$r] = ucfirst($r);
    }

    // DAILY: last 7 days including today
    $dailyMap = [];
    for ($i = 6; $i >= 0; $i--) {
        $d = date('Y-m-d', strtotime("-{$i} day"));
        $dailyLabels[] = $d;
        $dailyMap[$d] = array_fill_keys($topReasons, 0);
    }
    if (!empty($topReasons)) {
        $inReasons = implode(',', array_fill(0, count($topReasons), '?'));
        $params = $topReasons;
        $params[] = date('Y-m-d', strtotime('-6 day')) . ' 00:00:00';
        $sql = "SELECT DATE(prescription_date) d, COALESCE(NULLIF(LOWER(TRIM(reason)), ''), 'unspecified') r, COUNT(*) c
                FROM prescriptions
                WHERE prescription_date >= ? AND COALESCE(NULLIF(TRIM(reason), ''), 'Unspecified') IN ($inReasons)
                GROUP BY d, r";
        // Reorder params: first date, then reasons
        $params = array_merge([date('Y-m-d', strtotime('-6 day')) . ' 00:00:00'], $topReasons);
        $st = $db->prepare($sql);
        $st->execute($params);
        while ($row = $st->fetch(PDO::FETCH_ASSOC)) {
            $d = $row['d'];
            if (isset($dailyMap[$d]) && isset($dailyMap[$d][$row['r']])) {
                $dailyMap[$d][$row['r']] = (int)$row['c'];
            }
        }
    }
    foreach ($topReasons as $r) {
        $dailySeries[$r] = array_map(function($d) use ($dailyMap, $r) { return $dailyMap[$d][$r] ?? 0; }, $dailyLabels);
    }

    // WEEKLY: last 8 weeks (ISO weeks starting Monday). Label by week start date
    $weeklyMap = [];
    for ($i = 7; $i >= 0; $i--) {
        $monday = date('Y-m-d', strtotime("monday -{$i} week"));
        $weeklyLabels[] = $monday; // "Week of YYYY-MM-DD"
        $weeklyMap[$monday] = array_fill_keys($topReasons, 0);
    }
    if (!empty($topReasons)) {
        $inReasons = implode(',', array_fill(0, count($topReasons), '?'));
        $startMonday = $weeklyLabels[0];
        $sql = "SELECT YEARWEEK(prescription_date, 1) yw, COALESCE(NULLIF(LOWER(TRIM(reason)), ''), 'unspecified') r, COUNT(*) c
                FROM prescriptions
                WHERE prescription_date >= ? AND COALESCE(NULLIF(TRIM(reason), ''), 'Unspecified') IN ($inReasons)
                GROUP BY yw, r";
        $params = array_merge([$startMonday . ' 00:00:00'], $topReasons);
        $st = $db->prepare($sql);
        $st->execute($params);
        // Map YEARWEEK to Monday date
        $ywToMonday = [];
        foreach ($weeklyLabels as $monday) {
            $ywToMonday[date('oW', strtotime($monday))] = $monday; // ISO week-year + week number
        }
        while ($row = $st->fetch(PDO::FETCH_ASSOC)) {
            $key = $row['yw'];
            // Convert MySQL YEARWEEK mode 1 to ISO year-week like oW
            $weekYear = substr($key, 0, 4);
            $weekNum = substr($key, 4);
            $isoKey = sprintf('%s%02d', $weekYear, (int)$weekNum);
            $monday = $ywToMonday[$isoKey] ?? null;
            if ($monday && isset($weeklyMap[$monday]) && isset($weeklyMap[$monday][$row['r']])) {
                $weeklyMap[$monday][$row['r']] = (int)$row['c'];
            }
        }
    }
    foreach ($topReasons as $r) {
        $weeklySeries[$r] = array_map(function($d) use ($weeklyMap, $r) { return $weeklyMap[$d][$r] ?? 0; }, $weeklyLabels);
    }

    // MONTHLY: last 12 months, label YYYY-MM
    $monthlyMap = [];
    for ($i = 11; $i >= 0; $i--) {
        $ym = date('Y-m', strtotime("first day of -{$i} month"));
        $monthlyLabels[] = $ym;
        $monthlyMap[$ym] = array_fill_keys($topReasons, 0);
    }
    if (!empty($topReasons)) {
        $inReasons = implode(',', array_fill(0, count($topReasons), '?'));
        $startMonth = $monthlyLabels[0] . '-01 00:00:00';
        $sql = "SELECT DATE_FORMAT(prescription_date, '%Y-%m') ym, COALESCE(NULLIF(LOWER(TRIM(reason)), ''), 'unspecified') r, COUNT(*) c
                FROM prescriptions
                WHERE prescription_date >= ? AND COALESCE(NULLIF(TRIM(reason), ''), 'Unspecified') IN ($inReasons)
                GROUP BY ym, r";
        $params = array_merge([$startMonth], $topReasons);
        $st = $db->prepare($sql);
        $st->execute($params);
        while ($row = $st->fetch(PDO::FETCH_ASSOC)) {
            $ym = $row['ym'];
            if (isset($monthlyMap[$ym]) && isset($monthlyMap[$ym][$row['r']])) {
                $monthlyMap[$ym][$row['r']] = (int)$row['c'];
            }
        }
    }
    foreach ($topReasons as $r) {
        $monthlySeries[$r] = array_map(function($d) use ($monthlyMap, $r) { return $monthlyMap[$d][$r] ?? 0; }, $monthlyLabels);
    }
} catch (Exception $e) {}
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
        
        <!-- Line Chart: Frequent Illness Reasons -->
        <div class="bg-white rounded shadow p-6 mb-8">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-800">Frequent Illness</h3>
                <div class="inline-flex rounded border border-gray-200 overflow-hidden">
                    <button id="rangeDaily" class="px-3 py-1.5 text-sm bg-gray-100">Daily</button>
                    <button id="rangeWeekly" class="px-3 py-1.5 text-sm">Weekly</button>
                    <button id="rangeMonthly" class="px-3 py-1.5 text-sm">Monthly</button>
                </div>
                <div class="ml-4">
                    <button id="printCurrentChart" class="px-3 py-1.5 text-sm bg-green-100 text-green-800 rounded">Print Current</button>
                    <button id="printAllCharts" class="px-3 py-1.5 text-sm bg-blue-100 text-blue-800 rounded ml-2">Print All</button>
                </div>
            </div>
            <div id="illnessLineChart" class="w-full h-[340px]"></div>
            <div id="printChartsArea" style="display:none;"></div>
        </div>

</main>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Line Chart: Frequent Illness Reasons with range toggle
        const lineChart = echarts.init(document.getElementById('illnessLineChart'));
        // Map normalized keys to display labels (First letter uppercase)
        const topReasons = <?= json_encode($topReasons) ?>;
        const reasonDisplay = <?= json_encode($topReasonsDisplay) ?>;
        const datasets = {
            daily: { labels: <?= json_encode($dailyLabels) ?>, series: <?= json_encode($dailySeries) ?> },
            weekly: { labels: <?= json_encode($weeklyLabels) ?>, series: <?= json_encode($weeklySeries) ?> },
            monthly: { labels: <?= json_encode($monthlyLabels) ?>, series: <?= json_encode($monthlySeries) ?> }
        };

    function buildOption(rangeKey, withDots = true) {
            const ds = datasets[rangeKey];
            const palette = ['#4F46E5', '#60A5FA', '#10B981', '#F59E0B', '#EF4444'];
                return {
                    tooltip: { trigger: 'axis' },
                    legend: {
                        data: topReasons.map(k => reasonDisplay[k] || k),
                        top: 10,
                        right: 10,
                        orient: 'horizontal',
                        textStyle: { color: '#373d3f', fontWeight: 'bold', fontSize: 14 }
                    },
                    grid: { left: '3%', right: '4%', bottom: '3%', top: 60, containLabel: true, borderColor: '#D9DBF3' },
                    xAxis: {
                        type: 'category',
                        data: ds.labels,
                        axisLine: { lineStyle: { color: '#e5e7eb' } },
                        axisLabel: { color: '#6b7280' },
                        splitLine: { show: true, lineStyle: { color: '#D9DBF3' } },
                        tooltip: { show: false }
                    },
                    yAxis: {
                        type: 'value',
                        axisLine: { show: false },
                        axisLabel: { color: '#6b7280' },
                        splitLine: { lineStyle: { color: '#f3f4f6' } }
                    },
                    series: topReasons.map((r, idx) => ({
                        name: reasonDisplay[r] || r,
                        type: 'line',
                        smooth: true,
                        symbol: withDots ? 'circle' : 'none',
                        symbolSize: 6,
                        lineStyle: { width: 2 },
                        itemStyle: { color: palette[idx % palette.length] },
                        data: (ds.series[r] || Array(ds.labels.length).fill(0)),
                        emphasis: {
                            focus: 'series',
                            itemStyle: {
                                borderWidth: 0,
                                shadowBlur: 8,
                                shadowColor: palette[idx % palette.length],
                                symbolSize: 9
                            }
                        },
                        animationDuration: 600,
                        animationEasing: 'cubicOut',
                        animationDelay: function (idx) { return 0; },
                        animationDurationUpdate: 600,
                        animationEasingUpdate: 'cubicOut'
                    }))
                };
        }

        function setActive(rangeKey) {
            document.getElementById('rangeDaily').classList.toggle('bg-gray-100', rangeKey === 'daily');
            document.getElementById('rangeWeekly').classList.toggle('bg-gray-100', rangeKey === 'weekly');
            document.getElementById('rangeMonthly').classList.toggle('bg-gray-100', rangeKey === 'monthly');
        }

        let currentRange = 'daily';
        // Step 1: Draw line only
        lineChart.setOption(buildOption(currentRange, false));
        setActive(currentRange);
        // Step 2: Show dots after line animation
        setTimeout(() => {
            lineChart.setOption(buildOption(currentRange, true));
        }, 600);

        function changeRange(nextRange) {
            if (currentRange === nextRange) return;
            // Step 1: Draw line only
            lineChart.setOption(buildOption(nextRange, false));
            setActive(nextRange);
            currentRange = nextRange;
            // Step 2: Show dots after line animation
            setTimeout(() => {
                lineChart.setOption(buildOption(nextRange, true));
            }, 600);
        }

        document.getElementById('rangeDaily').addEventListener('click', () => changeRange('daily'));
        document.getElementById('rangeWeekly').addEventListener('click', () => changeRange('weekly'));
        document.getElementById('rangeMonthly').addEventListener('click', () => changeRange('monthly'));

        // Print logic
        document.getElementById('printCurrentChart').addEventListener('click', function() {
            // Convert current chart to image using a visible temp container
            const printArea = document.getElementById('printChartsArea');
            // Add a class to print area for landscape print
            printArea.classList.add('print-landscape');
            printArea.innerHTML = '';
            // Create a visible but hidden temp container
            const tempDiv = document.createElement('div');
            tempDiv.style.position = 'fixed';
            tempDiv.style.left = '-9999px';
            tempDiv.style.top = '0';
            tempDiv.style.width = '800px';
            tempDiv.style.height = '340px';
            document.body.appendChild(tempDiv);
            const tempChart = echarts.init(tempDiv);
            tempChart.setOption(buildOption(currentRange, true));
            setTimeout(() => {
                printArea.innerHTML = '';
                // Create a wrapper for centering and title
                const wrapper = document.createElement('div');
                wrapper.style.display = 'flex';
                wrapper.style.flexDirection = 'column';
                wrapper.style.alignItems = 'center';
                wrapper.style.justifyContent = 'center';
                wrapper.style.height = '100vh';
                wrapper.style.width = '100vw';
                // Add title above chart
                const title = document.createElement('h3');
                title.textContent = currentRange.charAt(0).toUpperCase() + currentRange.slice(1) + ' Chart';
                title.style.fontSize = '22px';
                title.style.fontWeight = 'bold';
                title.style.marginBottom = '18px';
                title.style.textAlign = 'center';
                wrapper.appendChild(title);
                // Chart image
                const img = document.createElement('img');
                img.src = tempChart.getDataURL({ type: 'png', pixelRatio: 2, backgroundColor: '#fff' });
                img.style.width = '700px';
                img.style.height = '400px';
                img.style.maxWidth = '90vw';
                img.style.maxHeight = '60vh';
                img.style.margin = '0 auto';
                img.style.display = 'block';
                wrapper.appendChild(img);
                printArea.style.display = 'flex';
                printArea.style.alignItems = 'center';
                printArea.style.justifyContent = 'center';
                printArea.style.height = '100vh';
                printArea.style.width = '100vw';
                printArea.appendChild(wrapper);
                setTimeout(() => {
                    tempChart.dispose();
                    document.body.removeChild(tempDiv);
                    window.print();
                    setTimeout(() => { printArea.style.display = 'none'; printArea.innerHTML = ''; printArea.classList.remove('print-landscape'); }, 1000);
                }, 500);
            }, 1000);
        });

        document.getElementById('printAllCharts').addEventListener('click', function() {
            // Render all charts as images in hidden area using visible temp containers
            const printArea = document.getElementById('printChartsArea');
            printArea.innerHTML = '';
            let chartsToPrint = ['daily','weekly','monthly'];
            let loaded = 0;
            // Always use 1 chart per page
            document.body.removeAttribute('data-print-layout');
            chartsToPrint.forEach(function(range) {
                const chartDiv = document.createElement('div');
                chartDiv.className = 'chart-print-block';
                chartDiv.style.width = '100%';
                chartDiv.style.height = '340px';
                chartDiv.style.marginBottom = '30px';
                // Add title as a separate block for better spacing
                const title = document.createElement('h3');
                title.textContent = range.charAt(0).toUpperCase() + range.slice(1) + ' Chart';
                title.style.fontSize = '18px';
                title.style.fontWeight = 'bold';
                title.style.marginBottom = '10px';
                title.style.textAlign = 'center';
                chartDiv.appendChild(title);
                printArea.appendChild(chartDiv);
                // Create a visible but hidden temp container
                const tempDiv = document.createElement('div');
                tempDiv.style.position = 'fixed';
                tempDiv.style.left = '-9999px';
                tempDiv.style.top = '0';
                tempDiv.style.width = '800px';
                tempDiv.style.height = '340px';
                document.body.appendChild(tempDiv);
                const tempChart = echarts.init(tempDiv);
                tempChart.setOption(buildOption(range, true));
                setTimeout(() => {
                    const img = document.createElement('img');
                    img.src = tempChart.getDataURL({ type: 'png', pixelRatio: 2, backgroundColor: '#fff' });
                    img.style.width = '100%';
                    img.style.height = '340px';
                    chartDiv.appendChild(img);
                    setTimeout(() => {
                        tempChart.dispose();
                        document.body.removeChild(tempDiv);
                        loaded++;
                        if (loaded === chartsToPrint.length) {
                            printArea.style.display = 'block';
                            window.print();
                            setTimeout(() => {
                                printArea.style.display = 'none';
                                printArea.innerHTML = '';
                                document.body.removeAttribute('data-print-layout');
                            }, 1000);
                        }
                    }, 500);
                }, 1000);
            });
        });

        window.addEventListener('resize', function () { lineChart.resize(); });
    });
    </script>
    <style>
    @media print {
        /* Force landscape orientation for Print Current only */
        #printChartsArea.print-landscape {
            width: 100vw !important;
            height: 100vh !important;
        }
        #printChartsArea.print-landscape > div {
            width: 100vw !important;
            height: 100vh !important;
            display: flex !important;
            flex-direction: column !important;
            align-items: center !important;
            justify-content: center !important;
        }
        #printChartsArea.print-landscape h3 {
            font-size: 22px !important;
            font-weight: bold !important;
            margin-bottom: 18px !important;
            text-align: center !important;
        }
        #printChartsArea.print-landscape img {
            width: 700px !important;
            height: 400px !important;
            max-width: 90vw !important;
            max-height: 60vh !important;
            margin: 0 auto !important;
            display: block !important;
        }
        @page print-landscape {
            size: landscape;
            margin: 0;
        }
        #printChartsArea.print-landscape {
            page: print-landscape;
        }
        body * {
            visibility: hidden !important;
        }
        #printChartsArea, #printChartsArea * {
            visibility: visible !important;
        }
        #printChartsArea {
            position: fixed !important;
            left: 0; top: 0; width: 100vw; min-height: 100vh;
            background: #fff;
            z-index: 9999;
            display: block !important;
            margin: 0 !important;
            padding: 0 !important;
        }
        #printChartsArea .chart-print-block {
            width: 100vw;
            height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            page-break-after: always;
            page-break-inside: avoid;
            margin: 0 !important;
            padding: 0 !important;
        }
        #printChartsArea img {
            width: 90vw !important;
            height: 70vh !important;
            max-width: 90vw !important;
            max-height: 70vh !important;
            margin: 0 auto !important;
            display: block;
        }
        #printChartsArea h3 {
            text-align: center;
            margin: 20px 0 20px 0;
            font-size: 22px;
            font-weight: bold;
        }
        @page {
            size: auto;
            margin: 0;
        }
    }
    </style>

<?php
include '../includes/footer.php';
?>