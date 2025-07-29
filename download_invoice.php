<?php
error_reporting(0);
ini_set('display_errors', 0);
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/vendor/phpqrcode/phpqrcode.php';

session_start();

$orderId = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;
if (!$orderId) {
    die('Invalid order ID.');
}

$isAdmin = isset($_SESSION['admin_id']);
$userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

$order = getOrderById($orderId, $isAdmin ? null : $userId);
if (!$order) {
    die('Order not found or access denied.');
}
$orderItems = getOrderItems($orderId);

// Address
$address = [
    'name' => $order['address_name'],
    'phone' => $order['address_phone'],
    'address_line1' => $order['address_line1'],
    'address_line2' => $order['address_line2'],
    'city' => $order['city'],
    'state' => $order['state'],
    'pincode' => $order['pincode'],
];

$company = [
    'name' => 'EverythingB2C',
    'address' => 'Shop 12, D, Media Park, Bhagwan Tatyasaheb Kawade Rd, Dombi Wadi, R, Pune, Maharashtra 411001',
    'gstin' => '27AAABC1711H1ZF',
    'phone' => '+91 90281 18070',
    'email' => 'info@EverythingB2C.in',
    'logo' => __DIR__ . '/asset/images/logo.webp',
];

$logoBase64 = '';
if (file_exists($company['logo'])) {
    $logoData = file_get_contents($company['logo']);
    $logoBase64 = 'data:image/webp;base64,' . base64_encode($logoData);
}

$invoiceNo = $order['order_number'];
$invoiceDate = date('d-m-Y', strtotime($order['created_at']));
$total = $order['total_amount'];

// Prepare QR code data with required info
$qrData =
    "Seller: EverythingB2C\n" .
    "Logo: EverythingB2C\n" .
    "\n" .
    "Order ID: $orderId\n" .
    "Date: $invoiceDate\n" .
    "\n" .
    "Payment\n" .
    "  Status: " . $order['payment_status'] . "\n" .
    "  Method: " . $order['payment_method'] . "\n" .
    (strtolower($order['payment_method']) === 'razorpay' && !empty($order['razorpay_payment_id']) ? "  Transaction ID: " . $order['razorpay_payment_id'] . "\n" : "") .
    "\n" .
    "Customer\n" .
    "  Name: " . $address['name'] . "\n" .
    "  Address: " . $address['address_line1'] .
        (!empty($address['address_line2']) ? ', ' . $address['address_line2'] : '') .
        ', ' . $address['city'] . ', ' . $address['state'] . ' - ' . $address['pincode'] . "\n" .
    (!empty($order['gst_number']) ? "  GSTIN: " . $order['gst_number'] . "\n" : "") .
    "\n" .
    "Total Amount: ₹" . number_format($total, 2) . "\n" .
    "\n" .
    "Products:" . "\n" .
    implode("\n", array_map(function($item) { return "  - " . $item['name']; }, $orderItems));

if (ob_get_level() > 0) { ob_end_clean(); }
ob_start();
QRcode::png($qrData, null, QR_ECLEVEL_L, 3, 1);
$qrPngData = ob_get_clean();
$qrBase64 = 'data:image/png;base64,' . base64_encode($qrPngData);

$html = '<html><head><style>
body { font-family: Arial, sans-serif; font-size: 12px; color: #222; }
.header { border-bottom: 1px solid #000; margin-bottom: 10px; padding-bottom: 10px; }
.header-table { width: 100%; }
.header-left { text-align: left; vertical-align: top; }
.header-right { text-align: right; vertical-align: top; }
.logo { height: 50px; }
.qr { height: 80px; }
.footer { border-top: 1px solid #000; margin-top: 30px; padding-top: 10px; font-size: 11px; text-align: center; color: #555; }
</style></head><body>';

// === HEADER (AJIO-STYLE) ===
$html .= '<div class="header">
<table class="header-table" style="width:100%;"><tr>';
// Left: Seller details
$html .= '<td class="header-left" style="width:40%; vertical-align:top;">';
$html .= '<b>Seller:</b><br>';
$html .= '<b>' . htmlspecialchars($company['name']) . '</b><br>';
$html .= htmlspecialchars($company['address']) . '<br>';
$html .= 'GSTIN: ' . htmlspecialchars($company['gstin']) . '<br>';
$html .= 'Invoice No: ' . htmlspecialchars($invoiceNo) . '<br>';
$html .= 'Date: ' . htmlspecialchars($invoiceDate) . '<br>';
$html .= '</td>';
// Center: QR code with labels
$html .= '<td style="width:20%; text-align:center; vertical-align:top;">';
$html .= '<div style="font-size:13px; font-weight:bold;">Tax Invoice</div>';
$html .= '<div style="font-size:11px; margin-bottom:4px;">Original for recipient</div>';
$html .= '<img src="' . $qrBase64 . '" class="qr" style="display:block; margin:0 auto;">';
$html .= '</td>';
// Right: Logo, support, email
$html .= '<td class="header-right" style="width:40%; text-align:right; vertical-align:top;">';
if ($logoBase64) {
    $html .= '<img src="' . $logoBase64 . '" class="logo" style="display:block; margin-left:auto; margin-bottom:8px;"><br>';
}
$html .= 'Customer Support: ' . htmlspecialchars($company['phone']) . '<br>';
$html .= 'Email: ' . htmlspecialchars($company['email']) . '<br>';
$html .= '</td>';
$html .= '</tr></table></div>';

// === BILL TO SECTION ===
$html .= '<div style="margin-bottom:18px; margin-top:10px;">
  <b>Bill To:</b><br>
  ' . htmlspecialchars($address['name']) . '<br>
  ' . htmlspecialchars($address['address_line1']);
if (!empty($address['address_line2'])) {
    $html .= ', ' . htmlspecialchars($address['address_line2']);
}
$html .= '<br>';
$html .= htmlspecialchars($address['city']) . ', ' . htmlspecialchars($address['state']) . ' - ' . htmlspecialchars($address['pincode']) . '<br>';
$html .= 'Phone: ' . htmlspecialchars($address['phone']) . '\n';
if (!empty($order['gst_number'])) {
    $html .= 'GSTIN: ' . htmlspecialchars($order['gst_number']) . '\n';
}
$html .= '</div>';

// === PAYMENT INFO SECTION ===
$html .= '<div style="margin-bottom:18px; margin-top:10px;">
  <b>Payment Method:</b> ' . htmlspecialchars(ucfirst($order['payment_method'])) . '<br>
  <b>Payment Status:</b> ' . htmlspecialchars(ucfirst($order['payment_status'])) . '<br>';
if (strtolower($order['payment_method']) === 'razorpay' && !empty($order['razorpay_payment_id'])) {
    $html .= '<b>Transaction ID:</b> ' . htmlspecialchars($order['razorpay_payment_id']) . '<br>';
}
$html .= '</div>';

// Calculate total discount (savings)
$total_discount = 0;
foreach ($orderItems as $item) {
    $total_discount += ($item['mrp'] - $item['selling_price']) * $item['quantity'];
}

// Determine GST type based on shipping/billing state
$maharashtra_states = ['maharashtra', 'maharastra', 'maha'];
$order_state = strtolower(trim($order['state'] ?? ''));
$is_maharashtra = in_array($order_state, $maharashtra_states);

// Set GST column headers based on state
$gst_col = $is_maharashtra ? 'GST' : 'IGST';
$item_gst_col = $is_maharashtra ? 'Item GST' : 'Item IGST';
$net_gst_col = $is_maharashtra ? 'Net GST' : 'Net IGST';

// === MIDDLE SECTION (FORMATTED) ===
$html .= '<h2>Invoice Details</h2>
<table border="1" cellpadding="5" cellspacing="0" width="100%">
<tr><th>Sr.</th><th>Product</th><th>Product Name</th><th>HSN Code</th><th>Qty.</th><th>Unit</th><th>' . $gst_col . '</th><th>Item MRP</th><th>Item Price</th><th>' . $item_gst_col . '</th><th>Net Price</th><th>' . $net_gst_col . '</th><th>Total</th><th>Savings</th></tr>';

$sr = 1;
$total_net_price = 0;
$total_net_gst = 0;
$total_total = 0;
$total_savings = 0;
$gst_summary = [];
foreach ($orderItems as $item) {
    $gst_rate = isset($item['gst_rate']) ? (float)$item['gst_rate'] : 0;
    // Force IGST if not Maharashtra
    $gst_type = $is_maharashtra ? ($item['gst_type'] ?? 'sgst_cgst') : 'igst';
    $qty = (int)$item['quantity'];
    $unit = 'No.';
    if (stripos($item['name'], 'shoes') !== false) $unit = 'Pair';
    $item_mrp = (float)$item['mrp'];
    $item_sp = (float)$item['selling_price'];
    $item_price = $item_sp / (1 + ($gst_rate / 100));
    $item_gst = $item_sp - $item_price;
    $net_price = $item_price * $qty;
    $net_gst = $item_gst * $qty;
    $total = $net_price + $net_gst;
    $savings = ($item_mrp - $item_sp) * $qty;
    $total_net_price += $net_price;
    $total_net_gst += $net_gst;
    $total_total += $total;
    $total_savings += $savings;
    // GST summary by HSN
    if (!isset($gst_summary[$item['hsn']])) {
        $gst_summary[$item['hsn']] = [
            'gst_rate' => $gst_rate,
            'net_gst' => 0,
            'cgst' => 0,
            'sgst' => 0,
            'igst' => 0
        ];
    }
    if ($gst_type === 'sgst_cgst') {
        $gst_summary[$item['hsn']]['cgst'] += $net_gst / 2;
        $gst_summary[$item['hsn']]['sgst'] += $net_gst / 2;
    } else {
        $gst_summary[$item['hsn']]['igst'] += $net_gst;
    }
    $gst_summary[$item['hsn']]['net_gst'] += $net_gst;
    $img_html = $item['main_image'] ? '<img src="./' . htmlspecialchars($item['main_image']) . '" style="height:40px;max-width:60px;object-fit:contain;" />' : '';
    $html .= '<tr>';
    $html .= '<td>' . $sr . '</td>';
    $html .= '<td>' . $img_html . '</td>';
    $html .= '<td>' . nl2br(htmlspecialchars($item['name'])) . '</td>';
    $html .= '<td>' . htmlspecialchars($item['hsn']) . '</td>';
    $html .= '<td>' . $qty . '</td>';
    $html .= '<td>' . $unit . '</td>';
    $html .= '<td>' . number_format($gst_rate, 2) . '%</td>';
    $html .= '<td>' . number_format($item_mrp, 2) . '</td>';
    $html .= '<td>' . number_format($item_price, 2) . '</td>';
    $html .= '<td>' . number_format($item_gst, 2) . '</td>';
    $html .= '<td>' . number_format($net_price, 2) . '</td>';
    $html .= '<td>' . number_format($net_gst, 2) . '</td>';
    $html .= '<td>' . number_format($total, 2) . '</td>';
    $html .= '<td>' . number_format($savings, 2) . '</td>';
    $html .= '</tr>';
    $sr++;
}
$html .= '<tr style="font-weight:bold;background:#f5f5f5;"><td colspan="10" align="right">Total</td><td>' . number_format($total_net_price, 2) . '</td><td>' . number_format($total_net_gst, 2) . '</td><td>' . number_format($total_total, 2) . '</td><td>' . number_format($total_savings, 2) . '</td></tr>';
$html .= '</table>';

// GST Summary Table
$html .= '<br><h3>GST Summary</h3>';
$html .= '<table border="1" cellpadding="5" cellspacing="0" width="60%"><tr><th>HSN Code</th><th>GST</th><th>CGST</th><th>SGST</th><th>IGST</th><th>Net GST</th></tr>';
$total_cgst = $total_sgst = $total_igst = $total_gst = 0;
foreach ($gst_summary as $hsn => $row) {
    $html .= '<tr>';
    $html .= '<td>' . htmlspecialchars($hsn) . '</td>';
    $html .= '<td>' . number_format($row['gst_rate'], 2) . '%</td>';
    $html .= '<td>' . number_format($row['cgst'], 2) . '</td>';
    $html .= '<td>' . number_format($row['sgst'], 2) . '</td>';
    $html .= '<td>' . ($row['igst'] > 0 ? number_format($row['igst'], 2) : '-') . '</td>';
    $html .= '<td>' . number_format($row['net_gst'], 2) . '</td>';
    $html .= '</tr>';
    $total_cgst += $row['cgst'];
    $total_sgst += $row['sgst'];
    $total_igst += $row['igst'];
    $total_gst += $row['net_gst'];
}
$html .= '<tr style="font-weight:bold;background:#f5f5f5;"><td colspan="2">Total GST</td><td>' . number_format($total_cgst, 2) . '</td><td>' . number_format($total_sgst, 2) . '</td><td>' . ($total_igst > 0 ? number_format($total_igst, 2) : '-') . '</td><td>' . number_format($total_gst, 2) . '</td></tr>';
$html .= '</table>';

// Total Invoice Value
$html .= '<br><b>Total Invoice Value:</b> ' . number_format($total_total, 2) . '<br>';
$html .= '<b>Total Invoice Value in Words:</b> ' . numberToWords($total_total) . ' Rupees Only<br>';

// Helper function to convert number to words (Indian style)
function numberToWords($number)
{
    $words = array(
        '0' => '',
        '1' => 'One',
        '2' => 'Two',
        '3' => 'Three',
        '4' => 'Four',
        '5' => 'Five',
        '6' => 'Six',
        '7' => 'Seven',
        '8' => 'Eight',
        '9' => 'Nine',
        '10' => 'Ten',
        '11' => 'Eleven',
        '12' => 'Twelve',
        '13' => 'Thirteen',
        '14' => 'Fourteen',
        '15' => 'Fifteen',
        '16' => 'Sixteen',
        '17' => 'Seventeen',
        '18' => 'Eighteen',
        '19' =>'Nineteen',
        '20' => 'Twenty',
        '30' => 'Thirty',
        '40' => 'Forty',
        '50' => 'Fifty',
        '60' => 'Sixty',
        '70' => 'Seventy',
        '80' => 'Eighty',
        '90' => 'Ninety'
    );
    $digits = array('', 'Hundred', 'Thousand', 'Lakh', 'Crore');
    $number = round($number);
    if ($number == 0) return 'Zero';
    $i = 0;
    $str = array();
    while ($number > 0) {
        if ($i == 1) {
            $divider = 10;
        } elseif ($i > 1) {
            $divider = 100;
        } else {
            $divider = 100;
        }
        $number_part = $number % $divider;
        $number = (int)($number / $divider);
        if ($number_part) {
            $plural = (($counter = count($str)) && $number_part > 9) ? 's' : '';
            $hundred = ($i == 1 && $str[0]) ? ' and ' : '';
            if ($number_part < 21) {
                $str[] = $words[$number_part] . ' ' . $digits[$i] . $plural . $hundred;
            } else {
                $str[] = $words[10 * floor($number_part / 10)] . ' ' . $words[$number_part % 10] . ' ' . $digits[$i] . $plural . $hundred;
            }
        } else {
            $str[] = null;
        }
        $i++;
    }
    $str = array_reverse(array_filter($str));
    return trim(implode(' ', $str));
}

// === FOOTER (AJIO-STYLE) ===
$html .= '<div class="footer" style="font-size:11px; margin-top:24px;">
  <table style="width:100%;"><tr>
    <td style="vertical-align:top; width:33%;">' . htmlspecialchars($company['name']) . '<br>' . htmlspecialchars($company['address']) . '</td>
    <td style="vertical-align:top; width:33%;"><b>Contact Us</b><br>' . htmlspecialchars($company['phone']) . '<br>' . htmlspecialchars($company['email']) . '</td>
    <td style="vertical-align:top; width:33%;">www.EverythingB2C.in<br><b>CIN:</b> U12345MH2024PTC000000</td>
  </tr></table>
  <div style="margin-top:10px; text-align:center; color:#555;">This is a system generated invoice issued under provisions of the Information Technology Act, 2000.<br>Thank you for shopping with us!</div>
</div>';

// Add subtotal in words below subtotal
$subtotal_words = numberToWords($order['subtotal']);
$html = str_replace('<b>Order Subtotal:</b> ₹' . number_format($order['subtotal'], 2) . '<br>', '<b>Order Subtotal:</b> ₹' . number_format($order['subtotal'], 2) . '<br><span style="font-size:10px; color:#555;">(' . $subtotal_words . ' Only)</span><br>', $html);

$html .= '</body></html>';

$mpdf = new \Mpdf\Mpdf(['format' => 'A4', 'allow_url_fopen' => true]);
$mpdf->showImageErrors = true;
$mpdf->WriteHTML($html);
$mpdf->Output('Invoice_' . $invoiceNo . '.pdf', 'D');
exit;
