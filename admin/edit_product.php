<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

$pageTitle = 'Edit Product';
$success_message = '';
$error_message = '';

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
    'max_quantity_per_order' => null,
    'gst_rate' => 18.00,
    'shipping_charge' => null,
    'is_active' => 1,
    'is_featured' => 0,
    'is_discounted' => 0,
    'main_image' => '',
    'category_name' => '',
    'sku' => '',
    'hsn' => ''
], $product);

// Get product images
$stmt = $pdo->prepare("SELECT * FROM product_images WHERE product_id = ? ORDER BY sort_order, is_main DESC");
$stmt->execute([$product_id]);
$product_images = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get all categories for dropdown with hierarchical structure
$allCategories = getAllCategoriesWithProductCount();
$categoryTree = buildCategoryTree($allCategories);

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
    
    // Handle category selection - use the selected category directly
    $category_id = intval($_POST['parent_category_id']);
    
    $stock_quantity = intval($_POST['stock_quantity']);
    $max_quantity_per_order = !empty($_POST['max_quantity_per_order']) ? intval($_POST['max_quantity_per_order']) : null;
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
    } else {
        try {
            $pdo->beginTransaction();

            // Calculate discount percentage
            $discount_percentage = calculateDiscountPercentage($mrp, $selling_price);

            // Update product
            $stmt = $pdo->prepare("UPDATE products SET name = ?, slug = ?, description = ?, mrp = ?, selling_price = ?, discount_percentage = ?, gst_rate = ?, category_id = ?, stock_quantity = ?, max_quantity_per_order = ?, is_active = ?, is_featured = ?, is_discounted = ?, sku = ?, hsn = ? WHERE id = ?");
            $stmt->execute([$name, $slug, $description, $mrp, $selling_price, $discount_percentage, $gst_rate, $category_id, $stock_quantity, $max_quantity_per_order, $is_active, $is_featured, $is_discounted, $sku, $hsn, $product_id]);

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

            $pdo->commit();
            $_SESSION['success_message'] = 'Product updated successfully!';
            header('Location: edit_product.php?id=' . $product_id);
            exit;
            
        } catch (Exception $e) {
            $pdo->rollBack();
            $error_message = 'Error updating product: ' . $e->getMessage();
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
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="assets/css/admin.css" rel="stylesheet">
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
            <div class="everythingb2c-dashboard-content">
                <div class="container-fluid">
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center">
                                <h1 class="h3 mb-0">Edit Product: <?php echo cleanProductName($product['name']); ?></h1>
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
                                            <div class="col-md-4">
                                                <label for="edit_name" class="form-label">Product Name *</label>
                                                <input type="text" class="form-control" id="edit_name" name="name" 
                                                       value="<?php echo cleanProductName($product['name']); ?>" required>
                                            </div>
                                            <div class="col-md-4">
                                                <label for="parent_category_id" class="form-label">Category *</label>
                                                <select class="form-control" id="parent_category_id" name="parent_category_id" required>
                                                    <option value="">Select Category</option>
                                                    <?php 
                                                    // Display categories in hierarchical structure
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
                                            <div class="col-md-4">
                                                <label for="mrp" class="form-label">MRP (₹) *</label>
                                                <input type="number" class="form-control" id="mrp" name="mrp" step="0.01" min="0" value="<?php echo htmlspecialchars($product['mrp']); ?>" required>
                                                <div class="invalid-feedback">Please provide a valid MRP.</div>
                                            </div>
                                            <div class="col-md-4">
                                                <label for="selling_price" class="form-label">Selling Price (₹) *</label>
                                                <input type="number" class="form-control" id="selling_price" name="selling_price" step="0.01" min="0" value="<?php echo htmlspecialchars($product['selling_price']); ?>" required>
                                                <div class="invalid-feedback">Please provide a valid selling price.</div>
                                            </div>
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
                                        </div>

                                        <div class="row mb-3">
                                            <div class="col-md-4">
                                                <label for="gst_rate" class="form-label">GST Rate (%)</label>
                                                <select class="form-control" id="gst_rate" name="gst_rate" required>
                                                    <option value="0" <?php echo ($product['gst_rate'] == '0') ? 'selected' : ''; ?>>0%</option>
                                                    <option value="5" <?php echo ($product['gst_rate'] == '5') ? 'selected' : ''; ?>>5%</option>
                                                    <option value="12" <?php echo ($product['gst_rate'] == '12') ? 'selected' : ''; ?>>12%</option>
                                                    <option value="18" <?php echo ($product['gst_rate'] == '18') ? 'selected' : ''; ?>>18%</option>
                                                </select>
                                                <div class="form-text">Select GST rate for record keeping</div>
                                                <div class="invalid-feedback">Please select a GST rate.</div>
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <div class="col-md-4">
                                                <label for="stock_quantity" class="form-label">Stock Quantity *</label>
                                                <input type="number" class="form-control" id="stock_quantity" name="stock_quantity" min="0" value="<?php echo htmlspecialchars($product['stock_quantity']); ?>" required>
                                                <div class="invalid-feedback">Please provide stock quantity.</div>
                                            </div>
                                            <div class="col-md-4">
                                                <label for="max_quantity_per_order" class="form-label">Max Quantity Per Order</label>
                                                <input type="number" class="form-control" id="max_quantity_per_order" name="max_quantity_per_order" min="1" value="<?php echo htmlspecialchars($product['max_quantity_per_order'] ?? ''); ?>" placeholder="Leave empty for no limit">
                                                <div class="form-text">Maximum quantity a customer can order at once</div>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">Total with Shipping</label>
                                                <div class="form-control-plaintext" id="total_with_shipping_display">
                                                    ₹<?php 
                                                        $shipping = $product['shipping_charge'] ?? 0;
                                                        echo number_format($product['selling_price'] + $shipping, 2); 
                                                    ?>
                                                </div>
                                                <div class="form-text">Price + GST + Shipping</div>
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <div class="col-md-4">
                                                <label for="sku" class="form-label">SKU *</label>
                                                <input type="text" class="form-control" id="sku" name="sku" value="<?php echo htmlspecialchars($product['sku']); ?>" required>
                                                <div class="invalid-feedback">Please provide a unique SKU.</div>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="description" class="form-label">Description *</label>
                                            <textarea class="form-control" id="description" name="description" rows="4" required><?php echo htmlspecialchars($product['description']); ?></textarea>
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

                                        <div class="mb-3">
                                            <label for="hsn" class="form-label">HSN Code</label>
                                            <input type="text" class="form-control" id="hsn" name="hsn" maxlength="20" value="<?php echo htmlspecialchars($product['hsn'] ?? ''); ?>">
                                        </div>
                                    </div>

                                    <!-- Images -->
                                    <div class="col-md-4">
                                        <h5 class="mb-3">Images</h5>
                                        
                                        <!-- Current Main Image -->
                                        <?php if ($product['main_image']): ?>
                                            <div class="mb-3">
                                                <label class="form-label">Current Main Image</label>
                                                <div class="mb-2">
                                                    <img src="../<?php echo $product['main_image']; ?>" 
                                                         alt="<?php echo htmlspecialchars($product['name']); ?>"
                                                         class="img-preview">
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <div class="mb-3">
                                            <label for="main_image" class="form-label">Update Main Image</label>
                                            <input type="file" class="form-control image-input" id="main_image" name="main_image" accept="image/*">
                                            <div class="mt-2">
                                                <img id="main_image_preview" class="img-preview" style="display: none;">
                                            </div>
                                        </div>

                                        <!-- Current Additional Images -->
                                        <?php if (!empty($product_images)): ?>
                                            <div class="mb-3">
                                                <label class="form-label">Current Additional Images</label>
                                                <?php foreach ($product_images as $image): ?>
                                                    <div class="d-flex align-items-center mb-2">
                                                        <img src="../<?php echo $image['image_path']; ?>" 
                                                             alt="Product Image" class="img-preview me-2">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" 
                                                                   name="delete_images[]" value="<?php echo $image['id']; ?>" 
                                                                   id="delete_<?php echo $image['id']; ?>">
                                                            <label class="form-check-label" for="delete_<?php echo $image['id']; ?>">
                                                                Delete
                                                            </label>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php endif; ?>

                                        <div class="mb-3">
                                            <label class="form-label">Add More Images</label>
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

        // Calculate total with shipping
        document.getElementById('shipping_charge').addEventListener('input', calculateTotalWithShipping);
        document.getElementById('selling_price').addEventListener('input', calculateTotalWithShipping);

        function calculateTotalWithShipping() {
            const sellingPrice = parseFloat(document.getElementById('selling_price').value) || 0;
            const shippingCharge = parseFloat(document.getElementById('shipping_charge').value) || 0;
            
            const total = sellingPrice + shippingCharge;
            
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