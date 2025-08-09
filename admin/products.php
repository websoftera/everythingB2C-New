<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';
require_once '../includes/gst_shipping_functions.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

$pageTitle = 'Products Management';

// Handle bulk actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bulk_action'])) {
    $action = $_POST['bulk_action'];
    $selected_products = $_POST['selected_products'] ?? [];
    
    if (!empty($selected_products)) {
        switch ($action) {
            case 'delete':
                $placeholders = str_repeat('?,', count($selected_products) - 1) . '?';
                $stmt = $pdo->prepare("DELETE FROM products WHERE id IN ($placeholders)");
                $stmt->execute($selected_products);
                $_SESSION['success_message'] = 'Selected products deleted successfully!';
                break;
            case 'activate':
                $placeholders = str_repeat('?,', count($selected_products) - 1) . '?';
                $stmt = $pdo->prepare("UPDATE products SET is_active = 1 WHERE id IN ($placeholders)");
                $stmt->execute($selected_products);
                $_SESSION['success_message'] = 'Selected products activated successfully!';
                break;
            case 'deactivate':
                $placeholders = str_repeat('?,', count($selected_products) - 1) . '?';
                $stmt = $pdo->prepare("UPDATE products SET is_active = 0 WHERE id IN ($placeholders)");
                $stmt->execute($selected_products);
                $_SESSION['success_message'] = 'Selected products deactivated successfully!';
                break;
        }
    }
    header('Location: products.php');
    exit;
}

// Get search parameters
$search = $_GET['search'] ?? '';
$category_filter = $_GET['category'] ?? '';
$status_filter = $_GET['status'] ?? '';

// Build query
$where_conditions = [];
$params = [];

if ($search) {
    $where_conditions[] = "(p.name LIKE ? OR p.description LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if ($category_filter) {
    $where_conditions[] = "p.category_id = ?";
    $params[] = $category_filter;
}

if ($status_filter !== '') {
    $where_conditions[] = "p.is_active = ?";
    $params[] = $status_filter;
}

$where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

// Get total count for pagination
$count_sql = "SELECT COUNT(*) FROM products p LEFT JOIN categories c ON p.category_id = c.id $where_clause";
$stmt = $pdo->prepare($count_sql);
$stmt->execute($params);
$total_products = $stmt->fetchColumn();

// Pagination
$page = max(1, $_GET['page'] ?? 1);
$per_page = 20;
$total_pages = ceil($total_products / $per_page);
$offset = ($page - 1) * $per_page;

// Get products
$sql = "SELECT p.*, c.name as category_name, c.parent_id, pc.name as parent_category_name, p.hsn 
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.id 
        LEFT JOIN categories pc ON c.parent_id = pc.id 
        $where_clause 
        ORDER BY p.created_at DESC 
        LIMIT $per_page OFFSET $offset";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get categories for filter with hierarchical structure
$categories = getAllCategories();
$parentCategories = getParentCategories();
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

            <!-- Products Content -->
            <div class="everythingb2c-dashboard-content">
                <div class="container-fluid">
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center">
                                <h1 class="h3 mb-0">Products Management</h1>
                                <a href="add_product.php" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> Add New Product
                                </a>
                            </div>
                        </div>
                    </div>

                    <?php 
                    if (isset($_SESSION['success_message'])) {
                        $success_message = $_SESSION['success_message'];
                        unset($_SESSION['success_message']);
                    }
                    if (isset($_SESSION['error_message'])) {
                        $error_message = $_SESSION['error_message'];
                        unset($_SESSION['error_message']);
                    }
                    ?>

                    <?php if (isset($success_message)): ?>
                        <div class="alert alert-success">
                            <?php echo htmlspecialchars($success_message); ?>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($error_message)): ?>
                        <div class="alert alert-danger">
                            <?php echo htmlspecialchars($error_message); ?>
                        </div>
                    <?php endif; ?>

                    <!-- Filters and Search -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <form method="GET" class="row g-3">
                                <div class="col-md-4">
                                    <div class="search-box">
                                        <i class="fas fa-search search-icon"></i>
                                        <input type="text" class="form-control" name="search" 
                                               placeholder="Search products..." value="<?php echo htmlspecialchars($search); ?>">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <select class="form-control" name="category">
                                        <option value="">All Categories</option>
                                        <?php foreach ($parentCategories as $parentCategory): ?>
                                            <optgroup label="<?php echo htmlspecialchars($parentCategory['name']); ?>">
                                                <option value="<?php echo $parentCategory['id']; ?>" 
                                                        <?php echo $category_filter == $parentCategory['id'] ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($parentCategory['name']); ?>
                                                </option>
                                                <?php 
                                                $subcategories = getSubcategoriesByParentId($parentCategory['id']);
                                                foreach ($subcategories as $subcategory): 
                                                ?>
                                                    <option value="<?php echo $subcategory['id']; ?>" 
                                                            <?php echo $category_filter == $subcategory['id'] ? 'selected' : ''; ?>>
                                                        &nbsp;&nbsp;&nbsp;&nbsp;→ <?php echo htmlspecialchars($subcategory['name']); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </optgroup>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <select class="form-control" name="status">
                                        <option value="">All Status</option>
                                        <option value="1" <?php echo $status_filter === '1' ? 'selected' : ''; ?>>Active</option>
                                        <option value="0" <?php echo $status_filter === '0' ? 'selected' : ''; ?>>Inactive</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <button type="submit" class="btn btn-primary me-2">
                                        <i class="fas fa-search"></i> Filter
                                    </button>
                                    <a href="products.php" class="btn btn-secondary">
                                        <i class="fas fa-times"></i> Clear
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Products Table -->
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Products (<?php echo $total_products; ?>)</h5>
                            <div class="d-flex gap-2">
                                <a href="export_products.php" class="btn btn-success btn-sm">
                                    <i class="fas fa-download"></i> Export CSV
                                </a>
                                <a href="import_products.php" class="btn btn-primary btn-sm">
                                    <i class="fas fa-upload"></i> Import CSV
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <?php if (empty($products)): ?>
                                <div class="text-center py-4">
                                    <i class="fas fa-box fa-3x text-muted mb-3"></i>
                                    <h5>No products found</h5>
                                    <p class="text-muted">Try adjusting your search criteria or add a new product.</p>
                                </div>
                            <?php else: ?>
                                <form method="POST" id="bulkActionForm">
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>
                                                        <input type="checkbox" id="selectAll" class="form-check-input">
                                                    </th>
                                                    <th>Image</th>
                                                    <th>Name</th>
                                                    <th>SKU</th>
                                                    <th>HSN</th>
                                                    <th>Category</th>
                                                    <th>Price</th>
                                                    <th>GST</th>
                                                    <th>Shipping</th>
                                                    <th>Stock</th>
                                                    <th>Status</th>
                                                    <th>Created</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($products as $product): ?>
                                                    <tr>
                                                        <td>
                                                            <input type="checkbox" name="selected_products[]" 
                                                                   value="<?php echo $product['id']; ?>" 
                                                                   class="form-check-input item-checkbox">
                                                        </td>
                                                        <td>
                                                            <?php if ($product['main_image']): ?>
                                                                <img src="../../<?php echo $product['main_image']; ?>" 
                                                                     alt="<?php echo cleanProductName($product['name']); ?>" 
                                                                     style="width:50px;height:50px;object-fit:cover;border-radius:4px;">
                                                            <?php else: ?>
                                                                <div class="img-preview bg-light d-flex align-items-center justify-content-center">
                                                                    <i class="fas fa-image text-muted"></i>
                                                                </div>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td>
                                                            <strong><?php echo cleanProductName($product['name']); ?></strong>
                                                            <br>
                                                            <small class="text-muted">SKU: <?php echo htmlspecialchars($product['sku']); ?></small>
                                                            <br>
                                                            <small class="text-muted"><?php echo htmlspecialchars($product['slug']); ?></small>
                                                        </td>
                                                        <td><?php echo htmlspecialchars($product['sku']); ?></td>
                                                        <td><?php echo htmlspecialchars($product['hsn'] ?? ''); ?></td>
                                                        <td>
                                                            <?php 
                                                            if ($product['parent_category_name']) {
                                                                // Product is in a subcategory
                                                                echo '<strong>' . htmlspecialchars($product['parent_category_name']) . '</strong>';
                                                                echo '<br><small class="text-muted">→ ' . htmlspecialchars($product['category_name']) . '</small>';
                                                            } else {
                                                                // Product is in a parent category
                                                                echo htmlspecialchars($product['category_name'] ?? 'Uncategorized');
                                                            }
                                                            ?>
                                                        </td>
                                                        <td>
                                                            <div class="d-flex flex-column">
                                                                <span class="text-decoration-line-through text-muted">
                                                                    ₹<?php echo number_format($product['mrp'], 0); ?>
                                                                </span>
                                                                <strong class="text-primary">
                                                                    ₹<?php echo number_format($product['selling_price'], 0); ?>
                                                                </strong>
                                                                <?php if ($product['discount_percentage'] > 0): ?>
                                                                    <small class="text-success">
                                                                        <?php echo $product['discount_percentage']; ?>% OFF
                                                                    </small>
                                                                <?php endif; ?>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <?php 
                                                            $gst_type_text = ($product['gst_type'] == 'sgst_cgst') ? 'SGST+CGST' : 'IGST';
                                                            echo $gst_type_text . ' ' . $product['gst_rate'] . '%';
                                                            ?>
                                                        </td>
                                                        <td>
                                                            <?php 
                                                            if ($product['shipping_charge'] !== null) {
                                                                echo '₹' . number_format($product['shipping_charge'], 0);
                                                            } else {
                                                                echo '<span class="text-muted">Free</span>';
                                                            }
                                                            ?>
                                                        </td>
                                                        <td><?php echo $product['stock_quantity']; ?></td>
                                                        <td>
                                                            <span class="badge bg-<?php echo $product['is_active'] ? 'success' : 'secondary'; ?>">
                                                                <?php echo $product['is_active'] ? 'Active' : 'Inactive'; ?>
                                                            </span>
                                                        </td>
                                                        <td><?php echo date('M d, Y', strtotime($product['created_at'])); ?></td>
                                                        <td>
                                                            <div class="action-buttons">
                                                                <a href="edit_product.php?id=<?php echo $product['id']; ?>" 
                                                                   class="btn btn-warning btn-sm" title="Edit">
                                                                    <i class="fas fa-edit"></i>
                                                                </a>
                                                                <a href="../product.php?slug=<?php echo $product['slug']; ?>" 
                                                                   class="btn btn-info btn-sm" title="View" target="_blank">
                                                                    <i class="fas fa-eye"></i>
                                                                </a>
                                                                <button type="button" class="btn btn-danger btn-sm btn-delete" 
                                                                        onclick="deleteProduct(<?php echo $product['id']; ?>)" title="Delete">
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>

                                    <!-- Bulk Actions -->
                                    <div class="d-flex justify-content-between align-items-center mt-3">
                                        <div class="d-flex gap-2">
                                            <select class="form-control form-control-sm" name="bulk_action" style="width: auto;">
                                                <option value="">Bulk Actions</option>
                                                <option value="activate">Activate</option>
                                                <option value="deactivate">Deactivate</option>
                                                <option value="delete">Delete</option>
                                            </select>
                                            <button type="submit" class="btn btn-primary btn-sm" id="bulkActionBtn" disabled>
                                                Apply
                                            </button>
                                        </div>

                                        <!-- Pagination -->
                                        <?php if ($total_pages > 1): ?>
                                            <nav>
                                                <ul class="pagination">
                                                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                                        <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                                            <a class="page-link" href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&category=<?php echo $category_filter; ?>&status=<?php echo $status_filter; ?>">
                                                                <?php echo $i; ?>
                                                            </a>
                                                        </li>
                                                    <?php endfor; ?>
                                                </ul>
                                            </nav>
                                        <?php endif; ?>
                                    </div>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/admin.js"></script>
    <script>
        function deleteProduct(productId) {
            if (confirm('Are you sure you want to delete this product? This action cannot be undone.')) {
                window.location.href = `delete_product.php?id=${productId}`;
            }
        }
    </script>
</body>
</html> 