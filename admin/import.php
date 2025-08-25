<?php
include '../includea/header.php';

// Database connection (using MySQL for clinic_management_system)
$db = new PDO('mysql:host=localhost;dbname=clinic_management_system;charset=utf8', 'root', '');

// Pagination settings
$records_per_page = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page = max($page, 1); // Ensure page is at least 1
$offset = ($page - 1) * $records_per_page;

// Get total count for pagination
$total_count_stmt = $db->query('SELECT COUNT(*) FROM imported_patients');
$total_records = $total_count_stmt->fetchColumn();
$total_pages = ceil($total_records / $records_per_page);

// Create imported_patients table if not exists
$db->exec('CREATE TABLE IF NOT EXISTS imported_patients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id VARCHAR(255),
    name VARCHAR(255),
    dob VARCHAR(255),
    gender VARCHAR(255),
    address VARCHAR(255),
    civil_status VARCHAR(255),
    password VARCHAR(255),
    year_level VARCHAR(255)
)');

// Handle CSV upload and import
$uploadStatus = '';
$previewRows = [];
$duplicateCount = 0;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['csvFile'])) {
    $file = $_FILES['csvFile']['tmp_name'];
    if (($handle = fopen($file, 'r')) !== false) {
        $header = fgetcsv($handle); // Assume first row is header
        $existingIds = [];
        $stmt = $db->query('SELECT id FROM imported_patients');
        foreach ($stmt as $row) {
            $existingIds[] = $row['id'];
        }
        $inserted = 0;
        while (($data = fgetcsv($handle)) !== false) {
            // Map columns: [student_id, name, dob, gender, address, civil_status, password, year_level]
            $student_id = $data[0];
            $name = $data[1];
            $dob = $data[2];
            $gender = $data[3];
            $address = $data[4];
            $civil_status = $data[5];
            $password = $data[6];
            $year_level = $data[7];
            $isDuplicate = false;
            // Check for duplicate student_id
            $stmtCheck = $db->prepare('SELECT COUNT(*) FROM imported_patients WHERE student_id = ?');
            $stmtCheck->execute([$student_id]);
            if ($stmtCheck->fetchColumn() > 0) {
                $isDuplicate = true;
            }
            $previewRows[] = [
                'student_id' => $student_id,
                'name' => $name,
                'dob' => $dob,
                'gender' => $gender,
                'address' => $address,
                'civil_status' => $civil_status,
                'password' => $password,
                'year_level' => $year_level,
                'duplicate' => $isDuplicate
            ];
            if (!$isDuplicate) {
                $stmt2 = $db->prepare('INSERT INTO imported_patients (student_id, name, dob, gender, address, civil_status, password, year_level) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
                $stmt2->execute([$student_id, $name, $dob, $gender, $address, $civil_status, $password, $year_level]);
                $inserted++;
            } else {
                $duplicateCount++;
            }
        }
        fclose($handle);
        $uploadStatus = "<span class='text-green-700'>Upload and import successful! $inserted new record(s) added.</span>";
    } else {
        $uploadStatus = "<span class='text-red-700'>Failed to open uploaded file.</span>";
    }
}
?>

<main class="flex-1 overflow-y-auto bg-gray-50 p-6 ml-16 md:ml-64 mt-[56px]">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Import CSV</h2>
        <!-- File Upload Form -->
        <div class="bg-white rounded shadow p-6 mb-8">
            <form id="csvUploadForm" enctype="multipart/form-data" method="post">
                <label class="block text-sm font-medium text-gray-700 mb-2">Select CSV File</label>
                <div class="flex items-center space-x-4 mb-4">
                    <input type="file" name="csvFile" id="csvFile" accept=".csv"
                        class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary file:text-white hover:file:bg-primary/90"
                        required />
                    <button type="submit"
                        class="px-4 py-2 bg-primary text-white font-medium text-sm rounded-button hover:bg-primary/90">Upload</button>
                </div>
            </form>
            <!-- Upload Status Notification -->
            <?php if ($uploadStatus): ?>
                <div id="uploadStatus" class="mt-2 text-sm "><?php echo $uploadStatus; ?></div>
            <?php else: ?>
                <div id="uploadStatus" class="hidden mt-2 text-sm"></div>
            <?php endif; ?>
        </div>
        <!-- Duplicate Detection Summary -->
        <?php if ($duplicateCount > 0): ?>
        <div id="duplicateSummary" class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6 rounded">
            <p class="text-sm text-yellow-800 font-medium"><?php echo $duplicateCount; ?> duplicate(s) detected in the uploaded file.</p>
        </div>
        <?php endif; ?>
        <!-- Imported Patients Table -->
        <div class="bg-white rounded shadow p-6 mt-8">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Imported Patients</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm" id="importedPatientsTable">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left font-semibold text-gray-600">Student ID</th>
                            <th class="px-4 py-2 text-left font-semibold text-gray-600">Name</th>
                            <th class="px-4 py-2 text-left font-semibold text-gray-600">DOB</th>
                            <th class="px-4 py-2 text-left font-semibold text-gray-600">Gender</th>
                            <th class="px-4 py-2 text-left font-semibold text-gray-600">Address</th>
                            <th class="px-4 py-2 text-left font-semibold text-gray-600">Civil Status</th>
                            <th class="px-4 py-2 text-left font-semibold text-gray-600">Year Level</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    $stmt = $db->prepare('SELECT id, student_id, name, dob, gender, address, civil_status, year_level FROM imported_patients ORDER BY id DESC LIMIT ' . (int)$records_per_page . ' OFFSET ' . (int)$offset);
                    $stmt->execute();
                    $patients = $stmt->fetchAll();
                    
                    if (count($patients) > 0):
                        foreach ($patients as $row): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-2"><?php echo htmlspecialchars($row['student_id']); ?></td>
                            <td class="px-4 py-2"><?php echo htmlspecialchars($row['name']); ?></td>
                            <td class="px-4 py-2"><?php echo htmlspecialchars($row['dob']); ?></td>
                            <td class="px-4 py-2"><?php echo htmlspecialchars($row['gender']); ?></td>
                            <td class="px-4 py-2"><?php echo htmlspecialchars($row['address']); ?></td>
                            <td class="px-4 py-2"><?php echo htmlspecialchars($row['civil_status']); ?></td>
                            <td class="px-4 py-2"><?php echo htmlspecialchars($row['year_level']); ?></td>
                        </tr>
                        <?php endforeach;
                    else: ?>
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-gray-500">No patients found</td>
                        </tr>
                    <?php endif; ?>
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
</main>

<?php
include '../includea/footer.php';
?>