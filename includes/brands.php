<?php
// Read-only schema detection keeps storefronts usable before deployment migration.
function brandsSchemaReady($pdo) {
    static $ready = null;
    if ($ready !== null) return $ready;
    try {
        $pdo->query('SELECT brand_id FROM products LIMIT 0');
        $pdo->query('SELECT id, name, image FROM brands LIMIT 0');
        return $ready = true;
    } catch (PDOException $e) {
        return $ready = false;
    }
}

function getBrands($pdo) {
    return brandsSchemaReady($pdo)
        ? $pdo->query('SELECT id, name, image FROM brands ORDER BY name')->fetchAll(PDO::FETCH_ASSOC)
        : [];
}

function selectedBrandIds($value) {
    $ids = [];
    foreach (is_array($value) ? $value : [$value] as $id) {
        if (is_scalar($id) && filter_var($id, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]) !== false) {
            $ids[] = (int)$id;
        }
    }
    return array_values(array_unique($ids));
}

function appendBrandFilter($pdo, &$conditions, &$params, $value) {
    $ids = selectedBrandIds($value);
    if (!$ids) return;
    if (!brandsSchemaReady($pdo)) {
        $conditions[] = '1 = 0';
        return;
    }
    $conditions[] = 'p.brand_id IN (' . implode(',', array_fill(0, count($ids), '?')) . ')';
    $params = array_merge($params, $ids);
}

function validProductBrand($brands, $value) {
    if ($value === '' || $value === null) return true;
    $ids = selectedBrandIds($value);
    return !is_array($value) && count($ids) === 1 && in_array($ids[0], array_map('intval', array_column($brands, 'id')), true);
}

function saveProductBrand($pdo, $productId, $value) {
    if (!brandsSchemaReady($pdo)) return;
    $stmt = $pdo->prepare('UPDATE products SET brand_id = ? WHERE id = ?');
    $stmt->execute([$value === '' || $value === null ? null : (int)$value, $productId]);
}
