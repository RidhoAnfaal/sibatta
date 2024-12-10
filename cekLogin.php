<?php
// Check if a session is already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database connection
require 'koneksi.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Query to check the user in the database
    $query = "SELECT * FROM sibatta_user WHERE username = ? AND password = ?";
    
    // Prepare the statement
    $params = array($username, $password);
    $stmt = sqlsrv_prepare($conn, $query, $params);
    
    if (!$stmt) {
        die(print_r(sqlsrv_errors(), true));
    }

    // Execute the statement
    $result = sqlsrv_execute($stmt);

    if ($result === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    // Fetch the data
    $user = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

    if ($user) {
        // Store user information in session
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['student_id'] = $user['student_id']; // Set student_id in session

        // Redirect based on role
        if ($user['role'] === 'student') {
            header("Location: student/main_student.php");
        } else if ($user['role'] === 'library_admin') {
            header("Location: adminPerpus/mainPerpus.php");
        } else if ($user['role'] === 'academic_admin') {
            header("Location: adminAkademik/mainAkademik.php");
        }
        exit;
    } else {
        // If no user found, return an error
        $message = "Invalid username or password.";
        include 'index.php'; // Show the login page with the error message
    }

    // Free the statement and close the connection
    sqlsrv_free_stmt($stmt);
    sqlsrv_close($conn);
}
?>