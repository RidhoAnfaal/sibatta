<?php
// Start the session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include the database connection file
include '../admin/koneksi.php'; // Adjust path as necessary

// Check if session username is set
if (!isset($_SESSION['username'])) {
    die("Session 'username' is not set.");
}

// Debug database connection
if ($conn === false) {
    die("Database connection failed: " . print_r(sqlsrv_errors(), true));
}

// Get the logged-in username
$username = $_SESSION['username'];

// Query to get user data
$queryUser = "SELECT TOP (1) [user_id], [username], [email], [role] 
              FROM [sibatta].[sibatta].[user]
              WHERE username = ?";
$params = [$username];

// Execute the user query
$stmt = sqlsrv_query($conn, $queryUser, $params);
if ($stmt === false) {
    die(print_r(sqlsrv_errors(), true));
}

// Fetch the user data
$userData = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

// Query to get student data
$queryStudent = "SELECT TOP (1) [student_id], [prodi], [fullName], [kelas] 
                 FROM [sibatta].[sibatta].[student]
                 WHERE user_id = ?";
$paramsStudent = [$userData['user_id']];

// Execute the student query
$stmtStudent = sqlsrv_query($conn, $queryStudent, $paramsStudent);
if ($stmtStudent === false) {
    die(print_r(sqlsrv_errors(), true));
}

// Fetch the student data
$student = sqlsrv_fetch_array($stmtStudent, SQLSRV_FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Information</title>
    <link rel="stylesheet" href="css/userinfo.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <!-- Header -->
    <?php include 'navbar.php'; ?>

    <div class="d-flex">
        <?php include 'Sidebar.php'; ?>

        <!-- Main Content -->
        <div class="container mt-4">
            <div class="card">
                <h1>Welcome, <?php echo htmlspecialchars($student['fullName']); ?></h1>
                <div class="card-body">
                    <!-- Table to display user and student information -->
                    <table class="table table-striped">
                        <tbody>
                            <tr>
                                <td><strong>Username</strong></td>
                                <td><?php echo htmlspecialchars($student['fullName']); ?></td>
                            </tr>
                            <tr>
                                <td><strong>Email</strong></td>
                                <td><?php echo htmlspecialchars($userData['email']); ?></td>
                            </tr>
                            <tr>
                                <td><strong>Student ID</strong></td>
                                <td><?php echo htmlspecialchars($student['student_id']); ?></td>
                            </tr>
                            <tr>
                                <td><strong>Study Program</strong></td>
                                <td><?php echo htmlspecialchars($student['prodi']); ?></td>
                            </tr>
                            <tr>
                            
                        <td><strong>Status</strong></td>
                        <td><?php echo htmlspecialchars($userData['role']); ?></td>
                    </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer mt-auto py-4">
        <div class="container text-center">
            <p>&copy; 2024 <strong>SIBATTA</strong>. All rights reserved.</p>
            <p>Contact us: <a href="mailto:support@sibatta.com">support@sibatta.com</a></p>
            <div class="social-icons">
                <a href="https://facebook.com" target="_blank" class="me-3">
                    <i class="bi bi-facebook"></i>
                </a>
                <a href="https://twitter.com" target="_blank" class="me-3">
                    <i class="bi bi-twitter"></i>
                </a>
                <a href="https://instagram.com" target="_blank">
                    <i class="bi bi-instagram"></i>
                    </a>
                </div>
            </div>
        </footer>
    </div>

</body>
</html>
