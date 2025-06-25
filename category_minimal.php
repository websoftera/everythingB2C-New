<?php
// Minimal category page without includes
require_once 'includes/functions.php';

// Prevent caching
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Get category slug from URL
$slug = $_GET['slug'] ?? '';

if (empty($slug)) {
    echo "No slug provided";
    exit;
}

echo "<h1>Minimal Category Test</h1>";
echo "<p>Testing slug: <strong>$slug</strong></p>";

// Get category details
$category = getCategoryBySlug($slug);

if (!$category) {
    echo "<p>Category not found for slug: '$slug'</p>";
    exit;
}

echo "<h2>Category Found:</h2>";
echo "<p>ID: {$category['id']}</p>";
echo "<p>Name: {$category['name']}</p>";
echo "<p>Slug: {$category['slug']}</p>";

// Get products in this category
$products = getProductsByCategory($category['id']);

echo "<h2>Products Found: " . count($products) . "</h2>";

if (empty($products)) {
    echo "<p>No products found in this category</p>";
} else {
    echo "<ul>";
    foreach ($products as $product) {
        echo "<li>{$product['name']} (ID: {$product['id']})</li>";
    }
    echo "</ul>";
}

echo "<hr>";
echo "<p><a href='?slug=cleaning-household'>Test Cleaning & Household</a></p>";
echo "<p><a href='?slug=kitchen'>Test Kitchen</a></p>";
echo "<p><a href='?slug=office-stationery'>Test Office Stationery</a></p>";
?> 