<?php

if (!function_exists('formatAdminNumberInput')) {
    function formatAdminNumberInput($value) {
        if ($value === null || $value === '') {
            return '';
        }

        $formatted = rtrim(rtrim(number_format((float)$value, 2, '.', ''), '0'), '.');
        return $formatted === '-0' ? '0' : $formatted;
    }
}

function ensureProductVariationSchema($pdo) {
    try {
        $pdo->exec("ALTER TABLE products ADD COLUMN has_variations TINYINT(1) DEFAULT 0");
    } catch (PDOException $e) {
        if (strpos($e->getMessage(), 'Duplicate column') === false && strpos($e->getMessage(), '1060') === false) {
            throw $e;
        }
    }

    $pdo->exec("CREATE TABLE IF NOT EXISTS product_variations (
        id INT AUTO_INCREMENT PRIMARY KEY,
        product_id INT NOT NULL,
        variation_label VARCHAR(255) NOT NULL,
        attributes_json TEXT,
        mrp DECIMAL(10,2) NOT NULL DEFAULT 0,
        selling_price DECIMAL(10,2) NOT NULL DEFAULT 0,
        stock_quantity INT NOT NULL DEFAULT 0,
        image_path VARCHAR(255) DEFAULT NULL,
        sort_order INT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX idx_product_variations_product (product_id),
        CONSTRAINT fk_product_variations_product
            FOREIGN KEY (product_id) REFERENCES products(id)
            ON DELETE CASCADE
    )");

    $pdo->exec("CREATE TABLE IF NOT EXISTS product_variation_attributes (
        id INT AUTO_INCREMENT PRIMARY KEY,
        variation_id INT NOT NULL,
        attribute_id INT NOT NULL,
        attribute_value_id INT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY unique_variation_attribute (variation_id, attribute_id),
        INDEX idx_variation_attributes_variation (variation_id),
        INDEX idx_variation_attributes_lookup (attribute_id, attribute_value_id),
        CONSTRAINT fk_variation_attributes_variation
            FOREIGN KEY (variation_id) REFERENCES product_variations(id)
            ON DELETE CASCADE,
        CONSTRAINT fk_variation_attributes_attribute
            FOREIGN KEY (attribute_id) REFERENCES product_attributes(id)
            ON DELETE CASCADE,
        CONSTRAINT fk_variation_attributes_value
            FOREIGN KEY (attribute_value_id) REFERENCES product_attribute_values(id)
            ON DELETE CASCADE
    )");
}

function getProductAttributeOptions($pdo) {
    try {
        $stmt = $pdo->query("
            SELECT a.id AS attribute_id, a.name AS attribute_name, v.id AS value_id, v.value
            FROM product_attributes a
            LEFT JOIN product_attribute_values v ON v.attribute_id = a.id
            WHERE a.is_active = 1
            ORDER BY a.sort_order ASC, a.name ASC, v.sort_order ASC, v.value ASC
        ");
    } catch (PDOException $e) {
        return [];
    }

    $attributes = [];
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $attributeId = (int)$row['attribute_id'];
        if (!isset($attributes[$attributeId])) {
            $attributes[$attributeId] = [
                'id' => $attributeId,
                'name' => $row['attribute_name'],
                'values' => []
            ];
        }

        if ($row['value_id']) {
            $attributes[$attributeId]['values'][] = [
                'id' => (int)$row['value_id'],
                'value' => $row['value']
            ];
        }
    }

    return array_values($attributes);
}

function getProductVariations($pdo, $productId) {
    $stmt = $pdo->prepare("SELECT * FROM product_variations WHERE product_id = ? ORDER BY sort_order ASC, id ASC");
    $stmt->execute([$productId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getSelectedAttributesFromVariations($variations) {
    $selected = [];

    foreach ($variations as $variation) {
        $attributes = json_decode($variation['attributes_json'] ?? '[]', true);
        if (!is_array($attributes)) {
            continue;
        }

        foreach ($attributes as $attribute) {
            $attributeId = (int)($attribute['attribute_id'] ?? 0);
            $valueId = (int)($attribute['value_id'] ?? 0);
            if ($attributeId <= 0 || $valueId <= 0) {
                continue;
            }

            if (!isset($selected[$attributeId])) {
                $selected[$attributeId] = [];
            }
            if (!in_array($valueId, $selected[$attributeId], true)) {
                $selected[$attributeId][] = $valueId;
            }
        }
    }

    return $selected;
}

function uploadVariationImage($file) {
    if (!isset($file) || ($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
        return null;
    }

    $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
    if (!in_array($file['type'], $allowedTypes, true)) {
        return null;
    }

    $uploadDir = "../uploads/products/";
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid('variation_', true) . '.' . $extension;
    $filepath = $uploadDir . $filename;

    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        return "uploads/products/" . $filename;
    }

    return null;
}

function normalizeVariationAttributes($attributesJson) {
    $attributes = json_decode($attributesJson ?: '[]', true);
    if (!is_array($attributes)) {
        return '[]';
    }

    $normalized = [];
    $seen = [];
    foreach ($attributes as $attribute) {
        $attributeId = (int)($attribute['attribute_id'] ?? 0);
        $valueId = (int)($attribute['value_id'] ?? 0);
        if ($attributeId <= 0 || $valueId <= 0) {
            continue;
        }

        $key = $attributeId . ':' . $valueId;
        if (isset($seen[$key])) {
            continue;
        }
        $seen[$key] = true;

        $normalized[] = [
            'attribute_id' => $attributeId,
            'attribute_name' => trim((string)($attribute['attribute_name'] ?? '')),
            'value_id' => $valueId,
            'value' => trim((string)($attribute['value'] ?? ''))
        ];
    }

    usort($normalized, function ($a, $b) {
        return $a['attribute_id'] <=> $b['attribute_id'];
    });

    return json_encode($normalized);
}

function getVariationLabelFromAttributesJson($attributesJson, $fallbackLabel = '') {
    $attributes = json_decode($attributesJson ?: '[]', true);
    if (!is_array($attributes) || empty($attributes)) {
        return trim($fallbackLabel);
    }

    $parts = [];
    foreach ($attributes as $attribute) {
        $attributeName = trim((string)($attribute['attribute_name'] ?? ''));
        $value = trim((string)($attribute['value'] ?? ''));
        if ($attributeName !== '' && $value !== '') {
            $parts[] = $attributeName . ': ' . $value;
        }
    }

    return $parts ? implode(' / ', $parts) : trim($fallbackLabel);
}

function getVariationValueMapJson($attributesJson) {
    $attributes = json_decode(normalizeVariationAttributes($attributesJson), true);
    if (!is_array($attributes)) {
        return '{}';
    }

    $valueMap = [];
    foreach ($attributes as $attribute) {
        $attributeId = (int)($attribute['attribute_id'] ?? 0);
        $valueId = (int)($attribute['value_id'] ?? 0);
        if ($attributeId > 0 && $valueId > 0) {
            $valueMap[(string)$attributeId] = (string)$valueId;
        }
    }

    ksort($valueMap, SORT_NUMERIC);
    return json_encode($valueMap);
}

function getPostedVariationSnapshot() {
    $hasVariations = isset($_POST['has_variations']) ? 1 : 0;
    if (!$hasVariations || empty($_POST['variation_label']) || !is_array($_POST['variation_label'])) {
        return [];
    }

    $snapshot = [];
    $labels = $_POST['variation_label'];
    $variationIds = $_POST['variation_id'] ?? [];
    $attributesJson = $_POST['variation_attributes_json'] ?? [];
    $mrps = $_POST['variation_mrp'] ?? [];
    $sellingPrices = $_POST['variation_selling_price'] ?? [];
    $stocks = $_POST['variation_stock'] ?? [];
    $existingImages = $_POST['existing_variation_image'] ?? [];

    foreach ($labels as $index => $label) {
        $label = trim($label);
        if ($label === '') {
            continue;
        }

        $normalizedAttributes = normalizeVariationAttributes($attributesJson[$index] ?? '[]');
        $snapshot[] = [
            'label' => getVariationLabelFromAttributesJson($normalizedAttributes, $label),
            'attributes' => $normalizedAttributes,
            'mrp' => number_format((float)($mrps[$index] ?? 0), 2, '.', ''),
            'selling_price' => number_format((float)($sellingPrices[$index] ?? 0), 2, '.', ''),
            'stock' => (string)(int)($stocks[$index] ?? 0),
            'image' => trim((string)($existingImages[$index] ?? ''))
        ];
    }

    return $snapshot;
}

function hasUploadedVariationImage() {
    $files = $_FILES['variation_image'] ?? null;
    if (!$files || empty($files['error']) || !is_array($files['error'])) {
        return false;
    }

    foreach ($files['error'] as $error) {
        if ($error === UPLOAD_ERR_OK) {
            return true;
        }
    }

    return false;
}

function haveProductVariationsChanged($pdo, $productId) {
    $postedHasVariations = isset($_POST['has_variations']) ? 1 : 0;
    $stmt = $pdo->prepare("SELECT has_variations FROM products WHERE id = ?");
    $stmt->execute([$productId]);
    $currentHasVariations = (int)$stmt->fetchColumn();

    if ($postedHasVariations !== $currentHasVariations) {
        return true;
    }

    if (hasUploadedVariationImage()) {
        return true;
    }

    $currentSnapshot = [];
    foreach (getProductVariations($pdo, $productId) as $variation) {
        $currentSnapshot[] = [
            'label' => getVariationLabelFromAttributesJson($variation['attributes_json'] ?? '[]', $variation['variation_label']),
            'attributes' => normalizeVariationAttributes($variation['attributes_json'] ?? '[]'),
            'mrp' => number_format((float)$variation['mrp'], 2, '.', ''),
            'selling_price' => number_format((float)$variation['selling_price'], 2, '.', ''),
            'stock' => (string)(int)$variation['stock_quantity'],
            'image' => trim((string)($variation['image_path'] ?? ''))
        ];
    }

    return $currentSnapshot !== getPostedVariationSnapshot();
}

function saveProductVariations($pdo, $productId) {
    $hasVariations = isset($_POST['has_variations']) ? 1 : 0;
    $pdo->prepare("UPDATE products SET has_variations = ? WHERE id = ?")->execute([$hasVariations, $productId]);

    if (!$hasVariations) {
        $pdo->prepare("DELETE FROM product_variations WHERE product_id = ?")->execute([$productId]);
        return 0;
    }

    if (empty($_POST['variation_label']) || !is_array($_POST['variation_label'])) {
        $pdo->prepare("DELETE FROM product_variations WHERE product_id = ?")->execute([$productId]);
        return 0;
    }

    $labels = $_POST['variation_label'];
    $attributesJson = $_POST['variation_attributes_json'] ?? [];
    $mrps = $_POST['variation_mrp'] ?? [];
    $sellingPrices = $_POST['variation_selling_price'] ?? [];
    $stocks = $_POST['variation_stock'] ?? [];
    $existingImages = $_POST['existing_variation_image'] ?? [];
    $files = $_FILES['variation_image'] ?? null;

    $stmt = $pdo->prepare("
        INSERT INTO product_variations
            (product_id, variation_label, attributes_json, mrp, selling_price, stock_quantity, image_path, sort_order)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $variationRows = [];
    $seenCombinations = [];

    foreach ($labels as $index => $label) {
        $label = trim($label);
        if ($label === '') {
            continue;
        }

        $imagePath = $existingImages[$index] ?? null;
        if ($files && isset($files['name'][$index]) && $files['error'][$index] === UPLOAD_ERR_OK) {
            $file = [
                'name' => $files['name'][$index],
                'type' => $files['type'][$index],
                'tmp_name' => $files['tmp_name'][$index],
                'error' => $files['error'][$index],
                'size' => $files['size'][$index]
            ];
            $uploadedImage = uploadVariationImage($file);
            if ($uploadedImage) {
                $imagePath = $uploadedImage;
            }
        }

        $normalizedAttributes = normalizeVariationAttributes($attributesJson[$index] ?? '[]');
        $combinationKey = getVariationValueMapJson($normalizedAttributes);
        if ($combinationKey === '{}' || isset($seenCombinations[$combinationKey])) {
            continue;
        }
        $seenCombinations[$combinationKey] = true;
        $label = getVariationLabelFromAttributesJson($normalizedAttributes, $label);

        $variationRows[] = [
            'label' => $label,
            'id' => isset($variationIds[$index]) ? (int)$variationIds[$index] : 0,
            'attributes' => $normalizedAttributes,
            'mrp' => (float)($mrps[$index] ?? 0),
            'selling_price' => (float)($sellingPrices[$index] ?? 0),
            'stock' => (int)($stocks[$index] ?? 0),
            'image' => $imagePath ?: null,
            'sort_order' => $index + 1
        ];
    }

    if (!$variationRows) {
        $pdo->prepare("DELETE FROM product_variations WHERE product_id = ?")->execute([$productId]);
        return 0;
    }

    $attributeStmt = $pdo->prepare("
        INSERT INTO product_variation_attributes
            (variation_id, attribute_id, attribute_value_id)
        VALUES (?, ?, ?)
    ");
    $updateStmt = $pdo->prepare("
        UPDATE product_variations
        SET variation_label = ?, attributes_json = ?, mrp = ?, selling_price = ?, stock_quantity = ?, image_path = ?, sort_order = ?
        WHERE id = ? AND product_id = ?
    ");
    $insertStmt = $stmt;
    $deleteAttributesStmt = $pdo->prepare("DELETE FROM product_variation_attributes WHERE variation_id = ?");
    $ownershipStmt = $pdo->prepare("SELECT id FROM product_variations WHERE id = ? AND product_id = ?");

    $savedCount = 0;
    $savedVariationIds = [];
    foreach ($variationRows as $variationRow) {
        $variationId = (int)($variationRow['id'] ?? 0);
        if ($variationId > 0) {
            $ownershipStmt->execute([$variationId, $productId]);
            if ($ownershipStmt->fetchColumn()) {
                $updateStmt->execute([
                    $variationRow['label'],
                    $variationRow['attributes'],
                    $variationRow['mrp'],
                    $variationRow['selling_price'],
                    $variationRow['stock'],
                    $variationRow['image'],
                    $variationRow['sort_order'],
                    $variationId,
                    $productId
                ]);
            } else {
                $variationId = 0;
            }
        }

        if ($variationId <= 0) {
            $insertStmt->execute([
                $productId,
                $variationRow['label'],
                $variationRow['attributes'],
                $variationRow['mrp'],
                $variationRow['selling_price'],
                $variationRow['stock'],
                $variationRow['image'],
                $variationRow['sort_order']
            ]);
            $variationId = (int)$pdo->lastInsertId();
        }

        $savedVariationIds[] = $variationId;
        $deleteAttributesStmt->execute([$variationId]);
        $normalizedItems = json_decode($variationRow['attributes'], true);
        foreach ($normalizedItems as $item) {
            $attributeStmt->execute([
                $variationId,
                (int)$item['attribute_id'],
                (int)$item['value_id']
            ]);
        }
        $savedCount++;
    }

    if (!empty($savedVariationIds)) {
        $placeholders = implode(',', array_fill(0, count($savedVariationIds), '?'));
        $deleteParams = array_merge([$productId], $savedVariationIds);
        $deleteStmt = $pdo->prepare("DELETE FROM product_variations WHERE product_id = ? AND id NOT IN ($placeholders)");
        $deleteStmt->execute($deleteParams);
    }

    return $savedCount;
}

function renderProductAttributesSection($attributeOptions, $selectedAttributes = [], $variations = [], $product = []) {
    $hasVariations = !empty($product['has_variations']) || !empty($variations);
    $baseMrp = formatAdminNumberInput($product['mrp'] ?? 0);
    $baseSellingPrice = formatAdminNumberInput($product['selling_price'] ?? 0);
    $baseStock = isset($product['stock_quantity']) ? (int)$product['stock_quantity'] : 0;
    ?>
    <div class="product-attributes-panel mt-4">
        <div class="product-attributes-heading">
            <div>
                <h5 class="mb-1">Product Attributes</h5>
                <p class="product-attributes-help mb-0">Choose attributes and values, then click Sync Combinations to create product variations.</p>
            </div>
            <button type="button" class="btn btn-outline-primary btn-sm" id="addProductAttributeBtn">
                <i class="fas fa-plus"></i> Add Attribute
            </button>
        </div>

        <div id="productAttributeSelectorWrap" class="d-none"></div>
        <div class="saved-attribute-groups" id="savedAttributeGroups"></div>

        <label class="product-variation-toggle" for="hasVariations">
            <input class="form-check-input" type="checkbox" id="hasVariations" name="has_variations" <?php echo $hasVariations ? 'checked' : ''; ?>>
            <span class="product-variation-switch" aria-hidden="true"></span>
            <span>This product has variations (Price/Image based on attributes)</span>
        </label>

        <div id="variationControls" class="<?php echo $hasVariations ? '' : 'd-none'; ?>">
            <div id="variationDuplicateMessage" class="variation-duplicate-message d-none">
                This variation already exists. Please select a different attribute value.
            </div>

            <div class="d-flex justify-content-between align-items-center mt-4 mb-2 product-variation-title-row">
                <h5 class="mb-0">Product Variations</h5>
                <button type="button" class="btn btn-primary btn-sm" id="syncVariationCombinationsBtn">
                    <i class="fas fa-sync-alt"></i> Sync Combinations
                </button>
            </div>

            <div id="variationInfoMessage" class="variation-info-message d-none">
                For products with 2 or more attributes, keep one variation row per exact combination like <strong>Black + Size 9</strong>.<br>
                Storefront price, stock and image use only exact combinations.
            </div>

            <div class="table-responsive product-variations-table-wrap">
                <table class="table table-bordered table-sm align-middle product-variations-table">
                    <thead>
                        <tr>
                            <th>Variation<br>(Attribute Mix)</th>
                            <th>MRP</th>
                            <th>Selling Price</th>
                            <th>Stock</th>
                            <th>Image</th>
                            <th style="width: 52px;"></th>
                        </tr>
                    </thead>
                    <tbody id="variationRows"></tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        window.productAttributeOptions = <?php echo json_encode($attributeOptions); ?>;
        window.productSelectedAttributes = <?php echo json_encode($selectedAttributes); ?>;
        window.productExistingVariations = <?php echo json_encode(array_map(function ($variation) {
            return [
                'label' => $variation['variation_label'],
                'id' => (int)$variation['id'],
                'attributes_json' => $variation['attributes_json'],
                'mrp' => formatAdminNumberInput($variation['mrp']),
                'selling_price' => formatAdminNumberInput($variation['selling_price']),
                'stock_quantity' => $variation['stock_quantity'],
                'image_path' => $variation['image_path']
            ];
        }, $variations)); ?>;
        window.productVariationDefaults = {
            mrp: <?php echo json_encode($baseMrp); ?>,
            sellingPrice: <?php echo json_encode($baseSellingPrice); ?>,
            stock: <?php echo json_encode((string)$baseStock); ?>
        };
    </script>
    <?php
}

function renderProductVariationAssets() {
    ?>
    <style>
        .product-form-page .product-attributes-panel {
            font-family: inherit;
            max-width: 960px;
            margin-top: 28px !important;
            padding: 26px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            background: #fff;
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.05);
        }

        .product-form-page .product-attributes-heading {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 18px;
            margin-bottom: 18px;
        }

        .product-form-page .product-attributes-heading h5 {
            color: #020617;
            font-size: inherit;
            font-weight: 500;
            line-height: 1.2;
            margin-bottom: 0 !important;
        }

        .product-form-page .product-attributes-help {
            color: #607086;
            font-size: .875rem;
            line-height: 1.35;
            white-space: nowrap;
        }

        .product-form-page #addProductAttributeBtn {
            align-self: flex-start;
            margin-left: auto;
            padding: .25rem .5rem;
            font-size: .875rem;
            border-radius: .2rem;
            display: inline-block;
            font-weight: 400;
            line-height: 1.5;
            text-align: center;
            text-decoration: none;
            vertical-align: middle;
            color: #0d6efd;
            border-color: #0d6efd;
            white-space: nowrap;
        }

        .product-form-page #addProductAttributeBtn:hover,
        .product-form-page #addProductAttributeBtn:focus {
            background: #0d6efd;
            border-color: #0d6efd;
            color: #fff;
        }

        .product-form-page .product-save-success-alert {
            color: #243041;
            background: #f8fafc;
            border: 1px solid #d9dee7;
            border-radius: 6px;
        }

        .product-form-page .no-success-icon::before,
        .product-form-page .no-success-icon i,
        .product-form-page .no-success-icon svg {
            display: none !important;
            content: none !important;
        }

        .product-form-page .product-attribute-row {
            display: grid;
            grid-template-columns: minmax(220px, 0.9fr) minmax(390px, 1.5fr) 34px;
            gap: 12px 0;
            align-items: center;
            max-width: 960px;
            margin: 0 0 12px;
            padding: 18px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            background: #fff;
        }

        .product-form-page #productAttributeSelectorWrap:not(.d-none) {
            margin-bottom: 14px;
        }

        .product-form-page .product-attribute-field {
            min-width: 0;
            padding-left: 12px;
            padding-right: 12px;
        }

        .product-form-page .saved-attribute-groups {
            display: grid;
            gap: 10px;
            margin-bottom: 14px;
        }

        .product-form-page .saved-attribute-groups:empty {
            display: none;
        }

        .product-form-page .saved-attribute-groups-title {
            margin: 0 0 2px;
            color: #0f172a;
            font-size: inherit;
            font-weight: 800;
            line-height: 1.2;
        }

        .product-form-page .saved-attribute-group {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            padding: 12px 14px;
            border: 1px solid #dbe3ec;
            border-radius: 8px;
            background: #f8fafc;
        }

        .product-form-page .saved-attribute-group-name {
            margin-bottom: 3px;
            color: #0f172a;
            font-size: inherit;
            font-weight: 800;
            line-height: 1.15;
        }

        .product-form-page .saved-attribute-group-values {
            color: #475569;
            font-size: inherit;
            font-weight: 600;
            line-height: 1.4;
        }

        .product-form-page .saved-attribute-edit {
            flex: 0 0 auto;
            border: 1px solid #0d6efd;
            border-radius: 7px;
            background: #fff;
            color: #0d6efd;
            padding: 5px 12px;
            font-size: inherit;
            font-weight: 700;
        }

        .product-form-page .inline-combination-editor {
            margin: 14px 0 18px;
            padding: 14px;
            border: 1px solid #ffba7a;
            border-left: 4px solid #ff7700;
            border-radius: 10px;
            background: #fffaf4;
        }

        .product-form-page .combination-editor-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 12px;
        }

        .product-form-page .combination-editor-title {
            color: #020617;
            font-size: inherit;
            font-weight: 800;
            line-height: 1.2;
        }

        .product-form-page .combination-editor-subtitle {
            color: #68758a;
            font-size: inherit;
            font-weight: 500;
        }

        .product-form-page .combination-editor-actions {
            display: flex;
            gap: 10px;
        }

        .product-form-page .combination-editor-values {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(110px, 1fr));
            gap: 10px;
            margin-bottom: 14px;
        }

        .product-form-page .combination-editor-option {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            min-height: 48px;
            padding: 8px 12px;
            border: 1px solid #dbe3ec;
            border-radius: 8px;
            background: #fff;
            color: #020617;
            font-size: inherit;
            font-weight: 500;
        }

        .product-form-page .combination-editor-option > span:first-child {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            line-height: 1;
        }

        .product-form-page .combination-editor-option.is-selected {
            background: #eef6ff;
            border-color: #b8d7ff;
        }

        .product-form-page .combination-editor-option input {
            width: 16px;
            height: 16px;
            margin: 0;
            flex: 0 0 auto;
            accent-color: #0d6efd;
        }

        .product-form-page .combination-editor-badge {
            min-width: 54px;
            padding: 2px 8px;
            border: 1px solid #cfddec;
            border-radius: 999px;
            background: #f8fafc;
            color: #667085;
            font-size: 0.85em;
            font-weight: 800;
            text-align: center;
        }

        .product-form-page .combination-editor-badge.added {
            border-color: #a9dfbb;
            background: #dcf8e6;
            color: #078c35;
        }

        .product-form-page .combination-editor-current {
            padding: 10px 12px;
            border: 1px solid #ffd9a8;
            border-radius: 8px;
            background: #fff8ef;
            color: #a73400;
            font-size: 0.875rem;
            font-weight: 700;
        }

        .product-form-page .product-variation-toggle {
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 18px 0 26px;
            padding: 12px 14px;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            background: #f8fafc;
            color: #020617;
            font-size: 0.875rem;
            font-weight: 800;
            cursor: pointer;
        }

        .product-form-page .product-variation-toggle input {
            position: absolute;
            opacity: 0;
            pointer-events: none;
        }

        .product-form-page .product-variation-switch {
            position: relative;
            flex: 0 0 44px;
            width: 44px;
            height: 22px;
            border-radius: 999px;
            background: #cbd5e1;
            transition: background 0.2s ease;
        }

        .product-form-page .product-variation-switch::after {
            content: "";
            position: absolute;
            top: 3px;
            left: 4px;
            width: 16px;
            height: 16px;
            border-radius: 50%;
            background: #fff;
            transition: transform 0.2s ease;
        }

        .product-form-page .product-variation-toggle input:checked + .product-variation-switch {
            background: #0d6efd;
        }

        .product-form-page .product-variation-toggle input:checked + .product-variation-switch::after {
            transform: translateX(20px);
        }

        .product-form-page #variationControls {
            border-top: 1px solid #e2e8f0;
            padding-top: 18px;
        }

        .product-form-page .product-attribute-field label {
            color: #667085 !important;
            font-size: inherit;
            font-weight: 700;
            margin: 0 0 8px;
        }

        .product-form-page .product-attribute-row .form-select,
        .product-form-page .attribute-value-grid {
            min-height: 46px;
            font-size: inherit;
        }

        .product-form-page .product-attribute-row .form-select {
            border-radius: 7px;
            width: 100%;
            border-color: #cbd5e1;
            box-shadow: none;
            font-weight: 600;
        }

        .product-form-page .attribute-value-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(170px, 1fr));
            gap: 10px;
            padding: 10px;
            border: 1px solid #cbd5e1;
            border-radius: 7px;
            background: #fff;
            width: 100%;
        }

        .product-form-page .attribute-value-grid > .text-muted {
            grid-column: 1 / -1;
            white-space: nowrap;
        }

        .product-form-page .attribute-value-option {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 6px;
            padding: 9px 10px;
            border: 1px solid #dbe3ec;
            border-radius: 7px;
            background: #fff;
            color: inherit;
            font-weight: 700;
            min-width: 0;
            overflow: hidden;
        }

        .product-form-page .attribute-value-option input {
            flex: 0 0 auto;
            width: 16px;
            height: 16px;
            accent-color: #0d6efd;
        }

        .product-form-page .attribute-value-option.is-selected {
            background: #f0f7ff;
            border-color: #b9d7ff;
        }

        .product-form-page .attribute-value-status {
            flex: 0 0 auto;
            margin-left: auto;
            padding: 2px 6px;
            border: 1px solid #cfddec;
            border-radius: 999px;
            background: #f8fafc;
            color: #667085;
            font-size: 0.8em;
            font-weight: 800;
            line-height: 1.2;
        }

        .product-form-page .attribute-value-status.added {
            border-color: #a9dfbb;
            background: #dcf8e6;
            color: #078c35;
        }

        .product-form-page .remove-product-attribute-btn,
        .product-form-page .remove-variation-row-btn {
            min-height: 34px;
            width: 34px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 0;
            background: transparent;
            color: #e62f49;
            font-size: 1.22em;
            font-weight: 800;
            line-height: 1;
            padding: 0 0 6px;
        }

        .product-form-page .product-attribute-row .remove-product-attribute-btn {
            width: 30px;
            min-height: 30px;
            height: 30px;
            margin-top: 36px;
            padding: 0;
            border: 1px solid #fee2e2;
            border-radius: 7px;
            background: #fff;
        }

        .product-form-page .product-variations-table-wrap {
            max-width: 100%;
            overflow-x: hidden;
        }

        .product-form-page .product-variations-table {
            margin-bottom: 0;
            width: 100%;
            max-width: 100%;
            table-layout: fixed;
        }

        .product-form-page .product-variations-table th {
            font-size: 0.9em;
            font-weight: 800;
            vertical-align: bottom;
            background: #f8fafc;
            border-bottom: 2px solid #0f172a;
        }

        .product-form-page .product-variations-table th:nth-child(1),
        .product-form-page .product-variations-table td:nth-child(1) {
            width: 32%;
        }

        .product-form-page .product-variations-table th:nth-child(2),
        .product-form-page .product-variations-table td:nth-child(2),
        .product-form-page .product-variations-table th:nth-child(3),
        .product-form-page .product-variations-table td:nth-child(3) {
            width: 14%;
        }

        .product-form-page .product-variations-table th:nth-child(4),
        .product-form-page .product-variations-table td:nth-child(4) {
            width: 10%;
        }

        .product-form-page .product-variations-table th:nth-child(5),
        .product-form-page .product-variations-table td:nth-child(5) {
            width: 25%;
        }

        .product-form-page .product-variations-table th:nth-child(6),
        .product-form-page .product-variations-table td:nth-child(6) {
            width: 5%;
        }

        .product-form-page .product-variations-table td {
            font-size: 0.86em;
            font-weight: 400;
            vertical-align: middle;
            overflow-wrap: anywhere;
            padding: 3px 6px;
        }

        .product-form-page .product-variations-table td:first-child strong {
            display: inline;
            font-size: inherit;
            font-weight: 400;
            line-height: 1.25;
        }

        .product-form-page .variation-label-line {
            display: inline;
            font-size: calc(1em + 1px);
            line-height: 1.25;
        }

        .product-form-page .variation-label-line strong {
            min-width: 0;
            overflow-wrap: anywhere;
        }

        .product-form-page .variation-label-line .variation-status-badge {
            margin-left: 6px;
            white-space: nowrap;
            vertical-align: middle;
        }

        .product-form-page .product-variations-table input[type="number"],
        .product-form-page .product-variations-table input[type="text"] {
            height: 32px;
            min-height: 32px;
            max-width: 100%;
            padding: 3px 8px;
            box-sizing: border-box;
        }

        .product-form-page .variation-file-input {
            height: 32px;
            min-height: 32px;
            min-width: 0;
            width: 100%;
            flex: 1 1 auto;
            font-size: .875rem;
            line-height: 20px;
            padding: 0 8px 0 0;
            overflow: hidden;
        }

        .product-form-page .variation-file-input::file-selector-button {
            height: 30px;
            margin: 0 10px 0 0;
            padding: 0 10px;
            border: 0;
            border-right: 1px solid #cfd6df;
            background: #eef1f5;
            color: #000;
        }

        .product-form-page .variation-current-image {
            flex: 0 0 34px;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 34px;
            height: 34px;
            color: transparent;
            font-size: 0;
            line-height: 0;
        }

        .product-form-page .variation-current-image img {
            width: 34px;
            height: 34px;
            border: 1px solid #d9dee7;
            border-radius: 4px;
            object-fit: cover;
        }

        .product-form-page .variation-image-field {
            display: flex;
            align-items: center;
            gap: 6px;
            min-width: 0;
            width: 100%;
        }

        .product-form-page .variations-disabled {
            opacity: 0.55;
            pointer-events: none;
        }

        .product-form-page .variation-empty-row td {
            height: 90px;
            color: #667085;
            font-size: inherit;
            font-weight: 500;
            text-align: center;
        }

        .product-form-page .product-variation-title-row {
            display: flex !important;
            align-items: center !important;
            max-width: 960px;
            margin-top: 10px !important;
            margin-bottom: 14px !important;
            min-height: 32px;
        }

        .product-form-page .product-variation-title-row h5 {
            font-size: inherit;
            font-weight: 400;
            line-height: 1.2;
            margin: 0 !important;
        }

        .product-form-page #syncVariationCombinationsBtn {
            padding: .25rem .5rem;
            font-size: .875rem;
            border-radius: .2rem;
            display: inline-block;
            font-weight: 400;
            line-height: 1.5;
            text-align: center;
            text-decoration: none;
            vertical-align: middle;
            white-space: nowrap;
        }

        .product-form-page .variation-duplicate-message {
            max-width: 960px;
            margin: 0 0 12px;
            padding: 8px 12px;
            border: 1px solid #f1b7bf;
            border-radius: 4px;
            background: #fff1f3;
            color: #b4233a;
            font-size: 14px;
            font-weight: 500;
        }

        .product-form-page .variation-info-message {
            max-width: 960px;
            margin: 10px 0 18px;
            padding: 12px 20px;
            border: 1px solid #b6effb;
            border-radius: 6px;
            background: #cff4fc;
            color: #055160;
            font-size: 14px;
            font-weight: 500;
            line-height: 1.45;
            transition: opacity 0.25s ease;
        }

        .product-form-page .variation-info-message.is-hiding {
            opacity: 0;
        }

        .product-form-page .variation-status-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 42px;
            padding: 2px 7px;
            border-radius: 999px;
            font-size: 0.78em;
            font-weight: 800;
            line-height: 1.2;
        }

        .product-form-page .variation-status-badge.added {
            border: 1px solid #a9dfbb;
            background: #dcf8e6;
            color: #078c35;
        }

        .product-form-page .variation-status-badge.new {
            border: 1px solid #ffd9a8;
            background: #fff4e4;
            color: #ff7800;
        }

        @media (max-width: 900px) {
            .product-form-page .product-attributes-panel {
                padding: 24px;
            }

            .product-form-page .product-attribute-row {
                grid-template-columns: 1fr;
                padding: 18px;
            }

            .product-form-page .attribute-value-grid {
                max-width: none;
            }

            .product-form-page .product-attribute-row .remove-product-attribute-btn {
                margin-top: 0;
                justify-self: end;
            }

            .product-form-page .product-attributes-help {
                white-space: normal;
            }
        }

    </style>
    <script>
        function initProductVariationManager() {
            const options = window.productAttributeOptions || [];
            const selectedAttributes = window.productSelectedAttributes || {};
            const existingVariations = window.productExistingVariations || [];
            const defaults = window.productVariationDefaults || { mrp: '0', sellingPrice: '0', stock: '0' };
            const hasVariations = document.getElementById('hasVariations');
            const variationControls = document.getElementById('variationControls');
            const selectorWrap = document.getElementById('productAttributeSelectorWrap');
            const rowsBody = document.getElementById('variationRows');
            const savedGroups = document.getElementById('savedAttributeGroups');
            const addBtn = document.getElementById('addProductAttributeBtn');
            const syncBtn = document.getElementById('syncVariationCombinationsBtn');
            const duplicateMsg = document.getElementById('variationDuplicateMessage');
            const infoMsg = document.getElementById('variationInfoMessage');
            const tableWrap = document.querySelector('.product-variations-table-wrap');
            let infoTimer = null;

            if (!hasVariations || !selectorWrap || !rowsBody || !addBtn || !syncBtn || !duplicateMsg || !infoMsg) {
                return;
            }

            function escapeHtml(value) {
                return String(value || '').replace(/[&<>"']/g, function (char) {
                    return {
                        '&': '&amp;',
                        '<': '&lt;',
                        '>': '&gt;',
                        '"': '&quot;',
                        "'": '&#039;'
                    }[char];
                });
            }

            function priceValue(value, fallback) {
                const rawValue = value !== undefined && value !== null && value !== '' ? value : fallback;
                const numericValue = Number(rawValue);
                if (!Number.isFinite(numericValue)) {
                    return rawValue || '0';
                }

                return String(numericValue);
            }

            function imageUrl(imagePath) {
                if (!imagePath) {
                    return '';
                }

                if (/^(https?:)?\/\//.test(imagePath) || imagePath.charAt(0) === '/') {
                    return imagePath;
                }

                return '../' + imagePath.replace(/^\.?\//, '');
            }

            function updateVariationImagePreview(fileInput) {
                const field = fileInput.closest('.variation-image-field');
                if (!field || !fileInput.files || !fileInput.files[0]) {
                    return;
                }

                let preview = field.querySelector('.variation-current-image');
                if (!preview) {
                    preview = document.createElement('span');
                    preview.className = 'variation-current-image';
                    preview.title = 'Selected image';
                    field.appendChild(preview);
                }

                preview.innerHTML = `<img src="${URL.createObjectURL(fileInput.files[0])}" alt="">`;
            }

            function showDuplicateMessage() {
                duplicateMsg.classList.remove('d-none');
            }

            function hideDuplicateMessage() {
                duplicateMsg.classList.add('d-none');
            }

            function showVariationInfoMessage() {
                clearTimeout(infoTimer);
                infoMsg.classList.remove('d-none', 'is-hiding');
                infoTimer = setTimeout(function () {
                    infoMsg.classList.add('is-hiding');
                    setTimeout(function () {
                        infoMsg.classList.add('d-none');
                        infoMsg.classList.remove('is-hiding');
                    }, 250);
                }, 4500);
            }

            function getAttributeById(attributeId) {
                return options.find(function (attribute) {
                    return String(attribute.id) === String(attributeId);
                });
            }

            function getValueById(attribute, valueId) {
                if (!attribute || !attribute.values) {
                    return null;
                }

                return attribute.values.find(function (value) {
                    return String(value.id) === String(valueId);
                });
            }

            function parseAttributes(attributesJson) {
                if (!attributesJson) {
                    return [];
                }

                if (Array.isArray(attributesJson)) {
                    return attributesJson;
                }

                try {
                    const parsed = JSON.parse(attributesJson);
                    return Array.isArray(parsed) ? parsed : [];
                } catch (error) {
                    return [];
                }
            }

            function normalizeVariationItems(items) {
                return (items || []).map(function (item) {
                    const attribute = getAttributeById(item.attribute_id);
                    const value = getValueById(attribute, item.value_id);
                    if (!attribute || !value) {
                        return null;
                    }

                    return {
                        attribute_id: Number(item.attribute_id || 0),
                        attribute_name: attribute.name,
                        value_id: Number(item.value_id || 0),
                        value: value.value
                    };
                }).filter(function (item) {
                    if (!item) {
                        return false;
                    }

                    return item.attribute_id && item.value_id && item.attribute_name && item.value;
                });
            }

            function variationKey(items) {
                const normalizedItems = normalizeVariationItems(items);
                if (!normalizedItems.length) {
                    return '';
                }

                return normalizedItems.map(function (item) {
                    return item.attribute_id + ':' + item.value_id;
                }).sort().join('|');
            }

            function variationValueMapKey(items) {
                const valueMap = {};
                normalizeVariationItems(items).forEach(function (item) {
                    valueMap[String(item.attribute_id)] = String(item.value_id);
                });

                return JSON.stringify(Object.keys(valueMap).sort().reduce(function (sorted, attributeId) {
                    sorted[attributeId] = valueMap[attributeId];
                    return sorted;
                }, {}));
            }

            function updateEnabledState() {
                variationControls.classList.toggle('d-none', !hasVariations.checked);
                renderEmptyVariationRow();
            }

            function getConfiguredValueIds(attributeId) {
                const configured = new Set();
                getConfiguredVariationRows().forEach(function (entry) {
                    entry.attrs.forEach(function (item) {
                        if (String(item.attribute_id) === String(attributeId)) {
                            configured.add(String(item.value_id));
                        }
                    });
                });
                return configured;
            }

            function renderValueCheckboxes(row, selectedValueIds) {
                const attributeId = row.querySelector('.product-attribute-select').value;
                const valueContainer = row.querySelector('.attribute-value-grid');
                const attribute = getAttributeById(attributeId);
                const selected = new Set((selectedValueIds || []).map(function (valueId) {
                    return String(valueId);
                }));
                const configured = getConfiguredValueIds(attributeId);

                if (!attribute || !attribute.values.length) {
                    valueContainer.innerHTML = '<span class="text-muted">Select an attribute first.</span>';
                    return;
                }

                valueContainer.innerHTML = attribute.values.map(function (value) {
                    const valueId = String(value.id);
                    const checked = selected.has(valueId);
                    const statusText = configured.has(valueId) ? 'Added' : 'Not Added';
                    return `
                        <label class="attribute-value-option ${checked ? 'is-selected' : ''}">
                            <span><input type="checkbox" class="product-attribute-value-checkbox" value="${escapeHtml(value.id)}" ${checked ? 'checked' : ''}> ${escapeHtml(value.value)}</span>
                            <span class="attribute-value-status ${configured.has(valueId) ? 'added' : ''}">${statusText}</span>
                        </label>
                    `;
                }).join('');
            }

            function addAttributeRow(attributeId, selectedValueId) {
                const row = document.createElement('div');
                row.className = 'product-attribute-row';
                row.innerHTML = `
                    <div class="product-attribute-field">
                        <label class="form-label">Attribute Type</label>
                        <select class="form-select product-attribute-select">
                            <option value="">Select...</option>
                            ${options.map(function (attribute) {
                                return `<option value="${attribute.id}">${escapeHtml(attribute.name)}</option>`;
                            }).join('')}
                        </select>
                    </div>
                    <div class="product-attribute-field">
                        <label class="form-label">Value</label>
                        <div class="attribute-value-grid">
                            <span class="text-muted">Select an attribute first.</span>
                        </div>
                    </div>
                    <button type="button" class="remove-product-attribute-btn" title="Remove attribute"><i class="fas fa-trash-alt"></i></button>
                `;
                selectorWrap.appendChild(row);

                if (attributeId) {
                    row.querySelector('.product-attribute-select').value = attributeId;
                }

                renderValueCheckboxes(row, selectedValueId ? [selectedValueId] : []);

                row.querySelector('.product-attribute-select').addEventListener('change', function () {
                    hideDuplicateMessage();
                    renderValueCheckboxes(row, []);
                });

                row.querySelector('.attribute-value-grid').addEventListener('change', function (event) {
                    if (!event.target.matches('.product-attribute-value-checkbox')) return;

                    const option = event.target.closest('.attribute-value-option');
                    if (option) {
                        option.classList.toggle('is-selected', event.target.checked);
                    }
                    hideDuplicateMessage();
                });

                row.querySelector('.remove-product-attribute-btn').addEventListener('click', function () {
                    hideDuplicateMessage();
                    row.remove();
                });

                updateEnabledState();
            }

            function selectedAttributeGroups() {
                const groups = new Map();

                Array.from(selectorWrap.querySelectorAll('.product-attribute-row')).forEach(function (row) {
                    const attributeId = row.querySelector('.product-attribute-select').value;
                    const attribute = getAttributeById(attributeId);
                    if (!attribute) {
                        return;
                    }

                    if (!groups.has(String(attribute.id))) {
                        groups.set(String(attribute.id), {
                            attribute_id: attribute.id,
                            attribute_name: attribute.name,
                            values: []
                        });
                    }

                    const group = groups.get(String(attribute.id));
                    row.querySelectorAll('.product-attribute-value-checkbox:checked').forEach(function (checkbox) {
                        const value = getValueById(attribute, checkbox.value);
                        if (!value) {
                            return;
                        }

                        if (!group.values.some(function (existingValue) {
                            return String(existingValue.value_id) === String(value.id);
                        })) {
                            group.values.push({
                                attribute_id: attribute.id,
                                attribute_name: attribute.name,
                                value_id: value.id,
                                value: value.value
                            });
                        }
                    });
                });

                return Array.from(groups.values()).filter(function (group) {
                    return group.values.length > 0;
                }).sort(function (a, b) {
                    return Number(a.attribute_id) - Number(b.attribute_id);
                });
            }

            function cartesianProduct(groups) {
                if (!groups.length) {
                    return [];
                }

                return groups.reduce(function (combinations, group) {
                    const nextCombinations = [];
                    combinations.forEach(function (combination) {
                        group.values.forEach(function (value) {
                            nextCombinations.push(combination.concat([value]));
                        });
                    });
                    return nextCombinations;
                }, [[]]);
            }

            function collectExistingVariationData() {
                const map = new Map();
                Array.from(rowsBody.querySelectorAll('tr:not(.variation-empty-row)')).forEach(function (row) {
                    const attributesInput = row.querySelector('input[name="variation_attributes_json[]"]');
                    const key = variationValueMapKey(parseAttributes(attributesInput ? attributesInput.value : '[]'));
                    if (!key || key === '{}') {
                        return;
                    }

                    map.set(key, {
                        id: (row.querySelector('input[name="variation_id[]"]') || {}).value || '',
                        attributes_json: attributesInput ? attributesInput.value : '[]',
                        mrp: (row.querySelector('input[name="variation_mrp[]"]') || {}).value || defaults.mrp,
                        selling_price: (row.querySelector('input[name="variation_selling_price[]"]') || {}).value || defaults.sellingPrice,
                        stock_quantity: (row.querySelector('input[name="variation_stock[]"]') || {}).value || defaults.stock,
                        image_path: (row.querySelector('input[name="existing_variation_image[]"]') || {}).value || ''
                    });
                });

                return map;
            }

            function variationLabel(items) {
                return normalizeVariationItems(items).map(function (item) {
                    return item.attribute_name.toLowerCase() + ': ' + item.value;
                }).join(' / ');
            }

            function getConfiguredVariationRows() {
                return Array.from(rowsBody.querySelectorAll('tr:not(.variation-empty-row)')).map(function (row) {
                    const attributesInput = row.querySelector('input[name="variation_attributes_json[]"]');
                    const attrs = normalizeVariationItems(parseAttributes(attributesInput ? attributesInput.value : '[]'));
                    return { row, attrs };
                }).filter(function (item) {
                    return item.attrs.length > 0;
                });
            }

            function findAttributeByName(pattern) {
                return options.find(function (attribute) {
                    return pattern.test(String(attribute.name || ''));
                }) || null;
            }

            function renderSavedAttributeGroups() {
                if (!savedGroups) return;

                const rows = getConfiguredVariationRows();
                if (!rows.length) {
                    savedGroups.innerHTML = '';
                    return;
                }

                const colorAttribute = findAttributeByName(/colou?r/i);
                const sizeAttribute = findAttributeByName(/size/i);
                const groups = new Map();
                const singleGroups = new Map();

                rows.forEach(function (entry) {
                    if (entry.attrs.length !== 1) return;

                    const item = entry.attrs[0];
                    const key = String(item.attribute_id);
                    if (!singleGroups.has(key)) {
                        singleGroups.set(key, {
                            name: item.attribute_name,
                            sourceAttributeId: item.attribute_id,
                            sourceValueId: '',
                            targetAttributeId: item.attribute_id,
                            values: [],
                            isSingle: true
                        });
                    }

                    if (!singleGroups.get(key).values.includes(item.value)) {
                        singleGroups.get(key).values.push(item.value);
                    }
                });

                if (singleGroups.size) {
                    singleGroups.forEach(function (group, key) {
                        groups.set('single:' + key, group);
                    });
                }

                if (colorAttribute && sizeAttribute) {
                    rows.forEach(function (entry) {
                        const color = entry.attrs.find(function (item) {
                            return String(item.attribute_id) === String(colorAttribute.id);
                        });
                        const size = entry.attrs.find(function (item) {
                            return String(item.attribute_id) === String(sizeAttribute.id);
                        });
                        if (!color || !size) return;

                        const key = String(color.value_id);
                        if (!groups.has('matrix:' + key)) {
                            groups.set('matrix:' + key, {
                                name: color.value,
                                sourceAttributeId: color.attribute_id,
                                sourceValueId: color.value_id,
                                targetAttributeId: size.attribute_id,
                                values: []
                            });
                        }
                        if (!groups.get('matrix:' + key).values.includes(size.value)) {
                            groups.get('matrix:' + key).values.push(size.value);
                        }
                    });
                }

                if (!groups.size) {
                    savedGroups.innerHTML = '';
                    return;
                }

                savedGroups.innerHTML = `
                    <div class="saved-attribute-groups-title">Selected Size and Colour</div>
                    ${Array.from(groups.values()).map(function (group) {
                        return `
                            <div class="saved-attribute-group" data-source-attribute-id="${escapeHtml(group.sourceAttributeId)}" data-source-value-id="${escapeHtml(group.sourceValueId)}" data-target-attribute-id="${escapeHtml(group.targetAttributeId)}" data-editor-type="${group.isSingle ? 'single' : 'matrix'}">
                                <div>
                                    <div class="saved-attribute-group-name">${escapeHtml(group.name)}</div>
                                    <div class="saved-attribute-group-values">${escapeHtml(group.values.join(', '))}</div>
                                </div>
                                <button type="button" class="saved-attribute-edit">Edit</button>
                            </div>
                        `;
                    }).join('')}
                `;
            }

            function renderEmptyVariationRow() {
                if (!hasVariations.checked || rowsBody.querySelector('tr:not(.variation-empty-row)')) {
                    const emptyRow = rowsBody.querySelector('.variation-empty-row');
                    if (emptyRow) emptyRow.remove();
                    renderSavedAttributeGroups();
                    return;
                }

                rowsBody.innerHTML = '<tr class="variation-empty-row"><td colspan="6">Select attributes above and click "Sync Combinations"</td></tr>';
                renderSavedAttributeGroups();
            }

            function addVariationRow(data) {
                const emptyRow = rowsBody.querySelector('.variation-empty-row');
                if (emptyRow) emptyRow.remove();

                const attributes = normalizeVariationItems(data.attributes || parseAttributes(data.attributes_json));
                const label = variationLabel(attributes) || data.label || '';
                const attributesJson = JSON.stringify(attributes.sort(function (a, b) {
                    return Number(a.attribute_id) - Number(b.attribute_id);
                }));
                const imagePath = data.image_path || '';
                const row = document.createElement('tr');
                row.dataset.variationKey = variationKey(attributes);
                row.dataset.variationValueMap = variationValueMapKey(attributes);

                row.innerHTML = `
                    <td>
                        <span class="variation-label-line">
                            <strong>${escapeHtml(label)}</strong>
                            <span class="variation-status-badge ${data.isNew ? 'new' : 'added'}">${data.isNew ? 'New' : 'Added'}</span>
                        </span>
                        <input type="hidden" name="variation_id[]" value="${escapeHtml(data.id || '')}">
                        <input type="hidden" name="variation_label[]" value="${escapeHtml(label)}">
                        <input type="hidden" name="variation_attributes_json[]" value="${escapeHtml(attributesJson)}">
                    </td>
                    <td><input type="number" class="form-control" name="variation_mrp[]" step="1" min="0" value="${escapeHtml(priceValue(data.mrp, defaults.mrp))}"></td>
                    <td><input type="number" class="form-control" name="variation_selling_price[]" step="1" min="0" value="${escapeHtml(priceValue(data.selling_price, defaults.sellingPrice))}"></td>
                    <td><input type="number" class="form-control" name="variation_stock[]" min="0" value="${escapeHtml(data.stock_quantity || defaults.stock)}"></td>
                    <td>
                        <div class="variation-image-field">
                            <input type="hidden" name="existing_variation_image[]" value="${escapeHtml(imagePath)}">
                            <input type="file" class="form-control variation-file-input" name="variation_image[]" accept="image/*">
                            ${imagePath ? `<span class="variation-current-image" title="Current image saved"><img src="${escapeHtml(imageUrl(imagePath))}" alt=""></span>` : ''}
                        </div>
                    </td>
                    <td>
                        <button type="button" class="remove-variation-row-btn" title="Remove variation">&times;</button>
                    </td>
                `;

                row.querySelector('.remove-variation-row-btn').addEventListener('click', function () {
                    row.remove();
                    renderEmptyVariationRow();
                    renderSavedAttributeGroups();
                });

                row.querySelector('.variation-file-input').addEventListener('change', function () {
                    updateVariationImagePreview(this);
                });

                rowsBody.appendChild(row);
                renderSavedAttributeGroups();
            }

            function getRowsForCombination(sourceAttributeId, sourceValueId, targetAttributeId, targetValueId) {
                return getConfiguredVariationRows().filter(function (entry) {
                    const sourceMatches = sourceValueId
                        ? entry.attrs.some(function (item) {
                            return String(item.attribute_id) === String(sourceAttributeId) && String(item.value_id) === String(sourceValueId);
                        })
                        : true;
                    const targetMatches = entry.attrs.some(function (item) {
                        return String(item.attribute_id) === String(targetAttributeId) && String(item.value_id) === String(targetValueId);
                    });
                    return sourceMatches && targetMatches;
                });
            }

            function openInlineCombinationEditor(groupEl) {
                document.querySelectorAll('.inline-combination-editor').forEach(function (editor) {
                    editor.remove();
                });

                const sourceAttributeId = groupEl.dataset.sourceAttributeId;
                const sourceValueId = groupEl.dataset.sourceValueId || '';
                const targetAttributeId = groupEl.dataset.targetAttributeId;
                const editorType = groupEl.dataset.editorType || 'matrix';
                const sourceAttribute = getAttributeById(sourceAttributeId);
                const sourceValue = sourceAttribute ? getValueById(sourceAttribute, sourceValueId) : null;
                const targetAttribute = getAttributeById(targetAttributeId);

                if (!targetAttribute || !targetAttribute.values || !targetAttribute.values.length) {
                    return;
                }

                const selectedValueIds = new Set();
                targetAttribute.values.forEach(function (value) {
                    if (getRowsForCombination(sourceAttributeId, sourceValueId, targetAttributeId, value.id).length > 0) {
                        selectedValueIds.add(String(value.id));
                    }
                });

                const title = editorType === 'single'
                    ? targetAttribute.name
                    : (sourceValue ? sourceValue.value : groupEl.querySelector('.saved-attribute-group-name')?.textContent || '');
                const subtitle = editorType === 'single'
                    ? `Select ${targetAttribute.name} values`
                    : `Select ${targetAttribute.name.toLowerCase()} for this colour`;

                const editor = document.createElement('div');
                editor.className = 'inline-combination-editor';
                editor.dataset.sourceAttributeId = sourceAttributeId;
                editor.dataset.sourceValueId = sourceValueId;
                editor.dataset.targetAttributeId = targetAttributeId;
                editor.dataset.editorType = editorType;
                editor.innerHTML = `
                    <div class="combination-editor-head">
                        <div>
                            <div class="combination-editor-title">${escapeHtml(title)}</div>
                            <div class="combination-editor-subtitle">${escapeHtml(subtitle)}</div>
                        </div>
                        <div class="combination-editor-actions">
                            <button type="button" class="btn btn-secondary combination-editor-cancel">Cancel</button>
                            <button type="button" class="btn btn-primary combination-editor-save">Save</button>
                        </div>
                    </div>
                    <div class="combination-editor-values">
                        ${targetAttribute.values.map(function (value) {
                            const checked = selectedValueIds.has(String(value.id));
                            return `
                                <label class="combination-editor-option ${checked ? 'is-selected' : ''}">
                                    <span><input type="checkbox" class="combination-editor-checkbox" value="${escapeHtml(value.id)}" ${checked ? 'checked' : ''}> ${escapeHtml(value.value)}</span>
                                    <span class="combination-editor-badge ${checked ? 'added' : ''}">${checked ? 'Added' : 'Add'}</span>
                                </label>
                            `;
                        }).join('')}
                    </div>
                    <div class="combination-editor-current"></div>
                `;

                groupEl.insertAdjacentElement('afterend', editor);

                function refreshCurrent() {
                    const selectedLabels = Array.from(editor.querySelectorAll('.combination-editor-checkbox:checked')).map(function (checkbox) {
                        return getValueById(targetAttribute, checkbox.value)?.value || checkbox.value;
                    });
                    editor.querySelector('.combination-editor-current').textContent = selectedLabels.length
                        ? `Selected sizes: ${selectedLabels.join(', ')}`
                        : 'No sizes selected';
                    editor.querySelectorAll('.combination-editor-option').forEach(function (option) {
                        const checked = option.querySelector('input').checked;
                        option.classList.toggle('is-selected', checked);
                        const badge = option.querySelector('.combination-editor-badge');
                        badge.classList.toggle('added', checked);
                        badge.textContent = checked ? 'Added' : 'Add';
                    });
                }

                editor.addEventListener('change', refreshCurrent);
                editor.querySelector('.combination-editor-cancel').addEventListener('click', function () {
                    editor.remove();
                });
                editor.querySelector('.combination-editor-save').addEventListener('click', function () {
                    saveInlineCombinationEditor(editor);
                    editor.remove();
                });
                refreshCurrent();
            }

            function saveInlineCombinationEditor(editor) {
                const sourceAttributeId = editor.dataset.sourceAttributeId;
                const sourceValueId = editor.dataset.sourceValueId || '';
                const targetAttributeId = editor.dataset.targetAttributeId;
                const editorType = editor.dataset.editorType || 'matrix';
                const sourceAttribute = getAttributeById(sourceAttributeId);
                const sourceValue = sourceAttribute ? getValueById(sourceAttribute, sourceValueId) : null;
                const targetAttribute = getAttributeById(targetAttributeId);
                const checked = new Set(Array.from(editor.querySelectorAll('.combination-editor-checkbox:checked')).map(function (checkbox) {
                    return String(checkbox.value);
                }));

                targetAttribute.values.forEach(function (targetValue) {
                    const rows = getRowsForCombination(sourceAttributeId, sourceValueId, targetAttributeId, targetValue.id);
                    const shouldExist = checked.has(String(targetValue.id));

                    if (!shouldExist) {
                        rows.forEach(function (entry) {
                            entry.row.remove();
                        });
                        return;
                    }

                    if (rows.length > 0) {
                        return;
                    }

                    const attributes = editorType === 'single'
                        ? [{
                            attribute_id: targetAttribute.id,
                            attribute_name: targetAttribute.name,
                            value_id: targetValue.id,
                            value: targetValue.value
                        }]
                        : [
                            {
                                attribute_id: targetAttribute.id,
                                attribute_name: targetAttribute.name,
                                value_id: targetValue.id,
                                value: targetValue.value
                            },
                            {
                                attribute_id: sourceAttribute.id,
                                attribute_name: sourceAttribute.name,
                                value_id: sourceValue.id,
                                value: sourceValue.value
                            }
                        ];

                    addVariationRow({
                        attributes,
                        mrp: defaults.mrp,
                        selling_price: defaults.sellingPrice,
                        stock_quantity: defaults.stock,
                        image_path: '',
                        isNew: true
                    });
                });

                renderEmptyVariationRow();
                renderSavedAttributeGroups();
                showVariationInfoMessage();
            }

            function syncCombinations() {
                const groups = selectedAttributeGroups();

                if (!groups.length || groups.some(function (group) { return !group.values.length; })) {
                    hideDuplicateMessage();
                    renderEmptyVariationRow();
                    return;
                }

                const existingData = collectExistingVariationData();
                const combinations = cartesianProduct(groups);
                const seenKeys = new Set(existingData.keys());

                let addedCount = 0;
                let duplicateCount = 0;
                combinations.forEach(function (items) {
                    const key = variationValueMapKey(items);
                    if (!key || seenKeys.has(key)) {
                        duplicateCount++;
                        return;
                    }

                    seenKeys.add(key);
                    addVariationRow(Object.assign({}, existingData.get(key) || {}, { attributes: items, isNew: true }));
                    addedCount++;
                });

                if (duplicateCount > 0) {
                    showDuplicateMessage();
                } else {
                    hideDuplicateMessage();
                }

                if (addedCount > 0 || duplicateCount > 0) {
                    selectorWrap.innerHTML = '';
                    selectorWrap.classList.add('d-none');
                }
                showVariationInfoMessage();
                renderEmptyVariationRow();
            }

            const renderedExistingKeys = [];
            existingVariations.forEach(function (variation) {
                const key = variationKey(parseAttributes(variation.attributes_json));
                if (!key || renderedExistingKeys.includes(key)) {
                    return;
                }

                addVariationRow({
                    label: variation.label,
                    id: variation.id || '',
                    attributes_json: variation.attributes_json,
                    mrp: variation.mrp,
                    selling_price: variation.selling_price,
                    stock_quantity: variation.stock_quantity,
                    image_path: variation.image_path,
                    isNew: false
                });

                if (key) {
                    renderedExistingKeys.push(key);
                }
            });

            addBtn.addEventListener('click', function () {
                hideDuplicateMessage();
                if (!hasVariations.checked) {
                    hasVariations.checked = true;
                    updateEnabledState();
                }
                selectorWrap.classList.remove('d-none');
                addAttributeRow('', '');
            });

            if (savedGroups) {
                savedGroups.addEventListener('click', function (event) {
                    const editButton = event.target.closest('.saved-attribute-edit');
                    if (!editButton) return;

                    const groupEl = editButton.closest('.saved-attribute-group');
                    if (groupEl) {
                        openInlineCombinationEditor(groupEl);
                    }
                });
            }

            syncBtn.addEventListener('click', syncCombinations);
            hasVariations.addEventListener('change', function () {
                hideDuplicateMessage();
                updateEnabledState();
                if (hasVariations.checked) {
                    showVariationInfoMessage();
                }
            });
            updateEnabledState();
            renderSavedAttributeGroups();
        }

        document.addEventListener('DOMContentLoaded', initProductVariationManager);
    </script>
    <?php
}
