<?php
session_start();
require_once '../includes/functions.php';

header('Content-Type: application/json');

$productId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($productId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid product ID']);
    exit;
}

ensureProductPackageQuantitySchema($pdo);

$stmt = $pdo->prepare("SELECT id, package_quantity, stock_quantity, max_quantity_per_order, mrp, selling_price FROM products WHERE id = ? AND is_active = 1");
$stmt->execute([$productId]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    echo json_encode(['success' => false, 'message' => 'Product not found']);
    exit;
}

echo json_encode([
    'success' => true,
    'product' => [
        'id' => (int)$product['id'],
        'package_quantity' => normalizePackageQuantity($product['package_quantity'] ?? 1),
        'stock_quantity' => (int)$product['stock_quantity'],
        'max_quantity_per_order' => $product['max_quantity_per_order'] !== null ? (int)$product['max_quantity_per_order'] : null,
        'mrp' => (float)$product['mrp'],
        'selling_price' => (float)$product['selling_price']
    ]
]);
?>
