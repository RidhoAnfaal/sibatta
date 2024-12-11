<?php
// Database connection configuration
require_once '../koneksi.php';

class UserManagement {
    private $db;

    public function __construct($dbConnection) {
        $this->db = $dbConnection;
    }

    // Function to fetch all students
    public function fetchAllStudents() {
        $query = "SELECT student.student_id, [user].username, [user].email, student.prodi, student.fullName 
                  FROM sibatta.student 
                  JOIN sibatta.[user] ON sibatta.student.user_id = sibatta.[user].user_id
                  WHERE sibatta.[user].role = 'student';";

        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Function to add a new student
    public function addStudent($username, $email, $prodi, $fullName) {
        try {
            $this->db->beginTransaction();

            // Insert into user table
            $userQuery = "INSERT INTO [user] (username, email, role) VALUES (:username, :email, 'student');";
            $stmt = $this->db->prepare($userQuery);
            $stmt->execute(['username' => $username, 'email' => $email]);

            $userId = $this->db->lastInsertId(); // SQL Server supports lastInsertId with PDO.

            // Insert into student table
            $studentQuery = "INSERT INTO student (user_id, prodi, fullName) VALUES (:user_id, :prodi, :fullName);";
            $stmt = $this->db->prepare($studentQuery);
            $stmt->execute(['user_id' => $userId, 'prodi' => $prodi, 'fullName' => $fullName]);

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    // Function to delete a student
    public function deleteStudent($studentId) {
        try {
            $query = "DELETE FROM student WHERE student_id = :student_id;";
            $stmt = $this->db->prepare($query);
            $stmt->execute(['student_id' => $studentId]);

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    // Function to update student data
    public function updateStudent($studentId, $username, $email, $prodi, $fullName) {
        try {
            $this->db->beginTransaction();

            // Update user table
            $userQuery = "UPDATE [user] SET username = :username, email = :email 
                          WHERE user_id = (SELECT user_id FROM student WHERE student_id = :student_id);";
            $stmt = $this->db->prepare($userQuery);
            $stmt->execute(['username' => $username, 'email' => $email, 'student_id' => $studentId]);

            // Update student table
            $studentQuery = "UPDATE student SET prodi = :prodi, fullName = :fullName 
                              WHERE student_id = :student_id;";
            $stmt = $this->db->prepare($studentQuery);
            $stmt->execute(['prodi' => $prodi, 'fullName' => $fullName, 'student_id' => $studentId]);

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    // Function to search for students by student_id
    public function searchStudentById($studentId) {
        $query = "SELECT student.student_id, [user].username, [user].email, student.prodi, student.fullName 
                  FROM student 
                  JOIN [user] ON student.user_id = [user].user_id
                  WHERE student.student_id = :student_id;";
        $stmt = $this->db->prepare($query);
        $stmt->execute(['student_id' => $studentId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}

// Instantiate the UserManagement class
try {
    $dbConnection = new PDO('sqlsrv:Server=MSI;Database=sibatta', '', ''); // Update with your DB credentials
    $dbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $userManager = new UserManagement($dbConnection);

    // Handle POST actions (add, update, delete)
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $action = $_POST['action'] ?? '';

        if ($action === 'add') {
            $userManager->addStudent($_POST['username'], $_POST['email'], $_POST['prodi'], $_POST['fullName']);
        } elseif ($action === 'update') {
            $userManager->updateStudent($_POST['student_id'], $_POST['username'], $_POST['email'], $_POST['prodi'], $_POST['fullName']);
        } elseif ($action === 'delete') {
            $userManager->deleteStudent($_POST['student_id']);
        }
    }

    // Fetch all students for display
    $students = $userManager->fetchAllStudents();
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
  <script type="module" src="https://cdn.jsdelivr.net/npm/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
  <script nomodule src="https://cdn.jsdelivr.net/npm/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
</head>

<body>
  <!-- Header -->
  <?php include 'navbar.php'; ?>

  <div class="d-flex">
    <?php include 'sidebar.php'; ?>

    <!-- Main Content -->
    <div class="container mt-4">
      <div class="d-flex justify-content-between align-items-center">
        <h2>Student List</h2>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addStudentModal">
          <ion-icon name="person-add"></ion-icon> Add Student
        </button>
      </div>

      <div class="my-3">
        <input type="text" class="form-control" id="search" placeholder="Search...">
      </div>

      <table class="table table-striped">
        <thead>
          <tr>
            <th>No</th>
            <th>Student ID</th>
            <th>Full Name</th>
            <th>Email</th>
            <th>Program</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody id="studentTableBody">
          <?php foreach ($students as $index => $student): ?>
            <tr>
              <td><?php echo $index + 1; ?></td>
              <td><?php echo htmlspecialchars($student['student_id']); ?></td>
              <td><?php echo htmlspecialchars($student['fullName']); ?></td>
              <td><?php echo htmlspecialchars($student['email']); ?></td>
              <td><?php echo htmlspecialchars($student['prodi']); ?></td>
              <td>
                <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#updateStudentModal-<?php echo $student['student_id']; ?>">Edit</button>
                <form method="POST" class="d-inline">
                  <input type="hidden" name="action" value="delete">
                  <input type="hidden" name="student_id" value="<?php echo htmlspecialchars($student['student_id']); ?>">
                  <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this student?');">Delete</button>
                </form>
              </td>
            </tr>

            <!-- Update Modal -->
            <div class="modal fade" id="updateStudentModal-<?php echo $student['student_id']; ?>" tabindex="-1" aria-labelledby="updateStudentModalLabel" aria-hidden="true">
              <div class="modal-dialog">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title">Update Student</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body">
                    <form method="POST">
                      <input type="hidden" name="action" value="update">
                      <input type="hidden" name="student_id" value="<?php echo htmlspecialchars($student['student_id']); ?>">
                      <div class="mb-3">
                        <label for="username" class="form-label">Full Name</label>
                        <input type="text" class="form-control" name="fullName" value="<?php echo htmlspecialchars($student['fullName']); ?>" required>
                      </div>
                      <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($student['email']); ?>" required>
                      </div>
                      <div class="mb-3">
                        <label for="prodi" class="form-label">Program</label>
                        <select class="form-select" name="prodi" required>
                          <option value="Teknik Informatika" <?php echo ($student['prodi'] === 'Teknik Informatika' ? 'selected' : ''); ?>>Teknik Informatika</option>
                          <option value="Sistem Informasi" <?php echo ($student['prodi'] === 'Sistem Informasi' ? 'selected' : ''); ?>>Sistem Informasi</option>
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

    <!-- Add Student Modal -->
    <div class="modal fade" id="addStudentModal" tabindex="-1" aria-labelledby="addStudentModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Add New Student</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form method="POST">
              <input type="hidden" name="action" value="add">
              <div class="mb-3">
                <label for="fullName" class="form-label">Full Name</label>
                <input type="text" class="form-control" id="fullName" name="fullName" required>
              </div>
              <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" required>
              </div>
              <div class="mb-3">
                <label for="prodi" class="form-label">Program</label>
                <select class="form-select" id="prodi" name="prodi" required>
                  <option value="Teknik Informatika">Teknik Informatika</option>
                  <option value="Sistem Informasi">Sistem Informasi</option>
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

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
