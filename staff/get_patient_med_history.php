<?php
// get_patient_med_history.php
header('Content-Type: application/json');
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['patient_name'])) {
    $db = new PDO('mysql:host=localhost;dbname=clinic_management_system;charset=utf8', 'root', '');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $stmt = $db->prepare('SELECT prescription_date, medicines FROM prescriptions WHERE patient_name = ? ORDER BY prescription_date DESC LIMIT 10');
    $stmt->execute([$_POST['patient_name']]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $history = [];
    foreach ($rows as $row) {
        $date = $row['prescription_date'];
        $meds = json_decode($row['medicines'], true);
        if (is_array($meds)) {
            foreach ($meds as $med) {
                $history[] = [
                    'prescription_date' => $date,
                    'medicine' => $med['medicine'] ?? '',
                    'dosage' => $med['dosage'] ?? '',
                    'quantity' => $med['quantity'] ?? ''
                ];
            }
        }
    }
    echo json_encode($history);
    exit;
}
echo json_encode([]);
