<?php
require_once __DIR__ . '/functions.php';

function ensureManualInvoiceSchema(PDO $pdo) {
    try {
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS manual_invoices (
                id INT PRIMARY KEY AUTO_INCREMENT,
                invoice_no VARCHAR(60) UNIQUE NOT NULL,
                invoice_date DATE NOT NULL,
                customer_name VARCHAR(150) NOT NULL,
                mobile_no VARCHAR(30),
                eway_bill_no VARCHAR(80),
                eway_bill_date DATE NULL,
                buyer_po_no VARCHAR(80),
                buyer_po_date DATE NULL,
                payment_terms VARCHAR(100) DEFAULT 'Manual',
                payment_date DATE NULL,
                transporter VARCHAR(150),
                lr_no VARCHAR(80),
                bill_to_name VARCHAR(150),
                bill_to_gstin VARCHAR(30),
                bill_to_mobile VARCHAR(30),
                bill_to_address TEXT,
                ship_to_name VARCHAR(150),
                ship_to_gstin VARCHAR(30),
                ship_to_mobile VARCHAR(30),
                ship_to_address TEXT,
                taxable_total DECIMAL(12,2) NOT NULL DEFAULT 0,
                cgst_total DECIMAL(12,2) NOT NULL DEFAULT 0,
                sgst_total DECIMAL(12,2) NOT NULL DEFAULT 0,
                grand_total DECIMAL(12,2) NOT NULL DEFAULT 0,
                stock_deducted TINYINT(1) NOT NULL DEFAULT 0,
                created_by INT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");

        $stmt = $pdo->query("SHOW COLUMNS FROM manual_invoices LIKE 'stock_deducted'");
        if (!$stmt->fetch(PDO::FETCH_ASSOC)) {
            $pdo->exec("ALTER TABLE manual_invoices ADD COLUMN stock_deducted TINYINT(1) NOT NULL DEFAULT 0 AFTER grand_total");
        }

        $pdo->exec("
            CREATE TABLE IF NOT EXISTS manual_invoice_items (
                id INT PRIMARY KEY AUTO_INCREMENT,
                invoice_id INT NOT NULL,
                product_id INT NULL,
                product_name VARCHAR(255) NOT NULL,
                product_image VARCHAR(255),
                hsn VARCHAR(40),
                unit VARCHAR(30) DEFAULT 'No.',
                gst_rate DECIMAL(5,2) NOT NULL DEFAULT 0,
                item_mrp DECIMAL(12,2) NOT NULL DEFAULT 0,
                item_price DECIMAL(12,2) NOT NULL DEFAULT 0,
                quantity DECIMAL(10,2) NOT NULL DEFAULT 1,
                taxable_value DECIMAL(12,2) NOT NULL DEFAULT 0,
                cgst_amount DECIMAL(12,2) NOT NULL DEFAULT 0,
                sgst_amount DECIMAL(12,2) NOT NULL DEFAULT 0,
                total_price DECIMAL(12,2) NOT NULL DEFAULT 0,
                sort_order INT NOT NULL DEFAULT 0,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_manual_invoice_items_invoice_id (invoice_id),
                CONSTRAINT fk_manual_invoice_items_invoice
                    FOREIGN KEY (invoice_id) REFERENCES manual_invoices(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");

        ensureManualInvoicePermission($pdo);
    } catch (Exception $e) {
        error_log('Manual invoice schema migration failed: ' . $e->getMessage());
    }
}

function ensureManualInvoicePermission(PDO $pdo) {
    try {
        $stmt = $pdo->query("SHOW TABLES LIKE 'permissions'");
        if (!$stmt->fetchColumn()) {
            return;
        }

        $stmt = $pdo->prepare("
            INSERT IGNORE INTO permissions (code, name, category, description)
            VALUES ('manage_manual_invoices', 'Manage Manual Invoices', 'Orders', 'Can create and download manual tax invoices')
        ");
        $stmt->execute();

        $stmt = $pdo->query("SHOW TABLES LIKE 'roles'");
        if (!$stmt->fetchColumn()) {
            return;
        }

        $stmt = $pdo->query("SHOW TABLES LIKE 'role_permissions'");
        if (!$stmt->fetchColumn()) {
            return;
        }

        $pdo->exec("
            INSERT IGNORE INTO role_permissions (role_id, permission_id)
            SELECT r.id, p.id
            FROM roles r
            JOIN permissions p ON p.code = 'manage_manual_invoices'
            WHERE r.name IN ('Super Admin', 'Admin')
        ");

        if (isset($_SESSION['admin_permissions']) && is_array($_SESSION['admin_permissions']) && !in_array('manage_manual_invoices', $_SESSION['admin_permissions'], true)) {
            $_SESSION['admin_permissions'][] = 'manage_manual_invoices';
        }
    } catch (Exception $e) {
        error_log('Manual invoice permission migration failed: ' . $e->getMessage());
    }
}

function generateManualInvoiceNumber(PDO $pdo) {
    $prefix = 'B2C-' . date('Ym') . '-';
    $stmt = $pdo->prepare("SELECT invoice_no FROM manual_invoices WHERE invoice_no LIKE ? ORDER BY id DESC LIMIT 1");
    $stmt->execute([$prefix . '%']);
    $last = $stmt->fetchColumn();
    $next = 1;

    if ($last && preg_match('/-(\d+)$/', $last, $matches)) {
        $next = (int)$matches[1] + 1;
    }

    return $prefix . str_pad((string)$next, 4, '0', STR_PAD_LEFT);
}

function getManualInvoiceProducts(PDO $pdo) {
    ensureProductPackageQuantitySchema($pdo);
    ensureProductUnitSchema($pdo);

    $stmt = $pdo->query("
        SELECT id, name, hsn, main_image, mrp, selling_price, gst_rate, unit_label
        FROM products
        WHERE is_active = 1
        ORDER BY name ASC
    ");

    return applyDisplayVariationPrices($stmt->fetchAll(PDO::FETCH_ASSOC));
}

function getManualInvoices(PDO $pdo, array $filters = []) {
    $where = [];
    $params = [];

    if (!empty($filters['month'])) {
        $where[] = "DATE_FORMAT(invoice_date, '%Y-%m') = ?";
        $params[] = $filters['month'];
    }

    if (!empty($filters['date'])) {
        $where[] = "invoice_date = ?";
        $params[] = $filters['date'];
    }

    if (!empty($filters['search'])) {
        $where[] = "(invoice_no LIKE ? OR customer_name LIKE ? OR mobile_no LIKE ? OR bill_to_gstin LIKE ? OR ship_to_gstin LIKE ?)";
        $search = '%' . $filters['search'] . '%';
        array_push($params, $search, $search, $search, $search, $search);
    }

    $whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';
    $stmt = $pdo->prepare("
        SELECT mi.*, COUNT(mii.id) AS item_count
        FROM manual_invoices mi
        LEFT JOIN manual_invoice_items mii ON mi.id = mii.invoice_id
        $whereSql
        GROUP BY mi.id
        ORDER BY mi.created_at DESC, mi.id DESC
        LIMIT 100
    ");
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getManualInvoiceById(PDO $pdo, $invoiceId) {
    $stmt = $pdo->prepare("SELECT * FROM manual_invoices WHERE id = ?");
    $stmt->execute([(int)$invoiceId]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function getManualInvoiceItems(PDO $pdo, $invoiceId) {
    $stmt = $pdo->prepare("SELECT * FROM manual_invoice_items WHERE invoice_id = ? ORDER BY sort_order ASC, id ASC");
    $stmt->execute([(int)$invoiceId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function deleteManualInvoice(PDO $pdo, $invoiceId) {
    $stmt = $pdo->prepare("DELETE FROM manual_invoices WHERE id = ?");
    return $stmt->execute([(int)$invoiceId]);
}

function calculateManualInvoiceItem(array $item) {
    $qty = max(0, (float)($item['quantity'] ?? 0));
    $price = max(0, (float)($item['item_price'] ?? 0));
    $gstRate = max(0, (float)($item['gst_rate'] ?? 0));
    $total = round($price * $qty, 2);
    $taxable = $gstRate > 0 ? round($total / (1 + ($gstRate / 100)), 2) : $total;
    $gst = round($total - $taxable, 2);

    $item['quantity'] = $qty;
    $item['item_price'] = $price;
    $item['gst_rate'] = $gstRate;
    $item['taxable_value'] = $taxable;
    $item['cgst_amount'] = round($gst / 2, 2);
    $item['sgst_amount'] = round($gst / 2, 2);
    $item['total_price'] = $total;

    return $item;
}

function manualInvoiceAmountWords($number) {
    $number = (int)round((float)$number);
    if ($number === 0) {
        return 'Zero';
    }

    $ones = ['', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine', 'Ten', 'Eleven', 'Twelve', 'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen', 'Seventeen', 'Eighteen', 'Nineteen'];
    $tens = ['', '', 'Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety'];

    $underHundred = function ($n) use ($ones, $tens) {
        if ($n < 20) {
            return $ones[$n];
        }
        return trim($tens[(int)($n / 10)] . ' ' . $ones[$n % 10]);
    };

    $underThousand = function ($n) use ($underHundred, $ones) {
        $text = '';
        if ($n >= 100) {
            $text .= $ones[(int)($n / 100)] . ' Hundred ';
            $n %= 100;
        }
        if ($n > 0) {
            $text .= $underHundred($n);
        }
        return trim($text);
    };

    $parts = [];
    $crore = (int)($number / 10000000);
    $number %= 10000000;
    $lakh = (int)($number / 100000);
    $number %= 100000;
    $thousand = (int)($number / 1000);
    $number %= 1000;

    if ($crore) {
        $parts[] = $underThousand($crore) . ' Crore';
    }
    if ($lakh) {
        $parts[] = $underThousand($lakh) . ' Lakh';
    }
    if ($thousand) {
        $parts[] = $underThousand($thousand) . ' Thousand';
    }
    if ($number) {
        $parts[] = $underThousand($number);
    }

    return trim(implode(' ', $parts));
}

function manualInvoiceImageDataUri($relativePath) {
    $relativePath = trim((string)$relativePath);
    if ($relativePath === '') {
        return '';
    }

    $path = realpath(__DIR__ . '/../' . ltrim($relativePath, '/\\'));
    if (!$path || !is_file($path)) {
        return '';
    }

    $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));

    if ($ext === 'webp') {
        if (!function_exists('imagecreatefromwebp') || !function_exists('imagepng')) {
            return '';
        }

        $image = @imagecreatefromwebp($path);
        if (!$image) {
            return '';
        }

        ob_start();
        imagepng($image);
        imagedestroy($image);
        $pngData = ob_get_clean();

        return $pngData ? 'data:image/png;base64,' . base64_encode($pngData) : '';
    }

    $mime = 'image/jpeg';
    if ($ext === 'png') {
        $mime = 'image/png';
    } elseif ($ext === 'gif') {
        $mime = 'image/gif';
    }

    return 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($path));
}
