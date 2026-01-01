<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 0); // Don't display errors inline
require_once '../config/database.php';
require_once '../includes/functions.php';
require_once '../includes/seller_functions.php';

if (!isset($_SESSION['seller_id'])) {
    header('Location: login.php');
    exit;
}

$sellerId = $_SESSION['seller_id'];
$productId = isset($_GET['id']) ? intval($_GET['id']) : 0;
$pageTitle = 'Edit Product';
$success_message = '';
$error_message = '';

// Helper functions
if (!function_exists('createSlug')) {
    function createSlug($string) {
        $slug = strtolower(trim($string));
        $slug = preg_replace('/[^a-z0-9-]/', '-', $slug);
        $slug = preg_replace('/-+/', '-', $slug);
        return trim($slug, '-');
    }
}

if (!function_exists('uploadImage')) {
    function uploadImage($file, $folder) {
        $upload_dir = "../uploads/$folder/";
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        $allowed_types = ['image/jpeg', 'image/png', 'image/webp', 'image/jpg'];
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
}

if (!function_exists('calculateDiscountPercentage')) {
    function calculateDiscountPercentage($mrp, $selling_price) {
        if ($mrp <= 0) return 0;
        return round((($mrp - $selling_price) / $mrp) * 100, 2);
    }
}

// Get product - make sure it belongs to this seller
$stmt = $pdo->prepare("SELECT p.*, c.name as category_name FROM products p 
                       LEFT JOIN categories c ON p.category_id = c.id 
                       WHERE p.id = ? AND p.seller_id = ?");
$stmt->execute([$productId, $sellerId]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    header('Location: products.php');
    exit;
}

// Get all categories for edit form
$allCategories = getAllCategoriesWithProductCount();
$categoryTree = buildCategoryTree($allCategories);

// Handle product update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Debug: Log the POST data
    error_log("POST received in edit_product.php");
    error_log("POST data keys: " . implode(", ", array_keys($_POST)));
    
    $name = trim($_POST['name']);
    $slug = createSlug($name);
    $description = trim($_POST['description']);
    $mrp = floatval($_POST['mrp']);
    $selling_price = floatval($_POST['selling_price']);
    $category_id = intval($_POST['parent_category_id']);
    $stock_quantity = intval($_POST['stock_quantity']);
    $max_quantity_per_order = !empty($_POST['max_quantity_per_order']) ? intval($_POST['max_quantity_per_order']) : null;
    $gst_type = 'sgst_cgst';
    $gst_rate = floatval($_POST['gst_rate']);
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    $is_discounted = isset($_POST['is_discounted']) ? 1 : 0;
    $sku = trim($_POST['sku']);
    $hsn = isset($_POST['hsn']) ? trim($_POST['hsn']) : null;

    // Debug: Log validation
    error_log("Validation - name: $name, mrp: $mrp, selling_price: $selling_price, category_id: $category_id");
    
    // Validation
    if (empty($name) || empty($description) || $mrp <= 0 || $selling_price <= 0) {
        $error_message = 'Please fill in all required fields with valid values.';
        error_log("Validation error: Empty fields");
    } elseif (empty($_POST['parent_category_id'])) {
        $error_message = 'Please select a category.';
        error_log("Validation error: No category");
    } elseif ($selling_price > $mrp) {
        $error_message = 'Selling price cannot be greater than MRP.';
        error_log("Validation error: Selling price > MRP");
    } elseif ($gst_rate < 0 || $gst_rate > 100) {
        $error_message = 'GST rate must be between 0 and 100.';
        error_log("Validation error: Invalid GST");
    } else {
        try {
            $pdo->beginTransaction();

            $discount_percentage = calculateDiscountPercentage($mrp, $selling_price);

            // If product was rejected, reset is_approved to 0 so it needs re-approval
            // Otherwise keep current approval status
            $is_approved = $product['is_approved'];
            $rejection_reason = $product['rejection_reason']; // Keep existing reason by default
            
            // If this is a resubmission of a REJECTED product (has rejection_reason)
            if ($product['rejection_reason']) {
                // Clear the rejection reason and set back to pending approval
                $is_approved = 0;           // Not approved yet
                $rejection_reason = null;  // Clear the reason - fresh start for re-review
            }

            $stmt = $pdo->prepare("UPDATE products SET name = ?, slug = ?, description = ?, mrp = ?, selling_price = ?, 
                                   discount_percentage = ?, gst_type = ?, gst_rate = ?, category_id = ?, stock_quantity = ?, 
                                   max_quantity_per_order = ?, is_active = ?, is_featured = ?, is_discounted = ?, 
                                   is_approved = ?, sku = ?, hsn = ?, rejection_reason = ?, updated_at = NOW()
                                   WHERE id = ? AND seller_id = ?");
            $stmt->execute([$name, $slug, $description, $mrp, $selling_price, $discount_percentage, $gst_type, $gst_rate, 
                           $category_id, $stock_quantity, $max_quantity_per_order, $is_active, $is_featured, $is_discounted,
                           $is_approved, $sku, $hsn, $rejection_reason, $productId, $sellerId]);

            // Handle main image upload
            if (isset($_FILES['main_image']) && $_FILES['main_image']['error'] === UPLOAD_ERR_OK) {
                $main_image_path = uploadImage($_FILES['main_image'], 'products');
                if ($main_image_path) {
                    $stmt = $pdo->prepare("UPDATE products SET main_image = ? WHERE id = ?");
                    $stmt->execute([$main_image_path, $productId]);
                }
            }

            // Log activity
            $action_type = $product['rejection_reason'] ? 'product_resubmitted' : 'product_updated';
            $action_desc = $product['rejection_reason'] ? 'resubmitted for approval after rejection' : 'updated';
            logSellerActivity($sellerId, $action_type, "Product '{$name}' (ID: {$productId}) {$action_desc}");
            
            // Update seller statistics
            updateSellerStatistics($sellerId);

            $pdo->commit();
            $success_message = $product['rejection_reason'] ? 
                'Product updated and resubmitted for approval! Admin will review your changes.' : 
                'Product updated successfully!';
            
            // Refresh product data
            $stmt = $pdo->prepare("SELECT p.*, c.name as category_name FROM products p 
                                 LEFT JOIN categories c ON p.category_id = c.id 
                                 WHERE p.id = ? AND p.seller_id = ?");
            $stmt->execute([$productId, $sellerId]);
            $product = $stmt->fetch(PDO::FETCH_ASSOC);
            
        } catch (Exception $e) {
            $pdo->rollBack();
            $error_message = 'Error updating product: ' . $e->getMessage();
        }
    }
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
    <link href="../admin/assets/css/admin.css" rel="stylesheet">
</head>
<body>
    <div class="everythingb2c-admin-container">
        <?php include 'includes/sidebar.php'; ?>
        <div class="everythingb2c-main-content">
            <?php include 'includes/header.php'; ?>
            <div class="everythingb2c-dashboard-content">
                <div class="container-fluid">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h1 class="h3 mb-0">Edit Product</h1>
                        <a href="products.php" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Products
                        </a>
                    </div>

                    <?php if ($product['rejection_reason']): ?>
                        <div class="alert alert-danger alert-dismissible fade show">
                            <i class="fas fa-times-circle"></i>
                            <strong>Product Rejected - Action Required!</strong> Your product was rejected by the admin. Please carefully review the rejection reason below and make the necessary corrections before resubmitting.
                            <br><br><strong style="font-size: 1.1em;">Rejection Reason:</strong>
                            <div style="margin-top: 10px; padding: 10px; background: rgba(0,0,0,0.1); border-left: 3px solid #dc3545; border-radius: 3px;">
                                <?php echo htmlspecialchars($product['rejection_reason']); ?>
                            </div>
                            <div style="margin-top: 10px;">
                                <small><i class="fas fa-info-circle"></i> Update your product details below and click "Update & Resubmit for Approval"</small>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php elseif (!$product['is_approved']): ?>
                        <div class="alert alert-warning">
                            <i class="fas fa-clock"></i>
                            <strong>Pending Approval:</strong> This product is waiting for admin approval. No rejection issues.
                        </div>
                    <?php else: ?>
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle"></i>
                            <strong>Approved:</strong> This product is approved and active. You can still make updates.
                        </div>
                    <?php endif; ?>

                    <?php if ($success_message): ?>
                        <div class="alert alert-success alert-dismissible fade show">
                            <i class="fas fa-check"></i>
                            <?php echo htmlspecialchars($success_message); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <?php if ($error_message): ?>
                        <div class="alert alert-danger alert-dismissible fade show">
                            <i class="fas fa-exclamation-circle"></i>
                            <?php echo htmlspecialchars($error_message); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <!-- Debug: Show if POST was received -->
                    <?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
                        <div class="alert alert-info alert-dismissible fade show">
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            <strong>Debug Info:</strong> Form submitted with <?php echo count($_POST); ?> fields.
                            <br><small>POST fields: <?php echo htmlspecialchars(implode(", ", array_keys($_POST))); ?></small>
                            <?php if ($success_message): ?>
                                <br><span style="color: green;"><strong>✓ Success:</strong> <?php echo htmlspecialchars($success_message); ?></span>
                            <?php elseif ($error_message): ?>
                                <br><span style="color: red;"><strong>✗ Error:</strong> <?php echo htmlspecialchars($error_message); ?></span>
                            <?php else: ?>
                                <br><span style="color: orange;"><strong>⚠ No handler response</strong> - Check server error logs</span>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" enctype="multipart/form-data">
                        <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">Product Information</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Product Name *</label>
                                        <input type="text" name="name" class="form-control" required 
                                               value="<?php echo htmlspecialchars($product['name']); ?>">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">SKU *</label>
                                        <input type="text" name="sku" class="form-control" required 
                                               value="<?php echo htmlspecialchars($product['sku']); ?>">
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Category *</label>
                                        <select name="parent_category_id" class="form-select" required>
                                            <option value="">Select Category</option>
                                            <?php foreach ($categoryTree as $category): ?>
                                                <option value="<?php echo $category['id']; ?>" 
                                                        <?php echo ($category['id'] == $product['category_id']) ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($category['name']); ?>
                                                </option>
                                                <?php if (!empty($category['children'])): ?>
                                                    <?php foreach ($category['children'] as $child): ?>
                                                        <option value="<?php echo $child['id']; ?>"
                                                                <?php echo ($child['id'] == $product['category_id']) ? 'selected' : ''; ?>>
                                                            &nbsp;&nbsp;&nbsp;└─ <?php echo htmlspecialchars($child['name']); ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">HSN Code</label>
                                        <input type="text" name="hsn" class="form-control" 
                                               value="<?php echo htmlspecialchars($product['hsn'] ?? ''); ?>">
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Description *</label>
                                    <textarea name="description" class="form-control" rows="4" required><?php echo htmlspecialchars($product['description']); ?></textarea>
                                </div>

                                <div class="row">
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label">MRP *</label>
                                        <input type="number" name="mrp" class="form-control" step="0.01" required 
                                               value="<?php echo htmlspecialchars($product['mrp']); ?>">
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label">Selling Price *</label>
                                        <input type="number" name="selling_price" class="form-control" step="0.01" required 
                                               value="<?php echo htmlspecialchars($product['selling_price']); ?>">
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label">Stock Quantity *</label>
                                        <input type="number" name="stock_quantity" class="form-control" required 
                                               value="<?php echo htmlspecialchars($product['stock_quantity']); ?>">
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label">Max Qty Per Order</label>
                                        <input type="number" name="max_quantity_per_order" class="form-control" 
                                               value="<?php echo htmlspecialchars($product['max_quantity_per_order'] ?? ''); ?>">
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">GST Rate (%) *</label>
                                        <select name="gst_rate" class="form-select" required>
                                            <option value="">Select GST Rate</option>
                                            <option value="0" <?php echo ($product['gst_rate'] == 0) ? 'selected' : ''; ?>>0%</option>
                                            <option value="5" <?php echo ($product['gst_rate'] == 5) ? 'selected' : ''; ?>>5%</option>
                                            <option value="12" <?php echo ($product['gst_rate'] == 12) ? 'selected' : ''; ?>>12%</option>
                                            <option value="18" <?php echo ($product['gst_rate'] == 18) ? 'selected' : ''; ?>>18%</option>
                                            <option value="28" <?php echo ($product['gst_rate'] == 28) ? 'selected' : ''; ?>>28%</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="checkbox" name="is_active" id="is_active" 
                                               <?php echo $product['is_active'] ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="is_active">Active</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="checkbox" name="is_featured" id="is_featured"
                                               <?php echo $product['is_featured'] ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="is_featured">Featured</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="checkbox" name="is_discounted" id="is_discounted"
                                               <?php echo $product['is_discounted'] ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="is_discounted">Discounted</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">Product Images</h6>
                            </div>
                            <div class="card-body">
                                <?php if ($product['main_image']): ?>
                                    <div class="mb-3">
                                        <label class="form-label">Current Main Image</label>
                                        <div>
                                            <?php 
                                            $imagePath = $product['main_image'];
                                            if (strpos($imagePath, 'uploads/') === 0) {
                                                $imagePath = '../' . $imagePath;
                                            } elseif (strpos($imagePath, '/') !== 0 && strpos($imagePath, 'http') !== 0) {
                                                $imagePath = '../uploads/' . $imagePath;
                                            } else {
                                                $imagePath = '../' . $imagePath;
                                            }
                                            ?>
                                            <img src="<?php echo htmlspecialchars($imagePath); ?>" 
                                                 style="max-height: 150px; max-width: 150px; object-fit: contain;">
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <div class="mb-3">
                                    <label class="form-label">Update Main Product Image</label>
                                    <input type="file" name="main_image" class="form-control" accept="image/*">
                                    <small class="text-muted">Leave blank to keep current image. Recommended: 800x800px, Max 2MB</small>
                                </div>
                            </div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end mb-4">
                            <a href="products.php" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> 
                                <?php echo $product['rejection_reason'] ? 'Update & Resubmit for Approval' : ($product['is_approved'] ? 'Update Product' : 'Submit for Approval'); ?>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../admin/assets/js/admin.js"></script>
</body>
</html>
