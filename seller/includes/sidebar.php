<?php
// Include seller functions if available (for pending product count)
if (file_exists(__DIR__ . '/../../includes/seller_functions.php')) {
    require_once __DIR__ . '/../../includes/seller_functions.php';
}

// Get seller info for display
$sellerId = $_SESSION['seller_id'] ?? null;
$permissions = $sellerId ? getSellerPermissions($sellerId) : null;
?>
<div class="everythingb2c-sidebar">
    <div class="everythingb2c-sidebar-header">
        <h3 class="everythingb2c-sidebar-brand">
            <i class="fas fa-store"></i> Seller Portal
        </h3>
    </div>
    
    <nav class="everythingb2c-sidebar-nav">
        <ul class="everythingb2c-nav-list">
            <li class="everythingb2c-nav-item">
                <a class="everythingb2c-nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>" href="index.php">
                    <i class="fas fa-home everythingb2c-nav-icon"></i> Dashboard
                </a>
            </li>
            
            <?php if ($permissions && $permissions['can_manage_products']): ?>
            <li class="everythingb2c-nav-item">
                <a class="everythingb2c-nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'products.php' ? 'active' : ''; ?>" href="products.php">
                    <i class="fas fa-box everythingb2c-nav-icon"></i> My Products
                    <?php
                    // Show pending approval count
                    try {
                        if (isset($pdo) && $sellerId) {
                            $stmt = $pdo->prepare("SELECT COUNT(*) FROM products WHERE seller_id = ? AND is_approved = 0");
                            $stmt->execute([$sellerId]);
                            $pendingCount = $stmt->fetchColumn();
                            if ($pendingCount > 0) {
                                echo "<span class='badge badge-warning' style='margin-left: 10px; background: #ffc107; color: #000; padding: 2px 6px; border-radius: 10px; font-size: 11px;'>{$pendingCount}</span>";
                            }
                        }
                    } catch (Exception $e) {
                        // Silently fail
                    }
                    ?>
                </a>
            </li>
            <?php endif; ?>
            
            <?php if ($permissions && $permissions['can_add_products']): ?>
            <li class="everythingb2c-nav-item">
                <a class="everythingb2c-nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'add_product.php' ? 'active' : ''; ?>" href="add_product.php">
                    <i class="fas fa-plus-circle everythingb2c-nav-icon"></i> Add Product
                </a>
            </li>
            <?php endif; ?>
            
            <?php if ($permissions && $permissions['can_manage_categories']): ?>
            <li class="everythingb2c-nav-item">
                <a class="everythingb2c-nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'categories.php' ? 'active' : ''; ?>" href="categories.php">
                    <i class="fas fa-tags everythingb2c-nav-icon"></i> My Categories
                </a>
            </li>
            <?php endif; ?>
            
            <?php if ($permissions && $permissions['can_view_orders']): ?>
            <li class="everythingb2c-nav-item">
                <a class="everythingb2c-nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'orders.php' ? 'active' : ''; ?>" href="orders.php">
                    <i class="fas fa-shopping-cart everythingb2c-nav-icon"></i> My Orders
                </a>
            </li>
            <?php endif; ?>
            
            <?php if ($permissions && $permissions['can_view_reports']): ?>
            <li class="everythingb2c-nav-item">
                <a class="everythingb2c-nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'reports.php' ? 'active' : ''; ?>" href="reports.php">
                    <i class="fas fa-chart-bar everythingb2c-nav-icon"></i> Reports
                </a>
            </li>
            <?php endif; ?>
            
            <?php if ($permissions && $permissions['can_update_settings']): ?>
            <li class="everythingb2c-nav-item">
                <a class="everythingb2c-nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'settings.php' ? 'active' : ''; ?>" href="settings.php">
                    <i class="fas fa-cog everythingb2c-nav-icon"></i> Settings
                </a>
            </li>
            <?php endif; ?>
        </ul>
    </nav>
    
    <div class="everythingb2c-sidebar-footer">
        <button class="everythingb2c-logout-btn" onclick="window.location.href='logout.php'">
            <i class="fas fa-sign-out-alt"></i> Logout
        </button>
    </div>
</div>
