<?php
session_start();
require_once '../includes/functions.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
$quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;

if ($product_id <= 0) {
    echo json_encode(['error' => 'Invalid product ID']);
    exit;
}

try {
    ensureProductPackageQuantitySchema($pdo);
    $stmt = $pdo->prepare("SELECT id, name, stock_quantity, package_quantity, max_quantity_per_order FROM products WHERE id = ? AND is_active = 1");
    $stmt->execute([$product_id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$product) {
        echo json_encode(['error' => 'Product not found or inactive']);
        exit;
    }
    
    $max_quantity = $product['max_quantity_per_order'];
    $stock_quantity = $product['stock_quantity'];
    $package_quantity = normalizePackageQuantity($product['package_quantity'] ?? 1);
    $requested_stock_quantity = getCartItemStockQuantity([
        'quantity' => $quantity,
        'package_quantity' => $package_quantity
    ]);

    if (!isValidPackageQuantity($quantity, $package_quantity)) {
        echo json_encode(packageQuantityErrorResponse($quantity, $package_quantity));
        exit;
    }
    
    // Check if quantity exceeds max_quantity_per_order
    if ($max_quantity !== null && $requested_stock_quantity > (int)$max_quantity) {
        echo json_encode([
            'error' => 'Maximum quantity exceeded',
            'max_quantity' => (int)$max_quantity * $package_quantity,
            'message' => "Maximum quantity allowed for this product is {$max_quantity}"
        ]);
        exit;
    }
    
    // Always return max_quantity info for display purposes
    $response = [
        'success' => true,
        'max_quantity' => $max_quantity,
        'stock_quantity' => $stock_quantity,
        'package_quantity' => $package_quantity
    ];
    
    // Check if quantity exceeds stock
    if ($requested_stock_quantity > $stock_quantity) {
        echo json_encode([
            'error' => 'Insufficient stock',
            'stock_quantity' => $stock_quantity,
            'max_quantity' => $stock_quantity * $package_quantity,
            'message' => "Only {$stock_quantity} package(s) available in stock"
        ]);
        exit;
    }
    
    // If user is logged in, include current cart quantity for callers that need it.
    // The submitted quantity is the final desired order quantity, not an extra
    // amount to add on top of what is already in cart.
    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
        $stmt = $pdo->prepare("SELECT quantity FROM cart WHERE user_id = ? AND product_id = ?");
        $stmt->execute([$user_id, $product_id]);
        $cart_item = $stmt->fetch(PDO::FETCH_ASSOC);

        $current_cart_quantity = $cart_item ? $cart_item['quantity'] : 0;
        $response['current_cart_quantity'] = (int)$current_cart_quantity;
    }
    
    echo json_encode($response);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>
