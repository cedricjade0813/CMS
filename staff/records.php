<?php
session_start();
include '../includes/header.php';
try {
    $db = new PDO('mysql:host=localhost;dbname=clinic_management_system;charset=utf8', 'root', '');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Create prescriptions table if not exists
    $db->exec("CREATE TABLE IF NOT EXISTS prescriptions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        patient_id INT,
        patient_name VARCHAR(255),
        prescribed_by VARCHAR(255),
        prescription_date DATETIME DEFAULT CURRENT_TIMESTAMP,
        medicines TEXT,
        notes TEXT
    )");
    // Create pending_prescriptions table if not exists
    $db->exec("CREATE TABLE IF NOT EXISTS pending_prescriptions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        patient_id INT,
        patient_name VARCHAR(255),
        prescribed_by VARCHAR(255),
        prescription_date DATETIME DEFAULT CURRENT_TIMESTAMP,
        medicines TEXT,
        notes TEXT
    )");
    
    // Create vital_signs table if not exists
    $db->exec("CREATE TABLE IF NOT EXISTS vital_signs (
        id INT AUTO_INCREMENT PRIMARY KEY,
        patient_id INT,
        patient_name VARCHAR(255),
        vital_date DATE,
        weight DECIMAL(5,2),
        height DECIMAL(5,2),
        body_temp DECIMAL(4,2),
        resp_rate INT,
        pulse INT,
        blood_pressure VARCHAR(20),
        oxygen_sat DECIMAL(5,2),
        remarks TEXT,
        recorded_by VARCHAR(255),
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY unique_patient_date (patient_id, vital_date)
    )");
    
    // Add blood_pressure column if it doesn't exist
    try {
        $db->exec("ALTER TABLE vital_signs ADD COLUMN blood_pressure VARCHAR(20) AFTER pulse");
    } catch (Exception $e) { 
        // Ignore if column already exists
    }
    
    // Option A: Add visitor support columns for vital_signs
    try {
        $db->exec("ALTER TABLE vital_signs ADD COLUMN IF NOT EXISTS visitor_id INT NULL AFTER patient_id");
    } catch (Exception $e) { /* ignore */ }
    try {
        $db->exec("ALTER TABLE vital_signs ADD COLUMN IF NOT EXISTS visitor_name VARCHAR(255) NULL AFTER patient_name");
    } catch (Exception $e) { /* ignore */ }
    try {
        $db->exec("CREATE INDEX IF NOT EXISTS idx_vital_signs_visitor_id ON vital_signs (visitor_id)");
    } catch (Exception $e) { /* ignore */ }
    
    // Create medication_referrals table if not exists
    $db->exec("CREATE TABLE IF NOT EXISTS medication_referrals (
        id INT AUTO_INCREMENT PRIMARY KEY,
        patient_id INT,
        patient_name VARCHAR(255),
        subjective TEXT,
        objective TEXT,
        assessment TEXT,
        plan TEXT,
        intervention TEXT,
        evaluation TEXT,
        recorded_by VARCHAR(255),
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");
    
    // Option A: Add visitor support columns for medication_referrals
    try {
        $db->exec("ALTER TABLE medication_referrals ADD COLUMN IF NOT EXISTS visitor_id INT NULL AFTER patient_id");
    } catch (Exception $e) { /* ignore */ }
    try {
        $db->exec("ALTER TABLE medication_referrals ADD COLUMN IF NOT EXISTS visitor_name VARCHAR(255) NULL AFTER patient_name");
    } catch (Exception $e) { /* ignore */ }
    try {
        $db->exec("CREATE INDEX IF NOT EXISTS idx_med_ref_visitor_id ON medication_referrals (visitor_id)");
    } catch (Exception $e) { /* ignore */ }
    
    // Add patient_email and parent_email columns to prescriptions and pending_prescriptions if not exist
    $db->exec("ALTER TABLE prescriptions ADD COLUMN IF NOT EXISTS patient_email VARCHAR(255) AFTER patient_name");
    $db->exec("ALTER TABLE prescriptions ADD COLUMN IF NOT EXISTS parent_email VARCHAR(255) AFTER patient_email");
    $db->exec("ALTER TABLE pending_prescriptions ADD COLUMN IF NOT EXISTS patient_email VARCHAR(255) AFTER patient_name");
    $db->exec("ALTER TABLE pending_prescriptions ADD COLUMN IF NOT EXISTS parent_email VARCHAR(255) AFTER patient_email");
    
    // Add reason column to prescriptions and pending_prescriptions if not exist
    $db->exec("ALTER TABLE prescriptions ADD COLUMN IF NOT EXISTS reason VARCHAR(255) AFTER medicines");
    $db->exec("ALTER TABLE pending_prescriptions ADD COLUMN IF NOT EXISTS reason VARCHAR(255) AFTER medicines");
    
    // Add unique constraint to vital_signs table if not exists
    try {
        $db->exec("ALTER TABLE vital_signs ADD UNIQUE KEY unique_patient_date (patient_id, vital_date)");
    } catch (Exception $e) { 
        // Ignore if constraint already exists
    }
} catch (PDOException $e) {
    die('Database connection failed: ' . $e->getMessage());
} catch (Exception $e) { /* Ignore if columns already exist */
}



// Fetch medicines from DB for dropdown

$medicines = [];
try {
    $medStmt = $db->query('SELECT name, quantity FROM medicines ORDER BY name ASC');
    $medicines = $medStmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $medicines = [];
}

// Dynamic suggestions for prescribe modal fields
function getDistinctValues($db, $table, $column)
{
    try {
        $stmt = $db->query("SELECT DISTINCT $column FROM $table WHERE $column IS NOT NULL AND $column != '' LIMIT 50");
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    } catch (Exception $e) {
        return [];
    }
}

$reasonSuggestions = getDistinctValues($db, 'prescriptions', 'reason');
$dosageSuggestions = [];
$qtySuggestions = [];
$frequencySuggestions = [];
$instructionsSuggestions = [];

// Parse medicines field for dosage, qty, frequency, instructions
try {
    $medRows = $db->query('SELECT medicines FROM prescriptions WHERE medicines IS NOT NULL AND medicines != "" LIMIT 100')->fetchAll(PDO::FETCH_COLUMN);
    foreach ($medRows as $row) {
        $meds = json_decode($row, true);
        if (is_array($meds)) {
            foreach ($meds as $med) {
                if (!empty($med['dosage'])) $dosageSuggestions[] = $med['dosage'];
                if (!empty($med['quantity'])) $qtySuggestions[] = $med['quantity'];
                if (!empty($med['frequency'])) $frequencySuggestions[] = $med['frequency'];
                if (!empty($med['instructions'])) $instructionsSuggestions[] = $med['instructions'];
            }
        }
    }
} catch (Exception $e) {
}

$dosageSuggestions = array_unique(array_filter($dosageSuggestions));
$qtySuggestions = array_unique(array_filter($qtySuggestions));
$frequencySuggestions = array_unique(array_filter($frequencySuggestions));
$instructionsSuggestions = array_unique(array_filter($instructionsSuggestions));
?>

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<style id="tableFilterBaseStyles">
  .filter-student #visitorSection{display:none !important;}
  .filter-visitor #studentSection{display:none !important;}
  /* .filter-all shows both by default */
  .filter-all #studentSection{display:block;}
  .filter-all #visitorSection{display:block;}
  @media (prefers-color-scheme: dark){
    .filter-all #studentSection{display:block;}
    .filter-all #visitorSection{display:block;}
  }
  
  /* Ensure blocks even if a utility class tries to override */
  #studentSection[hidden], #visitorSection[hidden]{display:none !important;}
}</style>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

<main class="flex-1 overflow-y-auto bg-gray-50 p-6 ml-16 md:ml-64 mt-[56px]">



        <h2 class="text-2xl font-bold text-gray-800 mb-6">Patient Records</h2>
    <!-- Search Bar and Add Patient Button -->
    <div class="mb-4 flex items-center justify-between gap-4">
        <div class="flex items-center gap-2">
            <select id="tableFilter" class="border border-gray-300 rounded px-3 py-2 text-sm">
                <option value="all" selected>All</option>
                <option value="student">Student</option>
                <option value="visitor">Visitor</option>
            </select>
            <input id="searchInput" type="text" class="w-full max-w-xs border border-gray-300 rounded px-3 py-2 text-sm" placeholder="Search patients...">
        </div>
        <button id="addPatientBtn" class="px-4 py-2 bg-primary text-white font-medium text-sm rounded-button hover:bg-primary/90 flex items-center">
            <i class="ri-user-add-line ri-lg mr-1"></i> Add New Patient
        </button>
        </div>
        <!-- Patient Table -->
        <div id="studentSection" class="bg-white rounded shadow p-6">
        <h3 class="text-lg font-semibold mb-4">Student Records</h3>
            <div class="overflow-x-auto">
                <table id="importedPatientsTable" class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left font-semibold text-gray-600">ID</th>
                            <th class="px-4 py-2 text-left font-semibold text-gray-600">Student ID</th>
                            <th class="px-4 py-2 text-left font-semibold text-gray-600">Name</th>
                            <th class="px-4 py-2 text-center font-semibold text-gray-600">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $stmt = $db->query('SELECT id, student_id, name, dob, gender, address, civil_status, year_level, email, contact_number, religion, citizenship, course_program, guardian_name, guardian_contact, emergency_contact_name, emergency_contact_number, parent_email, parent_phone, MAX(dob) as last_visit FROM imported_patients GROUP BY id, student_id, name ORDER BY id DESC');
                            // Pagination settings
                            $records_per_page = 10;
                            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                            $page = max($page, 1);
                            $offset = ($page - 1) * $records_per_page;

                            // Get total count for pagination
                            $total_count_stmt = $db->query('SELECT COUNT(*) FROM imported_patients');
                            $total_records = $total_count_stmt->fetchColumn();
                            $total_pages = ceil($total_records / $records_per_page);

                            $stmt = $db->prepare('SELECT id, student_id, name, dob, gender, address, civil_status, year_level, email, contact_number, religion, citizenship, course_program, guardian_name, guardian_contact, emergency_contact_name, emergency_contact_number, parent_email, parent_phone, MAX(dob) as last_visit FROM imported_patients GROUP BY id, student_id, name ORDER BY id DESC LIMIT :limit OFFSET :offset');
                            $stmt->bindValue(':limit', $records_per_page, PDO::PARAM_INT);
                            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
                            $stmt->execute();
                            foreach ($stmt as $row): ?>
                        <tr>
                            <td class="px-4 py-2"><?php echo htmlspecialchars($row['id']); ?></td>
                            <td class="px-4 py-2"><?php echo htmlspecialchars($row['student_id']); ?></td>
                            <td class="px-4 py-2"><?php echo htmlspecialchars($row['name']); ?></td>
                            <td class="px-4 py-2 text-center">
                                <button class="viewBtn px-3 py-1 text-xs bg-primary text-white rounded hover:bg-primary/90" 
                                    data-name="<?php echo htmlspecialchars($row['name']); ?>" 
                                    data-id="<?php echo htmlspecialchars($row['id']); ?>" 
                                    data-student_id="<?php echo htmlspecialchars($row['student_id']); ?>" 
                                    data-dob="<?php echo htmlspecialchars($row['dob'] ?? ''); ?>"
                                    data-gender="<?php echo htmlspecialchars($row['gender']); ?>" 
                                    data-year="<?php echo htmlspecialchars($row['year_level']); ?>" 
                                    data-address="<?php echo htmlspecialchars($row['address']); ?>" 
                                    data-civil="<?php echo htmlspecialchars($row['civil_status']); ?>"
                                    data-email="<?php echo htmlspecialchars($row['email'] ?? ''); ?>"
                                    data-contact="<?php echo htmlspecialchars($row['contact_number'] ?? ''); ?>"
                                    data-religion="<?php echo htmlspecialchars($row['religion'] ?? ''); ?>"
                                    data-citizenship="<?php echo htmlspecialchars($row['citizenship'] ?? ''); ?>"
                                    data-course="<?php echo htmlspecialchars($row['course_program'] ?? ''); ?>"
                                    data-guardian-name="<?php echo htmlspecialchars($row['guardian_name'] ?? ''); ?>"
                                    data-guardian-contact="<?php echo htmlspecialchars($row['guardian_contact'] ?? ''); ?>"
                                    data-emergency-name="<?php echo htmlspecialchars($row['emergency_contact_name'] ?? ''); ?>"
                                    data-emergency-contact="<?php echo htmlspecialchars($row['emergency_contact_number'] ?? ''); ?>"
                                    data-parent-email="<?php echo htmlspecialchars($row['parent_email'] ?? ''); ?>"
                                    data-parent-phone="<?php echo htmlspecialchars($row['parent_phone'] ?? ''); ?>">View</button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
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


    <!-- Visitor Records Table -->
    <div id="visitorSection" class="bg-white rounded shadow p-6 mt-6 relative z-10">
        <h3 class="text-xl font-bold text-gray-800 mb-4">Visitor Records</h3>
        <div class="overflow-x-auto">
            <table id="visitorTable" class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left font-semibold text-gray-600">Visitor ID</th>
                        <th class="px-4 py-2 text-left font-semibold text-gray-600">Full Name</th>
                        <th class="px-4 py-2 text-left font-semibold text-gray-600">Age</th>
                        <th class="px-4 py-2 text-left font-semibold text-gray-600">Gender</th>
                        <th class="px-4 py-2 text-left font-semibold text-gray-600">Contact</th>
                        <th class="px-4 py-2 text-center font-semibold text-gray-600">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Pagination settings for visitors
                    $visitor_records_per_page = 10;
                    $visitor_page = isset($_GET['visitor_page']) ? (int)$_GET['visitor_page'] : 1;
                    $visitor_page = max($visitor_page, 1);
                    $visitor_offset = ($visitor_page - 1) * $visitor_records_per_page;

                    // Get total count for visitor pagination
                    $visitor_total_count_stmt = $db->query('SELECT COUNT(*) FROM visitor');
                    $visitor_total_records = $visitor_total_count_stmt->fetchColumn();
                    $visitor_total_pages = ceil($visitor_total_records / $visitor_records_per_page);

                    $visitor_stmt = $db->prepare('SELECT * FROM visitor ORDER BY visitor_id DESC LIMIT :limit OFFSET :offset');
                    $visitor_stmt->bindValue(':limit', $visitor_records_per_page, PDO::PARAM_INT);
                    $visitor_stmt->bindValue(':offset', $visitor_offset, PDO::PARAM_INT);
                    $visitor_stmt->execute();
                    foreach ($visitor_stmt as $visitor): ?>
                        <tr>
                            <td class="px-4 py-2"><?php echo htmlspecialchars($visitor['visitor_id']); ?></td>
                            <td class="px-4 py-2"><?php echo htmlspecialchars($visitor['full_name']); ?></td>
                            <td class="px-4 py-2"><?php echo htmlspecialchars($visitor['age']); ?></td>
                            <td class="px-4 py-2"><?php echo htmlspecialchars($visitor['gender']); ?></td>
                            <td class="px-4 py-2"><?php echo htmlspecialchars($visitor['contact']); ?></td>
                            <td class="px-4 py-2 text-center">
                                <button class="viewVisitorBtn px-3 py-1 text-xs bg-primary text-white rounded hover:bg-primary/90"
                                    data-id="<?php echo htmlspecialchars($visitor['visitor_id']); ?>"
                                    data-name="<?php echo htmlspecialchars($visitor['full_name']); ?>"
                                    data-age="<?php echo htmlspecialchars($visitor['age']); ?>"
                                    data-gender="<?php echo htmlspecialchars($visitor['gender']); ?>"
                                    data-address="<?php echo htmlspecialchars($visitor['address']); ?>"
                                    data-contact="<?php echo htmlspecialchars($visitor['contact']); ?>">View</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php if ($visitor_total_records == 0): ?>
                <div class="text-center py-8 text-gray-500">
                    <p>No visitor records found.</p>
    </div>
            <?php endif; ?>
        </div>

        <!-- Visitor Pagination and Records Info -->
        <?php if ($visitor_total_records > 0): ?>
            <div class="flex justify-between items-center mt-6">
                <!-- Records Information -->
                <div class="text-sm text-gray-600">
                    <?php
                    $visitor_start = $visitor_offset + 1;
                    $visitor_end = min($visitor_offset + $visitor_records_per_page, $visitor_total_records);
                    ?>
                    Showing <?php echo $visitor_start; ?> to <?php echo $visitor_end; ?> of <?php echo $visitor_total_records; ?> entries
                </div>

                <!-- Pagination Navigation -->
                <?php if ($visitor_total_pages > 1): ?>
                    <nav class="flex justify-end items-center -space-x-px" aria-label="Visitor Pagination">
                        <!-- Previous Button -->
                        <?php if ($visitor_page > 1): ?>
                            <a href="?page=<?php echo isset($page) ? $page : 1; ?>&visitor_page=<?php echo $visitor_page - 1; ?>" class="min-h-9.5 min-w-9.5 py-2 px-2.5 inline-flex justify-center items-center gap-x-1.5 text-sm first:rounded-s-lg last:rounded-e-lg border border-gray-200 text-gray-800 hover:bg-gray-100 focus:outline-hidden focus:bg-gray-100" aria-label="Previous">
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
                        $visitor_start_page = max(1, $visitor_page - 2);
                        $visitor_end_page = min($visitor_total_pages, $visitor_page + 2);
                        // Show first page if not in range
                        if ($visitor_start_page > 1): ?>
                            <a href="?page=<?php echo isset($page) ? $page : 1; ?>&visitor_page=1" class="min-h-9.5 min-w-9.5 flex justify-center items-center border border-gray-200 text-gray-800 hover:bg-gray-100 py-2 px-3 text-sm first:rounded-s-lg last:rounded-e-lg focus:outline-hidden focus:bg-gray-100">1</a>
                            <?php if ($visitor_start_page > 2): ?>
                                <span class="min-h-9.5 min-w-9.5 flex justify-center items-center border border-gray-200 text-gray-800 py-2 px-3 text-sm">...</span>
                            <?php endif; ?>
                        <?php endif; ?>

                        <?php for ($i = $visitor_start_page; $i <= $visitor_end_page; $i++): ?>
                            <?php if ($i == $visitor_page): ?>
                                <button type="button" class="min-h-9.5 min-w-9.5 flex justify-center items-center bg-gray-200 text-gray-800 border border-gray-200 py-2 px-3 text-sm first:rounded-s-lg last:rounded-e-lg focus:outline-hidden focus:bg-gray-300" aria-current="page"><?php echo $i; ?></button>
                            <?php else: ?>
                                <a href="?page=<?php echo isset($page) ? $page : 1; ?>&visitor_page=<?php echo $i; ?>" class="min-h-9.5 min-w-9.5 flex justify-center items-center border border-gray-200 text-gray-800 hover:bg-gray-100 py-2 px-3 text-sm first:rounded-s-lg last:rounded-e-lg focus:outline-hidden focus:bg-gray-100"><?php echo $i; ?></a>
                            <?php endif; ?>
                        <?php endfor; ?>

                        <!-- Show last page if not in range -->
                        <?php if ($visitor_end_page < $visitor_total_pages): ?>
                            <?php if ($visitor_end_page < $visitor_total_pages - 1): ?>
                                <span class="min-h-9.5 min-w-9.5 flex justify-center items-center border border-gray-200 text-gray-800 py-2 px-3 text-sm">...</span>
                            <?php endif; ?>
                            <a href="?page=<?php echo isset($page) ? $page : 1; ?>&visitor_page=<?php echo $visitor_total_pages; ?>" class="min-h-9.5 min-w-9.5 flex justify-center items-center border border-gray-200 text-gray-800 hover:bg-gray-100 py-2 px-3 text-sm first:rounded-s-lg last:rounded-e-lg focus:outline-hidden focus:bg-gray-100"><?php echo $visitor_total_pages; ?></a>
                        <?php endif; ?>

                        <!-- Next Button -->
                        <?php if ($visitor_page < $visitor_total_pages): ?>
                            <a href="?page=<?php echo isset($page) ? $page : 1; ?>&visitor_page=<?php echo $visitor_page + 1; ?>" class="min-h-9.5 min-w-9.5 py-2 px-2.5 inline-flex justify-center items-center gap-x-1.5 text-sm first:rounded-s-lg last:rounded-e-lg border border-gray-200 text-gray-800 hover:bg-gray-100 focus:outline-hidden focus:bg-gray-100" aria-label="Next">
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

    <!-- Visitor Prescribe Medicine Modal (mirrors patient prescribe modal) -->
    <div id="visitorPrescribeMedModal" class="fixed inset-0 bg-black bg-opacity-30 flex items-center justify-center z-[60] hidden">
        <div class="w-full max-w-5xl h-[85vh] mx-4 flex flex-col bg-white border border-gray-200 shadow-2xl rounded-xl pointer-events-auto dark:bg-neutral-800 dark:border-neutral-700 dark:shadow-neutral-700/70">
            <div class="flex justify-between items-center py-4 px-6 border-b border-gray-200 dark:border-neutral-700">
                <h3 class="font-bold text-lg text-gray-800 dark:text-white">Prescribe Medicine</h3>
                <button id="closeVisitorPrescribeMedModal" type="button" class="size-8 inline-flex justify-center items-center gap-x-2 rounded-full border border-transparent bg-gray-100 text-gray-800 hover:bg-gray-200 focus:outline-hidden focus:bg-gray-200 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-700 dark:hover:bg-neutral-600 dark:text-neutral-400 dark:focus:bg-neutral-600" aria-label="Close">
                    <span class="sr-only">Close</span>
                    <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M18 6 6 18"></path>
                        <path d="m6 6 12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="p-6 overflow-y-auto flex-1">
                <form id="visitorPrescribeMedForm">
                    <div id="visitorMedsList">
                        <div class="medRow mb-4 border-b pb-4">
                            <div class="mb-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Medicine</label>
                                <select class="medicineSelect w-full border border-gray-300 rounded px-3 py-2 text-sm">
                                    <option value="">Select medicine</option>
                                    <?php foreach ($medicines as $med): ?>
                                        <option value="<?php echo htmlspecialchars($med['name']); ?>" data-stock="<?php echo htmlspecialchars($med['quantity']); ?>">
                                            <?php echo htmlspecialchars($med['name']); ?> (<?php echo htmlspecialchars($med['quantity']); ?> in stock)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="flex gap-2 mb-2">
                                <div class="flex-1">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Dosage</label>
                                    <input type="text" class="w-full border border-gray-300 rounded px-3 py-2 text-sm" placeholder="e.g. 500mg" list="dosageSuggestions" />
                                </div>
                                <div class="flex-1">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Quantity</label>
                                    <input type="number" class="w-full border border-gray-300 rounded px-3 py-2 text-sm qtyInput" min="1" list="qtySuggestions" />
                                    <span class="text-xs text-gray-500 stockMsg"></span>
                                </div>
                            </div>
                            <div class="mb-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Frequency</label>
                                <input type="text" class="w-full border border-gray-300 rounded px-3 py-2 text-sm" placeholder="e.g. 3x a day" list="frequencySuggestions" />
                            </div>
                            <div class="mb-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Instructions</label>
                                <input type="text" class="w-full border border-gray-300 rounded px-3 py-2 text-sm" placeholder="e.g. After meals" list="instructionsSuggestions" />
                            </div>
                            <button type="button" class="removeMedBtn text-xs text-red-500 hover:underline mt-1">Remove</button>
                        </div>
                    </div>
                    <button type="button" id="visitorAddMedRowBtn" class="mb-4 px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600">+ Add Another Medicine</button>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Reason for Prescription *</label>
                        <input type="text" name="reason" class="w-full border border-gray-300 rounded px-3 py-2 text-sm" placeholder="e.g. Fever, Headache, Cough, etc." required />
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                        <textarea class="w-full border border-gray-300 rounded px-3 py-2 text-sm" rows="2" placeholder="Additional info..."></textarea>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Recipient Email Address</label>
                        <input type="email" name="recipient_email" class="w-full border border-gray-300 rounded px-3 py-2 text-sm" placeholder="Enter recipient's email address" />
                    </div>
                    <div class="flex justify-center">
                        <button type="submit" class="py-2 px-3 inline-flex items-center justify-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-primary text-white hover:bg-primary/90 focus:outline-hidden focus:bg-primary/90 disabled:opacity-50 disabled:pointer-events-none min-w-[200px]">Submit Prescription</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Profile Modal -->
    <div id="profileModal" class="fixed inset-0 bg-black bg-opacity-30 flex items-center justify-center z-50 hidden">
        <div class="w-full max-w-5xl h-[85vh] mx-4 flex flex-col bg-white border border-gray-200 shadow-2xl rounded-xl pointer-events-auto dark:bg-neutral-800 dark:border-neutral-700 dark:shadow-neutral-700/70">
            <div class="flex justify-between items-center py-4 px-6 border-b border-gray-200 dark:border-neutral-700">
                <h3 id="modalPatientName" class="font-bold text-lg text-gray-800 dark:text-white">Patient Profile</h3>
                <button id="closeProfileModal" type="button" class="size-8 inline-flex justify-center items-center gap-x-2 rounded-full border border-transparent bg-gray-100 text-gray-800 hover:bg-gray-200 focus:outline-hidden focus:bg-gray-200 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-700 dark:hover:bg-neutral-600 dark:text-neutral-400 dark:focus:bg-neutral-600" aria-label="Close">
                    <span class="sr-only">Close</span>
                    <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M18 6 6 18"></path>
                        <path d="m6 6 12 12"></path>
                    </svg>
                </button>
            </div>
            
            <div class="p-6 overflow-y-auto flex-1">
                <!-- Navigation Tabs -->
                <div class="flex justify-start space-x-3 mb-6" id="profileModalTabs">
                    <button class="tabBtn px-4 py-2 text-sm rounded-lg font-semibold text-gray-700 bg-gray-200 hover:bg-primary/10 dark:bg-neutral-600 dark:text-neutral-300 dark:hover:bg-neutral-500" data-tab="infoTab">Information</button>
                    <button class="tabBtn px-4 py-2 text-sm rounded-lg font-semibold text-gray-700 bg-gray-200 hover:bg-primary/10 dark:bg-neutral-600 dark:text-neutral-300 dark:hover:bg-neutral-500" data-tab="vitalsTab">Vital Signs</button>
                    <button class="tabBtn px-4 py-2 text-sm rounded-lg font-semibold text-gray-700 bg-gray-200 hover:bg-primary/10 dark:bg-neutral-600 dark:text-neutral-300 dark:hover:bg-neutral-500" data-tab="medReferralTab">Medication Referral</button>
                </div>
                
                <!-- Tab Contents -->
                <div id="infoTab" class="tabContent">
                    <div id="modalPatientDetails" class="text-base text-gray-700 dark:text-neutral-300 mb-6">
                        <!-- Patient details will be shown here -->
                    </div>
                </div>
                
                <div id="vitalsTab" class="tabContent hidden modal-scroll-area">
                    <h4 class="text-sm font-semibold text-gray-800 dark:text-white mb-2">Patient Vital Signs</h4>
                    <form id="vitalsForm" class="space-y-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 dark:text-neutral-300">Date</label>
                            <input type="date" class="w-full border border-gray-300 dark:border-neutral-600 rounded px-3 py-2 text-sm dark:bg-neutral-700 dark:text-white" name="vital_date" required />
                        </div>
                        <div class="flex gap-2">
                            <div class="flex-1">
                                <label class="block text-xs font-medium text-gray-700 dark:text-neutral-300">Weight (kg)</label>
                                <input type="number" step="0.01" class="w-full border border-gray-300 dark:border-neutral-600 rounded px-3 py-2 text-sm dark:bg-neutral-700 dark:text-white" name="weight" />
                            </div>
                            <div class="flex-1">
                                <label class="block text-xs font-medium text-gray-700 dark:text-neutral-300">Height (cm)</label>
                                <input type="number" step="0.01" class="w-full border border-gray-300 dark:border-neutral-600 rounded px-3 py-2 text-sm dark:bg-neutral-700 dark:text-white" name="height" />
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <div class="flex-1">
                                <label class="block text-xs font-medium text-gray-700 dark:text-neutral-300">Body Temp (°C)</label>
                                <input type="number" step="0.01" class="w-full border border-gray-300 dark:border-neutral-600 rounded px-3 py-2 text-sm dark:bg-neutral-700 dark:text-white" name="body_temp" />
                            </div>
                            <div class="flex-1">
                                <label class="block text-xs font-medium text-gray-700 dark:text-neutral-300">Respiratory Rate</label>
                                <input type="number" class="w-full border border-gray-300 dark:border-neutral-600 rounded px-3 py-2 text-sm dark:bg-neutral-700 dark:text-white" name="resp_rate" />
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <div class="flex-1">
                                <label class="block text-xs font-medium text-gray-700 dark:text-neutral-300">Pulse</label>
                                <input type="number" class="w-full border border-gray-300 dark:border-neutral-600 rounded px-3 py-2 text-sm dark:bg-neutral-700 dark:text-white" name="pulse" />
                            </div>
                            <div class="flex-1">
                                <label class="block text-xs font-medium text-gray-700 dark:text-neutral-300">Blood Pressure</label>
                                <input type="text" class="w-full border border-gray-300 dark:border-neutral-600 rounded px-3 py-2 text-sm dark:bg-neutral-700 dark:text-white" name="blood_pressure" />
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <div class="flex-1">
                                <label class="block text-xs font-medium text-gray-700 dark:text-neutral-300">Oxygen Saturation (%)</label>
                                <input type="number" step="0.01" class="w-full border border-gray-300 dark:border-neutral-600 rounded px-3 py-2 text-sm dark:bg-neutral-700 dark:text-white" name="oxygen_sat" />
                            </div>
                            <div class="flex-1"></div>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 dark:text-neutral-300">Remarks</label>
                            <textarea class="w-full border border-gray-300 dark:border-neutral-600 rounded px-3 py-2 text-sm dark:bg-neutral-700 dark:text-white" name="remarks" rows="2"></textarea>
                        </div>
                    </form>
                </div>
                
                <div id="medReferralTab" class="tabContent hidden modal-scroll-area">
                    <!-- Patient's Medication Referral History -->
                    <div id="medReferralHistory" class="mb-4">
                        <h4 class="text-sm font-semibold text-gray-800 dark:text-white mb-2">Previous Medication Referrals</h4>
                        <div id="medReferralHistoryContent" class="text-xs text-gray-600 dark:text-neutral-400 mb-4">
                            <p class="text-center text-gray-400 dark:text-neutral-500">Loading medication referral history...</p>
                        </div>
                    </div>
                    
                    <h4 class="text-sm font-semibold text-gray-800 dark:text-white mb-2">Record New Medication Referral</h4>
                    <form id="medReferralForm" class="space-y-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 dark:text-neutral-300">Subjective</label>
                            <textarea class="w-full border border-gray-300 dark:border-neutral-600 rounded px-3 py-2 text-sm dark:bg-neutral-700 dark:text-white" name="subjective" rows="2" placeholder="Patient's complaints and symptoms"></textarea>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 dark:text-neutral-300">Objective</label>
                            <textarea class="w-full border border-gray-300 dark:border-neutral-600 rounded px-3 py-2 text-sm dark:bg-neutral-700 dark:text-white" name="objective" rows="2" placeholder="Observable signs and measurements"></textarea>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 dark:text-neutral-300">Assessment</label>
                            <textarea class="w-full border border-gray-300 dark:border-neutral-600 rounded px-3 py-2 text-sm dark:bg-neutral-700 dark:text-white" name="assessment" rows="2" placeholder="Clinical judgment and diagnosis"></textarea>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 dark:text-neutral-300">Plan</label>
                            <textarea class="w-full border border-gray-300 dark:border-neutral-600 rounded px-3 py-2 text-sm dark:bg-neutral-700 dark:text-white" name="plan" rows="2" placeholder="Treatment plan and recommendations"></textarea>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 dark:text-neutral-300">Intervention</label>
                            <textarea class="w-full border border-gray-300 dark:border-neutral-600 rounded px-3 py-2 text-sm dark:bg-neutral-700 dark:text-white" name="intervention" rows="2" placeholder="Actions taken during the visit"></textarea>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 dark:text-neutral-300">Evaluation</label>
                            <textarea class="w-full border border-gray-300 dark:border-neutral-600 rounded px-3 py-2 text-sm dark:bg-neutral-700 dark:text-white" name="evaluation" rows="2" placeholder="Outcome assessment and follow-up needed"></textarea>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 dark:text-neutral-300">Referral To</label>
                            <input type="text" class="w-full border border-gray-300 dark:border-neutral-600 rounded px-3 py-2 text-sm dark:bg-neutral-700 dark:text-white" name="referral_to" placeholder="Referral destination (e.g., Dental Clinic, ER, Specialist)" />
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="flex justify-end items-center gap-x-2 py-3 px-4 border-t border-gray-200 dark:border-neutral-700">
                <button id="prescribeMedBtn" type="button" class="py-2 px-3 inline-flex items-center justify-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 focus:outline-hidden focus:bg-blue-700 disabled:opacity-50 disabled:pointer-events-none min-w-[200px]">
                    <i class="ri-capsule-line"></i>
                    Prescribe Medicine
                </button>
                <button id="saveVitalsBtn" type="button" class="py-2 px-3 inline-flex items-center justify-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 focus:outline-hidden focus:bg-blue-700 disabled:opacity-50 disabled:pointer-events-none min-w-[200px] hidden">
                    Save Vital Signs
                </button>
                <button id="saveMedReferralBtn" type="button" class="py-2 px-3 inline-flex items-center justify-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 focus:outline-hidden focus:bg-blue-700 disabled:opacity-50 disabled:pointer-events-none min-w-[200px] hidden">
                    Save Medication Referral
                </button>
            </div>
        </div>
    </div>
    <!-- Prescribe Medicine Modal -->
    <div id="prescribeMedModal" class="fixed inset-0 bg-black bg-opacity-30 flex items-center justify-center z-[60] hidden">
        <div class="w-full max-w-5xl h-[85vh] mx-4 flex flex-col bg-white border border-gray-200 shadow-2xl rounded-xl pointer-events-auto dark:bg-neutral-800 dark:border-neutral-700 dark:shadow-neutral-700/70">
            <div class="flex justify-between items-center py-4 px-6 border-b border-gray-200 dark:border-neutral-700">
                <h3 class="font-bold text-lg text-gray-800 dark:text-white">Prescribe Medicine</h3>
                <button id="closePrescribeMedModal" type="button" class="size-8 inline-flex justify-center items-center gap-x-2 rounded-full border border-transparent bg-gray-100 text-gray-800 hover:bg-gray-200 focus:outline-hidden focus:bg-gray-200 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-700 dark:hover:bg-neutral-600 dark:text-neutral-400 dark:focus:bg-neutral-600" aria-label="Close">
                    <span class="sr-only">Close</span>
                    <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M18 6 6 18"></path>
                        <path d="m6 6 12 12"></path>
                    </svg>
                </button>
            </div>
            
            <div class="p-6 overflow-y-auto flex-1">
            <form id="prescribeMedForm">
                <div id="medsList">
                    <div class="medRow mb-4 border-b pb-4">
                        <div class="mb-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Medicine</label>
                            <select class="medicineSelect w-full border border-gray-300 rounded px-3 py-2 text-sm">
                                <option value="">Select medicine</option>
                                <?php foreach ($medicines as $med): ?>
                                    <option value="<?php echo htmlspecialchars($med['name']); ?>" data-stock="<?php echo htmlspecialchars($med['quantity']); ?>">
                                        <?php echo htmlspecialchars($med['name']); ?> (<?php echo htmlspecialchars($med['quantity']); ?> in stock)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="flex gap-2 mb-2">
                            <div class="flex-1">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Dosage</label>
                                <input type="text" class="w-full border border-gray-300 rounded px-3 py-2 text-sm" placeholder="e.g. 500mg" list="dosageSuggestions" />
                                <datalist id="dosageSuggestions" style="max-height:120px;overflow-y:auto;">
                                    <?php $limitedDosage = array_slice($dosageSuggestions, 0, 5); ?>
                                    <?php foreach ($limitedDosage as $dosage): ?>
                                        <option value="<?php echo htmlspecialchars($dosage); ?>" />
                                    <?php endforeach; ?>
                                </datalist>
                            </div>
                            <div class="flex-1">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Quantity</label>
                                <input type="number" class="w-full border border-gray-300 rounded px-3 py-2 text-sm qtyInput" min="1" list="qtySuggestions" />
                                <datalist id="qtySuggestions" style="max-height:120px;overflow-y:auto;">
                                    <?php $limitedQty = array_slice($qtySuggestions, 0, 5); ?>
                                    <?php foreach ($limitedQty as $qty): ?>
                                        <option value="<?php echo htmlspecialchars($qty); ?>" />
                                    <?php endforeach; ?>
                                </datalist>
                                <span class="text-xs text-gray-500 stockMsg"></span>
                            </div>
                        </div>
                        <div class="mb-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Frequency</label>
                            <input type="text" class="w-full border border-gray-300 rounded px-3 py-2 text-sm" placeholder="e.g. 3x a day" list="frequencySuggestions" />
                            <datalist id="frequencySuggestions" style="max-height:120px;overflow-y:auto;">
                                <?php $limitedFreq = array_slice($frequencySuggestions, 0, 5); ?>
                                <?php foreach ($limitedFreq as $freq): ?>
                                    <option value="<?php echo htmlspecialchars($freq); ?>" />
                                <?php endforeach; ?>
                            </datalist>
                        </div>
                        <div class="mb-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Instructions</label>
                            <input type="text" class="w-full border border-gray-300 rounded px-3 py-2 text-sm" placeholder="e.g. After meals" list="instructionsSuggestions" />
                            <datalist id="instructionsSuggestions" style="max-height:120px;overflow-y:auto;">
                                <?php $limitedInst = array_slice($instructionsSuggestions, 0, 5); ?>
                                <?php foreach ($limitedInst as $inst): ?>
                                    <option value="<?php echo htmlspecialchars($inst); ?>" />
                                <?php endforeach; ?>
                            </datalist>
                        </div>
                        <button type="button" class="removeMedBtn text-xs text-red-500 hover:underline mt-1">Remove</button>
                    </div>
                </div>
                <button type="button" id="addMedRowBtn" class="mb-4 px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600">+ Add Another Medicine</button>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Reason for Prescription *</label>
                    <input type="text" name="reason" class="w-full border border-gray-300 rounded px-3 py-2 text-sm" placeholder="e.g. Fever, Headache, Cough, etc." required list="reasonSuggestions" />
                    <datalist id="reasonSuggestions" style="max-height:120px;overflow-y:auto;">
                        <?php $limitedReason = array_slice($reasonSuggestions, 0, 5); ?>
                        <?php foreach ($limitedReason as $reason): ?>
                            <option value="<?php echo htmlspecialchars($reason); ?>" />
                        <?php endforeach; ?>
                    </datalist>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                    <textarea class="w-full border border-gray-300 rounded px-3 py-2 text-sm" rows="2" placeholder="Additional info..."></textarea>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Patient Email Address</label>
                    <input type="email" name="patient_email" class="w-full border border-gray-300 rounded px-3 py-2 text-sm" placeholder="Enter patient's email address" required list="patientEmailSuggestions" />
                    <datalist id="patientEmailSuggestions" style="max-height:120px;overflow-y:auto;">
                        <?php 
                        $allPatientEmails = getDistinctValues($db, 'imported_patients', 'email');
                        $limitedPatientEmails = array_slice($allPatientEmails, 0, 5);
                        foreach ($limitedPatientEmails as $email): ?>
                            <option value="<?php echo htmlspecialchars($email); ?>" />
                        <?php endforeach; ?>
                    </datalist>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Parent's Email Address</label>
                    <input type="email" name="parent_email" class="w-full border border-gray-300 rounded px-3 py-2 text-sm" placeholder="Enter parent's email address" required list="parentEmailSuggestions" />
                    <datalist id="parentEmailSuggestions" style="max-height:120px;overflow-y:auto;">
                        <?php 
                        $allParentEmails = getDistinctValues($db, 'imported_patients', 'parent_email');
                        $limitedParentEmails = array_slice($allParentEmails, 0, 5);
                        foreach ($limitedParentEmails as $email): ?>
                            <option value="<?php echo htmlspecialchars($email); ?>" />
                        <?php endforeach; ?>
                    </datalist>
                </div>
                <div class="flex justify-center">
                    <button type="submit" class="py-2 px-3 inline-flex items-center justify-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-primary text-white hover:bg-primary/90 focus:outline-hidden focus:bg-primary/90 disabled:opacity-50 disabled:pointer-events-none min-w-[200px]">Submit Prescription</button>
                </div>
            </form>
            </div>
        </div>
    </div>

    <!-- Add New Entry Type Modal (Student or Visitor) -->
    <div id="addEntityTypeModal" class="fixed inset-0 bg-black bg-opacity-30 flex items-center justify-center z-50 hidden">
        <div class="w-full max-w-md mx-4 flex flex-col bg-white border border-gray-200 shadow-2xl rounded-xl pointer-events-auto dark:bg-neutral-800 dark:border-neutral-700 dark:shadow-neutral-700/70">
            <div class="flex justify-between items-center py-4 px-6 border-b border-gray-200 dark:border-neutral-700">
                <h3 class="font-bold text-lg text-gray-800 dark:text-white">Add New</h3>
                <button id="closeAddEntityTypeModalBtn" type="button" class="size-8 inline-flex justify-center items-center gap-x-2 rounded-full border border-transparent bg-gray-100 text-gray-800 hover:bg-gray-200 focus:outline-hidden focus:bg-gray-200 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-700 dark:hover:bg-neutral-600 dark:text-neutral-400 dark:focus:bg-neutral-600" aria-label="Close">
                    <span class="sr-only">Close</span>
                    <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M18 6 6 18"></path>
                        <path d="m6 6 12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="p-6">
                <p class="text-sm text-gray-600 dark:text-neutral-300 mb-4">Choose the type of record to add:</p>
                <div class="grid grid-cols-1 gap-3">
                    <button id="chooseStudentBtn" class="w-full px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90">Student</button>
                    <button id="chooseVisitorBtn" class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Visitor</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add New Student Patient Modal -->
    <div id="addPatientModal" class="fixed inset-0 bg-black bg-opacity-30 flex items-center justify-center z-50 hidden">
        <div class="w-full max-w-5xl h-[85vh] mx-4 flex flex-col bg-white border border-gray-200 shadow-2xl rounded-xl pointer-events-auto dark:bg-neutral-800 dark:border-neutral-700 dark:shadow-neutral-700/70">
            <div class="flex justify-between items-center py-4 px-6 border-b border-gray-200 dark:border-neutral-700">
                <h3 class="font-bold text-lg text-gray-800 dark:text-white">Add New Student Patient</h3>
                <button id="closeAddPatientModalBtn" type="button" class="size-8 inline-flex justify-center items-center gap-x-2 rounded-full border border-transparent bg-gray-100 text-gray-800 hover:bg-gray-200 focus:outline-hidden focus:bg-gray-200 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-700 dark:hover:bg-neutral-600 dark:text-neutral-400 dark:focus:bg-neutral-600" aria-label="Close">
                    <span class="sr-only">Close</span>
                    <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M18 6 6 18"></path>
                        <path d="m6 6 12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="p-6 overflow-y-auto flex-1">
            <form id="addPatientForm" class="space-y-4" autocomplete="off">
                <input type="hidden" name="add_patient" value="1">

                <!-- Personal Information Section -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Student ID *</label>
                        <input type="text" name="student_id" id="student_id_input" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary" placeholder="SCC-00-0000000" required />
                        <div id="student_id_validation" class="mt-1 text-xs"></div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Full Name *</label>
                        <input type="text" name="name" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary" required />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date of Birth *</label>
                        <input type="date" name="dob" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary" required />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Gender *</label>
                        <select name="gender" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary" required>
                            <option value="">Select Gender</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                        <input type="email" name="email" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Contact Number</label>
                        <input type="tel" name="contact_number" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Parent's Email</label>
                        <input type="email" name="parent_email" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Parent's Phone</label>
                        <input type="tel" name="parent_phone" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Religion</label>
                        <input type="text" name="religion" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Citizenship</label>
                        <input type="text" name="citizenship" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Course/Program</label>
                        <input type="text" name="course_program" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Civil Status</label>
                        <select name="civil_status" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                            <option value="">Select Civil Status</option>
                            <option value="Single">Single</option>
                            <option value="Married">Married</option>
                            <option value="Widowed">Widowed</option>
                            <option value="Divorced">Divorced</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Year Level</label>
                        <select name="year_level" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                            <option value="">Select Year Level</option>
                            <option value="1st Year">1st Year</option>
                            <option value="2nd Year">2nd Year</option>
                            <option value="3rd Year">3rd Year</option>
                            <option value="4th Year">4th Year</option>
                            <option value="5th Year">5th Year</option>
                        </select>
                    </div>
                    <div class="relative">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Password *</label>
                        <input type="password" name="password" id="add_patient_password" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary pr-10" required />
                        <span class="absolute right-3 top-9 cursor-pointer" onclick="togglePatientPassword('add_patient_password', this)">
                            <i class="ri-eye-off-line" id="add_patient_password_icon"></i>
                        </span>
                    </div>
                </div>

                <!-- Address Section -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                    <textarea name="address" rows="2" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary"></textarea>
                </div>

                <!-- Guardian Information Section -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Guardian Name</label>
                        <input type="text" name="guardian_name" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Guardian Contact</label>
                        <input type="tel" name="guardian_contact" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary" />
                    </div>
                </div>

                <!-- Emergency Contact Section -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Emergency Contact Name</label>
                        <input type="text" name="emergency_contact_name" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Emergency Contact Number</label>
                        <input type="tel" name="emergency_contact_number" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary" />
                    </div>
                </div>

            </form>
            </div>
            <div class="flex justify-end items-center gap-x-2 py-3 px-4 border-t border-gray-200 dark:border-neutral-700">
                <button type="submit" form="addPatientForm" class="py-2 px-3 inline-flex items-center justify-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-primary text-white hover:bg-primary/90 focus:outline-hidden focus:bg-primary/90 disabled:opacity-50 disabled:pointer-events-none min-w-[200px]">Add Patient</button>
            </div>
        </div>
    </div>

    <!-- Add New Visitor Patient Modal -->
    <div id="addVisitorModal" class="fixed inset-0 bg-black bg-opacity-30 flex items-center justify-center z-50 hidden">
        <div class="w-full max-w-5xl h-[85vh] mx-4 flex flex-col bg-white border border-gray-200 shadow-2xl rounded-xl pointer-events-auto dark:bg-neutral-800 dark:border-neutral-700 dark:shadow-neutral-700/70">
            <div class="flex justify-between items-center py-4 px-6 border-b border-gray-200 dark:border-neutral-700">
                <h3 class="font-bold text-lg text-gray-800 dark:text-white">Add New Visitor Patient</h3>
                <button id="closeAddVisitorModalBtn" type="button" class="size-8 inline-flex justify-center items-center gap-x-2 rounded-full border border-transparent bg-gray-100 text-gray-800 hover:bg-gray-200 focus:outline-hidden focus:bg-gray-200 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-700 dark:hover:bg-neutral-600 dark:text-neutral-400 dark:focus:bg-neutral-600" aria-label="Close">
                    <span class="sr-only">Close</span>
                    <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M18 6 6 18"></path>
                        <path d="m6 6 12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="p-6 overflow-y-auto flex-1">
            <form id="addVisitorForm" class="space-y-4" autocomplete="off">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Full Name *</label>
                    <input type="text" name="full_name" class="w-full border border-gray-300 rounded px-3 py-2 text-sm" required />
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Age *</label>
                        <input type="number" name="age" class="w-full border border-gray-300 rounded px-3 py-2 text-sm" required />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Gender *</label>
                        <select name="gender" class="w-full border border-gray-300 rounded px-3 py-2 text-sm" required>
                            <option value="">Select Gender</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                    <textarea name="address" rows="2" class="w-full border border-gray-300 rounded px-3 py-2 text-sm"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Contact *</label>
                    <input type="text" name="contact" class="w-full border border-gray-300 rounded px-3 py-2 text-sm" required />
                </div>
            </form>
            </div>
            <div class="flex justify-end items-center gap-x-2 py-3 px-4 border-t border-gray-200 dark:border-neutral-700">
                <button type="submit" form="addVisitorForm" class="py-2 px-3 inline-flex items-center justify-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-primary text-white hover:bg-primary/90 focus:outline-hidden focus:bg-primary/90 disabled:opacity-50 disabled:pointer-events-none min-w-[200px]">Add Patient</button>
            </div>
        </div>
    </div>

    <!-- Visitor Details Modal -->
    <div id="visitorProfileModal" class="fixed inset-0 bg-black bg-opacity-30 flex items-center justify-center z-50 hidden">
        <div class="w-full max-w-5xl h-[85vh] mx-4 flex flex-col bg-white border border-gray-200 shadow-2xl rounded-xl pointer-events-auto dark:bg-neutral-800 dark:border-neutral-700 dark:shadow-neutral-700/70">
            <div class="flex justify-between items-center py-4 px-6 border-b border-gray-200 dark:border-neutral-700">
                <h3 id="visitorModalTitle" class="font-bold text-lg text-gray-800 dark:text-white">Visitor Profile</h3>
                <button id="closeVisitorProfileModal" type="button" class="size-8 inline-flex justify-center items-center gap-x-2 rounded-full border border-transparent bg-gray-100 text-gray-800 hover:bg-gray-200 focus:outline-hidden focus:bg-gray-200 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-700 dark:hover:bg-neutral-600 dark:text-neutral-400 dark:focus:bg-neutral-600" aria-label="Close">
                    <span class="sr-only">Close</span>
                    <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M18 6 6 18"></path>
                        <path d="m6 6 12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="p-6 overflow-y-auto flex-1">
                <!-- Navigation Tabs (matching student modal) -->
                <div class="flex justify-start space-x-3 mb-6" id="visitorProfileModalTabs">
                    <button class="tabBtnVisitor px-4 py-2 text-sm rounded-lg font-semibold text-gray-700 bg-gray-200 hover:bg-primary/10 dark:bg-neutral-600 dark:text-neutral-300 dark:hover:bg-neutral-500" data-tab="visitorInfoTab">Information</button>
                    <button class="tabBtnVisitor px-4 py-2 text-sm rounded-lg font-semibold text-gray-700 bg-gray-200 hover:bg-primary/10 dark:bg-neutral-600 dark:text-neutral-300 dark:hover:bg-neutral-500" data-tab="visitorVitalsTab">Vital Signs</button>
                    <button class="tabBtnVisitor px-4 py-2 text-sm rounded-lg font-semibold text-gray-700 bg-gray-200 hover:bg-primary/10 dark:bg-neutral-600 dark:text-neutral-300 dark:hover:bg-neutral-500" data-tab="visitorMedReferralTab">Medication Referral</button>
                </div>
                <!-- Tab Contents -->
                <div id="visitorInfoTab" class="tabContent">
                    <div id="visitorModalDetails" class="text-base text-gray-700 dark:text-neutral-300 mb-6">
                        <!-- Populated by JS -->
                    </div>
                </div>
                <div id="visitorVitalsTab" class="tabContent hidden modal-scroll-area">
                    <h4 class="text-sm font-semibold text-gray-800 dark:text-white mb-2">Visitor Vital Signs</h4>
                    <form id="visitorVitalsForm" class="space-y-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 dark:text-neutral-300">Date</label>
                            <input type="date" class="w-full border border-gray-300 dark:border-neutral-600 rounded px-3 py-2 text-sm dark:bg-neutral-700 dark:text-white" name="vital_date" required />
                        </div>
                        <div class="flex gap-2">
                            <div class="flex-1">
                                <label class="block text-xs font-medium text-gray-700 dark:text-neutral-300">Weight (kg)</label>
                                <input type="number" step="0.01" class="w-full border border-gray-300 dark:border-neutral-600 rounded px-3 py-2 text-sm dark:bg-neutral-700 dark:text-white" name="weight" />
                            </div>
                            <div class="flex-1">
                                <label class="block text-xs font-medium text-gray-700 dark:text-neutral-300">Height (cm)</label>
                                <input type="number" step="0.01" class="w-full border border-gray-300 dark:border-neutral-600 rounded px-3 py-2 text-sm dark:bg-neutral-700 dark:text-white" name="height" />
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <div class="flex-1">
                                <label class="block text-xs font-medium text-gray-700 dark:text-neutral-300">Body Temp (°C)</label>
                                <input type="number" step="0.01" class="w-full border border-gray-300 dark:border-neutral-600 rounded px-3 py-2 text-sm dark:bg-neutral-700 dark:text-white" name="body_temp" />
                            </div>
                            <div class="flex-1">
                                <label class="block text-xs font-medium text-gray-700 dark:text-neutral-300">Respiratory Rate</label>
                                <input type="number" class="w-full border border-gray-300 dark:border-neutral-600 rounded px-3 py-2 text-sm dark:bg-neutral-700 dark:text-white" name="resp_rate" />
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <div class="flex-1">
                                <label class="block text-xs font-medium text-gray-700 dark:text-neutral-300">Pulse</label>
                                <input type="number" class="w-full border border-gray-300 dark:border-neutral-600 rounded px-3 py-2 text-sm dark:bg-neutral-700 dark:text-white" name="pulse" />
                            </div>
                            <div class="flex-1">
                                <label class="block text-xs font-medium text-gray-700 dark:text-neutral-300">Blood Pressure</label>
                                <input type="text" class="w-full border border-gray-300 dark:border-neutral-600 rounded px-3 py-2 text-sm dark:bg-neutral-700 dark:text-white" name="blood_pressure" />
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <div class="flex-1">
                                <label class="block text-xs font-medium text-gray-700 dark:text-neutral-300">Oxygen Saturation (%)</label>
                                <input type="number" step="0.01" class="w-full border border-gray-300 dark:border-neutral-600 rounded px-3 py-2 text-sm dark:bg-neutral-700 dark:text-white" name="oxygen_sat" />
                            </div>
                            <div class="flex-1"></div>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 dark:text-neutral-300">Remarks</label>
                            <textarea class="w-full border border-gray-300 dark:border-neutral-600 rounded px-3 py-2 text-sm dark:bg-neutral-700 dark:text-white" name="remarks" rows="2"></textarea>
                        </div>
                    </form>
                </div>
                <div id="visitorMedReferralTab" class="tabContent hidden modal-scroll-area">
                    <div id="visitorMedReferralHistory" class="mb-4">
                        <h4 class="text-sm font-semibold text-gray-800 dark:text-white mb-2">Previous Medication Referrals</h4>
                        <div id="visitorMedReferralHistoryContent" class="text-xs text-gray-600 dark:text-neutral-400 mb-4">
                            <p class="text-center text-gray-400 dark:text-neutral-500">Loading medication referral history...</p>
                        </div>
                    </div>
                    <h4 class="text-sm font-semibold text-gray-800 dark:text-white mb-2">Record New Medication Referral</h4>
                    <form id="visitorMedReferralForm" class="space-y-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 dark:text-neutral-300">Subjective</label>
                            <textarea class="w-full border border-gray-300 dark:border-neutral-600 rounded px-3 py-2 text-sm dark:bg-neutral-700 dark:text-white" name="subjective" rows="2" placeholder="Patient's complaints and symptoms"></textarea>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 dark:text-neutral-300">Objective</label>
                            <textarea class="w-full border border-gray-300 dark:border-neutral-600 rounded px-3 py-2 text-sm dark:bg-neutral-700 dark:text-white" name="objective" rows="2" placeholder="Observable signs and measurements"></textarea>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 dark:text-neutral-300">Assessment</label>
                            <textarea class="w-full border border-gray-300 dark:border-neutral-600 rounded px-3 py-2 text-sm dark:bg-neutral-700 dark:text-white" name="assessment" rows="2" placeholder="Clinical judgment and diagnosis"></textarea>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 dark:text-neutral-300">Plan</label>
                            <textarea class="w-full border border-gray-300 dark:border-neutral-600 rounded px-3 py-2 text-sm dark:bg-neutral-700 dark:text-white" name="plan" rows="2" placeholder="Treatment plan and recommendations"></textarea>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 dark:text-neutral-300">Intervention</label>
                            <textarea class="w-full border border-gray-300 dark:border-neutral-600 rounded px-3 py-2 text-sm dark:bg-neutral-700 dark:text-white" name="intervention" rows="2" placeholder="Actions taken during the visit"></textarea>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 dark:text-neutral-300">Evaluation</label>
                            <textarea class="w-full border border-gray-300 dark:border-neutral-600 rounded px-3 py-2 text-sm dark:bg-neutral-700 dark:text-white" name="evaluation" rows="2" placeholder="Outcome assessment and follow-up needed"></textarea>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 dark:text-neutral-300">Referral To</label>
                            <input type="text" class="w-full border border-gray-300 dark:border-neutral-600 rounded px-3 py-2 text-sm dark:bg-neutral-700 dark:text-white" name="referral_to" placeholder="Referral destination (e.g., Dental Clinic, ER, Specialist)" />
                        </div>
                    </form>

                </div>
            </div>
            <div class="flex justify-end items-center gap-x-2 py-3 px-4 border-t border-gray-200 dark:border-neutral-700">
                <button id="visitorPrescribeMedBtn" type="button" class="py-2 px-3 inline-flex items-center justify-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 focus:outline-hidden focus:bg-blue-700 disabled:opacity-50 disabled:pointer-events-none min-w-[200px]">
                    <i class="ri-capsule-line"></i>
                    Prescribe Medicine
                </button>
                <button id="saveVisitorVitalsBtn" type="button" class="py-2 px-3 inline-flex items-center justify-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 focus:outline-hidden focus:bg-blue-700 disabled:opacity-50 disabled:pointer-events-none min-w-[200px] hidden">
                    Save Vital Signs
                </button>
                <button id="saveVisitorMedReferralBtn" type="button" class="py-2 px-3 inline-flex items-center justify-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-blue-600 text-white hover:bg-blue-700 focus:outline-hidden focus:bg-blue-700 disabled:opacity-50 disabled:pointer-events-none min-w-[200px] hidden">
                    Save Medication Referral
                </button>
            </div>
        </div>
    </div>

</main>

<script>
$(document).ready(function() {
        // Add New Patient Modal logic
        const addPatientBtn = document.getElementById('addPatientBtn');
        const addEntityTypeModal = document.getElementById('addEntityTypeModal');
        const closeAddEntityTypeModalBtn = document.getElementById('closeAddEntityTypeModalBtn');
        const chooseStudentBtn = document.getElementById('chooseStudentBtn');
        const chooseVisitorBtn = document.getElementById('chooseVisitorBtn');
        const addPatientModal = document.getElementById('addPatientModal');
        const closeAddPatientModalBtn = document.getElementById('closeAddPatientModalBtn');
        const addVisitorModal = document.getElementById('addVisitorModal');
        const closeAddVisitorModalBtn = document.getElementById('closeAddVisitorModalBtn');

        // Open chooser instead of student modal directly
        addPatientBtn.addEventListener('click', () => { addEntityTypeModal.classList.remove('hidden'); $('body, html').addClass('overflow-hidden'); });
        closeAddEntityTypeModalBtn.addEventListener('click', () => { addEntityTypeModal.classList.add('hidden'); $('body, html').removeClass('overflow-hidden'); });
        window.addEventListener('click', (e) => {
            if (e.target === addEntityTypeModal) { addEntityTypeModal.classList.add('hidden'); $('body, html').removeClass('overflow-hidden'); }
        });

        // Choose Student
        chooseStudentBtn.addEventListener('click', () => {
            addEntityTypeModal.classList.add('hidden');
            addPatientModal.classList.remove('hidden');
        });

        // Choose Visitor
        chooseVisitorBtn.addEventListener('click', () => {
            addEntityTypeModal.classList.add('hidden');
            addVisitorModal.classList.remove('hidden');
        });

        // Close student modal → return to chooser
        closeAddPatientModalBtn.addEventListener('click', () => {
            addPatientModal.classList.add('hidden');
            addEntityTypeModal.classList.remove('hidden');
            $('body, html').addClass('overflow-hidden');
        });
        window.addEventListener('click', (e) => {
            if (e.target === addPatientModal) {
                addPatientModal.classList.add('hidden');
                addEntityTypeModal.classList.remove('hidden');
                $('body, html').addClass('overflow-hidden');
            }
        });

        // Close visitor modal → return to chooser
        closeAddVisitorModalBtn.addEventListener('click', () => {
            addVisitorModal.classList.add('hidden');
            addEntityTypeModal.classList.remove('hidden');
            $('body, html').addClass('overflow-hidden');
        });
        window.addEventListener('click', (e) => {
            if (e.target === addVisitorModal) {
                addVisitorModal.classList.add('hidden');
                addEntityTypeModal.classList.remove('hidden');
                $('body, html').addClass('overflow-hidden');
            }
        });

        // Password toggle function for patient modal
        window.togglePatientPassword = function(inputId, element) {
            const input = document.getElementById(inputId);
            const icon = element.querySelector('i');

            if (input.type === 'password') {
                input.type = 'text';
                icon.className = 'ri-eye-line';
            } else {
                input.type = 'password';
                icon.className = 'ri-eye-off-line';
            }
        };

        // Student ID validation
        let studentIdTimeout;
        $('#student_id_input').on('input', function() {
            const studentId = $(this).val().trim();
            const validationDiv = $('#student_id_validation');

            // Clear previous timeout
            clearTimeout(studentIdTimeout);

            // Clear validation message if empty
            if (studentId === '') {
                validationDiv.html('').removeClass('text-red-600 text-green-600');
                return;
            }

            // Set timeout to avoid too many requests
            studentIdTimeout = setTimeout(function() {
                $.ajax({
                    url: 'check_student_id.php',
                    type: 'POST',
                    data: {
                        student_id: studentId
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (!response.valid_format) {
                            validationDiv.html(response.message).removeClass('text-green-600').addClass('text-red-600');
                        } else if (response.exists) {
                            validationDiv.html(response.message).removeClass('text-green-600').addClass('text-red-600');
                        } else {
                            validationDiv.html(response.message).removeClass('text-red-600').addClass('text-green-600');
                        }
                    },
                    error: function() {
                        validationDiv.html('Error checking Student ID. Please try again.').removeClass('text-green-600').addClass('text-red-600');
                    }
                });
            }, 500); // 500ms delay
        });

        // Add Patient Form Submission
        $('#addPatientForm').on('submit', function(e) {
            e.preventDefault();

            // Clear previous messages
            $('#addPatientForm .error-msg, #addPatientForm .success-msg').remove();

            // Check if student ID validation shows an error
            const validationDiv = $('#student_id_validation');
            if (validationDiv.hasClass('text-red-600')) {
                showErrorModal('Please fix the Student ID validation error before submitting.', 'Validation Error');
                return;
            }

            // If validation is still pending, wait a moment
            if (validationDiv.text() === '') {
                showErrorModal('Please wait for Student ID validation to complete.', 'Validation Pending');
                return;
            }

            // Check if Student ID is in correct format
            const studentId = $('#student_id_input').val().trim();
            const formatPattern = /^SCC-\d{2}-\d{7}$/;
            if (!formatPattern.test(studentId)) {
                showErrorModal('Please enter a valid Student ID in format: SCC-00-0000000', 'Invalid Format');
                return;
            }

            $.ajax({
                url: 'add_patient.php',
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        // Show success message
                        if ($('#patientToast').length) $('#patientToast').remove();
                        $('body').append(`
                      <div id="patientToast" style="position:fixed;top:0;left:0;width:100vw;height:100vh;z-index:9999;display:flex;align-items:center;justify-content:center;pointer-events:none;background:rgba(255,255,255,0.18);">
                        <div style="background:rgba(255,255,255,0.7); color:#2563eb; min-width:220px; max-width:90vw; padding:20px 36px; border-radius:16px; box-shadow:0 4px 32px rgba(37,99,235,0.10); font-size:1.1rem; font-weight:500; text-align:center; border:1.5px solid #2563eb; display:flex; align-items:center; gap:12px; pointer-events:auto;">
                          <span style="font-size:2rem;line-height:1;color:#2563eb;">&#10003;</span>
                          <span>Patient added successfully</span>
                        </div>
                      </div>
                    `);

                        // Close modal and reset form
                        $('#addPatientModal').addClass('hidden');
                        $('#addPatientForm')[0].reset();

                        // Reload the page after a short delay
                        setTimeout(function() {
                            $('#patientToast').fadeOut(300, function() {
                                $(this).remove();
                            });
                            window.location.reload();
                        }, 1200);
                    } else {
                        showErrorModal('Error: ' + response.message, 'Error');
                    }
                },
                error: function(xhr, status, error) {
                    showErrorModal('An error occurred while adding the patient. Please try again.', 'Error');
                }
            });
        });

    var table = $('#importedPatientsTable').DataTable({
        "paging": false,
        "ordering": true,
        "info": false,
        "autoWidth": false,
        "dom": 'lrtip'
    });

    // Add Visitor AJAX submission
    $('#addVisitorForm').on('submit', function(e) {
        e.preventDefault();
        const formData = $(this).serialize();
        $.ajax({
            url: 'save_visitor.php',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(resp){
                if(resp.success){
                    if ($('#visitorAddToast').length) $('#visitorAddToast').remove();
                    $('body').append(`
                      <div id="visitorAddToast" style="position:fixed;top:0;left:0;width:100vw;height:100vh;z-index:9999;display:flex;align-items:center;justify-content:center;pointer-events:none;background:rgba(255,255,255,0.18);">
                        <div style="background:rgba(255,255,255,0.7); color:#2563eb; min-width:220px; max-width:90vw; padding:20px 36px; border-radius:16px; box-shadow:0 4px 32px rgba(37,99,235,0.10); font-size:1.1rem; font-weight:500; text-align:center; border:1.5px solid #2563eb; display:flex; align-items:center; gap:12px; pointer-events:auto;">
                          <span style="font-size:2rem;line-height:1;color:#2563eb;">&#10003;</span>
                          <span>Patient added successfully</span>
                        </div>
                      </div>`);
                    setTimeout(function(){
                        $('#visitorAddToast').fadeOut(300, function(){ $(this).remove(); });
                        $('#addVisitorModal').addClass('hidden');
                        $('body, html').removeClass('overflow-hidden');
                        window.location.href = 'records.php';
                    }, 1000);
                } else {
                    showErrorModal('Error: ' + (resp.message || 'Failed to add visitor'), 'Error');
                }
            },
            error: function(){ showErrorModal('An error occurred while saving visitor.', 'Error'); }
        });

        // Table filter: show All, Student, Visitor
        function applyTableFilter(val){
            const studentEl = document.getElementById('studentSection');
            const visitorEl = document.getElementById('visitorSection');
            if (!studentEl || !visitorEl) return;
            if (val === 'all') {
                studentEl.style.removeProperty('display');
                visitorEl.style.removeProperty('display');
                studentEl.classList.remove('hidden');
                visitorEl.classList.remove('hidden');
            } else if (val === 'student') {
                studentEl.style.setProperty('display','', 'important');
                visitorEl.style.setProperty('display','none', 'important');
                studentEl.classList.remove('hidden');
                visitorEl.classList.add('hidden');
            } else if (val === 'visitor') {
                studentEl.style.setProperty('display','none', 'important');
                visitorEl.style.setProperty('display','', 'important');
                studentEl.classList.add('hidden');
                visitorEl.classList.remove('hidden');
            }
        }
        // Delegate change to ensure it binds regardless of load order
        function setBodyFilterClass(val){
            document.body.classList.remove('filter-all','filter-student','filter-visitor');
            document.body.classList.add('filter-' + val);
        }
        $(document).on('change', '#tableFilter', function(){ setBodyFilterClass(this.value); applyTableFilter(this.value); });
        // Apply default on load
        setBodyFilterClass($('#tableFilter').val());
        applyTableFilter($('#tableFilter').val());
    });
    // Connect the custom search bar to the table: filter by Name only (case-insensitive, trimmed)
    $('#searchInput').on('input', function() {
        var val = this.value ? this.value.trim() : '';
        // Remove any previous custom search
        $.fn.dataTable.ext.search = $.fn.dataTable.ext.search.filter(function(fn) {
            return !fn._isNameSearch;
        });
        if (val) {
            $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
                // Name is column index 2 in the DataTable (ID, Student ID, Name, Actions)
                var name = (data[2] || '').toLowerCase();
                return name.indexOf(val.toLowerCase()) !== -1;
            });
                $.fn.dataTable.ext.search[$.fn.dataTable.ext.search.length - 1]._isNameSearch = true;
        }
        table.draw();
    });
    // Remove default DataTables search effect (fix infinite loop)
    // table.on('search.dt', function() {
    //     table.search('').draw(false);
    // });
    // Custom filtering for Year Level and Gender
    $.fn.dataTable.ext.search = $.fn.dataTable.ext.search.filter(function(fn) {
        return !fn._isYearGenderFilter;
    });
    $.fn.dataTable.ext.search.push(
        function(settings, data, dataIndex) {
            var yearLevel = $('#yearLevelFilter').val();
            var gender = $('#genderFilter').val();
            // year_level is column 8, gender is column 4 in the DB, but in the table only a subset is shown
            // Table columns: 0:ID, 1:Student ID, 2:Name, 3:Actions
            // If you want to filter by year_level and gender, you must add those columns to the table or fetch them from data attributes
            return true;
        }
    );
    $('#yearLevelFilter, #genderFilter').on('change', function() {
        table.draw();
    });
    // View button logic
    $(document).on('click', '.viewBtn', function() {
        const name = $(this).data('name');
        const id = $(this).data('id');
        const studentId = $(this).data('student_id');
        const dob = $(this).data('dob');
        const gender = $(this).data('gender');
        const year = $(this).data('year');
        const address = $(this).data('address');
        const civil = $(this).data('civil');
        const email = $(this).data('email');
        const contact = $(this).data('contact');
        const religion = $(this).data('religion');
        const citizenship = $(this).data('citizenship');
        const course = $(this).data('course');
        const guardianName = $(this).data('guardian-name');
        const guardianContact = $(this).data('guardian-contact');
        const emergencyName = $(this).data('emergency-name');
        const emergencyContact = $(this).data('emergency-contact');
        const parentEmail = $(this).data('parent-email');
        const parentPhone = $(this).data('parent-phone');
        
        $('#modalPatientName').text(name + ' (' + id + ')');
        $('#modalPatientDetails').html(
            `<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Left Column -->
                <div class="space-y-6">
                    <!-- Personal Information Section -->
                    <div>
                        <h4 class="text-base font-semibold text-gray-800 dark:text-white mb-3 pb-2 border-b border-gray-200 dark:border-neutral-600">Personal Information</h4>
                        <div class="space-y-3">
                            <div class="grid grid-cols-[140px_1fr] gap-3 items-center">
                                <label class="text-sm font-medium text-gray-700 dark:text-neutral-300">Student ID:</label>
                                <p class="text-sm text-gray-900 dark:text-neutral-200">${studentId || 'N/A'}</p>
                            </div>
                            <div class="grid grid-cols-[140px_1fr] gap-3 items-center">
                                <label class="text-sm font-medium text-gray-700 dark:text-neutral-300">Date of Birth:</label>
                                <p class="text-sm text-gray-900 dark:text-neutral-200">${dob || 'N/A'}</p>
                            </div>
                            <div class="grid grid-cols-[140px_1fr] gap-3 items-center">
                                <label class="text-sm font-medium text-gray-700 dark:text-neutral-300">Gender:</label>
                                <p class="text-sm text-gray-900 dark:text-neutral-200">${gender || 'N/A'}</p>
                            </div>
                            <div class="grid grid-cols-[140px_1fr] gap-3 items-center">
                                <label class="text-sm font-medium text-gray-700 dark:text-neutral-300">Civil Status:</label>
                                <p class="text-sm text-gray-900 dark:text-neutral-200">${civil || 'N/A'}</p>
                            </div>
                            <div class="grid grid-cols-[140px_1fr] gap-3 items-center">
                                <label class="text-sm font-medium text-gray-700 dark:text-neutral-300">Religion:</label>
                                <p class="text-sm text-gray-900 dark:text-neutral-200">${religion || 'N/A'}</p>
                            </div>
                            <div class="grid grid-cols-[140px_1fr] gap-3 items-center">
                                <label class="text-sm font-medium text-gray-700 dark:text-neutral-300">Citizenship:</label>
                                <p class="text-sm text-gray-900 dark:text-neutral-200">${citizenship || 'N/A'}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Academic Information Section -->
                    <div>
                        <h4 class="text-base font-semibold text-gray-800 dark:text-white mb-3 pb-2 border-b border-gray-200 dark:border-neutral-600">Academic Information</h4>
                        <div class="space-y-3">
                            <div class="grid grid-cols-[140px_1fr] gap-3 items-center">
                                <label class="text-sm font-medium text-gray-700 dark:text-neutral-300">Year Level:</label>
                                <p class="text-sm text-gray-900 dark:text-neutral-200">${year || 'N/A'}</p>
                            </div>
                            <div class="grid grid-cols-[140px_1fr] gap-3 items-center">
                                <label class="text-sm font-medium text-gray-700 dark:text-neutral-300">Course/Program:</label>
                                <p class="text-sm text-gray-900 dark:text-neutral-200">${course || 'N/A'}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column -->
                <div class="space-y-6">
                    <!-- Contact Information Section -->
                    <div>
                        <h4 class="text-base font-semibold text-gray-800 dark:text-white mb-3 pb-2 border-b border-gray-200 dark:border-neutral-600">Contact Information</h4>
                        <div class="space-y-3">
                            <div class="grid grid-cols-[140px_1fr] gap-3 items-center">
                                <label class="text-sm font-medium text-gray-700 dark:text-neutral-300">Email:</label>
                                <p class="text-sm text-gray-900 dark:text-neutral-200">${email || 'N/A'}</p>
                            </div>
                            <div class="grid grid-cols-[140px_1fr] gap-3 items-center">
                                <label class="text-sm font-medium text-gray-700 dark:text-neutral-300">Contact Number:</label>
                                <p class="text-sm text-gray-900 dark:text-neutral-200">${contact || 'N/A'}</p>
                            </div>
                            <div class="grid grid-cols-[140px_1fr] gap-3 items-center">
                                <label class="text-sm font-medium text-gray-700 dark:text-neutral-300">Parent's Email:</label>
                                <p class="text-sm text-gray-900 dark:text-neutral-200">${parentEmail || 'N/A'}</p>
                            </div>
                            <div class="grid grid-cols-[140px_1fr] gap-3 items-center">
                                <label class="text-sm font-medium text-gray-700 dark:text-neutral-300">Parent's Phone:</label>
                                <p class="text-sm text-gray-900 dark:text-neutral-200">${parentPhone || 'N/A'}</p>
                            </div>
                            <div class="grid grid-cols-[140px_1fr] gap-3 items-start">
                                <label class="text-sm font-medium text-gray-700 dark:text-neutral-300">Address:</label>
                                <p class="text-sm text-gray-900 dark:text-neutral-200">${address || 'N/A'}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Emergency Contacts Section -->
                    <div>
                        <h4 class="text-base font-semibold text-gray-800 dark:text-white mb-3 pb-2 border-b border-gray-200 dark:border-neutral-600">Emergency Contacts</h4>
                        <div class="space-y-3">
                            <div class="grid grid-cols-[140px_1fr] gap-3 items-center">
                                <label class="text-sm font-medium text-gray-700 dark:text-neutral-300">Guardian Name:</label>
                                <p class="text-sm text-gray-900 dark:text-neutral-200">${guardianName || 'N/A'}</p>
                            </div>
                            <div class="grid grid-cols-[140px_1fr] gap-3 items-center">
                                <label class="text-sm font-medium text-gray-700 dark:text-neutral-300">Guardian Contact:</label>
                                <p class="text-sm text-gray-900 dark:text-neutral-200">${guardianContact || 'N/A'}</p>
                            </div>
                            <div class="grid grid-cols-[140px_1fr] gap-3 items-center">
                                <label class="text-sm font-medium text-gray-700 dark:text-neutral-300">Emergency Contact:</label>
                                <p class="text-sm text-gray-900 dark:text-neutral-200">${emergencyName || 'N/A'}</p>
                            </div>
                            <div class="grid grid-cols-[140px_1fr] gap-3 items-center">
                                <label class="text-sm font-medium text-gray-700 dark:text-neutral-300">Emergency Number:</label>
                                <p class="text-sm text-gray-900 dark:text-neutral-200">${emergencyContact || 'N/A'}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>`
        );
        $('#profileModal').removeClass('hidden');
        $('body, html').addClass('overflow-hidden');
    });
    $('#closeProfileModal').on('click', function() {
        $('#profileModal').addClass('hidden');
        $('body, html').removeClass('overflow-hidden');
    });
    $(window).on('click', function(e) {
        if (e.target === document.getElementById('profileModal')) {
            $('#profileModal').addClass('hidden');
            $('body, html').removeClass('overflow-hidden');
        }
    });
    // Prescribe Medicine Modal logic
    let currentPatientName = '';
    $(document).on('click', '.viewBtn', function() {
        const name = $(this).data('name');
        const id = $(this).data('id');
        const studentId = $(this).data('student_id');
        const dob = $(this).data('dob');
        const gender = $(this).data('gender');
        const year = $(this).data('year');
        const address = $(this).data('address');
        const civil = $(this).data('civil');
        const email = $(this).data('email');
        const contact = $(this).data('contact');
        const religion = $(this).data('religion');
        const citizenship = $(this).data('citizenship');
        const course = $(this).data('course');
        const guardianName = $(this).data('guardian-name');
        const guardianContact = $(this).data('guardian-contact');
        const emergencyName = $(this).data('emergency-name');
        const emergencyContact = $(this).data('emergency-contact');
        const parentEmail = $(this).data('parent-email');
        const parentPhone = $(this).data('parent-phone');
        
        $('#modalPatientName').text(name + ' (' + id + ')');
        $('#modalPatientDetails').html(
            `<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Left Column -->
                <div class="space-y-6">
                    <!-- Personal Information Section -->
                    <div>
                        <h4 class="text-base font-semibold text-gray-800 dark:text-white mb-3 pb-2 border-b border-gray-200 dark:border-neutral-600">Personal Information</h4>
                        <div class="space-y-3">
                            <div class="grid grid-cols-[140px_1fr] gap-3 items-center">
                                <label class="text-sm font-medium text-gray-700 dark:text-neutral-300">Student ID:</label>
                                <p class="text-sm text-gray-900 dark:text-neutral-200">${studentId || 'N/A'}</p>
                            </div>
                            
                            <div class="grid grid-cols-[140px_1fr] gap-3 items-center">
                                <label class="text-sm font-medium text-gray-700 dark:text-neutral-300">Date of Birth:</label>
                                <p class="text-sm text-gray-900 dark:text-neutral-200">${dob || 'N/A'}</p>
                            </div>
                            
                            <div class="grid grid-cols-[140px_1fr] gap-3 items-center">
                                <label class="text-sm font-medium text-gray-700 dark:text-neutral-300">Gender:</label>
                                <p class="text-sm text-gray-900 dark:text-neutral-200">${gender || 'N/A'}</p>
                            </div>
                            
                            <div class="grid grid-cols-[140px_1fr] gap-3 items-center">
                                <label class="text-sm font-medium text-gray-700 dark:text-neutral-300">Civil Status:</label>
                                <p class="text-sm text-gray-900 dark:text-neutral-200">${civil || 'N/A'}</p>
                            </div>
                            
                            <div class="grid grid-cols-[140px_1fr] gap-3 items-center">
                                <label class="text-sm font-medium text-gray-700 dark:text-neutral-300">Religion:</label>
                                <p class="text-sm text-gray-900 dark:text-neutral-200">${religion || 'N/A'}</p>
                            </div>
                            
                            <div class="grid grid-cols-[140px_1fr] gap-3 items-center">
                                <label class="text-sm font-medium text-gray-700 dark:text-neutral-300">Citizenship:</label>
                                <p class="text-sm text-gray-900 dark:text-neutral-200">${citizenship || 'N/A'}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Academic Information Section -->
                    <div>
                        <h4 class="text-base font-semibold text-gray-800 dark:text-white mb-3 pb-2 border-b border-gray-200 dark:border-neutral-600">Academic Information</h4>
                        <div class="space-y-3">
                            <div class="grid grid-cols-[140px_1fr] gap-3 items-center">
                                <label class="text-sm font-medium text-gray-700 dark:text-neutral-300">Year Level:</label>
                                <p class="text-sm text-gray-900 dark:text-neutral-200">${year || 'N/A'}</p>
                            </div>
                            
                            <div class="grid grid-cols-[140px_1fr] gap-3 items-center">
                                <label class="text-sm font-medium text-gray-700 dark:text-neutral-300">Course/Program:</label>
                                <p class="text-sm text-gray-900 dark:text-neutral-200">${course || 'N/A'}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column -->
                <div class="space-y-6">
                    <!-- Contact Information Section -->
                    <div>
                        <h4 class="text-base font-semibold text-gray-800 dark:text-white mb-3 pb-2 border-b border-gray-200 dark:border-neutral-600">Contact Information</h4>
                        <div class="space-y-3">
                            <div class="grid grid-cols-[140px_1fr] gap-3 items-center">
                                <label class="text-sm font-medium text-gray-700 dark:text-neutral-300">Email:</label>
                                <p class="text-sm text-gray-900 dark:text-neutral-200">${email || 'N/A'}</p>
                            </div>
                            
                            <div class="grid grid-cols-[140px_1fr] gap-3 items-center">
                                <label class="text-sm font-medium text-gray-700 dark:text-neutral-300">Contact Number:</label>
                                <p class="text-sm text-gray-900 dark:text-neutral-200">${contact || 'N/A'}</p>
                            </div>
                            <div class="grid grid-cols-[140px_1fr] gap-3 items-center">
                                <label class="text-sm font-medium text-gray-700 dark:text-neutral-300">Parent's Email:</label>
                                <p class="text-sm text-gray-900 dark:text-neutral-200">${parentEmail || 'N/A'}</p>
                            </div>
                            <div class="grid grid-cols-[140px_1fr] gap-3 items-center">
                                <label class="text-sm font-medium text-gray-700 dark:text-neutral-300">Parent's Phone:</label>
                                <p class="text-sm text-gray-900 dark:text-neutral-200">${parentPhone || 'N/A'}</p>
                            </div>
                            <div class="grid grid-cols-[140px_1fr] gap-3 items-start">
                                <label class="text-sm font-medium text-gray-700 dark:text-neutral-300">Address:</label>
                                <p class="text-sm text-gray-900 dark:text-neutral-200">${address || 'N/A'}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Emergency Contacts Section -->
                    <div>
                        <h4 class="text-base font-semibold text-gray-800 dark:text-white mb-3 pb-2 border-b border-gray-200 dark:border-neutral-600">Emergency Contacts</h4>
                        <div class="space-y-3">
                            <div class="grid grid-cols-[140px_1fr] gap-3 items-center">
                                <label class="text-sm font-medium text-gray-700 dark:text-neutral-300">Guardian Name:</label>
                                <p class="text-sm text-gray-900 dark:text-neutral-200">${guardianName || 'N/A'}</p>
                            </div>
                            <div class="grid grid-cols-[140px_1fr] gap-3 items-center">
                                <label class="text-sm font-medium text-gray-700 dark:text-neutral-300">Guardian Contact:</label>
                                <p class="text-sm text-gray-900 dark:text-neutral-200">${guardianContact || 'N/A'}</p>
                            </div>
                            <div class="grid grid-cols-[140px_1fr] gap-3 items-center">
                                <label class="text-sm font-medium text-gray-700 dark:text-neutral-300">Emergency Contact:</label>
                                <p class="text-sm text-gray-900 dark:text-neutral-200">${emergencyName || 'N/A'}</p>
                            </div>
                            <div class="grid grid-cols-[140px_1fr] gap-3 items-center">
                                <label class="text-sm font-medium text-gray-700 dark:text-neutral-300">Emergency Number:</label>
                                <p class="text-sm text-gray-900 dark:text-neutral-200">${emergencyContact || 'N/A'}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>`
        );
        $('#profileModal').removeClass('hidden');
        $('body, html').addClass('overflow-hidden');
        currentPatientName = name;
    });
    // --- PRESCRIBE MODAL: Show med history, then Notes, then a 'Next' button, then medicine fields ---
    $('#prescribeMedBtn').on('click', function() {
        $('#prescribeMedModal').removeClass('hidden');
        $('#prescribeMedModal h3').text('Prescribe Medicine for ' + currentPatientName);
        $('body, html').addClass('overflow-hidden');
        // Fetch and display medication history for this patient
        const patientName = currentPatientName;
        $.ajax({
            url: 'get_patient_med_history.php',
            type: 'POST',
                data: {
                    patient_name: patientName
                },
            success: function(response) {
                let historyHtml = '<div class="mb-4"><strong>Medication History:</strong>';
                historyHtml += '<table class="min-w-full text-xs mb-2 border border-gray-200"><thead><tr class="bg-gray-100"><th class="px-2 py-1 text-left">Date</th><th class="px-2 py-1 text-left">Medicine</th><th class="px-2 py-1 text-left">Dosage</th><th class="px-2 py-1 text-left">Qty</th></tr></thead><tbody>';
                if (response && response.length > 0) {
                    response.forEach(function(item) {
                        historyHtml += `<tr><td class='px-2 py-1 border-t'>${item.prescription_date}</td><td class='px-2 py-1 border-t'>${item.medicine}</td><td class='px-2 py-1 border-t'>${item.dosage}</td><td class='px-2 py-1 border-t'>${item.quantity}</td></tr>`;
                    });
                } else {
                    historyHtml += '<tr><td colspan="4" class="px-2 py-1 text-gray-400 border-t">No medication history found.</td></tr>';
                }
                historyHtml += '</tbody></table></div>';
                // Place med history at the very top of the modal
                $('#prescribeMedForm .med-history').remove();
                // Use a table design for medication history instead of a bulleted list
                let tableHtml = '<div class="mb-4"><strong>Medication History:</strong>';
                tableHtml += '<table class="min-w-full text-xs mb-2 border border-gray-200"><thead><tr class="bg-gray-100"><th class="px-2 py-1 text-left">Date</th><th class="px-2 py-1 text-left">Medicine</th><th class="px-2 py-1 text-left">Dosage</th><th class="px-2 py-1 text-left">Qty</th></tr></thead><tbody>';
                if (response && response.length > 0) {
                    response.forEach(function(item) {
                        tableHtml += `<tr><td class='px-2 py-1 border-t'>${item.prescription_date}</td><td class='px-2 py-1 border-t'>${item.medicine}</td><td class='px-2 py-1 border-t'>${item.dosage}</td><td class='px-2 py-1 border-t'>${item.quantity}</td></tr>`;
                    });
                } else {
                    tableHtml += '<tr><td colspan="4" class="px-2 py-1 text-gray-400 border-t">No medication history found.</td></tr>';
                }
                tableHtml += '</tbody></table></div>';
                $('#prescribeMedForm').prepend(`<div class='med-history'>${tableHtml}</div>`);
                // Move Notes field just below med history
                const notesDiv = $('#prescribeMedForm textarea').closest('.mb-4');
                notesDiv.insertAfter($('#prescribeMedForm .med-history'));
                // Insert Next button after Notes if not present
                if ($('#prescribeMedForm #nextToMedsBtn').length === 0) {
                    notesDiv.after('<button type="button" id="nextToMedsBtn" class="w-full bg-primary text-white py-2 rounded hover:bg-primary/90 mb-4">Prescribe Meds</button>');
                }
                // Hide medsList and addMedRowBtn initially
                $('#medsList, #addMedRowBtn').hide();
            },
            error: function() {
                $('#prescribeMedForm .med-history').remove();
                $('#prescribeMedForm').prepend('<div class="med-history mb-4 text-red-500">Unable to load medication history.</div>');
                const notesDiv = $('#prescribeMedForm textarea').closest('.mb-4');
                notesDiv.insertAfter($('#prescribeMedForm .med-history'));
                if ($('#prescribeMedForm #nextToMedsBtn').length === 0) {
                    notesDiv.after('<button type="button" id="nextToMedsBtn" class="w-full bg-primary text-white py-2 rounded hover:bg-primary/90 mb-4">Prescribe Meds</button>');
                }
                $('#medsList, #addMedRowBtn').hide();
            }
        });
    });
    // Next button logic: show medicine fields
    $(document).on('click', '#nextToMedsBtn', function() {
        // Remove 'required' from all hidden fields before showing
        $('#medsList .medicineSelect, #medsList input, #medsList textarea').removeAttr('required');
        $('#medsList, #addMedRowBtn').show();
        // Restore 'required' attributes only after showing
        setTimeout(function() {
            $('#medsList .medicineSelect, #medsList input[placeholder], #medsList input.qtyInput').each(function() {
                // Only add required to visible fields
                if ($(this).is(':visible')) {
                    if ($(this).attr('placeholder') === 'e.g. 500mg' || $(this).hasClass('qtyInput') || $(this).is('select')) {
                        $(this).attr('required', 'required');
                    }
                }
            });
        }, 10);
        $(this).hide();
    });
    $('#closePrescribeMedModal').on('click', function() {
        $('#prescribeMedModal').addClass('hidden');
        // Reset modal title
        $('#prescribeMedModal h3').text('Prescribe Medicine');
        $('body, html').removeClass('overflow-hidden');
    });
    $(window).on('click', function(e) {
        if (e.target === document.getElementById('prescribeMedModal')) {
            $('#prescribeMedModal').addClass('hidden');
            $('body, html').removeClass('overflow-hidden');
        }
    });
    // Add Medicine Row (no required attributes)
    $('#addMedRowBtn').on('click', function() {
        var newRow = `<div class="medRow mb-4 border-b pb-4">
                            <div class="mb-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Medicine</label>
                                <select class="medicineSelect w-full border border-gray-300 rounded px-3 py-2 text-sm">
                                    <option value="">Select medicine</option>
                                    <?php foreach ($medicines as $med): ?>
                                        <option value="<?php echo htmlspecialchars($med['name']); ?>" data-stock="<?php echo htmlspecialchars($med['quantity']); ?>">
                                            <?php echo htmlspecialchars($med['name']); ?> (<?php echo htmlspecialchars($med['quantity']); ?> in stock)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="flex gap-2 mb-2">
                                <div class="flex-1">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Dosage</label>
                                    <input type="text" class="w-full border border-gray-300 rounded px-3 py-2 text-sm" placeholder="e.g. 500mg" list="dosageSuggestions" />
                                    <datalist id="dosageSuggestions">
                                        <?php foreach ($dosageSuggestions as $dosage): ?>
                                            <option value="<?php echo htmlspecialchars($dosage); ?>" />
                                        <?php endforeach; ?>
                                    </datalist>
                                </div>
                                <div class="flex-1">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Quantity</label>
                                    <input type="number" class="w-full border border-gray-300 rounded px-3 py-2 text-sm qtyInput" min="1" list="qtySuggestions" />
                                    <datalist id="qtySuggestions">
                                        <?php foreach ($qtySuggestions as $qty): ?>
                                            <option value="<?php echo htmlspecialchars($qty); ?>" />
                                        <?php endforeach; ?>
                                    </datalist>
                                    <span class="text-xs text-gray-500 stockMsg"></span>
                                </div>
                            </div>
                            <div class="mb-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Frequency</label>
                                <input type="text" class="w-full border border-gray-300 rounded px-3 py-2 text-sm" placeholder="e.g. 3x a day" list="frequencySuggestions" />
                                <datalist id="frequencySuggestions">
                                    <?php $limitedFreq = array_slice($frequencySuggestions, 0, 5); ?>
                                    <?php foreach ($limitedFreq as $freq): ?>
                                        <option value="<?php echo htmlspecialchars($freq); ?>" />
                                    <?php endforeach; ?>
                                </datalist>
                            </div>
                            <div class="mb-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Instructions</label>
                                <input type="text" class="w-full border border-gray-300 rounded px-3 py-2 text-sm" placeholder="e.g. After meals" list="instructionsSuggestions" />
                                <datalist id="instructionsSuggestions">
                                    <?php $limitedInst = array_slice($instructionsSuggestions, 0, 5); ?>
                                    <?php foreach ($limitedInst as $inst): ?>
                                        <option value="<?php echo htmlspecialchars($inst); ?>" />
                                    <?php endforeach; ?>
                                </datalist>
                            </div>
                            <button type="button" class="removeMedBtn text-xs text-red-500 hover:underline mt-1">Remove</button>
                        </div>`;
        $('#medsList').append($(newRow));
    });
    // Remove Medicine Row
    $(document).on('click', '.removeMedBtn', function() {
        $(this).closest('.medRow').remove();
    });
    // Submit Prescription Form
    $('#prescribeMedForm').on('submit', function(e) {
        e.preventDefault();
        // Clear previous errors and success
        $('#prescribeMedForm .error-msg, #prescribeMedForm .success-msg').remove();
        // Do NOT require any fields in .medRow (all optional)
        var medsData = [];
        $('.medRow').each(function() {
            var row = $(this);
            var med = {
                medicine: row.find('.medicineSelect').val(),
                dosage: row.find('input[placeholder="e.g. 500mg"]').val(),
                quantity: row.find('input.qtyInput').val(),
                frequency: row.find('input[placeholder="e.g. 3x a day"]').val(),
                instructions: row.find('input[placeholder="e.g. After meals"]').val()
            };
            // Add row even if all fields are blank (or skip if you want only non-empty rows)
            if (med.medicine || med.dosage || med.quantity || med.frequency || med.instructions) {
                medsData.push(med);
            }
        });
        var notes = $('#prescribeMedForm textarea').val();
        var reason = $('#prescribeMedForm input[name="reason"]').val();
        var patientId = $('#profileModal').find('#modalPatientName').text().match(/\(([^)]+)\)$/);
        var patientName = $('#profileModal').find('#modalPatientName').text().replace(/\s*\([^)]*\)$/, '');
        var patientEmail = $('#prescribeMedForm input[name="patient_email"]').val();
        var parentEmail = $('#prescribeMedForm input[name="parent_email"]').val();
        $.ajax({
            url: 'submit_prescription.php',
            type: 'POST',
            data: {
                patient_id: patientId ? patientId[1] : '',
                patient_name: patientName,
                medicines: JSON.stringify(medsData),
                reason: reason,
                notes: notes,
                patient_email: patientEmail,
                parent_email: parentEmail
            },
            success: function(response) {
                // Popup with main page blue color, no blur
                if ($('#prescriptionToast').length) $('#prescriptionToast').remove();
                $('body').append(`
                  <div id="prescriptionToast" style="position:fixed;top:0;left:0;width:100vw;height:100vh;z-index:9999;display:flex;align-items:center;justify-content:center;pointer-events:none;background:rgba(255,255,255,0.18);">
                    <div style="background:rgba(255,255,255,0.7); color:#2563eb; min-width:220px; max-width:90vw; padding:20px 36px; border-radius:16px; box-shadow:0 4px 32px rgba(37,99,235,0.10); font-size:1.1rem; font-weight:500; text-align:center; border:1.5px solid #2563eb; display:flex; align-items:center; gap:12px; pointer-events:auto;">
                      <span style="font-size:2rem;line-height:1;color:#2563eb;">&#10003;</span>
                      <span>Prescription submitted</span>
                    </div>
                  </div>
                `);
                setTimeout(function() {
                        $('#prescriptionToast').fadeOut(300, function() {
                            $(this).remove();
                        });
                    $('#prescribeMedModal').addClass('hidden');
                    $('#profileModal').addClass('hidden');
                    window.location.href = 'records.php';
                }, 1200);
            },
            error: function(xhr, status, error) {
                console.error(error);
                showErrorModal('An error occurred while submitting the prescription. Please try again.', 'Error');
            }
        });
    });

    // Tab switching logic for profile modal
    function showProfileTab(tabId) {
        $('#profileModal .tabContent').addClass('hidden');
        $('#' + tabId).removeClass('hidden');
        $('#profileModalTabs .tabBtn').removeClass('bg-primary text-white').addClass('bg-gray-200 text-gray-700 dark:bg-neutral-600 dark:text-neutral-300');
        $('#profileModalTabs .tabBtn[data-tab="' + tabId + '"]').addClass('bg-primary text-white').removeClass('bg-gray-200 text-gray-700 dark:bg-neutral-600 dark:text-neutral-300');
        
        // Show/hide appropriate action buttons based on active tab
        $('#prescribeMedBtn, #saveVitalsBtn, #saveMedReferralBtn').addClass('hidden');
        if (tabId === 'infoTab') {
            $('#prescribeMedBtn').removeClass('hidden');
        } else if (tabId === 'vitalsTab') {
            $('#saveVitalsBtn').removeClass('hidden');
        } else if (tabId === 'medReferralTab') {
            $('#saveMedReferralBtn').removeClass('hidden');
        }
    }
    
    // Save button handlers
    $('#saveVitalsBtn').on('click', function() {
        $('#vitalsForm').submit();
    });
    
    $('#saveMedReferralBtn').on('click', function() {
        $('#medReferralForm').submit();
    });
    // Default to Information tab when modal opens
    $(document).on('click', '.viewBtn', function() {
        showProfileTab('infoTab');
        // Clear vital signs form when new patient is selected
        $('#vitalsForm input[name="weight"]').val('');
        $('#vitalsForm input[name="height"]').val('');
        $('#vitalsForm input[name="body_temp"]').val('');
        $('#vitalsForm input[name="resp_rate"]').val('');
        $('#vitalsForm input[name="pulse"]').val('');
        $('#vitalsForm input[name="blood_pressure"]').val('');
        $('#vitalsForm input[name="oxygen_sat"]').val('');
        $('#vitalsForm textarea[name="remarks"]').val('');
        $('#vitalsForm input[name="vital_date"]').val(new Date().toISOString().split('T')[0]);
        // Clear medication referral form
        $('#medReferralForm')[0].reset();
        $('#medReferralHistoryContent').html('<p class="text-center text-gray-400">Click on Medication Referral tab to load history...</p>');
    });
    // Tab button click
    $('#profileModalTabs .tabBtn').on('click', function() {
        const tabId = $(this).data('tab');
        showProfileTab(tabId);
        
        // Load patient history when switching to vitals or med referral tabs
        if (tabId === 'vitalsTab') {
            loadPatientHistory(); // This will auto-populate vital signs form
        } else if (tabId === 'medReferralTab') {
            loadPatientHistory(); // This will show medication referral history
        }
    });

    // Function to load patient history and populate forms
    function loadPatientHistory() {
        var patientInfo = $('#modalPatientName').text();
        var patientId = patientInfo.match(/\(([^)]+)\)$/);
        var patientName = patientInfo.replace(/\s*\([^)]*\)$/, '');
        
        // Clear all vital signs form fields first
        $('#vitalsForm input[name="weight"]').val('');
        $('#vitalsForm input[name="height"]').val('');
        $('#vitalsForm input[name="body_temp"]').val('');
        $('#vitalsForm input[name="resp_rate"]').val('');
        $('#vitalsForm input[name="pulse"]').val('');
        $('#vitalsForm input[name="blood_pressure"]').val('');
        $('#vitalsForm input[name="oxygen_sat"]').val('');
        $('#vitalsForm textarea[name="remarks"]').val('');
        $('#vitalsForm input[name="vital_date"]').val(new Date().toISOString().split('T')[0]);
        
        $.ajax({
            url: 'get_patient_records.php',
            type: 'POST',
            data: {
                patient_id: patientId ? patientId[1] : '',
                patient_name: patientName
            },
            dataType: 'json',
            success: function(response) {
                // Only populate vital signs form if this specific patient has records
                if (response.vital_signs && response.vital_signs.length > 0) {
                    const latestVitals = response.vital_signs[0]; // First item is the most recent
                    
                    // Populate form fields with latest vital signs for THIS patient only
                    if (latestVitals.weight) $('#vitalsForm input[name="weight"]').val(latestVitals.weight);
                    if (latestVitals.height) $('#vitalsForm input[name="height"]').val(latestVitals.height);
                    if (latestVitals.body_temp) $('#vitalsForm input[name="body_temp"]').val(latestVitals.body_temp);
                    if (latestVitals.resp_rate) $('#vitalsForm input[name="resp_rate"]').val(latestVitals.resp_rate);
                    if (latestVitals.pulse) $('#vitalsForm input[name="pulse"]').val(latestVitals.pulse);
                    if (latestVitals.blood_pressure) $('#vitalsForm input[name="blood_pressure"]').val(latestVitals.blood_pressure);
                    if (latestVitals.oxygen_sat) $('#vitalsForm input[name="oxygen_sat"]').val(latestVitals.oxygen_sat);
                    if (latestVitals.remarks) $('#vitalsForm textarea[name="remarks"]').val(latestVitals.remarks);
                    if (latestVitals.vital_date) $('#vitalsForm input[name="vital_date"]').val(latestVitals.vital_date);
                }
                // If no previous data for this patient, fields remain blank (already cleared above)
                
                // Display medication referral history for this specific patient
                if (response.medication_referrals && response.medication_referrals.length > 0) {
                    let referralHtml = '<div class="space-y-2 max-h-32 overflow-y-auto">';
                    response.medication_referrals.forEach(function(referral) {
                        referralHtml += `<div class="bg-gray-50 p-2 rounded text-xs">
                            <div class="font-semibold">${referral.created_at ? new Date(referral.created_at).toLocaleDateString() : 'No date'}</div>
                            ${referral.assessment ? `<div class="text-xs text-gray-600"><strong>Assessment:</strong> ${referral.assessment}</div>` : ''}
                            ${referral.plan ? `<div class="text-xs text-gray-600"><strong>Plan:</strong> ${referral.plan}</div>` : ''}
                            <div class="text-xs text-gray-500 mt-1">Recorded by: ${referral.recorded_by || 'Staff'}</div>
                        </div>`;
                    });
                    referralHtml += '</div>';
                    $('#medReferralHistoryContent').html(referralHtml);
                } else {
                    $('#medReferralHistoryContent').html('<p class="text-center text-gray-400 text-xs">No medication referrals recorded yet.</p>');
                }
            },
            error: function(xhr, status, error) {
                // If error, keep fields blank and just set today's date
                $('#vitalsForm input[name="vital_date"]').val(new Date().toISOString().split('T')[0]);
                $('#medReferralHistoryContent').html('<p class="text-center text-red-400 text-xs">Error loading medication referral history.</p>');
            }
        });
    }

    // Vital Signs Form Submission
    $('#vitalsForm').on('submit', function(e) {
        e.preventDefault();
        
        // Clear previous messages
        $('#vitalsForm .error-msg, #vitalsForm .success-msg').remove();
        
        // Get patient info from modal
        var patientInfo = $('#modalPatientName').text();
        var patientId = patientInfo.match(/\(([^)]+)\)$/);
        var patientName = patientInfo.replace(/\s*\([^)]*\)$/, '');
        
        // Get form data
        var formData = {
            patient_id: patientId ? patientId[1] : '',
            patient_name: patientName,
            vital_date: $(this).find('input[name="vital_date"]').val(),
            weight: $(this).find('input[name="weight"]').val(),
            height: $(this).find('input[name="height"]').val(),
            body_temp: $(this).find('input[name="body_temp"]').val(),
            resp_rate: $(this).find('input[name="resp_rate"]').val(),
            pulse: $(this).find('input[name="pulse"]').val(),
            blood_pressure: $(this).find('input[name="blood_pressure"]').val(),
            oxygen_sat: $(this).find('input[name="oxygen_sat"]').val(),
            remarks: $(this).find('textarea[name="remarks"]').val()
        };
        
        $.ajax({
            url: 'save_vital_signs.php',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // Remove any previous popup
                    if ($('#vitalsToast').length) $('#vitalsToast').remove();
                    $('body').append(`
                      <div id="vitalsToast" style="position:fixed;top:0;left:0;width:100vw;height:100vh;z-index:9999;display:flex;align-items:center;justify-content:center;pointer-events:none;background:rgba(255,255,255,0.18);">
                        <div style="background:rgba(255,255,255,0.7); color:#2563eb; min-width:220px; max-width:90vw; padding:20px 36px; border-radius:16px; box-shadow:0 4px 32px rgba(37,99,235,0.10); font-size:1.1rem; font-weight:500; text-align:center; border:1.5px solid #2563eb; display:flex; align-items:center; gap:12px; pointer-events:auto;">
                          <span style="font-size:2rem;line-height:1;color:#2563eb;">&#10003;</span>
                          <span>Vital signs saved</span>
                        </div>
                      </div>
                    `);
                    // Don't reset form - keep the data in the fields
                    setTimeout(function() {
                            $('#vitalsToast').fadeOut(300, function() {
                                $(this).remove();
                            });
                    }, 1200);
                } else {
                    showErrorModal('Error: ' + response.message, 'Error');
                }
            },
            error: function(xhr, status, error) {
                showErrorModal('An error occurred while saving vital signs. Please try again.', 'Error');
            }
        });
    });

    // Medication Referral Form Submission
    $('#medReferralForm').on('submit', function(e) {
        e.preventDefault();
        
        // Clear previous messages
        $('#medReferralForm .error-msg, #medReferralForm .success-msg').remove();
        
        // Get patient info from modal
        var patientInfo = $('#modalPatientName').text();
        var patientId = patientInfo.match(/\(([^)]+)\)$/);
        var patientName = patientInfo.replace(/\s*\([^)]*\)$/, '');
        
        // Get form data
        var formData = {
            patient_id: patientId ? patientId[1] : '',
            patient_name: patientName,
            subjective: $(this).find('textarea[name="subjective"]').val(),
            objective: $(this).find('textarea[name="objective"]').val(),
            assessment: $(this).find('textarea[name="assessment"]').val(),
            plan: $(this).find('textarea[name="plan"]').val(),
            intervention: $(this).find('textarea[name="intervention"]').val(),
            evaluation: $(this).find('textarea[name="evaluation"]').val()
        };
        
        $.ajax({
            url: 'save_medication_referral.php',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // Remove any previous popup
                    if ($('#medReferralToast').length) $('#medReferralToast').remove();
                    $('body').append(`
                      <div id="medReferralToast" style="position:fixed;top:0;left:0;width:100vw;height:100vh;z-index:9999;display:flex;align-items:center;justify-content:center;pointer-events:none;background:rgba(255,255,255,0.18);">
                        <div style="background:rgba(255,255,255,0.7); color:#2563eb; min-width:220px; max-width:90vw; padding:20px 36px; border-radius:16px; box-shadow:0 4px 32px rgba(37,99,235,0.10); font-size:1.1rem; font-weight:500; text-align:center; border:1.5px solid #2563eb; display:flex; align-items:center; gap:12px; pointer-events:auto;">
                          <span style="font-size:2rem;line-height:1;color:#2563eb;">&#10003;</span>
                          <span>Medication referral saved</span>
                        </div>
                      </div>
                    `);
                    // Reset form
                    $('#medReferralForm')[0].reset();
                    setTimeout(function() {
                            $('#medReferralToast').fadeOut(300, function() {
                                $(this).remove();
                            });
                    }, 1200);
                } else {
                    showErrorModal('Error: ' + response.message, 'Error');
                }
            },
            error: function(xhr, status, error) {
                showErrorModal('An error occurred while saving medication referral. Please try again.', 'Error');
            }
        });
    });

        // Visitor Modal functionality
        const visitorProfileModal = document.getElementById('visitorProfileModal');
        const closeVisitorProfileModal = document.getElementById('closeVisitorProfileModal');

        function openVisitorModal() { visitorProfileModal.classList.remove('hidden'); $('body, html').addClass('overflow-hidden'); }
        function closeVisitorModal() { visitorProfileModal.classList.add('hidden'); $('body, html').removeClass('overflow-hidden'); }

        closeVisitorProfileModal.addEventListener('click', closeVisitorModal);
        window.addEventListener('click', (e) => { if (e.target === visitorProfileModal) closeVisitorModal(); });

        // View visitor button logic - match student modal layout
        $(document).on('click', '.viewVisitorBtn', function() {
            const id = $(this).data('id') || 'N/A';
            const name = $(this).data('name') || 'N/A';
            const age = $(this).data('age') || 'N/A';
            const gender = $(this).data('gender') || 'N/A';
            const address = $(this).data('address') || 'N/A';
            const contact = $(this).data('contact') || 'N/A';

            $('#visitorModalTitle').text(name + ' (' + id + ')');
            $('#visitorModalDetails').html(`
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <div class="space-y-6">
                        <div class="grid grid-cols-[140px_1fr] gap-3 items-center">
                            <div class="text-sm font-semibold text-gray-600">Visitor ID</div>
                            <div class="text-sm text-gray-900">${id}</div>
                        </div>
                        <div class="grid grid-cols-[140px_1fr] gap-3 items-center">
                            <div class="text-sm font-semibold text-gray-600">Full Name</div>
                            <div class="text-sm text-gray-900">${name}</div>
                        </div>
                        <div class="grid grid-cols-[140px_1fr] gap-3 items-center">
                            <div class="text-sm font-semibold text-gray-600">Age</div>
                            <div class="text-sm text-gray-900">${age}</div>
                        </div>
                        <div class="grid grid-cols-[140px_1fr] gap-3 items-center">
                            <div class="text-sm font-semibold text-gray-600">Gender</div>
                            <div class="text-sm text-gray-900">${gender}</div>
                        </div>
                    </div>
                    <div class="space-y-6">
                        <div class="grid grid-cols-[140px_1fr] gap-3 items-center">
                            <div class="text-sm font-semibold text-gray-600">Contact</div>
                            <div class="text-sm text-gray-900">${contact}</div>
                        </div>
                        <div class="grid grid-cols-[140px_1fr] gap-3 items-start">
                            <div class="text-sm font-semibold text-gray-600">Address</div>
                            <div class="text-sm text-gray-900">${address}</div>
                        </div>
                    </div>
                </div>
            `);

            // Default to Information tab
            showVisitorTab('visitorInfoTab');
            openVisitorModal();
        });

        // Visitor tab switching
        function showVisitorTab(tabId) {
            $('#visitorProfileModal .tabContent').addClass('hidden');
            $('#' + tabId).removeClass('hidden');
            $('#visitorProfileModalTabs .tabBtnVisitor').removeClass('bg-primary text-white').addClass('bg-gray-200 text-gray-700 dark:bg-neutral-600 dark:text-neutral-300');
            $('#visitorProfileModalTabs .tabBtnVisitor[data-tab="' + tabId + '"]').addClass('bg-primary text-white').removeClass('bg-gray-200 text-gray-700 dark:bg-neutral-600 dark:text-neutral-300');
            // Show/hide visitor footer buttons
            $('#visitorPrescribeMedBtn, #saveVisitorVitalsBtn, #saveVisitorMedReferralBtn').addClass('hidden');
            if (tabId === 'visitorInfoTab') {
                $('#visitorPrescribeMedBtn').removeClass('hidden');
            } else if (tabId === 'visitorVitalsTab') {
                $('#saveVisitorVitalsBtn').removeClass('hidden');
            } else if (tabId === 'visitorMedReferralTab') {
                $('#saveVisitorMedReferralBtn').removeClass('hidden');
            }
        }

        $('#visitorProfileModalTabs .tabBtnVisitor').on('click', function() {
            const tabId = $(this).data('tab');
            showVisitorTab(tabId);
            if (tabId === 'visitorVitalsTab' || tabId === 'visitorMedReferralTab') {
                loadVisitorHistory();
            }
        });

        // Visitor Prescribe Medicine modal logic
        let currentVisitorName = '';
        $(document).on('click', '.viewVisitorBtn', function() {
            currentVisitorName = $(this).data('name') || '';
        });
        $('#visitorPrescribeMedBtn').on('click', function() {
            $('#visitorPrescribeMedModal').removeClass('hidden');
            $('#visitorPrescribeMedModal h3').text('Prescribe Medicine for ' + currentVisitorName);
            $('body, html').addClass('overflow-hidden');
            // Fetch and display medication history for this visitor (reuse patient endpoint by name)
            const visitorName = currentVisitorName;
            $.ajax({
                url: 'get_patient_med_history.php',
                type: 'POST',
                data: { patient_name: visitorName },
                success: function(response) {
                    // Build table HTML same as student flow
                    $('#visitorPrescribeMedForm .med-history').remove();
                    let tableHtml = '<div class="mb-4"><strong>Medication History:</strong>';
                    tableHtml += '<table class="min-w-full text-xs mb-2 border border-gray-200"><thead><tr class="bg-gray-100"><th class="px-2 py-1 text-left">Date</th><th class="px-2 py-1 text-left">Medicine</th><th class="px-2 py-1 text-left">Dosage</th><th class="px-2 py-1 text-left">Qty</th></tr></thead><tbody>';
                    if (response && response.length > 0) {
                        response.forEach(function(item) {
                            tableHtml += `<tr><td class='px-2 py-1 border-t'>${item.prescription_date}</td><td class='px-2 py-1 border-t'>${item.medicine}</td><td class='px-2 py-1 border-t'>${item.dosage}</td><td class='px-2 py-1 border-t'>${item.quantity}</td></tr>`;
                        });
                    } else {
                        tableHtml += '<tr><td colspan="4" class="px-2 py-1 text-gray-400 border-t">No medication history found.</td></tr>';
                    }
                    tableHtml += '</tbody></table></div>';
                    $('#visitorPrescribeMedForm').prepend(`<div class='med-history'>${tableHtml}</div>`);
                    // Move Notes under history
                    const notesDiv = $('#visitorPrescribeMedForm textarea').closest('.mb-4');
                    notesDiv.insertAfter($('#visitorPrescribeMedForm .med-history'));
                    // Add Next button if missing
                    if ($('#visitorPrescribeMedForm #visitorNextToMedsBtn').length === 0) {
                        notesDiv.after('<button type="button" id="visitorNextToMedsBtn" class="w-full bg-primary text-white py-2 rounded hover:bg-primary/90 mb-4">Prescribe Meds</button>');
                    }
                    // Hide meds initially
                    $('#visitorMedsList, #visitorAddMedRowBtn').hide();
                },
                error: function() {
                    $('#visitorPrescribeMedForm .med-history').remove();
                    $('#visitorPrescribeMedForm').prepend('<div class="med-history mb-4 text-red-500">Unable to load medication history.</div>');
                    const notesDiv = $('#visitorPrescribeMedForm textarea').closest('.mb-4');
                    notesDiv.insertAfter($('#visitorPrescribeMedForm .med-history'));
                    if ($('#visitorPrescribeMedForm #visitorNextToMedsBtn').length === 0) {
                        notesDiv.after('<button type="button" id="visitorNextToMedsBtn" class="w-full bg-primary text-white py-2 rounded hover:bg-primary/90 mb-4">Prescribe Meds</button>');
                    }
                    $('#visitorMedsList, #visitorAddMedRowBtn').hide();
                }
            });
        });
        $('#closeVisitorPrescribeMedModal').on('click', function() {
            $('#visitorPrescribeMedModal').addClass('hidden');
            $('body, html').removeClass('overflow-hidden');
        });
        $(window).on('click', function(e) {
            if (e.target === document.getElementById('visitorPrescribeMedModal')) {
                $('#visitorPrescribeMedModal').addClass('hidden');
                $('body, html').removeClass('overflow-hidden');
            }
        });
        // Add medicine row for visitor
        $('#visitorAddMedRowBtn').on('click', function() {
            const newRow = `
                <div class="medRow mb-4 border-b pb-4">
                    <div class="mb-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Medicine</label>
                        <select class="medicineSelect w-full border border-gray-300 rounded px-3 py-2 text-sm">
                            <option value="">Select medicine</option>
                            <?php foreach ($medicines as $med): ?>
                                <option value="<?php echo htmlspecialchars($med['name']); ?>" data-stock="<?php echo htmlspecialchars($med['quantity']); ?>">
                                    <?php echo htmlspecialchars($med['name']); ?> (<?php echo htmlspecialchars($med['quantity']); ?> in stock)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="flex gap-2 mb-2">
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Dosage</label>
                            <input type="text" class="w-full border border-gray-300 rounded px-3 py-2 text-sm" placeholder="e.g. 500mg" list="dosageSuggestions" />
                        </div>
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Quantity</label>
                            <input type="number" class="w-full border border-gray-300 rounded px-3 py-2 text-sm qtyInput" min="1" list="qtySuggestions" />
                            <span class="text-xs text-gray-500 stockMsg"></span>
                        </div>
                    </div>
                    <div class="mb-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Frequency</label>
                        <input type="text" class="w-full border border-gray-300 rounded px-3 py-2 text-sm" placeholder="e.g. 3x a day" list="frequencySuggestions" />
                    </div>
                    <div class="mb-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Instructions</label>
                        <input type="text" class="w-full border border-gray-300 rounded px-3 py-2 text-sm" placeholder="e.g. After meals" list="instructionsSuggestions" />
                    </div>
                    <button type="button" class="removeMedBtn text-xs text-red-500 hover:underline mt-1">Remove</button>
                </div>`;
            $('#visitorMedsList').append($(newRow));
        });
        // Submit visitor prescription form
        $('#visitorPrescribeMedForm').on('submit', function(e) {
            e.preventDefault();
            var medsData = [];
            $('#visitorMedsList .medRow').each(function() {
                var row = $(this);
                var med = {
                    medicine: row.find('.medicineSelect').val(),
                    dosage: row.find('input[placeholder="e.g. 500mg"]').val(),
                    quantity: row.find('input.qtyInput').val(),
                    frequency: row.find('input[placeholder="e.g. 3x a day"]').val(),
                    instructions: row.find('input[placeholder="e.g. After meals"]').val()
                };
                if (med.medicine || med.dosage || med.quantity || med.frequency || med.instructions) {
                    medsData.push(med);
                }
            });
            var notes = $('#visitorPrescribeMedForm textarea').val();
            var reason = $('#visitorPrescribeMedForm input[name="reason"]').val();
            var titleText = $('#visitorModalTitle').text();
            var idMatch = titleText.match(/\(([^)]+)\)$/);
            var visitorId = idMatch ? idMatch[1] : '';
            var visitorName = titleText.replace(/\s*\([^)]*\)$/,'');
            var recipientEmail = $('#visitorPrescribeMedForm input[name="recipient_email"]').val();

            $.ajax({
                url: 'submit_prescription.php',
                type: 'POST',
                data: {
                    patient_id: visitorId,
                    patient_name: visitorName,
                    medicines: JSON.stringify(medsData),
                    reason: reason,
                    notes: notes,
                    patient_email: recipientEmail
                },
                success: function(response) {
                    if ($('#prescriptionToast').length) $('#prescriptionToast').remove();
                    $('body').append(`
                      <div id="prescriptionToast" style="position:fixed;top:0;left:0;width:100vw;height:100vh;z-index:9999;display:flex;align-items:center;justify-content:center;pointer-events:none;background:rgba(255,255,255,0.18);">
                        <div style="background:rgba(255,255,255,0.7); color:#2563eb; min-width:220px; max-width:90vw; padding:20px 36px; border-radius:16px; box-shadow:0 4px 32px rgba(37,99,235,0.10); font-size:1.1rem; font-weight:500; text-align:center; border:1.5px solid #2563eb; display:flex; align-items:center; gap:12px; pointer-events:auto;">
                          <span style="font-size:2rem;line-height:1;color:#2563eb;">&#10003;</span>
                          <span>Prescription submitted</span>
                        </div>
                      </div>
                    `);
                    setTimeout(function() {
                        $('#prescriptionToast').fadeOut(300, function() { $(this).remove(); });
                        $('#visitorPrescribeMedModal').addClass('hidden');
                        $('#visitorProfileModal').addClass('hidden');
                        window.location.href = 'records.php';
                    }, 1200);
                },
                error: function() {
                    showErrorModal('An error occurred while submitting the prescription. Please try again.', 'Error');
                }
            });
        });

        // Reveal visitor meds section after Next button
        $(document).on('click', '#visitorNextToMedsBtn', function() {
            $('#visitorMedsList, #visitorAddMedRowBtn').show();
            $(this).remove();
        });

        // Load visitor history and populate vitals form
        function loadVisitorHistory() {
            var titleText = $('#visitorModalTitle').text();
            var idMatch = titleText.match(/\(([^)]+)\)$/);
            var visitorId = idMatch ? idMatch[1] : '';
            var visitorName = titleText.replace(/\s*\([^)]*\)$/, '');

            // Clear form first
            $('#visitorVitalsForm input[name="weight"]').val('');
            $('#visitorVitalsForm input[name="height"]').val('');
            $('#visitorVitalsForm input[name="body_temp"]').val('');
            $('#visitorVitalsForm input[name="resp_rate"]').val('');
            $('#visitorVitalsForm input[name="pulse"]').val('');
            $('#visitorVitalsForm input[name="blood_pressure"]').val('');
            $('#visitorVitalsForm input[name="oxygen_sat"]').val('');
            $('#visitorVitalsForm textarea[name="remarks"]').val('');
            $('#visitorVitalsForm input[name="vital_date"]').val(new Date().toISOString().split('T')[0]);

            $.ajax({
                url: 'get_visitor_records.php',
                type: 'POST',
                data: {
                    visitor_id: visitorId,
                    visitor_name: visitorName
                },
                dataType: 'json',
                success: function(response) {
                    if (response.vital_signs && response.vital_signs.length > 0) {
                        const latestVitals = response.vital_signs[0];
                        if (latestVitals.weight) $('#visitorVitalsForm input[name="weight"]').val(latestVitals.weight);
                        if (latestVitals.height) $('#visitorVitalsForm input[name="height"]').val(latestVitals.height);
                        if (latestVitals.body_temp) $('#visitorVitalsForm input[name="body_temp"]').val(latestVitals.body_temp);
                        if (latestVitals.resp_rate) $('#visitorVitalsForm input[name="resp_rate"]').val(latestVitals.resp_rate);
                        if (latestVitals.pulse) $('#visitorVitalsForm input[name="pulse"]').val(latestVitals.pulse);
                        if (latestVitals.blood_pressure) $('#visitorVitalsForm input[name="blood_pressure"]').val(latestVitals.blood_pressure);
                        if (latestVitals.oxygen_sat) $('#visitorVitalsForm input[name="oxygen_sat"]').val(latestVitals.oxygen_sat);
                        if (latestVitals.remarks) $('#visitorVitalsForm textarea[name="remarks"]').val(latestVitals.remarks);
                        if (latestVitals.vital_date) $('#visitorVitalsForm input[name="vital_date"]').val(latestVitals.vital_date);
                    }
                    // Render visitor referral history
                    if (response.medication_referrals && response.medication_referrals.length > 0) {
                        let referralHtml = '<div class="space-y-2 max-h-32 overflow-y-auto">';
                        response.medication_referrals.forEach(function(referral) {
                            referralHtml += `<div class="bg-gray-50 p-2 rounded text-xs">
                                <div class="font-semibold">${referral.created_at ? new Date(referral.created_at).toLocaleDateString() : 'No date'}</div>
                                ${referral.assessment ? `<div class="text-xs text-gray-600"><strong>Assessment:</strong> ${referral.assessment}</div>` : ''}
                                ${referral.plan ? `<div class="text-xs text-gray-600"><strong>Plan:</strong> ${referral.plan}</div>` : ''}
                                <div class="text-xs text-gray-500 mt-1">Recorded by: ${referral.recorded_by || 'Staff'}</div>
                            </div>`;
                        });
                        referralHtml += '</div>';
                        $('#visitorMedReferralHistoryContent').html(referralHtml);
                    } else {
                        $('#visitorMedReferralHistoryContent').html('<p class="text-center text-gray-400 text-xs">No medication referrals recorded yet.</p>');
                    }
                },
                error: function() {
                    $('#visitorVitalsForm input[name="vital_date"]').val(new Date().toISOString().split('T')[0]);
                    $('#visitorMedReferralHistoryContent').html('<p class="text-center text-red-400 text-xs">Error loading medication referral history.</p>');
                }
            });
        }

        // Visitor Vital Signs Form Submission
        $('#visitorVitalsForm').on('submit', function(e) {
            e.preventDefault();

            // Clear previous messages
            $('#visitorVitalsForm .error-msg, #visitorVitalsForm .success-msg').remove();

            // Get form data
            var vt = $('#visitorModalTitle').text();
            var vidMatch = vt.match(/\(([^)]+)\)$/);
            var visitorId = vidMatch ? vidMatch[1] : '';
            var visitorName = vt.replace(/\s*\([^)]*\)$/, '');
            var formData = {
                visitor_id: visitorId,
                visitor_name: visitorName,
                vital_date: $(this).find('input[name="vital_date"]').val(),
                weight: $(this).find('input[name="weight"]').val(),
                height: $(this).find('input[name="height"]').val(),
                body_temp: $(this).find('input[name="body_temp"]').val(),
                resp_rate: $(this).find('input[name="resp_rate"]').val(),
                pulse: $(this).find('input[name="pulse"]').val(),
                blood_pressure: $(this).find('input[name="blood_pressure"]').val(),
                oxygen_sat: $(this).find('input[name="oxygen_sat"]').val(),
                remarks: $(this).find('textarea[name="remarks"]').val(),
                entity_type: 'visitor'
            };

            $.ajax({
                url: 'save_vital_signs.php',
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        // Remove any previous popup
                        if ($('#vitalsToast').length) $('#vitalsToast').remove();
                        $('body').append(`
                      <div id="vitalsToast" style="position:fixed;top:0;left:0;width:100vw;height:100vh;z-index:9999;display:flex;align-items:center;justify-content:center;pointer-events:none;background:rgba(255,255,255,0.18);">
                        <div style="background:rgba(255,255,255,0.7); color:#2563eb; min-width:220px; max-width:90vw; padding:20px 36px; border-radius:16px; box-shadow:0 4px 32px rgba(37,99,235,0.10); font-size:1.1rem; font-weight:500; text-align:center; border:1.5px solid #2563eb; display:flex; align-items:center; gap:12px; pointer-events:auto;">
                          <span style="font-size:2rem;line-height:1;color:#2563eb;">&#10003;</span>
                          <span>Vital signs saved</span>
                        </div>
                      </div>
                    `);
                        // Don't reset form - keep the data in the fields
                        setTimeout(function() {
                            $('#vitalsToast').fadeOut(300, function() {
                                $(this).remove();
                            });
                        }, 1200);
                    } else {
                        showErrorModal('Error: ' + response.message, 'Error');
                    }
                },
                error: function(xhr, status, error) {
                    showErrorModal('An error occurred while saving vital signs. Please try again.', 'Error');
                }
            });
        });

        // Visitor Medication Referral Form Submission
        $('#visitorMedReferralForm').on('submit', function(e) {
            e.preventDefault();

            // Clear previous messages
            $('#visitorMedReferralForm .error-msg, #visitorMedReferralForm .success-msg').remove();

            // Get form data
            var vt = $('#visitorModalTitle').text();
            var vidMatch = vt.match(/\(([^)]+)\)$/);
            var visitorId = vidMatch ? vidMatch[1] : '';
            var visitorName = vt.replace(/\s*\([^)]*\)$/, '');
            var formData = {
                visitor_id: visitorId,
                visitor_name: visitorName,
                subjective: $(this).find('textarea[name="subjective"]').val(),
                objective: $(this).find('textarea[name="objective"]').val(),
                assessment: $(this).find('textarea[name="assessment"]').val(),
                plan: $(this).find('textarea[name="plan"]').val(),
                intervention: $(this).find('textarea[name="intervention"]').val(),
                evaluation: $(this).find('textarea[name="evaluation"]').val(),
                entity_type: 'visitor'
            };

            $.ajax({
                url: 'save_medication_referral.php',
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        // Remove any previous popup
                        if ($('#medReferralToast').length) $('#medReferralToast').remove();
                        $('body').append(`
                      <div id="medReferralToast" style="position:fixed;top:0;left:0;width:100vw;height:100vh;z-index:9999;display:flex;align-items:center;justify-content:center;pointer-events:none;background:rgba(255,255,255,0.18);">
                        <div style="background:rgba(255,255,255,0.7); color:#2563eb; min-width:220px; max-width:90vw; padding:20px 36px; border-radius:16px; box-shadow:0 4px 32px rgba(37,99,235,0.10); font-size:1.1rem; font-weight:500; text-align:center; border:1.5px solid #2563eb; display:flex; align-items:center; gap:12px; pointer-events:auto;">
                          <span style="font-size:2rem;line-height:1;color:#2563eb;">&#10003;</span>
                          <span>Medication referral saved</span>
                        </div>
                      </div>
                    `);
                        // Reset form
                        $('#visitorMedReferralForm')[0].reset();
                        setTimeout(function() {
                            $('#medReferralToast').fadeOut(300, function() {
                                $(this).remove();
                            });
                        }, 1200);
                    } else {
                        showErrorModal('Error: ' + response.message, 'Error');
                    }
                },
                error: function(xhr, status, error) {
                    showErrorModal('An error occurred while saving medication referral. Please try again.', 'Error');
                }
            });
        });

        // Submit Visitor Vital Signs
        $('#visitorVitalsForm').on('submit', function(e) {
            e.preventDefault();

            // Extract visitor info from title
            var titleText = $('#visitorModalTitle').text();
            var idMatch = titleText.match(/\(([^)]+)\)$/);
            var visitorId = idMatch ? idMatch[1] : '';
            var visitorName = titleText.replace(/\s*\([^)]*\)$/, '');

            var formData = {
                visitor_id: visitorId,
                visitor_name: visitorName,
                vital_date: $(this).find('input[name="vital_date"]').val(),
                weight: $(this).find('input[name="weight"]').val(),
                height: $(this).find('input[name="height"]').val(),
                body_temp: $(this).find('input[name="body_temp"]').val(),
                resp_rate: $(this).find('input[name="resp_rate"]').val(),
                pulse: $(this).find('input[name="pulse"]').val(),
                blood_pressure: $(this).find('input[name="blood_pressure"]').val(),
                oxygen_sat: $(this).find('input[name="oxygen_sat"]').val(),
                remarks: $(this).find('textarea[name="remarks"]').val(),
                entity_type: 'visitor'
            };

            $.ajax({
                url: 'save_vital_signs.php',
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        if ($('#vitalsToast').length) $('#vitalsToast').remove();
                        $('body').append(`
                        <div id="vitalsToast" style="position:fixed;top:0;left:0;width:100vw;height:100vh;z-index:9999;display:flex;align-items:center;justify-content:center;pointer-events:none;background:rgba(255,255,255,0.18);">
                          <div style="background:rgba(255,255,255,0.7); color:#2563eb; min-width:220px; max-width:90vw; padding:20px 36px; border-radius:16px; box-shadow:0 4px 32px rgba(37,99,235,0.10); font-size:1.1rem; font-weight:500; text-align:center; border:1.5px solid #2563eb; display:flex; align-items:center; gap:12px; pointer-events:auto;">
                            <span style="font-size:2rem;line-height:1;color:#2563eb;">&#10003;</span>
                            <span>Visitor vital signs saved!</span>
                          </div>
                        </div>`);
                        setTimeout(function(){ $('#vitalsToast').fadeOut(300, function(){ $(this).remove(); }); }, 1200);
                    } else {
                        showErrorModal('Error: ' + response.message, 'Error');
                    }
                },
                error: function(){ showErrorModal('An error occurred while saving visitor vital signs.', 'Error'); }
            });
        });

        // Submit Visitor Medication Referral
        $('#visitorMedReferralForm').on('submit', function(e) {
            e.preventDefault();

            var titleText = $('#visitorModalTitle').text();
            var idMatch = titleText.match(/\(([^)]+)\)$/);
            var visitorId = idMatch ? idMatch[1] : '';
            var visitorName = titleText.replace(/\s*\([^)]*\)$/, '');

            var formData = {
                visitor_id: visitorId,
                visitor_name: visitorName,
                subjective: $(this).find('textarea[name="subjective"]').val(),
                objective: $(this).find('textarea[name="objective"]').val(),
                assessment: $(this).find('textarea[name="assessment"]').val(),
                plan: $(this).find('textarea[name="plan"]').val(),
                intervention: $(this).find('textarea[name="intervention"]').val(),
                evaluation: $(this).find('textarea[name="evaluation"]').val(),
                entity_type: 'visitor'
            };

            $.ajax({
                url: 'save_medication_referral.php',
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        if ($('#medReferralToast').length) $('#medReferralToast').remove();
                        $('body').append(`
                        <div id="medReferralToast" style="position:fixed;top:0;left:0;width:100vw;height:100vh;z-index:9999;display:flex;align-items:center;justify-content:center;pointer-events:none;background:rgba(255,255,255,0.18);">
                          <div style="background:rgba(255,255,255,0.7); color:#2563eb; min-width:220px; max-width:90vw; padding:20px 36px; border-radius:16px; box-shadow:0 4px 32px rgba(37,99,235,0.10); font-size:1.1rem; font-weight:500; text-align:center; border:1.5px solid #2563eb; display:flex; align-items:center; gap:12px; pointer-events:auto;">
                            <span style="font-size:2rem;line-height:1;color:#2563eb;">&#10003;</span>
                            <span>Visitor referral saved!</span>
                          </div>
                        </div>`);
                        $('#visitorMedReferralForm')[0].reset();
                        setTimeout(function(){ $('#medReferralToast').fadeOut(300, function(){ $(this).remove(); }); }, 1200);
                    } else {
                        showErrorModal('Error: ' + response.message, 'Error');
                    }
                },
                error: function(){ showErrorModal('An error occurred while saving visitor medication referral.', 'Error'); }
        });
    });
});
</script>

<style>
    html,
    body {
        scrollbar-width: none;
        /* Firefox */
        -ms-overflow-style: none;
        /* Internet Explorer 10+ */
    }

  html::-webkit-scrollbar,
  body::-webkit-scrollbar {
        display: none;
        /* Safari and Chrome */
  }

@keyframes fade-in {
        from {
            opacity: 0;
            transform: translateY(-4px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

.animate-fade-in {
  animation: fade-in 0.3s ease;
}

.prescribe-modal-scroll {
  max-height: 80vh;
  overflow-y: scroll;
        border-radius: 0.75rem;
        /* Match modal's rounded-lg */
  /* Always reserve space for scrollbar, so content never shifts */
  scrollbar-gutter: stable both-edges;
}

.prescribe-modal-scroll::-webkit-scrollbar {
  width: 10px;
  border-radius: 0.75rem;
  background: transparent;
}

.prescribe-modal-scroll::-webkit-scrollbar-thumb {
  border-radius: 0.75rem;
        background: #c1c1c1;
        /* Use a neutral default, but let browser override */
  border: 2px solid transparent;
  background-clip: padding-box;
}

.prescribe-modal-scroll::-webkit-scrollbar-thumb:hover {
  background: #a0a0a0;
}

.tabContent {
  min-height: 400px;
  overflow-y: auto;
}

/* For Firefox */
.prescribe-modal-scroll {
  scrollbar-width: auto;
  scrollbar-color: auto;
}

/* Consistent scroll area for modal tab content */
.modal-scroll-area {
    max-height: 350px;
    overflow-y: auto;
    padding-right: 4px;
}

.modal-scroll-area::-webkit-scrollbar {
    width: 8px;
}

.modal-scroll-area::-webkit-scrollbar-thumb {
    background: #e5e7eb;
    border-radius: 4px;
}

.modal-scroll-area::-webkit-scrollbar-thumb:hover {
    background: #cbd5e1;
}
</style>
</style>