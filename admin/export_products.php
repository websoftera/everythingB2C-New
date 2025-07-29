<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

// Get all products with category information
$stmt = $pdo->query("SELECT p.*, c.name as category_name, c.slug as category_slug 
                     FROM products p 
                     LEFT JOIN categories c ON p.category_id = c.id 
                     ORDER BY p.id");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Set headers for CSV download
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="products_export_' . date('Y-m-d_H-i-s') . '.csv"');

// Create output stream
$output = fopen('php://output', 'w');

// Add UTF-8 BOM for proper Excel encoding
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

// CSV Headers
$headers = [
    'ID',
    'Name',
    'Slug',
    'SKU',
    'HSN',
    'Description',
    'MRP',
    'Selling Price',
    'Discount Percentage',
    'Category ID',
    'Category Name',
    'Category Slug',
    'Stock Quantity',
    'Max Quantity Per Order',
    'GST Type',
    'GST Rate',
    'Is Active',
    'Is Featured',
    'Is Discounted',
    'Main Image',
    'Created At'
];

// Write headers
fputcsv($output, $headers);

// Write product data
foreach ($products as $product) {
    $row = [
        $product['id'],
        $product['name'],
        $product['slug'],
        $product['sku'],
        $product['hsn'],
        $product['description'],
        $product['mrp'],
        $product['selling_price'],
        $product['discount_percentage'],
        $product['category_id'],
        $product['category_name'],
        $product['category_slug'],
        $product['stock_quantity'],
        $product['max_quantity_per_order'],
        $product['gst_type'],
        $product['gst_rate'],
        $product['is_active'],
        $product['is_featured'],
        $product['is_discounted'],
        $product['main_image'],
        $product['created_at']
    ];
    
    fputcsv($output, $row);
}

fclose($output);
exit;
?> 