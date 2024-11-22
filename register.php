<?php
include('db_config.php');  // Include the database connection file

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $first_name = $_POST['firstName'];
    $middle_name = $_POST['middleName'];
    $last_name = $_POST['lastName'];
    $email = $_POST['email'];
    $mobile_number = $_POST['mobileNumber'];
    $aadhar_number = $_POST['aadharNumber'];
    $password = $_POST['password'];
    $role = $_POST['userRole'];
    
    // Choose the table based on the selected role
    $table = '';
    if ($role == 'student') {
        $table = 'students';
    } elseif ($role == 'admin') {
        $table = 'admins';
    } elseif ($role == 'superadmin') {
        $table = 'superadmins';
    }
    
    // Prepare the SQL query for inserting the data
    $query = "INSERT INTO $table (first_name, middle_name, last_name, email, mobile_number, aadhar_number, password, role) 
              VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssssssss", $first_name, $middle_name, $last_name, $email, $mobile_number, $aadhar_number, $password, $role);
    
    if ($stmt->execute()) {
        echo "Registration successful!";
        // Redirect to login page
        header("Location: login.php");
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>
