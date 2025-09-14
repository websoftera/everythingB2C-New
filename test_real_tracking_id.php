<?php
/**
 * Test with Real DTDC Tracking ID
 */

require_once 'includes/dtdc_api_new.php';

echo "<h1>Testing Real DTDC Tracking ID</h1>\n";
echo "<style>body{font-family:Arial,sans-serif;margin:20px;} .success{color:green;} .error{color:red;} .info{color:blue;} .debug{background:#f0f0f0;padding:10px;margin:10px 0;}</style>\n";

$trackingId = '7D154319925'; // Real tracking ID from user's shipment

echo "<h2>Testing with Real Tracking ID: $trackingId</h2>\n";
echo "<p><strong>From User's DTDC Platform:</strong></p>\n";
echo "<ul>\n";
echo "<li><strong>Origin:</strong> PUNE, 411028</li>\n";
echo "<li><strong>Destination:</strong> RAIGAD, 410206</li>\n";
echo "<li><strong>Service:</strong> B2C Smart Express [D71]</li>\n";
echo "<li><strong>Status:</strong> In Transit</li>\n";
echo "<li><strong>Customer Ref:</strong> 128741103297</li>\n";
echo "</ul>\n";

try {
    // Initialize DTDC API
    $dtdcApi = new DTDCAPINew();
    
    echo "<h3>Step 1: Authentication</h3>\n";
    
    // Test authentication
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
    echo "<p><strong>Token:</strong> " . htmlspecialchars($authResponse) . "</p>\n";
    echo "</div>\n";
    
    if ($authHttpCode === 200 && !empty(trim($authResponse))) {
        echo "<div class='success'>\n";
        echo "<h4>✅ Authentication Successful!</h4>\n";
        echo "</div>\n";
        
        echo "<h3>Step 2: Testing Tracking API</h3>\n";
        
        $token = trim($authResponse);
        $endpoint = $dtdcApi->getConfig('api.base_url') . $dtdcApi->getConfig('api.endpoints.tracking');
        
        // Test different parameter combinations
        $testCases = [
            'case1' => [
                'trkType' => 'cnno',
                'strcnno' => $trackingId,
                'addtnlDtl' => 'Y'
            ],
            'case2' => [
                'trkType' => 'reference',
                'strcnno' => $trackingId,
                'addtnlDtl' => 'Y'
            ],
            'case3' => [
                'trkType' => 'cnno',
                'strcnno' => $trackingId,
                'addtnlDtl' => 'N'
            ]
        ];
        
        foreach ($testCases as $caseName => $data) {
            echo "<div class='debug'>\n";
            echo "<h4>Testing $caseName:</h4>\n";
            echo "<p><strong>Data:</strong> " . json_encode($data) . "</p>\n";
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $endpoint);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Accept: application/json',
                'User-Agent: EverythingB2C/1.0',
                'X-Access-Token: ' . $token
            ]);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            
            $startTime = microtime(true);
            $response = curl_exec($ch);
            $endTime = microtime(true);
            $responseTime = round(($endTime - $startTime) * 1000, 2);
            
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            
            curl_close($ch);
            
            echo "<p><strong>Response Time:</strong> {$responseTime}ms</p>\n";
            echo "<p><strong>HTTP Code:</strong> $httpCode</p>\n";
            echo "<p><strong>cURL Error:</strong> " . ($curlError ?: 'None') . "</p>\n";
            
            if ($response) {
                $decodedResponse = json_decode($response, true);
                if ($decodedResponse) {
                    echo "<p><strong>Status Code:</strong> " . ($decodedResponse['statusCode'] ?? 'N/A') . "</p>\n";
                    echo "<p><strong>Status:</strong> " . ($decodedResponse['status'] ?? 'N/A') . "</p>\n";
                    echo "<p><strong>Status Flag:</strong> " . ($decodedResponse['statusFlag'] ? 'true' : 'false') . "</p>\n";
                    
                    if (isset($decodedResponse['errorDetails']) && is_array($decodedResponse['errorDetails'])) {
                        echo "<p><strong>Error Details:</strong></p>\n";
                        echo "<ul>\n";
                        foreach ($decodedResponse['errorDetails'] as $error) {
                            echo "<li>" . ($error['name'] ?? 'N/A') . ": " . ($error['value'] ?? 'N/A') . "</li>\n";
                        }
                        echo "</ul>\n";
                    }
                    
                    if (isset($decodedResponse['trackHeader'])) {
                        echo "<div class='success'>\n";
                        echo "<p><strong>✅ SUCCESS! Found tracking data!</strong></p>\n";
                        echo "<p><strong>Shipment No:</strong> " . ($decodedResponse['trackHeader']['strShipmentNo'] ?? 'N/A') . "</p>\n";
                        echo "<p><strong>Status:</strong> " . ($decodedResponse['trackHeader']['strStatus'] ?? 'N/A') . "</p>\n";
                        echo "<p><strong>Origin:</strong> " . ($decodedResponse['trackHeader']['strOrigin'] ?? 'N/A') . "</p>\n";
                        echo "<p><strong>Destination:</strong> " . ($decodedResponse['trackHeader']['strDestination'] ?? 'N/A') . "</p>\n";
                        echo "</div>\n";
                        break; // Stop testing if we found success
                    }
                }
                
                echo "<p><strong>Full Response:</strong></p>\n";
                echo "<pre>" . htmlspecialchars(json_encode($decodedResponse, JSON_PRETTY_PRINT)) . "</pre>\n";
            }
            
            echo "</div>\n";
        }
        
    } else {
        echo "<div class='error'>\n";
        echo "<h4>❌ Authentication Failed</h4>\n";
        echo "</div>\n";
    }
    
} catch (Exception $e) {
    echo "<div class='error'>\n";
    echo "<h3>❌ Exception Occurred</h3>\n";
    echo "<p><strong>Error:</strong> " . htmlspecialchars($e->getMessage()) . "</p>\n";
    echo "</div>\n";
}

echo "<hr>\n";
echo "<h2>Analysis</h2>\n";
echo "<p>This test will help us understand why the real tracking ID is not working with the API, even though it exists in your DTDC platform.</p>\n";
echo "<p><strong>Possible Issues:</strong></p>\n";
echo "<ul>\n";
echo "<li>API permissions might be limited to certain tracking ID formats</li>\n";
echo "<li>Tracking ID might need to be in a different format</li>\n";
echo "<li>API might require different authentication parameters</li>\n";
echo "<li>Account might need additional API permissions</li>\n";
echo "</ul>\n";
?>
