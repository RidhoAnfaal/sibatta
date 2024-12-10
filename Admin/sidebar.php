<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sidebar with Overlay</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
    <style>
        /* Overlay */
        #overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1060;
            display: none;
            transition: opacity 0.3s ease-in-out;
        }

        #overlay.active {
            display: block;
            opacity: 1;
        }

        /* Sidebar */
        #sidebar {
            position: fixed;
            left: -250px;
            top: 0;
            height: 100vh;
            width: 250px;
            background-color: #f8f9fa;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
            transition: left 0.3s ease-in-out;
            z-index: 1070;
        }

        #sidebar.active {
            left: 0;
        }

        /* Sidebar items */
        .nav-link ion-icon {
            margin-right: 10px;
        }

        /* Logout button styling */
        .logout-btn {
            display: flex;
            align-items: center;
            gap: 8px;
            color: white;
            background-color: #1e2235;
            padding: 10px 20px;
            font-size: 16px;
            text-decoration: none;
            border-radius: 8px;
            transition: background-color 0.3s ease;
        }

        .logout-btn:hover {
            background-color: #555;
        }

        /* Navbar */
        .navbar {
            z-index: 1050;
        }

        .navbar .person-icon {
            font-size: 2rem;
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
                <a class="nav-link text-dark d-flex align-items-center" href="main.php">
                    <ion-icon name="home-outline"></ion-icon> <span>Beranda</span>
                </a>
            </li>
            <li class="nav-item mb-3">
                <a class="nav-link text-dark d-flex align-items-center" href="Tugas_akhir.php">
                    <ion-icon name="time-outline"></ion-icon> <span>Tugas Akhir</span>
                </a>
            </li>
            <li class="nav-item mb-3">
                <a class="nav-link text-dark d-flex align-items-center" href="Approve.php">
                    <ion-icon name="library-outline"></ion-icon> <span>Approve</span>
                </a>
            </li>
        </ul>
        <div class="p-3">
            <button class="logout-btn btn btn-danger" data-bs-toggle="modal" data-bs-target="#logoutModal">
                <ion-icon name="log-out-outline"></ion-icon>
                <span>Log Out</span>
            </button>
        </div>
    </div>

    <!-- JavaScript -->
    <script>
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('overlay');

        sidebarToggle.addEventListener('click', () => {
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