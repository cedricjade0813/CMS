<?php
header('Content-Type: application/json');
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'])) {
    $email = trim($_POST['email']);
    if (!$email) {
        echo json_encode(['success' => false, 'message' => 'Email is required.']);
        exit;
    }
    try {
        $db = new PDO('mysql:host=localhost;dbname=clinic_management_system;charset=utf8', 'root', '');
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stmt = $db->prepare('SELECT id, username FROM users WHERE email = ?');
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user) {
            // Generate a reset token
            $token = bin2hex(random_bytes(32));
            $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
            // Store token in DB (create table if not exists)
            $db->exec("CREATE TABLE IF NOT EXISTS password_resets (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT,
                email VARCHAR(255),
                token VARCHAR(64),
                expires_at DATETIME,
                used TINYINT(1) DEFAULT 0
            )");
            $db->prepare('INSERT INTO password_resets (user_id, email, token, expires_at) VALUES (?, ?, ?, ?)')
                ->execute([$user['id'], $email, $token, $expires]);
            // Send email (simple PHP mail, replace with SMTP in production)
            $resetLink = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']) . "/reset_password.php?token=$token";
            $subject = 'Password Reset Request';
            $message = "Hello,\n\nA password reset was requested for your account. Click the link below to reset your password:\n$resetLink\n\nIf you did not request this, please ignore this email.";
            $headers = 'From: noreply@' . $_SERVER['HTTP_HOST'];
            if (mail($email, $subject, $message, $headers)) {
                echo json_encode(['success' => true]);
            } else {
                // Log the reset link to a file for local/dev testing
                $logFile = __DIR__ . '/reset_links.log';
                $logMsg = date('Y-m-d H:i:s') . " | $email | $resetLink\n";
                file_put_contents($logFile, $logMsg, FILE_APPEND);
                echo json_encode([
                    'success' => false,
                    'message' => 'Failed to send email. The reset link has been logged for testing.',
                    'reset_link' => $resetLink
                ]);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'No account found with that email.']);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Server error.']);
    }
    exit;
}
echo json_encode(['success' => false, 'message' => 'Invalid request.']);
