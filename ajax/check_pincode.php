<?php
session_start();
require_once '../includes/delivery_popup_functions.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$pincode = $input['pincode'] ?? '';

if (empty($pincode)) {
    echo json_encode([
        'success' => false,
        'message' => 'Pincode is required'
    ]);
    exit;
}

// Check if pincode is serviceable
$isServiceable = isPincodeServiceable($pincode);
$settings = getPopupSettings();

if ($isServiceable) {
    $message = $settings['service_available_message'] ?? 'We Provide Delivery to Your Area';
    $status = 'success';
} else {
    $message = $settings['service_unavailable_message'] ?? 'We Don’t Provide Delivery to Your Area';
    $status = 'error';
}

echo json_encode([
    'success' => true,
    'serviceable' => $isServiceable,
    'message' => $message,
    'status' => $status,
    'pincode' => $pincode
]);
?>
