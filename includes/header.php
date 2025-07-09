<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'includes/functions.php';

// Build $wishlist_ids for both logged-in and guest users
$wishlist_ids = [];
if (isLoggedIn()) {
    $wishlistItems = getWishlistItems($_SESSION['user_id']);
    foreach ($wishlistItems as $item) {
        $wishlist_ids[] = $item['product_id'];
    }
} else {
    $wishlistItems = getWishlistItems();
    foreach ($wishlistItems as $item) {
        $wishlist_ids[] = $item['product_id'];
    }
}

$categories = getAllCategories();
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
    <link rel="icon" href="./logo.webp" type="image/webp">

    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="asset/style/header.css">
    <link rel="stylesheet" href="asset/style/footer.css">
    <link rel="stylesheet" href="asset/style/popup.css">
    <link rel="stylesheet" href="asset/style/style.css">
    <link rel="stylesheet" href="asset/style/product-card.css">

</head>
<body>

<section class="header2">
    <div class="nav-links">
        <a href="index.php">HOME</a><span>|</span>
        <a href="shop.php">SHOP</a><span>|</span>
        <a href="wishlist.php">WISHLIST</a><span>|</span>
        <?php if (isLoggedIn()): ?>
            <a href="myaccount.php">MY ACCOUNT</a><span>|</span>
            <a href="logout.php">LOGOUT</a>
        <?php else: ?>
            <a href="login.php">LOGIN</a>
        <?php endif; ?>
    </div>
</section>

<!-- NAVBAR START -->
<nav class="navbar navbar-expand-lg">
    <div class="container-fluid d-flex align-items-center justify-content-between">
        <div class="logo-wrapper d-flex align-items-center">
            <a href="index.php">
                <img src="./Kichen Page/page2/logo.webp" alt="Logo" class="img-fluid logo" />
            </a>
        </div>
        <form class="d-flex flex-grow-1 mx-4 position-relative" role="search" autocomplete="off" onsubmit="return false;">
            <div class="input-group w-100 flex-wrap">
                <!-- DESKTOP Dropdown -->
                <div class="dropdown-desktop">
                  <button id="categoryDropdownDesktop" class="btn btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown" data-selected-category="all">
                    <span id="selectedCategoryDesktop">All</span>
                  </button>
                  <ul class="dropdown-menu">
                    <li><a class="dropdown-item category-option" href="#" data-category="all">All</a></li>
                    <?php foreach ($categories as $cat): ?>
                      <li><a class="dropdown-item category-option" href="#" data-category="<?php echo $cat['slug']; ?>"><?php echo htmlspecialchars($cat['name']); ?></a></li>
                    <?php endforeach; ?>
                  </ul>
                </div>
                <!-- MOBILE Dropdown -->
                <div class="dropdown-mobile">
                  <button id="categoryDropdownMobile" class="btn btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown" data-selected-category="all">
                    <span id="selectedCategoryMobile">All</span>
                  </button>
                  <ul class="dropdown-menu">
                    <li><a class="dropdown-item category-option" href="#" data-category="all">All</a></li>
                    <?php foreach ($categories as $cat): ?>
                      <li><a class="dropdown-item category-option" href="#" data-category="<?php echo $cat['slug']; ?>"><?php echo htmlspecialchars($cat['name']); ?></a></li>
                    <?php endforeach; ?>
                  </ul>
                </div>
                <input class="form-control" id="headerSearchInput" type="search" name="query" placeholder="Search for Products" aria-label="Search" autocomplete="off">
                <button class="btn btn-primary" id="headerSearchBtn" type="button">
                    <i class="bi bi-search"></i>
                </button>
            </div>
            <div id="headerSearchResultsPopup" class="position-absolute w-100" style="z-index: 9999; display: none;"></div>
        </form>
        <div class="d-none d-lg-flex align-items-center">
            <a href="Customer-Support.html" class="text-decoration-none text-dark">
                <i class="bi bi-headset fs-4 me-2"></i>
                <span class="me-4 fw-semibold customer-support">Customer Support</span>
            </a>
            <a href="cart.php" class="text-decoration-none text-dark cart-link position-relative">
                <i class="bi bi-cart4 fs-4 cart-icon"></i>
                <!-- <span class="me-4 fw-semibold">Cart</span> -->
                <span id="cart-count" class="position-absolute top-0 start-100 translate-middle badge rounded-pill" style="display:<?php echo $cartCount > 0 ? 'inline-block' : 'none'; ?>;">
                    <?php echo $cartCount > 0 ? $cartCount : ''; ?>
                </span>
            </a>
        </div>
    </div>
</nav>

<!-- CATEGORY SCROLL - ONLY MOBILE -->
<div class="category-scroll-container d-block d-lg-none py-2 px-2">
    <div class="scroll-wrapper d-flex flex-nowrap overflow-auto">
        <?php function renderMobileCategoryMenu($tree) {
            foreach ($tree as $cat) {
                echo '<a href="category.php?slug=' . $cat['slug'] . '" class="category-item text-center mx-2">';
                echo '<img src="./' . $cat['image'] . '" alt="' . htmlspecialchars($cat['name']) . '" class="category-img mb-1">';
                echo '<div class="category-label">' . strtoupper($cat['name']) . '</div>';
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
    <nav class="navbar navbar-expand-lg navbar-light bg-light sticky-top w-100 category-navbar">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <div class="collapse navbar-collapse justify-content-center" id="navbarSupportedContent">
                <ul class="navbar-nav category-list mb-2 mb-lg-0 d-flex align-items-center">
                    <?php function renderCategoryMenu($tree) {
                        foreach ($tree as $cat) {
                            if (!empty($cat['children'])) {
                                echo '<li class="nav-item dropdown d-flex align-items-center">';
                                // Main category link (always clickable)
                                echo '<a class="nav-link" href="category.php?slug=' . $cat['slug'] . '" id="catDropdown' . $cat['id'] . '">' . strtoupper(htmlspecialchars($cat['name'])) . '</a>';
                                // Caret/arrow for dropdown only
                                echo '<a class="nav-link dropdown-toggle dropdown-caret-only" href="#" id="catDropdownToggle' . $cat['id'] . '" role="button" data-bs-toggle="dropdown" aria-expanded="false" style="width:18px;padding:0 6px;line-height:1;font-size:18px;">&#9660;</a>';
                                echo '<ul class="dropdown-menu" aria-labelledby="catDropdownToggle' . $cat['id'] . '">';
                                foreach ($cat['children'] as $subcat) {
                                    echo '<li><a class="dropdown-item" href="category.php?slug=' . $subcat['slug'] . '">' . htmlspecialchars($subcat['name']) . '</a></li>';
                                }
                                echo '</ul>';
                                echo '</li>';
                            } else {
                                echo '<li class="nav-item"><a class="nav-link" href="category.php?slug=' . $cat['slug'] . '">' . strtoupper(htmlspecialchars($cat['name'])) . '</a></li>';
                            }
                        }
                    }
                    renderCategoryMenu($categoryTree); ?>
                </ul>
            </div>
        </div>
    </nav>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="popup/popup.js"></script>
<script src="popup/searchbar.js"></script>
<main>

<!-- Go to Top Button -->
<button id="goToTopBtn" style="display:none; position:fixed; bottom:30px; right:30px; z-index:9999; background:#b2d235; color:white; border:none; border-radius:12px; width:56px; height:56px; box-shadow:0 2px 8px rgba(0,0,0,0.15); font-size:2rem; cursor:pointer; transition:background 0.2s;">
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
</script>
