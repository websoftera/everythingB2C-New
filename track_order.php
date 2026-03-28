<?php
session_start();
require_once 'includes/functions.php';

$trackingId = $_GET['tracking_id'] ?? '';
$order = null;
$orderItems = [];
$statusHistory = [];

if ($trackingId) {
    global $pdo;
    // Check both tracking_id and external_tracking_id fields
    $stmt = $pdo->prepare("SELECT o.*, os.name as status_name, os.color as status_color, os.description as status_description,
                                 a.name as address_name, a.phone as address_phone, a.address_line1, a.address_line2, a.city, a.state, a.pincode
                          FROM orders o 
                          LEFT JOIN order_statuses os ON o.order_status_id = os.id
                          LEFT JOIN addresses a ON o.address_id = a.id
                          WHERE o.tracking_id = ? OR o.external_tracking_id = ?");
    $stmt->execute([$trackingId, $trackingId]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($order) {
        $orderItems = getOrderItems($order['id']);
        $statusHistory = getOrderStatusHistory($order['id']);
    }
}

$pageTitle = 'Track Order';
include 'includes/header.php';
?>

<style>
:root {
    --primary-color: #007bff;
    --success-color: #28a745;
    --card-border-radius: 12px;
}

.track-order-wrapper {
    background-color: #f8f9fb;
    min-height: 100vh;
    padding-bottom: 50px;
    width: 100% !important;
}

.track-main-container {
    width: 100% !important;
    max-width: 1300px !important;
    margin: 0 auto !important;
    padding: 20px 15px;
    box-sizing: border-box;
}

.section-title {
    font-size: 1.8rem;
    font-weight: 800;
    margin-bottom: 25px;
    color: #2d3748;
}

/* Base card styles */
.track-card {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: var(--card-border-radius);
    box-shadow: 0 4px 12px rgba(0,0,0,0.03);
    margin-bottom: 24px;
    overflow: hidden;
    width: 100% !important;
}

/* Product Card with Blue Bar */
.product-item-card {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    margin-bottom: 10px;
    overflow: hidden;
    box-shadow: 0 4px 10px rgba(0,0,0,0.03);
}

.product-card-blue-bar {
    background: #007bff;
    color: #fff;
    padding: 6px 15px;
    font-size: 0.82rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.product-row-inner {
    display: flex;
    padding: 12px 20px;
}

.product-img-wrapper {
    width: 100px;
    min-width: 100px;
    height: 100px;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 1px solid #f0f4f8;
    border-radius: 10px;
    background: #fff;
    padding: 8px;
}

.product-img {
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
}

.product-info {
    flex-grow: 1;
    padding-left: 15px;
    display: flex;
    flex-direction: column;
    justify-content: center;
}

.product-price-desktop {
    width: 160px;
    min-width: 160px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: flex-end;
    font-weight: 700;
    font-size: 1.25rem;
    color: #007bff;
}

/* Mobile Adjustments */
@media (max-width: 767.98px) {
    .product-row-inner {
        padding: 10px !important;
        gap: 12px !important;
        display: flex !important;
        flex-direction: row !important;
        align-items: flex-start !important;
    }

    .product-img-wrapper {
        width: 65px !important;
        height: 65px !important;
        min-width: 65px !important;
    }

    .product-info {
        padding-left: 0 !important;
        display: flex !important;
        flex-direction: column !important;
        flex: 1 !important;
    }

    .product-info h5 {
        font-size: 11px !important;
        line-height: 1.3 !important;
        margin-bottom: 5px !important;
        width: 100% !important;
        display: block !important;
        float: none !important;
    }

    .product-details-line-mobile {
        display: block !important;
        width: 100% !important;
        font-size: 11px !important;
        font-weight: 700 !important;
        color: #1a202c !important;
        float: none !important;
        margin-top: 0 !important;
    }

    .qty-info {
        color: #718096 !important;
        font-weight: 600 !important;
        font-size: 10px !important;
        margin-left: 5px !important;
    }

    /* Section Headers to 11px */
    .sidebar-title, 
    .track-card h6,
    .address-title {
        font-size: 11px !important;
        font-weight: 800 !important;
        text-transform: uppercase !important;
        letter-spacing: 0.5px !important;
    }

    .section-title {
        font-size: 1.2rem !important;
    }

    .track-card h1 {
        font-size: 1.3rem !important;
    }

    /* General text in summary cards to 11px */
    .track-card .small, 
    .track-card .extra-small,
    .track-card span,
    .track-card div:not(.sidebar-title):not(h1):not(h4) {
        font-size: 11px !important;
    }

    .track-card .fs-4 {
        font-size: 1.2rem !important; /* Keep final amount slightly prominent */
    }
}

/* Stepper & Timeline */
.stepper-wrapper {
    display: flex;
    justify-content: space-between;
    position: relative;
    max-width: 1100px;
    margin: 30px auto 10px;
}

.stepper-wrapper::before {
    content: '';
    position: absolute;
    top: 24px;
    left: 50px;
    right: 50px;
    height: 4px;
    background: #f0f4f8;
}

.stepper-item {
    position: relative;
    display: flex;
    flex-direction: column;
    align-items: center;
    flex: 1;
    z-index: 1;
}

.step-counter {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: #fff;
    border: 3px solid #f0f4f8;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    margin-bottom: 10px;
    color: #cbd5e0;
}

.stepper-item.completed .step-counter {
    background: #28a745;
    border-color: #28a745;
    color: #fff;
}

.detailed-timeline {
    position: relative;
    padding-left: 30px;
}

.detailed-timeline::before {
    content: '';
    position: absolute;
    left: 10px;
    top: 5px;
    bottom: 5px;
    width: 2px;
    background: #f0f4f8;
}

.detailed-timeline-item {
    position: relative;
    padding-bottom: 25px;
}

.time-marker {
    position: absolute;
    left: -25px;
    top: 5px;
    width: 14px;
    height: 14px;
    border-radius: 50%;
    border: 3px solid #fff;
    box-shadow: 0 0 0 2px #f0f4f8;
}
</style>

<div class="track-order-wrapper">
    <div class="track-main-container">
        <!-- Search -->
        <div class="row mb-4">
            <div class="col-12">
                <h3 class="section-title">Track Your Order</h3>
                <div class="track-order-search-box" style="max-width: 550px;">
                    <form method="GET" action="track_order.php" class="d-flex gap-2">
                        <input type="text" class="form-control" name="tracking_id" placeholder="Enter Tracking ID" value="<?php echo htmlspecialchars($trackingId); ?>" required>
                        <button class="btn btn-primary px-4 fw-bold" type="submit">Track Status</button>
                    </form>
                </div>
            </div>
        </div>

        <?php if ($trackingId && !$order): ?>
            <div class="track-card p-5 text-center">
                <i class="fas fa-search-minus fa-4x mb-4 text-muted opacity-25"></i>
                <h4 class="fw-bold">No Order Found</h4>
                <p class="text-muted">No order matches the tracking ID "<?php echo htmlspecialchars($trackingId); ?>".</p>
                <a href="index.php" class="btn btn-primary rounded-pill px-4 mt-3">Back to Store</a>
            </div>
        <?php elseif ($order): ?>
            <?php 
            $currentStatus = strtolower($order['status_name'] ?? '');
            $step = 1;
            if (in_array($currentStatus, ['packed'])) $step = 2;
            if (in_array($currentStatus, ['shipped', 'out_for_delivery', 'dispatched'])) $step = 3;
            if (in_array($currentStatus, ['delivered'])) $step = 4;
            if (in_array($currentStatus, ['cancelled', 'returned'])) $step = -1;
            ?>

            <!-- Top Summary -->
            <div class="track-card p-4 p-md-5 mb-4">
                <div class="row align-items-center">
                    <div class="col-md-7">
                        <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 mb-3 fw-bold rounded-pill" style="font-size: 0.75rem;">ORDER STATUS TRACKER</span>
                        <h1 class="fw-bold mb-3" style="font-size: clamp(1.3rem, 4vw, 2.1rem);">Order #<?php echo htmlspecialchars($order['tracking_id'] ?? $order['id']); ?></h1>
                        <div class="d-flex flex-wrap gap-4 text-muted" style="font-size: 0.9rem;">
                            <span><i class="far fa-calendar-alt me-2 text-primary"></i> <?php echo date('M j, Y', strtotime($order['created_at'])); ?></span>
                            <span><i class="fas fa-wallet me-2 text-primary"></i> <?php echo strtoupper($order['payment_method']); ?></span>
                        </div>
                    </div>
                    <div class="col-md-5 text-md-end mt-4 mt-md-0">
                        <div class="d-inline-block p-3 rounded-4 bg-light border text-center" style="min-width: 180px;">
                            <div class="small fw-bold text-muted text-uppercase mb-1" style="font-size: 10px;">Status</div>
                            <div class="fw-bold fs-5" style="color: <?php echo $order['status_color']; ?>;"><?php echo $order['status_name']; ?></div>
                        </div>
                        <div class="mt-3">
                            <a href="download_invoice.php?order_id=<?php echo $order['id']; ?>" class="btn btn-dark rounded-pill px-4" target="_blank">
                                <i class="fas fa-file-invoice me-2"></i>Invoice
                            </a>
                        </div>
                    </div>
                </div>

                <?php if ($step != -1): ?>
                <div class="stepper-wrapper mt-5 pt-3">
                    <div class="stepper-item <?php echo $step >= 1 ? 'completed' : ''; ?>">
                        <div class="step-counter"><?php echo $step >= 1 ? '<i class="fas fa-check"></i>' : '1'; ?></div>
                        <div class="step-name small">Placed</div>
                    </div>
                    <div class="stepper-item <?php echo $step >= 2 ? 'completed' : ($step == 1 ? 'active' : ''); ?>">
                        <div class="step-counter"><?php echo $step > 2 ? '<i class="fas fa-check"></i>' : '2'; ?></div>
                        <div class="step-name small">Packed</div>
                    </div>
                    <div class="stepper-item <?php echo $step >= 3 ? 'completed' : ($step == 2 ? 'active' : ''); ?>">
                        <div class="step-counter"><?php echo $step > 3 ? '<i class="fas fa-check"></i>' : '3'; ?></div>
                        <div class="step-name small">Shipped</div>
                    </div>
                    <div class="stepper-item <?php echo $step >= 4 ? 'completed' : ($step == 3 ? 'active' : ''); ?>">
                        <div class="step-counter"><?php echo $step == 4 ? '<i class="fas fa-check"></i>' : '4'; ?></div>
                        <div class="step-name small">Delivered</div>
                    </div>
                </div>
                <?php else: ?>
                <div class="alert alert-danger mt-5 mb-0 rounded-4 border p-4 text-center">
                    <i class="fas fa-exclamation-circle me-2"></i> This order status is <strong><?php echo $order['status_name']; ?></strong>.
                </div>
                <?php endif; ?>
            </div>

            <div class="row g-4 mb-5">
                <!-- Products -->
                <div class="col-lg-8">
                    <h4 class="sidebar-title ms-1">Product Details</h4>
                    <div class="products-list mb-5">
                        <?php foreach ($orderItems as $item): ?>
                        <div class="product-item-card">
                            <div class="product-card-blue-bar">ITEM DETAILS</div>
                            <div class="product-row-inner">
                                <div class="product-img-wrapper">
                                    <img src="./<?php echo $item['main_image']; ?>" class="product-img" onerror="this.src='./uploads/products/blank-img.webp';">
                                </div>
                                <div class="product-info">
                                    <h5 class="fw-bold"><?php echo htmlspecialchars($item['name']); ?></h5>
                                    
                                    <div class="product-details-line-mobile d-md-none">
                                        ₹<?php echo number_format($item['price'] * $item['quantity'], 0); ?>
                                        <span class="qty-info">(₹<?php echo number_format($item['price'], 0); ?> x <?php echo $item['quantity']; ?>)</span>
                                    </div>

                                    <div class="d-none d-md-block text-muted small mt-1">
                                        ₹<?php echo number_format($item['price'], 0); ?> x <?php echo $item['quantity']; ?>
                                    </div>
                                </div>
                                <div class="product-price-desktop d-none d-md-flex">
                                    ₹<?php echo number_format($item['price'] * $item['quantity'], 0); ?>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <h4 class="sidebar-title ms-1">Tracking History</h4>
                    <div class="track-card p-4 p-md-5 border-0 shadow-sm">
                        <div class="detailed-timeline">
                            <?php foreach ($statusHistory as $status): ?>
                            <div class="detailed-timeline-item">
                                <div class="time-marker" style="background-color: <?php echo $status['status_color'] ?: '#007bff'; ?>;"></div>
                                <div class="ps-3 ps-md-4">
                                    <div class="fw-bold fs-6 mb-1"><?php echo htmlspecialchars($status['status_name']); ?></div>
                                    <div class="text-muted small mb-2"><i class="far fa-clock me-1"></i> <?php echo date('M j, Y - g:i A', strtotime($status['created_at'])); ?></div>
                                    <?php if ($status['status_description']): ?>
                                        <p class="mb-0 small text-muted lh-sm"><?php echo htmlspecialchars($status['status_description']); ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- Sidebar Summary -->
                <div class="col-lg-4">
                    <h4 class="sidebar-title">Order Details</h4>
                    <div class="track-card border-0 shadow-sm mb-4">
                        <div class="bg-primary bg-opacity-10 p-2 border-bottom text-primary fw-bold text-center address-title">
                            <i class="fas fa-truck me-2"></i> Shipping Address
                        </div>
                        <div class="card-body p-3">
                            <h6 class="mb-3 fw-bold extra-small text-uppercase text-muted letter-spacing-1">Shipping Address</h6>
                            <?php if (!empty($order['address_name']) || !empty($order['shipping_address'])): ?>
                                <?php if (!empty($order['address_name'])): ?>
                                    <div class="fw-bold mb-1 small"><?php echo htmlspecialchars($order['address_name']); ?></div>
                                    <div class="text-muted extra-small mb-3">
                                        <?php echo htmlspecialchars($order['address_line1']); ?><br>
                                        <?php if (!empty($order['address_line2'])) echo htmlspecialchars($order['address_line2']) . '<br>'; ?>
                                        <?php echo htmlspecialchars($order['city'] . ', ' . $order['state'] . ' - ' . $order['pincode']); ?>
                                    </div>
                                    <div class="bg-light p-2 rounded-3 border-start border-primary border-4">
                                        <span class="extra-small text-muted d-block mb-1">Mobile</span>
                                        <div class="fw-bold small"><?php echo htmlspecialchars($order['address_phone']); ?></div>
                                    </div>
                                <?php else: ?>
                                    <!-- Fallback to snapshotted address -->
                                    <div class="text-muted extra-small mb-3" style="white-space: pre-line;">
                                        <?php echo htmlspecialchars($order['shipping_address']); ?>
                                    </div>
                                <?php endif; ?>
                            <?php else: ?>
                                <div class="text-muted extra-small">Address details not available for this order.</div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="track-card p-4 border-0 shadow-sm mb-4">
                        <h6 class="fw-bold mb-4 small">Billing Summary</h6>
                        <div class="d-flex justify-content-between mb-2 extra-small text-muted">
                            <span>Subtotal</span>
                            <span class="fw-bold text-dark">₹<?php echo number_format($order['subtotal'] ?? 0, 0); ?></span>
                        </div>
                        <?php if (!empty($order['shipping_charge'])): ?>
                        <div class="d-flex justify-content-between mb-2 extra-small text-muted">
                            <span>Shipping</span>
                            <span class="fw-bold text-dark">₹<?php echo number_format($order['shipping_charge'], 0); ?></span>
                        </div>
                        <?php endif; ?>
                        <div class="d-flex justify-content-between mb-0 extra-small text-success">
                            <span>Total Savings</span>
                            <span class="fw-bold">-₹<?php 
                                $savings = 0;
                                foreach($orderItems as $it) {
                                    if(isset($it['mrp']) && isset($it['selling_price'])) $savings += ($it['mrp'] - $it['selling_price']) * $it['quantity'];
                                }
                                echo number_format($savings, 0);
                            ?></span>
                        </div>
                        <hr class="my-3 border-dashed">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fw-bold small">Net Amount</span>
                            <span class="fw-bold text-primary fs-4">₹<?php echo number_format($order['total_amount'], 0); ?></span>
                        </div>
                    </div>

                    <div class="track-card p-3 text-center bg-white border-0 shadow-sm">
                        <h6 class="fw-bold mb-2 small">Need Assistance?</h6>
                        <p class="extra-small text-muted mb-3">Our support team is here.</p>
                        <a href="tel:+918780406230" class="btn btn-outline-primary w-100 rounded-pill mb-2 fw-bold extra-small py-2">
                            <i class="fas fa-phone-alt me-2"></i>+91 878 040 6230
                        </a>
                        <a href="mailto:info@everythingb2c.in" class="btn btn-outline-primary w-100 rounded-pill fw-bold extra-small py-2">
                            <i class="fas fa-envelope me-2"></i>info@everythingb2c.in
                        </a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>