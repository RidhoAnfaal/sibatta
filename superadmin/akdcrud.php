<?php
// Include the database connection configuration
require_once '../koneksi.php'; 
session_start();

// Check if the user is logged in, if not redirect to login page
if (!isset($_SESSION['username'])) {
    header('Location: ../index.php');
    exit();
}

$username = $_SESSION['username'];

// Create an instance of the Koneksi class and establish connection
$koneksi = new Koneksi();
$conn = $koneksi->connect();

// Add Academic Admin function
function addAcademicAdmin($username, $password, $email, $fullName)
{
    global $conn;

    sqlsrv_begin_transaction($conn);

    try {
        // Insert into the user table with the role `admin_academic`
        $role = 'admin_academic';
        $insertUserQuery = "INSERT INTO [sibatta].[user] (username, password, email, role)
                            OUTPUT INSERTED.user_id
                            VALUES (?, ?, ?, ?)";
        $userParams = [$username, $password, $email, $role];
        $stmtUser = sqlsrv_query($conn, $insertUserQuery, $userParams);

        if ($stmtUser === false) {
            throw new Exception("Error inserting into user: " . print_r(sqlsrv_errors(), true));
        }

        // Retrieve the generated user_id
        sqlsrv_fetch($stmtUser);
        $userId = sqlsrv_get_field($stmtUser, 0);

        // Insert into the admin table with `admin_role admin_academic`
        $adminRole = 'academic';
        $insertAdminQuery = "INSERT INTO [sibatta].[admin] (user_id, admin_role, fullName)
                             VALUES (?, ?, ?)";
        $adminParams = [$userId, $adminRole, $fullName];
        $stmtAdmin = sqlsrv_query($conn, $insertAdminQuery, $adminParams);

        if ($stmtAdmin === false) {
            throw new Exception("Error inserting into admin: " . print_r(sqlsrv_errors(), true));
        }

        // Commit transaction
        sqlsrv_commit($conn);
        echo "Academic Admin added successfully!";
    } catch (Exception $e) {
        sqlsrv_rollback($conn);
        echo "Failed to add Academic Admin: " . $e->getMessage();
    }
}

// Fetch all admins
function fetchAdmins()
{
    global $conn;

    $query = "SELECT 
                a.admin_id, 
                u.username, 
                u.email, 
                a.fullName, 
                a.admin_role 
              FROM [sibatta].[admin] a 
              JOIN [sibatta].[user] u ON a.user_id = u.user_id";

    $stmt = sqlsrv_query($conn, $query);

    if ($stmt === false) {
        echo json_encode([]);
        die();
    }

    $admins = [];
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        $admins[] = $row;
    }

    echo json_encode($admins);
}

// Handle form submissions
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['action'])) {
        if ($_POST['action'] == 'add') {
            $username = $_POST['username'];
            $password = $_POST['password'];
            $email = $_POST['email'];
            $fullName = $_POST['fullName'];
            addAcademicAdmin($username, $password, $email, $fullName);
        } elseif ($_POST['action'] == 'fetch') {
            fetchAdmins();
        }
    }
}

$koneksi->close();
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin User CRUD</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
    <?php include 'navbar.php'; ?>
    <div class="main-content">
        <?php include 'sidebar.php'; ?>
        <div class="container mt-5">
            <h2 class="mb-4">Admin Data</h2>

            <!-- Add Admin Form -->
            <form id="addAdminForm">
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
                    <select name="admin_role" class="form-select" required>
                        <option value="admin_academic">Academic Admin</option>
                    </select>
                </div>
                <div class="mb-3">
                    <input type="text" name="fullName" placeholder="Full Name" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary">Add Admin</button>
            </form>

            <hr>

            <h3 class="mb-4">Admin List</h3>

            <!-- Search Form -->
            <form id="searchForm">
                <div class="input-group mb-3">
                    <input type="text" name="search" id="search" class="form-control" placeholder="Search admins (username, email, role)">
                    <button type="submit" class="btn btn-info">Search</button>
                </div>
            </form>

            <!-- Admin Table -->
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Full Name</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="adminList">
                        <!-- Admin rows will be appended dynamically via JavaScript -->
                </tbody>
            </table>
        </div>

        <!-- Footer -->
        <?php include 'footer.php'; ?>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        <script>
            $(document).ready(function () {
                // Fetch and display admins on page load
                function fetchAdmins() {
                    $.post('akdcrud.php', { action: 'fetch' }, function (data) {
                        const adminList = $('#adminList');
                        adminList.empty();

                        if (data.length === 0) {
                            adminList.append('<tr><td colspan="6">No admins found.</td></tr>');
                        } else {
                            data.forEach((admin, index) => {
                                adminList.append(`
                                    <tr>
                                        <td>${index + 1}</td>
                                        <td>${admin.username}</td>
                                        <td>${admin.email}</td>
                                        <td>${admin.admin_role}</td>
                                        <td>${admin.fullName}</td>
                                        <td>
                                            <button class="btn btn-warning btn-sm editAdmin" data-id="${admin.admin_id}">Edit</button>
                                            <button class="btn btn-danger btn-sm deleteAdmin" data-id="${admin.admin_id}">Delete</button>
                                        </td>
                                    </tr>
                                `);
                            });
                        }
                    }, 'json');
                }

                // Load admins on page load
                fetchAdmins();

                // Add Admin
                $('#addAdminForm').on('submit', function (e) {
                    e.preventDefault();
                    $.post('akdcrud.php', $(this).serialize(), function () {
                        alert('Admin added successfully!');
                        fetchAdmins(); // Refresh list
                    });
                });

                // Search Admins
                $('#searchForm').on('submit', function (e) {
                    e.preventDefault();
                    const searchTerm = $('#search').val();
                    $.post('akdcrud.php', { action: 'fetch', search: searchTerm }, function (data) {
                        const adminList = $('#adminList');
                        adminList.empty();

                        if (data.length === 0) {
                            adminList.append('<tr><td colspan="6">No admins found.</td></tr>');
                        } else {
                            data.forEach((admin, index) => {
                                adminList.append(`
                                    <tr>
                                        <td>${index + 1}</td>
                                        <td>${admin.username}</td>
                                        <td>${admin.email}</td>
                                        <td>${admin.admin_role}</td>
                                        <td>${admin.fullName}</td>
                                        <td>
                                            <button class="btn btn-warning btn-sm editAdmin" data-id="${admin.admin_id}">Edit</button>
                                            <button class="btn btn-danger btn-sm deleteAdmin" data-id="${admin.admin_id}">Delete</button>
                                        </td>
                                    </tr>
                                `);
                            });
                        }
                    }, 'json');
                });

                // Delete Admin
                $(document).on('click', '.deleteAdmin', function () {
                    const adminId = $(this).data('id');
                    if (confirm('Are you sure you want to delete this admin?')) {
                        $.post('akdcrud.php', { action: 'delete', admin_id: adminId }, function () {
                            alert('Admin deleted successfully!');
                            fetchAdmins(); // Refresh list
                        });
                    }
                });
            });
        </script>
    </div>
</body>

</html>
