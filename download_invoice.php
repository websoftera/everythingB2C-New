<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/includes/functions.php';

session_start();

$orderId = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;
if (!$orderId) {
    die('Invalid order ID.');
}

// Check if user is logged in or admin
$isAdmin = isset($_SESSION['admin_id']);
$userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

$order = getOrderById($orderId, $isAdmin ? null : $userId);
if (!$order) {
    die('Order not found or access denied.');
}
$orderItems = getOrderItems($orderId);

// Fetch address
$address = [
    'name' => $order['address_name'],
    'phone' => $order['address_phone'],
    'address_line1' => $order['address_line1'],
    'address_line2' => $order['address_line2'],
    'city' => $order['city'],
    'state' => $order['state'],
    'pincode' => $order['pincode'],
];

// Company details (customize as needed)
$company = [
    'name' => 'EverythingB2C',
    'address' => 'Your Company Address, City, State, Pincode',
    'gstin' => '27AAAPL1234C1ZV',
    'phone' => '0123-456789',
    'email' => 'support@everythingb2c.com',
    'logo' => __DIR__ . '/asset/images/logo.webp', // always use logo.webp
];

// Prepare logo as base64 for mPDF
$logoBase64 = '';
if (file_exists($company['logo'])) {
    $logoData = file_get_contents($company['logo']);
    $logoBase64 = 'data:image/webp;base64,' . base64_encode($logoData);
}

// Invoice number and date
$invoiceNo = $order['order_number'];
$invoiceDate = date('d-m-Y', strtotime($order['created_at']));

// Calculate totals
$subtotal = $order['subtotal'];
$shipping = $order['shipping_charge'];
$gst = $order['gst_amount'];
$total = $order['total_amount'];

// Buyer GSTIN if present
$buyer_gstin = $order['gst_number'] ?? '';
$company_name = $order['company_name'] ?? '';

// Prepare HTML for invoice
$html = '<html><head><style>
body { font-family: Arial, sans-serif; font-size: 12px; color: #222; }
.header-bar { background: #f6f6f6; color: #222; padding: 18px 24px 10px 24px; border-radius: 8px 8px 0 0; display: flex; align-items: center; }
.logo { height: 54px; width: auto; background: #fff; border-radius: 6px; padding: 4px 8px; margin-right: 18px; }
.invoice-title-block { flex: 1; text-align: right; }
.invoice-title { font-size: 2em; font-weight: bold; letter-spacing: 2px; }
.invoice-no { font-size: 1.1em; margin-top: 4px; }
.invoice-date { font-size: 1em; margin-top: 2px; }
.section { margin: 18px 0 0 0; }
.section-table { width: 100%; margin-bottom: 0; }
.section-table td { padding: 2px 8px; vertical-align: top; }
.table { width: 100%; border-collapse: collapse; margin-top: 18px; }
.table th, .table td { border: 1px solid #388e3c; padding: 8px; }
.table th { background: #388e3c; color: #fff; font-weight: bold; }
.table td { background: #f9f9f9; }
.totals-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
.totals-table td { padding: 6px 10px; border: none; }
.totals-table .label { text-align: right; color: #555; }
.totals-table .value { text-align: right; font-weight: bold; }
.status-row td { font-size: 1.1em; font-weight: bold; border-radius: 0 0 8px 8px; text-align: right; }
.status-paid { background: #388e3c; color: #fff; }
.status-due { background: #ffc107; color: #222; }
.status-failed { background: #dc3545; color: #fff; }
.status-badge { display: inline-block; padding: 6px 18px; border-radius: 16px; font-size: 1em; font-weight: bold; color: #fff; margin: 10px 0 0 0; }
.status-badge.paid { background: #388e3c; }
.status-badge.due { background: #ffc107; color: #222; }
.status-badge.failed { background: #dc3545; }
.payment-method { font-size: 1em; color: #222; margin: 4px 0 12px 0; }
.footer { margin-top: 30px; font-size: 11.5px; color: #555; }
.thankyou { margin-top: 30px; font-size: 1.2em; font-weight: bold; color: #388e3c; text-align: center; }
</style></head><body>';

// Header bar
$html .= '<div class="header-bar">';
if ($logoBase64) {
    $html .= '<img src="' . $logoBase64 . '" class="logo">';
}
$html .= '<div class="invoice-title-block">';
$html .= '<div class="invoice-title">INVOICE</div>';
$html .= '<div class="invoice-no">#' . htmlspecialchars($invoiceNo) . '</div>';
$html .= '<div class="invoice-date">DATE: ' . htmlspecialchars($invoiceDate) . '</div>';
$html .= '</div>';
$html .= '</div>';

// Payment terms
$html .= '<div style="margin: 8px 0 18px 0; font-size: 1em; color: #555;">Payment terms: Due on receipt</div>';

// Company/Bill To section
$html .= '<table class="section-table"><tr>';
$html .= '<td width="50%"><b>COMPANY NAME</b><br>' . htmlspecialchars($company['name']) . '<br>' . htmlspecialchars($company['address']) . '<br>GSTIN: ' . htmlspecialchars($company['gstin']) . '<br>Phone: ' . htmlspecialchars($company['phone']) . '<br>Email: ' . htmlspecialchars($company['email']) . '</td>';
$html .= '<td width="50%"><b>BILL TO</b><br>' . htmlspecialchars($address['name']) . '<br>' . htmlspecialchars($address['address_line1']) . ' ' . htmlspecialchars($address['address_line2']) . '<br>' . htmlspecialchars($address['city']) . ', ' . htmlspecialchars($address['state']) . ' - ' . htmlspecialchars($address['pincode']) . '<br>Phone: ' . htmlspecialchars($address['phone']) . '</td>';
$html .= '</tr></table>';

// Product table (remove HSN)
$html .= '<table class="table"><thead><tr>';
$html .= '<th>DESCRIPTION</th><th>SKU</th><th>QTY</th><th>UNIT PRICE</th><th>GST</th><th>TOTAL</th>';
$html .= '</tr></thead><tbody>';
foreach ($orderItems as $item) {
    $html .= '<tr>';
    $html .= '<td>' . htmlspecialchars($item['name']) . '</td>';
    $html .= '<td>' . htmlspecialchars($item['sku'] ?? $item['product_id']) . '</td>';
    $html .= '<td>' . $item['quantity'] . '</td>';
    $html .= '<td>₹' . number_format($item['price'], 2) . '</td>';
    $html .= '<td>' . (isset($item['gst_percent']) ? $item['gst_percent'] . '%' : '-') . '</td>';
    $html .= '<td>₹' . number_format($item['price'] * $item['quantity'], 2) . '</td>';
    $html .= '</tr>';
}
$html .= '</tbody></table>';

// Totals section (fix calculations and match order details)
$html .= '<table class="totals-table">';
$html .= '<tr><td class="label">Subtotal</td><td class="value">₹' . number_format($subtotal, 2) . '</td></tr>';
if (!empty($order['discount'])) {
    $html .= '<tr><td class="label">Discount</td><td class="value">-₹' . number_format($order['discount'], 2) . '</td></tr>';
}
$html .= '<tr><td class="label">Shipping/Handling</td><td class="value">₹' . number_format($shipping, 2) . '</td></tr>';
$html .= '<tr><td class="label">GST</td><td class="value">₹' . number_format($gst, 2) . '</td></tr>';
$html .= '</table>';

// Payment status badge (white text, colored background)
$paymentStatus = strtolower($order['payment_status']);
$statusText = strtoupper($order['payment_status']);
$statusClass = 'due';
if ($paymentStatus === 'paid') $statusClass = 'paid';
if ($paymentStatus === 'failed') $statusClass = 'failed';
$html .= '<div class="status-badge ' . $statusClass . '">' . $statusText . '</div>';

// Payment method
$html .= '<div class="payment-method">Payment Method: <b>' . htmlspecialchars(strtoupper($order['payment_method'])) . '</b></div>';

// If not paid, show balance due
if ($paymentStatus !== 'paid') {
    $html .= '<div class="status-badge due">BALANCE DUE: ₹' . number_format($total, 2) . '</div>';
}

// Payment instructions
$html .= '<div class="footer">Please make all payments to <b>' . htmlspecialchars($company['name']) . '</b>.<br>For support, contact: ' . htmlspecialchars($company['email']) . ' or ' . htmlspecialchars($company['phone']) . '.</div>';
$html .= '<div class="thankyou">Thank you for your order</div>';

$html .= '</body></html>';

// Generate PDF
$mpdf = new \Mpdf\Mpdf(['format' => 'A4']);
$mpdf->WriteHTML($html);
$mpdf->SetTitle('Invoice_' . $invoiceNo);
$mpdf->Output('Invoice_' . $invoiceNo . '.pdf', 'D');
exit; 