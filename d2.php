<?php
session_start();

// Check if user is logged in and the role is 'student'
if (!isset($_SESSION['user']) || $_SESSION['role'] != 'student') {
    // If not a student, redirect to an error page or login page
    header("Location: login.php");
    exit();
}


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
 ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
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
            <h3 class="ml-6">USER</h3>
            <nav class="flex flex-col space-y-4">
                <a href="#" class="flex items-center px-4 py-2 hover:bg-gray-700 rounded-lg text-white no-underline" id="dashboardLink">
                    <i class="fas fa-tachometer-alt mr-2"></i> Dashboard
                </a>
                <a href="#" class="flex items-center px-4 py-2 hover:bg-gray-700 rounded-lg text-white no-underline" id="applicationFormLink">
                    <i class="fas fa-file-alt mr-2"></i> Application Form
                </a>
                <a href="#" class="flex items-center px-4 py-2 hover:bg-gray-700 rounded-lg text-white no-underline">
                    <i class="fas fa-dollar-sign mr-2"></i> Student Fees
                </a>
            </nav>
        </aside>

        <!-- Main Content -->
        <div class="flex-grow">
            <!-- Top Bar -->
            <header class="flex items-center justify-end bg-white shadow p-3">
                <div class="relative">
                    <button id="profileButton" class="flex items-center text-gray-600 hover:text-indigo-500">
                        Hello, <?php echo htmlspecialchars($full_name); ?> <i class="fas fa-user-circle text-gray-600 text-2xl"></i>
                    </button>
                    <div class="absolute right-0 mt-2 w-48 bg-white text-gray-800 rounded-lg shadow-lg hidden" id="userDropdown">
                        <div class="px-4 py-2 border-b flex items-center">
                            <i class="fas fa-user-circle text-gray-600 text-2xl mr-2"></i>
                            <div>
                                <span id="dropdownUsername" class="text-sm text-gray-500 block"><?php echo htmlspecialchars($full_name); ?></span>
                            </div>
                        </div>
                        <a href="#" onclick="showEditProfileForm(); toggleUserDropdown();" class="block px-4 py-2 border-b hover:bg-indigo-100 flex items-center no-underline">
                            <i class="fas fa-user-edit text-gray-600 mr-2"></i> Edit Profile
                        </a>
                        <a href="logout.php" class="block px-4 py-2 hover:bg-indigo-100 flex items-center no-underline">
                            <i class="fas fa-sign-out-alt text-gray-600 mr-2"></i> Log Out
                        </a>
                    </div>
                </div>
            </header>
            <!-- Main Content Section -->
            <main class="p-6">
                <!-- Dashboard Content -->
                <div id="dashboardContent" class="content-section">
                    <h1 class="text-2xl font-semibold text-left">Welcome Back, <?php echo htmlspecialchars($full_name); ?>!</h1>
                    <br>
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mt-2 rounded">
                        <p class="text-sm">You have not applied for admission. Please fill out the application form.</p>
                    </div>
                </div>

                <!-- Application Form Content -->
                <div id="applicationFormContent" class="content-section hidden">
                    <h1 class="text-2xl font-semibold text-blue-600">Application Form</h1>
                    <div class="bg-white p-6 rounded-lg shadow hover:shadow-lg transition mt-6">
                        <p class="mt-4 text-gray-600">Please fill out the application form below.</p>
                        <form id="biodataForm" class="space-y-8 mt-8" action="submit_form.php" method="POST" enctype="multipart/form-data">
                            <!-- Personal Information Section -->
                            <div class="mt-8">
                                <h3 class="text-lg font-semibold text-gray- 700 border-l-4 border-blue-500 pl-2 mb-4">
                                    <i class="mr-2 text-blue-500"></i> Personal Information
                                </h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div class="space-y-1">
                                        <label for="name" class="block text-gray-700 font-medium">Full Name</label>
                                        <input type="text" id="name" name="name" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" placeholder="Enter your full name" required>
                                    </div>
                                    <div class="space-y-1">
                                        <label for="course_applied" class="block text-gray-700 font-medium">Course Applied</label>
                                        <select id="course_applied" name="course_applied" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" required>
                                            <option value="" disabled selected>Select your course</option>
                                            <option value="science">Science</option>
                                            <option value="commerce">Commerce</option>
                                            <option value="arts">Arts</option>
                                        </select>
                                    </div>

                                    
                                    <div class="space-y-1">
                                        <label for="dob" class="block text-gray-700 font-medium">Date of Birth</label>
                                        <input type="date" id="dob" name="dob" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" required>
                                    </div>
                                    <div class="space-y-1">
                                        <label for="gender" class="block text-gray-700 font-medium">Gender</label>
                                        <select id="gender" name="gender" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" required>
                                            <option value="" disabled selected>Select your gender</option>
                                            <option value="Male">Male</option>
                                            <option value="Female">Female</option>
                                            <option value="Other">Other</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Parents' Details Section -->
                            <div class="mt-8">
                                <h3 class="text-lg font-semibold text-gray-700 border-l-4 border-blue-500 pl-2 mb-4">
                                    <i class="fas fa-users mr-2 text-blue-500"></i> Parents' Details
                                </h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div class="space-y-1">
                                        <label for="fatherName" class="block text-gray-700 font-medium">Father's Name</label>
                                        <input type="text" id="fatherName" name="fatherName" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" placeholder="Enter father's full name" required>
                                    </div>
                                    <div class="space-y-1">
                                        <label for="motherName" class="block text-gray-700 font-medium">Mother's Name</label>
                                        <input type="text" id="motherName" name="motherName" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" placeholder="Enter mother's full name" required>
                                    </div>
                                </div>
                            </div>

                            <!-- Address Details Section -->
                            <div class="mt-8">
                                <h3 class="text-lg font-semibold text-gray-700 border-l-4 border-blue-500 pl-2 mb-4">
                                    <i class="fas fa-home mr-2 text-blue-500"></i> Address
                                </h3>
                                <div class="space-y-1">
                                    <label for="address" class="block text-gray-700 font-medium">Permanent Address</label>
                                    <input type="text" id="address" name="address" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" placeholder="Enter permanent address" required>
                                </div>
                            </div>

                            <!-- Caste and Income Details Section -->
                            <div class="mt-8">
                                <h3 class="text-lg font-semibold text-gray-700 border-l-4 border-blue-500 pl-2 mb-4">
                                    <i class="fas fa-coins mr-2 text-blue-500"></i> Caste and Income
                                </h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div class="space-y-1">
                                        <label for="caste" class="block text-gray-700 font-medium">Caste</label>
                                        <input type="text" id="caste" name="caste" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" placeholder="Enter your caste" required>
 
                                    </div>
                                    <div class="space-y-1">
                                        <label for="income" class="block text-gray-700 font-medium">Annual Family Income</label>
                                        <input type="number" id="income" name="income" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" placeholder="Enter family income" required>
                                    </div>
                                </div>
                            </div>

                            <!-- Previous College Field -->
                            <div class="space-y-1">
                                <label for="previousCollege" class="block text-gray-700 font-medium">Previous College</label>
                                <input type="text" id="previousCollege" name="previousCollege" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" placeholder="Enter previous college name">
                            </div>

                            <!-- Document Upload Section -->
                            <div class="mt-8">
                                <h3 class="text-lg font-semibold text-gray-700 border-l-4 border-blue-500 pl-2 mb-4">
                                    <i class="fas fa-file-upload mr-2 text-blue-500"></i> Upload Documents
                                </h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div class="space-y-1">
                                        <label for="aadhar" class="block text-gray-700">Aadhar Card</label>
                                        <input type="file" id="aadhar" name="aadhar" class="w-full border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" required>
                                    </div>
                                    <div class="space-y-1">
                                        <label for="lc" class="block text-gray-700">Leaving Certificate (LC)</label>
                                        <input type="file" id="lc" name="lc" class="w-full border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" required>
                                    </div>
                                    <div class="space-y-1">
                                        <label for="markSheet" class="block text-gray-700">Board Exam Mark Sheet</label>
                                        <input type="file" id="markSheet" name="markSheet" class="w-full border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" required>
                                    </div>
                                    <div class="space-y-1">
                                        <label for="incomeCert" class="block text-gray-700">Income Certificate</label>
                                        <input type="file" id="incomeCert" name="incomeCert" class="w-full border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" required>
                                    </div>
                                    <div class="space-y-1">
                                        <label for="casteCert" class="block text-gray-700">Caste Certificate</label>
                                        <input type="file" id="casteCert" name="casteCert" class="w-full border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" required>
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="flex justify-center mt-8">
                                <button type="submit" class="px-6 py-3 bg-gradient-to-r from-blue-500 to-blue-700 text-white rounded-lg font-semibold transform hover:scale-105 transition">
                                    Submit Application
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
<!-- Edit Profile Form -->
<div id="editProfileFormContent" class="content-section hidden">
    <h2 class="text-2xl font-semibold mb-4">Edit Profile</h2>
    <div class="bg-white p-6 rounded-lg shadow hover:shadow-lg transition mt-6">
        <h2 class="text-2xl font-semibold mb-4">Edit Profile</h2>
   
        <form id="editProfileForm" action="edit_profile.php" method="POST" class="space-y-6">

            <!-- Name Fields -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="space-y-1">
                    <label for="editFirstName" class="block text-gray-700 font-medium">First Name</label>
                    <input type="text" id="editFirstName" name="firstName" class="w-full px-3 py-2 border rounded-lg" value="<?php echo htmlspecialchars($first_name); ?>" required>
                </div>
                <div class="space-y-1">
                    <label for="editMiddleName" class="block text-gray-700 font-medium">Middle Name</label>
                    <input type="text" id="editMiddleName" name="middleName" class="w-full px-3 py-2 border rounded-lg" value="<?php echo htmlspecialchars($middle_name); ?>">
                </div>
                <div class="space-y-1">
                    <label for="editLastName" class="block text-gray-700 font-medium">Last Name</label>
                    <input type="text" id="editLastName" name="lastName" class="w-full px-3 py-2 border rounded-lg" value="<?php echo htmlspecialchars($last_name); ?>" required>
                </div>
            </div>

            <!-- Contact Details -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-1">
                    <label for="editEmail" class="block text-gray-700 font-medium">Email Address</label>
                    <input type="email" id="editEmail" name="email" class="w-full px-3 py-2 border rounded-lg" value="<?php echo htmlspecialchars($email); ?>" required>
                </div>
                <div class="space-y-1">
                    <label for="editMobile" class="block text-gray-700 font-medium">Mobile Number</label>
                    <input type="tel" id="editMobile" name="mobile" class="w-full px-3 py-2 border rounded-lg" value="<?php echo htmlspecialchars($mobile_number); ?>" required>
                </div>
            </div>
            
            <!-- Aadhar Field -->
            <div class="space-y-1">
                <label for="editAadharNumber" class="block text-gray-700 font-medium">Aadhar Number</label>
                <input type="text" id="editAadharNumber" name="aadharNumber" class="w-full px-3 py-2 border rounded-lg" value="<?php echo htmlspecialchars($aadhar_number); ?>" readonly>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-center mt-6">
                <button type="submit" class="px-6 py-3 bg-blue-500 text-white rounded-lg font-semibold">
                    Save Changes
                </button>
            </div>

        </form>
    </div>
</div>
</main></div></div>    
<script>
        document.addEventListener('DOMContentLoaded', function () {
            const profileButton = document.getElementById('profileButton');
            const userDropdown = document.getElementById('userDropdown');
            const dashboardLink = document.getElementById('dashboardLink');
            const applicationFormLink = document.getElementById('applicationFormLink');
            const dashboardContent = document.getElementById('dashboardContent');
            const applicationFormContent = document.getElementById('applicationFormContent');
            const editProfileFormContent = document.getElementById('editProfileFormContent');

            // Toggle dropdown visibility when profile button is clicked
            profileButton.addEventListener('click', function () {
                userDropdown.classList.toggle('hidden');
            });

            // Show Dashboard by default
            dashboardContent.classList.remove('hidden');

            // Show and hide content sections based on selected links
            dashboardLink.addEventListener('click', function () {
                dashboardContent.classList.remove('hidden');
                applicationFormContent.classList.add('hidden');
                editProfileFormContent.classList.add('hidden');
            });

            applicationFormLink.addEventListener('click', function () {
                applicationFormContent.classList.remove('hidden');
                dashboardContent.classList.add('hidden');
                editProfileFormContent.classList.add('hidden');
            });
        });

        // Show the Edit Profile form and hide others
        function showEditProfileForm() {
            const editProfileFormContent = document.getElementById('editProfileFormContent');
            const dashboardContent = document.getElementById('dashboardContent');
            const applicationFormContent = document.getElementById('applicationFormContent');

            editProfileFormContent.classList.remove('hidden');
            dashboardContent.classList.add('hidden');
            applicationFormContent.classList.add('hidden');
        }

        // Hide the user dropdown after selecting "Edit Profile"
        function toggleUserDropdown() {
            const userDropdown = document.getElementById('userDropdown');
            userDropdown.classList.add('hidden');
        }
    </script>

</body>
</html>