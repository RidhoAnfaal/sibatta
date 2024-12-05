<?php
// Start session and check if the user is logged in
session_start();
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

// Connect to the database
$host = "localhost";
$username = "root";  // Your database username
$password = "";      // Your database password
$dbname = "sibatta"; // Your database name

$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve form data
$username = $_POST['username'];
$no_telepon = $_POST['no_telepon'];
$email = $_POST['email'];
$password = $_POST['password'];
$confirm_password = $_POST['confirm_password'];
$level = $_POST['level'];
$jurusan_id = $_POST['jurusan'];
$prodi_id = $_POST['prodi'];

// Password validation
if ($password !== $confirm_password) {
    $_SESSION['error'] = "Password and confirm password do not match.";
    header('Location: add_user.php');
    exit();
}

// Hash the password
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Insert user into the database
$sql = "INSERT INTO users (username, no_telepon, email, password, level, jurusan_id, prodi_id) 
        VALUES (?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param('ssssssi', $username, $no_telepon, $email, $hashed_password, $level, $jurusan_id, $prodi_id);

if ($stmt->execute()) {
    $_SESSION['success'] = "User added successfully!";
} else {
    $_SESSION['error'] = "Failed to add user. Please try again.";
}

// Redirect back to the add user page
header('Location: add_user.php');
exit();

// Close the connection
$stmt->close();
$conn->close();
?>
