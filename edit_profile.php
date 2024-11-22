<?php
session_start();
include('db_config.php');  // Include the database connection file

// Ensure the user is logged in
if (!isset($_SESSION['user'])) {
    header("Location: login.php");  // Redirect to login if not logged in
    exit();
}

// Get user details from the session
$user = $_SESSION['user'];
$role = $_SESSION['role']; // This will contain 'student', 'admin', or 'superadmin'

// Handle form submission for updating profile
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize user inputs to prevent XSS attacks
    $firstName = htmlspecialchars($_POST['firstName']);
    $middleName = htmlspecialchars($_POST['middleName']);
    $lastName = htmlspecialchars($_POST['lastName']);
    $email = htmlspecialchars($_POST['email']);
    $mobile = htmlspecialchars($_POST['mobile']);
    $aadharNumber = htmlspecialchars($_POST['aadharNumber']);
    
    // Determine the table based on the user role
    $table = '';
    if ($role == 'student') {
        $table = 'students';
    } elseif ($role == 'admin') {
        $table = 'admins';
    } elseif ($role == 'superadmin') {
        $table = 'superadmins';
    }

    // Prepare SQL query to update the user data
    $query = "UPDATE $table SET first_name = ?, middle_name = ?, last_name = ?, email = ?, mobile_number = ? WHERE aadhar_number = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssssss", $firstName, $middleName, $lastName, $email, $mobile, $aadharNumber);

    // Execute the update query
    if ($stmt->execute()) {
        // Update the session with new user details
        $_SESSION['user']['first_name'] = $firstName;
        $_SESSION['user']['middle_name'] = $middleName;
        $_SESSION['user']['last_name'] = $lastName;
        $_SESSION['user']['email'] = $email;
        $_SESSION['user']['mobile_number'] = $mobile;  // Updated to match column name

             // Display a success alert and redirect to the user dashboard
        echo "<script>
                alert('Profile updated successfully!');
                window.location.href = 'userdashboard.php';  // Redirect to the user dashboard page
              </script>";
    } else {
        // Display an error alert without redirecting
        echo "<script>alert('Failed to update profile. Please try again later.');</script>";
    }
}
?>