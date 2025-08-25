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
                    foreach ($nonExpiredMeds as $med): ?>
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
                    if (!empty($expiredMeds)) {
                        foreach ($expiredMeds as $med) {
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
</style>

<?php include '../includes/footer.php'; ?>
