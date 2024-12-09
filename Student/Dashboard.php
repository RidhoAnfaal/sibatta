<?php
session_start();

// Include the User class
include_once 'User.php';
include 'koneksi.php';

// Create User object
$user = new User($host, $database, $username, $password, $_SESSION);

// Check if the user is logged in, if not redirect to login page
if (!$user->checkLogin()) {
    header('Location: index.php');
    exit();
}

$username = $_SESSION['username'];

$sql = "SELECT 
            s.student_id, 
            s.prodi, 
            s.fullName, 
            u.username, 
            u.email, 
            u.role
        FROM [sibatta].[sibatta].[student] s
        JOIN [sibatta].[sibatta].[user] u ON s.user_id = u.user_id
        WHERE LOWER(u.username) = LOWER(?)";

$params = array($username);
$stmt = sqlsrv_query($conn, $sql, $params);

if ($stmt === false) {
    die("SQL Error: " . print_r(sqlsrv_errors(), true));
}

$userData = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

if (!$userData) {
    echo "<!--<p>No data found for the user.</p>";
    exit;
}

sqlsrv_free_stmt($stmt);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <script type="module" src="https://cdn.jsdelivr.net/npm/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://cdn.jsdelivr.net/npm/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
    <link rel="stylesheet" href="css/main_student.css">
    <title>Home</title>
</head>

<body>
    <!-- Header -->
    <?php include 'navbar.php'; ?>

    <div class="d-flex flex-column min-vh-100">
        <?php include 'Sidebar.php'; ?>

        <!-- Main Content -->
        <div class="container flex-grow-1">

            <div class="image-container">
                <img src="css/images/Sibatta Picture.png" class="img-fluid" alt="Dashboard Image">
            </div>


            <div class="card2">
                <div class="card-body">
                    <div class="content-container d-flex flex-wrap">
                        <div class="text-container ms-4">
                            <h1>Welcome to SIBATTA</h1>
                            <p>
                            SIBATTA (Sistem Informasi Bebas Tanggungan Tugas Akhir) This project aims to implement a system where final-year students (D4) of Information Technology major in Politeknik Negeri Malang (Polinema) can upload their final project reports to the Admin Library. The system should automate the submission process, validate file completeness, verify student final project, Clearance status once all requirements are fulfilled.
                            </p>
                            <p>
                            Students
Information related to the status of the final project.
Publication of final project.
Provides a downloadable report of dependency-free status.

                            </p>
                        </div>
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

        <!-- Notification Pop-up -->
        <div class="toast-container position-fixed bottom-0 end-0 p-3">
            <div id="toastMessage" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="toast-header">
                    <strong class="me-auto">Notification</strong>
                    <small>just now</small>
                    <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
                <div class="toast-body">
                    You have a new update on your report!
                </div>
            </div>
        </div>

        <!-- Optional JavaScript -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
        <script>
            // Display toast notification on page load
            document.addEventListener('DOMContentLoaded', () => {
                const toast = new bootstrap.Toast(document.getElementById('toastMessage'));
                toast.show();
            });
        </script>


</body>

</html>