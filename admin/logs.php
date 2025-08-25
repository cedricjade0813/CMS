<?php
include '../includea/header.php';

// Pagination settings
$perPage = 10;
$currentPage = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($currentPage - 1) * $perPage;

// Get filter parameters
$userFilter = isset($_GET['user']) ? $_GET['user'] : 'all';
$dateFilter = isset($_GET['date']) ? $_GET['date'] : '';
$searchFilter = isset($_GET['search']) ? $_GET['search'] : '';

// Fetch logs from database
try {
    $db = new PDO('mysql:host=localhost;dbname=clinic_management_system;charset=utf8', 'root', '');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Build WHERE clause for filters
    $whereConditions = [];
    $params = [];
    
    if ($userFilter !== 'all') {
        $whereConditions[] = "user_email = ?";
        $params[] = $userFilter;
    }
    
    if ($dateFilter) {
        $whereConditions[] = "DATE(timestamp) = ?";
        $params[] = $dateFilter;
    }
    
    if ($searchFilter) {
        $whereConditions[] = "(action LIKE ? OR user_email LIKE ?)";
        $params[] = '%' . $searchFilter . '%';
        $params[] = '%' . $searchFilter . '%';
    }
    
    $whereClause = !empty($whereConditions) ? 'WHERE ' . implode(' AND ', $whereConditions) : '';
    
    // Get total count for pagination
    $countQuery = "SELECT COUNT(*) FROM logs $whereClause";
    $countStmt = $db->prepare($countQuery);
    $countStmt->execute($params);
    $totalLogs = $countStmt->fetchColumn();
    $totalPages = ceil($totalLogs / $perPage);
    
    // Get logs for current page
    $logsQuery = "SELECT * FROM logs $whereClause ORDER BY timestamp DESC LIMIT $perPage OFFSET $offset";
    $logsStmt = $db->prepare($logsQuery);
    $logsStmt->execute($params);
    $logs = $logsStmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Build user map: email => full name (username)
    $userMap = [];
    try {
        $userRows = $db->query('SELECT email, username FROM users')->fetchAll(PDO::FETCH_ASSOC);
        foreach ($userRows as $u) {
            $userMap[$u['email']] = $u['username'];
        }
    } catch (Exception $e) {}
    
} catch (PDOException $e) {
    $logs = [];
    $totalLogs = 0;
    $totalPages = 0;
    $userMap = [];
}
?>
<main class="flex-1 overflow-y-auto bg-gray-50 p-6 ml-16 md:ml-64 mt-[56px]">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold text-gray-800">System Logs</h2>
            <button id="exportLogsBtn" class="px-4 py-2 bg-primary text-white font-medium text-sm rounded-button hover:bg-primary/90 flex items-center"><i class="ri-download-2-line mr-1"></i> Export Logs</button>
        </div>
        <!-- Filters and Search -->
        <div class="bg-white rounded shadow p-4 mb-6">
            <form method="GET" class="flex flex-wrap gap-4 items-end">
                <input type="hidden" name="page" value="1">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">User</label>
                    <select name="user" class="border border-gray-300 rounded px-3 py-2 text-sm">
                        <option value="all">All Users</option>
                        <?php
                        // Fetch all users from users table for dropdown
                        try {
                            $userRows = $db->query('SELECT email, username FROM users ORDER BY username ASC')->fetchAll(PDO::FETCH_ASSOC);
                            foreach ($userRows as $u) {
                                $email = $u['email'];
                                $username = $u['username'];
                                $selected = ($userFilter === $email) ? 'selected' : '';
                                echo '<option value="' . htmlspecialchars($email) . '" ' . $selected . '>' . htmlspecialchars($username) . '</option>';
                            }
                        } catch (Exception $e) {}
                        ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                    <input type="date" name="date" value="<?php echo htmlspecialchars($dateFilter); ?>" class="border border-gray-300 rounded px-3 py-2 text-sm" />
                </div>
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                    <input type="text" name="search" value="<?php echo htmlspecialchars($searchFilter); ?>" placeholder="Search logs..." class="w-full border border-gray-300 rounded px-3 py-2 text-sm" />
                </div>
                <div>
                    <button type="submit" class="px-4 py-2 bg-primary text-white font-medium text-sm rounded-button hover:bg-primary/90">Filter</button>
                </div>
                <?php if ($userFilter !== 'all' || $dateFilter || $searchFilter): ?>
                <div>
                    <a href="?" class="px-4 py-2 bg-gray-500 text-white font-medium text-sm rounded-button hover:bg-gray-600">Clear</a>
                </div>
                <?php endif; ?>
            </form>
        </div>
        <!-- Activity Log Table -->
        <div class="bg-white rounded shadow p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-800">Activity Logs</h3>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left font-semibold text-gray-600">User</th>
                            <th class="px-4 py-2 text-left font-semibold text-gray-600">Action</th>
                            <th class="px-4 py-2 text-left font-semibold text-gray-600">Timestamp</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php if (!empty($logs)): ?>
                            <?php foreach ($logs as $log): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-2">
                                    <?php
                                        $user = $log['user_email'];
                                        echo htmlspecialchars(isset($userMap[$user]) ? $userMap[$user] : ($user ? $user : 'System'));
                                    ?>
                                </td>
                                <td class="px-4 py-2"><?php echo htmlspecialchars($log['action']); ?></td>
                                <td class="px-4 py-2"><?php echo htmlspecialchars($log['timestamp']); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="3" class="px-4 py-8 text-center text-gray-500">No logs found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
            <div class="flex justify-between items-center mt-6">
                <!-- Records Information -->
                <div class="text-sm text-gray-600">
                    <?php 
                    $start = $offset + 1;
                    $end = min($offset + $perPage, $totalLogs);
                    ?>
                    Showing <?php echo $start; ?> to <?php echo $end; ?> of <?php echo $totalLogs; ?> entries
                </div>

                <!-- Pagination Navigation -->
                <nav class="flex justify-end items-center -space-x-px" aria-label="Pagination">
                    <?php
                    // Build query string for pagination links
                    $queryParams = [];
                    if ($userFilter !== 'all') $queryParams['user'] = $userFilter;
                    if ($dateFilter) $queryParams['date'] = $dateFilter;
                    if ($searchFilter) $queryParams['search'] = $searchFilter;
                    
                    function buildPaginationLink($page, $queryParams) {
                        $queryParams['page'] = $page;
                        return '?' . http_build_query($queryParams);
                    }
                    ?>
                    
                    <!-- Previous Button -->
                    <?php if ($currentPage > 1): ?>
                        <a href="<?php echo buildPaginationLink($currentPage - 1, $queryParams); ?>" class="min-h-9.5 min-w-9.5 py-2 px-2.5 inline-flex justify-center items-center gap-x-1.5 text-sm first:rounded-s-lg last:rounded-e-lg border border-gray-200 text-gray-800 hover:bg-gray-100 focus:outline-hidden focus:bg-gray-100" aria-label="Previous">
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
                    $start_page = max(1, $currentPage - 2);
                    $end_page = min($totalPages, $currentPage + 2);
                    
                    // Show first page if not in range
                    if ($start_page > 1): ?>
                        <a href="<?php echo buildPaginationLink(1, $queryParams); ?>" class="min-h-9.5 min-w-9.5 flex justify-center items-center border border-gray-200 text-gray-800 hover:bg-gray-100 py-2 px-3 text-sm first:rounded-s-lg last:rounded-e-lg focus:outline-hidden focus:bg-gray-100">1</a>
                        <?php if ($start_page > 2): ?>
                            <span class="min-h-9.5 min-w-9.5 flex justify-center items-center border border-gray-200 text-gray-800 py-2 px-3 text-sm">...</span>
                        <?php endif; ?>
                    <?php endif; ?>

                    <?php for ($i = $start_page; $i <= $end_page; $i++): ?>
                        <?php if ($i == $currentPage): ?>
                            <button type="button" class="min-h-9.5 min-w-9.5 flex justify-center items-center bg-gray-200 text-gray-800 border border-gray-200 py-2 px-3 text-sm first:rounded-s-lg last:rounded-e-lg focus:outline-hidden focus:bg-gray-300" aria-current="page"><?php echo $i; ?></button>
                        <?php else: ?>
                            <a href="<?php echo buildPaginationLink($i, $queryParams); ?>" class="min-h-9.5 min-w-9.5 flex justify-center items-center border border-gray-200 text-gray-800 hover:bg-gray-100 py-2 px-3 text-sm first:rounded-s-lg last:rounded-e-lg focus:outline-hidden focus:bg-gray-100"><?php echo $i; ?></a>
                        <?php endif; ?>
                    <?php endfor; ?>

                    <!-- Show last page if not in range -->
                    <?php if ($end_page < $totalPages): ?>
                        <?php if ($end_page < $totalPages - 1): ?>
                            <span class="min-h-9.5 min-w-9.5 flex justify-center items-center border border-gray-200 text-gray-800 py-2 px-3 text-sm">...</span>
                        <?php endif; ?>
                        <a href="<?php echo buildPaginationLink($totalPages, $queryParams); ?>" class="min-h-9.5 min-w-9.5 flex justify-center items-center border border-gray-200 text-gray-800 hover:bg-gray-100 py-2 px-3 text-sm first:rounded-s-lg last:rounded-e-lg focus:outline-hidden focus:bg-gray-100"><?php echo $totalPages; ?></a>
                    <?php endif; ?>

                    <!-- Next Button -->
                    <?php if ($currentPage < $totalPages): ?>
                        <a href="<?php echo buildPaginationLink($currentPage + 1, $queryParams); ?>" class="min-h-9.5 min-w-9.5 py-2 px-2.5 inline-flex justify-center items-center gap-x-1.5 text-sm first:rounded-s-lg last:rounded-e-lg border border-gray-200 text-gray-800 hover:bg-gray-100 focus:outline-hidden focus:bg-gray-100" aria-label="Next">
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
            </div>
            <?php endif; ?>
        </div>
</main>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Export logs as CSV
    document.getElementById('exportLogsBtn').addEventListener('click', function() {
        // Get current filter parameters
        const urlParams = new URLSearchParams(window.location.search);
        const userFilter = urlParams.get('user') || 'all';
        const dateFilter = urlParams.get('date') || '';
        const searchFilter = urlParams.get('search') || '';
        
        // Create export URL with current filters
        let exportUrl = 'export_logs.php?';
        const exportParams = [];
        if (userFilter !== 'all') exportParams.push('user=' + encodeURIComponent(userFilter));
        if (dateFilter) exportParams.push('date=' + encodeURIComponent(dateFilter));
        if (searchFilter) exportParams.push('search=' + encodeURIComponent(searchFilter));
        
        if (exportParams.length > 0) {
            exportUrl += exportParams.join('&');
        }
        
        // Create download link
        const link = document.createElement('a');
        link.href = exportUrl;
        link.download = 'system_logs_<?php echo date('Ymd_His'); ?>.csv';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    });
});
</script>
<?php
include '../includea/footer.php';
?>