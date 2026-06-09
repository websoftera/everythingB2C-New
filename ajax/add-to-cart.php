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

$variationData = getProductVariationData($productId);
$variation = null;
if ($variationData['has_variations']) {
    if (!$variationId) {
        echo json_encode(['success' => false, 'message' => 'Please select a variant']);
        exit;
    }

    $variation = getProductVariationById($productId, $variationId);
    if (!$variation) {
        echo json_encode(['success' => false, 'message' => 'Selected variant is not available']);
        exit;
    }
}

$availableStock = (int)$product['stock_quantity'];
$packageQuantity = normalizePackageQuantity($product['package_quantity'] ?? 1);

if (!isValidPackageQuantity($quantity, $packageQuantity)) {
    echo json_encode(packageQuantityErrorResponse($quantity, $packageQuantity));
    exit;
}

// Check stock
if ($availableStock < $quantity) {
    echo json_encode(['success' => false, 'message' => 'Insufficient stock']);
    exit;
}

// Check max quantity per order
if ($product['max_quantity_per_order'] !== null && $quantity > $product['max_quantity_per_order']) {
    echo json_encode([
        'success' => false,
        'message' => "Maximum quantity allowed for this product is {$product['max_quantity_per_order']}"
    ]);
    exit;
}

// Check if user is logged in
if (isLoggedIn()) {
    $userId = $_SESSION['user_id'];
    $result = addToCart($userId, $productId, $quantity, $variationId);
} else {
    $result = addToSessionCart($productId, $quantity, $variationId);
}

if ($result) {
    echo json_encode(['success' => true, 'message' => 'Product added to cart']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to add product to cart']);
}
?>
