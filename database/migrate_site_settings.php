<?php
require_once __DIR__ . '/../config/database.php';

try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS site_settings (
        id INT AUTO_INCREMENT PRIMARY KEY,
        setting_key VARCHAR(100) NOT NULL UNIQUE,
        setting_value TEXT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )");

    $stmt = $pdo->prepare("
        INSERT INTO site_settings (setting_key, setting_value)
        VALUES ('google_search_visibility', 'visible')
        ON DUPLICATE KEY UPDATE setting_value = setting_value
    ");
    $stmt->execute();

    echo "site_settings migration completed.\n";
} catch (Exception $e) {
    fwrite(STDERR, "site_settings migration failed: " . $e->getMessage() . "\n");
    exit(1);
}
