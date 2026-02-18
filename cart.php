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

// Breadcrumb Navigation
$breadcrumbs = generateBreadcrumb($pageTitle);
echo renderBreadcrumb($breadcrumbs);
?>

<!-- Banner/Breadcrumb (skip homepage) -->
<link rel="stylesheet" href="./asset/style/style.css">
<link rel="stylesheet" href="./asset/style/responsive-cart-checkout.css">
<div class="container mt-4">
    <!-- <h1>Shopping Cart</h1> -->
    
    <?php if (empty($cartItems)): ?>
        <div class="text-center py-5">
          <img src="./Kichen Page/page2/logo.webp" alt="Logo" class="img-fluid logo" />
            <h3>Your cart is empty</h3>
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
                                <div style="flex:0 0 90px; min-width:60px; font-size:0.97em; color:#007bff; font-weight:500; text-align:center;"> <?php echo formatPrice($item['selling_price'] * $item['quantity']); ?> </div>
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
                                      <div class="d-flex justify-content-between mb-2"><span class="text-muted">Total MRP</span><span class="cart-summary-total-mrp" style="font-weight:600;text-decoration:line-through;">₹<?php echo number_format($orderTotals['total_mrp'], 0); ?></span></div>
                                      <div class="d-flex justify-content-between mb-2"><span class="text-muted">You Pay</span><span class="cart-summary-you-pay" style="font-weight:600;">₹<?php echo number_format($orderTotals['subtotal'], 0); ?></span></div>
                                                  <div class="d-flex justify-content-between mb-2"><span class="text-muted">Savings</span><span class="cart-summary-savings fw-bold" style="color:#2e7d32;">₹<?php
                                $total_savings = 0;
                                foreach ($cartItems as $item) {
                                    $total_savings += ($item['mrp'] - $item['selling_price']) * $item['quantity'];
                                }
                                echo number_format($total_savings, 0);
                            ?></span></div>
            <div class="d-flex justify-content-between mb-2"><span class="text-muted">Delivery Charge <i class='bi bi-info-circle' title='Delivery charges may vary'></i></span><span class="text-danger fw-bold">+ Extra</span></div>
            <div class="d-flex justify-content-between mb-2"></div>
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
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Invalid Quantity',
                        text: 'Please enter a valid quantity (1 or more).',
                        timer: 3000,
                        showConfirmButton: false
                    });
                } else {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Invalid Quantity',
                        text: 'Please enter a valid quantity (1 or more).',
                        confirmButtonText: 'OK'
                    });
                }
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
                    // Fetch updated cart summary and items
                    fetch('ajax/get-cart-summary.php?details=1')
                        .then(res => res.json())
                        .then(summary => {
                            if (summary.success && summary.cartItems) {
                                summary.cartItems.forEach(function(item) {
                                    // Find the cart row by cart id
                                    var row = document.querySelector('.cart-item-row input[data-cart-id="' + item.id + '"]').closest('.cart-item-row');
                                    if (row) {
                                        // Update You Pay (total)
                                        var youPayCell = row.children[3];
                                        if (youPayCell) {
                                            youPayCell.textContent = item.selling_price * item.quantity;
                                        }
                                        // Update You Save
                                        var youSaveCell = row.children[4];
                                        if (youSaveCell) {
                                            youSaveCell.textContent = formatPrice((item.mrp - item.selling_price) * item.quantity);
                                        }
                                        // Update Total
                                        var totalCell = row.children[6];
                                        if (totalCell) {
                                            totalCell.textContent = formatPrice(item.selling_price * item.quantity);
                                        }
                                    }
                                });
                                // Update summary components using the unified function
                                updateCartPageSummary();
                            }
                        });
                    // updateCartPageSummary();
                    // updateFloatingCartCount();
                    // Update order summary
                    // fetch('ajax/get-cart-summary.php') // This line is now handled by updateCartPageSummary()
                    //     .then(res => res.json())
                    //     .then(summary => {
                    //         if (summary.success) {
                    //             const totals = summary.totals;
                    //             document.getElementById('cart-shipping').textContent = (totals.total_shipping > 0) ? formatPrice(totals.total_shipping) : 'Free';
                    //             document.getElementById('cart-gst').textContent = formatPrice(totals.total_gst);
                    //             document.getElementById('cart-grandtotal').textContent = isNaN(totals.grand_total) ? formatPrice(0) : formatPrice(totals.grand_total);
                    //         }
                    //     });
                } else {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Error: ' + data.message,
                            timer: 4000,
                            showConfirmButton: false
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Error: ' + data.message,
                            confirmButtonText: 'OK'
                        });
                    }
                }
            });
        });
    });
    
    // Remove item
    const removeButtons = document.querySelectorAll('.remove-item');
    removeButtons.forEach(button => {
        button.addEventListener('click', function() {
            const cartId = this.getAttribute('data-cart-id');
            
            Swal.fire({
                title: 'Remove Item?',
                text: 'Are you sure you want to remove this item?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes',
                cancelButtonText: 'No'
            }).then((result) => {
                if (result.isConfirmed) {
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
                        // Dispatch cart-item-removed event
                        if (data.product_id) {
                            window.dispatchEvent(new CustomEvent('cart-item-removed', {
                                detail: { productId: data.product_id }
                            }));
                            
                            // Also dispatch cart-updated event to trigger button re-initialization
                            window.dispatchEvent(new Event('cart-updated'));
                        }
                        
                        location.reload();
                        updateCartPageSummary();
                        updateFloatingCartCount();
                    } else {
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Error: ' + data.message,
                                timer: 4000,
                                showConfirmButton: false
                            });
                        } else {
                            Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Error: ' + data.message,
                            confirmButtonText: 'OK'
                        });
                        }
                    }
                });
                }
            });
        });
    });
});

function formatPrice(amount) {
    if (isNaN(amount) || amount === null || amount === undefined) return '₹ 0';
    return '₹ ' + parseFloat(amount).toLocaleString('en-IN', {minimumFractionDigits: 0, maximumFractionDigits: 0});
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
                  <div class="d-flex justify-content-between mb-2"><span class="text-muted">Total MRP</span><span class="cart-summary-total-mrp" style="font-weight:600;text-decoration:line-through;">₹ ${parseFloat(totals.total_mrp || totals.subtotal).toLocaleString('en-IN', {minimumFractionDigits: 0, maximumFractionDigits: 0})}</span></div>
                  <div class="d-flex justify-content-between mb-2"><span class="text-muted">You Pay</span><span class="cart-summary-you-pay" style="font-weight:600;">₹ ${parseFloat(totals.subtotal).toLocaleString('en-IN', {minimumFractionDigits: 0, maximumFractionDigits: 0})}</span></div>
                  <div class="d-flex justify-content-between mb-2"><span class="text-muted">Savings</span><span class="cart-summary-savings fw-bold" style="color:#2e7d32;">₹ ${parseFloat(totals.total_savings).toLocaleString('en-IN', {minimumFractionDigits: 0, maximumFractionDigits: 0})}</span></div>
                  <div class="d-flex justify-content-between mb-2"><span class="text-muted">Delivery Charge <i class='bi bi-info-circle' title='Delivery charges may vary'></i></span><span class="text-danger fw-bold">+ Extra</span></div>
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
/* Responsive Cart Table Styles - Keep Horizontal Layout */
@media (max-width: 768px) {
  .cart-header-row, .cart-item-row {
    font-size: 0.85rem !important;
    gap: 3px !important;
    padding: 6px 3px !important;
    flex-wrap: nowrap !important;
    overflow-x: auto !important;
  }
  
  .cart-header-row > div, .cart-item-row > div {
    min-width: auto !important;
    font-size: 0.85em !important;
    padding: 1px !important;
    flex-shrink: 0 !important;
  }
  
  /* Product image */
  .cart-item-row > div:first-child {
    flex: 0 0 35px !important;
    max-width: 35px !important;
  }
  
  /* Product name */
  .cart-item-row > div:nth-child(2) {
    flex: 0 0 100px !important;
    min-width: 80px !important;
    max-width: 100px !important;
    font-size: 0.8em !important;
  }
  
  /* MRP */
  .cart-item-row > div:nth-child(3) {
    flex: 0 0 50px !important;
    min-width: 45px !important;
    font-size: 0.75em !important;
  }
  
  /* You Pay */
  .cart-item-row > div:nth-child(4) {
    flex: 0 0 50px !important;
    min-width: 45px !important;
    font-size: 0.75em !important;
  }
  
  /* You Save */
  .cart-item-row > div:nth-child(5) {
    flex: 0 0 50px !important;
    min-width: 45px !important;
    font-size: 0.75em !important;
  }
  
  /* Quantity */
  .cart-item-row > div:nth-child(6) {
    flex: 0 0 65px !important;
    min-width: 60px !important;
  }
  
  /* Total */
  .cart-item-row > div:nth-child(7) {
    flex: 0 0 50px !important;
    min-width: 45px !important;
    font-size: 0.75em !important;
  }
  
  /* Delete button */
  .cart-item-row > div:last-child {
    flex: 0 0 28px !important;
    min-width: 25px !important;
  }
  
  /* Quantity controls */
  .quantity-control {
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 1px !important;
  }
  
  .quantity-control .btn-qty {
    width: 18px !important;
    height: 18px !important;
    font-size: 9px !important;
    padding: 0 !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
  }
  
  .quantity-control .quantity-input {
    width: 22px !important;
    height: 18px !important;
    font-size: 9px !important;
    padding: 1px !important;
    text-align: center !important;
  }
  
  /* Delete button */
  .remove-item {
    padding: 2px 3px !important;
    font-size: 0.7rem !important;
    min-width: 22px !important;
    height: 22px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
  }
  
  /* Hide header on mobile for better space */
  .cart-header-row {
    display: none !important;
  }
}

@media (max-width: 576px) {
  .cart-header-row, .cart-item-row {
    font-size: 0.8rem !important;
    gap: 2px !important;
    padding: 5px 2px !important;
  }
  
  /* Product image */
  .cart-item-row > div:first-child {
    flex: 0 0 30px !important;
    max-width: 30px !important;
  }
  
  /* Product name */
  .cart-item-row > div:nth-child(2) {
    flex: 0 0 80px !important;
    min-width: 70px !important;
    max-width: 80px !important;
    font-size: 0.75em !important;
  }
  
  /* MRP */
  .cart-item-row > div:nth-child(3) {
    flex: 0 0 45px !important;
    min-width: 40px !important;
    font-size: 0.7em !important;
  }
  
  /* You Pay */
  .cart-item-row > div:nth-child(4) {
    flex: 0 0 45px !important;
    min-width: 40px !important;
    font-size: 0.7em !important;
  }
  
  /* You Save */
  .cart-item-row > div:nth-child(5) {
    flex: 0 0 45px !important;
    min-width: 40px !important;
    font-size: 0.7em !important;
  }
  
  /* Quantity */
  .cart-item-row > div:nth-child(6) {
    flex: 0 0 60px !important;
    min-width: 55px !important;
  }
  
  /* Total */
  .cart-item-row > div:nth-child(7) {
    flex: 0 0 45px !important;
    min-width: 40px !important;
    font-size: 0.7em !important;
  }
  
  /* Delete button */
  .cart-item-row > div:last-child {
    flex: 0 0 25px !important;
    min-width: 22px !important;
  }
  
  .quantity-control .btn-qty {
    width: 16px !important;
    height: 16px !important;
    font-size: 8px !important;
  }
  
  .quantity-control .quantity-input {
    width: 20px !important;
    height: 16px !important;
    font-size: 8px !important;
  }
  
  .remove-item {
    padding: 1px 2px !important;
    font-size: 0.65rem !important;
    min-width: 20px !important;
    height: 20px !important;
  }
}

@media (max-width: 480px) {
  .cart-header-row, .cart-item-row {
    font-size: 0.75rem !important;
    gap: 1px !important;
    padding: 4px 1px !important;
  }
  
  /* Product image */
  .cart-item-row > div:first-child {
    flex: 0 0 25px !important;
    max-width: 25px !important;
  }
  
  /* Product name */
  .cart-item-row > div:nth-child(2) {
    flex: 0 0 70px !important;
    min-width: 60px !important;
    max-width: 70px !important;
    font-size: 0.7em !important;
  }
  
  /* MRP */
  .cart-item-row > div:nth-child(3) {
    flex: 0 0 40px !important;
    min-width: 35px !important;
    font-size: 0.65em !important;
  }
  
  /* You Pay */
  .cart-item-row > div:nth-child(4) {
    flex: 0 0 40px !important;
    min-width: 35px !important;
    font-size: 0.65em !important;
  }
  
  /* You Save */
  .cart-item-row > div:nth-child(5) {
    flex: 0 0 40px !important;
    min-width: 35px !important;
    font-size: 0.65em !important;
  }
  
  /* Quantity */
  .cart-item-row > div:nth-child(6) {
    flex: 0 0 55px !important;
    min-width: 50px !important;
  }
  
  /* Total */
  .cart-item-row > div:nth-child(7) {
    flex: 0 0 40px !important;
    min-width: 35px !important;
    font-size: 0.65em !important;
  }
  
  /* Delete button */
  .cart-item-row > div:last-child {
    flex: 0 0 22px !important;
    min-width: 20px !important;
  }
  
  .quantity-control .btn-qty {
    width: 15px !important;
    height: 15px !important;
    font-size: 7px !important;
  }
  
  .quantity-control .quantity-input {
    width: 18px !important;
    height: 15px !important;
    font-size: 7px !important;
  }
  
  .remove-item {
    padding: 1px !important;
    font-size: 0.6rem !important;
    min-width: 18px !important;
    height: 18px !important;
  }
}
/* Additional responsive fixes for cart */
.cart-item-row .quantity-control {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 2px;
}

.cart-item-row .btn-qty {
  width: 24px;
  height: 24px;
  font-size: 12px;
  padding: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  border: 1px solid #ddd;
  background: #f8f9fa;
  border-radius: 3px;
  cursor: pointer;
}

.cart-item-row .btn-qty:hover {
  background: #e9ecef;
}

.cart-item-row .quantity-input {
  width: 30px;
  height: 24px;
  font-size: 12px;
  padding: 2px;
  text-align: center;
  border: 1px solid #ddd;
  border-radius: 3px;
}

.cart-item-row .remove-item {
  padding: 4px 6px;
  font-size: 0.9rem;
  border-radius: 4px;
  display: flex;
  align-items: center;
  justify-content: center;
  min-width: 28px;
  height: 28px;
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
                // Redirect to empty cart page or reload
                window.location.reload();
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
</script> 