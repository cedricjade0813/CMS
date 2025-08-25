<?php
include_once 'includes/modal_system.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modal System Test</title>
    <script src="https://cdn.tailwindcss.com/3.4.16"></script>
    <script>tailwind.config = { theme: { extend: { colors: { primary: '#2B7BE4', secondary: '#4CAF50' } } } }</script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.6.0/remixicon.min.css">
    <?php include_once 'includes/modal_system.php'; ?>
</head>
<body class="bg-gray-50 p-8">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold text-gray-800 mb-8">Modal System Test</h1>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Success Modal Test -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold mb-4">Success Modal</h2>
                <p class="text-gray-600 mb-4">Test the success modal with different messages.</p>
                <div class="space-y-2">
                    <button onclick="showSuccessModal('Operation completed successfully!', 'Success')" 
                            class="w-full bg-green-500 text-white py-2 px-4 rounded hover:bg-green-600">
                        Show Success Modal
                    </button>
                    <button onclick="showSuccessModal('Data saved successfully!', 'Success', true, 'index.php')" 
                            class="w-full bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-600">
                        Success with Redirect
                    </button>
                </div>
            </div>

            <!-- Error Modal Test -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold mb-4">Error Modal</h2>
                <p class="text-gray-600 mb-4">Test the error modal with different messages.</p>
                <div class="space-y-2">
                    <button onclick="showErrorModal('An error occurred while processing your request.', 'Error')" 
                            class="w-full bg-red-500 text-white py-2 px-4 rounded hover:bg-red-600">
                        Show Error Modal
                    </button>
                    <button onclick="showErrorModal('Database connection failed. Please try again.', 'Database Error')" 
                            class="w-full bg-red-600 text-white py-2 px-4 rounded hover:bg-red-700">
                        Database Error
                    </button>
                </div>
            </div>

            <!-- Warning Modal Test -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold mb-4">Warning Modal</h2>
                <p class="text-gray-600 mb-4">Test the warning modal with different messages.</p>
                <div class="space-y-2">
                    <button onclick="showWarningModal('Please review your input before proceeding.', 'Warning')" 
                            class="w-full bg-yellow-500 text-white py-2 px-4 rounded hover:bg-yellow-600">
                        Show Warning Modal
                    </button>
                    <button onclick="showWarningModal('This action cannot be undone.', 'Confirmation Required')" 
                            class="w-full bg-orange-500 text-white py-2 px-4 rounded hover:bg-orange-600">
                        Confirmation Warning
                    </button>
                </div>
            </div>

            <!-- Info Modal Test -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold mb-4">Info Modal</h2>
                <p class="text-gray-600 mb-4">Test the info modal with different messages.</p>
                <div class="space-y-2">
                    <button onclick="showInfoModal('Your session will expire in 5 minutes.', 'Session Info')" 
                            class="w-full bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-600">
                        Show Info Modal
                    </button>
                    <button onclick="showInfoModal('New features are available. Check the updates page.', 'System Update')" 
                            class="w-full bg-indigo-500 text-white py-2 px-4 rounded hover:bg-indigo-600">
                        System Update Info
                    </button>
                </div>
            </div>

            <!-- Alert Override Test -->
            <div class="bg-white rounded-lg shadow p-6 md:col-span-2">
                <h2 class="text-xl font-semibold mb-4">Alert Override Test</h2>
                <p class="text-gray-600 mb-4">Test that the global alert() function has been overridden to use modals.</p>
                <div class="space-y-2">
                    <button onclick="alert('This should show as a modal instead of a browser alert!')" 
                            class="w-full bg-purple-500 text-white py-2 px-4 rounded hover:bg-purple-600">
                        Test Alert Override
                    </button>
                    <button onclick="alert('Another test message to verify the override works.')" 
                            class="w-full bg-purple-600 text-white py-2 px-4 rounded hover:bg-purple-700">
                        Another Alert Test
                    </button>
                </div>
            </div>

            <!-- PHP Modal Test -->
            <div class="bg-white rounded-lg shadow p-6 md:col-span-2">
                <h2 class="text-xl font-semibold mb-4">PHP Modal Test</h2>
                <p class="text-gray-600 mb-4">Test modals generated from PHP.</p>
                <div class="space-y-2">
                    <form method="post" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Message Type:</label>
                            <select name="modal_type" class="w-full border border-gray-300 rounded px-3 py-2">
                                <option value="success">Success</option>
                                <option value="error">Error</option>
                                <option value="warning">Warning</option>
                                <option value="info">Info</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Message:</label>
                            <input type="text" name="message" value="Test message from PHP" 
                                   class="w-full border border-gray-300 rounded px-3 py-2">
                        </div>
                        <button type="submit" class="w-full bg-primary text-white py-2 px-4 rounded hover:bg-primary/90">
                            Show PHP Modal
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <?php
        // Handle PHP modal test
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $type = $_POST['modal_type'] ?? 'success';
            $message = $_POST['message'] ?? 'Test message';
            
            switch ($type) {
                case 'success':
                    showSuccessModal($message, 'Success');
                    break;
                case 'error':
                    showErrorModal($message, 'Error');
                    break;
                case 'warning':
                    showWarningModal($message, 'Warning');
                    break;
                case 'info':
                    showInfoModal($message, 'Information');
                    break;
            }
        }
        ?>
    </div>

    <?php includeModalSystem(); ?>
</body>
</html>
