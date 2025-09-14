<?php
/**
 * Simple DTDC API Test
 * This will help us identify the exact issue
 */

echo "<h1>DTDC API Simple Test</h1>\n";
echo "<style>body{font-family:Arial,sans-serif;margin:20px;} .success{color:green;} .error{color:red;} .info{color:blue;} .debug{background:#f5f5f5;padding:10px;margin:10px 0;border:1px solid #ddd;}</style>\n";

// Your exact credentials
$trackingId = 'D1005560078';
$username = 'PL3537_trk_json';
$password = 'wafBo';
$apiKey = 'bbb8196c734d8487983936199e880072';

echo "<div class='debug'>";
echo "<h2>Testing DTDC API with Your Credentials</h2>\n";
echo "<p><strong>Tracking ID:</strong> $trackingId</p>\n";
echo "<p><strong>Username:</strong> $username</p>\n";
echo "<p><strong>Password:</strong> " . str_repeat('*', strlen($password)) . "</p>\n";
echo "<p><strong>API Key:</strong> " . substr($apiKey, 0, 10) . "...</p>\n";
echo "</div>";

// Test different API endpoints and methods
$endpoints = [
    'https://apis.dtdc.in/apis/tracking',
    'https://apis.dtdc.in/apis/v1/tracking',
    'https://api.dtdc.in/tracking',
    'https://track.dtdc.in/api/tracking'
];

$authMethods = [
    'method1' => [
        'headers' => [
            'Content-Type: application/json',
            'Accept: application/json',
            'X-API-Key: ' . $apiKey
        ],
        'data' => [
            'tracking_id' => $trackingId,
            'username' => $username,
            'password' => $password
        ]
    ],
    'method2' => [
        'headers' => [
            'Content-Type: application/json',
            'Accept: application/json',
            'Authorization: ' . $username . ':' . $apiKey
        ],
        'data' => [
            'tracking_id' => $trackingId,
            'username' => $username,
            'password' => $password
        ]
    ],
    'method3' => [
        'headers' => [
            'Content-Type: application/json',
            'Accept: application/json',
            'X-API-Key: ' . $apiKey,
            'Authorization: ' . $username . ':' . $apiKey
        ],
        'data' => [
            'tracking_id' => $trackingId,
            'username' => $username,
            'password' => $password
        ]
    ],
    'method4' => [
        'headers' => [
            'Content-Type: application/json',
            'Accept: application/json'
        ],
        'data' => [
            'tracking_id' => $trackingId,
            'username' => $username,
            'password' => $password,
            'token' => $apiKey
        ]
    ]
];

foreach ($endpoints as $endpoint) {
    echo "<div class='debug'>";
    echo "<h3>Testing Endpoint: $endpoint</h3>\n";
    
    foreach ($authMethods as $methodName => $method) {
        echo "<p><strong>Testing Method: $methodName</strong></p>\n";
        
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $endpoint,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 15,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($method['data']),
            CURLOPT_HTTPHEADER => $method['headers']
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        $info = curl_getinfo($ch);
        curl_close($ch);
        
        echo "<p><strong>HTTP Code:</strong> $httpCode</p>\n";
        echo "<p><strong>Error:</strong> " . ($error ?: 'None') . "</p>\n";
        echo "<p><strong>Response Time:</strong> " . $info['total_time'] . "s</p>\n";
        
        if ($response) {
            echo "<p><strong>Response Length:</strong> " . strlen($response) . " bytes</p>\n";
            
            // Try to decode JSON
            $jsonData = json_decode($response, true);
            if ($jsonData) {
                echo "<p><span class='success'>✓ Valid JSON Response</span></p>\n";
                echo "<pre>" . htmlspecialchars(json_encode($jsonData, JSON_PRETTY_PRINT)) . "</pre>\n";
                
                // Check if it contains tracking data
                if (isset($jsonData['data']) || isset($jsonData['tracking_data']) || isset($jsonData['status'])) {
                    echo "<p><span class='success'>🎉 SUCCESS! Found tracking data!</span></p>\n";
                }
            } else {
                echo "<p><span class='error'>✗ Invalid JSON</span></p>\n";
                echo "<p><strong>Raw Response:</strong></p>\n";
                echo "<pre>" . htmlspecialchars(substr($response, 0, 500)) . "</pre>\n";
            }
        } else {
            echo "<p><span class='error'>✗ No response</span></p>\n";
        }
        
        echo "<hr style='margin: 10px 0;'>\n";
    }
    echo "</div>";
}

// Test if tracking ID exists on DTDC website
echo "<div class='debug'>";
echo "<h3>Manual Verification</h3>\n";
echo "<p>Please manually verify the tracking ID:</p>\n";
echo "<ol>\n";
echo "<li>Go to <a href='https://www.dtdc.in/tracking' target='_blank'>https://www.dtdc.in/tracking</a></li>\n";
echo "<li>Enter tracking ID: <strong>$trackingId</strong></li>\n";
echo "<li>Check if it returns any data</li>\n";
echo "</ol>\n";
echo "<p>If the tracking ID doesn't work on DTDC's website, it means:</p>\n";
echo "<ul>\n";
echo "<li>The tracking ID doesn't exist in their system</li>\n";
echo "<li>The tracking ID format is incorrect</li>\n";
echo "<li>The tracking ID has expired</li>\n";
echo "</ul>\n";
echo "</div>";

// Contact information
echo "<div class='debug'>";
echo "<h3>Next Steps</h3>\n";
echo "<p>If none of the above methods work:</p>\n";
echo "<ol>\n";
echo "<li><strong>Contact DTDC Support:</strong> support@dtdc.in</li>\n";
echo "<li><strong>Phone:</strong> 1800-123-4567</li>\n";
echo "<li><strong>Login to Customer Portal:</strong> <a href='https://customer.dtdc.in/login' target='_blank'>https://customer.dtdc.in/login</a></li>\n";
echo "<li><strong>Request:</strong> Live API documentation and working tracking ID</li>\n";
echo "</ol>\n";
echo "</div>";
?>
