<?php
session_start();
require_once 'includes/functions.php';

$trackingId = $_GET['tracking_id'] ?? '';
$order = null;
$orderItems = [];
$statusHistory = [];

if ($trackingId) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT o.*, os.name as status_name, os.color as status_color, os.description as status_description,
                                 a.name as address_name, a.phone as address_phone, a.address_line1, a.address_line2, a.city, a.state, a.pincode
                          FROM orders o 
                          LEFT JOIN order_statuses os ON o.order_status_id = os.id
                          LEFT JOIN addresses a ON o.address_id = a.id
                          WHERE o.tracking_id = ?");
    $stmt->execute([$trackingId]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($order) {
        $orderItems = getOrderItems($order['id']);
        $statusHistory = getOrderStatusHistory($order['id']);
    }
}

$pageTitle = 'Track Order';
include 'includes/header.php';
?>

<div class="page-banner" style="background: url('asset/images/internalpage-bg.webp') center/cover no-repeat; min-height: 240px; display: flex; align-items: center;">
    <div class="container">
        <h2 style="color: #fff; font-size: 2rem; font-weight: bold; text-shadow: 0 2px 8px rgba(0,0,0,0.3); margin: 0; padding: 32px 0;">
            Track Your Order
        </h2>
    </div>
</div>

<div class="container my-5">
    <!-- Track Order Form -->
    <div class="row justify-content-center mb-5">
        <div class="col-lg-6">
            <div class="trackorder-card">
                <div class="card-body">
                    <form method="GET" action="track_order.php">
                        <div class="input-group">
                            <input type="text" class="form-control" name="tracking_id" placeholder="Enter your tracking ID (e.g., Everythingb2c00000001)" value="<?php echo htmlspecialchars($trackingId); ?>" required>
                            <button class="btn btn-primary" type="submit">
                                <i class="fas fa-search"></i> Track
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <?php if ($trackingId && !$order): ?>
        <!-- Order Not Found -->
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="alert alert-warning text-center">
                    <i class="fas fa-exclamation-triangle fa-2x mb-3"></i>
                    <h4>Order Not Found</h4>
                    <p>The tracking ID "<?php echo htmlspecialchars($trackingId); ?>" was not found in our system.</p>
                    <p>Please check your tracking ID and try again.</p>
                </div>
            </div>
        </div>
    <?php elseif ($order): ?>
        <?php if ($order && $order['external_tracking_link']): ?>
            <div class="container mb-4">
                <div class="alert alert-info d-flex align-items-center justify-content-between" style="border-radius: 8px;">
                    <div>
                        <i class="fas fa-shipping-fast me-2"></i>
                        <strong>Check Live Updates:</strong> You can track your shipment in real time.
                    </div>
                    <a href="<?php echo htmlspecialchars($order['external_tracking_link']); ?>" target="_blank" class="btn btn-primary ms-3">
                        <i class="fas fa-external-link-alt"></i> Check Live Updates
                    </a>
                </div>
            </div>
        <?php endif; ?>
        <!-- Order Found - Display Details -->
        <div class="row">
            <div class="col-lg-8">
                <!-- Order Items -->
                <div class="trackorder-card mb-4">
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
                                    <h6><a href="product.php?slug=<?php echo urlencode($item['slug']); ?>" target="_blank"><?php echo htmlspecialchars($item['name']); ?></a></h6>
                                    <p class="text-muted">Quantity: <?php echo $item['quantity']; ?></p>
                                </div>
                                <div class="col-md-4">
                                    <p><strong>HSN:</strong> <?php echo htmlspecialchars($item['hsn'] ?? ''); ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <!-- Order Status Timeline -->
                <div class="trackorder-card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Order Status</h5>
                    </div>
                    <div class="card-body">
                        <div class="timeline">
                            <?php foreach ($statusHistory as $index => $status): ?>
                                <div class="timeline-item">
                                    <div class="timeline-marker" style="background-color: <?php echo $status['status_color']; ?>"></div>
                                    <div class="timeline-content">
                                        <h6 class="mb-1"><?php echo htmlspecialchars($status['status_name']); ?></h6>
                                        <p class="text-muted mb-1"><?php echo date('F j, Y g:i A', strtotime($status['created_at'])); ?></p>
                                        <?php if ($status['status_description']): ?>
                                            <p class="mb-0"><?php echo htmlspecialchars($status['status_description']); ?></p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <!-- Order Summary -->
                <div class="trackorder-card mb-4">
                    <div class="card-header d-flex align-items-center">
                        <h5 class="mb-0">Order Summary</h5>
                        <span title="These are the official order values as confirmed at checkout." style="margin-left:8px; color:#007bff; cursor:help;"><i class="fas fa-info-circle"></i></span>
                    </div>
                    <div class="card-body">
                        <p><strong>Order ID:</strong> #<?php echo $order['id']; ?></p>
                        <p><strong>Tracking ID:</strong> <?php echo $order['tracking_id']; ?></p>
                        <p><strong>Order Date:</strong> <?php echo date('F j, Y', strtotime($order['created_at'])); ?></p>
                        <p><strong>Current Status:</strong> 
                            <span class="badge" style="background-color: <?php echo $order['status_color']; ?>">
                                <?php echo $order['status_name']; ?>
                            </span>
                        </p>
                        <p><strong>Subtotal:</strong> ₹<?php echo number_format($order['subtotal'], 2); ?></p>
                        <?php if (!empty($order['shipping_charge'])): ?>
                            <p><strong>Shipping:</strong> ₹<?php echo number_format($order['shipping_charge'], 2); ?></p>
                        <?php endif; ?>
                        <?php if (!empty($order['gst_amount'])): ?>
                            <p><strong>Taxes (GST):</strong> ₹<?php echo number_format($order['gst_amount'], 2); ?></p>
                        <?php endif; ?>
                        <?php 
                        $total_savings = 0;
                        foreach ($orderItems as $item) {
                            if (isset($item['mrp']) && isset($item['selling_price'])) {
                                $total_savings += ($item['mrp'] - $item['selling_price']) * $item['quantity'];
                            }
                        }
                        ?>
                        <p class="text-success"><strong>Total Savings:</strong> ₹<?php echo number_format($total_savings, 2); ?></p>
                        <p><strong>Total Amount:</strong> ₹<?php echo number_format($order['total_amount'], 2); ?></p>
                        <?php if ($order['estimated_delivery_date']): ?>
                            <p><strong>Estimated Delivery:</strong> <?php echo date('F j, Y', strtotime($order['estimated_delivery_date'])); ?></p>
                        <?php endif; ?>
                        <?php if ($order['external_tracking_link']): ?>
                            <p><strong>External Tracking:</strong> 
                                <a href="<?php echo htmlspecialchars($order['external_tracking_link']); ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-external-link-alt"></i> Track on Courier Site
                                </a>
                            </p>
                        <?php endif; ?>
                    </div>
                    <div class="px-3 pb-3"><small class="text-muted"><i class="fas fa-info-circle"></i> All amounts are as per your order confirmation at checkout.</small></div>
                </div>

                <!-- Delivery Address -->
                <div class="trackorder-card mb-4">
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

                <!-- Action Buttons -->
                <div class="d-grid gap-2">
                    <?php if (isLoggedIn() && $order['user_id'] == $_SESSION['user_id']): ?>
                        <a href="myaccount.php" class="btn btn-outline-primary">
                            <i class="fas fa-user"></i> My Account
                        </a>
                    <?php endif; ?>
                    <a href="index.php" class="btn btn-outline-secondary">
                        <i class="fas fa-home"></i> Continue Shopping
                    </a>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.trackorder-card{
    width: 100%;
    min-width: 270px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 15px;
    top: 0;
    bottom: 0;
    width: 2px;
    background-color: #e9ecef;
}

.timeline-item {
    position: relative;
    margin-bottom: 30px;
}

.timeline-marker {
    position: absolute;
    left: -22px;
    top: 0;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    border: 2px solid #fff;
    box-shadow: 0 0 0 2px #e9ecef;
}

.timeline-content {
    background-color: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
    margin-left: 10px;
}

.timeline-item:last-child .timeline-content {
    background-color: #e3f2fd;
    border-left: 4px solid #2196f3;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
  var trackingInput = document.querySelector('input[name="tracking_id"]');
  if (trackingInput && trackingInput.value) {
    trackingInput.form && trackingInput.form.classList.add('d-none'); // Hide form if tracking_id is present
  }
  // Copy tracking ID button
  var copyBtn = document.getElementById('copyTrackingIdBtn');
  if (copyBtn) {
    copyBtn.onclick = function() {
      var tid = document.getElementById('trackingIdText');
      if (tid) {
        navigator.clipboard.writeText(tid.textContent);
        copyBtn.innerHTML = '<i class="fas fa-check"></i> Copied!';
        setTimeout(function(){ copyBtn.innerHTML = '<i class="fas fa-copy"></i> Copy'; }, 1500);
      }
    };
  }
});
</script>

<?php include 'includes/footer.php'; ?> 