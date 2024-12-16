<?php
// // mail.php
// session_start();

// // Check if the user is logged in
// if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
//     header('Location: login.php');
//     exit;
// }

// require_once 'config.php';

// $searchKeyword = '';
// $paymentData = [];

// // Handle search functionality
// if (isset($_POST['search'])) {
//     $searchKeyword = '%' . $_POST['search'] . '%';
//     $query = "SELECT * FROM ukt_payments WHERE student_name LIKE ? OR student_id LIKE ?";
//     $stmt = $conn->prepare($query);
//     $stmt->bind_param("ss", $searchKeyword, $searchKeyword);
//     $stmt->execute();
//     $result = $stmt->get_result();
//     $paymentData = $result->fetch_all(MYSQLI_ASSOC);
//     $stmt->close();
// } else {
//     // Default: Show logged-in user's data
//     $query = "SELECT * FROM ukt_payments WHERE student_id = ?";
//     $stmt = $conn->prepare($query);
//     $stmt->bind_param("i", $_SESSION['id']);
//     $stmt->execute();
//     $result = $stmt->get_result();
//     $paymentData[] = $result->fetch_assoc();
//     $stmt->close();
// }

// $conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UKT Payment Status</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="Mail.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
</head>

<body>
<?php include 'navbar.php'; ?>

<div class="d-flex flex-column min-vh-100">
    <?php include 'Sidebar.php'; ?>

    <div class="container">
        <div class="status-card">
            <div class="status-header">
                <h2>UKT Payment Status</h2>
            </div>
            <div class="p-3">
                <form method="POST" action="" class="search-container">
                    <input type="text" name="search" class="form-control me-2" placeholder="Search by Student Name or ID" value="<?php echo isset($_POST['search']) ? htmlspecialchars($_POST['search']) : ''; ?>">
                    <button type="submit" class="btn btn-success">Search</button>
                </form>
            </div>
            <div class="status-body">
                <?php if (!empty($paymentData)): ?>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Student ID</th>
                                <th>Student Name</th>
                                <th>Status</th>
                                <th>Amount Paid</th>
                                <th>Payment Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($paymentData as $payment): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($payment['student_id']); ?></td>
                                    <td><?php echo htmlspecialchars($payment['student_name']); ?></td>
                                    <td><?php echo htmlspecialchars($payment['status']); ?></td>
                                    <td>Rp <?php echo number_format($payment['amount'], 2, ',', '.'); ?></td>
                                    <td><?php echo htmlspecialchars($payment['payment_date']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>No payment records found.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php include 'footer.php';?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
