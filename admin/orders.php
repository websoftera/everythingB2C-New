<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

$pageTitle = 'Order Management';
$success_message = '';
$error_message = '';

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $orderId = intval($_POST['order_id']);
    $statusId = intval($_POST['status_id']);
    $description = sanitizeInput($_POST['status_description']);
    $externalTrackingId = sanitizeInput($_POST['external_tracking_id']);
    $externalTrackingLink = sanitizeInput($_POST['external_tracking_link']);
    $estimatedDeliveryDate = !empty($_POST['estimated_delivery_date']) ? $_POST['estimated_delivery_date'] : null;
    $updatePaymentStatus = false;
    if (isset($_POST['payment_status'])) {
        $paymentStatus = $_POST['payment_status'];
        $updatePaymentStatus = true;
    }
    if (updateOrderStatus($orderId, $statusId, $description, $externalTrackingId, $externalTrackingLink, $estimatedDeliveryDate)) {
        if ($updatePaymentStatus) {
            $order = getOrderById($orderId);
            if ($order && ($order['payment_method'] === 'cod' || $order['payment_method'] === 'direct_payment')) {
                updatePaymentStatus($orderId, $paymentStatus);
            }
        }
        $_SESSION['success_message'] = 'Order status updated successfully!';
    } else {
        $_SESSION['error_message'] = 'Error updating order status.';
    }
    header('Location: orders.php');
    exit;
}

// Get filter parameters
$status_filter = $_GET['status'] ?? '';
$payment_filter = $_GET['payment'] ?? '';
$date_filter = $_GET['date'] ?? '';
$search = $_GET['search'] ?? '';

// Build query
$where_conditions = [];
$params = [];

if ($status_filter) {
    $where_conditions[] = "o.order_status_id = ?";
    $params[] = $status_filter;
}

if ($payment_filter) {
    $where_conditions[] = "o.payment_status = ?";
    $params[] = $payment_filter;
}

if ($date_filter) {
    $where_conditions[] = "DATE(o.created_at) = ?";
    $params[] = $date_filter;
}

if ($search) {
    $where_conditions[] = "(o.tracking_id LIKE ? OR o.id LIKE ? OR u.name LIKE ? OR u.email LIKE ?)";
    $search_param = "%$search%";
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
}

$where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

// Get orders with pagination
$page = max(1, intval($_GET['page'] ?? 1));
$per_page = 20;
$offset = ($page - 1) * $per_page;

$sql = "SELECT o.*, os.name as status_name, os.color as status_color, 
               u.name as customer_name, u.email as customer_email,
               a.name as address_name, a.phone as address_phone
        FROM orders o 
        LEFT JOIN order_statuses os ON o.order_status_id = os.id
        LEFT JOIN users u ON o.user_id = u.id
        LEFT JOIN addresses a ON o.address_id = a.id
        $where_clause
        ORDER BY o.created_at DESC
        LIMIT $per_page OFFSET $offset";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get total count for pagination
$count_sql = "SELECT COUNT(*) FROM orders o 
              LEFT JOIN users u ON o.user_id = u.id
              $where_clause";
$stmt = $pdo->prepare($count_sql);
$stmt->execute($params);
$total_orders = $stmt->fetchColumn();
$total_pages = ceil($total_orders / $per_page);

// Get all statuses for filter
$statuses = getAllOrderStatuses();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - EverythingB2C</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/admin.css" rel="stylesheet">
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <?php include 'includes/sidebar.php'; ?>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Header -->
            <?php include 'includes/header.php'; ?>

            <!-- Orders Content -->
            <div class="dashboard-content">
                <div class="container-fluid">
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center">
                                <h1 class="h3 mb-0">Order Management</h1>
                                <div>
                                    <a href="order_statuses.php" class="btn btn-outline-primary">
                                        <i class="fas fa-cog"></i> Manage Statuses
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php if ($success_message): ?>
                        <div class="alert alert-success"><?php echo htmlspecialchars($success_message); ?></div>
                    <?php endif; ?>

                    <?php if ($error_message): ?>
                        <div class="alert alert-danger"><?php echo htmlspecialchars($error_message); ?></div>
                    <?php endif; ?>

                    <!-- Filters -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <form method="GET" class="row g-3">
                                <div class="col-md-2">
                                    <label class="form-label">Status</label>
                                    <select name="status" class="form-control">
                                        <option value="">All Statuses</option>
                                        <?php foreach ($statuses as $status): ?>
                                            <option value="<?php echo $status['id']; ?>" <?php echo $status_filter == $status['id'] ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($status['name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Payment</label>
                                    <select name="payment" class="form-control">
                                        <option value="">All Payments</option>
                                        <option value="pending" <?php echo $payment_filter == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                        <option value="paid" <?php echo $payment_filter == 'paid' ? 'selected' : ''; ?>>Paid</option>
                                        <option value="failed" <?php echo $payment_filter == 'failed' ? 'selected' : ''; ?>>Failed</option>
                                        <option value="refunded" <?php echo $payment_filter == 'refunded' ? 'selected' : ''; ?>>Refunded</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Date</label>
                                    <input type="date" name="date" class="form-control" value="<?php echo htmlspecialchars($date_filter); ?>">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Search</label>
                                    <input type="text" name="search" class="form-control" placeholder="Order ID, Tracking ID, Customer..." value="<?php echo htmlspecialchars($search); ?>">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">&nbsp;</label>
                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-search"></i> Filter
                                        </button>
                                        <a href="orders.php" class="btn btn-outline-secondary">
                                            <i class="fas fa-times"></i> Clear
                                        </a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Orders Table -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Orders (<?php echo $total_orders; ?> total)</h5>
                        </div>
                        <div class="card-body">
                            <?php if (empty($orders)): ?>
                                <div class="text-center py-4">
                                    <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                                    <h5>No orders found</h5>
                                    <p class="text-muted">Try adjusting your filters or search criteria.</p>
                                </div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Order ID</th>
                                                <th>Tracking ID</th>
                                                <th>Customer</th>
                                                <th>Amount</th>
                                                <th>Status</th>
                                                <th>Payment</th>
                                                <th>Date</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($orders as $order): ?>
                                                <tr>
                                                    <td>
                                                        <strong>#<?php echo $order['id']; ?></strong>
                                                        <?php if ($order['is_business_purchase']): ?>
                                                            <span class="badge bg-info">Business</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <a href="../track_order.php?tracking_id=<?php echo $order['tracking_id']; ?>" target="_blank" class="text-primary">
                                                            <?php echo $order['tracking_id']; ?>
                                                        </a>
                                                    </td>
                                                    <td>
                                                        <div>
                                                            <strong><?php echo htmlspecialchars($order['customer_name']); ?></strong>
                                                            <br><small class="text-muted"><?php echo htmlspecialchars($order['customer_email']); ?></small>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <strong>₹<?php echo number_format($order['total_amount'], 2); ?></strong>
                                                        <br><small class="text-muted"><?php echo strtoupper($order['payment_method']); ?></small>
                                                    </td>
                                                    <td>
                                                        <span class="badge" style="background-color: <?php echo $order['status_color']; ?>">
                                                            <?php echo $order['status_name']; ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <?php
                                                        $payment_badge_class = match($order['payment_status']) {
                                                            'paid' => 'bg-success',
                                                            'pending' => 'bg-warning',
                                                            'failed' => 'bg-danger',
                                                            'refunded' => 'bg-info',
                                                            default => 'bg-secondary'
                                                        };
                                                        ?>
                                                        <span class="badge <?php echo $payment_badge_class; ?>">
                                                            <?php echo ucfirst($order['payment_status']); ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <?php echo date('M j, Y', strtotime($order['created_at'])); ?>
                                                        <br><small class="text-muted"><?php echo date('g:i A', strtotime($order['created_at'])); ?></small>
                                                    </td>
                                                    <td>
                                                        <div class="btn-group" role="group">
                                                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="viewOrder(<?php echo $order['id']; ?>)">
                                                                <i class="fas fa-eye"></i>
                                                            </button>
                                                            <button type="button" class="btn btn-sm btn-outline-success" onclick="updateStatus(<?php echo $order['id']; ?>)">
                                                                <i class="fas fa-edit"></i>
                                                            </button>
                                                            <a href="../track_order.php?tracking_id=<?php echo $order['tracking_id']; ?>" target="_blank" class="btn btn-sm btn-outline-info">
                                                                <i class="fas fa-truck"></i>
                                                            </a>
                                                            <a href="../download_invoice.php?order_id=<?php echo $order['id']; ?>" target="_blank" class="btn btn-sm btn-outline-warning" title="Download Invoice">
                                                                <i class="fas fa-file-invoice"></i>
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Pagination -->
                                <?php if ($total_pages > 1): ?>
                                    <nav aria-label="Orders pagination">
                                        <ul class="pagination justify-content-center">
                                            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                                <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                                    <a class="page-link" href="?page=<?php echo $i; ?>&status=<?php echo $status_filter; ?>&payment=<?php echo $payment_filter; ?>&date=<?php echo $date_filter; ?>&search=<?php echo urlencode($search); ?>">
                                                        <?php echo $i; ?>
                                                    </a>
                                                </li>
                                            <?php endfor; ?>
                                        </ul>
                                    </nav>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Update Status Modal -->
            <div class="modal fade" id="updateStatusModal" tabindex="-1">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Update Order Status</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <form method="POST">
                            <input type="hidden" name="update_status" value="1">
                            <input type="hidden" name="order_id" id="update_order_id">
                            <div class="modal-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Order Status *</label>
                                        <select name="status_id" class="form-control" required>
                                            <?php foreach ($statuses as $status): ?>
                                                <option value="<?php echo $status['id']; ?>">
                                                    <?php echo htmlspecialchars($status['name']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Estimated Delivery Date</label>
                                        <input type="date" name="estimated_delivery_date" class="form-control">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">External Tracking ID</label>
                                        <input type="text" name="external_tracking_id" class="form-control" placeholder="Courier tracking ID">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">External Tracking Link</label>
                                        <input type="url" name="external_tracking_link" class="form-control" placeholder="https://courier.com/track/...">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">Status Description</label>
                                        <textarea name="status_description" class="form-control" rows="3" placeholder="Additional details about this status update..."></textarea>
                                    </div>
                                    <!-- Payment Status Update for COD -->
                                    <div class="col-12" id="paymentStatusSection" style="display:none;">
                                        <label class="form-label">Payment Status (COD & Direct Payment only)</label>
                                        <select name="payment_status" class="form-control">
                                            <option value="pending">Pending</option>
                                            <option value="paid">Paid</option>
                                            <option value="unpaid">Unpaid</option>
                                            <option value="failed">Failed</option>
                                            <option value="refunded">Refunded</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary">Update Status</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- View Order Modal -->
            <div class="modal fade" id="viewOrderModal" tabindex="-1">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Order Details</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body" id="orderDetailsContent">
                            <!-- Order details will be loaded here -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include 'includes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/admin.js"></script>
    <script>
    // Show payment status dropdown only for COD orders
    function updateStatus(orderId) {
        document.getElementById('update_order_id').value = orderId;
        // Fetch order details to check payment method
        fetch('ajax/get_order_details.php?id=' + orderId + '&json=1')
            .then(response => response.json())
            .then(data => {
                if (data.success && data.order) {
                    const paymentStatusSection = document.getElementById('paymentStatusSection');
                    if (data.order.payment_method === 'cod' || data.order.payment_method === 'direct_payment') {
                        paymentStatusSection.style.display = '';
                    } else {
                        paymentStatusSection.style.display = 'none';
                    }
                } else {
                    paymentStatusSection.style.display = 'none';
                }
                const modal = new bootstrap.Modal(document.getElementById('updateStatusModal'));
                modal.show();
            })
            .catch(() => {
                const paymentStatusSection = document.getElementById('paymentStatusSection');
                paymentStatusSection.style.display = 'none';
                const modal = new bootstrap.Modal(document.getElementById('updateStatusModal'));
                modal.show();
            });
    }

    function viewOrder(orderId) {
        // Load order details via AJAX
        fetch(`ajax/get_order_details.php?id=${orderId}`)
            .then(response => response.text())
            .then(html => {
                document.getElementById('orderDetailsContent').innerHTML = html;
                const modal = new bootstrap.Modal(document.getElementById('viewOrderModal'));
                modal.show();
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error loading order details');
            });
    }
    </script>
</body>
</html> 