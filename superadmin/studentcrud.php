<?php
// Include the database connection configuration
require_once '../koneksi.php';  // Make sure this path is correct based on your directory structure
session_start();
//Check if the user is logged in, if not redirect to login page
if (!isset($_SESSION['username'])) {
    header('Location: ../index.php');
    exit();
}
$username = $_SESSION['username'];

// Create an instance of the Koneksi class and establish connection
$koneksi = new Koneksi();
$conn = $koneksi->connect(); // This will call the connect method from Koneksi class to get the connection

// Add Student and User function
function addStudentUser($username, $password, $email, $prodi, $fullName)
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

        // Insert into student table (without specifying student_id since it's an identity column)
        $insertStudentQuery = "INSERT INTO [sibatta].[student] (user_id, prodi, fullName)
                               VALUES (?, ?, ?)";
        $studentParams = [$userId, $prodi, $fullName];
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
// function updateStudent($student_id, $fullName, $email, $prodi)
// {
//     global $conn;
//     $query = "UPDATE [sibatta].[student]
//               SET fullName = ?, email = ?, prodi = ?
//               WHERE student_id = ?";
//     $params = [$fullName, $email, $prodi, $student_id];
//     $stmt = sqlsrv_query($conn, $query, $params);

//     if ($stmt === false) {
//         echo "Error updating student: " . print_r(sqlsrv_errors(), true);
//         die();
//     }

//     echo "Student updated successfully!";
// }
// Update Student function
function updateStudent($student_id, $fullName, $email, $prodi)
{
    global $conn;

    // First, update the email in the user table
    $updateUserQuery = "
        UPDATE [sibatta].[user] 
        SET email = ? 
        WHERE user_id = (SELECT user_id FROM [sibatta].[student] WHERE student_id = ?)
    ";
    $userParams = [$email, $student_id];
    $stmtUser = sqlsrv_query($conn, $updateUserQuery, $userParams);

    if ($stmtUser === false) {
        echo "Error updating user email: " . print_r(sqlsrv_errors(), true);
        die();
    }

    // Then, update fullName and prodi in the student table
    $updateStudentQuery = "
        UPDATE [sibatta].[student]
        SET fullName = ?, prodi = ?
        WHERE student_id = ?
    ";
    $studentParams = [$fullName, $prodi, $student_id];
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
    $query = "DELETE FROM [sibatta].[student] WHERE student_id = ?";
    $params = [$student_id];
    $stmt = sqlsrv_query($conn, $query, $params);

    if ($stmt === false) {
        echo "Error deleting student: " . print_r(sqlsrv_errors(), true);
        die();
    }

    echo "Student deleted successfully!";
}

// Search Students function
// function searchStudents($searchTerm)
// {
//     global $conn;
//     $query = "SELECT s.student_id, s.fullName, s.email, s.prodi, u.username
//               FROM [sibatta].[student] s
//               JOIN [sibatta].[user] u ON s.user_id = u.user_id
//               WHERE s.fullName LIKE ? OR s.email LIKE ?";
//     $params = ["%$searchTerm%", "%$searchTerm%"];
//     $stmt = sqlsrv_query($conn, $query, $params);

//     if ($stmt === false) {
//         echo "Error searching students: " . print_r(sqlsrv_errors(), true);
//         die();
//     }

//     $students = [];
//     while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
//         $students[] = $row;
//     }

//     return $students;
// }
// Search students based on the provided search term
function searchStudents($searchTerm)
{
    global $conn;
    $students = [];

    // Use a parameterized query to search students by their email, username, or full name
    $query = "SELECT s.student_id, s.fullName, u.email, s.prodi, u.username
              FROM [sibatta].[student] s
              JOIN [sibatta].[user] u ON s.user_id = u.user_id
              WHERE u.email LIKE ? OR s.fullName LIKE ? OR u.username LIKE ?";

    $searchPattern = "%" . $searchTerm . "%"; // Add wildcards for partial matching
    $params = [$searchPattern, $searchPattern, $searchPattern];

    // Prepare and execute the query with parameters
    $stmt = sqlsrv_query($conn, $query, $params);

    if ($stmt === false) {
        echo "Error searching students: " . print_r(sqlsrv_errors(), true);
        die();
    }

    // Fetch all students that match the search term
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        $students[] = $row;
    }

    return $students;
}


// Fetch all students if search term is empty
// $students = [];
// if (isset($_POST['search'])) {
//     $searchTerm = $_POST['search'];
//     $students = searchStudents($searchTerm);
// } else {
//     // Fetch all students
//     $query = "SELECT s.student_id, s.fullName, s.email, s.prodi, u.username
//               FROM [sibatta].[student] s
//               JOIN [sibatta].[user] u ON s.user_id = u.user_id";
//     $stmt = sqlsrv_query($conn, $query);

//     if ($stmt === false) {
//         echo "Error fetching students: " . print_r(sqlsrv_errors(), true);
//         die();
//     }

//     while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
//         $students[] = $row;
//     }
// }
// Fetch all students if search term is empty
$students = [];
if (isset($_POST['search'])) {
    $searchTerm = $_POST['search'];
    $students = searchStudents($searchTerm);
} else {
    // Fetch all students, with email from the user table
    $query = "SELECT s.student_id, s.fullName, u.email, s.prodi, u.username
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


// Handle form submission for add, update, or delete
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['action'])) {
        if ($_POST['action'] == 'add') {
            // Getting form data for adding student
            $username = $_POST['username'];
            $password = $_POST['password'];
            $email = $_POST['email'];
            $prodi = $_POST['prodi'];
            $fullName = $_POST['fullName'];

            // Call the function to add user and student
            addStudentUser($username, $password, $email, $prodi, $fullName);
        } elseif ($_POST['action'] == 'update') {
            // Update student data
            $student_id = $_POST['student_id'];
            $fullName = $_POST['fullName'];
            $email = $_POST['email'];
            $prodi = $_POST['prodi'];

            // Call update function
            updateStudent($student_id, $fullName, $email, $prodi);
        } elseif ($_POST['action'] == 'delete') {
            // Delete student
            $student_id = $_POST['student_id'];

            // Call delete function
            deleteStudent($student_id);
        }
    }
}

// Close connection
$koneksi->close();
?>

<!-- Frontend Form to Add Student -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student User CRUD</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <?php include 'navbar.php'; ?>
    <div class="d-flex">
        <?php include 'sidebar.php'; ?>
        <div class="container mt-5">
            <h2 class="mb-4">Student Data:</h2>

            <!-- Add Student Form -->
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
                    <input type="text" name="prodi" placeholder="Study Program" class="form-control" required>
                </div>
                <div class="mb-3">
                    <input type="text" name="fullName" placeholder="Full Name" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary">Add Student</button>
            </form>

            <hr>

            <h3 class="mb-4">Student List</h3>

            <!-- Search Form -->
            <form method="POST" class="mb-3">
                <input type="text" name="search" class="form-control" placeholder="Search students" value="<?php echo isset($searchTerm) ? $searchTerm : ''; ?>">
                <button type="submit" class="btn btn-info mt-2">Search</button>
            </form>

            <!-- Student Table -->
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Program</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($students as $index => $student): ?>
                        <tr>
                            <td><?php echo $index + 1; ?></td>
                            <td><?php echo htmlspecialchars($student['fullName']); ?></td>
                            <td><?php echo htmlspecialchars($student['email']); ?></td>
                            <td><?php echo htmlspecialchars($student['prodi']); ?></td>
                            <td>
                                <!-- Edit Button -->
                                <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#updateStudentModal-<?php echo $student['student_id']; ?>">Edit</button>
                                <!-- Delete Button -->
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="student_id" value="<?php echo $student['student_id']; ?>">
                                    <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                </form>
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
                                        <form method="POST" action="">
                                            <input type="hidden" name="action" value="update">
                                            <input type="hidden" name="student_id" value="<?php echo $student['student_id']; ?>">
                                            <div class="mb-3">
                                                <input type="text" name="fullName" value="<?php echo htmlspecialchars($student['fullName']); ?>" class="form-control" required>
                                            </div>
                                            <div class="mb-3">
                                                <input type="email" name="email" value="<?php echo htmlspecialchars($student['email']); ?>" class="form-control" required>
                                            </div>
                                            <div class="mb-3">
                                                <input type="text" name="prodi" value="<?php echo htmlspecialchars($student['prodi']); ?>" class="form-control" required>
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

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>