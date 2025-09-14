<?php
/**
 * Test Official DTDC API Implementation
 */

require_once 'includes/dtdc_api_new.php';

echo "<h1>Testing Official DTDC API</h1>\n";
echo "<style>body{font-family:Arial,sans-serif;margin:20px;} .success{color:green;} .error{color:red;} .info{color:blue;} .debug{background:#f0f0f0;padding:10px;margin:10px 0;}</style>\n";

$trackingId = 'D1005560078';

echo "<h2>Testing with Tracking ID: $trackingId</h2>\n";

try {
    // Initialize DTDC API
    $dtdcApi = new DTDCAPINew();
    
    echo "<h3>API Configuration:</h3>\n";
    echo "<ul>\n";
    echo "<li><strong>Base URL:</strong> " . $dtdcApi->getConfig('api.base_url') . "</li>\n";
    echo "<li><strong>Username:</strong> " . $dtdcApi->getConfig('api.username') . "</li>\n";
    echo "<li><strong>Password:</strong> " . str_repeat('*', strlen($dtdcApi->getConfig('api.password'))) . "</li>\n";
    echo "<li><strong>Auth Endpoint:</strong> " . $dtdcApi->getConfig('api.endpoints.authenticate') . "</li>\n";
    echo "<li><strong>Tracking Endpoint:</strong> " . $dtdcApi->getConfig('api.endpoints.tracking') . "</li>\n";
    echo "</ul>\n";
    
    echo "<h3>Step 1: Testing Authentication</h3>\n";
    
    // Test authentication first
    $authUrl = $dtdcApi->getConfig('api.base_url') . $dtdcApi->getConfig('api.endpoints.authenticate');
    $username = $dtdcApi->getConfig('api.username');
    $password = $dtdcApi->getConfig('api.password');
    $fullAuthUrl = $authUrl . '?username=' . urlencode($username) . '&password=' . urlencode($password);
    
    echo "<p><strong>Auth URL:</strong> $fullAuthUrl</p>\n";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $fullAuthUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Accept: text/plain',
        'User-Agent: EverythingB2C/1.0'
    ]);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    
    $startTime = microtime(true);
    $authResponse = curl_exec($ch);
    $endTime = microtime(true);
    $authResponseTime = round(($endTime - $startTime) * 1000, 2);
    
    $authHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $authCurlError = curl_error($ch);
    
    curl_close($ch);
    
    echo "<div class='debug'>\n";
    echo "<h4>Authentication Response:</h4>\n";
    echo "<p><strong>Response Time:</strong> {$authResponseTime}ms</p>\n";
    echo "<p><strong>HTTP Code:</strong> $authHttpCode</p>\n";
    echo "<p><strong>cURL Error:</strong> " . ($authCurlError ?: 'None') . "</p>\n";
    echo "<p><strong>Response:</strong> " . htmlspecialchars($authResponse) . "</p>\n";
    echo "</div>\n";
    
    if ($authHttpCode === 200 && !empty(trim($authResponse))) {
        echo "<div class='success'>\n";
        echo "<h4>✅ Authentication Successful!</h4>\n";
        echo "<p>Token received: " . htmlspecialchars(substr(trim($authResponse), 0, 20)) . "...</p>\n";
        echo "</div>\n";
        
        echo "<h3>Step 2: Testing Tracking API</h3>\n";
        
        // Now test tracking
        $trackingData = $dtdcApi->trackShipment($trackingId);
        
        if ($trackingData) {
            echo "<div class='success'>\n";
            echo "<h4>✅ Tracking API Successful!</h4>\n";
            echo "<p><strong>Tracking ID:</strong> " . ($trackingData['tracking_id'] ?? 'N/A') . "</p>\n";
            echo "<p><strong>Status:</strong> " . ($trackingData['status'] ?? 'N/A') . "</p>\n";
            echo "<p><strong>Status Description:</strong> " . ($trackingData['status_description'] ?? 'N/A') . "</p>\n";
            echo "<p><strong>Current Location:</strong> " . ($trackingData['current_location'] ?? 'N/A') . "</p>\n";
            echo "<p><strong>Delivery Date:</strong> " . ($trackingData['delivery_date'] ?? 'N/A') . "</p>\n";
            echo "<p><strong>Mapped Status:</strong> " . ($trackingData['mapped_status'] ?? 'N/A') . "</p>\n";
            
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
            echo "<h4>❌ Tracking API Failed</h4>\n";
            echo "<p>The tracking API returned no data. Check the logs for details.</p>\n";
            echo "</div>\n";
        }
        
    } else {
        echo "<div class='error'>\n";
        echo "<h4>❌ Authentication Failed</h4>\n";
        echo "<p>Could not get authentication token. Check credentials and network connectivity.</p>\n";
        echo "</div>\n";
    }
    
} catch (Exception $e) {
    echo "<div class='error'>\n";
    echo "<h3>❌ Exception Occurred</h3>\n";
    echo "<p><strong>Error:</strong> " . htmlspecialchars($e->getMessage()) . "</p>\n";
    echo "</div>\n";
}

echo "<hr>\n";
echo "<h2>Summary</h2>\n";
echo "<p>This test verifies the official DTDC API implementation using the correct endpoints and authentication method from the documentation.</p>\n";
echo "<p><strong>Key Changes Made:</strong></p>\n";
echo "<ul>\n";
echo "<li>✅ Updated to official production endpoint: <code>https://blktracksvc.dtdc.com/dtdc-api</code></li>\n";
echo "<li>✅ Implemented proper authentication flow (GET token first)</li>\n";
echo "<li>✅ Updated request format to match documentation (POST with X-Access-Token header)</li>\n";
echo "<li>✅ Fixed parameter names (trkType, strcnno, addtnlDtl)</li>\n";
echo "<li>✅ Added proper response parsing for JSON format</li>\n";
echo "</ul>\n";
?>
