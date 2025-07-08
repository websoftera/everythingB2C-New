<?php
session_start();
require_once '../includes/functions.php';
header('Content-Type: application/json');
if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}
$userId = $_SESSION['user_id'];
$input = json_decode(file_get_contents('php://input'), true);
$addressId = intval($input['address_id'] ?? 0);
$data = [
    'name' => trim($input['name'] ?? ''),
    'phone' => trim($input['phone'] ?? ''),
    'pincode' => trim($input['pincode'] ?? ''),
    'address_line1' => trim($input['address_line1'] ?? ''),
    'address_line2' => trim($input['address_line2'] ?? ''),
    'city' => trim($input['city'] ?? ''),
    'state' => trim($input['state'] ?? ''),
    'is_default' => !empty($input['is_default']) ? 1 : 0
];
foreach (['name','phone','pincode','address_line1','city','state'] as $f) {
    if (empty($data[$f])) {
        echo json_encode(['success' => false, 'message' => 'All required fields must be filled']);
        exit;
    }
}
try {
    if ($addressId) {
        $ok = updateUserAddress($userId, $addressId, $data);
        if ($ok && $data['is_default']) setDefaultAddress($userId, $addressId);
    } else {
        $ok = addUserAddress($userId, $data);
        if ($ok && $data['is_default']) setDefaultAddress($userId, $GLOBALS['pdo']->lastInsertId());
    }
    if ($ok) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to save address']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
} 