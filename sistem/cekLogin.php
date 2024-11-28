<?php
// Include database connection
require 'koneksi.php';

// Start session
session_start();

// Placeholder for error message
$message = "";

// Function to check username and password
function checkLogin($username, $password, $conn) {
    // Query to check username
    $query = "SELECT user_id, username, password, role FROM users WHERE username = ?";
    $params = array($username);
    $stmt = sqlsrv_query($conn, $query, $params);

    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    // Fetch user data
    $user = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

    // Check if user exists and password matches
    if ($user && $user['password'] == $password) {
        return $user;
    }
    return false;
}

// Handle POST request from login form
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Validate input
    if (!empty($username) && !empty($password)) {
        $user = checkLogin($username, $password, $conn);
        
        if ($user) {
            // Set session variables
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            // Redirect based on role
            if ($user['role'] == 'student') {
                header('Location: main_user.php');
            } elseif ($user['role'] == 'lecturer') {
                header('Location: lecturer_dashboard.php');
            } elseif ($user['role'] == 'admin') {
                header('Location: admin_dashboard.php');
            }
            exit();
        } else {
            $message = "Invalid username or password!";
        }
    } else {
        $message = "Please fill in all fields!";
    }
}

?>
