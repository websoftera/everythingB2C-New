<?php

/** Add seller display fields to existing products tables after deployment. */
function ensureProductSellerFieldsSchema(PDO $pdo): bool {
    try {
        foreach ([
            'seller_name' => 'VARCHAR(255) NULL',
            'seller_code' => 'VARCHAR(40) NULL',
        ] as $column => $definition) {
            $stmt = $pdo->prepare("SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'products' AND COLUMN_NAME = ? LIMIT 1");
            $stmt->execute([$column]);
            if ($stmt->fetchColumn()) {
                continue;
            }

            $pdo->exec("ALTER TABLE products ADD COLUMN `$column` $definition");
        }
        return true;
    } catch (Throwable $e) {
        // Concurrent first requests may race while adding the same column.
        $driverCode = $e instanceof PDOException ? ($e->errorInfo[1] ?? null) : null;
        if ($e->getCode() === '42S21' || (int)$driverCode === 1060) {
            try {
                $stmt = $pdo->query("SELECT COLUMN_NAME FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'products'");
                $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
                if (in_array('seller_name', $columns, true) && in_array('seller_code', $columns, true)) {
                    return true;
                }
            } catch (Throwable $checkError) {
            }
        }
        error_log('Automatic product seller fields migration failed: ' . $e->getMessage());
        return false;
    }
}

function normalizeProductSellerFields(array $input): array {
    $sellerName = trim(strip_tags((string)($input['seller_name'] ?? '')));
    $sellerCode = trim(strip_tags((string)($input['seller_code'] ?? '')));

    return [
        'seller_name' => $sellerName !== '' ? $sellerName : null,
        'seller_code' => $sellerCode !== '' ? $sellerCode : null,
    ];
}

function productSellerFieldsValidationError(array $sellerFields): string {
    if (mb_strlen((string)($sellerFields['seller_name'] ?? '')) > 255) {
        return 'Seller Name must be 255 characters or fewer.';
    }
    $sellerCode = (string)($sellerFields['seller_code'] ?? '');
    if ($sellerCode !== '' && !preg_match('/^[A-Za-z0-9][A-Za-z0-9_-]{0,39}$/', $sellerCode)) {
        return 'Seller Code can contain letters, numbers, hyphens, and underscores (up to 40 characters).';
    }
    return '';
}

function renderProductSellerLine(array $product): string {
    $name = trim((string)($product['seller_name'] ?? ''));
    $code = trim((string)($product['seller_code'] ?? ''));
    if ($name === '' && $code === '') {
        return '';
    }

    $parts = [];
    if ($name !== '') {
        $parts[] = '<span class="product-seller-name">Seller: ' . htmlspecialchars($name, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '</span>';
    }
    if ($code !== '') {
        $parts[] = '<span class="product-seller-code">Code: ' . htmlspecialchars($code, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '</span>';
    }
    return '<div class="product-seller-line">' . implode('<span class="product-seller-separator" aria-hidden="true"> · </span>', $parts) . '</div>';
}
