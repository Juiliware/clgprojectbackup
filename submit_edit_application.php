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
$name = trim($_POST['name'] ?? '');
$dob = trim($_POST['dob'] ?? '');
$gender = trim($_POST['gender'] ?? '');
$fatherName = trim($_POST['fatherName'] ?? '');
$motherName = trim($_POST['motherName'] ?? '');
$address = trim($_POST['address'] ?? '');
$caste = trim($_POST['caste'] ?? '');
$income = trim($_POST['income'] ?? '');
$previousCollege = trim($_POST['previousCollege'] ?? '');
$courseApplied = trim($_POST['course_applied'] ?? '');

// Validate form data
if (empty($name) || empty($dob) || empty($gender) || empty($fatherName) || empty($motherName) || empty($address) || empty($caste) || empty($income) || empty($previousCollege) || empty($courseApplied)) {
    echo "<script>alert('Please fill in all the required fields.');</script>";
    exit();
}

// File uploads
$uploadDirectory = "uploads/";
$aadhar = time() . 'aadhar' . basename($_FILES['aadhar']['name']);
$lc = time() . 'lc' . basename($_FILES['lc']['name']);
$markSheet = time() . 'marksheet' . basename($_FILES['markSheet']['name']);
$incomeCert = time() . 'incomecert' . basename($_FILES['incomeCert']['name']);
$casteCert = time() . 'castecert' . basename($_FILES['casteCert']['name']);

// Move uploaded files to the target directory
if (!move_uploaded_file($_FILES['aadhar']['tmp_name'], $uploadDirectory . $aadhar)) {
    echo "<script>alert('Error uploading Aadhar card.');</script>";
    exit();
}
if (!move_uploaded_file($_FILES['lc']['tmp_name'], $uploadDirectory . $lc)) {
    echo "<script>alert('Error uploading Leaving Certificate.');</script>";
    exit();
}
if (!move_uploaded_file($_FILES['markSheet']['tmp_name'], $uploadDirectory . $markSheet)) {
    echo "<script>alert('Error uploading Board Exam Mark Sheet.');</script>";
    exit();
}
if (!move_uploaded_file($_FILES['incomeCert']['tmp_name'], $uploadDirectory . $incomeCert)) {
    echo "<script>alert('Error uploading Income Certificate.');</script>";
    exit();
}
if (!move_uploaded_file($_FILES['casteCert']['tmp_name'], $uploadDirectory . $casteCert)) {
    echo "<script>alert('Error uploading Caste Certificate.');</script>";
    exit();
}

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

// Validate that the students_id exists in the students table
$query_validate_student = "SELECT COUNT(*) FROM students WHERE id = :students_id";
$stmt_validate_student = $pdo->prepare($query_validate_student);
$stmt_validate_student->execute(['students_id' => $students_id]);
$studentExists = $stmt_validate_student->fetchColumn();

if (!$studentExists) {
    echo "<script>alert('The student ID does not exist. Please check and try again.');</script>";
    exit();
}

// Check if the student has already submitted the application
$query_check = "SELECT COUNT(*) FROM biodata WHERE students_id = :students_id";
$stmt_check = $pdo->prepare($query_check);
$stmt_check->execute(['students_id' => $students_id]);
$alreadySubmitted = $stmt_check->fetchColumn();

if ($alreadySubmitted > 0) {
    // If the student has already submitted the application, update the existing record
    $query_update = "UPDATE biodata SET 
        name = ?, 
        dob = ?, 
        gender = ?, 
        father_name = ?, 
        mother_name = ?, 
        address = ?, 
        caste = ?, 
        income = ?, 
        previous_college = ?, 
        aadhar = ?, 
        lc = ?, 
        marksheet = ?, 
        income_cert = ?, 
        caste_cert = ?, 
        course_applied = ? 
        WHERE students_id = ?";
    
    $stmt_update = $pdo->prepare($query_update);
    $stmt_update->execute([$name, $dob, $gender, $fatherName, $motherName, $address, $caste, $income, $previousCollege, $aadhar, $lc, $markSheet, $incomeCert, $casteCert, $courseApplied, $students_id]);

    // After successful update of biodata
    if ($stmt_update) {
        $_SESSION['application_submitted'] = true;
        echo "<script>alert('Application Updated Successfully!');</script>";
    } else {
        echo "Error: " . $stmt_update->errorInfo()[2];
    }
} else {
    // If the student has not submitted the application, insert a new record
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

$pdo = null; // 
?>