<?php
// Start the session
session_start();

// Check if the user is logged in, if not redirect to login page
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

$username = $_SESSION['username']; // Get the username from session
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <script type="module" src="https://cdn.jsdelivr.net/npm/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://cdn.jsdelivr.net/npm/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
    <link rel="stylesheet" href="css/dashboard.css">
    <title>Home</title>
  
</head>
<body>
   <!-- Header -->
   <?php include 'Header.php'; ?>

   <div class="d-flex">
        <?php include 'Sidebar.php'; ?>

    <!-- Main Content -->
    <div class="container mt-4">
    <?php 
        include 'Header.php';
        include 'Sidebar.php';
        ?>
        <h1>Welcome to SIBATTA</h1>
        <p>Lorem, ipsum dolor sit amet consectetur adipisicing elit. Molestiae facere obcaecati asperiores quod animi vero maxime quidem nobis enim suscipit. Alias illum dolores debitis reiciendis ea numquam eum. Deleniti, aut.
            Lorem ipsum dolor sit amet consectetur adipisicing elit. Dignissimos consectetur numquam magni sapiente, velit fugiat dolore alias nemo. Veritatis esse labore non nam praesentium beatae unde quod, quam modi expedita.
        </p>
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
    emailModal.addEventListener('hidden.bs.modal', function () {
        // Reset content to show default content
        document.getElementById("defaultContent").style.display = "block";
        document.getElementById("composeForm").style.display = "none";
    });

    
    </script>
</body>

</html>