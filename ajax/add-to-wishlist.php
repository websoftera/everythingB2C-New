<?php
session_start();
require_once '../includes/functions.php';

header('Content-Type: application/json');



try {
    // Get JSON input
    $rawInput = file_get_contents('php://input');
    $input = json_decode($rawInput, true);



    if (!$input || !isset($input['product_id'])) {
        echo json_encode(['success' => false, 'message' => 'Invalid input']);
        exit;
    }

    $productId = (int)$input['product_id'];


    // Check if product exists and is active
    $product = getProductById($productId);
    if (!$product) {
        echo json_encode(['success' => false, 'message' => 'Product not found', 'debug' => 'Product ID: ' . $productId]);
        exit;
    }

    

    // Check if product is already in wishlist
    $isInWishlist = false;
    if (isLoggedIn()) {

        $wishlistItems = getWishlistItems($_SESSION['user_id']);
        foreach ($wishlistItems as $item) {
            if ($item['product_id'] == $productId) {
                $isInWishlist = true;
                break;
            }
        }
    } else {

        $sessionWishlistItems = getSessionWishlistItems();
        foreach ($sessionWishlistItems as $item) {
            if ($item['product_id'] == $productId) {
                $isInWishlist = true;
                break;
            }
        }
    }



    // Toggle wishlist status
    if ($isInWishlist) {
        // Remove from wishlist

        if (isLoggedIn()) {
            $result = removeFromWishlist($_SESSION['user_id'], $productId);
        } else {
            $result = removeFromSessionWishlist($productId);
        }
        

        
        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Product removed from wishlist', 'action' => 'removed']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to remove product from wishlist']);
        }
    } else {
        // Add to wishlist

        if (isLoggedIn()) {
            $result = addToWishlist($_SESSION['user_id'], $productId);
        } else {
            $result = addToSessionWishlist($productId);
        }
        

        
        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Product added to wishlist', 'action' => 'added']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to add product to wishlist']);
        }
    }
} catch (Exception $e) {
    // Log the error
    
    echo json_encode(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()]);
}
?> 