<?php
include '../includep/header.php';

// Database connection
try {
    $db = new PDO('mysql:host=localhost;dbname=clinic_management_system;charset=utf8', 'root', '');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create messages table if not exists
    $db->exec("CREATE TABLE IF NOT EXISTS messages (
        id INT AUTO_INCREMENT PRIMARY KEY,
        sender_id INT NOT NULL,
        sender_name VARCHAR(255) NOT NULL,
        sender_role VARCHAR(50) NOT NULL,
        recipient_id INT NOT NULL,
        recipient_name VARCHAR(255) NOT NULL,
        subject VARCHAR(255) NOT NULL,
        message TEXT NOT NULL,
        is_read BOOLEAN DEFAULT FALSE,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_recipient (recipient_id),
        INDEX idx_sender (sender_id),
        INDEX idx_created_at (created_at)
    )");
    
    // Get patient ID from session
    $patient_id = isset($_SESSION['student_row_id']) ? $_SESSION['student_row_id'] : null;
    
    if ($patient_id) {
        // Fetch messages for this patient
        $stmt = $db->prepare('SELECT * FROM messages WHERE recipient_id = ? ORDER BY created_at DESC');
        $stmt->execute([$patient_id]);
        $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Mark messages as read when viewed
        if (!empty($messages)) {
            $update_stmt = $db->prepare('UPDATE messages SET is_read = TRUE WHERE recipient_id = ? AND is_read = FALSE');
            $update_stmt->execute([$patient_id]);
        }
    } else {
        $messages = [];
    }
    
} catch (PDOException $e) {
    $messages = [];
    $error_message = "Database connection failed: " . $e->getMessage();
}
?>
<main class="flex-1 overflow-y-auto bg-gray-50 p-6 ml-16 md:ml-64 mt-[56px]">
        <h2 class="text-2xl font-bold mb-6 text-gray-800">Inbox</h2>
        
        <?php if (isset($error_message)): ?>
            <?php showErrorModal(htmlspecialchars($error_message), 'Error'); ?>
        <?php endif; ?>
        
        <div class="bg-white rounded shadow p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Messages from Staff</h3>
            
            <?php if (empty($messages)): ?>
                <div class="text-center py-8">
                    <div class="text-gray-400 mb-2">
                        <i class="ri-mail-line text-4xl"></i>
                    </div>
                    <p class="text-gray-500">No messages yet</p>
                    <p class="text-sm text-gray-400 mt-1">Messages from clinic staff will appear here</p>
                </div>
            <?php else: ?>
                <ul class="divide-y divide-gray-200 text-sm">
                    <?php foreach ($messages as $index => $msg): ?>
                        <li class="py-4">
                            <div class="message-header cursor-pointer hover:bg-gray-50 p-3 rounded transition-colors" onclick="showMessageModal(<?php echo $index; ?>)">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2 mb-1">
                                            <span class="font-medium text-gray-700"><?php echo htmlspecialchars($msg['sender_name']); ?></span>
                                            <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded"><?php echo ucfirst(htmlspecialchars($msg['sender_role'])); ?></span>
                                            <?php if (!$msg['is_read']): ?>
                                                <span class="text-xs bg-red-100 text-red-800 px-2 py-1 rounded">New</span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="font-medium text-gray-800"><?php echo htmlspecialchars($msg['subject']); ?></div>
                                        <div class="text-xs text-gray-400 mt-1">
                                            <?php echo date('M j, Y g:i A', strtotime($msg['created_at'])); ?>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <i class="ri-eye-line text-gray-400"></i>
                                    </div>
                                </div>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
</main>

<!-- Message Modal -->
<div id="messageModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-[80vh] overflow-y-auto">
        <div class="flex items-center justify-between p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800" id="modalSubject"></h3>
            <button onclick="closeMessageModal()" class="text-gray-400 hover:text-gray-600">
                <i class="ri-close-line text-xl"></i>
            </button>
        </div>
        <div class="p-6">
            <div class="flex items-center gap-2 mb-4">
                <span class="font-medium text-gray-700" id="modalSender"></span>
                <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded" id="modalRole"></span>
                <span class="text-xs text-gray-400" id="modalDate"></span>
            </div>
            <div class="text-gray-600 mb-4" id="modalMessage"></div>
        </div>
    </div>
</div>

<script>
// Message data for modal
const messageData = <?php echo json_encode($messages); ?>;

// Show message modal
function showMessageModal(index) {
    const msg = messageData[index];
    const modal = document.getElementById('messageModal');
    
    // Populate modal content
    document.getElementById('modalSubject').textContent = msg.subject;
    document.getElementById('modalSender').textContent = msg.sender_name;
    document.getElementById('modalRole').textContent = msg.sender_role.charAt(0).toUpperCase() + msg.sender_role.slice(1);
    document.getElementById('modalDate').textContent = new Date(msg.created_at).toLocaleString();
    document.getElementById('modalMessage').innerHTML = msg.message.replace(/\n/g, '<br>');
    
    // Show modal
    modal.classList.remove('hidden');
    
    // Prevent body scroll
    document.body.style.overflow = 'hidden';
}

// Close message modal
function closeMessageModal() {
    const modal = document.getElementById('messageModal');
    modal.classList.add('hidden');
    
    // Restore body scroll
    document.body.style.overflow = 'auto';
}

// Close modal when clicking outside
document.getElementById('messageModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeMessageModal();
    }
});

// Close modal with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeMessageModal();
    }
});
</script>
<?php
include '../includep/footer.php';
?>
