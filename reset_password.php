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
    $confirm_password = $_POST['confirm_password'] ?? '';
    if ($password !== $confirm_password) {
        $error = 'Passwords do not match.';
        // Ensure we still render the form instead of the standalone error block
        try {
        $db = new PDO('mysql:host=localhost;dbname=clinic_management_system;charset=utf8', 'root', '');
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stmt = $db->prepare('SELECT * FROM password_resets WHERE token = ? AND used = 0 AND expires_at > NOW()');
        $stmt->execute([$token]);
        $reset = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($reset) {
            $valid = true;
        } else {
            $valid = false;
            $error = 'Invalid or expired token.';
        }
        } catch (Exception $e) {
        $valid = false;
        $error = 'Server error.';
        }
    } else {
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
        <script src="https://cdn.tailwindcss.com/3.4.16"></script>
        <script>
            tailwind.config = {
                theme: {
                    extend: {
                        colors: {
                            primary: '#4F46E5',
                            secondary: '#60A5FA',
                            green: {
                                600: '#22c55e',
                                700: '#16a34a'
                            }
                        },
                        borderRadius: {
                            'none': '0px',
                            'sm': '4px',
                            DEFAULT: '8px',
                            'md': '12px',
                            'lg': '16px',
                            'xl': '20px',
                            '2xl': '24px',
                            '3xl': '32px',
                            'full': '9999px',
                            'button': '8px'
                        }
                    }
                }
            }
        </script>
        <?php include_once 'includes/modal_system.php'; ?>
</head>
<body class="bg-gray-50 flex items-center justify-center min-h-screen">
        <!-- Reset Password Modal (Login Modal Style) -->
        <div id="resetPasswordModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
          <div class="bg-white rounded-lg w-full max-w-md mx-4 overflow-hidden">
            <div class="flex justify-between items-center p-6 border-b">
              <h2 class="text-2xl font-semibold text-gray-900">Reset Password</h2>
              <button id="closeResetPasswordModal" class="w-10 h-10 flex items-center justify-center text-gray-500 hover:text-gray-700">
                <i class="ri-close-line ri-lg"></i>
              </button>
            </div>
            <?php if (!empty($success)): ?>
              <div class="p-8 text-center">
                <div class="mx-auto mb-4 w-14 h-14 rounded-full bg-green-100 flex items-center justify-center">
                  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-8 h-8 text-green-600"><path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12Zm13.36-2.59a.75.75 0 1 0-1.22-.86l-3.63 5.16-1.87-1.87a.75.75 0 1 0-1.06 1.06l2.5 2.5c.32.32.84.27 1.09-.11l5.19-7.88Z" clip-rule="evenodd"/></svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900">Password changed successfully</h3>
                <p class="text-gray-600 mt-2">You can now sign in with your new password.</p>
                <div class="mt-6 flex items-center justify-center gap-3">
                  <a href="index.php#login" class="px-5 py-2.5 bg-primary text-white rounded-button font-semibold hover:bg-primary/90 transition">Continue to Login</a>
                  <a href="index.php" class="px-5 py-2.5 border border-gray-300 text-gray-700 rounded-button hover:bg-gray-50 transition">Back to Home</a>
                </div>
                <div class="mt-6 text-xs text-gray-500">Tip: For better security, avoid reusing passwords and enable 2FA if available.</div>
              </div>
            <?php elseif ($valid): ?>
              <form class="p-6 space-y-6" method="POST" id="resetPasswordForm">
                <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
                <div>
                  <label for="new_password" class="block text-sm font-medium text-gray-700 mb-2">New Password</label>
                  <div class="relative">
                    <input type="password" id="new_password" name="password" required class="block w-full pl-3 pr-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary" placeholder="Enter new password">
                  </div>
                </div>
                <div>
                  <label for="confirm_new_password" class="block text-sm font-medium text-gray-700 mb-2">Confirm New Password</label>
                  <div class="relative">
                    <input type="password" id="confirm_new_password" name="confirm_password" required class="block w-full pl-3 pr-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary" placeholder="Confirm new password">
                  </div>
                  <?php if (!empty($error) && $error === 'Passwords do not match.'): ?>
                    <div class="mt-1 text-xs text-red-600 animate-fade-in text-left">Passwords do not match.</div>
                  <?php endif; ?>
                </div>
                <?php if (!empty($error) && $error !== 'Passwords do not match.'): ?>
                  <div class="-mt-4 text-xs text-red-600 animate-fade-in text-left"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>
                <button type="submit" class="w-full bg-primary text-white py-2 !rounded-button font-medium hover:bg-opacity-90 transition-colors">Reset Password</button>
              </form>
            <?php else: ?>
              <div class="p-6">
                <div class="text-xs text-red-600 mt-1 animate-fade-in text-center"><?php echo htmlspecialchars($error ?? 'Invalid or expired link.'); ?></div>
              </div>
            <?php endif; ?>
          </div>
        </div>
</body>
</html>
