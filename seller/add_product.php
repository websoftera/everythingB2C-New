<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';
require_once '../includes/seller_functions.php';

if (!isset($_SESSION['seller_id'])) {
    header('Location: login.php');
    exit;
}

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

$sellerId = $_SESSION['seller_id'];
$pageTitle = 'Add New Product';
$success_message = '';
$error_message = '';

// Get all categories
$allCategories = getAllCategoriesWithProductCount();
$categoryTree = buildCategoryTree($allCategories);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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

            $discount_percentage = calculateDiscountPercentage($mrp, $selling_price);

            // Insert product with seller_id and is_approved=0 (requires approval)
            $stmt = $pdo->prepare("INSERT INTO products (seller_id, name, slug, description, mrp, selling_price, discount_percentage, gst_type, gst_rate, category_id, stock_quantity, max_quantity_per_order, is_active, is_featured, is_discounted, is_approved, sku, hsn) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0, ?, ?)");
            $stmt->execute([$sellerId, $name, $slug, $description, $mrp, $selling_price, $discount_percentage, $gst_type, $gst_rate, $category_id, $stock_quantity, $max_quantity_per_order, $is_active, $is_featured, $is_discounted, $sku, $hsn]);
            
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

            // Log activity
            logSellerActivity($sellerId, 'product_added', "Product '{$name}' added (ID: {$product_id}, pending approval)");
            
            // Update seller statistics
            updateSellerStatistics($sellerId);

            $pdo->commit();
            $success_message = 'Product added successfully and submitted for approval!';
            
        } catch (Exception $e) {
            $pdo->rollBack();
            $error_message = 'Error adding product: ' . $e->getMessage();
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
                        <h1 class="h3 mb-0">Add New Product</h1>
                        <a href="products.php" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Products
                        </a>
                    </div>

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>Important:</strong> Your product will be submitted for admin approval before it appears on the website.
                    </div>

                    <?php if ($success_message): ?>
                        <div class="alert alert-success alert-dismissible fade show">
                            <?php echo htmlspecialchars($success_message); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <?php if ($error_message): ?>
                        <div class="alert alert-danger alert-dismissible fade show">
                            <?php echo htmlspecialchars($error_message); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
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
                                        <input type="text" name="name" class="form-control" required value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">SKU *</label>
                                        <input type="text" name="sku" class="form-control" required value="<?php echo htmlspecialchars($_POST['sku'] ?? ''); ?>">
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Category *</label>
                                        <select name="parent_category_id" class="form-select" required>
                                            <option value="">Select Category</option>
                                            <?php foreach ($categoryTree as $category): ?>
                                                <option value="<?php echo $category['id']; ?>" <?php echo (isset($_POST['parent_category_id']) && $_POST['parent_category_id'] == $category['id']) ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($category['name']); ?>
                                                </option>
                                                <?php if (!empty($category['children'])): ?>
                                                    <?php foreach ($category['children'] as $child): ?>
                                                        <option value="<?php echo $child['id']; ?>" <?php echo (isset($_POST['parent_category_id']) && $_POST['parent_category_id'] == $child['id']) ? 'selected' : ''; ?>>
                                                            &nbsp;&nbsp;&nbsp;└─ <?php echo htmlspecialchars($child['name']); ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">HSN Code</label>
                                        <input type="text" name="hsn" class="form-control" value="<?php echo htmlspecialchars($_POST['hsn'] ?? ''); ?>">
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Description *</label>
                                    <textarea name="description" class="form-control" rows="4" required><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
                                </div>

                                <div class="row">
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label">MRP *</label>
                                        <input type="number" name="mrp" class="form-control" step="0.01" required value="<?php echo htmlspecialchars($_POST['mrp'] ?? ''); ?>">
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label">Selling Price *</label>
                                        <input type="number" name="selling_price" class="form-control" step="0.01" required value="<?php echo htmlspecialchars($_POST['selling_price'] ?? ''); ?>">
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label">Stock Quantity *</label>
                                        <input type="number" name="stock_quantity" class="form-control" required value="<?php echo htmlspecialchars($_POST['stock_quantity'] ?? ''); ?>">
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label">Max Qty Per Order</label>
                                        <input type="number" name="max_quantity_per_order" class="form-control" value="<?php echo htmlspecialchars($_POST['max_quantity_per_order'] ?? ''); ?>">
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">GST Rate (%) *</label>
                                        <select name="gst_rate" class="form-select" required>
                                            <option value="">Select GST Rate</option>
                                            <option value="0" <?php echo (isset($_POST['gst_rate']) && $_POST['gst_rate'] == 0) ? 'selected' : ''; ?>>0%</option>
                                            <option value="5" <?php echo (isset($_POST['gst_rate']) && $_POST['gst_rate'] == 5) ? 'selected' : ''; ?>>5%</option>
                                            <option value="12" <?php echo (isset($_POST['gst_rate']) && $_POST['gst_rate'] == 12) ? 'selected' : ''; ?>>12%</option>
                                            <option value="18" <?php echo (isset($_POST['gst_rate']) && $_POST['gst_rate'] == 18) ? 'selected' : ''; ?>>18%</option>
                                            <option value="28" <?php echo (isset($_POST['gst_rate']) && $_POST['gst_rate'] == 28) ? 'selected' : ''; ?>>28%</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="checkbox" name="is_active" id="is_active" <?php echo (isset($_POST['is_active']) || !isset($_POST['name'])) ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="is_active">Active</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="checkbox" name="is_featured" id="is_featured" <?php echo isset($_POST['is_featured']) ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="is_featured">Featured</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="checkbox" name="is_discounted" id="is_discounted" <?php echo isset($_POST['is_discounted']) ? 'checked' : ''; ?>>
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
                                <div class="mb-3">
                                    <label class="form-label">Main Product Image</label>
                                    <input type="file" name="main_image" class="form-control" accept="image/*">
                                    <small class="text-muted">Recommended: 800x800px, Max 2MB</small>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Additional Images (Optional)</label>
                                    <input type="file" name="images[]" class="form-control" multiple accept="image/*">
                                    <small class="text-muted">You can select multiple images</small>
                                </div>
                            </div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end mb-4">
                            <a href="products.php" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-check"></i> Submit for Approval
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