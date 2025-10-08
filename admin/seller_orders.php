<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';
require_once '../includes/seller_functions.php';

// Check if user is admin
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

$pageTitle = 'Seller Orders';

// Get filter parameters
$sellerId = isset($_GET['seller_id']) ? intval($_GET['seller_id']) : null;

// Get all sellers for filter dropdown
$sellers = getAllSellers();

// Build query to get orders containing seller products
$sql = "SELECT DISTINCT o.*, os.name as status_name, os.color as status_color,
        u.name as customer_name, u.email, u.phone,
        COUNT(DISTINCT oi.id) as total_items,
        SUM(CASE WHEN p.seller_id IS NOT NULL THEN 1 ELSE 0 END) as seller_items
        FROM orders o
        JOIN order_items oi ON o.id = oi.order_id
        JOIN products p ON oi.product_id = p.id
        JOIN order_statuses os ON o.order_status_id = os.id
        JOIN users u ON o.user_id = u.id
        WHERE p.seller_id IS NOT NULL";

$params = [];

if ($sellerId) {
    $sql .= " AND p.seller_id = ?";
    $params[] = $sellerId;
}

$sql .= " GROUP BY o.id ORDER BY o.created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - EverythingB2C</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="assets/css/admin.css" rel="stylesheet">
</head>
<body>
    <div class="everythingb2c-admin-container">
        <!-- Sidebar -->
        <?php include 'includes/sidebar.php'; ?>

        <!-- Main Content -->
        <div class="everythingb2c-main-content">
            <!-- Header -->
            <?php include 'includes/header.php'; ?>

            <!-- Page Content -->
            <div class="everythingb2c-dashboard-content">
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Seller Orders</h1>
    </div>

    <!-- Filters -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Filter by Seller</label>
                    <select name="seller_id" class="form-select">
                        <option value="">All Sellers</option>
                        <?php foreach ($sellers as $seller): ?>
                            <option value="<?php echo $seller['id']; ?>" <?php echo ($sellerId == $seller['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($seller['business_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">&nbsp;</label>
                    <div>
                        <button type="submit" class="btn btn-primary">Apply Filter</button>
                        <a href="seller_orders.php" class="btn btn-secondary">Clear</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Orders Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                Orders Containing Seller Products (<?php echo count($orders); ?>)
            </h6>
        </div>
        <div class="card-body">
            <?php if (empty($orders)): ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> No seller orders found.
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-bordered" id="ordersTable">
                        <thead>
                            <tr>
                                <th>Order #</th>
                                <th>Tracking ID</th>
                                <th>Customer</th>
                                <th>Items</th>
                                <th>Total Amount</th>
                                <th>Payment Method</th>
                                <th>Status</th>
                                <th>Order Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orders as $order): ?>
                            <tr>
                                <td><strong>#<?php echo $order['order_number']; ?></strong></td>
                                <td>
                                    <code><?php echo htmlspecialchars($order['tracking_id']); ?></code>
                                </td>
                                <td>
                                    <?php echo htmlspecialchars($order['customer_name']); ?>
                                    <br><small class="text-muted"><?php echo htmlspecialchars($order['email']); ?></small>
                                </td>
                                <td>
                                    <span class="badge bg-primary"><?php echo $order['seller_items']; ?> seller items</span>
                                    <br><small class="text-muted">of <?php echo $order['total_items']; ?> total</small>
                                </td>
                                <td>
                                    <strong>₹<?php echo number_format($order['total_amount'], 2); ?></strong>
                                </td>
                                <td>
                                    <?php 
                                    $paymentBadge = $order['payment_method'] === 'cod' ? 'bg-warning' : 'bg-info';
                                    ?>
                                    <span class="badge <?php echo $paymentBadge; ?>">
                                        <?php echo strtoupper($order['payment_method']); ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge" style="background-color: <?php echo $order['status_color']; ?>; color: white;">
                                        <?php echo htmlspecialchars($order['status_name']); ?>
                                    </span>
                                </td>
                                <td><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="orders.php?view=<?php echo $order['id']; ?>" 
                                           class="btn btn-info" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div><!-- /.container-fluid -->

<script>
$(document).ready(function() {
    $('#ordersTable').DataTable({
        order: [[7, 'desc']],
        pageLength: 25
    });
});
</script>

            </div><!-- /.everythingb2c-dashboard-content -->
        </div><!-- /.everythingb2c-main-content -->
    </div><!-- /.everythingb2c-admin-container -->

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="assets/js/admin.js"></script>
</body>
</html>
