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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="Header.css">
    <link rel="stylesheet" href="asset/style/footer.css">
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
  padding: 8px 10px 4px 10px !important;
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
.header2 {
  height: 35px;
  min-height: 35px;
  z-index: 10000;
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
}
.navbar {
  position: fixed !important;
  top: 35px !important;
  left: 0 !important;
  width: 100% !important;
  z-index: 9999 !important;
  background: #fff !important;
}
.category-navbar {
  position: sticky !important;
  top: 91px !important;
  left: 0 !important;
  width: 100% !important;
  z-index: 9998 !important;
  background: #fff !important;
}
body { padding-top: 125px !important; }
@media (max-width: 991.98px) {
  .navbar { top: 35px !important; }
  .category-navbar { top: 83px !important; }
  body { padding-top: 118px !important; }
}
.floating-cart {
  z-index: 99999 !important;
}
#floatingCartBtn {
  z-index: 99999 !important;
}

/* Product Added Highlight Effect */
.product-added-highlight {
  animation: productAddedPulse 0.6s ease-in-out;
  border: 2px solid #28a745 !important;
  box-shadow: 0 0 15px rgba(40, 167, 69, 0.3) !important;
  transform: scale(1.02);
  transition: all 0.3s ease;
}

@keyframes productAddedPulse {
  0% {
    border-color: #28a745;
    box-shadow: 0 0 5px rgba(40, 167, 69, 0.2);
    transform: scale(1);
  }
  50% {
    border-color: #20c997;
    box-shadow: 0 0 20px rgba(40, 167, 69, 0.5);
    transform: scale(1.03);
  }
  100% {
    border-color: #28a745;
    box-shadow: 0 0 15px rgba(40, 167, 69, 0.3);
    transform: scale(1.02);
  }
}

/* Ensure smooth transitions for all product cards */
.card, .product-detail-card, [data-id^="prod-"] {
  transition: all 0.3s ease;
  border: 1px solid #dee2e6;
}
</style>
</head>
<body>

<!-- Floating Cart Icon and Panel -->
<div id="floatingCartBtn" style="position:fixed;bottom:32px;right:32px;z-index:1050;display:flex;align-items:center;justify-content:center;width:60px;height:60px;background:#28a745;border-radius:50%;box-shadow:0 4px 16px rgba(0,0,0,0.18);cursor:pointer;transition:box-shadow 0.2s;">
  <span style="position:relative;display:flex;align-items:center;justify-content:center;width:100%;height:100%;">
    <i class="bi bi-cart4" style="font-size:2rem;color:#fff;"></i>
    <span id="floatingCartCount" style="position:absolute;top:0px;right:10px;background:none;color:#fff;font-weight:bold;font-size:0.95rem;padding:2px 7px;border-radius:12px;min-width:22px;text-align:center;box-shadow:0 2px 6px rgba(0,0,0,0.12);">0</span>
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
                <?php
                function renderCategoryDropdown($tree, $level = 0) {
                    foreach ($tree as $cat) {
                        $indent = str_repeat('&nbsp;&nbsp;&nbsp;', $level);
                        if (!empty($cat['children'])) {
                            // Main category as non-selectable header
                            echo '<li><span class="dropdown-header" style="font-weight:bold;">' . $indent . htmlspecialchars($cat['name']) . '</span></li>';
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
              <button id="categoryDropdownMobile" class="btn btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown" data-selected-category="all">
                <span id="selectedCategoryMobile">All</span>
              </button>
              <ul class="dropdown-menu">
                <li><a class="dropdown-item category-option" href="#" data-category="all">All</a></li>
                <?php renderCategoryDropdown($categoryTree); ?>
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
    <nav class="navbar navbar-expand-lg navbar-light bg-light category-navbar">
        <div class="navbar-collapse justify-content-center" id="navbarSupportedContent">
            <ul class="navbar-nav category-list mb-2 mb-lg-0 d-flex align-items-center">
                <?php function renderCategoryMenu($tree) {
                    foreach ($tree as $cat) {
                        if (!empty($cat['children'])) {
                            echo '<li class="nav-item dropdown d-flex align-items-center">';
                            // Main category name as clickable link
                            echo '<a class="nav-link" href="category.php?slug=' . $cat['slug'] . '" id="catLink' . $cat['id'] . '">' . strtoupper(htmlspecialchars($cat['name'])) . '</a>';
                            // Separate dropdown toggle button (no nav-link class, only btn btn-link)
                            echo '<button class="btn btn-link p-0 ms-1 align-self-center" id="catDropdown' . $cat['id'] . '" data-bs-toggle="dropdown" aria-expanded="false" style="font-size:0.85rem;line-height:1;vertical-align:middle;color:#222;">&#9660;</button>';
                            echo '<ul class="dropdown-menu" aria-labelledby="catDropdown' . $cat['id'] . '">';
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
    </nav>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="popup/popup.js"></script>
<script src="popup/searchbar.js"></script>
<script src="js/real-time-max-quantity.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
function updateFloatingCartCount() {
  fetch('ajax/get_cart_count.php')
    .then(res => res.json())
    .then(data => {
      document.getElementById('floatingCartCount').textContent = data.cart_count || 0;
      var headerCartCount = document.getElementById('cart-count');
      if (headerCartCount) {
        headerCartCount.textContent = data.cart_count > 0 ? data.cart_count : '';
        headerCartCount.style.display = data.cart_count > 0 ? 'inline-block' : 'none';
      }
      // Hide or show floating cart icon
      var floatingCartBtn = document.getElementById('floatingCartBtn');
      if (floatingCartBtn) {
        floatingCartBtn.style.display = (data.cart_count > 0) ? '' : 'none';
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
          <div class="d-flex justify-content-between mb-1"><span class="text-muted">Delivery <i class='bi bi-info-circle' title='Delivery charges may vary'></i></span><span class="text-danger fw-bold">+ Extra</span></div>
          <div class="d-flex justify-content-between mb-1"><span class="text-muted">Savings</span><span class="fw-bold" style="color:#2e7d32;">₹${parseFloat(totals.total_savings).toLocaleString('en-IN', {minimumFractionDigits: 0, maximumFractionDigits: 0})}</span></div>
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
              <div style="font-weight:700;font-size:0.98rem;">₹${(item.selling_price * item.quantity).toFixed(2)}</div>
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
          <div class="d-flex justify-content-between mb-1"><span class="text-muted">Delivery <i class='bi bi-info-circle' title='Delivery charges may vary'></i></span><span class="text-danger fw-bold">+ Extra</span></div>
          <div class="d-flex justify-content-between mb-1"><span class="text-muted">Savings</span><span class="fw-bold" style="color:#2e7d32;">₹${parseFloat(totals.total_savings).toLocaleString('en-IN', {minimumFractionDigits: 0, maximumFractionDigits: 0})}</span></div>
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
            const totalDiv = row.querySelector('div[style*="font-weight:700"]');
            if (totalDiv && unitPrice) {
              totalDiv.textContent = '₹' + ((qty - 1) * unitPrice).toFixed(2);
            }
            updateCartQuantity(cartId, qty - 1, input, btn, function(success) {
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
                    alert('Could not update cart.');
                }
              } else {
                updateFloatingCartCount();
                updateFloatingCartSummary();
              }
            });
          }
        };
      });
      content.querySelectorAll('.btn-qty-plus').forEach(btn => {
        btn.onclick = function(e) {
          e.stopPropagation();
          const cartId = btn.getAttribute('data-cart-id');
          const row = btn.closest('.d-flex.align-items-center');
          const input = content.querySelector('.cart-qty-input[data-cart-id="' + cartId + '"]');
          let qty = parseInt(input.value, 10) || 1;
          const prevQty = qty;
          input.value = qty + 1;
          // Update total in DOM
          const priceSpan = row.querySelector('.ms-1');
          const unitPriceText = priceSpan ? priceSpan.textContent : '';
          const unitPrice = parseFloat(unitPriceText.match(/₹(\d+\.?\d*)/)?.[1] || 0);
          const totalDiv = row.querySelector('div[style*="font-weight:700"]');
          if (totalDiv && unitPrice) {
            totalDiv.textContent = '₹' + ((qty + 1) * unitPrice).toFixed(2);
          }
          updateCartQuantity(cartId, qty + 1, input, btn, function(success) {
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
                  alert('Could not update cart.');
              }
            } else {
              updateFloatingCartCount();
              updateFloatingCartSummary();
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
              // If cart is now empty, reload floating cart
              if (!content.querySelector('.d-flex.align-items-center')) {
                renderFloatingCart();
                updateFloatingCartCount();
              } else {
                updateFloatingCartSummary();
                updateFloatingCartCount();
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
                  alert(resp.message || 'Could not remove item.');
              }
            }
          });
        };
      });
      // Quantity input direct change
      content.querySelectorAll('.cart-qty-input').forEach(input => {
        input.onchange = function(e) {
          let qty = parseInt(this.value, 10) || 1;
          if (qty < 1) qty = 1;
          if (qty > 99) qty = 99; // Enforce max quantity
          this.value = qty; // Update input value to reflect limits
          const cartId = this.getAttribute('data-cart-id');
          const row = this.closest('.d-flex.align-items-center');
          
          // Update total price display immediately
          const priceSpan = row.querySelector('.ms-1');
          const unitPriceText = priceSpan ? priceSpan.textContent : '';
          const unitPrice = parseFloat(unitPriceText.match(/₹(\d+\.?\d*)/)?.[1] || 0);
          const totalDiv = row.querySelector('div[style*="font-weight:700"]');
          if (totalDiv && unitPrice) {
            totalDiv.textContent = '₹' + (qty * unitPrice).toFixed(2);
          }
          
          updateCartQuantity(cartId, qty, this, null);
        };
        
        // Real-time price update on input
        input.oninput = function(e) {
          let qty = parseInt(this.value, 10) || 1;
          if (qty < 1) qty = 1;
          if (qty > 99) qty = 99; // Enforce max quantity
          const row = this.closest('.d-flex.align-items-center');
          
          // Update total price display immediately
          const priceSpan = row.querySelector('.ms-1');
          const unitPriceText = priceSpan ? priceSpan.textContent : '';
          const unitPrice = parseFloat(unitPriceText.match(/₹(\d+\.?\d*)/)?.[1] || 0);
          const totalDiv = row.querySelector('div[style*="font-weight:700"]');
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
            <div class="d-flex justify-content-between mb-2"><span class="text-muted">Delivery Charge <i class='bi bi-info-circle' title='Delivery charges may vary'></i></span><span class="text-danger fw-bold">+ Extra</span></div>
            <div class="d-flex justify-content-between mb-2"><span class="text-muted">Savings</span><span class="fw-bold" style="color:#2e7d32;">₹${parseFloat(totals.total_savings).toLocaleString('en-IN', {minimumFractionDigits: 0, maximumFractionDigits: 0})}</span></div>
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
updateFloatingCartCount();
setInterval(updateFloatingCartCount, 30000);
// Optionally update on add-to-cart events if available
// Listen for global cart updates (from add-to-cart or other actions)
window.addEventListener('cart-updated', function() {
  renderFloatingCart();
  updateFloatingCartCount();
});

// Floating Cart Remove All functionality
document.addEventListener('DOMContentLoaded', function() {
  const floatingRemoveAllBtn = document.getElementById('floatingRemoveAll');
  if (floatingRemoveAllBtn) {
    floatingRemoveAllBtn.addEventListener('click', function() {
      if (confirm('Are you sure you want to remove all items from your cart? This action cannot be undone.')) {
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
                    alert('All items have been removed from your cart.');
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
                    alert('Failed to remove items: ' + (data.message || 'Unknown error'));
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
                alert('An error occurred while removing items from cart.');
            }
          // Reset button
          button.innerHTML = originalText;
          button.disabled = false;
        });
      }
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
    console.log('[FloatingCart][DEBUG] Saving position:', left, top);
    localStorage.setItem('floatingCartPosition', JSON.stringify({ left, top }));
  }

  // Move both elements
  function moveBoth(left, top) {
    console.log('[FloatingCart][DEBUG] Moving both to:', left, top);
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
    console.log('[FloatingCart][DEBUG] MouseDown', {startX, startY});
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
      console.log('[FloatingCart][DEBUG] Dragging', {x, y});
    }
  }
  function onMouseUp(e) {
    if (isMouseDown) {
      if (isDragging) {
        // Save position if dragged
        const left = btn ? parseInt(btn.style.left) : 0;
        const top = btn ? parseInt(btn.style.top) : 0;
        savePosition(left, top);
        console.log('[FloatingCart][DEBUG] Drag End', {left, top});
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
      console.log('[FloatingCart][DEBUG] Click', {isDragging, isMouseDown, panel, display: panel ? panel.style.display : undefined});
      if (isDragging) {
        // Prevent click if just dragged
        e.preventDefault();
        e.stopPropagation();
        console.log('[FloatingCart][DEBUG] Click prevented due to drag');
        return;
      }
      e.stopPropagation();
      if (panel.style.display === 'block') {
        panel.style.display = 'none';
        console.log('[FloatingCart][DEBUG] Panel closed');
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
        console.log('[FloatingCart][DEBUG] Panel rect:', rect);
        console.log('[FloatingCart][DEBUG] Panel computed style:', {
          display: style.display,
          left: style.left,
          top: style.top,
          right: style.right,
          bottom: style.bottom,
          width: style.width,
          height: style.height,
          zIndex: style.zIndex,
          opacity: style.opacity,
          visibility: style.visibility
        });
      }, 100);
      console.log('[FloatingCart][DEBUG] Panel opened');
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
</script>
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
