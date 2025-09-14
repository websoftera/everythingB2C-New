<?php
/**
 * Debug DTDC API Call
 * 
 * This script will help debug the DTDC API integration
 */

// Test the DTDC API call directly with exact credentials
$trackingId = 'D1005560078';
$username = 'PL3537_trk_json';
$password = 'wafBo';
$apiKey = 'bbb8196c734d8487983936199e880072';
$url = 'https://apis.dtdc.in/apis/tracking';

echo "<h1>DTDC API Debug Test</h1>\n";
echo "<style>body{font-family:Arial,sans-serif;margin:20px;} .success{color:green;} .error{color:red;} .info{color:blue;} .debug{background:#f5f5f5;padding:10px;margin:10px 0;border:1px solid #ddd;}</style>\n";

$data = [
    'tracking_id' => $trackingId,
    'username' => $username,
    'password' => $password
];

echo "<div class='debug'>";
echo "<h2>Request Details</h2>\n";
echo "<p><strong>URL:</strong> $url</p>\n";
echo "<p><strong>Method:</strong> POST</p>\n";
echo "<p><strong>Tracking ID:</strong> $trackingId</p>\n";
echo "<p><strong>Username:</strong> $username</p>\n";
echo "<p><strong>Password:</strong> " . str_repeat('*', strlen($password)) . "</p>\n";
echo "<p><strong>API Key:</strong> " . substr($apiKey, 0, 10) . "...</p>\n";
echo "</div>";

$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => $url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_CONNECTTIMEOUT => 10,
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => json_encode($data),
    CURLOPT_HTTPHEADER => [
        'Content-Type: application/json',
        'Accept: application/json',
        'User-Agent: EverythingB2C/1.0',
        'X-API-Key: ' . $apiKey,
        'Authorization: ' . $username . ':' . $apiKey
    ],
    CURLOPT_VERBOSE => true,
    CURLOPT_STDERR => fopen('php://temp', 'w+')
]);

echo "<div class='debug'>";
echo "<h2>Making API Call...</h2>\n";

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
$info = curl_getinfo($ch);

// Get verbose output
rewind(curl_getinfo($ch, CURLOPT_STDERR));
$verboseOutput = stream_get_contents(curl_getinfo($ch, CURLOPT_STDERR));

curl_close($ch);

echo "<h3>Results</h3>\n";
echo "<p><strong>HTTP Status Code:</strong> $httpCode</p>\n";
echo "<p><strong>cURL Error:</strong> " . ($error ?: 'None') . "</p>\n";
echo "<p><strong>Response Size:</strong> " . strlen($response) . " bytes</p>\n";
echo "<p><strong>Response Time:</strong> " . $info['total_time'] . " seconds</p>\n";
echo "</div>";

echo "<div class='debug'>";
echo "<h3>Raw Response</h3>\n";
echo "<pre>" . htmlspecialchars($response) . "</pre>\n";
echo "</div>";

echo "<div class='debug'>";
echo "<h3>cURL Verbose Output</h3>\n";
echo "<pre>" . htmlspecialchars($verboseOutput) . "</pre>\n";
echo "</div>";

// Try to parse JSON response
echo "<div class='debug'>";
echo "<h3>JSON Parsing</h3>\n";
$jsonData = json_decode($response, true);
if ($jsonData) {
    echo "<p><span class='success'>✓ JSON parsed successfully</span></p>\n";
    echo "<pre>" . htmlspecialchars(json_encode($jsonData, JSON_PRETTY_PRINT)) . "</pre>\n";
} else {
    echo "<p><span class='error'>✗ JSON parsing failed: " . json_last_error_msg() . "</span></p>\n";
}
echo "</div>";

// Test alternative API endpoints
echo "<div class='debug'>";
echo "<h3>Testing Alternative Endpoints</h3>\n";

$alternativeEndpoints = [
    'https://apis.dtdc.in/apis/tracking',
    'https://apis.dtdc.in/apis/v1/tracking',
    'https://api.dtdc.in/tracking',
    'https://track.dtdc.in/api/tracking',
    'https://apis.dtdc.in/tracking',
    'https://customer.dtdc.in/api/tracking'
];

foreach ($alternativeEndpoints as $endpoint) {
    echo "<p><strong>Testing:</strong> $endpoint</p>\n";
    
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $endpoint,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 10,
        CURLOPT_CONNECTTIMEOUT => 5,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($data),
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'Accept: application/json',
            'User-Agent: EverythingB2C/1.0',
            'X-API-Key: ' . $apiKey,
            'Authorization: ' . $username . ':' . $apiKey
        ]
    ]);
    
    $testResponse = curl_exec($ch);
    $testHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $testError = curl_error($ch);
    curl_close($ch);
    
    if ($testError) {
        echo "<p><span class='error'>✗ Error: $testError</span></p>\n";
    } else {
        echo "<p><span class='info'>• HTTP $testHttpCode - Response: " . substr($testResponse, 0, 100) . "...</span></p>\n";
    }
}
echo "</div>";

echo "<div class='debug'>";
echo "<h3>Recommendations</h3>\n";
echo "<ol>\n";
echo "<li><strong>Check your DTDC API documentation</strong> for the correct endpoint URL</li>\n";
echo "<li><strong>Verify your credentials</strong> with DTDC support</li>\n";
echo "<li><strong>Check if the tracking ID exists</strong> in DTDC system</li>\n";
echo "<li><strong>Contact DTDC support</strong> at support@dtdc.in for API access</li>\n";
echo "<li><strong>Check error logs</strong> in your server for more details</li>\n";
echo "</ol>\n";
echo "</div>";

// Check error logs
echo "<div class='debug'>";
echo "<h3>Recent Error Logs</h3>\n";
$errorLog = error_get_last();
if ($errorLog) {
    echo "<p><strong>Last Error:</strong> " . htmlspecialchars($errorLog['message']) . "</p>\n";
} else {
    echo "<p>No recent PHP errors</p>\n";
}
echo "</div>";
?>
