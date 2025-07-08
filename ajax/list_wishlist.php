<?php
session_start();
require_once '../includes/functions.php';
if (!isLoggedIn()) exit;
$userId = $_SESSION['user_id'];
$wishlistItems = getWishlistItems($userId);
if (!empty($wishlistItems)) {
    foreach ($wishlistItems as $item) {
        echo '<li class="account-wishlist-item col-md-4 mb-3">';
        echo '<div class="account-wishlist-info">';
        echo '<img src="' . htmlspecialchars($item['main_image']) . '" alt="' . htmlspecialchars($item['name']) . '" style="width:100%;height:180px;object-fit:cover;border-radius:8px;">';
        echo '<h6 class="mt-2">' . htmlspecialchars($item['name']) . '</h6>';
        echo '<p>₹' . number_format($item['selling_price'], 2) . '</p>';
        echo '<a href="product.php?slug=' . $item['slug'] . '" class="account-btn">View Product</a>';
        echo '<button class="account-btn account-btn-danger" onclick="removeFromWishlist(' . $item['product_id'] . ')">Remove</button>';
        echo '</div>';
        echo '</li>';
    }
} else {
    echo '<div class="account-empty-state">';
    echo '<i class="fas fa-heart"></i>';
    echo '<h5>Your wishlist is empty</h5>';
    echo '<p>Start adding products to your wishlist</p>';
    echo '<a href="index.php" class="account-btn">Start Shopping</a>';
    echo '</div>';
} 