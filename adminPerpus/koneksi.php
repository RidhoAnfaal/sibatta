<?php
session_start();

// Database connection variables
$host = "localhost";
$username = "root";  // Your database username
$password = "";      // Your database password
$dbname = "sibatta"; // Your database name

// Create a connection to the database
$conn = new mysqli($host, $username, $password, $dbname);

// Check if the connection was successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Placeholder for message if login fails
$message = "";

// Check if user is already logged in
if (isset($_SESSION['username'])) {
    header('Location: main.php'); // Redirect to the main page if already logged in
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Collect form data
    $input_username = $_POST['username'];
    $input_password = $_POST['password'];

    // Sanitize input to prevent SQL Injection
    $input_username = $conn->real_escape_string($input_username);
    $input_password = $conn->real_escape_string($input_password);

    // Query to check if the user exists and password matches
    $sql = "SELECT * FROM users WHERE username = '$input_username' AND password = '$input_password' LIMIT 1";
    $result = $conn->query($sql);

    // Check if user exists and password is correct
    if ($result->num_rows > 0) {
        // Start the session and store username in session
        $_SESSION['username'] = $input_username;
        header('Location: main.php'); // Redirect to main.php on successful login
        exit();
    } else {
        // If login fails
        $message = "Invalid username or password!";
    }
}

?>