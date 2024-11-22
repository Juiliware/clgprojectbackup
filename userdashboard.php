
<?php
session_start();
include('db_config.php'); // Include the database connection file

// Check if user is logged in and the role is 'student'
if (!isset($_SESSION['user']) || $_SESSION['role'] != 'student') {
    header("Location: index2.php");
    exit();
}

// Initial progress setup
$progress = 30; // Start with 30% for login
// Get the student's full name and ID
$full_name = isset($_SESSION['full_name']) ? $_SESSION['full_name'] : 'Guest';

// Get the student's ID
$student_id = $_SESSION['user']['id'];

// Database connection
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

// Check if the student has already submitted the application
$query_check = "SELECT COUNT(*) FROM biodata WHERE students_id = :students_id";
$stmt_check = $pdo->prepare($query_check);
$stmt_check->execute(['students_id' => $student_id]);
$alreadySubmitted = $stmt_check->fetchColumn();

// Increment progress if the application has been submitted
if ($alreadySubmitted > 0) {
    $progress += 40; // Increment by 40% if application is submitted
}

// Check if fees have been paid
if (isset($_SESSION['fees_paid']) && $_SESSION['fees_paid'] === true) {
    $progress += 30; // Increment by 30% if fees are paid
}

// Ensure progress doesn't exceed 100%
$progress = min($progress, 100);

// Fetch existing application data for the student
$query_fetch = "SELECT * FROM biodata WHERE students_id = :students_id";
$stmt_fetch = $pdo->prepare($query_fetch);
$stmt_fetch->execute(['students_id' => $student_id]);
$existing_data = $stmt_fetch->fetch(PDO::FETCH_ASSOC);

// Check if $existing_data is false (which means no data was found)
if ($existing_data === false) {
    $existing_data = []; // Initialize as an empty array to avoid warnings
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

// Handle college application submission
$error_message = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['college_ids'])) {
    $selected_colleges = $_POST['college_ids'];

    // Check if the user is already applied to 5 colleges
    if (count($applied_colleges) + count($selected_colleges) <= 5) {
        foreach ($selected_colleges as $college_id) {
            // Check if already applied to this college
            $query_check = "SELECT COUNT(*) FROM college_applications WHERE student_id = :student_id AND college_id = :college_id";
            $stmt_check = $pdo->prepare($query_check);
            $stmt_check->execute(['student_id' => $student_id, 'college_id' => $college_id]);
            if ($stmt_check->fetchColumn() == 0) {
                // Insert new application into college_applications table
                $query_insert = "INSERT INTO college_applications (student_id, college_id, status) VALUES (:student_id, :college_id, 'applied')";
                $stmt_insert = $pdo->prepare($query_insert);
                $stmt_insert->execute(['student_id' => $student_id, 'college_id' => $college_id]);
            }
        }

        // Set the application submitted session variable to true
        $_SESSION['application_submitted'] = true;

        // Refresh applied colleges list
        $stmt_applied->execute(['student_id' => $student_id]);
        $applied_colleges = $stmt_applied->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $error_message = "You have already applied to 5 colleges.";
    }
}

// Handle college application removal
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['remove_college_id'])) {
    $remove_college_id = $_POST['remove_college_id'];

    // Delete the college application from the database
    $query_remove = "DELETE FROM college_applications WHERE student_id = :student_id AND college_id = :college_id";
    $stmt_remove = $pdo->prepare($query_remove);
    $stmt_remove->execute(['student_id' => $student_id, 'college_id' => $remove_college_id]);

    // Refresh applied colleges list
    $stmt_applied->execute(['student_id' => $student_id]);
    $applied_colleges = $stmt_applied->fetchAll(PDO::FETCH_ASSOC);
}

// Fetching user details from session
$first_name = isset($_SESSION['user']['first_name']) ? $_SESSION['user']['first_name'] : '';
$middle_name = isset($_SESSION['user']['middle_name']) ? $_SESSION['user']['middle_name'] : '';
$last_name = isset($_SESSION['user']['last_name']) ? $_SESSION['user']['last_name'] : '';
$email = isset($_SESSION['user']['email']) ? $_SESSION['user']['email'] : '';
$mobile_number = isset($_SESSION['user']['mobile_number']) ? $_SESSION['user']['mobile_number'] : '';
$aadhar_number = isset($_SESSION['user']['aadhar_number']) ? $_SESSION['user']['aadhar_number'] : '';
$password = isset($_SESSION['user']['password']) ? $_SESSION['user']['password'] : ''; // It's generally not recommended to store passwords in session

$pdo = null; // Close the database connection
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
                <a href="#" class="flex items-center px-4 py-2 hover:bg-gray-800 rounded-lg text-white no-underline" id="dashboardLink">
                    <i class="fas fa-tachometer-alt mr-2"></i> Dashboard
                </a>
                <a href="#" class="flex items-center px-4 py-2 hover:bg-gray-800 rounded-lg text-white no-underline" id="applicationEditFormLink">
                    <i class="fas fa-edit mr-2"></i> Application Form
                </a>
                
                <a href="#" class="flex items-center px-4 py-2 hover:bg-gray-800 rounded-lg text-white no-underline" id="applyCollegeLink">
                    <i class="fas fa-university mr-2"></i> Apply College List
                </a>
                 <a href="#" class="flex items-center px-4 py-2 hover:bg-gray-800 rounded-lg text-white no-underline" id="studentFeesLink">
                    <i class="fas fa-dollar-sign mr-2"></i> Student Fees
                </a>
              
                <a href="logout.php" class="flex items-center px-4 py-2 hover:bg-gray-700 rounded-lg text-white no-underline">
                    <i class="fas fa-sign-out-alt mr-2"></i> Log Out
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
    <div id="dashboardContent" class="content-section">
    <h1 class="text-2xl font-semibold text-left">Welcome Back, <?php echo htmlspecialchars($full_name); ?>!</h1>
    <div class="mt-4">
        <h2 class="text-lg font-semibold text-gray-700">Application Status</h2>
        <div class="relative w-full bg-gray-200 rounded-full h-4">
            <div id="progressBar" class="absolute top-0 left-0 h-4 bg-blue-600 rounded-full" style="width: <?php echo $progress; ?>%;"></div>
        </div>
        <p id="progressText" class="text-sm text-gray-600 mt-1"><?php echo $progress; ?>% completed</p>
    </div>

    <!-- Cards Section -->
    <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        <!-- Card 1 -->
        <!-- Card 1: Application Form -->
    <div class="bg-gradient-to-r from-blue-400 to-blue-600 p-6 rounded-lg shadow-lg flex flex-col justify-between transform transition duration-500 hover:scale-105 hover:shadow-xl h-40">
        <div>
            <h3 class="text-2xl font-bold text-white">Application Form</h3>
                    </div>
        <a href="view_application_form.php" class="mt-2 text-blue-200 hover:underline">View Application Form</a>
    </div>

           <!-- Card 2: College List -->
    <div class="bg-gradient-to-r from-green-400 to-green-600 p-6 rounded-lg shadow-lg flex flex-col justify-between transform transition duration-500 hover:scale-105 hover:shadow-xl h-40">
        <div>
            <h3 class="text-2xl font-bold text-white">Max Colleges: 5</h3> <!-- Displaying max colleges -->
            <p class="text-gray-200 text-sm">College List</p>
        </div>
        <a href="college_list.php" class="mt-2 text-green-200 hover:underline">View Colleges</a>
    </div>

        <!-- Card 3 -->
            <!-- Card 3: Student Fees -->
    <div class="bg-gradient-to-r from-red-400 to-red-600 p-6 rounded-lg shadow-lg flex flex-col justify-between transform transition duration-500 hover:scale-105 hover:shadow-xl h-40">
        <div>
            <h3 class="text-2xl font-bold text-white">Student Fees</h3>
            <p class="text-gray-200 text-sm">Total Fee: </p>
            <p class="text-gray-200 text-sm">Remaining Fee:</p>
        </div>
        <!-- <a href="view_student_fees.php" class="mt-2 text-red-200 hover:underline">View Fees Details</a> -->
    </div>

    </div>
</div>
    <!-- Apply College List Content -->
<div id="applyCollegeContent" class="hidden">
    <div class="flex space-x-4"> <!-- Flex container for side-by-side layout -->
        <!-- Application Form Section -->
        <div class="bg-white border border-gray-200 rounded-lg shadow-md p-6 w-1/2"> <!-- Adjust width as needed -->
            <h2 class="text-xl font-semibold mb-4">Apply to a College</h2>
            <p class="text-gray-700 mb-4">You can apply to up to 5 colleges.</p>

            <?php if (!empty($error_message)): ?>
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mt-2 rounded">
                    <p class="text-sm"><?php echo htmlspecialchars($error_message); ?></p>
                </div>
            <?php endif; ?>

            <!-- Display colleges available for application -->
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

        <!-- Applied Colleges Section -->
        <div class="bg-white border border-gray-200 rounded-lg shadow-md p-6 w-1/2">
            <h2 class="text-xl font-semibold mb-4">Applied Colleges</h2>
            <p class="text-gray-700 mb-4">You have applied to <?php echo count($applied_colleges); ?> colleges.</p>

            <!-- Display applied colleges -->
<ul class="space-y-2">
    <?php foreach ($applied_colleges as $college): ?>
        <li class="bg-gray-100 p-2 rounded-md flex justify-between items-center">
            <p class="font-semibold text-gray-800">
                <?php echo htmlspecialchars($college['name']); ?>
            </p>
            <form action="" method="POST" class="ml-2">
                <input type="hidden" name="remove_college_id" value="<?php echo $college['id']; ?>">
                <button type="submit" class="text-red-500 hover:text-red-700">
                    <i class="fas fa-trash-alt"></i> <!-- Font Awesome Trash Icon -->
                </button>
            </form>
        </li>
    <?php endforeach; ?>
</ul>
            <?php if (count($applied_colleges) === 0): ?>
                <p class="text-sm text-gray-500">You have not applied to any college yet. Please fill out the application form.</p>
            <?php endif; ?>
        </div>
    </div>
</div>
                
<!-- Application Edit Form Content -->
<div id="applicationEditFormContent" class="content-section hidden">
    <h1 class="text-2xl font-semibold text-blue-600"> Application Form</h1>
    <div class="bg-white p-6 rounded-lg shadow hover:shadow-lg transition mt-6">
        <form id="applicationEditForm" action="submit_edit_application.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="students_id" value="<?php echo htmlspecialchars(isset($existing_data['students_id']) ? $existing_data['students_id'] : ''); ?>">

            <input type="hidden" name="students_id" value="<?php echo htmlspecialchars($student_id); ?>">


            <!-- Personal Information Section -->
            <div class="mt-8">
                <h3 class="text-lg font-semibold text-gray-700 border-l-4 border-blue-500 pl-2 mb-4">
                    <i class="mr-2 text-blue-500"></i> Personal Information
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-1">
                        <label for="name" class="block text-gray-700 font-medium">Full Name</label>
                        <input type="text" id="name" name="name" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" value="<?php echo htmlspecialchars(isset($existing_data['name']) ? $existing_data['name'] : ''); ?>" required>
                    </div>
                    <div class="space-y-1">
                        <label for="course_applied" class="block text-gray-700 font-medium">Course Applied</label>
                        <select id="course_applied" name="course_applied" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" required>
                            <option value="Science" <?php echo (isset($existing_data['course_applied']) && $existing_data['course_applied'] == 'Science') ? 'selected' : ''; ?>>Science</option>
                            <option value="Commerce" <?php echo (isset($existing_data['course_applied']) && $existing_data['course_applied'] == 'Commerce') ? 'selected' : ''; ?>>Commerce</option>
                            <option value="Arts" <?php echo (isset($existing_data['course_applied']) && $existing_data['course_applied'] == 'Arts') ? 'selected' : ''; ?>>Arts</option>
                        </select>
                    </div>
                    <div class="space-y-1">
                        <label for="dob" class="block text-gray-700 font-medium">Date of Birth</label>
                        <input type="date" id="dob" name="dob" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" value="<?php echo htmlspecialchars(isset($existing_data['dob']) ? $existing_data['dob'] : ''); ?>" required>
                    </div>
                    <div class="space-y-1">
                        <label for="gender" class="block text-gray-700 font-medium">Gender</label>
                        <select id="gender" name="gender" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" required>
                            <option value="Male" <?php echo (isset($existing_data['gender']) && $existing_data['gender'] == 'Male') ? 'selected' : ''; ?>>Male</option>
                            <option value="Female" <?php echo (isset($existing_data['gender']) && $existing_data['gender'] == 'Female') ? 'selected' : ''; ?>>Female</option>
                            <option value="Other" <?php echo (isset($existing_data['gender']) && $existing_data['gender'] == 'Other') ? 'selected' : ''; ?>>Other</option>
                        </select>
                    </div>
                </div>
            </div>
              <!-- Address and Previous College Section -->
<div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-6">
    <!-- Address Details Section -->
    <div class="space-y-1">
        <label for="address" class="block text-gray-700 font-medium">Permanent Address</label>
        <input type="text" id="address" name="address" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" value="<?php echo htmlspecialchars(isset($existing_data['address']) ? $existing_data['address'] : ''); ?>" required>
    </div>

    <!-- Previous College Field -->
    <div class="space-y-1">
        <label for="previousCollege" class="block text-gray-700 font-medium">Nationality</label>
        <input type="text" id="previousCollege" name="previousCollege" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" value="<?php echo htmlspecialchars(isset($existing_data['previous_college']) ? $existing_data['previous_college'] : ''); ?>" required>
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
                        <input type="text" id="fatherName" name="fatherName" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" value="<?php echo htmlspecialchars(isset($existing_data['father_name']) ? $existing_data['father_name'] : ''); ?>" required>
                    </div>
                    <div class="space-y-1">
                        <label for="motherName" class="block text-gray-700 font-medium">Mother's Name</label>
                        <input type="text" id="motherName" name="motherName" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" value="<?php echo htmlspecialchars(isset($existing_data['mother_name']) ? $existing_data['mother_name'] : ''); ?>" required>
                    </div>
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
                        <input type="text" id="caste" name="caste" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" value="<?php echo htmlspecialchars(isset($existing_data['caste']) ? $existing_data['caste'] : ''); ?>" required>
                    </div>
                    <div class="space-y-1">
                        <label for="income" class="block text-gray-700 font-medium">Annual Family Income</label>
                        <input type="number" id="income" name="income" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" value="<?php echo htmlspecialchars(isset($existing_data['income']) ? $existing_data['income'] : ''); ?>" required>
                    </div>
                </div>
            </div>

           
            <!-- Document Upload Section -->
            <div class="mt-8">
                <h3 class="text-lg font-semibold text-gray-700 border-l-4 border-blue-500 pl-2 mb-4">
                    <i class="fas fa-file-upload mr-2 text-blue-500"></i> Upload Documents
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-1">
                        <label for="aadhar" class="block text-gray-700">Aadhar Card </label>
                        <input type="file" id="aadhar" name="aadhar" class="w-full border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400">
                        <small class="text-gray-500">Current: <?php echo htmlspecialchars(isset($existing_data['aadhar']) ? $existing_data['aadhar'] : ''); ?></small>
                    </div>
                    <div class="space-y-1">
                        <label for="lc" class="block text-gray-700">Leaving Certificate (LC)</label>
                        <input type="file" id="lc" name="lc" class="w-full border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400">
                        <small class="text-gray-500">Current: <?php echo htmlspecialchars(isset($existing_data['lc']) ? $existing_data['lc'] : ''); ?></small>
                    </div>
                    <div class="space-y-1">
                        <label for="markSheet" class="block text-gray-700">Board Exam Mark Sheet</label>
                        <input type="file" id="markSheet" name="markSheet" class="w-full border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400">
                        <small class="text-gray-500">Current: <?php echo htmlspecialchars(isset($existing_data['marksheet']) ? $existing_data['marksheet'] : ''); ?></small>
                    </div>
                    <div class="space-y-1">
                        <label for="incomeCert" class="block text-gray-700">Income Certificate</label>
                        <input type="file" id="incomeCert" name="incomeCert" class="w-full border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400">
                        <small class="text-gray-500">Current: <?php echo htmlspecialchars(isset($existing_data['income_cert']) ? $existing_data['income_cert'] : ''); ?></small>
                    </div>
                    <div class="space-y-1">
                        <label for="casteCert" class="block text-gray-700">Caste Certificate</label>
                        <input type="file" id="casteCert" name="casteCert" class="w-full border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400">
                        <small class="text-gray-500">Current: <?php echo htmlspecialchars(isset($existing_data['caste_cert']) ? $existing_data['caste_cert'] : ''); ?></small>
                    </div>
                </div>
            </div>

            <!-- Submit and Update Buttons -->
            <div class="flex justify-center mt-8 space-x-4">
                <?php if ($existing_data): ?>
                    <button type="button" id="updateButton" class="px-6 py-3 bg-gradient-to-r from-green-500 to-green-700 text-white rounded-lg font-semibold transform hover:scale-105 transition">
                        Update Application
                    </button>
                <?php else: ?>
                    <button type="submit" name="submit_application" class="px-6 py-3 bg-gradient-to-r from-blue-500 to-blue-700 text-white rounded-lg font-semibold transform hover:scale-105 transition">
                        Submit Application
                    </button>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('updateButton').addEventListener('click', function() {
    var formData = new FormData(document.getElementById('applicationEditForm'));
    
    fetch('submit_edit_application.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        alert('Application Updated Successfully!');
        // Optionally, you can update the form with the new data if needed
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while updating the application.');
    });
});
</script>



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


<!-- Student Fees Content -->
<div id="studentFeesContent" class="content-section hidden">
    <h1 class="text-2xl font-semibold text-blue-600">Student Fees</h1>
    <div class="bg-white p-6 rounded-lg shadow hover:shadow-lg transition mt-6">
        <p class="mt-4 text-gray-600">Please fill out the student fees form below.</p>
        <form id="studentFeesForm" class="space-y-8 mt-8" action="submit_fees.php" method="POST">
            <!-- Payment Details Section -->
            <div class="mt-8">
                <h3 class="text-lg font-semibold text-gray-700 border-l-4 border-blue-500 pl-2 mb-4">
                    <i class="fas fa-money-check-alt mr-2 text-blue-500"></i> Payment Details
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-1">
                        <label for="payment_amount" class="block text-gray-700 font-medium">Payment Amount</label>
                        <input type="number" id="payment_amount" name="payment_amount" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" placeholder="Enter payment amount" required>
                    </div>
                    <div class="space-y-1">
                        <label for="mode_of_payment" class="block text-gray-700 font-medium">Mode of Payment</label>
                        <select id="mode_of_payment" name="mode_of_payment" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" required>
                            <option value="" disabled selected>Select payment method</option>
                            <option value="Debit Card">Debit Card</option>
                            <option value="Credit Card">Credit Card</option>
                            <option value="E-Wallet">E-Wallet</option>
                            <option value="UPI">UPI</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Transaction Details Section -->
            <div class="mt-8">
                <h3 class="text-lg font-semibold text-gray-700 border-l-4 border-blue-500 pl-2 mb-4">
                    <i class="fas fa-file-invoice mr-2 text-blue-500"></i> Transaction Details
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-1">
                        <label for="transaction_number" class="block text-gray-700 font-medium">Transaction Number</label>
                        <input type="text" id="transaction_number" name="transaction_number" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" placeholder="Enter transaction number" required>
                    </div>
                    <div class="space-y-1">
                        <label for="transaction_date" class="block text-gray-700 font-medium">Date of Transaction</label>
                        <input type="date" id="transaction_date" name="transaction_date" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" required>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-center mt-8">
                <button type="submit" class="px-6 py-3 bg-gradient-to-r from-blue-500 to-blue-700 text-white rounded-lg font-semibold transform hover:scale-105 transition">
                    Submit Payment
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
        const studentFeesLink = document.getElementById('studentFeesLink');
        const applyCollegeLink = document.getElementById('applyCollegeLink');
        const applicationEditFormLink = document.getElementById('applicationEditFormLink'); // For Application Edit Form link

        const dashboardContent = document.getElementById('dashboardContent');
        const studentFeesContent = document.getElementById('studentFeesContent');
        const applyCollegeContent = document.getElementById('applyCollegeContent');
        const applicationEditFormContent = document.getElementById('applicationEditFormContent'); // Application Edit Form
        const editProfileFormContent = document.getElementById('editProfileFormContent'); // Edit Profile Form

        // Function to show the selected section and hide others
        function showSection(showElement) {
            const sections = [
                dashboardContent,
                studentFeesContent,
                applyCollegeContent,
                applicationEditFormContent,
                editProfileFormContent
            ];
            sections.forEach((section) => {
                if (section === showElement) {
                    section.classList.remove('hidden');
                } else {
                    section.classList.add('hidden');
                }
            });
        }

        // Toggle dropdown visibility when profile button is clicked
        profileButton.addEventListener('click', function () {
            userDropdown.classList.toggle('hidden');
        });

        // Show respective sections when links are clicked
        dashboardLink.addEventListener('click', function () {
            showSection(dashboardContent);
            // Ensure Edit Profile is hidden
            editProfileFormContent.classList.add('hidden');
        });

        studentFeesLink.addEventListener('click', function () {
            showSection(studentFeesContent);
            // Ensure Edit Profile is hidden
            editProfileFormContent.classList.add('hidden');
        });

        applyCollegeLink.addEventListener('click', function () {
            showSection(applyCollegeContent);
            // Ensure Edit Profile is hidden
            editProfileFormContent.classList.add('hidden');
        });

        // Show Application Edit Form section and hide others
        applicationEditFormLink.addEventListener('click', function () {
            showSection(applicationEditFormContent);
            // Ensure Edit Profile is hidden
            editProfileFormContent.classList.add('hidden');
        });
    });

    // Function for external calls to show Edit Profile form
    function showEditProfileForm() {
        const editProfileFormContent = document.getElementById('editProfileFormContent');
        const dashboardContent = document.getElementById('dashboardContent');
        const studentFeesContent = document.getElementById('studentFeesContent');
        const applyCollegeContent = document.getElementById('applyCollegeContent');
        const applicationEditFormContent = document.getElementById('applicationEditFormContent'); // Application Edit Form

        // Show the Edit Profile form and hide all others
        editProfileFormContent.classList.remove('hidden');
        dashboardContent.classList.add('hidden');
        studentFeesContent.classList.add('hidden');
        applyCollegeContent.classList.add('hidden');
        applicationEditFormContent.classList.add('hidden'); // Ensure Application Edit Form is hidden
    }
</script></body>
</html>