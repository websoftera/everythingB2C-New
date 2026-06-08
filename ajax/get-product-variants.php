<?php
session_start();
require_once '../includes/functions.php';

header('Content-Type: application/json');

$productId = isset($_GET['product_id']) ? (int)$_GET['product_id'] : 0;
if ($productId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid product']);
    exit;
}

$product = getProductById($productId);
if (!$product) {
    echo json_encode(['success' => false, 'message' => 'Product not found']);
    exit;
}

$variationData = getProductVariationData($productId);
if ($variationData['has_variations']) {
    $product = applyDisplayVariationPrice($product);
}

echo json_encode([
    'success' => true,
    'product' => [
        'id' => (int)$product['id'],
        'name' => cleanProductName($product['name']),
        'image' => $product['main_image'] ?: 'uploads/products/blank-img.webp',
        'mrp' => (float)$product['mrp'],
        'selling_price' => (float)$product['selling_price'],
        'stock_quantity' => (int)$product['stock_quantity']
    ],
    'has_variations' => $variationData['has_variations'],
    'attributes' => $variationData['attributes'],
    'variations' => $variationData['variations']
]);
?>
