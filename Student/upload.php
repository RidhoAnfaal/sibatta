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

// Query to get student data based on user_id
$queryStudent = "SELECT TOP (1) 
                      [student_id], 
                      [user_id], 
                      [prodi], 
                      [fullName], 
                      [kelas] 
                 FROM [sibatta].[sibatta].[student] 
                 WHERE user_id = ?";
$paramsStudent = array($userId);
$stmtStudent = sqlsrv_query($conn, $queryStudent, $paramsStudent);
if ($stmtStudent === false) {
    die(print_r(sqlsrv_errors(), true));
}

// Fetch the student data
$student = sqlsrv_fetch_array($stmtStudent, SQLSRV_FETCH_ASSOC);

// Create the upload directory if it doesn't exist
$uploadDir = 'uploads/';
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

$message = '';
$messageType = ''; // This will hold the message type for styling

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['files'])) {
    // Initialize title with an empty string, it will be replaced by the file name
    $title = '';
    $uploadedAt = date('Y-m-d'); // Current date

    if ($userId > 0) {
        foreach ($_FILES['files']['tmp_name'] as $key => $tmpName) {
            $fileName = $_FILES['files']['name'][$key];
            $fileTmpName = $_FILES['files']['tmp_name'][$key];
            $fileError = $_FILES['files']['error'][$key];
            $fileSize = $_FILES['files']['size'][$key];
            $fileType = $_FILES['files']['type'][$key];

            // Debugging: Output $_FILES array for checking
            echo '<pre>';
            print_r($_FILES);
            echo '</pre>';

            // Set the title as the file name (without extension)
            $title = pathinfo($fileName, PATHINFO_FILENAME);

            // Set message to yellow (processing)
            $message = 'Processing file upload...';
            $messageType = 'text-warning';

            // Check if there were any errors with the upload
            if ($fileError === UPLOAD_ERR_OK) {
                // Validate the file size (e.g., limit to 5MB)
                if ($fileSize > 5 * 1024 * 1024) {
                    $message = 'File size exceeds the limit of 5MB.';
                    $messageType = 'text-danger'; // Red (rejected)
                    continue;
                }

                // Validate the file type (e.g., allow only PDF and image files)
                $allowedTypes = ['image/jpeg', 'image/png', 'application/pdf']; // You can adjust this
                if (!in_array($fileType, $allowedTypes)) {
                    $message = 'Invalid file type. Only JPEG, PNG, and PDF files are allowed.';
                    $messageType = 'text-danger'; // Red (rejected)
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
                        $messageType = 'text-success'; // Green (accepted)
                    } else {
                        $message = 'Database error: ' . print_r(sqlsrv_errors(), true);
                        $messageType = 'text-danger'; // Red (rejected)
                    }
                } else {
                    $message = 'Error moving uploaded file.';
                    $messageType = 'text-danger'; // Red (rejected)
                }
            } else {
                $message = 'There was an error with the file upload.';
                $messageType = 'text-danger'; // Red (rejected)
            }
        }
    } else {
        $message = 'User ID not found.';
        $messageType = 'text-danger'; // Red (rejected)
    }
}

// Handle the search functionality
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Modify the query to filter by user_id and search term if provided
$query = "SELECT document_id, title, uploaded_at, document_status, file_path FROM [sibatta].[document] WHERE user_id = ?";
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
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
                <div class="alert <?php echo $messageType; ?>"><?php echo $message; ?></div>
            <?php endif; ?>

            <!-- Search Bar -->
            <form method="GET" class="mb-3">
                <div class="input-group ms-auto" style="max-width: 300px;">
                    <input type="text" class="form-control" name="search" placeholder="Search" value="<?php echo htmlspecialchars($search); ?>">
                    <button class="btn btn-outline-success" type="submit">Search</button>
                </div>
            </form>

            <!-- Upload Button to Trigger Modal -->
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#uploadModal">
                Upload
            </button>

            <!-- Modal -->
            <div class="modal fade" id="uploadModal" tabindex="-1" aria-labelledby="uploadModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="uploadModalLabel">Select File to Upload</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form method="POST" enctype="multipart/form-data">
                                <div class="mb-3">
                                    <label for="fileInput" class="form-label">Choose File</label>
                                    <input type="file" class="form-control" id="fileInput" name="files[]" multiple>
                                </div>
                                <button type="submit" class="btn btn-primary w-100">Upload</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Uploaded Files -->
            <div class="table-container mt-4">
                <h3>Uploaded Documents</h3>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Student ID</th>
                            <th>Program Studi</th>
                            <th>Nama</th>
                            <th>Kelas</th>
                            <th>Title</th>
                            <th>Uploaded</th>
                            <th>Document Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($documents)): ?>
                            <?php foreach ($documents as $doc): ?>
                                <tr>
                                    <td><?php echo $student['student_id']; ?></td>
                                    <td><?php echo $student['prodi']; ?></td>
                                    <td><?php echo $student['fullName']; ?></td>
                                    <td><?php echo $student['kelas']; ?></td>
                                    <td><?php echo $doc['title']; ?></td>
                                    <td><?php echo $doc['uploaded_at']->format('Y-m-d'); ?></td>
                
                                    <td><?php echo $doc['document_status'] ?: 'Not validated'; ?></td>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center">No documents uploaded yet.</td>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
