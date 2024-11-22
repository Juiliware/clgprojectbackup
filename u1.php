<?php
session_start();

// Check if user is logged in and the role is 'student'
if (!isset($_SESSION['user']) || $_SESSION['role'] != 'student') {
    header("Location: login.php");
    exit();
}

$full_name = isset($_SESSION['full_name']) ? $_SESSION['full_name'] : 'Guest';
$student_id = $_SESSION['user']['id'];

$host = 'localhost';
$dbname = 'college_project';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Could not connect to the database: " . $e->getMessage());
}

// Retrieve all colleges
$query = "SELECT * FROM colleges";
$stmt = $pdo->query($query);
$colleges = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch the applied colleges for the student
$query_applied = "SELECT colleges.* FROM colleges
                  JOIN college_applications ON colleges.id = college_applications.college_id
                  WHERE college_applications.student_id = :student_id";
$stmt_applied = $pdo->prepare($query_applied);
$stmt_applied->execute(['student_id' => $student_id]);
$applied_colleges = $stmt_applied->fetchAll(PDO::FETCH_ASSOC);

// Check if the student has already filled the application form
$query_application_status = "SELECT COUNT(*) FROM college_applications WHERE student_id = :student_id";
$stmt_application_status = $pdo->prepare($query_application_status);
$stmt_application_status->execute(['student_id' => $student_id]);
$application_status_count = $stmt_application_status->fetchColumn();

// Handle college application submission
$error_message = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['college_ids'])) {
    if ($application_status_count > 0) {
        $error_message = "You have already submitted your application.";
    } else {
        $selected_colleges = $_POST['college_ids'];
        $courseApplied = $_POST['courseApplied'] ?? '';

        // Check if the user is already applied to 5 colleges
        if (count($applied_colleges) + count($selected_colleges) <= 5) {
            foreach ($selected_colleges as $college_id) {
                // Insert new application into college_applications table
                $query_insert = "INSERT INTO college_applications (student_id, college_id, status, course_applied) VALUES (:student_id, :college_id, 'applied', :course_applied)";
                $stmt_insert = $pdo->prepare($query_insert);
                $stmt_insert->execute(['student_id' => $student_id, 'college_id' => $college_id, 'course_applied' => $courseApplied]);
            }

            // Refresh applied colleges list
            $stmt_applied->execute(['student_id' => $student_id]);
            $applied_colleges = $stmt_applied->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $error_message = "You have already applied to 5 colleges.";
        }
    }
}

// Calculate application progress
$progress = 0;
if (!empty($_SESSION['user']['first_name']) && !empty($_SESSION['user']['last_name'])) {
    $progress += 30; // Profile completed
}
if ($application_status_count > 0) {
    $progress += 40; // Application form filled
}
// Assuming you have a way to check if fees are submitted
// Uncomment and modify the following line according to your application logic
// if (/* condition to check if fees are submitted */) {
//     $progress += 30; // Fees submitted
// }

// Insert course applied into biodata table
if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($courseApplied)) {
    $query_biodata_update = "UPDATE biodata SET course_applied = :course_applied WHERE student_id = :student_id";
    $stmt_biodata_update = $pdo->prepare($query_biodata_update);
    $stmt_biodata_update->execute(['course_applied' => $courseApplied, 'student_id' => $student_id]);
}

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
</head>
<body class="font-sans antialiased bg-gray-100 min-h-screen">
    <div class="flex">
        <aside class="w-64 bg-gray-800 text-white min-h-screen p-4">
            <h3 class="ml-6">USER</h3>
            <nav class="flex flex-col space-y-4">
                <a href="#" class="flex items-center px-4 py-2 hover:bg-gray-700 rounded-lg text-white no-underline" id="dashboardLink">
                    <i class="fas fa-tachometer-alt mr-2"></i> Dashboard
                </a>
                <a href="#" class="flex items-center px-4 py-2 hover:bg-gray-800 rounded-lg text-white no-underline" id="applicationFormLink">
                    <i class="fas fa-file-alt mr-2"></i> Application Form
                </a>
                <a href="#" class="flex items-center px-4 py-2 hover:bg-gray-800 rounded-lg text-white no-underline" id="studentFeesLink">
                    <i class="fas fa-dollar-sign mr-2"></i> Student Fees
                </a>
                <a href="logout.php" class="flex items-center px-4 py-2 hover:bg-gray-700 rounded-lg text-white no-underline">
                    <i class="fas fa-sign-out-alt mr-2"></i> Log Out
                </a>
            </nav>
        </aside>

        <div class="flex-grow">
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
                        <a href="#" onclick="showEditProfileForm(); toggleUser Dropdown();" class="block px-4 py-2 border-b hover:bg-indigo-100 flex items-center no-underline">
                            <i class="fas fa-user-edit text-gray-600 mr-2"></i> Edit Profile
                        </a>
                        <a href="logout.php" class="block px-4 py-2 hover:bg-indigo-100 flex items-center no-underline">
                            <i class="fas fa-sign-out-alt text-gray-600 mr-2"></i> Log Out
                        </a>
                    </div>
                </div>
            </header>

            <main class="p-6">
                <div id="dashboardContent" class="content-section">
                    <h1 class="text-2xl font-semibold text-left">Welcome Back, <?php echo htmlspecialchars($full_name); ?>!</h1>
                    <div class="mt-4">
                        <h2 class="text-lg font-semibold text-gray-700">Application Status</h2>
                        <div class="relative w-full bg-gray-200 rounded-full h-4">
                            <div id="progressBar" class="absolute top-0 left-0 h-4 bg-blue-600 rounded-full" style="width: <?php echo $progress; ?>%;"></div>
                        </div>
                        <p id="progressText" class="text-sm text-gray-600 mt -1"> <?php echo $progress; ?>% completed</p>
                    </div>
                    <br>

                    <div class="flex space-x-4">
                        <div class="bg-white border border-gray-200 rounded-lg shadow-md p-6 w-1/2">
                            <h2 class="text-xl font-semibold mb-4">Apply to a College</h2>
                            <p class="text-gray-700 mb-4">You can apply to up to 5 colleges.</p>

                            <?php if (!empty($error_message)): ?>
                                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mt-2 rounded">
                                    <p class="text-sm"><?php echo htmlspecialchars($error_message); ?></p>
                                </div>
                            <?php endif; ?>

                            <form action="" method="POST">
                                <div class="space-y-2">
                                    <?php foreach ($colleges as $college): ?>
                                        <?php if (!in_array($college['id'], array_column($applied_colleges, 'id'))): ?>
                                            <div class="flex items-center">
                                                <input type="checkbox" name="college_ids[]" value="<?php echo $college['id']; ?>" class="mr-2" id="college_<?php echo $college['id']; ?>">
                                                <label for="college_<?php echo $college['id']; ?>" class="text-gray-800"><?php echo htmlspecialchars($college['name']); ?></label>
                                            </div>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </div>
                                <button type="submit" class="mt-4 bg-blue-500 text-white px-4 py-2 rounded-md">Apply</button>
                            </form>
                        </div>

                        <div class="bg-white border border-gray-200 rounded-lg shadow-md p-6 w-1/2">
                            <h2 class="text-xl font-semibold mb-4">Applied Colleges</h2>
                            <p class="text-gray-700 mb-4">You have applied to <?php echo count($applied_colleges); ?> colleges.</p>

                            <ul class="space-y-2">
                                <?php foreach ($applied_colleges as $college): ?>
                                    <li class="bg-gray-100 p-2 rounded-md">
                                        <p class="font-semibold text-gray-800">
                                            <?php echo htmlspecialchars($college['name']); ?>
                                        </p>
                                    </li>
                                <?php endforeach; ?>
                            </ul>

                            <?php if (count($applied_colleges) === 0): ?>
                                <p class="text-sm text-gray-500">You have not applied to any college yet. Please fill out the application form.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div id="applicationFormContent" class="content-section hidden">
                    <h1 class="text-2xl font-semibold text-blue-600">Application Form</h1>
                    <div class="bg-white p-6 rounded-lg shadow hover:shadow-lg transition mt-6">
                        <p class="mt-4 text-gray-600">Please fill out the application form below.</p>
                        <form id="biodataForm" class="space-y-8 mt-8" action="submit_form.php" method="POST" enctype="multipart/form-data">
                            <div class="mt-8">
                                <h3 class="text-lg font-semibold text-gray-700 border-l-4 border-blue-500 pl-2 mb-4">
                                    <i class="mr-2 text-blue-500"></i> Personal Information
                                </h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div class="space-y-1">
                                        <label for="name" class="block text-gray-700 font-medium">Full Name</label>
                                        <input type="text" id="name" name="name" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" placeholder="Enter your full name" required>
                                    </div>
                                    <div class="space-y-1">
                                        <label for="courseApplied" class="block text-gray-700 font-medium">Course Applied</label>
                                        <select id="courseApplied" name="courseApplied" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" required>
                                            <option value="" disabled selected>Select your course</option>
                                            <option value="Science">Science</option>
                                            <option value="Commerce">Commerce</option>
                                            <option value=" Arts">Arts</option>
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

                            <div class="mt-8">
                                <h3 class="text-lg font-semibold text-gray-700 border-l-4 border-blue-500 pl-2 mb-4">
                                    <i class="fas fa-home mr-2 text-blue-500"></i> Address
                                </h3>
                                <div class="space-y-1">
                                    <label for="address" class="block text-gray-700 font-medium">Permanent Address</label>
                                    <input type="text" id="address" name="address" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" placeholder="Enter permanent address" required>
                                </div>
                            </div>

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

                            <div class="space-y-1">
                                <label for="previousCollege" class="block text-gray-700 font-medium">Previous College</label>
                                <input type="text" id="previousCollege" name="previousCollege" class="w-full php
px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" placeholder="Enter previous college name">
                            </div>

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

                            <div class="flex justify-center mt-8">
                                <button type="submit" class="px-6 py-3 bg-gradient-to-r from-blue-500 to-blue-700 text-white rounded-lg font-semibold transform hover:scale-105 transition">
                                    Submit Application
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <div id="editProfileFormContent" class="content-section hidden">
                    <h2 class="text-2xl font-semibold mb-4">Edit Profile</h2>
                    <div class="bg-white p-6 rounded-lg shadow hover:shadow-lg transition mt-6">
                        <h2 class="text-2xl font-semibold mb-4">Edit Profile</h2>
                        <form id="editProfileForm" action="edit_profile.php" method="POST" class="space-y-6">
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

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                php
                                <div class="space-y-1">
                                    <label for="editEmail" class="block text-gray-700 font-medium">Email Address</label>
                                    <input type="email" id="editEmail" name="email" class="w-full px-3 py-2 border rounded-lg" value="<?php echo htmlspecialchars($email); ?>" required>
                                </div>
                                <div class="space-y-1">
                                    <label for="editMobile" class="block text-gray-700 font-medium">Mobile Number</label>
                                    <input type="tel" id="editMobile" name="mobile" class="w-full px-3 py-2 border rounded-lg" value="<?php echo htmlspecialchars($mobile_number); ?>" required>
                                </div>
                            </div>

                            <div class="space-y-1">
                                <label for="editAadharNumber" class="block text-gray-700 font-medium">Aadhar Number</label>
                                <input type="text" id="editAadharNumber" name="aadharNumber" class="w-full px-3 py-2 border rounded-lg" value="<?php echo htmlspecialchars($aadhar_number); ?>" readonly>
                            </div>

                            <div class="flex justify-center mt-6">
                                <button type="submit" class="px-6 py-3 bg-blue-500 text-white rounded-lg font-semibold">
                                    Save Changes
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <div id="studentFeesContent" class="content-section hidden">
                    <div class="bg-white p-6 rounded-lg shadow-lg max-w-4xl w-full hover:shadow-xl transition-shadow duration-300 ease-in-out">
                        <h2 class="text-2xl font-semibold mb-6 text-center text-gray-800">Submit Fees</h2>
                        <form class="grid grid-cols-1 md:grid-cols-2 gap-6 items-center">
                            <div>
                                <label for="paymentAmount" class="block text-gray-700 font-medium mb-2">Payment Amount</label>
                                <input type="number" id="paymentAmount" name="paymentAmount" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring focus:ring-blue-200 focus:outline-none transition-transform duration-300 transform hover:scale-105">
                            </div>

                            <div>
                                <label for="modeOfPayment" class="block text-gray-700 font-medium mb-2">Mode of Payments</label>
                                <select id="modeOfPayment" name="modeOfPayment" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring focus:ring-blue-200 focus:outline-none transition-transform duration-300 transform hover:scale-105">
                                    <option value="" disabled selected>Choose Mode of Payment</option>
                                    <option value="debitCard">Debit Card</option>
                                    <option value="creditCard">Credit Card</option>
                                    <option value="eWallet">E-Wallet</option>
                                    <option value="upi">UPI</option>
                                </select>
                            </div>

                            <div>
                                <label for="transactionNumber" class="block text-gray-700 font-medium mb-2">Transaction Number</label>
                                <input type="text" id="transactionNumber" name="transactionNumber" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring focus:ring-blue-200 focus:outline-none transition-transform duration-300 transform hover:scale-105">
                            </div>

                            <div>
                                <label for="dateOfTransaction" class="block text-gray-700 font-medium mb-2">Date of Transaction</label>
                                <input type="date" id="dateOfTransaction" name="dateOfTransaction" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring focus:ring-blue-200 focus:outline-none transition-transform duration-300 transform hover:scale-105">
                            </div>

                            <div class="md:col-span-2 mt-4 md:mt-6">
                                <button type="submit"
                                    class="w-auto bg-blue-500 text-white py-1 px-3 rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-400 hover:shadow-lg transition-all duration-300">
                                    Submit
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </main>
        </div>
    </div>
<script>
document.getElementById('biodataForm').addEventListener('submit', function(event) {
    event.preventDefault(); // Prevent the default form submission

    // Create a FormData object to hold the form data
    var formData = new FormData(this);

    // Use fetch to submit the form data via AJAX
    fetch('submit_form.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        // Handle the response from the server (optional)
        console.log(data); // You can log the response or handle it as needed

        // Update the progress bar to 100%
        document.getElementById('progressBar').style.width = '100%';
        document.getElementById('progressText').textContent = '100% completed';
    })
    .catch(error => {
        console.error('Error:', error);
    });
});
</script>    
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