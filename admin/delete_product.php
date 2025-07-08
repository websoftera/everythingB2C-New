<?php
session_start();
require_once '../config/database.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

$product_id = intval($_GET['id'] ?? 0);

if ($product_id) {
    try {
        $pdo->beginTransaction();
        
        // Get product images to delete files
        $stmt = $pdo->prepare("SELECT image_path FROM product_images WHERE product_id = ?");
        $stmt->execute([$product_id]);
        $images = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Get main image
        $stmt = $pdo->prepare("SELECT main_image FROM products WHERE id = ?");
        $stmt->execute([$product_id]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Delete product images from database
        $stmt = $pdo->prepare("DELETE FROM product_images WHERE product_id = ?");
        $stmt->execute([$product_id]);
        
        // Delete product
        $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
        $stmt->execute([$product_id]);
        
        $pdo->commit();
        
        // Delete image files (optional - you might want to keep them for backup)
        foreach ($images as $image) {
            $file_path = "../" . $image['image_path'];
            if (file_exists($file_path)) {
                unlink($file_path);
            }
        }
        
        // Delete main image file
        if ($product && $product['main_image']) {
            $file_path = "../" . $product['main_image'];
            if (file_exists($file_path)) {
                unlink($file_path);
            }
        }
        
        $_SESSION['success_message'] = 'Product deleted successfully!';
        
    } catch (Exception $e) {
        $pdo->rollBack();
        $_SESSION['error_message'] = 'Error deleting product: ' . $e->getMessage();
    }
}

header('Location: products.php');
exit;
?> 