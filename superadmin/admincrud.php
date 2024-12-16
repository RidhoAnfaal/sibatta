<?php
// Include the database connection configuration
require_once '../koneksi.php';  // Make sure this path is correct based on your directory structure
session_start();

// Check if the user is logged in, if not redirect to login page
if (!isset($_SESSION['username'])) {
    header('Location: ../index.php');
    exit();
}
$username = $_SESSION['username'];

// Create an instance of the Koneksi class and establish connection
$koneksi = new Koneksi();
$conn = $koneksi->connect(); // This will call the connect method from Koneksi class to get the connection

// Add Admin and User function
function addAdminUser($username, $password, $email, $admin_role, $fullName)
{
    global $conn;

    // Start transaction
    sqlsrv_begin_transaction($conn);

    try {
        // Insert into user table
        $role = 'admin';
        $insertUserQuery = "INSERT INTO [sibatta].[user] (username, password, email, role)
                            OUTPUT INSERTED.user_id
                            VALUES (?, ?, ?, ?)";
        $userParams = [$username, $password, $email, $role];
        $stmtUser = sqlsrv_query($conn, $insertUserQuery, $userParams);

        if ($stmtUser === false) {
            throw new Exception("Error inserting into user: " . print_r(sqlsrv_errors(), true));
        }

        // Get the generated user_id
        sqlsrv_fetch($stmtUser);
        $userId = sqlsrv_get_field($stmtUser, 0);

        // Insert into admin table
        $insertAdminQuery = "INSERT INTO [sibatta].[admin] (user_id, admin_role, fullName)
                             VALUES (?, ?, ?)";
        $adminParams = [$userId, $admin_role, $fullName];
        $stmtAdmin = sqlsrv_query($conn, $insertAdminQuery, $adminParams);

        if ($stmtAdmin === false) {
            throw new Exception("Error inserting into admin: " . print_r(sqlsrv_errors(), true));
        }

        // Commit transaction
        sqlsrv_commit($conn);
        echo "Admin and user added successfully!";
    } catch (Exception $e) {
        sqlsrv_rollback($conn);
        echo "Failed to add admin and user: " . $e->getMessage();
    }
}

// Update Admin function
function updateAdmin($admin_id, $fullName, $email, $admin_role)
{
    global $conn;

    // Update email in user table
    $updateUserQuery = "
        UPDATE [sibatta].[user] 
        SET email = ? 
        WHERE user_id = (SELECT user_id FROM [sibatta].[admin] WHERE admin_id = ?)
    ";
    $userParams = [$email, $admin_id];
    $stmtUser = sqlsrv_query($conn, $updateUserQuery, $userParams);

    if ($stmtUser === false) {
        echo "Error updating user email: " . print_r(sqlsrv_errors(), true);
        die();
    }

    // Update admin details in admin table
    $updateAdminQuery = "
        UPDATE [sibatta].[admin]
        SET fullName = ?, admin_role = ?
        WHERE admin_id = ?
    ";
    $adminParams = [$fullName, $admin_role, $admin_id];
    $stmtAdmin = sqlsrv_query($conn, $updateAdminQuery, $adminParams);

    if ($stmtAdmin === false) {
        echo "Error updating admin data: " . print_r(sqlsrv_errors(), true);
        die();
    }

    echo "Admin updated successfully!";
}

// Delete Admin function
function deleteAdmin($admin_id)
{
    global $conn;

    // First, delete from the admin table
    $deleteAdminQuery = "DELETE FROM [sibatta].[admin] WHERE admin_id = ?";
    $paramsAdmin = [$admin_id];
    $stmtAdmin = sqlsrv_query($conn, $deleteAdminQuery, $paramsAdmin);

    if ($stmtAdmin === false) {
        echo "Error deleting admin: " . print_r(sqlsrv_errors(), true);
        die();
    }

    // Then, delete the associated user data
    $deleteUserQuery = "DELETE FROM [sibatta].[user] WHERE user_id = (SELECT user_id FROM [sibatta].[admin] WHERE admin_id = ?)";
    $stmtUser = sqlsrv_query($conn, $deleteUserQuery, $paramsAdmin);

    if ($stmtUser === false) {
        echo "Error deleting user: " . print_r(sqlsrv_errors(), true);
        die();
    }

    echo "Admin and associated user deleted successfully!";
}

// Search Admins function
function searchAdmins($searchTerm)
{
    global $conn;
    $admins = [];

    // Use a parameterized query to search admins by their email, username, or full name
    $query = "SELECT a.admin_id, a.fullName, u.email, a.admin_role, u.username
              FROM [sibatta].[admin] a
              JOIN [sibatta].[user] u ON a.user_id = u.user_id
              WHERE u.email LIKE ? OR a.fullName LIKE ? OR u.username LIKE ?";

    $searchPattern = "%" . $searchTerm . "%"; // Add wildcards for partial matching
    $params = [$searchPattern, $searchPattern, $searchPattern];

    // Prepare and execute the query with parameters
    $stmt = sqlsrv_query($conn, $query, $params);

    if ($stmt === false) {
        echo "Error searching admins: " . print_r(sqlsrv_errors(), true);
        die();
    }

    // Fetch all admins that match the search term
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        $admins[] = $row;
    }

    return $admins;
}

// Fetch all admins if search term is empty
$admins = [];
if (isset($_POST['search'])) {
    $searchTerm = $_POST['search'];
    $admins = searchAdmins($searchTerm);
} else {
    // Fetch all admins
    $query = "SELECT a.admin_id, a.fullName, u.email, a.admin_role, u.username
              FROM [sibatta].[admin] a
              JOIN [sibatta].[user] u ON a.user_id = u.user_id";
    $stmt = sqlsrv_query($conn, $query);

    if ($stmt === false) {
        echo "Error fetching admins: " . print_r(sqlsrv_errors(), true);
        die();
    }

    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        $admins[] = $row;
    }
}

// Handle form submission for add, update, or delete
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['action'])) {
        if ($_POST['action'] == 'add') {
            // Getting form data for adding admin
            $username = $_POST['username'];
            $password = $_POST['password'];
            $email = $_POST['email'];
            $admin_role = $_POST['admin_role'];
            $fullName = $_POST['fullName'];

            // Call the function to add user and admin
            addAdminUser($username, $password, $email, $admin_role, $fullName);
        } elseif ($_POST['action'] == 'update') {
            // Update admin data
            $admin_id = $_POST['admin_id'];
            $fullName = $_POST['fullName'];
            $email = $_POST['email'];
            $admin_role = $_POST['admin_role'];

            // Call update function
            updateAdmin($admin_id, $fullName, $email, $admin_role);
        } elseif ($_POST['action'] == 'delete') {
            // Delete admin
            $admin_id = $_POST['admin_id'];

            // Call delete function
            deleteAdmin($admin_id);
        }
    }
}

// Close connection
$koneksi->close();
?>

<!-- Frontend Form to Add Admin -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin User CRUD</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body>
    <?php include 'navbar.php'; ?>
    <div class="d-flex">
        <?php include 'sidebar.php'; ?>
        <div class="container mt-5">
            <h2 class="mb-4">Admin Data:</h2>

            <!-- Add Admin Form -->
            <form method="POST" action="">
                <input type="hidden" name="action" value="add">
                <div class="mb-3">
                    <input type="text" name="username" placeholder="Username" class="form-control" required>
                </div>
                <div class="mb-3">
                    <input type="password" name="password" placeholder="Password" class="form-control" required>
                </div>
                <div class="mb-3">
                    <input type="email" name="email" placeholder="Email" class="form-control" required>
                </div>
                <div class="mb-3">
                    <input type="text" name="admin_role" placeholder="Admin Role" class="form-control" required>
                </div>
                <div class="mb-3">
                    <input type="text" name="fullName" placeholder="Full Name" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary">Add Admin</button>
            </form>

            <!-- Admin Table -->
            <h3 class="mt-5">Admin List</h3>
            <form method="POST">
                <input type="text" name="search" placeholder="Search Admin" class="form-control mb-3">
                <button type="submit" class="btn btn-secondary">Search</button>
            </form>

            <table class="table mt-3">
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Full Name</th>
                        <th>Admin Role</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($admins as $admin): ?>
                        <tr>
                            <td><?= $admin['username'] ?></td>
                            <td><?= $admin['email'] ?></td>
                            <td><?= $admin['fullName'] ?></td>
                            <td><?= $admin['admin_role'] ?></td>
                            <td>
                                <a href="updateAdmin.php?admin_id=<?= $admin['admin_id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                                <form action="" method="POST" style="display:inline;">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="admin_id" value="<?= $admin['admin_id'] ?>">
                                    <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <!-- Footer -->
    <?php include 'footer.php'; ?>
</body>

</html>