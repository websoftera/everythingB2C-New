<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    require_once 'config/database.php';
    require_once 'includes/seller_functions.php';
    
    echo "<h2>Testing seller/edit_product.php</h2>";
    echo "<p>Database connection: OK</p>";
    
    // Test query
    $stmt = $pdo->prepare("SELECT p.*, c.name as category_name FROM products p 
                           LEFT JOIN categories c ON p.category_id = c.id 
                           WHERE p.id = ? LIMIT 1");
    $stmt->execute([72]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($product) {
        echo "<h3>Product Found (ID: 72)</h3>";
        echo "<pre>";
        print_r($product);
        echo "</pre>";
        
        // Test the rejection_reason check
        echo "<h3>Rejection Reason Check:</h3>";
        echo "rejection_reason value: " . var_export($product['rejection_reason'], true) . "<br>";
        echo "Is NULL: " . (is_null($product['rejection_reason']) ? 'YES' : 'NO') . "<br>";
        echo "Condition (if rejection_reason): " . ($product['rejection_reason'] ? 'TRUE' : 'FALSE') . "<br>";
    } else {
        echo "<h3>Product ID 72 not found</h3>";
        
        // Show available products
        $stmt = $pdo->query("SELECT id FROM products LIMIT 5");
        $products = $stmt->fetchAll();
        echo "<p>Available products: ";
        foreach ($products as $p) {
            echo $p['id'] . " ";
        }
        echo "</p>";
    }
    
} catch (Exception $e) {
    echo "<h2 style='color: red;'>ERROR:</h2>";
    echo "<pre>";
    echo $e->getMessage() . "\n";
    echo $e->getTraceAsString();
    echo "</pre>";
}
?>
