<?php

/** Create the category ordering table used by the Top Deals homepage section. */
function ensureTopDealCategoryOrderSchema(PDO $pdo): bool
{
    static $ready = false;
    if ($ready) {
        return true;
    }

    try {
        if (!ensureProductCategoryAssignmentsSchema($pdo) || !ensureCategoryParentAssignmentsSchema($pdo)) {
            throw new RuntimeException('Product or category assignments are not available.');
        }
        $tableCheck = $pdo->query("SELECT COUNT(*) FROM information_schema.TABLES
            WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'top_deal_category_order'");
        $tableExisted = (int)$tableCheck->fetchColumn() > 0;
        $pdo->exec("CREATE TABLE IF NOT EXISTS top_deal_category_order (
            category_id INT NOT NULL PRIMARY KEY,
            sort_order INT NOT NULL DEFAULT 0,
            is_visible TINYINT(1) NOT NULL DEFAULT 0,
            updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            CONSTRAINT fk_top_deal_category_order_category
                FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE,
            INDEX idx_top_deal_category_order_sort (sort_order)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        $columnCheck = $pdo->query("SELECT COUNT(*) FROM information_schema.COLUMNS
            WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'top_deal_category_order' AND COLUMN_NAME = 'is_visible'");
        $visibilityColumnExisted = (int)$columnCheck->fetchColumn() > 0;
        if (!$visibilityColumnExisted) {
            $pdo->exec('ALTER TABLE top_deal_category_order ADD COLUMN is_visible TINYINT(1) NOT NULL DEFAULT 1 AFTER sort_order');
        }
        if (!$tableExisted || !$visibilityColumnExisted) {
            // Keep categories that were already part of the old Featured/Top Deals
            // homepage section visible through this one-time migration.
            $pdo->exec("INSERT IGNORE INTO top_deal_category_order (category_id, sort_order, is_visible)
                SELECT DISTINCT c.id, 0, 1
                FROM categories c
                LEFT JOIN category_parent_assignments cpa ON cpa.category_id = c.id
                WHERE (c.parent_id IS NOT NULL OR cpa.parent_id IS NOT NULL)
                  AND EXISTS (
                    SELECT 1 FROM products p
                    WHERE p.is_featured = 1 AND p.is_active = 1
                      AND (p.category_id = c.id OR EXISTS (
                        SELECT 1 FROM product_category_assignments pca
                        WHERE pca.product_id = p.id AND pca.category_id = c.id
                      ))
                  )");
        }
        $ready = true;
        return true;
    } catch (Throwable $e) {
        error_log('Top Deals category order schema setup failed: ' . $e->getMessage());
        return false;
    }
}

/** Return subcategories which contain active products marked for Top Deals. */
function getTopDealCategories(PDO $pdo, bool $visibleOnly = true): array
{
    if (!ensureProductCategoryAssignmentsSchema($pdo)
        || !ensureCategoryParentAssignmentsSchema($pdo)
        || !ensureTopDealCategoryOrderSchema($pdo)) {
        return [];
    }

    $visibilityCondition = $visibleOnly ? ' AND COALESCE(tdo.is_visible, 0) = 1' : '';
    $productCountCondition = $visibleOnly ? ' HAVING COUNT(DISTINCT p.id) > 0' : '';
    $sql = "SELECT c.id, c.name, c.slug, c.image,
                   COUNT(DISTINCT p.id) AS product_count,
                   COALESCE(tdo.sort_order, 2147483647) AS deal_sort_order,
                   COALESCE(tdo.is_visible, 0) AS is_visible
            FROM categories c
            LEFT JOIN products p ON p.is_featured = 1 AND p.is_active = 1
                AND (p.category_id = c.id OR EXISTS (
                    SELECT 1 FROM product_category_assignments pca
                    WHERE pca.product_id = p.id AND pca.category_id = c.id
                ))
            LEFT JOIN category_parent_assignments cpa ON cpa.category_id = c.id
            LEFT JOIN top_deal_category_order tdo ON tdo.category_id = c.id
            WHERE (c.parent_id IS NOT NULL OR cpa.parent_id IS NOT NULL){$visibilityCondition}
            GROUP BY c.id, c.name, c.slug, c.image, tdo.sort_order, tdo.is_visible
            {$productCountCondition}
            ORDER BY is_visible DESC, deal_sort_order ASC, c.name ASC";
    try {
        return $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    } catch (Throwable $e) {
        error_log('Unable to load Top Deals categories: ' . $e->getMessage());
        return [];
    }
}
