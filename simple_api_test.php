<?php
/**
 * Simple DTDC API Test
 */

require_once 'includes/dtdc_api.php';

echo "Testing DTDC API...\n";

try {
    $dtdcApi = new DTDCAPI();
    
    echo "Base URL: " . $dtdcApi->getConfig('api.base_url') . "\n";
    echo "Username: " . $dtdcApi->getConfig('api.username') . "\n";
    echo "API Key: " . substr($dtdcApi->getConfig('api.api_key'), 0, 8) . "...\n";
    
    echo "\nMaking API call for tracking ID: D1005560078\n";
    
    $trackingData = $dtdcApi->trackShipment('D1005560078');
    
    if ($trackingData) {
        echo "SUCCESS: API returned data\n";
        echo "Status: " . ($trackingData['status'] ?? 'N/A') . "\n";
        echo "Location: " . ($trackingData['current_location'] ?? 'N/A') . "\n";
    } else {
        echo "FAILED: API returned no data\n";
    }
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}

echo "\nDone.\n";
?>
