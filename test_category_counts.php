<?php
require_once 'includes/functions.php';

echo "<h2>Category Product Count Test</h2>";

// Get categories with real-time product counts
$categories = getAllCategoriesWithProductCount();

echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
echo "<tr><th>Category ID</th><th>Category Name</th><th>Slug</th><th>Product Count</th><th>Parent ID</th></tr>";

foreach ($categories as $category) {
    echo "<tr>";
    echo "<td>{$category['id']}</td>";
    echo "<td>{$category['name']}</td>";
    echo "<td>{$category['slug']}</td>";
    echo "<td>{$category['product_count']}</td>";
    echo "<td>" . ($category['parent_id'] ?? 'NULL') . "</td>";
    echo "</tr>";
}

echo "</table>";

// Show only main categories (parent_id is NULL)
echo "<h3>Main Categories (for homepage):</h3>";
$main_categories = array_filter($categories, function($cat) { 
    return empty($cat['parent_id']); 
});

echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
echo "<tr><th>Category Name</th><th>Product Count</th></tr>";

foreach ($main_categories as $category) {
    echo "<tr>";
    echo "<td>{$category['name']}</td>";
    echo "<td>{$category['product_count']}</td>";
    echo "</tr>";
}

echo "</table>";
?> 