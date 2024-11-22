<?php
session_start();
include('db_config.php');

// Check if user is logged in and the role is 'admin'
if (!isset($_SESSION['user']) || $_SESSION['role'] != 'admin') {
    // If not an admin, redirect to an error page or login page
    header("Location: login.php");
    exit();
}

// Display admin-specific dashboard content
echo "Welcome, Admin " . $_SESSION['full_name'];
// Add more admin-specific content here
// Get the user's full name from the session
$full_name = isset($_SESSION['full_name']) ? $_SESSION['full_name'] : 'Guest';  // Default to 'Guest' if full name is not set

// Retrieve the user details from session
$first_name = isset($_SESSION['user']['first_name']) ? $_SESSION['user']['first_name'] : '';
$middle_name = isset($_SESSION['user']['middle_name']) ? $_SESSION['user']['middle_name'] : '';
$last_name = isset($_SESSION['user']['last_name']) ? $_SESSION['user']['last_name'] : '';
$email = isset($_SESSION['user']['email']) ? $_SESSION['user']['email'] : '';
$mobile_number = isset($_SESSION['user']['mobile_number']) ? $_SESSION['user']['mobile_number'] : '';
$aadhar_number = isset($_SESSION['user']['aadhar_number']) ? $_SESSION['user']['aadhar_number'] : '';
$password = isset($_SESSION['user']['password']) ? $_SESSION['user']['password'] : ''; // It's generally not recommended to store passwords in session
$role = isset($_SESSION['role']) ? $_SESSION['role'] : ''; // Role of the user (student, admin, superadmin)

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/alpinejs/3.10.3/cdn.min.js" defer></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <style>
        .sidebar-toggle:checked ~ .sidebar {
            transform: translateX(-100%);
        }
    </style>
</head>

<body class="font-sans antialiased bg-gray-100 min-h-screen">
    <div class="flex">
        <input type="checkbox" id="sidebar-toggle" class="hidden sidebar-toggle">
        <aside class="w-64 bg-gray-800 text-white min-h-screen p-4 transition-transform transform sidebar">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-semibold">Admin</h2>
                <label for="sidebar-toggle" class="text-white cursor-pointer lg:hidden">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </label>
            </div>
            <ul class="space-y-1">
                <li onclick="showSection('dashboard')" class="flex items-center hover:bg-gray-700 p-2 rounded cursor-pointer transition duration-300 ease-in-out active:bg-gray-600">
                    <i class="fas fa-tachometer-alt mr-2"></i> Dashboard
                </li>
                <li class="flex items-center hover:bg-gray-700 p-2 rounded cursor-pointer transition duration-300 ease-in-out">
                    <i class="fas fa-users mr-2"></i> Reg. Users
                </li>
                <div x-data="{ open: false }">
                    <button @click="open = !open" class="flex items-center justify-between w-full py-2 px-4 rounded hover:bg-gray-700 transition duration-200">
                        <i class="fas fa-file-alt mr-2"></i> Admission Application
                        <span x-show="open">-</span><span x-show="!open">+</span>
                    </button>
                    <div x-show="open" class="pl-4 space-y-1 mt-1" x-cloak>
                        <a href="#" class="block py-1 hover:text-gray-300"><i class="fas fa-hourglass-start mr-2"></i> Pending</a>
                        <a href="#" class="block py-1 hover:text-gray-300"><i class="fas fa-check-circle mr-2"></i> Selected</a>
                        <a href="#" class="block py-1 hover:text-gray-300"><i class="fas fa-times-circle mr-2"></i> Rejected</a>
                        <a href="#" class="block py-1 hover:text-gray-300"><i class="fas fa-list mr-2"></i> All Applications</a>
                    </div>
                </div>
            </ul>
        </aside>

        <div class="flex-grow">
            <header class="flex items-center justify-end bg-white shadow p-3 mb-6">
                <div class="relative flex items-center">
                                      <h1 class="text-2xl font-semibold text-left">Welcome Back, <?php echo htmlspecialchars($full_name); ?>!</h1>

                </div>
            </header>

            <!-- Dynamic Content Section -->
            <div id="dynamicContent" class="px-8">
                <!-- Dashboard Cards -->
                <section id="dashboard" class="grid grid-cols-1 ```html
                sm:grid-cols-2 md:grid-cols-3 gap-6 mb-6 hidden">
                    <div class="bg-white p-4 rounded-lg shadow text-center transform transition duration-500 hover:scale-105 hover:shadow-lg">
                        <h3 class="text-xl font-bold">12</h3>
                        <p class="text-gray-600">Listed Courses</p>
                    </div>
                    <div class="bg-white p-4 rounded-lg shadow text-center transform transition duration-500 hover:scale-105 hover:shadow-lg">
                        <h3 class="text-xl font-bold">5</h3>
                        <p class="text-gray-600">Registered Users</p>
                    </div>
                    <div class="bg-white p-4 rounded-lg shadow text-center transform transition duration-500 hover:scale-105 hover:shadow-lg">
                        <h3 class="text-xl font-bold">1</h3>
                        <p class="text-gray-600">Pending Applications</p>
                    </div>
                    <div class="bg-white p-4 rounded-lg shadow text-center transform transition duration-500 hover:scale-105 hover:shadow-lg">
                        <h3 class="text-xl font-bold">3</h3>
                        <p class="text-gray-600">Selected Students</p>
                    </div>
                    <div class="bg-white p-4 rounded-lg shadow text-center transform transition duration-500 hover:scale-105 hover:shadow-lg">
                        <h3 class="text-xl font-bold">7</h3>
                        <p class="text-gray-600">Rejected Applications</p>
                    </div>
                    <div class="bg-white p-4 rounded-lg shadow text-center transform transition duration-500 hover:scale-105 hover:shadow-lg">
                        <h3 class="text-xl font-bold">5</h3>
                        <p class="text-gray-600">Enquiries</p>
                    </div>
                </section>
            </div>
        </div>
    </div>

    <script>
        function showSection(sectionId) {
            const sections = document.querySelectorAll('#dynamicContent > section');
            sections.forEach(section => section.classList.add('hidden'));
            document.getElementById(sectionId).classList.remove('hidden');
        }
    </script>
</body>
</html>