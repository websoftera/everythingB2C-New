<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

// Check if user is logged in
if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

$userId = $_SESSION['user_id'];
$addressId = intval($_GET['id'] ?? 0);

if (!$addressId) {
    echo json_encode(['success' => false, 'message' => 'Invalid address ID']);
    exit;
}

try {
    // Get address data
    $stmt = $pdo->prepare("SELECT * FROM addresses WHERE id = ? AND user_id = ?");
    $stmt->execute([$addressId, $userId]);
    $address = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$address) {
        echo json_encode(['success' => false, 'message' => 'Address not found']);
        exit;
    }
    
    echo json_encode(['success' => true, 'address' => $address]);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?> 