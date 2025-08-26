<?php
include '../includes/header.php';

// Database connection
$db = null;
try {
    $db = new PDO('mysql:host=localhost;dbname=clinic_management_system;charset=utf8', 'root', '');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Get current filter and date range
$reportType = isset($_GET['report']) ? $_GET['report'] : 'appointments';
$startDate = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01');
$endDate = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-t');
$medicineStatus = isset($_GET['medicine_status']) ? $_GET['medicine_status'] : 'all';

// Fetch data based on report type
$reportData = [];
$reportTitle = '';
$reportColumns = [];

switch ($reportType) {
    case 'appointments':
        $reportTitle = 'Appointments Report';
        $reportColumns = ['Patient Name', 'Date', 'Time', 'Reason', 'Status', 'Email'];
        try {
            $stmt = $db->prepare("
                SELECT ip.name, a.date, a.time, a.reason, a.status, a.email 
                FROM appointments a 
                LEFT JOIN imported_patients ip ON a.student_id = ip.id 
                WHERE a.date BETWEEN ? AND ?
                ORDER BY a.date DESC, a.time DESC
            ");
            $stmt->execute([$startDate, $endDate]);
            $reportData = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {}
        break;
        
    case 'prescriptions':
        $reportTitle = 'Issue Medication History Report';
        $reportColumns = ['Date', 'Patient Name', 'Medicine', 'Quantity'];
        try {
            $stmt = $db->prepare("
                SELECT prescription_date, patient_name, medicines
                FROM prescriptions 
                WHERE DATE(prescription_date) BETWEEN ? AND ?
                ORDER BY prescription_date DESC
            ");
            $stmt->execute([$startDate, $endDate]);
            $prescriptions = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Flatten prescription data so each medicine entry is a separate row (same as inventory.php)
            foreach ($prescriptions as $prescription) {
                $date = $prescription['prescription_date'];
                $patient = $prescription['patient_name'];
                $medicines = json_decode($prescription['medicines'], true);
                
                if (is_array($medicines)) {
                    foreach ($medicines as $medicine) {
                        $reportData[] = [
                            'prescription_date' => date('Y-m-d H:i', strtotime($date)),
                            'patient_name' => $patient,
                            'medicine' => $medicine['medicine'] ?? 'N/A',
                            'quantity' => $medicine['quantity'] ?? 'N/A'
                        ];
                    }
                }
            }
        } catch (Exception $e) {}
        break;
        
    case 'medicines':
        $reportTitle = 'Medicine Dispensing Report';
        $reportColumns = ['Medicine Name', 'Total Dispensed', 'Times Prescribed', 'Last Dispensed'];
        try {
            // Get all dispensed medicines from prescriptions
            $stmt = $db->prepare("
                SELECT prescription_date, medicines
                FROM prescriptions 
                WHERE DATE(prescription_date) BETWEEN ? AND ?
                ORDER BY prescription_date DESC
            ");
            $stmt->execute([$startDate, $endDate]);
            $prescriptions = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Track medicine dispensing data
            $medicineStats = [];
            
            foreach ($prescriptions as $prescription) {
                $medicines = json_decode($prescription['medicines'], true);
                $prescriptionDate = $prescription['prescription_date'];
                
                if (is_array($medicines)) {
                    foreach ($medicines as $medicine) {
                        $medicineName = $medicine['medicine'] ?? 'Unknown';
                        $quantity = (int)($medicine['quantity'] ?? 0);
                        
                        if (!isset($medicineStats[$medicineName])) {
                            $medicineStats[$medicineName] = [
                                'name' => $medicineName,
                                'total_dispensed' => 0,
                                'times_prescribed' => 0,
                                'last_dispensed' => $prescriptionDate
                            ];
                        }
                        
                        $medicineStats[$medicineName]['total_dispensed'] += $quantity;
                        $medicineStats[$medicineName]['times_prescribed']++;
                        
                        // Update last dispensed date if this prescription is more recent
                        if (strtotime($prescriptionDate) > strtotime($medicineStats[$medicineName]['last_dispensed'])) {
                            $medicineStats[$medicineName]['last_dispensed'] = $prescriptionDate;
                        }
                    }
                }
            }
            
            // Convert to report data format
            foreach ($medicineStats as $stats) {
                $reportData[] = [
                    'name' => $stats['name'],
                    'total_dispensed' => $stats['total_dispensed'],
                    'times_prescribed' => $stats['times_prescribed'],
                    'last_dispensed' => date('Y-m-d H:i', strtotime($stats['last_dispensed']))
                ];
            }
            
            // Sort by total dispensed (descending)
            usort($reportData, function($a, $b) {
                return $b['total_dispensed'] - $a['total_dispensed'];
            });
            
        } catch (Exception $e) {}
        break;
        
    case 'patients':
        $reportTitle = 'Patients Report';
        $reportColumns = ['Student ID', 'Name', 'Gender', 'Year Level', 'Date of Birth', 'Address'];
        try {
            $stmt = $db->query("SELECT student_id, name, gender, year_level, dob, address FROM imported_patients ORDER BY name ASC");
            $reportData = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {}
        break;
        
    case 'system_logs':
        $reportTitle = 'System Activity Logs';
        $reportColumns = ['User Email', 'Action', 'Timestamp'];
        try {
            $stmt = $db->prepare("
                SELECT user_email, action, timestamp 
                FROM logs 
                WHERE DATE(timestamp) BETWEEN ? AND ?
                ORDER BY timestamp DESC
            ");
            $stmt->execute([$startDate, $endDate]);
            $reportData = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {}
        break;
}
?>

<main class="flex-1 overflow-y-auto bg-gray-50 p-6 ml-16 md:ml-64 mt-[56px]">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between mb-6 gap-4">
            <h2 class="text-2xl font-bold text-gray-800">System Reports</h2>
            <div class="flex flex-col sm:flex-row gap-3">
                <button onclick="printReport()" class="px-4 py-2 bg-primary text-white font-medium text-sm rounded-button hover:bg-primary/90 flex items-center justify-center">
                    <i class="ri-printer-line mr-2"></i> Print Report
                </button>
                <button onclick="exportToCSV()" class="px-4 py-2 bg-secondary text-white font-medium text-sm rounded-button hover:bg-secondary/90 flex items-center justify-center">
                    <i class="ri-download-line mr-2"></i> Export CSV
                </button>
            </div>
        </div>

        <!-- Filters Card -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Report Filters</h3>
            <form method="GET" class="flex flex-col lg:flex-row gap-4">
                <div class="flex-1">
                    <label for="report" class="block text-sm font-medium text-gray-700 mb-2">Report Type</label>
                    <select name="report" id="report" class="w-full px-3 py-2 border border-gray-300 rounded-button focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                        <option value="appointments" <?php echo $reportType === 'appointments' ? 'selected' : ''; ?>>Appointments Report</option>
                        <option value="prescriptions" <?php echo $reportType === 'prescriptions' ? 'selected' : ''; ?>>Prescriptions Report</option>
                        <option value="medicines" <?php echo $reportType === 'medicines' ? 'selected' : ''; ?>>Medicine Reports</option>
                        <option value="patients" <?php echo $reportType === 'patients' ? 'selected' : ''; ?>>Patients Report</option>
                        <option value="system_logs" <?php echo $reportType === 'system_logs' ? 'selected' : ''; ?>>System Activity Logs</option>
                    </select>
                </div>
                
                <?php if (in_array($reportType, ['appointments', 'prescriptions', 'medicines', 'system_logs'])): ?>
                <div class="flex-1">
                    <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">Start Date</label>
                    <input type="date" name="start_date" id="start_date" value="<?php echo htmlspecialchars($startDate); ?>" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-button focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                </div>
                
                <div class="flex-1">
                    <label for="end_date" class="block text-sm font-medium text-gray-700 mb-2">End Date</label>
                    <input type="date" name="end_date" id="end_date" value="<?php echo htmlspecialchars($endDate); ?>" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-button focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                </div>
                <?php endif; ?>
                
                <div class="flex items-end">
                    <button type="submit" class="px-6 py-2 bg-primary text-white font-medium text-sm rounded-button hover:bg-primary/90 flex items-center">
                        <i class="ri-search-line mr-2"></i> Generate Report
                    </button>
                </div>
            </form>
        </div>

        <!-- Report Results -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-800"><?php echo htmlspecialchars($reportTitle); ?></h3>
                    <span class="text-sm text-gray-500">
                        <?php echo count($reportData); ?> records found
                        <?php if (in_array($reportType, ['appointments', 'prescriptions', 'medicines', 'system_logs'])): ?>
                            (<?php echo $startDate; ?> to <?php echo $endDate; ?>)
                        <?php endif; ?>
                    </span>
                </div>
            </div>
            
            <div class="p-6" id="reportContent">
                <?php if (!empty($reportData)): ?>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <?php foreach ($reportColumns as $column): ?>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            <?php echo htmlspecialchars($column); ?>
                                        </th>
                                    <?php endforeach; ?>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php
                                $report_total_records = count($reportData);
                                $report_records_per_page = 10;
                                $report_page = isset($_GET['report_page']) ? max(1, intval($_GET['report_page'])) : 1;
                                $report_total_pages = ceil($report_total_records / $report_records_per_page);
                                $report_offset = ($report_page - 1) * $report_records_per_page;
                                $report_page_data = array_slice(array_values($reportData), $report_offset, $report_records_per_page);
                                foreach ($report_page_data as $row): ?>
                                    <tr class="hover:bg-gray-50">
                                        <?php 
                                        $values = array_values($row);
                                        foreach ($values as $index => $value): 
                                            $cellClass = "px-6 py-4 whitespace-nowrap text-sm text-gray-900";
                                            // Special formatting for certain columns
                                            if ($reportType === 'appointments' && $index === 4) { // Status column
                                                if ($value === 'approved') {
                                                    $cellClass .= " text-green-600 font-semibold";
                                                } elseif ($value === 'declined') {
                                                    $cellClass .= " text-red-600 font-semibold";
                                                } elseif ($value === 'rescheduled') {
                                                    $cellClass .= " text-blue-600 font-semibold";
                                                } else {
                                                    $cellClass .= " text-orange-600 font-semibold";
                                                }
                                            }
                                        ?>
                                            <td class="<?php echo $cellClass; ?>">
                                                <?php echo htmlspecialchars($value ?? 'N/A'); ?>
                                            </td>
                                        <?php endforeach; ?>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <!-- Pagination and Records Info for System Reports Table -->
                    <?php if ($report_total_records > 0): ?>
                    <div class="flex justify-between items-center mt-6">
                        <div class="text-sm text-gray-600">
                            <?php 
                            $report_start = $report_offset + 1;
                            $report_end = min($report_offset + $report_records_per_page, $report_total_records);
                            ?>
                            Showing <?php echo $report_start; ?> to <?php echo $report_end; ?> of <?php echo $report_total_records; ?> entries
                        </div>
                        <?php if ($report_total_pages > 1): ?>
                        <nav class="flex justify-end items-center -space-x-px" aria-label="Pagination">
                            <?php if ($report_page > 1): ?>
                                <a href="?<?php echo http_build_query(array_merge($_GET, ['report_page' => $report_page - 1])); ?>" class="min-h-9.5 min-w-9.5 py-2 px-2.5 inline-flex justify-center items-center gap-x-1.5 text-sm first:rounded-s-lg last:rounded-e-lg border border-gray-200 text-gray-800 hover:bg-gray-100 focus:outline-hidden focus:bg-gray-100" aria-label="Previous">
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
                            $report_start_page = max(1, $report_page - 2);
                            $report_end_page = min($report_total_pages, $report_page + 2);
                            if ($report_start_page > 1): ?>
                                <a href="?<?php echo http_build_query(array_merge($_GET, ['report_page' => 1])); ?>" class="min-h-9.5 min-w-9.5 flex justify-center items-center border border-gray-200 text-gray-800 hover:bg-gray-100 py-2 px-3 text-sm first:rounded-s-lg last:rounded-e-lg focus:outline-hidden focus:bg-gray-100">1</a>
                                <?php if ($report_start_page > 2): ?>
                                    <span class="min-h-9.5 min-w-9.5 flex justify-center items-center border border-gray-200 text-gray-800 py-2 px-3 text-sm">...</span>
                                <?php endif; ?>
                            <?php endif; ?>
                            <?php for ($i = $report_start_page; $i <= $report_end_page; $i++): ?>
                                <?php if ($i == $report_page): ?>
                                    <button type="button" class="min-h-9.5 min-w-9.5 flex justify-center items-center bg-gray-200 text-gray-800 border border-gray-200 py-2 px-3 text-sm first:rounded-s-lg last:rounded-e-lg focus:outline-hidden focus:bg-gray-300" aria-current="page"><?php echo $i; ?></button>
                                <?php else: ?>
                                    <a href="?<?php echo http_build_query(array_merge($_GET, ['report_page' => $i])); ?>" class="min-h-9.5 min-w-9.5 flex justify-center items-center border border-gray-200 text-gray-800 hover:bg-gray-100 py-2 px-3 text-sm first:rounded-s-lg last:rounded-e-lg focus:outline-hidden focus:bg-gray-100"><?php echo $i; ?></a>
                                <?php endif; ?>
                            <?php endfor; ?>
                            <?php if ($report_end_page < $report_total_pages): ?>
                                <?php if ($report_end_page < $report_total_pages - 1): ?>
                                    <span class="min-h-9.5 min-w-9.5 flex justify-center items-center border border-gray-200 text-gray-800 py-2 px-3 text-sm">...</span>
                                <?php endif; ?>
                                <a href="?<?php echo http_build_query(array_merge($_GET, ['report_page' => $report_total_pages])); ?>" class="min-h-9.5 min-w-9.5 flex justify-center items-center border border-gray-200 text-gray-800 hover:bg-gray-100 py-2 px-3 text-sm first:rounded-s-lg last:rounded-e-lg focus:outline-hidden focus:bg-gray-100"><?php echo $report_total_pages; ?></a>
                            <?php endif; ?>
                            <?php if ($report_page < $report_total_pages): ?>
                                <a href="?<?php echo http_build_query(array_merge($_GET, ['report_page' => $report_page + 1])); ?>" class="min-h-9.5 min-w-9.5 py-2 px-2.5 inline-flex justify-center items-center gap-x-1.5 text-sm first:rounded-s-lg last:rounded-e-lg border border-gray-200 text-gray-800 hover:bg-gray-100 focus:outline-hidden focus:bg-gray-100" aria-label="Next">
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
                <?php else: ?>
                    <div class="text-center py-12">
                        <i class="ri-file-list-line text-6xl text-gray-300 mb-4"></i>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No Data Found</h3>
                        <p class="text-gray-500">
                            <?php if ($reportType === 'prescriptions'): ?>
                                No medication history found for the selected date range.
                            <?php elseif ($reportType === 'medicines'): ?>
                                No medicine dispensing data found for the selected date range.
                            <?php elseif ($reportType === 'appointments'): ?>
                                No appointments found for the selected date range.
                            <?php elseif ($reportType === 'system_logs'): ?>
                                No system activity logs found for the selected date range.
                            <?php else: ?>
                                No data available for the selected criteria.
                            <?php endif; ?>
                        </p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>

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
function printReport() {
    const reportContent = document.getElementById('reportContent');
    const reportTitle = <?php echo json_encode($reportTitle); ?>;
    const currentDate = new Date().toLocaleDateString();
    const recordCount = <?php echo count($reportData); ?>;
    const reportType = <?php echo json_encode($reportType); ?>;
    const startDate = <?php echo json_encode($startDate); ?>;
    const endDate = <?php echo json_encode($endDate); ?>;
    
    let dateRangeInfo = '';
    if (['appointments', 'prescriptions', 'system_logs'].includes(reportType)) {
        dateRangeInfo = `<p>Date Range: ${startDate} to ${endDate}</p>`;
    }
    
    const printWindow = window.open('', '_blank');
    printWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>${reportTitle}</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; }
                .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #ccc; padding-bottom: 20px; }
                .header h1 { margin: 0; color: #2B7BE4; }
                .header p { margin: 5px 0; color: #666; }
                table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                th { background-color: #f8f9fa; font-weight: bold; }
                tr:nth-child(even) { background-color: #f9f9f9; }
                .no-data { text-align: center; padding: 40px; color: #666; }
                @media print {
                    body { margin: 0; }
                    .header { page-break-after: avoid; }
                }
            </style>
        </head>
        <body>
            <div class="header">
                <h1>${reportTitle}</h1>
                <p>Generated on: ${currentDate}</p>
                <p>Total Records: ${recordCount}</p>
                ${dateRangeInfo}
            </div>
            ${reportContent.innerHTML}
        </body>
        </html>
    `);
    printWindow.document.close();
    printWindow.focus();
    printWindow.print();
}

function exportToCSV() {
    const reportTitle = <?php echo json_encode($reportTitle); ?>;
    const columns = <?php echo json_encode($reportColumns); ?>;
    const data = <?php echo json_encode($reportData); ?>;
    
    let csvContent = columns.join(',') + '\n';
    
    data.forEach(row => {
        const values = Object.values(row).map(value => {
            const stringValue = String(value || '');
            return stringValue.includes(',') || stringValue.includes('"') || stringValue.includes('\n') 
                ? `"${stringValue.replace(/"/g, '""')}"` 
                : stringValue;
        });
        csvContent += values.join(',') + '\n';
    });
    
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);
    link.setAttribute('href', url);
    link.setAttribute('download', `${reportTitle.replace(/[^a-z0-9]/gi, '_').toLowerCase()}_${new Date().toISOString().split('T')[0]}.csv`);
    link.style.visibility = 'hidden';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

// Auto-submit form when report type changes
document.getElementById('report').addEventListener('change', function() {
    this.form.submit();
});
</script>

<?php include '../includes/footer.php'; ?>