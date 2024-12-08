<?php
require('fpdf/fpdf.php');

$uploadDir = 'uploads';

$files = array_diff(scandir($uploadDir), array('..', '.')); // Mengambil semua file, kecuali '.' dan '..'

// Membuat folder jika belum ada
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Data formulir
    $username = !empty($_POST['username']) ? htmlspecialchars($_POST['username']) : 'Fahreiza Taura Muhammadani';
    $email = !empty($_POST['email']) && filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) ? $_POST['email'] : 'fahreizataura@gmail.com';
    $message = !empty($_POST['message']) ? htmlspecialchars($_POST['message']) : 'Anda Telah Bebas Tanggungan';
    $fileName = 'SURAT_BEBAS_TANGGUNGAN_' . time() . '.pdf';

    // Membuat PDF
    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 16);

    // Header PDF
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(0, 5, 'KEMENTERIAN PENDIDIKAN, KEBUDAYAAN', 0, 1, 'C');
    $pdf->Cell(0, 5, 'RISET, DAN TEKNOLOGI', 0, 1, 'C');
    $pdf->Cell(0, 5, 'POLITEKNIK NEGERI MALANG', 0, 1, 'C');
    $pdf->Cell(0, 5, 'JURUSAN TEKNOLOGI INFORMASI', 0, 1, 'C');
    $pdf->Cell(0, 5, 'PROGRAM STUDI SISTEM INFORMASI BISNIS', 0, 1, 'C');
    $pdf->SetFont('Arial', '', 7);
    $pdf->Cell(0, 5, 'Jalan Soekarno-Hatta Malang 65145 Telp. (0341) 404424', 0, 1, 'C');
    $pdf->Ln(2);
    $pdf->Line(0, $pdf->GetY(), 210, $pdf->GetY()); // Specify the end points for the line
    $pdf->Line(0, $pdf->GetY(), 230, $pdf->GetY()); // Specify the end points for the line
    
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->Cell(0, 10, 'BEBAS TANGGUNGAN', 0, 1, 'C');
    $pdf->Ln(5);

    // Isi PDF
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->MultiCell(0, 10, "Nama  : $username\nNIM    :\nProgram Studi : D4 - Sistem Informasi Bisnis\n\n");
    
    $pdf->Cell(190, 10, 'SUB.BAGIAN',1,'C');
    $pdf->Ln(0);
    $pdf->Cell(190, 10, 'JURUSAN', 1,'C');
    $pdf->SetFont('Arial', '', 10);
    $pdf->Ln(0);
    $pdf->Cell(100, 10, 'Tugas Akhir', 1,'C');
    $pdf->Cell(100, 10, 'Tanggal', 1, 1);
    
    $pdf->Cell(60, 10, 'Sumbangan', 1);
    $pdf->Cell(0, 10, 'Tugas Akhir Buku', 1, 1);

    $pdf->Ln(10);

    $pdf->Cell(0, 10, 'ADMINISTRASI PROGRAM STUDI', 0, 1);
    $pdf->Cell(60, 10, 'Distribusi Buku Skripsi', 1);
    $pdf->Cell(0, 10, 'Tanggal', 1, 1);

    $pdf->Cell(60, 10, 'Distribusi Laporan Akhir', 1);
    $pdf->Cell(0, 10, 'Kompenasi', 1, 1);

    $pdf->Ln(10);
    $pdf->SetFont('Arial', 'I', 10);
    $pdf->MultiCell(0, 10, "Mengetahui,\nKetua Jurusan\nDr. Eng. Rosa Andrie Asmara, S.T., M.T.\nNIP: 1980010102005011001");

    // Menyimpan PDF ke folder uploads
    $pdfFilePath = $uploadDir . '/' . $fileName;
    $pdf->Output('F', $pdfFilePath);

    // Mengirimkan email dengan lampiran PDF jika file berhasil dibuat
    if (file_exists($pdfFilePath)) {
       
    } else {
        echo 'Gagal membuat file PDF.';
    }
}
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Approve</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <script type="module" src="https://cdn.jsdelivr.net/npm/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://cdn.jsdelivr.net/npm/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
    <link rel="stylesheet" href="css/TA.css">
</head>

<body>
    <!-- Horizontal Navbar -->
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
                        <a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#messageModal" data-bs-target="#sendMessageModal">
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
            <img src="css/images/Logo_Sibatta.png" alt="Logo" width="50" height="40" class="img-fluid">
            <h5 class="mt-2 text-dark">SIBATTA</h5>
        </div>
        <ul class="nav flex-column">
            <li class="nav-item mb-3">
                <a class="nav-link text-dark d-flex align-items-center" href="main_admin.php">
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

    <!-- Overlay -->
    <div id="overlay"></div>

    <!-- Main Content -->
    <div class="container mt-4">
        <!-- <h1>APPROVE</h1>
        <?php if ($message): ?>
            <div class="alert alert-info"><?php echo $message; ?></div>
        <?php endif; ?> -->

        <!-- Table -->
        <div class="table-container mt-4">
            <h3>form untuk mengirim surat jika student sudah approve</h3>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Username</th>
                        <th>email</th>
                        <th>File Name</th>
                        <th>File Size</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($files)): ?>
                        <?php foreach ($files as $index => $file): ?>
                            <tr>
                                <td><?php echo $index + 1; ?></td>
                                <td></td>
                                <td></td>
                                <td><?php echo $file; ?></td>
                                <!-- <td><?php echo number_format(filesize($uploadDir . $file) / 1024, 2) . ' KB'; ?></td>
                                <td><?php echo date('d-m-Y H:i:s', filemtime($uploadDir . $file)); ?></td> -->
                                <td>
                                    <div class="d-flex align-items-center">
                                    <form action="approve.php" method="POST">
                            <button type="submit" class="btn btn-success btn-sm">Approve</button>
                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center">No files uploaded yet.</td>
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
        emailModal.addEventListener('hidden.bs.modal', function() {
            // Reset content to show default content
            document.getElementById("defaultContent").style.display = "block";
            document.getElementById("composeForm").style.display = "none";
        });
    </script>
</body>

</html>