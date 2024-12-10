<?php
include 'koneksi.php'; // Include the database connection
session_start();

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header('Location: login.php'); // Redirect to login if not logged in
    exit();
}

// Retrieve the logged-in user's username
$username = $_SESSION['username'];

// Get the user ID associated with the username
$sql = "SELECT user_id FROM [sibatta].[user] WHERE username = ?";
$params = array($username);
//$stmt = sqlsrv_query($conn, $sql, $params);
//$user = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

//if ($user) {
    //$userId = $user['user_id'];
//} else {
    // Handle the case if the user is not found in the database
    //$userId = 0;
//}

$uploadDir = 'uploads/';
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

$message = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['files'])) {
    // Retrieve the title from the form
    $title = isset($_POST['title']) ? $_POST['title'] : '';
    $uploadedAt = date('Y-m-d'); // Current date

    if ($userId > 0 && !empty($title)) {
        foreach ($_FILES['files']['tmp_name'] as $key => $tmpName) {
            $fileName = $_FILES['files']['name'][$key];
            $fileTmpName = $_FILES['files']['tmp_name'][$key];
            $fileError = $_FILES['files']['error'][$key];

            if ($fileError === UPLOAD_ERR_OK) {
                // Create a unique file name to avoid overwriting
                $targetFile = $uploadDir . time() . '_' . basename($fileName);
                if (move_uploaded_file($fileTmpName, $targetFile)) {
                    // Insert file data into the database
                    $sql = "INSERT INTO [sibatta].[document] (user_id, title, uploaded_at) 
                            VALUES (?, ?, ?)";
                    $params = [$userId, $title, $uploadedAt];
                    $stmt = sqlsrv_query($conn, $sql, $params);

                    if ($stmt) {
                        $message = 'File uploaded and saved to the database successfully!';
                    } else {
                        $message = 'Database error: ' . print_r(sqlsrv_errors(), true);
                    }
                } else {
                    $message = 'Error moving uploaded file.';
                }
            } else {
                $message = 'There was an error with the file upload.';
            }
        }
    } else {
        $message = 'Please provide a valid Title.';
    }
}

// Retrieve list of uploaded documents from the database
//$sql = "SELECT document_id, title, uploaded_at, validated_by FROM [sibatta].[document]";
//$stmt = sqlsrv_query($conn, $sql);
//$documents = [];
//if ($stmt) {
    //while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        //$documents[] = $row;
    //}
//}
?>



<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <script type="module" src="https://cdn.jsdelivr.net/npm/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://cdn.jsdelivr.net/npm/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
    <link rel="stylesheet" href="css/upload.css">
</head>

<body>
    <!-- Header -->
    <?php include 'navbar.php'; ?>

    <div class="d-flex">
        <?php include 'Sidebar.php'; ?>

    <!-- Main Content -->
    <div class="container mt-4">
        <h1>Upload File</h1>
        <?php if ($message): ?>
            <div class="alert alert-info"><?php echo $message; ?></div>
        <?php endif; ?>

        <!-- Form Upload -->
        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="title" class="form-label">Title</label>
                <input type="text" class="form-control" id="title" name="title" required>
            </div>
            <div class="mb-3">
                <label for="fileInput" class="form-label">Select Files</label>
                <input type="file" class="form-control" id="fileInput" name="files[]" multiple>
            </div>
            <button type="submit" class="btn btn-primary">Upload</button>
        </form>

        <!-- Uploaded Files -->
        <div class="table-container mt-4">
            <h3>Uploaded Documents</h3>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Document ID</th>
                        <th>Title</th>
                        <th>Uploaded At</th>
                        <th>Validated By</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($documents)): ?>
                        <?php foreach ($documents as $doc): ?>
                            <tr>
                                <td><?php echo $doc['document_id']; ?></td>
                                <td><?php echo $doc['title']; ?></td>
                                <td><?php echo $doc['uploaded_at']->format('Y-m-d'); ?></td>
                                <td><?php echo $doc['validated_by'] ?: 'Not validated'; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-center">No documents uploaded yet.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Optional JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('overlay');
        const sidebarToggle = document.getElementById('sidebarToggle');

        // Close sidebar when overlay is clicked
        overlay.addEventListener('click', () => {
            sidebar.classList.remove('active');
            overlay.classList.remove('active');
        });

        function confirmLogout(event) {
            event.preventDefault(); // Prevent langsung keluar
            if (confirm("Apakah Anda yakin ingin log out?")) {
                window.location.href = "login.php";
            }
        }
        document.getElementById("composeBtn").addEventListener("click", function() {
            // Hide default content and show the compose form
            document.getElementById("defaultContent").style.display = "none";
            document.getElementById("composeForm").style.display = "block";
        });

        // Reset modal content when it is closed
        const emailModal = document.getElementById('emailModal');
        emailModal.addEventListener('hidden.bs.modal', function() {
            // Reset content to show default content
            document.getElementById("defaultContent").style.display = "block";
            document.getElementById("composeForm").style.display = "none";
        });
    </script>
</body>

</html>