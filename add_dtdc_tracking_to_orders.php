<?php
/**
 * Add DTDC Tracking ID to Existing Orders
 * 
 * This script adds the DTDC tracking ID D1005560078 to all existing orders
 * so you can test the DTDC integration.
 */

require_once 'config/database.php';

$dtdcTrackingId = 'D1005560078';

echo "<h1>Adding DTDC Tracking ID to Existing Orders</h1>\n";
echo "<style>body{font-family:Arial,sans-serif;margin:20px;} .success{color:green;} .error{color:red;} .info{color:blue;}</style>\n";

try {
    global $pdo;
    
    // Get all orders that don't have DTDC tracking ID
    $stmt = $pdo->prepare("SELECT id, tracking_id, order_status_id FROM orders WHERE (dtdc_tracking_id IS NULL OR dtdc_tracking_id = '') AND (external_tracking_id IS NULL OR external_tracking_id = '')");
    $stmt->execute();
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<p><strong>Found " . count($orders) . " orders without DTDC tracking ID.</strong></p>\n";
    
    if (count($orders) > 0) {
        echo "<p>Adding DTDC tracking ID <strong>$dtdcTrackingId</strong> to all orders...</p>\n";
        
        $updatedCount = 0;
        foreach ($orders as $order) {
            try {
                // Update the order with DTDC tracking ID
                $updateStmt = $pdo->prepare("UPDATE orders SET external_tracking_id = ?, dtdc_enabled = 1 WHERE id = ?");
                $result = $updateStmt->execute([$dtdcTrackingId, $order['id']]);
                
                if ($result) {
                    $updatedCount++;
                    echo "<span class='success'>✓ Order #{$order['id']} updated</span><br>\n";
                } else {
                    echo "<span class='error'>✗ Failed to update Order #{$order['id']}</span><br>\n";
                }
            } catch (Exception $e) {
                echo "<span class='error'>✗ Error updating Order #{$order['id']}: " . $e->getMessage() . "</span><br>\n";
            }
        }
        
        echo "<hr>\n";
        echo "<p><strong>Update Summary:</strong></p>\n";
        echo "<p><span class='success'>✓ Successfully updated $updatedCount orders</span></p>\n";
        echo "<p><span class='info'>• DTDC Tracking ID: $dtdcTrackingId</span></p>\n";
        echo "<p><span class='info'>• All orders now have DTDC tracking enabled</span></p>\n";
        
    } else {
        echo "<p><span class='info'>• All orders already have DTDC tracking IDs</span></p>\n";
    }
    
    echo "<hr>\n";
    echo "<h2>How to Test DTDC Tracking</h2>\n";
    echo "<ol>\n";
    echo "<li><strong>Go to your website</strong> → Track Order page</li>\n";
    echo "<li><strong>Enter any order tracking ID</strong> from your orders</li>\n";
    echo "<li><strong>You should see:</strong></li>\n";
    echo "<ul>\n";
    echo "<li>Green 'DTDC Live Tracking' section</li>\n";
    echo "<li>Current status from DTDC</li>\n";
    echo "<li>Refresh and View Details buttons</li>\n";
    echo "<li>DTDC tracking events timeline</li>\n";
    echo "</ul>\n";
    echo "<li><strong>Click 'Refresh'</strong> to get latest tracking data</li>\n";
    echo "<li><strong>Click 'View Details'</strong> to see detailed tracking information</li>\n";
    echo "</ol>\n";
    
    echo "<h2>Admin Panel Testing</h2>\n";
    echo "<ol>\n";
    echo "<li><strong>Go to Admin Panel</strong> → Orders</li>\n";
    echo "<li><strong>Find any order</strong> - you should see DTDC buttons</li>\n";
    echo "<li><strong>Click the shipping truck icon</strong> to view DTDC tracking</li>\n";
    echo "<li><strong>Click refresh icon</strong> to update tracking data</li>\n";
    echo "</ol>\n";
    
} catch (Exception $e) {
    echo "<span class='error'>✗ Database error: " . $e->getMessage() . "</span><br>\n";
}

echo "<hr>\n";
echo "<p><strong>Note:</strong> All orders now use the same DTDC tracking ID for testing purposes. In production, each order should have its own unique DTDC tracking ID.</p>\n";
?>
