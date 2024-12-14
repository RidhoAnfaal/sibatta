<?php
require_once '../koneksi.php';

class CrudOperations
{
    private $conn;

    public function __construct()
    {
        $database = new Koneksi();
        $this->conn = $database->connect();
    }

    // Add new user and student
    // public function addStudentUser($username, $password, $email, $prodi, $fullName, $studentId)
    // {
    //     // Start transaction
    //     sqlsrv_begin_transaction($this->conn);

    //     try {
    //         // Insert into user table
    //         $role = 'student';
    //         $insertUserQuery = "INSERT INTO [sibatta].[user] (username, password, email, role)
    //                             OUTPUT INSERTED.user_id
    //                             VALUES (?, ?, ?, ?)";
    //         $userParams = [$username, $password, $email, $role];
    //         $stmtUser = sqlsrv_query($this->conn, $insertUserQuery, $userParams);

    //         if ($stmtUser === false) {
    //             throw new Exception("Error inserting into user: " . print_r(sqlsrv_errors(), true));
    //         }

    //         // Get the generated user_id
    //         sqlsrv_fetch($stmtUser);
    //         $userId = sqlsrv_get_field($stmtUser, 0);

    //         // Enable IDENTITY_INSERT for student table
    //         sqlsrv_query($this->conn, "SET IDENTITY_INSERT [sibatta].[student] ON");

    //         // Insert into student table with explicit student_id
    //         $insertStudentQuery = "INSERT INTO [sibatta].[student] (student_id, user_id, prodi, fullName)
    //                                VALUES (?, ?, ?, ?)";
    //         $studentParams = [$studentId, $userId, $prodi, $fullName];
    //         $stmtStudent = sqlsrv_query($this->conn, $insertStudentQuery, $studentParams);

    //         if ($stmtStudent === false) {
    //             throw new Exception("Error inserting into student: " . print_r(sqlsrv_errors(), true));
    //         }

    //         // Disable IDENTITY_INSERT
    //         sqlsrv_query($this->conn, "SET IDENTITY_INSERT [sibatta].[student] OFF");

    //         // Commit transaction
    //         sqlsrv_commit($this->conn);
    //         echo "Student and user added successfully!";
    //     } catch (Exception $e) {
    //         sqlsrv_rollback($this->conn);
    //         echo $e->getMessage();
    //     }
    // }
    public function addStudentUser($username, $password, $email, $prodi, $fullName, $studentId = null)
    {
        // Start transaction
        sqlsrv_begin_transaction($this->conn);

        try {
            // Insert into user table
            $role = 'student';
            $insertUserQuery = "INSERT INTO [sibatta].[user] (username, password, email, role)
                                OUTPUT INSERTED.user_id
                                VALUES (?, ?, ?, ?)";
            $userParams = [$username, $password, $email, $role];
            $stmtUser = sqlsrv_query($this->conn, $insertUserQuery, $userParams);

            if ($stmtUser === false) {
                throw new Exception("Error inserting into user: " . print_r(sqlsrv_errors(), true));
            }

            // Get the generated user_id
            sqlsrv_fetch($stmtUser);
            $userId = sqlsrv_get_field($stmtUser, 0);

            // Insert into student table without setting IDENTITY_INSERT
            if ($studentId === null) {
                // If student_id is auto-generated, do not pass it
                $insertStudentQuery = "INSERT INTO [sibatta].[student] (user_id, prodi, fullName)
                                    VALUES (?, ?, ?)";
                $studentParams = [$userId, $prodi, $fullName];
            } else {
                // If student_id is explicitly provided
                $insertStudentQuery = "INSERT INTO [sibatta].[student] (student_id, user_id, prodi, fullName)
                                    VALUES (?, ?, ?, ?)";
                $studentParams = [$studentId, $userId, $prodi, $fullName];
            }

            $stmtStudent = sqlsrv_query($this->conn, $insertStudentQuery, $studentParams);

            if ($stmtStudent === false) {
                throw new Exception("Error inserting into student: " . print_r(sqlsrv_errors(), true));
            }

            // Commit transaction
            sqlsrv_commit($this->conn);
            echo "Student and user added successfully!";
        } catch (Exception $e) {
            sqlsrv_rollback($this->conn);
            echo $e->getMessage();
        }
    }

    // Update user and student
    public function updateStudentUser($userId, $username, $password, $email, $prodi, $fullName, $studentId)
    {
        // Start transaction
        sqlsrv_begin_transaction($this->conn);

        try {
            // Update user table
            $updateUserQuery = "UPDATE [sibatta].[user]
                                SET username = ?, password = ?, email = ?
                                WHERE user_id = ?";
            $userParams = [$username, $password, $email, $userId];
            $stmtUser = sqlsrv_query($this->conn, $updateUserQuery, $userParams);

            if ($stmtUser === false) {
                throw new Exception("Error updating user: " . print_r(sqlsrv_errors(), true));
            }

            // Update student table
            $updateStudentQuery = "UPDATE [sibatta].[student]
                                   SET prodi = ?, fullName = ?
                                   WHERE student_id = ? AND user_id = ?";
            $studentParams = [$prodi, $fullName, $studentId, $userId];
            $stmtStudent = sqlsrv_query($this->conn, $updateStudentQuery, $studentParams);

            if ($stmtStudent === false) {
                throw new Exception("Error updating student: " . print_r(sqlsrv_errors(), true));
            }

            // Commit transaction
            sqlsrv_commit($this->conn);
            echo "Student and user updated successfully!";
        } catch (Exception $e) {
            sqlsrv_rollback($this->conn);
            echo $e->getMessage();
        }
    }

    // Delete user and student
    public function deleteStudentUser($userId, $studentId)
    {
        // Start transaction
        sqlsrv_begin_transaction($this->conn);

        try {
            // Delete from student table
            $deleteStudentQuery = "DELETE FROM [sibatta].[student] WHERE student_id = ? AND user_id = ?";
            $studentParams = [$studentId, $userId];
            $stmtStudent = sqlsrv_query($this->conn, $deleteStudentQuery, $studentParams);

            if ($stmtStudent === false) {
                throw new Exception("Error deleting student: " . print_r(sqlsrv_errors(), true));
            }

            // Delete from user table
            $deleteUserQuery = "DELETE FROM [sibatta].[user] WHERE user_id = ?";
            $userParams = [$userId];
            $stmtUser = sqlsrv_query($this->conn, $deleteUserQuery, $userParams);

            if ($stmtUser === false) {
                throw new Exception("Error deleting user: " . print_r(sqlsrv_errors(), true));
            }

            // Commit transaction
            sqlsrv_commit($this->conn);
            echo "Student and user deleted successfully!";
        } catch (Exception $e) {
            sqlsrv_rollback($this->conn);
            echo $e->getMessage();
        }
    }

    public function getAllStudents()
    {
        $query = "SELECT s.student_id, u.username, u.email, s.prodi, s.fullName
                FROM [sibatta].[student] s
                INNER JOIN [sibatta].[user] u ON s.user_id = u.user_id";
        $stmt = sqlsrv_query($this->conn, $query);

        if ($stmt === false) {
            die("Error fetching students: " . print_r(sqlsrv_errors(), true));
        }

        $students = [];
        while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            $students[] = $row;
        }
        return $students;
    }


    public function closeConnection()
    {
        sqlsrv_close($this->conn);
    }
}

// Example usage:
$crud = new CrudOperations();

// Add new user and student
// $crud->addStudentUser('john_doe', 'pass1234', 'john@example.com', 'Computer Science', 'John Doe', 1001);

// Update user and student
// $crud->updateStudentUser(1, 'john_doe_updated', 'newpass123', 'john_updated@example.com', 'Information Systems', 'John Updated Doe', 1001);

// Delete user and student
// $crud->deleteStudentUser(1, 1001);

$crud->closeConnection();
?>
