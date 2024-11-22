<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 font-sans leading-normal tracking-normal">
    <div class="flex">

        <!-- Sidebar -->
        <div class="w-64 bg-black text-white h-screen p-6">
            <div class="text-white text-lg font-semibold mb-6">User</div>
            <nav class="flex flex-col space-y-4">
                <a href="#" class="flex items-center px-4 py-2 hover:bg-gray-800 rounded-lg text-white no-underline">
                    <i class="fas fa-tachometer-alt mr-2"></i> Dashboard
                </a>
                <a href="#" class="flex items-center px-4 py-2 hover:bg-gray-800 rounded-lg text-white no-underline">
                    <i class="fas fa-file-alt mr-2"></i> Application Form
                </a>
                <a href="#" class="flex items-center px-4 py-2 hover:bg-gray-800 rounded-lg text-white no-underline">
                    <i class="fas fa-dollar-sign mr-2"></i> Student Fees
                </a>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="flex-grow">
            <!-- Top Bar -->
            <header class="flex items-center justify-end bg-white shadow p-3">
                <!-- Profile Dropdown -->
                <div class="relative">
                    <button id="profileButton" class="flex items-center text-gray-600 hover:text-indigo-500">
                        <!-- Profile Icon (instead of image) -->
                        <i class="fas fa-user-circle text-gray-600 text-2xl"></i>
                    </button>
                    <div class="absolute right-0 mt-2 w-48 bg-white text-gray-800 rounded-lg shadow-lg hidden" id="userDropdown">
                        <div class="px-4 py-2 border-b flex items-center">
                            <!-- Profile Icon (instead of image) -->
                            <i class="fas fa-user-circle text-gray-600 text-2xl mr-2"></i>
                            <div>
                                <span id="dropdownUsername" class="text-sm text-gray-500 block">John Doe</span>
                            </div>
                        </div>
                        <a href="edit-profile.php" class="block px-4 py-2 border-b hover:bg-indigo-100 flex items-center no-underline">
                            <i class="fas fa-user-edit text-gray-600 mr-2"></i> Edit Profile
                        </a>
                        <a href="logout.php" class="block px-4 py-2 hover:bg-indigo-100 flex items-center no-underline">
                            <i class="fas fa-sign-out-alt text-gray-600 mr-2"></i> Log Out
                        </a>
                    </div>
                </div>
            </header>

            <!-- Welcome Message and Content Section -->
            <main class="p-6">
                <!-- Welcome Message and Application Status -->
                <div class="mt-4 mb-4 px-6">
                    <h1 class="text-2xl font-semibold text-left">Welcome Back, John Doe!</h1>
                    <hr class="border-black-300 mt-2 mb-4">
                    
                    <!-- Admission Status Message -->
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mt-2 rounded">
                        <p class="text-sm">You have not applied for admission. Please fill out the application form.</p>
                    </div>
                    
                    <!-- Application Status Card -->
                    <div class="bg-white p-6 rounded-lg shadow hover:shadow-lg transition mt-6">
                        <h2 class="text-xl font-semibold mb-2">Application Status</h2>
                        <p class="text-gray-700">View and manage your application status here.</p>
                        <button class="mt-4 bg-gray-800 text-white px-3 py-2 rounded hover:bg-gray-900 focus:outline-none focus:ring focus:ring-gray-300">
                            View Status
                        </button>
                    </div>
                </div>

                <!-- Content Cards (Additional content if needed) -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mt-6">
                    <!-- Additional content cards can be added here -->
                </div>
            </main>
        </div>
    </div>

    <!-- JavaScript for Profile Dropdown -->
    <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function() {
            const profileButton = document.getElementById('profileButton');
            const userDropdown = document.getElementById('userDropdown');
            const username = "John Doe"; // Replace with dynamic username if needed

            // Set the dropdown username
            document.getElementById('dropdownUsername').textContent = username;

            profileButton.addEventListener('click', function(event) {
                event.preventDefault(); // Prevent default button behavior
                userDropdown.classList.toggle('hidden'); // Toggle dropdown visibility
            });

            // Close the dropdown if clicked outside
            window.addEventListener('click', function(event) {
                if (!profileButton.contains(event.target) && !userDropdown.contains(event.target)) {
                    userDropdown.classList.add('hidden'); // Hide dropdown
                }
            });
        });
    </script>
</body>
</html>