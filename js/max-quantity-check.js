// Max Quantity Check JavaScript
// Handles quantity validation and sends variation selections with cart requests.

class MaxQuantityChecker {
    constructor() {
        this.init();
    }

    init() {
        this.addEventListeners();
    }

    addEventListeners() {
        document.addEventListener('click', (e) => {
            if (e.target.matches('.add-to-cart-btn') || e.target.closest('.add-to-cart-btn')) {
                e.preventDefault();
                const button = e.target.matches('.add-to-cart-btn') ? e.target : e.target.closest('.add-to-cart-btn');
                this.handleAddToCart(button);
            }
        });

        document.addEventListener('change', (e) => {
            if (e.target.matches('.quantity-input')) {
                if (parseInt(e.target.value, 10) > 99) e.target.value = 99;
                this.checkMaxQuantity(e.target);
            }
        });

        document.addEventListener('input', (e) => {
            if (e.target.matches('.quantity-input')) {
                if (e.target.value.length > 2) e.target.value = e.target.value.slice(0, 2);
                if (parseInt(e.target.value, 10) > 99) e.target.value = 99;
                this.validateQuantityInput(e.target);
            }
        });

        document.addEventListener('blur', (e) => {
            if (e.target.matches('.quantity-input') && parseInt(e.target.value, 10) > 99) {
                e.target.value = 99;
            }
        }, true);
    }

    async handleAddToCart(button) {
        const productId = button.dataset.productId;
        const variationId = button.dataset.variationId || button.getAttribute('data-variation-id') || '';
        const container = button.closest('.product-form, .product-detail-card, .product-card, .card, .shop-page-product-card');
        const quantityInput = container ? container.querySelector('.quantity-input') : null;
        const quantity = quantityInput ? parseInt(quantityInput.value, 10) || 1 : 1;

        if ((button.dataset.requiresVariation === '1' || button.getAttribute('data-requires-variation') === '1') && !variationId) {
            this.showError('Please select available product options before adding to cart.');
            return;
        }

        this.showLoading(button);

        try {
            const checkResult = await this.checkMaxQuantityBeforeAdd(productId, quantity, variationId);

            if (checkResult.success) {
                const result = await this.addToCart(productId, quantity, variationId, this.getSelectedAttributes(button));
                if (result.success) {
                    this.showSuccess('Product added to cart successfully!');
                    this.updateCartCount();
                    this.highlightProductCard(button);
                } else {
                    this.showError(result.message);
                }
            } else {
                this.showError(checkResult.message || checkResult.error || 'Unable to validate quantity.');
            }
        } catch (error) {
            console.error('Error:', error);
            this.showError('An error occurred. Please try again.');
        } finally {
            this.hideLoading(button);
        }
    }

    async checkMaxQuantityBeforeAdd(productId, quantity, variationId = '') {
        try {
            const response = await fetch(this.ajaxUrl('ajax/check_max_quantity.php'), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'Accept': 'application/json'
                },
                body: `product_id=${encodeURIComponent(productId)}&quantity=${encodeURIComponent(quantity)}&variation_id=${encodeURIComponent(variationId || '')}`
            });

            return await this.parseJsonResponse(response, 'Failed to check quantity limits');
        } catch (error) {
            console.error('Error checking max quantity:', error);
            return { success: false, message: 'Failed to check quantity limits' };
        }
    }

    async addToCart(productId, quantity, variationId = '', selectedAttributes = {}) {
        try {
            const response = await fetch(this.ajaxUrl('ajax/add-to-cart.php'), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    product_id: productId,
                    quantity: quantity,
                    variation_id: variationId || undefined,
                    selected_attributes: selectedAttributes || {}
                })
            });

            return await this.parseJsonResponse(response, 'Failed to add to cart');
        } catch (error) {
            console.error('Error adding to cart:', error);
            return { success: false, message: 'Failed to add to cart' };
        }
    }

    async parseJsonResponse(response, fallbackMessage) {
        const text = await response.text();
        try {
            return JSON.parse(text);
        } catch (error) {
            console.error('Invalid JSON response:', text);
            return {
                success: false,
                message: text && text.trim().startsWith('Connection failed')
                    ? 'Database connection failed. Please try again later.'
                    : fallbackMessage
            };
        }
    }

    getSelectedAttributes(button) {
        const raw = button.dataset.selectedAttributes || button.getAttribute('data-selected-attributes') || '';
        if (!raw) return {};

        try {
            return JSON.parse(raw);
        } catch (error) {
            return {};
        }
    }

    async checkMaxQuantity(input) {
        const productId = input.dataset.productId;
        const variationId = this.getVariationIdFromInput(input);
        const quantity = parseInt(input.value, 10) || 1;

        if (quantity < 1) {
            this.showQuantityError(input, 'Quantity must be at least 1');
            return false;
        }

        if (this.requiresVariation(input) && !variationId) {
            this.clearQuantityError(input);
            return true;
        }

        try {
            const result = await this.checkMaxQuantityBeforeAdd(productId, quantity, variationId);

            if (result.error || result.success === false) {
                this.showQuantityError(input, result.message || result.error);
                return false;
            }

            this.clearQuantityError(input);
            return true;
        } catch (error) {
            console.error('Error checking quantity:', error);
            return false;
        }
    }

    getVariationIdFromInput(input) {
        const inputVariationId = input.dataset.variationId || input.getAttribute('data-variation-id') || '';
        if (inputVariationId) return inputVariationId;

        const container = input.closest('.product-form, .product-detail-card, .product-card, .card, .shop-page-product-card');
        if (!container) return '';

        const addToCartBtn = container.querySelector('.add-to-cart-btn, .add-to-cart, .shop-page-add-to-cart-btn');
        return addToCartBtn
            ? (addToCartBtn.dataset.variationId || addToCartBtn.getAttribute('data-variation-id') || '')
            : '';
    }

    requiresVariation(input) {
        if (input.dataset.requiresVariation === '1' || input.getAttribute('data-requires-variation') === '1') {
            return true;
        }

        const container = input.closest('.product-form, .product-detail-card, .product-card, .card, .shop-page-product-card');
        if (!container) return false;

        const addToCartBtn = container.querySelector('.add-to-cart-btn, .add-to-cart, .shop-page-add-to-cart-btn');
        return !!addToCartBtn && (
            addToCartBtn.dataset.requiresVariation === '1'
            || addToCartBtn.getAttribute('data-requires-variation') === '1'
        );
    }

    validateQuantityInput(input) {
        const value = parseInt(input.value, 10);
        const min = parseInt(input.min, 10) || 1;
        const max = parseInt(input.max, 10);

        if (value < min) {
            input.value = min;
        } else if (max && value > max) {
            input.value = max;
        }
    }

    showQuantityError(input, message) {
        this.clearQuantityError(input);
        input.classList.add('is-invalid');

        const errorDiv = document.createElement('div');
        errorDiv.className = 'invalid-feedback max-quantity-error';
        errorDiv.textContent = message || 'Invalid quantity';
        errorDiv.style.display = 'block';
        errorDiv.style.color = '#dc3545';
        errorDiv.style.fontSize = '0.875rem';
        errorDiv.style.marginTop = '0.25rem';
        input.parentNode.appendChild(errorDiv);

        const container = input.closest('.product-form, .product-detail-card, .product-card, .card, .shop-page-product-card');
        const addToCartBtn = container ? container.querySelector('.add-to-cart-btn') : null;
        if (addToCartBtn) {
            addToCartBtn.disabled = true;
            addToCartBtn.classList.add('btn-secondary');
            addToCartBtn.classList.remove('btn-primary');
        }
    }

    clearQuantityError(input) {
        input.classList.remove('is-invalid');

        const existingError = input.parentNode.querySelector('.max-quantity-error');
        if (existingError) {
            existingError.remove();
        }

        const container = input.closest('.product-form, .product-detail-card, .product-card, .card, .shop-page-product-card');
        const addToCartBtn = container ? container.querySelector('.add-to-cart-btn') : null;
        if (addToCartBtn) {
            if ((addToCartBtn.dataset.requiresVariation === '1' || addToCartBtn.getAttribute('data-requires-variation') === '1') &&
                !(addToCartBtn.dataset.variationId || addToCartBtn.getAttribute('data-variation-id'))) {
                return;
            }
            addToCartBtn.disabled = false;
            addToCartBtn.classList.remove('btn-secondary');
            addToCartBtn.classList.add('btn-primary');
        }
    }

    showLoading(button) {
        button.dataset.originalText = button.innerHTML;
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Adding...';
        button.disabled = true;
    }

    hideLoading(button) {
        if (button.dataset.originalText) {
            button.innerHTML = button.dataset.originalText;
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
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
        alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px; max-width: 400px; font-weight: 500;';

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

        document.body.appendChild(alertDiv);
        setTimeout(() => {
            if (alertDiv.parentNode) {
                alertDiv.remove();
            }
        }, 5000);
    }

    updateCartCount() {
        window.dispatchEvent(new CustomEvent('cart-updated', {
            detail: { action: 'added' }
        }));
    }

    ajaxUrl(path) {
        if (typeof window.b2cAjaxUrl === 'function') {
            return window.b2cAjaxUrl(path);
        }
        return (window.BASE_URL || '') + String(path || '').replace(/^\/+/, '');
    }

    highlightProductCard(button) {
        const productCard = button.closest('.card, .product-detail-card, [data-id^="prod-"]');

        if (productCard) {
            productCard.classList.add('product-added-highlight');
            button.classList.add('btn-success');
            button.innerHTML = '<i class="fas fa-check"></i> ADDED';

            setTimeout(() => {
                button.classList.remove('btn-success');
                button.innerHTML = '<i class="fas fa-shopping-cart" style="margin-right: 6px; transform: scaleX(-1); font-size: 18px;"></i>ADD TO CART';
            }, 2000);

            setTimeout(() => {
                productCard.classList.remove('product-added-highlight');
            }, 3000);
        }
    }
}

document.addEventListener('DOMContentLoaded', function() {
    new MaxQuantityChecker();
});
