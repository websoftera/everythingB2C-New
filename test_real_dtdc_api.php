<?php
/**
 * Test Real DTDC API Call
 */

require_once 'includes/dtdc_api.php';

echo "<h1>Testing Real DTDC API</h1>\n";
echo "<style>body{font-family:Arial,sans-serif;margin:20px;} .success{color:green;} .error{color:red;} .info{color:blue;} .warning{color:orange;}</style>\n";

$trackingId = 'D1005560078';

echo "<h2>Testing with Tracking ID: $trackingId</h2>\n";

try {
    // Initialize DTDC API
    $dtdcApi = new DTDCAPI();
    
    echo "<h3>API Configuration:</h3>\n";
    echo "<ul>\n";
    echo "<li><strong>Base URL:</strong> " . $dtdcApi->getConfig('api.base_url') . "</li>\n";
    echo "<li><strong>Username:</strong> " . $dtdcApi->getConfig('api.username') . "</li>\n";
    echo "<li><strong>Password:</strong> " . str_repeat('*', strlen($dtdcApi->getConfig('api.password'))) . "</li>\n";
    echo "<li><strong>API Key:</strong> " . substr($dtdcApi->getConfig('api.api_key'), 0, 8) . "...</li>\n";
    echo "</ul>\n";
    
    echo "<h3>Making API Call...</h3>\n";
    
    // Make the API call
    $startTime = microtime(true);
    $trackingData = $dtdcApi->trackShipment($trackingId);
    $endTime = microtime(true);
    $responseTime = round(($endTime - $startTime) * 1000, 2);
    
    echo "<p><strong>Response Time:</strong> {$responseTime}ms</p>\n";
    
    if ($trackingData) {
        echo "<div class='success'>\n";
        echo "<h3>✅ API Call Successful!</h3>\n";
        echo "<p><strong>Tracking ID:</strong> " . ($trackingData['tracking_id'] ?? 'N/A') . "</p>\n";
        echo "<p><strong>Status:</strong> " . ($trackingData['status'] ?? 'N/A') . "</p>\n";
        echo "<p><strong>Status Description:</strong> " . ($trackingData['status_description'] ?? 'N/A') . "</p>\n";
        echo "<p><strong>Current Location:</strong> " . ($trackingData['current_location'] ?? 'N/A') . "</p>\n";
        echo "<p><strong>Delivery Date:</strong> " . ($trackingData['delivery_date'] ?? 'N/A') . "</p>\n";
        
        if (isset($trackingData['events']) && is_array($trackingData['events'])) {
            echo "<h4>Tracking Events:</h4>\n";
            echo "<ul>\n";
            foreach ($trackingData['events'] as $event) {
                echo "<li><strong>" . ($event['date'] ?? 'N/A') . " " . ($event['time'] ?? 'N/A') . "</strong> - " . ($event['location'] ?? 'N/A') . " - " . ($event['description'] ?? 'N/A') . "</li>\n";
            }
            echo "</ul>\n";
        }
        
        echo "</div>\n";
        
        echo "<h3>Raw API Response:</h3>\n";
        echo "<pre>" . htmlspecialchars(json_encode($trackingData, JSON_PRETTY_PRINT)) . "</pre>\n";
        
    } else {
        echo "<div class='error'>\n";
        echo "<h3>❌ API Call Failed</h3>\n";
        echo "<p>The API returned no data. This could be due to:</p>\n";
        echo "<ul>\n";
        echo "<li>Invalid API credentials</li>\n";
        echo "<li>Network connectivity issues</li>\n";
        echo "<li>DTDC API server problems</li>\n";
        echo "<li>Incorrect API endpoint configuration</li>\n";
        echo "<li>Invalid tracking ID</li>\n";
        echo "</ul>\n";
        echo "</div>\n";
    }
    
} catch (Exception $e) {
    echo "<div class='error'>\n";
    echo "<h3>❌ Exception Occurred</h3>\n";
    echo "<p><strong>Error:</strong> " . htmlspecialchars($e->getMessage()) . "</p>\n";
    echo "</div>\n";
}

echo "<hr>\n";
echo "<h2>Next Steps:</h2>\n";
echo "<ol>\n";
echo "<li><strong>If API failed:</strong> Check the error logs for detailed information</li>\n";
echo "<li><strong>Contact DTDC Support:</strong> Verify your API credentials and endpoints</li>\n";
echo "<li><strong>Test with different tracking ID:</strong> Try with a known working tracking ID</li>\n";
echo "<li><strong>Check network:</strong> Ensure your server can reach DTDC API endpoints</li>\n";
echo "</ol>\n";

echo "<h2>Debug Information:</h2>\n";
echo "<p>Check the following files for detailed logs:</p>\n";
echo "<ul>\n";
echo "<li><code>ajax_debug.log</code> - General AJAX errors</li>\n";
echo "<li><code>debug_checkout.log</code> - Checkout related errors</li>\n";
echo "<li>PHP error logs - Server-side errors</li>\n";
echo "</ul>\n";
?>
