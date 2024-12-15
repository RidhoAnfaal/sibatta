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

// // Ambil data pembayaran dari database
// $sql = "SELECT 
//             p.payment_status,
//             p.total_amount,
//             p.amount_paid,
//             p.due_amount,
//             p.payment_date
//         FROM payment p
//         JOIN student s ON p.student_id = s.student_id
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
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <title>Status Pembayaran</title>
</head>

<body>
    <!-- Header -->
    <?php include 'navbar.php'; ?>

    <div class="d-flex flex-column min-vh-100">
        <?php include 'Sidebar.php'; ?>

        <!-- Main Content -->
        <div class="container flex-grow-1">
            <div class="row mt-4">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3>Status Pembayaran Anda</h3>
                        </div>
                        <div class="card-body">
                            <h5>Informasi Pembayaran</h5>
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
                                    <th>Tanggal Pembayaran Terakhir</th>
                                    <td><?php echo $paymentData['payment_date'] ? date("d-m-Y", strtotime($paymentData['payment_date'])) : '-'; ?></td>
                                </tr>
                            </table>

                            <?php if ($paymentData['due_amount'] > 0) { ?>
                                <a href="pay_now.php" class="btn btn-primary">Bayar Sekarang</a>
                            <?php } else { ?>
                                <a href="receipt.php" class="btn btn-success">Lihat Bukti Pembayaran</a>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
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
    </div>

    <!-- Optional JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
