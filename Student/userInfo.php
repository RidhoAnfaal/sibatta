<?php
// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    // Redirect to login page if not logged in
    header("Location: index.php"); // Corrected path for redirect
    exit;
}

// Get logged-in user's username
$username = $_SESSION['username'];

// Include the database connection file
include 'koneksi.php'; // Corrected path

// Fetch user information from the database
$sql = "SELECT 
            u.username,
            u.email,
            s.student_id,
            s.prodi,
            s.fullName
        FROM sibatta_user u
        JOIN sibatta_student s ON u.user_id = s.user_id
        WHERE u.username = ?";
$params = array($username);
$stmt = sqlsrv_query($conn, $sql, $params);

// Check for errors
if ($stmt === false) {
    die(print_r(sqlsrv_errors(), true));
}

// Fetch the user's data
$userData = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

// Close the statement
sqlsrv_free_stmt($stmt);

if (!$userData) {
    echo "No data found for the user.";
    exit;
}
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
    <title>User Information</title>
</head>

<body>
    <!-- Header -->
    <?php include 'navbar.php'; ?>

    <div class="d-flex">
        <?php include 'Sidebar.php'; ?>

        <!-- Main Content -->
        <div class="container mt-4">
            <h1>User Information</h1>
            <div class="card">
                <div class="card-body">
                    <p class="card-text"><strong>Username :</strong> <?php echo htmlspecialchars($userData['username']); ?></p>
                    <p class="card-text"><strong>Email :</strong> <?php echo htmlspecialchars($userData['email']); ?></p>
                    <p class="card-text"><strong>Student ID :</strong> <?php echo htmlspecialchars($userData['student_id']); ?></p>
                    <p class="card-text"><strong>Full Name :</strong> <?php echo htmlspecialchars($userData['fullName']); ?></p>
                    <p class="card-text"><strong>Study Program :</strong> <?php echo htmlspecialchars($userData['prodi']); ?></p>
                </div>
            </div>
        </div>
</body>

</html>