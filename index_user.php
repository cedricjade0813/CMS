<?php
// PATIENT LOGIN LOGIC: Only imported_patients can log in (student_id + last name)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['student_id'], $_POST['password']) && $_POST['student_id'] !== '' && $_POST['password'] !== '') {
        $student_id = trim($_POST['student_id']);
        $lastname = trim($_POST['password']);
        try {
            $db = new PDO('mysql:host=localhost;dbname=clinic_management_system;charset=utf8', 'root', '');
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $stmt = $db->prepare('SELECT * FROM imported_patients WHERE student_id = ?');
            $stmt->execute([$student_id]);
            $patient = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($patient) { 
                // Extract last name from name field (assume last word is last name)
                $name = trim($patient['name']);
                $nameParts = preg_split('/\s+/', $name);
                $input_lastname = strtolower(preg_replace('/[^a-z]/', '', $lastname));
                $db_lastname = strtolower(preg_replace('/[^a-z]/', '', end($nameParts)));
                if ($input_lastname === $db_lastname) {
                    session_start();
                    $_SESSION['patient_id'] = $patient['id'];
                    $_SESSION['student_id'] = $patient['student_id'];
                    $_SESSION['patient_name'] = $patient['name'];
                    header('Location: patient/profile.php');
                    exit;
                } else {
                    $login_error = 'Incorrect last name.';
                }
            } else {
                $login_error = 'Invalid school ID.';
            }
        } catch (PDOException $e) {
            $login_error = 'Database error.';
        }
    } else {
        $login_error = 'Please enter both School ID and Last Name.';
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

    body {
      font-family: 'Inter', sans-serif;
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
  </style>
</head>

<body class="bg-white">
  <!-- Header Section -->
  <header class="w-full bg-white shadow-sm fixed top-0 left-0 right-0 z-50">
    <div class="container mx-auto px-6 py-4 flex items-center justify-between">
      <div class="flex items-center">
        <a href="#" class="text-2xl font-['Pacifico'] text-primary mr-12">logo</a>
        <nav class="hidden md:flex space-x-8">
          <a href="#" class="nav-link text-gray-800 font-medium hover:text-primary transition-colors">Home</a>
          <a href="#" class="nav-link text-gray-800 font-medium hover:text-primary transition-colors">Services</a>
          <a href="#" class="nav-link text-gray-800 font-medium hover:text-primary transition-colors">Appointments</a>
          <a href="#" class="nav-link text-gray-800 font-medium hover:text-primary transition-colors">Patient Portal</a>
          <a href="#" class="nav-link text-gray-800 font-medium hover:text-primary transition-colors">Contact</a>
        </nav>
      </div>
      <div class="flex items-center space-x-4">
        <button id="loginBtn" class="text-gray-700 hover:text-primary font-medium whitespace-nowrap">Login /
          Register</button>
        <a href="#"
          class="bg-primary text-white px-5 py-2.5 !rounded-button font-medium hover:bg-opacity-90 transition-colors whitespace-nowrap flex items-center">
          <span class="w-5 h-5 flex items-center justify-center mr-2">
            <i class="ri-calendar-line"></i>
          </span>
          Book Appointment
        </a>
        <button class="md:hidden w-10 h-10 flex items-center justify-center text-gray-700">
          <i class="ri-menu-line ri-lg"></i>
        </button>
      </div>
    </div>
  </header>
  <!-- Hero Section -->
  <section class="w-full pt-28 pb-16 md:py-32 relative overflow-hidden"
    style="background-image: url('https://readdy.ai/api/search-image?query=modern%20medical%20clinic%20environment%20with%20soft%20lighting%2C%20clean%20design%2C%20medical%20professionals%20in%20white%20coats%2C%20advanced%20medical%20equipment%2C%20blue%20and%20white%20color%20scheme%2C%20professional%20atmosphere%2C%20blurred%20background%20with%20medical%20staff%20attending%20to%20patients&width=1920&height=1080&seq=1&orientation=landscape'); background-size: cover; background-position: center;">
    <div class="absolute inset-0 bg-gradient-to-r from-white via-white/90 to-white/30"></div>
    <div class="container mx-auto px-6 relative z-10">
      <div class="max-w-xl">
        <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4 leading-tight">Advanced Healthcare Management
          Solutions</h1>
        <p class="text-lg text-gray-700 mb-8">Streamline your clinic operations, enhance patient experience, and deliver
          superior healthcare with our comprehensive management system.</p>
        <div class="flex flex-col sm:flex-row gap-4 mb-8">
          <a href="#"
            class="bg-primary text-white px-6 py-3 !rounded-button font-medium hover:bg-opacity-90 transition-colors text-center whitespace-nowrap">Book
            Appointment</a>
          <a href="#"
            class="bg-white text-primary border border-primary px-6 py-3 !rounded-button font-medium hover:bg-gray-50 transition-colors text-center whitespace-nowrap">Patient
            Login</a>
        </div>
        <p class="text-sm text-gray-600">Trusted by over 500+ healthcare providers across the country</p>
      </div>
    </div>
  </section>
  <!-- Key Features Grid -->
  <section class="py-16 bg-gray-50">
    <div class="container mx-auto px-6">
      <div class="text-center mb-12">
        <h2 class="text-3xl font-bold text-gray-900 mb-4">Comprehensive Clinic Management Features</h2>
        <p class="text-gray-600 max-w-2xl mx-auto">Our platform offers everything you need to run your clinic
          efficiently and provide exceptional patient care.</p>
      </div>
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
        <!-- Feature 1 -->
        <div class="bg-white p-6 rounded-lg shadow-sm hover:shadow-md transition-shadow duration-300">
          <div class="w-14 h-14 bg-primary/10 rounded-full flex items-center justify-center mb-4 text-primary">
            <i class="ri-calendar-check-line ri-xl"></i>
          </div>
          <h3 class="text-xl font-semibold text-gray-900 mb-2">Online Appointments</h3>
          <p class="text-gray-600">Seamless scheduling system that allows patients to book appointments 24/7 with
            real-time availability.</p>
        </div>
        <!-- Feature 2 -->
        <div class="bg-white p-6 rounded-lg shadow-sm hover:shadow-md transition-shadow duration-300">
          <div class="w-14 h-14 bg-primary/10 rounded-full flex items-center justify-center mb-4 text-primary">
            <i class="ri-folder-user-line ri-xl"></i>
          </div>
          <h3 class="text-xl font-semibold text-gray-900 mb-2">Patient Records</h3>
          <p class="text-gray-600">Secure electronic health records with complete patient history, treatment plans, and
            visit notes.</p>
        </div>
        <!-- Feature 3 -->
        <div class="bg-white p-6 rounded-lg shadow-sm hover:shadow-md transition-shadow duration-300">
          <div class="w-14 h-14 bg-primary/10 rounded-full flex items-center justify-center mb-4 text-primary">
            <i class="ri-medicine-bottle-line ri-xl"></i>
          </div>
          <h3 class="text-xl font-semibold text-gray-900 mb-2">Prescription Management</h3>
          <p class="text-gray-600">Digital prescription system with medication tracking, refill management, and drug
            interaction alerts.</p>
        </div>
        <!-- Feature 4 -->
        <div class="bg-white p-6 rounded-lg shadow-sm hover:shadow-md transition-shadow duration-300">
          <div class="w-14 h-14 bg-primary/10 rounded-full flex items-center justify-center mb-4 text-primary">
            <i class="ri-test-tube-line ri-xl"></i>
          </div>
          <h3 class="text-xl font-semibold text-gray-900 mb-2">Lab Results</h3>
          <p class="text-gray-600">Integrated lab results management with automatic notifications and secure patient
            access.</p>
        </div>
      </div>
    </div>
  </section>
  <!-- Benefits Section -->
  <section class="py-16 bg-white">
    <div class="container mx-auto px-6">
      <div class="flex flex-col lg:flex-row items-center gap-12">
        <div class="w-full lg:w-1/2">
          <img
            src="https://readdy.ai/api/search-image?query=modern%20medical%20software%20interface%20displayed%20on%20a%20tablet%20and%20computer%2C%20doctor%20using%20digital%20healthcare%20system%2C%20clean%20medical%20office%20environment%2C%20professional%20healthcare%20worker%20reviewing%20patient%20data%20on%20screen%2C%20blue%20interface%20elements%2C%20high-tech%20medical%20software&width=800&height=600&seq=2&orientation=landscape"
            alt="System Interface" class="rounded-lg shadow-lg w-full object-cover object-top">
        </div>
        <div class="w-full lg:w-1/2">
          <h2 class="text-3xl font-bold text-gray-900 mb-6">Benefits for Your Practice</h2>
          <div class="space-y-5">
            <div class="flex items-start">
              <div
                class="w-10 h-10 flex items-center justify-center bg-primary/10 rounded-full text-primary mr-4 flex-shrink-0">
                <i class="ri-time-line"></i>
              </div>
              <div>
                <h3 class="text-xl font-semibold text-gray-900 mb-1">Time Efficiency</h3>
                <p class="text-gray-600">Reduce administrative workload by up to 40% with automated scheduling and
                  paperless records.</p>
              </div>
            </div>
            <div class="flex items-start">
              <div
                class="w-10 h-10 flex items-center justify-center bg-primary/10 rounded-full text-primary mr-4 flex-shrink-0">
                <i class="ri-global-line"></i>
              </div>
              <div>
                <h3 class="text-xl font-semibold text-gray-900 mb-1">Accessibility</h3>
                <p class="text-gray-600">Access patient information securely from anywhere, enabling telemedicine and
                  remote consultations.</p>
              </div>
            </div>
            <div class="flex items-start">
              <div
                class="w-10 h-10 flex items-center justify-center bg-primary/10 rounded-full text-primary mr-4 flex-shrink-0">
                <i class="ri-heart-pulse-line"></i>
              </div>
              <div>
                <h3 class="text-xl font-semibold text-gray-900 mb-1">Enhanced Patient Care</h3>
                <p class="text-gray-600">Improve treatment outcomes with comprehensive patient history and clinical
                  decision support.</p>
              </div>
            </div>
            <div class="flex items-start">
              <div
                class="w-10 h-10 flex items-center justify-center bg-primary/10 rounded-full text-primary mr-4 flex-shrink-0">
                <i class="ri-line-chart-line"></i>
              </div>
              <div>
                <h3 class="text-xl font-semibold text-gray-900 mb-1">Business Growth</h3>
                <p class="text-gray-600">Increase patient satisfaction and retention with streamlined processes and
                  reduced wait times.</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
  <!-- Statistics Section -->
  <section class="py-16 bg-gray-50">
    <div class="container mx-auto px-6">
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 text-center">
        <div class="p-6">
          <div class="text-4xl font-bold text-primary mb-2 stat-counter">25,000+</div>
          <p class="text-gray-600">Patients Served</p>
        </div>
        <div class="p-6">
          <div class="text-4xl font-bold text-primary mb-2 stat-counter">1,200+</div>
          <p class="text-gray-600">Healthcare Providers</p>
        </div>
        <div class="p-6">
          <div class="text-4xl font-bold text-primary mb-2 stat-counter">98%</div>
          <p class="text-gray-600">Satisfaction Rate</p>
        </div>
        <div class="p-6">
          <div class="text-4xl font-bold text-primary mb-2 stat-counter">12</div>
          <p class="text-gray-600">Years of Excellence</p>
        </div>
      </div>
    </div>
  </section>
  <!-- Call-to-Action Section -->
  <section class="py-16 bg-primary bg-opacity-5">
    <div class="container mx-auto px-6">
      <div class="max-w-4xl mx-auto text-center">
        <h2 class="text-3xl font-bold text-gray-900 mb-4">Ready to Transform Your Clinic Operations?</h2>
        <p class="text-lg text-gray-600 mb-8">Join thousands of healthcare providers who have streamlined their practice
          with our comprehensive clinic management system.</p>
        <div class="flex flex-col sm:flex-row justify-center gap-4 mb-8">
          <a href="#"
            class="bg-primary text-white px-8 py-3 !rounded-button font-medium hover:bg-opacity-90 transition-colors text-center whitespace-nowrap">Get
            Started Today</a>
          <a href="#"
            class="bg-white text-primary border border-primary px-8 py-3 !rounded-button font-medium hover:bg-gray-50 transition-colors text-center whitespace-nowrap">Request
            Demo</a>
        </div>
        <p class="text-gray-600">Have questions? Contact our support team at <a href="mailto:support@medicare.com"
            class="text-primary hover:underline">support@medicare.com</a> or call <a href="tel:+18005551234"
            class="text-primary hover:underline">1-800-555-1234</a></p>
      </div>
    </div>
  </section>
  <!-- Footer -->
  <footer class="bg-gray-900 text-white pt-16 pb-8">
    <div class="container mx-auto px-6">
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 mb-12">
        <!-- Column 1 -->
        <div>
          <h3 class="font-['Pacifico'] text-2xl mb-6 text-white">logo</h3>
          <p class="text-gray-400 mb-6">Advanced healthcare management solutions designed to streamline your clinic
            operations and enhance patient care.</p>
          <div class="flex space-x-4">
            <a href="#"
              class="w-10 h-10 flex items-center justify-center bg-gray-800 hover:bg-primary rounded-full transition-colors">
              <i class="ri-facebook-fill text-white"></i>
            </a>
            <a href="#"
              class="w-10 h-10 flex items-center justify-center bg-gray-800 hover:bg-primary rounded-full transition-colors">
              <i class="ri-twitter-x-fill text-white"></i>
            </a>
            <a href="#"
              class="w-10 h-10 flex items-center justify-center bg-gray-800 hover:bg-primary rounded-full transition-colors">
              <i class="ri-linkedin-fill text-white"></i>
            </a>
            <a href="#"
              class="w-10 h-10 flex items-center justify-center bg-gray-800 hover:bg-primary rounded-full transition-colors">
              <i class="ri-instagram-fill text-white"></i>
            </a>
          </div>
        </div>
        <!-- Column 2 -->
        <div>
          <h3 class="text-lg font-semibold mb-6">Quick Links</h3>
          <ul class="space-y-3">
            <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Home</a></li>
            <li><a href="#" class="text-gray-400 hover:text-white transition-colors">About Us</a></li>
            <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Services</a></li>
            <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Appointments</a></li>
            <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Patient Portal</a></li>
            <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Contact</a></li>
          </ul>
        </div>
        <!-- Column 3 -->
        <div>
          <h3 class="text-lg font-semibold mb-6">Contact Information</h3>
          <ul class="space-y-3">
            <li class="flex items-start">
              <span class="w-5 h-5 flex items-center justify-center mr-3 mt-1">
                <i class="ri-map-pin-line text-primary"></i>
              </span>
              <span class="text-gray-400">123 Medical Center Drive, Suite 500<br>Boston, MA 02115</span>
            </li>
            <li class="flex items-center">
              <span class="w-5 h-5 flex items-center justify-center mr-3">
                <i class="ri-phone-line text-primary"></i>
              </span>
              <a href="tel:+18005551234" class="text-gray-400 hover:text-white transition-colors">1-800-555-1234</a>
            </li>
            <li class="flex items-center">
              <span class="w-5 h-5 flex items-center justify-center mr-3">
                <i class="ri-mail-line text-primary"></i>
              </span>
              <a href="mailto:info@medicare.com"
                class="text-gray-400 hover:text-white transition-colors">info@medicare.com</a>
            </li>
            <li class="flex items-center">
              <span class="w-5 h-5 flex items-center justify-center mr-3">
                <i class="ri-time-line text-primary"></i>
              </span>
              <span class="text-gray-400">Mon-Fri: 8:00 AM - 6:00 PM</span>
            </li>
          </ul>
        </div>
        <!-- Column 4 -->
        <div>
          <h3 class="text-lg font-semibold mb-6">Newsletter</h3>
          <p class="text-gray-400 mb-4">Subscribe to our newsletter for the latest updates and healthcare insights.</p>
          <form class="mb-4">
            <div class="flex flex-col sm:flex-row gap-2">
              <input type="email" placeholder="Your email address"
                class="bg-gray-800 text-white px-4 py-2 rounded border-none focus:ring-2 focus:ring-primary outline-none w-full">
              <button type="submit"
                class="bg-primary text-white px-4 py-2 !rounded-button font-medium hover:bg-opacity-90 transition-colors whitespace-nowrap">Subscribe</button>
            </div>
          </form>
          <div class="flex items-center mb-4">
            <input type="checkbox" id="consent" class="custom-checkbox mr-3">
            <label for="consent" class="text-gray-400 text-sm">I agree to receive marketing emails</label>
          </div>
          <div class="flex items-center space-x-4">
            <span class="w-10 h-10 flex items-center justify-center">
              <i class="ri-visa-fill ri-lg text-gray-400"></i>
            </span>
            <span class="w-10 h-10 flex items-center justify-center">
              <i class="ri-mastercard-fill ri-lg text-gray-400"></i>
            </span>
            <span class="w-10 h-10 flex items-center justify-center">
              <i class="ri-paypal-fill ri-lg text-gray-400"></i>
            </span>
            <span class="w-10 h-10 flex items-center justify-center">
              <i class="ri-apple-fill ri-lg text-gray-400"></i>
            </span>
          </div>
        </div>
      </div>
      <div class="border-t border-gray-800 pt-8">
        <div class="flex flex-col md:flex-row justify-between items-center">
          <p class="text-gray-400 text-sm mb-4 md:mb-0">© 2025 MediCare. All rights reserved.</p>
          <div class="flex space-x-6">
            <a href="#" class="text-gray-400 hover:text-white text-sm transition-colors">Privacy Policy</a>
            <a href="#" class="text-gray-400 hover:text-white text-sm transition-colors">Terms of Service</a>
            <a href="#" class="text-gray-400 hover:text-white text-sm transition-colors">Cookie Policy</a>
          </div>
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
  <div id="loginModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center">
    <div class="bg-white rounded-lg w-full max-w-md mx-4 overflow-hidden">
      <div class="flex justify-between items-center p-6 border-b">
        <h2 class="text-2xl font-semibold text-gray-900">Login to Your Account</h2>
        <button id="closeLoginModal"
          class="w-10 h-10 flex items-center justify-center text-gray-500 hover:text-gray-700">
          <i class="ri-close-line ri-lg"></i>
        </button>
      </div>
      <form class="p-6 space-y-6" method="POST" action="">
        <div>
          <label for="username" class="block text-sm font-medium text-gray-700 mb-2">Username</label>
          <div class="relative">
            <input type="text" id="username" name="username" required
              class="block w-full pl-3 pr-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary"
              placeholder="Enter your username">
          </div>
        </div>
        <div>
          <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password</label>
          <div class="relative">
            <input type="password" id="password" name="password" required
              class="block w-full pl-3 pr-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary"
              placeholder="Enter your password">
          </div>
        </div>
        <div class="flex items-center justify-between">
          <div class="flex items-center">
          </div>
          <a href="#" class="text-sm text-primary hover:text-opacity-80">Forgot password?</a>
        </div>
        <button type="submit"
          class="w-full bg-primary text-white py-2 !rounded-button font-medium hover:bg-opacity-90 transition-colors">Login</button>
        <div class="relative">
          <div class="absolute inset-0 flex items-center">
          </div>
          <div class="relative flex justify-center text-sm">
          </div>
        </div>
        <div class="grid grid-cols-3 gap-3">
          <button type="button"
            class="flex items-center justify-center py-2 px-4 border border-gray-300 rounded hover:bg-gray-50">
          </button>
          <button type="button"
            class="flex items-center justify-center py-2 px-4 border border-gray-300 rounded hover:bg-gray-50">
          </button>
          <button type="button"
            class="flex items-center justify-center py-2 px-4 border border-gray-300 rounded hover:bg-gray-50">
          </button>
        </div>
      </form>
      <div class="px-6 pb-6 text-center">
        <p class="text-sm text-gray-600">
          Don't have an account?
          <a href="#" class="text-primary hover:text-opacity-80 font-medium">Sign up</a>
        </p>
      </div>
    </div>
  </div>
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const loginBtn = document.getElementById('loginBtn');
      const loginModal = document.getElementById('loginModal');
      const closeLoginModal = document.getElementById('closeLoginModal');
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
      // Remove JS form handler that bypasses PHP validation
    });
  </script>
  <!-- ...existing code... -->
</body>

</html>