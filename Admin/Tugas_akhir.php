<?php
require('koneksi.php');
session_start(); // Start session to access session variables

// Handle Validate button
if (isset($_POST['validate'])) {
    $document_id = $_POST['document_id'];
    $document_status = 'Admin'; // Ganti dengan nama pengguna jika diperlukan

    // Update query
    $query = "UPDATE [sibatta].[sibatta].[document] SET [document_status] = ? WHERE [document_id] = ?";
    $params = [$document_status, $document_id];
    $result = sqlsrv_query($conn, $query, $params);

    if ($result) {
        echo "<script>alert('Document validated successfully!');</script>";
        header("Location: Tugas_akhir.php");
        exit;
    } else {
        echo "<script>alert('Failed to validate document.');</script>";
    }
}

// Handle Reject button
if (isset($_POST['reject'])) {
    $document_id = $_POST['document_id'];
    $document_status = 'Rejected'; // Status baru untuk dokumen yang ditolak

    // Update query
    $query = "UPDATE [sibatta].[sibatta].[document] SET [document_status] = ? WHERE [document_id] = ?";
    $params = [$document_status, $document_id];
    $result = sqlsrv_query($conn, $query, $params);

    if ($result) {
        echo "<script>alert('Document rejected successfully!');</script>";
        header("Location: Tugas_akhir.php");
        exit;
    } else {
        echo "<script>alert('Failed to reject document.');</script>";
    }
}

// Check for search parameter
$search = isset($_GET['search']) ? $_GET['search'] : '';
$query = "SELECT [document_id], [user_id], [title], [uploaded_at], [document_status], [username], [file_path] 
          FROM [sibatta].[sibatta].[document]";

// Add search filter if present
if (!empty($search)) {
    $query .= " WHERE [username] LIKE ? OR [title] LIKE ? OR CAST([document_id] AS NVARCHAR) LIKE ?";
    $params = ["%$search%", "%$search%", "%$search%"];
    $viewdata = sqlsrv_query($conn, $query, $params);
} else {
    $viewdata = sqlsrv_query($conn, $query);
}

// Debugging query SQL
if ($viewdata === false) {
    die(print_r(sqlsrv_errors(), true));
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Approve</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <script type="module" src="https://cdn.jsdelivr.net/npm/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://cdn.jsdelivr.net/npm/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="css/TA.css">
</head>

<body>
    <!-- Header -->
    <?php
    $username = $_SESSION['username']; // Get the username from session
    include 'navbar.php';
    ?>

    <div class="d-flex">
        <?php include 'Sidebar.php'; ?>
        <div class="container mt-4">
            <!-- Search Bar -->
            <form method="GET" class="mb-3">
                <div class="input-group ms-auto" style="max-width: 300px;">
                    <input type="text" class="form-control" name="search" placeholder="Search" value="<?php echo htmlspecialchars($search); ?>">
                    <button class="btn btn-outline-success" type="submit">Search</button>
                </div>
            </form>

            <div class="container mt-4">
                <h3>Documents List</h3>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Username</th>
                            <th>Document ID</th>
                            <th>NIM</th>
                            <th>Title</th>
                            <th>Uploaded</th>
                            <th>Validated</th>
                            <th>Action</th>
                            <th>Download</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Fetch and display each row of data
                        $no = 1;
                        while ($tampil = sqlsrv_fetch_array($viewdata, SQLSRV_FETCH_ASSOC)) {
                            echo "<tr>";
                            echo "<td>{$no}</td>";
                            echo "<td>{$tampil['username']}</td>";
                            echo "<td>{$tampil['document_id']}</td>";
                            echo "<td>{$tampil['user_id']}</td>";
                            echo "<td>{$tampil['title']}</td>";
                            echo "<td>" . date_format($tampil['uploaded_at'], 'Y-m-d') . "</td>";
                            echo "<td>{$tampil['document_status']}</td>";

                            // Validation and rejection buttons
                            echo "<td>
                                   <form method='POST' action='Tugas_akhir.php' class='d-inline'>
                                       <input type='hidden' name='document_id' value='{$tampil['document_id']}'>
                                       <button type='submit' name='validate' class='btn btn-success'>Validate</button>
                                   </form>
                                   <form method='POST' action='Tugas_akhir.php' class='d-inline'>
                                       <input type='hidden' name='document_id' value='{$tampil['document_id']}'>
                                       <button type='submit' name='reject' class='btn btn-danger'>Reject</button>
                                   </form>
                               </td>";

                            // File download button
                            $file_path = '../Student/uploads/' . basename($tampil['file_path']);
                            if (file_exists($file_path)) {
                                echo "<td><a href='$file_path' class='btn btn-primary' download>Download</a></td>";
                            } else {
                                echo "<td>File not found</td>";
                            }

                            echo "</tr>";
                            $no++;
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php include 'footer.php'; ?>
</body>

</html>
