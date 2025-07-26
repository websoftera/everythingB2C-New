<?php
session_start();
require_once 'includes/functions.php';
require_once 'includes/gst_shipping_functions.php';

$pageTitle = 'Shopping Cart';

// Get cart items for both guests and logged-in users
if (isLoggedIn()) {
    $cartItems = getCartItems($_SESSION['user_id']);
} else {
    $cartItems = getCartItems();
}

// Determine delivery state for GST calculation
$delivery_state = 'Maharashtra';
$delivery_city = null;
$delivery_pincode = null;
if (isLoggedIn()) {
    $addresses = getUserAddresses($_SESSION['user_id']);
    $defaultAddress = getDefaultAddress($_SESSION['user_id']);
    if ($defaultAddress && !empty($defaultAddress['state'])) {
        $delivery_state = $defaultAddress['state'];
        $delivery_city = $defaultAddress['city'] ?? null;
        $delivery_pincode = $defaultAddress['pincode'] ?? null;
    }
} else if (isset($_POST['delivery_state'])) {
    $delivery_state = $_POST['delivery_state'];
}
$orderTotals = calculateOrderTotal($cartItems, $delivery_state, $delivery_city, $delivery_pincode);

require_once 'includes/header.php';
?>

<!-- Banner/Breadcrumb (skip homepage) -->
<div class="page-banner" style="background: url('asset/images/internalpage-bg.webp') center/cover no-repeat; min-height: 240px; display: flex; align-items: center;">
    <div class="container">
        <h2 style="color: #fff; font-size: 2rem; font-weight: bold; text-shadow: 0 2px 8px rgba(0,0,0,0.3); margin: 0; padding: 32px 0;">
            <?php echo htmlspecialchars($pageTitle); ?>
        </h2>
    </div>
</div>

<div class="container mt-4">
    <!-- <h1>Shopping Cart</h1> -->
    
    <?php if (empty($cartItems)): ?>
        <div class="text-center py-5">
            <h3>Your cart is empty</h3>
            <p>Add some products to your cart to get started.</p>
            <a href="index.php" class="btn btn-primary">Continue Shopping</a>
        </div>
    <?php else: ?>
        <div class="row">
            <div class="col-md-8">
                <div class="shopping-card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Cart Items (<?php echo count($cartItems); ?>)</h5>
                        <button type="button" class="btn btn-outline-danger btn-sm" id="removeAllItems" title="Remove all items from cart">
                            <i class="fas fa-trash-alt me-1"></i>Remove All
                        </button>
                    </div>
                    <div class="card-body">
                        <!-- Header Row for Cart Columns -->
                        <div class="cart-header-row d-flex align-items-center flex-nowrap" style="font-weight:600; color:#444; font-size:0.98rem; background:#f7f7f7; border-radius:6px; padding:7px 0 7px 8px; margin-bottom:8px; gap:8px;">
                            <div style="flex:0 0 56px; max-width:56px; min-width:40px;"></div>
                            <div style="flex:1 1 120px; min-width:60px; max-width:220px;">Product</div>
                            <div style="flex:0 0 90px; min-width:60px; text-align:center;">MRP</div>
                            <div style="flex:0 0 90px; min-width:60px; text-align:center;">You Pay</div>
                            <div style="flex:0 0 90px; min-width:60px; text-align:center;">You Save</div>
                            <div style="flex:0 0 80px; min-width:50px; text-align:center;">Qty</div>
                            <div style="flex:0 0 70px; min-width:50px; text-align:center;">Total</div>
                            <div style="flex:0 0 36px; min-width:28px; text-align:center; flex-shrink:0;"></div>
                        </div>
                        <?php foreach ($cartItems as $item): ?>
                            <div class="cart-item-row d-flex align-items-center flex-nowrap" style="border: 1px solid #e0e0e0; border-radius: 7px; padding: 7px 0; margin-bottom: 10px; background: #fff; gap: 8px;">
                                <div style="flex:0 0 56px; max-width:56px; min-width:40px;">
                                    <a href="product.php?slug=<?php echo urlencode($item['slug']); ?>">
                                        <img src="./<?php echo $item['main_image']; ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" class="img-fluid" style="width:44px;height:44px;object-fit:cover;border-radius:5px;">
                                    </a>
                                </div>
                                <div style="flex:1 1 120px; min-width:60px; max-width:220px; font-size:0.97em; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                                    <a href="product.php?slug=<?php echo urlencode($item['slug']); ?>" title="<?php echo htmlspecialchars($item['name']); ?>" style="color:inherit; text-decoration:underline dotted; cursor:pointer;">
                                        <?php echo htmlspecialchars($item['name']); ?>
                                    </a>
                                </div>
                                <div style="flex:0 0 90px; min-width:60px; font-size:0.93em; color:#888; text-align:center;"> <s><?php echo formatPrice($item['mrp']); ?></s> </div>
                                <div style="flex:0 0 90px; min-width:60px; font-size:0.97em; color:#007bff; font-weight:500; text-align:center;"> <?php echo formatPrice($item['selling_price']); ?> </div>
                                <div style="flex:0 0 90px; min-width:60px; font-size:0.93em; color:#23a036; text-align:center;"> <?php echo formatPrice(($item['mrp'] - $item['selling_price']) * $item['quantity']); ?> </div>
                                <div style="flex:0 0 80px; min-width:50px; text-align:center;">
                                    <div class="quantity-control d-inline-flex align-items-center justify-content-center">
                                        <button type="button" class="btn-qty btn-qty-minus" aria-label="Decrease quantity">-</button>
                                        <input type="number" class="form-control quantity-input" value="<?php echo $item['quantity']; ?>" min="1" max="99" data-cart-id="<?php echo $item['id']; ?>" style="width:34px;display:inline-block;">
                                        <button type="button" class="btn-qty btn-qty-plus" aria-label="Increase quantity">+</button>
                                    </div>
                                </div>
                                <div style="flex:0 0 70px; min-width:50px; text-align:center; font-weight:600; font-size:1.01em;"> <?php echo formatPrice($item['selling_price'] * $item['quantity']); ?> </div>
                                <div style="flex:0 0 36px; min-width:28px; text-align:center; flex-shrink:0;">
                                    <button class="btn btn-outline-danger btn-sm remove-item" data-cart-id="<?php echo $item['id']; ?>" title="Delete" style="padding: 4px 8px; font-size: 1.1rem;">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="shopping-card">
                    <div class="card-header">
                        <h5>Price Summary</h5>
                    </div>
                    <div class="card-body">
                        <div class="floating-cart-summary-box" style="border:1px solid #cfd8dc;border-radius:8px;padding:16px 16px 8px 16px;background:#fff;box-shadow:0 2px 8px rgba(0,0,0,0.04);margin-bottom:10px;">
                                      <div class="d-flex justify-content-between mb-2"><span class="text-muted">Total MRP</span><span style="font-weight:600;">₹<?php echo number_format($orderTotals['subtotal'], 0); ?></span></div>
            <div class="d-flex justify-content-between mb-2"><span class="text-muted">Delivery Charge <i class='bi bi-info-circle' title='Delivery charges may vary'></i></span><span class="text-danger fw-bold">+ Extra</span></div>
            <div class="d-flex justify-content-between mb-2"><span class="text-muted">Savings</span><span class="fw-bold" style="color:#2e7d32;">₹<?php
                                $total_savings = 0;
                                foreach ($cartItems as $item) {
                                    $total_savings += ($item['mrp'] - $item['selling_price']) * $item['quantity'];
                                }
                                echo number_format($total_savings, 0);
                            ?></span></div>
            <div class="d-flex justify-content-between mb-2"><span class="text-muted" style="font-size: 0.85rem;"><i>* All prices are inclusive of GST</i></span></div>
                          <div class="d-grid mt-3 mb-2">
                            <a href='checkout.php' class='btn btn-success btn-lg fw-bold' style='font-size:1.08rem;'>PROCEED TO CHECKOUT</a>
                          </div>
                          <div class="d-grid mb-2">
                            <a href='index.php' class='btn btn-outline-secondary btn-lg fw-bold' style='font-size:1.08rem;'>CONTINUE SHOPPING</a>
                          </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Product Detail Popup Modal -->
<div id="cartProductDetailModal" class="cart-product-modal" style="display:none;">
    <div class="cart-product-modal-content">
        <span class="cart-product-modal-close" id="cartProductModalClose">&times;</span>
        <div id="cartProductModalBody">
            <!-- Product details will be loaded here -->
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Update quantity
    const quantityInputs = document.querySelectorAll('.quantity-input');
    quantityInputs.forEach(input => {
        let lastValid = input.value;
        input.addEventListener('input', function() {
            if (!/^[1-9][0-9]*$/.test(this.value)) {
                this.classList.add('is-invalid');
            } else {
                this.classList.remove('is-invalid');
                lastValid = this.value;
            }
        });
        input.addEventListener('blur', function() {
            if (!/^[1-9][0-9]*$/.test(this.value)) {
                this.value = lastValid;
                this.classList.remove('is-invalid');
            }
        });
        input.addEventListener('change', function() {
            if (!/^[1-9][0-9]*$/.test(this.value)) {
                alert('Please enter a valid quantity (1 or more).');
                this.value = lastValid;
                this.classList.remove('is-invalid');
                return;
            }
            const cartId = this.getAttribute('data-cart-id');
            const quantity = this.value;
            const row = this.closest('.row');
            const priceElem = row.querySelector('p.text-muted');
            const itemTotalElem = row.querySelector('strong');
            let unitPrice = 0;
            if (priceElem) {
                const match = priceElem.textContent.match(/([\d,.]+)/);
                if (match) unitPrice = parseFloat(match[1].replace(/,/g, ''));
            }
            fetch('ajax/update-cart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    cart_id: cartId,
                    quantity: quantity
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update item total
                    if (itemTotalElem && unitPrice) {
                        itemTotalElem.textContent = formatPrice(unitPrice * quantity);
                    }
                    updateCartPageSummary();
                    updateFloatingCartCount();
                    // Update order summary
                    // fetch('ajax/get-cart-summary.php') // This line is now handled by updateCartPageSummary()
                    //     .then(res => res.json())
                    //     .then(summary => {
                    //         if (summary.success) {
                    //             const totals = summary.totals;
                    //             document.getElementById('cart-subtotal').textContent = formatPrice(totals.subtotal);
                    //             document.getElementById('cart-shipping').textContent = (totals.total_shipping > 0) ? formatPrice(totals.total_shipping) : 'Free';
                    //             document.getElementById('cart-gst').textContent = formatPrice(totals.total_gst);
                    //             document.getElementById('cart-grandtotal').textContent = isNaN(totals.grand_total) ? formatPrice(0) : formatPrice(totals.grand_total);
                    //         }
                    //     });
                } else {
                    alert('Error: ' + data.message);
                }
            });
        });
    });
    
    // Remove item
    const removeButtons = document.querySelectorAll('.remove-item');
    removeButtons.forEach(button => {
        button.addEventListener('click', function() {
            const cartId = this.getAttribute('data-cart-id');
            
            if (confirm('Are you sure you want to remove this item?')) {
                fetch('ajax/remove-from-cart.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        cart_id: cartId
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                        updateCartPageSummary();
                        updateFloatingCartCount();
                    } else {
                        alert('Error: ' + data.message);
                    }
                });
            }
        });
    });
});

function formatPrice(amount) {
    if (isNaN(amount) || amount === null || amount === undefined) return '₹0';
    return '₹' + parseFloat(amount).toLocaleString('en-IN', {minimumFractionDigits: 0, maximumFractionDigits: 0});
}

// Add this helper function to update the price summary on the cart page
function updateCartPageSummary() {
    const summaryBox = document.querySelector('.floating-cart-summary-box');
    if (!summaryBox) {
        // If the summary box is missing (cart is empty), reload the page
        location.reload();
        return;
    }
    fetch('ajax/get-cart-summary.php?t=' + Date.now())
        .then(res => res.json())
        .then(summary => {
            if (summary.success) {
                const totals = summary.totals;
                summaryBox.innerHTML = `
                  <div class="d-flex justify-content-between mb-2"><span class="text-muted">Total MRP</span><span style="font-weight:600;">₹${parseFloat(totals.subtotal).toLocaleString('en-IN', {minimumFractionDigits: 0, maximumFractionDigits: 0})}</span></div>
                  <div class="d-flex justify-content-between mb-2"><span class="text-muted">Delivery Charge <i class='bi bi-info-circle' title='Delivery charges may vary'></i></span><span class="text-danger fw-bold">+ Extra</span></div>
                  <div class="d-flex justify-content-between mb-2"><span class="text-muted">Savings</span><span class="fw-bold" style="color:#2e7d32;">₹${parseFloat(totals.total_savings).toLocaleString('en-IN', {minimumFractionDigits: 0, maximumFractionDigits: 0})}</span></div>
                  <div class="d-grid mt-3 mb-2">
                    <a href='checkout.php' class='btn btn-success btn-lg fw-bold' style='font-size:1.08rem;'>PROCEED TO CHECKOUT</a>
                  </div>
                  <div class="d-grid mb-2">
                    <a href='index.php' class='btn btn-outline-secondary btn-lg fw-bold' style='font-size:1.08rem;'>CONTINUE SHOPPING</a>
                  </div>
                `;
            }
        });
}

// Add this helper function to update the header cart count
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
        });
}
</script> 

<style>
@media (max-width: 768px) {
  .cart-header-row, .cart-item-row {
    font-size: 0.93rem !important;
    gap: 2px !important;
    padding-left: 1px !important;
    padding-right: 1px !important;
  }
  .cart-header-row > div, .cart-item-row > div {
    min-width: 28px !important;
    font-size: 0.92em !important;
    padding-left: 0 !important;
    padding-right: 0 !important;
  }
  .cart-item-row > div:nth-child(2) {
    min-width: 40px !important;
    max-width: 90px !important;
  }
  .cart-item-row > div {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }
}
.cart-product-modal {
  position: fixed;
  z-index: 99999;
  left: 0; top: 0; width: 100vw; height: 100vh;
  background: rgba(0,0,0,0.45);
  display: flex; align-items: center; justify-content: center;
}
.cart-product-modal-content {
  background: #fff;
  border-radius: 10px;
  max-width: 420px;
  width: 95vw;
  padding: 24px 18px 18px 18px;
  position: relative;
  box-shadow: 0 4px 24px rgba(0,0,0,0.18);
}
.cart-product-modal-close {
  position: absolute; top: 10px; right: 18px;
  font-size: 1.7rem; font-weight: bold; color: #888; cursor: pointer;
}
@media (max-width: 600px) {
  .cart-product-modal-content { max-width: 98vw; padding: 12px 4px 8px 4px; }
}
</style> 

<script>
document.querySelectorAll('.cart-product-link').forEach(function(link) {
  link.addEventListener('click', function(e) {
    e.preventDefault();
    var productId = this.getAttribute('data-product-id');
    if (!productId) return;
    var modal = document.getElementById('cartProductDetailModal');
    var modalBody = document.getElementById('cartProductModalBody');
    modalBody.innerHTML = '<div style="text-align:center;padding:30px 0;">Loading...</div>';
    modal.style.display = 'flex';
    fetch('ajax/get-product-detail-popup.php?id=' + encodeURIComponent(productId))
      .then(function(res) { return res.text(); })
      .then(function(html) { modalBody.innerHTML = html; });
  });
});
document.getElementById('cartProductModalClose').onclick = function() {
  document.getElementById('cartProductDetailModal').style.display = 'none';
};
document.getElementById('cartProductDetailModal').onclick = function(e) {
  if (e.target === this) this.style.display = 'none';
};

// Remove All Items functionality
document.getElementById('removeAllItems').addEventListener('click', function() {
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
                alert('All items have been removed from your cart.');
                // Redirect to empty cart page or reload
                window.location.reload();
            } else {
                alert('Failed to remove items: ' + (data.message || 'Unknown error'));
                // Reset button
                button.innerHTML = originalText;
                button.disabled = false;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while removing items from cart.');
            // Reset button
            button.innerHTML = originalText;
            button.disabled = false;
        });
    }
});
</script> 