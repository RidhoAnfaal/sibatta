<?php
// Database connection configuration
require_once '../koneksi.php';
session_start();

//Check if the user is logged in, if not redirect to login page
if (!isset($_SESSION['username'])) {
header('Location: index.php');
exit();
}

$username = $_SESSION['username']; 


class UserManagement {
    private $db;

    public function __construct($dbConnection) {
        $this->db = $dbConnection;
    }

    // Function to fetch all admins
    public function fetchAlladmins() {
        $query = "SELECT admin.admin_id, [user].username, [user].password, [user].email, admin.admin_role, admin.fullName 
                  FROM sibatta.admin 
                  JOIN sibatta.[user] ON sibatta.admin.user_id = sibatta.[user].user_id
                  WHERE sibatta.[user].role = 'admin';";

        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Function to add a new admin
    public function addadmin($username, $password, $email, $admin_role, $fullName) {
      try {
          $this->db->beginTransaction();
          // hash pw
          $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
          // insert
          $userQuery = "INSERT INTO [user] (username, password, email, role) VALUES (:username, :password, :email, 'admin');";
          $stmt = $this->db->prepare($userQuery);
          $stmt->execute(['username' => $username, 'password' => $hashedPassword, 'email' => $email]);

          $userId = $this->db->lastInsertId();

          // insert
          $adminQuery = "INSERT INTO admin (user_id, admin_role, fullName) VALUES (:user_id, :admin_role, :fullName);";
          $stmt = $this->db->prepare($adminQuery);
          $stmt->execute(['user_id' => $userId, 'admin_role' => $admin_role, 'fullName' => $fullName]);

          $this->db->commit();
          return true;
      } catch (Exception $e) {
          $this->db->rollBack();
          error_log("Error adding admin: " . $e->getMessage());
          return false;
      }
  }

    // Function to delete a admin
    public function deleteadmin($adminid) {
        try {
            $query = "DELETE FROM admin WHERE admin_id = :admin_id;";
            $stmt = $this->db->prepare($query);
            $stmt->execute(['admin_id' => $adminid]);

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    // Function to update admin data
    public function updateadmin($adminid, $username, $email, $admin_role, $fullName) {
        try {
            $this->db->beginTransaction();

            // Update user table
            $userQuery = "UPDATE [user] SET username = :username, email = :email 
                          WHERE user_id = (SELECT user_id FROM admin WHERE admin_id = :admin_id);";
            $stmt = $this->db->prepare($userQuery);
            $stmt->execute(['username' => $username, 'email' => $email, 'admin_id' => $adminid]);

            // Update admin table
            $adminQuery = "UPDATE admin SET admin_role = :admin_role, fullName = :fullName 
                              WHERE admin_id = :admin_id;";
            $stmt = $this->db->prepare($adminQuery);
            $stmt->execute(['admin_role' => $admin_role, 'fullName' => $fullName, 'admin_id' => $adminid]);

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    // Function to search for admins by admin_id
    public function searchadminById($adminid) {
        $query = "SELECTSELECT TOP (1) [admin_id], [admin_role], [fullName] 
                 FROM [sibatta].[sibatta].[admin]
                  JOIN [user] ON admin.user_id = [user].admin_id
                  WHERE admin.admin_id = :admin_id;";
        $stmt = $this->db->prepare($query);
        $stmt->execute(['admin_id' => $adminid]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}

// Inst UserManagement class
try {
    $dbConnection = new PDO('sqlsrv:Server=LAPTOP-DL9EJTU3\MSSQLSERVER01;Database=sibatta', '', ''); // Update with your DB credentials
    $dbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $userManager = new UserManagement($dbConnection);

    // Handle POST actions (add, update, delete)
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $action = $_POST['action'] ?? '';

        if ($action === 'add') {
          $userManager->addadmin($_POST['username'], $_POST['password'], $_POST['email'], $_POST['admin_role'], $_POST['fullName']);
        } elseif ($action === 'update') {
            $userManager->updateadmin($_POST['admin_id'], $_POST['username'], $_POST['email'], $_POST['admin_role'], $_POST['fullName']);
        } elseif ($action === 'delete') {
            $userManager->deleteadmin($_POST['admin_id']);
        }
    }

    // fetch admins for display
    $admins = $userManager->fetchAlladmins();
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}
?>

<!doctype html>
<html lang="en">

<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <script type="module" src="https://cdn.jsdelivr.net/npm/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://cdn.jsdelivr.net/npm/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
  <link rel="stylesheet" href="css/add_admin.css">
</head>

<body>
  <!-- header -->
  <?php include 'navbar.php'; ?>

  <div class="d-flex">
    <?php include 'sidebar.php'; ?>

    <!-- main -->
    <div class="container mt-4">
      <div class="d-flex justify-content-between align-items-center">
        <h2>admin List</h2>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addadminModal">
          <ion-icon name="person-add"></ion-icon> Add admin
        </button>
      </div>

      <div class="my-3">
        <input type="text" class="form-control" id="search" placeholder="Search...">
      </div>

      <table class="table table-striped">
        <thead>
          <tr>
            <th>No</th>
            <th>admin ID</th>
            <th>Full Name</th>
            <th>Email</th>
            <th>Program</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody id="adminTableBody">
          <?php foreach ($admins as $index => $admin): ?>
            <tr>
              <td><?php echo $index + 1; ?></td>
              <td><?php echo htmlspecialchars($admin['admin_id']); ?></td>
              <td><?php echo htmlspecialchars($admin['fullName']); ?></td>
              <td><?php echo htmlspecialchars($admin['email']); ?></td>
              <td><?php echo htmlspecialchars($admin['admin_role']); ?></td>
              <td>
                <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#updateadminModal-<?php echo $admin['admin_id']; ?>">Edit</button>
                <form method="POST" class="d-inline">
                  <input type="hidden" name="action" value="delete">
                  <input type="hidden" name="admin_id" value="<?php echo htmlspecialchars($admin['admin_id']); ?>">
                  <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this admin?');">Delete</button>
                </form>
              </td>
            </tr>

            <!-- update -->
            <div class="modal fade" id="updateadminModal-<?php echo $admin['admin_id']; ?>" tabindex="-1" aria-labelledby="updateadminModalLabel" aria-hidden="true">
              <div class="modal-dialog">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title">Update admin</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body">
                    <form method="POST">
                      <input type="hidden" name="action" value="update">
                      <input type="hidden" name="admin_id" value="<?php echo htmlspecialchars($admin['admin_id']); ?>">
                      <div class="mb-3">
                        <label for="username" class="form-label">Full Name</label>
                        <input type="text" class="form-control" name="fullName" value="<?php echo htmlspecialchars($admin['fullName']); ?>" required>
                      </div>
                      <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($admin['email']); ?>" required>
                      </div>
                      <div class="mb-3">
                        <label for="admin_role" class="form-label">Status</label>
                        <select class="form-select" name="admin_role" required>
                          <option value="Administrator library" <?php echo ($admin['admin_role'] === 'Administrator library' ? 'selected' : ''); ?>>Administrator library</option>
                        </select>
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                      </div>
                    </form>
                  </div>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <!-- add -->
    <div class="modal fade" id="addadminModal" tabindex="-1" aria-labelledby="addadminModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New admin</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="POST">
                    <input type="hidden" name="action" value="add">
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="fullName" class="form-label">Full Name</label>
                        <input type="text" class="form-control" id="fullName" name="fullName" required>
                    </div>
                    <div class="mb-3">
                        <label for="admin_role" class="form-label">Status</label>
                        <select class="form-select" id="admin_role" name="admin_role" required>
                            <option value="Administrator library">Administrator library</option>
                        </select>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

  </div>
 <!-- Footer -->
 <footer class="footer mt-auto py-4">
            <div class="container text-center">
                <p>&copy; 2024 <strong>SIBATTA</strong>. All rights reserved.</p>
                <p>Contact us: <a href="mailto:support@sibatta.com">support@sibatta.com</a></p>
                <div class="social-icons">
                    <a href="https://facebook.com" target="_blank" class="me-3">
                        <i class="bi bi-facebook"></i>
                    </a>
                    <a href="https://twitter.com" target="_blank" class="me-3">
                        <i class="bi bi-twitter"></i>
                    </a>
                    <a href="https://instagram.com" target="_blank">
                        <i class="bi bi-instagram"></i>
                    </a>
                </div>
            </div>
        </footer>
  
       
</body>

</html>
