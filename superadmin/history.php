<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>History</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <script type="module" src="https://cdn.jsdelivr.net/npm/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://cdn.jsdelivr.net/npm/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
    <link rel="stylesheet" href="css/history.css">
</head>

<body>
    <!-- Header -->
   <?php include 'navbar.php'; ?>

<div class="d-flex">
     <?php include 'Sidebar.php'; ?>

    <!-- Main Content -->
    <div class="container mt-4">
        <h1>History</h1>
        <table class="table table-bordered table-striped mt-4">
            <thead class="table-dark">
                <tr>
                    <th>No</th>
                    <th>Nama File</th>
                    <th>Tanggal Upload</th>
                    <th>Ukuran File</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <!-- Contoh data -->
                <tr>
                    <td>1</td>
                    <td>file1.pdf</td>
                    <td>2024-11-25</td>
                    <td>512 KB</td>
                    <td><button class="btn btn-danger btn-sm">Hapus</button></td>
                </tr>
                <tr>
                    <td>2</td>
                    <td>file2.jpg</td>
                    <td>2024-11-24</td>
                    <td>1.2 MB</td>
                    <td><button class="btn btn-danger btn-sm">Hapus</button></td>
                </tr>
            </tbody>
        </table>
    </div>

</body>

</html>