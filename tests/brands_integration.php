<?php
if (PHP_SAPI !== 'cli') { http_response_code(403); exit; }
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/brands.php';
function checkBrandTest($condition, $message) {
    if (!$condition) throw new RuntimeException($message);
}
try {
    checkBrandTest(brandsSchemaReady($pdo), 'Run the migration first.');
    $pdo->beginTransaction();
    $name = 'Brand test ' . bin2hex(random_bytes(8));
    $stmt = $pdo->prepare('INSERT INTO brands (name) VALUES (?)');
    $stmt->execute([$name]);
    $brandId = (int)$pdo->lastInsertId();
    $stmt->execute([$name . ' second']);
    $secondId = (int)$pdo->lastInsertId();
    $brands = getBrands($pdo);
    checkBrandTest(validProductBrand($brands, (string)$brandId), 'Valid brand rejected.');
    checkBrandTest(validProductBrand($brands, ''), 'No-brand option rejected.');
    checkBrandTest(!validProductBrand($brands, '999999999'), 'Unknown brand accepted.');
    checkBrandTest(!validProductBrand($brands, [$brandId]), 'Array accepted for single product brand.');
    checkBrandTest(selectedBrandIds(['1', '1', '-1', 'bad', ['2']]) === [1], 'Unsafe filter values accepted.');
    $productId = $pdo->query('SELECT id FROM products ORDER BY id LIMIT 1')->fetchColumn();
    checkBrandTest((bool)$productId, 'Need an existing product for integration verification.');
    saveProductBrand($pdo, $productId, $brandId);
    $where = ['p.id = ?'];
    $params = [$productId];
    appendBrandFilter($pdo, $where, $params, [$brandId, $secondId]);
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM products p WHERE ' . implode(' AND ', $where));
    $stmt->execute($params);
    checkBrandTest((int)$stmt->fetchColumn() === 1, 'Multi-brand filter did not match assigned product.');
    $where = ['p.id = ?']; $params = [$productId];
    appendBrandFilter($pdo, $where, $params, $secondId);
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM products p WHERE ' . implode(' AND ', $where));
    $stmt->execute($params);
    checkBrandTest((int)$stmt->fetchColumn() === 0, 'Other brand matched product.');
    saveProductBrand($pdo, $productId, '');
    $stmt = $pdo->prepare('SELECT brand_id FROM products WHERE id = ?');
    $stmt->execute([$productId]);
    checkBrandTest($stmt->fetchColumn() === null, 'Clearing brand failed.');
    saveProductBrand($pdo, $productId, $brandId);
    $pdo->prepare('DELETE FROM brands WHERE id = ?')->execute([$brandId]);
    $stmt->execute([$productId]);
    checkBrandTest($stmt->fetchColumn() === null, 'Foreign key did not clear removed brand.');
    $pdo->rollBack();
    echo "PASS: optional brand/image, validation, assignment, multi-brand filtering, exclusion, clearing and foreign key. All test data rolled back.\n";
} catch (Throwable $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    fwrite(STDERR, $e->getMessage() . "\n");
    exit(1);
}
