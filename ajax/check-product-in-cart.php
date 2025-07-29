<?php
session_start();
require_once '../includes/functions.php';

header('Content-Type: application/json');

if (!isset($_GET['product_id'])) {
    echo json_encode(['success' => false, 'message' => 'Product ID is required']);
    exit;
}

$productId = intval($_GET['product_id']);
$inCart = false;
$quantity = 0;

try {
    if (isLoggedIn()) {
        // Check database cart
        global $pdo;
        $stmt = $pdo->prepare("SELECT quantity FROM cart WHERE user_id = ? AND product_id = ?");
        $stmt->execute([$_SESSION['user_id'], $productId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result) {
            $inCart = true;
            $quantity = (int)$result['quantity'];
        }
    } else {
        // Check session cart
        if (isset($_SESSION['cart'][$productId])) {
            $inCart = true;
            $quantity = (int)$_SESSION['cart'][$productId];
        }
    }
    
    echo json_encode([
        'success' => true,
        'in_cart' => $inCart,
        'quantity' => $quantity
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error checking cart status: ' . $e->getMessage()
    ]);
}
?> 