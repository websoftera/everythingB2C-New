// popup.js
function showPopup() {
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
            Swal.fire({
                icon: 'success',
                title: 'Delivery Available',
                text: 'Delivery available in your area.',
                timer: 3000,
                showConfirmButton: false
            });
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
          Swal.fire({
              icon: 'error',
              title: 'Delivery Not Available',
              text: 'Sorry, we do not deliver in this area.',
              timer: 4000,
              showConfirmButton: false
          });
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

    // Update wishlist count in header
    function updateWishlistCount() {
        fetch('ajax/get_wishlist_count.php')
            .then(response => response.json())
            .then(data => {
                const wishlistCountElement = document.querySelector('.wishlist-count');
                if (wishlistCountElement) {
                    const count = data.wishlist_count || 0;
                    if (count > 0) {
                        wishlistCountElement.textContent = count;
                        wishlistCountElement.style.display = 'block';
                    } else {
                        wishlistCountElement.style.display = 'none';
                    }
                }
            })
            .catch(error => console.error('Error updating wishlist count:', error));
    }

    // Helper to re-initialize quantity controls for all product cards and re-attach event handlers
    function reinitQuantityControlsWithDebug() {
        document.querySelectorAll('.product-card, .card, .shop-page-product-card, .product-detail-card, .related-products-container .product-card').forEach(card => {
            card.querySelectorAll('.quantity-input, .shop-page-quantity-input').forEach(input => {
                input.disabled = false;
                input.removeEventListener('input', input._debugInputHandler || (()=>{}));
                const handler = function() {
                    let val = parseInt(this.value, 10) || 1;
                    if (val < 1) val = 1;
                    this.value = val;

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

                };
                btn.addEventListener('click', handler);
                btn._debugClickHandler = handler;
            });
        });

    }


    // Function to load current cart quantity for a product
    function loadCurrentCartQuantity(productId, quantityInput) {
        if (!productId || !quantityInput) {
            return;
        }
        
        fetch(`ajax/check-product-in-cart.php?product_id=${productId}`)
            .then(res => res.json())
            .then(data => {
                if (data.success && data.in_cart && data.quantity > 0) {
                    quantityInput.value = data.quantity;
                }
            })
            .catch(err => {});
    }

    // Global function to initialize quantity inputs (accessible from anywhere)
    window.initializeQuantityInputs = function(force = false) {

        
        // Find all quantity inputs across the site with comprehensive selectors
        const quantityInputs = document.querySelectorAll(`
            .quantity-input, 
            .shop-page-quantity-input, 
            input[name="quantity"], 
            .product-quantity-input,
            input[type="number"][min="1"],
            .quantity-control input[type="number"],
            .cart-actions input[type="number"],
            .cart-controls input[type="number"],
            .related-products-container .quantity-input,
            .related-products-container input[type="number"]
        `);

        
        quantityInputs.forEach((input, index) => {
            // Try multiple ways to find the product ID
            let productId = null;
            
            // Method 1: Check data attributes on the input itself
            productId = input.dataset.productId || input.getAttribute('data-product-id');
            
            // Method 2: Check parent containers
            if (!productId) {
                const productCard = input.closest('.product-card, .card, .shop-page-product-card, .product-detail-card, .related-products-container .product-card, [data-product-id]');
                if (productCard) {
                    productId = productCard.dataset.productId || productCard.getAttribute('data-product-id');
                }
            }
            
            // Method 3: Check for add to cart button in same container
            if (!productId) {
                const container = input.closest('.product-form, .cart-actions, .cart-controls, .shop-page-cart-actions');
                if (container) {
                    const addButton = container.querySelector('.add-to-cart-btn, .add-to-cart, .shop-page-add-to-cart-btn, [data-product-id]');
                    if (addButton) {
                        productId = addButton.dataset.productId || addButton.getAttribute('data-product-id');
                    }
                }
            }
            
            // Method 4: Check siblings for add to cart button
            if (!productId) {
                const parentContainer = input.closest('.product-card, .card, .shop-page-product-card, .product-detail-card');
                if (parentContainer) {
                    const addButton = parentContainer.querySelector('.add-to-cart-btn, .add-to-cart, .shop-page-add-to-cart-btn');
                    if (addButton) {
                        productId = addButton.dataset.productId || addButton.getAttribute('data-product-id');
                    }
                }
            }
            
            // Method 5: Check for any element with data-product-id in the same parent
            if (!productId) {
                const anyParent = input.closest('div, section, article');
                if (anyParent) {
                    const elementWithId = anyParent.querySelector('[data-product-id]');
                    if (elementWithId) {
                        productId = elementWithId.dataset.productId || elementWithId.getAttribute('data-product-id');
                    }
                }
            }
            

            
            if (productId) {
                // Only update if forced or if input value is 1 (default)
                if (force || input.value == "1") {
                    loadCurrentCartQuantity(productId, input);
                }
            } else {

            }
        });
        
        // Also update popup quantity input if popup is open
        const popupQtyInput = document.getElementById('popupQtyInput');
        if (popupQtyInput && window.currentPopupProductId) {
            loadCurrentCartQuantity(window.currentPopupProductId, popupQtyInput);
        }
    };

    document.body.addEventListener('click', function(event) {
        const target = event.target;
        
        // Add to Cart
        if (target.matches('.add-to-cart-btn, .add-to-cart, .shop-page-add-to-cart-btn')) {
            event.preventDefault();
            const productId = target.dataset.productId;
            let quantity = 1;
            let cardRoot = target.closest('.product-card, .card, .shop-page-product-card, .product-detail-card, .related-products-container .product-card');
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
            console.log('Added cart-added-highlight class to button (initial):', target);
            console.log('Button classes after adding:', target.className);
            
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
                            console.log('Added cart-added-highlight class to button (check):', target);
                            console.log('Button classes after adding:', target.className);
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
                    reinitQuantityControlsWithDebug();
                    // Refresh quantity inputs to show updated cart quantities
                    window.initializeQuantityInputs();
                    
                    // Dispatch cart-updated event with animation info
                    window.dispatchEvent(new CustomEvent('cart-updated', {
                      detail: { action: 'updated' }
                    }));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('An error occurred.', 'error');
            });
        }

        // Add/Remove from Wishlist (Toggle functionality)
        if (event.target.matches('.heart-checkbox, .shop-page-heart-checkbox')) {
             const checkbox = event.target;
             const productId = checkbox.dataset.productId;
             const label = checkbox.nextElementSibling;

            if(!productId) return;

            // Use single endpoint for both add and remove
            fetch('ajax/add-to-wishlist.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ product_id: productId })
            })
            .then(response => response.json())
            .then(data => {
                const statusType = data.success ? 'success' : 'error';
                showToast(data.message, statusType);
                
                if(data.success) {
                    // Update checkbox and label based on action
                    if(data.action === 'added') {
                        checkbox.checked = true;
                        if(label) label.classList.add('wishlist-active');
                    } else if(data.action === 'removed') {
                        checkbox.checked = false;
                        if(label) label.classList.remove('wishlist-active');
                    }
                } else {
                    // Revert checkbox state on failure
                    checkbox.checked = !checkbox.checked;
                    if(label) {
                        if(checkbox.checked) {
                            label.classList.add('wishlist-active');
                        } else {
                            label.classList.remove('wishlist-active');
                        }
                    }
                }
                updateWishlistCount(); // Update wishlist count in header
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('An error occurred.', 'error');
                // Revert checkbox state on error
                checkbox.checked = !checkbox.checked;
                if(label) {
                    if(checkbox.checked) {
                        label.classList.add('wishlist-active');
                    } else {
                        label.classList.remove('wishlist-active');
                    }
                }
            });
        }
    });
    
    // Initialize wishlist states on page load
    function initializeWishlistStates() {
        document.querySelectorAll('.heart-checkbox, .shop-page-heart-checkbox').forEach(checkbox => {
            const productId = checkbox.dataset.productId;
            const label = checkbox.nextElementSibling;
            
            if (checkbox.checked) {
                if (label) label.classList.add('wishlist-active');
            } else {
                if (label) label.classList.remove('wishlist-active');
            }
        });
    }

    // Initialize wishlist states when DOM is loaded
    initializeWishlistStates();

    // Initial cart count update
    updateCartCount();
    
    // Function to initialize all quantity inputs with current cart quantities
    function initializeQuantityInputs() {
        // Find all quantity inputs across the site
        const quantityInputs = document.querySelectorAll('.quantity-input, .shop-page-quantity-input, input[name="quantity"], .product-quantity-input');
        
        quantityInputs.forEach(input => {
            // Try multiple ways to find the product ID
            let productId = null;
            
            // Method 1: Check data attributes on the input itself
            productId = input.dataset.productId || input.getAttribute('data-product-id');
            
            // Method 2: Check parent containers
            if (!productId) {
                const productCard = input.closest('.product-card, .card, .shop-page-product-card, .product-detail-card, [data-product-id]');
                if (productCard) {
                    productId = productCard.dataset.productId || productCard.getAttribute('data-product-id');
                }
            }
            
            // Method 3: Check for add to cart button in same container
            if (!productId) {
                const container = input.closest('.product-form, .cart-actions, .cart-controls, .shop-page-cart-actions');
                if (container) {
                    const addButton = container.querySelector('.add-to-cart-btn, .add-to-cart, .shop-page-add-to-cart-btn, [data-product-id]');
                    if (addButton) {
                        productId = addButton.dataset.productId || addButton.getAttribute('data-product-id');
                    }
                }
            }
            
            // Method 4: Check siblings for add to cart button
            if (!productId) {
                const parentContainer = input.closest('.product-card, .card, .shop-page-product-card, .product-detail-card');
                if (parentContainer) {
                    const addButton = parentContainer.querySelector('.add-to-cart-btn, .add-to-cart, .shop-page-add-to-cart-btn');
                    if (addButton) {
                        productId = addButton.dataset.productId || addButton.getAttribute('data-product-id');
                    }
                }
            }
            
            if (productId) {
                loadCurrentCartQuantity(productId, input);
            }
        });
        
        // Also update popup quantity input if popup is open
        const popupQtyInput = document.getElementById('popupQtyInput');
        if (popupQtyInput && window.currentPopupProductId) {
            loadCurrentCartQuantity(window.currentPopupProductId, popupQtyInput);
        }
    }

    // Initial setup
    updateWishlistCount();
    
    // Multiple initialization points to ensure it runs
    
    // 1. Immediate initialization
    initializeQuantityInputs();
    
    // 2. After a short delay
    setTimeout(() => {
        initializeQuantityInputs();
    }, 100);
    
    // 3. When DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            setTimeout(() => {
                initializeQuantityInputs();
            }, 200);
        });
    } else {
        // DOM is already loaded
        setTimeout(() => {
            initializeQuantityInputs();
        }, 200);
    }
    
    // 4. When window is fully loaded
    window.addEventListener('load', () => {
        setTimeout(() => {
            initializeQuantityInputs(true); // Force update on window load
        }, 300);
    });
    
    // 5. Force initialization after additional delay (fallback)
    setTimeout(() => {
        initializeQuantityInputs(true);
    }, 1000);
    
    // Add manual refresh function for debugging
    window.refreshQuantities = function() {

        initializeQuantityInputs(true);
    };
    
    // Initialize quantity inputs when new content is dynamically added
    const observer = new MutationObserver((mutations) => {
        let shouldReinitialize = false;
        mutations.forEach((mutation) => {
            if (mutation.type === 'childList' && mutation.addedNodes.length > 0) {
                // Check if any added nodes contain quantity inputs
                mutation.addedNodes.forEach((node) => {
                    if (node.nodeType === 1) { // Element node
                        if (node.querySelector && node.querySelector('.quantity-input, .shop-page-quantity-input, input[name="quantity"]')) {
                            shouldReinitialize = true;
                        }
                    }
                });
            }
        });
        
        if (shouldReinitialize) {
            setTimeout(() => {
                initializeQuantityInputs();
            }, 50);
        }
    });
    
    // Start observing
    observer.observe(document.body, {
        childList: true,
        subtree: true
    });
});

// Listen for cart updates to refresh quantity inputs
window.addEventListener('cart-updated', function() {
    setTimeout(() => {
        if (window.initializeQuantityInputs) {
            window.initializeQuantityInputs();
        }
    }, 100);
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
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: result.message,
            confirmButtonText: 'OK'
        });
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
    btn.textContent = 'UPDATING...';
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
        btn.textContent = 'UPDATED!';
        setTimeout(function(){ btn.textContent = 'ADD TO CART'; }, 1200);
        // Refresh quantity inputs on the page
        initializeQuantityInputs();
        
        // Dispatch cart-updated event with animation info
        window.dispatchEvent(new CustomEvent('cart-updated', {
          detail: { action: 'added' }
        }));
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
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: data.message || 'Could not add to cart.',
                confirmButtonText: 'OK'
            });
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
      
      fetch('ajax/add-to-wishlist.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ product_id: productId })
      })
      .then(res => res.json())
      .then(data => {
        wishlistBtn.disabled = false;
        if (data.success) {
          if (icon) {
            if (data.action === 'added') {
              icon.style.color = '#DE0085'; // Pink color when added
            } else if (data.action === 'removed') {
              icon.style.color = '#fff'; // White color when removed
            }
          }
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
              Swal.fire({
                  icon: 'error',
                  title: 'Wishlist Error',
                  text: data.message || 'Could not update wishlist.',
                  confirmButtonText: 'OK'
              });
          }
        }
      });
    }
});

// Function to initialize button states for products already in cart
function initializeCartButtonStates() {
    console.log('Initializing cart button states...');
    
    // Find all add-to-cart buttons with comprehensive selectors
    const buttons = document.querySelectorAll(`
        .add-to-cart-btn, 
        .add-to-cart, 
        .shop-page-add-to-cart-btn,
        .related-products-container .add-to-cart-btn,
        .product-card .add-to-cart-btn,
        .card .add-to-cart-btn,
        button[data-product-id]
    `);
    
    console.log('Found buttons:', buttons.length);
    
    buttons.forEach(btn => {
        const productId = btn.dataset.productId;
        if (!productId) return;
        
        console.log('Checking button for product ID:', productId, 'Button:', btn);
        
        fetch(`ajax/check-product-in-cart.php?product_id=${productId}`)
            .then(res => res.json())
            .then(data => {
                if (data.success && data.in_cart) {
                    // Product is in cart, highlight the button but keep it enabled
                    btn.textContent = 'Added to Cart';
                    btn.disabled = false; // Keep enabled so user can add more
                    btn.classList.add('cart-added-highlight');
                    console.log('Button highlighted for product ID:', productId, 'Classes after adding:', btn.className);
                    console.log('Button element:', btn);
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
    setTimeout(() => {
        initializeCartButtonStates();
    }, 100);
});

// Also initialize when cart is updated
document.addEventListener('cart-updated', function() {
    setTimeout(() => {
        initializeCartButtonStates();
    }, 100);
});

// Initialize when window loads (for dynamic content)
window.addEventListener('load', function() {
    setTimeout(() => {
        initializeCartButtonStates();
    }, 200);
});

// Function to update a specific button state
function updateButtonState(productId, inCart) {
    console.log('Updating button state for product ID:', productId, 'in cart:', inCart);
    
    // More comprehensive selector to find all possible add-to-cart buttons
    const selectors = [
        `[data-product-id="${productId}"].add-to-cart-btn`,
        `[data-product-id="${productId}"].add-to-cart`,
        `[data-product-id="${productId}"].shop-page-add-to-cart-btn`,
        `.related-products-container [data-product-id="${productId}"].add-to-cart-btn`,
        `.product-card [data-product-id="${productId}"].add-to-cart-btn`,
        `.card [data-product-id="${productId}"].add-to-cart-btn`,
        `button[data-product-id="${productId}"]`,
        `[data-product-id="${productId}"]`
    ];
    
    let btn = null;
    for (const selector of selectors) {
        btn = document.querySelector(selector);
        if (btn) break;
    }
    
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

// Force pink color on page load for wishlist items
document.addEventListener('DOMContentLoaded', function() {
    // Find all wishlist labels with wishlist-active class
    const activeWishlistLabels = document.querySelectorAll('.wishlist-label.wishlist-active');
    
    activeWishlistLabels.forEach(function(label) {
        const heartIcon = label.querySelector('.heart-icon');
        if (heartIcon) {
            heartIcon.style.color = '#DE0085';
            heartIcon.style.webkitTextStroke = '2px #DE0085';
            heartIcon.style.textStroke = '2px #DE0085';
            heartIcon.style.filter = 'drop-shadow(0 2px 4px rgba(222, 0, 133, 0.3))';
        }
    });
    
    // Also check for checked checkboxes
    const checkedCheckboxes = document.querySelectorAll('.heart-checkbox:checked');
    checkedCheckboxes.forEach(function(checkbox) {
        const label = checkbox.nextElementSibling;
        if (label && label.classList.contains('wishlist-label')) {
            const heartIcon = label.querySelector('.heart-icon');
            if (heartIcon) {
                heartIcon.style.color = '#DE0085';
                heartIcon.style.webkitTextStroke = '2px #DE0085';
                heartIcon.style.textStroke = '2px #DE0085';
                heartIcon.style.filter = 'drop-shadow(0 2px 4px rgba(222, 0, 133, 0.3))';
            }
        }
    });
});
