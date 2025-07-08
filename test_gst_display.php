<?php
require_once 'config/database.php';
require_once 'includes/gst_shipping_functions.php';

echo "Testing GST Display (Percentages)\n";
echo "=================================\n\n";

// Test GST breakdown function
echo "🧮 GST Breakdown Test:\n";
$test_cases = [
    ['type' => 'sgst_cgst', 'rate' => 18.00],
    ['type' => 'igst', 'rate' => 18.00],
    ['type' => 'sgst_cgst', 'rate' => 12.00],
    ['type' => 'igst', 'rate' => 28.00]
];

foreach ($test_cases as $test) {
    $breakdown = getGSTBreakdownForDisplay($test['type'], $test['rate']);
    echo "GST Type: {$test['type']}, Rate: {$test['rate']}%\n";
    echo "  Display: {$breakdown['type']}\n";
    if ($test['type'] === 'sgst_cgst') {
        echo "  SGST: {$breakdown['sgst_rate']}%, CGST: {$breakdown['cgst_rate']}%\n";
    } else {
        echo "  IGST: {$breakdown['igst_rate']}%\n";
    }
    echo "  Total Rate: {$breakdown['total_rate']}%\n";
    echo "---\n";
}

// Test actual GST calculation
echo "\n💰 GST Calculation Test (Same State):\n";
$price = 1000.00;
$rate = 18.00;
$delivery_state = 'Maharashtra';
$billing_state = 'Maharashtra';

$gst_calc = calculateGST($price, $rate, 'sgst_cgst', $delivery_state, $billing_state);
echo "Price: ₹$price\n";
echo "GST Rate: $rate%\n";
echo "Delivery: $delivery_state, Billing: $billing_state\n";
echo "SGST: ₹" . number_format($gst_calc['sgst'], 2) . " (" . ($rate/2) . "%)\n";
echo "CGST: ₹" . number_format($gst_calc['cgst'], 2) . " (" . ($rate/2) . "%)\n";
echo "Total GST: ₹" . number_format($gst_calc['total_gst'], 2) . "\n";

echo "\n💰 GST Calculation Test (Different State):\n";
$delivery_state = 'Delhi';
$gst_calc = calculateGST($price, $rate, 'igst', $delivery_state, $billing_state);
echo "Price: ₹$price\n";
echo "GST Rate: $rate%\n";
echo "Delivery: $delivery_state, Billing: $billing_state\n";
echo "IGST: ₹" . number_format($gst_calc['igst'], 2) . " ($rate%)\n";
echo "Total GST: ₹" . number_format($gst_calc['total_gst'], 2) . "\n";

echo "\n✅ GST display test completed!\n";
?> 