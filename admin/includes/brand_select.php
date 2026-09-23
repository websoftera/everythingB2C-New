<div class="mb-3">
    <label for="brand_id" class="form-label">Brand (optional)</label>
    <select name="brand_id" id="brand_id" class="form-select">
        <option value="">No brand</option>
        <?php $selectedBrand = $_POST['brand_id'] ?? ($product['brand_id'] ?? ''); ?>
        <?php foreach ($brands as $brand): ?>
            <option value="<?php echo (int)$brand['id']; ?>" <?php echo (string)$selectedBrand === (string)$brand['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($brand['name'], ENT_QUOTES, 'UTF-8'); ?></option>
        <?php endforeach; ?>
    </select>
    <?php if (!brandsSchemaReady($pdo)): ?>
        <small class="text-muted">Run the brands migration to enable brand selection.</small>
    <?php endif; ?>
</div>
