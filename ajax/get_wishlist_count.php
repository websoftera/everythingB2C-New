<?php
session_start();
require_once '../includes/functions.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['wishlist_count' => 0]);
    exit;
}

$userId = $_SESSION['user_id'];
$wishlistItems = getWishlistItems($userId);
$count = !empty($wishlistItems) ? count($wishlistItems) : 0;

echo json_encode(['wishlist_count' => $count]);
?>
