<?php
/**
 * Comprehensive DTDC API Test
 * Tests multiple possible endpoints and authentication methods
 */

echo "<h1>Comprehensive DTDC API Test</h1>\n";
echo "<style>body{font-family:Arial,sans-serif;margin:20px;} .success{color:green;} .error{color:red;} .info{color:blue;} .debug{background:#f0f0f0;padding:10px;margin:10px 0;}</style>\n";

// Your credentials
$username = 'PL3537_trk_json';
$password = 'wafBo';
$apiKey = 'bbb8196c734d8487983936199e880072';
$token = 'PL3537_trk_json:bbb8196c734d8487983936199e880072';
$customerCode = 'PL3537';
$trackingId = 'D1005560078';

// Multiple possible endpoints to test
$endpoints = [
    // Standard DTDC API endpoints
    'https://apis.dtdc.in/apis/tracking',
    'https://apis.dtdc.in/apis/v1/tracking',
    'https://api.dtdc.com/api/v1/tracking',
    'https://api.dtdc.com/tracking',
    'https://apis.dtdc.in/tracking',
    
    // Alternative endpoints
    'https://customer.dtdc.in/api/tracking',
    'https://customer.dtdc.in/apis/tracking',
    'https://tracking.dtdc.in/api/tracking',
    'https://tracking.dtdc.in/apis/tracking',
    
    // Legacy endpoints
    'https://www.dtdc.in/api/tracking',
    'https://www.dtdc.in/apis/tracking',
    'https://dtdc.in/api/tracking',
    'https://dtdc.in/apis/tracking'
];

// Different request data formats to test
$requestFormats = [
    'format1' => [
        'awbno' => $trackingId,
        'username' => $username,
        'password' => $password,
        'token' => $apiKey
    ],
    'format2' => [
        'tracking_id' => $trackingId,
        'username' => $username,
        'password' => $password
    ],
    'format3' => [
        'awbno' => $trackingId,
        'customer_code' => $customerCode,
        'password' => $password
    ],
    'format4' => [
        'awbno' => $trackingId,
        'username' => $username,
        'password' => $password
    ],
    'format5' => [
        'tracking_number' => $trackingId,
        'username' => $username,
        'password' => $password
    ]
];

// Different authentication header formats
$authHeaders = [
    'auth1' => 'Authorization: ' . $token,
    'auth2' => 'Authorization: Bearer ' . $apiKey,
    'auth3' => 'Authorization: ' . $username . ':' . $apiKey,
    'auth4' => 'X-API-Key: ' . $apiKey,
    'auth5' => 'X-Auth-Token: ' . $apiKey,
    'auth6' => 'X-Customer-Code: ' . $customerCode
];

echo "<h2>Testing " . count($endpoints) . " endpoints with " . count($requestFormats) . " request formats and " . count($authHeaders) . " auth methods</h2>\n";
echo "<p><strong>Total combinations:</strong> " . (count($endpoints) * count($requestFormats) * count($authHeaders)) . "</p>\n";

$successCount = 0;
$totalTests = 0;

foreach ($endpoints as $endpoint) {
    echo "<div class='debug'>\n";
    echo "<h3>Testing Endpoint: $endpoint</h3>\n";
    
    foreach ($requestFormats as $formatName => $data) {
        foreach ($authHeaders as $authName => $authHeader) {
            $totalTests++;
            
            echo "<p><strong>Test $totalTests:</strong> $formatName + $authName</p>\n";
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $endpoint);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Accept: application/json',
                'User-Agent: EverythingB2C/1.0',
                $authHeader
            ]);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            
            $startTime = microtime(true);
            $response = curl_exec($ch);
            $endTime = microtime(true);
            $responseTime = round(($endTime - $startTime) * 1000, 2);
            
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            $effectiveUrl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
            
            curl_close($ch);
            
            // Check if this looks like a successful response
            $isSuccess = false;
            $responsePreview = '';
            
            if ($response && strlen($response) > 0) {
                $responsePreview = substr($response, 0, 200);
                
                // Check for success indicators
                if (strpos($response, 'success') !== false || 
                    strpos($response, 'tracking') !== false ||
                    strpos($response, 'status') !== false ||
                    strpos($response, 'delivered') !== false ||
                    strpos($response, 'transit') !== false ||
                    $httpCode == 200) {
                    $isSuccess = true;
                    $successCount++;
                }
            }
            
            if ($isSuccess) {
                echo "<span class='success'>✅ SUCCESS!</span> ";
            } else {
                echo "<span class='error'>❌ Failed</span> ";
            }
            
            echo "HTTP: $httpCode, Time: {$responseTime}ms";
            
            if ($curlError) {
                echo ", Error: $curlError";
            }
            
            if ($effectiveUrl !== $endpoint) {
                echo ", Redirected to: $effectiveUrl";
            }
            
            echo "<br>\n";
            
            if ($isSuccess && strlen($response) > 0) {
                echo "<div style='background:#e8f5e8;padding:5px;margin:5px 0;'>\n";
                echo "<strong>Response Preview:</strong><br>\n";
                echo "<pre>" . htmlspecialchars($responsePreview) . "...</pre>\n";
                echo "</div>\n";
                
                // Try to decode JSON
                $decoded = json_decode($response, true);
                if ($decoded) {
                    echo "<div style='background:#e8f5e8;padding:5px;margin:5px 0;'>\n";
                    echo "<strong>Decoded JSON:</strong><br>\n";
                    echo "<pre>" . htmlspecialchars(json_encode($decoded, JSON_PRETTY_PRINT)) . "</pre>\n";
                    echo "</div>\n";
                }
            }
            
            // Stop testing this endpoint if we found a working combination
            if ($isSuccess) {
                echo "<p><strong>🎉 Found working combination for this endpoint!</strong></p>\n";
                break 2; // Break out of both inner loops
            }
        }
    }
    
    echo "</div>\n";
}

echo "<h2>Test Summary</h2>\n";
echo "<p><strong>Total Tests:</strong> $totalTests</p>\n";
echo "<p><strong>Successful Tests:</strong> $successCount</p>\n";

if ($successCount > 0) {
    echo "<div class='success'>\n";
    echo "<h3>🎉 SUCCESS! Found working API endpoint(s)</h3>\n";
    echo "<p>The working combinations are highlighted above. Use these settings in your configuration.</p>\n";
    echo "</div>\n";
} else {
    echo "<div class='error'>\n";
    echo "<h3>❌ No working API endpoints found</h3>\n";
    echo "<p>Possible issues:</p>\n";
    echo "<ul>\n";
    echo "<li>Network connectivity problems</li>\n";
    echo "<li>DTDC API endpoints have changed</li>\n";
    echo "<li>API credentials are incorrect</li>\n";
    echo "<li>API requires different authentication method</li>\n";
    echo "<li>Tracking ID format is incorrect</li>\n";
    echo "</ul>\n";
    echo "<p><strong>Recommendation:</strong> Contact DTDC support to verify the correct API endpoints and authentication method.</p>\n";
    echo "</div>\n";
}

echo "<h2>Next Steps</h2>\n";
echo "<ol>\n";
echo "<li>If successful: Update your DTDC configuration with the working endpoint and format</li>\n";
echo "<li>If failed: Contact DTDC support with your credentials to get the correct API documentation</li>\n";
echo "<li>Test with a different tracking ID if available</li>\n";
echo "<li>Check if your IP address needs to be whitelisted for API access</li>\n";
echo "</ol>\n";
?>
