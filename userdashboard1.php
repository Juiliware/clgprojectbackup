<?php
session_start();

// Check if user is logged in and the role is 'student'
if (!isset($_SESSION['user']) || $_SESSION['role'] != 'student') {
    header("Location: index2.php");
    exit();
}



// Initial progress setup
$progress = 30; // Start with 30% for login

// Add 40% for application form submission if submitted
if (isset($_SESSION['application_submitted']) && $_SESSION['application_submitted'] === true) {
    $progress += 40;
}

// Add 30% for fees payment if paid
if (isset($_SESSION['fees_paid']) && $_SESSION['fees_paid'] === true) {
    $progress += 30;
}

// Ensure progress doesn't exceed 100%
$progress = min($progress, 100);

// Get the student's full name and ID
$full_name = isset($_SESSION['full_name']) ? $_SESSION['full_name'] : 'Guest';
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

// Fetching user details from session
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
</head>
<body class="font-sans antialiased bg-gray-100 min-h-screen">
    <div class="flex">
        <aside class="w-64 bg-gray-800 text-white min-h-screen p-4">
            <h3 class="ml-6">USER</h3>
            <nav class="flex flex-col space-y-4">
                <a href="#" class="flex items-center px-4 py-2 hover:bg-gray-800 rounded-lg text-white no-underline" id="dashboardLink">
                    <i class="fas fa-tachometer-alt mr-2"></i> Dashboard
                </a>
                 <a href="#" class="flex items-center px-4 py-2 hover:bg-gray-800 rounded-lg text-white no-underline" id="applicationFormLink">
                    <i class="fas fa-file-alt mr-2"></i> Application Form
                </a>
                <a href="#" class="flex items-center px-4 py-2 hover:bg-gray-800 rounded-lg text-white no-underline" id="applicationEditFormLink">
                    <i class="fas fa-edit mr-2"></i> Application Edit Form
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

        <!-- Flex container for side-by-side layout -->
        <div class="flex space-x-4"> <!-- Add horizontal spacing between cards -->
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
<?php
// Handle removal of a college application
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['remove_college_id'])) {
    $college_id_to_remove = $_POST['remove_college_id'];

    // Remove the application from the college_applications table
    $query_remove = "DELETE FROM college_applications WHERE student_id = :student_id AND college_id = :college_id";
    $stmt_remove = $pdo->prepare($query_remove);
    $stmt_remove->execute(['student_id' => $student_id, 'college_id' => $college_id_to_remove]);

    // Refresh the applied colleges list
    $stmt_applied->execute(['student_id' => $student_id]);
    $applied_colleges = $stmt_applied->fetchAll(PDO::FETCH_ASSOC);
}
?>

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
</button>                </form>
            </li>
        <?php endforeach; ?>
    </ul>

    <?php if (count($applied_colleges) === 0): ?>
        <p class="text-sm text-gray-500">You have not applied to any college yet. Please fill out the application form.</p>
    <?php endif; ?>
</div>

        </div>
    </div>

                <!-- Application Form Content -->
                <div id="applicationFormContent" class="content-section hidden">
                    <h1 class="text-2xl font-semibold text-blue-600">Application Form</h1>
                    <div class="bg-white p-6 rounded-lg shadow hover:shadow-lg transition mt-6">
                        <p class="mt-4 text-gray-600">Please fill out the application form below.</p>
                        <form id="biodataForm" class="space-y-8 mt-8" action="submit_form.php" method="POST" enctype="multipart/form-data">
                                <input type="hidden" name="students_id" value="<?php echo htmlspecialchars($student_id); ?>">

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
                                            <option value="Science">Science</option>
                                            <option value="Commerce">Commerce</option>
                                            <option value="Arts">Arts</option>
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

<div id="applyCollegeContent" class="hidden">Apply College List Content Here</div>

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
<!-- Application Edit Form Content -->
<div id="applicationEditFormContent" class="content-section hidden">
    <h1 class="text-2xl font-semibold text-blue-600">Edit Application Form</h1>
    <div class="bg-white p-6 rounded-lg shadow hover:shadow-lg transition mt-6">
        <form id="applicationEditForm" class="space-y-8 mt-8" action="submit_edit_application.php" method="POST">
            <!-- Add your form fields here -->
            <div class="mt-8">
                <label for="edit_name" class="block text-gray-700 font-medium">Name</label>
                <input type="text" id="edit_name" name="edit_name" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" placeholder="Enter your name" required>
            </div>
            <!-- Add more fields as necessary -->
            <div class="flex justify-center mt-8">
                <button type="submit" class="px-6 py-3 bg-gradient-to-r from-blue-500 to-blue-700 text-white rounded-lg font-semibold transform hover:scale-105 transition">
                    Update Application
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
        const studentFeesLink = document.getElementById('studentFeesLink');
        const applyCollegeLink = document.getElementById('applyCollegeLink');
        const applicationEditFormLink = document.getElementById('applicationEditFormLink'); // For Application Edit Form link

        const dashboardContent = document.getElementById('dashboardContent');
        const applicationFormContent = document.getElementById('applicationFormContent');
        const editProfileFormContent = document.getElementById('editProfileFormContent'); // Always visible
        const studentFeesContent = document.getElementById('studentFeesContent');
        const applyCollegeContent = document.getElementById('applyCollegeContent');
        const applicationEditFormContent = document.getElementById('applicationEditFormContent'); // Application Edit Form

        // Function to show the selected section and hide others
        function showSection(showElement) {
            const sections = [
                dashboardContent,
                applicationFormContent,
                studentFeesContent,
                applyCollegeContent,
                applicationEditFormContent,
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
            editProfileFormContent.classList.add('hidden'); // Ensure Edit Profile is hidden
        });

        applicationFormLink.addEventListener('click', function () {
            showSection(applicationFormContent);
            editProfileFormContent.classList.add('hidden'); // Ensure Edit Profile is hidden
        });

        studentFeesLink.addEventListener('click', function () {
            showSection(studentFeesContent);
            editProfileFormContent.classList.add('hidden'); // Ensure Edit Profile is hidden
        });

        applyCollegeLink.addEventListener('click', function () {
            showSection(applyCollegeContent);
            editProfileFormContent.classList.add('hidden'); // Ensure Edit Profile is hidden
        });

        // Show Application Edit Form section and hide others
        applicationEditFormLink.addEventListener('click', function () {
            showSection(applicationEditFormContent);
            editProfileFormContent.classList.add('hidden'); // Ensure Edit Profile is hidden
        });
    });

    // Function for external calls to show Edit Profile form
    function showEditProfileForm() {
        const editProfileFormContent = document.getElementById('editProfileFormContent');
        const dashboardContent = document.getElementById('dashboardContent');
        const applicationFormContent = document.getElementById('applicationFormContent');
        const studentFeesContent = document.getElementById('studentFeesContent');
        const applyCollegeContent = document.getElementById('applyCollegeContent');
        const applicationEditFormContent = document.getElementById('applicationEditFormContent'); // Application Edit Form

        // Show the Edit Profile form and hide all others
        editProfileFormContent.classList.remove('hidden');
        dashboardContent.classList.add('hidden');
        applicationFormContent.classList.add('hidden');
        studentFeesContent.classList.add('hidden');
        applyCollegeContent.classList.add('hidden');
        applicationEditFormContent.classList.add('hidden'); // Ensure Application Edit Form is hidden
    }
</script>
</body>
</html>