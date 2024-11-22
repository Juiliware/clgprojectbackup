<?php
session_start(); // Start the session
include('db_config.php'); // Include your database connection file

// Check if user is logged in and the role is 'admin'
if (!isset($_SESSION['user']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

// Get the user's full name from the session
$full_name = isset($_SESSION['full_name']) ? $_SESSION['full_name'] : 'Guest';

// Database connection
$host = 'localhost'; // Change this to your database host
$db = 'college_project'; // Change this to your database name
$user = 'root'; // Change this to your database username
$pass = ''; // Change this to your database password

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Could not connect to the database $db :" . $e->getMessage());
}

// Fetch registered users from the database
$query = "SELECT * FROM students"; // Adjust the table name if necessary
$stmt = $pdo->query($query);
$registeredUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle deletion
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $deleteStmt = $pdo->prepare("DELETE FROM students WHERE id = :id");
    $deleteStmt->bindParam(':id', $id);
    if ($deleteStmt->execute()) {
        header("Location: " . $_SERVER['PHP_SELF'] . "?message=User  deleted successfully");
        exit();
    } else {
        echo "Error deleting user.";
    }
}

// Function to get all student names from the biodata table based on student_id
function getStudentNamesByIds($pdo, $student_ids) {
    if (empty($student_ids)) {
        return [];
    }
    
    $placeholders = implode(',', array_fill(0, count($student_ids), '?'));
    $stmt = $pdo->prepare("SELECT name, students_id FROM biodata WHERE students_id IN ($placeholders)");
    $stmt->execute($student_ids);
    return $stmt->fetchAll(PDO::FETCH_ASSOC); // Fetch all names as an array
}

// Fetch student IDs that have filled the biodata form
$student_ids_query = "SELECT DISTINCT students_id FROM biodata";
$student_ids_stmt = $pdo->query($student_ids_query);
$student_ids = $student_ids_stmt->fetchAll(PDO::FETCH_COLUMN);

// Fetch student names who have filled the biodata form
$student_names = getStudentNamesByIds($pdo, $student_ids);
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
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h 16M4 12h16M4 18h16"></path>
                    </svg>
                </label>
            </div>
            <ul class="space-y-4">
                <li onclick="showSection('dashboard')" class="flex items-center hover:bg-gray-700 p-3 rounded cursor-pointer transition duration-300 ease-in-out" id="dashboard-link">
                    <i class="fas fa-tachometer-alt mr-3"></i> Dashboard
                </li>
                <li onclick="showSection('registeredUsers')" class="flex items-center hover:bg-gray-700 p-3 rounded cursor-pointer transition duration-300 ease-in-out">
                    <i class="fas fa-users mr-3"></i> Reg. Users
                </li>
                <div x-data="{ open: false }">
                    <button @click="open = !open" class="flex items-center justify-between w-full py-3 px-4 rounded hover:bg-gray-700 transition duration-200">
                        <i class="fas fa-file-alt mr-3"></i> Admission Application
                        <span x-show="open">-</span><span x-show="!open">+</span>
                    </button>
                    <div x-show="open" class="pl-4 space-y-1 mt-1" x-cloak>
                        <a href="#" class="block py-2 hover:text-gray-300"><i class="fas fa-hourglass-start mr-2"></i> Pending</a>
                        <a href="#" class="block py-2 hover:text-gray-300"><i class="fas fa-check-circle mr-2"></i> Selected</a>
                        <a href="#" class="block py-2 hover:text-gray-300"><i class="fas fa-times-circle mr-2"></i> Rejected</a>
                        <a href="#" class="block py-2 hover:text-gray-300"><i class="fas fa-list mr-2"></i> All Applications</a>
                    </div>
                </div>
                <li onclick="showSection('searchApplication')" class="flex items-center hover:bg-gray-700 p-3 rounded cursor-pointer transition duration-300 ease-in-out">
                    <i class="fas fa-search mr-3"></i> Search Application
                </li>
            </ul>
        </aside>

        <div class="flex-grow">
            <header class="flex items-center justify-end bg-white shadow p-3 mb-6">
                <div class="relative flex items-center">
                    Hello, <?php echo htmlspecialchars($full_name); ?> 
                    <i id="profileIcon" class="fas fa-user-circle text-gray-600 text-2xl ml-2 cursor-pointer" onclick="toggleUser Dropdown()"></i>
                    <div class="absolute mt-10 w-48 bg-white text-gray-800 z-30 rounded-lg shadow-lg hidden" id="userDropdown">
                        <div class="px-4 py-2 border-b flex items-center">
                            <i class="fas fa-user-circle text-gray-600 text-2xl mr-2"></i>
                            <div>
                                <span id="dropdownUsername" class="text-sm text-gray-500 block"><?php echo htmlspecialchars($full_name); ?></span>
                            </div>
                        </div>
                        <a href="#" onclick="showEditProfileForm(); toggleUser Dropdown();" class="block px-4 py-2 border-b hover:bg-indigo-100 flex items-center no-underline">
                            <i class="fas fa-user-edit text-gray-600 mr-2"></i> Edit Profile
                        </a>
                        <a href="logout.php" class="block px-4 py-2 hover:bg-indigo-100 flex items-center no-underline">
                            <i class="fas fa-sign-out-alt text-gray-600 mr-2"></i> Log Out
                        </a>
                    </div>

                    <div class="relative cursor-pointer ml-4">
                        <button id="notificationButton" class="flex items-center text-xl text-gray-600" onclick="toggleNotificationPanel()">
                            🔔
                        </button>
                        <div id="notificationPanel" class="absolute right-0 mt-2 w-64 bg-white shadow-md rounded-lg p-4 hidden z-10">
                            <p class="font-bold">New Notifications</p>
                            <ul class="list-disc pl-5 space-y-2">
                                <?php foreach ($student_names as $student): ?>
                                    <li class="flex justify-between items-center 
                        </div>
                    </div>text-gray-700 hover:text-indigo-600 transition duration-200">
                                        <span><?php echo htmlspecialchars($student['name']); ?> has filled out the Application form</span>
                                        <a href="view_application.php?id=<?php echo htmlspecialchars($student['students_id']); ?>" class="text-indigo-600 hover:underline ml-2">
                                            View Application
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div></div>
                </div>
            </header>

            <script>
                // Toggle user dropdown visibility
                document.getElementById('profileIcon').addEventListener('click', function() {
                    const userDropdown = document.getElementById('userDropdown');
                    userDropdown.classList.toggle('hidden');
                });

                // Toggle notification panel visibility
                function toggleNotificationPanel() {
                    const notificationPanel = document.getElementById('notificationPanel');
                    notificationPanel.classList.toggle('hidden');
                }

                // Optional: Close dropdown if clicked outside
                window.addEventListener('click', function(e) {
                    const profileIcon = document.getElementById('profileIcon');
                    const userDropdown = document.getElementById('userDropdown');
                    const notificationButton = document.getElementById('notificationButton');
                    const notificationPanel = document.getElementById('notificationPanel');
                    
                    if (!profileIcon.contains(e.target) && !userDropdown.contains(e.target)) {
                        userDropdown.classList.add('hidden');
                    }
                    if (!notificationButton.contains(e.target) && !notificationPanel.contains(e.target)) {
                        notificationPanel.classList.add('hidden');
                    }
                });
            </script>

            <!-- Dynamic Content Section -->
            <div id="dynamicContent" class="px-8">
                <!-- Dashboard Cards -->
                <section id="dashboard" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6 mb-6 hidden">
                    <div class="bg-gradient-to-r from-blue-400 to-blue-600 p-6 rounded-lg shadow-lg flex items-center transform transition duration-500 hover:scale-105 hover:shadow-xl h-40">
                        <div class="flex justify-center items-center w-1/4">
                            <i class="fas fa-book text-white text-4xl"></i>
                        </div>
                        <div class="w-3/4 text-left">
                            <h3 class="text-2xl font-bold text-white">12</h3>
                            <p class="text-gray-200 text-sm">Listed Courses</p>
                        </div>
                    </div>
                    <div class="bg-gradient-to-r from-green-400 to-green-600 p-6 rounded-lg shadow-lg flex items-center transform transition duration-500 hover:scale-105 hover:shadow-xl h-40">
                        <div class="flex justify-center items-center w-1/4">
                            <i class="fas fa-users text-white text-4xl"></i>
                        </div>
                        <div class="w-3/4 text-left">
                            <h3 class="text-2xl font-bold text-white">5</h3>
                            <p class="text-gray-200 text-sm">Registered Users</p>
                        </div>
                    </div>
                    <div class="bg-gradient-to-r from-yellow-400 to-yellow-600 p-6 rounded-lg shadow-lg flex items-center transform transition duration-500 hover:scale-105 hover:shadow-xl h-40">
                        <div class="flex justify-center items-center w-1/4">
                            <i class="fas fa-clock text-white text-4xl"></i>
                        </div>
                        <div class="w-3/4 text-left">
                            <h3 class="text-2xl font-bold text-white">1</h3>
                            <p class="text-gray-200 text-sm">Pending Applications</p>
                        </div>
                    </div>
                    <div class="bg-gradient-to-r from-purple-400 to-purple-600 p-6 rounded-lg shadow-lg flex items-center transform transition duration-500 hover:scale-105 hover:shadow-xl h-40">
                        <div class="flex justify-center items-center w-1/4">
                            <i class="fas fa-user-check text-white text-4xl"></i>
                        </div>
                        <div class="w-3/4 text-left">
                            <h3 class="text-2xl font-bold text-white">3</h3>
                            <p class="text-gray-200 text-sm">Selected Students</p>
                        </div>
                    </div>
                    <div class="bg-gradient-to-r from-red-400 to-red-600 p-6 rounded-lg shadow-lg flex items-center transform transition duration-500 hover:scale-105 hover:shadow-xl h-40">
                        <div class="flex justify-center items-center w-1/4">
                            <i class="fas fa-user-times text-white text-4xl"></i>
                        </div>
                        <div class="w-3/4 text-left">
                            <h3 class="text-2xl font-bold text-white">7</h3>
                            <p class="text-gray-200 text-sm">Rejected Applications</p>
                        </div>
                    </div>
                    <div class="bg-gradient-to-r from-pink-400 to-purple-600 p-6 rounded-lg shadow-lg flex items-center transform transition duration-500 hover:scale-105 hover:shadow-xl h-40">
                        <div class="flex justify-center items-center w-1/4">
                            <i class="fas fa-question-circle text-white text-4xl"></i>
                        </div>
                        <div class="w-3/4 text-left">
                            <h3 class="text-2xl font-bold text-white">5</h3>
                            <p class="text-gray-200 text-sm">Enquiries</p>
                        </div>
                    </div>
                </section>
            </div>

            <!-- Registered Users Section -->
            <section id="registeredUsers" class="p-6 bg-white rounded-lg shadow-lg mt-6 mx-4">
                <h2 class="text-2xl font-semibold mb-4 text-gray-700">Registered Users</h2>
                <div class="relative overflow-x-auto">
                    <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                        <table class="w-full text-md text-left text-gray-500 border-collapse">
                            <thead class="text-md text-gray-700 uppercase bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-4 py-2">Id</th>
                                    <th scope="col" class="px-4 py-2">First Name</th>
                                    <th scope="col" class="px-4 py-2">Middle Name</th>
                                    <th scope="col" class="px-4 py-2">Last Name</th>
                                    <th scope="col" class="px-4 py-2">Email</th>
                                    <th scope="col" class="px-4 py-2">Mobile Number</th>
                                    <th scope="col" class="px-4 py-2">Aadhar Number</th>
                                    <th scope="col" class="px-4 py-2">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($registeredUsers as $user): ?>
                                <tr class="odd:bg-white even:bg-gray-50 border-b hover:bg-gray-100 transition duration-150">
                                    <th scope="row" class="px-4 py-3 font-medium text-gray-900 whitespace-nowrap">
                                        <?php echo htmlspecialchars($user['id']); ?>
                                    </th>
                                    <td class="px-4 py-3"><?php echo htmlspecialchars($user['first_name']); ?></td>
                                    <td class="px-4 py-3"><?php echo htmlspecialchars($user['middle_name']); ?></td>
                                    <td class="px-4 py-3"><?php echo htmlspecialchars($user['last_name']); ?></td>
                                    <td class="px-4 py-3"><?php echo htmlspecialchars($user['email']); ?></td>
                                    <td class="px-4 py-3"><?php echo htmlspecialchars($user['mobile_number']); ?></td>
                                    <td class="px-4 py-3"><?php echo htmlspecialchars($user['aadhar_number']); ?></td>
                                    <td class="px-4 py-3 space-x-2">
                                        <a href="#" class="text-blue-600 hover:underline" onclick="showEditProfileForm(<?php echo htmlspecialchars($user['id']); ?>)" title="Edit User">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="?action=delete&id=<?php echo htmlspecialchars($user['id']); ?>" class="text-red-600 hover:underline" onclick="return confirm('Are you sure you want to delete this user?');" title="Delete User">
                                            <i class="fas fa-trash-alt"></i>
                                        </a>
                                        <a href="view_application.php?id=<?php echo htmlspecialchars($user['id']); ?>" class="text-green-600 hover:underline" title="View Application">
                                            View Application
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>

            <!-- Search Application Section -->
            <section id="searchApplication" class="p-6 bg-white rounded-lg shadow-lg mt-6 mx-4">
            
                <h2 class="text-2xl font-semibold mb-4 text-gray-700">Search Applications</h2>
                <div class="flex items-center space-x-4">
                    <div class="relative w-full">
                        <input type="text" id="searchInput" placeholder="Enter search query..." 
                               class="w-full p-2 border border-gray-300 rounded-lg focus:outline-none focus:border-indigo-500" 
                               aria-label="Search Applications">
                        <button id="searchButton" class="absolute right-2 top-2 bg-indigo-600 text-white rounded-lg px-3 py-1 hover:bg-indigo-700 transition duration-300">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                    <button id="clearButton" class="bg-red-600 text-white rounded-lg px-4 py-2 hover:bg-red-700 transition duration-300">
                        Clear
                    </button>
                </div>
                <div id="loadingSpinner" class="hidden mt-4">
                    <i class="fas fa-spinner fa-spin text-indigo-600"></i> Searching...
                </div>
            </section>

            <script>
                function showSection(sectionId) {
                    const sections = ['dashboard', 'registeredUsers', 'searchApplication'];
                    sections.forEach(section => {
                        document.getElementById(section).classList.add('hidden');
                    });
                    document.getElementById(sectionId).classList.remove('hidden');
                }

                function showEditProfileForm(userId) {
                    // Logic to show edit profile form
                    alert('Edit Profile Form for user ID: ' + userId + ' would be displayed here.');
                }

                // Show the dashboard by default when the page loads
                document.addEventListener('DOMContentLoaded', function() {
                    showSection('dashboard');
                });

                // Example of search functionality
                document.getElementById('searchButton').addEventListener('click', function() {
                    const searchInput = document.getElementById('searchInput').value;
                    const loadingSpinner = document.getElementById('loadingSpinner');
                    loadingSpinner.classList.remove('hidden');

                    // Simulate a search operation
                    setTimeout(() => {
                        loadingSpinner.classList.add('hidden');
                        // Here you would typically filter the results based on the search input
                        alert('Search completed for: ' + searchInput);
                    }, 2000);
                });

                document.getElementById('clearButton').addEventListener('click', function() {
                    document.getElementById('searchInput').value = '';
                    // Clear results logic can be added here
                });
            </script>
        </div>
    </div>
</body>
</html>