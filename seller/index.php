<?php
session_start();
require_once '../config/database.php';
require_once '../includes/seller_functions.php';

// Check if seller is logged in
if (!isset($_SESSION['seller_id'])) {
    header('Location: login.php');
    exit;
}

$sellerId = $_SESSION['seller_id'];
$pageTitle = 'Seller Dashboard';

// Always update statistics to ensure fresh data
updateSellerStatistics($sellerId);

// Get seller dashboard data
$dashboardData = getSellerDashboardData($sellerId);
$stats = $dashboardData['statistics'];
$permissions = $dashboardData['permissions'];
$sellerDetails = getSellerDetails($sellerId);
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
    <style>
        .seller-stats-card {
            background: linear-gradient(135deg, #9fbe1b 0%, #7a9615 100%);
            color: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .stat-icon {
            font-size: 2.5rem;
            opacity: 0.8;
        }
        .pending-badge {
            background: #ffc107;
            color: #000;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="everythingb2c-admin-container">
        <!-- Sidebar -->
        <?php include 'includes/sidebar.php'; ?>

        <!-- Main Content -->
        <div class="everythingb2c-main-content">
            <!-- Header -->
            <?php include 'includes/header.php'; ?>

            <!-- Dashboard Content -->
            <div class="everythingb2c-dashboard-content">
                <div class="container-fluid">
                    <!-- Welcome Section -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h1 class="h3 mb-0">
                                <i class="fas fa-store text-success"></i> 
                                Welcome, <?php echo htmlspecialchars($sellerDetails['business_name']); ?>!
                            </h1>
                            <p class="text-muted">Manage your products, orders, and business</p>
                        </div>
                    </div>

                    <!-- Statistics Cards -->
                    <div class="row">
                        <!-- Total Products -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-primary shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                Total Products
                                            </div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                <?php echo $stats['total_products'] ?? 0; ?>
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-box fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Active Products -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-success shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                                Active Products
                                            </div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                <?php echo $stats['active_products'] ?? 0; ?>
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Pending Approval -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-warning shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                                Pending Approval
                                            </div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                <?php $pending = count(array_filter($dashboardData['pending_products'] ?? [], function($p) { return !$p['rejection_reason']; })); echo $pending; ?>
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Rejected Products -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-danger shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                                Rejected
                                            </div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                <?php echo count($dashboardData['rejected_products'] ?? []); ?>
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-exclamation-circle fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Total Orders -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-info shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                                Total Orders
                                            </div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                <?php echo $stats['total_orders'] ?? 0; ?>
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-shopping-cart fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Revenue Section -->
                    <div class="row">
                        <div class="col-xl-6 col-lg-6 mb-4">
                            <div class="card shadow h-100">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">
                                        <i class="fas fa-rupee-sign"></i> Revenue Overview
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="text-muted">Total Revenue:</span>
                                            <span class="h4 mb-0 text-success">₹<?php echo number_format($stats['total_revenue'] ?? 0, 2); ?></span>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="text-muted">Commission (<?php echo $sellerDetails['commission_percentage']; ?>%):</span>
                                            <span class="h5 mb-0 text-warning">₹<?php echo number_format($stats['pending_commission'] ?? 0, 2); ?></span>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="text-muted">Your Earnings:</span>
                                            <span class="h4 mb-0 text-primary">₹<?php echo number_format(($stats['total_revenue'] ?? 0) - ($stats['pending_commission'] ?? 0), 2); ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-6 col-lg-6 mb-4">
                            <div class="card shadow h-100">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">
                                        <i class="fas fa-info-circle"></i> Quick Actions
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="d-grid gap-2">
                                        <a href="products.php" class="btn btn-outline-primary">
                                            <i class="fas fa-box"></i> Manage Products
                                        </a>
                                        <a href="add_product.php" class="btn btn-success">
                                            <i class="fas fa-plus"></i> Add New Product
                                        </a>
                                        <a href="orders.php" class="btn btn-outline-info">
                                            <i class="fas fa-shopping-cart"></i> View Orders
                                        </a>
                                        <a href="reports.php" class="btn btn-outline-secondary">
                                            <i class="fas fa-chart-bar"></i> View Reports
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Important Notes -->
                    <?php if (($stats['pending_approval_products'] ?? 0) > 0): ?>
                    <div class="row">
                        <div class="col-12">
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle"></i>
                                <strong>Pending Approval:</strong> You have <?php echo $stats['pending_approval_products']; ?> product(s) waiting for admin approval.
                                These products will be visible on the website once approved.
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php 
                    // Check for rejected products
                    $rejectedProducts = getRejectedProducts($sellerId);
                    if (!empty($rejectedProducts)): 
                    ?>
                    <div class="row">
                        <div class="col-12">
                            <div class="alert alert-danger alert-dismissible fade show">
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                <i class="fas fa-times-circle"></i>
                                <strong>Products Rejected:</strong> You have <?php echo count($rejectedProducts); ?> product(s) that were rejected by the admin. 
                                <br><small>Please review the rejection reasons and make necessary corrections before resubmitting.</small>
                                <div style="margin-top: 10px;">
                                    <?php foreach ($rejectedProducts as $product): ?>
                                        <div style="margin-bottom: 10px; padding: 8px; background: rgba(255,255,255,0.2); border-radius: 4px;">
                                            <strong><?php echo htmlspecialchars($product['name']); ?></strong> (ID: <?php echo $product['id']; ?>)
                                            <br><small>Reason: <?php echo htmlspecialchars($product['rejection_reason']); ?></small>
                                            <br><a href="edit_product.php?id=<?php echo $product['id']; ?>" class="btn btn-sm btn-light mt-1">
                                                <i class="fas fa-edit"></i> Edit & Resubmit
                                            </a>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Business Information -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card shadow">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">
                                        <i class="fas fa-building"></i> Business Information
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p><strong>Business Name:</strong> <?php echo htmlspecialchars($sellerDetails['business_name']); ?></p>
                                            <p><strong>Business Type:</strong> <?php echo htmlspecialchars($sellerDetails['business_type'] ?? 'N/A'); ?></p>
                                            <p><strong>GST Number:</strong> <?php echo htmlspecialchars($sellerDetails['gst_number'] ?? 'N/A'); ?></p>
                                        </div>
                                        <div class="col-md-6">
                                            <p><strong>Email:</strong> <?php echo htmlspecialchars($sellerDetails['business_email'] ?? $sellerDetails['email']); ?></p>
                                            <p><strong>Phone:</strong> <?php echo htmlspecialchars($sellerDetails['business_phone'] ?? $sellerDetails['phone'] ?? 'N/A'); ?></p>
                                            <p><strong>Commission Rate:</strong> <?php echo $sellerDetails['commission_percentage']; ?>%</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div><!-- /.everythingb2c-dashboard-content -->
        </div><!-- /.everythingb2c-main-content -->
    </div><!-- /.everythingb2c-admin-container -->

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../admin/assets/js/admin.js"></script>
</body>
</html>
