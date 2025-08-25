<?php
// profile_cancel_appointment.php
header('Content-Type: application/json');
require_once '../includes/db_connect.php'; // Use your DB connection or adjust as needed

$data = json_decode(file_get_contents('php://input'), true);
if (!isset($_SESSION)) session_start();
$student_id = $_SESSION['student_row_id'] ?? null;
$date = $data['date'] ?? null;
$time = $data['time'] ?? null;
$reason = $data['reason'] ?? null;

if (!$student_id || !$date || !$time || !$reason) {
    echo json_encode(['success' => false, 'error' => 'Missing data.']);
    exit;
}

$conn = new mysqli('localhost', 'root', '', 'clinic_management_system');
if ($conn->connect_errno) {
    echo json_encode(['success' => false, 'error' => 'DB error.']);
    exit;
}
// Update the appointment status to cancelled (only if currently pending)
$stmt = $conn->prepare('UPDATE appointments SET status = "cancelled" WHERE student_id = ? AND date = ? AND time = ? AND reason = ? AND status = "pending" LIMIT 1');
$stmt->bind_param('isss', $student_id, $date, $time, $reason);
$stmt->execute();
$success = $stmt->affected_rows > 0;
$stmt->close();
$conn->close();
echo json_encode(['success' => $success]);
