<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once '../includes/functions.php';
require_once '../includes/gst_shipping_functions.php';
require_once __DIR__ . '/../vendor/autoload.php';
use Razorpay\Api\Api;

header('Content-Type: application/json');

if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$userId = $_SESSION['user_id'];

// Validate required fields
$addressId = isset($input['selected_address']) ? intval($input['selected_address']) : 0;
$gstNumber = isset($input['gst_number']) ? trim($input['gst_number']) : '';
$paymentMethod = isset($input['payment_method']) ? $input['payment_method'] : '';

if (!$addressId || $paymentMethod !== 'razorpay') {
    echo json_encode(['success' => false, 'message' => 'Invalid address or payment method.']);
    exit;
}

$cartItems = getCartItems($userId);
if (empty($cartItems)) {
    echo json_encode(['success' => false, 'message' => 'Your cart is empty.']);
    exit;
}

$addresses = getUserAddresses($userId);
$selectedAddress = null;
foreach ($addresses as $addr) {
    if ($addr['id'] == $addressId) {
        $selectedAddress = $addr;
        break;
    }
}
if (!$selectedAddress) {
    echo json_encode(['success' => false, 'message' => 'Invalid address.']);
    exit;
}

// Calculate order total
$delivery_state = $selectedAddress['state'] ?? 'Maharashtra';
$delivery_city = $selectedAddress['city'] ?? null;
$delivery_pincode = $selectedAddress['pincode'] ?? null;
$orderTotals = calculateOrderTotal($cartItems, $delivery_state, $delivery_city, $delivery_pincode);
$amount = isset($orderTotals['total']) ? $orderTotals['total'] : 0;
if ($amount < 1) {
    echo json_encode(['success' => false, 'message' => 'Invalid order amount.']);
    exit;
}

// Generate order_number and tracking_id
$orderNumber = generateOrderNumber();
$trackingId = generateTrackingId();

// Create temporary order in DB (status: pending, payment_method: razorpay)
try {
    global $pdo;
    $pdo->beginTransaction();
    $stmt = $pdo->prepare("INSERT INTO orders (user_id, address_id, order_number, tracking_id, total_amount, subtotal, gst_amount, shipping_charge, payment_method, gst_number, order_status_id, payment_status, status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1, 'pending', 'pending', NOW())");
    $stmt->execute([
        $userId,
        $addressId,
        $orderNumber,
        $trackingId,
        $orderTotals['total'],
        $orderTotals['subtotal'],
        $orderTotals['gst_amount'],
        $orderTotals['shipping_charge'],
        'razorpay',
        $gstNumber
    ]);
    $tempOrderId = $pdo->lastInsertId();
    // Insert order items (use correct columns)
    foreach ($cartItems as $item) {
        $stmt2 = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price, unit_price, gst_rate, gst_amount) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $gstBreakdown = getGSTBreakdown($item['selling_price'] * $item['quantity'], $item['gst_type'] ?? 'IGST', $item['gst_rate'] ?? 18);
        $stmt2->execute([
            $tempOrderId,
            $item['product_id'],
            $item['quantity'],
            $item['selling_price'] * $item['quantity'],
            $item['selling_price'],
            $item['gst_rate'] ?? 18,
            $gstBreakdown['total_gst']
        ]);
    }
    $pdo->commit();
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => 'Could not create order: ' . $e->getMessage()]);
    exit;
}

// Create Razorpay order
try {
    $keyId = 'rzp_test_2uufCk4Q1h1NDn';
    $keySecret = 'AwxauKLBobgU0EcBftpm1ssd';
    $api = new Api($keyId, $keySecret);
    $amountPaise = (int) round($amount * 100); // Ensure integer paise
    $razorpayOrder = $api->order->create([
        'receipt' => 'order_rcptid_' . $tempOrderId,
        'amount' => $amountPaise, // in paise
        'currency' => 'INR'
    ]);
    $razorpayOrderId = $razorpayOrder['id'];
    // Update order with Razorpay order ID
    $stmt = $pdo->prepare("UPDATE orders SET razorpay_order_id = ? WHERE id = ?");
    $stmt->execute([$razorpayOrderId, $tempOrderId]);
    // Return details for Razorpay popup
    echo json_encode([
        'success' => true,
        'key_id' => $keyId,
        'amount' => $amountPaise,
        'currency' => 'INR',
        'razorpay_order_id' => $razorpayOrderId,
        'temp_order_id' => $tempOrderId,
        'customer_name' => $selectedAddress['name'],
        'customer_email' => isset($_SESSION['user_email']) ? $_SESSION['user_email'] : '',
        'customer_phone' => $selectedAddress['phone']
    ]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error creating payment order: ' . $e->getMessage()]);
} 