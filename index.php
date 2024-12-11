<?php
session_start();
require 'koneksi.php';
require 'cek.php';

// message if login fails
$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Init
    $db = new Koneksi();
    $userModel = new cek($db);

    $user = $userModel->login($username, $password);

    if ($user) {
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['username'] = $user['username'];

        // Redirect based on role
        switch ($user['role']) {
            case 'super_admin':
                header("Location: superadmin/Dashboard.php");
                break;
            case 'admin':
                header("Location: admin/Dashboard.php");
                break;
            case 'student':
                header("Location: Student/Dashboard.php");
                break;
            default:
                $message = "Invalid role.";
                break;
        }
        exit;
    } else {
        $message = "Invalid username or password.";
    }
}
?>

<!doctype html>
<html lang="en" data-bs-theme="auto">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>PBL</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="superadmin/css/login.css">
</head>

<body class="d-flex align-items-center py-4 bg-body-tertiary">
    <main class="form-signin w-100 m-auto">
        <div class="container">
            <!-- form login -->
            <form class="needs-validation" novalidate method="POST" action="">
                <div class="header text-center mb-4">
                    <img src="superadmin/css/images/Logo_Sibatta.png" alt="Logo" class="logo" />
                </div>

                <h1 class="h3 mb-3 fw-normal text-center" style="color: black;">SIBATTA</h1>

                <?php if (!empty($message)): ?>
                    <div class="alert alert-danger text-center"><?php echo $message; ?></div>
                <?php endif; ?>

                <!-- input -->
                <div class="form-floating mb-3">
                    <input name="username" type="text" class="form-control" id="floatingInput" placeholder="Enter Username" required>
                    <label for="floatingInput">Username</label>
                    <div class="invalid-feedback">
                        Please enter your registered username.
                    </div>
                </div>

                <!-- input pw -->
                <div class="form-floating mb-3">
                    <input name="password" type="password" class="form-control" id="floatingPassword" placeholder="Password" required>
                    <label for="floatingPassword">Password</label>
                    <span id="togglePassword" style="position: absolute; right: 10px; top: 35%; cursor: pointer;">👁️‍🗨️</span>
                    <div class="invalid-feedback">
                        Please enter your password.
                    </div>
                </div>

                <!-- submit btn -->
                <button class="btn btn-primary w-100 py-2" type="submit">Login</button>
            </form>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Client-side validation
        (function() {
            'use strict';
            // Ambil semua formulir dengan class 'needs-validation'
            var forms = document.querySelectorAll('.needs-validation');
            // Loop semua form dan tambahkan event listener
            Array.prototype.slice.call(forms).forEach(function(form) {
                form.addEventListener('submit', function(event) {
                    if (!form.checkValidity()) {
                        // Jika form tidak valid, hentikan submit
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    // Tambahkan class 'was-validated' untuk memberikan feedback
                    form.classList.add('was-validated');
                }, false);
            });
        })();
        // Toggle visibility password
        const passwordInput = document.getElementById('floatingPassword');
        const togglePassword = document.getElementById('togglePassword');
        // Tambahkan event listener untuk toggle password
        togglePassword.addEventListener('click', () => {
            // Ubah tipe input antara 'password' dan 'text'
            const type = passwordInput.type === 'password' ? 'text' : 'password';
            passwordInput.type = type;
            // Ubah ikon (atau teks) berdasarkan tipe input
            togglePassword.textContent = type === 'password' ? '👁️‍🗨️' : '👁️‍🗨️';
        });
    </script>