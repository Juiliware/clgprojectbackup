<?php
include('db_config.php');  // Include the database connection file

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $aadhar_number = $_POST['aadharLogin'];
    $password = $_POST['loginPassword'];
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

    // Prepare the SQL query to fetch the user record
    $query = "SELECT * FROM $table WHERE aadhar_number = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $aadhar_number);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if ($password == $user['password']) {
            // Password matches, login successful
            session_start();
            $_SESSION['user'] = $user;
            $_SESSION['role'] = $role;

            // Concatenate first, middle, and last name into one session variable
            $full_name = $user['first_name'] . " " . $user['middle_name'] . " " . $user['last_name'];
            $_SESSION['full_name'] = $full_name;

            // Redirect based on user role with JavaScript
            echo "<script>
                    alert('Login Successful!');
                    window.location.href = '";
            
            // Redirect based on user role
            if ($role == 'student') {
                echo "userdashboard.php';</script>";
            } elseif ($role == 'admin') {
                echo "college_project.php';</script>";
            } elseif ($role == 'superadmin') {
                echo "college1.php';</script>";
            }
        } else {
            echo "Invalid password!";
        }
    } else {
        echo "No user found with this Aadhar number!";
    }
}
?>
