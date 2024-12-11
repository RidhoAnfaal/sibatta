<?php
<<<<<<< HEAD
require('/sibatta/koneksi.php');
=======


require('koneksi.php');
>>>>>>> bf247e75bf67110728b17ddbec24ba616f299720

// Cek apakah tombol 'validate' diklik
if (isset($_POST['validate'])) {
    $document_id = $_POST['document_id'];
    $validated_by = 'Admin'; // Ganti dengan nama pengguna jika diperlukan

    // Update query
    $query = "UPDATE [sibatta].[sibatta].[document] SET [validated_by] = ? WHERE [document_id] = ?";
    $params = [$validated_by, $document_id];
    $result = sqlsrv_query($conn, $query, $params);

    if ($result) {
        echo "<script>alert('Document validated successfully!');</script>";
        header("Location: Tugas_akhir.php");
        exit;
    } else {
        echo "<script>alert('Failed to validate document.');</script>";
    }
}

// Cek apakah ada parameter pencarian
$search = isset($_GET['search']) ? $_GET['search'] : '';
$query = "SELECT [document_id], [user_id], [title], [uploaded_at], [validated_by], [username], [file_path] 
          FROM [sibatta].[sibatta].[document]";

// Tambahkan filter pencarian jika ada
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
    <link rel="stylesheet" href="css/TA.css">
</head>

<body>
    <!-- Header -->
    <?php include 'navbar.php'; ?>

    <?php include 'Sidebar.php'; ?>

    <!-- Main Content -->
    <div class="container mt-4">
        <form method="GET" class="mb-3">
            <div class="input-group">
                <input type="text" class="form-control" name="search" placeholder="Search by username, title, or document ID" 
                       value="<?php echo htmlspecialchars($search); ?>">
                <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Search</button>
            </div>
        </form>

        <!-- Overlay -->
        <div id="overlay"></div>

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
                        echo "<td>{$tampil['validated_by']}</td>";

                        // Validation button
                        echo "<td>
                                  <form method='POST' action='Tugas_akhir.php'>
                                      <input type='hidden' name='document_id' value='{$tampil['document_id']}'>
                                      <button type='submit' name='validate' class='btn btn-success'>Validate</button>
                                  </form>
                              </td>";

                        // Direct download button
                        if (file_exists('../Student/uploads/' . $tampil['file_path'])) {
                            echo "<td><a href='../Student/uploads/{$tampil['file_path']}' class='btn btn-primary' download>Download</a></td>";
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

    <!-- Optional JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('overlay');
        const sidebarToggle = document.getElementById('sidebarToggle');

        sidebarToggle.addEventListener('click', () => {
            sidebar.classList.toggle('active');
            overlay.classList.toggle('active');
        });

        overlay.addEventListener('click', () => {
            sidebar.classList.remove('active');
            overlay.classList.remove('active');
        });
    </script>
</body>

</html>
