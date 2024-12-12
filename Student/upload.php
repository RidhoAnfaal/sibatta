<?php
include 'koneksi.php'; // Include the database connection
session_start();

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header('Location: index.php'); // Redirect to login if not logged in
    exit();
}

// Retrieve the logged-in user's username
$username = $_SESSION['username'];

// Get the user ID associated with the username
$sql = "SELECT user_id FROM [sibatta].[user] WHERE username = ?";
$params = array($username);
$stmt = sqlsrv_query($conn, $sql, $params);
$user = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

if ($user) {
    $userId = $user['user_id'];
} else {
    // Handle the case if the user is not found in the database
    $userId = 0;
}

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
            $fileSize = $_FILES['files']['size'][$key];
            $fileType = $_FILES['files']['type'][$key];

            // Check if there were any errors with the upload
            if ($fileError === UPLOAD_ERR_OK) {

                // Validate the file size (e.g., limit to 5MB)
                if ($fileSize > 5 * 1024 * 1024) {
                    $message = 'File size exceeds the limit of 5MB.';
                    continue;
                }

                // Validate the file type (e.g., allow only PDF and image files)
                $allowedTypes = ['image/jpeg', 'image/png', 'application/pdf']; // You can adjust this
                if (!in_array($fileType, $allowedTypes)) {
                    $message = 'Invalid file type. Only JPEG, PNG, and PDF files are allowed.';
                    continue;
                }

                // Generate a unique filename to avoid overwrite
                $targetFile = $uploadDir . time() . '_' . basename($fileName);

                // Move the uploaded file to the target directory
                if (move_uploaded_file($fileTmpName, $targetFile)) {
                    // Insert file data into the database including the file path
                    $sql = "INSERT INTO [sibatta].[document] (user_id, title, uploaded_at, file_path) 
                            VALUES (?, ?, ?, ?)";
                    $params = [$userId, $title, $uploadedAt, $targetFile];
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

// Handle the search functionality
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Modify the query to filter by user_id and search term if provided
$query = "SELECT document_id, title, uploaded_at, validated_by, file_path FROM [sibatta].[document] WHERE user_id = ?";
$params = [$userId]; // Filter by the logged-in user's ID

if (!empty($search)) {
    $query .= " AND (title LIKE ? OR CAST(document_id AS NVARCHAR) LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$stmt = sqlsrv_query($conn, $query, $params);
$documents = [];
if ($stmt) {
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        $documents[] = $row;
    }
}
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

           <!-- Search Bar -->
<form method="GET" class="mb-3">
    <div class="input-group ms-auto" style="max-width: 300px;">
        <input type="text" class="form-control" name="search" placeholder="Search" value="<?php echo htmlspecialchars($search); ?>">
        <button class="btn btn-outline-success" type="submit">Search</button>
    </div>
</form>

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
                            <th>Uploaded</th>
                            <th>Validated</th>
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
                                <td colspan="5" class="text-center">No documents uploaded yet.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
 <!-- Footer -->
</div>
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