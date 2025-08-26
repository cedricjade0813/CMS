<?php
include '../includep/header.php';
$student_id = $_SESSION['student_row_id'];
// Fetch prescription history for this patient only
$prescriptionHistory = [];
try {
    $db = new PDO('mysql:host=localhost;dbname=clinic_management_system;charset=utf8', 'root', '');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $stmt = $db->prepare('SELECT prescription_date, medicines, reason FROM prescriptions WHERE patient_id = ? ORDER BY prescription_date DESC');
    $stmt->execute([$student_id]);
    $prescriptionHistory = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $prescriptionHistory = [];
}

// Flatten prescription history so each medicine is a row
$medicationRows = [];
foreach ($prescriptionHistory as $presc) {
    // Format date to YYYY-MM-DD only
    $date = htmlspecialchars(substr($presc['prescription_date'], 0, 10));
    $reason = htmlspecialchars($presc['reason'] ?? 'N/A');
    $meds = json_decode($presc['medicines'], true);
    if (is_array($meds)) {
        foreach ($meds as $med) {
            $medicationRows[] = [
                'date' => $date,
                'reason' => $reason,
                'medicine' => htmlspecialchars($med['medicine'] ?? ''),
                'quantity' => htmlspecialchars($med['quantity'] ?? ''),
                'presc' => $presc
            ];
        }
    }
}
// Pagination for medication rows
$selected_date = isset($_GET['filter_date']) ? $_GET['filter_date'] : '';
if ($selected_date) {
    $medicationRows = array_filter($medicationRows, function($row) use ($selected_date) {
        return $row['date'] === htmlspecialchars($selected_date);
    });
}
$med_records_per_page = 10;
$med_page = isset($_GET['med_page']) ? (int)$_GET['med_page'] : 1;
$med_page = max($med_page, 1);
$med_offset = ($med_page - 1) * $med_records_per_page;
$total_med_records = count($medicationRows);
$total_med_pages = ceil($total_med_records / $med_records_per_page);
$medicationRows_paginated = array_slice($medicationRows, $med_offset, $med_records_per_page);
?>
<main class="flex-1 overflow-y-auto bg-gray-50 p-6 ml-16 md:ml-64 mt-[56px]">
        <h2 class="text-2xl font-bold mb-6 text-gray-800">Medical History</h2>
        <!-- Filters -->
        <form method="get" class="flex flex-wrap gap-4 items-end mb-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                <input type="date" name="filter_date" value="<?php echo htmlspecialchars($selected_date); ?>" class="border border-gray-300 rounded px-3 py-2 text-sm" />
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Illness</label>
                <input type="text" class="border border-gray-300 rounded px-3 py-2 text-sm" placeholder="Search illness..." disabled />
            </div>
            <button type="submit" class="px-4 py-2 bg-primary text-white rounded hover:bg-primary/90">Filter</button>
            <?php if ($selected_date): ?>
                <a href="?" class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300 ml-2">Clear</a>
            <?php endif; ?>
        </form>
        <!-- Visit Log Table: Medication History -->
        <div class="bg-white rounded shadow p-6 mb-8">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold">Medication History</h3>
                <button class="px-4 py-2 bg-primary text-white rounded hover:bg-primary/90 flex items-center"><i class="ri-download-2-line mr-1"></i> Download as PDF</button>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left font-semibold text-gray-600">Date</th>
                            <th class="px-4 py-2 text-left font-semibold text-gray-600">Reason</th>
                            <th class="px-4 py-2 text-left font-semibold text-gray-600">Medicine</th>
                            <th class="px-4 py-2 text-left font-semibold text-gray-600">Quantity</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (!empty($medicationRows_paginated)) {
                            foreach ($medicationRows_paginated as $row) {
                                echo "<tr>";
                                echo "<td class='px-4 py-2 flex items-center gap-2'>";
                                echo "<button onclick='viewPrescriptionDetails(" . json_encode($row['presc']) . ")' class='viewPrescriptionBtn text-primary hover:text-blue-700' title='View Details'><i class='ri-eye-line text-lg'></i></button>";
                                echo $row['date'];
                                echo "</td>";
                                echo "<td class='px-4 py-2'>" . $row['reason'] . "</td>";
                                echo "<td class='px-4 py-2'>" . $row['medicine'] . "</td>";
                                echo "<td class='px-4 py-2'>" . $row['quantity'] . "</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='4' class='px-4 py-2 text-center text-gray-500'>No medication history found.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            <?php if ($total_med_records > 0): ?>
            <div class="flex justify-between items-center mt-6">
                <div class="text-sm text-gray-600">
                    <?php 
                    $med_start = $med_offset + 1;
                    $med_end = min($med_offset + $med_records_per_page, $total_med_records);
                    ?>
                    Showing <?php echo $med_start; ?> to <?php echo $med_end; ?> of <?php echo $total_med_records; ?> entries
                </div>
                <?php if ($total_med_pages > 1): ?>
                <nav class="flex justify-end items-center -space-x-px" aria-label="Pagination">
                    <!-- Previous Button -->
                    <?php if ($med_page > 1): ?>
                        <a href="?med_page=<?php echo $med_page - 1; ?><?php echo $selected_date ? '&filter_date=' . urlencode($selected_date) : ''; ?>" class="min-h-9.5 min-w-9.5 py-2 px-2.5 inline-flex justify-center items-center gap-x-1.5 text-sm first:rounded-s-lg last:rounded-e-lg border border-gray-200 text-gray-800 hover:bg-gray-100 focus:outline-hidden focus:bg-gray-100" aria-label="Previous">
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
                    $med_start_page = max(1, $med_page - 2);
                    $med_end_page = min($total_med_pages, $med_page + 2);
                    if ($med_start_page > 1): ?>
                        <a href="?med_page=1<?php echo $selected_date ? '&filter_date=' . urlencode($selected_date) : ''; ?>" class="min-h-9.5 min-w-9.5 flex justify-center items-center border border-gray-200 text-gray-800 hover:bg-gray-100 py-2 px-3 text-sm first:rounded-s-lg last:rounded-e-lg focus:outline-hidden focus:bg-gray-100">1</a>
                        <?php if ($med_start_page > 2): ?>
                            <span class="min-h-9.5 min-w-9.5 flex justify-center items-center border border-gray-200 text-gray-800 py-2 px-3 text-sm">...</span>
                        <?php endif; ?>
                    <?php endif; ?>
                    <?php for ($i = $med_start_page; $i <= $med_end_page; $i++): ?>
                        <?php if ($i == $med_page): ?>
                            <button type="button" class="min-h-9.5 min-w-9.5 flex justify-center items-center bg-gray-200 text-gray-800 border border-gray-200 py-2 px-3 text-sm first:rounded-s-lg last:rounded-e-lg focus:outline-hidden focus:bg-gray-300" aria-current="page"><?php echo $i; ?></button>
                        <?php else: ?>
                            <a href="?med_page=<?php echo $i; ?><?php echo $selected_date ? '&filter_date=' . urlencode($selected_date) : ''; ?>" class="min-h-9.5 min-w-9.5 flex justify-center items-center border border-gray-200 text-gray-800 hover:bg-gray-100 py-2 px-3 text-sm first:rounded-s-lg last:rounded-e-lg focus:outline-hidden focus:bg-gray-100"><?php echo $i; ?></a>
                        <?php endif; ?>
                    <?php endfor; ?>
                    <?php if ($med_end_page < $total_med_pages): ?>
                        <?php if ($med_end_page < $total_med_pages - 1): ?>
                            <span class="min-h-9.5 min-w-9.5 flex justify-center items-center border border-gray-200 text-gray-800 py-2 px-3 text-sm">...</span>
                        <?php endif; ?>
                        <a href="?med_page=<?php echo $total_med_pages; ?><?php echo $selected_date ? '&filter_date=' . urlencode($selected_date) : ''; ?>" class="min-h-9.5 min-w-9.5 flex justify-center items-center border border-gray-200 text-gray-800 hover:bg-gray-100 py-2 px-3 text-sm first:rounded-s-lg last:rounded-e-lg focus:outline-hidden focus:bg-gray-100"><?php echo $total_med_pages; ?></a>
                    <?php endif; ?>
                    <!-- Next Button -->
                    <?php if ($med_page < $total_med_pages): ?>
                        <a href="?med_page=<?php echo $med_page + 1; ?><?php echo $selected_date ? '&filter_date=' . urlencode($selected_date) : ''; ?>" class="min-h-9.5 min-w-9.5 py-2 px-2.5 inline-flex justify-center items-center gap-x-1.5 text-sm first:rounded-s-lg last:rounded-e-lg border border-gray-200 text-gray-800 hover:bg-gray-100 focus:outline-hidden focus:bg-gray-100" aria-label="Next">
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

<!-- Prescription Details Modal -->
<div id="prescriptionModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-lg w-full max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center p-6 border-b">
                <h3 id="modalTitle" class="text-lg font-semibold text-gray-800">Medication Details</h3>
                <button id="closePrescriptionModalTop" type="button" class="size-8 inline-flex justify-center items-center gap-x-2 rounded-full border border-transparent bg-gray-100 text-gray-800 hover:bg-gray-200" aria-label="Close">
                    <span class="sr-only">Close</span>
                    <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M18 6 6 18"></path>
                        <path d="m6 6 12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="p-6" id="modalBody">
                <!-- Details will be populated here -->
            </div>
            <div class="flex justify-end p-6 border-t bg-gray-50">
                <button id="closePrescriptionModalBottom" type="button" class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-white text-gray-800 hover:bg-gray-50">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>
<script>
function viewPrescriptionDetails(prescription) {
    // Set modal title
    document.getElementById('modalTitle').textContent = prescription.patient_name || 'Medication Details';
    // Build modal body like Issue Medication History
    let html = '';
    html += `<div class="grid grid-cols-[120px_1fr] gap-3 items-center">
        <label class="text-sm font-medium text-gray-700">Date:</label>
        <p class="text-sm text-gray-900">${prescription.prescription_date || 'N/A'}</p>
    </div>`;
    html += `<div class="grid grid-cols-[120px_1fr] gap-3 items-center">
        <label class="text-sm font-medium text-gray-700">Reason:</label>
        <p class="text-sm text-gray-900">${prescription.reason || 'N/A'}</p>
    </div>`;
    // Medicines
    try {
        const medicines = typeof prescription.medicines === 'string' ? JSON.parse(prescription.medicines) : prescription.medicines;
        if (Array.isArray(medicines) && medicines.length > 0) {
            medicines.forEach(med => {
                html += `<div class=\"grid grid-cols-[120px_1fr] gap-3 items-center\">`;
                html += `<label class=\"text-sm font-medium text-gray-700\">Medicine:</label>`;
                html += `<p class=\"text-sm text-gray-900\">${med.medicine || med.name || 'N/A'}</p>`;
                html += `</div>`;
                html += `<div class=\"grid grid-cols-[120px_1fr] gap-3 items-center\">`;
                html += `<label class=\"text-sm font-medium text-gray-700\">Quantity:</label>`;
                html += `<p class=\"text-sm text-gray-900\">${med.quantity || 'N/A'}</p>`;
                html += `</div>`;
                html += `<div class=\"grid grid-cols-[120px_1fr] gap-3 items-center\">`;
                html += `<label class=\"text-sm font-medium text-gray-700\">Dosage:</label>`;
                html += `<p class=\"text-sm text-gray-900\">${med.dosage || 'N/A'}</p>`;
                html += `</div>`;
                html += `<div class=\"grid grid-cols-[120px_1fr] gap-3 items-center\">`;
                html += `<label class=\"text-sm font-medium text-gray-700\">Frequency:</label>`;
                html += `<p class=\"text-sm text-gray-900\">${med.frequency || 'N/A'}</p>`;
                html += `</div>`;
                html += `<div class=\"grid grid-cols-[120px_1fr] gap-3 items-start\">`;
                html += `<label class=\"text-sm font-medium text-gray-700\">Instructions:</label>`;
                html += `<p class=\"text-sm text-gray-900\">${med.instructions || 'No instructions provided'}</p>`;
                html += `</div>`;
            });
        } else {
            html += `<div class=\"grid grid-cols-[120px_1fr] gap-3 items-center\"><label class=\"text-sm font-medium text-gray-700\">Medicine:</label><p class=\"text-sm text-gray-900\">No medicines prescribed</p></div>`;
        }
    } catch (error) {
        html += `<div class=\"grid grid-cols-[120px_1fr] gap-3 items-center\"><label class=\"text-sm font-medium text-red-700\">Error:</label><p class=\"text-sm text-red-700\">Error loading medicine data</p></div>`;
    }
    document.getElementById('modalBody').innerHTML = html;
    document.getElementById('prescriptionModal').classList.remove('hidden');
}
function closePrescriptionModal() {
    document.getElementById('prescriptionModal').classList.add('hidden');
}
document.getElementById('closePrescriptionModalTop').addEventListener('click', closePrescriptionModal);
document.getElementById('closePrescriptionModalBottom').addEventListener('click', closePrescriptionModal);
document.getElementById('prescriptionModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closePrescriptionModal();
    }
});
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closePrescriptionModal();
    }
});
</script>

<?php
include '../includep/footer.php';
?>