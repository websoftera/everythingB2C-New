<?php
session_start();
require_once '../config/database.php';
require_once '../includes/seller_functions.php';

if (!isset($_SESSION['seller_id'])) {
    header('Location: login.php');
    exit;
}

$sellerId = $_SESSION['seller_id'];

// Get filter parameters (from URL)
$filters = [];
if (isset($_GET['status'])) $filters['status'] = $_GET['status'];
if (isset($_GET['date'])) {
    switch ($_GET['date']) {
        case 'today':
            $filters['date_from'] = date('Y-m-d');
            $filters['date_to'] = date('Y-m-d');
            break;
        case 'week':
            $filters['date_from'] = date('Y-m-d', strtotime('-7 days'));
            $filters['date_to'] = date('Y-m-d');
            break;
        case 'month':
            $filters['date_from'] = date('Y-m-01');
            $filters['date_to'] = date('Y-m-d');
            break;
    }
}

// Get orders
$orders = getSellerOrders($sellerId, $filters);

// Set headers for CSV download
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=seller_orders_' . date('Y-m-d') . '.csv');

// Create output stream
$output = fopen('php://output', 'w');

// Add BOM for Excel UTF-8 support
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

// Add CSV headers
fputcsv($output, [
    'Order ID',
    'Order Number',
    'Tracking ID',
    'Customer Name',
    'Customer Email',
    'Customer Phone',
    'Total Amount',
    'Payment Method',
    'Payment Status',
    'Order Status',
    'Order Date'
]);

// Add order data
foreach ($orders as $order) {
    fputcsv($output, [
        $order['id'],
        $order['order_number'] ?? $order['id'],
        $order['tracking_id'],
        $order['customer_name'],
        $order['email'],
        $order['phone'] ?? '',
        $order['total_amount'],
        strtoupper($order['payment_method']),
        ucfirst($order['payment_status']),
        $order['status_name'],
        date('Y-m-d H:i:s', strtotime($order['created_at']))
    ]);
}

fclose($output);

// Log activity
logSellerActivity($sellerId, 'orders_exported', 'Exported ' . count($orders) . ' orders to CSV');

exit;
?>
