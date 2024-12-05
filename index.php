<?php
// Start the session
session_start();

// Include cekLogin.php for the login logic
require 'cekLogin.php';

// Placeholder for error message
$message = isset($message) ? $message : ""; // Use the $message variable set in cekLogin.php if available
?>

<!doctype html>
<html lang="en" data-bs-theme="auto">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>PBL</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <style>
        html, body {
            height: 100%;
            margin: 0;
            font-family: Arial, sans-serif;
        }
        body {
            background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('loginImg/loginBg.png');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }
        .logo {
            width: 150px;
            display: block;
            margin: 0 auto;
        }
        .container {
            width: 300px;
            margin: 0 auto;
            padding: 20px;
            border-radius: 5px;
            background-color: #f3f3f3;
        }
        .form-signin {
            max-width: 330px;
            padding: 1rem;
        }
        .form-signin .form-floating:focus-within {
            z-index: 2;
        }
    </style>
</head>

<body class="d-flex align-items-center py-4 bg-body-tertiary">
    <main class="form-signin w-100 m-auto">
        <div class="container">
            <!-- Login Form -->
            <form class="needs-validation" novalidate method="POST" action="cekLogin.php">
                <div class="header text-center mb-4">
                    <img src="loginImg/Logo_Sibatta.png" alt="Logo" class="logo" />
                </div>

                <h1 class="h3 mb-3 fw-normal text-center" style="color: black;">SIBATTA</h1>

                <?php if (!empty($message)): ?>
                    <div class="alert alert-danger text-center"><?php echo $message; ?></div>
                <?php endif; ?>

                <!-- Username Input -->
                <div class="form-floating mb-3">
                    <input name="username" type="text" class="form-control" id="floatingInput" placeholder="Enter Username" required>
                    <label for="floatingInput">Username</label>
                    <div class="invalid-feedback">
                        Please enter your registered username.
                    </div>
                </div>

                <!-- Password Input -->
                <div class="form-floating mb-3">
                    <input name="password" type="password" class="form-control" id="floatingPassword" placeholder="Password" required>
                    <label for="floatingPassword">Password</label>
                    <div class="invalid-feedback">
                        Please enter your password.
                    </div>
                </div>

                <!-- Submit Button -->
                <button class="btn btn-primary w-100 py-2" type="submit">Login</button>
            </form>
        </div>
    </main>

    <!-- Optional JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Basic client-side validation
        (function () {
            'use strict'
            var forms = document.querySelectorAll('.needs-validation')
            Array.prototype.slice.call(forms)
                .forEach(function (form) {
                    form.addEventListener('submit', function (event) {
                        if (!form.checkValidity()) {
                            event.preventDefault()
                            event.stopPropagation()
                        }
                        form.classList.add('was-validated')
                    }, false)
                })
        })()
    </script>
</body>

</html>
