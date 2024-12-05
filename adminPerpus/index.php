<?php
// Start the session
session_start();
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Guest';

// Placeholder for message if login fails
$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Collect form data
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Simulate database check (you should replace this with actual database validation)
    if ($username == "your_correct_username" && $password == "your_correct_password") {
        // Set session variables and redirect to the main page
        $_SESSION['username'] = $username;
        header('Location: main.php'); // Redirect to main.php on success
        exit();
    } else {
        // Display error message if login fails
        $message = "Invalid NIM or Password!";
    }

}
// Simulasi nama pengguna yang disimpan di session

?>
<!-- Replace in HTML -->
<span id="username"><?php echo $username; ?></span>

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
    /* background-image: url('images/Graha.jpg'); */
    background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url(' css/images/loginBg.png');
    background-size: cover; /* Menutup seluruh layar */
    background-position: center;
    background-repeat: no-repeat;
   
  }
  
  .logo {
    width: 150px;
    height: auto; 
    display: block;
    margin: 0 auto; 
  }
  .container {
    width: 300px;
    margin: 0 auto;
    padding: 20px;
    border: 1px solid #f3f3f3;
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
  
  .form-signin input[type="email"] {
    margin-bottom: -1px;
    border-bottom-right-radius: 0;
    border-bottom-left-radius: 0;
  }
  
  .form-signin input[type="password"] {
    margin-bottom: 10px;
    border-top-left-radius: 0;
    border-top-right-radius: 0;
  }
  img {
    border-radius: 8px;
  }
  
    </style>
</head>

<body class="d-flex align-items-center py-4 bg-body-tertiary">
    <main class="form-signin w-100 m-auto">
        <div class="container">
            <!-- Login Form -->
            <form class="needs-validation" novalidate method="POST" action="main.php">
                <div class="header text-center mb-4">
                    <img src="css/images/Logo_Sibatta.png" alt="Logo" class="logo" />
                </div>
                
                <h1 class="h3 mb-3 fw-normal text-center" style="color: black;">SIBATTA</h1>
                
                <?php if (!empty($message)): ?>
                    <div class="alert alert-danger text-center"><?php echo $message; ?></div>
                <?php endif; ?>
                
                <!-- NIM Input -->
                <div class="form-floating mb-3">
                    <input name="username" type="" class="form-control" id="floatingInput" placeholder="Enter NIM" required>
                    <label for="floatingInput">NIM</label>
                    <div class="invalid-feedback">
                        Masukan NIM Yang Terdaftar
                    </div>
                </div>

                <!-- Password Input -->
                <div class="form-floating mb-3">
                    <input name="password" type="password" class="form-control" id="floatingPassword" placeholder="Password" required>
                    <label for="floatingPassword">Password</label>
                    <div class="invalid-feedback">
                        Masukan Password.
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
