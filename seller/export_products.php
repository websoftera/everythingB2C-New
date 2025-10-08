<?php
session_start();
require_once '../config/database.php';
require_once '../includes/seller_functions.php';

if (!isset($_SESSION['seller_id'])) {
    header('Location: login.php');
    exit;
}

$sellerId = $_SESSION['seller_id'];

// Get all seller products
$stmt = $pdo->prepare("SELECT p.*, c.name as category_name 
                       FROM products p
                       LEFT JOIN categories c ON p.category_id = c.id
                       WHERE p.seller_id = ?
                       ORDER BY p.created_at DESC");
$stmt->execute([$sellerId]);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Set headers for CSV download
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=seller_products_' . date('Y-m-d') . '.csv');

// Create output stream
$output = fopen('php://output', 'w');

// Add BOM for Excel UTF-8 support
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

// Add CSV headers
fputcsv($output, [
    'ID',
    'Name',
    'SKU',
    'HSN',
    'Category',
    'Description',
    'MRP',
    'Selling Price',
    'Discount %',
    'Stock Quantity',
    'GST Rate',
    'Status',
    'Approval Status',
    'Featured',
    'Discounted',
    'Created Date'
]);

// Add product data
foreach ($products as $product) {
    fputcsv($output, [
        $product['id'],
        $product['name'],
        $product['sku'],
        $product['hsn'] ?? '',
        $product['category_name'] ?? '',
        $product['description'],
        $product['mrp'],
        $product['selling_price'],
        $product['discount_percentage'],
        $product['stock_quantity'],
        $product['gst_rate'],
        $product['is_active'] ? 'Active' : 'Inactive',
        $product['is_approved'] ? 'Approved' : 'Pending',
        $product['is_featured'] ? 'Yes' : 'No',
        $product['is_discounted'] ? 'Yes' : 'No',
        date('Y-m-d H:i:s', strtotime($product['created_at']))
    ]);
}

fclose($output);

// Log activity
logSellerActivity($sellerId, 'products_exported', 'Exported ' . count($products) . ' products to CSV');

exit;
?>
