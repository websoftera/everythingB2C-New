<?php
ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);

session_start();
require_once '../includes/functions.php';

header('Content-Type: application/json');

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

if (!$input || !isset($input['cart_id']) || !isset($input['quantity'])) {
    error_log('Invalid input: ' . print_r($input, true));
    session_write_close();
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
    exit;
}

$cartId = (int)$input['cart_id'];
$quantity = (int)$input['quantity'];

// Validate quantity
if ($quantity < 1) {
    session_write_close();
    echo json_encode(['success' => false, 'message' => 'Invalid quantity']);
    exit;
}

// --- MAX QUANTITY CHECK ---
// For logged-in users, get product_id from cart table
// For guests, cartId is productId
$productId = null;
if (isset($_SESSION['user_id'])) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT product_id FROM cart WHERE id = ? AND user_id = ?");
    $stmt->execute([$cartId, $_SESSION['user_id']]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row) {
        $productId = $row['product_id'];
    }
} else {
    // For guests, cartId is productId
    $productId = $cartId;
}
if ($productId) {
    $product = getProductById($productId);
    if ($product && $product['max_quantity_per_order'] !== null && $quantity > $product['max_quantity_per_order']) {
        session_write_close();
        echo json_encode(['success' => false, 'message' => "Maximum quantity allowed for this product is {$product['max_quantity_per_order']}"]);
        exit;
    }
}
// --- END MAX QUANTITY CHECK ---

if (isset($_SESSION['user_id'])) {
    // Logged-in user: update DB cart
    global $pdo;
    $stmt = $pdo->prepare("UPDATE cart SET quantity = ? WHERE id = ? AND user_id = ?");
    $result = $stmt->execute([$quantity, $cartId, $_SESSION['user_id']]);
    if ($result) {
        session_write_close();
        echo json_encode(['success' => true, 'message' => 'Cart updated']);
    } else {
        error_log('DB update failed for cart_id ' . $cartId . ', user_id ' . $_SESSION['user_id']);
        session_write_close();
        echo json_encode(['success' => false, 'message' => 'Failed to update cart']);
    }
} else {
    // Guest: update session cart
    if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
        error_log('Session cart not found or not array: ' . print_r($_SESSION['cart'] ?? null, true));
        session_write_close();
        echo json_encode(['success' => false, 'message' => 'Cart not found']);
        exit;
    }
    error_log('Session cart before update: ' . print_r($_SESSION['cart'], true));
    if (isset($_SESSION['cart'][$cartId])) {
        $_SESSION['cart'][$cartId] = $quantity;
        error_log('Session cart after update: ' . print_r($_SESSION['cart'], true));
        session_write_close();
        echo json_encode(['success' => true, 'message' => 'Cart updated']);
    } else {
        error_log('Cart item not found for id: ' . $cartId);
        session_write_close();
        echo json_encode(['success' => false, 'message' => 'Cart item not found']);
    }
}
?> 