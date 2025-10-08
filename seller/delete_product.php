<?php
session_start();
require_once '../config/database.php';
require_once '../includes/seller_functions.php';

if (!isset($_SESSION['seller_id'])) {
    header('Location: login.php');
    exit;
}

$sellerId = $_SESSION['seller_id'];
$productId = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Check permission
$permissions = getSellerPermissions($sellerId);
if (!$permissions['can_delete_products']) {
    $_SESSION['error_message'] = 'You do not have permission to delete products.';
    header('Location: products.php');
    exit;
}

if ($productId) {
    try {
        // Verify product belongs to this seller
        $stmt = $pdo->prepare("SELECT name FROM products WHERE id = ? AND seller_id = ?");
        $stmt->execute([$productId, $sellerId]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($product) {
            // Delete product
            $stmt = $pdo->prepare("DELETE FROM products WHERE id = ? AND seller_id = ?");
            $stmt->execute([$productId, $sellerId]);
            
            // Log activity
            logSellerActivity($sellerId, 'product_deleted', "Deleted product: {$product['name']} (ID: {$productId})");
            
            // Update statistics
            updateSellerStatistics($sellerId);
            
            $_SESSION['success_message'] = 'Product deleted successfully!';
        } else {
            $_SESSION['error_message'] = 'Product not found or access denied.';
        }
    } catch (Exception $e) {
        $_SESSION['error_message'] = 'Error deleting product: ' . $e->getMessage();
    }
}

header('Location: products.php');
exit;
?>
