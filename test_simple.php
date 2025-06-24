<?php
// Simple test without any includes or caching
require_once 'config/database.php';

echo "<h2>Simple Category Test</h2>";

// Get the slug from URL
$slug = $_GET['slug'] ?? 'cleaning-household';
echo "<p>Testing slug: <strong>$slug</strong></p>";

// Direct database query
$stmt = $pdo->prepare("SELECT * FROM categories WHERE slug = ?");
$stmt->execute([$slug]);
$category = $stmt->fetch(PDO::FETCH_ASSOC);

if ($category) {
    echo "<h3>Category Found:</h3>";
    echo "ID: {$category['id']}<br>";
    echo "Name: {$category['name']}<br>";
    echo "Slug: {$category['slug']}<br>";
    
    // Get products for this category
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM products WHERE category_id = ? AND is_active = 1");
    $stmt->execute([$category['id']]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo "<h3>Products:</h3>";
    echo "Active products: {$result['count']}<br>";
    
    if ($result['count'] > 0) {
        $stmt = $pdo->prepare("SELECT id, name FROM products WHERE category_id = ? AND is_active = 1");
        $stmt->execute([$category['id']]);
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<ul>";
        foreach ($products as $product) {
            echo "<li>{$product['name']} (ID: {$product['id']})</li>";
        }
        echo "</ul>";
    }
} else {
    echo "<p><strong>Category not found!</strong></p>";
}

echo "<hr>";
echo "<p><a href='?slug=cleaning-household'>Test Cleaning & Household</a></p>";
echo "<p><a href='?slug=kitchen'>Test Kitchen</a></p>";
echo "<p><a href='?slug=office-stationery'>Test Office Stationery</a></p>";
?> 