<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

$pageTitle = 'Add New Product';
$success_message = '';
$error_message = '';

// Get categories for dropdown
$categories = getAllCategories();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $slug = createSlug($name);
    $description = trim($_POST['description']);
    $mrp = floatval($_POST['mrp']);
    $selling_price = floatval($_POST['selling_price']);
    $category_id = intval($_POST['category_id']);
    $stock_quantity = intval($_POST['stock_quantity']);
    $gst_type = $_POST['gst_type'];
    $gst_rate = floatval($_POST['gst_rate']);
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    $is_discounted = isset($_POST['is_discounted']) ? 1 : 0;
    $sku = trim($_POST['sku']);
    $hsn = isset($_POST['hsn']) ? trim($_POST['hsn']) : null;

    // Validation
    if (empty($name) || empty($description) || $mrp <= 0 || $selling_price <= 0) {
        $error_message = 'Please fill in all required fields with valid values.';
    } elseif ($selling_price > $mrp) {
        $error_message = 'Selling price cannot be greater than MRP.';
    } elseif ($gst_rate < 0 || $gst_rate > 100) {
        $error_message = 'GST rate must be between 0 and 100.';
    } else {
        try {
            $pdo->beginTransaction();

            // Calculate discount percentage
            $discount_percentage = calculateDiscountPercentage($mrp, $selling_price);

            // Insert product
            $stmt = $pdo->prepare("INSERT INTO products (name, slug, description, mrp, selling_price, discount_percentage, gst_type, gst_rate, category_id, stock_quantity, is_active, is_featured, is_discounted, sku, hsn) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$name, $slug, $description, $mrp, $selling_price, $discount_percentage, $gst_type, $gst_rate, $category_id, $stock_quantity, $is_active, $is_featured, $is_discounted, $sku, $hsn]);
            
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

            $pdo->commit();
            $success_message = 'Product added successfully!';
            
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
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/admin.css" rel="stylesheet">
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <?php include 'includes/sidebar.php'; ?>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Header -->
            <?php include 'includes/header.php'; ?>

            <!-- Add Product Content -->
            <div class="dashboard-content">
                <div class="container-fluid">
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center">
                                <h1 class="h3 mb-0">Add New Product</h1>
                                <a href="products.php" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Back to Products
                                </a>
                            </div>
                        </div>
                    </div>

                    <?php if ($success_message): ?>
                        <div class="alert alert-success"><?php echo htmlspecialchars($success_message); ?></div>
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
                                                <label for="category_id" class="form-label">Category *</label>
                                                <select class="form-control" id="category_id" name="category_id" required>
                                                    <option value="">Select Category</option>
                                                    <?php foreach ($categories as $category): ?>
                                                        <option value="<?php echo $category['id']; ?>">
                                                            <?php echo htmlspecialchars($category['name']); ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                                <div class="invalid-feedback">Please select a category.</div>
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <label for="sku" class="form-label">SKU *</label>
                                                <input type="text" class="form-control" id="sku" name="sku" required>
                                                <div class="invalid-feedback">Please provide a unique SKU.</div>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="description" class="form-label">Description *</label>
                                                <textarea class="form-control" id="description" name="description" rows="4" required></textarea>
                                                <div class="invalid-feedback">Please provide a description.</div>
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <div class="col-md-4">
                                                <label for="mrp" class="form-label">MRP (₹) *</label>
                                                <input type="number" class="form-control" id="mrp" name="mrp" step="0.01" min="0" required>
                                                <div class="invalid-feedback">Please provide a valid MRP.</div>
                                            </div>
                                            <div class="col-md-4">
                                                <label for="selling_price" class="form-label">Selling Price (₹) *</label>
                                                <input type="number" class="form-control" id="selling_price" name="selling_price" step="0.01" min="0" required>
                                                <div class="invalid-feedback">Please provide a valid selling price.</div>
                                            </div>
                                            <div class="col-md-4">
                                                <label for="stock_quantity" class="form-label">Stock Quantity *</label>
                                                <input type="number" class="form-control" id="stock_quantity" name="stock_quantity" min="0" required>
                                                <div class="invalid-feedback">Please provide stock quantity.</div>
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <div class="col-md-4">
                                                <label for="gst_rate" class="form-label">GST Rate (%) *</label>
                                                <input type="number" class="form-control" id="gst_rate" name="gst_rate" step="0.01" min="0" max="100" value="18.00" required>
                                                <div class="form-text">Enter GST rate as percentage (e.g., 18 for 18%)</div>
                                                <div class="invalid-feedback">Please provide a valid GST rate between 0 and 100.</div>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">GST Amount (Auto)</label>
                                                <div class="form-control-plaintext" id="gst_amount_display">
                                                    ₹0.00
                                                </div>
                                                <div class="form-text">Calculated automatically for reference</div>
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <div class="col-md-4">
                                                <label for="hsn" class="form-label">HSN Code</label>
                                                <input type="text" class="form-control" id="hsn" name="hsn" maxlength="20" value="<?php echo isset($_POST['hsn']) ? htmlspecialchars($_POST['hsn']) : ''; ?>">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">Total with Shipping</label>
                                                <div class="form-control-plaintext" id="total_with_shipping_display">
                                                    ₹0.00
                                                </div>
                                                <div class="form-text">Price + GST + Shipping</div>
                                            </div>
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
                                    </div>

                                    <!-- Images -->
                                    <div class="col-md-4">
                                        <h5 class="mb-3">Images</h5>
                                        
                                        <div class="mb-3">
                                            <label for="main_image" class="form-label">Main Image</label>
                                            <input type="file" class="form-control image-input" id="main_image" name="main_image" accept="image/*">
                                            <div class="mt-2">
                                                <img id="main_image_preview" class="img-preview" style="display: none;">
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Additional Images</label>
                                            <div id="imageContainer">
                                                <div class="image-field row mb-3">
                                                    <div class="col-12">
                                                        <input type="file" class="form-control image-input" name="images[]" accept="image/*">
                                                    </div>
                                                </div>
                                            </div>
                                            <button type="button" class="btn btn-outline-primary btn-sm" id="addImageBtn">
                                                <i class="fas fa-plus"></i> Add Another Image
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
        // Image preview functionality for main image
        document.getElementById('main_image').addEventListener('change', function() {
            const file = this.files[0];
            const preview = document.getElementById('main_image_preview');
            
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                };
                reader.readAsDataURL(file);
            } else {
                preview.style.display = 'none';
            }
        });

        // Add Another Image functionality
        document.getElementById('addImageBtn').addEventListener('click', function() {
            const container = document.getElementById('imageContainer');
            const newField = document.createElement('div');
            newField.className = 'image-field row mb-3';
            newField.innerHTML = `
                <div class="col-11">
                    <input type="file" class="form-control image-input" name="images[]" accept="image/*">
                </div>
                <div class="col-1">
                    <button type="button" class="btn btn-danger btn-sm remove-image" onclick="removeImageField(this)">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;
            container.appendChild(newField);
        });

        // Remove image field functionality
        function removeImageField(button) {
            button.closest('.image-field').remove();
        }

        // Image preview for additional images
        document.addEventListener('change', function(e) {
            if (e.target.classList.contains('image-input') && e.target.name === 'images[]') {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const preview = document.createElement('img');
                        preview.src = e.target.result;
                        preview.className = 'img-preview mt-2';
                        preview.style.maxWidth = '100px';
                        
                        const parent = e.target.closest('.image-field');
                        const existingPreview = parent.querySelector('.img-preview');
                        if (existingPreview) {
                            existingPreview.remove();
                        }
                        parent.appendChild(preview);
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
                if (discount > 0) {
                    document.getElementById('is_discounted').checked = true;
                }
            }
            
            // Calculate GST amount
            calculateGSTAmount();
        }

        // Calculate GST amount
        document.getElementById('gst_rate').addEventListener('input', calculateGSTAmount);
        document.getElementById('selling_price').addEventListener('input', calculateGSTAmount);

        function calculateGSTAmount() {
            const sellingPrice = parseFloat(document.getElementById('selling_price').value) || 0;
            const gstRate = parseFloat(document.getElementById('gst_rate').value) || 0;
            
            if (sellingPrice > 0 && gstRate > 0) {
                const gstAmount = (sellingPrice * gstRate) / 100;
                document.getElementById('gst_amount_display').textContent = '₹' + gstAmount.toFixed(2);
            } else {
                document.getElementById('gst_amount_display').textContent = '₹0.00';
            }
            
            // Calculate total with shipping
            calculateTotalWithShipping();
        }

        // Calculate total with shipping
        document.getElementById('selling_price').addEventListener('input', calculateTotalWithShipping);
        document.getElementById('gst_rate').addEventListener('input', calculateTotalWithShipping);

        function calculateTotalWithShipping() {
            const sellingPrice = parseFloat(document.getElementById('selling_price').value) || 0;
            const gstRate = parseFloat(document.getElementById('gst_rate').value) || 0;
            
            const gstAmount = (sellingPrice * gstRate) / 100;
            const total = sellingPrice + gstAmount;
            
            document.getElementById('total_with_shipping_display').textContent = '₹' + total.toFixed(2);
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