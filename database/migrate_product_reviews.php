<?php
if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('This migration must be run from the command line.');
}

$direction = strtolower($argv[1] ?? 'up');
if (!in_array($direction, ['up', 'down'], true)) {
    fwrite(STDERR, "Usage: php database/migrate_product_reviews.php [up|down] [--production]\n");
    exit(2);
}

// CLI defaults to localhost in database.php. Production runs must be explicit.
if (in_array('--production', array_slice($argv, 2), true)) {
    $_SERVER['HTTP_HOST'] = 'production';
}

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/product_reviews_migration.php';

try {
    if ($direction === 'up') {
        migrateProductReviewsUp($pdo);
        echo "Product reviews migration completed.\n";
    } else {
        migrateProductReviewsDown($pdo);
        echo "Product reviews migration rolled back.\n";
    }
} catch (Throwable $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    fwrite(STDERR, 'Product reviews migration failed: ' . $e->getMessage() . "\n");
    exit(1);
}
