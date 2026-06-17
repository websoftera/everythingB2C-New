<?php
error_reporting(0);
ini_set('display_errors', 0);

session_start();
require_once '../config/database.php';
require_once '../vendor/autoload.php';
require_once '../includes/quotation_functions.php';

if (!isset($_SESSION['admin_id'])) {
    die('Access denied.');
}

ensureQuotationSchema($pdo);

if (!canAccess('manage_quotations') && !canAccess('view_orders')) {
    die('Access denied.');
}

$quotationId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$quotation = getQuotationById($pdo, $quotationId);
if (!$quotation) {
    die('Quotation not found.');
}

$items = getQuotationItems($pdo, $quotationId);

$company = [
    'name' => 'EVERYTHINGB2C',
    'address' => 'Shop 12, D, Media Park, Bhagwan Tatyasaheb Kawade Rd,<br>Dombi Wadi, R, Pune, Maharashtra 411001',
    'gst' => '27AAABC1711H1ZF',
    'msme' => '',
    'mobile' => '+91 878 040 6230',
    'email' => 'info@everythingb2c.in',
    'web' => 'www.everythingb2c.in',
    'logo' => '../logo.webp',
    'pdf_logo' => '../uploads/logo_pdf.png',
];

$logoPath = realpath(__DIR__ . '/../uploads/logo_pdf.png') ?: realpath(__DIR__ . '/../logo.webp');
$logoSrc = $logoPath ? $logoPath : '';
$line = function ($label, $value) {
    return '<tr><td class="meta-label">' . htmlspecialchars($label) . '</td><td>' . htmlspecialchars($value ?: '-') . '</td></tr>';
};
$date = function ($value) {
    return $value ? date('d M Y', strtotime($value)) : '-';
};
$fmt = function ($value) {
    $amount = (float)$value;
    return abs($amount - round($amount)) < 0.005
        ? number_format($amount, 0)
        : number_format($amount, 2);
};
$bold = function ($text) {
    return '<b style="color:#000;font-weight:bold;">' . $text . '</b>';
};
$pdfImageSrc = function ($relativePath) {
    $relativePath = trim((string)$relativePath);
    if ($relativePath === '') {
        return '';
    }

    $path = realpath(__DIR__ . '/../' . ltrim($relativePath, '/\\'));
    return ($path && is_file($path)) ? $path : '';
};

$grandTotal = (float)$quotation['grand_total'];
$roundOff = round($grandTotal) - $grandTotal;
$displayGrandTotal = round($grandTotal);
$terms = [
    'The quoted price is on an Ex Works (EXW) basis.',
    'This quotation is valid for 2 weeks from the date of quotation.',
    'Freight, packing, insurance, and handling charges, if applicable, will be borne by the buyer.',
    'Delivery timelines are subject to stock availability and transportation conditions.',
    'Once the order is confirmed, cancellation or modification may not be accepted.',
    'Warranty, if applicable, will be limited to manufacturing defects only.',
    'Goods once sold will not be taken back or exchanged unless mutually agreed.',
    'Subject to Pune jurisdiction only.',
];

$html = '<html><head><style>
@page { margin: 12mm 13mm; }
body { font-family: Arial, Helvetica, sans-serif; font-size: 9.2px; color: #303030; line-height: 1.12; }
.title { text-align:center; font-size: 15px; font-weight: 800; margin: 0 0 17px; letter-spacing: .6px; color: #222; text-transform: uppercase; }
.top-table, .box-table, .items-table, .terms-table { width:100%; border-collapse: collapse; }
.top-left { width: 39%; vertical-align: top; padding-left: 1px; padding-top: 1px; }
.top-right { width: 61%; vertical-align: top; text-align: center; }
.logo { height: 32px; max-width: 150px; margin-bottom: 7px; }
.company-name { font-size: 11px; font-weight: 800; margin-top: 2px; line-height: 1.05; color: #111; }
.company-address { line-height: 1.14; }
.pdf-label, .company-label, .meta-label, .inner-title, .inner-body b, .terms-body b, .words-row td, .sign {
    color: #000;
    font-weight: 800;
}
.meta-table { width: 92%; margin-left: auto; margin-right: auto; border-collapse: separate; border-spacing: 2px 2px; }
.meta-table td { border: 1px solid #4e73df; padding: 5.4px 7px; line-height: 1.08; text-align: left; }
.meta-label { width: 29%; background: #f1f5ff; }
.divider { border-top: 1px solid #777; margin: 12px 0 8px; }
.box-cell { border: 1px solid #4e73df; vertical-align: top; padding: 0; width: 100%; }
.inner-box { width: 100%; border-collapse: collapse; }
.inner-title { background: #f1f5ff; border-bottom: 1px solid #4e73df; padding: 5px 8px; line-height: 1.05; }
.inner-body { padding: 8px 10px; line-height: 1.28; height: 74px; vertical-align: top; }
.section-title { font-size: 16px; font-weight: bold; margin: 13px 0 7px; color: #000; }
.items-table th, .items-table td { border: 1px solid #4e73df; padding: 4.5px 4px; vertical-align: middle; text-align: center; }
.items-table th { font-size: 7.7px; font-weight: 800; text-align: center; background: #f1f5ff; line-height: 1.05; color: #111; }
.items-table td { font-size: 8.2px; line-height: 1.18; }
.text-center { text-align: center; }
.text-left { text-align: left; }
.text-right { text-align: right; }
.items-table td.text-left { text-align: left; }
.items-table td.text-right { text-align: center; }
.photo { width: 39px; height: 34px; object-fit: contain; }
.total-row td { font-weight: 800; background: #f8fbff; }
.words-row td { background: #f1f5ff; }
.terms-cell { border: 1px solid #4e73df; vertical-align: top; padding: 0; width: 64%; }
.sign-cell { border: 1px solid #4e73df; vertical-align: bottom; padding: 0; width: 36%; }
.terms-body { padding: 8px 10px; height: 84px; line-height: 1.28; vertical-align: top; }
.sign { text-align:center; vertical-align: bottom; padding-bottom: 17px; }
</style></head><body>';

$html .= '<div class="title"><b style="color:#000;font-weight:bold;">QUOTATION</b></div>';
$html .= '<table class="top-table"><tr><td class="top-left">';
if ($logoSrc) {
    $html .= '<img src="' . htmlspecialchars($logoSrc) . '" class="logo">';
}
$html .= '<div class="company-name">' . htmlspecialchars($company['name']) . '</div>';
$html .= '<div class="company-address">' . $company['address'] . '</div><br>';
$html .= $bold('GST:') . ' ' . htmlspecialchars($company['gst']) . '<br>';
if ($company['msme']) {
    $html .= $bold('MSME - UDYAM:') . ' ' . htmlspecialchars($company['msme']) . '<br>';
}
$html .= '<br>' . $bold('Mobile:') . ' ' . htmlspecialchars($company['mobile']) . '<br>';
$html .= $bold('Email:') . ' ' . htmlspecialchars($company['email']) . '<br>';
$html .= $bold('Web:') . ' ' . htmlspecialchars($company['web']);
$html .= '</td><td class="top-right">';
$html .= '<table class="meta-table">';
$html .= '<tr><td class="meta-label">' . $bold('Quotation No:') . '</td><td>' . htmlspecialchars($quotation['quotation_no']) . '</td><td class="meta-label">' . $bold('Date:') . '</td><td>' . htmlspecialchars($date($quotation['quotation_date'])) . '</td></tr>';
$html .= '<tr><td class="meta-label">' . $bold('Buyer Name:') . '</td><td>' . htmlspecialchars($quotation['customer_name']) . '</td><td class="meta-label">' . $bold('Mobile:') . '</td><td>' . htmlspecialchars($quotation['mobile_no'] ?: '-') . '</td></tr>';
$html .= '<tr><td class="meta-label">' . $bold('Payment Terms:') . '</td><td>' . htmlspecialchars($quotation['payment_terms'] ?: '-') . '</td><td class="meta-label">' . $bold('Date:') . '</td><td>' . htmlspecialchars($date($quotation['payment_date'])) . '</td></tr>';
$html .= '</table></td></tr></table>';

$html .= '<div class="divider"></div>';
$html .= '<table class="box-table"><tr>';
$html .= '<td class="box-cell"><table class="inner-box"><tr><td class="inner-title">' . $bold('Quotation To:') . '</td></tr><tr><td class="inner-body">';
$html .= '<b>' . htmlspecialchars($quotation['bill_to_name'] ?: $quotation['customer_name']) . '</b><br>';
$html .= nl2br(htmlspecialchars($quotation['bill_to_address'] ?: '-')) . '<br><br>';
$html .= $bold('GST:') . ' ' . htmlspecialchars($quotation['bill_to_gstin'] ?: '-') . '<br>';
$html .= $bold('Mobile:') . ' ' . htmlspecialchars($quotation['bill_to_mobile'] ?: $quotation['mobile_no'] ?: '-') . '</td></tr></table></td>';
$html .= '</tr></table>';

$html .= '<div class="section-title">' . $bold('Quotation Details') . '</div>';
$html .= '<table class="items-table"><thead><tr>
    <th style="width:4%;">' . $bold('Sr.') . '</th>
    <th style="width:8%;">' . $bold('Product<br>Photo') . '</th>
    <th style="width:19%;">' . $bold('Product Name') . '</th>
    <th style="width:10%;">' . $bold('HSN<br>Code') . '</th>
    <th style="width:6%;">' . $bold('Unit') . '</th>
    <th style="width:8%;">' . $bold('Price /<br>Unit') . '</th>
    <th style="width:6%;">' . $bold('Qty.') . '</th>
    <th style="width:10%;">' . $bold('Taxable<br>Value') . '</th>
    <th style="width:8%;">' . $bold('GST<br>Rate') . '</th>
    <th style="width:8%;">' . $bold('CGST<br>Amount') . '</th>
    <th style="width:8%;">' . $bold('SGST<br>Amount') . '</th>
    <th style="width:9%;">' . $bold('Total Price') . '</th>
</tr></thead><tbody>';

$sr = 1;
foreach ($items as $item) {
    $image = $pdfImageSrc($item['product_image']);
    $html .= '<tr>';
    $html .= '<td class="text-center">' . $sr . '</td>';
    $html .= '<td class="text-center">' . ($image ? '<img src="' . $image . '" class="photo">' : '') . '</td>';
    $html .= '<td>' . htmlspecialchars($item['product_name']) . '</td>';
    $html .= '<td class="text-center">' . htmlspecialchars($item['hsn'] ?: '-') . '</td>';
    $html .= '<td class="text-center">' . htmlspecialchars($item['unit'] ?: 'No.') . '</td>';
    $html .= '<td class="text-right">' . $fmt($item['item_price']) . '</td>';
    $html .= '<td class="text-center">' . rtrim(rtrim(number_format((float)$item['quantity'], 2), '0'), '.') . '</td>';
    $html .= '<td class="text-right">' . $fmt($item['taxable_value']) . '</td>';
    $html .= '<td class="text-center">' . $fmt($item['gst_rate']) . '%</td>';
    $html .= '<td class="text-right">' . $fmt($item['cgst_amount']) . '</td>';
    $html .= '<td class="text-right">' . $fmt($item['sgst_amount']) . '</td>';
    $html .= '<td class="text-right">' . $fmt($item['total_price']) . '</td>';
    $html .= '</tr>';
    $sr++;
}

$html .= '<tr class="total-row"><td colspan="7"></td><td class="text-right">' . $fmt($quotation['taxable_total']) . '</td><td></td><td class="text-right">' . $fmt($quotation['cgst_total']) . '</td><td class="text-right">' . $fmt($quotation['sgst_total']) . '</td><td class="text-right">' . $fmt($quotation['grand_total']) . '</td></tr>';
$roundOffDisplay = abs($roundOff) < 0.005 ? '' : $fmt($roundOff);
$html .= '<tr><td colspan="11" class="text-left">' . $bold('Round Off') . '</td><td class="text-right">' . $roundOffDisplay . '</td></tr>';
$html .= '<tr class="words-row"><td colspan="11" class="text-left">' . $bold('Grand Total in Words - ' . htmlspecialchars(QuotationAmountWords($displayGrandTotal)) . ' Rupees Only') . '</td><td class="text-right">' . $bold($fmt($displayGrandTotal)) . '</td></tr>';
$html .= '</tbody></table>';

$html .= '<br><table class="terms-table"><tr>';
$html .= '<td class="terms-cell"><table class="inner-box"><tr><td class="inner-title">' . $bold('Terms & Conditions:') . '</td></tr><tr><td class="terms-body">';
foreach ($terms as $index => $term) {
    $html .= ((int)$index + 1) . '. ' . htmlspecialchars($term) . '<br>';
}
$html .= '</td></tr></table></td>';
$html .= '<td class="sign-cell sign"><br><br><br><br>' . $bold('For, ' . htmlspecialchars($company['name']) . '<br>(Authorized Signatory)') . '</td>';
$html .= '</tr></table>';

$html .= '</body></html>';

if (ob_get_level() > 0) {
    ob_end_clean();
}

try {
    $mpdf = new \Mpdf\Mpdf([
        'format' => 'A4',
        'margin_left' => 13,
        'margin_right' => 13,
        'margin_top' => 12,
        'margin_bottom' => 12,
        'tempDir' => sys_get_temp_dir(),
    ]);
    $mpdf->showImageErrors = false;
    $mpdf->WriteHTML($html);
    $safeQuotationNo = preg_replace('/[^A-Za-z0-9_-]/', '_', $quotation['quotation_no']);
    $clientName = trim((string)($quotation['customer_name'] ?: $quotation['bill_to_name'] ?: 'Client'));
    $safeClientName = preg_replace('/[^A-Za-z0-9_-]+/', '_', $clientName);
    $safeClientName = trim($safeClientName, '_') ?: 'Client';
    $mpdf->Output('Quotation_' . $safeClientName . '_' . $safeQuotationNo . '.pdf', 'D');
} catch (Throwable $e) {
    error_log('quotation PDF failed: ' . $e->getMessage());
    http_response_code(500);
    echo 'Unable to generate quotation PDF. Please check server error log.';
}
exit;

