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
            offer_text VARCHAR(100) NOT NULL DEFAULT '30-20% OFF',
            offer_override VARCHAR(100) NOT NULL DEFAULT '',
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
        $offerColumnCheck = $pdo->query("SELECT COUNT(*) FROM information_schema.COLUMNS
            WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'top_deal_category_order' AND COLUMN_NAME = 'offer_text'");
        if (!(int)$offerColumnCheck->fetchColumn()) {
            $pdo->exec("ALTER TABLE top_deal_category_order ADD COLUMN offer_text VARCHAR(100) NOT NULL DEFAULT '30-20% OFF' AFTER is_visible");
        }
        $overrideColumnCheck = $pdo->query("SELECT COUNT(*) FROM information_schema.COLUMNS
            WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'top_deal_category_order' AND COLUMN_NAME = 'offer_override'");
        if (!(int)$overrideColumnCheck->fetchColumn()) {
            $pdo->exec("ALTER TABLE top_deal_category_order ADD COLUMN offer_override VARCHAR(100) NOT NULL DEFAULT '' AFTER offer_text");
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
                   MIN(CASE WHEN p.mrp > p.selling_price AND p.mrp > 0
                       THEN ROUND(((p.mrp - p.selling_price) / p.mrp) * 100) END) AS discount_min,
                   MAX(CASE WHEN p.mrp > p.selling_price AND p.mrp > 0
                       THEN ROUND(((p.mrp - p.selling_price) / p.mrp) * 100) END) AS discount_max,
                   COALESCE(tdo.sort_order, 2147483647) AS deal_sort_order,
                   COALESCE(tdo.is_visible, 0) AS is_visible,
                   COALESCE(NULLIF(tdo.offer_text, ''), '30-20% OFF') AS offer_text,
                   COALESCE(tdo.offer_override, '') AS offer_override
            FROM categories c
            LEFT JOIN products p ON p.is_featured = 1 AND p.is_active = 1
                AND (p.category_id = c.id OR EXISTS (
                    SELECT 1 FROM product_category_assignments pca
                    WHERE pca.product_id = p.id AND pca.category_id = c.id
                ))
            LEFT JOIN category_parent_assignments cpa ON cpa.category_id = c.id
            LEFT JOIN top_deal_category_order tdo ON tdo.category_id = c.id
            WHERE (c.parent_id IS NOT NULL OR cpa.parent_id IS NOT NULL){$visibilityCondition}
            GROUP BY c.id, c.name, c.slug, c.image, tdo.sort_order, tdo.is_visible, tdo.offer_text, tdo.offer_override
            {$productCountCondition}
            ORDER BY is_visible DESC, deal_sort_order ASC, c.name ASC";
    try {
        $categories = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        foreach ($categories as &$category) {
            $minimumDiscount = (int)($category['discount_min'] ?? 0);
            $maximumDiscount = (int)($category['discount_max'] ?? 0);
            if ($maximumDiscount <= 0) {
                $category['discount_label'] = '0% OFF';
            } elseif ($minimumDiscount === $maximumDiscount) {
                $category['discount_label'] = $maximumDiscount . '% OFF';
            } else {
                $category['discount_label'] = $minimumDiscount . '-' . $maximumDiscount . '% OFF';
            }
        }
        unset($category);
        return $categories;
    } catch (Throwable $e) {
        error_log('Unable to load Top Deals categories: ' . $e->getMessage());
        return [];
    }
}

/** Return the active Top Deals products grouped by subcategory for the admin picker. */
function getTopDealProductsByCategory(PDO $pdo, array $categoryIds): array
{
    $categoryIds = array_values(array_unique(array_filter(array_map('intval', $categoryIds))));
    if (!$categoryIds || !ensureProductCategoryAssignmentsSchema($pdo)) {
        return [];
    }

    $placeholders = implode(',', array_fill(0, count($categoryIds), '?'));
    $sql = "SELECT c.id AS category_id, p.id, p.name, p.main_image
            FROM categories c
            INNER JOIN products p ON p.is_featured = 1 AND p.is_active = 1
                AND (p.category_id = c.id OR EXISTS (
                    SELECT 1 FROM product_category_assignments pca
                    WHERE pca.product_id = p.id AND pca.category_id = c.id
                ))
            WHERE c.id IN ({$placeholders})
            ORDER BY c.name ASC, p.name ASC";
    try {
        $statement = $pdo->prepare($sql);
        $statement->execute($categoryIds);
        $productsByCategory = array_fill_keys($categoryIds, []);
        foreach ($statement->fetchAll(PDO::FETCH_ASSOC) as $product) {
            $productsByCategory[(int)$product['category_id']][] = $product;
        }
        return $productsByCategory;
    } catch (Throwable $e) {
        error_log('Unable to load Top Deals products: ' . $e->getMessage());
        return [];
    }
}
