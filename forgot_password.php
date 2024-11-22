<?php
include('db_config.php');  // Include the database connection file

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve form data
    $aadhar_number = $_POST['aadharNumber'];
    $user_role = $_POST['userRole'];
    $existing_password = $_POST['existingPassword'];
    $new_password = $_POST['newPassword'];
    $confirm_password = $_POST['confirmPassword'];
    
    // Check if password and confirm password match
    if ($new_password !== $confirm_password) {
        echo "Passwords do not match.";
        exit;
    }
    
    // Choose the table based on the selected role
    $table = '';
    if ($user_role == 'student') {
        $table = 'students';
    } elseif ($user_role == 'admin') {
        $table = 'admins';
    } elseif ($user_role == 'superadmin') {
        $table = 'superadmins';
    }
    
    // Prepare the SQL query to find the user based on Aadhar number, role, and existing password
    $query = "SELECT * FROM $table WHERE aadhar_number = ? AND role = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $aadhar_number, $user_role);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        // User found, fetch the current password from the database
        $user = $result->fetch_assoc();
        $db_existing_password = $user['password']; // The password stored in the database
        
        // Check if the entered existing password matches the database password
        if ($existing_password !== $db_existing_password) {
            echo "The existing password you entered is incorrect.";
            exit;
        }

        // Update the password if everything is correct
        $update_query = "UPDATE $table SET password = ? WHERE aadhar_number = ? AND role = ?";
        $update_stmt = $conn->prepare($update_query);
        $update_stmt->bind_param("sss", $new_password, $aadhar_number, $user_role);
        
        if ($update_stmt->execute()) {
            echo "Password reset successfully!";
        } else {
            echo "Error: " . $update_stmt->error;
        }
    } else {
        echo "No user found with this Aadhar number and role.";
    }
}
?>
