<?php
if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('This migration must be run from the command line.');
}

require_once __DIR__ . '/../config/database.php';

$direction = strtolower($argv[1] ?? 'up');
if (!in_array($direction, ['up', 'down'], true)) {
    fwrite(STDERR, "Usage: php database/migrate_product_reviews.php [up|down]\n");
    exit(2);
}

try {
    if ($direction === 'up') {
        $pdo->exec("CREATE TABLE IF NOT EXISTS product_reviews (
            id INT UNSIGNED NOT NULL AUTO_INCREMENT,
            product_id INT NOT NULL,
            customer_id INT NULL,
            seller_code VARCHAR(40) NULL,
            rating TINYINT UNSIGNED NOT NULL,
            review_title VARCHAR(200) NOT NULL,
            review_content TEXT NOT NULL,
            is_verified TINYINT(1) NOT NULL DEFAULT 0,
            status ENUM('pending', 'approved', 'rejected', 'spam') NOT NULL DEFAULT 'pending',
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            approved_at DATETIME NULL DEFAULT NULL,
            PRIMARY KEY (id),
            KEY idx_product_reviews_product_status_date (product_id, status, created_at),
            KEY idx_product_reviews_status_date (status, created_at),
            KEY idx_product_reviews_customer (customer_id),
            KEY idx_product_reviews_seller_code (seller_code),
            KEY idx_product_reviews_rating (rating),
            CONSTRAINT fk_product_reviews_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
            CONSTRAINT fk_product_reviews_customer FOREIGN KEY (customer_id) REFERENCES users(id) ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

        $pdo->beginTransaction();
        $pdo->exec("INSERT IGNORE INTO permissions (code, name, category, description)
            VALUES ('manage_reviews', 'Manage Reviews', 'Reviews', 'Can view, edit, moderate, and delete product reviews')");
        $pdo->exec("INSERT IGNORE INTO role_permissions (role_id, permission_id)
            SELECT r.id, p.id FROM roles r CROSS JOIN permissions p
            WHERE r.name IN ('Super Admin', 'Admin') AND p.code = 'manage_reviews'");
        $pdo->commit();
        echo "Product reviews migration completed.\n";
        exit(0);
    }

    $pdo->beginTransaction();
    $pdo->exec("DELETE rp FROM role_permissions rp INNER JOIN permissions p ON p.id = rp.permission_id WHERE p.code = 'manage_reviews'");
    $pdo->exec("DELETE FROM permissions WHERE code = 'manage_reviews'");
    $pdo->commit();
    $pdo->exec('DROP TABLE IF EXISTS product_reviews');
    echo "Product reviews migration rolled back.\n";
} catch (Throwable $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    fwrite(STDERR, 'Product reviews migration failed: ' . $e->getMessage() . "\n");
    exit(1);
}

