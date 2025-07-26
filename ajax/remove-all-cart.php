<?php
session_start();
require_once '../includes/functions.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

try {
    if (isLoggedIn()) {
        // Remove all items for logged-in user
        $userId = $_SESSION['user_id'];
        $result = clearUserCart($userId);
        
        if ($result) {
            echo json_encode(['success' => true, 'message' => 'All items removed from cart']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to remove items from cart']);
        }
    } else {
        // Remove all items for guest user
        $result = clearSessionCart();
        
        if ($result) {
            echo json_encode(['success' => true, 'message' => 'All items removed from cart']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to remove items from cart']);
        }
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?> 