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
$addressId = intval($input['id'] ?? 0);
if (!$addressId) {
    echo json_encode(['success' => false, 'message' => 'Invalid address ID']);
    exit;
}
if (deleteUserAddress($userId, $addressId)) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to delete address']);
} 