
<?php
session_start();
header('Content-Type: application/json');

try {
    $db = new PDO('mysql:host=localhost;dbname=clinic_management_system;charset=utf8', 'root', '');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    $entity_type = $_POST['entity_type'] ?? '';
    $subjective = $_POST['subjective'] ?? '';
    $objective = $_POST['objective'] ?? '';
    $assessment = $_POST['assessment'] ?? '';
    $plan = $_POST['plan'] ?? '';
    $intervention = $_POST['intervention'] ?? '';
    $evaluation = $_POST['evaluation'] ?? '';
    $referral_to = $_POST['referral_to'] ?? '';
    $recorded_by = $_SESSION['username'] ?? 'Staff';

    if ($entity_type === 'faculty' || !empty($_POST['faculty_id'])) {
        $faculty_id = $_POST['faculty_id'] ?? '';
        $faculty_name = $_POST['faculty_name'] ?? '';
        if (empty($faculty_id) || empty($faculty_name)) {
            throw new Exception('Faculty ID and name are required');
        }
        $stmt = $db->prepare("INSERT INTO medication_referrals (faculty_id, faculty_name, subjective, objective, assessment, plan, intervention, evaluation, referral_to, recorded_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $faculty_id,
            $faculty_name,
            $subjective,
            $objective,
            $assessment,
            $plan,
            $intervention,
            $evaluation,
            $referral_to,
            $recorded_by
        ]);
    } else if ($entity_type === 'visitor' || !empty($_POST['visitor_id'])) {
        $visitor_id = $_POST['visitor_id'] ?? '';
        $visitor_name = $_POST['visitor_name'] ?? '';
        if (empty($visitor_id) || empty($visitor_name)) {
            throw new Exception('Visitor ID and name are required');
        }
        $stmt = $db->prepare("INSERT INTO medication_referrals (visitor_id, visitor_name, subjective, objective, assessment, plan, intervention, evaluation, referral_to, recorded_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $visitor_id,
            $visitor_name,
            $subjective,
            $objective,
            $assessment,
            $plan,
            $intervention,
            $evaluation,
            $referral_to,
            $recorded_by
        ]);
    } else {
        $patient_id = $_POST['patient_id'] ?? '';
        $patient_name = $_POST['patient_name'] ?? '';
        if (empty($patient_id) || empty($patient_name)) {
            throw new Exception('Patient ID and name are required');
        }
        $stmt = $db->prepare("INSERT INTO medication_referrals (patient_id, patient_name, subjective, objective, assessment, plan, intervention, evaluation, referral_to, recorded_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $patient_id,
            $patient_name,
            $subjective,
            $objective,
            $assessment,
            $plan,
            $intervention,
            $evaluation,
            $referral_to,
            $recorded_by
        ]);
    }

    echo json_encode(['success' => true, 'message' => 'Medication referral saved successfully']);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
