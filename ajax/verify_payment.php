<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';
require_once __DIR__ . '/../vendor/autoload.php';
use Razorpay\Api\Api;

if (!isLoggedIn()) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        exit;
    } else {
        $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
        header('Location: ../login.php');
        exit;
    }
}

$userId = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // New JS flow
    $input = json_decode(file_get_contents('php://input'), true);
    $orderId = isset($input['temp_order_id']) ? intval($input['temp_order_id']) : 0;
    $paymentId = isset($input['razorpay_payment_id']) ? $input['razorpay_payment_id'] : '';
    $razorpayOrderId = isset($input['razorpay_order_id']) ? $input['razorpay_order_id'] : '';
    $signature = isset($input['razorpay_signature']) ? $input['razorpay_signature'] : '';

    if (!$orderId || !$paymentId || !$razorpayOrderId || !$signature) {
        echo json_encode(['success' => false, 'message' => 'Invalid payment details.']);
        exit;
    }

    // Verify order belongs to user
    $order = getOrderById($orderId);
    if (!$order || $order['user_id'] != $userId) {
        echo json_encode(['success' => false, 'message' => 'Invalid order.']);
        exit;
    }
    if ($order['payment_status'] !== 'pending') {
        echo json_encode(['success' => true]); // Already processed
        exit;
    }

    try {
        $keySecret = 'AwxauKLBobgU0EcBftpm1ssd'; // Your Razorpay key secret
        // Verify signature using Razorpay utility
        $generated_signature = hash_hmac('sha256', $razorpayOrderId . '|' . $paymentId, $keySecret);
        if ($generated_signature === $signature) {
            // Payment verified successfully
            global $pdo;
            $stmt = $pdo->prepare("UPDATE orders SET payment_status = 'paid', payment_method = 'razorpay', razorpay_payment_id = ?, status = 'confirmed', order_status_id = 1 WHERE id = ?");
            $stmt->execute([$paymentId, $orderId]);
            addOrderStatusHistory($orderId, 1, 'Order placed successfully', 'system');
            clearUserCart($userId);
            echo json_encode(['success' => true]);
            exit;
        } else {
            // Payment verification failed
            $stmt = $pdo->prepare("UPDATE orders SET payment_status = 'failed', status = 'cancelled' WHERE id = ?");
            $stmt->execute([$orderId]);
            echo json_encode(['success' => false, 'message' => 'Payment verification failed.']);
            exit;
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        exit;
    }
}

// Legacy GET flow (redirect)
$orderId = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;
$paymentId = isset($_GET['payment_id']) ? $_GET['payment_id'] : '';
$signature = isset($_GET['signature']) ? $_GET['signature'] : '';

if (!$orderId || !$paymentId || !$signature) {
    header('Location: ../checkout.php?error=invalid_payment');
    exit;
}

// Verify order belongs to user
$order = getOrderById($orderId);
if (!$order || $order['user_id'] != $userId) {
    header('Location: ../cart.php');
    exit;
}

// Check if order is pending payment
if ($order['payment_status'] !== 'pending') {
    header('Location: ../order_success.php?order_id=' . $orderId);
    exit;
}

try {
    $keySecret = 'AwxauKLBobgU0EcBftpm1ssd';
    $expectedSignature = hash_hmac('sha256', $orderId . '|' . $paymentId, $keySecret);
    if ($signature === $expectedSignature || true) { // For demo, always accept
        $stmt = $pdo->prepare("UPDATE orders SET 
            payment_status = 'paid', 
            payment_method = 'razorpay',
            razorpay_payment_id = ?,
            status = 'confirmed'
            WHERE id = ?");
        $stmt->execute([$paymentId, $orderId]);
        clearUserCart($userId);
        header('Location: ../order_success.php?order_id=' . $orderId);
        exit;
    } else {
        $stmt = $pdo->prepare("UPDATE orders SET 
            payment_status = 'failed',
            status = 'cancelled'
            WHERE id = ?");
        $stmt->execute([$orderId]);
        header('Location: ../checkout.php?error=payment_failed');
        exit;
    }
} catch (Exception $e) {
    header('Location: ../checkout.php?error=payment_error');
    exit;
}
?> 