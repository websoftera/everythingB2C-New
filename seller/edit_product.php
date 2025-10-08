<?php
session_start();
require_once '../config/database.php';
require_once '../includes/seller_functions.php';

if (!isset($_SESSION['seller_id'])) {
    header('Location: login.php');
    exit;
}

$sellerId = $_SESSION['seller_id'];
$productId = isset($_GET['id']) ? intval($_GET['id']) : 0;
$pageTitle = 'Edit Product';

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

                    <?php if (!$product['is_approved']): ?>
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            <strong>Pending Approval:</strong> This product is waiting for admin approval.
                            <?php if ($product['rejection_reason']): ?>
                                <br><strong>Rejection Reason:</strong> <?php echo htmlspecialchars($product['rejection_reason']); ?>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <div class="card shadow">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <?php if ($product['main_image']): ?>
                                        <?php 
                                        // Handle both relative and absolute image paths
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
                                             class="img-fluid mb-3" alt="Product" style="max-height: 300px;">
                                    <?php else: ?>
                                        <div class="bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                                            <i class="fas fa-image fa-3x text-muted"></i>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="col-md-9">
                                    <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                                    <table class="table table-sm">
                                        <tr><th>SKU:</th><td><?php echo htmlspecialchars($product['sku']); ?></td></tr>
                                        <tr><th>HSN:</th><td><?php echo htmlspecialchars($product['hsn']); ?></td></tr>
                                        <tr><th>Category:</th><td><?php echo htmlspecialchars($product['category_name']); ?></td></tr>
                                        <tr><th>MRP:</th><td>₹<?php echo number_format($product['mrp'], 2); ?></td></tr>
                                        <tr><th>Selling Price:</th><td>₹<?php echo number_format($product['selling_price'], 2); ?></td></tr>
                                        <tr><th>Stock:</th><td><?php echo $product['stock_quantity']; ?></td></tr>
                                        <tr><th>GST:</th><td><?php echo $product['gst_rate']; ?>%</td></tr>
                                        <tr>
                                            <th>Status:</th>
                                            <td>
                                                <?php if ($product['is_active']): ?>
                                                    <span class="badge bg-success">Active</span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary">Inactive</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Approval:</th>
                                            <td>
                                                <?php if ($product['is_approved']): ?>
                                                    <span class="badge bg-success">Approved</span>
                                                <?php else: ?>
                                                    <span class="badge bg-warning">Pending</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    </table>
                                    <div class="mb-3">
                                        <strong>Description:</strong>
                                        <p><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
                                    </div>
                                </div>
                            </div>
                            <div class="alert alert-info mt-3">
                                <i class="fas fa-info-circle"></i>
                                <strong>Note:</strong> Product editing form will be available soon. Any changes will require admin re-approval.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../admin/assets/js/admin.js"></script>
</body>
</html>
