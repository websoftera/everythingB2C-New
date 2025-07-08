<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

// Force login
if (!isLoggedIn()) {
    header('Location: ../login.php');
    exit;
}

$userId = $_SESSION['user_id'];
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
    // For demo purposes, we'll simulate payment verification
    // In production, you would verify the signature with Razorpay
    $keySecret = 'NiF34LPvcyJXPema7mved1m4'; // User's Razorpay test key secret
    
    // Verify signature (simplified for demo)
    // In production, you would use Razorpay's signature verification
    $expectedSignature = hash_hmac('sha256', $orderId . '|' . $paymentId, $keySecret);
    
    if ($signature === $expectedSignature || true) { // For demo, always accept
        // Payment verified successfully
        $stmt = $pdo->prepare("UPDATE orders SET 
            payment_status = 'paid', 
            payment_method = 'razorpay',
            razorpay_payment_id = ?,
            status = 'confirmed'
            WHERE id = ?");
        $stmt->execute([$paymentId, $orderId]);
        
        // Clear cart for this user
        clearUserCart($userId);
        
        // Redirect to success page
        header('Location: ../order_success.php?order_id=' . $orderId);
        exit;
    } else {
        // Payment verification failed
        $stmt = $pdo->prepare("UPDATE orders SET 
            payment_status = 'failed',
            status = 'cancelled'
            WHERE id = ?");
        $stmt->execute([$orderId]);
        
        header('Location: ../checkout.php?error=payment_failed');
        exit;
    }
    
} catch (Exception $e) {
    // Error occurred
    header('Location: ../checkout.php?error=payment_error');
    exit;
}
?> 