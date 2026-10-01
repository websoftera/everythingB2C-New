<?php
if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('This migration is applied automatically by the application after deployment.');
}

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/seller_application.php';

if (!ensureSellerApplicationsSchema($pdo)) {
    fwrite(STDERR, "Seller applications migration failed. Check database permissions and the PHP error log.\n");
    exit(1);
}

echo "Seller applications migration completed.\n";
