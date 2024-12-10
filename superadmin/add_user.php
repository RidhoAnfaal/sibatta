<?php
session_start();
if (!isset($_SESSION['username'])) {
  header('Location: index.php');
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
  <link rel="stylesheet" href="css/add_user.css">
  <style>
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
  <!-- Horizontal Navbar -->
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">

      <button class="btn btn" id="sidebarToggle">
        <img src="css/images/Logo_Sibatta.png" alt="Toggle Sidebar" style="width: 30px; height: 40px; object-fit:cover;">
      </button>
      <span class="navbar-brand">SIBATTA</span>

      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item">
            <a class="nav-link" href="#" onclick="location.reload()">
              <ion-icon name="refresh-outline"></ion-icon>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="massage.php" data-bs-toggle="modal" data-bs-target="#messageModal" data-bs-target="#sendMessageModal">
              <ion-icon name="chatbubbles-outline"></ion-icon>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link text-light" href="#" data-bs-toggle="modal" data-bs-target="#notificationModal">
              <ion-icon name="notifications-outline"></ion-icon>
            </a>
          </li>
          <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
              <!-- Add Compose Email Button -->
              <li class="nav-item">
                <a class="nav-link text-light" href="#" data-bs-toggle="modal" data-bs-target="#emailModal">
                  <ion-icon name="mail-outline"></ion-icon>
                </a>
              </li>
            </ul>
          </div>
          <li class="nav-item dropdown">
            <a class="nav-link text-light" href="#" role="button" data-bs-toggle="modal" aria-expanded="false">
              <ion-icon name="person-circle-outline"></ion-icon>
              <span id="username">Username</span>
            </a>
          </li>

        </ul>
      </div>

    </div>
  </nav>
  <!-- Sidebar -->
  <div id="sidebar">
    <div class="text-center p-3">
      <img src="css/images/Logo_Sibatta.png" alt="Logo" width="50" height="40" class="img-fluid">
      <h5 class="mt-2 text-dark">SIBATTA</h5>
    </div>
    <ul class="nav flex-column">
      <li class="nav-item mb-3">
        <a class="nav-link text-dark d-flex align-items-center" href="Dashboard.php">
          <ion-icon name="home-outline" class="me-2"></ion-icon> <span>Beranda</span>
        </a>
      </li>
      <li class="nav-item mb-3">
        <a class="nav-link text-dark d-flex align-items-center" href="history.php">
          <ion-icon name="time-outline" class="me-2"></ion-icon> <span>History</span>
        </a>
      </li>
      <li class="nav-item mb-3">
        <a class="nav-link text-dark d-flex align-items-center" href="add_user.php">
          <ion-icon name="cloud-upload-outline" class="me-2"></ion-icon> <span>ADD</span>
        </a>
      </li>
    </ul>
    <div class="modal-footer">
      <button class="logout-btn btn btn-danger" data-bs-toggle="modal" data-bs-target="#logoutModal">
        <ion-icon name="log-out-outline" style="font-size: 20px;"></ion-icon>
        <span>Log Out</span>
        </a>
    </div>

  </div>

  <!-- Overlay -->
  <div id="overlay"></div>

  <!-- Table and Add User Section -->
  <div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center">
      <h2>User List</h2>
      <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal"><i class="bi bi-person-plus-fill"></i> Add User</button>
    </div>
    <div class="my-3">
      <input type="text" class="form-control" id="search" placeholder="Search..." />
    </div>
    <table class="table table-striped">
      <thead>
        <tr>
          <th>No</th>
          <th>NIM</th>
          <th>Nama</th>
          <th>Email</th>
          <th>No Telepon</th>
          <th>Prodi</th>
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
          <h5 class="modal-title" id="addUserModalLabel">Add New User</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form method="POST" action="add_user_process.php">
            <div class="mb-3">
              <label for="ID" class="form-label">NIM</label>
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
              <label for="prodi" class="form-label">Prodi</label>
              <select class="form-select" id="prodi" name="prodi" required>
                <option value="user">Teknologi Informasi</option>
                <option value="admin">Sistem Informasi Bisnis</option>
              </select>
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
                <option value="user">User</option>
                <option value="admin">Admin</option>
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

  <!-- Email Pop Up And Notification -->
  <div class="modal fade" id="emailModal" tabindex="-1" aria-labelledby="emailModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="emailModalLabel">Emails</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <!-- Default Content (Before Compose) -->
          <div id="defaultContent">
            <ul class="list-group">
              <li class="list-group-item">You have a new message from Admin</li>

            </ul>
            <button class="btn btn-primary mt-3" id="composeBtn">Compose New Email</button>
          </div>

          <!-- Compose Form (Initially Hidden) -->
          <div id="composeForm" style="display: none;">
            <form method="POST" action="send_email.php" id="emailForm">
              <!-- Email Address -->
              <div class="mb-3">
                <label for="toEmail" class="form-label">Recipient Email</label>
                <input type="email" class="form-control" id="toEmail" name="toEmail" required>
                <div class="invalid-feedback">Please enter a valid email address.</div>
              </div>

              <!-- Subject -->
              <div class="mb-3">
                <label for="subject" class="form-label">Subject</label>
                <input type="text" class="form-control" id="subject" name="subject" required>
                <div class="invalid-feedback">Please enter a subject.</div>
              </div>

              <!-- Message -->
              <div class="mb-3">
                <label for="message" class="form-label">Message</label>
                <textarea class="form-control" id="message" name="message" rows="5" required></textarea>
                <div class="invalid-feedback">Please enter your message.</div>
              </div>

              <!-- Submit Button -->
              <div class="d-grid">
                <button type="submit" class="btn btn-primary">Send</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="notificationModal" tabindex="-1" aria-labelledby="notificationModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="notificationModalLabel">Notifications</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <ul class="list-group">
            <li class="list-group-item">New file uploaded</li>

          </ul>
        </div>
      </div>
    </div>
  </div>



  <!-- Modal Pop-Up -->
  <div class="modal fade" id="messageModal" tabindex="-1" aria-labelledby="messageModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="messageModalLabel">Report</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body">
          <div class="mb-3">
            <label for="message" class="form-label">Pesan</label>
            <textarea class="form-control" id="message" name="message" rows="1" required></textarea>
          </div>
          <div class="mb-3">
            <label for="chatFileInput" class="form-label">Lampiran</label>
            <input type="file" id="chatFileInput" name="chat_file" class="form-control">
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal Logout -->
  <!-- Modal Logout -->
  <div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <!-- Header -->
        <div class="modal-header bg-danger text-white">
          <h5 class="modal-title mx-auto" id="logoutModalLabel">Apakah Anda yakin ingin keluar dari akun Anda?</h5>
        </div>
        <!-- Body -->
        <div class="modal-body text-center">
          <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Batal</button>
          <a href="index.php" class="btn btn-danger">Log Out</a>
        </div>

      </div>
    </div>
  </div>




  <!-- footeer -->
  <div class="fixed-bottom text-center mb-2">
    &copy; Copyright Rey 2024
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Sidebar toggle
    document.getElementById('sidebarToggle').onclick = function() {
      document.getElementById('sidebar').classList.toggle('active');
      document.getElementById('overlay').classList.toggle('active');
    };

    // Search functionality
    document.getElementById('search').addEventListener('input', function() {
      let filter = this.value.toUpperCase();
      let rows = document.getElementById('userTableBody').getElementsByTagName('tr');
      for (let i = 0; i < rows.length; i++) {
        let cells = rows[i].getElementsByTagName('td');
        let found = false;
        for (let j = 0; j < cells.length; j++) {
          if (cells[j].innerText.toUpperCase().includes(filter)) {
            found = true;
            break;
          }
        }
        rows[i].style.display = found ? '' : 'none';
      }
    });
  </script>
</body>

</html>