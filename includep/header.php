<?php
// Determine user type and name for header
if (session_status() === PHP_SESSION_NONE) { session_start(); }
$userName = "";
$userRole = "";
$userInitials = "";
$patient = null;
$unread_messages = 0;

// If a student is logged in, always fetch their student_id and name from imported_patients using id from session.
if (isset($_SESSION['student_row_id'])) { // expects 'student_row_id' to be set in session after login
    require_once __DIR__ . '/../includes/db_connect.php';
    // Only create a new connection if $conn is not set, not a valid mysqli object, or is closed
    if (!isset($conn) || !$conn instanceof mysqli || $conn->connect_errno || !$conn->ping()) {
        $conn = new mysqli('localhost', 'root', '', 'clinic_management_system');
    }
    $row_id = $_SESSION['student_row_id'];
    $stmt = $conn->prepare("SELECT student_id, name, dob, gender, address, civil_status, year_level FROM imported_patients WHERE id = ? LIMIT 1");
    if ($stmt) {
        $stmt->bind_param("i", $row_id);
        $stmt->execute();
        $stmt->bind_result($student_id, $name, $dob, $gender, $address, $civil_status, $year_level);
        if ($stmt->fetch()) {
            $patient = [
                'student_id' => $student_id,
                'name' => $name,
                'dob' => $dob,
                'gender' => $gender,
                'address' => $address,
                'civil_status' => $civil_status,
                'year_level' => $year_level
            ];
        }
        $stmt->close();
    }
    
    // Get unread message and notification count for patient
    try {
        $db = new PDO('mysql:host=localhost;dbname=clinic_management_system;charset=utf8', 'root', '');
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        // Unread messages
        $unread_stmt = $db->prepare('SELECT COUNT(*) FROM messages WHERE recipient_id = ? AND is_read = FALSE');
        $unread_stmt->execute([$row_id]);
        $unread_messages = $unread_stmt->fetchColumn();
        // Unread notifications
        $unread_notif_stmt = $db->prepare('SELECT COUNT(*) FROM notifications WHERE student_id = ? AND is_read = 0');
        $unread_notifs = 0;
        $unread_notif_stmt->execute([$row_id]);
        $unread_notifs = $unread_notif_stmt->fetchColumn();
    } catch (PDOException $e) {
        $unread_messages = 0;
        $unread_notifs = 0;
    }
    
    // Only close if we created the connection here
    if (!isset($GLOBALS['conn_from_db_connect']) && isset($conn) && $conn instanceof mysqli && $conn->ping()) {
        $conn->close();
    }
} elseif (isset($_SESSION['user_name'])) {
    // Staff/Admin login
    $userName = $_SESSION['user_name'];
    $userRole = isset($_SESSION['role']) ? ucfirst($_SESSION['role']) : "Administrator";
    $userStudentId = "";
} else {
    $userName = "Guest";
    $userRole = "";
    $userStudentId = "";
}
// Calculate initials
if (!empty($userName)) {
    $parts = explode(' ', trim($userName));
    $userInitials = strtoupper(substr($parts[0], 0, 1));
    if (count($parts) > 1) {
        $userInitials .= strtoupper(substr($parts[1], 0, 1));
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clinic Management Dashboard</title>
    <script src="https://cdn.tailwindcss.com/3.4.16"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#2B7BE4',
                        secondary: '#4CAF50'
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
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.6.0/remixicon.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/echarts/5.5.0/echarts.min.js"></script>
    <?php include_once '../includes/modal_system.php'; ?>
    <style>
        :where([class^="ri-"])::before {
            content: "\f3c2";
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f9fafb;
        }

        input[type="number"]::-webkit-inner-spin-button,
        input[type="number"]::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        .custom-checkbox {
            position: relative;
            display: inline-block;
            width: 18px;
            height: 18px;
            border-radius: 4px;
            border: 2px solid #d1d5db;
            background-color: white;
            cursor: pointer;
        }

        .custom-checkbox.checked {
            background-color: #2B7BE4;
            border-color: #2B7BE4;
        }

        .custom-checkbox.checked::after {
            content: '';
            position: absolute;
            top: 2px;
            left: 5px;
            width: 5px;
            height: 10px;
            border: solid white;
            border-width: 0 2px 2px 0;
            transform: rotate(45deg);
        }

        .toggle-switch {
            position: relative;
            display: inline-block;
            width: 44px;
            height: 24px;
        }

        .toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .toggle-slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #e5e7eb;
            transition: .4s;
            border-radius: 34px;
        }

        .toggle-slider:before {
            position: absolute;
            content: "";
            height: 18px;
            width: 18px;
            left: 3px;
            bottom: 3px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }

        input:checked+.toggle-slider {
            background-color: #2B7BE4;
        }

        input:checked+.toggle-slider:before {
            transform: translateX(20px);
        }

        .custom-select {
            position: relative;
            display: inline-block;
        }

        .custom-select-trigger {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.5rem 1rem;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            background-color: white;
            cursor: pointer;
            min-width: 150px;
        }

        .custom-select-options {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background-color: white;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            margin-top: 0.25rem;
            z-index: 10;
            display: none;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        .custom-select-option {
            padding: 0.5rem 1rem;
            cursor: pointer;
        }

        .custom-select-option:hover {
            background-color: #f3f4f6;
        }

        .custom-select.open .custom-select-options {
            display: block;
        }

        .drop-zone {
            border: 2px dashed #d1d5db;
            border-radius: 8px;
            padding: 2rem;
            text-align: center;
            transition: border-color 0.3s ease;
            cursor: pointer;
        }

        .drop-zone:hover {
            border-color: #2B7BE4;
        }

        .drop-zone.active {
            border-color: #2B7BE4;
            background-color: rgba(43, 123, 228, 0.05);
        }
    </style>
</head>

<body>

    <div class="min-h-screen flex flex-col">
        <!-- Header -->
        <header class="bg-white border-b border-gray-200 fixed top-0 left-0 w-full z-30">
            <div class="flex items-center justify-between px-6 py-3">
                <div class="flex items-center">
                    <img src="../logo.jpg" alt="St. Cecilia's College Logo"
                        class="h-12 w-12 object-contain rounded-full border border-gray-200 bg-white shadow mr-4" />
                    <h1 class="text-xl font-semibold text-gray-800 hidden md:block">Clinic Management System</h1>
                </div>
                <div class="flex items-center space-x-1">

                    <div class="relative">
                        <button class="w-10 h-10 flex items-center justify-center text-gray-500 hover:text-primary">
                            <i class="ri-notification-3-line ri-xl"></i>
                        </button>
                        <?php if ($unread_notifs > 0): ?>
                            <span class="absolute top-1 right-1 w-5 h-5 bg-red-500 text-white text-xs flex items-center justify-center rounded-full"><?php echo $unread_notifs > 9 ? '9+' : $unread_notifs; ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="relative">
                        <div id="userAvatarBtn"
                            class="w-10 h-10 bg-primary rounded-full flex items-center justify-center text-white mr-2 cursor-pointer select-none">
                            <span class="font-medium">
                                <?php
                                if (isset($patient) && $patient) {
                                    // Show initials from patient name
                                    $parts = explode(' ', trim($patient['name']));
                                    $initials = strtoupper(substr($parts[0], 0, 1));
                                    if (count($parts) > 1) {
                                        $initials .= strtoupper(substr($parts[1], 0, 1));
                                    }
                                    echo htmlspecialchars($initials);
                                } else {
                                    echo htmlspecialchars($userInitials);
                                }
                                ?>
                            </span>
                        </div>
                        <!-- Dropdown Pop-up -->
                        <div id="userDropdown"
                            class="hidden absolute -right-46 mt-2 w-56 bg-white rounded-lg shadow-lg border border-gray-100 z-50">
                            <div class="py-2">
                                <button onclick="openProfileModal()" class="w-full flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 text-left">
                                    <i class="ri-user-line mr-2 text-lg text-primary"></i> My Profile
                                </button>
                                <a href="#" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="ri-settings-3-line mr-2 text-lg text-primary"></i> Settings & privacy
                                </a>
                                <a href="#" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="ri-question-line mr-2 text-lg text-primary"></i> Help & support
                                </a>
                                <div class="border-t my-2"></div>
                                <a href="../index.php"
                                    class="flex items-center px-4 py-2 text-sm text-red-600 hover:bg-gray-100">
                                    <i class="ri-logout-box-line mr-2 text-lg"></i> Log out
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="hidden md:block">
                        <?php if (isset($patient) && $patient): ?>
                            <p class="text-sm font-medium text-gray-800">
                                <?php echo htmlspecialchars($patient['name']); ?>
                            </p>
                            <p class="text-xs text-gray-400">ID: <?php echo htmlspecialchars($patient['student_id']); ?>
                            </p>
                        <?php else: ?>
                            <p class="text-sm font-medium text-gray-800"><?php echo htmlspecialchars($userName); ?></p>
                            <p class="text-xs text-gray-500"><?php echo htmlspecialchars($userRole); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </header>
        <div class="flex flex-1">
            <!-- Sidebar -->
            <aside
                class="w-16 md:w-64 bg-white border-r border-gray-200 flex flex-col fixed top-[73px] left-0 h-[calc(100vh-56px)] z-40">
                <nav class="flex-1 pt-5 pb-4 overflow-y-auto">
                    <ul class="space-y-1 px-2" id="sidebarMenu">
                        <li>
                            <a href="profile.php"
                                class="flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-600 hover:bg-primary hover:bg-opacity-10 hover:text-primary"
                                data-page="profile.php">
                                <div class="w-8 h-8 flex items-center justify-center mr-3 md:mr-4">
                                    <i class="ri-user-line ri-lg"></i>
                                </div>
                                <span class="hidden md:inline">Profile</span>
                            </a>
                        </li>
                        <li>
                            <a href="inbox.php"
                                class="flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-600 hover:bg-primary hover:bg-opacity-10 hover:text-primary"
                                data-page="inbox.php">
                                <div class="w-8 h-8 flex items-center justify-center mr-3 md:mr-4 relative">
                                    <i class="ri-inbox-line ri-lg"></i>
                                    <?php if ($unread_messages > 0): ?>
                                        <span class="absolute -top-1 -right-1 w-4 h-4 bg-red-500 text-white text-xs flex items-center justify-center rounded-full"><?php echo $unread_messages > 9 ? '9+' : $unread_messages; ?></span>
                                    <?php endif; ?>
                                </div>
                                <span class="hidden md:inline">Inbox</span>
                            </a>
                        </li>
                        <li>
                            <a href="appointments.php"
                                class="flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-600 hover:bg-primary hover:bg-opacity-10 hover:text-primary"
                                data-page="appointments.php">
                                <div class="w-8 h-8 flex items-center justify-center mr-3 md:mr-4">
                                    <i class="ri-calendar-line ri-lg"></i>
                                </div>
                                <span class="hidden md:inline">Appointments</span>
                            </a>
                        </li>
                        <li>
                            <a href="history.php"
                                class="flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-600 hover:bg-primary hover:bg-opacity-10 hover:text-primary"
                                data-page="history.php">
                                <div class="w-8 h-8 flex items-center justify-center mr-3 md:mr-4">
                                    <i class="ri-history-line ri-lg"></i>
                                </div>
                                <span class="hidden md:inline">Medical History</span>
                            </a>
                        </li>
                        <li>
                            <a href="notifications.php"
                                class="flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-600 hover:bg-primary hover:bg-opacity-10 hover:text-primary"
                                data-page="notifications.php">
                                <div class="w-8 h-8 flex items-center justify-center mr-3 md:mr-4 relative">
                                    <i class="ri-notification-line ri-lg"></i>
                                    <?php if ($unread_notifs > 0): ?>
                                        <span class="absolute -top-1 -right-1 w-4 h-4 bg-red-500 text-white text-xs flex items-center justify-center rounded-full"><?php echo $unread_notifs > 9 ? '9+' : $unread_notifs; ?></span>
                                    <?php endif; ?>
                                </div>
                                <span class="hidden md:inline">Notification</span>
                            </a>
                        </li>
                    </ul>
                </nav>
                <div class="p-4 border-t border-gray-200 hidden md:block">
                    <a href="../index.php"
                        class="flex items-center text-sm font-medium text-gray-600 hover:text-primary">
                        <div class="w-8 h-8 flex items-center justify-center mr-3">
                            <i class="ri-graduation-cap-line ri-lg"></i>
                        </div>
                        <span>Hello Student</span>
                    </a>
                </div>


            </aside>

            <script>
                // Sidebar active state logic
                (function() {
                    const sidebarLinks = document.querySelectorAll('#sidebarMenu a');
                    const currentPage = window.location.pathname.split('/').pop();
                    sidebarLinks.forEach(link => {
                        if (link.getAttribute('data-page') === currentPage) {
                            link.classList.add('bg-primary', 'bg-opacity-10', 'text-primary');
                            link.classList.remove('text-gray-600');
                        } else {
                            link.classList.remove('bg-primary', 'bg-opacity-10', 'text-primary');
                            link.classList.add('text-gray-600');
                        }
                        link.addEventListener('click', function() {
                            sidebarLinks.forEach(l => l.classList.remove('bg-primary', 'bg-opacity-10', 'text-primary'));
                            this.classList.add('bg-primary', 'bg-opacity-10', 'text-primary');
                        });
                    });
                })();
            </script>
            <script>
                // User avatar dropdown logic
                const userAvatarBtn = document.getElementById('userAvatarBtn');
                const userDropdown = document.getElementById('userDropdown');
                if (userAvatarBtn && userDropdown) {
                    userAvatarBtn.addEventListener('click', function(e) {
                        e.stopPropagation();
                        userDropdown.classList.toggle('hidden');
                    });
                    document.addEventListener('click', function(e) {
                        if (!userDropdown.classList.contains('hidden')) {
                            userDropdown.classList.add('hidden');
                        }
                    });
                    userDropdown.addEventListener('click', function(e) {
                        e.stopPropagation();
                    });
                }
            </script>
            <?php
            // Patient notification logic (show only approved, cancelled, rescheduled appointment notifications)
            $notifCount = 0;
            $notifList = [];
            if (isset($patient['student_id'])) {
                $connNotif = new mysqli('localhost', 'root', '', 'clinic_management_system');
                if (!$connNotif->connect_errno) {
                    // Fetch notifications for this patient that are only for approved, cancelled, or rescheduled appointments
                    $sql = "SELECT message, created_at, is_read FROM notifications WHERE student_id = ? AND (
                        message LIKE '%approved%' OR message LIKE '%confirmed%' OR message LIKE '%declined%' OR message LIKE '%cancelled%' OR message LIKE '%canceled%' OR message LIKE '%rescheduled%'
                    ) ORDER BY created_at DESC LIMIT 10";
                    $stmt = $connNotif->prepare($sql);
                    $stmt->bind_param('i', $patient['student_id']);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    while ($row = $result->fetch_assoc()) {
                        $notifList[] = [
                            'msg' => strip_tags($row['message']),
                            'created_at' => $row['created_at'],
                            'is_read' => $row['is_read']
                        ];
                    }
                    $stmt->close();
                    $stmt2 = $connNotif->prepare("SELECT COUNT(*) as cnt FROM notifications WHERE student_id = ? AND is_read = 0 AND (
                        message LIKE '%approved%' OR message LIKE '%confirmed%' OR message LIKE '%declined%' OR message LIKE '%cancelled%' OR message LIKE '%canceled%' OR message LIKE '%rescheduled%'
                    )");
                    $stmt2->bind_param('i', $patient['student_id']);
                    $stmt2->execute();
                    $stmt2->bind_result($notifCount);
                    $stmt2->fetch();
                    $stmt2->close();
                    $connNotif->close();
                }
            }
            ?>
            
            <!-- Profile Modal -->
            <div id="profileModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
                <div class="flex items-center justify-center min-h-screen p-4">
                    <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
                        <div class="flex justify-between items-center p-6 border-b">
                            <h3 class="text-lg font-semibold text-gray-800">Profile Information</h3>
                            <button onclick="closeProfileModal()" class="text-gray-400 hover:text-gray-600">
                                <i class="ri-close-line text-xl"></i>
                            </button>
                        </div>
                        <div class="p-6">
                            <div class="flex flex-col items-center mb-6">
                                <div class="w-20 h-20 rounded-full bg-primary text-white flex items-center justify-center text-2xl font-bold mb-4">
                                    <?php 
                                    if (isset($patient) && $patient) {
                                        $parts = explode(' ', trim($patient['name']));
                                        $initials = strtoupper(substr($parts[0], 0, 1));
                                        if (count($parts) > 1) {
                                            $initials .= strtoupper(substr($parts[1], 0, 1));
                                        }
                                        echo htmlspecialchars($initials);
                                    } else {
                                        echo htmlspecialchars($userInitials);
                                    }
                                    ?>
                                </div>
                                <h4 class="text-xl font-semibold text-gray-800 text-center">
                                    <?php echo isset($patient) && $patient ? htmlspecialchars($patient['name']) : htmlspecialchars($userName); ?>
                                </h4>
                            </div>
                            <div class="space-y-3">
                                <?php if (isset($patient) && $patient): ?>
                                <div class="flex justify-between">
                                    <span class="text-sm font-medium text-gray-600">Student ID:</span>
                                    <span class="text-sm text-gray-900"><?php echo htmlspecialchars($patient['student_id']); ?></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm font-medium text-gray-600">Date of Birth:</span>
                                    <span class="text-sm text-gray-900"><?php echo htmlspecialchars($patient['dob'] ?? 'N/A'); ?></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm font-medium text-gray-600">Gender:</span>
                                    <span class="text-sm text-gray-900"><?php echo htmlspecialchars($patient['gender'] ?? 'N/A'); ?></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm font-medium text-gray-600">Year Level:</span>
                                    <span class="text-sm text-gray-900"><?php echo htmlspecialchars($patient['year_level'] ?? 'N/A'); ?></span>
                                </div>
                                <?php if (!empty($patient['address'])): ?>
                                <div class="flex justify-between">
                                    <span class="text-sm font-medium text-gray-600">Address:</span>
                                    <span class="text-sm text-gray-900"><?php echo htmlspecialchars($patient['address']); ?></span>
                                </div>
                                <?php endif; ?>
                                <?php if (!empty($patient['civil_status'])): ?>
                                <div class="flex justify-between">
                                    <span class="text-sm font-medium text-gray-600">Civil Status:</span>
                                    <span class="text-sm text-gray-900"><?php echo htmlspecialchars($patient['civil_status']); ?></span>
                                </div>
                                <?php endif; ?>
                                <?php else: ?>
                                <div class="flex justify-between">
                                    <span class="text-sm font-medium text-gray-600">Role:</span>
                                    <span class="text-sm text-gray-900"><?php echo htmlspecialchars($userRole); ?></span>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="flex justify-end p-6 border-t bg-gray-50">
                            <button onclick="closeProfileModal()" class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700 transition-colors">
                                Close
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <script>
                function openProfileModal() {
                    document.getElementById('profileModal').classList.remove('hidden');
                    // Close the dropdown when modal opens
                    document.getElementById('userDropdown').classList.add('hidden');
                }

                function closeProfileModal() {
                    document.getElementById('profileModal').classList.add('hidden');
                }

                // Close modal when clicking outside
                document.getElementById('profileModal').addEventListener('click', function(e) {
                    if (e.target === this) {
                        closeProfileModal();
                    }
                });

                // Close modal with Escape key
                document.addEventListener('keydown', function(e) {
                    if (e.key === 'Escape') {
                        const modal = document.getElementById('profileModal');
                        if (!modal.classList.contains('hidden')) {
                            closeProfileModal();
                        }
                    }
                });
            </script>
            
            <script>
                // Notification icon dropdown logic
                (function() {
                    const notifBtn = document.getElementById('notifIconBtn');
                    const notifDropdown = document.getElementById('notifDropdown');
                    let notifOpen = false;
                    if (notifBtn && notifDropdown) {
                        notifBtn.addEventListener('click', function(e) {
                            e.stopPropagation();
                            notifDropdown.classList.toggle('hidden');
                            notifOpen = !notifOpen;
                        });
                        document.addEventListener('click', function(e) {
                            if (notifOpen && !notifBtn.contains(e.target) && !notifDropdown.contains(e.target)) {
                                notifDropdown.classList.add('hidden');
                                notifOpen = false;
                            }
                        });
                    }
                })();
            </script>
            <?php includeModalSystem(); ?>