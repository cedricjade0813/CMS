<?php
include '../includes/header.php';
// Create medicines table if not exists
try {
    $db = new PDO('mysql:host=localhost;dbname=clinic_management_system;charset=utf8', 'root', '');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->exec("CREATE TABLE IF NOT EXISTS medicines (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        dosage VARCHAR(255) NOT NULL,
        quantity INT NOT NULL,
        expiry DATE NOT NULL
    )");
    // Fetch all medicines for the table
    $medicines = $db->query('SELECT * FROM medicines ORDER BY name ASC')->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die('Database connection failed: ' . $e->getMessage());
}

// Fetch prescription history for Issue Medication History
$prescriptionHistory = [];
try {
    // Get year filter parameter
    $filterYear = isset($_GET['year']) ? $_GET['year'] : '';
    
    // Build query with year filtering
    $query = 'SELECT prescription_date, patient_name, medicines, reason FROM prescriptions';
    $params = [];
    
    if ($filterYear) {
        $query .= ' WHERE YEAR(prescription_date) = ?';
        $params = [$filterYear];
    }
    
    $query .= ' ORDER BY prescription_date DESC';
    
    $prescStmt = $db->prepare($query);
    $prescStmt->execute($params);
    $prescriptionHistory = $prescStmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $prescriptionHistory = [];
}

// Pagination for Issue Medication History
// Flatten prescription history so each medicine entry is a separate row (even for same patient)
$flatPrescriptionHistory = [];
foreach ($prescriptionHistory as $presc) {
    $date = $presc['prescription_date'];
    $patient = $presc['patient_name'];
    $reason = $presc['reason'] ?? 'N/A';
    $meds = json_decode($presc['medicines'], true);
    if (is_array($meds)) {
        foreach ($meds as $med) {
            $flatPrescriptionHistory[] = [
                'prescription_date' => $date,
                'patient_name' => $patient,
                'reason' => $reason,
                'medicine' => $med['medicine'] ?? '',
                'quantity' => $med['quantity'] ?? ''
            ];
        }
    }
}
$historyPage = isset($_GET['history_page']) ? max(1, intval($_GET['history_page'])) : 1;
$historyPerPage = 10;
$historyTotal = count($flatPrescriptionHistory);
$historyTotalPages = ceil($historyTotal / $historyPerPage);
$historyStart = ($historyPage - 1) * $historyPerPage;
$historyPageData = array_slice($flatPrescriptionHistory, $historyStart, $historyPerPage);
?>
<!-- Dashboard Content -->
<main class="flex-1 overflow-y-auto bg-gray-50 p-6 ml-16 md:ml-64 mt-[56px]">
    <h2 class="text-2xl font-bold mb-6 text-gray-800">Inventory</h2>
    <!-- Filters -->
    <div class="flex flex-wrap gap-4 items-end mb-6">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Filter</label>
            <select id="medicineFilter" class="border border-gray-300 rounded px-3 py-2 text-sm">
                <option value="all">All</option>
                <?php
                // Use the same stockMap logic to populate filter options
                $stockMap = [];
                foreach ($medicines as $med) {
                    $key = strtolower(trim($med['name']));
                    if (!isset($stockMap[$key])) {
                        $stockMap[$key] = ucfirst(strtolower($med['name']));
                    }
                }
                foreach ($stockMap as $key => $name) {
                    echo '<option value="' . htmlspecialchars($key) . '">' . htmlspecialchars($name) . '</option>';
                }
                ?>
            </select>
        </div>
        <input id="medicineSearch" type="text" placeholder="Search medicine..." class="border border-gray-300 rounded px-3 py-2 text-sm" />
        <button id="addMedBtn"
            class="ml-auto px-4 py-2 bg-primary text-white rounded hover:bg-primary/90 flex items-center"><i
                class="ri-add-line mr-1"></i> Add Medicine</button>
    </div>
    <!-- Medicine Stock Available Table -->
    <div class="bg-white rounded shadow p-6 mb-8">
        <h3 class="text-lg font-semibold mb-4">Medicine Stock Available</h3>
        <div class="overflow-x-auto">
            <table id="medicineTable" class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left font-semibold text-gray-600">Name</th>
                        <th class="px-4 py-2 text-left font-semibold text-gray-600">Dosage</th>
                        <th class="px-4 py-2 text-left font-semibold text-gray-600">Total Quantity</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Group medicines by name (case-insensitive, trim spaces)
                    $stockMap = [];
                    foreach ($medicines as $med) {
                        $key = strtolower(trim($med['name']));
                        $dosage = $med['dosage'];
                        if (!isset($stockMap[$key])) {
                            $stockMap[$key] = [
                                'name' => ucfirst(strtolower($med['name'])),
                                'dosage' => $dosage,
                                'quantity' => (int)$med['quantity']
                            ];
                        } else {
                            $stockMap[$key]['quantity'] += (int)$med['quantity'];
                        }
                    }
                    $medicine_total_records = count($stockMap);
                    $medicine_records_per_page = 10;
                    $medicine_page = isset($_GET['medicine_page']) ? max(1, intval($_GET['medicine_page'])) : 1;
                    $medicine_total_pages = ceil($medicine_total_records / $medicine_records_per_page);
                    $medicine_offset = ($medicine_page - 1) * $medicine_records_per_page;
                    $medicine_stock_data = array_slice(array_values($stockMap), $medicine_offset, $medicine_records_per_page);
                    if (!empty($medicine_stock_data)) {
                        foreach ($medicine_stock_data as $stock) {
                            $lowerName = strtolower(trim($stock['name']));
                            echo '<tr data-name="' . htmlspecialchars($lowerName) . '">';
                            echo '<td class="px-4 py-2">' . htmlspecialchars($stock['name']) . '</td>';
                            echo '<td class="px-4 py-2">' . htmlspecialchars($stock['dosage']) . '</td>';
                            echo '<td class="px-4 py-2">' . htmlspecialchars($stock['quantity']) . '</td>';
                            echo '</tr>';
                        }
                    } else {
                        echo '<tr><td colspan="3" class="px-4 py-2 text-center text-gray-500">No medicine stock available.</td></tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>
            <!-- Pagination and Records Info for Medicine Stock Available -->
            <?php if ($medicine_total_records > 0): ?>
            <div class="flex justify-between items-center mt-6">
                <div class="text-sm text-gray-600">
                    <?php 
                    $medicine_start = $medicine_offset + 1;
                    $medicine_end = min($medicine_offset + $medicine_records_per_page, $medicine_total_records);
                    ?>
                    Showing <?php echo $medicine_start; ?> to <?php echo $medicine_end; ?> of <?php echo $medicine_total_records; ?> entries
                </div>
                <?php if ($medicine_total_pages > 1): ?>
                <nav class="flex justify-end items-center -space-x-px" aria-label="Pagination">
                    <?php if ($medicine_page > 1): ?>
                        <a href="?medicine_page=<?php echo $medicine_page - 1; ?>" class="min-h-9.5 min-w-9.5 py-2 px-2.5 inline-flex justify-center items-center gap-x-1.5 text-sm first:rounded-s-lg last:rounded-e-lg border border-gray-200 text-gray-800 hover:bg-gray-100 focus:outline-hidden focus:bg-gray-100" aria-label="Previous">
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
                    $medicine_start_page = max(1, $medicine_page - 2);
                    $medicine_end_page = min($medicine_total_pages, $medicine_page + 2);
                    if ($medicine_start_page > 1): ?>
                        <a href="?medicine_page=1" class="min-h-9.5 min-w-9.5 flex justify-center items-center border border-gray-200 text-gray-800 hover:bg-gray-100 py-2 px-3 text-sm first:rounded-s-lg last:rounded-e-lg focus:outline-hidden focus:bg-gray-100">1</a>
                        <?php if ($medicine_start_page > 2): ?>
                            <span class="min-h-9.5 min-w-9.5 flex justify-center items-center border border-gray-200 text-gray-800 py-2 px-3 text-sm">...</span>
                        <?php endif; ?>
                    <?php endif; ?>
                    <?php for ($i = $medicine_start_page; $i <= $medicine_end_page; $i++): ?>
                        <?php if ($i == $medicine_page): ?>
                            <button type="button" class="min-h-9.5 min-w-9.5 flex justify-center items-center bg-gray-200 text-gray-800 border border-gray-200 py-2 px-3 text-sm first:rounded-s-lg last:rounded-e-lg focus:outline-hidden focus:bg-gray-300" aria-current="page"><?php echo $i; ?></button>
                        <?php else: ?>
                            <a href="?medicine_page=<?php echo $i; ?>" class="min-h-9.5 min-w-9.5 flex justify-center items-center border border-gray-200 text-gray-800 hover:bg-gray-100 py-2 px-3 text-sm first:rounded-s-lg last:rounded-e-lg focus:outline-hidden focus:bg-gray-100"><?php echo $i; ?></a>
                        <?php endif; ?>
                    <?php endfor; ?>
                    <?php if ($medicine_end_page < $medicine_total_pages): ?>
                        <?php if ($medicine_end_page < $medicine_total_pages - 1): ?>
                            <span class="min-h-9.5 min-w-9.5 flex justify-center items-center border border-gray-200 text-gray-800 py-2 px-3 text-sm">...</span>
                        <?php endif; ?>
                        <a href="?medicine_page=<?php echo $medicine_total_pages; ?>" class="min-h-9.5 min-w-9.5 flex justify-center items-center border border-gray-200 text-gray-800 hover:bg-gray-100 py-2 px-3 text-sm first:rounded-s-lg last:rounded-e-lg focus:outline-hidden focus:bg-gray-100"><?php echo $medicine_total_pages; ?></a>
                    <?php endif; ?>
                    <?php if ($medicine_page < $medicine_total_pages): ?>
                        <a href="?medicine_page=<?php echo $medicine_page + 1; ?>" class="min-h-9.5 min-w-9.5 py-2 px-2.5 inline-flex justify-center items-center gap-x-1.5 text-sm first:rounded-s-lg last:rounded-e-lg border border-gray-200 text-gray-800 hover:bg-gray-100 focus:outline-hidden focus:bg-gray-100" aria-label="Next">
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
    <!-- Issue Medication History -->
    <div class="bg-white rounded shadow p-6 mb-8">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold">Issue Medication History</h3>
            
            <!-- Year Filter -->
            <div class="flex items-center gap-2">
                <label for="year" class="text-sm font-medium text-gray-700">Year:</label>
                <form method="GET" class="flex items-center gap-2">
                    <select name="year" id="year" class="border border-gray-300 rounded px-3 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent" onchange="this.form.submit()">
                        <option value="">All Years</option>
                        <?php
                        // Generate years from 1 to present (all years) but CSS will limit display
                        $currentYear = date('Y');
                        for ($year = $currentYear; $year >= 1; $year--) {
                            $selected = ($filterYear == $year) ? 'selected' : '';
                            echo "<option value='{$year}' {$selected}>{$year}</option>";
                        }
                        ?>
                    </select>
                    <?php if ($filterYear): ?>
                        <a href="inventory.php" class="text-sm text-gray-500 hover:text-gray-700 flex items-center">
                            <i class="ri-close-line"></i>
                        </a>
                    <?php endif; ?>
                </form>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table id="issueHistoryTable" class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left font-semibold text-gray-600">Date</th>
                        <th class="px-4 py-2 text-left font-semibold text-gray-600">Patient</th>
                        <th class="px-4 py-2 text-left font-semibold text-gray-600">Reason</th>
                        <th class="px-4 py-2 text-left font-semibold text-gray-600">Medicine</th>
                        <th class="px-4 py-2 text-left font-semibold text-gray-600">Quantity</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (!empty($historyPageData)) {
                        foreach ($historyPageData as $idx => $row) {
                            $date = htmlspecialchars($row['prescription_date']);
                            $patient = htmlspecialchars($row['patient_name']);
                            $reason = htmlspecialchars($row['reason']);
                            $medName = htmlspecialchars($row['medicine']);
                            $qty = htmlspecialchars($row['quantity']);
                            echo "<tr>";
                            echo "<td class='px-4 py-2 flex items-center gap-2'>";
                            echo "<button class='viewHistoryBtn text-primary hover:text-blue-700' data-idx='{$idx}' title='View Details'><i class='ri-eye-line text-lg'></i></button>";
                            echo $date;
                            echo "</td>";
                            echo "<td class='px-4 py-2'>" . $patient . "</td>";
                            echo "<td class='px-4 py-2'>" . $reason . "</td>";
                            echo "<td class='px-4 py-2'>" . $medName . "</td>";
                            echo "<td class='px-4 py-2'>" . $qty . "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='5' class='px-4 py-2 text-center text-gray-500'>No prescription history found.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
        <!-- Pagination and Records Info for Issue Medication History -->
        <?php if ($historyTotal > 0): ?>
        <div class="flex justify-between items-center mt-6">
            <div class="text-sm text-gray-600">
                <?php 
                $history_start = $historyStart + 1;
                $history_end = min($historyStart + $historyPerPage, $historyTotal);
                ?>
                Showing <?php echo $historyTotal == 0 ? 0 : $history_start; ?> to <?php echo $history_end; ?> of <?php echo $historyTotal; ?> entries
            </div>
            <?php if ($historyTotalPages > 1): ?>
            <nav class="flex justify-end items-center -space-x-px" aria-label="Pagination">
                <?php if ($historyPage > 1): ?>
                    <a href="?history_page=<?php echo $historyPage - 1; ?><?php echo $filterYear ? '&year=' . urlencode($filterYear) : ''; ?>" class="min-h-9.5 min-w-9.5 py-2 px-2.5 inline-flex justify-center items-center gap-x-1.5 text-sm first:rounded-s-lg last:rounded-e-lg border border-gray-200 text-gray-800 hover:bg-gray-100 focus:outline-hidden focus:bg-gray-100" aria-label="Previous">
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
                $history_start_page = max(1, $historyPage - 2);
                $history_end_page = min($historyTotalPages, $historyPage + 2);
                if ($history_start_page > 1): ?>
                    <a href="?history_page=1<?php echo $filterYear ? '&year=' . urlencode($filterYear) : ''; ?>" class="min-h-9.5 min-w-9.5 flex justify-center items-center border border-gray-200 text-gray-800 hover:bg-gray-100 py-2 px-3 text-sm first:rounded-s-lg last:rounded-e-lg focus:outline-hidden focus:bg-gray-100">1</a>
                    <?php if ($history_start_page > 2): ?>
                        <span class="min-h-9.5 min-w-9.5 flex justify-center items-center border border-gray-200 text-gray-800 py-2 px-3 text-sm">...</span>
                    <?php endif; ?>
                <?php endif; ?>
                <?php for ($i = $history_start_page; $i <= $history_end_page; $i++): ?>
                    <?php if ($i == $historyPage): ?>
                        <button type="button" class="min-h-9.5 min-w-9.5 flex justify-center items-center bg-gray-200 text-gray-800 border border-gray-200 py-2 px-3 text-sm first:rounded-s-lg last:rounded-e-lg focus:outline-hidden focus:bg-gray-300" aria-current="page"><?php echo $i; ?></button>
                    <?php else: ?>
                        <a href="?history_page=<?php echo $i; ?><?php echo $filterYear ? '&year=' . urlencode($filterYear) : ''; ?>" class="min-h-9.5 min-w-9.5 flex justify-center items-center border border-gray-200 text-gray-800 hover:bg-gray-100 py-2 px-3 text-sm first:rounded-s-lg last:rounded-e-lg focus:outline-hidden focus:bg-gray-100"><?php echo $i; ?></a>
                    <?php endif; ?>
                <?php endfor; ?>
                <?php if ($history_end_page < $historyTotalPages): ?>
                    <?php if ($history_end_page < $historyTotalPages - 1): ?>
                        <span class="min-h-9.5 min-w-9.5 flex justify-center items-center border border-gray-200 text-gray-800 py-2 px-3 text-sm">...</span>
                    <?php endif; ?>
                    <a href="?history_page=<?php echo $historyTotalPages; ?><?php echo $filterYear ? '&year=' . urlencode($filterYear) : ''; ?>" class="min-h-9.5 min-w-9.5 flex justify-center items-center border border-gray-200 text-gray-800 hover:bg-gray-100 py-2 px-3 text-sm first:rounded-s-lg last:rounded-e-lg focus:outline-hidden focus:bg-gray-100"><?php echo $historyTotalPages; ?></a>
                <?php endif; ?>
                <?php if ($historyPage < $historyTotalPages): ?>
                    <a href="?history_page=<?php echo $historyPage + 1; ?><?php echo $filterYear ? '&year=' . urlencode($filterYear) : ''; ?>" class="min-h-9.5 min-w-9.5 py-2 px-2.5 inline-flex justify-center items-center gap-x-1.5 text-sm first:rounded-s-lg last:rounded-e-lg border border-gray-200 text-gray-800 hover:bg-gray-100 focus:outline-hidden focus:bg-gray-100" aria-label="Next">
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
    <!-- Add Medicine Modal -->
    <div id="addMedModal" class="fixed inset-0 bg-black bg-opacity-30 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6 relative">
            <button id="closeAddMedModal" class="absolute top-2 right-2 text-gray-400 hover:text-gray-700">
                <i class="ri-close-line ri-2x"></i>
            </button>
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Add Medicine</h3>
            <form id="addMedForm">
                <div class="mb-4 relative">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Medicine Name</label>
                    <input type="text" name="name" id="medicineNameInput" class="w-full border border-gray-300 rounded px-3 py-2 text-sm" required autocomplete="off" />
                    <div id="medicineNameSuggestions" class="absolute top-full left-0 right-0 bg-white border border-gray-300 rounded-b shadow-lg max-h-40 overflow-y-auto z-10 hidden"></div>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Dosage</label>
                    <input type="text" name="dosage" class="w-full border border-gray-300 rounded px-3 py-2 text-sm" required />
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Quantity</label>
                    <input type="number" name="quantity" class="w-full border border-gray-300 rounded px-3 py-2 text-sm" min="1" required />
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Expiry Date</label>
                    <input type="date" name="expiry" class="w-full border border-gray-300 rounded px-3 py-2 text-sm" required />
                </div>
                <button type="submit" class="w-full bg-primary text-white py-2 rounded hover:bg-primary/90">Add
                    Medicine</button>
            </form>
        </div>
    </div>
    <!-- Add Edit Medicine Modal (hidden by default) -->
    <div id="editMedModal" class="fixed inset-0 bg-black bg-opacity-30 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6 relative">
            <button id="closeEditMedModal" class="absolute top-2 right-2 text-gray-400 hover:text-gray-700">
                <i class="ri-close-line ri-2x"></i>
            </button>
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Edit Medicine</h3>
            <form id="editMedForm">
                <input type="hidden" name="id" id="editMedId">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Medicine Name</label>
                    <input type="text" name="name" id="editMedName" class="w-full border border-gray-300 rounded px-3 py-2 text-sm" required />
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Dosage</label>
                    <input type="text" name="dosage" id="editMedDosage" class="w-full border border-gray-300 rounded px-3 py-2 text-sm" required />
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Quantity</label>
                    <input type="number" name="quantity" id="editMedQuantity" class="w-full border border-gray-300 rounded px-3 py-2 text-sm" min="1" required />
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Expiry Date</label>
                    <input type="date" name="expiry" id="editMedExpiry" class="w-full border border-gray-300 rounded px-3 py-2 text-sm" required />
                </div>
                <button type="submit" class="w-full bg-primary text-white py-2 rounded hover:bg-primary/90">Save Changes</button>
            </form>
        </div>
    </div>
    <!-- Modal for viewing prescription details -->
    <div id="historyViewModal" class="fixed inset-0 bg-black bg-opacity-30 flex items-center justify-center z-50 hidden">
        <div class="w-full max-w-md mx-4 flex flex-col bg-white border border-gray-200 shadow-2xl rounded-xl pointer-events-auto dark:bg-neutral-800 dark:border-neutral-700 dark:shadow-neutral-700/70">
            <div class="flex justify-between items-center py-3 px-4 border-b border-gray-200 dark:border-neutral-700">
                <h3 id="historyViewModalTitle" class="font-bold text-gray-800 dark:text-white">
                    Prescription Details
                </h3>
                <button id="closeHistoryViewModal" type="button" class="size-8 inline-flex justify-center items-center gap-x-2 rounded-full border border-transparent bg-gray-100 text-gray-800 hover:bg-gray-200 focus:outline-hidden focus:bg-gray-200 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-700 dark:hover:bg-neutral-600 dark:text-neutral-400 dark:focus:bg-neutral-600" aria-label="Close">
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
                        <div id="historyViewModalBody" class="text-sm text-gray-700 space-y-3"></div>
                    </div>
                </div>
            </div>
            
            <div class="flex justify-end items-center gap-x-2 py-3 px-4 border-t border-gray-200 dark:border-neutral-700">
                <button id="closeHistoryViewModalBottom" type="button" class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-white text-gray-800 shadow-sm hover:bg-gray-50 focus:outline-hidden focus:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-700 dark:text-white dark:hover:bg-neutral-700 dark:focus:bg-neutral-700">
                    Close
                </button>
            </div>
        </div>
    </div>
    <!-- Reusable centered modal for alerts/info messages -->
    <div id="centeredModal" class="fixed inset-0 flex items-start justify-center z-50 bg-black bg-opacity-40 hidden">
        <div id="centeredModalBox" class="rounded-lg shadow-lg max-w-sm w-full p-6 text-center relative mt-32 transition-all duration-200">
            <div id="centeredModalMsg" class="text-lg mb-2"></div>
        </div>
    </div>
    <!-- Delete confirmation modal -->
    <div id="deleteConfirmModal" class="fixed inset-0 flex items-start justify-center z-50 bg-black bg-opacity-40 hidden">
        <div class="rounded-lg shadow-lg max-w-sm w-full p-6 text-center relative mt-32 transition-all duration-200 bg-white">
            <div class="text-lg mb-4 text-red-600 font-semibold">Are you sure you want to delete this medicine?</div>
            <div class="flex justify-center gap-4">
                <button id="deleteConfirmYes" class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600">Delete</button>
                <button id="deleteConfirmNo" class="px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400">Cancel</button>
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
    // Medicine name autocomplete functionality
    const medicineNameInput = document.getElementById('medicineNameInput');
    const medicineNameSuggestions = document.getElementById('medicineNameSuggestions');
    let selectedIndex = -1;
    let suggestionTimeout = null;

    function fetchMedicineSuggestions(query) {
        fetch(`get_medicine_suggestions.php?q=${encodeURIComponent(query)}`)
            .then(response => response.json())
            .then(suggestions => {
                displaySuggestions(suggestions);
            })
            .catch(error => {
                console.error('Error fetching suggestions:', error);
                hideSuggestions();
            });
    }

    function displaySuggestions(suggestions) {
        medicineNameSuggestions.innerHTML = '';
        selectedIndex = -1;
        
        if (suggestions.length === 0) {
            hideSuggestions();
            return;
        }
        
        suggestions.forEach((suggestion, index) => {
            const div = document.createElement('div');
            div.className = 'suggestion-item';
            div.textContent = suggestion;
            div.addEventListener('click', function() {
                selectSuggestion(suggestion);
            });
            medicineNameSuggestions.appendChild(div);
        });
        
        medicineNameSuggestions.classList.remove('hidden');
    }

    function updateHighlight(suggestions) {
        suggestions.forEach((item, index) => {
            if (index === selectedIndex) {
                item.classList.add('highlighted');
            } else {
                item.classList.remove('highlighted');
            }
        });
    }

    function selectSuggestion(suggestion) {
        medicineNameInput.value = suggestion;
        hideSuggestions();
        medicineNameInput.focus();
    }

    function hideSuggestions() {
        if (medicineNameSuggestions) {
            medicineNameSuggestions.classList.add('hidden');
            selectedIndex = -1;
        }
    }

    // Set up autocomplete event listeners when DOM is loaded
    document.addEventListener('DOMContentLoaded', function() {
        if (medicineNameInput) {
            medicineNameInput.addEventListener('input', function() {
                const query = this.value.trim();
                
                // Clear previous timeout
                if (suggestionTimeout) {
                    clearTimeout(suggestionTimeout);
                }
                
                if (query.length < 1) {
                    hideSuggestions();
                    return;
                }
                
                // Debounce the API call
                suggestionTimeout = setTimeout(() => {
                    fetchMedicineSuggestions(query);
                }, 300);
            });

            medicineNameInput.addEventListener('keydown', function(e) {
                const suggestions = medicineNameSuggestions.querySelectorAll('.suggestion-item');
                
                if (e.key === 'ArrowDown') {
                    e.preventDefault();
                    selectedIndex = Math.min(selectedIndex + 1, suggestions.length - 1);
                    updateHighlight(suggestions);
                } else if (e.key === 'ArrowUp') {
                    e.preventDefault();
                    selectedIndex = Math.max(selectedIndex - 1, -1);
                    updateHighlight(suggestions);
                } else if (e.key === 'Enter') {
                    e.preventDefault();
                    if (selectedIndex >= 0 && suggestions[selectedIndex]) {
                        selectSuggestion(suggestions[selectedIndex].textContent);
                    }
                } else if (e.key === 'Escape') {
                    hideSuggestions();
                }
            });

            // Hide suggestions when clicking outside
            document.addEventListener('click', function(e) {
                if (!medicineNameInput.contains(e.target) && !medicineNameSuggestions.contains(e.target)) {
                    hideSuggestions();
                }
            });
        }
    });

    // Modal logic
    const addMedBtn = document.getElementById('addMedBtn');
    const addMedModal = document.getElementById('addMedModal');
    const closeAddMedModal = document.getElementById('closeAddMedModal');
    addMedBtn.addEventListener('click', () => {
        addMedModal.classList.remove('hidden');
        // Clear form and suggestions when opening modal
        document.querySelector('#addMedModal form').reset();
        hideSuggestions();
    });
    closeAddMedModal.addEventListener('click', () => {
        addMedModal.classList.add('hidden');
        hideSuggestions();
    });
    window.addEventListener('click', (e) => {
        if (e.target === addMedModal) {
            addMedModal.classList.add('hidden');
            hideSuggestions();
        }
    });
    // Prevent form submit (demo)
    document.querySelector('#addMedModal form').addEventListener('submit', function (e) {
        e.preventDefault();
        addMedModal.classList.add('hidden');
    });

    // Add Medicine Modal logic with backend integration
    document.querySelector('#addMedModal form').addEventListener('submit', function (e) {
        e.preventDefault();
        const name = this.querySelector('input[name="name"]').value;
        const dosage = this.querySelector('input[name="dosage"]').value;
        const quantity = this.querySelector('input[name="quantity"]').value;
        const expiry = this.querySelector('input[name="expiry"]').value;
        fetch('add_medicine.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `name=${encodeURIComponent(name)}&dosage=${encodeURIComponent(dosage)}&quantity=${encodeURIComponent(quantity)}&expiry=${encodeURIComponent(expiry)}`
        })
        .then(res => res.json())
        .then (data => {
            if(data.success) {
                showSuccessModal('Medicine added!', 'Success');
                setTimeout(() => location.reload(), 1200);
            } else {
                showErrorModal('Error: ' + data.message, 'Error');
            }
        })
        .catch(() => showErrorModal('Error adding medicine.', 'Error'));
    });

    // Edit Medicine Modal logic
    const editMedModal = document.getElementById('editMedModal');
    const closeEditMedModal = document.getElementById('closeEditMedModal');
    const editMedForm = document.getElementById('editMedForm');
    let currentEditRow = null;
    document.querySelectorAll('.editMedBtn').forEach(btn => {
        btn.addEventListener('click', function() {
            const row = this.closest('tr');
            currentEditRow = row;
            const id = row.getAttribute('data-id');
            const name = row.children[0].textContent.trim();
            const dosage = row.children[1].textContent.trim();
            const quantity = row.children[2].textContent.trim();
            const expiry = row.children[4].textContent.trim();
            document.getElementById('editMedId').value = id;
            document.getElementById('editMedName').value = name;
            document.getElementById('editMedDosage').value = dosage;
            document.getElementById('editMedQuantity').value = quantity;
            document.getElementById('editMedExpiry').value = expiry;
            editMedModal.classList.remove('hidden');
        });
    });
    closeEditMedModal.addEventListener('click', () => editMedModal.classList.add('hidden'));
    window.addEventListener('click', (e) => {
        if (e.target === editMedModal) editMedModal.classList.add('hidden');
    });
    editMedForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const id = document.getElementById('editMedId').value;
        const name = document.getElementById('editMedName').value;
        const dosage = document.getElementById('editMedDosage').value;
        const quantity = document.getElementById('editMedQuantity').value;
        const expiry = document.getElementById('editMedExpiry').value;
        fetch('edit_medicine.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `id=${encodeURIComponent(id)}&name=${encodeURIComponent(name)}&dosage=${encodeURIComponent(dosage)}&quantity=${encodeURIComponent(quantity)}&expiry=${encodeURIComponent(expiry)}`
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                showSuccessModal('Medicine updated!', 'Success');
                setTimeout(() => location.reload(), 1200);
            } else {
                showErrorModal('Error: ' + data.message, 'Error');
            }
        })
        .catch(() => showErrorModal('Error updating medicine.', 'Error'));
        editMedModal.classList.add('hidden');
    });

    // Delete Medicine logic
    const deleteMedBtns = document.querySelectorAll('.deleteMedBtn');
    let deleteMedicineId = null;
    const deleteConfirmModal = document.getElementById('deleteConfirmModal');
    const deleteConfirmYes = document.getElementById('deleteConfirmYes');
    const deleteConfirmNo = document.getElementById('deleteConfirmNo');
    deleteMedBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const row = this.closest('tr');
            deleteMedicineId = row.getAttribute('data-id');
            // Show confirmation modal
            deleteConfirmModal.classList.remove('hidden');
        });
    });
    deleteConfirmYes.addEventListener('click', function() {
        if (!deleteMedicineId) return;
        fetch('delete_medicine.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `id=${encodeURIComponent(deleteMedicineId)}`
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                showSuccessModal('Medicine deleted!', 'Success');
                setTimeout(() => location.reload(), 1200);
            } else {
                showErrorModal('Error: ' + data.message, 'Error');
            }
        })
        .catch(() => showErrorModal('Error deleting medicine.', 'Error'));
        deleteConfirmModal.classList.add('hidden');
        deleteMedicineId = null;
    });
    deleteConfirmNo.addEventListener('click', function() {
        deleteConfirmModal.classList.add('hidden');
        deleteMedicineId = null;
    });
    window.addEventListener('click', (e) => {
        if (e.target === deleteConfirmModal) {
            deleteConfirmModal.classList.add('hidden');
            deleteMedicineId = null;
        }
    });



    // Medicine filter and search logic
    const medicineFilter = document.getElementById('medicineFilter');
    const medicineSearch = document.getElementById('medicineSearch');
    const medicineTable = document.getElementById('medicineTable');
    
    if (medicineFilter && medicineSearch && medicineTable) {
        medicineFilter.addEventListener('change', filterMedicines);
        medicineSearch.addEventListener('input', filterMedicines);
        
        function filterMedicines() {
            const filter = medicineFilter.value.toLowerCase();
            const search = medicineSearch.value.toLowerCase();
            const rows = medicineTable.querySelectorAll('tbody tr');
            
            rows.forEach(row => {
                const nameAttr = row.getAttribute('data-name');
                if (!nameAttr) return; // Skip rows without data-name (like "no data" rows)
                
                const name = nameAttr.toLowerCase();
                const matchesFilter = (filter === 'all' || name === filter);
                const matchesSearch = name.includes(search);
                row.style.display = (matchesFilter && matchesSearch) ? '' : 'none';
            });
        }
    }

    // Issue Medication History View Modal logic
    const historyData = <?php echo json_encode(array_values($historyPageData)); ?>;
    const viewBtns = document.querySelectorAll('.viewHistoryBtn');
    const viewModal = document.getElementById('historyViewModal');
    const closeViewModal = document.getElementById('closeHistoryViewModal');
    const closeViewModalBottom = document.getElementById('closeHistoryViewModalBottom');
    const viewModalTitle = document.getElementById('historyViewModalTitle');
    const viewModalBody = document.getElementById('historyViewModalBody');
    viewBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const idx = this.getAttribute('data-idx');
            const row = historyData[idx];
            viewModalTitle.textContent = row.patient_name;
            viewModalBody.innerHTML = `
                <div class="grid grid-cols-[120px_1fr] gap-3 items-center">
                    <label class="text-sm font-medium text-gray-700 dark:text-neutral-300">Date:</label>
                    <p class="text-sm text-gray-900 dark:text-neutral-200">${row.prescription_date}</p>
                </div>
                <div class="grid grid-cols-[120px_1fr] gap-3 items-center">
                    <label class="text-sm font-medium text-gray-700 dark:text-neutral-300">Patient:</label>
                    <p class="text-sm text-gray-900 dark:text-neutral-200">${row.patient_name}</p>
                </div>
                <div class="grid grid-cols-[120px_1fr] gap-3 items-center">
                    <label class="text-sm font-medium text-gray-700 dark:text-neutral-300">Medicine:</label>
                    <p class="text-sm text-gray-900 dark:text-neutral-200">${row.medicine}</p>
                </div>
                <div class="grid grid-cols-[120px_1fr] gap-3 items-center">
                    <label class="text-sm font-medium text-gray-700 dark:text-neutral-300">Quantity:</label>
                    <p class="text-sm text-gray-900 dark:text-neutral-200">${row.quantity}</p>
                </div>
                <div class="grid grid-cols-[120px_1fr] gap-3 items-start">
                    <label class="text-sm font-medium text-gray-700 dark:text-neutral-300">Reason:</label>
                    <p class="text-sm text-gray-900 dark:text-neutral-200">${row.reason}</p>
                </div>
            `;
            viewModal.classList.remove('hidden');
        });
    });
    closeViewModal.addEventListener('click', () => viewModal.classList.add('hidden'));
    closeViewModalBottom.addEventListener('click', () => viewModal.classList.add('hidden'));
    window.addEventListener('click', (e) => {
        if (e.target === viewModal) viewModal.classList.add('hidden');
    });
</script>
<?php
include '../includes/footer.php';
?>