<?php
include '../includea/header.php';

// Database connection and pagination setup
try {
    $db = new PDO('mysql:host=localhost;dbname=clinic_management_system;charset=utf8', 'root', '');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Pagination settings
    $records_per_page = 10;
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $page = max($page, 1); // Ensure page is at least 1
    $offset = ($page - 1) * $records_per_page;
    
    // Create a reports table if it doesn't exist for better demo
    $db->exec("CREATE TABLE IF NOT EXISTS reports (
        id INT AUTO_INCREMENT PRIMARY KEY,
        date DATE NOT NULL,
        type VARCHAR(50) NOT NULL,
        summary TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    
    // Insert sample data if table is empty
    $count_check = $db->query('SELECT COUNT(*) FROM reports')->fetchColumn();
    if ($count_check == 0) {
        // Calculate dynamic data
        $visitCount = $db->query('SELECT COUNT(*) FROM prescriptions')->fetchColumn();
        $lowStockMed = $db->query("SELECT name FROM medicines WHERE quantity < 10 ORDER BY quantity ASC LIMIT 1")->fetchColumn();
        $pendingAppointments = $db->query("SELECT COUNT(*) FROM pending_prescriptions")->fetchColumn();
        $soonExpireMed = $db->query("SELECT name, expiry FROM medicines WHERE expiry BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 30 DAY) ORDER BY expiry ASC LIMIT 1")->fetch(PDO::FETCH_ASSOC);
        
        // Insert sample reports
        $sample_reports = [
            [date('Y-m-d'), 'Visits', "Total: {$visitCount} visits"],
            [date('Y-m-d', strtotime('-1 day')), 'Medications', ($lowStockMed ? htmlspecialchars($lowStockMed) . ' low stock' : 'No low stock')],
            [date('Y-m-d', strtotime('-1 day')), 'Medications', ($soonExpireMed ? htmlspecialchars($soonExpireMed['name']) . ' expiring soon (' . htmlspecialchars($soonExpireMed['expiry']) . ')' : 'No soon-to-expire')],
            [date('Y-m-d', strtotime('-2 days')), 'Appointments', "{$pendingAppointments} pending appointments"],
            [date('Y-m-d', strtotime('-3 days')), 'Inventory', 'Monthly stock review completed'],
            [date('Y-m-d', strtotime('-4 days')), 'Visits', 'Weekly patient summary'],
            [date('Y-m-d', strtotime('-5 days')), 'Medications', 'Prescription analysis report'],
            [date('Y-m-d', strtotime('-6 days')), 'Appointments', 'Daily appointment summary'],
            [date('Y-m-d', strtotime('-7 days')), 'Inventory', 'Low stock alert report'],
            [date('Y-m-d', strtotime('-8 days')), 'Visits', 'Patient demographics analysis'],
            [date('Y-m-d', strtotime('-9 days')), 'Medications', 'Drug interaction review'],
            [date('Y-m-d', strtotime('-10 days')), 'Appointments', 'Missed appointments report'],
            [date('Y-m-d', strtotime('-11 days')), 'Inventory', 'Expiry date monitoring'],
            [date('Y-m-d', strtotime('-12 days')), 'Visits', 'Treatment outcome analysis'],
            [date('Y-m-d', strtotime('-13 days')), 'Medications', 'Prescription volume report']
        ];
        
        $stmt = $db->prepare('INSERT INTO reports (date, type, summary) VALUES (?, ?, ?)');
        foreach ($sample_reports as $report) {
            $stmt->execute($report);
        }
    }
    
    // Get total count for pagination
    $total_count_stmt = $db->query('SELECT COUNT(*) FROM reports');
    $total_records = $total_count_stmt->fetchColumn();
    $total_pages = ceil($total_records / $records_per_page);
    
} catch (PDOException $e) {
    // Fallback values
    $total_records = 0;
    $total_pages = 0;
}
?>

<main class="flex-1 overflow-y-auto bg-gray-50 p-6 ml-16 md:ml-64 mt-[56px]">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Reports</h2>
        <!-- Report Generator -->
        <div class="bg-white rounded shadow p-6 mb-8">
            <form class="flex flex-wrap gap-4 items-end">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date Range</label>
                    <input type="date" class="border border-gray-300 rounded px-3 py-2 text-sm mr-2" value="2025-05-01" />
                    <span class="mx-1 text-gray-400">to</span>
                    <input type="date" class="border border-gray-300 rounded px-3 py-2 text-sm" value="2025-05-15" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Report Type</label>
                    <select class="border border-gray-300 rounded px-3 py-2 text-sm">
                        <option value="all">All</option>
                        <option value="visits">Visits</option>
                        <option value="medications">Medications</option>
                        <option value="appointments">Appointments</option>
                        <option value="inventory">Inventory</option>
                    </select>
                </div>
                <div>
                    <button type="submit" class="px-4 py-2 bg-primary text-white font-medium text-sm rounded-button hover:bg-primary/90">Generate</button>
                </div>
            </form>
        </div>
        <!-- Export Buttons -->
        <div class="flex justify-end mb-4 gap-2">
            <button class="px-4 py-2 bg-primary text-white font-medium text-sm rounded-button hover:bg-primary/90 flex items-center"><i class="ri-file-pdf-line mr-1"></i> Export PDF</button>
            <button class="px-4 py-2 bg-green-600 text-white font-medium text-sm rounded-button hover:bg-green-700 flex items-center"><i class="ri-file-excel-2-line mr-1"></i> Export CSV</button>
        </div>
        <!-- Sample Reports Table -->
        <div class="bg-white rounded shadow p-6 mb-8">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Sample Reports</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left font-semibold text-gray-600">Date</th>
                            <th class="px-4 py-2 text-left font-semibold text-gray-600">Type</th>
                            <th class="px-4 py-2 text-left font-semibold text-gray-600">Summary</th>
                            <th class="px-4 py-2 text-center font-semibold text-gray-600">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        try {
                            // Fetch reports with pagination
                            $stmt = $db->prepare('SELECT id, date, type, summary, created_at FROM reports ORDER BY date DESC LIMIT ' . (int)$records_per_page . ' OFFSET ' . (int)$offset);
                            $stmt->execute();
                            $reports = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            
                            if (count($reports) > 0):
                                foreach ($reports as $report): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-2"><?= htmlspecialchars($report['date']) ?></td>
                                    <td class="px-4 py-2"><?= htmlspecialchars($report['type']) ?></td>
                                    <td class="px-4 py-2"><?= htmlspecialchars($report['summary']) ?></td>
                                    <td class="px-4 py-2 text-center space-x-2">
                                        <button class="px-2 py-1 text-xs bg-blue-100 text-blue-700 rounded hover:bg-blue-200">View</button>
                                        <button class="px-2 py-1 text-xs bg-primary text-white rounded hover:bg-primary/90">Download</button>
                                    </td>
                                </tr>
                                <?php endforeach;
                            else: ?>
                                <tr>
                                    <td colspan="4" class="px-4 py-8 text-center text-gray-500">No reports found</td>
                                </tr>
                            <?php endif;
                        } catch (PDOException $e) { ?>
                            <tr>
                                <td colspan="4" class="px-4 py-8 text-center text-red-500">Error loading reports</td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination and Records Info -->
            <?php if ($total_records > 0): ?>
            <div class="flex justify-between items-center mt-6">
                <!-- Records Information -->
                <div class="text-sm text-gray-600">
                    <?php 
                    $start = $offset + 1;
                    $end = min($offset + $records_per_page, $total_records);
                    ?>
                    Showing <?php echo $start; ?> to <?php echo $end; ?> of <?php echo $total_records; ?> entries
                </div>

                <!-- Pagination Navigation -->
                <?php if ($total_pages > 1): ?>
                <nav class="flex justify-end items-center -space-x-px" aria-label="Pagination">
                    <!-- Previous Button -->
                    <?php if ($page > 1): ?>
                        <a href="?page=<?php echo $page - 1; ?>" class="min-h-9.5 min-w-9.5 py-2 px-2.5 inline-flex justify-center items-center gap-x-1.5 text-sm first:rounded-s-lg last:rounded-e-lg border border-gray-200 text-gray-800 hover:bg-gray-100 focus:outline-hidden focus:bg-gray-100" aria-label="Previous">
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
                    $start_page = max(1, $page - 2);
                    $end_page = min($total_pages, $page + 2);
                    
                    // Show first page if not in range
                    if ($start_page > 1): ?>
                        <a href="?page=1" class="min-h-9.5 min-w-9.5 flex justify-center items-center border border-gray-200 text-gray-800 hover:bg-gray-100 py-2 px-3 text-sm first:rounded-s-lg last:rounded-e-lg focus:outline-hidden focus:bg-gray-100">1</a>
                        <?php if ($start_page > 2): ?>
                            <span class="min-h-9.5 min-w-9.5 flex justify-center items-center border border-gray-200 text-gray-800 py-2 px-3 text-sm">...</span>
                        <?php endif; ?>
                    <?php endif; ?>

                    <?php for ($i = $start_page; $i <= $end_page; $i++): ?>
                        <?php if ($i == $page): ?>
                            <button type="button" class="min-h-9.5 min-w-9.5 flex justify-center items-center bg-gray-200 text-gray-800 border border-gray-200 py-2 px-3 text-sm first:rounded-s-lg last:rounded-e-lg focus:outline-hidden focus:bg-gray-300" aria-current="page"><?php echo $i; ?></button>
                        <?php else: ?>
                            <a href="?page=<?php echo $i; ?>" class="min-h-9.5 min-w-9.5 flex justify-center items-center border border-gray-200 text-gray-800 hover:bg-gray-100 py-2 px-3 text-sm first:rounded-s-lg last:rounded-e-lg focus:outline-hidden focus:bg-gray-100"><?php echo $i; ?></a>
                        <?php endif; ?>
                    <?php endfor; ?>

                    <!-- Show last page if not in range -->
                    <?php if ($end_page < $total_pages): ?>
                        <?php if ($end_page < $total_pages - 1): ?>
                            <span class="min-h-9.5 min-w-9.5 flex justify-center items-center border border-gray-200 text-gray-800 py-2 px-3 text-sm">...</span>
                        <?php endif; ?>
                        <a href="?page=<?php echo $total_pages; ?>" class="min-h-9.5 min-w-9.5 flex justify-center items-center border border-gray-200 text-gray-800 hover:bg-gray-100 py-2 px-3 text-sm first:rounded-s-lg last:rounded-e-lg focus:outline-hidden focus:bg-gray-100"><?php echo $total_pages; ?></a>
                    <?php endif; ?>

                    <!-- Next Button -->
                    <?php if ($page < $total_pages): ?>
                        <a href="?page=<?php echo $page + 1; ?>" class="min-h-9.5 min-w-9.5 py-2 px-2.5 inline-flex justify-center items-center gap-x-1.5 text-sm first:rounded-s-lg last:rounded-e-lg border border-gray-200 text-gray-800 hover:bg-gray-100 focus:outline-hidden focus:bg-gray-100" aria-label="Next">
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
        <!-- Auto-Scheduling Report Toggle -->
        <div class="bg-white rounded shadow p-6 flex items-center justify-between">
            <div>
                <h4 class="text-md font-semibold text-gray-800 mb-1">Auto-Scheduling Reports</h4>
                <p class="text-xs text-gray-500">Enable to receive scheduled reports automatically via email.</p>
            </div>
            <label class="toggle-switch">
                <input type="checkbox" checked>
                <span class="toggle-slider"></span>
            </label>
        </div>
</main>

<?php
include '../includea/footer.php';
?>