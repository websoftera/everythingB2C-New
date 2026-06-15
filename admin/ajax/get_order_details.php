<?php
session_start();
require_once '../../config/database.php';
require_once '../../includes/functions.php';

if (!isset($_SESSION['admin_id'])) {
    http_response_code(403);
    exit('Unauthorized');
}

$order_id = intval($_GET['id'] ?? 0);
if (!$order_id) {
    echo '<div class="alert alert-warning">Invalid order ID.</div>';
    exit;
}

$stmt = $pdo->prepare("SELECT o.*, os.name as status_name, os.color as status_color, u.name as customer_name, u.email as customer_email, u.phone as customer_phone, a.name as address_name, a.phone as address_phone, a.address_line1, a.address_line2, a.city, a.state, a.pincode FROM orders o LEFT JOIN order_statuses os ON o.order_status_id = os.id LEFT JOIN users u ON o.user_id = u.id LEFT JOIN addresses a ON o.address_id = a.id WHERE o.id = ?");
$stmt->execute([$order_id]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (isset($_GET['json']) && $_GET['json'] == '1') {
    header('Content-Type: application/json');
    if ($order) {
        echo json_encode(['success' => true, 'order' => $order]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Order not found']);
    }
    exit;
}

try {
    if (!$order) {
        echo '<div class="alert alert-warning">Order not found.</div>';
        exit;
    }

    $orderStatusName = $order['status_name'] ?: ($order['status'] ?? 'Pending');
    $orderStatusColor = $order['status_color'] ?: '#0d6efd';
    $paymentStatus = strtolower($order['payment_status'] ?? 'pending');
    $paymentBadgeClass = match ($paymentStatus) {
        'paid' => 'bg-success',
        'pending' => 'bg-warning text-dark',
        'failed' => 'bg-danger',
        'refunded' => 'bg-info text-dark',
        default => 'bg-secondary'
    };
    $paymentMethodLabel = strtoupper(str_replace('_', ' ', $order['payment_method'] ?? ''));
    $money = function ($amount) {
        return '&#8377;' . number_format((float)$amount, 0);
    };

    $stmt = $pdo->prepare("SELECT oi.*, p.name as product_name, p.main_image, p.slug, p.hsn, p.package_quantity FROM order_items oi LEFT JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?");
    $stmt->execute([$order_id]);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $totalMrp = 0;
    $productTotal = 0;
    $totalSavings = 0;

    echo '<style>
        .order-detail-shell { color: #2b2f33; }
        .order-detail-eyebrow { color: #7b8794; font-size: 11px; font-weight: 700; letter-spacing: .04em; text-transform: uppercase; }
        .order-detail-card { border: 1px solid #dee2e6; border-radius: 8px; padding: 14px; height: 100%; background: #fff; }
        .order-detail-card-soft { background: #f8fbff; border-color: #d8e7ff; }
        .order-detail-muted { color: #6c757d; font-size: 13px; }
        .order-detail-table th { background: #f6f7f9; font-size: 13px; white-space: nowrap; }
        .order-detail-table td { vertical-align: middle; font-size: 13px; }
        .order-detail-product { display: flex; gap: 10px; align-items: center; min-width: 260px; }
        .order-detail-product img { width: 42px; height: 42px; object-fit: cover; border: 1px solid #dee2e6; border-radius: 6px; flex: 0 0 42px; }
        .order-detail-summary-row { display: flex; justify-content: space-between; gap: 16px; padding: 8px 0; border-bottom: 1px solid #dee2e6; }
        .order-detail-summary-row:last-child { border-bottom: 0; }
    </style>';

    echo '<div class="order-detail-shell">';
    echo '<div class="d-flex justify-content-between align-items-start gap-3 mb-3">';
    echo '<div>';
    echo '<div class="order-detail-eyebrow mb-2">Order</div>';
    echo '<h5 class="mb-2">#'.htmlspecialchars($order['order_number']).' <small class="text-muted">('.htmlspecialchars($order['tracking_id']).')</small></h5>';
    echo '<div class="d-flex flex-wrap gap-2">';
    echo '<span class="badge" style="background-color: '.htmlspecialchars($orderStatusColor).'">'.htmlspecialchars($orderStatusName).'</span>';
    echo '<span class="badge bg-light text-dark border">'.htmlspecialchars($paymentMethodLabel).'</span>';
    echo '</div>';
    echo '</div>';
    echo '<div class="text-end order-detail-muted">'.date('M d, Y', strtotime($order['created_at'])).'<br>'.date('g:i A', strtotime($order['created_at'])).'</div>';
    echo '</div>';

    echo '<div class="row g-3 mb-3">';
    echo '<div class="col-md-6"><div class="order-detail-card">';
    echo '<div class="order-detail-eyebrow mb-2">Customer</div>';
    echo '<div class="fw-bold mb-2">'.htmlspecialchars($order['customer_name'] ?? '').'</div>';
    echo '<div class="order-detail-muted">Email: '.htmlspecialchars($order['customer_email'] ?? '').'</div>';
    echo '<div class="order-detail-muted">Phone: '.htmlspecialchars($order['customer_phone'] ?? '').'</div>';
    echo '</div></div>';
    echo '<div class="col-md-6"><div class="order-detail-card">';
    echo '<div class="order-detail-eyebrow mb-2">Shipping Address</div>';
    echo '<div class="fw-bold mb-2">'.htmlspecialchars($order['address_name'] ?? '').'</div>';
    echo '<div>'.htmlspecialchars($order['address_phone'] ?? '').'</div>';
    echo '<div>'.htmlspecialchars($order['address_line1'] ?? '').'</div>';
    if (!empty($order['address_line2'])) {
        echo '<div>'.htmlspecialchars($order['address_line2']).'</div>';
    }
    echo '<div>'.htmlspecialchars($order['city'] ?? '').', '.htmlspecialchars($order['state'] ?? '').' - '.htmlspecialchars($order['pincode'] ?? '').'</div>';
    echo '</div></div>';
    echo '</div>';

    echo '<div class="order-detail-card mb-3">';
    echo '<div class="order-detail-eyebrow mb-2">Items</div>';
    echo '<h6 class="mb-3">Order Items</h6>';
    if (empty($items)) {
        echo '<p class="text-muted">No items found for this order.</p>';
    } else {
        echo '<div class="table-responsive">';
        echo '<table class="table table-sm order-detail-table align-middle mb-0">';
        echo '<thead><tr><th>Product</th><th>HSN</th><th class="text-end">MRP</th><th class="text-end">Selling Price</th><th class="text-end">Qty</th><th class="text-end">MRP Total</th><th class="text-end">Selling Total</th><th class="text-end">Savings</th></tr></thead>';
        echo '<tbody>';
        foreach ($items as $item) {
            $mrp = isset($item['mrp']) ? (float)$item['mrp'] : 0;
            $amounts = getOrderItemDisplayAmounts($item);
            $selling = (float)$amounts['unit_price'];
            $qty = formatDisplayQuantity(getOrderItemDisplayQuantity($item));
            $priceMultiplier = (float)$amounts['price_multiplier'];
            $mrpTotal = $mrp * $priceMultiplier;
            $sellingTotal = (float)$amounts['line_total'];
            $itemSavings = max(0, $mrp - $selling) * $priceMultiplier;
            $totalMrp += $mrpTotal;
            $productTotal += $sellingTotal;
            $totalSavings += $itemSavings;

            echo '<tr>';
            echo '<td><div class="order-detail-product">';
            if (!empty($item['main_image'])) {
                echo '<img src="../../'.htmlspecialchars($item['main_image']).'" alt="'.htmlspecialchars(cleanProductName($item['product_name'] ?? '')).'">';
            }
            echo '<div>';
            echo '<div>'.htmlspecialchars(cleanProductName($item['product_name'] ?? '')).'</div>';
            echo '<small class="text-muted">SKU: '.htmlspecialchars($item['slug'] ?? '').'</small>';
            if (!empty($item['package_quantity']) && (int)$item['package_quantity'] > 1) {
                echo '<br><small class="text-muted">Package: '.(int)$item['package_quantity'].' units</small>';
            }
            echo '</div></div></td>';
            echo '<td>'.htmlspecialchars($item['hsn'] ?? '').'</td>';
            echo '<td class="text-end">'.$money($mrp).'</td>';
            echo '<td class="text-end">'.$money($selling).'</td>';
            echo '<td class="text-end">'.$qty.'</td>';
            echo '<td class="text-end">'.$money($mrpTotal).'</td>';
            echo '<td class="text-end">'.$money($sellingTotal).'</td>';
            echo '<td class="text-end text-success">'.$money($itemSavings).'</td>';
            echo '</tr>';
        }
        echo '</tbody></table></div>';
    }
    echo '</div>';

    $storedProductTotal = isset($order['subtotal']) && (float)$order['subtotal'] > 0 ? (float)$order['subtotal'] : $productTotal;
    $deliveryCharge = isset($order['shipping_charge']) ? (float)$order['shipping_charge'] : max(0, (float)($order['total_amount'] ?? 0) - $storedProductTotal);
    $totalAmount = (float)($order['total_amount'] ?? ($storedProductTotal + $deliveryCharge));

    echo '<div class="row g-3">';
    echo '<div class="col-md-7"><div class="order-detail-card">';
    echo '<div class="order-detail-eyebrow mb-3">Payment</div>';
    echo '<div class="row g-3">';
    echo '<div class="col-sm-6"><div class="order-detail-muted">Method</div><div>'.htmlspecialchars($paymentMethodLabel).'</div></div>';
    echo '<div class="col-sm-6"><div class="order-detail-muted">Payment Status</div><div>'.htmlspecialchars(ucfirst($paymentStatus)).'</div></div>';
    echo '<div class="col-sm-6"><div class="order-detail-muted">Order Status</div><div>'.htmlspecialchars($orderStatusName).'</div></div>';
    if (($order['payment_method'] ?? '') === 'direct_payment') {
        echo '<div class="col-sm-6"><div class="order-detail-muted">UPI Transaction ID</div><div>' . (!empty($order['upi_transaction_id']) ? htmlspecialchars($order['upi_transaction_id']) : '<span class="text-danger">Not provided</span>') . '</div></div>';
        if (!empty($order['upi_screenshot'])) {
            echo '<div class="col-12"><div class="order-detail-muted mb-1">Payment Screenshot</div><img src="/'.htmlspecialchars($order['upi_screenshot']).'" alt="UPI Screenshot" style="max-width:220px;border:1px solid #ccc;border-radius:6px;"></div>';
        }
    }
    echo '</div></div></div>';
    echo '<div class="col-md-5"><div class="order-detail-card order-detail-card-soft">';
    echo '<div class="order-detail-eyebrow mb-3">Order Summary</div>';
    echo '<div class="order-detail-summary-row"><span>Total MRP</span><strong>'.$money($totalMrp).'</strong></div>';
    echo '<div class="order-detail-summary-row"><span>Product Total</span><strong>'.$money($storedProductTotal).'</strong></div>';
    echo '<div class="order-detail-summary-row"><span>Delivery Charges</span><strong>'.$money($deliveryCharge).'</strong></div>';
    echo '<div class="order-detail-summary-row text-success"><span>Total Savings</span><strong>'.$money($totalSavings).'</strong></div>';
    echo '<div class="order-detail-summary-row text-primary"><span><strong>Total Amount</strong></span><strong>'.$money($totalAmount).'</strong></div>';
    echo '</div></div>';
    echo '</div>';
    echo '</div>';
} catch (Exception $e) {
    echo '<div class="alert alert-danger">Error loading order details: '.htmlspecialchars($e->getMessage()).'</div>';
}
