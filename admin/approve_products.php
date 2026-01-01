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

// Handle product approval
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['approve_product'])) {
    $productId = intval($_POST['product_id']);
    $result = approveProduct($productId, $_SESSION['admin_id']);
    
    if ($result['success']) {
        $_SESSION['success_message'] = 'Product approved successfully!';
    } else {
        $_SESSION['error_message'] = 'Error approving product: ' . $result['message'];
    }
    
    header('Location: approve_products.php');
    exit;
}

// Handle product rejection
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reject_product'])) {
    $productId = intval($_POST['product_id']);
    $reason = trim($_POST['rejection_reason']);
    
    $result = rejectProduct($productId, $_SESSION['admin_id'], $reason);
    
    if ($result['success']) {
        $_SESSION['success_message'] = 'Product rejected successfully!';
    } else {
        $_SESSION['error_message'] = 'Error rejecting product: ' . $result['message'];
    }
    
    header('Location: approve_products.php');
    exit;
}

$pageTitle = 'Approve Products';

// Get pending approval products
$pendingProducts = getPendingApprovalProducts();
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

            <!-- Page Content -->
            <div class="everythingb2c-dashboard-content">
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Product Approvals</h1>
        <span class="badge bg-warning text-dark" style="font-size: 1.2rem;">
            <?php echo count($pendingProducts); ?> Pending
        </span>
    </div>

    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php 
            echo htmlspecialchars($_SESSION['success_message']); 
            unset($_SESSION['success_message']);
            ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php 
            echo htmlspecialchars($_SESSION['error_message']); 
            unset($_SESSION['error_message']);
            ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (empty($pendingProducts)): ?>
        <div class="alert alert-info">
            <i class="fas fa-check-circle"></i> No products pending approval at this time.
        </div>
    <?php else: ?>
        <!-- Pending Products Grid -->
        <div class="row">
            <?php foreach ($pendingProducts as $product): ?>
            <div class="col-xl-4 col-lg-6 mb-4">
                <div class="card shadow h-100">
                    <div class="card-header bg-warning text-dark">
                        <h6 class="m-0 font-weight-bold">
                            <i class="fas fa-clock"></i> Pending Approval
                        </h6>
                    </div>
                    <div class="card-body">
                        <!-- Product Image -->
                        <?php if ($product['main_image']): ?>
                            <?php 
                            // Handle both relative and absolute image paths
                            $imagePath = $product['main_image'];
                            if (strpos($imagePath, 'uploads/') === 0) {
                                // Path already includes 'uploads/', use ../ prefix
                                $imagePath = '../' . $imagePath;
                            } elseif (strpos($imagePath, '/') !== 0) {
                                // Relative path without uploads/, add it
                                $imagePath = '../uploads/' . $imagePath;
                            }
                            ?>
                            <img src="<?php echo htmlspecialchars($imagePath); ?>" 
                                 class="img-fluid mb-3" alt="<?php echo htmlspecialchars($product['name']); ?>"
                                 style="max-height: 200px; width: 100%; object-fit: contain;">
                        <?php else: ?>
                            <div class="bg-light d-flex align-items-center justify-content-center mb-3" 
                                 style="height: 200px;">
                                <i class="fas fa-image fa-3x text-muted"></i>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Product Details -->
                        <h5 class="card-title"><?php echo htmlspecialchars($product['name']); ?></h5>
                        
                        <table class="table table-sm table-borderless">
                            <tr>
                                <th width="40%">Seller:</th>
                                <td><strong><?php echo htmlspecialchars($product['seller_name']); ?></strong></td>
                            </tr>
                            <tr>
                                <th>Category:</th>
                                <td><?php echo htmlspecialchars($product['category_name'] ?? 'N/A'); ?></td>
                            </tr>
                            <tr>
                                <th>SKU:</th>
                                <td><?php echo htmlspecialchars($product['sku']); ?></td>
                            </tr>
                            <tr>
                                <th>HSN:</th>
                                <td><?php echo htmlspecialchars($product['hsn']); ?></td>
                            </tr>
                            <tr>
                                <th>MRP:</th>
                                <td>₹<?php echo number_format($product['mrp'], 2); ?></td>
                            </tr>
                            <tr>
                                <th>Selling Price:</th>
                                <td>₹<?php echo number_format($product['selling_price'], 2); ?></td>
                            </tr>
                            <tr>
                                <th>Stock:</th>
                                <td><?php echo $product['stock_quantity']; ?> units</td>
                            </tr>
                            <tr>
                                <th>GST:</th>
                                <td><?php echo $product['gst_rate']; ?>% (<?php echo $product['gst_type']; ?>)</td>
                            </tr>
                            <tr>
                                <th>Added on:</th>
                                <td><?php echo date('M d, Y', strtotime($product['created_at'])); ?></td>
                            </tr>
                        </table>
                        
                        <!-- Description -->
                        <?php if ($product['description']): ?>
                            <div class="mb-3">
                                <strong>Description:</strong>
                                <p class="small text-muted mb-0">
                                    <?php echo nl2br(htmlspecialchars(substr($product['description'], 0, 200))); ?>
                                    <?php if (strlen($product['description']) > 200) echo '...'; ?>
                                </p>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Action Buttons -->
                        <div class="d-grid gap-2">
                            <button type="button" class="btn btn-success" 
                                    onclick="approveProduct(<?php echo $product['id']; ?>)">
                                <i class="fas fa-check"></i> Approve Product
                            </button>
                            <?php if ($product['rejection_reason']): ?>
                                <!-- Product is already rejected - disable reject button -->
                                <button type="button" class="btn btn-secondary disabled" title="Product is already rejected. Seller needs to resubmit before rejecting again." disabled>
                                    <i class="fas fa-times"></i> Reject Product (Already Rejected)
                                </button>
                            <?php else: ?>
                                <!-- Product is not rejected - allow rejection -->
                                <button type="button" class="btn btn-danger" 
                                        onclick="rejectProduct(<?php echo $product['id']; ?>)">
                                    <i class="fas fa-times"></i> Reject Product
                                </button>
                            <?php endif; ?>
                            <a href="edit_product.php?id=<?php echo $product['id']; ?>" 
                               class="btn btn-info btn-sm">
                                <i class="fas fa-eye"></i> View Full Details
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div><!-- /.container-fluid -->

<!-- Approve Form (Hidden) -->
<form id="approveForm" method="POST" style="display: none;">
    <input type="hidden" name="approve_product" value="1">
    <input type="hidden" name="product_id" id="approve_product_id">
</form>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Reject Product</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <input type="hidden" name="reject_product" value="1">
                <input type="hidden" name="product_id" id="reject_product_id">
                <div class="modal-body">
                    <p>Please provide a reason for rejecting this product. The seller will be notified.</p>
                    <div class="mb-3">
                        <label class="form-label">Rejection Reason *</label>
                        <textarea name="rejection_reason" class="form-control" rows="4" required 
                                  placeholder="e.g., Images are not clear, Description is incomplete, Price is too high..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-times"></i> Reject Product
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function approveProduct(productId) {
    Swal.fire({
        title: 'Approve Product?',
        text: 'This product will become visible on the website.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#9fbe1b',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, Approve',
        cancelButtonText: 'Cancel',
        width: '380px',
        padding: '20px',
        customClass: {
            popup: 'swal-with-logo'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('approve_product_id').value = productId;
            document.getElementById('approveForm').submit();
        }
    });
}

function rejectProduct(productId) {
    Swal.fire({
        title: 'Reject Product?',
        html: '<textarea id="rejection_reason" class="swal2-textarea" placeholder="Enter reason for rejection..." rows="4" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;"></textarea>',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Reject Product',
        cancelButtonText: 'Cancel',
        width: '400px',
        padding: '20px',
        customClass: {
            popup: 'swal-with-logo'
        },
        preConfirm: () => {
            const reason = document.getElementById('rejection_reason').value;
            if (!reason || reason.trim() === '') {
                Swal.showValidationMessage('Please enter a reason for rejection');
                return false;
            }
            return reason;
        }
    }).then((result) => {
        if (result.isConfirmed) {
            // Create a form and submit
            const form = document.createElement('form');
            form.method = 'POST';
            form.innerHTML = `
                <input type="hidden" name="reject_product" value="1">
                <input type="hidden" name="product_id" value="${productId}">
                <input type="hidden" name="rejection_reason" value="${result.value}">
            `;
            document.body.appendChild(form);
            form.submit();
        }
    });
}
</script>

            </div><!-- /.everythingb2c-dashboard-content -->
        </div><!-- /.everythingb2c-main-content -->
    </div><!-- /.everythingb2c-admin-container -->

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/admin.js"></script>
</body>
</html>
