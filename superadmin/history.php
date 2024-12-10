<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>History</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <script type="module" src="https://cdn.jsdelivr.net/npm/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://cdn.jsdelivr.net/npm/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
    <link rel="stylesheet" href="css/history.css">
</head>

<body>
    <!-- Horizontal Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
           
            <button class="btn btn" id="sidebarToggle">
                <img src="css/images/Logo_Sibatta.png" alt="Toggle Sidebar" style="width: 30px; height: 40px; object-fit:cover;">
            </button>
            <span class= "navbar-brand">SIBATTA</span>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
        
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#" onclick="location.reload()">
                            <ion-icon name="refresh-outline"></ion-icon>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="massage.php" data-bs-toggle="modal" data-bs-target="#messageModal" data-bs-target="#sendMessageModal">
                            <ion-icon name="chatbubbles-outline"></ion-icon>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-light" href="#" data-bs-toggle="modal" data-bs-target="#notificationModal">
                            <ion-icon name="notifications-outline"></ion-icon>
                        </a>
                    </li>
                    <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <!-- Add Compose Email Button -->
                <li class="nav-item">
                    <a class="nav-link text-light" href="#" data-bs-toggle="modal" data-bs-target="#emailModal">
                        <ion-icon name="mail-outline"></ion-icon>
                    </a>
                </li>
            </ul>
        </div>
                    <li class="nav-item dropdown">
                    <a class="nav-link text-light" href="#" role="button" data-bs-toggle="modal" aria-expanded="false">
                    <ion-icon name="person-circle-outline"></ion-icon>
                    <span id="username">Username</span>
                    </a>
                </li>
            
                </ul>
            </div>
            
        </div>
    </nav>
    <!-- Sidebar -->
    <div id="sidebar">
        <div class="text-center p-3">
            <img src="css/images/logo_Polinema.png" alt="Logo" width="50" height="40" class="img-fluid">
            <h5 class="mt-2 text-dark">SIBATTA</h5>
        </div>
        <ul class="nav flex-column">
            <li class="nav-item mb-3">
                <a class="nav-link text-dark d-flex align-items-center" href="Dashboard.php">
                    <ion-icon name="home-outline" class="me-2"></ion-icon> <span>Beranda</span>
                </a>
            </li>
                <li class="nav-item mb-3">
                    <a class="nav-link text-dark d-flex align-items-center" href="history.php">
                        <ion-icon name="time-outline" class="me-2"></ion-icon> <span>History</span>
                    </a>
                </li>
                <li class="nav-item mb-3">
                <a class="nav-link text-dark d-flex align-items-center" href="add_user.php">
                    <ion-icon name="cloud-upload-outline" class="me-2"></ion-icon> <span>ADD</span>
                </a>
            </li>
        </ul>
    </div>

    <!-- Overlay -->
    <div id="overlay"></div>

    <!-- Main Content -->
    <div class="container mt-4">
        <h1>History</h1>
        <table class="table table-bordered table-striped mt-4">
            <thead class="table-dark">
                <tr>
                    <th>No</th>
                    <th>Nama File</th>
                    <th>Tanggal Upload</th>
                    <th>Ukuran File</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <!-- Contoh data -->
                <tr>
                    <td>1</td>
                    <td>file1.pdf</td>
                    <td>2024-11-25</td>
                    <td>512 KB</td>
                    <td><button class="btn btn-danger btn-sm">Hapus</button></td>
                </tr>
                <tr>
                    <td>2</td>
                    <td>file2.jpg</td>
                    <td>2024-11-24</td>
                    <td>1.2 MB</td>
                    <td><button class="btn btn-danger btn-sm">Hapus</button></td>
                </tr>
            </tbody>
        </table>
    </div>
    
<!-- Email Pop Up And Notification -->
<div class="modal fade" id="emailModal" tabindex="-1" aria-labelledby="emailModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="emailModalLabel">Emails</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Default Content (Before Compose) -->
                <div id="defaultContent">
                    <ul class="list-group">
                        <li class="list-group-item">You have a new message from Admin</li>
                        
                    </ul>
                    <button class="btn btn-primary mt-3" id="composeBtn">Compose New Email</button>
                </div>

                <!-- Compose Form (Initially Hidden) -->
                <div id="composeForm" style="display: none;">
                    <form method="POST" action="send_email.php" id="emailForm">
                        <!-- Email Address -->
                        <div class="mb-3">
                            <label for="toEmail" class="form-label">Recipient Email</label>
                            <input type="email" class="form-control" id="toEmail" name="toEmail" required>
                            <div class="invalid-feedback">Please enter a valid email address.</div>
                        </div>

                        <!-- Subject -->
                        <div class="mb-3">
                            <label for="subject" class="form-label">Subject</label>
                            <input type="text" class="form-control" id="subject" name="subject" required>
                            <div class="invalid-feedback">Please enter a subject.</div>
                        </div>

                        <!-- Message -->
                        <div class="mb-3">
                            <label for="message" class="form-label">Message</label>
                            <textarea class="form-control" id="message" name="message" rows="5" required></textarea>
                            <div class="invalid-feedback">Please enter your message.</div>
                        </div>

                        <!-- Submit Button -->
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">Send</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

    <div class="modal fade" id="notificationModal" tabindex="-1" aria-labelledby="notificationModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="notificationModalLabel">Notifications</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <ul class="list-group">
                        <li class="list-group-item">New file uploaded</li>

                    </ul>
                </div>
            </div>
        </div>
    </div>

    

    <!-- Modal Pop-Up -->
    <div class="modal fade" id="messageModal" tabindex="-1" aria-labelledby="messageModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="messageModalLabel">Report</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
         
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="message" class="form-label">Pesan</label>
                        <textarea class="form-control" id="message" name="message" rows="1" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="chatFileInput" class="form-label">Lampiran</label>
                        <input type="file" id="chatFileInput" name="chat_file" class="form-control">
                    </div>
                </div>
            </div>
        </div>
    </div>

   <!-- Modal Logout -->
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
            <a href="login.php" class="btn btn-danger">Log Out</a>
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
