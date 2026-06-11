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

function validateAjaxAddressData($data) {
    $errors = [];

    if ($data['name'] === '' || strlen($data['name']) < 2 || strlen($data['name']) > 60 || !preg_match("/^[A-Za-z][A-Za-z .'-]*$/", $data['name'])) {
        $errors[] = 'Please enter a valid full name.';
    }
    if (!preg_match('/^(?:\+91|0)?[6-9][0-9]{9}$/', $data['phone'])) {
        $errors[] = 'Please enter a valid phone number.';
    }
    if (!preg_match('/^[1-9][0-9]{5}$/', $data['pincode'])) {
        $errors[] = 'Please enter a valid 6 digit PIN code.';
    }
    if (strlen($data['address_line1']) < 5 || strlen($data['address_line1']) > 150) {
        $errors[] = 'Address Line 1 must be between 5 and 150 characters.';
    }
    if ($data['address_line2'] !== '' && strlen($data['address_line2']) > 150) {
        $errors[] = 'Address Line 2 must be 150 characters or less.';
    }
    if ($data['city'] === '' || strlen($data['city']) < 2 || strlen($data['city']) > 60 || !preg_match("/^[A-Za-z][A-Za-z .'-]*$/", $data['city'])) {
        $errors[] = 'Please enter a valid city.';
    }
    if ($data['state'] === '' || strlen($data['state']) < 2 || strlen($data['state']) > 60 || !preg_match("/^[A-Za-z][A-Za-z .'-]*$/", $data['state'])) {
        $errors[] = 'Please enter a valid state.';
    }

    return $errors;
}

$validationErrors = validateAjaxAddressData($data);
if (!empty($validationErrors)) {
    echo json_encode(['success' => false, 'message' => implode(' ', $validationErrors)]);
    exit;
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
        exit;
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to save address']);
        exit;
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    exit;
} 
