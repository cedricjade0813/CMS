<?php
include '../includes/header.php';

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
    
    // Fetch all patients for dropdown
    $patients = $db->query('SELECT id, name, student_id FROM imported_patients ORDER BY name ASC')->fetchAll(PDO::FETCH_ASSOC);
    
    // Get current staff member info
    $staff_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;
    $staff_name = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Staff';
    
    // Fetch messages sent by current staff member (grouped by subject and date)
    $sent_messages_query = "
        SELECT 
            subject,
            message,
            created_at,
            COUNT(*) as recipient_count,
            GROUP_CONCAT(recipient_name SEPARATOR ', ') as recipients,
            SUM(CASE WHEN is_read = 1 THEN 1 ELSE 0 END) as read_count
        FROM messages 
        WHERE sender_id = ? 
        GROUP BY subject, DATE(created_at)
        ORDER BY created_at DESC 
        LIMIT 20
    ";
    $sent_messages = $db->prepare($sent_messages_query);
    $sent_messages->execute([$staff_id]);
    $messages = $sent_messages->fetchAll(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    die('Database connection failed: ' . $e->getMessage());
}

// Handle message sending
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_message'])) {
    $recipient_type = $_POST['recipient_type'];
    $subject = trim($_POST['subject']);
    $message_text = trim($_POST['message']);
    
    if ($subject && $message_text) {
        try {
            $sender_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;
            $sender_name = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Staff';
            $sender_role = isset($_SESSION['role']) ? $_SESSION['role'] : 'staff';
            
            if ($recipient_type === 'all') {
                // Send to all patients
                $inserted = 0;
                foreach ($patients as $patient) {
                    $stmt = $db->prepare('INSERT INTO messages (sender_id, sender_name, sender_role, recipient_id, recipient_name, subject, message) VALUES (?, ?, ?, ?, ?, ?, ?)');
                    $stmt->execute([$sender_id, $sender_name, $sender_role, $patient['id'], $patient['name'], $subject, $message_text]);
                    $inserted++;
                }
                $success_message = "Message sent successfully to all " . $inserted . " patients";
            } else {
                // Send to specific patient
                $recipient_id = $_POST['recipient_id'];
                $recipient_stmt = $db->prepare('SELECT name FROM imported_patients WHERE id = ?');
                $recipient_stmt->execute([$recipient_id]);
                $recipient = $recipient_stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($recipient) {
                    $insert_stmt = $db->prepare('INSERT INTO messages (sender_id, sender_name, sender_role, recipient_id, recipient_name, subject, message) VALUES (?, ?, ?, ?, ?, ?, ?)');
                    $insert_stmt->execute([$sender_id, $sender_name, $sender_role, $recipient_id, $recipient['name'], $subject, $message_text]);
                    $success_message = "Message sent successfully to " . $recipient['name'];
                }
            }
            
            // Refresh messages list
            $sent_messages = $db->prepare($sent_messages_query);
            $sent_messages->execute([$staff_id]);
            $messages = $sent_messages->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            $error_message = "Failed to send message: " . $e->getMessage();
        }
    } else {
        $error_message = "Please fill in all fields.";
    }
}
?>
<main class="flex-1 overflow-y-auto bg-gray-50 p-6 ml-16 md:ml-64 mt-[56px]">

        <h2 class="text-2xl font-bold text-gray-800 mb-6">Send Messages to Patients</h2>
        
        <?php if (isset($success_message)): ?>
            <?php showSuccessModal(htmlspecialchars($success_message), 'Message Sent'); ?>
        <?php endif; ?>
        
        <?php if (isset($error_message)): ?>
            <?php showErrorModal(htmlspecialchars($error_message), 'Error'); ?>
        <?php endif; ?>
        
        <!-- Message Form -->
        <div class="bg-white rounded shadow p-6 mb-8">
            <form method="post" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Send to:</label>
                    <div class="space-y-2">
                        <label class="flex items-center">
                            <input type="radio" name="recipient_type" value="all" class="mr-2" checked>
                            <span class="text-sm">All Patients (<?php echo count($patients); ?> patients)</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" name="recipient_type" value="specific" class="mr-2">
                            <span class="text-sm">Specific Patient</span>
                        </label>
                    </div>
                </div>
                
                <div id="specificPatientDiv" class="hidden">
                    <label for="recipient_id" class="block text-sm font-medium text-gray-700 mb-1">Select Patient</label>
                    <select id="recipient_id" name="recipient_id" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-primary focus:border-primary">
                        <option value="">Select a patient...</option>
                        <?php foreach ($patients as $patient): ?>
                            <option value="<?php echo $patient['id']; ?>">
                                <?php echo htmlspecialchars($patient['name'] . ' (' . $patient['student_id'] . ')'); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div>
                    <label for="subject" class="block text-sm font-medium text-gray-700 mb-1">Subject</label>
                    <input type="text" id="subject" name="subject" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-primary focus:border-primary" placeholder="Enter message subject..." required>
                </div>
                <div>
                    <label for="message" class="block text-sm font-medium text-gray-700 mb-1">Message</label>
                    <textarea id="message" name="message" rows="4" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-primary focus:border-primary" placeholder="Type your message here..." required></textarea>
                </div>
                <div class="flex justify-end">
                    <button type="submit" name="send_message" class="bg-primary text-white px-5 py-2 rounded hover:bg-primary/90 font-semibold flex items-center gap-2">
                        <i class="ri-send-plane-2-line"></i> Send Message
                    </button>
                </div>
            </form>
        </div>
        
        <!-- Previous Messages Sent by Current Staff -->
        <div class="bg-white rounded shadow p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Previous Messages Sent by <?php echo htmlspecialchars($staff_name); ?></h3>
            <?php if (empty($messages)): ?>
                <p class="text-gray-500 text-center py-4">No messages sent yet.</p>
            <?php else: ?>
                <ul class="divide-y divide-gray-200 text-sm">
                    <?php foreach ($messages as $index => $msg): ?>
                        <li class="py-4">
                            <div class="message-header cursor-pointer hover:bg-gray-50 p-3 rounded transition-colors" onclick="showMessageModal(<?php echo $index; ?>)">
                    <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded">
                                            <?php echo $msg['recipient_count']; ?> recipient<?php echo $msg['recipient_count'] > 1 ? 's' : ''; ?>
                                        </span>
                                        <span class="text-xs <?php echo $msg['read_count'] > 0 ? 'text-green-600' : 'text-yellow-600'; ?>">
                                            <?php echo $msg['read_count']; ?> read
                                        </span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs text-gray-400"><?php echo date('M j, Y g:i A', strtotime($msg['created_at'])); ?></span>
                                        <i class="ri-eye-line text-gray-400"></i>
                                    </div>
                    </div>
                                <div class="font-medium text-gray-800 mt-2"><?php echo htmlspecialchars($msg['subject']); ?></div>
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
                <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded" id="modalRecipients"></span>
                <span class="text-xs text-green-600" id="modalReadCount"></span>
                <span class="text-xs text-gray-400" id="modalDate"></span>
            </div>
            <div class="text-gray-600 mb-4" id="modalMessage"></div>
            <div class="text-xs text-gray-500" id="modalRecipientList"></div>
        </div>
    </div>
</div>

<script>
// Toggle specific patient selection based on radio button
document.addEventListener('DOMContentLoaded', function() {
    const recipientTypeRadios = document.querySelectorAll('input[name="recipient_type"]');
    const specificPatientDiv = document.getElementById('specificPatientDiv');
    const recipientSelect = document.getElementById('recipient_id');
    
    recipientTypeRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            if (this.value === 'specific') {
                specificPatientDiv.classList.remove('hidden');
                recipientSelect.required = true;
            } else {
                specificPatientDiv.classList.add('hidden');
                recipientSelect.required = false;
                recipientSelect.value = '';
            }
        });
    });
});

// Message data for modal
const messageData = <?php echo json_encode($messages); ?>;

// Show message modal
function showMessageModal(index) {
    const msg = messageData[index];
    const modal = document.getElementById('messageModal');
    
    // Populate modal content
    document.getElementById('modalSubject').textContent = msg.subject;
    document.getElementById('modalRecipients').textContent = msg.recipient_count + ' recipient' + (msg.recipient_count > 1 ? 's' : '');
    document.getElementById('modalReadCount').textContent = msg.read_count + ' read';
    document.getElementById('modalDate').textContent = new Date(msg.created_at).toLocaleString();
    document.getElementById('modalMessage').innerHTML = msg.message.replace(/\n/g, '<br>');
    
    if (msg.recipient_count <= 5) {
        document.getElementById('modalRecipientList').innerHTML = '<strong>Sent to:</strong> ' + msg.recipients;
    } else {
        document.getElementById('modalRecipientList').innerHTML = '<strong>Sent to:</strong> ' + msg.recipient_count + ' patients';
    }
    
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
