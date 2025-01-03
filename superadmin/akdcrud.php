<?php
require_once '../koneksi.php';
session_start();

if (!isset($_SESSION['username'])) {
    header('Location: ../index.php');
    exit();
}

$username = $_SESSION['username'];
$koneksi = new Koneksi();
$conn = $koneksi->connect();

function addAcademicAdmin($username, $password, $email, $fullName)
{
    global $conn;
    sqlsrv_begin_transaction($conn);

    try {
        $role = 'admin_academic';
        $insertUserQuery = "INSERT INTO [sibatta].[user] (username, password, email, role) 
                            OUTPUT INSERTED.user_id VALUES (?, ?, ?, ?)";
        $userParams = [$username, $password, $email, $role];
        $stmtUser = sqlsrv_query($conn, $insertUserQuery, $userParams);

        if ($stmtUser === false) {
            throw new Exception("Error inserting into user: " . print_r(sqlsrv_errors(), true));
        }

        sqlsrv_fetch($stmtUser);
        $userId = sqlsrv_get_field($stmtUser, 0);

        $adminRole = 'academic';
        $insertAdminQuery = "INSERT INTO [sibatta].[admin] (user_id, admin_role, fullName)
                             VALUES (?, ?, ?)";
        $adminParams = [$userId, $adminRole, $fullName];
        $stmtAdmin = sqlsrv_query($conn, $insertAdminQuery, $adminParams);

        if ($stmtAdmin === false) {
            throw new Exception("Error inserting into admin: " . print_r(sqlsrv_errors(), true));
        }

        sqlsrv_commit($conn);
        echo "Academic Admin added successfully!";
    } catch (Exception $e) {
        sqlsrv_rollback($conn);
        echo "Failed to add Academic Admin: " . $e->getMessage();
    }
}

function updateAdmin($admin_id, $username, $password, $fullName, $email, $admin_role)
{
    global $conn;

    sqlsrv_begin_transaction($conn);

    try {
        $updateUserQuery = "
            UPDATE [sibatta].[user]
            SET username = ?, password = ?, email = ?
            WHERE user_id = (SELECT user_id FROM [sibatta].[admin] WHERE admin_id = ?)";
        $userParams = [$username, $password, $email, $admin_id];
        $stmtUser = sqlsrv_query($conn, $updateUserQuery, $userParams);

        if ($stmtUser === false) {
            throw new Exception("Error updating user: " . print_r(sqlsrv_errors(), true));
        }

        $updateAdminQuery = "
            UPDATE [sibatta].[admin]
            SET fullName = ?, admin_role = ?
            WHERE admin_id = ?";
        $adminParams = [$fullName, $admin_role, $admin_id];
        $stmtAdmin = sqlsrv_query($conn, $updateAdminQuery, $adminParams);

        if ($stmtAdmin === false) {
            throw new Exception("Error updating admin data: " . print_r(sqlsrv_errors(), true));
        }

        sqlsrv_commit($conn);
        echo "success";
    } catch (Exception $e) {
        sqlsrv_rollback($conn);
        echo "Failed to update admin: " . $e->getMessage();
    }
}

function deleteAdmin($admin_id)
{
    global $conn;

    sqlsrv_begin_transaction($conn);

    try {
        $query = "SELECT user_id FROM [sibatta].[admin] WHERE admin_id = ?";
        $params = [$admin_id];
        $stmt = sqlsrv_query($conn, $query, $params);

        if ($stmt === false) {
            throw new Exception("Error fetching user_id: " . print_r(sqlsrv_errors(), true));
        }

        sqlsrv_fetch($stmt);
        $user_id = sqlsrv_get_field($stmt, 0);

        if (!$user_id) {
            throw new Exception("No associated user_id found for admin_id: $admin_id");
        }

        $deleteAdminQuery = "DELETE FROM [sibatta].[admin] WHERE admin_id = ?";
        $stmtAdmin = sqlsrv_query($conn, $deleteAdminQuery, [$admin_id]);

        if ($stmtAdmin === false) {
            throw new Exception("Error deleting admin: " . print_r(sqlsrv_errors(), true));
        }

        $deleteUserQuery = "DELETE FROM [sibatta].[user] WHERE user_id = ?";
        $stmtUser = sqlsrv_query($conn, $deleteUserQuery, [$user_id]);

        if ($stmtUser === false) {
            throw new Exception("Error deleting user: " . print_r(sqlsrv_errors(), true));
        }

        sqlsrv_commit($conn);

        echo "success";
    } catch (Exception $e) {
        sqlsrv_rollback($conn);
        echo "Failed to delete admin and user: " . $e->getMessage();
    }
}

function searchAdmins($searchTerm)
{
    global $conn;
    $admins = [];

    $query = "SELECT a.admin_id, a.fullName, u.email, a.admin_role, u.username
              FROM [sibatta].[admin] a
              JOIN [sibatta].[user] u ON a.user_id = u.user_id
              WHERE u.role = 'admin_academic' AND (u.email LIKE ? OR a.fullName LIKE ? OR u.username LIKE ?)";

    $searchPattern = "%" . $searchTerm . "%";
    $params = [$searchPattern, $searchPattern, $searchPattern];

    $stmt = sqlsrv_query($conn, $query, $params);

    if ($stmt === false) {
        echo "Error searching admins: " . print_r(sqlsrv_errors(), true);
        die();
    }

    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        $admins[] = $row;
    }

    return $admins;
}

function fetchAllAdmins()
{
    global $conn;
    $admins = [];

    $query = "SELECT a.admin_id, a.fullName, u.email, a.admin_role, u.username
              FROM [sibatta].[admin] a
              JOIN [sibatta].[user] u ON a.user_id = u.user_id
              WHERE u.role = 'admin_academic'";

    $stmt = sqlsrv_query($conn, $query);

    if ($stmt === false) {
        echo "Error fetching admins: " . print_r(sqlsrv_errors(), true);
        die();
    }

    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        $admins[] = $row;
    }

    return $admins;
}

$admins = fetchAllAdmins();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['action'])) {
        if ($_POST['action'] == 'add') {
            $username = $_POST['username'];
            $password = $_POST['password'];
            $email = $_POST['email'];
            $admin_role = $_POST['admin_role'];
            $fullName = $_POST['fullName'];
            addAcademicAdmin($username, $password, $email, $fullName);
        } elseif ($_POST['action'] == 'update') {
            $admin_id = $_POST['admin_id'];
            $username = $_POST['username'];
            $password = $_POST['password'];
            $fullName = $_POST['fullName'];
            $email = $_POST['email'];
            $admin_role = $_POST['admin_role'];
            updateAdmin($admin_id, $username, $password, $fullName, $email, $admin_role);
        } elseif ($_POST['action'] == 'delete') {
            $admin_id = $_POST['admin_id'];
            deleteAdmin($admin_id);
        } elseif ($_POST['action'] == 'search') {
            $searchTerm = $_POST['search'];
            echo json_encode(searchAdmins($searchTerm));
            exit();
        } elseif ($_POST['action'] == 'fetchAdmin') {
            $admin_id = $_POST['admin_id'];
            $query = "SELECT a.admin_id, a.fullName, u.email, a.admin_role, u.username, u.password
                      FROM [sibatta].[admin] a
                      JOIN [sibatta].[user] u ON a.user_id = u.user_id
                      WHERE a.admin_id = ?";
            $params = [$admin_id];
            $stmt = sqlsrv_query($conn, $query, $params);

            if ($stmt === false) {
                echo "Error fetching admin: " . print_r(sqlsrv_errors(), true);
                die();
            }

            $admin = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
            echo json_encode($admin);
            exit();
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
    <title>Academic Admin User CRUD</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
    <?php include 'navbar.php'; ?>
    <div class="main-content">
        <?php include 'sidebar.php'; ?>
        <div class="container mt-5">
            <h2 class="mb-4">Academic Admin Data :</h2>

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

            <h3 class="mb-4">Academic Admin List</h3>

            <!-- Search Form -->
            <form id="searchForm">
                <div class="input-group mb-3">
                    <input type="text" name="search" id="search" class="form-control" placeholder="Search admins (username, email)">
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
                <?php if (!empty($admins)) : ?>
                        <?php foreach ($admins as $index => $admin) : ?>
                            <tr>
                                <td><?php echo $index + 1; ?></td>
                                <td><?php echo htmlspecialchars($admin['username']); ?></td>
                                <td><?php echo htmlspecialchars($admin['email']); ?></td>
                                <td><?php echo htmlspecialchars($admin['admin_role']); ?></td>
                                <td><?php echo htmlspecialchars($admin['fullName']); ?></td>
                                <td>
                                    <button class="btn btn-warning btn-sm editAdmin" data-id="<?php echo $admin['admin_id']; ?>" data-username="<?php echo htmlspecialchars($admin['username']); ?>" data-email="<?php echo htmlspecialchars($admin['email']); ?>" data-role="<?php echo htmlspecialchars($admin['admin_role']); ?>" data-fullname="<?php echo htmlspecialchars($admin['fullName']); ?>">Edit</button>
                                    <button class="btn btn-danger btn-sm deleteAdmin" data-id="<?php echo $admin['admin_id']; ?>">Delete</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="6">No admins found.</td>
                        </tr>
                    <?php endif; ?>
                        <!-- Admin rows will be appended dynamically via JavaScript -->
                </tbody>
            </table>
        </div>

        
        <!-- Edit Admin Modal -->
        <div class="modal fade" id="updateAdminModal" tabindex="-1" aria-labelledby="updateAdminModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form id="updateAdminForm">
                        <div class="modal-header">
                            <h5 class="modal-title" id="updateAdminModalLabel">Edit Admin</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="admin_id" id="editAdminId">
                            <div class="mb-3">
                                <label for="editUsername" class="form-label">Username</label>
                                <input type="text" name="username" id="editUsername" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="editPassword" class="form-label">Password</label>
                                <input type="password" name="password" id="editPassword" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label for="editEmail" class="form-label">Email</label>
                                <input type="email" name="email" id="editEmail" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="editFullName" class="form-label">Full Name</label>
                                <input type="text" name="fullName" id="editFullName" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="editAdminRole" class="form-label">Admin Role</label>
                                <select name="admin_role" id="editAdminRole" class="form-select" required>
                                    <option value="academic">Admin Academic</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <?php include 'footer.php'; ?>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        <script>
//             $(document).ready(function () {
//     // Fetch and display admins
//     function fetchAdmins(searchTerm = '') {
//         $.post('akdcrud.php', { action: 'fetch', search: searchTerm }, function (data) {
//             console.log(data);  // Log the response from the server

//             const adminList = $('#adminList');
//             adminList.empty();

//             if (data.length === 0) {
//                 adminList.append('<tr><td colspan="6">No admins found.</td></tr>');
//             } else {
//                 data.forEach((admin, index) => {
//                     adminList.append(`
//                         <tr>
//                             <td>${index + 1}</td>
//                             <td>${admin.username}</td>
//                             <td>${admin.email}</td>
//                             <td>${admin.admin_role}</td>
//                             <td>${admin.fullName}</td>
//                             <td>
//                                 <button class="btn btn-warning btn-sm editAdmin" data-id="${admin.admin_id}" data-username="${admin.username}" data-email="${admin.email}" data-fullname="${admin.fullName}">Edit</button>
//                                 <button class="btn btn-danger btn-sm deleteAdmin" data-id="${admin.admin_id}">Delete</button>
//                             </td>
//                         </tr>
//                     `);
//                 });
//             }
//         }, 'json');
//     }

//     // Load admins on page load
//     fetchAdmins();

//     // Add Admin
//     $('#addAdminForm').on('submit', function (e) {
//         e.preventDefault();
//         $.post('akdcrud.php', $(this).serialize(), function () {
//             alert('Admin added successfully!');
//             fetchAdmins(); // Refresh list
//         });
//     });

//     // Search Admins
//     $('#searchForm').on('submit', function (e) {
//         e.preventDefault();
//         const searchTerm = $('#search').val();
//         fetchAdmins(searchTerm); // Fetch with search term
//     });

//     // Edit Admin
//     $(document).on('click', '.editAdmin', function () {
//         const adminId = $(this).data('id');
//         const username = $(this).data('username');
//         const email = $(this).data('email');
//         const fullName = $(this).data('fullname');

//         // Display the admin's current details in the form for editing
//         $('#addAdminForm input[name="username"]').val(username);
//         $('#addAdminForm input[name="email"]').val(email);
//         $('#addAdminForm input[name="fullName"]').val(fullName);
//         $('#addAdminForm input[name="action"]').val('edit');
//         $('#addAdminForm').append(`<input type="hidden" name="admin_id" value="${adminId}">`);
//     });

//     // Delete Admin
//     $(document).on('click', '.deleteAdmin', function () {
//         const adminId = $(this).data('id');
//         if (confirm('Are you sure you want to delete this admin?')) {
//             $.post('akdcrud.php', { action: 'delete', admin_id: adminId }, function () {
//                 alert('Admin deleted successfully!');
//                 fetchAdmins(); // Refresh list
//             });
//         }
//     });
// });
// Add Admin
$('#addAdminForm').on('submit', function (e) {
                e.preventDefault();
                $.post('akdcrud.php', $(this).serialize(), function (response) {
                    alert('Admin added successfully!');
                    location.reload();
                });
            });

            // Search Admins
            $('#searchForm').on('submit', function (e) {
                e.preventDefault();
                const searchTerm = $('#search').val();
                $.post('akdcrud.php', { action: 'search', search: searchTerm }, function (data) {
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
                                        <button class="btn btn-warning btn-sm editAdmin" data-id="${admin.admin_id}" data-username="${admin.username}" data-email="${admin.email}" data-role="${admin.admin_role}" data-fullname="${admin.fullName}">Edit</button>
                                        <button class="btn btn-danger btn-sm deleteAdmin" data-id="${admin.admin_id}">Delete</button>
                                    </td>
                                </tr>
                            `);
                        });
                    }
                }, 'json');
            });

            // Fetch Admin Data for Editing
            $(document).on('click', '.editAdmin', function () {
                const adminId = $(this).data('id');
                $.post('akdcrud.php', { action: 'fetchAdmin', admin_id: adminId }, function (response) {
                    try {
                        const admin = JSON.parse(response);
                        $('#editAdminId').val(admin.admin_id);
                        $('#editUsername').val(admin.username);
                        $('#editPassword').val('');
                        $('#editEmail').val(admin.email);
                        $('#editFullName').val(admin.fullName);
                        $('#editAdminRole').val(admin.admin_role);
                        $('#updateAdminModal').modal('show');
                    } catch (e) {
                        alert('Failed to fetch admin details: ' + response);
                    }
                });
            });

            // Update Admin
            $('#updateAdminForm').on('submit', function (e) {
                e.preventDefault();
                $.post('akdcrud.php', $(this).serialize() + '&action=update', function (response) {
                    if (response.trim() === 'success') {
                        alert('Admin updated successfully!');
                        location.reload();
                    } else {
                        alert('Failed to update admin: ' + response);
                    }
                });
            });

            // Delete Admin
            $(document).on('click', '.deleteAdmin', function () {
                const adminId = $(this).data('id');
                if (confirm('Are you sure you want to delete this admin?')) {
                    $.post('akdcrud.php', { action: 'delete', admin_id: adminId }, function (response) {
                        alert('Admin deleted successfully!');
                        location.reload();
                    });
                }
            });

        </script>
    </div>
</body>

</html>
