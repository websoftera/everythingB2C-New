<?php
session_start();
require_once '../includes/functions.php';

header('Content-Type: application/json');

$count = 0;
if (isLoggedIn()) {
    $cartItems = getCartItems($_SESSION['user_id']);
    foreach ($cartItems as $item) {
        $count += (int)$item['quantity'];
    }
} else {
    $cartItems = getCartItems();
    foreach ($cartItems as $item) {
        $count += (int)$item['quantity'];
    }
}

echo json_encode(['cart_count' => $count]); 