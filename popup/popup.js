// popup.js
console.log('popup.js loaded');

// Test event listener registration
window.addEventListener('cart-item-removed', function(event) {
    console.log('TEST: Cart item removed event received:', event.detail);
});

// Additional test to ensure event listener is working
document.addEventListener('DOMContentLoaded', function() {
    console.log('popup.js: DOMContentLoaded - Event listeners should be attached');
});
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
            if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'success',
                title: 'Delivery Available',
                text: 'Delivery available in your area.',
                timer: 3000,
                showConfirmButton: false
            });
        } else {
            alert("Delivery available in your area.");
        }
  } else {
          if (typeof Swal !== 'undefined') {
          Swal.fire({
              icon: 'error',
              title: 'Delivery Not Available',
              text: 'Sorry, we do not deliver in this area.',
              timer: 4000,
              showConfirmButton: false
          });
      } else {
          alert("Sorry, we do not deliver in this area.");
      }
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
            target.textContent = 'Added to Cart';
            // --- HIGHLIGHT BUTTON ---
            target.classList.add('cart-added-highlight');
            
            // Check if product is already in cart after adding
            setTimeout(function() {
                fetch(`ajax/check-product-in-cart.php?product_id=${productId}`)
                    .then(res => res.json())
                    .then(data => {
                        if (data.success && data.in_cart) {
                            // Product is in cart, keep it highlighted but enabled
                            target.textContent = 'Added to Cart';
                            target.disabled = false; // Keep enabled so user can add more
                            target.classList.add('cart-added-highlight');
                        } else {
                            // Product not in cart, revert to original state
                            target.textContent = originalLabel;
                            target.disabled = false;
                            target.classList.remove('cart-added-highlight');
                        }
                        
                        if (cardRoot) {
                            cardRoot.querySelectorAll('.quantity-input, .shop-page-quantity-input').forEach(el => el.disabled = false);
                            cardRoot.querySelectorAll('.btn-qty-minus, .btn-qty-plus').forEach(el => el.disabled = false);
                        }
                        reinitQuantityControlsWithDebug();
                        console.log('[popup.js][DEBUG] Re-enabled quantity controls for product card:', cardRoot);
                    })
                    .catch(error => {
                        console.error('Error checking cart status:', error);
                        // Fallback: revert to original state
                        target.textContent = originalLabel;
                        target.disabled = false;
                        target.classList.remove('cart-added-highlight');
                        if (cardRoot) {
                            cardRoot.querySelectorAll('.quantity-input, .shop-page-quantity-input').forEach(el => el.disabled = false);
                            cardRoot.querySelectorAll('.btn-qty-minus, .btn-qty-plus').forEach(el => el.disabled = false);
                        }
                        reinitQuantityControlsWithDebug();
                    });
            }, 3000);
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
document.body.addEventListener('change', async function(e) {
  const input = e.target;
  if (!input.classList.contains('cart-qty-input')) return;
  const value = parseInt(input.value, 10) || 1;
  // Try to get product_id from data attribute or parent
  let productId = input.dataset.productId;
  if (!productId) {
    // Try to find from parent card or container
    const card = input.closest('[data-product-id]');
    if (card) productId = card.getAttribute('data-product-id');
  }
  if (!productId) return;
  // Check max quantity via AJAX
  try {
    const formData = new URLSearchParams();
    formData.append('product_id', productId);
    formData.append('quantity', value);
    const response = await fetch('ajax/check_max_quantity.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: formData.toString()
    });
    const result = await response.json();
    if (result.error && result.max_quantity) {
      input.value = result.max_quantity;
      if (typeof Swal !== 'undefined') {
        Swal.fire({
          icon: 'error',
          title: 'Maximum quantity reached',
          text: result.message,
          timer: 4000,
          showConfirmButton: false
        });
      } else {
        alert(result.message);
      }
    }
  } catch (err) {
    console.error('Error checking max quantity:', err);
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
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: data.message || 'Could not add to cart.',
                timer: 4000,
                showConfirmButton: false
            });
        } else {
            alert(data.message || 'Could not add to cart.');
        }
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
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: data.message || 'Could not update wishlist.',
                timer: 4000,
                showConfirmButton: false
            });
        } else {
            alert(data.message || 'Could not update wishlist.');
        }
      }
    });
  }
});

// Function to initialize button states for products already in cart
function initializeCartButtonStates() {
    console.log('Initializing cart button states...');
    document.querySelectorAll('.add-to-cart-btn, .add-to-cart, .shop-page-add-to-cart-btn').forEach(btn => {
        const productId = btn.dataset.productId;
        if (!productId) return;
        
        fetch(`ajax/check-product-in-cart.php?product_id=${productId}`)
            .then(res => res.json())
            .then(data => {
                if (data.success && data.in_cart) {
                    // Product is in cart, highlight the button but keep it enabled
                    btn.textContent = 'Added to Cart';
                    btn.disabled = false; // Keep enabled so user can add more
                    btn.classList.add('cart-added-highlight');
                    console.log('Button highlighted for product ID:', productId);
                } else {
                    // Product not in cart, ensure button is normal
                    btn.textContent = 'Add to Cart';
                    btn.disabled = false;
                    btn.classList.remove('cart-added-highlight');
                    console.log('Button normalized for product ID:', productId);
                }
            })
            .catch(error => {
                console.error('Error checking cart status for button initialization:', error);
            });
    });
}

// Initialize button states when page loads
document.addEventListener('DOMContentLoaded', function() {
    initializeCartButtonStates();
});

// Also initialize when cart is updated
document.addEventListener('cart-updated', function() {
    initializeCartButtonStates();
});

// Function to update a specific button state
function updateButtonState(productId, inCart) {
    console.log('Updating button state for product ID:', productId, 'in cart:', inCart);
    const btn = document.querySelector(`[data-product-id="${productId}"].add-to-cart-btn, [data-product-id="${productId}"].add-to-cart, [data-product-id="${productId}"].shop-page-add-to-cart-btn, button[data-product-id="${productId}"]`);
    
    if (btn) {
        if (inCart) {
            btn.textContent = 'Added to Cart';
            btn.disabled = false; // Keep enabled so user can add more
            btn.classList.add('cart-added-highlight');
        } else {
            btn.textContent = 'Add to Cart';
            btn.disabled = false;
            btn.classList.remove('cart-added-highlight');
        }
        console.log('Button state updated for product ID:', productId, 'Button found:', btn);
    } else {
        console.log('No button found for product ID:', productId);
        // Let's try a broader search to see what buttons exist
        const allButtons = document.querySelectorAll('[data-product-id]');
        console.log('All buttons with data-product-id:', allButtons);
        allButtons.forEach(btn => {
            console.log('Button:', btn.dataset.productId, 'Classes:', btn.className);
        });
    }
}

// Handle cart item removal to update button states
window.addEventListener('cart-item-removed', function(event) {
    console.log('Cart item removed event received:', event.detail);
    const productId = event.detail?.productId;
    if (productId) {
        console.log('Attempting to update button state for product ID:', productId);
        // Update the specific button state
        updateButtonState(productId, false);
        
        // Also check if the button was actually updated by re-checking cart status
        setTimeout(() => {
            console.log('Re-checking cart status for product ID:', productId);
            fetch(`ajax/check-product-in-cart.php?product_id=${productId}`)
                .then(res => res.json())
                .then(data => {
                    console.log('Cart status check result:', data);
                    if (!data.success || !data.in_cart) {
                        console.log('Product confirmed not in cart, ensuring button is unhighlighted');
                        updateButtonState(productId, false);
                    }
                })
                .catch(error => {
                    console.error('Error checking cart status:', error);
                });
        }, 1000);
    } else {
        // If no specific product ID, reinitialize all buttons
        console.log('No product ID provided, reinitializing all buttons');
        initializeCartButtonStates();
    }
});

// Handle cart remove all to update all button states
window.addEventListener('cart-removed-all', function(event) {
    console.log('Cart removed all event received');
    // Reinitialize all buttons to remove highlighting
    initializeCartButtonStates();
});
