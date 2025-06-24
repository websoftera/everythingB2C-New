<?php
require_once '../includes/functions.php';
header('Content-Type: application/json');

$query = isset($_GET['query']) ? trim($_GET['query']) : '';
if ($query === '') {
    echo json_encode(['success' => false, 'results' => []]);
    exit;
}

$results = searchProducts($query, 10); // Limit to 10 results for live search

// Format results for frontend
$formatted = array_map(function($product) {
    return [
        'name' => $product['name'],
        'slug' => $product['slug'],
        'image' => $product['main_image'],
        'category' => $product['category_name'],
        'category_slug' => $product['category_slug'] ?? null,
        'price' => $product['selling_price'],
        'mrp' => $product['mrp'],
        'discount' => $product['discount_percentage'],
    ];
}, $results);

echo json_encode(['success' => true, 'results' => $formatted]); 