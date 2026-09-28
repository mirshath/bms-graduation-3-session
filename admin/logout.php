<?php
session_start(); // Start the session

// Set a success message before session is destroyed
$_SESSION['success_message'] = 'You have successfully logged out.';

// Unset all session variables
$_SESSION = [];

// Destroy the session
session_destroy();

// Redirect to the login page or home page after logout
header("Location: login"); // Change 'login' to your login page
exit;
?>
