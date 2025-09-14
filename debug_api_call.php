<?php
/**
 * Debug DTDC API Call
 */

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>DTDC API Debug Test</h1>\n";
echo "<style>body{font-family:Arial,sans-serif;margin:20px;} .success{color:green;} .error{color:red;} .info{color:blue;} .debug{background:#f0f0f0;padding:10px;margin:10px 0;}</style>\n";

// Load configuration
$config = include 'config/dtdc_config.php';
echo "<div class='debug'>\n";
echo "<h3>Configuration Loaded:</h3>\n";
echo "<pre>" . htmlspecialchars(json_encode($config, JSON_PRETTY_PRINT)) . "</pre>\n";
echo "</div>\n";

// Test the API call manually
$trackingId = 'D1005560078';
$baseUrl = $config['api']['base_url'];
$endpoint = $baseUrl . $config['api']['endpoints']['tracking'];
$username = $config['api']['username'];
$password = $config['api']['password'];
$apiKey = $config['api']['api_key'];

echo "<div class='debug'>\n";
echo "<h3>API Request Details:</h3>\n";
echo "<p><strong>Endpoint:</strong> $endpoint</p>\n";
echo "<p><strong>Username:</strong> $username</p>\n";
echo "<p><strong>Password:</strong> " . str_repeat('*', strlen($password)) . "</p>\n";
echo "<p><strong>API Key:</strong> " . substr($apiKey, 0, 8) . "...</p>\n";
echo "<p><strong>Tracking ID:</strong> $trackingId</p>\n";
echo "</div>\n";

// Prepare request data
$data = [
    'username' => $username,
    'password' => $password,
    'token' => $apiKey,
    'awbno' => $trackingId
];

echo "<div class='debug'>\n";
echo "<h3>Request Data:</h3>\n";
echo "<pre>" . htmlspecialchars(json_encode($data, JSON_PRETTY_PRINT)) . "</pre>\n";
echo "</div>\n";

// Make cURL request
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $endpoint);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json',
    'Authorization: ' . $username . ':' . $apiKey
]);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_VERBOSE, true);

// Capture verbose output
$verbose = fopen('php://temp', 'w+');
curl_setopt($ch, CURLOPT_STDERR, $verbose);

echo "<div class='debug'>\n";
echo "<h3>Making API Call...</h3>\n";
echo "</div>\n";

$startTime = microtime(true);
$response = curl_exec($ch);
$endTime = microtime(true);
$responseTime = round(($endTime - $startTime) * 1000, 2);

// Get cURL info
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);

// Get verbose output
rewind($verbose);
$verboseOutput = stream_get_contents($verbose);
fclose($verbose);

curl_close($ch);

echo "<div class='debug'>\n";
echo "<h3>Response Details:</h3>\n";
echo "<p><strong>Response Time:</strong> {$responseTime}ms</p>\n";
echo "<p><strong>HTTP Code:</strong> $httpCode</p>\n";
echo "<p><strong>cURL Error:</strong> " . ($curlError ?: 'None') . "</p>\n";
echo "</div>\n";

echo "<div class='debug'>\n";
echo "<h3>Raw Response:</h3>\n";
echo "<pre>" . htmlspecialchars($response) . "</pre>\n";
echo "</div>\n";

echo "<div class='debug'>\n";
echo "<h3>cURL Verbose Output:</h3>\n";
echo "<pre>" . htmlspecialchars($verboseOutput) . "</pre>\n";
echo "</div>\n";

// Try to parse response
if ($response) {
    $decodedResponse = json_decode($response, true);
    if ($decodedResponse) {
        echo "<div class='debug'>\n";
        echo "<h3>Decoded Response:</h3>\n";
        echo "<pre>" . htmlspecialchars(json_encode($decodedResponse, JSON_PRETTY_PRINT)) . "</pre>\n";
        echo "</div>\n";
    }
}

// Test alternative endpoints
echo "<h2>Testing Alternative Endpoints</h2>\n";

$alternativeEndpoints = [
    'https://apis.dtdc.in/apis/v1/tracking',
    'https://api.dtdc.com/api/v1/tracking',
    'https://apis.dtdc.in/tracking',
    'https://api.dtdc.com/tracking'
];

foreach ($alternativeEndpoints as $altEndpoint) {
    echo "<div class='debug'>\n";
    echo "<h3>Testing: $altEndpoint</h3>\n";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $altEndpoint);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Accept: application/json',
        'Authorization: ' . $username . ':' . $apiKey
    ]);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    
    $altResponse = curl_exec($ch);
    $altHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $altCurlError = curl_error($ch);
    
    curl_close($ch);
    
    echo "<p><strong>HTTP Code:</strong> $altHttpCode</p>\n";
    echo "<p><strong>cURL Error:</strong> " . ($altCurlError ?: 'None') . "</p>\n";
    echo "<p><strong>Response Length:</strong> " . strlen($altResponse) . " bytes</p>\n";
    
    if ($altResponse && strlen($altResponse) > 0) {
        echo "<p><strong>Response Preview:</strong> " . htmlspecialchars(substr($altResponse, 0, 200)) . "...</p>\n";
    }
    
    echo "</div>\n";
}

echo "<h2>Summary</h2>\n";
echo "<p>This test shows exactly what's happening with the DTDC API call. Check the responses above to see if any endpoint is working.</p>\n";
?>
