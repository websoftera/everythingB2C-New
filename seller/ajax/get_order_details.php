<?php
session_start();
require_once '../../config/database.php';
require_once '../../includes/functions.php';
require_once '../../includes/seller_functions.php';

if (!isset($_SESSION['seller_id'])) {
    http_response_code(403);
    exit('Unauthorized');
}

$sellerId = $_SESSION['seller_id'];
$order_id = intval($_GET['id'] ?? 0);

if (!$order_id) {
    echo '<div class="alert alert-warning">Invalid order ID.</div>';
    exit;
}

// SECURITY: Verify order contains seller's products
$stmt = $pdo->prepare("SELECT COUNT(*) FROM order_items oi 
                       JOIN products p ON oi.product_id = p.id 
                       WHERE oi.order_id = ? AND p.seller_id = ?");
$stmt->execute([$order_id, $sellerId]);
if ($stmt->fetchColumn() == 0) {
    echo '<div class="alert alert-danger">Access denied. This order does not contain your products.</div>';
    exit;
}

// Get order details
$stmt = $pdo->prepare("SELECT o.*, u.name as customer_name, u.email as customer_email, u.phone as customer_phone,
                       a.name as address_name, a.phone as address_phone, a.address_line1, a.address_line2, 
                       a.city, a.state, a.pincode,
                       os.name as status_name, os.color as status_color
                       FROM orders o 
                       LEFT JOIN users u ON o.user_id = u.id 
                       LEFT JOIN addresses a ON o.address_id = a.id
                       LEFT JOIN order_statuses os ON o.order_status_id = os.id
                       WHERE o.id = ?");
$stmt->execute([$order_id]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    if (isset($_GET['json']) && $_GET['json'] == '1') {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Order not found']);
        exit;
    }
    echo '<div class="alert alert-danger">Order not found.</div>';
    exit;
}

// JSON response for status update form
if (isset($_GET['json']) && $_GET['json'] == '1') {
    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'order' => $order]);
    exit;
}

try {
    // Order Info
    echo '<div class="mb-3">';
    echo '<h5>Order #' . htmlspecialchars($order['order_number']) . ' <small class="text-muted">(' . htmlspecialchars($order['tracking_id']) . ')</small></h5>';
    echo '<span class="badge" style="background-color: ' . htmlspecialchars($order['status_color']) . '; color: white;">' . htmlspecialchars($order['status_name']) . '</span> ';
    echo '<span class="badge bg-' . ($order['payment_status'] === 'paid' ? 'success' : ($order['payment_status'] === 'pending' ? 'warning' : 'secondary')) . '">' . ucfirst($order['payment_status']) . '</span>';
    echo '<br><small class="text-muted">' . date('M d, Y g:i A', strtotime($order['created_at'])) . '</small>';
    echo '</div>';
    
    // Customer Info
    echo '<div class="mb-3">';
    echo '<h6>Customer Information</h6>';
    echo '<p><strong>' . htmlspecialchars($order['customer_name']) . '</strong><br>';
    echo '<i class="fas fa-envelope"></i> ' . htmlspecialchars($order['customer_email']) . '<br>';
    echo '<i class="fas fa-phone"></i> ' . htmlspecialchars($order['customer_phone']) . '</p>';
    echo '</div>';
    
    // Shipping Address
    echo '<div class="mb-3">';
    echo '<h6>Shipping Address</h6>';
    echo '<p><strong>' . htmlspecialchars($order['address_name']) . '</strong><br>';
    echo htmlspecialchars($order['address_phone']) . '<br>';
    echo htmlspecialchars($order['address_line1']) . '<br>';
    if ($order['address_line2']) echo htmlspecialchars($order['address_line2']) . '<br>';
    echo htmlspecialchars($order['city']) . ', ' . htmlspecialchars($order['state']) . ' - ' . htmlspecialchars($order['pincode']);
    echo '</p>';
    echo '</div>';
    
    // Order Items - SECURITY: Only show items from seller's products
    echo '<div class="mb-3">';
    echo '<h6>Your Products in This Order</h6>';
    
    $stmt = $pdo->prepare("SELECT oi.*, p.name as product_name, p.main_image, p.slug, p.hsn 
                           FROM order_items oi 
                           JOIN products p ON oi.product_id = p.id 
                           WHERE oi.order_id = ? AND p.seller_id = ?");
    $stmt->execute([$order_id, $sellerId]);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($items)) {
        echo '<p class="text-muted">No items found.</p>';
    } else {
        echo '<div class="table-responsive">';
        echo '<table class="table table-sm table-bordered">';
        echo '<thead><tr><th>Product</th><th>HSN</th><th>MRP</th><th>Selling Price</th><th>Qty</th><th>Total</th><th>GST</th></tr></thead>';
        echo '<tbody>';
        
        $seller_total = 0;
        $seller_gst = 0;
        
        foreach ($items as $item) {
            $mrp = isset($item['mrp']) ? $item['mrp'] : 0;
            $selling = isset($item['selling_price']) ? $item['selling_price'] : $item['price'];
            $qty = $item['quantity'];
            $item_total = $selling * $qty;
            $item_gst = $item['gst_amount'] ?? 0;
            
            $seller_total += $item_total;
            $seller_gst += $item_gst;
            
            echo '<tr>';
            echo '<td>';
            if ($item['main_image']) {
                $imgPath = $item['main_image'];
                if (strpos($imgPath, 'uploads/') === 0) {
                    $imgPath = '../../' . $imgPath;
                } elseif (strpos($imgPath, '/') !== 0 && strpos($imgPath, 'http') !== 0) {
                    $imgPath = '../../uploads/' . $imgPath;
                } else {
                    $imgPath = '../../' . $imgPath;
                }
                echo '<img src="' . htmlspecialchars($imgPath) . '" alt="Product" style="height:40px;width:40px;object-fit:cover;margin-right:8px;border-radius:4px;">';
            }
            echo '<strong>' . htmlspecialchars($item['product_name']) . '</strong>';
            echo '<br><small class="text-muted">SKU: ' . htmlspecialchars($item['slug']) . '</small>';
            echo '</td>';
            echo '<td>' . htmlspecialchars($item['hsn'] ?? 'N/A') . '</td>';
            echo '<td>₹' . number_format($mrp, 2) . '</td>';
            echo '<td>₹' . number_format($selling, 2) . '</td>';
            echo '<td>' . $qty . '</td>';
            echo '<td><strong>₹' . number_format($item_total, 2) . '</strong></td>';
            echo '<td>₹' . number_format($item_gst, 2) . '</td>';
            echo '</tr>';
        }
        
        echo '</tbody>';
        echo '<tfoot>';
        echo '<tr><th colspan="5" class="text-end">Your Items Subtotal:</th><th colspan="2"><strong>₹' . number_format($seller_total, 2) . '</strong></th></tr>';
        echo '<tr><th colspan="5" class="text-end">GST on Your Items:</th><th colspan="2"><strong>₹' . number_format($seller_gst, 2) . '</strong></th></tr>';
        echo '</tfoot>';
        echo '</table>';
        echo '</div>';
        
        echo '<div class="alert alert-info">';
        echo '<i class="fas fa-info-circle"></i> ';
        echo '<strong>Note:</strong> This shows only YOUR products from this order. ';
        echo 'The order may contain items from other sellers.';
        echo '</div>';
    }
    echo '</div>';
    
    // Payment Info
    echo '<div class="mb-3">';
    echo '<h6>Payment Information</h6>';
    echo '<p><strong>Payment Method:</strong> ' . strtoupper(str_replace('_', ' ', $order['payment_method'])) . '</p>';
    
    if ($order['payment_method'] === 'direct_payment') {
        echo '<p><strong>UPI Transaction ID:</strong> ' . ($order['upi_transaction_id'] ? htmlspecialchars($order['upi_transaction_id']) : '<span class="text-danger">Not provided</span>') . '</p>';
        if ($order['upi_screenshot']) {
            echo '<p><strong>Payment Screenshot:</strong><br>';
            echo '<img src="../../' . htmlspecialchars($order['upi_screenshot']) . '" alt="UPI Screenshot" style="max-width:200px; border:1px solid #ccc; border-radius:6px;">';
            echo '</p>';
        }
    }
    
    echo '<p><strong>Payment Status:</strong> <span class="badge bg-' . ($order['payment_status'] === 'paid' ? 'success' : 'warning') . '">' . ucfirst($order['payment_status']) . '</span></p>';
    echo '</div>';
    
    // Overall Order Summary
    echo '<div class="mb-3">';
    echo '<h6>Overall Order Summary</h6>';
    echo '<table class="table table-sm">';
    echo '<tr><th>Subtotal:</th><td>₹' . number_format($order['subtotal'] ?? 0, 2) . '</td></tr>';
    echo '<tr><th>GST:</th><td>₹' . number_format($order['gst_amount'] ?? 0, 2) . '</td></tr>';
    echo '<tr><th>Shipping:</th><td>₹' . number_format($order['shipping_charge'] ?? 0, 2) . '</td></tr>';
    echo '<tr><th><strong>Total Amount:</strong></th><td><strong>₹' . number_format($order['total_amount'], 2) . '</strong></td></tr>';
    echo '</table>';
    echo '</div>';
    
} catch (Exception $e) {
    echo '<div class="alert alert-danger">Error loading order details: ' . htmlspecialchars($e->getMessage()) . '</div>';
}
?>
