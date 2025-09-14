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
            'data' => $trackingData
        ]);
    } else {
        // Create fallback mock data for testing
        $mockData = [
            'tracking_id' => $trackingId,
            'status' => 'IN_TRANSIT',
            'status_description' => 'Package is in transit (Demo Data)',
            'current_location' => 'Mumbai Hub',
            'delivery_date' => date('Y-m-d', strtotime('+2 days')),
            'delivered_to' => '',
            'mapped_status' => 'In Transit',
            'events' => [
                [
                    'date' => date('Y-m-d', strtotime('-2 days')),
                    'time' => '10:30:00',
                    'location' => 'Origin Hub - Delhi',
                    'status' => 'PICKED_UP',
                    'description' => 'Package picked up from sender'
                ],
                [
                    'date' => date('Y-m-d', strtotime('-1 day')),
                    'time' => '14:15:00',
                    'location' => 'Sorting Facility - Delhi',
                    'status' => 'PROCESSED',
                    'description' => 'Package processed at sorting facility'
                ],
                [
                    'date' => date('Y-m-d'),
                    'time' => '09:45:00',
                    'location' => 'Mumbai Hub',
                    'status' => 'IN_TRANSIT',
                    'description' => 'Package in transit to destination'
                ]
            ]
        ];
        
        echo json_encode([
            'success' => true,
            'data' => $mockData,
            'message' => 'Demo tracking data (API not available)'
        ]);
    }
}
?>
