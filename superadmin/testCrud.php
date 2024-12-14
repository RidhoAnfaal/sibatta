<?php
require_once 'crud.php';
$crud = new CrudOperations();
$students = $crud->getAllStudents();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student and User Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2 class="mb-4">Student and User Management</h2>
    
    <!-- Add Student Form -->
    <form method="POST" action="crud.php">
        <h3>Add Student</h3>
        <div class="mb-3">
            <input type="text" name="student_id" placeholder="Student ID (optional)" class="form-control">
        </div>
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
        <button type="submit" name="add" class="btn btn-primary">Add Student</button>
    </form>

    <hr>

    <!-- Student List -->
    <h3>Student List</h3>
    <table class="table table-striped mt-5">
        <thead>
            <tr>
                <th>Student ID</th>
                <th>Username</th>
                <th>Email</th>
                <th>Study Program</th>
                <th>Full Name</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($students as $student): ?>
                <tr>
                    <td><?= $student['student_id'] ?></td>
                    <td><?= $student['username'] ?></td>
                    <td><?= $student['email'] ?></td>
                    <td><?= $student['prodi'] ?></td>
                    <td><?= $student['fullName'] ?></td>
                    <td>
                        <!-- Update Button -->
                        <a href="update_student.php?student_id=<?= $student['student_id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                        <!-- Delete Button -->
                        <a href="crud.php?delete=<?= $student['student_id'] ?>" class="btn btn-danger btn-sm">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
