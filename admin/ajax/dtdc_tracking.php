<?php
session_start();
require_once '../../config/database.php';
require_once '../../includes/functions.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

header('Content-Type: application/json');

try {
    $action = $_POST['action'] ?? $_GET['action'] ?? '';
    
    switch ($action) {
        case 'create_dtdc_order':
            handleCreateDTDCOrder();
            break;
            
        case 'track_shipment':
            handleTrackShipment();
            break;
            
        case 'generate_label':
            handleGenerateLabel();
            break;
            
        case 'cancel_order':
            handleCancelOrder();
            break;
            
        case 'get_tracking_events':
            handleGetTrackingEvents();
            break;
            
        case 'refresh_tracking':
            handleRefreshTracking();
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
 * Create DTDC order for shipping
 */
function handleCreateDTDCOrder() {
    $orderId = intval($_POST['order_id']);
    
    if (!$orderId) {
        throw new Exception('Order ID is required');
    }
    
    // Get order details
    $order = getOrderById($orderId);
    if (!$order) {
        throw new Exception('Order not found');
    }
    
    // Get order items
    $orderItems = getOrderItems($orderId);
    
    // Get address details
    $address = getAddressById($order['address_id']);
    
    // Prepare order data for DTDC
    $orderData = [
        'consignee_name' => $address['name'] ?? '',
        'consignee_address' => ($address['address_line1'] ?? '') . ' ' . ($address['address_line2'] ?? ''),
        'consignee_city' => $address['city'] ?? '',
        'consignee_state' => $address['state'] ?? '',
        'consignee_pincode' => $address['pincode'] ?? '',
        'consignee_phone' => $address['phone'] ?? '',
        'consignee_email' => $address['email'] ?? '',
        'shipper_name' => 'EverythingB2C',
        'shipper_address' => 'Warehouse Address, Mumbai',
        'shipper_city' => 'Mumbai',
        'shipper_state' => 'Maharashtra',
        'shipper_pincode' => '400001',
        'shipper_phone' => '+91-8780406230',
        'declared_value' => $order['total_amount'],
        'collectable_amount' => $order['payment_method'] === 'cod' ? $order['total_amount'] : 0,
        'weight' => calculateOrderWeight($orderItems),
        'reference_number' => $order['tracking_id'],
        'pieces' => count($orderItems),
        'invoice_number' => 'INV-' . $orderId,
        'invoice_date' => date('Y-m-d', strtotime($order['created_at']))
    ];
    
    // Create DTDC order
    $result = createDTDCOrder($orderId, $orderData);
    
    if ($result) {
        echo json_encode([
            'success' => true,
            'message' => 'DTDC order created successfully',
            'data' => $result
        ]);
    } else {
        throw new Exception('Failed to create DTDC order');
    }
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
        throw new Exception('Failed to fetch tracking data');
    }
}

/**
 * Generate shipping label
 */
function handleGenerateLabel() {
    $orderId = intval($_POST['order_id']);
    
    if (!$orderId) {
        throw new Exception('Order ID is required');
    }
    
    $labelData = generateDTDCShippingLabel($orderId);
    
    if ($labelData) {
        echo json_encode([
            'success' => true,
            'message' => 'Shipping label generated successfully',
            'data' => $labelData
        ]);
    } else {
        throw new Exception('Failed to generate shipping label');
    }
}

/**
 * Cancel DTDC order
 */
function handleCancelOrder() {
    $orderId = intval($_POST['order_id']);
    
    if (!$orderId) {
        throw new Exception('Order ID is required');
    }
    
    $result = cancelDTDCOrder($orderId);
    
    if ($result) {
        echo json_encode([
            'success' => true,
            'message' => 'DTDC order cancelled successfully'
        ]);
    } else {
        throw new Exception('Failed to cancel DTDC order');
    }
}

/**
 * Get tracking events for an order
 */
function handleGetTrackingEvents() {
    $orderId = intval($_GET['order_id']);
    
    if (!$orderId) {
        throw new Exception('Order ID is required');
    }
    
    $events = getDTDCTrackingEvents($orderId);
    
    echo json_encode([
        'success' => true,
        'data' => $events
    ]);
}

/**
 * Refresh tracking data
 */
function handleRefreshTracking() {
    $orderId = intval($_POST['order_id']);
    
    if (!$orderId) {
        throw new Exception('Order ID is required');
    }
    
    // Get DTDC order details
    $dtdcOrder = getDTDCOrderByOrderId($orderId);
    
    if (!$dtdcOrder || !$dtdcOrder['dtdc_tracking_id']) {
        throw new Exception('DTDC tracking ID not found for this order');
    }
    
    // Fetch fresh tracking data
    $trackingData = getDTDCTracking($dtdcOrder['dtdc_tracking_id']);
    
    if ($trackingData) {
        echo json_encode([
            'success' => true,
            'message' => 'Tracking data refreshed successfully',
            'data' => $trackingData
        ]);
    } else {
        throw new Exception('Failed to refresh tracking data');
    }
}

/**
 * Calculate total weight of order items
 */
function calculateOrderWeight($orderItems) {
    $totalWeight = 0;
    
    foreach ($orderItems as $item) {
        // Assuming each item has a weight field or default weight
        $itemWeight = $item['weight'] ?? 1; // Default 1 kg per item
        $totalWeight += $itemWeight * $item['quantity'];
    }
    
    return max($totalWeight, 1); // Minimum 1 kg
}
?>
