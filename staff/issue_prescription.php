<?php
header('Content-Type: application/json');
try {
    $db = new PDO('mysql:host=localhost;dbname=clinic_management_system;charset=utf8', 'root', '');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $ids = isset($_POST['ids']) ? json_decode($_POST['ids'], true) : [];
        if (!$ids || !is_array($ids)) {
            echo json_encode(['success' => false, 'message' => 'No prescription IDs provided.']);
            exit;
        }
        foreach ($ids as $id) {
            // Fetch pending prescription
            $stmt = $db->prepare('SELECT * FROM pending_prescriptions WHERE id=?');
            $stmt->execute([$id]);
            $pending = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($pending) {
                // Insert into prescriptions table
                $ins = $db->prepare('INSERT INTO prescriptions (patient_id, patient_name, prescribed_by, prescription_date, medicines, notes, patient_email, parent_email) VALUES (?, ?, ?, NOW(), ?, ?, ?, ?)');
                $ins->execute([
                    $pending['patient_id'],
                    $pending['patient_name'],
                    $pending['prescribed_by'],
                    $pending['medicines'],
                    $pending['notes'],
                    $pending['patient_email'],
                    $pending['parent_email']
                ]);
                // Log action
                session_start();
                $user_email = isset($_SESSION['user_email']) ? $_SESSION['user_email'] : $pending['prescribed_by'];
                $logDb = new PDO('mysql:host=localhost;dbname=clinic_management_system;charset=utf8', 'root', '');
                $logDb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $logDb->prepare('CREATE TABLE IF NOT EXISTS logs (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    user_id INT,
                    user_email VARCHAR(255),
                    action VARCHAR(255),
                    timestamp DATETIME DEFAULT CURRENT_TIMESTAMP
                )')->execute();
                $logDb->prepare('INSERT INTO logs (user_email, action) VALUES (?, ?)')->execute([
                    $user_email,
                    'Issued prescription for patient: ' . $pending['patient_name']
                ]);
                // Deduct from inventory
                $meds = json_decode($pending['medicines'], true);
                if (is_array($meds)) {
                    foreach ($meds as $med) {
                        $medName = $med['medicine'] ?? '';
                        $qty = (int)($med['quantity'] ?? 0);
                        if ($medName && $qty > 0) {
                            $upd = $db->prepare('UPDATE medicines SET quantity = GREATEST(quantity - ?, 0) WHERE name = ?');
                            $upd->execute([$qty, $medName]);
                        }
                    }
                }
                // Remove from pending_prescriptions
                $del = $db->prepare('DELETE FROM pending_prescriptions WHERE id=?');
                $del->execute([$id]);
            }
        }
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid request.']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
