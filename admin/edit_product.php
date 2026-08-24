<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';
require_once 'includes/product_variation_helpers.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

$pageTitle = 'Edit Product';
$success_message = '';
$error_message = '';
ensureProductVariationSchema($pdo);
ensureProductUnitSchema($pdo);
ensureProductUnitOptionsSchema($pdo);
ensureProductPackageQuantitySchema($pdo);
$return_to = $_GET['return_to'] ?? $_POST['return_to'] ?? 'products.php';
if (strpos($return_to, 'products.php') !== 0) {
    $return_to = 'products.php';
}
$encoded_return_to = urlencode($return_to);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['unit_action'])) {
    header('Content-Type: application/json');
    $unitAction = $_POST['unit_action'];
    $unitLabel = sanitizeProductUnitOption($_POST['unit_label'] ?? '');

    if ($unitLabel === '') {
        echo json_encode(['success' => false, 'message' => 'Please enter a unit name.']);
        exit;
    }

    try {
        if ($unitAction === 'add') {
            $stmt = $pdo->prepare("INSERT IGNORE INTO product_unit_options (label, is_default) VALUES (?, 0)");
            $stmt->execute([$unitLabel]);
            echo json_encode(['success' => true, 'label' => $unitLabel]);
            exit;
        }

        if ($unitAction === 'delete') {
            if (in_array($unitLabel, ['No.', 'Pair'], true)) {
                echo json_encode(['success' => false, 'message' => 'Default units cannot be deleted.']);
                exit;
            }

            $stmt = $pdo->prepare("DELETE FROM product_unit_options WHERE label = ? AND is_default = 0");
            $stmt->execute([$unitLabel]);
            echo json_encode(['success' => true]);
            exit;
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Unable to update unit options.']);
        exit;
    }

    echo json_encode(['success' => false, 'message' => 'Invalid unit action.']);
    exit;
}

// Get product ID
$product_id = intval($_GET['id'] ?? 0);
if (!$product_id) {
    header('Location: products.php');
    exit;
}

// Get product data
$stmt = $pdo->prepare("SELECT p.*, c.name as category_name 
                       FROM products p 
                       LEFT JOIN categories c ON p.category_id = c.id 
                       WHERE p.id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    header('Location: products.php');
    exit;
}

// Ensure all required fields exist with default values
$product = array_merge([
    'name' => '',
    'description' => '',
    'mrp' => 0,
    'selling_price' => 0,
    'category_id' => '',
    'stock_quantity' => 0,
    'package_quantity' => 1,
    'max_quantity_per_order' => null,
    'gst_rate' => 18.00,
    'shipping_charge' => null,
    'is_active' => 1,
    'is_featured' => 0,
    'is_discounted' => 0,
    'main_image' => '',
    'category_name' => '',
    'sku' => '',
    'hsn' => '',
    'pay_per_unit' => null,
    'unit_label' => 'No.'
], $product);

// Get product images
$stmt = $pdo->prepare("SELECT * FROM product_images WHERE product_id = ? ORDER BY sort_order, is_main DESC");
$stmt->execute([$product_id]);
$product_images = $stmt->fetchAll(PDO::FETCH_ASSOC);
$attributeOptions = getProductAttributeOptions($pdo);
$productVariations = getProductVariations($pdo, $product_id);
$selectedProductAttributes = getSelectedAttributesFromVariations($productVariations);

// Get all categories for dropdown with hierarchical structure
$allCategories = getAllCategoriesWithProductCount();
$categoryTree = buildCategoryTree($allCategories);

if (isset($_SESSION['success_message'])) {
    $success_message = $_SESSION['success_message'];
    unset($_SESSION['success_message']);
}

// Get current category with parent info
$currentCategory = getCategoryWithParent($product['category_id']);
// Determine parent category for dropdown
if ($currentCategory && $currentCategory['parent_id']) {
    $selectedParentCategoryId = $currentCategory['parent_id'];
    $selectedSubCategoryId = $currentCategory['id'];
} else {
    $selectedParentCategoryId = $currentCategory ? $currentCategory['id'] : '';
    $selectedSubCategoryId = '';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $slug = createSlug($name);
    $description = trim($_POST['description']);
    $mrp = floatval($_POST['mrp']);
    $selling_price = floatval($_POST['selling_price']);
    $pay_per_unit = isset($_POST['pay_per_unit']) && $_POST['pay_per_unit'] !== '' ? floatval($_POST['pay_per_unit']) : $selling_price;
    $unit_label = sanitizeProductUnitOption($_POST['unit_label'] ?? 'No.');
    $unit_label = $unit_label !== '' && $unit_label !== '__add_new_unit__' ? $unit_label : 'No.';
    
    // Handle category selection - use the selected category directly
    $category_id = intval($_POST['parent_category_id']);
    
    $stock_quantity = isset($_POST['stock_quantity']) ? (int)round((float)$_POST['stock_quantity']) : 0;
    $package_quantity = isset($_POST['package_quantity']) ? (int)round((float)$_POST['package_quantity']) : 1;
    $max_quantity_per_order = !empty($_POST['max_quantity_per_order']) ? (int)round((float)$_POST['max_quantity_per_order']) : null;
    $gst_type = 'sgst_cgst'; // Default GST type
    $gst_rate = floatval($_POST['gst_rate']);
    $sku = trim($_POST['sku']);
    $hsn = isset($_POST['hsn']) ? trim($_POST['hsn']) : null;

    // --- FIX: Ensure checkboxes are always set ---
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    $is_discounted = isset($_POST['is_discounted']) ? 1 : 0;
    // --- END FIX ---

    // Validation
    if (empty($name) || empty($description) || $mrp <= 0 || $selling_price <= 0) {
        $error_message = 'Please fill in all required fields with valid values.';
    } elseif (empty($_POST['parent_category_id'])) {
        $error_message = 'Please select a category.';
    } elseif ($selling_price > $mrp) {
        $error_message = 'Selling price cannot be greater than MRP.';
    } elseif ($gst_rate < 0 || $gst_rate > 100) {
        $error_message = 'GST rate must be between 0 and 100.';
    } elseif ($package_quantity < 1) {
        $error_message = 'Package quantity must be at least 1.';
    } else {
        try {
            $pdo->beginTransaction();

            // Calculate discount percentage
            $discount_percentage = calculateDiscountPercentage($mrp, $selling_price);

            // Update product
            $stmt = $pdo->prepare("UPDATE products SET name = ?, slug = ?, description = ?, mrp = ?, selling_price = ?, pay_per_unit = ?, unit_label = ?, discount_percentage = ?, gst_rate = ?, category_id = ?, stock_quantity = ?, package_quantity = ?, max_quantity_per_order = ?, is_active = ?, is_featured = ?, is_discounted = ?, sku = ?, hsn = ? WHERE id = ?");
            $stmt->execute([$name, $slug, $description, $mrp, $selling_price, $pay_per_unit, $unit_label, $discount_percentage, $gst_rate, $category_id, $stock_quantity, $package_quantity, $max_quantity_per_order, $is_active, $is_featured, $is_discounted, $sku, $hsn, $product_id]);

            // Handle main image upload
            if (isset($_FILES['main_image']) && $_FILES['main_image']['error'] === UPLOAD_ERR_OK) {
                $main_image_path = uploadImage($_FILES['main_image'], 'products');
                if ($main_image_path) {
                    $stmt = $pdo->prepare("UPDATE products SET main_image = ? WHERE id = ?");
                    $stmt->execute([$main_image_path, $product_id]);
                }
            }

            // Handle additional images
            if (isset($_FILES['images']) && is_array($_FILES['images']['name'])) {
                $sort_orders = $_POST['sort_order'] ?? [];
                
                for ($i = 0; $i < count($_FILES['images']['name']); $i++) {
                    if ($_FILES['images']['error'][$i] === UPLOAD_ERR_OK) {
                        $file = [
                            'name' => $_FILES['images']['name'][$i],
                            'type' => $_FILES['images']['type'][$i],
                            'tmp_name' => $_FILES['images']['tmp_name'][$i],
                            'error' => $_FILES['images']['error'][$i],
                            'size' => $_FILES['images']['size'][$i]
                        ];
                        
                        $image_path = uploadImage($file, 'products');
                        if ($image_path) {
                            $sort_order = isset($sort_orders[$i]) ? intval($sort_orders[$i]) : $i + 1;
                            $is_main = ($i === 0 && empty($_FILES['main_image']['name'])) ? 1 : 0;
                            
                            $stmt = $pdo->prepare("INSERT INTO product_images (product_id, image_path, is_main, sort_order) VALUES (?, ?, ?, ?)");
                            $stmt->execute([$product_id, $image_path, $is_main, $sort_order]);
                        }
                    }
                }
            }

            // Handle image deletions
            if (isset($_POST['delete_images']) && is_array($_POST['delete_images'])) {
                foreach ($_POST['delete_images'] as $image_id) {
                    $stmt = $pdo->prepare("DELETE FROM product_images WHERE id = ? AND product_id = ?");
                    $stmt->execute([$image_id, $product_id]);
                }
            }

            $variationsChanged = haveProductVariationsChanged($pdo, $product_id);
            $savedVariationCount = saveProductVariations($pdo, $product_id);

            $pdo->commit();
            $_SESSION['success_message'] = ($variationsChanged && $savedVariationCount > 0)
                ? 'Product updated successfully! Product variations saved successfully.'
                : 'Product updated successfully!';
            header('Location: edit_product.php?id=' . $product_id . '&return_to=' . $encoded_return_to);
            exit;
            
        } catch (Exception $e) {
            $pdo->rollBack();
            $error_message = 'Error updating product: ' . $e->getMessage();
        }
    }
}

// Helper functions
function createSlug($string) {
    $string = html_entity_decode($string, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $string = strip_tags($string);
    $slug = strtolower(trim($string));
    $slug = preg_replace('/[^a-z0-9-]/', '-', $slug);
    $slug = preg_replace('/-+/', '-', $slug);
    return trim($slug, '-');
}

function uploadImage($file, $folder) {
    $upload_dir = "../uploads/$folder/";
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    
    $allowed_types = ['image/jpeg', 'image/png', 'image/webp'];
    if (!in_array($file['type'], $allowed_types)) {
        return false;
    }
    
    $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid() . '.' . $file_extension;
    $filepath = $upload_dir . $filename;
    
    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        return "uploads/$folder/" . $filename;
    }
    
    return false;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - EverythingB2C</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="assets/css/admin.css" rel="stylesheet">
    <style>
        .product-form-page {
            background: #f6f7fb;
            color: #000;
        }

        .product-form-page,
        .product-form-page label,
        .product-form-page .form-label,
        .product-form-page .form-check-label,
        .product-form-page .form-control,
        .product-form-page .form-select,
        .product-form-page select,
        .product-form-page textarea,
        .product-form-page .description-toolbar,
        .product-form-page .description-toolbar select,
        .product-form-page .description-toolbar button,
        .product-form-page .description-content,
        .product-form-page .form-text {
            color: #000 !important;
        }

        .product-form-page h1 {
            font-size: 30px;
            font-weight: 500;
            color: #000;
        }

        .product-form-header {
            gap: 18px;
        }

        .product-form-header h1 {
            flex: 1 1 auto;
            min-width: 0;
        }

        .product-form-actions {
            flex: 0 0 auto;
        }

        .product-form-actions .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
            white-space: nowrap;
        }

        .product-form-page .btn {
            border-radius: 4px;
            font-size: 15px;
            font-weight: 500;
            min-height: 38px;
        }

        .product-form-page .card {
            border: 1px solid #d9dee7;
            border-radius: 4px;
            box-shadow: none;
        }

        .product-form-page .card-body {
            padding: 20px;
        }

        .product-form-page h5 {
            font-size: 22px;
            font-weight: 500;
            color: #000;
            margin-bottom: 20px !important;
        }

        .product-form-page .form-label {
            font-size: 15px;
            font-weight: 500;
            color: #000;
            margin-bottom: 8px;
        }

        .product-form-page .form-control,
        .product-form-page .form-select,
        .product-form-page select {
            min-height: 42px;
            border: 1px solid #cfd6df;
            border-radius: 4px;
            color: #000;
            font-size: 15px;
            font-weight: 500;
        }

        .product-form-page input[type="number"] {
            appearance: textfield;
            -moz-appearance: textfield;
        }

        .product-form-page input[type="number"]::-webkit-outer-spin-button,
        .product-form-page input[type="number"]::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        .product-form-page textarea.form-control {
            min-height: 280px;
        }

        .product-form-page .description-editor {
            border: 1px solid #cfd6df;
            border-radius: 8px;
            overflow: hidden;
            background: #fff;
        }

        .product-form-page .description-toolbar {
            min-height: 58px;
            display: flex;
            align-items: center;
            gap: 16px;
            padding: 10px 24px;
            border-bottom: 1px solid #cfd6df;
            color: #000;
        }

        .product-form-page .description-toolbar select {
            width: 150px;
            min-height: 34px;
            border: 0;
            color: #000;
            font-size: 15px;
            background-color: transparent;
        }

        .product-form-page .description-toolbar button {
            border: 0;
            background: transparent;
            color: #000;
            padding: 2px;
            font-size: 17px;
            line-height: 1;
        }

        .product-form-page .description-content {
            border: 0;
            border-radius: 0;
            min-height: 360px;
            padding: 24px;
            box-shadow: none;
            color: #000;
            font-size: 15px;
            font-weight: 500;
            outline: 0;
        }

        .product-form-page .description-content:empty::before {
            content: attr(data-placeholder);
            color: #000;
        }

        .product-form-page .description-content.is-invalid {
            box-shadow: inset 0 0 0 1px #dc3545;
        }

        .product-form-page .description-content h1 {
            font-size: 30px;
            font-weight: 700;
            margin: 0 0 14px;
        }

        .product-form-page .description-content h2 {
            font-size: 26px;
            font-weight: 700;
            margin: 0 0 12px;
        }

        .product-form-page .description-content h3 {
            font-size: 22px;
            font-weight: 700;
            margin: 0 0 10px;
        }

        .product-form-page .description-content h4 {
            font-size: 18px;
            font-weight: 700;
            margin: 0 0 8px;
        }

        .product-form-page .description-source {
            display: none;
        }

        .product-form-page .form-text {
            color: #000;
            font-size: 13px;
            font-weight: 500;
        }

        .product-form-page .unit-help-text {
            white-space: nowrap;
            font-size: 12px;
        }

        .product-form-page .unit-select-control {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .product-form-page .unit-select-control .form-select {
            flex: 1 1 auto;
            min-width: 0;
        }

        .product-form-page .unit-delete-btn {
            width: 42px;
            min-width: 42px;
            height: 42px;
            padding: 0;
            display: none;
            align-items: center;
            justify-content: center;
        }

        .product-form-page .unit-delete-btn.is-visible {
            display: inline-flex;
        }

        .unit-modal .modal-content {
            border: 0;
            border-radius: 8px;
            box-shadow: 0 18px 45px rgba(15, 23, 42, 0.22);
        }

        .unit-modal .modal-header,
        .unit-modal .modal-footer {
            border-color: #e2e8f0;
        }

        .unit-modal .modal-title {
            color: #111827;
            font-size: 18px;
            font-weight: 600;
        }

        .unit-modal .modal-body {
            color: #374151;
            font-size: 14px;
        }

        .unit-modal .unit-modal-icon {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #eef4ff;
            color: #0d6efd;
            margin-bottom: 12px;
        }

        .unit-modal .unit-modal-error {
            display: none;
            color: #dc3545;
            font-size: 13px;
            margin-top: 8px;
        }

        .product-form-page .form-control-plaintext {
            min-height: 42px;
            border: 1px solid #cfd6df;
            border-radius: 4px;
            padding: 8px 12px;
            color: #000;
            font-size: 15px;
            font-weight: 500;
        }

        .product-form-page .image-upload-tile {
            position: relative;
            width: 150px;
            min-height: 160px;
            border: 2px dashed #c9d4e3;
            border-radius: 14px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 10px;
            color: #000;
            cursor: pointer;
            background: #f8fafc;
            text-align: center;
            font-size: 15px;
            font-weight: 500;
            overflow: hidden;
        }

        .product-form-page .image-upload-tile i {
            color: #98a4b6;
            font-size: 30px;
        }

        .product-form-page .image-upload-tile.has-image i,
        .product-form-page .image-upload-tile.has-image span {
            display: none;
        }

        .product-form-page .image-upload-tile img {
            width: 100%;
            height: 120px;
            object-fit: cover;
            display: none;
            border-radius: 4px;
        }

        .product-form-page .image-upload-tile.has-image img {
            display: block;
        }

        .product-form-page .feature-image-wrap,
        .product-form-page .gallery-image-wrap {
            position: relative;
            display: inline-block;
        }

        .product-form-page .remove-image-preview {
            position: absolute;
            top: -12px;
            right: -12px;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            border: 0;
            background: #ff4848;
            color: #fff;
            display: none;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.18);
        }

        .product-form-page .feature-image-wrap.has-image .remove-image-preview,
        .product-form-page .gallery-image-wrap.has-image .remove-image-preview {
            display: inline-flex;
        }

        .product-form-page .gallery-upload-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            align-items: flex-start;
        }

        .product-form-page .image-upload-input {
            position: absolute;
            width: 1px;
            height: 1px;
            opacity: 0;
            pointer-events: none;
        }

        .product-form-page .img-preview {
            max-width: 150px;
            max-height: 150px;
            object-fit: contain;
            border: 1px solid #d9dee7;
            border-radius: 4px;
            margin-top: 10px;
        }

        .product-form-page .gallery-add-btn {
            border: 0;
            background: transparent;
            color: #0d6efd;
            padding: 0;
            font-size: 15px;
            font-weight: 500;
        }

        .product-form-page .was-validated .form-control:valid,
        .product-form-page .was-validated .form-select:valid,
        .product-form-page .form-control.is-valid,
        .product-form-page .form-select.is-valid {
            border-color: #cfd6df;
            background-image: none;
            padding-right: 12px;
            box-shadow: none;
        }
    </style>
    <?php renderProductVariationAssets(); ?>
</head>
<body>
    <div class="everythingb2c-admin-container">
        <!-- Sidebar -->
        <?php include 'includes/sidebar.php'; ?>

        <!-- Main Content -->
        <div class="everythingb2c-main-content">
            <!-- Header -->
            <?php include 'includes/header.php'; ?>

            <!-- Edit Product Content -->
            <div class="everythingb2c-dashboard-content product-form-page">
                <div class="container-fluid">
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-start product-form-header">
                                <h1 class="h3 mb-0">Edit Product: <?php echo cleanProductName($product['name']); ?></h1>
                                <div class="d-flex gap-2 product-form-actions">
                                    <a href="../product.php?slug=<?php echo urlencode($product['slug']); ?>" class="btn btn-outline-primary" target="_blank">
                                        <i class="fas fa-eye"></i> View Page
                                    </a>
                                    <a href="<?php echo htmlspecialchars($return_to); ?>" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left"></i> Back to Products
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php if ($success_message): ?>
                        <div class="alert product-save-success-alert no-success-icon"><?php echo htmlspecialchars($success_message); ?></div>
                    <?php endif; ?>

                    <?php if ($error_message): ?>
                        <div class="alert alert-danger"><?php echo htmlspecialchars($error_message); ?></div>
                    <?php endif; ?>

                    <div class="card">
                        <div class="card-body">
                            <form method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
                                <input type="hidden" name="return_to" value="<?php echo htmlspecialchars($return_to); ?>">
                                <div class="row">
                                    <!-- Basic Information -->
                                    <div class="col-md-8">
                                        <h5 class="mb-3">Basic Information</h5>
                                        
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <label for="edit_name" class="form-label">Product Name *</label>
                                                <input type="text" class="form-control" id="edit_name" name="name" value="<?php echo htmlspecialchars($product['name'], ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="parent_category_id" class="form-label">Category *</label>
                                                <select class="form-control form-select" id="parent_category_id" name="parent_category_id" required>
                                                    <option value="">Select Category</option>
                                                    <?php 
                                                    function displayCategoryOptions($categories, $level = 0, $selectedId = null) {
                                                        foreach ($categories as $category) {
                                                            $indent = str_repeat('— ', $level);
                                                            $selected = ($selectedId == $category['id']) ? 'selected' : '';
                                                            echo '<option value="' . $category['id'] . '" ' . $selected . '>';
                                                            echo $indent . htmlspecialchars($category['name']);
                                                            echo '</option>';
                                                            if (!empty($category['children'])) {
                                                                displayCategoryOptions($category['children'], $level + 1, $selectedId);
                                                            }
                                                        }
                                                    }
                                                    displayCategoryOptions($categoryTree, 0, $product['category_id']);
                                                    ?>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <div class="col-md-3">
                                                <label for="mrp" class="form-label">MRP (₹) *</label>
                                                <input type="number" class="form-control" id="mrp" name="mrp" step="1" min="0" value="<?php echo htmlspecialchars(formatAdminNumberInput($product['mrp'])); ?>" required>
                                                <div class="invalid-feedback">Please provide a valid MRP.</div>
                                            </div>
                                            <div class="col-md-3">
                                                <label for="selling_price" class="form-label">Selling Price (₹) *</label>
                                                <input type="number" class="form-control" id="selling_price" name="selling_price" step="1" min="0" value="<?php echo htmlspecialchars(formatAdminNumberInput($product['selling_price'])); ?>" required>
                                                <div class="invalid-feedback">Please provide a valid selling price.</div>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label">Pay / Unit (₹)</label>
                                                <input type="number" class="form-control" name="pay_per_unit" step="1" min="0" placeholder="e.g. 49" value="<?php echo htmlspecialchars(formatAdminNumberInput($product['pay_per_unit'] ?? '')); ?>">
                                                <div class="form-text unit-help-text">Shown as ₹ price / selected unit.</div>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label">Unit</label>
                                                <?php $selectedUnitLabel = $product['unit_label'] ?? 'No.'; ?>
                                                <?php $unitOptions = getProductUnitOptions($pdo, $selectedUnitLabel); ?>
                                                <div class="unit-select-control">
                                                    <select class="form-control form-select unit-label-select" name="unit_label">
                                                        <?php foreach ($unitOptions as $unitOption): ?>
                                                            <?php $unitLabelOption = $unitOption['label']; ?>
                                                            <option value="<?php echo htmlspecialchars($unitLabelOption); ?>" data-default="<?php echo !empty($unitOption['is_default']) ? '1' : '0'; ?>" <?php echo $selectedUnitLabel === $unitLabelOption ? 'selected' : ''; ?>><?php echo htmlspecialchars($unitLabelOption); ?></option>
                                                        <?php endforeach; ?>
                                                        <option value="__add_new_unit__" data-default="1">+ Add new unit</option>
                                                    </select>
                                                    <button type="button" class="btn btn-outline-danger unit-delete-btn" title="Delete unit">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <div class="col-md-4">
                                                <label for="gst_rate" class="form-label">GST Rate (%)</label>
                                                <select class="form-control form-select" id="gst_rate" name="gst_rate" required>
                                                    <option value="0" <?php echo ($product['gst_rate'] == '0') ? 'selected' : ''; ?>>0%</option>
                                                    <option value="5" <?php echo ($product['gst_rate'] == '5') ? 'selected' : ''; ?>>5%</option>
                                                    <option value="12" <?php echo ($product['gst_rate'] == '12') ? 'selected' : ''; ?>>12%</option>
                                                    <option value="18" <?php echo ($product['gst_rate'] == '18') ? 'selected' : ''; ?>>18%</option>
                                                </select>
                                                <div class="form-text">Select GST rate for record keeping</div>
                                                <div class="invalid-feedback">Please select a GST rate.</div>
                                            </div>
                                            <div class="col-md-4">
                                                <label for="sku" class="form-label">SKU *</label>
                                                <input type="text" class="form-control" id="sku" name="sku" value="<?php echo htmlspecialchars($product['sku']); ?>" required>
                                                <div class="invalid-feedback">Please provide a unique SKU.</div>
                                            </div>
                                            <div class="col-md-4">
                                                <label for="hsn" class="form-label">HSN Code</label>
                                                <input type="text" class="form-control" id="hsn" name="hsn" maxlength="20" value="<?php echo htmlspecialchars($product['hsn'] ?? ''); ?>">
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <div class="col-md-3">
                                                <label for="stock_quantity" class="form-label">Stock Quantity *</label>
                                                <input type="number" class="form-control" id="stock_quantity" name="stock_quantity" min="0" step="1" value="<?php echo htmlspecialchars($product['stock_quantity']); ?>" required>
                                                <div class="invalid-feedback">Please provide stock quantity.</div>
                                            </div>
                                            <div class="col-md-4">
                                                <label for="package_quantity" class="form-label">Package Quantity</label>
                                                <input type="number" class="form-control" id="package_quantity" name="package_quantity" min="1" step="1" value="<?php echo htmlspecialchars($product['package_quantity'] ?? 1); ?>">
                                                <div class="form-text">Buy in multiples of this quantity</div>
                                            </div>
                                            <div class="col-md-5">
                                                <label for="max_quantity_per_order" class="form-label">Max Quantity Per Order</label>
                                                <input type="number" class="form-control" id="max_quantity_per_order" name="max_quantity_per_order" min="1" step="1" value="<?php echo htmlspecialchars($product['max_quantity_per_order'] ?? ''); ?>" placeholder="Leave empty for no limit">
                                                <div class="form-text">Maximum quantity a customer can order at once</div>
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <div class="col-md-4">
                                                <label class="form-label">Discount % (Auto)</label>
                                                <div class="form-control-plaintext" id="discount_display">
                                                    <?php 
                                                        $discount = $product['mrp'] > 0 ? (($product['mrp'] - $product['selling_price']) / $product['mrp']) * 100 : 0;
                                                        echo number_format($discount, 2) . '%';
                                                    ?>
                                                </div>
                                                <div class="form-text">Calculated automatically</div>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">Total with Shipping</label>
                                                <div class="form-control-plaintext" id="total_with_shipping_display">
                                                    ₹<?php 
                                                        $shipping = $product['shipping_charge'] ?? 0;
                                                        echo formatAdminNumberInput($product['selling_price'] + $shipping); 
                                                    ?>
                                                </div>
                                                <div class="form-text">Price + GST + Shipping</div>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="description" class="form-label">Description *</label>
                                            <div class="description-editor">
                                                <div class="description-toolbar">
                                                    <select class="description-block-format" aria-label="Description format">
                                                        <option value="P">Normal</option>
                                                        <option value="H1">Heading 1</option>
                                                        <option value="H2">Heading 2</option>
                                                        <option value="H3">Heading 3</option>
                                                        <option value="H4">Heading 4</option>
                                                    </select>
                                                    <button type="button" data-format="bold"><i class="fas fa-bold"></i></button>
                                                    <button type="button" data-format="italic"><i class="fas fa-italic"></i></button>
                                                    <button type="button" data-format="underline"><i class="fas fa-underline"></i></button>
                                                    <button type="button" data-format="strike"><i class="fas fa-strikethrough"></i></button>
                                                    <button type="button" data-format="ordered-list"><i class="fas fa-list-ol"></i></button>
                                                    <button type="button" data-format="unordered-list"><i class="fas fa-list-ul"></i></button>
                                                    <button type="button" data-format="outdent"><i class="fas fa-outdent"></i></button>
                                                    <button type="button" data-format="indent"><i class="fas fa-indent"></i></button>
                                                    <button type="button" data-format="align-left"><i class="fas fa-align-left"></i></button>
                                                    <button type="button" data-format="align-center"><i class="fas fa-align-center"></i></button>
                                                    <button type="button" data-format="link"><i class="fas fa-link"></i></button>
                                                    <button type="button" data-format="image"><i class="fas fa-image"></i></button>
                                                    <button type="button" data-format="clear"><i class="fas fa-text-slash"></i></button>
                                                </div>
                                                <div class="description-content" contenteditable="true" data-placeholder="Write product description..."><?php echo $product['description']; ?></div>
                                                <textarea class="description-source" id="description" name="description"><?php echo htmlspecialchars($product['description']); ?></textarea>
                                            </div>
                                            <div class="invalid-feedback">Please provide a description.</div>
                                        </div>

                                        <div class="row mb-3">
                                            <div class="col-md-4">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="edit_is_active" name="is_active" 
                                                           <?php echo $product['is_active'] ? 'checked' : ''; ?>>
                                                    <label class="form-check-label" for="edit_is_active">Active</label>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="edit_is_featured" name="is_featured" 
                                                           <?php echo $product['is_featured'] ? 'checked' : ''; ?>>
                                                    <label class="form-check-label" for="edit_is_featured">Featured</label>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="edit_is_discounted" name="is_discounted" 
                                                           <?php echo $product['is_discounted'] ? 'checked' : ''; ?>>
                                                    <label class="form-check-label" for="edit_is_discounted">Discounted</label>
                                                </div>
                                            </div>
                                        </div>

                                        <?php renderProductAttributesSection($attributeOptions, $selectedProductAttributes, $productVariations, $product); ?>

                                    </div>

                                    <!-- Images -->
                                    <div class="col-md-4">
                                        <h5 class="mb-3">Images</h5>

                                        <div class="mb-3">
                                            <label class="form-label">Feature Image</label>
                                            <div class="form-text mb-2">Upload Feature Image</div>
                                            <?php $mainImageSrc = $product['main_image'] ? '../' . $product['main_image'] : ''; ?>
                                            <div class="feature-image-wrap <?php echo $mainImageSrc ? 'has-image' : ''; ?>" id="featureImageWrap">
                                                <label for="main_image" class="image-upload-tile <?php echo $mainImageSrc ? 'has-image' : ''; ?>" id="featureImageTile">
                                                    <i class="fas fa-cloud-upload-alt"></i>
                                                    <span>Select an image</span>
                                                    <img id="main_image_preview"
                                                         alt="Feature preview"
                                                         src="<?php echo htmlspecialchars($mainImageSrc); ?>"
                                                         data-original-src="<?php echo htmlspecialchars($mainImageSrc); ?>">
                                                </label>
                                                <button type="button" class="remove-image-preview" id="removeFeatureImage" aria-label="Remove feature image">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                            <input type="file" class="image-input image-upload-input" id="main_image" name="main_image" accept="image/*">
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Gallery</label>
                                            <div id="imageContainer" class="gallery-upload-grid">
                                                <?php if (!empty($product_images)): ?>
                                                    <?php foreach ($product_images as $image): ?>
                                                        <div class="gallery-image-wrap has-image">
                                                            <label class="image-upload-tile has-image">
                                                                <img src="../<?php echo $image['image_path']; ?>" alt="Product Image">
                                                            </label>
                                                            <button type="button" class="remove-image-preview" onclick="markCurrentGalleryImageForDelete(this)" aria-label="Remove gallery image">
                                                                <i class="fas fa-times"></i>
                                                            </button>
                                                            <input class="form-check-input d-none" type="checkbox"
                                                                   name="delete_images[]" value="<?php echo $image['id']; ?>"
                                                                   id="delete_<?php echo $image['id']; ?>">
                                                        </div>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </div>
                                            <button type="button" class="gallery-add-btn" id="addImageBtn">
                                                <i class="fas fa-plus"></i> Add gallery images
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <hr>

                                <div class="d-flex justify-content-end gap-2">
                                    <a href="<?php echo htmlspecialchars($return_to); ?>" class="btn btn-secondary">Cancel</a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Update Product
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade unit-modal" id="unitAddModal" tabindex="-1" aria-labelledby="unitAddModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="unitAddModalLabel">Add Unit</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="unit-modal-icon"><i class="fas fa-ruler"></i></div>
                    <label for="unitAddInput" class="form-label">Unit name</label>
                    <input type="text" class="form-control" id="unitAddInput" maxlength="20" placeholder="Example: Gram">
                    <div class="unit-modal-error" id="unitAddError"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="unitAddConfirmBtn">Add Unit</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade unit-modal" id="unitDeleteModal" tabindex="-1" aria-labelledby="unitDeleteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="unitDeleteModalLabel">Delete Unit</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="unit-modal-icon text-danger" style="background:#fff1f2;"><i class="fas fa-trash"></i></div>
                    <p class="mb-0">Delete <strong id="unitDeleteName"></strong> from the unit dropdown?</p>
                    <div class="unit-modal-error" id="unitDeleteError"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="unitDeleteConfirmBtn">Delete Unit</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/admin.js"></script>
    <script>
        function initDescriptionEditors() {
            document.querySelectorAll('.description-editor').forEach(function(editor) {
                const content = editor.querySelector('.description-content');
                const source = editor.querySelector('.description-source');
                const form = editor.closest('form');

                function syncDescription() {
                    source.value = content.innerHTML.trim();
                }

                function focusEditor() {
                    content.focus();
                }

                const blockFormat = editor.querySelector('.description-block-format');
                if (blockFormat) {
                    blockFormat.addEventListener('change', function() {
                        focusEditor();
                        document.execCommand('formatBlock', false, this.value);
                        syncDescription();
                    });
                }

                editor.querySelectorAll('[data-format]').forEach(function(button) {
                    button.addEventListener('mousedown', function(e) {
                        e.preventDefault();
                    });

                    button.addEventListener('click', function() {
                        focusEditor();
                        const format = this.dataset.format;

                        if (format === 'link') {
                            const url = prompt('Enter link URL');
                            if (url) document.execCommand('createLink', false, url);
                        } else if (format === 'image') {
                            const url = prompt('Enter image URL');
                            if (url) document.execCommand('insertImage', false, url);
                        } else if (format === 'clear') {
                            document.execCommand('removeFormat', false, null);
                        } else {
                            const commands = {
                                bold: 'bold',
                                italic: 'italic',
                                underline: 'underline',
                                strike: 'strikeThrough',
                                'ordered-list': 'insertOrderedList',
                                'unordered-list': 'insertUnorderedList',
                                outdent: 'outdent',
                                indent: 'indent',
                                'align-left': 'justifyLeft',
                                'align-center': 'justifyCenter'
                            };
                            document.execCommand(commands[format], false, null);
                        }

                        syncDescription();
                    });
                });

                content.addEventListener('input', syncDescription);
                content.addEventListener('input', function() {
                    content.classList.remove('is-invalid');
                });
                content.addEventListener('blur', syncDescription);

                if (form) {
                    form.addEventListener('submit', function(e) {
                        syncDescription();
                        if (!content.textContent.trim()) {
                            e.preventDefault();
                            content.focus();
                            content.classList.add('is-invalid');
                        }
                    });
                }
            });
        }

        initDescriptionEditors();

        // Image preview functionality for main image
        document.getElementById('main_image').addEventListener('change', function() {
            const file = this.files[0];
            const preview = document.getElementById('main_image_preview');
            const tile = document.getElementById('featureImageTile');
            const wrap = document.getElementById('featureImageWrap');
            
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    tile.classList.add('has-image');
                    wrap.classList.add('has-image');
                };
                reader.readAsDataURL(file);
            } else if (preview.dataset.originalSrc) {
                preview.src = preview.dataset.originalSrc;
                tile.classList.add('has-image');
                wrap.classList.add('has-image');
            } else {
                preview.src = '';
                tile.classList.remove('has-image');
                wrap.classList.remove('has-image');
            }
        });

        document.getElementById('removeFeatureImage').addEventListener('click', function() {
            const input = document.getElementById('main_image');
            const preview = document.getElementById('main_image_preview');
            input.value = '';

            if (preview.dataset.originalSrc) {
                preview.src = preview.dataset.originalSrc;
                document.getElementById('featureImageTile').classList.add('has-image');
                document.getElementById('featureImageWrap').classList.add('has-image');
                return;
            }

            preview.src = '';
            document.getElementById('featureImageTile').classList.remove('has-image');
            document.getElementById('featureImageWrap').classList.remove('has-image');
        });

        // Add Another Image functionality
        let galleryImageIndex = 0;
        document.getElementById('addImageBtn').addEventListener('click', function() {
            const container = document.getElementById('imageContainer');
            const newField = document.createElement('div');
            const inputId = 'gallery_image_' + galleryImageIndex++;
            newField.className = 'image-field gallery-image-wrap';
            newField.innerHTML = `
                <label for="${inputId}" class="image-upload-tile">
                    <i class="fas fa-cloud-upload-alt"></i>
                    <span>Select an image</span>
                    <img alt="Gallery preview">
                </label>
                <button type="button" class="remove-image-preview" onclick="removeImageField(this)" aria-label="Remove gallery image">
                    <i class="fas fa-times"></i>
                </button>
                <input type="file" class="image-input image-upload-input" id="${inputId}" name="images[]" accept="image/*">
            `;
            container.appendChild(newField);
        });

        // Remove image field functionality
        function removeImageField(button) {
            const field = button.closest('.image-field');
            const input = field.querySelector('input[type="file"]');
            const tile = field.querySelector('.image-upload-tile');
            const preview = field.querySelector('img');
            input.value = '';
            preview.src = '';
            tile.classList.remove('has-image');
            field.classList.remove('has-image');
        }

        function markCurrentGalleryImageForDelete(button) {
            const field = button.closest('.gallery-image-wrap');
            const checkbox = field.querySelector('input[type="checkbox"]');
            checkbox.checked = true;
            field.style.display = 'none';
        }

        // Image preview for additional images
        document.addEventListener('change', function(e) {
            if (e.target.classList.contains('image-input') && e.target.name === 'images[]') {
                const input = e.target;
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const parent = input.closest('.image-field');
                        const preview = parent.querySelector('.image-upload-tile img');
                        const tile = parent.querySelector('.image-upload-tile');
                        preview.src = e.target.result;
                        tile.classList.add('has-image');
                        parent.classList.add('has-image');
                    };
                    reader.readAsDataURL(file);
                }
            }
        });



        // Calculate discount percentage
        document.getElementById('mrp').addEventListener('input', calculateDiscount);
        document.getElementById('selling_price').addEventListener('input', calculateDiscount);

        function calculateDiscount() {
            const mrp = parseFloat(document.getElementById('mrp').value) || 0;
            const sellingPrice = parseFloat(document.getElementById('selling_price').value) || 0;
            
            if (mrp > 0 && sellingPrice > 0) {
                const discount = ((mrp - sellingPrice) / mrp) * 100;
                document.getElementById('discount_display').textContent = discount.toFixed(2) + '%';
            } else {
                document.getElementById('discount_display').textContent = '0.00%';
            }
        }

        function formatAdminDisplayNumber(value) {
            return (Number(value) || 0).toFixed(2).replace(/\.?0+$/, '');
        }

        // Calculate total with shipping
        document.getElementById('selling_price').addEventListener('input', calculateTotalWithShipping);

        function calculateTotalWithShipping() {
            const sellingPrice = parseFloat(document.getElementById('selling_price').value) || 0;
            const shippingChargeInput = document.getElementById('shipping_charge');
            const shippingCharge = shippingChargeInput ? (parseFloat(shippingChargeInput.value) || 0) : 0;
            
            const total = sellingPrice + shippingCharge;
            
            document.getElementById('total_with_shipping_display').textContent = '₹' + formatAdminDisplayNumber(total);
        }

        function setUnitModalError(element, message) {
            element.textContent = message || '';
            element.style.display = message ? 'block' : 'none';
        }

        function openUnitAddModal() {
            const modalEl = document.getElementById('unitAddModal');
            const input = document.getElementById('unitAddInput');
            const error = document.getElementById('unitAddError');
            const confirmBtn = document.getElementById('unitAddConfirmBtn');
            const modal = bootstrap.Modal.getOrCreateInstance(modalEl);

            input.value = '';
            setUnitModalError(error, '');
            modal.show();

            setTimeout(function() {
                input.focus();
            }, 180);

            return new Promise(function(resolve) {
                function cleanup(value) {
                    confirmBtn.removeEventListener('click', onConfirm);
                    input.removeEventListener('keydown', onKeydown);
                    modalEl.removeEventListener('hidden.bs.modal', onHidden);
                    resolve(value);
                }

                function onConfirm() {
                    const value = input.value.trim().replace(/\s+/g, ' ').slice(0, 20);
                    if (!value) {
                        setUnitModalError(error, 'Please enter a unit name.');
                        input.focus();
                        return;
                    }

                    modal.hide();
                    cleanup(value);
                }

                function onKeydown(event) {
                    if (event.key === 'Enter') {
                        event.preventDefault();
                        onConfirm();
                    }
                }

                function onHidden() {
                    cleanup('');
                }

                confirmBtn.addEventListener('click', onConfirm);
                input.addEventListener('keydown', onKeydown);
                modalEl.addEventListener('hidden.bs.modal', onHidden, { once: true });
            });
        }

        function openUnitDeleteModal(unitName) {
            const modalEl = document.getElementById('unitDeleteModal');
            const nameEl = document.getElementById('unitDeleteName');
            const error = document.getElementById('unitDeleteError');
            const confirmBtn = document.getElementById('unitDeleteConfirmBtn');
            const modal = bootstrap.Modal.getOrCreateInstance(modalEl);

            nameEl.textContent = unitName;
            setUnitModalError(error, '');
            modal.show();

            return new Promise(function(resolve) {
                function cleanup(value) {
                    confirmBtn.removeEventListener('click', onConfirm);
                    modalEl.removeEventListener('hidden.bs.modal', onHidden);
                    resolve(value);
                }

                function onConfirm() {
                    modal.hide();
                    cleanup(true);
                }

                function onHidden() {
                    cleanup(false);
                }

                confirmBtn.addEventListener('click', onConfirm);
                modalEl.addEventListener('hidden.bs.modal', onHidden, { once: true });
            });
        }

        document.querySelectorAll('.unit-select-control').forEach(function(control) {
            const select = control.querySelector('.unit-label-select');
            const deleteBtn = control.querySelector('.unit-delete-btn');
            const addNewValue = '__add_new_unit__';
            let previousValue = select.value || 'No.';

            function getSelectedOption() {
                return select.options[select.selectedIndex];
            }

            function updateDeleteButton() {
                const selected = getSelectedOption();
                const canDelete = selected && selected.value !== addNewValue && selected.dataset.default !== '1';
                deleteBtn.disabled = !canDelete;
                deleteBtn.classList.toggle('is-visible', !!canDelete);
            }

            function postUnitAction(action, label) {
                const formData = new FormData();
                formData.append('unit_action', action);
                formData.append('unit_label', label);

                return fetch(window.location.href, {
                    method: 'POST',
                    body: formData,
                    credentials: 'same-origin'
                }).then(function(response) {
                    return response.json();
                });
            }

            select.addEventListener('focus', function() {
                if (select.value !== addNewValue) {
                    previousValue = select.value;
                }
            });

            select.addEventListener('change', function() {
                if (select.value !== addNewValue) {
                    previousValue = select.value;
                    updateDeleteButton();
                    return;
                }

                openUnitAddModal().then(function(newUnit) {
                    if (!newUnit) {
                        select.value = previousValue;
                        updateDeleteButton();
                        return;
                    }

                    postUnitAction('add', newUnit).then(function(data) {
                        if (!data.success) {
                            alert(data.message || 'Unable to add unit.');
                            select.value = previousValue;
                            updateDeleteButton();
                            return;
                        }

                        let option = Array.from(select.options).find(function(item) {
                            return item.value.toLowerCase() === data.label.toLowerCase();
                        });

                        if (!option) {
                            option = new Option(data.label, data.label);
                            option.dataset.default = '0';
                            select.insertBefore(option, select.querySelector('option[value="' + addNewValue + '"]'));
                        }

                        option.selected = true;
                        previousValue = option.value;
                        updateDeleteButton();
                    }).catch(function() {
                        alert('Unable to add unit.');
                        select.value = previousValue;
                        updateDeleteButton();
                    });
                }).catch(function() {
                    select.value = previousValue;
                    updateDeleteButton();
                });
            });

            deleteBtn.addEventListener('click', function() {
                const selected = getSelectedOption();
                if (!selected || selected.dataset.default === '1' || selected.value === addNewValue) {
                    return;
                }

                openUnitDeleteModal(selected.value).then(function(confirmed) {
                    if (!confirmed) {
                        return;
                    }

                    postUnitAction('delete', selected.value).then(function(data) {
                        if (!data.success) {
                            alert(data.message || 'Unable to delete unit.');
                            return;
                        }

                        selected.remove();
                        select.value = 'No.';
                        previousValue = select.value;
                        updateDeleteButton();
                    }).catch(function() {
                        alert('Unable to delete unit.');
                    });
                });
            });

            updateDeleteButton();
        });

        document.querySelectorAll('input[type="number"]').forEach(function(input) {
            input.addEventListener('wheel', function(event) {
                if (document.activeElement === input) {
                    event.preventDefault();
                }
            }, { passive: false });
        });

        // Form validation
        (function() {
            'use strict';
            window.addEventListener('load', function() {
                var forms = document.getElementsByClassName('needs-validation');
                var validation = Array.prototype.filter.call(forms, function(form) {
                    form.addEventListener('submit', function(event) {
                        if (form.checkValidity() === false) {
                            event.preventDefault();
                            event.stopPropagation();
                        }
                        form.classList.add('was-validated');
                    }, false);
                });
            }, false);
        })();
    </script>
</body>
</html> 
