<?php

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('This migration must be run from the command line.');
}

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/footer_pages_migration.php';

try {
    migrateFooterPagesSchema($pdo);
    echo "Footer pages migration completed successfully.\n";
} catch (Throwable $e) {
    fwrite(STDERR, 'Footer pages migration failed: ' . $e->getMessage() . "\n");
    exit(1);
}
