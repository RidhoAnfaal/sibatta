<?php
require('koneksi.php');

// Check if the 'validate' button was clicked
if (isset($_POST['validate'])) {
    $document_id = $_POST['document_id'];

    // Update the 'validated_by' field for the selected document
    $validated_by = 'Admin'; // You can replace 'Admin' with the logged-in user's username or ID
    $query = "UPDATE [sibatta].[sibatta].[document] SET [validated_by] = ? WHERE [document_id] = ?";
    $params = array($validated_by, $document_id);

    // Execute the update query
    $result = sqlsrv_query($conn, $query, $params);

    if ($result) {
        echo "<script>alert('Document validated successfully!');</script>";
        // Reload the page to reflect the changes
        header("Location: Tugas_akhir.php");
    } else {
        echo "<script>alert('Failed to validate document.');</script>";
    }
}

// Fetch documents to display in the table
$query = "SELECT TOP (1000) [document_id], [user_id], [title], [uploaded_at], [validated_by], [username] FROM [sibatta].[sibatta].[document]";
$viewdata = sqlsrv_query($conn, $query);
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

<div class="d-flex">
     <?php include 'Sidebar.php'; ?>

 <!-- Main Content -->
 <div class="container mt-4">
 
    <!-- Overlay -->
    <div id="overlay"></div>

    <!-- Main Content -->
    <div class="container mt-4">
        <h3>Documents List</h3>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Document ID</th>
                    <th>User ID</th>
                    <th>Title</th>
                    <th>Uploaded At</th>
                    <th>Validated By</th>
                    <th>Username</th>
                </tr>
                <tbody>
    <?php
    // Fetch and display each row of data
    $no = 1;
    while ($tampil = sqlsrv_fetch_array($viewdata, SQLSRV_FETCH_ASSOC)) {
        echo "<tr>";
        echo "<td>{$no}</td>";
        echo "<td>{$tampil['document_id']}</td>";
        echo "<td>{$tampil['user_id']}</td>";
        echo "<td>{$tampil['title']}</td>";
        echo "<td>" . date_format($tampil['uploaded_at'], 'Y-m-d H:i:s') . "</td>";
        echo "<td>{$tampil['validated_by']}</td>";
        echo "<td>{$tampil['username']}</td>";
        // Add a button for validation
        echo "<td><form method='POST' action='Tugas_akhir.php'>
                  <input type='hidden' name='document_id' value='{$tampil['document_id']}'>
                  <button type='submit' name='validate' class='btn btn-success'>Validate</button>
              </form></td>";
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

        // Handle sidebar toggle
        sidebarToggle.addEventListener('click', () => {
            sidebar.classList.toggle('active');
            overlay.classList.toggle('active');
        });

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

