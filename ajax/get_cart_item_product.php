<?php
session_start();
require_once '../config/database.php';

header('Content-Type: application/json');

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

if (!$input || !isset($input['cart_id'])) {
    echo json_encode(['error' => 'Invalid input']);
    exit;
}

$cartId = (int)$input['cart_id'];

try {
    if (isset($_SESSION['user_id'])) {
        // Logged-in user: get from cart table
        $stmt = $pdo->prepare("SELECT product_id FROM cart WHERE id = ? AND user_id = ?");
        $stmt->execute([$cartId, $_SESSION['user_id']]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($row) {
            echo json_encode(['product_id' => $row['product_id']]);
        } else {
            echo json_encode(['error' => 'Cart item not found']);
        }
    } else {
        // Guest user: cart_id is the product_id
        echo json_encode(['product_id' => $cartId]);
    }
} catch (Exception $e) {
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?> 