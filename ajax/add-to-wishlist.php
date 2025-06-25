<?php
session_start();
require_once '../includes/functions.php';

header('Content-Type: application/json');

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

if (!$input || !isset($input['product_id'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
    exit;
}

$productId = (int)$input['product_id'];

// Check if product exists and is active
$product = getProductById($productId);
if (!$product) {
    echo json_encode(['success' => false, 'message' => 'Product not found']);
    exit;
}

// Add to wishlist
if (isLoggedIn()) {
    $result = addToWishlist($_SESSION['user_id'], $productId);
} else {
    $result = addToSessionWishlist($productId);
}

if ($result) {
    echo json_encode(['success' => true, 'message' => 'Product added to wishlist']);
} else {
    echo json_encode(['success' => false, 'message' => 'Product already in wishlist']);
}
?> 