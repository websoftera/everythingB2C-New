<div class="everythingb2c-sidebar">
    <div class="everythingb2c-sidebar-header">
        <h3 class="everythingb2c-sidebar-brand"><i class="fas fa-tachometer-alt"></i> EverythingB2C</h3>
    </div>
    
    <nav class="everythingb2c-sidebar-nav">
        <ul class="everythingb2c-nav-list">
            <li class="everythingb2c-nav-item">
                <a class="everythingb2c-nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>" href="index.php">
                    <i class="fas fa-home everythingb2c-nav-icon"></i> Dashboard
                </a>
            </li>
            
            <li class="everythingb2c-nav-item">
                <a class="everythingb2c-nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'products.php' ? 'active' : ''; ?>" href="products.php">
                    <i class="fas fa-box everythingb2c-nav-icon"></i> Products
                </a>
            </li>
            
            <li class="everythingb2c-nav-item">
                <a class="everythingb2c-nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'categories.php' ? 'active' : ''; ?>" href="categories.php">
                    <i class="fas fa-tags everythingb2c-nav-icon"></i> Categories
                </a>
            </li>
            
            <li class="everythingb2c-nav-item">
                <a class="everythingb2c-nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'shipping.php' ? 'active' : ''; ?>" href="shipping.php">
                    <i class="fas fa-shipping-fast everythingb2c-nav-icon"></i> Shipping
                </a>
            </li>
            
            <li class="everythingb2c-nav-item">
                <a class="everythingb2c-nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'manage_pincodes.php' ? 'active' : ''; ?>" href="manage_pincodes.php">
                    <i class="fas fa-map-marker-alt everythingb2c-nav-icon"></i> Manage Pincodes
                </a>
            </li>
            
            <li class="everythingb2c-nav-item">
                <a class="everythingb2c-nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'orders.php' ? 'active' : ''; ?>" href="orders.php">
                    <i class="fas fa-shopping-cart everythingb2c-nav-icon"></i> Orders
                </a>
            </li>
            
            <li class="everythingb2c-nav-item">
                <a class="everythingb2c-nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'users.php' ? 'active' : ''; ?>" href="users.php">
                    <i class="fas fa-users everythingb2c-nav-icon"></i> Users
                </a>
            </li>
            
            <li class="everythingb2c-nav-item">
                <a class="everythingb2c-nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'reports.php' ? 'active' : ''; ?>" href="reports.php">
                    <i class="fas fa-chart-bar everythingb2c-nav-icon"></i> Reports
                </a>
            </li>
            
            <li class="everythingb2c-nav-item">
                <a class="everythingb2c-nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'settings.php' ? 'active' : ''; ?>" href="settings.php">
                    <i class="fas fa-cog everythingb2c-nav-icon"></i> Settings
                </a>
            </li>
        </ul>
    </nav>
    
    <div class="everythingb2c-sidebar-footer">
        <button class="everythingb2c-logout-btn" onclick="window.location.href='logout.php'">
            <i class="fas fa-sign-out-alt"></i> Logout
        </button>
    </div>
</div> 