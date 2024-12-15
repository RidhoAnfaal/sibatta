<?php
// session_start();

// // Include the User class
// include_once 'User.php';
// include 'koneksi.php';

// // Create User object
// $user = new User($host, $database, $username, $password, $_SESSION);

// // Check if the user is logged in, if not redirect to login page
// if (!$user->checkLogin()) {
//     header('Location: index.php');
//     exit();
// }

// $username = $_SESSION['username'];

// // Ambil data pembayaran mahasiswa
// $sql = "SELECT s.student_id, p.total_amount, p.amount_paid, p.due_amount, p.payment_status, p.payment_date
//         FROM student s
//         JOIN payment p ON s.student_id = p.student_id
//         JOIN user u ON s.user_id = u.user_id
//         WHERE LOWER(u.username) = LOWER(?)";

// $params = array($username);
// $stmt = sqlsrv_query($conn, $sql, $params);

// if ($stmt === false) {
//     die("SQL Error: " . print_r(sqlsrv_errors(), true));
// }

// $paymentData = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

// if (!$paymentData) {
//     echo "<p>No payment data found for the user.</p>";
//     exit;
// }

// sqlsrv_free_stmt($stmt);
// ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <title>Bukti Pembayaran</title>
</head>
<body>
    <!-- Header -->
    <?php include 'navbar.php'; ?>

    <div class="container mt-5">
        <h3>Bukti Pembayaran</h3>
        <table class="table table-bordered">
            <tr>
                <th>Total Tagihan</th>
                <td>Rp <?php echo number_format($paymentData['total_amount'], 0, ',', '.'); ?></td>
            </tr>
            <tr>
                <th>Jumlah yang Sudah Dibayar</th>
                <td>Rp <?php echo number_format($paymentData['amount_paid'], 0, ',', '.'); ?></td>
            </tr>
            <tr>
                <th>Sisa Tagihan</th>
                <td>Rp <?php echo number_format($paymentData['due_amount'], 0, ',', '.'); ?></td>
            </tr>
            <tr>
                <th>Status Pembayaran</th>
                <td>
                    <?php
                    if ($paymentData['payment_status'] == 1) {
                        echo "<span class='badge bg-success'>Lunas</span>";
                    } else {
                        echo "<span class='badge bg-danger'>Belum Lunas</span>";
                    }
                    ?>
                </td>
            </tr>
            <tr>
                <th>Tanggal Pembayaran</th>
                <td><?php echo date("d-m-Y", strtotime($paymentData['payment_date'])); ?></td>
            </tr>
        </table>
        <a href="index.php" class="btn btn-primary">Kembali ke Dashboard</a>
    </div>

    <!-- Footer -->
    <?php include 'footer.php'; ?>
</body>
</html>
