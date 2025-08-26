<?php
include '../includes/header.php';
// Connect to DB and fetch medicines
try {
    $db = new PDO('mysql:host=localhost;dbname=clinic_management_system;charset=utf8', 'root', '');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Get year filter parameter
    $filterYear = isset($_GET['year']) ? $_GET['year'] : '';
    
    // Build query with year filtering
    $query = 'SELECT * FROM medicines';
    $params = [];
    
    if ($filterYear) {
        $query .= ' WHERE YEAR(created_at) = ? OR YEAR(expiry) = ?';
        $params = [$filterYear, $filterYear];
    }
    
    $query .= ' ORDER BY name ASC';
    
    $stmt = $db->prepare($query);
    $stmt->execute($params);
    $medicines = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die('Database connection failed: ' . $e->getMessage());
}
?>
<main class="flex-1 overflow-y-auto bg-gray-50 p-6 ml-16 md:ml-64 mt-[56px]">
    <h2 class="text-2xl font-bold mb-6 text-gray-800">Medicine List</h2>
    
    <!-- Year Filter -->
    <div class="bg-white rounded shadow p-4 mb-6">
        <div class="flex items-center gap-2">
            <label for="year" class="text-sm font-medium text-gray-700">Filter by Year:</label>
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
                    <a href="list.php" class="text-sm text-gray-500 hover:text-gray-700 flex items-center">
                        <i class="ri-close-line"></i>
                    </a>
                <?php endif; ?>
            </form>
        </div>
    </div>
    
    <div class="bg-white rounded shadow p-6 mb-8">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left font-semibold text-gray-600">Name</th>
                        <th class="px-4 py-2 text-left font-semibold text-gray-600">Dosage</th>
                        <th class="px-4 py-2 text-left font-semibold text-gray-600">Quantity</th>
                        <th class="px-4 py-2 text-left font-semibold text-gray-600">Date Added</th>
                        <th class="px-4 py-2 text-left font-semibold text-gray-600">Expiry</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $today = date('Y-m-d');
                    $nonExpiredMeds = array_filter($medicines, function($med) use ($today) {
                        return $med['expiry'] >= $today;
                    });
                    $medlist_total_records = count($nonExpiredMeds);
                    $medlist_records_per_page = 10;
                    $medlist_page = isset($_GET['medlist_page']) ? max(1, intval($_GET['medlist_page'])) : 1;
                    $medlist_total_pages = ceil($medlist_total_records / $medlist_records_per_page);
                    $medlist_offset = ($medlist_page - 1) * $medlist_records_per_page;
                    $medlist_data = array_slice(array_values($nonExpiredMeds), $medlist_offset, $medlist_records_per_page);
                    foreach ($medlist_data as $med): ?>
                    <tr>
                        <td class="px-4 py-2"><?php echo htmlspecialchars($med['name']); ?></td>
                        <td class="px-4 py-2"><?php echo htmlspecialchars($med['dosage']); ?></td>
                        <td class="px-4 py-2"><?php echo htmlspecialchars($med['quantity']); ?></td>
                        <td class="px-4 py-2"><?php echo isset($med['created_at']) ? htmlspecialchars($med['created_at']) : '-'; ?></td>
                        <td class="px-4 py-2<?php echo (strtotime($med['expiry']) < strtotime('+30 days')) ? ' text-red-600 font-semibold' : ''; ?>"><?php echo htmlspecialchars($med['expiry']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <!-- Pagination and Records Info for Medicine List -->
        <?php if ($medlist_total_records > 0): ?>
        <div class="flex justify-between items-center mt-6">
            <div class="text-sm text-gray-600">
                <?php 
                $medlist_start = $medlist_offset + 1;
                $medlist_end = min($medlist_offset + $medlist_records_per_page, $medlist_total_records);
                ?>
                Showing <?php echo $medlist_start; ?> to <?php echo $medlist_end; ?> of <?php echo $medlist_total_records; ?> entries
            </div>
            <?php if ($medlist_total_pages > 1): ?>
            <nav class="flex justify-end items-center -space-x-px" aria-label="Pagination">
                <?php if ($medlist_page > 1): ?>
                    <a href="?medlist_page=<?php echo $medlist_page - 1; ?><?php echo $filterYear ? '&year=' . urlencode($filterYear) : ''; ?>" class="min-h-9.5 min-w-9.5 py-2 px-2.5 inline-flex justify-center items-center gap-x-1.5 text-sm first:rounded-s-lg last:rounded-e-lg border border-gray-200 text-gray-800 hover:bg-gray-100 focus:outline-hidden focus:bg-gray-100" aria-label="Previous">
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
                $medlist_start_page = max(1, $medlist_page - 2);
                $medlist_end_page = min($medlist_total_pages, $medlist_page + 2);
                if ($medlist_start_page > 1): ?>
                    <a href="?medlist_page=1<?php echo $filterYear ? '&year=' . urlencode($filterYear) : ''; ?>" class="min-h-9.5 min-w-9.5 flex justify-center items-center border border-gray-200 text-gray-800 hover:bg-gray-100 py-2 px-3 text-sm first:rounded-s-lg last:rounded-e-lg focus:outline-hidden focus:bg-gray-100">1</a>
                    <?php if ($medlist_start_page > 2): ?>
                        <span class="min-h-9.5 min-w-9.5 flex justify-center items-center border border-gray-200 text-gray-800 py-2 px-3 text-sm">...</span>
                    <?php endif; ?>
                <?php endif; ?>
                <?php for ($i = $medlist_start_page; $i <= $medlist_end_page; $i++): ?>
                    <?php if ($i == $medlist_page): ?>
                        <button type="button" class="min-h-9.5 min-w-9.5 flex justify-center items-center bg-gray-200 text-gray-800 border border-gray-200 py-2 px-3 text-sm first:rounded-s-lg last:rounded-e-lg focus:outline-hidden focus:bg-gray-300" aria-current="page"><?php echo $i; ?></button>
                    <?php else: ?>
                        <a href="?medlist_page=<?php echo $i; ?><?php echo $filterYear ? '&year=' . urlencode($filterYear) : ''; ?>" class="min-h-9.5 min-w-9.5 flex justify-center items-center border border-gray-200 text-gray-800 hover:bg-gray-100 py-2 px-3 text-sm first:rounded-s-lg last:rounded-e-lg focus:outline-hidden focus:bg-gray-100"><?php echo $i; ?></a>
                    <?php endif; ?>
                <?php endfor; ?>
                <?php if ($medlist_end_page < $medlist_total_pages): ?>
                    <?php if ($medlist_end_page < $medlist_total_pages - 1): ?>
                        <span class="min-h-9.5 min-w-9.5 flex justify-center items-center border border-gray-200 text-gray-800 py-2 px-3 text-sm">...</span>
                    <?php endif; ?>
                    <a href="?medlist_page=<?php echo $medlist_total_pages; ?><?php echo $filterYear ? '&year=' . urlencode($filterYear) : ''; ?>" class="min-h-9.5 min-w-9.5 flex justify-center items-center border border-gray-200 text-gray-800 hover:bg-gray-100 py-2 px-3 text-sm first:rounded-s-lg last:rounded-e-lg focus:outline-hidden focus:bg-gray-100"><?php echo $medlist_total_pages; ?></a>
                <?php endif; ?>
                <?php if ($medlist_page < $medlist_total_pages): ?>
                    <a href="?medlist_page=<?php echo $medlist_page + 1; ?><?php echo $filterYear ? '&year=' . urlencode($filterYear) : ''; ?>" class="min-h-9.5 min-w-9.5 py-2 px-2.5 inline-flex justify-center items-center gap-x-1.5 text-sm first:rounded-s-lg last:rounded-e-lg border border-gray-200 text-gray-800 hover:bg-gray-100 focus:outline-hidden focus:bg-gray-100" aria-label="Next">
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
    <!-- Expired Medicines Table -->
    <div class="bg-white rounded shadow p-6 mb-8">
        <h3 class="text-lg font-semibold mb-4 text-red-600">Expired Medicines</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left font-semibold text-gray-600">Name</th>
                        <th class="px-4 py-2 text-left font-semibold text-gray-600">Dosage</th>
                        <th class="px-4 py-2 text-left font-semibold text-gray-600">Quantity</th>
                        <th class="px-4 py-2 text-left font-semibold text-gray-600">Date Added</th>
                        <th class="px-4 py-2 text-left font-semibold text-gray-600">Expiry</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $today = date('Y-m-d');
                    $expiredMeds = array_filter($medicines, function($med) use ($today) {
                        return $med['expiry'] < $today;
                    });
                    $expired_total_records = count($expiredMeds);
                    $expired_records_per_page = 10;
                    $expired_page = isset($_GET['expired_page']) ? max(1, intval($_GET['expired_page'])) : 1;
                    $expired_total_pages = ceil($expired_total_records / $expired_records_per_page);
                    $expired_offset = ($expired_page - 1) * $expired_records_per_page;
                    $expired_data = array_slice(array_values($expiredMeds), $expired_offset, $expired_records_per_page);
                    if (!empty($expired_data)) {
                        foreach ($expired_data as $med) {
                            echo '<tr>';
                            echo '<td class="px-4 py-2">' . htmlspecialchars($med['name']) . '</td>';
                            echo '<td class="px-4 py-2">' . htmlspecialchars($med['dosage']) . '</td>';
                            echo '<td class="px-4 py-2">' . htmlspecialchars($med['quantity']) . '</td>';
                            echo '<td class="px-4 py-2">' . (isset($med['created_at']) ? htmlspecialchars($med['created_at']) : '-') . '</td>';
                            echo '<td class="px-4 py-2 text-red-600 font-semibold">' . htmlspecialchars($med['expiry']) . '</td>';
                            echo '</tr>';
                        }
                    } else {
                        echo '<tr><td colspan="5" class="px-4 py-2 text-center text-gray-500">No expired medicines.</td></tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>
        <!-- Pagination and Records Info for Expired Medicines -->
        <?php if ($expired_total_records > 0): ?>
        <div class="flex justify-between items-center mt-6">
            <div class="text-sm text-gray-600">
                <?php 
                $expired_start = $expired_offset + 1;
                $expired_end = min($expired_offset + $expired_records_per_page, $expired_total_records);
                ?>
                Showing <?php echo $expired_start; ?> to <?php echo $expired_end; ?> of <?php echo $expired_total_records; ?> entries
            </div>
            <?php if ($expired_total_pages > 1): ?>
            <nav class="flex justify-end items-center -space-x-px" aria-label="Pagination">
                <?php if ($expired_page > 1): ?>
                    <a href="?expired_page=<?php echo $expired_page - 1; ?><?php echo $filterYear ? '&year=' . urlencode($filterYear) : ''; ?>" class="min-h-9.5 min-w-9.5 py-2 px-2.5 inline-flex justify-center items-center gap-x-1.5 text-sm first:rounded-s-lg last:rounded-e-lg border border-gray-200 text-gray-800 hover:bg-gray-100 focus:outline-hidden focus:bg-gray-100" aria-label="Previous">
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
                $expired_start_page = max(1, $expired_page - 2);
                $expired_end_page = min($expired_total_pages, $expired_page + 2);
                if ($expired_start_page > 1): ?>
                    <a href="?expired_page=1<?php echo $filterYear ? '&year=' . urlencode($filterYear) : ''; ?>" class="min-h-9.5 min-w-9.5 flex justify-center items-center border border-gray-200 text-gray-800 hover:bg-gray-100 py-2 px-3 text-sm first:rounded-s-lg last:rounded-e-lg focus:outline-hidden focus:bg-gray-100">1</a>
                    <?php if ($expired_start_page > 2): ?>
                        <span class="min-h-9.5 min-w-9.5 flex justify-center items-center border border-gray-200 text-gray-800 py-2 px-3 text-sm">...</span>
                    <?php endif; ?>
                <?php endif; ?>
                <?php for ($i = $expired_start_page; $i <= $expired_end_page; $i++): ?>
                    <?php if ($i == $expired_page): ?>
                        <button type="button" class="min-h-9.5 min-w-9.5 flex justify-center items-center bg-gray-200 text-gray-800 border border-gray-200 py-2 px-3 text-sm first:rounded-s-lg last:rounded-e-lg focus:outline-hidden focus:bg-gray-300" aria-current="page"><?php echo $i; ?></button>
                    <?php else: ?>
                        <a href="?expired_page=<?php echo $i; ?><?php echo $filterYear ? '&year=' . urlencode($filterYear) : ''; ?>" class="min-h-9.5 min-w-9.5 flex justify-center items-center border border-gray-200 text-gray-800 hover:bg-gray-100 py-2 px-3 text-sm first:rounded-s-lg last:rounded-e-lg focus:outline-hidden focus:bg-gray-100"><?php echo $i; ?></a>
                    <?php endif; ?>
                <?php endfor; ?>
                <?php if ($expired_end_page < $expired_total_pages): ?>
                    <?php if ($expired_end_page < $expired_total_pages - 1): ?>
                        <span class="min-h-9.5 min-w-9.5 flex justify-center items-center border border-gray-200 text-gray-800 py-2 px-3 text-sm">...</span>
                    <?php endif; ?>
                    <a href="?expired_page=<?php echo $expired_total_pages; ?><?php echo $filterYear ? '&year=' . urlencode($filterYear) : ''; ?>" class="min-h-9.5 min-w-9.5 flex justify-center items-center border border-gray-200 text-gray-800 hover:bg-gray-100 py-2 px-3 text-sm first:rounded-s-lg last:rounded-e-lg focus:outline-hidden focus:bg-gray-100"><?php echo $expired_total_pages; ?></a>
                <?php endif; ?>
                <?php if ($expired_page < $expired_total_pages): ?>
                    <a href="?expired_page=<?php echo $expired_page + 1; ?><?php echo $filterYear ? '&year=' . urlencode($filterYear) : ''; ?>" class="min-h-9.5 min-w-9.5 py-2 px-2.5 inline-flex justify-center items-center gap-x-1.5 text-sm first:rounded-s-lg last:rounded-e-lg border border-gray-200 text-gray-800 hover:bg-gray-100 focus:outline-hidden focus:bg-gray-100" aria-label="Next">
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

<style>
/* Year dropdown styling - limit visible options to ~5 */
select#year {
    max-height: 120px;
    overflow-y: auto;
}
select#year option {
    padding: 4px 8px;
    height: 24px;
}
html, body {
  scrollbar-width: none; /* Firefox */
  -ms-overflow-style: none; /* Internet Explorer 10+ */
}
html::-webkit-scrollbar,
body::-webkit-scrollbar {
  display: none; /* Safari and Chrome */
}
</style>

<?php include '../includes/footer.php'; ?>
