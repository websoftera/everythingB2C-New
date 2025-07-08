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
    $stmt = $pdo->prepare("SELECT oi.*, p.name as product_name, p.main_image, p.slug FROM order_items oi LEFT JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?");
    $stmt->execute([$order_id]);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (empty($items)) {
        echo '<p class="text-muted">No items found for this order.</p>';
    } else {
        echo '<div class="table-responsive">';
        echo '<table class="table table-sm">';
        echo '<thead><tr><th>Product</th><th>Price</th><th>Qty</th><th>Total</th></tr></thead>';
        echo '<tbody>';
        $total = 0;
        foreach ($items as $item) {
            $item_total = $item['price'] * $item['quantity'];
            $total += $item_total;
            echo '<tr>';
            echo '<td>';
            if ($item['main_image']) {
                echo '<img src="../../'.htmlspecialchars($item['main_image']).'" alt="'.htmlspecialchars($item['product_name']).'" style="height:40px;width:40px;object-fit:cover;margin-right:8px;">';
            }
            echo htmlspecialchars($item['product_name']);
            echo '<br><small class="text-muted">SKU: '.htmlspecialchars($item['slug']).'</small>';
            echo '</td>';
            echo '<td>₹'.number_format($item['price'],2).'</td>';
            echo '<td>'.$item['quantity'].'</td>';
            echo '<td><strong>₹'.number_format($item_total,2).'</strong></td>';
            echo '</tr>';
        }
        echo '</tbody>';
        echo '<tfoot><tr><th colspan="3" class="text-end">Total:</th><th>₹'.number_format($total,2).'</th></tr></tfoot>';
        echo '</table>';
        echo '</div>';
    }
    echo '</div>';
    // Order Summary
    echo '<div class="mb-3">';
    echo '<h6>Order Summary</h6>';
    echo '<p><strong>Total Amount:</strong> ₹'.number_format($order['total_amount'],2).'</p>';
    echo '<p><strong>Payment Method:</strong> '.htmlspecialchars($order['payment_method']).'</p>';
    echo '<p><strong>Status:</strong> '.ucfirst($order['status']).'</p>';
    echo '</div>';
} catch (Exception $e) {
    echo '<div class="alert alert-danger">Error loading order details: '.htmlspecialchars($e->getMessage()).'</div>';
} 