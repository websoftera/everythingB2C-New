<?php
session_start();
require_once '../includes/functions.php';

header('Content-Type: application/json');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    // Get JSON input
    $rawInput = file_get_contents('php://input');
    $input = json_decode($rawInput, true);

    // Log the input for debugging
    error_log("Wishlist input: " . $rawInput);

    if (!$input || !isset($input['product_id'])) {
        echo json_encode(['success' => false, 'message' => 'Invalid input', 'debug' => 'No product_id found']);
        exit;
    }

    $productId = (int)$input['product_id'];
    error_log("Processing product ID: " . $productId);

    // Check if product exists and is active
    $product = getProductById($productId);
    if (!$product) {
        echo json_encode(['success' => false, 'message' => 'Product not found', 'debug' => 'Product ID: ' . $productId]);
        exit;
    }

    error_log("Product found: " . $product['name']);

    // Check if product is already in wishlist
    $isInWishlist = false;
    if (isLoggedIn()) {
        error_log("User is logged in, checking database wishlist");
        $wishlistItems = getWishlistItems($_SESSION['user_id']);
        foreach ($wishlistItems as $item) {
            if ($item['product_id'] == $productId) {
                $isInWishlist = true;
                break;
            }
        }
    } else {
        error_log("User is not logged in, checking session wishlist");
        $sessionWishlistItems = getSessionWishlistItems();
        foreach ($sessionWishlistItems as $item) {
            if ($item['product_id'] == $productId) {
                $isInWishlist = true;
                break;
            }
        }
    }

    error_log("Is in wishlist: " . ($isInWishlist ? 'true' : 'false'));

    // Toggle wishlist status
    if ($isInWishlist) {
        // Remove from wishlist
        error_log("Attempting to remove from wishlist");
        if (isLoggedIn()) {
            $result = removeFromWishlist($_SESSION['user_id'], $productId);
        } else {
            $result = removeFromSessionWishlist($productId);
        }
        
        error_log("Remove result: " . ($result ? 'true' : 'false'));
        
        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Product removed from wishlist', 'action' => 'removed']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to remove product from wishlist']);
        }
    } else {
        // Add to wishlist
        error_log("Attempting to add to wishlist");
        if (isLoggedIn()) {
            $result = addToWishlist($_SESSION['user_id'], $productId);
        } else {
            $result = addToSessionWishlist($productId);
        }
        
        error_log("Add result: " . ($result ? 'true' : 'false'));
        
        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Product added to wishlist', 'action' => 'added']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to add product to wishlist']);
        }
    }
} catch (Exception $e) {
    // Log the error
    error_log("Wishlist error: " . $e->getMessage());
    error_log("Wishlist error trace: " . $e->getTraceAsString());
    echo json_encode(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()]);
}
?> 