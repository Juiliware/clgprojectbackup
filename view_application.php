<?php
session_start();

// Database configuration
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

// Check if user is logged in and the role is 'student' or 'admin'
if (!isset($_SESSION['user']) || ($_SESSION['role'] != 'student' && $_SESSION['role'] != 'admin')) {
    header("Location: index2.php");
    exit();
}

// Initialize variables
$existing_data = []; // This will hold the existing data if available
$student_id = $_GET['id'] ?? ''; // Get student ID from the URL

// Fetch existing data from biodata table
if ($student_id) {
    $query = "SELECT * FROM biodata WHERE students_id = :students_id";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['students_id' => $student_id]);
    $existing_data = $stmt->fetch(PDO::FETCH_ASSOC);
}

if (!$existing_data) {
    echo "<p class='text-red-600'>No application found for this student.</p>";
    exit();
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet" />
    <style>
        body {
            font-family: 'Roboto', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-100 p-4">
    <div class="max-w-4xl mx-auto bg-white shadow-lg rounded-lg p-8">
        <!-- Title -->
        <h1 class="text-2xl font-bold text-gray-800 mb-6 text-center border-b-2 pb-4">Application Details</h1>

        <!-- Personal Information Section -->
        <div class="mb-6">
            <h2 class="text-lg font-semibold text-gray-700 border-b pb-2">Personal Information</h2>
            <table class="w-full mt-4 border border-gray-300 rounded-lg shadow-sm">
                <tbody class="divide-y divide-gray-300">
                    <tr class="bg-gray-50 hover:bg-gray-100 transition">
                        <td class="font-semibold text-gray-700 border border-gray-300 p-3">Full Name</td>
                        <td class="text-gray-900 border border-gray-300 p-3"><?php echo htmlspecialchars($existing_data['name']); ?></td>
                        <td class="font-semibold text-gray-700 border border-gray-300 p-3">Course Applied</td>
                        <td class="text-gray-900 border border-gray-300 p-3"><?php echo htmlspecialchars($existing_data['course_applied']); ?></td>
                    </tr>
                    <tr class="hover:bg-gray-50 transition">
                        <td class="font-semibold text-gray-700 border border-gray-300 p-3">Date of Birth</td>
                        <td class="text-gray-900 border border-gray-300 p-3"><?php echo htmlspecialchars($existing_data['dob']); ?></td>
                        <td class="font-semibold text-gray-700 border border-gray-300 p-3">Gender</td>
                        <td class="text-gray-900 border border-gray-300 p-3"><?php echo htmlspecialchars($existing_data['gender']); ?></td>
                    </tr>
                    <tr class="bg-gray-50 hover:bg-gray-100 transition">
                        <td class="font-semibold text-gray-700 border border-gray-300 p-3">Address</td>
                        <td class="text-gray-900 border border-gray-300 p-3"><?php echo htmlspecialchars($existing_data['address']); ?></td>
                        <td class="font-semibold text-gray-700 border border-gray-300 p-3">Nationality</td>
                        <td class="text-gray-900 border border-gray-300 p-3"><?php echo htmlspecialchars($existing_data['previous_college']); ?></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Parents Details Section -->
        <div class="mb-6">
            <h2 class="text-lg font-semibold text-gray-700 border-b pb-2">Parents Details</h2>
            <table class="w-full mt-4 border border-gray-300 rounded-lg shadow-sm">
                <tbody class="divide-y divide-gray-300">
                    <tr class="bg-gray-50 hover:bg-gray-100 transition">
                        <td class="font-semibold text-gray-700 border border -gray-300 p-3">Father's Name</td>
                        <td class="text-gray-900 border border-gray-300 p-3"><?php echo htmlspecialchars($existing_data['father_name']); ?></td>
                        <td class="font-semibold text-gray-700 border border-gray-300 p-3">Mother's Name</td>
                        <td class="text-gray-900 border border-gray-300 p-3"><?php echo htmlspecialchars($existing_data['mother_name']); ?></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Caste and Income Section -->
        <div class="mb-6">
            <h2 class="text-lg font-semibold text-gray-700 border-b pb-2">Caste and Income</h2>
            <table class="w-full mt-4 border border-gray-300 rounded-lg shadow-sm">
                <tbody class="divide-y divide-gray-300">
                    <tr class="bg-gray-50 hover:bg-gray-100 transition">
                        <td class="font-semibold text-gray-700 border border-gray-300 p-3">Caste</td>
                        <td class="text-gray-900 border border-gray-300 p-3"><?php echo htmlspecialchars($existing_data['caste']); ?></td>
                        <td class="font-semibold text-gray-700 border border-gray-300 p-3">Annual Income</td>
                        <td class="text-gray-900 border border-gray-300 p-3"><?php echo htmlspecialchars($existing_data['income']); ?> INR</td>
                    </tr>
                </tbody>
            </table>
        </div>

                <!-- Upload Documents Section -->
        <div class="mb-6">
            <h2 class="text-lg font-semibold text-gray-700 border-b pb-2">Uploaded Documents</h2>
            <div class="grid grid-cols-2 gap-6 mt-4">
                <!-- Document Cards -->
                <div class="border border-gray-300 p-4 rounded-lg shadow-sm hover:shadow-md transition">
                    <span class="font-semibold text-gray-700 block mb-2">Aadhar Card</span>
                    <a class="text-blue-500 hover:underline" href="<?php echo htmlspecialchars('uploads/' . $existing_data['aadhar'] ?? '#'); ?>">View File</a>

                </div>
                <div class="border border-gray-300 p-4 rounded-lg shadow-sm hover:shadow-md transition">
                    <span class="font-semibold text-gray-700 block mb-2">Income Certificate</span>
                    <a class="text-blue-500 hover:underline" href="<?php echo htmlspecialchars('uploads/' . $existing_data['income_cert'] ?? '#'); ?>">View File</a>
                </div>
                <div class="border border-gray-300 p-4 rounded-lg shadow-sm hover:shadow-md transition">
                    <span class="font-semibold text-gray-700 block mb-2">Leaving Certificate</span>
                    <a class="text-blue-500 hover:underline" href="<?php echo htmlspecialchars('uploads/' . $existing_data['lc'] ?? '#'); ?>">View File</a>
                </div>
                <div class="border border-gray-300 p-4 rounded-lg shadow-sm hover:shadow-md transition">
                    <span class="font-semibold text-gray-700 block mb-2">Caste Certificate</span>
                    <a class="text-blue-500 hover:underline" href="<?php echo htmlspecialchars('uploads/' . $existing_data['caste_cert'] ?? '#'); ?>">View File</a>
                </div>
                <div class="border border-gray-300 p-4 rounded-lg shadow-sm hover:shadow-md transition">
                    <span class="font-semibold text-gray-700 block mb-2">Board Exam Certificate</span>
                    <a class="text-blue-500 hover:underline" href="<?php echo htmlspecialchars('uploads/' . $existing_data['marksheet'] ?? '#'); ?>">View File</a>
                </div>
                
            </div>
        </div>
    </div>

</body>
</html>