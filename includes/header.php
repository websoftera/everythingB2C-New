<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'includes/functions.php';

// Helper: Use this snippet for authentication checks
// if (!isLoggedIn()) {
//     $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
//     header('Location: login.php');
//     exit;
// }

// Build $wishlist_ids for both logged-in and guest users
$wishlist_ids = [];
$wishlistCount = 0;
if (isLoggedIn()) {
    $wishlistItems = getWishlistItems($_SESSION['user_id']);
    foreach ($wishlistItems as $item) {
        $wishlist_ids[] = $item['product_id'];
    }
    $wishlistCount = count($wishlistItems);
} else {
    $wishlistItems = getWishlistItems();
    foreach ($wishlistItems as $item) {
        $wishlist_ids[] = $item['product_id'];
    }
    $wishlistCount = count($wishlistItems);
}

$categories = getAllCategoriesWithRecursiveProductCount();
$categoryTree = buildCategoryTree($categories);
$currentUser = getCurrentUser();

// Get cart count for header
$cartCount = 0;
if (isLoggedIn()) {
    $cartItems = getCartItems($_SESSION['user_id']);
    foreach ($cartItems as $item) {
        $cartCount += (int)$item['quantity'];
    }
} else {
    $cartItems = getCartItems();
    foreach ($cartItems as $item) {
        $cartCount += (int)$item['quantity'];
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $pageTitle ?? 'EverythingB2C'; ?></title>

    <!-- Favicon -->
    <link rel="icon" href="./sitelogo.png" type="image/webp">

    <!-- Font Awesome Preload for better performance -->
    <link rel="preload" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"></noscript>
    
    <!-- Google Fonts - Mulish -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Mulish:wght@200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="Header.css">

    <link rel="stylesheet" href="asset/style/global-colors.css">
    <link rel="stylesheet" href="asset/style/popup.css">
    <link rel="stylesheet" href="asset/style/style.css">
    <link rel="stylesheet" href="asset/style/product-card.css">
    <style>
html, body {
  overflow-x: hidden !important;
  max-width: 100vw;
}
.container, .row {
  max-width: 100vw !important;
  min-width: 0 !important;
  width: 100% !important;
  box-sizing: border-box;
}
/* Hide spinner arrows for quantity input in floating cart */
.cart-qty-input::-webkit-inner-spin-button,
.cart-qty-input::-webkit-outer-spin-button {
  -webkit-appearance: none;
  margin: 0;
}
.cart-qty-input {
  -moz-appearance: textfield;
}

/* Force breadcrumb separator to be ">" only */

/* Cart Added Highlight - Maximum Specificity Override */
html body .cart-added-highlight,
html body .add-to-cart.cart-added-highlight,
html body .add-to-cart-btn.cart-added-highlight,
html body .shop-page-add-to-cart-btn.cart-added-highlight,
html body .related-products-container .add-to-cart-btn.cart-added-highlight,
html body .product-card .add-to-cart-btn.cart-added-highlight,
html body .card .add-to-cart-btn.cart-added-highlight,
html body .discounted-products-section .add-to-cart-btn.cart-added-highlight,
html body .category-section .add-to-cart-btn.cart-added-highlight,
html body .products-grid .add-to-cart-btn.cart-added-highlight,
html body .search-results .add-to-cart-btn.cart-added-highlight,
html body .homepage-section .add-to-cart-btn.cart-added-highlight,
html body .top-products-section .add-to-cart-btn.cart-added-highlight,
html body button.cart-added-highlight,
html body input[type="button"].cart-added-highlight,
html body input[type="submit"].cart-added-highlight,
html body [data-product-id].cart-added-highlight {
  border: 2.5px solid #ffd600 !important;
  background: #9fbe1b !important;
  color: #fff !important;
  box-shadow: 0 0 0 6px rgba(255,214,0,0.18), 0 2px 12px rgba(40,167,69,0.22) !important;
  transition: box-shadow 0.2s, border 0.2s, background 0.2s, color 0.2s !important;
  transform: scale(1.02) !important;
  position: relative !important;
  z-index: 999 !important;
}
.breadcrumb-item::before,
.breadcrumb-item::after {
  content: none !important;
}
.breadcrumb-item:not(:last-child)::after {
  content: ">" !important;
  margin-left: 4px !important;
  color: #adb5bd !important;
  font-weight: bold !important;
}

/* Breadcrumb item colors */
.breadcrumb-item a {
  color: #878787 !important;
  text-decoration: none !important;
  font-weight: bold !important;
}
.breadcrumb-item a:hover {
  color: #878787 !important;
  text-decoration: none !important;
  font-weight: bold !important;
}
.breadcrumb-item.active {
  color: #878787 !important;
  font-weight: bold !important;
}

/* Universal card bottom padding for entire site */
.card,
.product-card,
.products .card,
#wishlist-container .card,
.swiper-slide .card {
  padding-bottom: 0 !important;
  border-radius: 0 !important;
}

#floatingCartPanel.fixed-panel {
  display: flex;
  flex-direction: column;
  position: absolute;
  bottom: 20px;
  right: 0;
  width: 340px;
  max-width: 90vw;
  height: 540px;
  max-height: 90vh;
  background: #fff;
  border: 1px solid #ddd;
  box-shadow: 0 8px 24px rgba(0,0,0,0.15);
  z-index: 2000;
  border-radius: 12px;
  padding: 0;
  overflow: hidden;
}
#floatingCartPanel.fixed-panel .floating-cart-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 14px 18px 8px 18px;
  border-bottom: 1px solid #eee;
  flex-shrink: 0;
}
/* Product list area always scrolls, never summary or actions */
#floatingCartPanel.fixed-panel #floatingCartContent {
  height: 140px;
  max-height: 140px;
  overflow-y: auto;
  min-height: 0;
  padding: 6px 10px 0 10px;
}
#floatingCartPanel.fixed-panel #floatingCartSummary {
  padding: 4px 10px 0 10px;
  font-size: 0.93rem;
  background: #fff;
}
#floatingCartPanel.fixed-panel .floating-cart-actions {
  padding: 10px 18px 10px 18px;
  flex-shrink: 0;
  background: #fff;
  box-shadow: 0 -2px 8px rgba(0,0,0,0.04);
}
#floatingCartPanel.fixed-panel .floating-cart-summary-box {
  padding: 7px 10px; 4px 10px !important;
  margin-bottom: 6px !important;
}
#floatingCartPanel.fixed-panel .floating-cart-summary-box > div {
  margin-bottom: 6px !important;
}
#floatingCartPanel.fixed-panel .floating-cart-summary-box .d-flex {
  margin-bottom: 4px !important;
}
#floatingCartPanel.fixed-panel .floating-cart-summary-box .d-grid {
  margin-top: 8px !important;
  margin-bottom: 4px !important;
}
/* Sticky header styles moved to Header.css */
.floating-cart {
  z-index: 99999 !important;
}
#floatingCartBtn {
  z-index: 99999 !important;
}

/* Product Added Highlight Effect */
.product-added-highlight {
  animation: productAddedPulse 0.6s ease-in-out;
  border: 2px solid #9fbe1b !important;
  box-shadow: 0 0 15px rgba(40, 167, 69, 0.3) !important;
  transform: scale(1.02);
  transition: all 0.3s ease;
}

@keyframes productAddedPulse {
  0% {
    border-color: #9fbe1b;
    box-shadow: 0 0 5px rgba(40, 167, 69, 0.2);
    transform: scale(1);
  }
  50% {
    border-color: #20c997;
    box-shadow: 0 0 20px rgba(40, 167, 69, 0.5);
    transform: scale(1.03);
  }
  100% {
    border-color: #9fbe1b;
    box-shadow: 0 0 15px rgba(40, 167, 69, 0.3);
    transform: scale(1.02);
  }
}

/* Ensure smooth transitions for all product cards */
.card, .product-detail-card, [data-id^="prod-"] {
  transition: all 0.3s ease;
  border: 1px solid #dee2e6;
}
/* Multi-level dropdowns for Bootstrap 5 - Improved UI */
.dropdown-menu {
  min-width: 220px;
  border-radius: 0.6rem;
  box-shadow: 0 8px 32px rgba(0,0,0,0.13);
  background: #fff;
  border: 1px solid #e0e0e0;
  padding: 0.3rem 0;
}
.dropdown-menu .dropdown-item {
  padding: 0.55rem 1.25rem 0.55rem 1.25rem;
  font-size: 1rem;
  color: #222;
  border-radius: 0.4rem;
  transition: background 0.15s, color 0.15s;
}
.dropdown-menu .dropdown-item:hover, .dropdown-menu .dropdown-item:focus {
  background: #f3f8f3;
  color: #9fbe1b;
}
.dropdown-submenu {
  position: relative;
}
.dropdown-submenu > .dropdown-menu {
  top: 0;
  left: 100%;
  margin-top: -1px;
  margin-left: 0.1rem;
  border-radius: 0.6rem;
  min-width: 210px;
  box-shadow: 0 8px 32px rgba(0,0,0,0.13);
  background: #fff;
  border: 1px solid #e0e0e0;
}
.dropdown-menu > .dropdown-submenu > .dropdown-toggle:after {
  content: "\25B6";
  float: right;
  margin-left: 0.7em;
  font-size: 0.95em;
  color: #888;
}
.dropdown-menu .dropdown-menu {
  display: none;
  position: absolute;
  left: 100%;
  top: 0;
  z-index: 1051;
}
.dropdown-menu > .dropdown-submenu:hover > .dropdown-menu {
  display: block;
}
.dropdown-menu > .dropdown-submenu > .dropdown-toggle {
  display: flex;
  justify-content: space-between;
  align-items: center;
  width: 100%;
}
/* Prevent submenu from going off the right edge */
@media (min-width: 992px) {
  .dropdown-menu .dropdown-menu {
    right: auto;
    left: 100%;
  }
}

/* CSS: Make sure pointer events and hover work for both link and submenu */
<style>
.dropdown-menu > .dropdown-submenu > .dropdown-toggle:after {
  content: "\25B6";
  float: right;
  margin-left: 0.7em;
  font-size: 0.95em;
  color: #888;
}
.dropdown-menu > .dropdown-submenu > .dropdown-toggle {
  cursor: pointer;
}
.dropdown-menu.show {
  display: block !important;
}
.dropdown-menu .submenu-arrow.dropdown-toggle::after {
  display: none !important;
}
    .submenu-arrow span {
      color: #222 !important;
    }
    .submenu-arrow.dropdown-toggle::after,
    .submenu-arrow::after {
      display: none !important;
      content: none !important;
    }
</style>
</head>
<body>

<!-- Floating Cart Icon and Panel -->
<?php 
// Hide floating cart on checkout page and cart page, otherwise show/hide based on cart count
$isCheckoutPage = (basename($_SERVER['PHP_SELF']) === 'checkout.php');
$isCartPage = (basename($_SERVER['PHP_SELF']) === 'cart.php');
$displayStyle = ($isCheckoutPage || $isCartPage) ? 'none' : ($cartCount > 0 ? 'flex' : 'none');
?>
<div id="floatingCartBtn" class="floating-cart-btn" style="display: <?php echo $displayStyle; ?>;">
  <span style="position:relative;display:flex;align-items:center;justify-content:center;width:100%;height:100%;">
    <img src="./asset/images/Cart_Icon.png" alt="Cart" class="floating-cart-icon">
    <span id="floatingCartCount" style="position:absolute;top:0px;right:10px;background:none;color:#fff;font-weight:bold;font-size:0.95rem;padding:2px 7px;border-radius:12px;min-width:22px;text-align:center;box-shadow:0 2px 6px rgba(0,0,0,0.12);"><?php echo $cartCount; ?></span>
  </span>
  <!-- Floating Cart Panel (dropdown style) -->
  <div id="floatingCartPanel" class="fixed-panel" style="display:none;">
    <div class="floating-cart-header">
      <h5 style="margin:0;font-weight:bold;">My Cart</h5>
      <button id="closeFloatingCartPanel" style="background:none;border:none;font-size:1.7rem;line-height:1;color:#888;cursor:pointer;">&times;</button>
    </div>
    <div id="floatingCartContent"></div>
    <div id="floatingCartSummary"></div>
    <div class="floating-cart-actions">
      <a href="cart.php" class="btn btn-outline-primary w-100 mb-2" style="padding:6px 0;font-size:0.97rem;">View Full Cart</a>
      <button type="button" class="btn btn-outline-danger w-100" id="floatingRemoveAll" style="padding:6px 0;font-size:0.97rem;">
        <i class="fas fa-trash-alt me-1"></i>Remove All
      </button>
    </div>
  </div>
</div>


<!-- NAVBAR START -->
<nav class="navbar navbar-expand-lg sticky-top bg-white">
    <div class="container-fluid">
        <div class="d-flex align-items-center">
            <!-- Logo -->
            <div class="logo-wrapper d-flex align-items-center">
                <a href="index.php">
                    <img src="./Kichen Page/page2/logo.webp" alt="Logo" class="img-fluid logo" />
                </a>
            </div>
            
            <!-- Mobile Navigation Icons -->
            <div class="d-lg-none ms-auto mobile-nav-icons">
                <!-- Wishlist Icon -->
                <a href="wishlist.php" class="text-decoration-none text-dark mobile-nav-link position-relative me-2">
                    <i class="bi <?php echo $wishlistCount > 0 ? 'bi-heart-fill' : 'bi-heart'; ?>" style="font-size: 24px; color: #DE0085;"></i>
                    <?php if ($wishlistCount > 0): ?>
                        <span class="position-absolute badge rounded-pill wishlist-count-badge" style="background-color: #DE0085; font-size: 10px; min-width: 16px; height: 16px; display: flex; align-items: center; justify-content: center; transform: translate(50%, -50%);">
                            <?php echo $wishlistCount; ?>
                        </span>
                    <?php endif; ?>
                </a>
                
                <!-- Account/Login Dropdown -->
                <div class="dropdown mobile-account-dropdown">
                  <?php if (isLoggedIn()): ?>
                    <a href="#" class="text-decoration-none text-dark mobile-nav-link me-2 d-flex align-items-center dropdown-toggle" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                      <i class="fas fa-user me-1" style="font-size: 24px; color: #007bff;"></i>
                      <div class="user-welcome-text" style="display: block !important;">
                        <div class="welcome-line-1" style="font-size: 10px; line-height: 1; margin: 0; color: #333;">Welcome</div>
                        <div class="welcome-line-2" style="font-size: 10px; line-height: 1; font-weight: 600; margin: 0; color: #333; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><?php echo htmlspecialchars($currentUser['name']); ?></div>
                      </div>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end auth-dropdown-menu">
                      <li><a class="dropdown-item" href="myaccount.php">Customer Account</a></li>
                      <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                      <li><hr class="dropdown-divider"></li>
                      <li><a class="dropdown-item" href="seller/login.php">Seller Login</a></li>
                    </ul>
                  <?php else: ?>
                    <a href="#" class="text-decoration-none text-dark mobile-nav-link me-2 d-flex align-items-center dropdown-toggle" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                      <i class="fas fa-user me-1" style="font-size: 24px; color: #99d052;"></i>
                      <span class="user-signin-text" style="font-size: 10px; font-weight: 600; display: block !important; color: #333; white-space: nowrap; overflow: visible;">Sign In</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end auth-dropdown-menu">
                      <li><a class="dropdown-item" href="login.php">Customer Login</a></li>
                      <li><a class="dropdown-item" href="seller/login.php">Seller Login</a></li>
                    </ul>
                  <?php endif; ?>
                </div>
                
                <!-- Cart Icon -->
                <a href="cart.php" class="text-decoration-none text-dark cart-link position-relative">
                    <img src="./asset/images/Cart_Icon.png" alt="Cart" class="cart-icon" style="width:30px;height:30px;">
                    <span id="cart-count-mobile" class="position-absolute" style="display:<?php echo $cartCount > 0 ? 'inline-block' : 'none'; ?>; top: -2px; right: -2px; transform: translate(50%, -50%); color: var(--dark-green); font-size: 11px; font-weight: 600; background: none; border: none;">
                        <?php echo $cartCount > 0 ? $cartCount : ''; ?>
                    </span>
                </a>
            </div>
        </div>
        
        <!-- Search Form -->
        <form class="d-flex flex-grow-1 mx-4 position-relative" role="search" autocomplete="off" onsubmit="return false;">
            <div class="input-group w-100 flex-wrap">
                <!-- DESKTOP Dropdown -->
                <div class="dropdown-desktop">
                  <button id="categoryDropdownDesktop" class="btn btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown" data-selected-category="all">
                    <span id="selectedCategoryDesktop">All Categories</span>
                  </button>
                  <ul class="dropdown-menu">
                    <li><a class="dropdown-item category-option" href="#" data-category="all">All Categories</a></li>
                    <?php
                    function renderCategoryDropdown($tree, $level = 0) {
                        foreach ($tree as $cat) {
                            $indent = str_repeat('&nbsp;&nbsp;&nbsp;', $level);
                            if (!empty($cat['children'])) {
                                // Parent category as clickable item
                                echo '<li><a class="dropdown-item category-option" href="#" data-category="' . $cat['slug'] . '">' . $indent . '<strong>' . htmlspecialchars($cat['name']) . '</strong></a></li>';
                                renderCategoryDropdown($cat['children'], $level + 1);
                            } else {
                                // Selectable category
                                echo '<li><a class="dropdown-item category-option" href="#" data-category="' . $cat['slug'] . '">' . $indent . htmlspecialchars($cat['name']) . '</a></li>';
                            }
                        }
                    }
                    renderCategoryDropdown($categoryTree);
                    ?>
                  </ul>
                </div>
                <!-- MOBILE Dropdown -->
                <div class="dropdown-mobile">
                  <button id="categoryDropdownMobile" class="btn btn-light dropdown-toggle mobile-category-btn" type="button" data-bs-toggle="dropdown" data-selected-category="all">
                    <span id="selectedCategoryMobile">All</span>
                  </button>
                  <ul class="dropdown-menu">
                    <li><a class="dropdown-item category-option" href="#" data-category="all">All Categories</a></li>
                    <?php renderCategoryDropdown($categoryTree); ?>
                  </ul>
                </div>
                <input class="form-control mobile-search-input" id="headerSearchInput" type="search" name="query" placeholder="Search for Products" aria-label="Search" autocomplete="off">
                <button class="btn btn-primary mobile-search-btn" id="headerSearchBtn" type="button">
                    <i class="bi bi-search"></i>
                </button>
            </div>
            <div id="headerSearchResultsPopup" class="position-absolute w-100" style="z-index: 9999; display: none;"></div>
        </form>
        
        <!-- Desktop Right Section -->
        <div class="d-none d-lg-flex align-items-center">
            <!-- Topbar Navigation Items -->
            <div class="topbar-nav-items d-flex align-items-center me-3">
                <a href="index.php" title="Home" class="text-decoration-none me-3 home-link">
                    <i class="bi bi-house-door home-icon"></i>
                </a>
                <a href="wishlist.php" title="Wishlist" class="text-decoration-none me-3 position-relative wishlist-link">
                    <div class="wishlist-icon-container">
                        <i class="bi <?php echo $wishlistCount > 0 ? 'bi-heart-fill' : 'bi-heart'; ?> wishlist-icon"></i>
                    </div>
                </a>
                <div class="dropdown user-auth-dropdown me-3">
                  <?php if (isLoggedIn()): ?>
                    <a href="#" title="Account" class="text-decoration-none user-account-link d-flex align-items-center dropdown-toggle" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                      <i class="fas fa-user user-account-icon me-2"></i>
                      <div class="user-welcome-text">
                        <div class="welcome-line-1">Welcome</div>
                        <div class="welcome-line-2"><?php echo htmlspecialchars($currentUser['name']); ?></div>
                      </div>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end auth-dropdown-menu">
                      <li><a class="dropdown-item" href="myaccount.php">Customer Account</a></li>
                      <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                      <li><hr class="dropdown-divider"></li>
                      <li><a class="dropdown-item" href="seller/login.php">Seller Login</a></li>
                    </ul>
                  <?php else: ?>
                    <a href="#" title="Sign In" class="text-decoration-none user-signin-link d-flex align-items-center dropdown-toggle" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                      <i class="fas fa-user user-signin-icon me-1"></i>
                      <span class="user-signin-text">Sign In / Register</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end auth-dropdown-menu">
                      <li><a class="dropdown-item" href="login.php">Customer Login</a></li>
                      <li><a class="dropdown-item" href="seller/login.php">Seller Login</a></li>
                    </ul>
                  <?php endif; ?>
                </div>
            </div>
            <!-- Cart Section -->
            <div class="cart-section">
                <a href="cart.php" class="text-decoration-none text-dark cart-link position-relative">
                    <img src="./asset/images/Cart_Icon.png" alt="Cart" class="cart-icon" style="width:45px;height:35px;">
                    <span id="cart-count" class="position-absolute top-0 start-100 translate-middle badge rounded-pill" style="display:<?php echo $cartCount > 0 ? 'inline-block' : 'none'; ?>;">
                        <?php echo $cartCount > 0 ? $cartCount : ''; ?>
                    </span>
                </a>
            </div>
        </div>
    </div>
</nav>

<!-- CATEGORY SCROLL - ONLY MOBILE -->
<div class="category-scroll-container d-block d-lg-none">
    <div class="scroll-wrapper d-flex flex-nowrap overflow-auto">
        <?php function renderMobileCategoryMenu($tree) {
            // Get current category from URL parameter
            $currentCategory = isset($_GET['slug']) ? $_GET['slug'] : '';
            
            foreach ($tree as $cat) {
                // Check if this category is currently active
                $activeClass = ($currentCategory === $cat['slug']) ? 'active' : '';
                
                echo '<a href="category.php?slug=' . $cat['slug'] . '" class="custom-category-item text-center mx-2 ' . $activeClass . '">';
                echo '<img src="./' . $cat['image'] . '" alt="' . htmlspecialchars($cat['name']) . '" class="category-img mb-1">';
                echo '<div class="category-label">' . htmlspecialchars($cat['name']) . '</div>';
                echo '</a>';
                if (!empty($cat['children'])) {
                    renderMobileCategoryMenu($cat['children']);
                }
            }
        }
        renderMobileCategoryMenu($categoryTree); ?>
    </div>
</div>

<!-- Desktop Category Navigation -->
<div class="second-navbar d-none d-lg-block">
    <nav class="navbar navbar-expand-lg navbar-light bg-light category-navbar">
        <div class="container-fluid">
            <div class="navbar-collapse justify-content-center" id="navbarSupportedContent">
                <ul class="navbar-nav category-list mb-2 mb-lg-0 d-flex align-items-center">
                    <?php
function renderCategoryMenu($tree, $level = 0) {
    foreach ($tree as $cat) {
        $hasChildren = !empty($cat['children']);
        $liClass = $hasChildren ? 'nav-item navigationtext dropdown' : 'navigationtext nav-item';
        echo '<li class="' . $liClass . '">';
        
        if ($hasChildren) {
            // Main category as dropdown toggle
            echo '<a class="nav-link navigationtext dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">' . htmlspecialchars($cat['name']) . '</a>';
            echo '<ul class="dropdown-menu">';
            // Parent category as clickable item
            echo '<li><a class="dropdown-item navigationtext parent-category" href="category.php?slug=' . $cat['slug'] . '">' . htmlspecialchars($cat['name']) . '</a></li>';
            echo '<li><hr class="dropdown-divider"></li>';
            // Render all subcategories recursively
            renderSubcategories($cat['children'], $level + 1);
            echo '</ul>';
        } else {
            // Main category link (no children)
            echo '<a class="nav-link navigationtext" href="category.php?slug=' . $cat['slug'] . '">' . htmlspecialchars($cat['name']) . '</a>';
        }
        echo '</li>';
    }
}

function renderSubcategories($subcategories, $level = 1) {
    foreach ($subcategories as $subcat) {
        $hasGrandchildren = !empty($subcat['children']);
        $indentClass = 'subcategory-level-' . $level;
        
        if ($hasGrandchildren) {
            // Subcategory with its own children
            echo '<li><a class="dropdown-item ' . $indentClass . ' subcategory-with-children" href="category.php?slug=' . $subcat['slug'] . '">' . htmlspecialchars($subcat['name']) . '</a></li>';
            // Render grandchildren
            renderSubcategories($subcat['children'], $level + 1);
        } else {
            // Regular subcategory
            echo '<li><a class="dropdown-item ' . $indentClass . '" href="category.php?slug=' . $subcat['slug'] . '">' . htmlspecialchars($subcat['name']) . '</a></li>';
        }
    }
}
renderCategoryMenu($categoryTree);
?>
                </ul>
            </div>
        </div>
    </nav>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="popup/popup.js"></script>
<script src="popup/searchbar.js"></script>
<script src="js/real-time-max-quantity.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// Ensure all dropdown toggles (e.g., Sign In) initialize correctly
document.addEventListener('DOMContentLoaded', function () {
  if (!window.bootstrap || !window.bootstrap.Dropdown) return;
  document.querySelectorAll('[data-bs-toggle="dropdown"]').forEach(function (toggleEl) {
    new bootstrap.Dropdown(toggleEl);
    // Prevent jumping to top on "#" anchors
    if (toggleEl.getAttribute('href') === '#') {
      toggleEl.addEventListener('click', function (e) { e.preventDefault(); });
    }
  });
});
</script>
<script>
// Fallback dropdown toggler if Bootstrap JS fails to wire up (keeps Sign In dropdown clickable)
document.addEventListener('DOMContentLoaded', function () {
  if (window.bootstrap && window.bootstrap.Dropdown) return; // Bootstrap handles it

  function closeAll() {
    document.querySelectorAll('.dropdown-menu.show').forEach(function (menu) {
      menu.classList.remove('show');
      const toggle = menu.previousElementSibling;
      if (toggle && toggle.matches('[data-bs-toggle="dropdown"]')) {
        toggle.setAttribute('aria-expanded', 'false');
      }
    });
  }

  document.addEventListener('click', function (evt) {
    const toggle = evt.target.closest('[data-bs-toggle="dropdown"]');
    if (!toggle) {
      closeAll();
      return;
    }
    const menu = toggle.nextElementSibling;
    if (!menu || !menu.classList.contains('dropdown-menu')) return;
    evt.preventDefault();
    evt.stopPropagation();
    const willOpen = !menu.classList.contains('show');
    closeAll();
    if (willOpen) {
      menu.classList.add('show');
      toggle.setAttribute('aria-expanded', 'true');
    }
  });
});
</script>
<script>
// Force-open Sign In / Account dropdowns even if other scripts interfere
document.addEventListener('DOMContentLoaded', function () {
  const toggles = document.querySelectorAll('.user-auth-dropdown .dropdown-toggle, .user-signin-link.dropdown-toggle, .mobile-account-dropdown .dropdown-toggle');
  const menus = [];

  function closeAll(except) {
    menus.forEach(function (menu) {
      if (menu !== except) {
        menu.classList.remove('show');
        const t = menu.previousElementSibling;
        if (t && t.matches('[data-bs-toggle="dropdown"]')) t.setAttribute('aria-expanded', 'false');
      }
    });
  }

  toggles.forEach(function (toggle) {
    const menu = toggle.nextElementSibling;
    if (menu && menu.classList.contains('dropdown-menu')) {
      menus.push(menu);
      toggle.addEventListener('click', function (e) {
        e.preventDefault();
        e.stopPropagation();
        const willOpen = !menu.classList.contains('show');
        closeAll(willOpen ? menu : null);
        if (willOpen) {
          menu.classList.add('show');
          toggle.setAttribute('aria-expanded', 'true');
        } else {
          menu.classList.remove('show');
          toggle.setAttribute('aria-expanded', 'false');
        }
      });
    }
  });

  document.addEventListener('click', function () { closeAll(null); });
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') closeAll(null);
  });
});
</script>
<script>
// Force-open category dropdowns (All Categories desktop/mobile + category navbar) when clicks are blocked
document.addEventListener('DOMContentLoaded', function () {
  const catToggles = document.querySelectorAll('#categoryDropdownDesktop, #categoryDropdownMobile, .category-navbar .dropdown-toggle');
  const catMenus = [];

  function closeCat(except) {
    catMenus.forEach(function (menu) {
      if (menu !== except) {
        menu.classList.remove('show');
        const t = menu.previousElementSibling;
        if (t && t.matches('[data-bs-toggle="dropdown"]')) t.setAttribute('aria-expanded', 'false');
      }
    });
  }

  catToggles.forEach(function (toggle) {
    const menu = toggle.nextElementSibling;
    if (!menu || !menu.classList.contains('dropdown-menu')) return;
    catMenus.push(menu);
    toggle.addEventListener('click', function (e) {
      e.preventDefault();
      e.stopPropagation();
      const willOpen = !menu.classList.contains('show');
      closeCat(willOpen ? menu : null);
      if (willOpen) {
        menu.classList.add('show');
        toggle.setAttribute('aria-expanded', 'true');
      } else {
        menu.classList.remove('show');
        toggle.setAttribute('aria-expanded', 'false');
      }
    });
  });

  document.addEventListener('click', function () { closeCat(null); });
  document.addEventListener('keydown', function (e) { if (e.key === 'Escape') closeCat(null); });
});
</script>
<script>
// Store the original SweetAlert before overriding
const OriginalSwal = window.Swal;

// Global SweetAlert configuration with logo
const SwalWithLogo = {
    fire: function(options) {
        // Add logo to all SweetAlert dialogs
        const defaultOptions = {
            customClass: {
                popup: 'swal-with-logo',
                icon: 'swal-logo-icon'
            },
            showClass: {
                popup: 'swal2-show swal2-noanimation'
            },
            didOpen: function(popup) {
                // Replace the default SweetAlert icon with our logo
                
                // Try different selectors to find the popup element
                let popupElement = popup.querySelector('.swal2-popup');
                if (!popupElement) {
                    popupElement = popup;
                }
                
                const iconElement = popupElement.querySelector('.swal2-icon');
                
                if (popupElement) {
                    // Hide the default icon if it exists
                    if (iconElement) {
                        iconElement.style.display = 'none';
                    }
                    
                    // Create logo container
                    const logoContainer = document.createElement('div');
                    logoContainer.className = 'swal-logo-container';
                    logoContainer.style.cssText = `
                        position: absolute;
                        top: 5px;
                        left: 0;
                        right: 0;
                        width: 100%;
                        height: 35px;
                        background: transparent;
                        padding: 0;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        z-index: 10002;
                    `;
                    
                    // Create logo image
                    const logoImg = document.createElement('img');
                    logoImg.src = './asset/images/logo.webp';
                    logoImg.alt = 'EverythingB2C';
                    logoImg.style.cssText = `
                        max-width: 200px;
                        height: auto;
                        object-fit: contain;
                        border-radius: 0;
                        display: block;
                    `;
                    
                    // Error handling
                    logoImg.onerror = function() {
                        console.log('Logo image failed to load, showing text fallback');
                        logoContainer.innerHTML = '<span style="color: #007bff; font-size: 18px; font-weight: bold; text-align: center;">EverythingB2C</span>';
                    };
                    
                    logoImg.onload = function() {
                        console.log('Logo image loaded successfully');
                    };
                    
                    logoContainer.appendChild(logoImg);
                    popupElement.appendChild(logoContainer);
                    
                    // Adjust title position
                    const titleElement = popupElement.querySelector('.swal2-title');
                    if (titleElement) {
                        titleElement.style.marginTop = '50px';
                        titleElement.style.marginBottom = '0';
                        titleElement.style.paddingTop = '0';
                    }
                    
                    // Remove default SweetAlert spacing
                    const headerElement = popupElement.querySelector('.swal2-header');
                    if (headerElement) {
                        headerElement.style.marginBottom = '0';
                        headerElement.style.paddingBottom = '0';
                    }
                    
                    const contentElement = popupElement.querySelector('.swal2-content');
                    if (contentElement) {
                        contentElement.style.marginTop = '0';
                        contentElement.style.paddingTop = '0';
                    }
                    
                    // Adjust actions container to remove extra space
                    const actionsElement = popupElement.querySelector('.swal2-actions');
                    if (actionsElement) {
                        actionsElement.style.marginBottom = '0';
                        actionsElement.style.paddingBottom = '0';
                    }
                    
                    // Adjust HTML container
                    const htmlContainer = popupElement.querySelector('.swal2-html-container');
                    if (htmlContainer) {
                        htmlContainer.style.margin = '0';
                        htmlContainer.style.padding = '0';
                        htmlContainer.style.lineHeight = '1.3';
                    }
                    
                    // Force remove any remaining spacing
                    const allElements = popupElement.querySelectorAll('*');
                    allElements.forEach(el => {
                        if (el.classList.contains('swal2-title') || el.classList.contains('swal2-html-container')) {
                            el.style.marginBottom = '0';
                            el.style.paddingBottom = '0';
                        }
                    });
                }
            }
        };
        
        // Merge options
        const mergedOptions = { ...defaultOptions, ...options };
        return OriginalSwal.fire(mergedOptions);
    }
};

// Copy all other SweetAlert methods to our wrapper
Object.keys(OriginalSwal).forEach(key => {
    if (key !== 'fire') {
        SwalWithLogo[key] = OriginalSwal[key];
    }
});

// Override the global Swal.fire to use our custom version
window.Swal = SwalWithLogo;

// Ensure SweetAlert wrapper is always available
if (typeof window.Swal === 'undefined') {
    window.Swal = SwalWithLogo;
}

// Add CSS for SweetAlert logo styling
const style = document.createElement('style');
style.textContent = `
    .swal-with-logo {
        padding-top: 50px !important;
        padding-bottom: 20px !important;
        min-height: auto !important;
    }
    
    .swal-logo-container {
        position: absolute !important;
        top: 30px !important;
        left: 0 !important;
        right: 0 !important;
        width: 100% !important;
        height: 35px !important;
        background: transparent !important;
        padding: 0 !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        z-index: 10002 !important;
    }
    
    .swal-logo-container img {
        max-width: 200px !important;
        height: auto !important;
        object-fit: contain !important;
        border-radius: 0 !important;
        display: block !important;
    }
    
    .swal2-title {
        margin-top: 50px !important;
        margin-bottom: 0 !important;
        padding-top: 0 !important;
        line-height: 1.2 !important;
    }
    
    .swal2-html-container {
        margin: 5px 0 !important;
        padding: 0 !important;
    }
    
    /* Remove default SweetAlert spacing */
    .swal2-popup .swal2-header {
        margin-bottom: 0 !important;
        padding-bottom: 0 !important;
    }
    
    .swal2-popup .swal2-content {
        margin-top: 0 !important;
        padding-top: 0 !important;
    }
    
    .swal2-actions {
        margin-top: 20px !important;
        margin-bottom: 0 !important;
        padding-bottom: 0 !important;
    }
    
    .swal-logo-icon {
        display: none !important;
    }
    
    /* Remove extra space from SweetAlert popup */
    .swal2-popup {
        padding-bottom: 20px !important;
        min-height: auto !important;
        max-height: none !important;
    }
    
    /* Ensure content doesn't have extra margins */
    .swal2-content {
        margin: 0 !important;
        padding: 0 !important;
    }
    
    /* Override all default SweetAlert spacing */
    .swal2-popup * {
        box-sizing: border-box !important;
    }
    
    .swal2-popup .swal2-header {
        margin: 0 !important;
        padding: 0 !important;
    }
    
    .swal2-popup .swal2-title {
        margin: 50px 0 0 0 !important;
        padding: 0 !important;
        line-height: 1.2 !important;
    }
    
    .swal2-popup .swal2-html-container {
        margin: 0 !important;
        padding: 0 !important;
        line-height: 1.3 !important;
    }
    
    /* Target the specific SweetAlert structure */
    .swal2-popup .swal2-header {
        margin: 0 !important;
        padding: 0 !important;
        min-height: 0 !important;
    }
    
    .swal2-popup .swal2-content {
        margin: 0 !important;
        padding: 0 !important;
        min-height: 0 !important;
    }
    
    /* Remove any icon space */
    .swal2-popup .swal2-icon {
        display: none !important;
        margin: 0 !important;
        padding: 0 !important;
        height: 0 !important;
    }
    
    .swal2-popup .swal2-actions {
        margin: 20px 0 0 0 !important;
        padding: 0 !important;
    }
    
    /* Force remove any extra space */
    .swal2-popup .swal2-icon {
        margin: 0 !important;
        padding: 0 !important;
    }
`;
document.head.appendChild(style);

// Function to add logo to any existing SweetAlert popups
function addLogoToExistingPopups() {
    const existingPopups = document.querySelectorAll('.swal2-popup');
    existingPopups.forEach(popup => {
        if (!popup.querySelector('.swal-logo-container')) {
            const iconElement = popup.querySelector('.swal2-icon');
            if (iconElement) {
                iconElement.style.display = 'none';
            }
            
            const logoContainer = document.createElement('div');
            logoContainer.className = 'swal-logo-container';
            logoContainer.style.cssText = `
                position: absolute;
                top: 5px;
                left: 0;
                right: 0;
                width: 100%;
                height: 35px;
                background: transparent;
                padding: 0;
                display: flex;
                align-items: center;
                justify-content: center;
                z-index: 10002;
            `;
            
            const logoImg = document.createElement('img');
            logoImg.src = './asset/images/logo.webp';
            logoImg.alt = 'EverythingB2C';
            logoImg.style.cssText = `
                max-width: 200px;
                height: auto;
                object-fit: contain;
                border-radius: 0;
                display: block;
            `;
            
            logoImg.onerror = function() {
                logoContainer.innerHTML = '<span style="color: #007bff; font-size: 18px; font-weight: bold; text-align: center;">EverythingB2C</span>';
            };
            
            logoContainer.appendChild(logoImg);
            popup.appendChild(logoContainer);
            
            const titleElement = popup.querySelector('.swal2-title');
            if (titleElement) {
                titleElement.style.marginTop = '50px';
                titleElement.style.marginBottom = '0';
                titleElement.style.paddingTop = '0';
            }
            
            // Remove default SweetAlert spacing
            const headerElement = popup.querySelector('.swal2-header');
            if (headerElement) {
                headerElement.style.marginBottom = '0';
                headerElement.style.paddingBottom = '0';
            }
            
            const contentElement = popup.querySelector('.swal2-content');
            if (contentElement) {
                contentElement.style.marginTop = '0';
                contentElement.style.paddingTop = '0';
            }
            
            // Adjust actions container to remove extra space
            const actionsElement = popup.querySelector('.swal2-actions');
            if (actionsElement) {
                actionsElement.style.marginBottom = '0';
                actionsElement.style.paddingBottom = '0';
            }
            
            // Adjust HTML container
            const htmlContainer = popup.querySelector('.swal2-html-container');
            if (htmlContainer) {
                htmlContainer.style.margin = '0';
                htmlContainer.style.padding = '0';
                htmlContainer.style.lineHeight = '1.3';
            }
            
            // Force remove any remaining spacing
            const allElements = popup.querySelectorAll('*');
            allElements.forEach(el => {
                if (el.classList.contains('swal2-title') || el.classList.contains('swal2-html-container')) {
                    el.style.marginBottom = '0';
                    el.style.paddingBottom = '0';
                }
            });
        }
    });
}

// Run the function periodically to catch any popups that might have been missed
setInterval(addLogoToExistingPopups, 100);

// Also run when DOM changes to catch dynamically created popups
const observer = new MutationObserver(function(mutations) {
    mutations.forEach(function(mutation) {
        if (mutation.type === 'childList') {
            mutation.addedNodes.forEach(function(node) {
                if (node.nodeType === Node.ELEMENT_NODE) {
                    if (node.classList && node.classList.contains('swal2-popup')) {
                        setTimeout(addLogoToExistingPopups, 10);
                    }
                    if (node.querySelector && node.querySelector('.swal2-popup')) {
                        setTimeout(addLogoToExistingPopups, 10);
                    }
                }
            });
        }
    });
});

observer.observe(document.body, {
    childList: true,
    subtree: true
});
</script>
<script>
// Initialize Bootstrap dropdowns
document.addEventListener('DOMContentLoaded', function() {
    // Wait for Bootstrap to be fully loaded
    setTimeout(function() {
        // Initialize Bootstrap 5 dropdowns with explicit configuration
        if (typeof bootstrap !== 'undefined' && bootstrap.Dropdown) {
            const dropdownElementList = [].slice.call(document.querySelectorAll('[data-bs-toggle="dropdown"]'));
            dropdownElementList.map(function (dropdownToggleEl) {
                // Ensure dropdown isn't already initialized
                if (!dropdownToggleEl.hasAttribute('data-bs-dropdown-initialized')) {
                    dropdownToggleEl.setAttribute('data-bs-dropdown-initialized', 'true');
                    return new bootstrap.Dropdown(dropdownToggleEl, {
                        autoClose: true,
                        boundary: 'viewport'
                    });
                }
            });
        }
        
        // Force re-initialization specifically for category navigation dropdowns
        const categoryDropdowns = document.querySelectorAll('.category-navbar .dropdown-toggle');
        categoryDropdowns.forEach(dropdown => {
            // Remove any existing Bootstrap instance
            const existingInstance = bootstrap.Dropdown.getInstance(dropdown);
            if (existingInstance) {
                existingInstance.dispose();
            }
            
            // Create new Bootstrap dropdown instance
            if (typeof bootstrap !== 'undefined' && bootstrap.Dropdown) {
                new bootstrap.Dropdown(dropdown, {
                    autoClose: true,
                    boundary: 'viewport'
                });
            }
            
            // Add manual fallback handler
            if (!dropdown.hasAttribute('data-manual-initialized')) {
                dropdown.setAttribute('data-manual-initialized', 'true');
                dropdown.addEventListener('click', function(e) {
                    // Let Bootstrap handle it first, fallback only if needed
                    setTimeout(() => {
                        const dropdownMenu = this.nextElementSibling;
                        if (dropdownMenu && dropdownMenu.classList.contains('dropdown-menu')) {
                            // Check if Bootstrap handled it
                            if (!dropdownMenu.classList.contains('show') && !e.defaultPrevented) {
                                e.preventDefault();
                                // Close other open dropdowns first
                                document.querySelectorAll('.category-navbar .dropdown-menu.show').forEach(menu => {
                                    if (menu !== dropdownMenu) {
                                        menu.classList.remove('show');
                                    }
                                });
                                // Toggle current dropdown
                                dropdownMenu.classList.toggle('show');
                            }
                        }
                    }, 10);
                });
            }
        });
        
        // Close dropdowns when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.dropdown')) {
                document.querySelectorAll('.category-navbar .dropdown-menu.show').forEach(menu => {
                    menu.classList.remove('show');
                });
            }
        });
    }, 100); // Small delay to ensure Bootstrap is ready

    // Ensure sticky header works
    const navbar = document.querySelector('.navbar.sticky-top');
    if (navbar) {
        // Force sticky positioning
        navbar.style.position = 'sticky';
        navbar.style.top = '0';
        navbar.style.zIndex = '1030';
        navbar.style.backgroundColor = '#fff';
        navbar.style.boxShadow = '0 2px 4px rgba(0,0,0,0.1)';
        navbar.style.width = '100%';
    }
});
</script>
<script>
// --- Floating Cart Logic (Dropdown/Panel, Advanced) ---
function updateCartQuantity(cartId, qty, inputElem, btnElem, callback) {
  const content = document.getElementById('floatingCartContent');
  if (qty < 1) qty = 1;
  if (inputElem) inputElem.disabled = true;
  if (btnElem) btnElem.disabled = true;
  fetch('ajax/update-cart.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ cart_id: cartId, quantity: qty })
  })
  .then(res => res.json())
  .then(resp => {
    if (inputElem) inputElem.disabled = false;
    if (btnElem) btnElem.disabled = false;
    if (resp.success) {
      // Only reload if cart is now empty
      if (!content.querySelector('.d-flex.align-items-center')) {
        renderFloatingCart();
        updateFloatingCartCount();
      } else {
        updateFloatingCartSummary();
      }
      if (callback) callback(true);
    } else {
      if (callback) callback(false);
    }
  });
}
// Animation functions for floating cart
function animateFloatingCart(animationType = 'updated') {
  const floatingCartBtn = document.getElementById('floatingCartBtn');
  const floatingCartCount = document.getElementById('floatingCartCount');
  const floatingCartPanel = document.getElementById('floatingCartPanel');
  
  if (!floatingCartBtn) return;
  
  // Don't animate if cart panel is open
  if (floatingCartPanel && floatingCartPanel.style.display === 'block') {
    return;
  }
  
  // Remove any existing animation classes
  floatingCartBtn.classList.remove('cart-updated', 'cart-added', 'cart-removed');
  floatingCartCount.classList.remove('count-updated');
  
  // Add the appropriate animation class
  setTimeout(() => {
    switch(animationType) {
      case 'added':
        floatingCartBtn.classList.add('cart-added');
        break;
      case 'removed':
        floatingCartBtn.classList.add('cart-removed');
        break;
      case 'updated':
      default:
        floatingCartBtn.classList.add('cart-updated');
        break;
    }
    
    // Animate the count badge
    floatingCartCount.classList.add('count-updated');
    
    // Remove animation classes after animation completes
    setTimeout(() => {
      floatingCartBtn.classList.remove('cart-updated', 'cart-added', 'cart-removed');
      floatingCartCount.classList.remove('count-updated');
    }, 1000); // Professional animation duration
  }, 50);
}

function updateFloatingCartCount(animationType = null) {
  fetch('ajax/get_cart_count.php')
    .then(res => res.json())
    .then(data => {
      const oldCount = parseInt(document.getElementById('floatingCartCount').textContent) || 0;
      const newCount = data.cart_count || 0;
      
      document.getElementById('floatingCartCount').textContent = newCount;
      var headerCartCount = document.getElementById('cart-count');
      if (headerCartCount) {
        headerCartCount.textContent = newCount > 0 ? newCount : '';
        headerCartCount.style.display = newCount > 0 ? 'inline-block' : 'none';
      }
      // Update mobile cart count
      var mobileCartCount = document.getElementById('cart-count-mobile');
      if (mobileCartCount) {
        mobileCartCount.textContent = newCount > 0 ? newCount : '';
        mobileCartCount.style.display = newCount > 0 ? 'inline-block' : 'none';
        console.log('Updated mobile cart count:', newCount);
      } else {
        console.log('Mobile cart count element not found');
      }
      // Hide or show floating cart icon (but not on checkout page)
      var floatingCartBtn = document.getElementById('floatingCartBtn');
      if (floatingCartBtn) {
        // Always hide on checkout page and cart page, regardless of cart count
        if (window.location.pathname.endsWith('checkout.php') || window.location.pathname.endsWith('cart.php')) {
          floatingCartBtn.style.display = 'none';
        } else {
          // On other pages, show/hide based on cart count
          const shouldShow = newCount > 0;
          const wasVisible = floatingCartBtn.style.display !== 'none';
          
          floatingCartBtn.style.display = shouldShow ? 'flex' : 'none';
          
          // Trigger animation if cart count changed and cart is visible
          if (shouldShow && (oldCount !== newCount)) {
            // Determine animation type if not provided
            if (!animationType) {
              if (newCount > oldCount) {
                animationType = oldCount === 0 ? 'added' : 'updated';
              } else if (newCount < oldCount) {
                animationType = newCount === 0 ? 'removed' : 'updated';
              }
            }
            
            if (animationType) {
              animateFloatingCart(animationType);
            }
          }
        }
      }
    });
}

function updateFloatingCartSummary() {
  const summary = document.getElementById('floatingCartSummary');
  if (!summary) return;
  
  fetch('ajax/get-cart-summary.php?details=1&t=' + Date.now())
    .then(res => res.json())
    .then(data => {
      if (!data || !data.totals) return;
      
      const totals = data.totals;
      summary.innerHTML = `
        <div class="floating-cart-summary-box" style="border:1px solid #cfd8dc;border-radius:8px;padding:8px 10px 4px 10px;background:#fff;box-shadow:0 2px 8px rgba(0,0,0,0.04);margin-bottom:6px;">
          <div style="font-weight:600;font-size:1.02rem;margin-bottom:6px;">Price Summary</div>
          <div class="d-flex justify-content-between mb-1"><span class="text-muted">Total MRP</span><span style="font-weight:600;text-decoration:line-through;">₹${parseFloat(totals.total_mrp || totals.subtotal).toLocaleString('en-IN', {minimumFractionDigits: 0, maximumFractionDigits: 0})}</span></div>
          <div class="d-flex justify-content-between mb-1"><span class="text-muted">You Pay</span><span style="font-weight:600;">₹${parseFloat(totals.subtotal).toLocaleString('en-IN', {minimumFractionDigits: 0, maximumFractionDigits: 0})}</span></div>
          <div class="d-flex justify-content-between mb-1"><span class="text-muted">Savings</span><span class="fw-bold" style="color:#2e7d32;">₹${parseFloat(totals.total_savings).toLocaleString('en-IN', {minimumFractionDigits: 0, maximumFractionDigits: 0})}</span></div>
          <div class="d-flex justify-content-between mb-1"><span class="text-muted">Delivery <i class='bi bi-info-circle' title='Delivery charges may vary'></i></span><span class="text-danger fw-bold">+ Extra</span></div>
          <div class="d-grid mt-2 mb-1">
            <a href='checkout.php' class='btn btn-success btn-sm fw-bold' style='font-size:0.98rem;'>PROCEED TO CHECKOUT</a>
          </div>
        </div>
      `;
    })
    .catch(error => {
      console.error('Error updating floating cart summary:', error);
    });
}
function renderFloatingCart() {
  const content = document.getElementById('floatingCartContent');
  const summary = document.getElementById('floatingCartSummary');
  content.innerHTML = '<div class="text-center py-4"><div class="spinner-border text-success" role="status"><span class="visually-hidden">Loading...</span></div></div>';
  summary.innerHTML = '';
  fetch('ajax/get-cart-summary.php?details=1&t=' + Date.now())
    .then(res => res.json())
    .then(data => {
      if (!data || !data.cartItems) {
        content.innerHTML = '<div class="text-center text-danger py-4">Could not load cart.</div>';
        return;
      }
      const items = data.cartItems;
      if (!items || items.length === 0) {
        content.innerHTML = '<div class="text-center text-muted py-4">Your cart is empty.</div>';
        summary.innerHTML = '';
        return;
      }
      let itemsHtml = '';
      items.forEach(item => {
        const cartId = item.id; // Use the cart table's primary key
        itemsHtml += `
          <div class="d-flex align-items-center gap-1 mb-2 border-bottom pb-1" style="min-width:0;">
            <img src="./${item.main_image}" alt="${item.name}" style="width:38px;height:38px;object-fit:cover;border-radius:6px;border:1px solid #eee;flex-shrink:0;">
            <div class="flex-grow-1" style="min-width:0;">
              <div style="font-weight:600;font-size:0.97rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">${item.name}</div>
              <div class="text-muted d-flex align-items-center gap-1" style="font-size:0.85rem;">
                <button class="btn btn-xs btn-outline-secondary btn-qty-minus" data-cart-id="${cartId}" ${item.quantity <= 1 ? 'disabled' : ''} style="width:22px;height:22px;padding:0 0 2px 0;line-height:1;">-</button>
                <input type="number" min="1" class="form-control form-control-xs cart-qty-input" data-cart-id="${cartId}" value="${item.quantity}" style="width:32px;text-align:center;display:inline-block;padding:0 2px;font-size:0.9rem;height:22px;">
                <button class="btn btn-xs btn-outline-secondary btn-qty-plus" data-cart-id="${cartId}" style="width:22px;height:22px;padding:0 0 2px 0;line-height:1;">+</button>
                <span class="ms-1">x ₹${parseFloat(item.selling_price).toFixed(2)}</span>
              </div>
            </div>
            <div class="text-end ms-1" style="min-width:54px;">
              <div data-cart-total-id="${cartId}" style="font-weight:700;font-size:0.98rem;">₹${(item.selling_price * item.quantity).toFixed(2)}</div>
              <button class="btn btn-xs btn-outline-danger mt-1 remove-cart-item-btn" data-cart-id="${cartId}" style="padding:0 5px;font-size:0.9rem;"><i class="fas fa-trash"></i></button>
            </div>
          </div>
        `;
      });
      content.innerHTML = itemsHtml;
      // Summary
      const totals = data.totals;
      summary.innerHTML = `
        <div class="floating-cart-summary-box" style="border:1px solid #cfd8dc;border-radius:8px;padding:8px 10px 4px 10px;background:#fff;box-shadow:0 2px 8px rgba(0,0,0,0.04);margin-bottom:6px;">
          <div style="font-weight:600;font-size:1.02rem;margin-bottom:6px;">Price Summary</div>
          <div class="d-flex justify-content-between mb-1"><span class="text-muted">Total MRP</span><span style="font-weight:600;text-decoration:line-through;">₹${parseFloat(totals.total_mrp || totals.subtotal).toLocaleString('en-IN', {minimumFractionDigits: 0, maximumFractionDigits: 0})}</span></div>
          <div class="d-flex justify-content-between mb-1"><span class="text-muted">You Pay</span><span style="font-weight:600;">₹${parseFloat(totals.subtotal).toLocaleString('en-IN', {minimumFractionDigits: 0, maximumFractionDigits: 0})}</span></div>
          <div class="d-flex justify-content-between mb-1"><span class="text-muted">Savings</span><span class="fw-bold" style="color:#2e7d32;">₹${parseFloat(totals.total_savings).toLocaleString('en-IN', {minimumFractionDigits: 0, maximumFractionDigits: 0})}</span></div>
          <div class="d-flex justify-content-between mb-1"><span class="text-muted">Delivery <i class='bi bi-info-circle' title='Delivery charges may vary'></i></span><span class="text-danger fw-bold">+ Extra</span></div>
          <div class="d-grid mt-2 mb-1">
            <a href='checkout.php' class='btn btn-success btn-sm fw-bold' style='font-size:0.98rem;'>PROCEED TO CHECKOUT</a>
          </div>
        </div>
      `;
      // Attach direct handlers to floating cart buttons
      content.querySelectorAll('.btn-qty-minus').forEach(btn => {
        btn.onclick = function(e) {
          e.stopPropagation();
          const cartId = btn.getAttribute('data-cart-id');
          const row = btn.closest('.d-flex.align-items-center');
          const input = content.querySelector('.cart-qty-input[data-cart-id="' + cartId + '"]');
          let qty = parseInt(input.value, 10) || 1;
          if (qty > 1) {
            const prevQty = qty;
            input.value = qty - 1;
            // Update total in DOM
            const priceSpan = row.querySelector('.ms-1');
            const unitPriceText = priceSpan ? priceSpan.textContent : '';
            const unitPrice = parseFloat(unitPriceText.match(/₹(\d+\.?\d*)/)?.[1] || 0);
            const totalDiv = row.querySelector('div[data-cart-total-id]');
            if (totalDiv && unitPrice) {
              totalDiv.textContent = '₹' + ((qty - 1) * unitPrice).toFixed(2);
            }
            updateFloatingCartSummary(); // Real-time update
            updateCartQuantity(cartId, qty - 1, input, btn, function(success, updatedItem) {
              if (!success) {
                input.value = prevQty;
                if (totalDiv && unitPrice) {
                  totalDiv.textContent = '₹' + (prevQty * unitPrice).toFixed(2);
                }
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Could not update cart.',
                        timer: 3000,
                        showConfirmButton: false
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Cart Error',
                        text: 'Could not update cart.',
                        confirmButtonText: 'OK'
                    });
                }
              } else {
                updateFloatingCartCount('updated');
                // Update the per-item total directly
                const unitPrice = parseFloat(priceSpan.textContent.match(/₹(\d+\.?\d*)/)?.[1] || 0);
                if (unitPrice > 0) {
                  updateItemTotal(cartId, qty - 1, unitPrice);
                }
              }
            });
          }
        };
      });
      content.querySelectorAll('.btn-qty-plus').forEach(btn => {
        btn.onclick = async function(e) {
          e.stopPropagation();
          const cartId = btn.getAttribute('data-cart-id');
          const row = btn.closest('.d-flex.align-items-center');
          const input = content.querySelector('.cart-qty-input[data-cart-id="' + cartId + '"]');
          let qty = parseInt(input.value, 10) || 1;
          const prevQty = qty;
          
          // Get product ID from cart item
          let productId = null;
          try {
            const response = await fetch(`ajax/get_product_id_from_cart.php?cart_id=${cartId}`);
            const data = await response.json();
            if (data.success) {
              productId = data.product_id;
            }
          } catch (error) {
            console.error('Error getting product ID:', error);
          }
          
          // Check max quantity if we have product ID
          let newQty = qty + 1;
          if (productId) {
            try {
              const maxResponse = await fetch('ajax/check_max_quantity.php', {
                method: 'POST',
                headers: {
                  'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `product_id=${productId}&quantity=${newQty}`
              });
              const allowedQuantity = 99; // Default max if not set
              const result = await maxResponse.json();
              if (result.error && result.max_quantity || newQty > allowedQuantity) {
                // Show SweetAlert but don't change the input value
                if(!result.max_quantity && newQty > allowedQuantity) {
                  result.message = `Maximum quantity is ${allowedQuantity}.`;
                }
                if (typeof Swal !== 'undefined') {
                  Swal.fire({
                    icon: 'error',
                    title: 'Maximum quantity reached',
                    text: result.message,
                    timer: 4000,
                    showConfirmButton: false
                  });
                } else {
                  Swal.fire({
                      icon: 'error',
                      title: 'Error',
                      text: result.message,
                      confirmButtonText: 'OK'
                  });
                }
                return; // Don't proceed with the update
              }
            } catch (error) {
              console.error('Error checking max quantity:', error);
            }
          }
          
          input.value = newQty;
          // Update total in DOM
          const priceSpan = row.querySelector('.ms-1');
          const unitPriceText = priceSpan ? priceSpan.textContent : '';
          const unitPrice = parseFloat(unitPriceText.match(/₹(\d+\.?\d*)/)?.[1] || 0);
          const totalDiv = row.querySelector('div[data-cart-total-id]');
          if (totalDiv && unitPrice) {
            totalDiv.textContent = '₹' + (newQty * unitPrice).toFixed(2);
          }
          updateFloatingCartSummary(); // Real-time update
          updateCartQuantity(cartId, newQty, input, btn, function(success, updatedItem) {
            if (!success) {
              input.value = prevQty;
              if (totalDiv && unitPrice) {
                totalDiv.textContent = '₹' + (prevQty * unitPrice).toFixed(2);
              }
              if (typeof Swal !== 'undefined') {
                  Swal.fire({
                      icon: 'error',
                      title: 'Error',
                      text: 'Could not update cart.',
                      timer: 3000,
                      showConfirmButton: false
                  });
              } else {
                  Swal.fire({
                      icon: 'error',
                      title: 'Cart Error',
                      text: 'Could not update cart.',
                      confirmButtonText: 'OK'
                  });
              }
            } else {
              updateFloatingCartCount();
              // Update the per-item total directly
              const unitPrice = parseFloat(priceSpan.textContent.match(/₹(\d+\.?\d*)/)?.[1] || 0);
              if (unitPrice > 0) {
                updateItemTotal(cartId, newQty, unitPrice);
              }
            }
          });
        };
      });
      content.querySelectorAll('.remove-cart-item-btn').forEach(btn => {
        btn.onclick = function(e) {
          e.stopPropagation();
          const cartId = btn.getAttribute('data-cart-id');
          const row = btn.closest('.d-flex.align-items-center');
          const rowClone = row.cloneNode(true);
          row.parentNode.removeChild(row);
          fetch('ajax/remove-from-cart.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ cart_id: cartId })
          })
          .then(res => res.json())
          .then(resp => {
            if (resp.success) {
              // Dispatch cart-item-removed event
              if (resp.product_id) {
                console.log('Dispatching cart-item-removed event for product ID:', resp.product_id);
                window.dispatchEvent(new CustomEvent('cart-item-removed', {
                  detail: { productId: resp.product_id }
                }));
                
                // Also dispatch cart-updated event to trigger button re-initialization
                window.dispatchEvent(new Event('cart-updated'));
                
                // Also directly update button state if updateButtonState function exists
                if (typeof updateButtonState === 'function') {
                  updateButtonState(resp.product_id, false);
                }
              } else {
                console.log('No product_id in response:', resp);
              }
              
              // If cart is now empty, reload floating cart
              if (!content.querySelector('.d-flex.align-items-center')) {
                renderFloatingCart();
                updateFloatingCartCount('updated');
              } else {
                updateFloatingCartSummary();
                updateFloatingCartCount('updated');
              }
            } else {
              // Restore row if error
              content.insertBefore(rowClone, content.firstChild);
              if (typeof Swal !== 'undefined') {
                  Swal.fire({
                      icon: 'error',
                      title: 'Error',
                      text: resp.message || 'Could not remove item.',
                      timer: 3000,
                      showConfirmButton: false
                  });
              } else {
                  Swal.fire({
                      icon: 'error',
                      title: 'Remove Error',
                      text: resp.message || 'Could not remove item.',
                      confirmButtonText: 'OK'
                  });
              }
            }
          });
        };
      });
      // Quantity input direct change
      content.querySelectorAll('.cart-qty-input').forEach(input => {
        input.onchange = async function(e) {
          // Store the actual user input value before any parsing
          const originalUserInput = this.value;
          let qty = parseInt(this.value, 10) || 1;
          if (qty < 1) qty = 1;
          
          // Get product ID from cart item
          const cartId = this.getAttribute('data-cart-id');
          let productId = null;
          
          // Try to get product ID from the cart item
          try {
            const response = await fetch(`ajax/get_product_id_from_cart.php?cart_id=${cartId}`);
            const data = await response.json();
            if (data.success) {
              productId = data.product_id;
            }
          } catch (error) {
            console.error('Error getting product ID:', error);
          }
          
          // Check max quantity if we have product ID
          let validationFailed = false;
          if (productId) {
            try {
              const maxResponse = await fetch('ajax/check_max_quantity.php', {
                method: 'POST',
                headers: {
                  'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `product_id=${productId}&quantity=${qty}`
              });
              const allowedQuantity = 99; // Default max if not set
              const result = await maxResponse.json();
              if (result.error && result.max_quantity || qty > allowedQuantity) {
                // Show SweetAlert but don't change the input value
                if(!result.max_quantity && qty > allowedQuantity) {
                  result.message = `Maximum quantity is ${allowedQuantity}.`;
                }
                if (typeof Swal !== 'undefined') {
                  Swal.fire({
                    icon: 'error',
                    title: 'Maximum quantity reached',
                    text: result.message,
                    timer: 4000,
                    showConfirmButton: false
                  });
                } else {
                  Swal.fire({
                      icon: 'error',
                      title: 'Error',
                      text: result.message,
                      confirmButtonText: 'OK'
                  });
                }
                // Reset to original user input and don't proceed with update
                console.log('Validation failed, resetting to:', originalUserInput);
                this.value = originalUserInput;
                validationFailed = true;
              }
            } catch (error) {
              console.error('Error checking max quantity:', error);
            }
          }
          
          // If validation failed, don't proceed with cart update
          if (validationFailed) {
            return;
          }
          
          if (qty > 99) qty = 99; // Enforce max quantity
          this.value = qty; // Update input value to reflect limits
          const row = this.closest('.d-flex.align-items-center');
          
          // Update total price display immediately
          const priceSpan = row.querySelector('.ms-1');
          const unitPriceText = priceSpan ? priceSpan.textContent : '';
          const unitPrice = parseFloat(unitPriceText.match(/₹(\d+\.?\d*)/)?.[1] || 0);
          const totalDiv = row.querySelector('div[data-cart-total-id]');
          if (totalDiv && unitPrice) {
            totalDiv.textContent = '₹' + (qty * unitPrice).toFixed(2);
          }
          
          updateCartQuantity(cartId, qty, this, null, function(success, updatedItem) {
            if (success && updatedItem) {
              updatePerItemTotal(updatedItem.id, updatedItem.selling_price * updatedItem.quantity);
            }
          });
          // Update the per-item total directly after backend update
          setTimeout(() => {
            const unitPrice = parseFloat(priceSpan.textContent.match(/₹(\d+\.?\d*)/)?.[1] || 0);
            if (unitPrice > 0) {
              updateItemTotal(cartId, qty, unitPrice);
            }
          }, 300);
        };
        
        // Real-time price update on input
        input.oninput = function(e) {
          let qty = parseInt(this.value, 10) || 1;
          if (qty < 1) qty = 1;
          if (qty > 99) qty = 99; // Enforce max quantity
          const cartId = this.getAttribute('data-cart-id');
          const row = this.closest('.d-flex.align-items-center');
          
          // Update total price display immediately
          const priceSpan = row.querySelector('.ms-1');
          const unitPriceText = priceSpan ? priceSpan.textContent : '';
          const unitPrice = parseFloat(unitPriceText.match(/₹(\d+\.?\d*)/)?.[1] || 0);
          const totalDiv = row.querySelector('div[data-cart-total-id]');
          if (totalDiv && unitPrice) {
            totalDiv.textContent = '₹' + (qty * unitPrice).toFixed(2);
          }
        };
      });
    })
    .catch(() => {
      content.innerHTML = '<div class="text-center text-danger py-4">Could not load cart.</div>';
      summary.innerHTML = '';
    });
}
// Add this helper function to update only the floating cart summary
function updateFloatingCartSummary() {
  const summary = document.getElementById('floatingCartSummary');
  fetch('ajax/get-cart-summary.php?t=' + Date.now())
    .then(res => res.json())
    .then(data => {
      if (!data || !data.totals) return;
      const totals = data.totals;
      const isEmpty = parseFloat(totals.subtotal) === 0;
      if (isEmpty) {
        summary.innerHTML = `
          <div class="floating-cart-summary-box" style="border:1px solid #cfd8dc;border-radius:8px;padding:24px 16px 16px 16px;background:#fff;box-shadow:0 2px 8px rgba(0,0,0,0.04);margin-bottom:10px;text-align:center;">
            <div style="font-weight:600;font-size:1.1rem;margin-bottom:12px;">Your cart is empty</div>
            <div class="d-grid mt-3 mb-2">
              <a href='shop.php' class='btn btn-outline-secondary btn-lg fw-bold' style='font-size:1.08rem;'>CONTINUE SHOPPING</a>
            </div>
          </div>
        `;
      } else {
        summary.innerHTML = `
          <div class="floating-cart-summary-box" style="border:1px solid #cfd8dc;border-radius:8px;padding:16px 16px 8px 16px;background:#fff;box-shadow:0 2px 8px rgba(0,0,0,0.04);margin-bottom:10px;">
            <div style="font-weight:600;font-size:1.1rem;margin-bottom:12px;">Price Summary</div>
            <div class="d-flex justify-content-between mb-2"><span class="text-muted">Total MRP</span><span style="font-weight:600;">₹${parseFloat(totals.subtotal).toLocaleString('en-IN', {minimumFractionDigits: 0, maximumFractionDigits: 0})}</span></div>
            <div class="d-flex justify-content-between mb-2"><span class="text-muted">Savings</span><span class="fw-bold" style="color:#2e7d32;">₹${parseFloat(totals.total_savings).toLocaleString('en-IN', {minimumFractionDigits: 0, maximumFractionDigits: 0})}</span></div>
            <div class="d-flex justify-content-between mb-2"><span class="text-muted">Delivery Charge <i class='bi bi-info-circle' title='Delivery charges may vary'></i></span><span class="text-danger fw-bold">+ Extra</span></div>
            <div class="d-grid mt-3 mb-2"><a href='checkout.php' class='btn btn-success btn-lg fw-bold' style='font-size:1.08rem;'>PROCEED TO CHECKOUT</a></div>
          </div>
        `;
      }
      // Show/hide floating cart actions (both buttons) if cart is empty
      var cartActionsDiv = document.querySelector('#floatingCartPanel .floating-cart-actions');
      if (cartActionsDiv) {
        cartActionsDiv.style.display = isEmpty ? 'none' : '';
      }
    });
}
// Show/hide panel as dropdown
// const floatingCartBtn = document.getElementById('floatingCartBtn');
// const floatingCartPanel = document.getElementById('floatingCartPanel');
// const closeFloatingCartPanel = document.getElementById('closeFloatingCartPanel');
// floatingCartBtn.onclick = function(e) {
//   e.stopPropagation();
//   if (floatingCartPanel.style.display === 'block') {
//     floatingCartPanel.style.display = 'none';
//     return;
//   }
//   floatingCartPanel.style.display = 'block';
//   renderFloatingCart();
// };
// closeFloatingCartPanel.onclick = function(e) {
//   e.stopPropagation();
//   floatingCartPanel.style.display = 'none';
// };
// Hide panel when clicking outside
window.addEventListener('click', function(e) {
  const floatingCartBtn = document.getElementById('floatingCartBtn');
  const floatingCartPanel = document.getElementById('floatingCartPanel');
  if (floatingCartPanel && floatingCartPanel.style.display === 'block' && !floatingCartBtn.contains(e.target)) {
    floatingCartPanel.style.display = 'none';
  }
});
// Prevent closing when clicking inside the panel
const floatingCartPanel = document.getElementById('floatingCartPanel');
if (floatingCartPanel) {
  floatingCartPanel.onclick = function(e) {
    e.stopPropagation();
  };
}
// Close button logic
const closeFloatingCartPanel = document.getElementById('closeFloatingCartPanel');
if (closeFloatingCartPanel) {
  closeFloatingCartPanel.onclick = function(e) {
    e.stopPropagation();
    const floatingCartPanel = document.getElementById('floatingCartPanel');
    if (floatingCartPanel) floatingCartPanel.style.display = 'none';
  };
}
// Update cart count on page load and every 30s
updateFloatingCartCount(); // No animation on initial load
setInterval(() => updateFloatingCartCount(), 30000); // No animation on periodic updates

// Function to update mobile navigation icons
function updateMobileNavIcons() {
  // Update wishlist icon
  const wishlistLink = document.querySelector('.mobile-nav-link[href*="wishlist.php"]');
  if (wishlistLink) {
    const wishlistIcon = wishlistLink.querySelector('i');
    const wishlistBadge = wishlistLink.querySelector('.badge');
    
    // Fetch current wishlist count
    fetch('ajax/get_wishlist_count.php')
      .then(res => res.json())
      .then(data => {
        const count = data.wishlist_count || 0;
        
        // Update icon
        if (wishlistIcon) {
          wishlistIcon.className = count > 0 ? 'bi bi-heart-fill' : 'bi bi-heart';
        }
        
        // Update badge
        if (wishlistBadge) {
          if (count > 0) {
            wishlistBadge.textContent = count;
            wishlistBadge.style.display = 'flex';
          } else {
            wishlistBadge.style.display = 'none';
          }
        }
      })
      .catch(err => console.log('Error updating wishlist count:', err));
  }
}

// Listen for global cart updates (from add-to-cart or other actions)
window.addEventListener('cart-updated', function(event) {
  renderFloatingCart();
  // Use animation type from event detail if provided
  const animationType = event.detail && event.detail.action ? event.detail.action : 'updated';
  updateFloatingCartCount(animationType);
});

// Listen for wishlist updates
window.addEventListener('wishlist-updated', function(event) {
  updateMobileNavIcons();
});

// Update mobile nav icons on page load
document.addEventListener('DOMContentLoaded', function() {
  updateMobileNavIcons();
});

// Floating Cart Remove All functionality
document.addEventListener('DOMContentLoaded', function() {
  const floatingRemoveAllBtn = document.getElementById('floatingRemoveAll');
  if (floatingRemoveAllBtn) {
    floatingRemoveAllBtn.addEventListener('click', function() {
      Swal.fire({
        title: 'Remove All Items?',
        text: 'Do you want to remove all items from your cart? This action cannot be undone.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes',
        cancelButtonText: 'No'
      }).then((result) => {
        if (result.isConfirmed) {
        // Show loading state
        const button = this;
        const originalText = button.innerHTML;
        button.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Removing...';
        button.disabled = true;
        
        fetch('ajax/remove-all-cart.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          }
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            // Dispatch cart-removed-all event
            if (data.remove_all) {
              console.log('Dispatching cart-removed-all event');
              window.dispatchEvent(new CustomEvent('cart-removed-all'));
              
              // Also dispatch cart-updated event to trigger button re-initialization
              window.dispatchEvent(new Event('cart-updated'));
            }
            
            // Show success message
                            if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: 'All items have been removed from your cart.',
                        timer: 3000,
                        showConfirmButton: false
                    });
                } else {
                    Swal.fire({
                        icon: 'success',
                        title: 'Cart Cleared',
                        text: 'All items have been removed from your cart.',
                        confirmButtonText: 'OK'
                    });
                }
            // Update floating cart
            renderFloatingCart();
            updateFloatingCartCount();
            // Close floating cart panel
            const floatingCartPanel = document.getElementById('floatingCartPanel');
            if (floatingCartPanel) {
              floatingCartPanel.style.display = 'none';
            }
          } else {
                            if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to remove items: ' + (data.message || 'Unknown error'),
                        timer: 4000,
                        showConfirmButton: false
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Remove Failed',
                        text: 'Failed to remove items: ' + (data.message || 'Unknown error'),
                        confirmButtonText: 'OK'
                    });
                }
            // Reset button
            button.innerHTML = originalText;
            button.disabled = false;
          }
        })
        .catch(error => {
          console.error('Error:', error);
                      if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred while removing items from cart.',
                    timer: 4000,
                    showConfirmButton: false
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred while removing items from cart.',
                    confirmButtonText: 'OK'
                });
            }
          // Reset button
          button.innerHTML = originalText;
          button.disabled = false;
        });
        }
      });
    });
  }
});

// Robust floating cart drag logic with click/drag separation and debug logs
(function() {
  const btn = document.getElementById('floatingCartBtn');
  const panel = document.getElementById('floatingCartPanel');
  let isMouseDown = false;
  let isDragging = false;
  let offsetX = 0, offsetY = 0;
  let startX = 0, startY = 0;

  // Load position from localStorage
  function loadPosition() {
    const pos = localStorage.getItem('floatingCartPosition');
    if (pos) {
      try {
        const { left, top } = JSON.parse(pos);
        if (btn) {
          btn.style.left = left + 'px';
          btn.style.top = top + 'px';
          btn.style.right = 'auto';
          btn.style.bottom = 'auto';
        }
        if (panel) {
          panel.style.left = left + 'px';
          panel.style.top = top + 'px';
          panel.style.right = 'auto';
          panel.style.bottom = 'auto';
        }
      } catch(e) {console.log('[FloatingCart][DEBUG] Error loading position:', e);}
    }
  }

  // Save position to localStorage
  function savePosition(left, top) {
    localStorage.setItem('floatingCartPosition', JSON.stringify({ left, top }));
  }

  // Move both elements
  function moveBoth(left, top) {
    if (btn) {
      btn.style.left = left + 'px';
      btn.style.top = top + 'px';
      btn.style.right = 'auto';
      btn.style.bottom = 'auto';
    }
    if (panel) {
      panel.style.left = left + 'px';
      panel.style.top = top + 'px';
      panel.style.right = 'auto';
      panel.style.bottom = 'auto';
    }
  }

  // Drag handler for both btn and panel header
  function onMouseDown(e) {
    isMouseDown = true;
    isDragging = false;
    startX = e.clientX;
    startY = e.clientY;
    const rect = btn.getBoundingClientRect();
    offsetX = startX - rect.left;
    offsetY = startY - rect.top;
    if (btn) btn.style.transition = 'none';
    if (panel) panel.style.transition = 'none';
    document.body.style.userSelect = 'none';
  }
  function onMouseMove(e) {
    if (!isMouseDown) return;
    const dx = Math.abs(e.clientX - startX);
    const dy = Math.abs(e.clientY - startY);
    if (dx > 5 || dy > 5) {
      isDragging = true;
      let x = e.clientX - offsetX;
      let y = e.clientY - offsetY;
      x = Math.max(0, Math.min(window.innerWidth - (btn ? btn.offsetWidth : 60), x));
      y = Math.max(0, Math.min(window.innerHeight - (btn ? btn.offsetHeight : 60), y));
      moveBoth(x, y);
    }
  }
  function onMouseUp(e) {
    if (isMouseDown) {
      if (isDragging) {
        // Save position if dragged
        const left = btn ? parseInt(btn.style.left) : 0;
        const top = btn ? parseInt(btn.style.top) : 0;
        savePosition(left, top);
      }
      isMouseDown = false;
      isDragging = false;
      if (btn) btn.style.transition = '';
      if (panel) panel.style.transition = '';
      document.body.style.userSelect = '';
    }
  }

  // Attach drag to both btn and panel header
  function attachDrag() {
    if (btn) btn.addEventListener('mousedown', onMouseDown);
    if (panel) {
      const header = panel.querySelector('.floating-cart-header');
      if (header) header.addEventListener('mousedown', onMouseDown);
    }
    document.addEventListener('mousemove', onMouseMove);
    document.addEventListener('mouseup', onMouseUp);
  }
  function detachDrag() {
    if (btn) btn.removeEventListener('mousedown', onMouseDown);
    if (panel) {
      const header = panel.querySelector('.floating-cart-header');
      if (header) header.removeEventListener('mousedown', onMouseDown);
    }
    document.removeEventListener('mousemove', onMouseMove);
    document.removeEventListener('mouseup', onMouseUp);
  }

  // Click handler for open/close (only if not dragging)
  if (btn) {
    btn.addEventListener('click', function(e) {
      if (isDragging) {
        // Prevent click if just dragged
        e.preventDefault();
        e.stopPropagation();
        return;
      }
      e.stopPropagation();
      if (panel.style.display === 'block') {
        panel.style.display = 'none';
        return;
      }
      // Force panel to a visible position and size
      panel.style.left = 'auto';
      panel.style.top = 'auto';
      panel.style.right = '32px';
      panel.style.bottom = '10px';
      panel.style.width = '340px';
      panel.style.height = 'auto';
      panel.style.maxHeight = '540px';
      panel.style.display = 'block';
      if (typeof renderFloatingCart === 'function') renderFloatingCart();
      // Debug: log computed style and bounding rect
      setTimeout(function() {
        const rect = panel.getBoundingClientRect();
        const style = window.getComputedStyle(panel);
      }, 100);
    });
  }

  document.addEventListener('DOMContentLoaded', function() {
    loadPosition();
    attachDrag();
    // Hide floating cart on checkout.php
    if (window.location.pathname.endsWith('checkout.php')) {
      if (btn) btn.style.display = 'none';
    }
  });
})();
// Add this simple function to update per-item total
function updateItemTotal(cartId, quantity, unitPrice) {
  console.log('[updateItemTotal] Called with cartId:', cartId, 'quantity:', quantity, 'unitPrice:', unitPrice);
  
  const totalDiv = document.querySelector('div[data-cart-total-id="' + cartId + '"]');
  console.log('[updateItemTotal] Found totalDiv:', totalDiv);
  
  if (totalDiv) {
    const oldText = totalDiv.textContent;
    const newTotal = (quantity * unitPrice).toFixed(2);
    totalDiv.textContent = '₹' + newTotal;
    console.log('[updateItemTotal] Updated from', oldText, 'to ₹' + newTotal);
  } else {
    console.log('[updateItemTotal] ERROR: Could not find totalDiv for cartId:', cartId);
    console.log('[updateItemTotal] Available elements with data-cart-total-id:');
    document.querySelectorAll('div[data-cart-total-id]').forEach(el => {
      console.log('  - Element:', el, 'cartId:', el.getAttribute('data-cart-total-id'));
    });
  }
}


// Add global event listener for real-time updates
document.body.addEventListener('input', function(e) {
  
  
  if (e.target.classList.contains('cart-qty-input')) {
    const cartId = e.target.getAttribute('data-cart-id');
    const quantity = parseInt(e.target.value) || 1;
    
    const row = e.target.closest('.d-flex.align-items-center');
    
    const priceSpan = row.querySelector('.ms-1');
    
    const unitPrice = parseFloat(priceSpan.textContent.match(/₹(\d+\.?\d*)/)?.[1] || 0);
    
    if (unitPrice > 0) {
      updateItemTotal(cartId, quantity, unitPrice);
    }
  }
});

// Add global event listener for change events
document.body.addEventListener('change', function(e) {
  if (e.target.classList.contains('cart-qty-input')) {
    const cartId = e.target.getAttribute('data-cart-id');
    const quantity = parseInt(e.target.value) || 1;
    
    const row = e.target.closest('.d-flex.align-items-center');
    
    const priceSpan = row.querySelector('.ms-1');
    
    const unitPrice = parseFloat(priceSpan.textContent.match(/₹(\d+\.?\d*)/)?.[1] || 0);
    
    if (unitPrice > 0) {
      updateItemTotal(cartId, quantity, unitPrice);
    }
  }
});



// Add smooth update function for immediate feedback
function smoothUpdatePerItemTotal(cartId, newTotal) {
  const totalDiv = document.querySelector('div[data-cart-total-id="' + cartId + '"]');
  if (totalDiv) {
    const oldTotal = parseFloat(totalDiv.textContent.replace('₹', '')) || 0;
    const newTotalValue = parseFloat(newTotal);
    
    // Immediate visual feedback
    totalDiv.style.transition = 'all 0.2s ease';
    totalDiv.style.backgroundColor = '#d4edda';
    totalDiv.style.borderRadius = '4px';
    totalDiv.style.padding = '2px 4px';
    
    // Update value
    totalDiv.textContent = '₹' + newTotalValue.toFixed(2);
    
    // Remove highlight
    setTimeout(() => {
      totalDiv.style.backgroundColor = '';
      totalDiv.style.borderRadius = '';
      totalDiv.style.padding = '';
    }, 200);
    

  }
}
</script>
<main>

<!-- Quantity Initialization Script -->
<script>
// Direct initialization script to ensure quantity inputs show current cart quantities
(function() {
    'use strict';
    
    // Function to load current cart quantity for a product
    function loadCartQuantity(productId, input) {
        if (!productId || !input) return;
        
        fetch(`ajax/check-product-in-cart.php?product_id=${productId}`)
            .then(res => res.json())
            .then(data => {
                if (data.success && data.in_cart && data.quantity > 0) {
                    input.value = data.quantity;
                }
            })
            .catch(err => {});
    }
    
    // Function to initialize all quantity inputs
    function initQuantityInputs() {
        // Find all possible quantity inputs
        const inputs = document.querySelectorAll(`
            input[type="number"][min="1"],
            .quantity-input,
            .shop-page-quantity-input,
            input[name="quantity"],
            .product-quantity-input,
            .quantity-control input[type="number"],
            .cart-actions input[type="number"],
            .cart-controls input[type="number"]
        `);
        
        inputs.forEach((input, i) => {
            let productId = null;
            
            // Try multiple ways to find product ID
            productId = input.dataset.productId || input.getAttribute('data-product-id');
            
            if (!productId) {
                const card = input.closest('[data-product-id]');
                if (card) productId = card.dataset.productId || card.getAttribute('data-product-id');
            }
            
            if (!productId) {
                const addBtn = input.closest('*').querySelector('[data-product-id]');
                if (addBtn) productId = addBtn.dataset.productId || addBtn.getAttribute('data-product-id');
            }
            
            if (!productId) {
                // Look in parent elements
                let parent = input.parentElement;
                while (parent && !productId) {
                    const elementWithId = parent.querySelector('[data-product-id]');
                    if (elementWithId) {
                        productId = elementWithId.dataset.productId || elementWithId.getAttribute('data-product-id');
                        break;
                    }
                    parent = parent.parentElement;
                    if (!parent || parent === document.body) break;
                }
            }
            
            if (productId && input.value == "1") {
                loadCartQuantity(productId, input);
            }
        });
    }
    
    // Run initialization at multiple points
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            setTimeout(initQuantityInputs, 100);
        });
    } else {
        setTimeout(initQuantityInputs, 100);
    }
    
    // Also run when window is fully loaded
    window.addEventListener('load', () => {
        setTimeout(initQuantityInputs, 200);
    });
    
    // Run after longer delays as fallbacks
    setTimeout(initQuantityInputs, 1000);
    setTimeout(initQuantityInputs, 2000);
    setTimeout(initQuantityInputs, 3000);
    
    // Make function globally available
    window.headerInitQuantities = initQuantityInputs;
    
})();
</script>

<!-- Go to Top Button -->
<button id="goToTopBtn" style="display:none; position:fixed; bottom:30px; right:30px; z-index:9999; background:#b2d235; color:white; border:none; border-radius:12px; width:56px; height:56px; box-shadow:0 2px 8px rgba(0,0,0,0.15); font-size:2rem; cursor:pointer; transition:all 0.2s;">
    <span style="display:flex; align-items:center; justify-content:center;"><svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="18 15 12 9 6 15"></polyline></svg></span>
</button>
<script>
// Show/hide Go to Top button
window.addEventListener('scroll', function() {
    const btn = document.getElementById('goToTopBtn');
    if (window.scrollY > 200) {
        btn.style.display = 'block';
    } else {
        btn.style.display = 'none';
    }
});
// Smooth scroll to top
if(document.getElementById('goToTopBtn')){
    document.getElementById('goToTopBtn').onclick = function() {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    };
}

// Add mobile-specific positioning for goToTopBtn
document.addEventListener('DOMContentLoaded', function() {
    const goToTopBtn = document.getElementById('goToTopBtn');
    if (goToTopBtn) {
        function updateButtonPosition() {
            if (window.innerWidth <= 480) {
                // Extra small mobile
                goToTopBtn.style.setProperty('left', '15px', 'important');
                goToTopBtn.style.setProperty('right', 'auto', 'important');
                goToTopBtn.style.setProperty('bottom', '15px', 'important');
                goToTopBtn.style.setProperty('width', '40px', 'important');
                goToTopBtn.style.setProperty('height', '40px', 'important');
                goToTopBtn.style.setProperty('font-size', '1.2rem', 'important');
            } else if (window.innerWidth <= 768) {
                // Mobile/tablet
                goToTopBtn.style.setProperty('left', '20px', 'important');
                goToTopBtn.style.setProperty('right', 'auto', 'important');
                goToTopBtn.style.setProperty('bottom', '20px', 'important');
                goToTopBtn.style.setProperty('width', '45px', 'important');
                goToTopBtn.style.setProperty('height', '45px', 'important');
                goToTopBtn.style.setProperty('font-size', '1.5rem', 'important');
            } else {
                // Desktop
                goToTopBtn.style.setProperty('left', 'auto', 'important');
                goToTopBtn.style.setProperty('right', '30px', 'important');
                goToTopBtn.style.setProperty('bottom', '30px', 'important');
                goToTopBtn.style.setProperty('width', '56px', 'important');
                goToTopBtn.style.setProperty('height', '56px', 'important');
                goToTopBtn.style.setProperty('font-size', '2rem', 'important');
            }
        }
        
        // Update on load
        updateButtonPosition();
        
        // Update on resize
        window.addEventListener('resize', updateButtonPosition);
        
        // Also update when scroll event triggers (in case button is recreated)
        window.addEventListener('scroll', function() {
            setTimeout(updateButtonPosition, 10);
        });
    }
});

// Home and Wishlist Icon Hover Effects
document.addEventListener('DOMContentLoaded', function() {
    // Home icon hover effects
    const homeLink = document.querySelector('.home-link');
    const homeIcon = document.querySelector('.home-icon');
    
    if (homeLink && homeIcon) {
        homeLink.addEventListener('mouseenter', function() {
            homeIcon.classList.remove('bi-house-door');
            homeIcon.classList.add('bi-house-door-fill');
        });
        
        homeLink.addEventListener('mouseleave', function() {
            homeIcon.classList.remove('bi-house-door-fill');
            homeIcon.classList.add('bi-house-door');
        });
    }
    
    // Wishlist icon hover effects
    const wishlistLink = document.querySelector('.wishlist-link');
    const wishlistIcon = document.querySelector('.wishlist-icon');
    
    if (wishlistLink && wishlistIcon) {
        // Only apply hover effect if it's currently outline (bi-heart)
        if (wishlistIcon.classList.contains('bi-heart')) {
            wishlistLink.addEventListener('mouseenter', function() {
                wishlistIcon.classList.remove('bi-heart');
                wishlistIcon.classList.add('bi-heart-fill');
            });
            
            wishlistLink.addEventListener('mouseleave', function() {
                wishlistIcon.classList.remove('bi-heart-fill');
                wishlistIcon.classList.add('bi-heart');
            });
        }
    }
});
</script>

<script>
  // JavaScript-based infinite auto-scroll for category section
  document.addEventListener('DOMContentLoaded', function () {
    const scrollWrapper = document.querySelector('.category-scroll-container .scroll-wrapper');
    if (!scrollWrapper) return;

    // Get all category items
    const categoryItems = scrollWrapper.querySelectorAll('.custom-category-item');
    if (categoryItems.length === 0) return;

    // Clear the wrapper
    scrollWrapper.innerHTML = '';

    // Create multiple sets of items for infinite scroll
    const numSets = 3; // Number of complete sets to create
    
    for (let i = 0; i < numSets; i++) {
      categoryItems.forEach(item => {
        // Clone each item
        const clonedItem = item.cloneNode(true);
        scrollWrapper.appendChild(clonedItem);
      });
    }

    // Calculate dimensions
    const totalWidth = scrollWrapper.scrollWidth;
    const singleWidth = totalWidth / numSets;
    const visibleWidth = scrollWrapper.clientWidth;

    // Start position (middle set)
    scrollWrapper.scrollLeft = singleWidth;

    // Auto-scroll settings
    const step = 25; // Pixels per step (extremely fast)
    const intervalTime = 1; // Milliseconds between steps (extremely fast)

    function autoScroll() {
      if (!scrollWrapper) return;

      let current = scrollWrapper.scrollLeft;

      // If we've scrolled past the second set, reset to first set
      if (current >= singleWidth * 2) {
        scrollWrapper.scrollLeft = singleWidth;
      } else {
        scrollWrapper.scrollLeft += step;
      }
    }

    let interval = setInterval(autoScroll, intervalTime);
    let isPaused = false;

    // Pause on mouseenter/touchstart
    function pauseScroll() {
      if (!isPaused) {
        clearInterval(interval);
        isPaused = true;
      }
    }

    // Resume on mouseleave/touchend
    function resumeScroll() {
      if (isPaused) {
        interval = setInterval(autoScroll, intervalTime);
        isPaused = false;
      }
    }

    // Event listeners for pause/resume
    scrollWrapper.addEventListener('mouseenter', pauseScroll);
    scrollWrapper.addEventListener('mouseleave', resumeScroll);
    scrollWrapper.addEventListener('touchstart', pauseScroll);
    scrollWrapper.addEventListener('touchend', resumeScroll);

    // Pause on user scroll interaction
    scrollWrapper.addEventListener('scroll', function() {
      if (!isPaused) {
        pauseScroll();
        setTimeout(resumeScroll, 2000);
      }
    });
  });
</script>