<?php
session_start();
require_once '../config/database.php';
require_once '../includes/seller_functions.php';

if (!isset($_SESSION['seller_id'])) {
    header('Location: login.php');
    exit;
}

$sellerId = $_SESSION['seller_id'];
$pageTitle = 'Reports';

// Always update statistics to ensure fresh data
updateSellerStatistics($sellerId);
$stats = getSellerStatistics($sellerId);
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
                    <h1 class="h3 mb-4">Sales Reports</h1>
                    <div class="row">
                        <div class="col-md-4 mb-4">
                            <div class="card shadow">
                                <div class="card-body text-center">
                                    <i class="fas fa-box fa-3x text-primary mb-3"></i>
                                    <h5>Total Products</h5>
                                    <h2><?php echo $stats['total_products'] ?? 0; ?></h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-4">
                            <div class="card shadow">
                                <div class="card-body text-center">
                                    <i class="fas fa-shopping-cart fa-3x text-success mb-3"></i>
                                    <h5>Total Orders</h5>
                                    <h2><?php echo $stats['total_orders'] ?? 0; ?></h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-4">
                            <div class="card shadow">
                                <div class="card-body text-center">
                                    <i class="fas fa-rupee-sign fa-3x text-info mb-3"></i>
                                    <h5>Total Revenue</h5>
                                    <h2>₹<?php echo number_format($stats['total_revenue'] ?? 0, 2); ?></h2>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card shadow">
                        <div class="card-body">
                            <p class="text-center text-muted py-4">
                                <i class="fas fa-chart-line fa-3x mb-3"></i><br>
                                <strong>Detailed Reports - Coming Soon</strong>
                            </p>
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
