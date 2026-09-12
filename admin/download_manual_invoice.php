<?php
error_reporting(0);
ini_set('display_errors', 0);

session_start();
require_once '../vendor/autoload.php';
require_once '../includes/manual_invoice_functions.php';

if (!isset($_SESSION['admin_id'])) {
    die('Access denied.');
}

ensureManualInvoiceSchema($pdo);

if (!canAccess('manage_manual_invoices') && !canAccess('view_orders')) {
    die('Access denied.');
}

$invoiceId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$invoice = getManualInvoiceById($pdo, $invoiceId);
if (!$invoice) {
    die('Invoice not found.');
}

$items = getManualInvoiceItems($pdo, $invoiceId);
// Reconcile historical invoice snapshots too, without changing stored invoices or stock.
$isInterstate = ($invoice['tax_type'] ?? 'within_state') === 'out_of_state';
$invoice['igst_total'] = 0;
$invoice['taxable_total'] = 0;
$invoice['cgst_total'] = 0;
$invoice['sgst_total'] = 0;
foreach ($items as &$invoiceItem) {
    $invoiceItem = calculateManualInvoiceItem($invoiceItem, $isInterstate ? 'out_of_state' : 'within_state');
    $invoice['igst_total'] += $invoiceItem['igst_amount'];
    $invoice['taxable_total'] += $invoiceItem['taxable_value'];
    $invoice['cgst_total'] += $invoiceItem['cgst_amount'];
    $invoice['sgst_total'] += $invoiceItem['sgst_amount'];
}
unset($invoiceItem);

$company = [
    'name' => 'INPROTECH',
    'address' => 'A-98, Shree Lal Duplex, Opposite Khodiyar Dairy, Makrand Desai Road, Rangavdhutpura, Diwalipura, Vadodara - 390007',
    'gst' => '24AVEPS9404M1Z5',
    'msme' => '',
    'mobile' => '7218430068',
    'email' => 'info@everythingb2c.in',
    'web' => 'www.everythingb2c.in',
    'logo' => '../logo.webp',
    'pdf_logo' => '../uploads/logo_pdf.png',
];

$logoPath = realpath(__DIR__ . '/../uploads/logo_pdf.png') ?: realpath(__DIR__ . '/../logo.webp');
$logoSrc = $logoPath ? $logoPath : '';
$signatureSrc = realpath(__DIR__ . '/assets/images/authorized-signature.png');
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

$grandTotal = (float)$invoice['grand_total'];
$roundOff = round($grandTotal) - $grandTotal;
$displayGrandTotal = round($grandTotal);
$hasShipTo = trim((string)($invoice['ship_to_name'] ?? '')) !== ''
    || trim((string)($invoice['ship_to_gstin'] ?? '')) !== ''
    || trim((string)($invoice['ship_to_mobile'] ?? '')) !== ''
    || trim((string)($invoice['ship_to_address'] ?? '')) !== '';

$html = '<html><head><style>
@page { margin: 12mm 13mm; }
body { font-family: mulish, sans-serif !important; font-size: 9pt; color: #111; line-height: 1.25; }
.title { text-align:center; font-size: 11pt; font-weight: 800; margin: 0 0 17px; letter-spacing: .2px; color: #111; }
.top-table, .box-table, .items-table, .bank-table { width:100%; border-collapse: collapse; }
.top-left { width: 45%; vertical-align: top; padding-left: 1px; padding-top: 1px; line-height: 1.45; }
.header-gap { width: 5%; }
.top-right { width: 50%; vertical-align: bottom; text-align: center; }
.logo { width: 241px; height: auto; margin-bottom: 8px; }
.signature { width: 130px; height: auto; }
.company-name { font-size: 11pt; font-weight: 800; margin-top: 2px; margin-bottom: 4px; line-height: 1.45; color: #111; }
.company-gst { line-height: 1.45; }
.gst-gap { font-size: 2px; line-height: 1; }
.company-address { font-size: 9pt; line-height: 1.45; margin-top: 0; margin-bottom: 4px; }
.pdf-label, .company-label, .meta-label, .inner-title, .inner-body b, .bank-body b, .words-row td, .sign {
    color: #000;
    font-weight: 800;
}
.meta-table { width: 100%; margin-left: auto; margin-right: auto; border-collapse: separate; border-spacing: 2px 2px; }
.meta-table td { border: 1px solid #4e73df; height: 34px; font-size: 9pt; padding: 3px 4px; vertical-align: middle; line-height: 1.08; text-align: left; }
.meta-table td { width: 27%; }
.meta-table td.meta-label { width: 28%; background: #f1f5ff; white-space: nowrap; font-size: 9pt; padding-left: 4px; padding-right: 4px; }
.meta-table td.meta-short { width: 18%; }
.divider { border-top: 1px solid #777; margin: 12px 0 8px; }
.box-cell { border: 1px solid #4e73df; vertical-align: top; padding: 0; width: 50%; }
.inner-box { width: 100%; border-collapse: collapse; }
.inner-title { font-size: 11pt; background: #f1f5ff; border-bottom: 1px solid #4e73df; padding: 5px 8px; line-height: 1.05; }
.bank-title { font-size: 11pt; font-weight: 800; text-align: center; color: #111; }
.inner-body { padding: 8px 10px; line-height: 1.28; height: 74px; vertical-align: top; }
.section-title { font-size: 11pt; font-weight: bold; margin: 13px 0 7px; color: #000; }
.items-table th, .items-table td { border: 1px solid #4e73df; padding: 6px 3px; vertical-align: middle; text-align: center; }
.items-table th { font-size: 9pt; font-weight: 800; text-align: center; background: #f1f5ff; line-height: 1.05; color: #111; }
.items-table td { font-size: 9pt; line-height: 1.18; }
.text-center { text-align: center; }
.text-left { text-align: left; }
.text-right { text-align: right; }
.items-table td.text-left { text-align: left; }
.items-table td.text-right { text-align: center; }
.photo { width: 39px; height: 34px; object-fit: contain; }
.total-row td { font-weight: 800; background: #f8fbff; }
.words-row td { background: #f1f5ff; }
.bank-cell { border: 1px solid #4e73df; vertical-align: top; padding: 0; width: 50%; }
.bank-body { font-size: 9pt; padding: 8px 10px; height: 85px; line-height: 1.28; vertical-align: top; color: #000; }
.sign { font-size: 9pt; text-align:center; vertical-align: bottom; padding-bottom: 17px; }
</style></head><body>';

$html .= '<div class="title"><b style="color:#000;font-weight:bold;">TAX INVOICE</b></div>';
$html .= '<table class="top-table"><tr><td class="top-left">';
if ($logoSrc) {
    $html .= '<img src="' . htmlspecialchars($logoSrc) . '" class="logo">';
}
$html .= '<div class="company-name"><b>' . htmlspecialchars($company['name']) . '</b></div>';
$html .= '<span class="gst-gap"><br></span><div class="company-gst">' . $bold('GSTIN No. -') . ' ' . htmlspecialchars($company['gst']) . '</div><span class="gst-gap"><br></span>';
$html .= '<div class="company-address">' . $bold('Address:') . ' ' . $company['address'] . '</div>';
if ($company['msme']) {
    $html .= $bold('MSME - UDYAM:') . ' ' . htmlspecialchars($company['msme']) . '<br>';
}
$html .= $bold('Mobile:') . ' ' . htmlspecialchars($company['mobile']) . '<br>';
$html .= $bold('Email:') . ' ' . htmlspecialchars($company['email']) . '<br>';
$html .= $bold('Web:') . ' ' . htmlspecialchars($company['web']);
$html .= '</td><td class="header-gap"></td><td class="top-right">';
$html .= '<table class="meta-table">';
$html .= '<tr><td class="meta-label">' . $bold('Invoice No:') . '</td><td>' . htmlspecialchars($invoice['invoice_no']) . '</td><td class="meta-label meta-short">' . $bold('Date:') . '</td><td>' . htmlspecialchars($date($invoice['invoice_date'])) . '</td></tr>';
$html .= '<tr><td class="meta-label">' . $bold('E-Way Bill No:') . '</td><td>' . htmlspecialchars($invoice['eway_bill_no'] ?: '-') . '</td><td class="meta-label meta-short">' . $bold('Date:') . '</td><td>' . htmlspecialchars($date($invoice['eway_bill_date'])) . '</td></tr>';
$html .= '<tr><td class="meta-label">' . $bold('Buyer PO No:') . '</td><td>' . htmlspecialchars($invoice['buyer_po_no'] ?: '-') . '</td><td class="meta-label meta-short">' . $bold('Date:') . '</td><td>' . htmlspecialchars($date($invoice['buyer_po_date'])) . '</td></tr>';
$html .= '<tr><td class="meta-label">' . $bold('Buyer Name:') . '</td><td>' . htmlspecialchars($invoice['customer_name']) . '</td><td class="meta-label meta-short">' . $bold('Mobile:') . '</td><td>' . htmlspecialchars($invoice['mobile_no'] ?: '-') . '</td></tr>';
$html .= '<tr><td class="meta-label">' . $bold('Payment Terms:') . '</td><td>' . htmlspecialchars($invoice['payment_terms'] ?: '-') . '</td><td class="meta-label meta-short">' . $bold('Date:') . '</td><td>' . htmlspecialchars($date($invoice['payment_date'])) . '</td></tr>';
$html .= '<tr><td class="meta-label">' . $bold('Transporter') . '</td><td>' . htmlspecialchars($invoice['transporter'] ?: '-') . '</td><td class="meta-label meta-short">' . $bold('LR No.:') . '</td><td>' . htmlspecialchars($invoice['lr_no'] ?: '-') . '</td></tr>';
$html .= '</table></td></tr></table>';

$html .= '<div class="divider"></div>';
$html .= '<table class="box-table"><tr>';
$html .= '<td class="box-cell" ' . (!$hasShipTo ? 'style="width:100%;"' : '') . '><table class="inner-box"><tr><td class="inner-title">' . $bold('Invoice To:') . '</td></tr><tr><td class="inner-body">';
$html .= '<b>' . htmlspecialchars($invoice['bill_to_name'] ?: $invoice['customer_name']) . '</b><br>';
$html .= nl2br(htmlspecialchars($invoice['bill_to_address'] ?: '-')) . '<br><br>';
$html .= $bold('GST:') . ' ' . htmlspecialchars($invoice['bill_to_gstin'] ?: '-') . '<br>';
$html .= $bold('Mobile:') . ' ' . htmlspecialchars($invoice['bill_to_mobile'] ?: $invoice['mobile_no'] ?: '-') . '</td></tr></table></td>';
if ($hasShipTo) {
    $html .= '<td class="box-cell"><table class="inner-box"><tr><td class="inner-title">' . $bold('Ship To:') . '</td></tr><tr><td class="inner-body">';
    $html .= '<b>' . htmlspecialchars($invoice['ship_to_name'] ?: '-') . '</b><br>';
    $html .= nl2br(htmlspecialchars($invoice['ship_to_address'] ?: '-')) . '<br><br>';
    $html .= $bold('GST:') . ' ' . htmlspecialchars($invoice['ship_to_gstin'] ?: '-') . '<br>';
    $html .= $bold('Mobile:') . ' ' . htmlspecialchars($invoice['ship_to_mobile'] ?: '-') . '</td></tr></table></td>';
}
$html .= '</tr></table>';

$html .= '<div class="section-title">' . $bold('Invoice Details') . '</div>';
$html .= '<table class="items-table"><thead><tr>
    <th style="width:4%;">' . $bold('Sr.') . '</th>
    <th style="width:8%;">' . $bold('Product<br>Photo') . '</th>
    <th style="width:' . ($isInterstate ? 27 : 19) . '%;">' . $bold('Product Name') . '</th>
    <th style="width:10%;">' . $bold('HSN<br>Code') . '</th>
    <th style="width:6%;">' . $bold('Unit') . '</th>
    <th style="width:8%;">' . $bold('Price /<br>Unit') . '</th>
    <th style="width:6%;">' . $bold('Qty.') . '</th>
    <th style="width:10%;">' . $bold('Taxable<br>Value') . '</th>
    <th style="width:8%;">' . $bold('GST<br>Rate') . '</th>
    ' . ($isInterstate
        ? '<th style="width:8%;">' . $bold('IGST<br>Amount') . '</th>'
        : '<th style="width:8%;">' . $bold('CGST<br>Amount') . '</th><th style="width:8%;">' . $bold('SGST<br>Amount') . '</th>') . '
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
    if ($isInterstate) {
        $html .= '<td class="text-right">' . $fmt($item['igst_amount']) . '</td>';
    } else {
    $html .= '<td class="text-right">' . $fmt($item['cgst_amount']) . '</td>';
    $html .= '<td class="text-right">' . $fmt($item['sgst_amount']) . '</td>';
    }
    $html .= '<td class="text-right">' . $fmt($item['total_price']) . '</td>';
    $html .= '</tr>';
    $sr++;
}

$html .= '<tr class="total-row"><td colspan="7"></td><td class="text-right">' . $fmt($invoice['taxable_total']) . '</td><td></td><td class="text-right">' . ($isInterstate ? $fmt($invoice['igst_total']) : $fmt($invoice['cgst_total']) . '</td><td class="text-right">' . $fmt($invoice['sgst_total'])) . '</td><td class="text-right">' . $fmt($invoice['grand_total']) . '</td></tr>';
$roundOffDisplay = abs($roundOff) < 0.005 ? '' : $fmt($roundOff);
$html .= '<tr><td colspan="' . ($isInterstate ? 10 : 11) . '" class="text-left">' . $bold('Round Off') . '</td><td class="text-right">' . $roundOffDisplay . '</td></tr>';
$html .= '<tr class="words-row"><td colspan="' . ($isInterstate ? 10 : 11) . '" class="text-left">' . $bold('Grand Total in Words - ' . htmlspecialchars(manualInvoiceAmountWords($displayGrandTotal)) . ' Rupees Only') . '</td><td class="text-right">' . $bold($fmt($displayGrandTotal)) . '</td></tr>';
$html .= '</tbody></table>';

$html .= '<br><table class="bank-table"><tr>';
$html .= '<td class="bank-cell"><table class="inner-box"><tr><td class="inner-title bank-title">' . $bold('Bank Account Details:') . '</td></tr><tr><td class="bank-body">';
$html .= '<b>ACCOUNT NAME</b> - INPROTECH<br>';
$html .= '<b>Type of Account</b> - CURRENT<br>';
$html .= '<b>ACCOUNT NUMBER</b> - 50200091758435<br>';
$html .= '<b>IFSC CODE</b> - HDFC0001712<br>';
$html .= '<b>BANK</b> - HDFC';
$html .= '</td></tr></table></td>';
$html .= '<td class="bank-cell sign">';
if ($signatureSrc) {
    $html .= '<img src="' . htmlspecialchars($signatureSrc) . '" class="signature" alt="Authorized signature"><br>';
}
$html .= $bold('For, INPROTECH (Authorized Signatory)') . '</td>';
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
        'default_font' => 'mulish',
        'fontDir' => array_merge((new \Mpdf\Config\ConfigVariables())->getDefaults()['fontDir'], [__DIR__ . '/../assets/fonts/mulish']),
        'fontdata' => array_merge((new \Mpdf\Config\FontVariables())->getDefaults()['fontdata'], ['mulish' => ['R' => 'Mulish-Regular.ttf', 'B' => 'Mulish-Bold.ttf']]),
        'shrink_tables_to_fit' => 1,
    ]);
    $mpdf->showImageErrors = false;
    $mpdf->WriteHTML($html);
    $safeInvoiceNo = preg_replace('/[^A-Za-z0-9_-]/', '_', $invoice['invoice_no']);
    $clientName = trim((string)($invoice['customer_name'] ?: $invoice['bill_to_name'] ?: 'Client'));
    $safeClientName = preg_replace('/[^A-Za-z0-9_-]+/', '_', $clientName);
    $safeClientName = trim($safeClientName, '_') ?: 'Client';
    $mpdf->Output('Invoice_' . $safeClientName . '_' . $safeInvoiceNo . '.pdf', 'D');
} catch (Throwable $e) {
    error_log('Manual invoice PDF failed: ' . $e->getMessage());
    http_response_code(500);
    echo 'Unable to generate invoice PDF. Please check server error log.';
}
exit;
