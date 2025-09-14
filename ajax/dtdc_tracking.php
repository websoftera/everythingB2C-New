<?php
/**
 * Frontend DTDC Tracking AJAX Endpoint
 * 
 * This file handles DTDC tracking requests from the frontend tracking page.
 */

require_once '../config/database.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');

try {
    $action = $_POST['action'] ?? $_GET['action'] ?? '';
    
    switch ($action) {
        case 'track_shipment':
            handleTrackShipment();
            break;
            
        default:
            throw new Exception('Invalid action');
    }
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

/**
 * Track shipment using DTDC tracking ID
 */
function handleTrackShipment() {
    $trackingId = $_POST['tracking_id'] ?? $_GET['tracking_id'] ?? '';
    
    if (!$trackingId) {
        throw new Exception('Tracking ID is required');
    }
    
    $trackingData = getDTDCTracking($trackingId);
    
    if ($trackingData) {
        echo json_encode([
            'success' => true,
            'data' => $trackingData,
            'source' => 'api'
        ]);
    } else {
        // Check if it's a valid DTDC tracking ID format
        if (!preg_match('/^(D|7D)\d+$/', $trackingId)) {
            throw new Exception('Invalid DTDC tracking ID format. Expected format: D or 7D followed by numbers (e.g., D1005560078, 7D154319925)');
        }
        
        // API failed - provide detailed error information
        $errorMessage = 'DTDC API Error: ';
        $errorMessage .= 'Tracking ID not found or not authorized for your account. ';
        $errorMessage .= 'This could be due to:';
        $errorMessage .= '<br>• Tracking ID does not belong to your DTDC account';
        $errorMessage .= '<br>• Tracking ID is invalid or expired';
        $errorMessage .= '<br>• Insufficient permissions for this tracking ID';
        
        throw new Exception($errorMessage);
    }
}
?>
