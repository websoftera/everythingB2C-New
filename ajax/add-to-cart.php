<?php
session_start();
require_once '../includes/functions.php';

header('Content-Type: application/json');

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

if (!$input || !isset($input['product_id']) || !isset($input['quantity'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
    exit;
}

$productId = (int)$input['product_id'];
$quantity = (int)$input['quantity'];
$variationId = !empty($input['variation_id']) ? (int)$input['variation_id'] : null;
$selectedAttributes = normalizeCartSelectedAttributes($input['selected_attributes'] ?? []);

// Validate quantity
if ($quantity < 1) {
    echo json_encode(['success' => false, 'message' => 'Invalid quantity']);
    exit;
}

// Check if product exists and is active
$product = getProductById($productId);
if (!$product) {
    echo json_encode(['success' => false, 'message' => 'Product not found']);
    exit;
}

$packageQuantity = normalizePackageQuantity($product['package_quantity'] ?? 1);
$requestedStockQuantity = getCartItemStockQuantity([
    'quantity' => $quantity,
    'package_quantity' => $packageQuantity
]);

$variationValidation = validateProductVariationSelection($productId, $variationId, $requestedStockQuantity);
if (!$variationValidation['success']) {
    echo json_encode($variationValidation);
    exit;
}

$variation = $variationValidation['variation'] ?? null;
$availableStock = $variation
    ? min((int)$product['stock_quantity'], (int)$variation['stock_quantity'])
    : (int)$product['stock_quantity'];

if (!isValidPackageQuantity($quantity, $packageQuantity)) {
    echo json_encode(packageQuantityErrorResponse($quantity, $packageQuantity));
    exit;
}

// Check stock
if ($availableStock < $requestedStockQuantity) {
    echo json_encode(['success' => false, 'message' => 'Insufficient stock']);
    exit;
}

// Check max quantity per order
if ($product['max_quantity_per_order'] !== null && $requestedStockQuantity > (int)$product['max_quantity_per_order']) {
    echo json_encode([
        'success' => false,
        'message' => "Maximum quantity allowed for this product is {$product['max_quantity_per_order']}"
    ]);
    exit;
}

// Check if user is logged in
if (isLoggedIn()) {
    $userId = $_SESSION['user_id'];
    $result = addToCart($userId, $productId, $quantity, $variationId, $selectedAttributes);
} else {
    $result = addToSessionCart($productId, $quantity, $variationId, $selectedAttributes);
}

if ($result) {
    if (isLoggedIn()) {
        $cartCount = count(getCartItems($_SESSION['user_id']));
    } else {
        $cartCount = count(getCartItems());
    }
    echo json_encode([
        'success' => true,
        'message' => 'Product added to cart',
        'cart_count' => $cartCount
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to add product to cart']);
}
?>
