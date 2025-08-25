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
?>
<main class="flex-1 overflow-y-auto bg-gray-50 p-6 ml-16 md:ml-64 mt-[56px]">
        <h2 class="text-2xl font-bold mb-6 text-gray-800">Medical History</h2>
        <!-- Filters -->
        <div class="flex flex-wrap gap-4 items-end mb-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                <input type="date" class="border border-gray-300 rounded px-3 py-2 text-sm" />
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Illness</label>
                <input type="text" class="border border-gray-300 rounded px-3 py-2 text-sm" placeholder="Search illness..." />
            </div>
        </div>
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
                            <th class="px-4 py-2 text-left font-semibold text-gray-600">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (!empty($prescriptionHistory)) {
                            foreach ($prescriptionHistory as $index => $presc) {
                                $date = htmlspecialchars($presc['prescription_date']);
                                $reason = htmlspecialchars($presc['reason'] ?? 'N/A');
                                $meds = json_decode($presc['medicines'], true);
                                if (is_array($meds)) {
                                    foreach ($meds as $medIndex => $med) {
                                        $medName = htmlspecialchars($med['medicine'] ?? '');
                                        $qty = htmlspecialchars($med['quantity'] ?? '');
                                        $prescriptionId = $index . '_' . $medIndex;
                                        echo "<tr>";
                                        echo "<td class='px-4 py-2'>" . $date . "</td>";
                                        echo "<td class='px-4 py-2'>" . $reason . "</td>";
                                        echo "<td class='px-4 py-2'>" . $medName . "</td>";
                                        echo "<td class='px-4 py-2'>" . $qty . "</td>";
                                        echo "<td class='px-4 py-2'>";
                                        echo "<button onclick='viewPrescriptionDetails(" . json_encode($presc) . ")' class='px-3 py-1 bg-blue-500 text-white text-xs rounded hover:bg-blue-600 transition-colors'>";
                                        echo "<i class='ri-eye-line mr-1'></i>View";
                                        echo "</button>";
                                        echo "</td>";
                                        echo "</tr>";
                                    }
                                }
                            }
                        } else {
                            echo "<tr><td colspan='5' class='px-4 py-2 text-center text-gray-500'>No medication history found.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
</main>

<!-- Prescription Details Modal -->
<div id="prescriptionModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-5xl w-full max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center p-6 border-b">
                <h3 class="text-lg font-semibold text-gray-800">Prescription Details</h3>
                <button onclick="closePrescriptionModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="ri-close-line text-xl"></i>
                </button>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                        <p id="modalDate" class="text-sm text-gray-900 bg-gray-50 p-2 rounded"></p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Reason/Diagnosis</label>
                        <p id="modalReason" class="text-sm text-gray-900 bg-gray-50 p-2 rounded"></p>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-3">Prescribed Medicines</label>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm border border-gray-200 rounded">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-3 py-2 text-left font-medium text-gray-600">Medicine</th>
                                    <th class="px-3 py-2 text-left font-medium text-gray-600">Dosage</th>
                                    <th class="px-3 py-2 text-left font-medium text-gray-600">Quantity</th>
                                    <th class="px-3 py-2 text-left font-medium text-gray-600">Frequency</th>
                                    <th class="px-3 py-2 text-left font-medium text-gray-600">Instructions</th>
                                </tr>
                            </thead>
                            <tbody id="modalMedicines" class="bg-white divide-y divide-gray-200">
                                <!-- Medicines will be populated here -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="flex justify-end p-6 border-t bg-gray-50">
                <button onclick="closePrescriptionModal()" class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700 transition-colors">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function viewPrescriptionDetails(prescription) {
    // Populate modal with prescription data
    document.getElementById('modalDate').textContent = prescription.prescription_date || 'N/A';
    document.getElementById('modalReason').textContent = prescription.reason || 'N/A';
    
    // Clear and populate medicines table
    const medicinesBody = document.getElementById('modalMedicines');
    medicinesBody.innerHTML = '';
    
    try {
        const medicines = typeof prescription.medicines === 'string' 
            ? JSON.parse(prescription.medicines) 
            : prescription.medicines;
        
        if (Array.isArray(medicines) && medicines.length > 0) {
            medicines.forEach(medicine => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td class="px-3 py-2 border-r">${medicine.medicine || medicine.name || 'N/A'}</td>
                    <td class="px-3 py-2 border-r">${medicine.dosage || 'N/A'}</td>
                    <td class="px-3 py-2 border-r">${medicine.quantity || 'N/A'}</td>
                    <td class="px-3 py-2 border-r">${medicine.frequency || 'N/A'}</td>
                    <td class="px-3 py-2">${medicine.instructions || 'No instructions provided'}</td>
                `;
                medicinesBody.appendChild(row);
            });
        } else {
            medicinesBody.innerHTML = '<tr><td colspan="5" class="px-3 py-2 text-center text-gray-500">No medicines prescribed</td></tr>';
        }
    } catch (error) {
        console.error('Error parsing medicines:', error);
        medicinesBody.innerHTML = '<tr><td colspan="5" class="px-3 py-2 text-center text-red-500">Error loading medicine data</td></tr>';
    }
    
    // Show modal
    document.getElementById('prescriptionModal').classList.remove('hidden');
}

function closePrescriptionModal() {
    document.getElementById('prescriptionModal').classList.add('hidden');
}

// Close modal when clicking outside
document.getElementById('prescriptionModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closePrescriptionModal();
    }
});

// Close modal with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closePrescriptionModal();
    }
});
</script>

<?php
include '../includep/footer.php';
?>