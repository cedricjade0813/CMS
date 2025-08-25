<?php
// Password reset page
if (!isset($_GET['token'])) {
    die('Invalid or missing token.');
}
$token = $_GET['token'];
$valid = false;
$user_id = null;
$email = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['password'], $_POST['token'])) {
    $token = $_POST['token'];
    $password = $_POST['password'];
    try {
        $db = new PDO('mysql:host=localhost;dbname=clinic_management_system;charset=utf8', 'root', '');
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stmt = $db->prepare('SELECT * FROM password_resets WHERE token = ? AND used = 0 AND expires_at > NOW()');
        $stmt->execute([$token]);
        $reset = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($reset) {
            $user_id = $reset['user_id'];
            $email = $reset['email'];
            // Update user password
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $db->prepare('UPDATE users SET password = ? WHERE id = ?')->execute([$password_hash, $user_id]);
            // Mark token as used
            $db->prepare('UPDATE password_resets SET used = 1 WHERE id = ?')->execute([$reset['id']]);
            $success = true;
        } else {
            $error = 'Invalid or expired token.';
        }
    } catch (Exception $e) {
        $error = 'Server error.';
    }
}
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        $db = new PDO('mysql:host=localhost;dbname=clinic_management_system;charset=utf8', 'root', '');
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stmt = $db->prepare('SELECT * FROM password_resets WHERE token = ? AND used = 0 AND expires_at > NOW()');
        $stmt->execute([$token]);
        $reset = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($reset) {
            $valid = true;
        } else {
            $error = 'Invalid or expired token.';
        }
    } catch (Exception $e) {
        $error = 'Server error.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link rel="stylesheet" href="https://cdn.tailwindcss.com/3.4.16/tailwind.min.css">
    <?php include_once 'includes/modal_system.php'; ?>
</head>
<body class="bg-gray-50 flex items-center justify-center min-h-screen">
    <div class="bg-white rounded-lg shadow-lg p-8 w-full max-w-md">
        <h2 class="text-2xl font-bold mb-6 text-center">Reset Password</h2>
        <?php if (!empty($success)): ?>
            <?php showSuccessModal('Your password has been reset successfully.', 'Password Reset'); ?>
            <div class="text-center mb-4"><a href="index.php" class="text-primary underline">Login</a></div>
        <?php elseif ($valid): ?>
            <form method="POST" class="space-y-6">
                <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">New Password</label>
                    <input type="password" name="password" required class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary">
                </div>
                <button type="submit" class="w-full bg-primary text-white py-2 rounded font-medium hover:bg-primary/90 transition-colors">Reset Password</button>
            </form>
        <?php else: ?>
            <?php showErrorModal($error ?? 'Invalid or expired link.', 'Error'); ?>
        <?php endif; ?>
    </div>
    <?php includeModalSystem(); ?>
</body>
</html>
