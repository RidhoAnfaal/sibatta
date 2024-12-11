<?php
// Start the session
session_start();

// Check if the user is logged in, if not redirect to login page
if (!isset($_SESSION['username'])) {
    header('Location: index.php');
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
    <link rel="stylesheet" href="css/main_student.css">
    <title>Home</title>
    <style>
        /* Style for hidden sidebar */
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
    </style>
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
</body>

</html>