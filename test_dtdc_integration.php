<?php
/**
 * DTDC Integration Test Script
 * 
 * This script tests the DTDC API integration functionality.
 * Run this script to verify that the integration is working correctly.
 */

require_once 'config/database.php';
require_once 'includes/functions.php';
require_once 'includes/dtdc_api.php';

echo "<h1>DTDC Integration Test</h1>\n";
echo "<style>body{font-family:Arial,sans-serif;margin:20px;} .success{color:green;} .error{color:red;} .info{color:blue;}</style>\n";

// Test 1: Check if DTDC API class loads
echo "<h2>Test 1: DTDC API Class Loading</h2>\n";
try {
    $dtdcApi = new DTDCAPI();
    echo "<span class='success'>✓ DTDC API class loaded successfully</span><br>\n";
} catch (Exception $e) {
    echo "<span class='error'>✗ Failed to load DTDC API class: " . $e->getMessage() . "</span><br>\n";
}

// Test 2: Check configuration
echo "<h2>Test 2: Configuration Check</h2>\n";
try {
    $config = include 'config/dtdc_config.php';
    echo "<span class='info'>• Service Enabled: " . ($config['service']['enabled'] ? 'Yes' : 'No') . "</span><br>\n";
    echo "<span class='info'>• API Base URL: " . $config['api']['base_url'] . "</span><br>\n";
    echo "<span class='info'>• Username Set: " . (!empty($config['api']['username']) ? 'Yes' : 'No') . "</span><br>\n";
    echo "<span class='success'>✓ Configuration loaded successfully</span><br>\n";
} catch (Exception $e) {
    echo "<span class='error'>✗ Failed to load configuration: " . $e->getMessage() . "</span><br>\n";
}

// Test 3: Check database tables
echo "<h2>Test 3: Database Tables Check</h2>\n";
try {
    global $pdo;
    
    $tables = ['dtdc_orders', 'dtdc_tracking_events', 'dtdc_api_logs'];
    foreach ($tables as $table) {
        $stmt = $pdo->prepare("SHOW TABLES LIKE ?");
        $stmt->execute([$table]);
        if ($stmt->fetch()) {
            echo "<span class='success'>✓ Table '$table' exists</span><br>\n";
        } else {
            echo "<span class='error'>✗ Table '$table' does not exist</span><br>\n";
        }
    }
    
    // Check if orders table has DTDC columns
    $stmt = $pdo->prepare("SHOW COLUMNS FROM orders LIKE 'dtdc_%'");
    $stmt->execute();
    $dtdcColumns = $stmt->fetchAll();
    if (count($dtdcColumns) >= 3) {
        echo "<span class='success'>✓ Orders table has DTDC columns</span><br>\n";
    } else {
        echo "<span class='error'>✗ Orders table missing DTDC columns</span><br>\n";
    }
    
} catch (Exception $e) {
    echo "<span class='error'>✗ Database check failed: " . $e->getMessage() . "</span><br>\n";
}

// Test 4: Check cache directory
echo "<h2>Test 4: Cache Directory Check</h2>\n";
$cacheDir = __DIR__ . '/cache';
if (is_dir($cacheDir)) {
    if (is_writable($cacheDir)) {
        echo "<span class='success'>✓ Cache directory exists and is writable</span><br>\n";
    } else {
        echo "<span class='error'>✗ Cache directory exists but is not writable</span><br>\n";
    }
} else {
    echo "<span class='info'>• Creating cache directory...</span><br>\n";
    if (mkdir($cacheDir, 0755, true)) {
        echo "<span class='success'>✓ Cache directory created successfully</span><br>\n";
    } else {
        echo "<span class='error'>✗ Failed to create cache directory</span><br>\n";
    }
}

// Test 5: Test cache functions
echo "<h2>Test 5: Cache Functions Test</h2>\n";
try {
    $testData = ['test' => 'data', 'timestamp' => time()];
    $testTrackingId = 'TEST123456';
    
    if (setDTDCCache($testTrackingId, $testData)) {
        echo "<span class='success'>✓ Cache write function works</span><br>\n";
        
        $retrievedData = getDTDCCache($testTrackingId);
        if ($retrievedData && $retrievedData['test'] === 'data') {
            echo "<span class='success'>✓ Cache read function works</span><br>\n";
        } else {
            echo "<span class='error'>✗ Cache read function failed</span><br>\n";
        }
    } else {
        echo "<span class='error'>✗ Cache write function failed</span><br>\n";
    }
    
    // Clean up test cache file
    $cacheFile = $cacheDir . '/dtdc_' . md5($testTrackingId) . '.json';
    if (file_exists($cacheFile)) {
        unlink($cacheFile);
    }
    
} catch (Exception $e) {
    echo "<span class='error'>✗ Cache functions test failed: " . $e->getMessage() . "</span><br>\n";
}

// Test 6: Check if there are any orders for testing
echo "<h2>Test 6: Sample Data Check</h2>\n";
try {
    global $pdo;
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM orders");
    $stmt->execute();
    $result = $stmt->fetch();
    echo "<span class='info'>• Total orders in database: " . $result['count'] . "</span><br>\n";
    
    if ($result['count'] > 0) {
        echo "<span class='success'>✓ Sample orders available for testing</span><br>\n";
    } else {
        echo "<span class='info'>• No orders found - create some orders to test DTDC integration</span><br>\n";
    }
    
} catch (Exception $e) {
    echo "<span class='error'>✗ Failed to check sample data: " . $e->getMessage() . "</span><br>\n";
}

// Test 7: API Configuration Validation
echo "<h2>Test 7: API Configuration Validation</h2>\n";
try {
    $dtdcApi = new DTDCAPI();
    
    if ($dtdcApi->isEnabled()) {
        echo "<span class='success'>✓ DTDC service is enabled</span><br>\n";
    } else {
        echo "<span class='info'>• DTDC service is disabled in configuration</span><br>\n";
    }
    
    $config = $dtdcApi->getConfig('api');
    if ($config && !empty($config['username']) && !empty($config['password'])) {
        echo "<span class='success'>✓ API credentials are configured</span><br>\n";
    } else {
        echo "<span class='error'>✗ API credentials are missing or incomplete</span><br>\n";
        echo "<span class='info'>• Please update your DTDC API credentials in config/dtdc_config.php</span><br>\n";
    }
    
} catch (Exception $e) {
    echo "<span class='error'>✗ API configuration validation failed: " . $e->getMessage() . "</span><br>\n";
}

echo "<h2>Test Summary</h2>\n";
echo "<p><strong>Next Steps:</strong></p>\n";
echo "<ol>\n";
echo "<li>Update your DTDC API credentials in <code>config/dtdc_config.php</code></li>\n";
echo "<li>Run the database schema from <code>database/dtdc_tracking_schema.sql</code></li>\n";
echo "<li>Test creating a DTDC order from the admin panel</li>\n";
echo "<li>Verify tracking functionality on the frontend</li>\n";
echo "</ol>\n";

echo "<p><strong>Documentation:</strong> See <code>DTDC_INTEGRATION_SETUP.md</code> for detailed setup instructions.</p>\n";
?>
