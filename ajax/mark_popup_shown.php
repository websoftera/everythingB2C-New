<?php
session_start();
require_once '../includes/delivery_popup_functions.php';

// Mark popup as shown in session
markDeliveryPopupAsShown();

// Check if this is an AJAX request
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
    header('Content-Type: application/json');
    
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
        exit;
    }

    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);

    echo json_encode([
        'success' => true,
        'message' => 'Popup marked as shown',
        'session_id' => session_id()
    ]);
} else {
    // Regular form submission - redirect back to homepage
    header('Location: ../index.php');
    exit;
}
?>
