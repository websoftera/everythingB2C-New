// Real-time Max Quantity Checker
// This file handles real-time max quantity validation for all quantity inputs across the website

class RealTimeMaxQuantityChecker {
    constructor() {
        this.init();
    }

    init() {
        // Initialize for existing elements
        this.initializeQuantityInputs();
        
        // Watch for new elements added to DOM (for dynamic content)
        this.observeDOMChanges();
    }

    initializeQuantityInputs() {
        // Find all quantity inputs
        const quantityInputs = document.querySelectorAll('.quantity-input, .shop-page-quantity-input, .cart-qty-input');
        
        quantityInputs.forEach(input => {
            this.setupQuantityInput(input);
        });
    }

    setupQuantityInput(input) {
        // Skip if already initialized
        if (input.dataset.maxQuantityInitialized === 'true') {
            return;
        }

        // Skip floating cart inputs - they have their own logic
        if (input.classList.contains('cart-qty-input')) {
            return;
        }

        // Get product ID from the input or its parent elements
        const productId = this.getProductIdFromInput(input);
        
        if (!productId) {
            console.warn('No product ID found for quantity input:', input);
            return;
        }

        // Mark as initialized
        input.dataset.maxQuantityInitialized = 'true';
        input.dataset.productId = productId;

        // Remove any previous max quantity display
        this.removeMaxQuantityDisplay(input);

        // Add event listeners
        this.addQuantityInputListeners(input);
    }

    getProductIdFromInput(input) {
        if (input.dataset.productId) {
            return input.dataset.productId;
        }
        const productCard = input.closest('.product-card, .card, .shop-page-product-card, .product-detail-card');
        if (productCard) {
            if (productCard.dataset.productId) {
                return productCard.dataset.productId;
            }
            const addToCartBtn = productCard.querySelector('.add-to-cart-btn, .add-to-cart, .shop-page-add-to-cart-btn');
            if (addToCartBtn && addToCartBtn.dataset.productId) {
                return addToCartBtn.dataset.productId;
            }
        }
        const cartItem = input.closest('[data-cart-id]');
        if (cartItem) {
            return this.getProductIdFromCartItem(cartItem.dataset.cartId);
        }
        return null;
    }

    async getProductIdFromCartItem(cartId) {
        try {
            const response = await fetch('ajax/get_cart_item_product.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ cart_id: cartId })
            });
            const data = await response.json();
            return data.product_id;
        } catch (error) {
            console.error('Error getting product ID from cart item:', error);
            return null;
        }
    }

    removeMaxQuantityDisplay(input) {
        const existingDisplay = input.parentNode.querySelector('.max-quantity-display');
        if (existingDisplay) {
            existingDisplay.remove();
        }
    }

    addQuantityInputListeners(input) {
        input.addEventListener('input', (e) => {
            this.checkMaxQuantity(input);
        });
        input.addEventListener('change', (e) => {
            this.validateQuantityInput(input);
        });
        input.addEventListener('focus', (e) => {
            this.checkMaxQuantity(input);
        });
    }

    async checkMaxQuantity(input) {
        const productId = input.dataset.productId;
        const quantity = parseInt(input.value) || 1;
        if (!productId) {
            return;
        }
        
        // Skip validation for floating cart inputs - they have their own logic
        if (input.classList.contains('cart-qty-input')) {
            return;
        }
        
        try {
            const response = await fetch('ajax/check_max_quantity.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `product_id=${productId}&quantity=${quantity}`
            });
            const allowedQuantity = 99; // Default max if not set
            const result = await response.json();
            if (result.error && result.max_quantity ||  quantity > allowedQuantity) {
                // Limit the input value and show SweetAlert
                if(!result.max_quantity && quantity > allowedQuantity) {
                    input.value = allowedQuantity;
                    result.message = `Maximum quantity is ${allowedQuantity}.`;
                } else {
                    input.value = result.max_quantity;
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
                    alert(result.message);
                }
            }
        } catch (error) {
            console.error('Error checking max quantity:', error);
        }
    }

    validateQuantityInput(input) {
        const value = parseInt(input.value);
        const min = parseInt(input.min) || 1;
        if (value < min) {
            input.value = min;
        }
    }

    observeDOMChanges() {
        const observer = new MutationObserver((mutations) => {
            mutations.forEach((mutation) => {
                mutation.addedNodes.forEach((node) => {
                    if (node.nodeType === Node.ELEMENT_NODE) {
                        if (node.matches('.quantity-input, .shop-page-quantity-input, .cart-qty-input')) {
                            this.setupQuantityInput(node);
                        }
                        const quantityInputs = node.querySelectorAll('.quantity-input, .shop-page-quantity-input, .cart-qty-input');
                        quantityInputs.forEach(input => {
                            this.setupQuantityInput(input);
                        });
                    }
                });
            });
        });
        observer.observe(document.body, {
            childList: true,
            subtree: true
        });
    }
}

document.addEventListener('DOMContentLoaded', function() {
    new RealTimeMaxQuantityChecker();
});
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function() {
        new RealTimeMaxQuantityChecker();
    });
} else {
    new RealTimeMaxQuantityChecker();
} 