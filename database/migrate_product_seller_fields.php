<?php
if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('This migration is applied automatically by the application after deployment.');
}

if (in_array('--production', array_slice($argv, 1), true)) {
    $_SERVER['HTTP_HOST'] = 'production';
}

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/product_seller_fields.php';

if (!ensureProductSellerFieldsSchema($pdo)) {
    fwrite(STDERR, "Product seller fields migration failed. Check the database permissions and PHP error log.\n");
    exit(1);
}

echo "Product seller fields migration completed.\n";
