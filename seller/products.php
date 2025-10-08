<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';
require_once '../includes/seller_functions.php';
require_once '../includes/gst_shipping_functions.php';

if (!isset($_SESSION['seller_id'])) {
    header('Location: login.php');
    exit;
}

$sellerId = $_SESSION['seller_id'];
$pageTitle = 'My Products';

// Handle bulk actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bulk_action'])) {
    $action = $_POST['bulk_action'];
    $selected_products = $_POST['selected_products'] ?? [];
    
    if (!empty($selected_products)) {
        $placeholders = str_repeat('?,', count($selected_products) - 1) . '?';
        $params = array_merge($selected_products, [$sellerId]);
        
        switch ($action) {
            case 'delete':
                // Only allow if permission grants it
                $permissions = getSellerPermissions($sellerId);
                if ($permissions['can_delete_products']) {
                    $stmt = $pdo->prepare("DELETE FROM products WHERE id IN ($placeholders) AND seller_id = ?");
                    $stmt->execute($params);
                    $_SESSION['success_message'] = 'Selected products deleted successfully!';
                    logSellerActivity($sellerId, 'bulk_delete', 'Deleted ' . count($selected_products) . ' products');
                } else {
                    $_SESSION['error_message'] = 'You do not have permission to delete products.';
                }
                break;
            case 'activate':
                $stmt = $pdo->prepare("UPDATE products SET is_active = 1 WHERE id IN ($placeholders) AND seller_id = ?");
                $stmt->execute($params);
                $_SESSION['success_message'] = 'Selected products activated successfully!';
                logSellerActivity($sellerId, 'bulk_activate', 'Activated ' . count($selected_products) . ' products');
                break;
            case 'deactivate':
                $stmt = $pdo->prepare("UPDATE products SET is_active = 0 WHERE id IN ($placeholders) AND seller_id = ?");
                $stmt->execute($params);
                $_SESSION['success_message'] = 'Selected products deactivated successfully!';
                logSellerActivity($sellerId, 'bulk_deactivate', 'Deactivated ' . count($selected_products) . ' products');
                break;
        }
    }
    header('Location: products.php');
    exit;
}

// Get search and filter parameters
$search = $_GET['search'] ?? '';
$category_filter = $_GET['category'] ?? '';
$status_filter = $_GET['status'] ?? '';
$approval_filter = $_GET['approval'] ?? '';

// SECURITY: Sellers can only see their own products
$where_conditions = ["p.seller_id = ?"];
$params = [$sellerId];

if ($search) {
    $where_conditions[] = "(p.name LIKE ? OR p.description LIKE ? OR p.sku LIKE ?)";
    $params[] = "%$search%";
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

if ($approval_filter !== '') {
    $where_conditions[] = "p.is_approved = ?";
    $params[] = $approval_filter;
}

$where_clause = 'WHERE ' . implode(' AND ', $where_conditions);

// Get total count
$count_sql = "SELECT COUNT(*) FROM products p $where_clause";
$stmt = $pdo->prepare($count_sql);
$stmt->execute($params);
$total_products = $stmt->fetchColumn();

// Pagination
$page = max(1, $_GET['page'] ?? 1);
$per_page = 25;
$offset = ($page - 1) * $per_page;
$total_pages = ceil($total_products / $per_page);

// Get products
$sql = "SELECT p.*, c.name as category_name, c.parent_id,
        pc.name as parent_category_name
        FROM products p
        LEFT JOIN categories c ON p.category_id = c.id
        LEFT JOIN categories pc ON c.parent_id = pc.id
        $where_clause
        ORDER BY p.created_at DESC
        LIMIT $per_page OFFSET $offset";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get categories for filter
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
    <link href="../admin/assets/css/admin.css" rel="stylesheet">
</head>
<body>
    <div class="everythingb2c-admin-container">
        <?php include 'includes/sidebar.php'; ?>
        <div class="everythingb2c-main-content">
            <?php include 'includes/header.php'; ?>
            <div class="everythingb2c-dashboard-content">
                <div class="container-fluid">
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center">
                                <h1 class="h3 mb-0">My Products</h1>
                                <a href="add_product.php" class="btn btn-success">
                                    <i class="fas fa-plus"></i> Add New Product
                                </a>
                            </div>
                        </div>
                    </div>

                    <?php if (isset($_SESSION['success_message'])): ?>
                        <div class="alert alert-success alert-dismissible fade show">
                            <?php echo htmlspecialchars($_SESSION['success_message']); unset($_SESSION['success_message']); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['error_message'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show">
                            <?php echo htmlspecialchars($_SESSION['error_message']); unset($_SESSION['error_message']); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <?php 
                    // Count pending products
                    $stmt = $pdo->prepare("SELECT COUNT(*) FROM products WHERE seller_id = ? AND is_approved = 0");
                    $stmt->execute([$sellerId]);
                    $pendingCount = $stmt->fetchColumn();
                    if ($pendingCount > 0):
                    ?>
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            <strong>Pending Approval:</strong> You have <?php echo $pendingCount; ?> product(s) waiting for admin approval.
                        </div>
                    <?php endif; ?>

                    <!-- Filters and Search -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <form method="GET" class="row g-3">
                                <div class="col-md-3">
                                    <input type="text" class="form-control" name="search" 
                                           placeholder="Search products..." value="<?php echo htmlspecialchars($search); ?>">
                                </div>
                                <div class="col-md-2">
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
                                <div class="col-md-2">
                                    <select class="form-control" name="approval">
                                        <option value="">All Approval</option>
                                        <option value="1" <?php echo $approval_filter === '1' ? 'selected' : ''; ?>>Approved</option>
                                        <option value="0" <?php echo $approval_filter === '0' ? 'selected' : ''; ?>>Pending</option>
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
                            <a href="export_products.php" class="btn btn-success btn-sm">
                                <i class="fas fa-download"></i> Export CSV
                            </a>
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
                                                    <th><input type="checkbox" id="selectAll" class="form-check-input"></th>
                                                    <th>Image</th>
                                                    <th>Name</th>
                                                    <th>SKU</th>
                                                    <th>Category</th>
                                                    <th>Price</th>
                                                    <th>Stock</th>
                                                    <th>Status</th>
                                                    <th>Approval</th>
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
                                                                <?php 
                                                                // Handle both relative and absolute image paths
                                                                $imagePath = $product['main_image'];
                                                                if (strpos($imagePath, 'uploads/') === 0) {
                                                                    $imagePath = '../' . $imagePath;
                                                                } elseif (strpos($imagePath, '/') !== 0) {
                                                                    $imagePath = '../uploads/' . $imagePath;
                                                                }
                                                                ?>
                                                                <img src="<?php echo htmlspecialchars($imagePath); ?>" 
                                                                     alt="Product" style="width:50px;height:50px;object-fit:cover;border-radius:4px;">
                                                            <?php else: ?>
                                                                <div style="width:50px;height:50px;background:#f0f0f0;display:flex;align-items:center;justify-content:center;">
                                                                    <i class="fas fa-image text-muted"></i>
                                                                </div>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td>
                                                            <strong><?php echo htmlspecialchars($product['name']); ?></strong>
                                                            <br><small class="text-muted"><?php echo htmlspecialchars($product['slug']); ?></small>
                                                        </td>
                                                        <td><?php echo htmlspecialchars($product['sku']); ?></td>
                                                        <td>
                                                            <?php 
                                                            if ($product['parent_category_name']) {
                                                                echo '<strong>' . htmlspecialchars($product['parent_category_name']) . '</strong>';
                                                                echo '<br><small>→ ' . htmlspecialchars($product['category_name']) . '</small>';
                                                            } else {
                                                                echo htmlspecialchars($product['category_name'] ?? 'N/A');
                                                            }
                                                            ?>
                                                        </td>
                                                        <td>
                                                            <span class="text-muted"><del>₹<?php echo number_format($product['mrp'], 0); ?></del></span>
                                                            <br><strong class="text-primary">₹<?php echo number_format($product['selling_price'], 0); ?></strong>
                                                            <?php if ($product['discount_percentage'] > 0): ?>
                                                                <br><small class="text-success"><?php echo $product['discount_percentage']; ?>% OFF</small>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td>
                                                            <span class="badge <?php echo $product['stock_quantity'] > 0 ? 'bg-success' : 'bg-danger'; ?>">
                                                                <?php echo $product['stock_quantity']; ?>
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <span class="badge bg-<?php echo $product['is_active'] ? 'success' : 'secondary'; ?>">
                                                                <?php echo $product['is_active'] ? 'Active' : 'Inactive'; ?>
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <?php if ($product['is_approved']): ?>
                                                                <span class="badge bg-success"><i class="fas fa-check"></i> Approved</span>
                                                            <?php else: ?>
                                                                <span class="badge bg-warning"><i class="fas fa-clock"></i> Pending</span>
                                                                <?php if ($product['rejection_reason']): ?>
                                                                    <br><small class="text-danger" data-bs-toggle="tooltip" 
                                                                           title="<?php echo htmlspecialchars($product['rejection_reason']); ?>">
                                                                        <i class="fas fa-exclamation-circle"></i> Rejected
                                                                    </small>
                                                                <?php endif; ?>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td>
                                                            <a href="edit_product.php?id=<?php echo $product['id']; ?>" 
                                                               class="btn btn-warning btn-sm" title="Edit">
                                                                <i class="fas fa-edit"></i>
                                                            </a>
                                                            <a href="../product.php?slug=<?php echo $product['slug']; ?>" 
                                                               class="btn btn-info btn-sm" title="View" target="_blank">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
                                                            <?php 
                                                            $permissions = getSellerPermissions($sellerId);
                                                            if ($permissions['can_delete_products']): 
                                                            ?>
                                                                <button type="button" class="btn btn-danger btn-sm" 
                                                                        onclick="deleteProduct(<?php echo $product['id']; ?>)" title="Delete">
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                            <?php endif; ?>
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
                                                <?php if ($permissions['can_delete_products']): ?>
                                                    <option value="delete">Delete</option>
                                                <?php endif; ?>
                                            </select>
                                            <button type="submit" class="btn btn-primary btn-sm" id="bulkActionBtn" disabled>
                                                Apply
                                            </button>
                                        </div>

                                        <!-- Pagination -->
                                        <?php if ($total_pages > 1): ?>
                                            <nav>
                                                <ul class="pagination pagination-sm mb-0">
                                                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                                        <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                                            <a class="page-link" href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&category=<?php echo $category_filter; ?>&status=<?php echo $status_filter; ?>&approval=<?php echo $approval_filter; ?>">
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

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../admin/assets/js/admin.js"></script>
    <script>
        // Select all checkbox
        document.getElementById('selectAll')?.addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.item-checkbox');
            checkboxes.forEach(cb => cb.checked = this.checked);
            updateBulkActionButton();
        });

        // Update bulk action button state
        document.querySelectorAll('.item-checkbox').forEach(cb => {
            cb.addEventListener('change', updateBulkActionButton);
        });

        function updateBulkActionButton() {
            const checked = document.querySelectorAll('.item-checkbox:checked').length;
            document.getElementById('bulkActionBtn').disabled = checked === 0;
        }

        function deleteProduct(productId) {
            Swal.fire({
                title: 'Delete Product?',
                text: 'This action cannot be undone.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, Delete',
                cancelButtonText: 'Cancel',
                width: '380px',
                customClass: {
                    popup: 'swal-with-logo'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = `delete_product.php?id=${productId}`;
                }
            });
        }

        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    </script>
</body>
</html>