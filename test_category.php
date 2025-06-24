<?php
require_once 'includes/functions.php';

echo "<h2>Category Test</h2>";

// Test 1: Check all categories
echo "<h3>All Categories:</h3>";
$categories = getAllCategories();
foreach ($categories as $cat) {
    echo "ID: {$cat['id']}, Name: {$cat['name']}, Slug: {$cat['slug']}<br>";
}

// Test 2: Test getCategoryBySlug function with different slugs
echo "<h3>Testing getCategoryBySlug function:</h3>";
$testSlugs = ['cleaning-household', 'kitchen', 'office-stationery', 'personal-care', 'other'];

foreach ($testSlugs as $slug) {
    echo "<h4>Testing slug: '$slug'</h4>";
    $category = getCategoryBySlug($slug);
    if ($category) {
        echo "Found: ID: {$category['id']}, Name: {$category['name']}, Slug: {$category['slug']}<br>";
    } else {
        echo "NOT FOUND<br>";
    }
}

// Test 3: Direct database query for each slug
echo "<h3>Direct Database Queries:</h3>";
global $pdo;

foreach ($testSlugs as $slug) {
    echo "<h4>Direct query for slug: '$slug'</h4>";
    $stmt = $pdo->prepare("SELECT * FROM categories WHERE slug = ?");
    $stmt->execute([$slug]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($result) {
        echo "Found: ID: {$result['id']}, Name: {$result['name']}, Slug: {$result['slug']}<br>";
    } else {
        echo "NOT FOUND<br>";
    }
}

// Test 4: Check products for each category
echo "<h3>Products for each category:</h3>";
foreach ($categories as $cat) {
    echo "<h4>Category: {$cat['name']} (ID: {$cat['id']})</h4>";
    
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM products WHERE category_id = ? AND is_active = 1");
    $stmt->execute([$cat['id']]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Active products: " . $result['count'] . "<br>";
    
    if ($result['count'] > 0) {
        $stmt = $pdo->prepare("SELECT id, name FROM products WHERE category_id = ? AND is_active = 1 LIMIT 3");
        $stmt->execute([$cat['id']]);
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($products as $product) {
            echo "- {$product['name']} (ID: {$product['id']})<br>";
        }
    }
}
?> 