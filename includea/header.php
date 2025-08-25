<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$user_display_name = '';
if (isset($_SESSION['username'])) {
    $user_display_name = htmlspecialchars($_SESSION['username']);
} elseif (isset($_SESSION['student_name'])) {
    $user_display_name = htmlspecialchars($_SESSION['student_name']);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clinic Management Dashboard</title>
    <script src="https://cdn.tailwindcss.com/3.4.16"></script>
    <script>tailwind.config = { theme: { extend: { colors: { primary: '#2B7BE4', secondary: '#4CAF50' }, borderRadius: { 'none': '0px', 'sm': '4px', DEFAULT: '8px', 'md': '12px', 'lg': '16px', 'xl': '20px', '2xl': '24px', '3xl': '32px', 'full': '9999px', 'button': '8px' } } } }</script>
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

        /* Hide scrollbars while maintaining scroll functionality */
        html, body {
            scrollbar-width: none; /* Firefox */
            -ms-overflow-style: none; /* Internet Explorer 10+ */
        }

        html::-webkit-scrollbar,
        body::-webkit-scrollbar,
        *::-webkit-scrollbar {
            display: none; /* Safari and Chrome */
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
                        <div id="userAvatarBtn"
                            class="w-10 h-10 bg-primary rounded-full flex items-center justify-center text-white mr-2 cursor-pointer select-none">
                            <span class="font-medium">
                                <?php
                                // Show initials from user_display_name, fallback to 'U'
                                $initials = 'U';
                                if ($user_display_name) {
                                    $parts = explode(' ', $user_display_name);
                                    $initials = strtoupper(substr($parts[0], 0, 1));
                                    if (count($parts) > 1) {
                                        $initials .= strtoupper(substr($parts[1], 0, 1));
                                    }
                                }
                                echo $initials;
                                ?>
                            </span>
                        </div>
                        <!-- Dropdown Pop-up -->
                        <div id="userDropdown"
                            class="hidden absolute -right-11 mt-2 w-56 bg-white rounded-lg shadow-lg border border-gray-100 z-50">
                            <div class="py-2">
                                <a href="#" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="ri-user-line mr-2 text-lg text-primary"></i> My Profile
                                </a>
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
                        <p class="text-sm font-medium text-gray-800">
                            <?php echo $user_display_name ? $user_display_name : 'User'; ?>
                        </p>
                        <p class="text-xs text-gray-500">
                            <?php echo isset($_SESSION['role']) ? htmlspecialchars($_SESSION['role']) : ''; ?>
                        </p>
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
                            <a href="dashboard.php"
                                class="flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-600 hover:bg-primary hover:bg-opacity-10 hover:text-primary"
                                data-page="dashboard.php">
                                <div class="w-8 h-8 flex items-center justify-center mr-3 md:mr-4">
                                    <i class="ri-dashboard-line ri-lg"></i>
                                </div>
                                <span class="hidden md:inline">Dashboard</span>
                            </a>
                        </li>
                        <li>
                            <a href="import.php"
                                class="flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-600 hover:bg-primary hover:bg-opacity-10 hover:text-primary"
                                data-page="import.php">
                                <div class="w-8 h-8 flex items-center justify-center mr-3 md:mr-4">
                                    <i class="ri-import-line ri-lg"></i> <!-- If available in your version -->
                                </div>
                                <span class="hidden md:inline">Import CSV</span>
                            </a>
                        </li>
                        <li>
                            <a href="users.php"
                                class="flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-600 hover:bg-primary hover:bg-opacity-10 hover:text-primary"
                                data-page="users.php">
                                <div class="w-8 h-8 flex items-center justify-center mr-3 md:mr-4">
                                    <i class="ri-user-line ri-lg"></i>
                                </div>
                                <span class="hidden md:inline">Manage Users</span>
                            </a>
                        </li>
                        <li>
                            <a href="reports.php"
                                class="flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-600 hover:bg-primary hover:bg-opacity-10 hover:text-primary"
                                data-page="reports.php">
                                <div class="w-8 h-8 flex items-center justify-center mr-3 md:mr-4">
                                    <i class="ri-bar-chart-line ri-lg"></i>
                                </div>
                                <span class="hidden md:inline">Reports</span>
                            </a>
                        </li>
                        <li>
                            <a href="logs.php"
                                class="flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-600 hover:bg-primary hover:bg-opacity-10 hover:text-primary"
                                data-page="logs.php">
                                <div class="w-8 h-8 flex items-center justify-center mr-3 md:mr-4">
                                    <i class="ri-clipboard-line ri-lg"></i>
                                </div>
                                <span class="hidden md:inline">System Logs</span>
                            </a>
                        </li>
                    </ul>
                </nav>
                <div class="p-4 border-t border-gray-200 hidden md:block">
                    <a href="../index.php"
                        class="flex items-center text-sm font-medium text-gray-600 hover:text-primary">
                        <div class="w-8 h-8 flex items-center justify-center mr-3">
                            <i class="ri-admin-line ri-lg"></i>
                        </div>
                        <span>Hello Admin</span>
                    </a>
                </div>

            </aside>

            <script>
                    // Sidebar active state logic
                    (function () {
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
                            link.addEventListener('click', function () {
                                sidebarLinks.forEach(l => l.classList.remove('bg-primary', 'bg-opacity-10', 'text-primary'));
                                this.classList.add('bg-primary', 'bg-opacity-10', 'text-primary');
                            });
                        });
                    })();
            </script>
            <script>
                // User avatar dropdown logic
                document.addEventListener('DOMContentLoaded', function () {
                    const avatarBtn = document.getElementById('userAvatarBtn');
                    const dropdown = document.getElementById('userDropdown');
                    let dropdownOpen = false;
                    avatarBtn.addEventListener('click', function (e) {
                        e.stopPropagation();
                        dropdown.classList.toggle('hidden');
                        dropdownOpen = !dropdownOpen;
                    });
                    document.addEventListener('click', function (e) {
                        if (dropdownOpen && !avatarBtn.contains(e.target) && !dropdown.contains(e.target)) {
                            dropdown.classList.add('hidden');
                            dropdownOpen = false;
                        }
                    });
                });
            </script>
            <?php includeModalSystem(); ?>