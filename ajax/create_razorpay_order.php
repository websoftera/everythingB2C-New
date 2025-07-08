<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once '../includes/functions.php';
require_once __DIR__ . '/../vendor/autoload.php';
use Razorpay\Api\Api;

// Force login
if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);
$orderId = isset($input['order_id']) ? intval($input['order_id']) : 0;
$amount = isset($input['amount']) ? intval($input['amount']) : 0;

if (!$orderId || !$amount) {
    echo json_encode(['success' => false, 'message' => 'Invalid parameters']);
    exit;
}

// Verify order belongs to user
$userId = $_SESSION['user_id'];
$order = getOrderById($orderId);
if (!$order || $order['user_id'] != $userId) {
    echo json_encode(['success' => false, 'message' => 'Invalid order']);
    exit;
}

// Check if order is pending payment
if ($order['payment_status'] !== 'pending') {
    echo json_encode(['success' => false, 'message' => 'Order already processed']);
    exit;
}

try {
    // TODO: Replace with your Razorpay credentials
    $keyId = 'rzp_test_2uufCk4Q1h1NDn';
    $keySecret = 'AwxauKLBobgU0EcBftpm1ssd';

    $api = new Api($keyId, $keySecret);
    
    // Create Razorpay order
    $razorpayOrder = $api->order->create([
        'receipt' => 'order_rcptid_' . time(),
        'amount' => $amount, // Amount in paise
        'currency' => 'INR'
    ]);
    
    $razorpayOrderId = $razorpayOrder['id'];
    
    // Update order with Razorpay order ID
    $stmt = $pdo->prepare("UPDATE orders SET razorpay_order_id = ? WHERE id = ?");
    $stmt->execute([$razorpayOrderId, $orderId]);
    
    echo json_encode([
        'success' => true,
        'key_id' => $keyId,
        'amount' => $amount,
        'currency' => 'INR',
        'razorpay_order_id' => $razorpayOrderId
    ]);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error creating payment order: ' . $e->getMessage()]);
}
?>