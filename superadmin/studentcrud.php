<?php
// Include the database connection configuration
require_once '../koneksi.php';  // Adjust path based on your directory structure
session_start();

// Check if the user is logged in, if not redirect to login page
if (!isset($_SESSION['username'])) {
    header('Location: ../index.php');
    exit();
}

$username = $_SESSION['username'];

// Create an instance of the Koneksi class and establish connection
$koneksi = new Koneksi();
$conn = $koneksi->connect(); // This will call the connect method from the Koneksi class

// Add Student and User function
function addStudentUser($username, $password, $email, $prodi, $fullName, $kelas)
{
    global $conn;

    // Start transaction
    sqlsrv_begin_transaction($conn);

    try {
        // Insert into user table
        $role = 'student';
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

        // Insert into student table
        $insertStudentQuery = "INSERT INTO [sibatta].[student] (user_id, prodi, fullName, kelas)
                               VALUES (?, ?, ?, ?)";
        $studentParams = [$userId, $prodi, $fullName, $kelas];
        $stmtStudent = sqlsrv_query($conn, $insertStudentQuery, $studentParams);

        if ($stmtStudent === false) {
            throw new Exception("Error inserting into student: " . print_r(sqlsrv_errors(), true));
        }

        // Commit transaction
        sqlsrv_commit($conn);
        echo "Student and user added successfully!";
    } catch (Exception $e) {
        sqlsrv_rollback($conn);
        echo "Failed to add student and user: " . $e->getMessage();
    }
}

// Update Student function
function updateStudent($student_id, $fullName, $email, $prodi, $kelas, $username, $password)
{
    global $conn;

    // Update user table
    $updateUserQuery = "
        UPDATE [sibatta].[user] 
        SET email = ?, username = ?, password = ?
        WHERE user_id = (SELECT user_id FROM [sibatta].[student] WHERE student_id = ?)
    ";
    $userParams = [$email, $username, $password, $student_id];
    $stmtUser = sqlsrv_query($conn, $updateUserQuery, $userParams);

    if ($stmtUser === false) {
        echo "Error updating user data: " . print_r(sqlsrv_errors(), true);
        die();
    }

    // Update student table
    $updateStudentQuery = "
        UPDATE [sibatta].[student]
        SET fullName = ?, prodi = ?, kelas = ?
        WHERE student_id = ?
    ";
    $studentParams = [$fullName, $prodi, $kelas, $student_id];
    $stmtStudent = sqlsrv_query($conn, $updateStudentQuery, $studentParams);

    if ($stmtStudent === false) {
        echo "Error updating student data: " . print_r(sqlsrv_errors(), true);
        die();
    }
}

// Delete Student function
function deleteStudent($student_id)
{
    global $conn;

    // Start transaction
    sqlsrv_begin_transaction($conn);

    try {
        // Get associated user_id for the student
        $query = "SELECT user_id FROM [sibatta].[student] WHERE student_id = ?";
        $params = [$student_id];
        $stmt = sqlsrv_query($conn, $query, $params);

        if ($stmt === false) {
            throw new Exception("Error fetching user_id: " . print_r(sqlsrv_errors(), true));
        }

        sqlsrv_fetch($stmt);
        $user_id = sqlsrv_get_field($stmt, 0);

        if (!$user_id) {
            throw new Exception("No associated user_id found for student_id: $student_id");
        }

        // Delete the student record
        $deleteStudentQuery = "DELETE FROM [sibatta].[student] WHERE student_id = ?";
        $stmtStudent = sqlsrv_query($conn, $deleteStudentQuery, [$student_id]);

        if ($stmtStudent === false) {
            throw new Exception("Error deleting student: " . print_r(sqlsrv_errors(), true));
        }

        // Delete the user record
        $deleteUserQuery = "DELETE FROM [sibatta].[user] WHERE user_id = ?";
        $stmtUser = sqlsrv_query($conn, $deleteUserQuery, [$user_id]);

        if ($stmtUser === false) {
            throw new Exception("Error deleting user: " . print_r(sqlsrv_errors(), true));
        }

        // Commit transaction
        sqlsrv_commit($conn);

        echo "Student and associated user deleted successfully!";
    } catch (Exception $e) {
        sqlsrv_rollback($conn);
        echo "Failed to delete student and user: " . $e->getMessage();
    }
}

// Search Students function
function searchStudents($searchTerm)
{
    global $conn;
    $students = [];

    // Search query including 'kelas'
    $query = "SELECT s.student_id, s.fullName, u.email, s.prodi, u.username, s.kelas
              FROM [sibatta].[student] s
              JOIN [sibatta].[user] u ON s.user_id = u.user_id
              WHERE u.email LIKE ? OR s.fullName LIKE ? OR s.kelas LIKE ?";
    $searchPattern = "%" . $searchTerm . "%";
    $params = [$searchPattern, $searchPattern, $searchPattern];

    $stmt = sqlsrv_query($conn, $query, $params);

    if ($stmt === false) {
        echo "Error searching students: " . print_r(sqlsrv_errors(), true);
        die();
    }

    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        $students[] = $row;
    }

    echo "<pre>";
    print_r($students); // Display the results of the search query
    echo "</pre>";

    return $students;
}

// Main logic
$students = [];
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['action'])) {
        if ($_POST['action'] == 'add') {
            $username = $_POST['username'];
            $password = $_POST['password'];
            $email = $_POST['email'];
            $prodi = $_POST['prodi'];
            $fullName = $_POST['fullName'];
            $kelas = $_POST['kelas'];

            addStudentUser($username, $password, $email, $prodi, $fullName, $kelas);
        } elseif ($_POST['action'] == 'update') {
            $student_id = $_POST['student_id'];
            $fullName = $_POST['fullName'];
            $email = $_POST['email'];
            $prodi = $_POST['prodi'];
            $kelas = $_POST['kelas'];
            $username = $_POST['username'];
            $password = $_POST['password'];

            updateStudent($student_id, $fullName, $email, $prodi, $kelas, $username, $password);
        } elseif ($_POST['action'] == 'delete') {
            $student_id = $_POST['student_id'];
            deleteStudent($student_id);
        }
    } elseif (!empty($_POST['search'])) {
        $searchTerm = $_POST['search'];
        $students = searchStudents($searchTerm);
    }
}

// Fetch all students if no search term provided
if (empty($students)) {
    $query = "SELECT s.student_id, s.fullName, u.email, s.prodi, u.username, s.kelas
              FROM [sibatta].[student] s
              JOIN [sibatta].[user] u ON s.user_id = u.user_id";
    $stmt = sqlsrv_query($conn, $query);

    if ($stmt === false) {
        echo "Error fetching students: " . print_r(sqlsrv_errors(), true);
        die();
    }

    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        $students[] = $row;
    }
}

// Close the connection
$koneksi->close();
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student User CRUD</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
    <?php include 'navbar.php'; ?>
    <div class="main-content">
        <?php include 'sidebar.php'; ?>
        <div class="container mt-5">
            <h2 class="mb-4">Student Data:</h2>

            <!-- Add Student Form -->
            <form id="addStudentForm" method="POST">
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
                    <select name="prodi" class="form-select" required>
                        <option value="">Select Study Program</option>
                        <option value="Informatics Engineering">Informatics Engineering</option>
                        <option value="Business Information System">Business Information System</option>
                    </select>
                </div>
                <div class="mb-3">
                    <select name="kelas" class="form-select" required>
                        <option value="">Select Class</option>
                        <?php for ($i = 'A'; $i <= 'I'; $i++): ?>
                            <option value="4<?php echo $i; ?>">4<?php echo $i; ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <input type="text" name="fullName" placeholder="Full Name" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary">Add Student</button>
            </form>

            <hr>

            <h3 class="mb-4">Student List</h3>

            <!-- Search Form -->
            <form method="POST" action="studentcrud.php">
                <div class="input-group mb-3">
                    <input type="text" name="search" class="form-control" placeholder="Search students (fullName, email, kelas)" 
                    value="<?php echo isset($_POST['search']) ? $_POST['search'] : ''; ?>">
                    <button type="submit" class="btn btn-info">Search</button>
                </div>
            </form>
            
            <!-- Student Table -->
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Program</th>
                        <th>Class</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="studentList">
                    <?php foreach ($students as $index => $student): ?>
                        <tr>
                            <td><?php echo $index + 1; ?></td>
                            <td><?php echo htmlspecialchars($student['fullName']); ?></td>
                            <td><?php echo htmlspecialchars($student['email']); ?></td>
                            <td><?php echo htmlspecialchars($student['prodi']); ?></td>
                            <td><?php echo htmlspecialchars($student['kelas']); ?></td>
                            <td>
                                <!-- Edit Button -->
                                <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#updateStudentModal-<?php echo $student['student_id']; ?>">Edit</button>
                                <!-- Delete Button -->
                                <button class="btn btn-danger btn-sm deleteStudent" data-id="<?php echo $student['student_id']; ?>">Delete</button>
                            </td>
                        </tr>

                        <!-- Edit Student Modal -->
                        <div class="modal fade" id="updateStudentModal-<?php echo $student['student_id']; ?>" tabindex="-1" aria-labelledby="updateStudentModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Update Student</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <form class="updateStudentForm" method="POST">
                                            <input type="hidden" name="action" value="update">
                                            <input type="hidden" name="student_id" value="<?php echo $student['student_id']; ?>">
                                            <div class="mb-3">
                                                <input type="text" name="username" value="<?php echo htmlspecialchars($student['username']); ?>" class="form-control" required>
                                            </div>
                                            <div class="mb-3">
                                                <input type="password" name="password" placeholder="New Password" class="form-control">
                                            </div>
                                            <div class="mb-3">
                                                <input type="text" name="fullName" value="<?php echo htmlspecialchars($student['fullName']); ?>" class="form-control" required>
                                            </div>
                                            <div class="mb-3">
                                                <input type="email" name="email" value="<?php echo htmlspecialchars($student['email']); ?>" class="form-control" required>
                                            </div>
                                            <div class="mb-3">
                                                <select name="prodi" class="form-select" required>
                                                    <option value="Informatics Engineering" <?php echo $student['prodi'] == 'Informatics Engineering' ? 'selected' : ''; ?>>Informatics Engineering</option>
                                                    <option value="Business Information System" <?php echo $student['prodi'] == 'Business Information System' ? 'selected' : ''; ?>>Business Information System</option>
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <select name="kelas" class="form-select" required>
                                                    <?php for ($i = 'A'; $i <= 'I'; $i++): ?>
                                                        <option value="4<?php echo $i; ?>" <?php echo $student['kelas'] == '4' . $i ? 'selected' : ''; ?>>4<?php echo $i; ?></option>
                                                    <?php endfor; ?>
                                                </select>
                                            </div>
                                            <button type="submit" class="btn btn-primary">Update</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Footer -->
        <?php include 'footer.php'; ?>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        <script>
            // Handle form submissions using AJAX for reload-free updates
            $(document).on('submit', 'form', function (e) {
                e.preventDefault();
                const form = $(this);
                $.post('studentcrud.php', form.serialize(), function () {
                    location.reload();
                });
            });

            // Handle delete button clicks
            $(document).on('click', '.deleteStudent', function () {
                const studentId = $(this).data('id');
                $.post('studentcrud.php', { action: 'delete', student_id: studentId }, function () {
                    location.reload();
                });
            });
        </script>
    </div>
</body>

</html>
