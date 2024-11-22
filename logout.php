<?php
session_start(); // Start the session

// Check if the user is logged in by verifying if session variables are set
if (isset($_SESSION['aadhar_number'])) {
    // Destroy the session variables
    session_unset(); // Clears all session variables

    // Destroy the session
    session_destroy(); // Ends the session

    // Redirect to the login page after successful logout
    header('Location: index2.php');
    exit(); // Ensure no further script is executed
} else {
    // If no session is found, redirect to the login page
    header('Location: index2.php');
    exit();
}
?>
