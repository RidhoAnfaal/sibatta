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
            <div class="row mt-4">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3>Welcome, <?php echo $userData['fullName']; ?>!</h3>
                        </div>
                        <div class="card-body">
                            <h5>Informasi Mahasiswa</h5>
                            <table class="table table-bordered">
                                <tr>
                                    <th>Nama</th>
                                    <td><?php echo $userData['fullName']; ?></td>
                                </tr>
                                <tr>
                                    <th>Program Studi</th>
                                    <td><?php echo $userData['prodi']; ?></td>
                                </tr>
                                <tr>
                                    <th>Username</th>
                                    <td><?php echo $userData['username']; ?></td>
                                </tr>
                                <tr>
                                    <th>Email</th>
                                    <td><?php echo $userData['email']; ?></td>
                                </tr>
                                <tr>
                                    <th>Status Tugas Akhir</th>
                                    <td>
                                        <?php
                                        if ($userData['final_project_status'] == 1) {
                                            echo "<span class='badge bg-success'>Approved</span>";
                                        } elseif ($userData['final_project_status'] == 0) {
                                            echo "<span class='badge bg-warning'>Waiting</span>";
                                        } elseif ($userData['final_project_status'] == -1) {
                                            echo "<span class='badge bg-danger'>Approval Denied</span>";
                                        }
                                        ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Status Hutang</th>
                                    <td>
                                        <?php
                                        if ($userData['debt_status'] == 0) {
                                            echo "<span class='badge bg-danger'>Arrears</span>";
                                        } else {
                                            echo "<span class='badge bg-success'>No Arrears</span>";
                                        }
                                        ?>
                                    </td>
                                </tr>
                            </table>
                            <a href="upload_report.php" class="btn btn-primary">Upload Tugas Akhir</a>
                            <a href="payment_status.php" class="btn btn-warning">Cek Status Pembayaran</a>
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