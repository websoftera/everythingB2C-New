<?php
session_start();
require_once '../config/database.php';

// Test if we can connect to database
echo "<h2>Testing Seller System</h2>";

// Test 1: Database Connection
echo "<h3>1. Database Connection:</h3>";
if (isset($pdo)) {
    echo "✅ Database connected<br>";
} else {
    echo "❌ Database NOT connected<br>";
    exit;
}

// Test 2: Check if seller_functions.php exists
echo "<h3>2. Seller Functions File:</h3>";
if (file_exists('../includes/seller_functions.php')) {
    echo "✅ seller_functions.php exists<br>";
    require_once '../includes/seller_functions.php';
} else {
    echo "❌ seller_functions.php NOT found<br>";
}

// Test 3: Check if seller tables exist
echo "<h3>3. Seller Tables:</h3>";
try {
    $tables = ['sellers', 'seller_permissions', 'seller_statistics', 'seller_product_approval_history', 'seller_activity_log'];
    foreach ($tables as $table) {
        $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
        if ($stmt->rowCount() > 0) {
            echo "✅ Table '$table' exists<br>";
        } else {
            echo "❌ Table '$table' NOT found - Need to import seller_system_schema.sql<br>";
        }
    }
} catch (Exception $e) {
    echo "❌ Error checking tables: " . $e->getMessage() . "<br>";
}

// Test 4: Check if products table has seller columns
echo "<h3>4. Products Table Columns:</h3>";
try {
    $stmt = $pdo->query("SHOW COLUMNS FROM products LIKE 'seller_id'");
    if ($stmt->rowCount() > 0) {
        echo "✅ Column 'seller_id' exists in products table<br>";
    } else {
        echo "❌ Column 'seller_id' NOT found - Need to import seller_system_schema.sql<br>";
    }
    
    $stmt = $pdo->query("SHOW COLUMNS FROM products LIKE 'is_approved'");
    if ($stmt->rowCount() > 0) {
        echo "✅ Column 'is_approved' exists in products table<br>";
    } else {
        echo "❌ Column 'is_approved' NOT found - Need to import seller_system_schema.sql<br>";
    }
} catch (Exception $e) {
    echo "❌ Error checking columns: " . $e->getMessage() . "<br>";
}

// Test 5: Check if users table has seller columns
echo "<h3>5. Users Table Columns:</h3>";
try {
    $stmt = $pdo->query("SHOW COLUMNS FROM users LIKE 'user_role'");
    if ($stmt->rowCount() > 0) {
        echo "✅ Column 'user_role' exists in users table<br>";
    } else {
        echo "❌ Column 'user_role' NOT found - Need to import seller_system_schema.sql<br>";
    }
} catch (Exception $e) {
    echo "❌ Error checking columns: " . $e->getMessage() . "<br>";
}

// Test 6: Test loading seller pages
echo "<h3>6. Page Access Test:</h3>";
echo "Click links to test pages:<br>";
echo "<a href='manage_sellers.php'>Test Manage Sellers</a><br>";
echo "<a href='approve_products.php'>Test Approve Products</a><br>";
echo "<a href='seller_products.php'>Test Seller Products</a><br>";
echo "<a href='seller_orders.php'>Test Seller Orders</a><br>";

echo "<h3>Summary:</h3>";
echo "<p><strong>If you see ❌ marks above, you need to:</strong></p>";
echo "<ol>";
echo "<li>Import the database schema: <code>database/seller_system_schema.sql</code></li>";
echo "<li>Make sure all tables and columns are created</li>";
echo "<li>Set your admin role: <code>UPDATE users SET user_role = 'admin' WHERE id = 1;</code></li>";
echo "</ol>";
?>
