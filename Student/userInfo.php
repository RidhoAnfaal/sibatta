<?php
// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    // Redirect to login page if not logged in
    header("Location: ../index.php"); // Corrected path for redirect
    exit;
}

// Get logged-in user's username
$username = $_SESSION['username'];

// Include the database connection file
include '../koneksi.php'; // Corrected path

// Fetch user information from the database
$sql = "SELECT 
            u.username,
            u.email,
            s.student_id,
            s.prodi,
            s.fullName
        FROM sibatta_user u
        JOIN sibatta_student s ON u.user_id = s.user_id
        WHERE u.username = ?";
$params = array($username);
$stmt = sqlsrv_query($conn, $sql, $params);

// Check for errors
if ($stmt === false) {
    die(print_r(sqlsrv_errors(), true));
}

// Fetch the user's data
$userData = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

// Close the statement
sqlsrv_free_stmt($stmt);

if (!$userData) {
    echo "No data found for the user.";
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <script type="module" src="https://cdn.jsdelivr.net/npm/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://cdn.jsdelivr.net/npm/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
    <link rel="stylesheet" href="css/main_student.css">
    <title>User Information</title>
    <style>
        /* Style for hidden sidebar */
        #sidebar {
            position: fixed;
            left: -250px;
            top: 56px;
            height: calc(100vh - 56px);
            width: 250px;
            background-color: #f8f9fa;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
            transition: left 0.3s ease-in-out;
            z-index: 1050;
        }

        #sidebar.active {
            left: 0;
        }

        #overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1040;
            display: none;
        }

        #overlay.active {
            display: block;
        }
    </style>
</head>

<body>
    <!-- Horizontal Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <button class="btn btn" id="sidebarToggle">
                <img src="css/images/Logo_Sibatta.png" alt="Toggle Sidebar" style="width: 30px; height: 40px; object-fit:cover;">
                <span class="navbar-brand">SIBATTA</span>
            </button>
            <div>
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link text-light" href="#" role="button" data-bs-toggle="modal" aria-expanded="false">
                            <ion-icon name="person-circle-outline"></ion-icon>
                            <span id="username"><?php echo htmlspecialchars($username); ?></span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Sidebar -->
    <div id="sidebar">
        <div class="text-center p-3">
            <img src="css/images/Logo_Sibatta.png" alt="Logo" width="50" height="40" class="img-fluid">
            <h5 class="mt-2 text-dark">SIBATTA</h5>
        </div>
        <ul class="nav flex-column">
            <li class="nav-item mb-3">
                <a class="nav-link text-dark d-flex align-items-center" href="mainStudent.php">
                    <ion-icon name="home-outline" class="me-2"></ion-icon> <span>Home</span>
                </a>
            </li>
            <li class="nav-item mb-3">
                <a class="nav-link text-dark d-flex align-items-center" href="userInfo.php">
                    <ion-icon name="person-circle-outline" class="me-2"></ion-icon> <span>User Information</span>
                </a>
            </li>
            <li class="nav-item mb-3">
                <a class="nav-link text-dark d-flex align-items-center" href="uploadTA.php">
                    <ion-icon name="cloud-upload-outline" class="me-2"></ion-icon> <span>Upload your final project</span>
                </a>
            </li>
            <li class="nav-item mb-3">
                <a class="nav-link text-dark d-flex align-items-center" href="upload.php">
                    <ion-icon name="cloud-upload-outline" class="me-2"></ion-icon> <span>Dependents free</span>
                </a>
            </li>
            <li class="nav-item mb-3">
                <a class="nav-link text-dark d-flex align-items-center" href="upload.php">
                    <ion-icon name="cloud-upload-outline" class="me-2"></ion-icon> <span>Mail</span>
                </a>
            </li>
        </ul>
        <div class="modal-footer">
            <button class="logout-btn btn btn-danger" data-bs-toggle="modal" data-bs-target="#logoutModal">
                <ion-icon name="log-out-outline" style="font-size: 20px;"></ion-icon>
                <span>Log Out</span>
            </button>
        </div>
    </div>  

    <!-- Overlay -->
    <div id="overlay"></div>

    <!-- Main Content -->
    <div class="container mt-4">
        <h1>User Information</h1>
        <div class="card">
            <div class="card-body">
                <p class="card-text"><strong>Username :</strong> <?php echo htmlspecialchars($userData['username']); ?></p>
                <p class="card-text"><strong>Email :</strong> <?php echo htmlspecialchars($userData['email']); ?></p>
                <p class="card-text"><strong>Student ID :</strong> <?php echo htmlspecialchars($userData['student_id']); ?></p>
                <p class="card-text"><strong>Full Name :</strong> <?php echo htmlspecialchars($userData['fullName']); ?></p>
                <p class="card-text"><strong>Study Program :</strong> <?php echo htmlspecialchars($userData['prodi']); ?></p>
            </div>
        </div>
    </div>

    <!-- Modal Logout -->
    <div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <!-- Header -->
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title mx-auto" id="logoutModalLabel">Apakah Anda yakin ingin keluar dari akun Anda?</h5>
                </div>
                <!-- Body -->
                <div class="modal-body text-center">
                    <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Batal</button>
                    <a href="/pbl/logout.php" class="btn btn-danger">Log Out</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Optional JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('overlay');
        const sidebarToggle = document.getElementById('sidebarToggle');
       
        // Handle sidebar toggle
        sidebarToggle.addEventListener('click', () => {
            sidebar.classList.toggle('active');
            overlay.classList.toggle('active');
        });

        // Close sidebar when overlay is clicked
        overlay.addEventListener('click', () => {
            sidebar.classList.remove('active');
            overlay.classList.remove('active');
        });
    </script>
</body>

</html>
