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
$queryUser = "SELECT TOP (1) [user_id], [email], [role] 
              FROM [sibatta].[sibatta].[user]
              WHERE username = ?";
$params = [$username];
$stmtUser = sqlsrv_query($conn, $queryUser, $params);
if ($stmtUser === false) {
    die("User query failed: " . print_r(sqlsrv_errors(), true));
}

$userData = sqlsrv_fetch_array($stmtUser, SQLSRV_FETCH_ASSOC);
if (!$userData) {
    die("No user data found for username: " . htmlspecialchars($username));
}

// Query to get student data
$queryStudent = "SELECT TOP (1) [student_id], [prodi], [fullName], [kelas]
                 FROM [sibatta].[sibatta].[student]
                 WHERE user_id = ?";
$paramsStudent = [$userData['user_id']];
$stmtStudent = sqlsrv_query($conn, $queryStudent, $paramsStudent);
if ($stmtStudent === false) {
    die("Student query failed: " . print_r(sqlsrv_errors(), true));
}

$student = sqlsrv_fetch_array($stmtStudent, SQLSRV_FETCH_ASSOC);
if (!$student) {
    die("No student data found for user_id: " . htmlspecialchars($userData['user_id']));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Information</title>
    <link rel="stylesheet" href="css/main_student.css">
</head>
<body>
    <!-- Header -->
    <?php include 'navbar.php'; ?>

    <div class="d-flex">
        <?php include 'Sidebar.php'; ?>

        <!-- Main Content -->
<div class="container mt-4">
    <div class="card">
        <h1 class="text-center">Profil</h1>
        <div class="card-body">
            <!-- Table to display user and student information -->
            <table class="table table-striped">
                <tbody>
                    <tr>
                        <td><strong>Student ID</strong></td>
                        <td><?php echo htmlspecialchars($student['student_id']); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Username</strong></td>
                        <td><?php echo htmlspecialchars($student['fullName']); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Class</strong></td>
                        <td><?php echo htmlspecialchars($student['kelas']); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Study Program</strong></td>
                        <td><?php echo htmlspecialchars($student['prodi']); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Email</strong></td>
                        <td><?php echo htmlspecialchars($userData['email']); ?></td>
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

</body>
</html>
