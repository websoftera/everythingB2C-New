<?php

/**
 * Database migration for the admin-managed footer pages.
 *
 * This is intentionally idempotent: it is safe to run during deployment and
 * when an older installation first opens the footer-page manager.
 */
function migrateFooterPagesSchema(PDO $pdo): void
{
    $pdo->exec("CREATE TABLE IF NOT EXISTS footer_pages (
        id INT UNSIGNED NOT NULL AUTO_INCREMENT,
        title VARCHAR(150) NOT NULL,
        slug VARCHAR(160) NOT NULL,
        content MEDIUMTEXT NULL,
        legacy_path VARCHAR(100) NULL,
        sort_order INT UNSIGNED NOT NULL DEFAULT 0,
        is_active TINYINT(1) NOT NULL DEFAULT 1,
        created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        UNIQUE KEY uq_footer_pages_slug (slug),
        UNIQUE KEY uq_footer_pages_legacy_path (legacy_path),
        KEY idx_footer_pages_active_order (is_active, sort_order, id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    $pdo->beginTransaction();
    try {
        $pdo->exec("INSERT IGNORE INTO permissions (code, name, category, description)
            VALUES ('manage_footer_pages', 'Manage Footer Pages', 'Website', 'Can create, edit, publish, and delete footer pages')");
        $pdo->exec("INSERT IGNORE INTO role_permissions (role_id, permission_id)
            SELECT r.id, p.id
            FROM roles r
            INNER JOIN permissions p ON p.code = 'manage_footer_pages'
            WHERE r.name IN ('Super Admin', 'Admin')");

        $seed = $pdo->prepare("INSERT IGNORE INTO footer_pages
            (title, slug, content, legacy_path, sort_order, is_active)
            VALUES (?, ?, NULL, ?, ?, 1)");
        foreach ([
            ['About Us', 'about-us', 'about.php', 10],
            ['Returns & Refunds', 'returns-refunds', 'returns.php', 20],
            ['Privacy Policy', 'privacy-policy', 'privacy.php', 30],
            ['FAQ', 'faq', 'faq.php', 40],
        ] as $page) {
            $seed->execute($page);
        }

        $pdo->commit();
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        throw $e;
    }
}
