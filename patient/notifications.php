<?php
include '../includep/header.php';
$student_id = $_SESSION['student_row_id'];
$conn = new mysqli('localhost', 'root', '', 'clinic_management_system');
$notifications = [];
if (!$conn->connect_errno) {
    $stmt = $conn->prepare('SELECT id, message, is_read, created_at FROM notifications WHERE student_id = ? ORDER BY created_at DESC');
    $stmt->bind_param('i', $student_id);
    $stmt->execute();
    $stmt->bind_result($id, $message, $is_read, $created_at);
    while ($stmt->fetch()) {
        $notifications[] = ['id' => $id, 'message' => $message, 'is_read' => $is_read, 'created_at' => $created_at];
    }
    $stmt->close();
}
$conn->close();
?>

<main class="flex-1 overflow-y-auto bg-gray-50 p-6 ml-16 md:ml-64 mt-[56px]">
    <h2 class="text-2xl font-bold mb-6 text-gray-800">Notifications</h2>
    <div class="bg-white rounded shadow p-6">
        <h3 class="text-lg font-semibold mb-4">Notification Feed</h3>
        <ul class="divide-y divide-gray-200">
            <?php foreach ($notifications as $notif): ?>
            <li class="flex items-start gap-4 py-4<?= $notif['is_read'] ? ' opacity-50' : '' ?>">
                <div class="flex-1">
                    <div class="text-gray-800"><?= $notif['message'] ?></div>
                    <div class="text-xs text-gray-400 mt-1"><?= htmlspecialchars($notif['created_at']) ?></div>
                </div>
            </li>
            <?php endforeach; ?>
            <?php if (empty($notifications)): ?>
            <li class="py-4 text-gray-500">No notifications found.</li>
            <?php endif; ?>
        </ul>
    </div>
</main>

<?php
include '../includep/footer.php';
?>