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
        body {
            font-family: Arial, sans-serif;
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
            transition: opacity 0.3s ease-in-out;
        }

        #overlay.active {
            display: block;
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

        #sidebar .nav-link {
            font-weight: 500;
            padding: 10px 15px;
            /* Jarak dalam elemen */
            margin: 5px 10px;
            /* Jarak luar elemen */
            display: flex;
            /* Untuk menyelaraskan konten dalam elemen */
            align-items: center;
            background-color: transparent;
            /* Warna default */
            border-radius: 25px;
            /* Membuat sudut melengkung */
            transition: all 0.3s ease-in-out;
        }

        #sidebar .nav-link:hover {
            color: white;
        }

        #sidebar .nav-link.active {
            background-color: #333;
            /* Warna latar untuk nav-link aktif */
            color: white !important;
        }

        .logout-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            background-color: #333;
            padding: 10px 20px;
            font-size: 16px;
            text-decoration: none;
            border-radius: 8px;
            transition: background-color 0.3s ease;
            width: 100%;
        }

        .logout-btn:hover {
            background-color: #000;
        }

        .logout-container {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            padding: 20px;
        }

        #logoutModal .modal-content {
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        }

        #logoutModal .modal-header {
            background-color: #343a40;
            color: white;
            border-bottom: none;
            text-align: center;
        }

        #logoutModal .modal-title {
            font-size: 20px;
            font-weight: 600;
        }

        #logoutModal .modal-body {
            padding: 20px;
            font-size: 16px;
            color: #333;
            text-align: center;
        }

        #logoutModal .modal-footer {
            border-top: none;
        }

        #logoutModal .btn-secondary {
            background-color: #6c757d;
        }

        #logoutModal .btn-secondary:hover {
            background-color: #5a6268;
        }

        #logoutModal .btn-danger {
            background-color: #6c757d;
        }

        #logoutModal .btn-danger:hover {
            background-color: #5a6268;
        }

        @media (max-width: 768px) {
            #sidebar {
                width: 200px;
            }
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
        <?php
        $current_page = basename($_SERVER['PHP_SELF']); // Mendapatkan nama file saat ini
        ?>
        <ul class="nav flex-column">
            <li class="nav-item mb-3">
                <a class="nav-link text-dark <?php echo $current_page == 'Dashboard.php' ? 'active' : ''; ?>" href="Dashboard.php">
                    <ion-icon name="home-outline" class="me-2"></ion-icon> <span>Beranda</span>
                </a>
            </li>
            
            <li class="nav-item mb-3">
                <a class="nav-link text-dark <?php echo $current_page == 'StudentManagement.php' ? 'active' : ''; ?>" href="StudentManagement.php">
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

    <div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title mx-auto" id="logoutModalLabel">Apakah Anda yakin ingin keluar dari akun Anda?</h5>
                </div>
                <div class="modal-body">
                    <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Batal</button>
                    <a href="../logout.php" class="btn btn-danger">Log Out</a>
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

        function confirmLogout(event) {
            event.preventDefault(); // Prevent langsung keluar
            if (confirm("Apakah Anda yakin ingin log out?")) {
                window.location.href = "../index.php";
            }
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>