<?php
session_start();
require_once '../config/database.php';
require_once '../includes/seller_functions.php';

if (!isset($_SESSION['seller_id'])) {
    header('Location: login.php');
    exit;
}

$sellerId = $_SESSION['seller_id'];
$pageTitle = 'Settings';
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
</head>
<body>
    <div class="everythingb2c-admin-container">
        <?php include 'includes/sidebar.php'; ?>
        <div class="everythingb2c-main-content">
            <?php include 'includes/header.php'; ?>
            <div class="everythingb2c-dashboard-content">
                <div class="container-fluid">
                    <h1 class="h3 mb-4">Business Settings</h1>
                    <div class="card shadow">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Business Information</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <strong>Business Name:</strong>
                                    <p><?php echo htmlspecialchars($sellerDetails['business_name']); ?></p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <strong>Business Type:</strong>
                                    <p><?php echo htmlspecialchars($sellerDetails['business_type'] ?? 'N/A'); ?></p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <strong>GST Number:</strong>
                                    <p><?php echo htmlspecialchars($sellerDetails['gst_number'] ?? 'N/A'); ?></p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <strong>PAN Number:</strong>
                                    <p><?php echo htmlspecialchars($sellerDetails['pan_number'] ?? 'N/A'); ?></p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <strong>Email:</strong>
                                    <p><?php echo htmlspecialchars($sellerDetails['business_email'] ?? $sellerDetails['email']); ?></p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <strong>Phone:</strong>
                                    <p><?php echo htmlspecialchars($sellerDetails['business_phone'] ?? $sellerDetails['phone'] ?? 'N/A'); ?></p>
                                </div>
                                <div class="col-12 mb-3">
                                    <strong>Business Address:</strong>
                                    <p><?php echo nl2br(htmlspecialchars($sellerDetails['business_address'] ?? 'N/A')); ?></p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <strong>Commission Rate:</strong>
                                    <p><?php echo $sellerDetails['commission_percentage']; ?>%</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <strong>Account Status:</strong>
                                    <p>
                                        <?php if ($sellerDetails['is_active']): ?>
                                            <span class="badge bg-success">Active</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Inactive</span>
                                        <?php endif; ?>
                                    </p>
                                </div>
                            </div>
                            <div class="alert alert-info mt-3">
                                <i class="fas fa-info-circle"></i>
                                To update your business information, please contact admin.
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
