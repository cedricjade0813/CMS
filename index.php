<?php
session_start();
// LOGIN LOGIC: Only users in users table can log in, with role-based redirect
$login_error = '';
$username_val = '';
$password_val = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_POST['username'], $_POST['password'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $username_val = htmlspecialchars($username);
    $password_val = htmlspecialchars($password);
    try {
      $db = new PDO('mysql:host=localhost;dbname=clinic_management_system;charset=utf8', 'root', '');
      $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      $stmt = $db->prepare('SELECT * FROM users WHERE username = ?');
      $stmt->execute([$username]);
      $user = $stmt->fetch(PDO::FETCH_ASSOC);
      if ($user) {
        $role = strtolower($user['role']);
        if (password_verify($password, $user['password'])) {
          session_start();
          $_SESSION['user_id'] = $user['id'];
          $_SESSION['username'] = $user['username'];
          $_SESSION['role'] = $user['role'];
          // Log the login event
          try {
            $logDb = new PDO('mysql:host=localhost;dbname=clinic_management_system;charset=utf8', 'root', '');
            $logDb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $logStmt = $logDb->prepare('CREATE TABLE IF NOT EXISTS logs (
                            id INT AUTO_INCREMENT PRIMARY KEY,
                            user_id INT,
                            user_email VARCHAR(255),
                            action VARCHAR(255),
                            timestamp DATETIME DEFAULT CURRENT_TIMESTAMP
                        )');
            $logStmt->execute();
            $logInsert = $logDb->prepare('INSERT INTO logs (user_id, user_email, action) VALUES (?, ?, ?)');
            $logInsert->execute([$user['id'], $user['username'], 'Logged in']);
          } catch (PDOException $e) {
            // Optionally handle log DB error silently
          }
          if ($role === 'admin') {
            header('Location: admin/dashboard.php');
            exit;
          } elseif ($role === 'doctor' || $role === 'nurse' || $role === 'doctor/nurse') {
            header('Location: staff/dashboard.php');
            exit;
          } else {
            $login_error = 'Access denied: Only admin, doctor, or nurse can log in here.';
          }
        } else {
          $login_error = 'incorrect_password';
        }
      } else {
        // Try imported_patients table (student login) only if user not found in users table
        $importDb = new PDO('mysql:host=localhost;dbname=clinic_management_system;charset=utf8', 'root', '');
        $importDb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stmt2 = $importDb->prepare('SELECT * FROM imported_patients WHERE student_id = ?');
        $stmt2->execute([$username]);
        $student = $stmt2->fetch(PDO::FETCH_ASSOC);
        if ($student) {
          if ($student['password'] === $password) {
            // Step 1: Store pending login and show DOB form
            session_start();
            $_SESSION['pending_patient_id'] = $student['id'];
            $_SESSION['pending_student_id'] = $student['student_id'];
            $_SESSION['pending_patient_name'] = $student['name'];
            header('Location: index.php?dobstep=1');
            exit;
          } else {
            $login_error = 'incorrect_password';
          }
        } else {
          $login_error = 'invalid_username';
        }
      }
    } catch (PDOException $e) {
      $login_error = 'Database error.';
    }
  } elseif (isset($_POST['ajax_dob_check']) && isset($_SESSION['pending_patient_id'])) {
    // Handle AJAX DOB check
    session_start();
    $dob = trim($_POST['dob']);
    $pending_id = $_SESSION['pending_patient_id'];
    
    try {
      $db = new PDO('mysql:host=localhost;dbname=clinic_management_system;charset=utf8', 'root', '');
      $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      $stmt = $db->prepare('SELECT dob FROM imported_patients WHERE id = ?');
      $stmt->execute([$pending_id]);
      $row = $stmt->fetch(PDO::FETCH_ASSOC);
      
      if ($row && trim($row['dob']) === $dob) {
        // Login success - set session variables
        $_SESSION['user_id'] = $pending_id;
        $_SESSION['username'] = $_SESSION['pending_student_id'];
        $_SESSION['role'] = 'student';
        $_SESSION['student_row_id'] = $pending_id;
        unset($_SESSION['pending_patient_id'], $_SESSION['pending_student_id'], $_SESSION['pending_patient_name']);
        
        // Return success response
        header('Content-Type: application/json');
        echo json_encode(['success' => true]);
        exit;
      } else {
        // Return error response
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'error' => 'Incorrect date of birth.']);
        exit;
      }
    } catch (PDOException $e) {
      header('Content-Type: application/json');
      echo json_encode(['success' => false, 'error' => 'Database error.']);
      exit;
    }
  } elseif (isset($_POST['dob']) && isset($_SESSION['pending_patient_id'])) {
    // Handle DOB step (Step 2)
    $dob = trim($_POST['dob']);
    $pending_id = $_SESSION['pending_patient_id'];
    try {
      $db = new PDO('mysql:host=localhost;dbname=clinic_management_system;charset=utf8', 'root', '');
      $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      $stmt = $db->prepare('SELECT dob FROM imported_patients WHERE id = ?');
      $stmt->execute([$pending_id]);
      $row = $stmt->fetch(PDO::FETCH_ASSOC);
      if ($row && trim($row['dob']) === $dob) {
        // Login success
        session_start();
        $_SESSION['user_id'] = $pending_id;
        $_SESSION['username'] = $_SESSION['pending_student_id'];
        $_SESSION['role'] = 'student';
        $_SESSION['student_row_id'] = $pending_id;
        unset($_SESSION['pending_patient_id'], $_SESSION['pending_student_id'], $_SESSION['pending_patient_name']);
        header('Location: patient/profile.php');
        exit;
      } else {
        $login_error = 'Incorrect date of birth.';
      } 
    } catch (PDOException $e) {
      $login_error = 'Database error.';
    }
  }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>MediCare - Advanced Clinic Management System</title>
  <script src="https://cdn.tailwindcss.com/3.4.16"></script>
  <script>tailwind.config = { theme: { extend: { colors: { primary: '#4F46E5', secondary: '#60A5FA' }, borderRadius: { 'none': '0px', 'sm': '4px', DEFAULT: '8px', 'md': '12px', 'lg': '16px', 'xl': '20px', '2xl': '24px', '3xl': '32px', 'full': '9999px', 'button': '8px' } } } }</script>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.6.0/remixicon.min.css">
  <style>
    :where([class^="ri-"])::before {
      content: "\f3c2";
    }

    /* Hide scrollbars while maintaining scroll functionality */
    html, body {
      scrollbar-width: none; /* Firefox */
      -ms-overflow-style: none; /* Internet Explorer 10+ */
      font-family: 'Inter', sans-serif;
    }

    html::-webkit-scrollbar,
    body::-webkit-scrollbar {
      display: none; /* Safari and Chrome */
    }

    .stat-counter {
      display: inline-block;
    }

    .nav-link {
      position: relative;
    }

    .nav-link::after {
      content: '';
      position: absolute;
      width: 0;
      height: 2px;
      bottom: -4px;
      left: 0;
      background-color: #4F46E5;
      transition: width 0.3s;
    }

    .nav-link:hover::after {
      width: 100%;
    }

    .custom-checkbox {
      appearance: none;
      -webkit-appearance: none;
      width: 20px;
      height: 20px;
      border: 2px solid #d1d5db;
      border-radius: 4px;
      outline: none;
      transition: all 0.2s;
      position: relative;
      cursor: pointer;
    }

    .custom-checkbox:checked {
      background-color: #4F46E5;
      border-color: #4F46E5;
    }

    .custom-checkbox:checked::after {
      content: '';
      position: absolute;
      top: 2px;
      left: 6px;
      width: 5px;
      height: 10px;
      border: solid white;
      border-width: 0 2px 2px 0;
      transform: rotate(45deg);
    }

    @keyframes fade-in {
      from { opacity: 0; transform: translateY(-4px); }
      to { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in {
      animation: fade-in 0.3s ease;
    }
  </style>
</head>

<body class="bg-white">
  <!-- Header Section -->
  <header class="w-full bg-white shadow-sm fixed top-0 left-0 right-0 z-50">
    <div class="container mx-auto px-6 py-4 flex items-center justify-between">
      <div class="flex items-center">
        <a href="index.php" class="mr-12 block" style="width:64px;height:64px;">
          <img src="logo.jpg" alt="St. Cecilia's College Logo"
            class="h-16 w-16 object-contain rounded-full border border-gray-200 bg-white shadow"
            onerror="this.onerror=null;this.src='data:image/svg+xml;utf8,<svg xmlns=\'http://www.w3.org/2000/svg\' width=\'64\' height=\'64\'><rect width=\'100%\' height=\'100%\' fill=\'%23f3f4f6\'/><text x=\'50%\' y=\'50%\' font-size=\'12\' fill=\'%23999\' text-anchor=\'middle\' alignment-baseline=\'middle\'>Logo?</text></svg>';this.style.background='#f3f4f6';this.style.border='2px dashed #f87171';" />
          <!-- If logo.jpg does not display, check for case sensitivity, file permissions, or file corruption. -->
        </a>
        <nav class="hidden md:flex space-x-8">
          <a href="index.php" class="nav-link text-gray-800 font-medium hover:text-primary transition-colors">Home</a>
          <a href="#features" class="nav-link text-gray-800 font-medium hover:text-primary transition-colors">Features</a>
          <a href="#roles" class="nav-link text-gray-800 font-medium hover:text-primary transition-colors">Roles</a>
          <a href="#contact" class="nav-link text-gray-800 font-medium hover:text-primary transition-colors">Contact</a>
        </nav>
      </div>
      <div class="flex items-center space-x-4">
        <a href="#loginModal" id="loginBtn" class="text-gray-700 hover:text-primary font-medium whitespace-nowrap">Login</a>
        <button class="md:hidden w-10 h-10 flex items-center justify-center text-gray-700">
          <i class="ri-menu-line ri-lg"></i>
        </button>
      </div>
    </div>
  </header>
  <!-- Hero Section -->
  <section class="w-full pt-28 pb-16 md:py-32 relative overflow-hidden">
    <div id="heroBg" class="absolute inset-0">
      <div id="heroBg1" class="absolute inset-0 bg-cover bg-center transition-opacity duration-1000 ease-in-out"></div>
      <div id="heroBg2" class="absolute inset-0 bg-cover bg-center transition-opacity duration-1000 ease-in-out opacity-0"></div>
      <div class="absolute inset-0 bg-gradient-to-r from-white via-white/80 to-white/20"></div>
    </div>
    <div class="container mx-auto px-6 relative z-10">
      <div class="max-w-xl">
        <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4 leading-tight">Clinic Management System</h1>
        <p class="text-lg text-gray-700 mb-8">A modern platform for managing appointments, patient records, inventory, and more for clinics and schools. Empowering <span class="text-primary font-semibold">Admins</span>, <span class="text-primary font-semibold">Doctors/Nurses</span>, and <span class="text-primary font-semibold">Students</span>.</p>
        <div class="flex flex-col sm:flex-row gap-4 mb-8">
          <a href="#features" class="bg-primary text-white px-6 py-3 !rounded-button font-medium hover:bg-opacity-90 transition-colors text-center whitespace-nowrap">Explore Features</a>
          <a href="#roles" class="bg-white text-primary border border-primary px-6 py-3 !rounded-button font-medium hover:bg-gray-50 transition-colors text-center whitespace-nowrap">See User Roles</a>
        </div>
        <p class="text-sm text-gray-600">St. Cecilia's College Clinic Management System</p>
      </div>
    </div>
  </section>
  <!-- Roles Section -->
  <section id="roles" class="py-12 bg-white">
    <div class="container mx-auto px-6">
      <div class="text-center mb-10">
        <h2 class="text-3xl font-bold text-gray-900 mb-4">Who Can Use This System?</h2>
        <p class="text-gray-600 max-w-2xl mx-auto">Designed for all clinic stakeholders. Each role has a dedicated dashboard and features.</p>
      </div>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <div class="bg-gray-50 rounded-lg shadow p-6 flex flex-col items-center">
          <i class="ri-shield-user-line text-4xl text-primary mb-2"></i>
          <h3 class="text-xl font-semibold text-primary mb-1">Admin</h3>
          <p class="text-gray-600 text-center mb-2">Manage users, view reports, oversee all clinic operations.</p>
          <a href="#loginModal" class="text-primary font-medium hover:underline" onclick="document.getElementById('loginModal').classList.remove('hidden');document.body.style.overflow='hidden';return false;">Go to Admin Dashboard</a>
        </div>
        <div class="bg-gray-50 rounded-lg shadow p-6 flex flex-col items-center">
          <i class="ri-stethoscope-line text-4xl text-primary mb-2"></i>
          <h3 class="text-xl font-semibold text-primary mb-1">Doctor/Nurse</h3>
          <p class="text-gray-600 text-center mb-2">View appointments, manage patient records, issue prescriptions, and monitor inventory.</p>
          <a href="#loginModal" class="text-primary font-medium hover:underline" onclick="document.getElementById('loginModal').classList.remove('hidden');document.body.style.overflow='hidden';return false;">Go to Staff Dashboard</a>
        </div>
        <div class="bg-gray-50 rounded-lg shadow p-6 flex flex-col items-center">
          <i class="ri-user-3-line text-4xl text-primary mb-2"></i>
          <h3 class="text-xl font-semibold text-primary mb-1">Student</h3>
          <p class="text-gray-600 text-center mb-2">Book appointments, view your medical history, and receive notifications.</p>
          <a href="#loginModal" class="text-primary font-medium hover:underline" onclick="document.getElementById('loginModal').classList.remove('hidden');document.body.style.overflow='hidden';return false;">Go to Student Portal</a>
        </div>
      </div>
    </div>
  </section>
  <!-- Features Section -->
  <section id="features" class="py-16 bg-gray-50">
    <div class="container mx-auto px-6">
      <div class="text-center mb-12">
        <h2 class="text-3xl font-bold text-gray-900 mb-4">Comprehensive Clinic Management Features</h2>
        <p class="text-gray-600 max-w-2xl mx-auto">Our platform offers everything you need to run your clinic efficiently and provide exceptional care.</p>
      </div>
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
        <div class="bg-white p-6 rounded-lg shadow-sm hover:shadow-md transition-shadow duration-300">
          <div class="w-14 h-14 bg-primary/10 rounded-full flex items-center justify-center mb-4 text-primary">
            <i class="ri-calendar-check-line ri-xl"></i>
          </div>
          <h3 class="text-xl font-semibold text-gray-900 mb-2">Online Appointments</h3>
          <p class="text-gray-600">Book and manage appointments 24/7 with real-time availability.</p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow-sm hover:shadow-md transition-shadow duration-300">
          <div class="w-14 h-14 bg-primary/10 rounded-full flex items-center justify-center mb-4 text-primary">
            <i class="ri-folder-user-line ri-xl"></i>
          </div>
          <h3 class="text-xl font-semibold text-gray-900 mb-2">Patient Records</h3>
          <p class="text-gray-600">Secure electronic health records with complete patient history and visit notes.</p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow-sm hover:shadow-md transition-shadow duration-300">
          <div class="w-14 h-14 bg-primary/10 rounded-full flex items-center justify-center mb-4 text-primary">
            <i class="ri-medicine-bottle-line ri-xl"></i>
          </div>
          <h3 class="text-xl font-semibold text-gray-900 mb-2">Prescription Management</h3>
          <p class="text-gray-600">Digital prescription system with medication tracking and refill management.</p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow-sm hover:shadow-md transition-shadow duration-300">
          <div class="w-14 h-14 bg-primary/10 rounded-full flex items-center justify-center mb-4 text-primary">
            <i class="ri-bar-chart-2-line ri-xl"></i>
          </div>
          <h3 class="text-xl font-semibold text-gray-900 mb-2">Reports & Analytics</h3>
          <p class="text-gray-600">Generate reports and gain insights to improve clinic performance.</p>
        </div>
      </div>
    </div>
  </section>
  <!-- Contact Section -->
  <section id="contact" class="py-16 bg-white">
    <div class="container mx-auto px-6">
      <div class="max-w-2xl mx-auto text-center">
        <h2 class="text-3xl font-bold text-gray-900 mb-4">Contact Us</h2>
        <p class="text-gray-600 mb-8">Have questions or need support? Reach out to our team.</p>
        <div class="flex flex-col md:flex-row justify-center gap-8">
          <div class="flex flex-col items-center">
            <i class="ri-map-pin-line text-primary text-2xl mb-2"></i>
            <span class="text-gray-700">St. Cecilia's College Cebu, Minglanilla</span>
          </div>
          <div class="flex flex-col items-center">
            <i class="ri-phone-line text-primary text-2xl mb-2"></i>
            <a href="tel:09166764802" class="text-gray-700 hover:text-primary">09166764802</a>
          </div>
          <div class="flex flex-col items-center">
            <i class="ri-mail-line text-primary text-2xl mb-2"></i>
            <a href="mailto:cms@medicare.com" class="text-gray-700 hover:text-primary">cms@medicare.com</a>
          </div>
        </div>
      </div>
    </div>
  </section>
  <!-- Footer -->
  <footer class="bg-gray-900 text-white pt-10 pb-6 mt-12">
    <div class="container mx-auto px-6">
      <div class="flex flex-col md:flex-row justify-between items-center">
        <div class="mb-4 md:mb-0">
          <span class="font-['Pacifico'] text-2xl text-white">SSCMS</span>
          <span class="text-gray-400 ml-4">© 2025 Clinic Management System. All rights reserved.</span>
        </div>
        <div class="flex space-x-6">
          <a href="#" class="text-gray-400 hover:text-white text-sm transition-colors">Privacy Policy</a>
          <a href="#" class="text-gray-400 hover:text-white text-sm transition-colors">Terms of Service</a>
        </div>
      </div>
    </div>
  </footer>
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      // Mobile menu toggle
      const menuButton = document.querySelector('button.md\\:hidden');
      const mobileMenu = document.createElement('div');
      mobileMenu.className = 'fixed inset-0 bg-white z-50 transform translate-x-full transition-transform duration-300 md:hidden';
      mobileMenu.innerHTML = `
<div class="flex justify-between items-center p-6 border-b">
<a href="#" class="text-2xl font-['Pacifico'] text-primary">logo</a>
<button class="w-10 h-10 flex items-center justify-center text-gray-700">
<i class="ri-close-line ri-lg"></i>
</button>
</div>
<nav class="p-6 space-y-6">
<a href="#" class="block text-gray-800 font-medium hover:text-primary transition-colors py-2">Home</a>
<a href="#" class="block text-gray-800 font-medium hover:text-primary transition-colors py-2">Services</a>
<a href="#" class="block text-gray-800 font-medium hover:text-primary transition-colors py-2">Appointments</a>
<a href="#" class="block text-gray-800 font-medium hover:text-primary transition-colors py-2">Patient Portal</a>
<a href="#" class="block text-gray-800 font-medium hover:text-primary transition-colors py-2">Contact</a>
<div class="pt-4 border-t">
<a href="#" class="block text-gray-700 hover:text-primary font-medium py-2">Login / Register</a>
<a href="#" class="block bg-primary text-white px-5 py-2.5 !rounded-button font-medium hover:bg-opacity-90 transition-colors text-center mt-4">Book Appointment</a>
</div>
</nav>
`;
      document.body.appendChild(mobileMenu);
      menuButton.addEventListener('click', function () {
        mobileMenu.classList.remove('translate-x-full');
      });
      mobileMenu.querySelector('button').addEventListener('click', function () {
        mobileMenu.classList.add('translate-x-full');
      });
    });
  </script>
  <!-- Login Modal -->
  <div id="loginModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center hidden">
    <div class="bg-white rounded-lg w-full max-w-md mx-4 overflow-hidden">
      <div class="flex justify-between items-center p-6 border-b">
        <h2 class="text-2xl font-semibold text-gray-900">Login to Your Account</h2>
        <button id="closeLoginModal"
          class="w-10 h-10 flex items-center justify-center text-gray-500 hover:text-gray-700">
          <i class="ri-close-line ri-lg"></i>
        </button>
      </div>
      <form class="p-6 space-y-6" method="POST" action="" id="loginForm">
        <div>
          <label for="username" class="block text-sm font-medium text-gray-700 mb-2">Username</label>
          <div class="relative">
            <input type="text" id="username" name="username" required
              class="block w-full pl-3 pr-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary"
              placeholder="Enter your username" value="<?php echo $username_val; ?>">
            <?php if ($login_error === 'invalid_username'): ?>
              <div class="absolute left-0 right-0 mt-1 text-xs text-red-600 animate-fade-in">
                The account you’ve entered does not exist.
              </div>
            <?php endif; ?>
          </div>
        </div>
        <div>
          <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password</label>
          <div class="relative">
            <input type="password" id="password" name="password" required
              class="block w-full pl-3 pr-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary"
              placeholder="Enter your password" value="<?php echo $password_val; ?>">
            <?php if ($login_error === 'incorrect_password'): ?>
              <div class="absolute left-0 right-0 mt-1 text-xs text-red-600 animate-fade-in">
                The password you’ve entered is incorrect.
              </div>
            <?php endif; ?>
          </div>
          
        </div>
        <div class="flex items-center justify-between">
          <div class="flex items-center">
            <label class="flex items-center text-sm text-gray-600">
              <input type="checkbox" id="showPassword" class="mr-2 rounded border-gray-300 text-primary focus:ring-primary focus:ring-offset-0">
              Show password
            </label>
          </div>
          <a href="#" id="forgotPasswordLink" class="text-sm text-primary hover:text-opacity-80">Forgot password?</a>
        </div>
        <button type="submit"
          class="w-full bg-primary text-white py-2 !rounded-button font-medium hover:bg-opacity-90 transition-colors">Login</button>
      </form>
      <div class="px-6 pb-6 text-center">
        <p class="text-sm text-gray-600">
          Don't have an account?
          <a href="#" class="text-primary hover:text-opacity-80 font-medium">Sign up</a>
        </p>
      </div>
    </div>
  </div>
  <!-- Forgot Password Modal -->
  <div id="forgotPasswordModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center hidden">
    <div class="bg-white rounded-lg w-full max-w-md mx-4 overflow-hidden">
      <div class="flex justify-between items-center p-6 border-b">
        <h2 class="text-2xl font-semibold text-gray-900">Forgot Password</h2>
        <button id="closeForgotPasswordModal" class="w-10 h-10 flex items-center justify-center text-gray-500 hover:text-gray-700">
          <i class="ri-close-line ri-lg"></i>
        </button>
      </div>
      <form class="p-6 space-y-6" id="forgotPasswordForm">
        <div>
          <label for="forgot_email" class="block text-sm font-medium text-gray-700 mb-2">Enter your email address</label>
          <input type="email" id="forgot_email" name="forgot_email" required class="block w-full pl-3 pr-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary" placeholder="you@email.com">
          <div id="forgotEmailError" class="text-xs text-red-600 mt-1 hidden"></div>
        </div>
        <button type="submit" class="w-full bg-primary text-white py-2 !rounded-button font-medium hover:bg-opacity-90 transition-colors">Send Reset Link</button>
        <div id="forgotSuccessMsg" class="text-xs text-green-600 mt-2 hidden"></div>
      </form>
    </div>
  </div>
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const loginBtn = document.getElementById('loginBtn');
      const loginModal = document.getElementById('loginModal');
      const closeLoginModal = document.getElementById('closeLoginModal');
      const forgotPasswordLink = document.getElementById('forgotPasswordLink');
      const forgotPasswordModal = document.getElementById('forgotPasswordModal');
      const closeForgotPasswordModal = document.getElementById('closeForgotPasswordModal');
      const forgotPasswordForm = document.getElementById('forgotPasswordForm');
      const forgotEmailError = document.getElementById('forgotEmailError');
      const forgotSuccessMsg = document.getElementById('forgotSuccessMsg');

      // Show modal only if loginBtn is clicked
      if (loginBtn && loginModal) {
        loginBtn.addEventListener('click', function () {
          loginModal.classList.remove('hidden');
          document.body.style.overflow = 'hidden';
        });
      }
      if (closeLoginModal && loginModal) {
        closeLoginModal.addEventListener('click', function () {
          loginModal.classList.add('hidden');
          document.body.style.overflow = '';
        });
        loginModal.addEventListener('click', function (e) {
          if (e.target === loginModal) {
            loginModal.classList.add('hidden');
            document.body.style.overflow = '';
          }
        });
      }
      if (forgotPasswordLink && forgotPasswordModal) {
        forgotPasswordLink.addEventListener('click', function (e) {
          e.preventDefault();
          forgotPasswordModal.classList.remove('hidden');
          document.body.style.overflow = 'hidden';
        });
      }
      if (closeForgotPasswordModal && forgotPasswordModal) {
        closeForgotPasswordModal.addEventListener('click', function () {
          forgotPasswordModal.classList.add('hidden');
          document.body.style.overflow = '';
        });
        forgotPasswordModal.addEventListener('click', function (e) {
          if (e.target === forgotPasswordModal) {
            forgotPasswordModal.classList.add('hidden');
            document.body.style.overflow = '';
          }
        });
      }
      if (forgotPasswordForm) {
        forgotPasswordForm.addEventListener('submit', async function (e) {
          e.preventDefault();
          forgotEmailError.classList.add('hidden');
          forgotSuccessMsg.classList.add('hidden');
          const email = document.getElementById('forgot_email').value.trim();
          if (!email) {
            forgotEmailError.textContent = 'Email is required.';
            forgotEmailError.classList.remove('hidden');
            return;
          }
          // AJAX to backend for password reset (to be implemented)
          const res = await fetch('send_reset_link.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'email=' + encodeURIComponent(email)
          });
          let data;
          try {
            data = await res.json();
          } catch (err) {
            forgotEmailError.textContent = 'Server error. Please try again later.';
            forgotEmailError.classList.remove('hidden');
            return;
          }
          if (data.success) {
            forgotSuccessMsg.textContent = 'A password reset link has been sent to your email.';
            forgotSuccessMsg.classList.remove('hidden');
            // If reset_link is present (local/dev), show it for testing
            if (data.reset_link) {
              forgotSuccessMsg.innerHTML += '<br><span class="text-xs text-gray-500">(For testing: <a href="' + data.reset_link + '" target="_blank" class="underline text-primary">Open reset link</a>)</span>';
            }
          } else {
            forgotEmailError.textContent = data.message || 'No account found with that email.';
            forgotEmailError.classList.remove('hidden');
            // If reset_link is present (local/dev), show it for testing
            if (data.reset_link) {
              forgotEmailError.innerHTML += '<br><span class="text-xs text-gray-500">(For testing: <a href="' + data.reset_link + '" target="_blank" class="underline text-primary">Open reset link</a>)</span>';
            }
          }
        });
      }
      // If there is a login error (but not DOB error), show the modal automatically
      <?php if (!empty($login_error) && !isset($_GET['dobstep'])): ?>
        loginModal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
      <?php endif; ?>

      // Check for hash to auto-open login modal
      if (window.location.hash === '#login') {
        loginModal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        // Remove the hash from URL after opening modal
        history.replaceState(null, null, ' ');
      }
    });
  </script>
  <!-- Render DOB form if needed -->
  <?php if (isset($_GET['dobstep']) && isset($_SESSION['pending_patient_id'])): ?>
    <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
      <div class="bg-white rounded-lg w-full max-w-md mx-4 overflow-hidden">
        <div class="flex justify-between items-center p-6 border-b">
          <h2 class="text-2xl font-semibold text-gray-900">Security Question</h2>
        </div>
        <form class="p-6 space-y-6" method="POST" action="">
          <div>
            <label for="dob" class="block text-sm font-medium text-gray-700 mb-2">What is your date of birth?</label>
            <div class="relative">
              <input type="text" id="dob" name="dob" required placeholder="MM/DD/YYYY" class="block w-full pl-3 pr-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary">
              <?php if (!empty($login_error)): ?>
                <div class="absolute left-0 right-0 mt-1 text-xs text-red-600 animate-fade-in">
                  <?php echo htmlspecialchars($login_error); ?>
                </div>
              <?php endif; ?>
            </div>
          </div>
          <div class="pt-4">
            <div class="flex gap-3">
              <button type="button" onclick="window.location.href='index.php#login'" class="flex-1 bg-gray-200 text-gray-800 py-2 !rounded-button font-medium hover:bg-gray-300 transition-colors text-center">Cancel</button>
              <button type="submit" class="flex-1 bg-primary text-white py-2 !rounded-button font-medium hover:bg-opacity-90 transition-colors">Continue</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  <?php endif; ?>
  <script>
    // Background images for hero section with smooth transitions
const heroImages = [
  'scc3.png',
  'scc1.png',
  'scc2.png',
  'scc4.png'
];
let heroBgIdx = 0;
let isTransitioning = false;

function setHeroBg() {
  const heroBg1 = document.getElementById('heroBg1');
  const heroBg2 = document.getElementById('heroBg2');
  
  if (heroBg1 && heroBg2 && !isTransitioning) {
    isTransitioning = true;
    
    // Set the new image on the hidden background
    const currentImage = heroImages[heroBgIdx];
    const hiddenBg = heroBg1.style.opacity === '0' ? heroBg1 : heroBg2;
    const visibleBg = hiddenBg === heroBg1 ? heroBg2 : heroBg1;
    
    hiddenBg.style.backgroundImage = `url('${currentImage}')`;
    
    // Crossfade effect
    setTimeout(() => {
      hiddenBg.style.opacity = '1';
      visibleBg.style.opacity = '0';
      
      // Reset transition flag after animation completes
      setTimeout(() => {
        isTransitioning = false;
      }, 1000);
    }, 50);
  }
}

// Initialize first image
function initHeroBg() {
  const heroBg1 = document.getElementById('heroBg1');
  if (heroBg1) {
    heroBg1.style.backgroundImage = `url('${heroImages[0]}')`;
    heroBg1.style.opacity = '1';
  }
}

initHeroBg();
setInterval(() => {
  heroBgIdx = (heroBgIdx + 1) % heroImages.length;
  setHeroBg();
}, 3000); // Increased interval to allow for transition

// Password toggle functionality
document.addEventListener('DOMContentLoaded', function() {
  const showPasswordCheckbox = document.getElementById('showPassword');
  const passwordInput = document.getElementById('password');

  if (showPasswordCheckbox && passwordInput) {
    showPasswordCheckbox.addEventListener('change', function() {
      // Toggle the type attribute based on checkbox state
      const type = this.checked ? 'text' : 'password';
      passwordInput.setAttribute('type', type);
    });
  }
});

// Password toggle functionality
document.addEventListener('DOMContentLoaded', function() {
  const showPasswordCheckbox = document.getElementById('showPassword');
  const passwordInput = document.getElementById('password');

  if (showPasswordCheckbox && passwordInput) {
    showPasswordCheckbox.addEventListener('change', function() {
      // Toggle the type attribute based on checkbox state
      const type = this.checked ? 'text' : 'password';
      passwordInput.setAttribute('type', type);
    });
  }
});
  </script>
</body>

</html>