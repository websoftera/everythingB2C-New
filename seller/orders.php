<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';
require_once '../includes/seller_functions.php';

if (!isset($_SESSION['seller_id'])) {
    header('Location: login.php');
    exit;
}

$sellerId = $_SESSION['seller_id'];
$pageTitle = 'My Orders';

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $orderId = intval($_POST['order_id']);
    $statusId = intval($_POST['status_id']);
    $description = trim($_POST['status_description'] ?? '');
    $externalTrackingId = trim($_POST['external_tracking_id'] ?? '');
    $externalTrackingLink = trim($_POST['external_tracking_link'] ?? '');
    $estimatedDeliveryDate = !empty($_POST['estimated_delivery_date']) ? $_POST['estimated_delivery_date'] : null;
    
    // SECURITY: Verify order contains seller's products
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM order_items oi 
                           JOIN products p ON oi.product_id = p.id 
                           WHERE oi.order_id = ? AND p.seller_id = ?");
    $stmt->execute([$orderId, $sellerId]);
    
    if ($stmt->fetchColumn() > 0) {
        if (updateOrderStatus($orderId, $statusId, $description, $externalTrackingId, $externalTrackingLink, $estimatedDeliveryDate)) {
            $_SESSION['success_message'] = 'Order status updated successfully!';
            logSellerActivity($sellerId, 'order_status_updated', "Updated status for order #{$orderId}");
        } else {
            $_SESSION['error_message'] = 'Error updating order status.';
        }
    } else {
        $_SESSION['error_message'] = 'Access denied.';
    }
    
    header('Location: orders.php');
    exit;
}

$pageTitle = 'My Orders';

// Get filter parameters
$status_filter = $_GET['status'] ?? '';
$payment_filter = $_GET['payment'] ?? '';
$date_filter = $_GET['date'] ?? '';
$search = $_GET['search'] ?? '';

// Build filters array
$filters = [];
if ($status_filter) $filters['status'] = $status_filter;
if ($date_filter) {
    switch ($date_filter) {
        case 'today':
            $filters['date_from'] = date('Y-m-d');
            $filters['date_to'] = date('Y-m-d');
            break;
        case 'week':
            $filters['date_from'] = date('Y-m-d', strtotime('-7 days'));
            $filters['date_to'] = date('Y-m-d');
            break;
        case 'month':
            $filters['date_from'] = date('Y-m-01');
            $filters['date_to'] = date('Y-m-d');
            break;
    }
}

// Get orders - SECURITY: Only orders with seller's products
$orders = getSellerOrders($sellerId, $filters);

// Apply additional filters
if ($search || $payment_filter) {
    $orders = array_filter($orders, function($order) use ($search, $payment_filter) {
        $searchMatch = true;
        $paymentMatch = true;
        
        if ($search) {
            $searchMatch = stripos($order['order_number'] ?? '', $search) !== false ||
                          stripos($order['tracking_id'] ?? '', $search) !== false ||
                          stripos($order['customer_name'] ?? '', $search) !== false ||
                          stripos($order['email'] ?? '', $search) !== false;
        }
        
        if ($payment_filter) {
            $paymentMatch = ($order['payment_method'] === $payment_filter);
        }
        
        return $searchMatch && $paymentMatch;
    });
}

// Get all order statuses for filter
$orderStatuses = $pdo->query("SELECT * FROM order_statuses ORDER BY sort_order")->fetchAll(PDO::FETCH_ASSOC);
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
    <link href="../admin/assets/css/admin.css" rel="stylesheet">
</head>
<body>
    <div class="everythingb2c-admin-container">
        <?php include 'includes/sidebar.php'; ?>
        <div class="everythingb2c-main-content">
            <?php include 'includes/header.php'; ?>
            <div class="everythingb2c-dashboard-content">
                <div class="container-fluid">
                    <h1 class="h3 mb-4">My Orders</h1>

                    <?php if (isset($_SESSION['success_message'])): ?>
                        <div class="alert alert-success alert-dismissible fade show">
                            <?php echo htmlspecialchars($_SESSION['success_message']); unset($_SESSION['success_message']); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['error_message'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show">
                            <?php echo htmlspecialchars($_SESSION['error_message']); unset($_SESSION['error_message']); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <!-- Filters -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <form method="GET" class="row g-3">
                                <div class="col-md-3">
                                    <input type="text" class="form-control" name="search" 
                                           placeholder="Search orders..." value="<?php echo htmlspecialchars($search); ?>">
                                    <small class="text-muted">Order #, Tracking ID, Customer</small>
                                </div>
                                <div class="col-md-2">
                                    <select class="form-control" name="status">
                                        <option value="">All Status</option>
                                        <?php foreach ($orderStatuses as $status): ?>
                                            <option value="<?php echo $status['id']; ?>" <?php echo $status_filter == $status['id'] ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($status['name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <select class="form-control" name="payment">
                                        <option value="">All Payment</option>
                                        <option value="cod" <?php echo $payment_filter === 'cod' ? 'selected' : ''; ?>>Cash on Delivery</option>
                                        <option value="razorpay" <?php echo $payment_filter === 'razorpay' ? 'selected' : ''; ?>>Razorpay</option>
                                        <option value="direct_payment" <?php echo $payment_filter === 'direct_payment' ? 'selected' : ''; ?>>Direct Payment</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <select class="form-control" name="date">
                                        <option value="">All Time</option>
                                        <option value="today" <?php echo $date_filter === 'today' ? 'selected' : ''; ?>>Today</option>
                                        <option value="week" <?php echo $date_filter === 'week' ? 'selected' : ''; ?>>Last 7 Days</option>
                                        <option value="month" <?php echo $date_filter === 'month' ? 'selected' : ''; ?>>This Month</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <button type="submit" class="btn btn-primary me-2">
                                        <i class="fas fa-search"></i> Filter
                                    </button>
                                    <a href="orders.php" class="btn btn-secondary">
                                        <i class="fas fa-times"></i> Clear
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Orders Table -->
                    <div class="card shadow">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Orders (<?php echo count($orders); ?>)</h5>
                            <a href="export_orders.php?<?php echo http_build_query($_GET); ?>" class="btn btn-success btn-sm">
                                <i class="fas fa-download"></i> Export CSV
                            </a>
                        </div>
                        <div class="card-body">
                            <?php if (empty($orders)): ?>
                                <div class="text-center py-4">
                                    <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                                    <h5>No orders found</h5>
                                    <p class="text-muted">
                                        <?php echo $search || $status_filter || $payment_filter || $date_filter ? 
                                            'Try adjusting your filters.' : 
                                            'You haven\'t received any orders yet.'; ?>
                                    </p>
                                </div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-hover" id="ordersTable">
                                        <thead>
                                            <tr>
                                                <th>Order #</th>
                                                <th>Tracking ID</th>
                                                <th>Customer</th>
                                                <th>Contact</th>
                                                <th>Total Amount</th>
                                                <th>Payment</th>
                                                <th>Status</th>
                                                <th>Order Date</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($orders as $order): ?>
                                            <tr>
                                                <td><strong>#<?php echo htmlspecialchars($order['order_number'] ?? $order['id']); ?></strong></td>
                                                <td><code style="font-size:11px;"><?php echo htmlspecialchars($order['tracking_id']); ?></code></td>
                                                <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
                                                <td>
                                                    <small>
                                                        <i class="fas fa-envelope"></i> <?php echo htmlspecialchars($order['email']); ?><br>
                                                        <i class="fas fa-phone"></i> <?php echo htmlspecialchars($order['phone'] ?? 'N/A'); ?>
                                                    </small>
                                                </td>
                                                <td><strong>₹<?php echo number_format($order['total_amount'], 2); ?></strong></td>
                                                <td>
                                                    <?php 
                                                    $paymentBadge = $order['payment_method'] === 'cod' ? 'bg-warning' : 'bg-info';
                                                    $paymentText = strtoupper(str_replace('_', ' ', $order['payment_method']));
                                                    ?>
                                                    <span class="badge <?php echo $paymentBadge; ?>"><?php echo $paymentText; ?></span>
                                                </td>
                                                <td>
                                                    <span class="badge" style="background-color: <?php echo $order['status_color']; ?>; color: white;">
                                                        <?php echo htmlspecialchars($order['status_name']); ?>
                                                    </span>
                                                </td>
                                                <td><?php echo date('M d, Y H:i', strtotime($order['created_at'])); ?></td>
                                                <td>
                                                    <div class="btn-group btn-group-sm" role="group">
                                                        <button type="button" class="btn btn-outline-primary" 
                                                                onclick="viewOrder(<?php echo $order['id']; ?>)" title="View Details">
                                                            <i class="fas fa-eye"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-outline-success" 
                                                                onclick="updateStatus(<?php echo $order['id']; ?>)" title="Update Status">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        <a href="../track_order.php?tracking_id=<?php echo urlencode($order['tracking_id']); ?>" 
                                                           target="_blank" class="btn btn-outline-info" title="Track Order">
                                                            <i class="fas fa-truck"></i>
                                                        </a>
                                                        <a href="../download_invoice.php?order_id=<?php echo $order['id']; ?>" 
                                                           target="_blank" class="btn btn-outline-warning" title="Download Invoice">
                                                            <i class="fas fa-file-invoice"></i>
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
                </div>
            </div>
        </div>
    </div>

    <!-- Order Details Modal -->
    <div class="modal fade" id="orderDetailsModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Order Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="orderDetailsContent">
                    <div class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
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
                                <select class="form-select" name="status_id" id="update_status_id" required>
                                    <?php foreach ($orderStatuses as $status): ?>
                                        <option value="<?php echo $status['id']; ?>"><?php echo htmlspecialchars($status['name']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Estimated Delivery Date</label>
                                <input type="date" class="form-control" name="estimated_delivery_date" id="update_delivery_date">
                            </div>
                        </div>
                        
                        <div class="mb-3 mt-3">
                            <label class="form-label">Status Description</label>
                            <textarea class="form-control" name="status_description" id="update_description" rows="2" 
                                      placeholder="Optional notes about this status update..."></textarea>
                        </div>
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">External Tracking ID</label>
                                <input type="text" class="form-control" name="external_tracking_id" id="update_tracking_id" 
                                       placeholder="e.g., DTDC123456">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">External Tracking Link</label>
                                <input type="url" class="form-control" name="external_tracking_link" id="update_tracking_link" 
                                       placeholder="https://track.example.com/...">
                            </div>
                        </div>
                        
                        <div class="alert alert-info mt-3">
                            <i class="fas fa-info-circle"></i>
                            <strong>Note:</strong> Customer will receive an email notification when you update the order status.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-check"></i> Update Status
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="../admin/assets/js/admin.js"></script>
    <script>
    $(document).ready(function() {
        $('#ordersTable').DataTable({
            order: [[7, 'desc']],
            pageLength: 25,
            language: {
                emptyTable: "No orders found"
            }
        });
    });

    function viewOrder(orderId) {
        const modal = new bootstrap.Modal(document.getElementById('orderDetailsModal'));
        modal.show();
        
        // Load order details via AJAX
        fetch(`ajax/get_order_details.php?id=${orderId}`)
            .then(response => response.text())
            .then(html => {
                document.getElementById('orderDetailsContent').innerHTML = html;
            })
            .catch(error => {
                document.getElementById('orderDetailsContent').innerHTML = 
                    '<div class="alert alert-danger">Error loading order details</div>';
            });
    }

    function updateStatus(orderId) {
        // Load current order data
        fetch(`ajax/get_order_details.php?id=${orderId}&json=1`)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.order) {
                    const order = data.order;
                    
                    // Populate form
                    document.getElementById('update_order_id').value = orderId;
                    document.getElementById('update_status_id').value = order.order_status_id || '';
                    document.getElementById('update_description').value = order.status_description || '';
                    document.getElementById('update_tracking_id').value = order.external_tracking_id || '';
                    document.getElementById('update_tracking_link').value = order.external_tracking_link || '';
                    document.getElementById('update_delivery_date').value = order.estimated_delivery_date || '';
                    
                    // Show modal
                    const modal = new bootstrap.Modal(document.getElementById('updateStatusModal'));
                    modal.show();
                } else {
                    alert('Error loading order data');
                }
            })
            .catch(error => {
                alert('Error: ' + error);
            });
    }
    </script>
</body>
</html>