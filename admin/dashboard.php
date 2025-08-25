<?php
include '../includea/header.php';
// Fetch dynamic stats
try {
    $db = new PDO('mysql:host=localhost;dbname=clinic_management_system;charset=utf8', 'root', '');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Total visits today (prescriptions today)
    $visitsToday = $db->query("SELECT COUNT(*) FROM prescriptions WHERE DATE(prescription_date) = CURDATE()")->fetchColumn();
    // Appointments pending (appointments table)
    $pendingAppointments = $db->query("SELECT COUNT(*) FROM appointments WHERE status = 'pending'")->fetchColumn();
    // Total students in system (imported_patients)
    $totalStudents = $db->query("SELECT COUNT(*) FROM imported_patients")->fetchColumn();
    // Fetch monthly visits for the current year
    $monthlyVisits = array_fill(1, 12, 0);
    try {
        $stmt = $db->prepare('SELECT MONTH(prescription_date) as month, COUNT(*) as count FROM prescriptions WHERE YEAR(prescription_date) = YEAR(CURDATE()) GROUP BY month');
        $stmt->execute();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $monthlyVisits[(int)$row['month']] = (int)$row['count'];
        }
    } catch (Exception $e) {
    }
    // Fetch medication stock status for pie chart
    $stockStatus = [];
    try {
        $stmt = $db->query('SELECT name, quantity FROM medicines');
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $stockStatus[] = [
                'value' => (int)$row['quantity'],
                'name' => $row['name']
            ];
        }
    } catch (Exception $e) {
    }
} catch (PDOException $e) {
    $visitsToday = 0;
    $pendingAppointments = 0;
    $totalStudents = 0;
}
?>

<!-- Main content -->
<main class="flex-1 overflow-y-auto bg-gray-50 p-6 ml-16 md:ml-64 mt-[56px]">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">Dashboard</h2>
    <!-- Key Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <!-- Total Visits Today -->
        <div class="bg-white rounded shadow p-6 flex flex-col items-center justify-center">
            <div class="w-12 h-12 flex items-center justify-center bg-primary bg-opacity-10 rounded-full text-primary mb-2">
                <i class="ri-user-heart-line ri-xl"></i>
            </div>
            <span class="text-3xl font-bold text-gray-800 mb-1"><?= $visitsToday ?></span>
            <span class="text-sm font-medium text-gray-500 mb-2">Total Visits Today</span>
        </div>
        <!-- Appointments Pending -->
        <div class="bg-white rounded shadow p-6 flex flex-col items-center justify-center">
            <div class="w-12 h-12 flex items-center justify-center bg-orange-100 rounded-full text-orange-600 mb-2">
                <i class="ri-calendar-check-line ri-xl"></i>
            </div>
            <span class="text-3xl font-bold text-gray-800 mb-1"><?= $pendingAppointments ?></span>
            <span class="text-sm font-medium text-gray-500 mb-2">Appointments Pending</span>
        </div>
        <!-- Total Students in System -->
        <div class="bg-white rounded shadow p-6 flex flex-col items-center justify-center">
            <div class="w-12 h-12 flex items-center justify-center bg-blue-100 rounded-full text-blue-600 mb-2">
                <i class="ri-team-line ri-xl"></i>
            </div>
            <span class="text-3xl font-bold text-gray-800 mb-1"><?= $totalStudents ?></span>
            <span class="text-sm font-medium text-gray-500 mb-2">Total Students in System</span>
        </div>
    </div>
    <!-- Analytics Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-8">
        <!-- Line Graph: Monthly Visit Trends -->
        <div class="bg-white rounded shadow p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-800">Monthly Visit Trends</h3>
            </div>
            <div id="monthlyVisitsChart" class="w-full h-[300px]"></div>
        </div>
        <!-- Pie Chart: Medication Stock Status -->
        <div class="bg-white rounded shadow p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-800">Medication Stock Status</h3>
            </div>
            <div id="stockUsageChart" class="w-full h-[300px]"></div>
        </div>
    </div>
</main>
</div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Drop zone functionality
        const dropZone = document.getElementById('dropZone');
        const fileInput = document.getElementById('fileInput');
        if (dropZone && fileInput) {
            dropZone.addEventListener('click', () => {
                fileInput.click();
            });
            dropZone.addEventListener('dragover', (e) => {
                e.preventDefault();
                dropZone.classList.add('active');
            });
            dropZone.addEventListener('dragleave', () => {
                dropZone.classList.remove('active');
            });
            dropZone.addEventListener('drop', (e) => {
                e.preventDefault();
                dropZone.classList.remove('active');
                if (e.dataTransfer.files.length) {
                    fileInput.files = e.dataTransfer.files;
                    // Handle file upload logic here
                }
            });
            fileInput.addEventListener('change', () => {
                // Handle file upload logic here
            });
        }
    });
    document.addEventListener('DOMContentLoaded', function() {
        // Custom select functionality
        const customSelects = document.querySelectorAll('.custom-select');
        customSelects.forEach(select => {
            const trigger = select.querySelector('.custom-select-trigger');
            const options = select.querySelectorAll('.custom-select-option');
            const selectedText = trigger.querySelector('span');
            trigger.addEventListener('click', () => {
                select.classList.toggle('open');
            });
            options.forEach(option => {
                option.addEventListener('click', () => {
                    selectedText.textContent = option.textContent;
                    select.classList.remove('open');
                });
            });
            document.addEventListener('click', (e) => {
                if (!select.contains(e.target)) {
                    select.classList.remove('open');
                }
            });
        });
    });
    document.addEventListener('DOMContentLoaded', function() {
        // Monthly Visits Chart
        const monthlyVisitsChart = echarts.init(document.getElementById('monthlyVisitsChart'));
        const monthlyVisitsOption = {
            animation: false,
            tooltip: {
                trigger: 'axis',
                backgroundColor: 'rgba(255, 255, 255, 0.8)',
                borderColor: '#e5e7eb',
                borderWidth: 1,
                textStyle: {
                    color: '#1f2937'
                }
            },
            grid: {
                left: '3%',
                right: '4%',
                bottom: '3%',
                top: '3%',
                containLabel: true
            },
            xAxis: {
                type: 'category',
                data: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                axisLine: {
                    lineStyle: {
                        color: '#e5e7eb'
                    }
                },
                axisLabel: {
                    color: '#6b7280'
                }
            },
            yAxis: {
                type: 'value',
                axisLine: {
                    show: false
                },
                axisLabel: {
                    color: '#6b7280'
                },
                splitLine: {
                    lineStyle: {
                        color: '#f3f4f6'
                    }
                }
            },
            series: [{
                name: 'Student Visits',
                type: 'line',
                smooth: true,
                symbol: 'none',
                lineStyle: {
                    width: 3,
                    color: 'rgba(87, 181, 231, 1)'
                },
                areaStyle: {
                    color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [{
                            offset: 0,
                            color: 'rgba(87, 181, 231, 0.2)'
                        },
                        {
                            offset: 1,
                            color: 'rgba(87, 181, 231, 0.01)'
                        }
                    ])
                },
                data: <?= json_encode(array_values($monthlyVisits)) ?>
            }]
        };
        monthlyVisitsChart.setOption(monthlyVisitsOption);
        // Stock Usage Chart
        const stockUsageChart = echarts.init(document.getElementById('stockUsageChart'));
        const stockUsageOption = {
            animation: false,
            tooltip: {
                trigger: 'item',
                backgroundColor: 'rgba(255, 255, 255, 0.8)',
                borderColor: '#e5e7eb',
                borderWidth: 1,
                textStyle: {
                    color: '#1f2937'
                }
            },
            legend: {
                orient: 'vertical',
                right: '5%',
                top: 'center',
                textStyle: {
                    color: '#6b7280'
                }
            },
            series: [{
                name: 'Medication Stock',
                type: 'pie',
                radius: ['40%', '70%'],
                avoidLabelOverlap: false,
                itemStyle: {
                    borderRadius: 8,
                    borderColor: '#fff',
                    borderWidth: 2
                },
                label: {
                    show: false
                },
                emphasis: {
                    label: {
                        show: false
                    }
                },
                labelLine: {
                    show: false
                },
                data: <?= json_encode($stockStatus) ?>
            }]
        };
        stockUsageChart.setOption(stockUsageOption);
        // Resize charts when window size changes
        window.addEventListener('resize', function() {
            monthlyVisitsChart.resize();
            stockUsageChart.resize();
        });
    });
</script>

<?php
include '../includea/footer.php';
?>