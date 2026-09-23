<?php
// Run from the command line using the same DB_* environment settings as the site.
if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('This migration must be run from the command line.');
}
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/brands_migration.php';
try {
    migrateBrandsSchema($pdo);
    echo "Brands migration completed successfully.\n";
} catch (Throwable $e) {
    fwrite(STDERR, 'Brands migration failed: ' . $e->getMessage() . "\n");
    exit(1);
}
