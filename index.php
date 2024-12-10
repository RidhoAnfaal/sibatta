<?php
// Start the session
session_start();

// Include the database connection
require './user/Student/koneksi.php';

// Placeholder for message if login fails
$message = "";

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Collect form data
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Validate input
    if (!empty($username) && !empty($password)) {
        // Query to check if the user exists and password matches
        $sql = "SELECT * FROM [sibatta].[user] WHERE username = ? AND password = ?";
        $params = array($username, $password);

        $stmt = sqlsrv_query($conn, $sql, $params);

        // Check if the query executed successfully
        if ($stmt === false) {
            die(print_r(sqlsrv_errors(), true));
        }

        // Fetch the user data
        if ($user = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            // Set session variables
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];  // Store the role in the session

            // Redirect based on user role
            if ($user['role'] == 'super_admin') {
                header('Location: ./superadmin/main.php'); // Redirect to super admin dashboard
            } elseif ($user['role'] == 'admin') {
                header('Location: ./user/admin/main_admin.php'); // Redirect to admin dashboard
            } elseif ($user['role'] == 'student') {
                header('Location: ./user/Student/main_student.php'); // Redirect to student dashboard
            }
            exit();
        } else {
            // Invalid username or password
            $message = "Invalid username or password!";
        }
    } else {
        $message = "Please fill in all fields!";
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
    <link rel="stylesheet" href="./superadmin//css//login.css">
</head>

<body class="d-flex align-items-center py-4 bg-body-tertiary">
    <main class="form-signin w-100 m-auto">
        <div class="container">
            <!-- Login Form -->
            <form class="needs-validation" novalidate method="POST" action="">
                <div class="header text-center mb-4">
                    <img src="logo_Polinema.png" alt="Logo" class="logo" />
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
        // Client-side validation
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
