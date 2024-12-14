<?php
// Database connection configuration
require_once '../koneksi.php';
session_start();

// Check if the user is logged in, if not redirect to login page
if (!isset($_SESSION['username'])) {
    header('Location: ../index.php');
    exit();
}

$username = $_SESSION['username'];

class UserManagement {
    private $db;

    public function __construct($dbConnection) {
        $this->db = $dbConnection;
    }

    // Fetch all students
    public function fetchAllStudents() {
        $query = "SELECT s.student_id, u.username, u.email, s.prodi, s.fullName 
                  FROM sibatta.student AS s 
                  JOIN sibatta.[user] AS u ON s.user_id = u.user_id 
                  WHERE u.role = 'student';";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Add a new student
    public function addStudent($username, $password, $email, $prodi, $fullName) {
        try {
            $this->db->beginTransaction();

            // Hash password for security
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Insert into user table
            $userQuery = "INSERT INTO [user] (username, password, email, role) 
                          VALUES (:username, :password, :email, 'student');";
            $stmt = $this->db->prepare($userQuery);
            $stmt->execute(['username' => $username, 'password' => $hashedPassword, 'email' => $email]);

            // Get last inserted user ID
            $userId = $this->db->lastInsertId();

            // Insert into student table
            $studentQuery = "INSERT INTO student (user_id, prodi, fullName) 
                             VALUES (:user_id, :prodi, :fullName);";
            $stmt = $this->db->prepare($studentQuery);
            $stmt->execute(['user_id' => $userId, 'prodi' => $prodi, 'fullName' => $fullName]);

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Error adding student: " . $e->getMessage());
            return false;
        }
    }

    // Update student data
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
            error_log("Error updating student: " . $e->getMessage());
            return false;
        }
    }

    // Delete student data
    public function deleteStudent($studentId) {
        try {
            $query = "DELETE FROM student WHERE student_id = :student_id;";
            $stmt = $this->db->prepare($query);
            $stmt->execute(['student_id' => $studentId]);
            return true;
        } catch (Exception $e) {
            error_log("Error deleting student: " . $e->getMessage());
            return false;
        }
    }
}

// Instantiate UserManagement class
try {
    $dbConnection = new PDO('sqlsrv:Server=MSI;Database=sibatta', '', ''); // Replace with valid credentials
    $dbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $userManager = new UserManagement($dbConnection);

    // Handle POST actions
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $action = $_POST['action'] ?? '';

        if ($action === 'add') {
            $userManager->addStudent($_POST['username'], $_POST['password'], $_POST['email'], $_POST['prodi'], $_POST['fullName']);
        } elseif ($action === 'update') {
            $userManager->updateStudent($_POST['student_id'], $_POST['username'], $_POST['email'], $_POST['prodi'], $_POST['fullName']);
        } elseif ($action === 'delete') {
            $userManager->deleteStudent($_POST['student_id']);
        }
    }

    // Fetch students for display
    $students = $userManager->fetchAllStudents();
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}
?>


<?php
require_once 'StudentManagement.php'; // Include backend logic
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Management</title>
    <link rel="stylesheet" href="css/crud.css"> <!-- Custom CSS -->
</head>
<body>
    <div class="container">
        <h1>Student Management</h1>

        <!-- Add Student Form -->
        <h2>Add Student</h2>
        <form method="POST" action="studentManagement.php">
            <input type="hidden" name="action" value="add">
            <label>Username:</label>
            <input type="text" name="username" required>
            <label>Password:</label>
            <input type="password" name="password" required>
            <label>Email:</label>
            <input type="email" name="email" required>
            <label>Program (Prodi):</label>
            <input type="text" name="prodi" required>
            <label>Full Name:</label>
            <input type="text" name="fullName" required>
            <button type="submit">Add Student</button>
        </form>

        <!-- Student List -->
        <h2>Student List</h2>
        <table>
            <tr>
                <th>Student ID</th>
                <th>Username</th>
                <th>Email</th>
                <th>Program</th>
                <th>Full Name</th>
                <th>Actions</th>
            </tr>
            <?php foreach ($students as $student): ?>
            <tr>
                <td><?= htmlspecialchars($student['student_id']); ?></td>
                <td><?= htmlspecialchars($student['username']); ?></td>
                <td><?= htmlspecialchars($student['email']); ?></td>
                <td><?= htmlspecialchars($student['prodi']); ?></td>
                <td><?= htmlspecialchars($student['fullName']); ?></td>
                <td>
                    <!-- Update Form -->
                    <form method="POST" action="studentManagement.php" style="display:inline;">
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" name="student_id" value="<?= $student['student_id']; ?>">
                        <input type="text" name="username" value="<?= $student['username']; ?>" required>
                        <input type="email" name="email" value="<?= $student['email']; ?>" required>
                        <input type="text" name="prodi" value="<?= $student['prodi']; ?>" required>
                        <input type="text" name="fullName" value="<?= $student['fullName']; ?>" required>
                        <button type="submit">Update</button>
                    </form>
                    <!-- Delete Form -->
                    <form method="POST" action="studentManagement.php" style="display:inline;">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="student_id" value="<?= $student['student_id']; ?>">
                        <button type="submit" onclick="return confirm('Are you sure you want to delete this student?');">Delete</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
</body>
</html>
