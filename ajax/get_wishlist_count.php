<?php
session_start();
require_once '../includes/functions.php';

header('Content-Type: application/json');

if (isLoggedIn()) {
    $userId = $_SESSION['user_id'];
    $wishlistItems = getWishlistItems($userId);
} else {
    $wishlistItems = getSessionWishlistItems();
}

$count = !empty($wishlistItems) ? count($wishlistItems) : 0;

echo json_encode(['wishlist_count' => $count]);
?>
