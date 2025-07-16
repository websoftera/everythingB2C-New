<?php
session_start();
require_once '../includes/functions.php';
require_once '../includes/gst_shipping_functions.php';
header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);

if (isLoggedIn()) {
    $cartItems = getCartItems($_SESSION['user_id']);
} else {
    $cartItems = getCartItems();
}

// Default values
$delivery_state = 'Maharashtra';
$delivery_city = null;
$delivery_pincode = null;

// Try to get from POST/JSON
if (!empty($input['delivery_state'])) {
    $delivery_state = $input['delivery_state'];
}
if (!empty($input['delivery_city'])) {
    $delivery_city = $input['delivery_city'];
}
if (!empty($input['delivery_pincode'])) {
    $delivery_pincode = $input['delivery_pincode'];
}

// For logged-in users, try to use default address if not provided
if (isLoggedIn() && (empty($input['delivery_state']) || empty($input['delivery_city']) || empty($input['delivery_pincode']))) {
    $defaultAddress = getDefaultAddress($_SESSION['user_id']);
    if ($defaultAddress) {
        if (empty($input['delivery_state']) && !empty($defaultAddress['state'])) $delivery_state = $defaultAddress['state'];
        if (empty($input['delivery_city']) && !empty($defaultAddress['city'])) $delivery_city = $defaultAddress['city'];
        if (empty($input['delivery_pincode']) && !empty($defaultAddress['pincode'])) $delivery_pincode = $defaultAddress['pincode'];
    }
}

$totals = calculateOrderTotal($cartItems, $delivery_state, $delivery_city, $delivery_pincode);

// Add cartItems to response if details=1 in query string
$withDetails = isset($_GET['details']) && $_GET['details'] == '1';

$response = [
    'success' => true,
    'totals' => [
        'subtotal' => $totals['subtotal'],
        'total_shipping' => $totals['shipping_charge'],
        'total_gst' => $totals['gst_amount'],
        'grand_total' => $totals['total'],
        'sgst_total' => isset($totals['sgst_total']) ? $totals['sgst_total'] : 0,
        'cgst_total' => isset($totals['cgst_total']) ? $totals['cgst_total'] : 0,
        'igst_total' => isset($totals['igst_total']) ? $totals['igst_total'] : 0,
        'delivery_state' => $delivery_state,
        'shipping_zone_name' => isset($totals['shipping_zone_name']) ? $totals['shipping_zone_name'] : '',
        'shipping_zone_id' => isset($totals['shipping_zone_id']) ? $totals['shipping_zone_id'] : null,
        'gst_breakdown' => isset($totals['gst_breakdown']) ? $totals['gst_breakdown'] : [],
        'total_savings' => isset($totals['total_savings']) ? $totals['total_savings'] : 0,
    ]
];
if ($withDetails) {
    $response['cartItems'] = $cartItems;
}
echo json_encode($response); 