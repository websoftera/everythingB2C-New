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
// Always fetch order before any output
$stmt = $pdo->prepare("SELECT o.*, u.name as customer_name, u.email as customer_email, u.phone as customer_phone, a.name as address_name, a.phone as address_phone, a.address_line1, a.address_line2, a.city, a.state, a.pincode FROM orders o LEFT JOIN users u ON o.user_id = u.id LEFT JOIN addresses a ON o.address_id = a.id WHERE o.id = ?");
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
    // Order Info
    echo '<div class="mb-3">';
    echo '<h5>Order #'.htmlspecialchars($order['order_number']).' <small class="text-muted">('.htmlspecialchars($order['tracking_id']).')</small></h5>';
    echo '<span class="badge bg-primary">'.ucfirst($order['status']).'</span> ';
    echo '<span class="badge bg-'.($order['payment_status']==='paid'?'success':($order['payment_status']==='pending'?'warning':'secondary')).'">'.ucfirst($order['payment_status']).'</span>';
    echo '<br><small class="text-muted">'.date('M d, Y g:i A', strtotime($order['created_at'])).'</small>';
    echo '</div>';
    // Customer Info
    echo '<div class="mb-3">';
    echo '<h6>Customer</h6>';
    echo '<p><strong>'.htmlspecialchars($order['customer_name']).'</strong><br>';
    echo 'Email: '.htmlspecialchars($order['customer_email']).'<br>';
    echo 'Phone: '.htmlspecialchars($order['customer_phone']).'</p>';
    echo '</div>';
    // Address
    echo '<div class="mb-3">';
    echo '<h6>Shipping Address</h6>';
    echo '<p><strong>'.htmlspecialchars($order['address_name']).'</strong><br>';
    echo htmlspecialchars($order['address_phone']).'<br>';
    echo htmlspecialchars($order['address_line1']).'<br>';
    if ($order['address_line2']) echo htmlspecialchars($order['address_line2']).'<br>';
    echo htmlspecialchars($order['city']).', '.htmlspecialchars($order['state']).' - '.htmlspecialchars($order['pincode']);
    echo '</p>';
    echo '</div>';
    // Items
    echo '<div class="mb-3">';
    echo '<h6>Order Items</h6>';
    // Reuse get_order_items.php logic
    $stmt = $pdo->prepare("SELECT oi.*, p.name as product_name, p.main_image, p.slug, p.hsn FROM order_items oi LEFT JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?");
    $stmt->execute([$order_id]);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $total_savings = 0;
    if (empty($items)) {
        echo '<p class="text-muted">No items found for this order.</p>';
    } else {
        echo '<div class="table-responsive">';
        echo '<table class="table table-sm">';
        echo '<thead><tr><th>Product</th><th>HSN</th><th>MRP</th><th>Selling Price</th><th>Qty</th><th>MRP Total</th><th>Selling Total</th><th>Savings</th></tr></thead>';
        echo '<tbody>';
        $total_mrp = 0;
        $total_selling = 0;
        $total_savings = 0;
        foreach ($items as $item) {
            $mrp = isset($item['mrp']) ? $item['mrp'] : 0;
            $selling = isset($item['selling_price']) ? $item['selling_price'] : $item['price'];
            $qty = $item['quantity'];
            $mrp_total = $mrp * $qty;
            $selling_total = $selling * $qty;
            $item_savings = ($mrp - $selling) * $qty;
            $total_mrp += $mrp_total;
            $total_selling += $selling_total;
            $total_savings += $item_savings;
            echo '<tr>';
            echo '<td>';
            if ($item['main_image']) {
                echo '<img src="../../'.htmlspecialchars($item['main_image']).'" alt="'.cleanProductName($item['product_name']).'" style="height:40px;width:40px;object-fit:cover;margin-right:8px;">';
            }
            echo '<div>';
            echo cleanProductName($item['product_name']);
            echo '<br><small class="text-muted">SKU: '.htmlspecialchars($item['slug']).'</small>';
            echo '</div>';
            echo '</td>';
            echo '<td>' . htmlspecialchars($item['hsn'] ?? '') . '</td>';
            echo '<td>₹'.number_format($mrp,0).'</td>';
            echo '<td>₹'.number_format($selling,0).'</td>';
            echo '<td>'.$qty.'</td>';
            echo '<td>₹'.number_format($mrp_total,0).'</td>';
            echo '<td>₹'.number_format($selling_total,0).'</td>';
            echo '<td class="text-success">₹'.number_format($item_savings,0).'</td>';
            echo '</tr>';
        }
        echo '</tbody>';
        echo '<tfoot>';
        echo '<tr><th colspan="4" class="text-end">Total MRP:</th><th>₹'.number_format($total_mrp,0).'</th><th></th><th></th></tr>';
        echo '<tr><th colspan="4" class="text-end">Total Selling:</th><th></th><th>₹'.number_format($total_selling,0).'</th><th></th></tr>';
        echo '<tr><th colspan="4" class="text-end">Total Savings:</th><th></th><th></th><th class="text-success">₹'.number_format($total_savings,0).'</th></tr>';
        echo '</tfoot>';
        echo '</table>';
        echo '</div>';
    }
    echo '</div>';
    // Order Summary
    echo '<div class="mb-3">';
    echo '<h6>Order Summary</h6>';
    echo '<p><strong>Total Amount:</strong> ₹'.number_format($order['total_amount'],0).'</p>';
    echo '<p class="text-success"><strong>Total Savings:</strong> ₹'.number_format($total_savings,0).'</p>';
    echo '<p><strong>Payment Method:</strong> '.htmlspecialchars($order['payment_method']).'</p>';
    // Show UPI details for direct payment
    if ($order['payment_method'] === 'direct_payment') {
        echo '<p><strong>UPI Transaction ID:</strong> ' . ($order['upi_transaction_id'] ? htmlspecialchars($order['upi_transaction_id']) : '<span class="text-danger">Not provided</span>') . '</p>';
        if ($order['upi_screenshot']) {
            echo '<p><strong>Payment Screenshot:</strong><br><img src="/' . htmlspecialchars($order['upi_screenshot']) . '" alt="UPI Screenshot" style="max-width:220px; border:1px solid #ccc; border-radius:6px;"></p>';
        }
    }
    echo '<p><strong>Status:</strong> '.ucfirst($order['status']).'</p>';
    echo '</div>';
} catch (Exception $e) {
    echo '<div class="alert alert-danger">Error loading order details: '.htmlspecialchars($e->getMessage()).'</div>';
} 