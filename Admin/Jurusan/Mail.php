<?php
session_start();

// Check if the user is logged in
// if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
//     header('Location: Mail.php');
//     exit;
// }

require_once '../koneksi.php';

$searchKeyword = '';
$paymentData = [];

// Handle search functionality
if (isset($_POST['search']) && !empty($_POST['search'])) {
    $searchKeyword = '%' . $_POST['search'] . '%';  // Default search behavior

    // Check for specific search terms (lunas or belum lunas)
    if ($_POST['search'] == 'lunas') {
        // Query for payments with "Lunas" status (1)
        $query = "SELECT * FROM [sibatta].[sibatta].[ukt_payment] WHERE payment_status = ?";
        $params = array(1);
    } else if ($_POST['search'] == 'belum lunas') {
        // Query for payments with "Belum Lunas" status (0)
        $query = "SELECT * FROM [sibatta].[sibatta].[ukt_payment] WHERE payment_status = ?";
        $params = array(0);
    } else {
        // Default query for searching by student_id
        $query = "SELECT * FROM [sibatta].[sibatta].[ukt_payment] WHERE student_id LIKE ?";
        $params = array($searchKeyword);
    }

    // Prepare and execute the query
    $stmt = sqlsrv_prepare($conn, $query, $params);
    if (!$stmt) {
        die("Statement preparation failed: " . print_r(sqlsrv_errors(), true));
    }

    $result = sqlsrv_execute($stmt);
    if ($result === false) {
        die("Query execution failed: " . print_r(sqlsrv_errors(), true));
    }

    // Fetch the data
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        $paymentData[] = $row;
    }

    sqlsrv_free_stmt($stmt);
} else {
    // Default: Show logged-in user's data
    $query = "SELECT * FROM [sibatta].[sibatta].[ukt_payment] WHERE student_id = ?";
    $params = array($_SESSION['user_id']);

    $stmt = sqlsrv_prepare($conn, $query, $params);
    if (!$stmt) {
        die("Statement preparation failed: " . print_r(sqlsrv_errors(), true));
    }

    $result = sqlsrv_execute($stmt);
    if ($result === false) {
        die("Query execution failed: " . print_r(sqlsrv_errors(), true));
    }

    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        $paymentData[] = $row;
    }

    sqlsrv_free_stmt($stmt);
}

// Close the connection
sqlsrv_close($conn);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title class="judul">UKT Payment Status</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/Mail.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
</head>

<body>
    <?php
    $username = $_SESSION['username']; // Get the username from session
    include 'navbar.php';
    ?>

    <div class="d-flex flex-column min-vh-100">
        <?php include 'sidebar.php'; ?>

        <div class="container">
            <div class="status-card">
                <div class="status-header">
                    <h2>UKT Payment Status</h2>
                </div>
                <div class="p-3">
                    <form method="POST" action="" class="search-container">
                        <input type="text" name="search" class="form-control me-2" placeholder="Search by Student ID or Status" value="<?php echo isset($_POST['search']) ? htmlspecialchars($_POST['search']) : ''; ?>">
                        <button type="submit" class="btn btn-success">Search</button>
                    </form>
                </div>
                <div class="status-body">
                    <?php if (!empty($paymentData)): ?>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Payment ID</th>
                                    <th>Student ID</th>
                                    <th>Payment Date</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($paymentData as $payment): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($payment['payment_id']); ?></td>
                                        <td><?php echo htmlspecialchars($payment['student_id']); ?></td>
                                        <td>
                                            <?php
                                            // Ensure payment_date is valid before formatting
                                            if ($payment['payment_date'] instanceof DateTime) {
                                                echo htmlspecialchars($payment['payment_date']->format('Y-m-d'));
                                            } else {
                                                echo "Invalid Date";
                                            }
                                            ?>
                                        </td>
                                        <td>Rp <?php echo number_format($payment['amount'], 2, ',', '.'); ?></td>
                                        <td>
                                            <?php
                                            // Display payment status with appropriate badge
                                            if ($payment['payment_status'] == 1) {
                                                echo "<span class='badge bg-success'>Lunas</span>";
                                            } else {
                                                echo "<span class='badge bg-danger'>Belum Lunas</span>";
                                            }
                                            ?>
                                        </td>
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

        <?php include 'footer.php'; ?>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    </div>
</body>

</html>