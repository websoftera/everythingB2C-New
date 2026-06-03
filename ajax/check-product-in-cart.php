<?php
session_start();
require_once '../includes/functions.php';

header('Content-Type: application/json');

if (!isset($_GET['product_id'])) {
    echo json_encode(['success' => false, 'message' => 'Product ID is required']);
    exit;
}

$productId = intval($_GET['product_id']);
$variationId = isset($_GET['variation_id']) && $_GET['variation_id'] !== '' ? intval($_GET['variation_id']) : null;
$inCart = false;
$quantity = 0;

try {
    if (isLoggedIn()) {
        // Check database cart
        global $pdo;
        ensureCartVariationSchema();
        if ($variationId) {
            $stmt = $pdo->prepare("SELECT quantity FROM cart WHERE user_id = ? AND product_id = ? AND variation_id = ?");
            $stmt->execute([$_SESSION['user_id'], $productId, $variationId]);
        } else {
            $stmt = $pdo->prepare("SELECT quantity FROM cart WHERE user_id = ? AND product_id = ? AND variation_id IS NULL");
            $stmt->execute([$_SESSION['user_id'], $productId]);
        }
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            $inCart = true;
            $quantity = (int)$result['quantity'];
        }
    } else {
        // Check session cart
        $cartKey = cartSessionKey($productId, $variationId);
        if (isset($_SESSION['cart'][$cartKey])) {
            $inCart = true;
            $quantity = (int)$_SESSION['cart'][$cartKey];
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
