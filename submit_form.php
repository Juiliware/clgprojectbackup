<?php
session_start();
include('db_config.php'); // Include the database connection file

// Check if user is logged in and the role is 'student'
if (!isset($_SESSION['user']) || $_SESSION['role'] != 'student') {
    header("Location: index2.php");
    exit();
}

// Collect form data
$students_id = $_POST['students_id'] ?? ''; // Get the students_id from the form
$name = $_POST['name'] ?? '';
$dob = $_POST['dob'] ?? '';
$gender = $_POST['gender'] ?? '';
$fatherName = $_POST['fatherName'] ?? '';
$motherName = $_POST['motherName'] ?? '';
$address = $_POST['address'] ?? '';
$caste = $_POST['caste'] ?? '';
$income = $_POST['income'] ?? '';
$previousCollege = $_POST['previousCollege'] ?? '';
$courseApplied = $_POST['course_applied'] ?? '';

// File uploads
$uploadDirectory = "uploads/";
$aadhar = time() . 'aadhar' . basename($_FILES['aadhar']['name']);
$lc = time() . 'lc' . basename($_FILES['lc']['name']);
$markSheet = time() . 'marksheet' . basename($_FILES['markSheet']['name']);
$incomeCert = time() . 'incomecert' . basename($_FILES['incomeCert']['name']);
$casteCert = time() . 'castecert' . basename($_FILES['casteCert']['name']);

// Move uploaded files to the target directory
move_uploaded_file($_FILES['aadhar']['tmp_name'], $uploadDirectory . $aadhar);
move_uploaded_file($_FILES['lc']['tmp_name'], $uploadDirectory . $lc);
move_uploaded_file($_FILES['markSheet']['tmp_name'], $uploadDirectory . $markSheet);
move_uploaded_file($_FILES['incomeCert']['tmp_name'], $uploadDirectory . $incomeCert);
move_uploaded_file($_FILES['casteCert']['tmp_name'], $uploadDirectory . $casteCert);

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
$stmt_check->execute(['students_id' => $students_id]);
$alreadySubmitted = $stmt_check->fetchColumn();

if ($alreadySubmitted > 0) {
    // If the student has already submitted the application
    echo "<script>alert('You have already submitted the application form.'); window.location.href='view_form.php';</script>";
} else {
    // Insert new record into biodata table
    $query_insert = "INSERT INTO biodata (name, dob, gender, father_name, mother_name, address, caste, income, previous_college, aadhar, lc, marksheet, income_cert, caste_cert, course_applied, students_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt_insert = $pdo->prepare($query_insert);
    $stmt_insert->execute([$name, $dob, $gender, $fatherName, $motherName, $address, $caste, $income, $previousCollege, $aadhar, $lc, $markSheet, $incomeCert, $casteCert, $courseApplied, $students_id]);

    // After successful insertion of biodata
if ($stmt_insert) {
    $_SESSION['application_submitted'] = true;
    echo "<script>alert('Application Submitted Successfully!');</script>";
} else {
    echo "Error: " . $stmt_insert->errorInfo()[2];
}

}

$pdo = null; // Close the database connection
?>