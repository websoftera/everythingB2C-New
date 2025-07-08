<?php
session_start();
require_once '../../config/database.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    http_response_code(403);
    exit('Unauthorized');
}

$order_id = intval($_GET['id'] ?? 0);

if (!$order_id) {
    echo '<p class="text-muted">Invalid order ID.</p>';
    exit;
}

try {
    // Get order items
    $stmt = $pdo->prepare("SELECT oi.*, p.name as product_name, p.main_image, p.slug 
                          FROM order_items oi 
                          LEFT JOIN products p ON oi.product_id = p.id 
                          WHERE oi.order_id = ?");
    $stmt->execute([$order_id]);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($items)) {
        echo '<p class="text-muted">No items found for this order.</p>';
        exit;
    }
    
    echo '<div class="table-responsive">';
    echo '<table class="table table-sm">';
    echo '<thead><tr><th>Product</th><th>Price</th><th>Quantity</th><th>Total</th></tr></thead>';
    echo '<tbody>';
    
    $total = 0;
    foreach ($items as $item) {
        $item_total = $item['price'] * $item['quantity'];
        $total += $item_total;
        
        echo '<tr>';
        echo '<td>';
        echo '<div class="d-flex align-items-center">';
        if ($item['main_image']) {
            echo '<img src="../../' . htmlspecialchars($item['main_image']) . '" alt="' . htmlspecialchars($item['product_name']) . '" class="img-preview me-2">';
        }
        echo '<div>';
        echo '<strong>' . htmlspecialchars($item['product_name']) . '</strong><br>';
        echo '<small class="text-muted">SKU: ' . htmlspecialchars($item['slug']) . '</small>';
        echo '</div>';
        echo '</div>';
        echo '</td>';
        echo '<td>₹' . number_format($item['price'], 2) . '</td>';
        echo '<td>' . $item['quantity'] . '</td>';
        echo '<td><strong>₹' . number_format($item_total, 2) . '</strong></td>';
        echo '</tr>';
    }
    
    echo '</tbody>';
    echo '<tfoot><tr><th colspan="3" class="text-end">Total:</th><th>₹' . number_format($total, 2) . '</th></tr></tfoot>';
    echo '</table>';
    echo '</div>';
    
} catch (Exception $e) {
    echo '<p class="text-danger">Error loading order items: ' . htmlspecialchars($e->getMessage()) . '</p>';
}
?> 