<style>
    .nav-link ion-icon {
        margin-left: 5px;
        font-size: 18px;
    }

    .navbar .person-icon {
        font-size: 2rem;
        /* Adjust the size as needed */
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
                        <span id="username"><?php //echo htmlspecialchars($username); 
                                            ?></span>
                    </a>
                </li>

            </ul>
        </div>

    </div>
</nav>