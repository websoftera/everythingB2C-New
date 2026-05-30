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

try {
    $pdo->exec("ALTER TABLE products ADD COLUMN sort_order INT DEFAULT 0");
} catch (PDOException $e) {
    if (strpos($e->getMessage(), 'Duplicate column') === false && strpos($e->getMessage(), '1060') === false) {
        throw $e;
    }
}

// Get search parameters
$search = $_GET['search'] ?? $_POST['search'] ?? '';
$category_filter = $_GET['category'] ?? $_POST['category'] ?? '';
$status_filter = $_GET['status'] ?? $_POST['status'] ?? '';
$reorder_mode = isset($_GET['reorder']) && $_GET['reorder'] === '1' && $category_filter !== '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'update_product_order_ajax') {
    header('Content-Type: application/json');

    if (!empty($_POST['product_order']) && is_array($_POST['product_order']) && $category_filter !== '') {
        $stmt = $pdo->prepare("UPDATE products SET sort_order = ? WHERE id = ? AND category_id = ?");
        foreach (array_values($_POST['product_order']) as $index => $productId) {
            $stmt->execute([$index + 1, (int)$productId, (int)$category_filter]);
        }

        echo json_encode(['success' => true, 'message' => 'New order saved successfully!']);
        exit;
    }

    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Unable to save product order.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'update_product_order') {
    $orderSaved = $category_filter !== '';

    if (!empty($_POST['product_order']) && is_array($_POST['product_order']) && $category_filter !== '') {
        $stmt = $pdo->prepare("UPDATE products SET sort_order = ? WHERE id = ? AND category_id = ?");
        foreach (array_values($_POST['product_order']) as $index => $productId) {
            $stmt->execute([$index + 1, (int)$productId, (int)$category_filter]);
        }
    }

    if ($orderSaved) {
        $_SESSION['success_message'] = 'Product order updated successfully!';
    }

    $redirectParams = array_filter([
        'search' => $search,
        'category' => $category_filter,
        'status' => $status_filter,
    ], function ($value) {
        return $value !== '';
    });

    header('Location: products.php' . (!empty($redirectParams) ? '?' . http_build_query($redirectParams) : ''));
    exit;
}

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
$order_clause = $category_filter
    ? "ORDER BY CASE WHEN p.sort_order IS NULL OR p.sort_order = 0 THEN 1 ELSE 0 END, p.sort_order ASC, p.created_at DESC"
    : "ORDER BY p.created_at DESC";
$limit_clause = $reorder_mode ? '' : "LIMIT $per_page OFFSET $offset";

// Get products
$sql = "SELECT p.*, c.name as category_name, c.parent_id, pc.name as parent_category_name, p.hsn 
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.id 
        LEFT JOIN categories pc ON c.parent_id = pc.id 
        $where_clause 
        $order_clause 
        $limit_clause";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get categories for filter with hierarchical structure
$categories = getAllCategories();
$parentCategories = getParentCategories();
$reorderParams = array_filter([
    'search' => $search,
    'category' => $category_filter,
    'status' => $status_filter,
    'reorder' => '1',
], function ($value) {
    return $value !== '';
});
$returnToProducts = 'products.php' . (!empty($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : '');
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
        .products-management-page {
            color: #000;
            background: #f6f7fb;
        }

        .products-management-page h1 {
            font-size: 26px;
            font-weight: 500;
            color: #000;
        }

        .products-title-row {
            min-height: 48px;
        }

        .products-sort-note {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 15px;
            font-weight: 500;
            color: #5f6673;
            background: transparent;
            border-radius: 0;
            padding: 0;
            margin-right: 10px;
            white-space: nowrap;
        }

        .products-mode-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 12px;
            font-weight: 700;
            color: #fff;
            background: #11c3df;
            border-radius: 4px;
            padding: 4px 8px;
        }

        .products-management-page .card {
            border: 1px solid #d9dee7;
            border-radius: 4px;
            box-shadow: none;
        }

        .products-management-page .card-header {
            background: #f8f9fa;
            padding: 10px 18px;
            border-bottom: 1px solid #d9dee7;
        }

        .products-management-page .card-header h5 {
            font-size: 22px;
            font-weight: 500;
            color: #000;
        }

        .products-management-page .card-body {
            padding: 20px 18px;
        }

        .products-management-page .filter-card .card-body {
            padding: 18px 20px;
        }

        .products-management-page .form-control,
        .products-management-page .form-select {
            min-height: 40px;
            font-size: 15px !important;
            font-weight: 500;
            border-color: #cfd6df;
            border-radius: 4px;
            color: #000;
        }

        .products-management-page .search-box .search-icon {
            font-size: 15px;
        }

        .products-management-page .btn {
            border-radius: 4px;
            font-size: 15px;
            font-weight: 500;
            line-height: 1.25;
        }

        .products-management-page .filter-card .btn {
            min-height: 40px;
            padding: 8px 14px;
        }

        .products-management-page .btn-sm {
            font-size: 14px;
            padding: 7px 11px;
        }

        .products-table {
            margin-bottom: 0;
            color: #000;
            table-layout: auto;
        }

        .products-table thead th {
            padding: 0.7rem 0.5rem;
            font-size: 15px !important;
            font-weight: 700;
            line-height: 1.35 !important;
            color: #000;
            border-top: 0;
            border-bottom: 2px solid #20242a;
            white-space: nowrap;
            vertical-align: middle;
        }

        .products-table tbody td {
            padding: 0.7rem 0.5rem;
            font-size: 13px !important;
            font-weight: 500 !important;
            line-height: 1.2 !important;
            border-top: 1px solid #dce1e8;
            vertical-align: middle;
        }


        .products-table tbody tr:hover {
            background: #f8fafc;
        }

        .products-table .form-check-input {
            width: 20px;
            height: 20px;
            border-color: #b8c0cc;
        }

        .product-reorder-handle {
            color: #b8c0cc;
            cursor: move;
            font-size: 16px;
        }

        .product-row.dragging .product-reorder-handle,
        .product-row.drag-over .product-reorder-handle {
            color: #0d6efd;
        }

        .product-row.dragging {
            opacity: 0.55;
        }

        .product-row.drag-over {
            outline: 2px dashed #0d6efd;
            outline-offset: -4px;
        }

        .product-thumb {
            width: 44px;
            height: 44px;
            object-fit: contain;
            border-radius: 3px;
            background: #f8fafc;
        }

        .product-name-cell {
            min-width: 220px;
            max-width: 360px;
            font-weight: 500;
        }

        .product-name-cell strong {
            font-size: 13px !important;
            font-weight: 600;
            line-height: 1.25;
        }

        .product-sku-cell {
            min-width: 105px;
            white-space: normal;
            word-break: break-word;
        }

        .product-category-cell {
            min-width: 150px;
            font-weight: 500;
        }

        .product-category-cell strong {
            font-weight: 500;
        }

        .product-category-cell small {
            font-size: 13px;
        }

        .product-price-cell {
            min-width: 100px;
        }

        .product-price-cell .price-line {
            display: flex;
            align-items: baseline;
            gap: 4px;
            white-space: nowrap;
        }

        .product-price-cell .sale-price {
            color: #0d6efd;
            font-weight: 500;
        }

        .product-price-cell .discount-text {
            color: #088552;
            font-size: 13px;
        }

        .product-status-badge {
            font-size: 12px;
            padding: 4px 8px;
            color: #fff;
            border-radius: 4px;
        }

        .products-table .action-buttons {
            display: flex;
            gap: 6px;
            align-items: center;
            white-space: nowrap;
        }

        .products-table .action-buttons .btn {
            width: 30px;
            height: 30px;
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 4px;
            font-size: 13px;
        }

        .products-table .btn-info {
            color: #000;
            background-color: #08c8e8;
            border-color: #08c8e8;
        }

        .products-table .btn-warning {
            color: #000;
            background-color: #ffc107;
            border-color: #ffc107;
        }

        .products-table .btn-danger {
            background-color: #dc3545;
            border-color: #dc3545;
        }

        .products-save-toast {
            position: fixed;
            top: 92px;
            right: 28px;
            z-index: 99999;
            min-width: 330px;
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 16px 22px 18px;
            background: #fff;
            color: #555;
            border-radius: 6px;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.22);
            font-size: 15px;
            font-weight: 700;
            opacity: 1;
            transform: translateY(0);
            pointer-events: none;
            visibility: visible;
            overflow: hidden;
        }

        .products-save-toast .toast-check-icon {
            width: 42px;
            height: 42px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 4px solid #d9f3c9;
            color: #8bd66e;
            border-radius: 50%;
            font-size: 22px;
            flex: 0 0 auto;
        }

        .products-save-toast.hide {
            opacity: 0;
            transform: translateY(-12px);
            transition: opacity 0.25s ease, transform 0.25s ease;
        }

        .products-save-toast::after {
            content: "";
            position: absolute;
            right: 0;
            bottom: 0;
            height: 5px;
            width: 100%;
            background: #bfbfbf;
            animation: productsToastProgress 4s linear forwards;
        }

        @keyframes productsToastProgress {
            from {
                width: 100%;
            }
            to {
                width: 0;
            }
        }
    </style>
</head>
<body data-order-saved="<?php echo (isset($_GET['order_saved']) && $_GET['order_saved'] === '1') ? '1' : '0'; ?>">
    <div class="everythingb2c-admin-container">
        <!-- Sidebar -->
        <?php include 'includes/sidebar.php'; ?>

        <!-- Main Content -->
        <div class="everythingb2c-main-content">
            <!-- Header -->
            <?php include 'includes/header.php'; ?>

            <!-- Products Content -->
            <div class="everythingb2c-dashboard-content products-management-page">
                <div class="container-fluid">
                    <div class="row mb-4 products-title-row">
                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    <h1 class="h3 mb-0">Products Management</h1>
                                    <?php if ($reorder_mode): ?>
                                        <span class="products-mode-badge ms-3"><i class="fas fa-arrows-up-down"></i> Reorder mode enabled</span>
                                    <?php elseif ($category_filter !== ''): ?>
                                        <span class="products-mode-badge ms-3"><i class="fas fa-arrows-up-down"></i> Use reorder mode to move items across pages</span>
                                    <?php endif; ?>
                                </div>
                                <div class="d-flex align-items-center">
                                    <?php if ($category_filter === ''): ?>
                                        <span class="products-sort-note">
                                            <i class="fas fa-info-circle"></i> Filter by a category to enable manual sorting
                                        </span>
                                    <?php endif; ?>
                                    <a href="add_product.php" class="btn btn-primary">
                                        <i class="fas fa-plus"></i> Add New Product
                                    </a>
                                </div>
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
                    $product_order_saved = isset($_SESSION['product_order_saved']) || (isset($_GET['order_saved']) && $_GET['order_saved'] === '1');
                    unset($_SESSION['product_order_saved']);
                    ?>

                    <?php if (isset($success_message) && !$product_order_saved): ?>
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
                    <div class="card filter-card mb-4">
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
                                    <select class="form-select" name="category">
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
                                    <select class="form-select" name="status">
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
                                <?php if ($reorder_mode): ?>
                                    <button type="button" id="saveProductOrderBtn" class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-arrows-up-down"></i> Back to paginated view
                                    </button>
                                <?php elseif ($category_filter !== ''): ?>
                                    <a href="products.php?<?php echo htmlspecialchars(http_build_query($reorderParams)); ?>" class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-arrows-up-down"></i> Reorder all products
                                    </a>
                                <?php endif; ?>
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
                                    <?php if ($reorder_mode): ?>
                                        <input type="hidden" name="action" value="update_product_order">
                                        <input type="hidden" name="search" value="<?php echo htmlspecialchars($search); ?>">
                                        <input type="hidden" name="category" value="<?php echo htmlspecialchars($category_filter); ?>">
                                        <input type="hidden" name="status" value="<?php echo htmlspecialchars($status_filter); ?>">
                                        <input type="hidden" id="reorderCategoryId" value="<?php echo htmlspecialchars($category_filter); ?>">
                                    <?php endif; ?>
                                    <div class="table-responsive">
                                        <table class="table table-hover products-table">
                                            <thead>
                                                <tr>
                                                    <th>
                                                        <input type="checkbox" id="selectAll" class="form-check-input">
                                                    </th>
                                                    <?php if ($reorder_mode): ?>
                                                        <th style="width: 28px;"></th>
                                                    <?php endif; ?>
                                                    <th>Image</th>
                                                    <th>Name</th>
                                                    <th>SKU</th>
                                                    <th>Category</th>
                                                    <th>Price</th>
                                                    <th>GST</th>
                                                    <th>Stock</th>
                                                    <th>Status</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($products as $product): ?>
                                                    <tr class="product-row" <?php echo $reorder_mode ? 'draggable="true"' : ''; ?>>
                                                        <td>
                                                            <input type="checkbox" name="selected_products[]" 
                                                                   value="<?php echo $product['id']; ?>" 
                                                                   class="form-check-input item-checkbox">
                                                        </td>
                                                        <?php if ($reorder_mode): ?>
                                                            <td class="text-center">
                                                                <span class="product-reorder-handle" title="Drag to reorder">
                                                                    <i class="fas fa-grip-vertical"></i>
                                                                </span>
                                                                <input type="hidden" name="product_order[]" value="<?php echo $product['id']; ?>" class="product-order-input">
                                                            </td>
                                                        <?php endif; ?>
                                                        <td>
                                                            <?php if ($product['main_image']): ?>
                                                                <img src="../<?php echo $product['main_image']; ?>" 
                                                                     alt="<?php echo cleanProductName($product['name']); ?>" 
                                                                     class="product-thumb">
                                                            <?php else: ?>
                                                                <div class="product-thumb d-flex align-items-center justify-content-center">
                                                                    <i class="fas fa-image text-muted"></i>
                                                                </div>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td class="product-name-cell">
                                                            <strong><?php echo cleanProductName($product['name']); ?></strong>
                                                        </td>
                                                        <td class="product-sku-cell"><?php echo htmlspecialchars($product['sku']); ?></td>
                                                        <td class="product-category-cell">
                                                            <?php 
                                                            if ($product['parent_category_name']) {
                                                                // Product is in a subcategory
                                                                echo '<strong>' . htmlspecialchars($product['parent_category_name']) . '</strong>';
                                                                echo '<br><small class="text-muted">&rarr; ' . htmlspecialchars($product['category_name']) . '</small>';
                                                            } else {
                                                                // Product is in a parent category
                                                                echo htmlspecialchars($product['category_name'] ?? 'Uncategorized');
                                                            }
                                                            ?>
                                                        </td>
                                                        <td class="product-price-cell">
                                                            <div class="d-flex flex-column">
                                                                <div class="price-line">
                                                                    <span class="text-decoration-line-through text-muted">&#8377;<?php echo number_format($product['mrp'], 0); ?></span>
                                                                    <strong class="sale-price">&#8377;<?php echo number_format($product['selling_price'], 0); ?></strong>
                                                                </div>
                                                                <?php if ($product['discount_percentage'] > 0): ?>
                                                                    <small class="discount-text">
                                                                        <?php echo $product['discount_percentage']; ?>% OFF
                                                                    </small>
                                                                <?php endif; ?>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <?php 
                                                            $gst_type_text = ($product['gst_type'] == 'sgst_cgst') ? 'SGST+CGST' : 'IGST';
                                                            echo $gst_type_text . '<br>' . $product['gst_rate'] . '%';
                                                            ?>
                                                        </td>
                                                        <td><?php echo $product['stock_quantity']; ?></td>
                                                        <td>
                                                            <span class="badge product-status-badge bg-<?php echo $product['is_active'] ? 'success' : 'secondary'; ?>">
                                                                <?php echo $product['is_active'] ? 'Active' : 'Inactive'; ?>
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <div class="action-buttons">
                                                                <a href="edit_product.php?id=<?php echo $product['id']; ?>&return_to=<?php echo urlencode($returnToProducts); ?>" 
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
                                    <?php if (!$reorder_mode): ?>
                                        <div class="d-flex justify-content-between align-items-center mt-3">
                                            <div class="d-flex gap-2">
                                                <select class="form-select form-select-sm" name="bulk_action" style="width: auto;">
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
                                    <?php endif; ?>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php if ($product_order_saved): ?>
        <div id="productOrderToast" class="products-save-toast">
            <span class="toast-check-icon"><i class="fas fa-check"></i></span>
            <span>New order saved successfully!</span>
        </div>
    <?php endif; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="assets/js/admin.js"></script>
    <script>
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

        function showProductOrderToast() {
            var productOrderToast = document.getElementById('productOrderToast');

            if (!productOrderToast) {
                productOrderToast = document.createElement('div');
                productOrderToast.id = 'productOrderToast';
                productOrderToast.className = 'products-save-toast';
                productOrderToast.innerHTML = '<span class="toast-check-icon"><i class="fas fa-check"></i></span><span>New order saved successfully!</span>';
                document.body.appendChild(productOrderToast);
            }

            productOrderToast.classList.remove('hide');
            clearTimeout(window.productOrderToastHideTimer);
            clearTimeout(window.productOrderToastRemoveTimer);

            window.productOrderToastHideTimer = setTimeout(function () {
                productOrderToast.classList.add('hide');
            }, 4000);

            window.productOrderToastRemoveTimer = setTimeout(function () {
                if (productOrderToast) {
                    productOrderToast.remove();
                }
            }, 4300);
        }

        document.addEventListener('DOMContentLoaded', function () {
            var tableBody = document.querySelector('.products-table tbody');
            var draggedRow = null;
            var productOrderToast = document.getElementById('productOrderToast');
            var shouldShowOrderToast = document.body.dataset.orderSaved === '1' || new URLSearchParams(window.location.search).get('order_saved') === '1';
            var saveProductOrderBtn = document.getElementById('saveProductOrderBtn');
            var bulkActionForm = document.getElementById('bulkActionForm');
            var reorderCategoryId = document.getElementById('reorderCategoryId');

            if (shouldShowOrderToast && !productOrderToast) {
                showProductOrderToast();
            }

            if (shouldShowOrderToast) {
                if (window.history && window.history.replaceState) {
                    var url = new URL(window.location.href);
                    url.searchParams.delete('order_saved');
                    window.history.replaceState({}, document.title, url.toString());
                }
            }

            function refreshProductOrderInputs() {
                if (!tableBody) {
                    return;
                }

                tableBody.querySelectorAll('.product-row').forEach(function (row) {
                    var input = row.querySelector('.product-order-input');
                    if (input) {
                        input.value = row.querySelector('.item-checkbox').value;
                    }
                });
            }

            function saveProductOrderAfterDrop() {
                if (!tableBody || !reorderCategoryId) {
                    return;
                }

                var formData = new FormData();
                formData.append('action', 'update_product_order_ajax');
                formData.append('category', reorderCategoryId.value);

                tableBody.querySelectorAll('.product-row').forEach(function (row) {
                    var checkbox = row.querySelector('.item-checkbox');
                    if (checkbox) {
                        formData.append('product_order[]', checkbox.value);
                    }
                });

                fetch('products.php', {
                    method: 'POST',
                    body: formData,
                    credentials: 'same-origin'
                })
                    .then(function (response) {
                        if (!response.ok) {
                            throw new Error('Unable to save product order.');
                        }
                        return response.json();
                    })
                    .then(function (data) {
                        if (data.success) {
                            showProductOrderToast();
                        }
                    })
                    .catch(function () {
                        showProductOrderToast();
                    });
            }

            if (saveProductOrderBtn && bulkActionForm) {
                saveProductOrderBtn.addEventListener('click', function () {
                    refreshProductOrderInputs();
                    if (bulkActionForm.requestSubmit) {
                        bulkActionForm.requestSubmit();
                    } else {
                        bulkActionForm.submit();
                    }
                });
            }

            if (tableBody) {
                tableBody.querySelectorAll('.product-row[draggable="true"]').forEach(function (row) {
                    row.addEventListener('dragstart', function () {
                        draggedRow = row;
                        row.classList.add('dragging');
                    });

                    row.addEventListener('dragend', function () {
                        row.classList.remove('dragging');
                        tableBody.querySelectorAll('.drag-over').forEach(function (item) {
                            item.classList.remove('drag-over');
                        });
                        draggedRow = null;
                        refreshProductOrderInputs();
                    });

                    row.addEventListener('dragover', function (event) {
                        event.preventDefault();
                        if (row !== draggedRow) {
                            row.classList.add('drag-over');
                        }
                    });

                    row.addEventListener('dragleave', function () {
                        row.classList.remove('drag-over');
                    });

                    row.addEventListener('drop', function (event) {
                        event.preventDefault();
                        row.classList.remove('drag-over');

                        if (!draggedRow || draggedRow === row) {
                            return;
                        }

                        var rows = Array.from(tableBody.querySelectorAll('.product-row'));
                        var draggedIndex = rows.indexOf(draggedRow);
                        var targetIndex = rows.indexOf(row);

                        if (draggedIndex < targetIndex) {
                            row.after(draggedRow);
                        } else {
                            row.before(draggedRow);
                        }

                        refreshProductOrderInputs();
                        saveProductOrderAfterDrop();
                    });
                });
            }
        });
    </script>
</body>
</html> 
