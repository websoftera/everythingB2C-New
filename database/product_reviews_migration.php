<?php

function migrateProductReviewsUp(PDO $pdo): void {
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
    try {
        $pdo->exec("INSERT IGNORE INTO permissions (code, name, category, description)
            VALUES ('manage_reviews', 'Manage Reviews', 'Reviews', 'Can view, edit, moderate, and delete product reviews')");
        $pdo->exec("INSERT IGNORE INTO role_permissions (role_id, permission_id)
            SELECT r.id, p.id FROM roles r CROSS JOIN permissions p
            WHERE r.name IN ('Super Admin', 'Admin') AND p.code = 'manage_reviews'");
        $pdo->commit();
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        throw $e;
    }
}

function migrateProductReviewsDown(PDO $pdo): void {
    $pdo->beginTransaction();
    try {
        $pdo->exec("DELETE rp FROM role_permissions rp INNER JOIN permissions p ON p.id = rp.permission_id WHERE p.code = 'manage_reviews'");
        $pdo->exec("DELETE FROM permissions WHERE code = 'manage_reviews'");
        $pdo->commit();
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        throw $e;
    }
    $pdo->exec('DROP TABLE IF EXISTS product_reviews');
}
