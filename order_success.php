<?php
session_start();
require_once 'includes/functions.php';

// Check if user is logged in
if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$orderId = intval($_GET['order_id'] ?? 0);
if (!$orderId) {
    header('Location: index.php');
    exit;
}

$order = getOrderById($orderId, $_SESSION['user_id']);
if (!$order) {
    header('Location: index.php');
    exit;
}

$orderItems = getOrderItems($orderId);
$pageTitle = 'Order Confirmed';
include 'includes/header.php';
?>

<div class="page-banner" style="background: url('asset/images/internalpage-bg.webp') center/cover no-repeat; min-height: 240px; display: flex; align-items: center;">
    <div class="container">
        <h2 style="color: #fff; font-size: 2rem; font-weight: bold; text-shadow: 0 2px 8px rgba(0,0,0,0.3); margin: 0; padding: 32px 0;">
            Order Confirmed
        </h2>
    </div>
</div>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Success Message -->
            <div class="ordersuccess-card border-success mb-4">
                <div class="card-body text-center">
                    <i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>
                    <h3 class="text-success mt-3">Thank You!</h3>
                    <p class="lead">Your order has been placed successfully.</p>
                    <p class="text-muted">We'll send you updates about your order via email and SMS.</p>
                </div>
            </div>

            <!-- Order Details -->
            <div class="ordersuccess-card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Order Details</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Order ID:</strong> #<?php echo $order['id']; ?></p>
                            <p><strong>Tracking ID:</strong> 
                                <a href="track_order.php?tracking_id=<?php echo $order['tracking_id']; ?>" class="text-primary">
                                    <?php echo $order['tracking_id']; ?>
                                </a>
                            </p>
                            <p><strong>Order Date:</strong> <?php echo date('F j, Y', strtotime($order['created_at'])); ?></p>
                            <p><strong>Payment Method:</strong> 
                                <?php echo strtoupper($order['payment_method']); ?>
                                <?php if ($order['payment_method'] === 'cod'): ?>
                                    <span class="badge bg-warning">Pay on Delivery</span>
                                <?php else: ?>
                                    <span class="badge bg-success">Paid Online</span>
                                <?php endif; ?>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Order Status:</strong> 
                                <span class="badge" style="background-color: <?php echo $order['status_color']; ?>">
                                    <?php echo $order['status_name']; ?>
                                </span>
                            </p>
                            <p><strong>Total Amount:</strong> ₹<?php echo number_format($order['total_amount'], 2); ?></p>
                            <?php if ($order['estimated_delivery_date']): ?>
                                <p><strong>Estimated Delivery:</strong> <?php echo date('F j, Y', strtotime($order['estimated_delivery_date'])); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Delivery Address -->
            <div class="ordersuccess-card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Delivery Address</h5>
                </div>
                <div class="card-body">
                    <p><strong><?php echo htmlspecialchars($order['address_name']); ?></strong></p>
                    <p><?php echo htmlspecialchars($order['address_line1']); ?></p>
                    <?php if ($order['address_line2']): ?>
                        <p><?php echo htmlspecialchars($order['address_line2']); ?></p>
                    <?php endif; ?>
                    <p><?php echo htmlspecialchars($order['city'] . ', ' . $order['state'] . ' - ' . $order['pincode']); ?></p>
                    <p>Phone: <?php echo htmlspecialchars($order['address_phone']); ?></p>
                </div>
            </div>

            <!-- Order Items -->
            <div class="ordersuccess-card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Order Items</h5>
                </div>
                <div class="card-body">
                    <?php foreach ($orderItems as $item): ?>
                        <div class="row mb-3 align-items-center">
                            <div class="col-md-2">
                                <img src="./<?php echo $item['main_image']; ?>" alt="<?php echo $item['name']; ?>" class="img-fluid">
                            </div>
                            <div class="col-md-6">
                                <h6><?php echo htmlspecialchars($item['name']); ?></h6>
                                <p class="text-muted">Quantity: <?php echo $item['quantity']; ?></p>
                            </div>
                            <div class="col-md-4 text-end">
                                <strong>₹<?php echo number_format($item['price'] * $item['quantity'], 2); ?></strong>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="text-center">
                <a href="track_order.php?tracking_id=<?php echo $order['tracking_id']; ?>" class="btn btn-primary me-2">
                    <i class="fas fa-truck"></i> Track Order
                </a>
                <a href="myaccount.php" class="btn btn-outline-primary me-2">
                    <i class="fas fa-user"></i> My Account
                </a>
                <a href="index.php" class="btn btn-outline-secondary">
                    <i class="fas fa-home"></i> Continue Shopping
                </a>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?> 