<?php
session_start();
require_once '../config/database.php';

header('Content-Type: application/json');

if (!isset($_GET['cart_id'])) {
    echo json_encode(['success' => false, 'message' => 'Cart ID is required']);
    exit;
}

$cartId = intval($_GET['cart_id']);

try {
    // Get product ID from cart item
    $stmt = $pdo->prepare("SELECT product_id FROM cart WHERE id = ?");
    $stmt->execute([$cartId]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($result) {
        echo json_encode([
            'success' => true, 
            'product_id' => $result['product_id']
        ]);
    } else {
        echo json_encode([
            'success' => false, 
            'message' => 'Cart item not found'
        ]);
    }
} catch (PDOException $e) {
    echo json_encode([
        'success' => false, 
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
?> 