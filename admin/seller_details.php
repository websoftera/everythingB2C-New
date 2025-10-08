<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';
require_once '../includes/seller_functions.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

$sellerId = isset($_GET['id']) ? intval($_GET['id']) : 0;
if (!$sellerId) {
    header('Location: manage_sellers.php');
    exit;
}

$pageTitle = 'Seller Details';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_seller'])) {
    $businessName = trim($_POST['business_name']);
    $businessData = [
        'business_type' => trim($_POST['business_type'] ?? ''),
        'gst_number' => trim($_POST['gst_number'] ?? ''),
        'pan_number' => trim($_POST['pan_number'] ?? ''),
        'business_address' => trim($_POST['business_address'] ?? ''),
        'business_email' => trim($_POST['business_email'] ?? ''),
        'business_phone' => trim($_POST['business_phone'] ?? ''),
        'bank_account_name' => trim($_POST['bank_account_name'] ?? ''),
        'bank_account_number' => trim($_POST['bank_account_number'] ?? ''),
        'bank_ifsc_code' => trim($_POST['bank_ifsc_code'] ?? ''),
        'bank_name' => trim($_POST['bank_name'] ?? ''),
        'commission_percentage' => floatval($_POST['commission_percentage'] ?? 10)
    ];
    
    try {
        $stmt = $pdo->prepare("UPDATE sellers SET 
                               business_name = ?, business_type = ?, gst_number = ?, pan_number = ?,
                               business_address = ?, business_email = ?, business_phone = ?,
                               bank_account_name = ?, bank_account_number = ?, bank_ifsc_code = ?, 
                               bank_name = ?, commission_percentage = ?
                               WHERE id = ?");
        
        $stmt->execute([
            $businessName,
            $businessData['business_type'],
            $businessData['gst_number'],
            $businessData['pan_number'],
            $businessData['business_address'],
            $businessData['business_email'],
            $businessData['business_phone'],
            $businessData['bank_account_name'],
            $businessData['bank_account_number'],
            $businessData['bank_ifsc_code'],
            $businessData['bank_name'],
            $businessData['commission_percentage'],
            $sellerId
        ]);
        
        $_SESSION['success_message'] = 'Seller details updated successfully!';
        header('Location: seller_details.php?id=' . $sellerId);
        exit;
        
    } catch (Exception $e) {
        $_SESSION['error_message'] = 'Error updating seller: ' . $e->getMessage();
    }
}

// Get seller details
$seller = getSellerDetails($sellerId);
if (!$seller) {
    header('Location: manage_sellers.php');
    exit;
}

// Always update statistics to ensure fresh data
updateSellerStatistics($sellerId);

// Get seller statistics
$stats = getSellerStatistics($sellerId);

// Get seller permissions
$permissions = getSellerPermissions($sellerId);
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
        <?php include 'includes/sidebar.php'; ?>
        <div class="everythingb2c-main-content">
            <?php include 'includes/header.php'; ?>
            <div class="everythingb2c-dashboard-content">
                <div class="container-fluid">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h1 class="h3 mb-0">
                            <i class="fas fa-store"></i> <?php echo htmlspecialchars($seller['business_name']); ?>
                        </h1>
                        <a href="manage_sellers.php" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Sellers
                        </a>
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

                    <!-- Statistics Row -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card border-left-primary shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Products</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        <?php echo $stats['total_products'] ?? 0; ?>
                                        <small class="text-muted">(<?php echo $stats['active_products'] ?? 0; ?> active)</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card border-left-success shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Orders</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $stats['total_orders'] ?? 0; ?></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card border-left-info shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Revenue</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">₹<?php echo number_format($stats['total_revenue'] ?? 0, 2); ?></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card border-left-warning shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Pending Approval</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $stats['pending_approval_products'] ?? 0; ?></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Seller Information Form -->
                    <form method="POST">
                        <input type="hidden" name="update_seller" value="1">
                        
                        <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">Business Information</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Business Name *</label>
                                        <input type="text" name="business_name" class="form-control" 
                                               value="<?php echo htmlspecialchars($seller['business_name']); ?>" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Business Type</label>
                                        <select name="business_type" class="form-select">
                                            <option value="">Select...</option>
                                            <option value="Sole Proprietorship" <?php echo $seller['business_type'] === 'Sole Proprietorship' ? 'selected' : ''; ?>>Sole Proprietorship</option>
                                            <option value="Partnership" <?php echo $seller['business_type'] === 'Partnership' ? 'selected' : ''; ?>>Partnership</option>
                                            <option value="Private Limited" <?php echo $seller['business_type'] === 'Private Limited' ? 'selected' : ''; ?>>Private Limited</option>
                                            <option value="LLP" <?php echo $seller['business_type'] === 'LLP' ? 'selected' : ''; ?>>LLP</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">GST Number</label>
                                        <input type="text" name="gst_number" class="form-control" 
                                               value="<?php echo htmlspecialchars($seller['gst_number'] ?? ''); ?>">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">PAN Number</label>
                                        <input type="text" name="pan_number" class="form-control" 
                                               value="<?php echo htmlspecialchars($seller['pan_number'] ?? ''); ?>">
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Business Address</label>
                                    <textarea name="business_address" class="form-control" rows="2"><?php echo htmlspecialchars($seller['business_address'] ?? ''); ?></textarea>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Business Email</label>
                                        <input type="email" name="business_email" class="form-control" 
                                               value="<?php echo htmlspecialchars($seller['business_email'] ?? ''); ?>">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Business Phone</label>
                                        <input type="text" name="business_phone" class="form-control" 
                                               value="<?php echo htmlspecialchars($seller['business_phone'] ?? ''); ?>">
                                    </div>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Commission % *</label>
                                    <input type="number" name="commission_percentage" class="form-control" 
                                           value="<?php echo $seller['commission_percentage']; ?>" step="0.01" min="0" max="100" required>
                                </div>
                            </div>
                        </div>

                        <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">Bank Details</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Account Holder Name</label>
                                        <input type="text" name="bank_account_name" class="form-control" 
                                               value="<?php echo htmlspecialchars($seller['bank_account_name'] ?? ''); ?>">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Account Number</label>
                                        <input type="text" name="bank_account_number" class="form-control" 
                                               value="<?php echo htmlspecialchars($seller['bank_account_number'] ?? ''); ?>">
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">IFSC Code</label>
                                        <input type="text" name="bank_ifsc_code" class="form-control" 
                                               value="<?php echo htmlspecialchars($seller['bank_ifsc_code'] ?? ''); ?>">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Bank Name</label>
                                        <input type="text" name="bank_name" class="form-control" 
                                               value="<?php echo htmlspecialchars($seller['bank_name'] ?? ''); ?>">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">Contact Person</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <p><strong>Name:</strong> <?php echo htmlspecialchars($seller['name']); ?></p>
                                    </div>
                                    <div class="col-md-4">
                                        <p><strong>Email:</strong> <?php echo htmlspecialchars($seller['email']); ?></p>
                                    </div>
                                    <div class="col-md-4">
                                        <p><strong>Phone:</strong> <?php echo htmlspecialchars($seller['phone'] ?? 'N/A'); ?></p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <p><strong>Seller Since:</strong> <?php echo date('M d, Y', strtotime($seller['created_at'])); ?></p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>Status:</strong> 
                                            <?php if ($seller['is_active']): ?>
                                                <span class="badge bg-success">Active</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger">Inactive</span>
                                            <?php endif; ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex gap-2 mb-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Seller Details
                            </button>
                            <a href="seller_products.php?seller_id=<?php echo $sellerId; ?>" class="btn btn-info">
                                <i class="fas fa-box"></i> View Products
                            </a>
                            <a href="seller_orders.php?seller_id=<?php echo $sellerId; ?>" class="btn btn-success">
                                <i class="fas fa-shopping-cart"></i> View Orders
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/admin.js"></script>
</body>
</html>
