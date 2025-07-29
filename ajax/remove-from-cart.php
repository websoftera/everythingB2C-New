<?php
session_start();
require_once '../includes/functions.php';

header('Content-Type: application/json');

// Remove login check for guests
// if (!isLoggedIn()) {
//     echo json_encode(['success' => false, 'message' => 'Please login first']);
//     exit;
// }

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

if (!$input || !isset($input['cart_id'])) {
    session_write_close();
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
    exit;
}

$cartId = (int)$input['cart_id'];
$productId = null;

// Remove cart item
if (isLoggedIn()) {
    global $pdo;
    // Get product_id before deleting
    $stmt = $pdo->prepare("SELECT product_id FROM cart WHERE id = ? AND user_id = ?");
    $stmt->execute([$cartId, $_SESSION['user_id']]);
    $cartItem = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($cartItem) {
        $productId = $cartItem['product_id'];
    }
    
    $stmt = $pdo->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?");
    $result = $stmt->execute([$cartId, $_SESSION['user_id']]);
} else {
    $productId = $cartId; // For guests, cartId is productId
    $result = removeFromSessionCart($cartId);
}

if ($result) {
    session_write_close();
    echo json_encode(['success' => true, 'message' => 'Item removed from cart', 'product_id' => $productId]);
} else {
    session_write_close();
    echo json_encode(['success' => false, 'message' => 'Failed to remove item']);
}
?> 