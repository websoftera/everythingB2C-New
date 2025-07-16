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
    <style>
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
  height: 200px;
  max-height: 200px;
  overflow-y: auto;
  min-height: 0;
  padding: 10px 18px 0 18px;
}
#floatingCartPanel.fixed-panel #floatingCartSummary {
  padding: 8px 18px 0 18px;
  font-size: 0.97rem;
  flex-shrink: 0;
  background: #fff;
}
#floatingCartPanel.fixed-panel .floating-cart-actions {
  padding: 10px 18px 10px 18px;
  flex-shrink: 0;
  background: #fff;
  box-shadow: 0 -2px 8px rgba(0,0,0,0.04);
}
</style>
</head>
<body>

<!-- Floating Cart Icon and Panel -->
<div id="floatingCartBtn" style="position:fixed;bottom:32px;right:32px;z-index:1050;display:flex;align-items:center;justify-content:center;width:60px;height:60px;background:#28a745;border-radius:50%;box-shadow:0 4px 16px rgba(0,0,0,0.18);cursor:pointer;transition:box-shadow 0.2s;">
  <span style="position:relative;display:flex;align-items:center;justify-content:center;width:100%;height:100%;">
    <i class="bi bi-cart4" style="font-size:2rem;color:#fff;"></i>
    <span id="floatingCartCount" style="position:absolute;top:8px;right:10px;background:#fff;color:#28a745;font-weight:bold;font-size:0.95rem;padding:2px 7px;border-radius:12px;min-width:22px;text-align:center;box-shadow:0 2px 6px rgba(0,0,0,0.12);">0</span>
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
      <a href="checkout.php" class="btn btn-success w-100" style="padding:6px 0;font-size:0.97rem;">Checkout</a>
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
<script>
// --- Floating Cart Logic (Dropdown/Panel, Advanced) ---
function updateFloatingCartCount() {
  fetch('ajax/get_cart_count.php')
    .then(res => res.json())
    .then(data => {
      document.getElementById('floatingCartCount').textContent = data.cart_count || 0;
    });
}
function renderFloatingCart() {
  const content = document.getElementById('floatingCartContent');
  const summary = document.getElementById('floatingCartSummary');
  content.innerHTML = '<div class="text-center py-4"><div class="spinner-border text-success" role="status"><span class="visually-hidden">Loading...</span></div></div>';
  summary.innerHTML = '';
  fetch('ajax/get-cart-summary.php?details=1')
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
        const cartId = item.cart_id || item.product_id;
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
              <div class="text-muted" style="font-size:0.78rem;">HSN: ${item.hsn || '-'}</div>
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
        <div class="d-flex justify-content-between mb-1 text-success" style="font-size:0.98rem;"><span><b>Total Savings</b></span><span><b>₹${parseFloat(totals.total_savings).toFixed(2)}</b></span></div>
        <div class="d-flex justify-content-between mb-1"><span>Subtotal:</span><span>₹${parseFloat(totals.subtotal).toFixed(2)}</span></div>
        <div class="d-flex justify-content-between mb-1"><span>GST:</span><span>₹${parseFloat(totals.total_gst).toFixed(2)}</span></div>
        <div class="d-flex justify-content-between mb-1"><span>Shipping:</span><span>${totals.total_shipping > 0 ? '₹' + parseFloat(totals.total_shipping).toFixed(2) : 'Free'}</span></div>
        <hr class="my-2" style="margin:4px 0;">
        <div class="d-flex justify-content-between fw-bold" style="font-size:1.05rem;"><span>Total:</span><span>₹${parseFloat(totals.grand_total).toFixed(2)}</span></div>
      `;
      // Remove item event
      document.querySelectorAll('.remove-cart-item-btn').forEach(btn => {
        btn.onclick = function(e) {
          e.stopPropagation();
          const cartId = this.getAttribute('data-cart-id');
          this.disabled = true;
          fetch('ajax/remove-from-cart.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ cart_id: cartId })
          })
          .then(res => res.json())
          .then(resp => {
            if (resp.success) {
              renderFloatingCart();
              updateFloatingCartCount();
            } else {
              alert(resp.message || 'Could not remove item.');
              this.disabled = false;
            }
          });
        };
      });
      // Quantity minus/plus events
      document.querySelectorAll('.btn-qty-minus').forEach(btn => {
        btn.onclick = function(e) {
          e.stopPropagation();
          const cartId = this.getAttribute('data-cart-id');
          const input = content.querySelector('.cart-qty-input[data-cart-id="' + cartId + '"]');
          let qty = parseInt(input.value, 10) || 1;
          if (qty > 1) {
            updateCartQuantity(cartId, qty - 1, input, this);
          }
        };
      });
      document.querySelectorAll('.btn-qty-plus').forEach(btn => {
        btn.onclick = function(e) {
          e.stopPropagation();
          const cartId = this.getAttribute('data-cart-id');
          const input = content.querySelector('.cart-qty-input[data-cart-id="' + cartId + '"]');
          let qty = parseInt(input.value, 10) || 1;
          updateCartQuantity(cartId, qty + 1, input, this);
        };
      });
      // Quantity input direct change
      document.querySelectorAll('.cart-qty-input').forEach(input => {
        input.onchange = function(e) {
          let qty = parseInt(this.value, 10) || 1;
          if (qty < 1) qty = 1;
          const cartId = this.getAttribute('data-cart-id');
          updateCartQuantity(cartId, qty, this, null);
        };
      });
      function updateCartQuantity(cartId, qty, inputElem, btnElem) {
        if (qty < 1) qty = 1;
        if (inputElem) inputElem.disabled = true;
        if (btnElem) btnElem.disabled = true;
        content.innerHTML = '<div class="text-center py-4"><div class="spinner-border text-success" role="status"><span class="visually-hidden">Updating...</span></div></div>';
        fetch('ajax/update-cart.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ cart_id: cartId, quantity: qty })
        })
        .then(res => res.json())
        .then(resp => {
          renderFloatingCart();
          updateFloatingCartCount();
        });
      }
    })
    .catch(() => {
      content.innerHTML = '<div class="text-center text-danger py-4">Could not load cart.</div>';
      summary.innerHTML = '';
    });
}
// Show/hide panel as dropdown
const floatingCartBtn = document.getElementById('floatingCartBtn');
const floatingCartPanel = document.getElementById('floatingCartPanel');
const closeFloatingCartPanel = document.getElementById('closeFloatingCartPanel');
floatingCartBtn.onclick = function(e) {
  e.stopPropagation();
  if (floatingCartPanel.style.display === 'block') {
    floatingCartPanel.style.display = 'none';
    return;
  }
  floatingCartPanel.style.display = 'block';
  renderFloatingCart();
};
closeFloatingCartPanel.onclick = function(e) {
  e.stopPropagation();
  floatingCartPanel.style.display = 'none';
};
// Hide panel when clicking outside
window.addEventListener('click', function(e) {
  if (floatingCartPanel.style.display === 'block' && !floatingCartBtn.contains(e.target)) {
    floatingCartPanel.style.display = 'none';
  }
});
// Prevent closing when clicking inside the panel
floatingCartPanel.onclick = function(e) {
  e.stopPropagation();
};
// Update cart count on page load and every 30s
updateFloatingCartCount();
setInterval(updateFloatingCartCount, 30000);
// Optionally update on add-to-cart events if available
// Listen for global cart updates (from add-to-cart or other actions)
window.addEventListener('cart-updated', function() {
  renderFloatingCart();
  updateFloatingCartCount();
});
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
