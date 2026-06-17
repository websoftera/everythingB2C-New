<?php
session_start();
require_once '../config/database.php';
require_once '../includes/manual_invoice_functions.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

ensureManualInvoiceSchema($pdo);

if (!canAccess('manage_manual_invoices') && !canAccess('view_orders')) {
    header('Location: permission-denied.php');
    exit;
}

$pageTitle = 'Invoice Generate';
$success_message = $_SESSION['success_message'] ?? '';
$error_message = $_SESSION['error_message'] ?? '';
unset($_SESSION['success_message'], $_SESSION['error_message']);

function manualInvoiceHasLetter($value) {
    return preg_match('/[A-Za-z]/', (string)$value) === 1;
}

function manualInvoiceValidMobile($value, $required = true) {
    $value = trim((string)$value);
    if ($value === '') {
        return !$required;
    }
    return preg_match('/^[0-9+\-\s]{8,15}$/', $value) === 1;
}

function manualInvoiceValidDateValue($value, $required = false) {
    $value = trim((string)$value);
    if ($value === '') {
        return !$required;
    }
    $date = DateTime::createFromFormat('Y-m-d', $value);
    return $date && $date->format('Y-m-d') === $value;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_invoice'])) {
    $deleteId = (int)($_POST['invoice_id'] ?? 0);
    if ($deleteId > 0 && deleteManualInvoice($pdo, $deleteId)) {
        $_SESSION['success_message'] = 'Invoice deleted successfully.';
    } else {
        $_SESSION['error_message'] = 'Unable to delete invoice.';
    }
    header('Location: invoice_generate.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_invoice'])) {
    try {
        $editingInvoiceId = (int)($_POST['invoice_id'] ?? 0);
        $invoiceNo = trim($_POST['invoice_no'] ?? '') ?: generateManualInvoiceNumber($pdo);
        $items = $_POST['items'] ?? [];
        $preparedItems = [];
        $taxableTotal = 0;
        $cgstTotal = 0;
        $sgstTotal = 0;
        $grandTotal = 0;

        foreach ($items as $index => $item) {
            $productName = trim($item['product_name'] ?? '');
            if ($productName === '') {
                continue;
            }

            if ((int)($item['product_id'] ?? 0) <= 0) {
                throw new Exception('Please select a valid product.');
            }

            $hsn = trim((string)($item['hsn'] ?? ''));
            if ($hsn !== '' && preg_match('/^\d+$/', $hsn) !== 1) {
                throw new Exception('HSN must contain numbers only.');
            }

            $gstRate = (int)round((float)($item['gst_rate'] ?? 0));
            $itemMrp = (int)round((float)($item['item_mrp'] ?? 0));
            $itemPrice = (int)round((float)($item['item_price'] ?? 0));

            if ($itemPrice <= 0) {
                throw new Exception('Item price must be greater than zero.');
            }

            $quantity = (float)($item['quantity'] ?? 0);
            if ($quantity <= 0 || floor($quantity) != $quantity) {
                throw new Exception('Product quantity must be a whole number like 1, 2, 3.');
            }

            $prepared = calculateManualInvoiceItem([
                'product_id' => (int)($item['product_id'] ?? 0),
                'product_name' => sanitizeInput($productName),
                'product_image' => trim($item['product_image'] ?? ''),
                'hsn' => sanitizeInput($hsn),
                'unit' => sanitizeInput($item['unit'] ?? 'No.'),
                'gst_rate' => $gstRate,
                'item_mrp' => $itemMrp,
                'item_price' => $itemPrice,
                'quantity' => (int)$quantity,
                'sort_order' => $index,
            ]);

            $prepared['item_mrp'] = max(0, (float)$prepared['item_mrp']);
            $preparedItems[] = $prepared;
            $taxableTotal += $prepared['taxable_value'];
            $cgstTotal += $prepared['cgst_amount'];
            $sgstTotal += $prepared['sgst_amount'];
            $grandTotal += $prepared['total_price'];
        }

        if (empty($preparedItems)) {
            throw new Exception('Please add at least one product.');
        }

        $customerName = sanitizeInput($_POST['customer_name'] ?? '');
        if ($customerName === '' || !manualInvoiceHasLetter($customerName)) {
            throw new Exception('Customer name must contain letters.');
        }

        if (!manualInvoiceValidDateValue($_POST['invoice_date'] ?? '', true)) {
            throw new Exception('Valid invoice date is required.');
        }

        if (!manualInvoiceValidMobile($_POST['mobile_no'] ?? '', true)) {
            throw new Exception('Enter a valid mobile number.');
        }

        if (trim($_POST['bill_to_name'] ?? '') === '' || !manualInvoiceHasLetter($_POST['bill_to_name'] ?? '') || trim($_POST['bill_to_mobile'] ?? '') === '' || trim($_POST['bill_to_address'] ?? '') === '') {
            throw new Exception('Bill To name, mobile number, and address are required.');
        }

        if (!manualInvoiceValidMobile($_POST['bill_to_mobile'] ?? '', true)) {
            throw new Exception('Enter a valid Bill To mobile number.');
        }

        if (!manualInvoiceValidMobile($_POST['ship_to_mobile'] ?? '', false)) {
            throw new Exception('Enter a valid Ship To mobile number.');
        }

        if (trim($_POST['ship_to_name'] ?? '') !== '' && !manualInvoiceHasLetter($_POST['ship_to_name'])) {
            throw new Exception('Ship To name must contain letters.');
        }

        foreach (['eway_bill_date', 'buyer_po_date', 'payment_date'] as $optionalDateField) {
            if (!manualInvoiceValidDateValue($_POST[$optionalDateField] ?? '', false)) {
                throw new Exception('Please enter valid dates.');
            }
        }

        $pdo->beginTransaction();
        $invoiceData = [
            $invoiceNo,
            $_POST['invoice_date'],
            $customerName,
            sanitizeInput($_POST['mobile_no'] ?? ''),
            sanitizeInput($_POST['eway_bill_no'] ?? ''),
            $_POST['eway_bill_date'] ?: null,
            sanitizeInput($_POST['buyer_po_no'] ?? ''),
            $_POST['buyer_po_date'] ?: null,
            sanitizeInput($_POST['payment_terms'] ?? 'Manual'),
            $_POST['payment_date'] ?: null,
            sanitizeInput($_POST['transporter'] ?? ''),
            sanitizeInput($_POST['lr_no'] ?? ''),
            sanitizeInput($_POST['bill_to_name'] ?? ''),
            sanitizeInput($_POST['bill_to_gstin'] ?? ''),
            sanitizeInput($_POST['bill_to_mobile'] ?? ''),
            sanitizeInput($_POST['bill_to_address'] ?? ''),
            sanitizeInput($_POST['ship_to_name'] ?? ''),
            sanitizeInput($_POST['ship_to_gstin'] ?? ''),
            sanitizeInput($_POST['ship_to_mobile'] ?? ''),
            sanitizeInput($_POST['ship_to_address'] ?? ''),
            $taxableTotal,
            $cgstTotal,
            $sgstTotal,
            $grandTotal,
        ];

        if ($editingInvoiceId > 0) {
            $stmt = $pdo->prepare("
                UPDATE manual_invoices SET
                    invoice_no = ?, invoice_date = ?, customer_name = ?, mobile_no = ?,
                    eway_bill_no = ?, eway_bill_date = ?, buyer_po_no = ?, buyer_po_date = ?,
                    payment_terms = ?, payment_date = ?, transporter = ?, lr_no = ?,
                    bill_to_name = ?, bill_to_gstin = ?, bill_to_mobile = ?, bill_to_address = ?,
                    ship_to_name = ?, ship_to_gstin = ?, ship_to_mobile = ?, ship_to_address = ?,
                    taxable_total = ?, cgst_total = ?, sgst_total = ?, grand_total = ?
                WHERE id = ?
            ");
            $stmt->execute(array_merge($invoiceData, [$editingInvoiceId]));
            $invoiceId = $editingInvoiceId;
            $pdo->prepare("DELETE FROM manual_invoice_items WHERE invoice_id = ?")->execute([$invoiceId]);
        } else {
            $stmt = $pdo->prepare("
                INSERT INTO manual_invoices (
                    invoice_no, invoice_date, customer_name, mobile_no, eway_bill_no, eway_bill_date,
                    buyer_po_no, buyer_po_date, payment_terms, payment_date, transporter, lr_no,
                    bill_to_name, bill_to_gstin, bill_to_mobile, bill_to_address,
                    ship_to_name, ship_to_gstin, ship_to_mobile, ship_to_address,
                    taxable_total, cgst_total, sgst_total, grand_total, created_by
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute(array_merge($invoiceData, [$_SESSION['admin_id'] ?? null]));
            $invoiceId = (int)$pdo->lastInsertId();
        }

        $itemStmt = $pdo->prepare("
            INSERT INTO manual_invoice_items (
                invoice_id, product_id, product_name, product_image, hsn, unit, gst_rate,
                item_mrp, item_price, quantity, taxable_value, cgst_amount, sgst_amount,
                total_price, sort_order
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        foreach ($preparedItems as $item) {
            $itemStmt->execute([
                $invoiceId,
                $item['product_id'] ?: null,
                $item['product_name'],
                $item['product_image'],
                $item['hsn'],
                $item['unit'],
                $item['gst_rate'],
                $item['item_mrp'],
                $item['item_price'],
                $item['quantity'],
                $item['taxable_value'],
                $item['cgst_amount'],
                $item['sgst_amount'],
                $item['total_price'],
                $item['sort_order'],
            ]);
        }

        $pdo->commit();
        $_SESSION['success_message'] = $editingInvoiceId > 0 ? 'Invoice updated successfully.' : 'Invoice saved successfully.';
        header('Location: invoice_generate.php');
        exit;
    } catch (Exception $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        $error_message = $e->getMessage();
    }
}

$products = getManualInvoiceProducts($pdo);
$productPayload = [];
foreach ($products as $product) {
    $productPayload[] = [
        'id' => (int)$product['id'],
        'name' => cleanProductName($product['name']),
        'hsn' => (string)($product['hsn'] ?? ''),
        'unit' => getProductUnitLabel($product),
        'gst_rate' => (float)($product['gst_rate'] ?? 0),
        'mrp' => (float)($product['mrp'] ?? 0),
        'price' => (float)($product['selling_price'] ?? 0),
        'image' => (string)($product['main_image'] ?? ''),
    ];
}

$filters = [
    'month' => $_GET['month'] ?? '',
    'date' => $_GET['date'] ?? '',
    'search' => trim($_GET['search'] ?? ''),
];
$createdInvoices = getManualInvoices($pdo, $filters);
$nextInvoiceNo = generateManualInvoiceNumber($pdo);
$today = date('Y-m-d');
$editingInvoice = null;
$editingItems = [];
$editId = isset($_GET['edit']) ? (int)$_GET['edit'] : 0;
if ($editId > 0) {
    $editingInvoice = getManualInvoiceById($pdo, $editId);
    if ($editingInvoice) {
        $editingItems = getManualInvoiceItems($pdo, $editId);
    }
}

$formInvoice = $editingInvoice ?: [
    'id' => 0,
    'invoice_no' => $nextInvoiceNo,
    'invoice_date' => $today,
    'customer_name' => '',
    'mobile_no' => '',
    'eway_bill_no' => '',
    'eway_bill_date' => $today,
    'buyer_po_no' => '',
    'buyer_po_date' => $today,
    'payment_terms' => 'Manual',
    'payment_date' => $today,
    'transporter' => '',
    'lr_no' => '',
    'bill_to_name' => '',
    'bill_to_gstin' => '',
    'bill_to_mobile' => '',
    'bill_to_address' => '',
    'ship_to_name' => '',
    'ship_to_gstin' => '',
    'ship_to_mobile' => '',
    'ship_to_address' => '',
];

$editingItemsPayload = [];
foreach ($editingItems as $item) {
    $editingItemsPayload[] = [
        'product_id' => (int)($item['product_id'] ?? 0),
        'product_name' => $item['product_name'] ?? '',
        'product_image' => $item['product_image'] ?? '',
        'hsn' => $item['hsn'] ?? '',
        'unit' => $item['unit'] ?? 'No.',
        'gst_rate' => (float)($item['gst_rate'] ?? 0),
        'item_mrp' => (float)($item['item_mrp'] ?? 0),
        'item_price' => (float)($item['item_price'] ?? 0),
        'quantity' => (float)($item['quantity'] ?? 1),
    ];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?> - EverythingB2C</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="assets/css/admin.css" rel="stylesheet">
    <style>
        .manual-invoice-page .card { border-radius: 4px; }
        .manual-invoice-page .form-label { font-weight: 600; color: #111827; }
        .manual-invoice-page .form-control,
        .manual-invoice-page .form-select { border-radius: 4px; min-height: 38px; }
        .manual-invoice-page .was-validated .form-control:valid,
        .manual-invoice-page .was-validated .form-select:valid,
        .manual-invoice-page .form-control.is-valid,
        .manual-invoice-page .form-select.is-valid {
            border-color: #ced4da;
            background-image: none;
            padding-right: .75rem;
        }
        .manual-invoice-page .was-validated .form-control:valid:focus,
        .manual-invoice-page .was-validated .form-select:valid:focus,
        .manual-invoice-page .form-control.is-valid:focus,
        .manual-invoice-page .form-select.is-valid:focus {
            border-color: #86b7fe;
            box-shadow: 0 0 0 .25rem rgba(13, 110, 253, .25);
        }
        .manual-invoice-product-row {
            display: grid;
            grid-template-columns: minmax(190px, 1.7fr) 92px minmax(100px, 1fr) minmax(100px, 1fr) minmax(92px, .85fr) minmax(110px, 1fr) minmax(120px, 1fr) 86px 82px;
            gap: 14px 12px;
            align-items: start;
            padding: 14px;
            border: 1px solid #dbe3ef;
            border-radius: 6px;
            background: #fff;
        }
        .manual-invoice-field {
            min-width: 0;
            display: flex;
            flex-direction: column;
        }
        .manual-invoice-product-row .form-label { margin-bottom: 8px; white-space: nowrap; }
        .manual-invoice-product-row .invalid-feedback {
            display: block;
            visibility: hidden;
            min-height: 20px;
            margin-top: 4px;
            white-space: nowrap;
        }
        .manual-invoice-page .was-validated .manual-invoice-product-row .form-control:invalid ~ .invalid-feedback,
        .manual-invoice-page .was-validated .manual-invoice-product-row .form-select:invalid ~ .invalid-feedback {
            visibility: visible;
        }
        .manual-invoice-product-row + .manual-invoice-product-row { margin-top: 12px; }
        .manual-invoice-image-box {
            width: 100%;
            height: 42px;
            border: 1px solid #dbe3ef;
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #6b7280;
            font-size: 11px;
            overflow: hidden;
            background: #f8fafc;
        }
        .manual-invoice-image-box img { max-width: 100%; max-height: 100%; object-fit: contain; }
        .manual-total { color: #224abe; font-weight: 800; }
        .action-btn { width: 31px; height: 31px; display: inline-flex; align-items: center; justify-content: center; }
        .invoice-actions { display: flex; gap: 6px; align-items: center; }
        .invoice-actions form { margin: 0; }
        @media (max-width: 1400px) {
            .manual-invoice-product-row { grid-template-columns: repeat(3, minmax(180px, 1fr)); }
        }
        @media (max-width: 992px) {
            .manual-invoice-product-row { grid-template-columns: repeat(2, minmax(160px, 1fr)); }
        }
        @media (max-width: 768px) {
            .manual-invoice-product-row { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
<div class="everythingb2c-admin-container">
    <?php include 'includes/sidebar.php'; ?>
    <div class="everythingb2c-main-content">
        <?php include 'includes/header.php'; ?>
        <div class="everythingb2c-dashboard-content manual-invoice-page">
            <div class="container-fluid">
                <h1 class="h3 mb-4">Invoice Generate</h1>

                <?php if ($success_message): ?>
                    <div class="alert alert-success"><?php echo htmlspecialchars($success_message); ?></div>
                <?php endif; ?>
                <?php if ($error_message): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($error_message); ?></div>
                <?php endif; ?>

                <form method="POST" class="card mb-4 needs-validation" id="manualInvoiceForm" novalidate>
                    <input type="hidden" name="invoice_id" value="<?php echo (int)($formInvoice['id'] ?? 0); ?>">
                    <div class="card-header bg-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0"><?php echo $editingInvoice ? 'Edit Manual Invoice' : 'Create Manual Invoice'; ?></h5>
                            <?php if ($editingInvoice): ?>
                                <a href="invoice_generate.php" class="btn btn-sm btn-outline-secondary"><i class="fas fa-plus"></i> New Invoice</a>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">Invoice No.</label>
                                <input type="text" name="invoice_no" class="form-control" value="<?php echo htmlspecialchars($formInvoice['invoice_no']); ?>" pattern="[A-Za-z0-9\-\/]+" required>
                                <div class="invalid-feedback">Invoice number is required.</div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Invoice Date</label>
                                <input type="date" name="invoice_date" class="form-control" value="<?php echo htmlspecialchars($formInvoice['invoice_date']); ?>" required>
                                <div class="invalid-feedback">Invoice date is required.</div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Customer Name</label>
                                <input type="text" name="customer_name" class="form-control text-name-field" value="<?php echo htmlspecialchars($formInvoice['customer_name']); ?>" pattern=".*[A-Za-z].*" required>
                                <div class="invalid-feedback">Customer name must contain letters.</div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Mobile No.</label>
                                <input type="tel" inputmode="numeric" name="mobile_no" class="form-control phone-field" value="<?php echo htmlspecialchars($formInvoice['mobile_no']); ?>" pattern="[0-9+\-\s]{8,15}" required>
                                <div class="invalid-feedback">Enter a valid mobile number.</div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">E-Way Bill No.</label>
                                <input type="text" name="eway_bill_no" class="form-control" value="<?php echo htmlspecialchars($formInvoice['eway_bill_no']); ?>">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">E-Way Bill Date</label>
                                <input type="date" name="eway_bill_date" class="form-control" value="<?php echo htmlspecialchars($formInvoice['eway_bill_date']); ?>">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Buyer PO No.</label>
                                <input type="text" name="buyer_po_no" class="form-control" value="<?php echo htmlspecialchars($formInvoice['buyer_po_no']); ?>">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Buyer PO Date</label>
                                <input type="date" name="buyer_po_date" class="form-control" value="<?php echo htmlspecialchars($formInvoice['buyer_po_date']); ?>">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Payment Terms</label>
                                <input type="text" name="payment_terms" class="form-control text-name-field" value="<?php echo htmlspecialchars($formInvoice['payment_terms']); ?>" pattern=".*[A-Za-z].*" required>
                                <div class="invalid-feedback">Payment terms are required.</div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Payment Date</label>
                                <input type="date" name="payment_date" class="form-control" value="<?php echo htmlspecialchars($formInvoice['payment_date']); ?>">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Transporter</label>
                                <input type="text" name="transporter" class="form-control text-name-field" value="<?php echo htmlspecialchars($formInvoice['transporter']); ?>" pattern=".*[A-Za-z].*">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">LR No.</label>
                                <input type="text" name="lr_no" class="form-control" value="<?php echo htmlspecialchars($formInvoice['lr_no']); ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Bill To Name</label>
                                <input type="text" name="bill_to_name" class="form-control text-name-field" value="<?php echo htmlspecialchars($formInvoice['bill_to_name']); ?>" pattern=".*[A-Za-z].*" required>
                                <div class="invalid-feedback">Bill To name must contain letters.</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Ship To Name</label>
                                <input type="text" name="ship_to_name" class="form-control text-name-field" value="<?php echo htmlspecialchars($formInvoice['ship_to_name']); ?>" pattern=".*[A-Za-z].*">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Bill To GSTIN / UIN</label>
                                <input type="text" name="bill_to_gstin" class="form-control" value="<?php echo htmlspecialchars($formInvoice['bill_to_gstin']); ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Ship To GSTIN / UIN</label>
                                <input type="text" name="ship_to_gstin" class="form-control" value="<?php echo htmlspecialchars($formInvoice['ship_to_gstin']); ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Bill To Mobile No.</label>
                                <input type="tel" inputmode="numeric" name="bill_to_mobile" class="form-control phone-field" value="<?php echo htmlspecialchars($formInvoice['bill_to_mobile']); ?>" pattern="[0-9+\-\s]{8,15}" required>
                                <div class="invalid-feedback">Bill To mobile number is required.</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Ship To Mobile No.</label>
                                <input type="tel" inputmode="numeric" name="ship_to_mobile" class="form-control phone-field" value="<?php echo htmlspecialchars($formInvoice['ship_to_mobile']); ?>" pattern="[0-9+\-\s]{8,15}">
                                <div class="invalid-feedback">Enter a valid Ship To mobile number.</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Bill To Address</label>
                                <textarea name="bill_to_address" class="form-control" rows="4" required><?php echo htmlspecialchars($formInvoice['bill_to_address']); ?></textarea>
                                <div class="invalid-feedback">Bill To address is required.</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Ship To Address</label>
                                <textarea name="ship_to_address" class="form-control" rows="4"><?php echo htmlspecialchars($formInvoice['ship_to_address']); ?></textarea>
                            </div>
                        </div>

                        <div class="d-flex align-items-center justify-content-between mt-4 mb-3">
                            <h5 class="mb-0">Products</h5>
                            <button type="button" class="btn btn-outline-primary btn-sm" id="addInvoiceProduct">
                                <i class="fas fa-plus"></i> Add Product
                            </button>
                        </div>
                        <div id="invoiceProductRows"></div>

                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <div class="manual-total">Total: Rs. <span id="invoiceGrandTotal">0</span></div>
                            <button type="submit" name="save_invoice" class="btn btn-primary">
                                <i class="fas fa-save"></i> Save Invoice
                            </button>
                        </div>
                    </div>
                </form>

                <div class="card mb-4">
                    <div class="card-body">
                        <form method="GET" class="row g-3 align-items-end">
                            <div class="col-md-3">
                                <label class="form-label">Month Wise</label>
                                <input type="month" name="month" class="form-control" value="<?php echo htmlspecialchars($filters['month']); ?>">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Date Wise</label>
                                <input type="date" name="date" class="form-control" value="<?php echo htmlspecialchars($filters['date']); ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Search</label>
                                <input type="text" name="search" class="form-control" placeholder="Invoice, customer, mobile, GSTIN" value="<?php echo htmlspecialchars($filters['search']); ?>">
                            </div>
                            <div class="col-md-2 d-flex gap-2">
                                <button type="submit" class="btn btn-primary"><i class="fas fa-filter"></i> Filter</button>
                                <a href="invoice_generate.php" class="btn btn-outline-secondary"><i class="fas fa-times"></i></a>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Created Invoices</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table align-middle">
                                <thead>
                                <tr>
                                    <th>Invoice No.</th>
                                    <th>Customer</th>
                                    <th>Invoice Date</th>
                                    <th>Created</th>
                                    <th>Items</th>
                                    <th>Total</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php if (empty($createdInvoices)): ?>
                                    <tr><td colspan="7" class="text-center text-muted py-4">No manual invoices found.</td></tr>
                                <?php endif; ?>
                                <?php foreach ($createdInvoices as $invoice): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($invoice['invoice_no']); ?></td>
                                        <td>
                                            <strong><?php echo htmlspecialchars($invoice['customer_name']); ?></strong><br>
                                            <span class="text-muted"><?php echo htmlspecialchars($invoice['mobile_no']); ?></span>
                                        </td>
                                        <td><?php echo date('d-m-Y', strtotime($invoice['invoice_date'])); ?></td>
                                        <td><?php echo date('d-m-Y', strtotime($invoice['created_at'])); ?></td>
                                        <td><?php echo (int)$invoice['item_count']; ?></td>
                                        <td>Rs. <?php echo number_format((float)$invoice['grand_total'], 2); ?></td>
                                        <td>
                                            <div class="invoice-actions">
                                                <a class="btn btn-sm btn-outline-primary action-btn" href="invoice_generate.php?edit=<?php echo (int)$invoice['id']; ?>" title="Edit Invoice">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a class="btn btn-sm btn-outline-warning action-btn" href="download_manual_invoice.php?id=<?php echo (int)$invoice['id']; ?>" title="Download Invoice">
                                                    <i class="fas fa-file-invoice"></i>
                                                </a>
                                                <form method="POST" onsubmit="return confirm('Delete this invoice?');">
                                                    <input type="hidden" name="invoice_id" value="<?php echo (int)$invoice['id']; ?>">
                                                    <button type="submit" name="delete_invoice" class="btn btn-sm btn-outline-danger action-btn" title="Delete Invoice">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
const invoiceProducts = <?php echo json_encode($productPayload, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT); ?>;
const existingInvoiceItems = <?php echo json_encode($editingItemsPayload, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT); ?>;
const uploadBase = '../';
let invoiceRowIndex = 0;

function money(value) {
    const number = Number(value) || 0;
    return Number.isInteger(number) ? String(number) : number.toFixed(2);
}

function displayNumber(value) {
    const number = Number(value);
    if (!Number.isFinite(number) || number === 0) {
        return '';
    }
    return String(Math.round(number));
}

function displayQuantity(value) {
    const number = Number(value);
    if (!Number.isFinite(number) || number <= 0) {
        return '';
    }
    return String(Math.max(1, Math.floor(number)));
}

function createProductOptions(selectedId) {
    let html = '<option value="">Select product</option>';
    invoiceProducts.forEach(product => {
        html += `<option value="${product.id}" ${Number(selectedId) === product.id ? 'selected' : ''}>${product.name}</option>`;
    });
    return html;
}

function addInvoiceRow(data = {}) {
    const index = invoiceRowIndex++;
    const row = document.createElement('div');
    row.className = 'manual-invoice-product-row';
    row.innerHTML = `
        <div class="manual-invoice-field">
            <label class="form-label">Product</label>
            <select class="form-select product-select" name="items[${index}][product_id]" required>${createProductOptions(data.product_id)}</select>
            <input type="hidden" name="items[${index}][product_name]" class="product-name-field">
            <input type="hidden" name="items[${index}][product_image]" class="product-image-field">
            <div class="invalid-feedback">Select a product.</div>
        </div>
        <div class="manual-invoice-field">
            <label class="form-label">Image</label>
            <div class="manual-invoice-image-box">No image</div>
        </div>
        <div class="manual-invoice-field">
            <label class="form-label">HSN</label>
            <input type="text" inputmode="numeric" name="items[${index}][hsn]" class="form-control hsn-field whole-number-field" pattern="[0-9]*">
        </div>
        <div class="manual-invoice-field">
            <label class="form-label">Unit</label>
            <input type="text" name="items[${index}][unit]" class="form-control unit-field" value="No." required>
            <div class="invalid-feedback">Unit is required.</div>
        </div>
        <div class="manual-invoice-field">
            <label class="form-label">GST %</label>
            <input type="number" step="1" min="0" name="items[${index}][gst_rate]" class="form-control gst-field whole-number-field" placeholder="GST">
        </div>
        <div class="manual-invoice-field">
            <label class="form-label">Item MRP</label>
            <input type="number" step="1" min="0" name="items[${index}][item_mrp]" class="form-control mrp-field whole-number-field" placeholder="MRP">
        </div>
        <div class="manual-invoice-field">
            <label class="form-label">Item Price</label>
            <input type="number" step="1" min="1" name="items[${index}][item_price]" class="form-control price-field whole-number-field" placeholder="Price" required>
            <div class="invalid-feedback">Item price is required.</div>
        </div>
        <div class="manual-invoice-field">
            <label class="form-label">Qty</label>
            <input type="number" step="1" min="1" name="items[${index}][quantity]" class="form-control qty-field" placeholder="Qty" data-locked="1" required>
            <div class="invalid-feedback">Qty is required.</div>
        </div>
        <div class="manual-invoice-field">
            <label class="form-label">&nbsp;</label>
            <button type="button" class="btn btn-outline-danger w-100 remove-row"><i class="fas fa-trash"></i></button>
        </div>
    `;
    document.getElementById('invoiceProductRows').appendChild(row);
    bindInvoiceRow(row);
    hydrateInvoiceRow(row, data);
    updateInvoiceTotal();
}

function hydrateInvoiceRow(row, data = {}) {
    const selectedProduct = invoiceProducts.find(item => item.id === Number(data.product_id));
    const imageBox = row.querySelector('.manual-invoice-image-box');
    const imagePath = data.product_image || selectedProduct?.image || '';

    row.querySelector('.product-name-field').value = data.product_name || selectedProduct?.name || '';
    row.querySelector('.product-image-field').value = imagePath;
    row.querySelector('.hsn-field').value = data.hsn || selectedProduct?.hsn || '';
    row.querySelector('.unit-field').value = data.unit || selectedProduct?.unit || 'No.';
    row.querySelector('.gst-field').value = displayNumber(data.gst_rate ?? selectedProduct?.gst_rate);
    row.querySelector('.mrp-field').value = displayNumber(data.item_mrp ?? selectedProduct?.mrp);
    row.querySelector('.price-field').value = displayNumber(data.item_price ?? selectedProduct?.price);
    const hasSelectedProduct = !!(data.product_id || data.product_name || selectedProduct);
    const qtyField = row.querySelector('.qty-field');
    qtyField.dataset.locked = hasSelectedProduct ? '0' : '1';
    qtyField.value = hasSelectedProduct ? displayQuantity(data.quantity || 1) : '';
    imageBox.innerHTML = imagePath ? `<img src="${uploadBase}${imagePath}" alt="">` : 'No image';
}

function bindInvoiceRow(row) {
    const select = row.querySelector('.product-select');
    select.addEventListener('change', () => {
        const product = invoiceProducts.find(item => item.id === Number(select.value));
        const imageBox = row.querySelector('.manual-invoice-image-box');
        if (!product) {
            row.querySelector('.product-name-field').value = '';
            row.querySelector('.product-image-field').value = '';
            row.querySelector('.hsn-field').value = '';
            row.querySelector('.unit-field').value = 'No.';
            row.querySelector('.gst-field').value = '';
            row.querySelector('.mrp-field').value = '';
            row.querySelector('.price-field').value = '';
            row.querySelector('.qty-field').value = '';
            row.querySelector('.qty-field').dataset.locked = '1';
            imageBox.textContent = 'No image';
            updateInvoiceTotal();
            return;
        }

        row.querySelector('.product-name-field').value = product.name;
        row.querySelector('.product-image-field').value = product.image;
        row.querySelector('.hsn-field').value = product.hsn || '';
        row.querySelector('.unit-field').value = product.unit || 'No.';
        row.querySelector('.gst-field').value = displayNumber(product.gst_rate);
        row.querySelector('.mrp-field').value = displayNumber(product.mrp);
        row.querySelector('.price-field').value = displayNumber(product.price);
        row.querySelector('.qty-field').dataset.locked = '0';
        row.querySelector('.qty-field').value = '1';
        imageBox.innerHTML = product.image ? `<img src="${uploadBase}${product.image}" alt="">` : 'No image';
        updateInvoiceTotal();
    });

    row.querySelectorAll('input').forEach(input => input.addEventListener('input', () => {
        if (input.classList.contains('qty-field') && input.dataset.locked === '1') {
            input.value = '';
            updateInvoiceTotal();
            return;
        }
        if ((input.classList.contains('qty-field') || input.classList.contains('whole-number-field')) && input.value !== '') {
            input.value = input.value.replace(/[^\d]/g, '');
            const minValue = input.classList.contains('gst-field') || input.classList.contains('mrp-field') ? 0 : 1;
            if (Number(input.value) < minValue) {
                input.value = '';
            }
        }
        updateInvoiceTotal();
    }));
    row.querySelector('.qty-field').addEventListener('keydown', event => {
        if (event.currentTarget.dataset.locked === '1') {
            event.preventDefault();
        }
    });
    row.querySelector('.remove-row').addEventListener('click', () => {
        row.remove();
        if (!document.querySelector('.manual-invoice-product-row')) {
            addInvoiceRow();
        }
        updateInvoiceTotal();
    });
}

function updateInvoiceTotal() {
    let total = 0;
    document.querySelectorAll('.manual-invoice-product-row').forEach(row => {
        total += (Number(row.querySelector('.price-field').value) || 0) * (Number(row.querySelector('.qty-field').value) || 0);
    });
    document.getElementById('invoiceGrandTotal').textContent = money(total);
}

document.getElementById('addInvoiceProduct').addEventListener('click', () => addInvoiceRow());

const manualInvoiceForm = document.getElementById('manualInvoiceForm');
manualInvoiceForm.querySelectorAll('.phone-field').forEach(input => {
    input.addEventListener('input', () => {
        input.value = input.value.replace(/[^0-9+\-\s]/g, '');
    });
});

manualInvoiceForm.querySelectorAll('.text-name-field').forEach(input => {
    input.addEventListener('input', () => {
        input.value = input.value.replace(/[0-9]/g, '');
    });
});

manualInvoiceForm.addEventListener('submit', event => {
    if (!manualInvoiceForm.checkValidity()) {
        event.preventDefault();
        event.stopPropagation();
    }
    manualInvoiceForm.classList.add('was-validated');
});

if (existingInvoiceItems.length) {
    existingInvoiceItems.forEach(item => addInvoiceRow(item));
} else {
    addInvoiceRow();
}
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/admin.js"></script>
</body>
</html>
