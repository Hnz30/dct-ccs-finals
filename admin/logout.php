<?php
session_start(); // Start the session

// Destroy all session data to log the user out
session_unset(); // Remove all session variables
session_destroy(); // Destroy the session

// Redirect to the login page (index.php)
header("Location: ../index.php");
exit;
?>
