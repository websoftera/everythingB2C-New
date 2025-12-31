<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';
require_once '../includes/seller_functions.php';

// Check if user is admin
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

$pageTitle = 'All Seller Products';

// Get filter parameters
$sellerId = isset($_GET['seller_id']) ? intval($_GET['seller_id']) : null;
$approvalFilter = isset($_GET['approval']) ? $_GET['approval'] : 'all';

// Build query
$sql = "SELECT p.*, c.name as category_name, s.business_name as seller_name, s.id as seller_id,
        u.name as seller_contact_name, u.email as seller_email
        FROM products p
        LEFT JOIN categories c ON p.category_id = c.id
        LEFT JOIN sellers s ON p.seller_id = s.id
        LEFT JOIN users u ON s.user_id = u.id
        WHERE p.seller_id IS NOT NULL";

$params = [];

if ($sellerId) {
    $sql .= " AND p.seller_id = ?";
    $params[] = $sellerId;
}

if ($approvalFilter === 'pending') {
    $sql .= " AND p.is_approved = 0 AND p.rejection_reason IS NULL";
} elseif ($approvalFilter === 'approved') {
    $sql .= " AND p.is_approved = 1";
} elseif ($approvalFilter === 'rejected') {
    $sql .= " AND p.is_approved = 0 AND p.rejection_reason IS NOT NULL";
}

$sql .= " ORDER BY p.created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get all sellers for filter dropdown
$sellers = getAllSellers();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - EverythingB2C</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
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

            <!-- Page Content -->
            <div class="everythingb2c-dashboard-content">
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">All Seller Products</h1>
    </div>

    <!-- Filters -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Filter by Seller</label>
                    <select name="seller_id" class="form-select">
                        <option value="">All Sellers</option>
                        <?php foreach ($sellers as $seller): ?>
                            <option value="<?php echo $seller['id']; ?>" <?php echo ($sellerId == $seller['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($seller['business_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Approval Status</label>
                    <select name="approval" class="form-select">
                        <option value="all" <?php echo ($approvalFilter === 'all') ? 'selected' : ''; ?>>All Products</option>
                        <option value="approved" <?php echo ($approvalFilter === 'approved') ? 'selected' : ''; ?>>Approved Only</option>
                        <option value="pending" <?php echo ($approvalFilter === 'pending') ? 'selected' : ''; ?>>Pending Approval</option>
                        <option value="rejected" <?php echo ($approvalFilter === 'rejected') ? 'selected' : ''; ?>>Rejected</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">&nbsp;</label>
                    <div>
                        <button type="submit" class="btn btn-primary">Apply Filters</button>
                        <a href="seller_products.php" class="btn btn-secondary">Clear</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Products Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                Seller Products (<?php echo count($products); ?>)
            </h6>
        </div>
        <div class="card-body">
            <?php if (empty($products)): ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> No seller products found.
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-bordered" id="productsTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Image</th>
                                <th>Product Name</th>
                                <th>SKU</th>
                                <th>Seller</th>
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
                                <td><?php echo $product['id']; ?></td>
                                <td>
                                    <?php if ($product['main_image']): ?>
                                        <?php 
                                        // Handle image path
                                        $imgPath = $product['main_image'];
                                        if (strpos($imgPath, 'uploads/') === 0) {
                                            $imgPath = '../' . $imgPath;
                                        } elseif (strpos($imgPath, '/') !== 0 && strpos($imgPath, 'http') !== 0) {
                                            $imgPath = '../uploads/' . $imgPath;
                                        } else {
                                            $imgPath = '../' . $imgPath;
                                        }
                                        ?>
                                        <img src="<?php echo htmlspecialchars($imgPath); ?>" 
                                             alt="Product" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">
                                    <?php else: ?>
                                        <div style="width: 50px; height: 50px; background: #f0f0f0; display: flex; align-items: center; justify-content: center; border-radius: 4px;">
                                            <i class="fas fa-image text-muted"></i>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <strong><?php echo htmlspecialchars($product['name']); ?></strong>
                                </td>
                                <td><?php echo htmlspecialchars($product['sku']); ?></td>
                                <td>
                                    <a href="manage_sellers.php" class="text-decoration-none">
                                        <?php echo htmlspecialchars($product['seller_name']); ?>
                                    </a>
                                    <br><small class="text-muted"><?php echo htmlspecialchars($product['seller_contact_name']); ?></small>
                                </td>
                                <td><?php echo htmlspecialchars($product['category_name'] ?? 'N/A'); ?></td>
                                <td>
                                    <strong>₹<?php echo number_format($product['selling_price'], 2); ?></strong>
                                    <?php if ($product['mrp'] > $product['selling_price']): ?>
                                        <br><small class="text-muted"><del>₹<?php echo number_format($product['mrp'], 2); ?></del></small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge <?php echo $product['stock_quantity'] > 0 ? 'bg-success' : 'bg-danger'; ?>">
                                        <?php echo $product['stock_quantity']; ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($product['is_active']): ?>
                                        <span class="badge bg-success">Active</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Inactive</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($product['is_approved']): ?>
                                        <span class="badge bg-success">
                                            <i class="fas fa-check"></i> Approved
                                        </span>
                                    <?php elseif ($product['rejection_reason']): ?>
                                        <span class="badge bg-danger">
                                            <i class="fas fa-times"></i> Rejected
                                        </span>
                                        <br><small class="text-muted d-block mt-1" title="<?php echo htmlspecialchars($product['rejection_reason']); ?>">
                                            <?php echo substr(htmlspecialchars($product['rejection_reason']), 0, 25) . (strlen($product['rejection_reason']) > 25 ? '...' : ''); ?>
                                        </small>
                                    <?php else: ?>
                                        <span class="badge bg-warning">
                                            <i class="fas fa-clock"></i> Pending
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="edit_product.php?id=<?php echo $product['id']; ?>" 
                                           class="btn btn-info" title="View/Edit">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <?php if (!$product['is_approved']): ?>
                                            <a href="approve_products.php" class="btn btn-warning" title="Approve">
                                                <i class="fas fa-check"></i>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div><!-- /.container-fluid -->

<script>
$(document).ready(function() {
    $('#productsTable').DataTable({
        order: [[0, 'desc']],
        pageLength: 25
    });
});
</script>

            </div><!-- /.everythingb2c-dashboard-content -->
        </div><!-- /.everythingb2c-main-content -->
    </div><!-- /.everythingb2c-admin-container -->

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="assets/js/admin.js"></script>
</body>
</html>
