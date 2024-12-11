<?php
session_start();
if (!isset($_SESSION['username'])) {
  header('Location: login.php');
  exit();
}

// Database connection
$host = "localhost";
$username = "root";
$password = "";
$dbname = "sibatta";

$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  // Get form data
  $user = $_POST['username'];
  $phone = $_POST['no_telepon'];
  $email = $_POST['email'];
  $password = $_POST['password'];
  $confirm_password = $_POST['confirm_password'];

  // Basic validation for password match
  if ($password !== $confirm_password) {
    echo "Passwords do not match!";
    exit();
  }

  // Hash the password before saving to the database
  $hashed_password = password_hash($password, PASSWORD_DEFAULT);

  // Insert user into the database
  $sql = "INSERT INTO users (username, no_telepon, email, password) VALUES (?, ?, ?, ?)";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("ssss", $user, $phone, $email, $hashed_password);

  if ($stmt->execute()) {
    echo "User added successfully!";
  } else {
    echo "Error: " . $stmt->error;
  }

  // Close connection
  $stmt->close();
  $conn->close();
}
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <script type="module" src="https://cdn.jsdelivr.net/npm/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://cdn.jsdelivr.net/npm/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/add_admin.css">
</head>

<body>
    <!-- Header -->
        <?php include 'navbar.php'; ?>

    <div class="d-flex">
        <?php include 'Sidebar.php'; ?>

    <!-- Table and Add Dosen Section -->
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center">
            <h2>User List</h2>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal"><i class="bi bi-person-plus-fill"></i> Add Dosen</button>
        </div>
        <div class="my-3">
            <input type="text" class="form-control" id="search" placeholder="Search..." />
        </div>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>No</th>
                    <th>NIP</th>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>No Telepon</th>
                    <th>Level</th>
                </tr>
            </thead>
            <tbody id="userTableBody">
                <!-- Populate with PHP dynamically -->
            </tbody>
        </table>
    </div>

    <!-- Modal to Add User -->
    <div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addUserModalLabel">Add New Dosen</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="add_user_process.php">
                        <div class="mb-3">
                            <label for="ID" class="form-label">NIP</label>
                            <input type="text" class="form-control" id="ID" name="ID" required>
                        </div>
                        <div class="mb-3">
                            <label for="username" class="form-label">Nama</label>
                            <input type="text" class="form-control" id="username" name="username" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="no_telepon" class="form-label">No Telepon</label>
                            <input type="text" class="form-control" id="no_telepon" name="no_telepon" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">Confirm Password</label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                        </div>
                        <div class="mb-3">
                            <label for="level" class="form-label">User Level</label>
                            <select class="form-select" id="level" name="level" required>
                                <option value="User Dosen">User Dosen</option>
                                <option value="admin">User Mahasiswa</option>
                            </select>
                        </div>
                        <br>
                        <br>
                        <br>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Add</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <!-- footeer -->
    <div class="fixed-bottom text-center mb-2">
        &copy; Copyright Rey 2024
    </div>
</body>

</html>