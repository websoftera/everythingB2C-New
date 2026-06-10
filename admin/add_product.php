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

$pageTitle = 'Add New Product';
$success_message = '';
$error_message = '';

// Get all categories for dropdown with hierarchical structure
$allCategories = getAllCategoriesWithProductCount();
$categoryTree = buildCategoryTree($allCategories);
ensureProductVariationSchema($pdo);
ensureProductUnitSchema($pdo);
ensureProductPackageQuantitySchema($pdo);
$attributeOptions = getProductAttributeOptions($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $slug = createSlug($name);
    $description = trim($_POST['description']);
    $mrp = floatval($_POST['mrp']);
    $selling_price = floatval($_POST['selling_price']);
    $pay_per_unit = isset($_POST['pay_per_unit']) && $_POST['pay_per_unit'] !== '' ? floatval($_POST['pay_per_unit']) : $selling_price;
    $unit_label = in_array($_POST['unit_label'] ?? 'No.', ['No.', 'Pair'], true) ? $_POST['unit_label'] : 'No.';
    
    // Handle category selection - use the selected category directly
    $category_id = intval($_POST['parent_category_id']);
    
    $stock_quantity = intval($_POST['stock_quantity']);
    $package_quantity = isset($_POST['package_quantity']) ? intval($_POST['package_quantity']) : 1;
    $max_quantity_per_order = !empty($_POST['max_quantity_per_order']) ? intval($_POST['max_quantity_per_order']) : null;
    $gst_type = 'sgst_cgst'; // Default GST type
    $gst_rate = floatval($_POST['gst_rate']);
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    $is_discounted = isset($_POST['is_discounted']) ? 1 : 0;
    $sku = trim($_POST['sku']);
    $hsn = isset($_POST['hsn']) ? trim($_POST['hsn']) : null;

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

            // Insert product
            $stmt = $pdo->prepare("INSERT INTO products (name, slug, description, mrp, selling_price, pay_per_unit, unit_label, discount_percentage, gst_type, gst_rate, category_id, stock_quantity, package_quantity, max_quantity_per_order, is_active, is_featured, is_discounted, sku, hsn) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$name, $slug, $description, $mrp, $selling_price, $pay_per_unit, $unit_label, $discount_percentage, $gst_type, $gst_rate, $category_id, $stock_quantity, $package_quantity, $max_quantity_per_order, $is_active, $is_featured, $is_discounted, $sku, $hsn]);
            
            $product_id = $pdo->lastInsertId();

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

            $savedVariationCount = saveProductVariations($pdo, $product_id);

            $pdo->commit();
            $success_message = $savedVariationCount > 0
                ? 'Product added successfully! Product variations saved successfully.'
                : 'Product added successfully!';
            
            // Redirect to products list after a short delay
            header("refresh:2;url=products.php");
            
        } catch (Exception $e) {
            $pdo->rollBack();
            $error_message = 'Error adding product: ' . $e->getMessage();
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

            <!-- Add Product Content -->
            <div class="everythingb2c-dashboard-content product-form-page">
                <div class="container-fluid">
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center">
                                <h1 class="h3 mb-0">Add New Product</h1>
                                <div class="d-flex gap-2">
                                    <a href="../products.php" class="btn btn-outline-primary" target="_blank">
                                        <i class="fas fa-eye"></i> View Page
                                    </a>
                                    <a href="products.php" class="btn btn-secondary">
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
                                <div class="row">
                                    <!-- Basic Information -->
                                    <div class="col-md-8">
                                        <h5 class="mb-3">Basic Information</h5>
                                        
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <label for="name" class="form-label">Product Name *</label>
                                                <input type="text" class="form-control" id="name" name="name" required>
                                                <div class="invalid-feedback">Please provide a product name.</div>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="parent_category_id" class="form-label">Category *</label>
                                                <select class="form-control form-select" id="parent_category_id" name="parent_category_id" required>
                                                    <option value="">Select Category</option>
                                                    <?php 
                                                    // Display categories in hierarchical structure
                                                    function displayCategoryOptions($categories, $level = 0) {
                                                        foreach ($categories as $category) {
                                                            $indent = str_repeat('— ', $level);
                                                            $selected = (isset($_POST['parent_category_id']) && $_POST['parent_category_id'] == $category['id']) ? 'selected' : '';
                                                            echo '<option value="' . $category['id'] . '" ' . $selected . '>';
                                                            echo $indent . htmlspecialchars($category['name']);
                                                            echo '</option>';
                                                            
                                                            if (!empty($category['children'])) {
                                                                displayCategoryOptions($category['children'], $level + 1);
                                                            }
                                                        }
                                                    }
                                                    displayCategoryOptions($categoryTree);
                                                    ?>
                                                </select>
                                                <div class="invalid-feedback">Please select a category.</div>
                                            </div>
                                        </div>



                                        <div class="row mb-3">
                                            <div class="col-md-3">
                                                <label for="mrp" class="form-label">MRP (₹) *</label>
                                                <input type="number" class="form-control" id="mrp" name="mrp" step="1" min="0" required>
                                                <div class="invalid-feedback">Please provide a valid MRP.</div>
                                            </div>
                                            <div class="col-md-3">
                                                <label for="selling_price" class="form-label">Selling Price (₹) *</label>
                                                <input type="number" class="form-control" id="selling_price" name="selling_price" step="1" min="0" required>
                                                <div class="invalid-feedback">Please provide a valid selling price.</div>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label">Pay / Unit (₹)</label>
                                                <input type="number" class="form-control" name="pay_per_unit" step="1" min="0" placeholder="e.g. 49" value="<?php echo htmlspecialchars(formatAdminNumberInput($_POST['pay_per_unit'] ?? '')); ?>">
                                                <div class="form-text unit-help-text">Shown as ₹ price / selected unit.</div>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label">Unit</label>
                                                <select class="form-control form-select" name="unit_label">
                                                    <?php $selectedUnitLabel = $_POST['unit_label'] ?? 'No.'; ?>
                                                    <option value="No." <?php echo $selectedUnitLabel === 'No.' ? 'selected' : ''; ?>>No.</option>
                                                    <option value="Pair" <?php echo $selectedUnitLabel === 'Pair' ? 'selected' : ''; ?>>Pair</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <div class="col-md-4">
                                                <label for="gst_rate" class="form-label">GST Rate (%)</label>
                                                <select class="form-control form-select" id="gst_rate" name="gst_rate" required>
                                                    <option value="0" <?php echo (isset($_POST['gst_rate']) && $_POST['gst_rate'] == '0') ? 'selected' : ''; ?>>0%</option>
                                                    <option value="5" <?php echo (isset($_POST['gst_rate']) && $_POST['gst_rate'] == '5') ? 'selected' : ''; ?>>5%</option>
                                                    <option value="12" <?php echo (isset($_POST['gst_rate']) && $_POST['gst_rate'] == '12') ? 'selected' : ''; ?>>12%</option>
                                                    <option value="18" <?php echo (isset($_POST['gst_rate']) && $_POST['gst_rate'] == '18') ? 'selected' : ''; ?>>18%</option>
                                                </select>
                                                <div class="form-text">Select GST rate for record keeping</div>
                                                <div class="invalid-feedback">Please select a GST rate.</div>
                                            </div>
                                            <div class="col-md-4">
                                                <label for="sku" class="form-label">SKU *</label>
                                                <input type="text" class="form-control" id="sku" name="sku" required>
                                                <div class="invalid-feedback">Please provide a unique SKU.</div>
                                            </div>
                                            <div class="col-md-4">
                                                <label for="hsn" class="form-label">HSN Code</label>
                                                <input type="text" class="form-control" id="hsn" name="hsn" maxlength="20" value="<?php echo isset($_POST['hsn']) ? htmlspecialchars($_POST['hsn']) : ''; ?>">
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <div class="col-md-3">
                                                <label for="stock_quantity" class="form-label">Stock Quantity *</label>
                                                <input type="number" class="form-control" id="stock_quantity" name="stock_quantity" min="0" required>
                                                <div class="invalid-feedback">Please provide stock quantity.</div>
                                            </div>
                                            <div class="col-md-4">
                                                <label for="package_quantity" class="form-label">Package Quantity</label>
                                                <input type="number" class="form-control" id="package_quantity" name="package_quantity" min="1" value="<?php echo htmlspecialchars($_POST['package_quantity'] ?? '1'); ?>">
                                                <div class="form-text">Customers can buy only multiples of this quantity</div>
                                            </div>
                                            <div class="col-md-5">
                                                <label for="max_quantity_per_order" class="form-label">Max Quantity Per Order</label>
                                                <input type="number" class="form-control" id="max_quantity_per_order" name="max_quantity_per_order" min="1" placeholder="Leave empty for no limit">
                                                <div class="form-text">Maximum quantity a customer can order at once</div>
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <div class="col-md-4">
                                                <label class="form-label">Discount % (Auto)</label>
                                                <div class="form-control-plaintext" id="discount_display">0.00%</div>
                                                <div class="form-text">Calculated automatically</div>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">Total with Shipping</label>
                                                <div class="form-control-plaintext" id="total_with_shipping_display">₹0</div>
                                                <div class="form-text">Price + GST + Shipping</div>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="description" class="form-label">Description *</label>
                                            <div class="description-editor">
                                                <div class="description-toolbar">
                                                    <select tabindex="-1">
                                                        <option>Normal</option>
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
                                                <div class="description-content" contenteditable="true" data-placeholder="Write product description..."></div>
                                                <textarea class="description-source" id="description" name="description"></textarea>
                                            </div>
                                            <div class="invalid-feedback">Please provide a description.</div>
                                        </div>

                                        <div class="row mb-3">
                                            <div class="col-md-4">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" checked>
                                                    <label class="form-check-label" for="is_active">Active</label>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured">
                                                    <label class="form-check-label" for="is_featured">Featured</label>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="is_discounted" name="is_discounted">
                                                    <label class="form-check-label" for="is_discounted">Discounted</label>
                                                </div>
                                            </div>
                                        </div>

                                        <?php renderProductAttributesSection($attributeOptions, [], [], [
                                            'mrp' => $_POST['mrp'] ?? 0,
                                            'selling_price' => $_POST['selling_price'] ?? 0,
                                            'stock_quantity' => $_POST['stock_quantity'] ?? 0,
                                            'has_variations' => isset($_POST['has_variations']) ? 1 : 0
                                        ]); ?>
                                    </div>

                                    <!-- Images -->
                                    <div class="col-md-4">
                                        <h5 class="mb-3">Images</h5>
                                        
                                        <div class="mb-3">
                                            <label class="form-label">Feature Image</label>
                                            <div class="form-text mb-2">Upload Feature Image</div>
                                            <div class="feature-image-wrap" id="featureImageWrap">
                                                <label for="main_image" class="image-upload-tile" id="featureImageTile">
                                                    <i class="fas fa-cloud-upload-alt"></i>
                                                    <span>Select an image</span>
                                                    <img id="main_image_preview" alt="Feature preview">
                                                </label>
                                                <button type="button" class="remove-image-preview" id="removeFeatureImage" aria-label="Remove feature image">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                            <input type="file" class="image-input image-upload-input" id="main_image" name="main_image" accept="image/*">
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Gallery</label>
                                            <div id="imageContainer" class="gallery-upload-grid"></div>
                                            <button type="button" class="gallery-add-btn" id="addImageBtn">
                                                <i class="fas fa-plus"></i> Add gallery images
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <hr>

                                <div class="d-flex justify-content-end gap-2">
                                    <a href="products.php" class="btn btn-secondary">Cancel</a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Save Product
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
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

        // Auto-calculate discount percentage
        document.getElementById('mrp').addEventListener('input', calculateDiscount);
        document.getElementById('selling_price').addEventListener('input', calculateDiscount);

        function calculateDiscount() {
            const mrp = parseFloat(document.getElementById('mrp').value) || 0;
            const sellingPrice = parseFloat(document.getElementById('selling_price').value) || 0;
            
            if (mrp > 0 && sellingPrice > 0) {
                const discount = ((mrp - sellingPrice) / mrp) * 100;
                document.getElementById('discount_display').textContent = discount.toFixed(2) + '%';
                if (discount > 0) {
                    document.getElementById('is_discounted').checked = true;
                }
            } else {
                document.getElementById('discount_display').textContent = '0.00%';
            }
        }
            

        function formatAdminDisplayNumber(value) {
            return (Number(value) || 0).toFixed(2).replace(/\.?0+$/, '');
        }


        // Calculate total with shipping
        document.getElementById('selling_price').addEventListener('input', calculateTotalWithShipping);
        document.getElementById('gst_rate').addEventListener('input', calculateTotalWithShipping);

        function calculateTotalWithShipping() {
            const sellingPrice = parseFloat(document.getElementById('selling_price').value) || 0;
            
            document.getElementById('total_with_shipping_display').textContent = '₹' + formatAdminDisplayNumber(sellingPrice);
        }



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
