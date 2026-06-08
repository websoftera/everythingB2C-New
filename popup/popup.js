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

    function numericPrice(value) {
        return Number(String(value || '').replace(/[^\d.]/g, '')) || 0;
    }

    function money(value) {
        return '₹ ' + Number(value || 0).toLocaleString('en-IN', {
            maximumFractionDigits: 0
        });
    }

    function getProductPricingRoot(input) {
        return input.closest('.product-card, .card.product-card, .product-detail-card, .shop-page-product-card, .related-products-container .product-card');
    }

    window.updateDisplayedPriceForQuantity = function (input, unitOverride = null) {
        if (!input || input.classList.contains('cart-qty-input')) {
            return;
        }

        const root = getProductPricingRoot(input);
        if (!root) {
            return;
        }

        const mrpValue = root.querySelector('.price-btn.mrp .value');
        const payValue = root.querySelector('.price-btn.pay .value');
        if (!mrpValue || !payValue) {
            return;
        }

        if (unitOverride) {
            root.dataset.unitMrp = String(unitOverride.mrp || 0);
            root.dataset.unitPay = String(unitOverride.selling_price || 0);
        }

        if (!root.dataset.unitMrp) {
            root.dataset.unitMrp = String(numericPrice(mrpValue.textContent));
        }
        if (!root.dataset.unitPay) {
            root.dataset.unitPay = String(numericPrice(payValue.textContent));
        }

        const quantity = Math.max(1, parseInt(input.value, 10) || 1);
        const unitMrp = Number(root.dataset.unitMrp || 0);
        const unitPay = Number(root.dataset.unitPay || 0);
        const totalMrp = unitMrp * quantity;
        const totalPay = unitPay * quantity;
        const totalSave = Math.max(0, totalMrp - totalPay);
        const discountPercent = totalMrp > 0 ? Math.round((totalSave / totalMrp) * 100) : 0;

        mrpValue.textContent = money(totalMrp);
        payValue.textContent = money(totalPay);

        const saveBanner = root.querySelector('.discount-banner, .discount-banner-detail');
        if (saveBanner && saveBanner.style.visibility !== 'hidden') {
            saveBanner.textContent = totalSave > 0
                ? `SAVE ₹${totalSave.toLocaleString('en-IN', { maximumFractionDigits: 0 })} (${discountPercent}% OFF)`
                : 'SAVE ₹0 (0% OFF)';
        }
    };

    const variantStyle = document.createElement('style');
    variantStyle.textContent = `
        .variant-drawer-overlay {
            position: fixed;
            inset: 0;
            z-index: 12000;
            display: none;
            background: rgba(17, 24, 39, 0.62);
        }
        .variant-drawer-overlay.show {
            display: block;
        }
        body.variant-drawer-open #floatingCartBtn,
        body.variant-drawer-open #goToTopBtn,
        body.variant-drawer-open #backToTopBtn {
            display: none !important;
        }
        .variant-drawer {
            position: absolute;
            top: 0;
            right: 0;
            width: min(345px, 100%);
            height: 100%;
            background: #fff;
            display: flex;
            flex-direction: column;
            box-shadow: -18px 0 40px rgba(15, 23, 42, 0.18);
            font-family: 'Mulish', sans-serif !important;
            border-top: 4px solid var(--site-blue, #0c79e7);
        }
        .variant-drawer-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 14px 16px 10px;
            border-bottom: 1px solid #edf0f5;
        }
        .variant-drawer-title {
            margin: 0;
            color: #243041;
            font-size: 16px;
            font-weight: 800;
        }
        .variant-drawer-close {
            width: 34px;
            height: 34px;
            border: 0;
            border-radius: 50%;
            background: #f2f6fb;
            color: #5d6978;
            font-size: 22px;
            line-height: 1;
        }
        .variant-drawer-body {
            flex: 1;
            overflow-y: auto;
            padding: 14px 16px 18px;
        }
        .variant-product-summary {
            display: grid;
            grid-template-columns: 68px 1fr;
            gap: 10px;
            align-items: center;
            padding-bottom: 14px;
            border-bottom: 1px solid #edf0f5;
        }
        .variant-product-summary img {
            width: 68px;
            height: 68px;
            object-fit: contain;
            border: 1px solid #e4e9f1;
            border-radius: 10px;
            background: #fff;
        }
        .variant-product-name {
            margin: 0 0 6px;
            color: #243041;
            font-size: 13px;
            font-weight: 800;
            line-height: 1.35;
        }
        .variant-stock {
            color: #6b7280;
            font-size: 12px;
            font-weight: 700;
            margin-bottom: 8px;
        }
        .variant-price-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px;
        }
        .variant-price-pill {
            border: 0 !important;
            border-radius: 4px;
            padding: 8px 6px;
            text-align: center;
            color: #111827;
            font-size: 12px;
            font-weight: 800;
            line-height: 1.15;
        }
        .variant-price-pill.mrp {
            background: var(--mrp-light-blue, #cde3ef) !important;
            color: var(--site-blue, #0c79e7) !important;
            text-decoration: line-through;
        }
        .variant-price-pill.pay {
            background: var(--pay-light-green, #E3F2AA) !important;
            color: #000 !important;
        }
        .variant-group {
            margin-top: 16px;
        }
        .variant-group-title {
            color: #243041;
            font-size: 13px;
            font-weight: 800;
            margin-bottom: 10px;
        }
        .variant-options {
            display: flex;
            flex-wrap: wrap;
            gap: 9px;
        }
        .variant-option {
            min-width: 54px;
            min-height: 34px;
            border: 1px solid #d8e0ea;
            border-radius: 8px;
            background: #fff;
            color: #243041;
            font-size: 13px;
            font-weight: 800;
            padding: 6px 12px;
        }
        .variant-option.active {
            border-color: var(--site-blue, #0c79e7);
            background: var(--site-blue, #0c79e7);
            color: #fff;
        }
        .variant-option:disabled {
            opacity: 0.45;
            cursor: not-allowed;
        }
        .variant-drawer-footer {
            display: flex;
            justify-content: flex-end;
            padding: 10px 16px 14px;
            border-top: 1px solid #edf0f5;
            background: #fff;
        }
        .variant-continue-btn {
            width: auto;
            min-width: 148px;
            min-height: 40px;
            border: 0;
            border-radius: 999px;
            background: var(--site-blue, #0c79e7);
            color: #fff;
            font-size: 12px;
            font-weight: 900;
            letter-spacing: 0.02em;
            box-shadow: 0 10px 20px rgba(12, 121, 231, 0.18);
        }
        .variant-continue-btn:hover:not(:disabled) {
            background: #0a67c6;
        }
        .variant-continue-btn:disabled {
            background: #94a3b8;
            box-shadow: none;
        }
        @media (max-width: 575px) {
            .variant-drawer {
                width: 100%;
            }
            .variant-drawer-header {
                padding: 18px 16px 12px;
            }
            .variant-drawer-body {
                padding: 14px 16px 20px;
            }
            .variant-product-summary {
                grid-template-columns: 72px 1fr;
            }
            .variant-product-summary img {
                width: 72px;
                height: 72px;
            }
            .variant-drawer-title {
                font-size: 18px;
            }
        }
    `;
    document.head.appendChild(variantStyle);

    function formatVariantPrice(value) {
        return '₹ ' + Number(value || 0).toLocaleString('en-IN', {
            maximumFractionDigits: 0
        });
    }

    function normalizeImagePath(path) {
        if (!path) return 'uploads/products/blank-img.webp';
        return path;
    }

    function performAddToCart(productId, quantity, target, cardRoot, variationId = null) {
        const originalLabel = target ? target.innerHTML : '';
        if (target) {
            target.textContent = 'Adding...';
            target.disabled = true;
        }

        const query = variationId ? `&variation_id=${encodeURIComponent(variationId)}` : '';
        fetch(`ajax/check-product-in-cart.php?product_id=${productId}${query}`)
            .then(res => res.json())
            .then(data => {
                const payload = { product_id: productId, quantity: quantity };
                if (variationId) payload.variation_id = variationId;

                if (data.success && data.in_cart) {
                    return fetch('ajax/update-cart-quantity.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(payload)
                    });
                }

                return fetch('ajax/add-to-cart.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
                });
            })
            .then(response => response.json())
            .then(data => {
                const statusType = data.success ? 'success' : 'error';
                showToast(data.message, statusType);

                if (target) {
                    if (data.success) {
                        target.innerHTML = '<i class="fas fa-shopping-cart"></i> ADDED TO CART';
                        target.classList.add('cart-added-highlight');
                        target.disabled = false;
                    } else {
                        target.innerHTML = originalLabel;
                        target.disabled = false;
                        target.classList.remove('cart-added-highlight');
                    }
                }

                if (data.success) {
                    updateCartCount();
                    reinitQuantityControlsWithDebug();
                    window.initializeQuantityInputs();
                    window.dispatchEvent(new CustomEvent('cart-updated', {
                        detail: { action: 'updated' }
                    }));
                }

                if (cardRoot) {
                    cardRoot.querySelectorAll('.quantity-input, .shop-page-quantity-input').forEach(el => el.disabled = false);
                    cardRoot.querySelectorAll('.btn-qty-minus, .btn-qty-plus').forEach(el => el.disabled = false);
                }
            })
            .catch(error => {
                console.error('Error adding/updating cart:', error);
                if (target) {
                    target.innerHTML = originalLabel;
                    target.disabled = false;
                    target.classList.remove('cart-added-highlight');
                }

                if (cardRoot) {
                    cardRoot.querySelectorAll('.quantity-input, .shop-page-quantity-input').forEach(el => el.disabled = false);
                    cardRoot.querySelectorAll('.btn-qty-minus, .btn-qty-plus').forEach(el => el.disabled = false);
                }
            });
    }

    function openVariantDrawer(data, productId, quantity, sourceButton, cardRoot) {
        let selected = data.variations.find(variation => variation.stock_quantity > 0) || data.variations[0];
        const selectedValues = {};
        (selected.attributes || []).forEach(item => {
            selectedValues[item.attribute_id] = item.value_id;
        });

        function hidePageFloatingControls() {
            ['goToTopBtn', 'backToTopBtn'].forEach(id => {
                const button = document.getElementById(id);
                if (!button) return;
                button.style.setProperty('display', 'none', 'important');
                button.style.setProperty('opacity', '0', 'important');
                button.style.setProperty('visibility', 'hidden', 'important');
            });
        }

        const overlay = document.createElement('div');
        overlay.className = 'variant-drawer-overlay show';
        overlay.innerHTML = `
            <aside class="variant-drawer" role="dialog" aria-modal="true">
                <div class="variant-drawer-header">
                    <h3 class="variant-drawer-title">Select Variant</h3>
                    <button type="button" class="variant-drawer-close" aria-label="Close">&times;</button>
                </div>
                <div class="variant-drawer-body">
                    <div class="variant-product-summary">
                        <img class="variant-product-image" src="${normalizeImagePath(selected.image_path || data.product.image)}" alt="">
                        <div>
                            <h4 class="variant-product-name">${data.product.name}</h4>
                            <div class="variant-stock">Available stock: <span>${selected.stock_quantity}</span></div>
                            <div class="variant-price-row">
                                <div class="variant-price-pill mrp">MRP ${formatVariantPrice(selected.mrp)}</div>
                                <div class="variant-price-pill pay">PAY ${formatVariantPrice(selected.selling_price)}</div>
                            </div>
                        </div>
                    </div>
                    <div class="variant-groups"></div>
                </div>
                <div class="variant-drawer-footer">
                    <button type="button" class="variant-continue-btn">CONTINUE <i class="fas fa-arrow-right"></i></button>
                </div>
            </aside>
        `;
        document.body.appendChild(overlay);
        document.body.style.overflow = 'hidden';
        document.body.classList.add('variant-drawer-open');
        hidePageFloatingControls();

        const groupsWrap = overlay.querySelector('.variant-groups');
        const imageEl = overlay.querySelector('.variant-product-image');
        const stockEl = overlay.querySelector('.variant-stock span');
        const mrpEl = overlay.querySelector('.variant-price-pill.mrp');
        const payEl = overlay.querySelector('.variant-price-pill.pay');
        const continueBtn = overlay.querySelector('.variant-continue-btn');

        function closeDrawer() {
            overlay.remove();
            document.body.style.removeProperty('overflow');
            document.body.classList.remove('variant-drawer-open');
            window.dispatchEvent(new Event('scroll'));
        }

        function findMatchingVariation() {
            return data.variations
                .filter(variation => {
                    const attributes = variation.attributes || [];
                    return attributes.every(item => String(selectedValues[item.attribute_id]) === String(item.value_id));
                })
                .sort((a, b) => (b.attributes || []).length - (a.attributes || []).length)[0] || null;
        }

        function refreshSelected() {
            selected = findMatchingVariation();
            if (!selected) {
                stockEl.textContent = '0';
                continueBtn.disabled = true;
                continueBtn.textContent = 'UNAVAILABLE';
                return;
            }

            imageEl.src = normalizeImagePath(selected.image_path || data.product.image);
            stockEl.textContent = selected.stock_quantity;
            mrpEl.textContent = 'MRP ' + formatVariantPrice(selected.mrp);
            payEl.textContent = 'PAY ' + formatVariantPrice(selected.selling_price);
            continueBtn.disabled = selected.stock_quantity <= 0;
            continueBtn.textContent = selected.stock_quantity > 0 ? 'CONTINUE' : 'OUT OF STOCK';
            if (selected.stock_quantity > 0) {
                continueBtn.innerHTML = 'CONTINUE <i class="fas fa-arrow-right"></i>';
            }
        }

        data.attributes.forEach(attribute => {
            const group = document.createElement('div');
            group.className = 'variant-group';
            group.innerHTML = `<div class="variant-group-title">Select ${attribute.name}</div><div class="variant-options"></div>`;
            const optionsWrap = group.querySelector('.variant-options');

            attribute.values.forEach(value => {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'variant-option';
                btn.textContent = value.value;
                btn.dataset.attributeId = attribute.id;
                btn.dataset.valueId = value.id;
                if (selectedValues[attribute.id] == value.id) {
                    btn.classList.add('active');
                }
                btn.addEventListener('click', function () {
                    selectedValues[attribute.id] = value.id;
                    optionsWrap.querySelectorAll('.variant-option').forEach(option => option.classList.remove('active'));
                    btn.classList.add('active');
                    refreshSelected();
                });
                optionsWrap.appendChild(btn);
            });

            groupsWrap.appendChild(group);
        });

        overlay.querySelector('.variant-drawer-close').addEventListener('click', closeDrawer);
        overlay.addEventListener('click', function (event) {
            if (event.target === overlay) closeDrawer();
        });
        continueBtn.addEventListener('click', function () {
            if (!selected || selected.stock_quantity <= 0) return;
            closeDrawer();
            performAddToCart(productId, quantity, sourceButton, cardRoot, selected.id);
        });

        refreshSelected();
    }

    // Update wishlist count and header icon state
    function updateWishlistCount() {
        const timeStamp = new Date().getTime();
        fetch(`ajax/get_wishlist_count.php?t=${timeStamp}`)
            .then(response => response.json())
            .then(data => {
                const count = data.wishlist_count || 0;

                // Update optional badge if it exists elsewhere
                const wishlistCountElement = document.querySelector('.wishlist-count, .wishlist-badge');
                if (wishlistCountElement) {
                    if (count > 0) {
                        wishlistCountElement.textContent = count;
                        wishlistCountElement.style.display = 'block';
                    } else {
                        wishlistCountElement.style.display = 'none';
                    }
                }

                // Update the main header wishlist icon
                const wishlistIconElement = document.querySelector('.wishlist-icon');
                if (wishlistIconElement) {
                    if (count > 0) {
                        wishlistIconElement.classList.add('bi-heart-fill', 'active-wishlist');
                        wishlistIconElement.classList.remove('bi-heart');
                        wishlistIconElement.style.setProperty('color', '#DE0085', 'important');
                    } else {
                        wishlistIconElement.classList.remove('bi-heart-fill', 'active-wishlist');
                        wishlistIconElement.classList.add('bi-heart');
                        wishlistIconElement.style.removeProperty('color');
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
                input.removeEventListener('input', input._debugInputHandler || (() => { }));
                const handler = function () {
                    let val = parseInt(this.value, 10) || 1;
                    if (val < 1) val = 1;
                    this.value = val;
                    window.updateDisplayedPriceForQuantity(this);

                };
                input.addEventListener('input', handler);
                input._debugInputHandler = handler;
            });
            card.querySelectorAll('.btn-qty-minus, .btn-qty-plus').forEach(btn => {
                btn.disabled = false;
                btn.removeEventListener('click', btn._debugClickHandler || (() => { }));
                const handler = function (e) {
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
            console.log('loadCurrentCartQuantity: Missing productId or quantityInput');
            return;
        }

        console.log('Loading cart quantity for product:', productId);
        fetch(`ajax/check-product-in-cart.php?product_id=${productId}`)
            .then(res => res.json())
            .then(data => {
                console.log('Cart check response for product', productId, ':', data);
                if (data.success && data.in_cart && data.quantity > 0) {
                    quantityInput.value = data.quantity;
                    window.updateDisplayedPriceForQuantity(quantityInput);
                    console.log('Updated input value to:', data.quantity);
                }
            })
            .catch(err => {
                console.error('Error loading cart quantity:', err);
            });
    }

    // Global function to initialize quantity inputs (accessible from anywhere)
    window.initializeQuantityInputs = function (force = false) {


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
                console.log('Found product ID:', productId, 'for input:', input);
                // Only update if forced or if input value is 1 (default)
                if (force || input.value == "1") {
                    loadCurrentCartQuantity(productId, input);
                }
            } else {
                console.log('No product ID found for input:', input);
            }
        });

        // Also update popup quantity input if popup is open
        const popupQtyInput = document.getElementById('popupQtyInput');
        if (popupQtyInput && window.currentPopupProductId) {
            loadCurrentCartQuantity(window.currentPopupProductId, popupQtyInput);
        }
    };

    document.body.addEventListener('click', function (event) {
        let target = event.target;

        // Add to Cart
        const addTarget = target.closest ? target.closest('.add-to-cart-btn, .add-to-cart, .shop-page-add-to-cart-btn') : null;
        if (addTarget) {
            target = addTarget;
            event.preventDefault();
            event.stopPropagation(); // Stop click from triggering card navigation
            const productId = target.dataset.productId;
            let quantity = 1;
            let cardRoot = target.closest('.product-card, .card, .shop-page-product-card, .product-detail-card, .related-products-container .product-card');

            // Find quantity input in the product card
            let quantityInput = null;
            if (cardRoot) {
                quantityInput = cardRoot.querySelector('.quantity-input, .shop-page-quantity-input');
                console.log('Found quantity input:', quantityInput);
                if (quantityInput) {
                    quantity = parseInt(quantityInput.value, 10) || 1;
                    console.log('Quantity from input:', quantity);
                } else {
                    console.log('No quantity input found in card');
                }
            } else {
                console.log('No card root found');
            }

            console.log('Add to cart - Product ID:', productId, 'Quantity:', quantity);

            if (target.dataset.variationId) {
                performAddToCart(productId, quantity, target, cardRoot, target.dataset.variationId);
                return;
            }

            fetch(`ajax/get-product-variants.php?product_id=${productId}`)
                .then(res => res.json())
                .then(data => {
                    if (data.success && data.has_variations && data.variations.length) {
                        openVariantDrawer(data, productId, quantity, target, cardRoot);
                        return;
                    }

                    performAddToCart(productId, quantity, target, cardRoot);
                })
                .catch(error => {
                    console.error('Error loading variants:', error);
                    performAddToCart(productId, quantity, target, cardRoot);
                });
        }

        // Add/Remove from Wishlist (Toggle functionality)
        if (event.target.matches('.heart-checkbox, .shop-page-heart-checkbox')) {
            event.stopPropagation(); // Stop click from triggering card navigation
            const checkbox = event.target;
            const productId = checkbox.dataset.productId;
            const label = checkbox.nextElementSibling;

            if (!productId) return;

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

                    if (data.success) {
                        // Update checkbox and label based on action
                        if (data.action === 'added') {
                            checkbox.checked = true;
                            if (label) {
                                label.classList.add('wishlist-active');
                                const icon = label.querySelector('i');
                                if (icon) {
                                    icon.classList.add('bi-heart-fill');
                                    icon.classList.remove('bi-heart');
                                }
                            }
                        } else if (data.action === 'removed') {
                            checkbox.checked = false;
                            if (label) {
                                label.classList.remove('wishlist-active');
                                const icon = label.querySelector('i');
                                if (icon) {
                                    icon.classList.remove('bi-heart-fill');
                                    icon.classList.add('bi-heart');
                                }
                            }
                        }
                    } else {
                        // Revert checkbox state on failure
                        checkbox.checked = !checkbox.checked;
                        if (label) {
                            if (checkbox.checked) {
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
                    if (label) {
                        const icon = label.querySelector('i');
                        if (checkbox.checked) {
                            label.classList.add('wishlist-active');
                            if (icon) {
                                icon.classList.add('bi-heart-fill');
                                icon.classList.remove('bi-heart');
                            }
                        } else {
                            label.classList.remove('wishlist-active');
                            if (icon) {
                                icon.classList.remove('bi-heart-fill');
                                icon.classList.add('bi-heart');
                            }
                        }
                    }
                });
        }

        // Entire Product Card Click Navigation
        const cardRoot = target.closest('.product-card, .card');
        // Only navigate if:
        // 1. We clicked a card
        // 2. Not clicking a link directly (let browser handle it)
        // 3. Not clicking quantity controls
        // 4. Not clicking price buttons or wishlist label
        // 5. Not clicking an input or button
        if (cardRoot && 
            !target.closest('a') && 
            !target.closest('.quantity-control') && 
            !target.closest('.price-btn') && 
            !target.closest('.wishlist') &&
            !target.matches('input, button')) {
            const detailLink = cardRoot.querySelector('a[href^="product.php"]');
            if (detailLink) {
                window.location.href = detailLink.href;
            }
        }
    });

    // Initialize wishlist states on page load
    function initializeWishlistStates() {
        document.querySelectorAll('.heart-checkbox, .shop-page-heart-checkbox').forEach(checkbox => {
            const productId = checkbox.dataset.productId;
            const label = checkbox.nextElementSibling;
            const icon = label? label.querySelector('i') : null;

            if (checkbox.checked) {
                if (label) label.classList.add('wishlist-active');
                if (icon) {
                    icon.classList.add('bi-heart-fill');
                    icon.classList.remove('bi-heart');
                }
            } else {
                if (label) label.classList.remove('wishlist-active');
                if (icon) {
                    icon.classList.remove('bi-heart-fill');
                    icon.classList.add('bi-heart');
                }
            }
        });
    }

    // Initialize wishlist states when DOM is loaded
    initializeWishlistStates();

    // Initial cart count update
    updateCartCount();

    // Function to initialize add-to-cart button states
    function initializeAddToCartButtonStates() {
        document.querySelectorAll('.add-to-cart-btn, .add-to-cart, .shop-page-add-to-cart-btn').forEach(button => {
            const productId = button.dataset.productId;
            if (productId) {
                fetch(`ajax/check-product-in-cart.php?product_id=${productId}`)
                    .then(res => res.json())
                    .then(data => {
                        if (data.success && data.in_cart) {
                            // Product is in cart, update button state
                            button.innerHTML = '<i class="fas fa-shopping-cart"></i> ADDED TO CART';
                            button.classList.add('cart-added-highlight');
                            button.disabled = false;
                        }
                    })
                    .catch(err => {
                        console.error('Error checking cart status for button:', err);
                    });
            }
        });
    }

    // Initialize add-to-cart button states
    initializeAddToCartButtonStates();

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
    initializeAddToCartButtonStates();

    // 2. After a short delay
    setTimeout(() => {
        initializeQuantityInputs();
        initializeAddToCartButtonStates();
    }, 100);

    // 3. When DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            setTimeout(() => {
                initializeQuantityInputs();
                initializeAddToCartButtonStates();
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
    window.refreshQuantities = function () {

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
window.addEventListener('cart-updated', function () {
    setTimeout(() => {
        if (window.initializeQuantityInputs) {
            window.initializeQuantityInputs();
        }
    }, 100);
});


// Global handler for direct quantity input changes
// (for cart items and product cards, update cart via AJAX)
document.body.addEventListener('change', async function (e) {
    const input = e.target;
    const value = parseInt(input.value, 10) || 1;

    // Get product ID from the input or its parent container
    let productId = input.dataset.productId;
    if (!productId) {
        const productCard = input.closest('.product-card, .card, .shop-page-product-card, .product-detail-card, [data-product-id]');
        if (productCard) {
            productId = productCard.dataset.productId || productCard.getAttribute('data-product-id');
        }
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
            window.updateDisplayedPriceForQuantity(input);
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
            return;
        }
    } catch (err) {
        console.error('Error checking max quantity:', err);
    }

    // If this is a cart quantity input, update cart
    if (input.classList.contains('cart-qty-input')) {
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
    }
    // For product card quantity inputs, just update the input value (don't update cart yet)
    else if (input.classList.contains('quantity-input') || input.closest('.quantity-control')) {
        console.log('Direct input change for product:', productId, 'to:', value);
        window.updateDisplayedPriceForQuantity(input);
        // Don't update cart automatically - wait for add-to-cart button click
    }
});

// Event delegation for popup controls
// Handles popup quantity, add-to-cart, and wishlist

document.body.addEventListener('click', function (e) {
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
                btn.innerHTML = '<i class="fas fa-shopping-cart" style="margin-right: 6px; transform: scaleX(-1); font-size: 18px;"></i>ADD TO CART';
                if (data.success) {
                    btn.innerHTML = 'UPDATED!';
                    setTimeout(function () { btn.innerHTML = '<i class="fas fa-shopping-cart" style="margin-right: 6px; transform: scaleX(-1); font-size: 18px;"></i>ADD TO CART'; }, 1200);
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
                            icon.classList.add('bi-heart-fill');
                            icon.classList.remove('bi-heart');
                            icon.style.color = '#DE0085'; // Pink color when added
                        } else if (data.action === 'removed') {
                            icon.classList.remove('bi-heart-fill');
                            icon.classList.add('bi-heart');
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



    buttons.forEach(btn => {
        const productId = btn.dataset.productId;
        if (!productId) return;



        fetch(`ajax/check-product-in-cart.php?product_id=${productId}`)
            .then(res => res.json())
            .then(data => {
                if (data.success && data.in_cart) {
                    // Product is in cart, highlight the button but keep it enabled
                    btn.innerHTML = '<i class="fas fa-shopping-cart"></i> ADDED TO CART';
                    btn.disabled = false; // Keep enabled so user can add more
                    btn.classList.add('cart-added-highlight');

                } else {
                    // Product not in cart, ensure button is normal
                    btn.innerHTML = '<i class="fas fa-shopping-cart"></i> ADD TO CART';
                    btn.disabled = false;
                    btn.classList.remove('cart-added-highlight');

                }
            })
            .catch(error => {
                console.error('Error checking cart status for button initialization:', error);
            });
    });
}

// Initialize button states when page loads
document.addEventListener('DOMContentLoaded', function () {
    setTimeout(() => {
        initializeCartButtonStates();
    }, 100);
});

// Also initialize when cart is updated
document.addEventListener('cart-updated', function () {
    setTimeout(() => {
        initializeCartButtonStates();
    }, 100);
});

// Initialize when window loads (for dynamic content)
window.addEventListener('load', function () {
    setTimeout(() => {
        initializeCartButtonStates();
    }, 200);
});

// Function to update a specific button state
function updateButtonState(productId, inCart) {


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
            btn.innerHTML = '<i class="fas fa-shopping-cart"></i> ADDED TO CART';
            btn.disabled = false; // Keep enabled so user can add more
            btn.classList.add('cart-added-highlight');
        } else {
            btn.innerHTML = '<i class="fas fa-shopping-cart"></i> ADD TO CART';
            btn.disabled = false;
            btn.classList.remove('cart-added-highlight');
        }
    }
}

// Handle cart item removal to update button states
window.addEventListener('cart-item-removed', function (event) {
    const productId = event.detail?.productId;
    if (productId) {
        // Update the specific button state
        updateButtonState(productId, false);

        // Also check if the button was actually updated by re-checking cart status
        setTimeout(() => {
            fetch(`ajax/check-product-in-cart.php?product_id=${productId}`)
                .then(res => res.json())
                .then(data => {
                    if (!data.success || !data.in_cart) {
                        updateButtonState(productId, false);
                    }
                })
                .catch(error => {
                    console.error('Error checking cart status:', error);
                });
        }, 1000);
    } else {
        // If no specific product ID, reinitialize all buttons
        initializeCartButtonStates();
    }
});

// Handle cart remove all to update all button states
window.addEventListener('cart-removed-all', function (event) {
    // Reinitialize all buttons to remove highlighting
    initializeCartButtonStates();
});

// Force pink color on page load for wishlist items
document.addEventListener('DOMContentLoaded', function () {
    // Find all wishlist labels with wishlist-active class
    const activeWishlistLabels = document.querySelectorAll('.wishlist-label.wishlist-active');

    activeWishlistLabels.forEach(function (label) {
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
    checkedCheckboxes.forEach(function (checkbox) {
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
