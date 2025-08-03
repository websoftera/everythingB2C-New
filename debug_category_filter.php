<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

$pdo = $GLOBALS['pdo'];

// Function to get all descendant category IDs recursively
function getAllDescendantCategoryIdsRecursive($pdo, $parentId) {
    $descendants = [$parentId];
    
    $stmt = $pdo->prepare('SELECT id FROM categories WHERE parent_id = ?');
    $stmt->execute([$parentId]);
    $children = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    foreach ($children as $childId) {
        $descendants = array_merge($descendants, getAllDescendantCategoryIdsRecursive($pdo, $childId));
    }
    
    return $descendants;
}

// Test different category scenarios
echo "<h2>Category Filter Debug</h2>";

// Test 1: No category parameter
echo "<h3>Test 1: No category parameter</h3>";
$selectedCategory = null;
echo "selectedCategory: " . var_export($selectedCategory, true) . "<br>";
echo "selectedCategory !== '': " . var_export($selectedCategory !== '', true) . "<br>";
echo "selectedCategory && selectedCategory !== '': " . var_export($selectedCategory && $selectedCategory !== '', true) . "<br><br>";

// Test 2: Empty category parameter
echo "<h3>Test 2: Empty category parameter</h3>";
$selectedCategory = '';
echo "selectedCategory: " . var_export($selectedCategory, true) . "<br>";
echo "selectedCategory !== '': " . var_export($selectedCategory !== '', true) . "<br>";
echo "selectedCategory && selectedCategory !== '': " . var_export($selectedCategory && $selectedCategory !== '', true) . "<br><br>";

// Test 3: Specific category parameter
echo "<h3>Test 3: Specific category parameter</h3>";
$selectedCategory = '11';
echo "selectedCategory: " . var_export($selectedCategory, true) . "<br>";
echo "selectedCategory !== '': " . var_export($selectedCategory !== '', true) . "<br>";
echo "selectedCategory && selectedCategory !== '': " . var_export($selectedCategory && $selectedCategory !== '', true) . "<br><br>";

// Test 4: Simulate the actual filtering logic
echo "<h3>Test 4: Simulate filtering logic</h3>";

// Test with empty category
$selectedCategory = '';
$whereConditions = ['p.is_active = 1'];
$params = [];

if ($selectedCategory && $selectedCategory !== '') {
    echo "Condition 1: Specific category selected<br>";
    $categoryIds = getAllDescendantCategoryIdsRecursive($pdo, intval($selectedCategory));
    $placeholders = str_repeat('?,', count($categoryIds) - 1) . '?';
    $whereConditions[] = "p.category_id IN ($placeholders)";
    $params = array_merge($params, $categoryIds);
} elseif ($selectedCategory === '') {
    echo "Condition 2: All Categories selected - NO category filter applied<br>";
    // Don't add any category filter condition
} else {
    echo "Condition 3: Default to current page category<br>";
    // This would be the current page's category
}

echo "Final WHERE conditions: " . implode(' AND ', $whereConditions) . "<br>";
echo "Parameters: " . implode(', ', $params) . "<br><br>";

// Test 5: Check total products
echo "<h3>Test 5: Product counts</h3>";
$stmt = $pdo->query('SELECT COUNT(*) as total FROM products WHERE is_active = 1');
$totalProducts = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
echo "Total active products: $totalProducts<br>";

// Test with category filter
$stmt = $pdo->prepare('SELECT COUNT(*) as total FROM products WHERE is_active = 1 AND category_id IN (1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20)');
$stmt->execute();
$filteredProducts = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
echo "Products with category filter: $filteredProducts<br>";

echo "<h3>Test 6: All categories query</h3>";
$sql = "SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.is_active = 1 ORDER BY p.created_at DESC LIMIT 5";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "Products found with no category filter: " . count($products) . "<br>";
foreach ($products as $product) {
    echo "- " . $product['name'] . " (Category: " . $product['category_name'] . ")<br>";
}
?> 