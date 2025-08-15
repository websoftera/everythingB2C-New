// Max Quantity Check JavaScript
// This file handles maximum quantity validation for cart operations

class MaxQuantityChecker {
    constructor() {
        this.init();
    }

    init() {
        // Add event listeners to all add-to-cart buttons
        this.addEventListeners();
    }

    addEventListeners() {
        // Listen for add-to-cart button clicks
        document.addEventListener('click', (e) => {
            if (e.target.matches('.add-to-cart-btn') || e.target.closest('.add-to-cart-btn')) {
                e.preventDefault();
                const button = e.target.matches('.add-to-cart-btn') ? e.target : e.target.closest('.add-to-cart-btn');
                this.handleAddToCart(button);
            }
        });

        // Listen for quantity input changes
        document.addEventListener('change', (e) => {
            if (e.target.matches('.quantity-input')) {
                if (parseInt(e.target.value) > 99) e.target.value = 99;
                this.checkMaxQuantity(e.target);
            }
        });

        // Listen for quantity input keyup for real-time validation
        document.addEventListener('input', (e) => {
            if (e.target.matches('.quantity-input')) {
                if (e.target.value.length > 2) e.target.value = e.target.value.slice(0,2);
                if (parseInt(e.target.value) > 99) e.target.value = 99;
                this.validateQuantityInput(e.target);
            }
        });

        // Listen for blur to clamp value
        document.addEventListener('blur', (e) => {
            if (e.target.matches('.quantity-input')) {
                if (parseInt(e.target.value) > 99) e.target.value = 99;
            }
        }, true);
    }

    async handleAddToCart(button) {
        const productId = button.dataset.productId;
        const quantityInput = button.closest('.product-form').querySelector('.quantity-input');
        const quantity = quantityInput ? parseInt(quantityInput.value) || 1 : 1;

        // Show loading state
        this.showLoading(button);

        try {
            // Check max quantity first
            const checkResult = await this.checkMaxQuantityBeforeAdd(productId, quantity);
            
            if (checkResult.success) {
                // Proceed with adding to cart
                const result = await this.addToCart(productId, quantity);
                if (result.success) {
                    this.showSuccess('Product added to cart successfully!');
                    this.updateCartCount();
                    this.highlightProductCard(button);
                } else {
                    this.showError(result.message);
                }
            } else {
                this.showError(checkResult.message);
            }
        } catch (error) {
            console.error('Error:', error);
            this.showError('An error occurred. Please try again.');
        } finally {
            this.hideLoading(button);
        }
    }

    async checkMaxQuantityBeforeAdd(productId, quantity) {
        try {
            const response = await fetch('ajax/check_max_quantity.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `product_id=${productId}&quantity=${quantity}`
            });

            const result = await response.json();
            return result;
        } catch (error) {
            console.error('Error checking max quantity:', error);
            return { error: 'Failed to check quantity limits' };
        }
    }

    async addToCart(productId, quantity) {
        try {
            const response = await fetch('ajax/add-to-cart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    product_id: productId,
                    quantity: quantity
                })
            });

            return await response.json();
        } catch (error) {
            console.error('Error adding to cart:', error);
            return { success: false, message: 'Failed to add to cart' };
        }
    }

    async checkMaxQuantity(input) {
        const productId = input.dataset.productId;
        const quantity = parseInt(input.value) || 1;

        if (quantity < 1) {
            this.showQuantityError(input, 'Quantity must be at least 1');
            return false;
        }

        try {
            const result = await this.checkMaxQuantityBeforeAdd(productId, quantity);
            
            if (result.error) {
                this.showQuantityError(input, result.message);
                return false;
            } else {
                this.clearQuantityError(input);
                return true;
            }
        } catch (error) {
            console.error('Error checking quantity:', error);
            return false;
        }
    }

    validateQuantityInput(input) {
        const value = parseInt(input.value);
        const min = parseInt(input.min) || 1;
        const max = parseInt(input.max);

        if (value < min) {
            input.value = min;
        } else if (max && value > max) {
            input.value = max;
        }
    }

    showQuantityError(input, message) {
        // Remove existing error message
        this.clearQuantityError(input);

        // Add error class to input
        input.classList.add('is-invalid');

        // Create error message element
        const errorDiv = document.createElement('div');
        errorDiv.className = 'invalid-feedback max-quantity-error';
        errorDiv.textContent = message;
        errorDiv.style.display = 'block';
        errorDiv.style.color = '#dc3545';
        errorDiv.style.fontSize = '0.875rem';
        errorDiv.style.marginTop = '0.25rem';

        // Insert error message after input
        input.parentNode.appendChild(errorDiv);

        // Disable add to cart button
        const addToCartBtn = input.closest('.product-form').querySelector('.add-to-cart-btn');
        if (addToCartBtn) {
            addToCartBtn.disabled = true;
            addToCartBtn.classList.add('btn-secondary');
            addToCartBtn.classList.remove('btn-primary');
        }
    }

    clearQuantityError(input) {
        // Remove error class from input
        input.classList.remove('is-invalid');

        // Remove existing error message
        const existingError = input.parentNode.querySelector('.max-quantity-error');
        if (existingError) {
            existingError.remove();
        }

        // Enable add to cart button
        const addToCartBtn = input.closest('.product-form').querySelector('.add-to-cart-btn');
        if (addToCartBtn) {
            addToCartBtn.disabled = false;
            addToCartBtn.classList.remove('btn-secondary');
            addToCartBtn.classList.add('btn-primary');
        }
    }

    showLoading(button) {
        const originalText = button.innerHTML;
        button.dataset.originalText = originalText;
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Adding...';
        button.disabled = true;
    }

    hideLoading(button) {
        const originalText = button.dataset.originalText;
        if (originalText) {
            button.innerHTML = originalText;
        }
        button.disabled = false;
    }

    showSuccess(message) {
        this.showAlert('✓ ' + message, 'success');
    }

    showError(message) {
        this.showAlert(message, 'danger');
    }

    showAlert(message, type = 'info') {
        // Create alert element
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
        alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px; max-width: 400px; font-weight: 500;';
        
        // Add icon based on type
        let icon = '';
        if (type === 'success') {
            icon = '<i class="fas fa-check-circle me-2"></i>';
        } else if (type === 'danger') {
            icon = '<i class="fas fa-exclamation-circle me-2"></i>';
        } else {
            icon = '<i class="fas fa-info-circle me-2"></i>';
        }
        
        alertDiv.innerHTML = `
            ${icon}${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;

        // Add to page
        document.body.appendChild(alertDiv);

        // Auto remove after 5 seconds
        setTimeout(() => {
            if (alertDiv.parentNode) {
                alertDiv.remove();
            }
        }, 5000);
    }

    updateCartCount() {
        // Update cart count in header if it exists
        const cartCountElement = document.querySelector('.cart-count');
        if (cartCountElement) {
            // You can implement AJAX call to get updated cart count
            // For now, just increment the current count
            const currentCount = parseInt(cartCountElement.textContent) || 0;
            cartCountElement.textContent = currentCount + 1;
        }
    }

    highlightProductCard(button) {
        // Find the product card container
        const productCard = button.closest('.card, .product-detail-card, [data-id^="prod-"]');
        
        if (productCard) {
            // Add highlight class
            productCard.classList.add('product-added-highlight');
            
            // Animate the button
            button.classList.add('btn-success');
            button.innerHTML = '<i class="fas fa-check"></i> ADDED';
            
            // Reset button after 2 seconds
            setTimeout(() => {
                button.classList.remove('btn-success');
                button.innerHTML = '<i class="fas fa-shopping-cart" style="margin-right: 6px; transform: scaleX(-1); font-size: 18px;"></i>ADD TO CART';
            }, 2000);
            
            // Remove highlight after 3 seconds
            setTimeout(() => {
                productCard.classList.remove('product-added-highlight');
            }, 3000);
        }
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    new MaxQuantityChecker();
}); 