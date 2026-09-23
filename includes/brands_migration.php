<?php
// Shared by the authenticated Brands page and the optional CLI migration.
function migrateBrandsSchema(PDO $pdo) {
    $lockName = 'brands_migration_' . substr(hash('sha256', (string)$pdo->query('SELECT DATABASE()')->fetchColumn()), 0, 32);
    $lock = $pdo->prepare('SELECT GET_LOCK(?, 10)');
    $lock->execute([$lockName]);
    if ((int)$lock->fetchColumn() !== 1) {
        throw new RuntimeException('Brand setup is busy. Please try again.');
    }
    try {
        $pdo->exec("CREATE TABLE IF NOT EXISTS brands (
            id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(150) NOT NULL,
            image VARCHAR(255) NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY uq_brand_name (name)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        if (!$pdo->query("SHOW COLUMNS FROM products LIKE 'brand_id'")->fetch()) {
            $pdo->exec('ALTER TABLE products ADD COLUMN brand_id INT NULL DEFAULT NULL');
        }
        if (!$pdo->query("SHOW INDEX FROM products WHERE Key_name = 'idx_products_brand'")->fetch()) {
            $pdo->exec('ALTER TABLE products ADD INDEX idx_products_brand (brand_id)');
        }
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS WHERE CONSTRAINT_SCHEMA = DATABASE() AND TABLE_NAME = 'products' AND CONSTRAINT_NAME = 'fk_products_brand'");
        $stmt->execute();
        if (!$stmt->fetchColumn()) {
            $pdo->exec('ALTER TABLE products ADD CONSTRAINT fk_products_brand FOREIGN KEY (brand_id) REFERENCES brands(id) ON DELETE SET NULL');
        }
        } finally {
        $release = $pdo->prepare('SELECT RELEASE_LOCK(?)');
        $release->execute([$lockName]);
    }
}