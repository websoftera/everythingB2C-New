<header class="everythingb2c-admin-header">
    <div class="everythingb2c-header-content">
        <div class="everythingb2c-header-left">
            <button class="everythingb2c-sidebar-toggle" id="sidebarToggle">
                <i class="fas fa-bars"></i>
            </button>
            <h4 class="everythingb2c-header-title everythingb2c-mb-0"><?php echo $pageTitle ?? 'Seller Dashboard'; ?></h4>
        </div>
        
        <div class="everythingb2c-header-right">
            <div class="everythingb2c-user-dropdown">
                <button class="everythingb2c-user-btn" type="button" id="userDropdown">
                    <i class="fas fa-user-circle"></i>
                    <?php echo htmlspecialchars($_SESSION['seller_business_name'] ?? $_SESSION['seller_name'] ?? 'Seller'); ?>
                </button>
                <ul class="everythingb2c-dropdown-menu" id="userDropdownMenu">
                    <li><a class="everythingb2c-dropdown-item" href="settings.php"><i class="fas fa-cog"></i> Settings</a></li>
                    <li><hr class="everythingb2c-dropdown-divider"></li>
                    <li><a class="everythingb2c-dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                </ul>
            </div>
        </div>
    </div>
</header>
