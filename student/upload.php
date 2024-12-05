<?php
session_start();  // Start session

if (!isset($_SESSION['user_id'])) {
    $message = "User is not logged in.";
} else {
    // Define the directory to save uploaded files
    $uploadDir = 'uploads/';

    // Create uploads directory if it doesn't exist
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $message = '';
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['files'])) {
        // Get the user ID from the session
        $userId = $_SESSION['user_id'];

        // Loop through uploaded files
        foreach ($_FILES['files']['tmp_name'] as $key => $tmpName) {
            $fileName = $_FILES['files']['name'][$key];
            $fileSize = $_FILES['files']['size'][$key];
            $fileTmpName = $_FILES['files']['tmp_name'][$key];
            $fileError = $_FILES['files']['error'][$key];

            if ($fileError === UPLOAD_ERR_OK) {
                // Move the uploaded file to the upload directory
                $targetFile = $uploadDir . basename($fileName);
                if (move_uploaded_file($fileTmpName, $targetFile)) {
                    // Insert the record into the database
                    $title = "Uploaded Document"; // Modify as needed
                    $uploadedAt = date('Y-m-d'); // Current date
                    $validatedBy = null; // Set to null or another value if needed

                    $sql = "INSERT INTO [sibatta].[document] (user_id, title, uploaded_at, validated_by) 
                            VALUES (?, ?, ?, ?)";
                    $params = array($userId, $title, $uploadedAt, $validatedBy);

                    $stmt = sqlsrv_query($conn, $sql, $params);

                    if ($stmt) {
                        $message = 'File uploaded and saved to the database successfully!';
                    } else {
                        $message = 'Database error: ' . print_r(sqlsrv_errors(), true);
                    }
                } else {
                    $message = 'Error uploading file.';
                }
            } else {
                $message = 'There was an error with the file upload.';
            }
        }
    }

    // Retrieve list of uploaded files
    $files = array_diff(scandir($uploadDir), array('.', '..')); // List files in the uploads directory
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File Upload</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>

<body>
<div class="container mt-4">
    <h1>Upload File</h1>
    <?php if ($message): ?>
        <div class="alert alert-info"><?php echo $message; ?></div>
    <?php endif; ?>
    <!-- Form Upload -->
    <form method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="fileInput" class="form-label">Pilih File</label>
            <input type="file" class="form-control" id="fileInput" name="files[]" multiple>
        </div>
        <button type="submit" class="btn btn-primary">Upload</button>
    </form>

    <!-- Table to display uploaded files -->
    <div class="table-container mt-4">
        <h3>Uploaded Files</h3>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Name</th>
                    <th>File Name</th>
                    <th>File Size</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($files)): ?>
                    <?php foreach ($files as $index => $file): ?>
                        <tr>
                            <td><?php echo $index + 1; ?></td>
                            <td><?php echo $file; ?></td>
                            <td><?php echo number_format(filesize($uploadDir . $file) / 1024, 2) . ' KB'; ?></td>
                            <td><?php echo date('d-m-Y H:i:s', filemtime($uploadDir . $file)); ?></td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <a href="<?php echo $uploadDir . $file; ?>" download class="btn btn-sm btn-success me-2">Download</a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center">No files uploaded yet.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Optional JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
