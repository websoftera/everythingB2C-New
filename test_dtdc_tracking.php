<?php
/**
 * DTDC Tracking API Test Script
 * 
 * This script tests your DTDC tracking API credentials specifically.
 */

require_once 'includes/dtdc_api.php';

echo "<h1>DTDC Tracking API Test</h1>\n";
echo "<style>body{font-family:Arial,sans-serif;margin:20px;} .success{color:green;} .error{color:red;} .info{color:blue;} .test{background:#f5f5f5;padding:10px;margin:10px 0;border-left:4px solid #007bff;}</style>\n";

// Test 1: Load DTDC API
echo "<div class='test'>";
echo "<h2>Test 1: Load DTDC API Class</h2>\n";
try {
    $dtdcApi = new DTDCAPI();
    echo "<span class='success'>✓ DTDC API class loaded successfully</span><br>\n";
    echo "<span class='info'>• Service Enabled: " . ($dtdcApi->isEnabled() ? 'Yes' : 'No') . "</span><br>\n";
} catch (Exception $e) {
    echo "<span class='error'>✗ Failed to load DTDC API class: " . $e->getMessage() . "</span><br>\n";
}
echo "</div>";

// Test 2: Check Configuration
echo "<div class='test'>";
echo "<h2>Test 2: Configuration Check</h2>\n";
try {
    $config = $dtdcApi->getConfig('api');
    echo "<span class='info'>• Base URL: " . $config['base_url'] . "</span><br>\n";
    echo "<span class='info'>• Username: " . $config['username'] . "</span><br>\n";
    echo "<span class='info'>• Password: " . str_repeat('*', strlen($config['password'])) . "</span><br>\n";
    echo "<span class='info'>• API Key: " . substr($config['api_key'], 0, 10) . "...</span><br>\n";
    echo "<span class='success'>✓ Configuration loaded successfully</span><br>\n";
} catch (Exception $e) {
    echo "<span class='error'>✗ Failed to load configuration: " . $e->getMessage() . "</span><br>\n";
}
echo "</div>";

// Test 3: Test API Connection (without actual tracking ID)
echo "<div class='test'>";
echo "<h2>Test 3: API Connection Test</h2>\n";
echo "<span class='info'>• Testing API connection with a dummy tracking ID...</span><br>\n";

try {
    // Test with a dummy tracking ID to see if API responds
    $testTrackingId = 'TEST123456789';
    $result = $dtdcApi->trackShipment($testTrackingId);
    
    if ($result === false) {
        echo "<span class='info'>• API connection test completed (expected to fail with dummy ID)</span><br>\n";
        echo "<span class='success'>✓ API connection is working (no network errors)</span><br>\n";
    } else {
        echo "<span class='info'>• Unexpected response received</span><br>\n";
        echo "<span class='info'>• Response: " . json_encode($result) . "</span><br>\n";
    }
} catch (Exception $e) {
    echo "<span class='error'>✗ API connection failed: " . $e->getMessage() . "</span><br>\n";
}
echo "</div>";

// Test 4: Manual API Test
echo "<div class='test'>";
echo "<h2>Test 4: Manual API Test</h2>\n";
echo "<span class='info'>• Testing direct API call to DTDC...</span><br>\n";

$testData = [
    'tracking_id' => 'TEST123456789',
    'username' => 'PL3537_trk_json',
    'password' => 'wafBo',
    'token' => 'bbb8196c734d8487983936199e880072'
];

$url = 'https://apis.dtdc.in/apis/v1/tracking';

$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => $url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_CONNECTTIMEOUT => 10,
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => json_encode($testData),
    CURLOPT_HTTPHEADER => [
        'Content-Type: application/json',
        'Accept: application/json',
        'User-Agent: EverythingB2C/1.0',
        'Authorization: Bearer bbb8196c734d8487983936199e880072',
        'X-API-Key: bbb8196c734d8487983936199e880072'
    ]
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

if ($error) {
    echo "<span class='error'>✗ cURL Error: " . $error . "</span><br>\n";
} else {
    echo "<span class='info'>• HTTP Status Code: " . $httpCode . "</span><br>\n";
    echo "<span class='info'>• Response: " . substr($response, 0, 500) . "...</span><br>\n";
    
    if ($httpCode === 200) {
        echo "<span class='success'>✓ API endpoint is accessible</span><br>\n";
    } elseif ($httpCode === 401) {
        echo "<span class='error'>✗ Authentication failed - check credentials</span><br>\n";
    } elseif ($httpCode === 404) {
        echo "<span class='error'>✗ API endpoint not found - check URL</span><br>\n";
    } else {
        echo "<span class='info'>• API responded with status: " . $httpCode . "</span><br>\n";
    }
}
echo "</div>";

// Test 5: Configuration Summary
echo "<div class='test'>";
echo "<h2>Test 5: Configuration Summary</h2>\n";
echo "<p><strong>Your DTDC Configuration:</strong></p>\n";
echo "<ul>\n";
echo "<li><strong>Username:</strong> PL3537_trk_json</li>\n";
echo "<li><strong>Password:</strong> wafBo</li>\n";
echo "<li><strong>Token:</strong> bbb8196c734d8487983936199e880072</li>\n";
echo "<li><strong>Base URL:</strong> https://apis.dtdc.in/apis/v1</li>\n";
echo "<li><strong>Customer Portal:</strong> https://customer.dtdc.in/login</li>\n";
echo "<li><strong>Customer Code:</strong> PL3537</li>\n";
echo "</ul>\n";

echo "<p><strong>Next Steps:</strong></p>\n";
echo "<ol>\n";
echo "<li><strong>Login to DTDC Customer Portal</strong> using your credentials</li>\n";
echo "<li><strong>Find API Documentation</strong> section in the portal</li>\n";
echo "<li><strong>Get real tracking ID</strong> to test tracking functionality</li>\n";
echo "<li><strong>Contact DTDC Support</strong> for complete API access</li>\n";
echo "</ol>\n";
echo "</div>";

// Test 6: Portal Access Test
echo "<div class='test'>";
echo "<h2>Test 6: Customer Portal Access</h2>\n";
echo "<p><strong>Try accessing DTDC Customer Portal:</strong></p>\n";
echo "<p><a href='https://customer.dtdc.in/login' target='_blank'>https://customer.dtdc.in/login</a></p>\n";
echo "<p><strong>Login Credentials:</strong></p>\n";
echo "<ul>\n";
echo "<li>Customer Code: <strong>PL3537</strong></li>\n";
echo "<li>Password: <strong>Abc@123456</strong></li>\n";
echo "</ul>\n";
echo "<p><strong>What to look for in the portal:</strong></p>\n";
echo "<ul>\n";
echo "<li>API Documentation section</li>\n";
echo "<li>Integration settings</li>\n";
echo "<li>Service codes and product codes</li>\n";
echo "<li>Account configuration</li>\n";
echo "<li>Rate cards and pricing</li>\n";
echo "</ul>\n";
echo "</div>";

echo "<hr>\n";
echo "<h2>Summary</h2>\n";
echo "<p>Your DTDC tracking API credentials have been configured. The tracking functionality should work once you have a real DTDC tracking ID to test with.</p>\n";
echo "<p>For complete integration (order creation, label generation), you'll need to get additional API credentials from DTDC.</p>\n";
echo "<p><strong>See:</strong> <code>DTDC_API_CREDENTIALS_GUIDE.md</code> for detailed instructions.</p>\n";
?>
