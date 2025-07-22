// popup.js
console.log('popup.js loaded');
function showPopup() {
  console.log('showPopup called');
  var overlay = document.getElementById("popupOverlay");
  var form = document.getElementById("popupForm");
  if (overlay) overlay.style.display = "block";
  if (form) form.style.display = "block";
}

function closeLoginForm() {
  document.getElementById("popupOverlay").style.display = "none";
  document.getElementById("popupForm").style.display = "none";
}

function checkPincode() {
  const pin = document.getElementById("pin").value;
  const serviceablePins = ["400001", "500001", "560001", "380001"]; // example pins

  if (serviceablePins.includes(pin)) {
    alert("Delivery available in your area.");
  } else {
    alert("Sorry, we do not deliver in this area.");
  }
}

// Show popup 5 seconds after page load
window.addEventListener("load", () => {
  setTimeout(showPopup, 3000);
});

document.addEventListener('DOMContentLoaded', function () {

    // Create and append the toast element to the body
    const toast = document.createElement('div');
    toast.className = 'toast-notification';
    document.body.appendChild(toast);

    function showToast(message, type = 'success') {
        toast.textContent = message;
        toast.className = 'toast-notification'; // Reset classes
        toast.classList.add(type, 'show');

        setTimeout(() => {
            toast.classList.remove('show');
        }, 3000); // Hide after 3 seconds
    }
    
    function updateCartCount() {
        fetch('ajax/get_cart_count.php')
            .then(response => response.json())
            .then(data => {
                const cartCountElement = document.getElementById('cart-count');
                if (cartCountElement) {
                    if (data.cart_count > 0) {
                        cartCountElement.textContent = data.cart_count;
                        cartCountElement.style.display = 'block';
                    } else {
                        cartCountElement.style.display = 'none';
                    }
                }
            })
            .catch(error => console.error('Error updating cart count:', error));
    }

    // Helper to re-initialize quantity controls for all product cards and re-attach event handlers
    function reinitQuantityControlsWithDebug() {
        document.querySelectorAll('.product-card, .card, .shop-page-product-card, .product-detail-card').forEach(card => {
            card.querySelectorAll('.quantity-input, .shop-page-quantity-input').forEach(input => {
                input.disabled = false;
                input.removeEventListener('input', input._debugInputHandler || (()=>{}));
                const handler = function() {
                    let val = parseInt(this.value, 10) || 1;
                    if (val < 1) val = 1;
                    this.value = val;
                    console.log('[popup.js][DEBUG] Quantity input changed:', this, 'value:', val);
                };
                input.addEventListener('input', handler);
                input._debugInputHandler = handler;
            });
            card.querySelectorAll('.btn-qty-minus, .btn-qty-plus').forEach(btn => {
                btn.disabled = false;
                btn.removeEventListener('click', btn._debugClickHandler || (()=>{}));
                const handler = function(e) {
                    e.stopPropagation();
                    const input = btn.closest('.quantity-control')?.querySelector('input[type="number"]');
                    if (!input) return;
                    let value = parseInt(input.value, 10) || 1;
                    if (btn.classList.contains('btn-qty-minus')) {
                        value = Math.max(1, value - 1);
                    } else if (btn.classList.contains('btn-qty-plus')) {
                        value = value + 1;
                    }
                    input.value = value;
                    input.dispatchEvent(new Event('input', { bubbles: true }));
                    input.dispatchEvent(new Event('change', { bubbles: true }));
                    console.log('[popup.js][DEBUG] Qty button clicked:', btn, 'new value:', value);
                };
                btn.addEventListener('click', handler);
                btn._debugClickHandler = handler;
            });
        });
        console.log('[popup.js][DEBUG] Re-initialized all quantity controls and handlers after add-to-cart');
    }


    document.body.addEventListener('click', function(event) {
        const target = event.target;
        
        // Add to Cart
        if (target.matches('.add-to-cart-btn, .add-to-cart, .shop-page-add-to-cart-btn')) {
            event.preventDefault();
            const productId = target.dataset.productId;
            let quantity = 1;
            let cardRoot = target.closest('.product-card, .card, .shop-page-product-card, .product-detail-card');
            let cartActions = target.closest('.cart-actions');
            let cartControls = target.closest('.cart-controls');
            let shopCartActions = target.closest('.shop-page-cart-actions');
            let quantityInput = null;
            if (cartActions) {
                quantityInput = cartActions.querySelector('.quantity-input');
                if (quantityInput) {
                    quantity = parseInt(quantityInput.value, 10);
                }
            } else if (cartControls) {
                quantityInput = cartControls.querySelector('.quantity-input');
                if (quantityInput) {
                    quantity = parseInt(quantityInput.value, 10);
                }
            } else if (shopCartActions) {
                quantityInput = shopCartActions.querySelector('.shop-page-quantity-input');
                if (quantityInput) {
                    quantity = parseInt(quantityInput.value, 10);
                }
            }
            const originalLabel = target.textContent;
            target.disabled = true;
            target.textContent = 'Added to Cart';
            setTimeout(function() {
                target.textContent = originalLabel;
                target.disabled = false;
                if (cardRoot) {
                    cardRoot.querySelectorAll('.quantity-input, .shop-page-quantity-input').forEach(el => el.disabled = false);
                    cardRoot.querySelectorAll('.btn-qty-minus, .btn-qty-plus').forEach(el => el.disabled = false);
                }
                reinitQuantityControlsWithDebug();
                console.log('[popup.js][DEBUG] Re-enabled quantity controls for product card:', cardRoot);
            }, 4000);
            if (cardRoot) {
                cardRoot.querySelectorAll('.quantity-input, .shop-page-quantity-input').forEach(el => el.disabled = false);
                cardRoot.querySelectorAll('.btn-qty-minus, .btn-qty-plus').forEach(el => el.disabled = false);
            }
            console.log('[popup.js][DEBUG] Add to cart clicked for productId:', productId, 'quantity:', quantity, 'cardRoot:', cardRoot);
            fetch('ajax/add-to-cart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    product_id: productId,
                    quantity: quantity
                })
            })
            .then(response => response.json())
            .then(data => {
                const statusType = data.success ? 'success' : 'error';
                showToast(data.message, statusType);
                if (data.success) {
                    updateCartCount();
                    window.dispatchEvent(new Event('cart-updated'));
                    reinitQuantityControlsWithDebug();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('An error occurred.', 'error');
            });
        }

        // Add/Remove from Wishlist
        if (event.target.matches('.heart-checkbox')) {
             const checkbox = event.target;
             const productId = checkbox.dataset.productId;
             const label = checkbox.nextElementSibling;

            if(!productId) return;

            if (checkbox.checked) {
                // Add to Wishlist
                 fetch('ajax/add-to-wishlist.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ product_id: productId })
                })
                .then(response => response.json())
                .then(data => {
                    const statusType = data.success ? 'success' : 'error';
                    showToast(data.message, statusType);
                    if(!data.success) {
                        checkbox.checked = false; // Revert checkbox on failure
                        if(label) label.classList.remove('wishlist-active');
                    } else {
                        if(label) label.classList.add('wishlist-active');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast('An error occurred.', 'error');
                    checkbox.checked = false; // Revert checkbox on failure
                    if(label) label.classList.remove('wishlist-active');
                });
            } else {
                // Remove from Wishlist
                fetch('ajax/remove-from-wishlist.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ product_id: productId })
                })
                .then(response => response.json())
                .then(data => {
                    const statusType = data.success ? 'success' : 'error';
                    showToast(data.message, statusType);
                    if(!data.success) {
                        checkbox.checked = true; // Revert checkbox on failure
                        if(label) label.classList.add('wishlist-active');
                    } else {
                        if(label) label.classList.remove('wishlist-active');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast('An error occurred.', 'error');
                    checkbox.checked = true; // Revert checkbox on failure
                    if(label) label.classList.add('wishlist-active');
                });
            }
        }
    });
    
    // Initial cart count update
    updateCartCount();
});

// Global handler for all quantity plus/minus buttons (event delegation)
document.body.addEventListener('click', function(e) {
  const btn = e.target;
  if (!btn.classList.contains('btn-qty')) return;
  const control = btn.closest('.quantity-control');
  if (!control) return;
  const input = control.querySelector('input[type="number"]');
  if (!input) return;
  let value = parseInt(input.value, 10) || 1;
  if (btn.classList.contains('btn-qty-minus')) {
    value = Math.max(1, value - 1);
  } else if (btn.classList.contains('btn-qty-plus')) {
    value = value + 1;
  }
  input.value = value;
  // If this input is for a cart item, update cart via AJAX
  const cartId = input.dataset.cartId;
  if (cartId) {
    fetch('ajax/update-cart.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ cart_id: cartId, quantity: value })
    })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        window.dispatchEvent(new Event('cart-updated'));
      }
    });
  }
  input.dispatchEvent(new Event('input', { bubbles: true }));
  input.dispatchEvent(new Event('change', { bubbles: true }));
});

// Global handler for direct quantity input changes
// (for cart items, update cart via AJAX)
document.body.addEventListener('change', function(e) {
  const input = e.target;
  if (!input.classList.contains('quantity-input')) return;
  let value = parseInt(input.value, 10) || 1;
  input.value = value;
  const cartId = input.dataset.cartId;
  if (cartId) {
    fetch('ajax/update-cart.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ cart_id: cartId, quantity: value })
    })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        window.dispatchEvent(new Event('cart-updated'));
      }
    });
  }
});

// Event delegation for popup controls
// Handles popup quantity, add-to-cart, and wishlist

document.body.addEventListener('click', function(e) {
  // Quantity minus in popup
  if (e.target.id === 'popupQtyMinus') {
    const qtyInput = document.getElementById('popupQtyInput');
    if (qtyInput) qtyInput.value = Math.max(1, parseInt(qtyInput.value, 10) - 1);
  }
  // Quantity plus in popup
  if (e.target.id === 'popupQtyPlus') {
    const qtyInput = document.getElementById('popupQtyInput');
    if (qtyInput) qtyInput.value = parseInt(qtyInput.value, 10) + 1;
  }
  // Add to cart in popup
  if (e.target.id === 'popupAddToCartBtn') {
    e.preventDefault();
    const btn = e.target;
    const qtyInput = document.getElementById('popupQtyInput');
    const qty = qtyInput ? parseInt(qtyInput.value, 10) || 1 : 1;
    // Try to get productId from data attribute or fallback to global
    const productId = btn.dataset.productId || btn.getAttribute('data-product-id') || window.currentPopupProductId;
    if (!productId) return;
    btn.disabled = true;
    btn.textContent = 'ADDING...';
    fetch('ajax/add-to-cart.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ product_id: productId, quantity: qty })
    })
    .then(res => res.json())
    .then(data => {
      btn.disabled = false;
      btn.textContent = 'ADD TO CART';
      if (data.success) {
        window.dispatchEvent(new Event('cart-updated'));
        btn.textContent = 'ADDED!';
        setTimeout(function(){ btn.textContent = 'ADD TO CART'; }, 1200);
      } else {
        alert(data.message || 'Could not add to cart.');
      }
    });
  }
  // Wishlist in popup
  if (e.target.id === 'popupWishlistBtn' || (e.target.closest && e.target.closest('#popupWishlistBtn'))) {
    e.preventDefault();
    const wishlistBtn = document.getElementById('popupWishlistBtn');
    if (!wishlistBtn) return;
    const icon = wishlistBtn.querySelector('i');
    wishlistBtn.disabled = true;
    // Try to get productId from data attribute or fallback to global
    const productId = wishlistBtn.dataset.productId || window.currentPopupProductId;
    const action = icon && icon.style.color === 'orange' ? 'remove' : 'add';
    fetch('ajax/' + (action === 'add' ? 'add-to-wishlist.php' : 'remove-from-wishlist.php'), {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ product_id: productId })
    })
    .then(res => res.json())
    .then(data => {
      wishlistBtn.disabled = false;
      if (data.success) {
        if (icon) icon.style.color = action === 'add' ? 'orange' : '#fff';
      } else {
        alert(data.message || 'Could not update wishlist.');
      }
    });
  }
});
