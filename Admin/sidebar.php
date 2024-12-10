<style>
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
.logout-btn {
    display: flex;
    align-items: center;
    gap: 8px;
    color: white;
    background-color: #1e2235; /* Warna merah */
    padding: 10px 20px;
    font-size: 16px;
    text-decoration: none;
    border-radius: 8px;
    transition: background-color 0.3s ease;
}

.logout-btn:hover {
    background-color: #555; /* Efek hover */
}

.modal-footer {
    position: absolute;
    bottom: 20px;
    left: 20px;
    width: calc(100% - 40px);
}

/* Modal Styling */
#logoutModal .modal-content {
    border-radius: 10px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
}

#logoutModal .modal-header {
    border-bottom: none; /* Menghilangkan garis bawah header */
    padding: 15px 20px;
    text-align: center;
}

#logoutModal .modal-title {
    font-size: 20px;
    font-weight: 600;
    margin: 0 auto; /* Memastikan judul berada di tengah */
}

#logoutModal .modal-body {
    padding: 20px;
    font-size: 16px;
    line-height: 1.5;
    color: #333;
}

#logoutModal .modal-footer {
    border-top: none; /* Menghilangkan garis atas footer */
    padding: 15px 20px;
}

#logoutModal .btn {
    width: 100px; /* Membuat tombol memiliki lebar konsisten */
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
<div id="sidebar">
        <div class="text-center p-3">
            <img src="css/images/Logo_Sibatta.png" alt="Logo" width="50" height="40" class="img-fluid">
            <h5 class="mt-2 text-dark">SIBATTA</h5>
        </div>
        <ul class="nav flex-column">
            <li class="nav-item mb-3">
                <a class="nav-link text-dark d-flex align-items-center" href="main.php">
                    <ion-icon name="home-outline" class="me-2"></ion-icon> <span>Beranda</span>
                </a>
            </li>
            <li class="nav-item mb-3">
                <a class="nav-link text-dark d-flex align-items-center" href="Tugas_akhir.php">
                    <ion-icon name="time-outline" class="me-2"></ion-icon> <span>Tugas Akhir</span>
                </a>
            </li>
            <li class="nav-item mb-3">
                <a class="nav-link text-dark d-flex align-items-center" href="Approve.php">
                <ion-icon name="library-outline" class="me-2"></ion-icon> <span>Approve</span>
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