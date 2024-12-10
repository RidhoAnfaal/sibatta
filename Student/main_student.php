<?php
// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    // Redirect to login page if not logged in
    header("Location: /sibatta/index.php");
    exit;
}

// Get the logged-in user's username
$username = $_SESSION['username'];
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
    <title>Home</title>
    <style>
        /* Style for hidden sidebar */
        #sidebar {
            position: fixed;
            left: -250px;
            top: 56px;
            /* Adjust to match the height of the horizontal navbar */
            height: calc(100vh - 56px);
            width: 250px;
            background-color: #f8f9fa;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
            transition: left 0.3s ease-in-out;
            z-index: 1050;
            /* Ensure it is above other content */
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
            /* Just below the sidebar */
            display: none;
        }

        #overlay.active {
            display: block;
        }

        /* Ensure the sidebar toggle button is clickable */
        #sidebarToggle {
            z-index: 1060;
        }
    </style>
</head>

<body>
    <!-- Horizontal Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
           
            <button class="btn btn" id="sidebarToggle">
                <img src="css/images/Logo_Sibatta.png" alt="Toggle Sidebar" style="width: 30px; height: 40px; object-fit:cover;">
                <span class= "navbar-brand" >SIBATTA</span>
            </button>
            
            <div >
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link text-light" href="#" role="button" data-bs-toggle="modal" aria-expanded="false">
                            <ion-icon name="person-circle-outline"></ion-icon>
                            <span id="username"><?php echo htmlspecialchars($username); ?></span>
                        </a>
                    </li>
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
    </a>
</div>

    </div>  

    <!-- Overlay -->
    <div id="overlay"></div>

    <!-- Main Content -->
    <div class="container mt-4">
        <h1>Welcome to SIBATTA</h1>
        <p>Lorem, ipsum dolor sit amet consectetur adipisicing elit. Molestiae facere obcaecati asperiores quod animi vero maxime quidem nobis enim suscipit. Alias illum dolores debitis reiciendis ea numquam eum. Deleniti, aut.
            Lorem ipsum dolor sit amet consectetur adipisicing elit. Dignissimos consectetur numquam magni sapiente, velit fugiat dolore alias nemo. Veritatis esse labore non nam praesentium beatae unde quod, quam modi expedita.
        </p>
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
            <a href="/sibatta/logout.php" class="btn btn-danger">Log Out</a>
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

        function confirmLogout(event) {
    event.preventDefault(); // Prevent langsung keluar
    if (confirm("Apakah Anda yakin ingin log out?")) {
        window.location.href = "login.php";
    }
}
document.getElementById("composeBtn").addEventListener("click", function() {
        // Hide default content and show the compose form
        document.getElementById("defaultContent").style.display = "none";
        document.getElementById("composeForm").style.display = "block";
    });

    // Reset modal content when it is closed
    const emailModal = document.getElementById('emailModal');
    emailModal.addEventListener('hidden.bs.modal', function () {
        // Reset content to show default content
        document.getElementById("defaultContent").style.display = "block";
        document.getElementById("composeForm").style.display = "none";
    });

    
    </script>
</body>

</html>
