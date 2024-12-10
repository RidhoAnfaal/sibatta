<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sidebar with Logout</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
    <style>
        #overlay {
            position: fixed;
            top: 0; 
            left: 0;
            width: 100vw;
            height: 100vh; 
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1070; 
            display: none;
            transition: opacity 0.3s ease-in-out;
        }

        #overlay.active {
            display: block;
            opacity: 1;
        }

        #sidebar {
            position: fixed;
            left: -250px;
            top: 0; 
            height: 100vh; 
            width: 250px;
            background-color: #f8f9fa;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
            transition: left 0.3s ease-in-out;
            z-index: 1060; 
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

        .logout-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            background-color: #1e2235;
            padding: 10px 20px;
            font-size: 16px;
            text-decoration: none;
            border-radius: 8px;
            transition: background-color 0.3s ease;
            width: 100%;
            height: 100%;
        }

        .logout-btn:hover {
            background-color: #555;
        }

        .logout-container {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            padding: 20px;
        }

        /* Modal Styling */
        #logoutModal .modal-content {
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        }

        #logoutModal .modal-header {
            border-bottom: none;
            padding: 15px 20px;
            text-align: center;
        }

        #logoutModal .modal-title {
            font-size: 20px;
            font-weight: 600;
            margin: 0 auto;
        }

        #logoutModal .modal-body {
            padding: 20px;
            font-size: 16px;
            line-height: 1.5;
            color: #333;
        }

        #logoutModal .modal-footer {
            border-top: none;
            padding: 15px 20px;
        }

        #logoutModal .btn {
            width: 100px;
        }

        #logoutModal .btn-secondary {
            background-color: #6c757d;
            border: none;
        }

        #logoutModal .btn-secondary:hover {
            background-color: #5a6268;
        }

        #logoutModal .btn-danger {
            background-color: #dc3545;
            border: none;
        }

        #logoutModal .btn-danger:hover {
            background-color: #c82333;
        }
    </style>
</head>

<body>
    <!-- Overlay -->
    <div id="overlay"></div>

    <!-- Sidebar -->
    <div id="sidebar">
        <div class="text-center p-3">
            <img src="css/images/Logo_Sibatta.png" alt="Logo" width="50" height="40" class="img-fluid">
            <h5 class="mt-2 text-dark">SIBATTA</h5>
        </div>
        <ul class="nav flex-column">
            <li class="nav-item mb-3">
                <a class="nav-link text-dark d-flex align-items-center" href="Dashboard.php">
                    <ion-icon name="home-outline"></ion-icon> <span>Beranda</span>
                </a>
            </li>
            <li class="nav-item mb-3">
                <a class="nav-link text-dark d-flex align-items-center" href="history.php">
                    <ion-icon name="time-outline"></ion-icon> <span>Riwayat</span>
                </a>
            </li>
            <li class="nav-item mb-3">
                <a class="nav-link text-dark d-flex align-items-center" href="add_admin.php">
                <ion-icon name="cloud-upload-outline" class="me-2"></ion-icon> <span>Tambah Admin</span>
                </a>
            </li>
            <li class="nav-item mb-3">
                <a class="nav-link text-dark d-flex align-items-center" href="add_user.php">
                <ion-icon name="cloud-upload-outline" class="me-2"></ion-icon> <span>Tambah User</span>
                </a>
            </li>
        </ul>
        <div class="logout-container">
            <button class="logout-btn btn btn-danger" data-bs-toggle="modal" data-bs-target="#logoutModal">
                <ion-icon name="log-out-outline"></ion-icon>
                <span>Log Out</span>
            </button>
    </div>
        </div>

    <!-- Log Out -->
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
                    <a href="index.php" class="btn btn-danger">Log Out</a>
                </div>

            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script>
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('overlay');

        sidebarToggle?.addEventListener('click', () => {
            sidebar.classList.toggle('active');
            overlay.classList.toggle('active');
        });

        overlay.addEventListener('click', () => {
            sidebar.classList.remove('active');
            overlay.classList.remove('active');
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>