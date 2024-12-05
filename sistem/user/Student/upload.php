<?php
include 'koneksi.php'; // Include the database connection
session_start();

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header('Location: login.php'); // Redirect to login if not logged in
    exit();
}

// Retrieve the logged-in user's username
$username = $_SESSION['username'];

// Get the user ID associated with the username
$sql = "SELECT user_id FROM [sibatta].[user] WHERE username = ?";
$params = array($username);
$stmt = sqlsrv_query($conn, $sql, $params);
$user = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

if ($user) {
    $userId = $user['user_id'];
} else {
    // Handle the case if the user is not found in the database
    $userId = 0;
}

$uploadDir = 'uploads/';
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

$message = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['files'])) {
    // Retrieve the title from the form
    $title = isset($_POST['title']) ? $_POST['title'] : '';
    $uploadedAt = date('Y-m-d'); // Current date

    if ($userId > 0 && !empty($title)) {
        foreach ($_FILES['files']['tmp_name'] as $key => $tmpName) {
            $fileName = $_FILES['files']['name'][$key];
            $fileTmpName = $_FILES['files']['tmp_name'][$key];
            $fileError = $_FILES['files']['error'][$key];

            if ($fileError === UPLOAD_ERR_OK) {
                // Create a unique file name to avoid overwriting
                $targetFile = $uploadDir . time() . '_' . basename($fileName);
                if (move_uploaded_file($fileTmpName, $targetFile)) {
                    // Insert file data into the database
                    $sql = "INSERT INTO [sibatta].[document] (user_id, title, uploaded_at) 
                            VALUES (?, ?, ?)";
                    $params = [$userId, $title, $uploadedAt];
                    $stmt = sqlsrv_query($conn, $sql, $params);

                    if ($stmt) {
                        $message = 'File uploaded and saved to the database successfully!';
                    } else {
                        $message = 'Database error: ' . print_r(sqlsrv_errors(), true);
                    }
                } else {
                    $message = 'Error moving uploaded file.';
                }
            } else {
                $message = 'There was an error with the file upload.';
            }
        }
    } else {
        $message = 'Please provide a valid Title.';
    }
}

// Retrieve list of uploaded documents from the database
$sql = "SELECT document_id, title, uploaded_at, validated_by FROM [sibatta].[document]";
$stmt = sqlsrv_query($conn, $sql);
$documents = [];
if ($stmt) {
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        $documents[] = $row;
    }
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
    <link rel="stylesheet" href="css/upload.css">
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
                <a class="nav-link text-dark d-flex align-items-center" href="main_user.php">
                    <ion-icon name="home-outline" class="me-2"></ion-icon> <span>Beranda</span>
                </a>
            </li>
            <li class="nav-item mb-3">
                <a class="nav-link text-dark d-flex align-items-center" href="upload.php">
                    <ion-icon name="time-outline" class="me-2"></ion-icon> <span>Upload TA</span>
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
        <h1>Upload File</h1>
        <?php if ($message): ?>
            <div class="alert alert-info"><?php echo $message; ?></div>
        <?php endif; ?>

        <!-- Form Upload -->
        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="title" class="form-label">Title</label>
                <input type="text" class="form-control" id="title" name="title" required>
            </div>
            <div class="mb-3">
                <label for="fileInput" class="form-label">Select Files</label>
                <input type="file" class="form-control" id="fileInput" name="files[]" multiple>
            </div>
            <button type="submit" class="btn btn-primary">Upload</button>
        </form>

        <!-- Uploaded Files -->
        <div class="table-container mt-4">
            <h3>Uploaded Documents</h3>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Document ID</th>
                        <th>Title</th>
                        <th>Uploaded At</th>
                        <th>Validated By</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($documents)): ?>
                        <?php foreach ($documents as $doc): ?>
                            <tr>
                                <td><?php echo $doc['document_id']; ?></td>
                                <td><?php echo $doc['title']; ?></td>
                                <td><?php echo $doc['uploaded_at']->format('Y-m-d'); ?></td>
                                <td><?php echo $doc['validated_by'] ?: 'Not validated'; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-center">No documents uploaded yet.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
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
    <!-- Notif -->
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
        emailModal.addEventListener('hidden.bs.modal', function() {
            // Reset content to show default content
            document.getElementById("defaultContent").style.display = "block";
            document.getElementById("composeForm").style.display = "none";
        });
    </script>
</body>

</html>