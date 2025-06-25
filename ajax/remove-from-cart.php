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
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
    exit;
}

$cartId = (int)$input['cart_id'];

// Remove cart item
if (isLoggedIn()) {
    global $pdo;
    $stmt = $pdo->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?");
    $result = $stmt->execute([$cartId, $_SESSION['user_id']]);
} else {
    $result = removeFromSessionCart($cartId);
}

if ($result) {
    echo json_encode(['success' => true, 'message' => 'Item removed from cart']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to remove item']);
}
?> 