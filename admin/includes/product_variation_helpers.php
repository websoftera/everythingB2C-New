<?php

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

    $pdo->prepare("DELETE FROM product_variations WHERE product_id = ?")->execute([$productId]);

    $attributeStmt = $pdo->prepare("
        INSERT INTO product_variation_attributes
            (variation_id, attribute_id, attribute_value_id)
        VALUES (?, ?, ?)
    ");

    $savedCount = 0;
    foreach ($variationRows as $variationRow) {
        $stmt->execute([
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

    return $savedCount;
}

function renderProductAttributesSection($attributeOptions, $selectedAttributes = [], $variations = [], $product = []) {
    $hasVariations = !empty($product['has_variations']) || !empty($variations);
    $baseMrp = isset($product['mrp']) ? (float)$product['mrp'] : 0;
    $baseSellingPrice = isset($product['selling_price']) ? (float)$product['selling_price'] : 0;
    $baseStock = isset($product['stock_quantity']) ? (int)$product['stock_quantity'] : 0;
    ?>
    <div class="product-attributes-panel mt-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0">Product Attributes</h5>
            <button type="button" class="btn btn-outline-primary btn-sm" id="addProductAttributeBtn">
                <i class="fas fa-plus"></i> Add Attribute
            </button>
        </div>

        <div class="form-check form-switch mb-3">
            <input class="form-check-input" type="checkbox" id="hasVariations" name="has_variations" <?php echo $hasVariations ? 'checked' : ''; ?>>
            <label class="form-check-label fw-bold" for="hasVariations">This product has variations (Price/Image based on attributes)</label>
        </div>

        <div id="variationControls" class="<?php echo $hasVariations ? '' : 'd-none'; ?>">
            <div id="productAttributeSelectorWrap"></div>
            <div id="variationDuplicateMessage" class="variation-duplicate-message d-none">
                This variation already exists. Please select a different attribute value.
            </div>

            <div class="d-flex justify-content-between align-items-center mt-4 mb-2 product-variation-title-row">
                <h5 class="mb-0">Product Variations</h5>
                <button type="button" class="btn btn-primary btn-sm" id="syncVariationCombinationsBtn">
                    <i class="fas fa-sync-alt"></i> Sync Combinations
                </button>
            </div>

            <div class="table-responsive product-variations-table-wrap">
                <table class="table table-bordered align-middle product-variations-table">
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
                'attributes_json' => $variation['attributes_json'],
                'mrp' => $variation['mrp'],
                'selling_price' => $variation['selling_price'],
                'stock_quantity' => $variation['stock_quantity'],
                'image_path' => $variation['image_path']
            ];
        }, $variations)); ?>;
        window.productVariationDefaults = {
            mrp: <?php echo json_encode(rtrim(rtrim(number_format($baseMrp, 2, '.', ''), '0'), '.')); ?>,
            sellingPrice: <?php echo json_encode(rtrim(rtrim(number_format($baseSellingPrice, 2, '.', ''), '0'), '.')); ?>,
            stock: <?php echo json_encode((string)$baseStock); ?>
        };
    </script>
    <?php
}

function renderProductVariationAssets() {
    ?>
    <style>
        .product-form-page .product-attributes-panel {
            border-top: 1px solid #d9dee7;
            padding-top: 22px;
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
            grid-template-columns: minmax(220px, 366px) minmax(260px, 445px) 40px;
            gap: 20px;
            align-items: end;
            max-width: 960px;
            margin-bottom: 12px;
            padding: 12px 14px;
            border: 1px solid #cfd6df;
            border-radius: 8px;
            background: #f8fafc;
        }

        .product-form-page .product-attribute-field label {
            color: #667085 !important;
            font-size: 14px;
            font-weight: 500;
            margin: 0 0 6px;
        }

        .product-form-page .product-attribute-row .form-select {
            min-height: 38px;
            font-size: 14px;
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
            font-size: 18px;
            line-height: 1;
            padding: 0 0 6px;
        }

        .product-form-page .product-variations-table {
            margin-bottom: 0;
            width: 100%;
            max-width: 100%;
            table-layout: fixed;
        }

        .product-form-page .product-variations-table th {
            font-size: 15px;
            font-weight: 700;
            vertical-align: bottom;
            background: #f8fafc;
            border-bottom: 2px solid #222;
        }

        .product-form-page .product-variations-table th:nth-child(1),
        .product-form-page .product-variations-table td:nth-child(1) {
            width: 23%;
        }

        .product-form-page .product-variations-table th:nth-child(2),
        .product-form-page .product-variations-table td:nth-child(2),
        .product-form-page .product-variations-table th:nth-child(3),
        .product-form-page .product-variations-table td:nth-child(3) {
            width: 14.5%;
        }

        .product-form-page .product-variations-table th:nth-child(4),
        .product-form-page .product-variations-table td:nth-child(4) {
            width: 10%;
        }

        .product-form-page .product-variations-table th:nth-child(5),
        .product-form-page .product-variations-table td:nth-child(5) {
            width: 32%;
        }

        .product-form-page .product-variations-table th:nth-child(6),
        .product-form-page .product-variations-table td:nth-child(6) {
            width: 6%;
        }

        .product-form-page .product-variations-table td {
            font-size: 14px;
            font-weight: 600;
            vertical-align: middle;
            overflow-wrap: anywhere;
        }

        .product-form-page .product-variations-table td:first-child strong {
            display: inline-block;
            font-size: 12px;
            line-height: 1.35;
        }

        .product-form-page .product-variations-table input[type="number"],
        .product-form-page .product-variations-table input[type="text"] {
            height: 38px;
            min-height: 38px;
            padding: 6px 10px;
        }

        .product-form-page .variation-file-input {
            height: 38px;
            min-height: 38px;
            min-width: 0;
            flex: 1 1 auto;
            font-size: 13px;
            line-height: 24px;
            padding: 0 8px 0 0;
            overflow: hidden;
        }

        .product-form-page .variation-file-input::file-selector-button {
            height: 36px;
            margin: 0 10px 0 0;
            padding: 0 10px;
            border: 0;
            border-right: 1px solid #cfd6df;
            background: #eef1f5;
            color: #000;
        }

        .product-form-page .variation-current-image {
            flex: 0 0 38px;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 38px;
            height: 38px;
            color: transparent;
            font-size: 0;
            line-height: 0;
        }

        .product-form-page .variation-current-image img {
            width: 38px;
            height: 38px;
            border: 1px solid #d9dee7;
            border-radius: 4px;
            object-fit: cover;
        }

        .product-form-page .variation-image-field {
            display: flex;
            align-items: center;
            gap: 6px;
            min-width: 0;
        }

        .product-form-page .variations-disabled {
            opacity: 0.55;
            pointer-events: none;
        }

        .product-form-page .variation-empty-row td {
            height: 90px;
            color: #667085;
            font-size: 17px;
            font-weight: 500;
            text-align: center;
        }

        .product-form-page .product-variation-title-row {
            max-width: 960px;
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
            const addBtn = document.getElementById('addProductAttributeBtn');
            const syncBtn = document.getElementById('syncVariationCombinationsBtn');
            const duplicateMsg = document.getElementById('variationDuplicateMessage');
            const tableWrap = document.querySelector('.product-variations-table-wrap');

            if (!hasVariations || !selectorWrap || !rowsBody || !addBtn || !syncBtn || !duplicateMsg) {
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

            function renderValueSelect(row, selectedValueId) {
                const attributeId = row.querySelector('.product-attribute-select').value;
                const valueSelect = row.querySelector('.product-attribute-value-select');
                const attribute = getAttributeById(attributeId);

                if (!attribute || !attribute.values.length) {
                    valueSelect.innerHTML = '<option value="">Select Value...</option>';
                    valueSelect.disabled = true;
                    return;
                }

                valueSelect.disabled = false;
                valueSelect.innerHTML = '<option value="">Select Value...</option>' + attribute.values.map(function (value) {
                    const selected = String(value.id) === String(selectedValueId || '') ? 'selected' : '';
                    return `<option value="${value.id}" ${selected}>${escapeHtml(value.value)}</option>`;
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
                        <select class="form-select product-attribute-value-select">
                            <option value="">Select Value...</option>
                        </select>
                    </div>
                    <button type="button" class="remove-product-attribute-btn" title="Remove attribute"><i class="fas fa-trash-alt"></i></button>
                `;
                selectorWrap.appendChild(row);

                if (attributeId) {
                    row.querySelector('.product-attribute-select').value = attributeId;
                }

                renderValueSelect(row, selectedValueId || '');

                row.querySelector('.product-attribute-select').addEventListener('change', function () {
                    hideDuplicateMessage();
                    renderValueSelect(row, '');
                });

                row.querySelector('.product-attribute-value-select').addEventListener('change', hideDuplicateMessage);

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
                    const valueId = row.querySelector('.product-attribute-value-select').value;
                    const attribute = getAttributeById(attributeId);
                    if (!attribute || !valueId) {
                        return;
                    }

                    const value = getValueById(attribute, valueId);
                    if (!value) {
                        return;
                    }

                    const normalizedValue = {
                        attribute_id: attribute.id,
                        attribute_name: attribute.name,
                        value_id: value.id,
                        value: value.value
                    };

                    if (!groups.has(String(attribute.id))) {
                        groups.set(String(attribute.id), {
                            attribute_id: attribute.id,
                            attribute_name: attribute.name,
                            values: []
                        });
                    }

                    const group = groups.get(String(attribute.id));
                    if (!group.values.some(function (existingValue) {
                        return String(existingValue.value_id) === String(value.id);
                    })) {
                        group.values.push(normalizedValue);
                    }
                });

                return Array.from(groups.values()).sort(function (a, b) {
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
                    return item.attribute_name + ': ' + item.value;
                }).join(' / ');
            }

            function renderEmptyVariationRow() {
                if (!hasVariations.checked || rowsBody.querySelector('tr:not(.variation-empty-row)')) {
                    const emptyRow = rowsBody.querySelector('.variation-empty-row');
                    if (emptyRow) emptyRow.remove();
                    return;
                }

                rowsBody.innerHTML = '<tr class="variation-empty-row"><td colspan="6">Select attributes above and click "Sync Combinations"</td></tr>';
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
                        <strong>${escapeHtml(label)}</strong>
                        <input type="hidden" name="variation_label[]" value="${escapeHtml(label)}">
                        <input type="hidden" name="variation_attributes_json[]" value="${escapeHtml(attributesJson)}">
                    </td>
                    <td><input type="number" class="form-control" name="variation_mrp[]" step="0.01" min="0" value="${escapeHtml(priceValue(data.mrp, defaults.mrp))}"></td>
                    <td><input type="number" class="form-control" name="variation_selling_price[]" step="0.01" min="0" value="${escapeHtml(priceValue(data.selling_price, defaults.sellingPrice))}"></td>
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
                });

                row.querySelector('.variation-file-input').addEventListener('change', function () {
                    updateVariationImagePreview(this);
                });

                rowsBody.appendChild(row);
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
                const seenKeys = new Set();
                rowsBody.innerHTML = '';

                let duplicateCount = 0;
                combinations.forEach(function (items) {
                    const key = variationValueMapKey(items);
                    if (!key || seenKeys.has(key)) {
                        duplicateCount++;
                        return;
                    }

                    seenKeys.add(key);
                    addVariationRow(Object.assign({}, existingData.get(key) || {}, { attributes: items }));
                });

                if (duplicateCount > 0) {
                    showDuplicateMessage();
                } else {
                    hideDuplicateMessage();
                }

                selectorWrap.innerHTML = '';
                selectorWrap.classList.add('d-none');
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
                    attributes_json: variation.attributes_json,
                    mrp: variation.mrp,
                    selling_price: variation.selling_price,
                    stock_quantity: variation.stock_quantity,
                    image_path: variation.image_path
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

            syncBtn.addEventListener('click', syncCombinations);
            hasVariations.addEventListener('change', function () {
                hideDuplicateMessage();
                updateEnabledState();
            });
            updateEnabledState();
        }

        document.addEventListener('DOMContentLoaded', initProductVariationManager);
    </script>
    <?php
}
