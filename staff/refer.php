<?php
include '../includes/header.php';

// Database connection
try {
    $db = new PDO('mysql:host=localhost;dbname=clinic_management_system;charset=utf8', 'root', '');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Debug: Let's check the actual data first
    $debug_stmt = $db->query('SELECT patient_id FROM medication_referrals LIMIT 3');
    $referral_ids = $debug_stmt->fetchAll(PDO::FETCH_COLUMN);
    
    $debug_stmt2 = $db->query('SELECT student_id FROM imported_patients LIMIT 3');
    $patient_ids = $debug_stmt2->fetchAll(PDO::FETCH_COLUMN);
    
    error_log("Referral patient_ids: " . print_r($referral_ids, true));
    error_log("Imported patient student_ids: " . print_r($patient_ids, true));

    // Check if referral_to column exists, if not add it
    try {
        $stmt = $db->query("SHOW COLUMNS FROM medication_referrals LIKE 'referral_to'");
        if ($stmt->rowCount() == 0) {
            $db->exec("ALTER TABLE medication_referrals ADD COLUMN referral_to VARCHAR(255) DEFAULT NULL");
        }
    } catch (Exception $e) {
        // Column might already exist, continue
    }

    // Fetch all medication referrals with patient year level and latest vital signs
    $stmt = $db->query('
        SELECT mr.*, ip.year_level,
               ip.student_id as matched_student_id,
               mr.patient_id as referral_patient_id,
               vs.body_temp, vs.resp_rate, vs.pulse, vs.blood_pressure, vs.weight, vs.height, vs.oxygen_sat
        FROM medication_referrals mr 
        LEFT JOIN imported_patients ip ON 
            CAST(TRIM(mr.patient_id) AS CHAR) = CAST(TRIM(ip.student_id) AS CHAR)
            OR TRIM(mr.patient_name) = TRIM(ip.name)
        LEFT JOIN (
            SELECT patient_id, body_temp, resp_rate, pulse, blood_pressure, weight, height, oxygen_sat,
                   ROW_NUMBER() OVER (PARTITION BY patient_id ORDER BY created_at DESC) as rn
            FROM vital_signs
        ) vs ON CAST(TRIM(mr.patient_id) AS CHAR) = CAST(TRIM(vs.patient_id) AS CHAR) AND vs.rn = 1
        ORDER BY mr.created_at DESC
    ');
    $referrals = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Debug: Check if we're getting year_level data
    if (!empty($referrals)) {
        error_log("First referral data: " . print_r($referrals[0], true));
    }
} catch (PDOException $e) {
    die('Database connection failed: ' . $e->getMessage());
}
?>

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

<main class="flex-1 overflow-y-auto bg-gray-50 p-6 ml-16 md:ml-64 mt-[56px]">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Referral Records</h2>
        <div class="bg-white rounded shadow p-6">
            <div class="overflow-x-auto">
                <table id="referralTable" class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left font-semibold text-gray-600">Date</th>
                            <th class="px-4 py-2 text-left font-semibold text-gray-600">Patient Name</th>
                            <th class="px-4 py-2 text-left font-semibold text-gray-600">Patient ID</th>
                            <th class="px-4 py-2 text-left font-semibold text-gray-600">Recorded By</th>
                            <th class="px-4 py-2 text-center font-semibold text-gray-600">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($referrals as $referral): ?>
                        <tr>
                            <td class="px-4 py-2"><?php echo date('Y-m-d', strtotime($referral['created_at'])); ?></td>
                            <td class="px-4 py-2"><?php echo htmlspecialchars($referral['patient_name']); ?></td>
                            <td class="px-4 py-2"><?php echo htmlspecialchars($referral['patient_id']); ?></td>
                            <td class="px-4 py-2"><?php echo htmlspecialchars($referral['recorded_by'] ?? 'Staff'); ?></td>
                            <td class="px-4 py-2 text-center">
                                <!-- Debug info -->
                                <!-- Referral ID: <?php echo htmlspecialchars($referral['referral_patient_id'] ?? 'NULL'); ?> -->
                                <!-- Matched Student ID: <?php echo htmlspecialchars($referral['matched_student_id'] ?? 'NULL'); ?> -->
                                <!-- Year Level: <?php echo htmlspecialchars($referral['year_level'] ?? 'NULL'); ?> -->
                                <button class="viewReferralBtn px-3 py-1 text-xs bg-blue-600 text-white rounded hover:bg-blue-700" 
                                        data-id="<?php echo $referral['id']; ?>"
                                        data-patient-name="<?php echo htmlspecialchars($referral['patient_name']); ?>"
                                        data-patient-id="<?php echo htmlspecialchars($referral['patient_id']); ?>"
                                        data-year-level="<?php echo htmlspecialchars($referral['year_level'] ?? 'NO-MATCH-FOUND'); ?>"
                                        data-debug-referral-id="<?php echo htmlspecialchars($referral['referral_patient_id'] ?? 'NULL'); ?>"
                                        data-debug-matched-id="<?php echo htmlspecialchars($referral['matched_student_id'] ?? 'NULL'); ?>"
                                        data-date="<?php echo date('Y-m-d', strtotime($referral['created_at'])); ?>"
                                        data-recorded-by="<?php echo htmlspecialchars($referral['recorded_by'] ?? 'Staff'); ?>"
                                        data-subjective="<?php echo htmlspecialchars($referral['subjective']); ?>"
                                        data-objective="<?php echo htmlspecialchars($referral['objective']); ?>"
                                        data-assessment="<?php echo htmlspecialchars($referral['assessment']); ?>"
                                        data-plan="<?php echo htmlspecialchars($referral['plan']); ?>"
                                        data-intervention="<?php echo htmlspecialchars($referral['intervention']); ?>"
                                        data-evaluation="<?php echo htmlspecialchars($referral['evaluation']); ?>"
                                        data-referral-to="<?php echo htmlspecialchars($referral['referral_to'] ?? ''); ?>"
                                        data-body-temp="<?php echo htmlspecialchars($referral['body_temp'] ?? ''); ?>"
                                        data-resp-rate="<?php echo htmlspecialchars($referral['resp_rate'] ?? ''); ?>"
                                        data-pulse="<?php echo htmlspecialchars($referral['pulse'] ?? ''); ?>"
                                        data-blood-pressure="<?php echo htmlspecialchars($referral['blood_pressure'] ?? ''); ?>"
                                        data-weight="<?php echo htmlspecialchars($referral['weight'] ?? ''); ?>"
                                        data-height="<?php echo htmlspecialchars($referral['height'] ?? ''); ?>"
                                        data-oxygen-sat="<?php echo htmlspecialchars($referral['oxygen_sat'] ?? ''); ?>">
                                    View
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Referral View Modal -->
    <div id="referralViewModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-lg shadow-lg w-full max-w-4xl max-h-[90vh] overflow-y-auto p-8 relative">
            <button id="closeReferralModal" class="absolute top-4 right-4 text-gray-500 hover:text-gray-700">
                <i class="ri-close-line ri-2x"></i>
            </button>
            
            <!-- Printable Content -->
            <div id="printableContent" class="space-y-6">
                <!-- Header -->
                <div class="text-center border-b pb-4 mb-6">
                    <div class="flex justify-center mb-4">
                        <div class="w-16 h-16 border-2 border-gray-400 rounded-full flex items-center justify-center">
                            <span class="text-xs">LOGO</span>
                        </div>
                    </div>
                    <h1 class="text-2xl font-bold text-gray-800 mb-2">MEDICAL REFERRAL</h1>
                </div>

                <!-- Form Layout -->
                <div class="space-y-4">
                    <!-- Date Row - Top Right -->
                    <div class="flex justify-end mb-16">
                        <div class="flex items-center">
                            <span class="font-semibold text-sm mr-2">Date:</span>
                            <span id="modalDate" class="text-sm"><?php echo date('Y-m-d'); ?></span>
                        </div>
                    </div>

                    <!-- Name of Student and Grade/Level Row -->
                    <div class="grid grid-cols-2 gap-8">
                        <div>
                            <div class="flex items-center">
                                <span class="font-semibold text-sm mr-2">Name of Student:</span>
                                <span id="modalPatientName" class="text-sm"></span>
                            </div>
                        </div>
                        <div>
                            <div class="flex items-center">
                                <span class="font-semibold text-sm mr-2">Grade/Level:</span>
                                <span id="modalYearLevel" class="text-sm"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Initial Vital Signs Row -->
                    <div class="mt-4">
                        <div class="text-sm">
                            <div class="font-semibold mb-2">Initial Vital Signs</div>
                            <div class="grid grid-cols-4 gap-6 text-xs">
                                <div class="flex items-center">
                                    <span class="font-medium mr-2">BP:</span>
                                    <span id="modalBP"></span>
                                </div>
                                <div class="flex items-center">
                                    <span class="font-medium mr-2">P:</span>
                                    <span id="modalPulse"></span>
                                </div>
                                <div class="flex items-center">
                                    <span class="font-medium mr-2">R:</span>
                                    <span id="modalRespRate"></span>
                                </div>
                                <div class="flex items-center">
                                    <span class="font-medium mr-2">Temp:</span>
                                    <span id="modalTemp"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Third Row - Incident Information -->
                    

                    <!-- SOAP Notes Section -->
                    <div class="space-y-4 mt-40 pt-8" >
                        <!-- Subjective -->
                        <div>
                            <div class="flex items-start mb-2">
                                <span class="font-semibold text-sm mr-2">Observation / Additional Information</span>
                            </div>
                            <div class="mb-3">
                                <span class="font-semibold text-sm">Subjective =</span>
                                <div class="ml-4">
                                    <div class="border-b border-gray-300 pb-1 mb-1 min-h-[20px]">
                                        <span id="modalSubjective" class="text-xs"></span>
                                    </div>
                                    <div class="border-b border-gray-300 pb-1 mb-1 min-h-[20px]"></div>
                                    <div class="border-b border-gray-300 pb-1 mb-1 min-h-[20px]"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Objective -->
                        <div>
                            <span class="font-semibold text-sm">Objective =</span>
                            <div class="ml-4">
                                <div class="border-b border-gray-300 pb-1 mb-1 min-h-[20px]">
                                    <span id="modalObjective" class="text-xs"></span>
                                </div>
                                <div class="border-b border-gray-300 pb-1 mb-1 min-h-[20px]"></div>
                                <div class="border-b border-gray-300 pb-1 mb-1 min-h-[20px]"></div>
                            </div>
                        </div>

                        <!-- Assessment -->
                        <div>
                            <span class="font-semibold text-sm">Assessment =</span>
                            <div class="ml-4">
                                <div class="border-b border-gray-300 pb-1 mb-1 min-h-[20px]">
                                    <span id="modalAssessment" class="text-xs"></span>
                                </div>
                                <div class="border-b border-gray-300 pb-1 mb-1 min-h-[20px]"></div>
                                <div class="border-b border-gray-300 pb-1 mb-1 min-h-[20px]"></div>
                            </div>
                        </div>

                        <!-- Plan -->
                        <div>
                            <span class="font-semibold text-sm">Plan =</span>
                            <div class="ml-4">
                                <div class="border-b border-gray-300 pb-1 mb-1 min-h-[20px]">
                                    <span id="modalPlan" class="text-xs"></span>
                                </div>
                                <div class="border-b border-gray-300 pb-1 mb-1 min-h-[20px]"></div>
                                <div class="border-b border-gray-300 pb-1 mb-1 min-h-[20px]"></div>
                            </div>
                        </div>

                        <!-- Intervention -->
                        <div>
                            <span class="font-semibold text-sm">Intervention =</span>
                            <div class="ml-4">
                                <div class="border-b border-gray-300 pb-1 mb-1 min-h-[20px]">
                                    <span id="modalIntervention" class="text-xs"></span>
                                </div>
                                <div class="border-b border-gray-300 pb-1 mb-1 min-h-[20px]"></div>
                            </div>
                        </div>

                        <!-- Evaluation -->
                        <div>
                            <span class="font-semibold text-sm">Evaluation =</span>
                            <div class="ml-4">
                                <div class="border-b border-gray-300 pb-1 mb-1 min-h-[20px]">
                                    <span id="modalEvaluation" class="text-xs"></span>
                                </div>
                                <div class="border-b border-gray-300 pb-1 mb-1 min-h-[20px]"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Vital Signs Footer -->
                    

                    <!-- Referral Section -->
                    <div class="mt-6">
                        <div class="flex items-center mb-4">
                            <span class="font-semibold text-sm mr-2">Referral to:</span>
                            <input type="text" id="modalReferralTo" class="flex-1 px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-blue-500" placeholder="Enter referral destination">
                        </div>
                    </div>

                    <!-- Signature -->
                    <div class="flex justify-start mt-40 pt-8">
                        <div class="text-center">
                            
                            <div class="text-sm">
                                <div class="font-semibold" id="modalRecordedBy">Mrs. Vilma A. Valencia</div>
                                <div class="border-b border-gray-400 w-48 mt-2"></div>
                                <div class="text-xs">School Nurse</div>
                            </div>
                        </div>
                    </div>

                    <!-- Print Button - Bottom Right -->
                    <div class="flex justify-end mt-6">
                        <button id="printReferralBtn" class="px-3 py-1 text-xs bg-green-600 text-white rounded hover:bg-green-700 flex items-center">
                            <i class="ri-printer-line mr-1"></i> Print
                        </button>
                    </div>
                </div>
            </div>
        </div>
</main>

<script>
$(document).ready(function() {
    // Initialize DataTable
    $('#referralTable').DataTable({
        "pageLength": 10,
        "lengthChange": false,
        "ordering": true,
        "info": true,
        "autoWidth": false,
        "order": [[0, "desc"]], // Sort by date descending
        "language": {
            "paginate": {
                "previous": "Prev",
                "next": "Next"
            }
        }
    });

    // View Referral Button Click
    $(document).on('click', '.viewReferralBtn', function() {
        const data = $(this).data();
        
        // Store current referral ID globally for printing
        window.currentReferralId = data.id;
        
        // Show modal first
        $('#referralViewModal').removeClass('hidden');
        
        // Get current date dynamically and set immediately
        const today = new Date();
        const year = today.getFullYear();
        const month = String(today.getMonth() + 1).padStart(2, '0');
        const day = String(today.getDate()).padStart(2, '0');
        const currentDate = `${year}-${month}-${day}`;
        
        // Set date immediately - no setTimeout
        $('#modalDate').text(currentDate);
        
        // Populate other modal data
        $('#modalPatientName').text(data.patientName || 'N/A');
        
        // Debug: Check all possible ways to get year level
        console.log('All data attributes:', data);
        console.log('data.yearLevel:', data.yearLevel);
        console.log('data["year-level"]:', data["year-level"]);
        console.log('Button element:', this);
        console.log('Raw data-year-level attr:', $(this).attr('data-year-level'));
        
        // Try multiple ways to get the year level
        let yearLevel = data.yearLevel || data["year-level"] || $(this).attr('data-year-level') || 'Still N/A';
        $('#modalYearLevel').text(yearLevel);
        
        $('#modalRecordedBy').text(data.recordedBy || 'Staff');
        $('#modalSubjective').text(data.subjective || 'No information provided');
        $('#modalObjective').text(data.objective || 'No information provided');
        $('#modalAssessment').text(data.assessment || 'No information provided');
        $('#modalPlan').text(data.plan || 'No information provided');
        $('#modalIntervention').text(data.intervention || 'No information provided');
        $('#modalEvaluation').text(data.evaluation || 'No information provided');
        
        // Populate referral_to field - always show as editable input initially
        const existingReferralTo = data.referralTo || data["referral-to"] || $(this).attr('data-referral-to') || '';
        $('#modalReferralTo').val(existingReferralTo);
        
        // Populate vital signs data from the latest records
        console.log('Debug blood pressure data:', {
            dataBloodPressure: data.bloodPressure,
            dataBloodPressureKebab: data["blood-pressure"],
            attrBloodPressure: $(this).attr('data-blood-pressure')
        });
        
        $('#modalBP').text(data.bloodPressure || data["blood-pressure"] || $(this).attr('data-blood-pressure') || '');
        $('#modalPulse').text(data.pulse || $(this).attr('data-pulse') || '');
        $('#modalRespRate').text(data.respRate || data["resp-rate"] || $(this).attr('data-resp-rate') || '');
        $('#modalTemp').text(data.bodyTemp || data["body-temp"] || $(this).attr('data-body-temp') || '');
        
        console.log('Current date set to:', currentDate);
        console.log('Modal date element text:', $('#modalDate').text());
        console.log('All vital signs data:', {
            bloodPressure: data.bloodPressure || data["blood-pressure"] || $(this).attr('data-blood-pressure'),
            pulse: data.pulse || $(this).attr('data-pulse'),
            respRate: data.respRate || data["resp-rate"] || $(this).attr('data-resp-rate'),
            bodyTemp: data.bodyTemp || data["body-temp"] || $(this).attr('data-body-temp')
        });
    });

    // Close Modal
    $('#closeReferralModal').on('click', function() {
        $('#referralViewModal').addClass('hidden');
        
        // Reset referral section back to input field for next use
        resetReferralSection();
    });

    // Close modal when clicking outside
    $(window).on('click', function(e) {
        if (e.target === document.getElementById('referralViewModal')) {
            $('#referralViewModal').addClass('hidden');
            
            // Reset referral section back to input field for next use
            resetReferralSection();
        }
    });
    
    // Function to reset referral section to input field
    function resetReferralSection() {
        const referralSection = $('#modalReferralTo').parent();
        referralSection.html(`
            <span class="font-semibold text-sm mr-2">Referral to:</span>
            <input type="text" id="modalReferralTo" class="flex-1 px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-1 focus:ring-blue-500" placeholder="Enter referral destination">
        `);
    }

    // Print Referral
    $('#printReferralBtn').on('click', function() {
        // Check if "Referral to" field is filled - handle both input field and static text
        let referralToValue = '';
        const referralInput = $('#modalReferralTo');
        
        if (referralInput.length && referralInput.is('input')) {
            // It's still an input field
            referralToValue = referralInput.val().trim();
        } else {
            // It's been converted to static text, get the text content
            const staticReferralSpan = referralInput.parent().find('span').last();
            referralToValue = staticReferralSpan.text().trim();
        }
        
        if (!referralToValue) {
            showErrorModal('Please fill in the "Referral to" field before printing.', 'Validation Error');
            if (referralInput.length && referralInput.is('input')) {
                referralInput.focus(); // Focus on the empty field if it's still an input
            }
            return; // Stop the printing process
        }
        
        // Create a new window for printing
        const printWindow = window.open('', '_blank', 'width=800,height=600');
        const printContent = document.getElementById('printableContent').innerHTML;
        
        printWindow.document.write(`
            <!DOCTYPE html>
            <html>
            <head>
                <title>Medical Referral Form</title>
                <style>
                    @page {
                        size: A4;
                        margin: 0.5in;
                    }
                    body { 
                        font-family: Arial, sans-serif; 
                        margin: 0;
                        padding: 0;
                        line-height: 1.3;
                        color: #333;
                        font-size: 12px;
                        height: 100vh;
                        display: flex;
                        flex-direction: column;
                    }
                    .text-center { text-align: center; }
                    .font-bold { font-weight: bold; }
                    .text-2xl { font-size: 18px; margin-bottom: 4px; }
                    .text-lg { font-size: 14px; }
                    .text-sm { font-size: 11px; }
                    .text-xs { font-size: 10px; }
                    .mb-2 { margin-bottom: 4px; }
                    .mb-3 { margin-bottom: 6px; }
                    .mb-6 { margin-bottom: 8px; }
                    .mt-2 { margin-top: 4px; }
                    .mt-8 { margin-top: 12px; }
                    .p-3 { padding: 8px; }
                    .pt-4 { padding-top: 8px; }
                    .pb-4 { padding-bottom: 8px; }
                    .border { border: 1px solid #ccc; }
                    .border-b { border-bottom: 1px solid #ccc; }
                    .border-t { border-top: 1px solid #ccc; }
                    .border-gray-400 { border-color: #9ca3af; }
                    .bg-gray-50 { background-color: #f9fafb; }
                    .text-gray-600 { color: #6b7280; }
                    .text-gray-500 { color: #9ca3af; }
                    .text-gray-800 { color: #1f2937; }
                    .rounded { border-radius: 4px; }
                    .space-y-2 > * + * { margin-top: 4px; }
                    .space-y-4 > * + * { margin-top: 6px; }
                    .space-y-6 > * + * { margin-top: 8px; }
                    .grid { display: grid; }
                    .grid-cols-2 { grid-template-columns: repeat(2, 1fr); }
                    .gap-6 { gap: 12px; }
                    .gap-8 { gap: 16px; }
                    .min-h-60 { min-height: 40px; }
                    .w-full { width: 100%; }
                    
                    /* Preserve modal spacing for print */
                    .mt-40 { margin-top: 10rem !important; }
                    .pt-8 { padding-top: 2rem !important; }
                    .mt-6 { margin-top: 1.5rem !important; }
                    .mt-12 { margin-top: 3rem !important; }
                    .mb-16 { margin-bottom: 4rem !important; }
                    .gap-8 { gap: 2rem !important; }
                    .gap-6 { gap: 1.5rem !important; }
                    
                    /* Ensure inline styles are preserved */
                    [style*="margin-top: 4rem"] { margin-top: 4rem !important; }
                    
                    /* Form layout preservation */
                    .grid-cols-2 { 
                        display: grid; 
                        grid-template-columns: repeat(2, 1fr); 
                    }
                    .grid-cols-4 { 
                        display: grid; 
                        grid-template-columns: repeat(4, 1fr); 
                    }
                    .flex { display: flex; }
                    .justify-end { justify-content: flex-end; }
                    .justify-start { justify-content: flex-start; }
                    .justify-center { justify-content: center; }
                    .items-center { align-items: center; }
                    .items-start { align-items: flex-start; }
                    
                    /* Hide print button in print view */
                    #printReferralBtn { display: none !important; }
                    
                    @media print {
                        body { 
                            margin: 0; 
                            font-size: 12px !important;
                            height: auto;
                        }
                        .no-print { display: none; }
                        #printReferralBtn { display: none !important; }
                        
                        /* Preserve exact modal spacing */
                        .mt-40 { margin-top: 10rem !important; }
                        .pt-8 { padding-top: 2rem !important; }
                        .mt-6 { margin-top: 1.5rem !important; }
                        .mt-12 { margin-top: 3rem !important; }
                        .mb-16 { margin-bottom: 4rem !important; }
                        .gap-8 { gap: 2rem !important; }
                        .gap-6 { gap: 1.5rem !important; }
                        .space-y-4 > * + * { margin-top: 1rem !important; }
                        .space-y-6 > * + * { margin-top: 1.5rem !important; }
                        
                        /* Ensure proper layout structure */
                        .grid { display: grid !important; }
                        .grid-cols-2 { grid-template-columns: repeat(2, 1fr) !important; }
                        .grid-cols-4 { grid-template-columns: repeat(4, 1fr) !important; }
                        .flex { display: flex !important; }
                        .justify-end { justify-content: flex-end !important; }
                        .justify-start { justify-content: flex-start !important; }
                        .text-center { text-align: center !important; }
                        
                        /* Preserve border styling */
                        .border-b { border-bottom: 1px solid #9ca3af !important; }
                        .border-gray-400 { border-color: #9ca3af !important; }
                        .w-48 { width: 12rem !important; }
                        
                        /* Font sizes */
                        .text-2xl { font-size: 1.5rem !important; }
                        .text-sm { font-size: 0.875rem !important; }
                        .text-xs { font-size: 0.75rem !important; }
                        
                        /* Color adjustments for print */
                        * {
                            -webkit-print-color-adjust: exact !important;
                            color-adjust: exact !important;
                        }
                        
                        .min-h-60 { 
                            min-height: 20px !important; 
                        }
                    }
                </style>
            </head>
            <body>
                ${printContent}
            </body>
            </html>
        `);
        
        printWindow.document.close();
        printWindow.focus();
        printWindow.print();
        printWindow.close();
        
        // Save the referral data to database after printing
        const referralId = window.currentReferralId;
        
        // Send AJAX request to save referral destination
        $.ajax({
            url: 'update_referral.php',
            type: 'POST',
            data: {
                referral_id: referralId,
                referral_to: referralToValue
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    console.log('Referral destination saved successfully');
                    
                    // Change the input field to display static text after successful save
                    const referralSection = $('#modalReferralTo').parent();
                    referralSection.html(`
                        <span class="font-semibold text-sm mr-2">Referral to:</span>
                        <span class="text-sm font-medium text-gray-800">${referralToValue}</span>
                    `);
                    
                    // Optional: Show success message to user
                    // alert('Referral printed and saved successfully!');
                } else {
                    console.error('Failed to save referral destination:', response.message);
                    showWarningModal('Print completed, but failed to save referral destination.', 'Warning');
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX error:', error);
                showErrorModal('Print completed, but there was an error saving the referral destination.', 'Error');
            }
        });
    });
});
</script>
</main>
