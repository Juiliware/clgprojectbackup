<?php
// learnmore.php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "college_project";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the college ID is set in the URL
if (isset($_GET['id'])) {
    $college_id = $_GET['id'];

    // Fetch college data based on ID
    $sql = "SELECT * FROM colleges WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $college_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $college = $result->fetch_assoc();
    } else {
        echo "No college found with this ID.";
        exit();
    }
    $stmt->close();

    // Fetch unique categories for filtering gallery images
    $sql = "SELECT DISTINCT category FROM gallery WHERE college_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $college_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Prepare an array to store the categories
    $categories = [];
    while ($row = $result->fetch_assoc()) {
        $categories[] = $row['category'];
    }
    $stmt->close();

    // Fetch facilities from the database for the specific college
    $sql = "SELECT * FROM facilities WHERE college_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $college_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Prepare an array to store facilities
    $facilities = [];
    while ($row = $result->fetch_assoc()) {
        $facilities[] = $row;
    }
    $stmt->close();

    // Fetch alumni data based on college ID
    $sql = "SELECT * FROM alumni WHERE college_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $college_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Prepare an array to store alumni data
    $alumni = [];
    while ($row = $result->fetch_assoc()) {
        $alumni[] = $row;
    }
    $stmt->close();

   // Fetch images from slideimg table
$sql = "SELECT image_path FROM slideimg";
$result = $conn->query($sql);

$images = [];
if ($result->num_rows > 0) {
    // Store images in an array
    while ($row = $result->fetch_assoc()) {
        $images[] = $row['image_path'];
    }
} else {
    echo "No images found in the slideimg table.";
    exit();
}

 // Fetch gallery images based on college ID
    $sql = "SELECT * FROM gallery WHERE college_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $college_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Prepare an array to store gallery images
    $gallery_images = [];
    while ($row = $result->fetch_assoc()) {
        $gallery_images[] = $row;
    }
    $stmt->close();
} else {
    echo "No ID provided.";
    exit();
}

// Close the connection after all queries are executed
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>College Information Portal</title>
       <!-- Tailwind CSS -->
       <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <!-- Font Awesome for Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <!-- Include Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Include Owl Carousel CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css">
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    

  
    </style>
</head>

<body class="bg-gray-100 text-gray-800">
   <!-- Header Section -->
<header style="z-index: 1; position: relative;">
    <div class="col-md-12 text-white bg-black py-2">
        <!-- Contact and Social Media -->
        <div class="container flex flex-wrap justify-between items-center text-center sm:text-left">
            <!-- Contact Info -->
            <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-4 w-full sm:w-auto">
                <a href="tel:+1234567890" class="text-gray-200 hover:text-indigo-500 flex items-center justify-center">
                    <i class="fas fa-phone-alt mr-2"></i> +1234567890
                </a>
                <a href="mailto:info@college.com" class="text-gray-200 hover:text-indigo-500 flex items-center justify-center">
                    <i class="fas fa-envelope mr-2"></i> info@college.com
                </a>
            </div>
            <!-- Social Media Links -->
            <div class="flex space-x-4 mt-2 sm:mt-0 w-full sm:w-auto justify-center">
                <a href="https://facebook.com" class="text-blue-600 hover:text-indigo-500" aria-label="Facebook">
                    <i class="fab fa-facebook fa-lg"></i>
                </a>
                <a href="https://linkedin.com" class="text-blue-700 hover:text-indigo-500" aria-label="LinkedIn">
                    <i class="fab fa-linkedin fa-lg"></i>
                </a>
                <a href="https://instagram.com" class="text-pink-500 hover:text-indigo-500" aria-label="Instagram">
                    <i class="fab fa-instagram fa-lg"></i>
                </a>
                <a href="https://twitter.com" class="text-blue-400 hover:text-indigo-500" aria-label="Twitter">
                    <i class="fab fa-twitter fa-lg"></i>
                </a>
                <a href="https://youtube.com" class="text-red-600 hover:text-indigo-500" aria-label="YouTube">
                    <i class="fab fa-youtube fa-lg"></i>
                </a>
            </div>
        </div>
    </div>
</header>

<!-- Navigation Section -->
<nav id="navbar" class="sticky top-0 flex justify-between items-center py-2 px-4 bg-white shadow-md z-50">
    <a href="#" class="text-xl font-bold text-indigo-600">College Info Portal</a>
    <button id="mobileMenuButton" class="block md:hidden text-gray-600 focus:outline-none" aria-label="Toggle Menu">
        <i class="fas fa-bars"></i>
    </button>
    <ul id="navMenu" class="hidden md:flex space-x-6">
        <li><a href="#" class="text-sm text-gray-600 hover:text-indigo-500">Home</a></li>
        <li class="relative group">
            <a href="#" class="text-sm text-gray-600 hover:text-indigo-500">Colleges</a>
            <ul class="absolute left-0 hidden group-hover:block bg-white shadow-lg rounded-lg mt-2 py-2 w-48">
                <li><a href="#" class="block px-4 py-2 hover:bg-indigo-100">Public Colleges</a></li>
                <li><a href="#" class="block px-4 py-2 hover:bg-indigo-100">Private Colleges</a></li>
                <li><a href="#" class="block px-4 py-2 hover:bg-indigo-100">Community Colleges</a></li>
            </ul>
        </li>
        <li><a href="#" class="text-sm text-gray-600 hover:text-indigo-500">About Us</a></li>
        <li><a href="#" class="text-sm text-gray-600 hover:text-indigo-500">Contact Us</a></li>
    </ul>
    <div class="flex items-center space-x-2">
        <!-- Search Bar Toggle Button -->
        <!-- <button id="toggleSearch" class="text-gray-600 hover:text-indigo-500 focus:outline-none" aria-label="Toggle Search">
            <i class="fas fa-search"></i>
        </button>
         -->
        <!-- <div id="searchBar" class="relative hidden md:block">
            <input type="text" id="searchInput" placeholder="Search..."
                class="border border-gray-300 rounded-md py-1 pl-3 pr-8 focus:outline-none focus:ring focus:ring-indigo-200"
                aria-label="Search" />
            <i class="fas fa-search absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
        </div> -->

<!-- Login Button -->
<button id="mainBtn" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 focus:outline-none">
    Login
</button>


<!-- Admin Button -->
<button id="adminBtn" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 focus:outline-none">
    Admin
</button>


</div>
</nav>

<!-- Combined Modal for Login, Register, and Forgot Password -->
<div id="mainModal" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center hidden z-50">
    <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-lg relative">
        <!-- Close Button -->
        <button class="close absolute top-2 right-2 text-gray-500 hover:text-gray-700">&times;</button>

        <!-- Login Form -->
        <div id="loginForm" class="block">
            <h2 class="text-2xl font-semibold text-center mb-6 text-gray-800">Login</h2>
            <form method="POST" action="login.php"> <!-- Pointing to login.php -->
                <div class="mb-4">
                    <label for="aadharLogin" class="block text-sm font-medium text-gray-700">Aadhar number:</label>
                    <input type="text" id="aadharLogin" name="aadharLogin" required pattern="\d{12}" maxlength="12" class="mt-1 block w-full border border-gray-300 rounded-md p-2 focus:outline-none focus:ring focus:ring-indigo-200" placeholder="12-digit Aadhar number" title="Please enter a valid 12-digit Aadhar number">
                </div>
                <div class="mb-4">
                    <label for="loginPassword" class="block text-sm font-medium text-gray-700">Password:</label>
                    <input type="password" id="loginPassword" name="loginPassword" required class="mt-1 block w-full border border-gray-300 rounded-md p-2 focus:outline-none focus:ring focus:ring-indigo-200" placeholder="Password">
                </div>

                <button type="submit" class="w-full bg-green-600 text-white py-2 rounded-md hover:bg-green-700 focus:outline-none focus:ring focus:ring-green-200">Login</button>
            </form>

            <!-- Additional Links -->
            <div class="mt-4 text-center text-sm text-gray-600">
                <p>Don't have an account? <a href="#" id="toggleRegister" class="text-blue-500 hover:text-blue-700">Register</a></p>
                <p><a href="#" id="toggleForgotPassword" class="text-blue-500 hover:text-blue-700">Forgot Password?</a></p>
            </div>
        </div>

        <!-- Registration Form -->
        <div id="registerForm" class="hidden">
            <h2 class="text-2xl font-semibold text-center mb-6 text-gray-800">Register</h2>
            <form method="POST" action="register.php"> <!-- Pointing to register.php -->
                <div class="flex space-x-2 mb-4">
                    <div class="w-1/3">
                        <label for="firstName" class="block text-sm font-medium text-gray-700">First name:</label>
                        <input type="text" id="firstName" name="firstName" required class="mt-1 block w-full border border-gray-300 rounded-md p-2 focus:outline-none focus:ring focus:ring-indigo-200" placeholder="First name">
                    </div>
                    <div class="w-1/3">
                        <label for="middleName" class="block text-sm font-medium text-gray-700">Middle name:</label>
                        <input type="text" id="middleName" name="middleName" class="mt-1 block w-full border border-gray-300 rounded-md p-2 focus:outline-none focus:ring focus:ring-indigo-200" placeholder="Middle name">
                    </div>
                    <div class="w-1/3">
                        <label for="lastName" class="block text-sm font-medium text-gray-700">Last name:</label>
                        <input type="text" id="lastName" name="lastName" required class="mt-1 block w-full border border-gray-300 rounded-md p-2 focus:outline-none focus:ring focus:ring-indigo-200" placeholder="Last name">
                    </div>
                </div>

                <div class="flex space-x-2 mb-4">
                    <div class="w-1/2">
                        <label for="email" class="block text-sm font-medium text-gray-700">Email address:</label>
                        <input type="email" id="email" name="email" required class="mt-1 block w-full border border-gray-300 rounded-md p-2 focus:outline-none focus:ring focus:ring-indigo-200" placeholder="Email address">
                    </div>
                    <div class="w-1/2">
                        <label for="mobileNumber" class="block text-sm font-medium text-gray-700">Mobile number:</label>
                        <input type="tel" id="mobileNumber" name="mobileNumber" required pattern="\d{10}" maxlength="10" class="mt-1 block w-full border border-gray-300 rounded-md p-2 focus:outline-none focus:ring focus:ring-indigo-200" placeholder="10-digit mobile number" title="Please enter a valid 10-digit mobile number">
                    </div>
                </div>

                <div class="mb-4">
                    <label for="aadharNumber" class="block text-sm font-medium text-gray-700">Aadhar number:</label>
                    <input type="text" id="aadharNumber" name="aadharNumber" required pattern="\d{12}" maxlength="12" class="mt-1 block w-full border border-gray-300 rounded-md p-2 focus:outline-none focus:ring focus:ring-indigo-200" placeholder="12-digit Aadhar number" title="Please enter a valid 12-digit Aadhar number">
                </div>

                <div class="flex space-x-2 mb-4">
                    <div class="w-1/2">
                        <label for="password" class="block text-sm font-medium text-gray-700">Password:</label>
                        <input type="password" id="password" name="password" required class="mt-1 block w-full border border-gray-300 rounded-md p-2 focus:outline-none focus:ring focus:ring-indigo-200" placeholder="Password">
                    </div>
                    <div class="w-1/2">
                        <label for="confirmPassword" class="block text-sm font-medium text-gray-700">Confirm password:</label>
                        <input type="password" id="confirmPassword" name="confirmPassword" required class="mt-1 block w-full border border-gray-300 rounded-md p-2 focus:outline-none focus:ring focus:ring-indigo-200" placeholder="Confirm password">
                    </div>
                </div>

                <button type="submit" class="w-full bg-indigo-600 text-white py-2 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring focus:ring-indigo-200">Register</button>
            </form>

            <!-- Back to Login link -->
            <div class="mt-4 text-center text-sm text-gray-600">
                <p>Already have an account? <a href="#" id="toggleLogin" class="text-blue-500 hover:text-blue-700">Login</a></p>
            </div>
        </div>

        <!-- Forgot Password Form -->
        <div id="forgotPasswordForm" class="hidden">
            <h2 class="text-2xl font-semibold text-center mb-6 text-gray-800">Forgot Password</h2>
            <form id="forgotPasswordFormSubmit" method="POST" action="forgot_password.php">
                <div class="mb-4">
                    <label for="aadharLogin" class="block text-sm font-medium text-gray-700">Aadhar Number:</label>
                    <input type="text" id="aadharLogin" name="aadharLogin" required pattern="\d{12}" maxlength="12" class="mt-1 block w-full border border-gray-300 rounded-md p-2 focus:outline-none focus:ring focus:ring-indigo-200" placeholder="12-digit Aadhar Number" title="Please enter a valid 12-digit Aadhar number">
                </div>
                <div class="mb-4">
                    <label for="existingPassword" class="block text-sm font-medium text-gray-700">Existing Password:</label>
                    <input type="password" id="existingPassword" name="existingPassword" required class="mt-1 block w-full border border-gray-300 rounded-md p-2 focus:outline-none focus:ring focus:ring-indigo-200" placeholder="Existing Password">
                </div>
                <div class="mb-4">
                    <label for="newPassword" class="block text-sm font-medium text-gray-700">New Password:</label>
                    <input type="password" id="newPassword" name="newPassword" required class="mt-1 block w-full border border-gray-300 rounded-md p-2 focus:outline-none focus:ring focus:ring-indigo-200" placeholder="New Password">
                </div>
                <div class="mb-4">
                    <label for="confirmNewPassword" class="block text-sm font -medium text-gray-700">Confirm New Password:</label>
                    <input type="password" id="confirmNewPassword" name="confirmNewPassword" required class="mt-1 block w-full border border-gray-300 rounded-md p-2 focus:outline-none focus:ring focus:ring-indigo-200" placeholder="Confirm New Password">
                </div>

                <button type="submit" class="w-full bg-indigo-600 text-white py-2 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring focus:ring-indigo-200">Submit</button>
            </form>

            <!-- Back to Login link -->
            <div class="mt-4 text-center text-sm text-gray-600">
                <p><a href="#" id="toggleLoginFromForgot" class="text-blue-500 hover:text-blue-700">Back to Login</a></p>
            </div>
        </div>
    </div>
</div>

<!-- Combined Modal for Admin Login -->
<div id="adminModal" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center hidden z-50">
    <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-lg relative">
        <!-- Close Button -->
        <button class="admin-close absolute top-2 right-2 text-gray-500 hover:text-gray-700">&times;</button>

        <!-- Admin Login Form -->
        <div id="adminLoginForm" class="block">
            <h2 class="text-2xl font-semibold text-center mb-6 text-gray-800">Admin Login</h2>
            <form method="POST" action="admin_login.php">
                <div class="mb-4">
                    <label for="adminEmail" class="block text-sm font-medium text-gray-700">Email ID / UID:</label>
                    <input type="text" id="adminEmail" name="adminEmail" required class="mt-1 block w-full border border-gray-300 rounded-md p-2 focus:outline-none focus:ring focus:ring-indigo-200" placeholder="Email ID / UID">
                </div>
                <div class="mb-4">
                    <label for="adminLoginPassword" class="block text-sm font-medium text-gray-700">Password:</label>
                    <input type="password" id="adminLoginPassword" name="adminLoginPassword" required class="mt-1 block w-full border border-gray-300 rounded-md p-2 focus:outline-none focus:ring focus:ring-indigo-200" placeholder="Password">
                </div>

                <button type="submit" class="w-full bg-green-600 text-white py-2 rounded-md hover:bg-green-700 focus:outline-none focus:ring focus:ring-green-200">Login</button>
            </form>
        </div>
    </div>
</div>
<script>
// Toggle between forms for Admin Modal
document.getElementById("adminBtn").onclick = function () {
    document.getElementById("adminModal").classList.remove("hidden");
    document.getElementById("adminLoginForm").classList.remove("hidden");
};

document.querySelector(".admin-close").onclick = function () {
    document.getElementById("adminModal").classList.add("hidden");
};
</script>




<script>
// Toggle between forms
document.getElementById("mainBtn").onclick = function () {
    document.getElementById("mainModal").classList.remove("hidden");
    document.getElementById("loginForm").classList.remove("hidden");
};

document.querySelector(".close").onclick = function () {
    document.getElementById("mainModal").classList.add("hidden");
};

document.getElementById("toggleRegister").onclick = function () {
    document.getElementById("loginForm").classList.add("hidden");
    document.getElementById("registerForm").classList.remove("hidden");
};

document.getElementById("toggleLogin").onclick = function () {
    document.getElementById("registerForm").classList.add("hidden");
    document.getElementById("loginForm").classList.remove("hidden");
};

document.getElementById("toggleForgotPassword").onclick = function () {
    document.getElementById("loginForm").classList.add("hidden");
    document.getElementById("forgotPasswordForm").classList.remove("hidden");
};

document.getElementById("toggleLoginFromForgot").onclick = function () {
    document.getElementById("forgotPasswordForm").classList.add("hidden");
    document.getElementById("loginForm").classList.remove("hidden");
};
</script>



<section class="bg-gray-900 text-white py-4">
    <div class="container mx-auto px-4">
        <div class="flex flex-col md:flex-row md:space-x-8 space-y-4 md:space-y-0">
            <!-- Logo Section -->
            <div class="w-full md:w-1/4">
                <h5 class="font-bold text-lg mb-2">Our Logo</h5>
                <a href="#">
                    <img src="path/to/logo.svg" alt="Your Project Logo" class="h-10 mb-2">
                </a>
                <p class="text-gray-400 text-sm">Your tagline or description.</p>
            </div>
            <!-- Quick Links -->
            <div class="w-full md:w-1/4">
                <h5 class="font-bold text-lg mb-2">Quick Links</h5>
                <ul class="space-y-1">
                    <li>
                        <a href="#" class="text-gray-400 hover:text-indigo-500 transition duration-300">Contact Us</a>
                    </li>
                    <li>
                        <a href="#" class="text-gray-400 hover:text-indigo-500 transition duration-300">About Us</a>
                    </li>
                    <li>
                        <a href="#" class="text-gray-400 hover:text-indigo-500 transition duration-300">Privacy Policy</a>
                    </li>
                    <li>
                        <a href="#" class="text-gray-400 hover:text-indigo-500 transition duration-300">Terms & Conditions</a>
                    </li>
                </ul>
            </div>
            <!-- Follow Us -->
            <div class="w-full md:w-1/4">
                <h5 class="font-bold text-lg mb-2">Follow Us</h5>
                <div class="flex space-x-4">
                    <a href="#" class="text-gray-400 hover:text-indigo-500 transition duration-300" aria-label="Facebook">
                        <i class="bi bi-facebook"></i>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-indigo-500 transition duration-300" aria-label="Twitter">
                        <i class="bi bi-twitter"></i>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-indigo-500 transition duration-300" aria-label="Instagram">
                        <i class="bi bi-instagram"></i>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-indigo-500 transition duration-300" aria-label="LinkedIn">
                        <i class="bi bi-linkedin"></i>
                    </a>
                </div>
            </div>
            <!-- Contact Information -->
            <div class="w-full md:w-1/4">
                <h5 class="font-bold text-lg mb-2">Contact Info</h5>
                <div class="flex items-center mb-1">
                    <i class="bi bi-geo-alt-fill text-indigo-500"></i>
                    <a href="https://www.google.com/maps?q=Your+Address"
                        class="text-gray-400 hover:text-indigo-500 transition duration-300 ml-2">Your Address</a>
                </div>
                <div class="flex items-center mb-1">
                    <i class="bi bi-envelope-fill text-indigo-500"></i>
                    <a href="mailto:your-email@example.com"
                        class="text-gray-400 hover:text-indigo-500 transition duration-300 ml-2">your-email@example.com</a>
                </div>
                <div class="flex items-center">
                    <i class="bi bi-telephone-fill text-indigo-500"></i>
                    <a href="tel:+1234567890"
                        class="text-gray-400 hover:text-indigo-500 transition duration-300 ml-2">+1 234 567 890</a>
                </div>
            </div>
        </div>
        <div class="text-center text-gray-500 mt-6">
    <p class="text-center sm:text-left">Designed by 
        <a href="https://yourwebsite.com" class="text-indigo-400 hover:text-indigo-500 transition duration-300">Paarsh Infotech Pvt. Ltd.</a>
    </p>
    <p class="text-sm text-center sm:text-left">Copyright © 2024 - 2025 College Portal. All rights reserved.</p>
</div>
    </div>
</section>


    <!-- Include jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Include Bootstrap JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
    <!-- Include Owl Carousel JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>
</body></html>