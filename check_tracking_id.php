<?php
/**
 * Check if DTDC Tracking ID exists
 * This will help verify if the tracking ID is valid
 */

$trackingId = 'D1005560078';

echo "<h1>DTDC Tracking ID Verification</h1>\n";
echo "<style>body{font-family:Arial,sans-serif;margin:20px;} .success{color:green;} .error{color:red;} .info{color:blue;} .debug{background:#f5f5f5;padding:10px;margin:10px 0;border:1px solid #ddd;}</style>\n";

echo "<div class='debug'>";
echo "<h2>Checking Tracking ID: $trackingId</h2>\n";

// Try to check DTDC's public tracking page
$url = 'https://www.dtdc.in/tracking';
$postData = [
    'trackingNumber' => $trackingId,
    'trackingType' => 'awb'
];

$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => $url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_CONNECTTIMEOUT => 10,
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => http_build_query($postData),
    CURLOPT_HTTPHEADER => [
        'Content-Type: application/x-www-form-urlencoded',
        'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
    ],
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_MAXREDIRS => 5
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

echo "<p><strong>HTTP Code:</strong> $httpCode</p>\n";
echo "<p><strong>Error:</strong> " . ($error ?: 'None') . "</p>\n";

if ($response) {
    // Check if response contains tracking data
    if (strpos($response, 'tracking') !== false || strpos($response, 'status') !== false) {
        echo "<p><span class='success'>✓ Response contains tracking-related content</span></p>\n";
    } else {
        echo "<p><span class='error'>✗ No tracking data found in response</span></p>\n";
    }
    
    echo "<p><strong>Response Preview:</strong></p>\n";
    echo "<pre>" . htmlspecialchars(substr($response, 0, 1000)) . "</pre>\n";
} else {
    echo "<p><span class='error'>✗ No response received</span></p>\n";
}
echo "</div>";

echo "<div class='debug'>";
echo "<h2>Manual Verification Required</h2>\n";
echo "<p>Please manually check the tracking ID on DTDC's website:</p>\n";
echo "<ol>\n";
echo "<li>Go to <a href='https://www.dtdc.in/tracking' target='_blank'>https://www.dtdc.in/tracking</a></li>\n";
echo "<li>Enter tracking ID: <strong>$trackingId</strong></li>\n";
echo "<li>Click Track</li>\n";
echo "</ol>\n";

echo "<h3>Possible Issues:</h3>\n";
echo "<ul>\n";
echo "<li><strong>Tracking ID doesn't exist:</strong> The ID may not be in DTDC's system</li>\n";
echo "<li><strong>Wrong format:</strong> DTDC might use a different tracking ID format</li>\n";
echo "<li><strong>Expired:</strong> The tracking ID might be too old</li>\n";
echo "<li><strong>Different system:</strong> The ID might be from a different courier</li>\n";
echo "</ul>\n";
echo "</div>";

echo "<div class='debug'>";
echo "<h2>Alternative Tracking IDs to Test</h2>\n";
echo "<p>Try these common DTDC tracking ID formats:</p>\n";
echo "<ul>\n";
echo "<li>D1005560078 (your current ID)</li>\n";
echo "<li>DTDC1005560078</li>\n";
echo "<li>1005560078</li>\n";
echo "<li>PL3537D1005560078</li>\n";
echo "</ul>\n";
echo "</div>";

echo "<div class='debug'>";
echo "<h2>Contact DTDC Support</h2>\n";
echo "<p>If the tracking ID doesn't work, contact DTDC:</p>\n";
echo "<ul>\n";
echo "<li><strong>Email:</strong> support@dtdc.in</li>\n";
echo "<li><strong>Phone:</strong> 1800-123-4567</li>\n";
echo "<li><strong>Customer Portal:</strong> <a href='https://customer.dtdc.in/login' target='_blank'>https://customer.dtdc.in/login</a></li>\n";
echo "<li><strong>Login:</strong> Customer Code: PL3537, Password: Abc@123456</li>\n";
echo "</ul>\n";
echo "<p><strong>Request:</strong> Provide a working DTDC tracking ID for testing</p>\n";
echo "</div>";
?>
