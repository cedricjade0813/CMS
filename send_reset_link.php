<?php
header('Content-Type: application/json');
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'])) {
    $email = trim($_POST['email']);
    $logFile = __DIR__ . '/reset_links.log';
    if (!$email) {
        file_put_contents($logFile, date('Y-m-d H:i:s') . " | ERROR: Email is empty\n", FILE_APPEND);
        echo json_encode(['success' => false, 'message' => 'Email is required.']);
        exit;
    }
    try {
        $db = new PDO('mysql:host=localhost;dbname=clinic_management_system;charset=utf8', 'root', '');
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        file_put_contents($logFile, date('Y-m-d H:i:s') . " | DB CONNECT SUCCESS\n", FILE_APPEND);
        $stmt = $db->prepare('SELECT id, username FROM users WHERE email = ?');
        $stmt->execute([$email]);
        file_put_contents($logFile, date('Y-m-d H:i:s') . " | QUERY EXECUTED for email: $email\n", FILE_APPEND);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user) {
            file_put_contents($logFile, date('Y-m-d H:i:s') . " | USER FOUND: " . json_encode($user) . "\n", FILE_APPEND);
            $token = bin2hex(random_bytes(32));
            $expires = date('Y-m-d H:i:s', strtotime('+100 years'));
            $db->exec("CREATE TABLE IF NOT EXISTS password_resets (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT,
                email VARCHAR(225),
                token VARCHAR(64),
                expires_at DATETIME,
                used TINYINT(1) DEFAULT 0
            )");
            file_put_contents($logFile, date('Y-m-d H:i:s') . " | PASSWORD_RESET TABLE CHECKED\n", FILE_APPEND);
            $db->prepare('INSERT INTO password_resets (user_id, email, token, expires_at) VALUES (?, ?, ?, ?)')
                ->execute([$user['id'], $email, $token, $expires]);
            file_put_contents($logFile, date('Y-m-d H:i:s') . " | PASSWORD_RESET INSERTED\n", FILE_APPEND);
            $resetLink = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']) . "/reset_password.php?token=$token";

            // PHPMailer setup
            require __DIR__ . '/phpmailer/src/PHPMailer.php';
            require __DIR__ . '/phpmailer/src/SMTP.php';
            require __DIR__ . '/phpmailer/src/Exception.php';
            $mail = new PHPMailer\PHPMailer\PHPMailer(true);
            try {
                // SMTP config (update with your real SMTP settings)
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com'; // e.g. smtp.gmail.com
                $mail->SMTPAuth = true;
                $mail->Username = 'cedricjade13@gmail.com'; // your SMTP username
                $mail->Password = 'brkegvmjmefjqlza'; // your SMTP password or app password
                $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

                $mail->setFrom('cedricjade13@gmail.com', 'Clinic Management');
                $mail->addAddress($email);
                $mail->Subject = 'Password Reset Request';
                $mail->Body = "Hello,\n\nA password reset was requested for your account. Click the link below to reset your password:\n$resetLink\n\nIf you did not request this, please ignore this email.";

                $mail->send();
                file_put_contents($logFile, date('Y-m-d H:i:s') . " | MAIL SENT (PHPMailer)\n", FILE_APPEND);
                echo json_encode([
                    'success' => true,
                    'reset_link' => $resetLink
                ]);
            } catch (Exception $e) {
                $logMsg = date('Y-m-d H:i:s') . " | $email | $resetLink\n";
                file_put_contents($logFile, $logMsg, FILE_APPEND);
                file_put_contents($logFile, date('Y-m-d H:i:s') . " | MAIL FAILED (PHPMailer): " . $mail->ErrorInfo . "\n", FILE_APPEND);
                echo json_encode([
                    'success' => false,
                    'message' => 'Failed to send email. The reset link has been logged for testing.',
                    'reset_link' => $resetLink
                ]);
            }
        } else {
            file_put_contents($logFile, date('Y-m-d H:i:s') . " | ERROR: No account found with that email: $email\n", FILE_APPEND);
            echo json_encode(['success' => false, 'message' => 'No account found with that email.']);
        }
    } catch (Exception $e) {
        file_put_contents($logFile, date('Y-m-d H:i:s') . " | ERROR: " . $e->getMessage() . "\n", FILE_APPEND);
        echo json_encode(['success' => false, 'message' => 'Server error.']);
    }
    exit;
}
echo json_encode(['success' => false, 'message' => 'Invalid request.']);
