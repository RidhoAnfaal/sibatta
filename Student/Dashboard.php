<?php
session_start();
include 'koneksi.php';
include '../Admin/koneksi.php';

if (!isset($_SESSION['username'])) {
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
    echo "<p>No data found for the user.</p>";
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
                                SIBATTA (Sistem Informasi Bebas Tanggungan Tugas Akhir) This project aims to
                                implement a system where final-year students (D3, D4, and S2) of Politeknik Negeri Malang
                                (Polinema) can upload their final project reports (Laporan Akhir, Skripsi, Tesis) to the
                                Polinema Library website. The system should automate the submission process, validate file
                                completeness, verify student debt obligations (such as book loans), and issue a "Surat
                                Keterangan Bebas Tanggungan" (Clearance Letter) once all requirements are fulfilled. This
                                will enable the student to obtain their diploma, transcripts, and SKPI (Surat Keterangan
                                Pendamping Ijazah).
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="info-card mt-4">
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Important Information</h5>
            <p class="card-text">
                Information Here
            </p>
        </div>
    </div>
</div>

            <!-- Other content can go here -->
        </div>

        <div class="toast" id="toastMessage" role="alert" aria-live="assertive" aria-atomic="true" style="position: fixed; top: 20px; right: 20px; z-index: 1050;">
    <div class="toast-header">
        <strong class="me-auto">Notification</strong>
        <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
    <div class="toast-body">
        Welcome to SIBATTA! You have successfully logged in.
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