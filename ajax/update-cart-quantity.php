<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');

try {
    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($input['product_id']) || !isset($input['quantity'])) {
        throw new Exception('Missing required parameters');
    }
    
    $productId = intval($input['product_id']);
    $quantity = intval($input['quantity']);
    
    // Validate quantity
    if ($quantity < 1) {
        throw new Exception('Quantity must be at least 1');
    }
    
    // Check if user is logged in
    if (isLoggedIn()) {
        $userId = $_SESSION['user_id'];
        
        // Check if product exists and get stock info
        $stmt = $pdo->prepare("SELECT stock_quantity FROM products WHERE id = ? AND is_active = 1");
        $stmt->execute([$productId]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$product) {
            throw new Exception('Product not found');
        }
        
        // Check if quantity exceeds stock
        if ($quantity > $product['stock_quantity']) {
            throw new Exception('Quantity exceeds available stock');
        }
        
        // Check if product is already in cart
        $stmt = $pdo->prepare("SELECT id, quantity FROM cart WHERE user_id = ? AND product_id = ?");
        $stmt->execute([$userId, $productId]);
        $cartItem = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($cartItem) {
            // Update existing cart item
            $stmt = $pdo->prepare("UPDATE cart SET quantity = ?, updated_at = NOW() WHERE id = ?");
            $stmt->execute([$quantity, $cartItem['id']]);
            
            echo json_encode([
                'success' => true,
                'message' => 'Cart quantity updated successfully',
                'quantity' => $quantity
            ]);
        } else {
            // Product not in cart, add it
            $stmt = $pdo->prepare("INSERT INTO cart (user_id, product_id, quantity, created_at, updated_at) VALUES (?, ?, ?, NOW(), NOW())");
            $stmt->execute([$userId, $productId, $quantity]);
            
            echo json_encode([
                'success' => true,
                'message' => 'Product added to cart',
                'quantity' => $quantity
            ]);
        }
    } else {
        // Handle session cart for non-logged in users
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
        
        // Check if product exists and get stock info
        $stmt = $pdo->prepare("SELECT stock_quantity FROM products WHERE id = ? AND is_active = 1");
        $stmt->execute([$productId]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$product) {
            throw new Exception('Product not found');
        }
        
        // Check if quantity exceeds stock
        if ($quantity > $product['stock_quantity']) {
            throw new Exception('Quantity exceeds available stock');
        }
        
        // Update session cart
        $_SESSION['cart'][$productId] = $quantity;
        
        echo json_encode([
            'success' => true,
            'message' => 'Cart quantity updated successfully',
            'quantity' => $quantity
        ]);
    }
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
