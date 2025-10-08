<?php
// Quick diagnostic page - no login required for testing
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<html><head><title>Seller System Check</title>";
echo "<style>body{font-family:Arial;padding:20px;} .ok{color:green;} .error{color:red;} .warning{color:orange;}</style>";
echo "</head><body>";

echo "<h1>Seller System Diagnostic</h1>";

// 1. Check database config
echo "<h2>1. Database Configuration</h2>";
if (file_exists('../config/database.php')) {
    echo "<span class='ok'>✓ database.php exists</span><br>";
    try {
        require_once '../config/database.php';
        echo "<span class='ok'>✓ database.php loaded successfully</span><br>";
        if (isset($pdo)) {
            echo "<span class='ok'>✓ PDO connection established</span><br>";
        } else {
            echo "<span class='error'>✗ PDO variable not set</span><br>";
        }
    } catch (Exception $e) {
        echo "<span class='error'>✗ Error loading database.php: " . $e->getMessage() . "</span><br>";
    }
} else {
    echo "<span class='error'>✗ database.php NOT found</span><br>";
}

// 2. Check seller_functions.php
echo "<h2>2. Seller Functions File</h2>";
if (file_exists('../includes/seller_functions.php')) {
    echo "<span class='ok'>✓ seller_functions.php exists</span><br>";
    try {
        require_once '../includes/seller_functions.php';
        echo "<span class='ok'>✓ seller_functions.php loaded successfully</span><br>";
    } catch (Exception $e) {
        echo "<span class='error'>✗ Error loading seller_functions.php: " . $e->getMessage() . "</span><br>";
        echo "<pre>" . $e->getTraceAsString() . "</pre>";
    }
} else {
    echo "<span class='error'>✗ seller_functions.php NOT found</span><br>";
}

// 3. Check seller tables
echo "<h2>3. Database Tables</h2>";
if (isset($pdo)) {
    $requiredTables = [
        'sellers' => 'Main seller information',
        'seller_permissions' => 'Seller permissions',
        'seller_statistics' => 'Seller statistics',
        'seller_product_approval_history' => 'Product approval history',
        'seller_activity_log' => 'Activity log'
    ];
    
    foreach ($requiredTables as $table => $description) {
        try {
            $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
            if ($stmt->rowCount() > 0) {
                echo "<span class='ok'>✓ $table ($description)</span><br>";
            } else {
                echo "<span class='error'>✗ $table NOT found ($description)</span><br>";
            }
        } catch (Exception $e) {
            echo "<span class='error'>✗ Error checking $table: " . $e->getMessage() . "</span><br>";
        }
    }
}

// 4. Check modified columns
echo "<h2>4. Modified Table Columns</h2>";
if (isset($pdo)) {
    // Check products table
    try {
        $stmt = $pdo->query("SHOW COLUMNS FROM products WHERE Field IN ('seller_id', 'is_approved', 'approved_at', 'approved_by', 'rejection_reason')");
        $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
        echo "<strong>Products table:</strong><br>";
        foreach (['seller_id', 'is_approved', 'approved_at', 'approved_by', 'rejection_reason'] as $col) {
            if (in_array($col, $columns)) {
                echo "<span class='ok'>✓ $col</span><br>";
            } else {
                echo "<span class='error'>✗ $col NOT found</span><br>";
            }
        }
    } catch (Exception $e) {
        echo "<span class='error'>✗ Error checking products: " . $e->getMessage() . "</span><br>";
    }
    
    // Check users table
    try {
        $stmt = $pdo->query("SHOW COLUMNS FROM users WHERE Field IN ('user_role', 'is_seller_approved')");
        $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
        echo "<br><strong>Users table:</strong><br>";
        foreach (['user_role', 'is_seller_approved'] as $col) {
            if (in_array($col, $columns)) {
                echo "<span class='ok'>✓ $col</span><br>";
            } else {
                echo "<span class='error'>✗ $col NOT found</span><br>";
            }
        }
    } catch (Exception $e) {
        echo "<span class='error'>✗ Error checking users: " . $e->getMessage() . "</span><br>";
    }
}

// 5. Test page files
echo "<h2>5. Admin Page Files</h2>";
$pages = [
    'manage_sellers.php' => 'Manage Sellers',
    'approve_products.php' => 'Approve Products',
    'seller_products.php' => 'All Seller Products',
    'seller_orders.php' => 'Seller Orders'
];

foreach ($pages as $file => $name) {
    if (file_exists($file)) {
        echo "<span class='ok'>✓ $file exists</span> - <a href='$file'>Test $name</a><br>";
    } else {
        echo "<span class='error'>✗ $file NOT found</span><br>";
    }
}

// Summary
echo "<h2>Summary & Next Steps</h2>";
echo "<div style='background:#fff3cd;padding:15px;border-radius:5px;'>";
echo "<strong>If you see any ✗ marks above:</strong><br><br>";
echo "<ol>";
echo "<li><strong>Missing Tables/Columns:</strong> Import <code>database/seller_system_schema.sql</code> via phpMyAdmin or command line</li>";
echo "<li><strong>Missing Files:</strong> Ensure all seller system files are uploaded to the correct directories</li>";
echo "<li><strong>After fixing:</strong> Refresh this page to verify all checks pass</li>";
echo "</ol>";
echo "</div>";

echo "<br><p><a href='index.php'>← Back to Admin Dashboard</a></p>";

echo "</body></html>";
?>
