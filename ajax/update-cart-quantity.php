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
    $variationId = !empty($input['variation_id']) ? intval($input['variation_id']) : null;

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

        $variation = $variationId ? getProductVariationById($productId, $variationId) : null;
        $availableStock = $variation ? $variation['stock_quantity'] : $product['stock_quantity'];

        // Check if quantity exceeds stock
        if ($quantity > $availableStock) {
            throw new Exception('Quantity exceeds available stock');
        }

        // Check if product is already in cart
        ensureCartVariationSchema();
        if ($variationId) {
            $stmt = $pdo->prepare("SELECT id, quantity FROM cart WHERE user_id = ? AND product_id = ? AND variation_id = ?");
            $stmt->execute([$userId, $productId, $variationId]);
        } else {
            $stmt = $pdo->prepare("SELECT id, quantity FROM cart WHERE user_id = ? AND product_id = ? AND variation_id IS NULL");
            $stmt->execute([$userId, $productId]);
        }
        $cartItem = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($cartItem) {
            // Update existing cart item
            $stmt = $pdo->prepare("UPDATE cart SET quantity = ? WHERE id = ?");
            $stmt->execute([$quantity, $cartItem['id']]);

            echo json_encode([
                'success' => true,
                'message' => 'Cart quantity updated successfully',
                'quantity' => $quantity
            ]);
        } else {
            // Product not in cart, add it
            $stmt = $pdo->prepare("INSERT INTO cart (user_id, product_id, variation_id, quantity, created_at) VALUES (?, ?, ?, ?, NOW())");
            $stmt->execute([$userId, $productId, $variationId ?: null, $quantity]);

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

        $variation = $variationId ? getProductVariationById($productId, $variationId) : null;
        $availableStock = $variation ? $variation['stock_quantity'] : $product['stock_quantity'];

        // Check if quantity exceeds stock
        if ($quantity > $availableStock) {
            throw new Exception('Quantity exceeds available stock');
        }

        // Update session cart
        $_SESSION['cart'][cartSessionKey($productId, $variationId)] = $quantity;

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
