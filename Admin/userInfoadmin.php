<?php
// Start the session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include the database connection file
include 'koneksi.php'; // Adjust path as necessary

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
$queryUser = "SELECT TOP (1) [user_id], [username], [email], [role] 
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

// Query to get admin data
$queryadmin = "SELECT TOP (1) [admin_id], [admin_role], [fullName] 
                 FROM [sibatta].[sibatta].[admin]
                 WHERE user_id = ?";
$paramsadmin = [$userData['user_id']];
$stmtadmin = sqlsrv_query($conn, $queryadmin, $paramsadmin);
if ($stmtadmin === false) {
    die("admin query failed: " . print_r(sqlsrv_errors(), true));
}

$admin = sqlsrv_fetch_array($stmtadmin, SQLSRV_FETCH_ASSOC);
if (!$admin) {
    die("No admin data found for user_id: " . htmlspecialchars($userData['user_id']));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Information</title>
    <link rel="stylesheet" href="css/infoadmin.css">
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
            <!-- Table to display user and admin information -->
            <table class="table table-striped">
                <tbody>
                    <tr>
                        <td><strong>admin ID</strong></td>
                        <td><?php echo htmlspecialchars($admin['admin_id']); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Username</strong></td>
                        <td><?php echo htmlspecialchars($admin['fullName']); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Email</strong></td>
                        <td><?php echo htmlspecialchars($userData['email']); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Status</strong></td>
                        <td><?php echo htmlspecialchars($admin['admin_role']); ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
      <!-- Footer -->
      =<footer class="footer mt-auto py-4">
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
