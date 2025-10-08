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

// Handle seller creation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_seller'])) {
    $userId = intval($_POST['user_id']);
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
    
    $result = createSeller($userId, $businessName, $businessData);
    
    if ($result['success']) {
        $_SESSION['success_message'] = 'Seller created successfully!';
    } else {
        $_SESSION['error_message'] = 'Error creating seller: ' . $result['message'];
    }
    
    header('Location: manage_sellers.php');
    exit;
}

// Handle seller status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $sellerId = intval($_POST['seller_id']);
    $isActive = intval($_POST['is_active']);
    
    if (updateSellerStatus($sellerId, $isActive)) {
        $_SESSION['success_message'] = 'Seller status updated successfully!';
    } else {
        $_SESSION['error_message'] = 'Error updating seller status.';
    }
    
    header('Location: manage_sellers.php');
    exit;
}

$pageTitle = 'Manage Sellers';

// Get all sellers
$sellers = getAllSellers();

// Get all users who are not sellers (for creating new sellers)
$stmt = $pdo->prepare("SELECT id, name, email FROM users 
                       WHERE user_role = 'customer' ORDER BY name ASC");
$stmt->execute();
$availableUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
        <h1 class="h3 mb-0 text-gray-800">Manage Sellers</h1>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createSellerModal">
            <i class="fas fa-plus"></i> Add New Seller
        </button>
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

    <!-- Sellers Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">All Sellers</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="sellersTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Business Name</th>
                            <th>Contact Person</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Products</th>
                            <th>Orders</th>
                            <th>Revenue</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($sellers as $seller): ?>
                        <tr>
                            <td><?php echo $seller['id']; ?></td>
                            <td>
                                <strong><?php echo htmlspecialchars($seller['business_name']); ?></strong>
                                <?php if ($seller['gst_number']): ?>
                                    <br><small class="text-muted">GST: <?php echo htmlspecialchars($seller['gst_number']); ?></small>
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($seller['name']); ?></td>
                            <td><?php echo htmlspecialchars($seller['email']); ?></td>
                            <td><?php echo htmlspecialchars($seller['phone'] ?? 'N/A'); ?></td>
                            <td>
                                <span class="badge bg-primary"><?php echo $seller['total_products'] ?? 0; ?></span>
                                <small class="text-muted">(<?php echo $seller['active_products'] ?? 0; ?> active)</small>
                            </td>
                            <td><span class="badge bg-info"><?php echo $seller['total_orders'] ?? 0; ?></span></td>
                            <td>₹<?php echo number_format($seller['total_revenue'] ?? 0, 2); ?></td>
                            <td>
                                <?php if ($seller['is_active']): ?>
                                    <span class="badge bg-success">Active</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">Inactive</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="seller_details.php?id=<?php echo $seller['id']; ?>" 
                                       class="btn btn-info" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="seller_products.php?seller_id=<?php echo $seller['id']; ?>" 
                                       class="btn btn-primary" title="View Products">
                                        <i class="fas fa-box"></i>
                                    </a>
                                    <a href="seller_orders.php?seller_id=<?php echo $seller['id']; ?>" 
                                       class="btn btn-success" title="View Orders">
                                        <i class="fas fa-shopping-cart"></i>
                                    </a>
                                    <button type="button" class="btn btn-warning" 
                                            onclick="editSeller(<?php echo $seller['id']; ?>)" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button" class="btn <?php echo $seller['is_active'] ? 'btn-danger' : 'btn-success'; ?>" 
                                            onclick="toggleSellerStatus(<?php echo $seller['id']; ?>, <?php echo $seller['is_active'] ? 0 : 1; ?>)" 
                                            title="<?php echo $seller['is_active'] ? 'Deactivate' : 'Activate'; ?>">
                                        <i class="fas fa-<?php echo $seller['is_active'] ? 'ban' : 'check'; ?>"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div><!-- /.container-fluid -->

<!-- Create Seller Modal -->
<div class="modal fade" id="createSellerModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create New Seller</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Select User *</label>
                            <select name="user_id" class="form-select" required>
                                <option value="">Choose a user...</option>
                                <?php foreach ($availableUsers as $user): ?>
                                    <option value="<?php echo $user['id']; ?>">
                                        <?php echo htmlspecialchars($user['name'] . ' (' . $user['email'] . ')'); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Business Name *</label>
                            <input type="text" name="business_name" class="form-control" required>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Business Type</label>
                            <select name="business_type" class="form-select">
                                <option value="">Select...</option>
                                <option value="Sole Proprietorship">Sole Proprietorship</option>
                                <option value="Partnership">Partnership</option>
                                <option value="Private Limited">Private Limited</option>
                                <option value="LLP">LLP</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">GST Number</label>
                            <input type="text" name="gst_number" class="form-control" placeholder="27XXXXXXXXXXXXX">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">PAN Number</label>
                            <input type="text" name="pan_number" class="form-control" placeholder="ABCDE1234F">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Commission %</label>
                            <input type="number" name="commission_percentage" class="form-control" value="10" step="0.01" min="0" max="100">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Business Address</label>
                        <textarea name="business_address" class="form-control" rows="2"></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Business Email</label>
                            <input type="email" name="business_email" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Business Phone</label>
                            <input type="text" name="business_phone" class="form-control">
                        </div>
                    </div>
                    
                    <h6 class="mt-3 mb-3">Bank Details</h6>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Account Holder Name</label>
                            <input type="text" name="bank_account_name" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Account Number</label>
                            <input type="text" name="bank_account_number" class="form-control">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">IFSC Code</label>
                            <input type="text" name="bank_ifsc_code" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Bank Name</label>
                            <input type="text" name="bank_name" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="create_seller" class="btn btn-primary">Create Seller</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Status Update Form (Hidden) -->
<form id="statusForm" method="POST" style="display: none;">
    <input type="hidden" name="update_status" value="1">
    <input type="hidden" name="seller_id" id="status_seller_id">
    <input type="hidden" name="is_active" id="status_is_active">
</form>

<script>
// Initialize DataTable
$(document).ready(function() {
    $('#sellersTable').DataTable({
        order: [[0, 'desc']],
        pageLength: 25
    });
});

function toggleSellerStatus(sellerId, newStatus) {
    const action = newStatus ? 'activate' : 'deactivate';
    Swal.fire({
        title: `${action.charAt(0).toUpperCase() + action.slice(1)} Seller?`,
        text: `Are you sure you want to ${action} this seller?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: newStatus ? '#9fbe1b' : '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: `Yes, ${action.charAt(0).toUpperCase() + action.slice(1)}`,
        cancelButtonText: 'Cancel',
        width: '380px',
        customClass: {
            popup: 'swal-with-logo'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('status_seller_id').value = sellerId;
            document.getElementById('status_is_active').value = newStatus;
            document.getElementById('statusForm').submit();
        }
    });
}

function editSeller(sellerId) {
    window.location.href = 'seller_details.php?id=' + sellerId;
}
</script>

            </div><!-- /.everythingb2c-dashboard-content -->
        </div><!-- /.everythingb2c-main-content -->
    </div><!-- /.everythingb2c-admin-container -->

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="assets/js/admin.js"></script>
</body>
</html>
