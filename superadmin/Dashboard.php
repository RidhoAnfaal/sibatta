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
    <link rel="stylesheet" href="css/main.css">
    <title>Home</title>
</head>

<body>

    <!-- Header -->
    <?php include 'navbar.php'; ?>

    <div class="d-flex">
        <?php include 'Sidebar.php'; ?>

        <!-- Main Content -->
        <div class="container mt-4">
            <h1>Welcome to SIBATTA</h1>
            <p>Lorem, ipsum dolor sit amet consectetur adipisicing elit. Molestiae facere obcaecati asperiores quod animi vero maxime quidem nobis enim suscipit. Alias illum dolores debitis reiciendis ea numquam eum. Deleniti, aut.
                Lorem ipsum dolor sit amet consectetur adipisicing elit. Dignissimos consectetur numquam magni sapiente, velit fugiat dolore alias nemo. Veritatis esse labore non nam praesentium beatae unde quod, quam modi expedita.
            </p>
        </div>

        <!-- footeer -->
        <div class="fixed-bottom text-center mb-2">
            &copy; Copyright Rey 2024
        </div>

</body>

</html>