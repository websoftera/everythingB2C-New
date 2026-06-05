<?php
session_start();
require_once '../includes/functions.php';

header('Content-Type: application/json');

$count = 0;
if (isLoggedIn()) {
    $cartItems = getCartItems($_SESSION['user_id']);
    $count = count($cartItems);
} else {
    $cartItems = getCartItems();
    $count = count($cartItems);
}

echo json_encode(['cart_count' => $count]); 
