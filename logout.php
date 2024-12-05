<?php
// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Destroy all session variables
$_SESSION = [];

// Destroy the session
session_destroy();

// Redirect to the login page
header("Location: index.php");
exit;
?>
