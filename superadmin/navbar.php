<style>
    .nav-link ion-icon {
        margin-left: 5px;
        font-size: 18px;
    }
    .navbar .person-icon {
        font-size: 2rem; /* Adjust the size as needed */
    }
</style>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <button class="btn btn" id="sidebarToggle">
            <img src="css/images/Logo_Sibatta.png" alt="Toggle Sidebar" style="width: 30px; height: 40px; object-fit:cover;">
        </button>
        <span class="navbar-brand">SIBATTA</span>
        
        
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
                <ul class="navbar-nav ms-auto">
                        <!-- Add Compose Email Button -->
                        <li class="nav-item">
                            <a class="nav-link text-light" href="#" data-bs-toggle="modal" data-bs-target="#emailModal">
                                <ion-icon name="mail-outline"></ion-icon>
                            </a>
                        </li>
                    </ul>
                <li class="nav-item position-relative">
                    <!-- Username Button -->
                    <a class="nav-link text-light" href="#" id="usernameToggle" role="button" aria-expanded="false" data-bs-toggle="dropdown" aria-haspopup="true" aria-controls="userFeatures">
                        <ion-icon name="person-circle-outline"></ion-icon>
                        <span id="username"><?php echo htmlspecialchars($username); ?></span>
                    </a>

                    <!-- Feature Dropdown -->
                    <div id="userFeatures" class="dropdown-menu position-absolute bg-light shadow-sm" aria-labelledby="usernameToggle" style="right: 0; top: 110%; z-index: 1050; width: 200px;">
                        <a class="dropdown-item d-flex align-items-center <?php echo $current_page == 'userInfo.php' ? 'active' : ''; ?>" href="userInfo.php">
                            <ion-icon name="person-circle-outline" class="me-2"></ion-icon> Profil
                        </a>
                        <a class="dropdown-item d-flex align-items-center" href="../logout.php">
                            <ion-icon name="log-out-outline" class="me-2"></ion-icon> Logout
                        </a>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</nav>
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
                    <label for="message" class="form-label">Message</label>
                    <textarea class="form-control" id="message" name="message" rows="1" required></textarea>
                </div>
                <div class="mb-3">
                    <label for="chatFileInput" class="form-label">Attachment</label>
                    <input type="file" id="chatFileInput" name="chat_file" class="form-control">
                </div>
            </div>
        </div>
    </div>
</div>


<script>
    // This script enables the dropdown functionality for the username button
    document.getElementById("usernameToggle").addEventListener("click", function () {
        var menu = document.getElementById("userFeatures");
        menu.style.display = (menu.style.display === "none" || menu.style.display === "") ? "block" : "none";
    });

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

    document.getElementById('usernameToggle').addEventListener('click', function(e) {
        e.preventDefault(); // Mencegah reload halaman
        const features = document.getElementById('userFeatures');
        features.style.display = features.style.display === 'none' || features.style.display === '' ? 'block' : 'none';
    });

    // Ambil elemen tombol dan fitur
    const usernameToggle = document.getElementById('usernameToggle');
    const userFeatures = document.getElementById('userFeatures');

    // Tutup dropdown saat klik di luar elemen
    document.addEventListener('click', function (e) {
        if (!usernameToggle.contains(e.target) && !userFeatures.contains(e.target)) {
            userFeatures.style.display = 'none';
        }
    });
</script>
