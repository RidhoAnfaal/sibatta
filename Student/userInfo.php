<?php
// Start the session
session_start();

// Check if the user is logged in, if not redirect to login page
if (!isset($_SESSION['username'])) {
    header('Location: index.php');
    exit();
}

$username = $_SESSION['username']; // Get the username from session

// Include the database connection file
include '../Admin/koneksi.php';

// // Debug: Output session username
// echo 'Session Username: ' . htmlspecialchars($username) . '<br>';

$sql = "SELECT 
            s.student_id, 
            s.prodi, 
            s.fullName, 
            u.username, 
            u.email, 
            u.role
        FROM [sibatta].[sibatta].[student] s
        JOIN [sibatta].[sibatta].[user] u ON s.user_id = u.user_id
        WHERE LOWER(u.username) = LOWER(?)";


// Set the query parameter to the session's username
$params = array($username); // This will pass the logged-in user's username

// // Debug: Output the query and parameters
// echo 'SQL Query: ' . $sql . '<br>';
// echo 'Username Parameter: ' . htmlspecialchars($username) . '<br>';

// Prepare and execute the query
$stmt = sqlsrv_query($conn, $sql, $params);

if ($stmt === false) {
    echo "<p>SQL Error: " . print_r(sqlsrv_errors(), true) . "</p>";
} else {
    echo "</p>";
}


// Fetch the user's data
$userData = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

// // Debug: Output fetched data
// echo '<pre>';
// print_r($userData);
// echo '</pre>';

// Check if data is found
if (!$userData) {
    echo '<p>No data found for the user.</p>';
    exit;
}


// Close the statement
sqlsrv_free_stmt($stmt);
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
</head>

<body>
    <!-- Header -->
    <?php include 'navbar.php'; ?>

    <div class="d-flex">
        <?php include 'Sidebar.php'; ?>

        <!-- Main Content -->
        <div class="container mt-4">
            <h1>Welcome, <?php echo htmlspecialchars($userData['fullName']); ?></h1>
            <p><strong>Username:</strong> <?php echo htmlspecialchars($userData['username']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($userData['email']); ?></p>
            <p><strong>Student ID:</strong> <?php echo htmlspecialchars($userData['student_id']); ?></p>
            <p><strong>Study Program:</strong> <?php echo htmlspecialchars($userData['prodi']); ?></p>
            <p><strong>Role:</strong> <?php echo htmlspecialchars($userData['role']); ?></p>
        </div>
    </div>
</body>

</html>
