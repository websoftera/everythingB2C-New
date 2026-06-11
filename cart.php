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

function cartDisplayName($item) {
    $name = (string)($item['name'] ?? '');
    $variationLabel = trim((string)($item['variation_label'] ?? ''));
    if ($variationLabel !== '') {
        $suffix = ' - ' . $variationLabel;
        if (substr($name, -strlen($suffix)) === $suffix) {
            $name = substr($name, 0, -strlen($suffix));
        }
    }
    return $name;
}

function renderCartVariationDetails($item) {
    $variationLabel = trim((string)($item['variation_label'] ?? ''));
    if ($variationLabel === '') {
        return '';
    }

    $lines = [];
    foreach (explode('/', $variationLabel) as $part) {
        $pieces = explode(':', $part, 2);
        if (count($pieces) < 2) {
            continue;
        }

        $label = trim($pieces[0]);
        $value = trim($pieces[1]);
        if ($label === '' || $value === '') {
            continue;
        }

        if (preg_match('/colou?r/i', $label)) {
            $label = 'colour';
        }

        $lines[] = [
            'label' => $label,
            'value' => $value,
            'is_colour' => preg_match('/colou?r/i', $label) ? 1 : 0
        ];
    }

    usort($lines, function ($a, $b) {
        return $b['is_colour'] <=> $a['is_colour'];
    });

    $html = '';
    foreach ($lines as $line) {
        $html .= '<div class="cart-variation-line">' . htmlspecialchars($line['label']) . ': <span>' . htmlspecialchars($line['value']) . '</span></div>';
    }

    return $html;
}

require_once 'includes/header.php';

// Breadcrumb Navigation
$breadcrumbs = generateBreadcrumb($pageTitle);
echo renderBreadcrumb($breadcrumbs);
?>

<!-- Banner/Breadcrumb (skip homepage) -->
<link rel="stylesheet" href="./asset/style/style.css">
<link rel="stylesheet" href="./asset/style/responsive-cart-checkout.css">
<style>
/* SweetAlert overrides for close button */
.swal2-popup .swal2-close {
    position: absolute !important;
    top: 5px !important;
    right: 5px !important;
    font-size: 24px !important;
    display: flex !important;
    color: #444 !important;
}

/* Fix desktop cart quantity control collapsing from product-card.css overrides */
@media (min-width: 768px) {
    .cart-header-row,
    .cart-item-row {
        font-family: 'Mulish', sans-serif !important;
    }

    .cart-item-row .quantity-control {
        flex: 0 0 92px !important;
        width: 92px !important;
        min-width: 92px !important;
        max-width: 92px !important;
    }

    .cart-product-cell {
        flex: 1 1 150px !important;
        min-width: 120px !important;
        max-width: 260px !important;
    }

    .cart-unit-cell {
        flex: 0 0 92px !important;
        min-width: 82px !important;
        text-align: center !important;
        white-space: nowrap !important;
    }
}

.cart-product-title {
    color: inherit;
    text-decoration: none;
    cursor: pointer;
    font-family: 'Mulish', sans-serif !important;
}

.cart-mobile-unit-line {
    display: none;
    color: #6f7a89;
    font-family: 'Mulish', sans-serif !important;
    font-size: 0.82rem;
    font-weight: 600;
    line-height: 1.2;
    margin-top: 2px;
}

.cart-variation-line {
    color: #667085;
    font-family: 'Mulish', sans-serif !important;
    font-size: 0.78rem;
    font-weight: 700;
    line-height: 1.25;
    margin-top: 1px;
    white-space: normal;
}

.cart-variation-line span {
    font-weight: 600;
}

.cart-item-row .cart-unit-cell,
.cart-item-row .cart-mrp-cell,
.cart-item-row .cart-you-pay-cell,
.cart-item-row .cart-save-cell,
.cart-item-row .cart-total-cell,
.cart-item-row .quantity-control,
.cart-item-row .quantity-control .btn-qty,
.cart-item-row .quantity-control .quantity-input {
    font-family: 'Mulish', sans-serif !important;
    font-size: 0.95rem !important;
    font-weight: 600 !important;
    line-height: 1.2 !important;
}

.cart-item-row .cart-mrp-cell {
    color: #888 !important;
}

.cart-item-row .cart-you-pay-cell {
    color: #007bff !important;
}

.cart-item-row .cart-save-cell {
    color: #23a036 !important;
}

.cart-item-row .cart-total-cell {
    color: #111 !important;
}

.cart-item-row .quantity-control .quantity-input {
    width: 38px !important;
    min-width: 38px !important;
    padding-left: 2px !important;
    padding-right: 2px !important;
}

.cart-item-row .quantity-control .btn-qty {
    width: 27px !important;
    min-width: 27px !important;
    max-width: 27px !important;
}

.cart-item-row .cart-total-cell {
    flex: 0 0 88px !important;
    min-width: 88px !important;
    text-align: right !important;
    padding-right: 8px !important;
    white-space: nowrap !important;
}

@media (max-width: 767.98px) {
    .container.mt-4 {
        padding-left: 16px !important;
        padding-right: 16px !important;
    }

    .container.mt-4 > .row {
        --bs-gutter-x: 0 !important;
        margin-left: 0 !important;
        margin-right: 0 !important;
    }

    .container.mt-4 > .row > [class*="col-"] {
        padding-left: 0 !important;
        padding-right: 0 !important;
    }

    .shopping-card {
        width: 100% !important;
        margin-left: 0 !important;
        margin-right: 0 !important;
    }

    .shopping-card .card-header,
    .shopping-card .card-body {
        padding-left: 0 !important;
        padding-right: 0 !important;
    }

    .text-end.d-md-none {
        width: 100% !important;
        padding-left: 0 !important;
        padding-right: 0 !important;
    }

    .floating-cart-summary-box {
        width: 100% !important;
        margin-left: 0 !important;
        margin-right: 0 !important;
        box-sizing: border-box !important;
    }

    .cart-item-row {
        grid-template-columns: 48px minmax(0, 1fr) minmax(74px, auto) !important;
        grid-template-areas:
            "img title total"
            "img qty delete" !important;
        column-gap: 7px !important;
        row-gap: 3px !important;
        padding: 7px 8px !important;
        width: 100% !important;
        margin-left: 0 !important;
        margin-right: 0 !important;
        box-sizing: border-box !important;
    }

    .cart-item-row > div:nth-child(1) {
        min-width: 0 !important;
        max-width: none !important;
    }

    .cart-item-row > div:nth-child(2),
    .cart-item-row .cart-product-cell {
        min-width: 0 !important;
        max-width: none !important;
    }

    .cart-item-row > div:nth-child(7) {
        min-width: 0 !important;
        justify-self: start !important;
        align-self: start !important;
        width: auto !important;
    }

    .cart-item-row .cart-total-cell,
    .cart-item-row > div:nth-child(8) {
        width: auto !important;
        min-width: 0 !important;
        max-width: none !important;
        padding-right: 0 !important;
        justify-self: end !important;
        text-align: right !important;
    }

    .cart-item-row > div:nth-child(9) {
        width: auto !important;
        min-width: 0 !important;
        justify-self: end !important;
        text-align: right !important;
    }

    .cart-item-row .quantity-control {
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        width: 82px !important;
        min-width: 82px !important;
        max-width: 82px !important;
        height: 28px !important;
        border: 1px solid #b8c0ca !important;
        border-radius: 4px !important;
        overflow: hidden !important;
        background: #fff !important;
    }

    .cart-item-row .quantity-control .btn-qty {
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        flex: 0 0 25px !important;
        width: 25px !important;
        min-width: 25px !important;
        max-width: 25px !important;
        height: 26px !important;
        border: 0 !important;
        border-radius: 0 !important;
        background: #fff !important;
        color: #111 !important;
        font-size: 0.9rem !important;
        padding: 0 !important;
    }

    .cart-item-row .quantity-control .quantity-input {
        display: block !important;
        flex: 0 0 30px !important;
        width: 30px !important;
        min-width: 30px !important;
        max-width: 30px !important;
        height: 26px !important;
        border: 0 !important;
        border-left: 1px solid #b8c0ca !important;
        border-right: 1px solid #b8c0ca !important;
        border-radius: 0 !important;
        background: #fff !important;
        color: #111 !important;
        text-align: center !important;
        padding: 0 !important;
    }
}
</style>
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
            <div class="col-xl-8 col-lg-12">
                <div class="shopping-card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Cart Items (<?php echo count($cartItems); ?>)</h5>
                        <button type="button" class="btn btn-outline-danger btn-sm d-none d-md-block" id="removeAllItems" title="Remove all items from cart">
                            <i class="fas fa-trash me-1"></i>Remove All
                        </button>
                    </div>
                    <div class="card-body">
                        <!-- Header Row for Cart Columns -->
                        <div class="cart-header-row d-flex align-items-center flex-nowrap" style="font-weight:700; color:#000; font-size:0.98rem; background:#f7f7f7; border-radius:6px; padding:7px 0 7px 8px; margin-top:15px; margin-bottom:12px; gap:8px;">
                            <div style="flex:0 0 56px; max-width:56px; min-width:40px;"></div>
                            <div class="cart-product-cell" style="flex:1 1 150px; min-width:120px; max-width:260px;">Product</div>
                            <div class="cart-unit-cell" style="flex:0 0 92px; min-width:82px; text-align:center;">Per Unit</div>
                            <div style="flex:0 0 90px; min-width:60px; text-align:center;">MRP</div>
                            <div style="flex:0 0 90px; min-width:60px; text-align:center;">You Pay</div>
                            <div style="flex:0 0 90px; min-width:60px; text-align:center;">You Save</div>
                            <div style="flex:0 0 80px; min-width:50px; text-align:center;">Qty</div>
                            <div style="flex:0 0 70px; min-width:50px; text-align:center;">Total</div>
                            <div style="flex:0 0 36px; min-width:28px; text-align:center; flex-shrink:0;"></div>
                        </div>
                        <?php foreach ($cartItems as $item): ?>
                            <?php
                            $displayName = cartDisplayName($item);
                            $variationDetails = renderCartVariationDetails($item);
                            $packageQuantity = normalizePackageQuantity($item['package_quantity'] ?? 1);
                            $priceMultiplier = getCartItemPriceMultiplier($item);
                            $cartMaxQuantity = (int)($item['product_stock_quantity'] ?? $item['stock_quantity'] ?? 99);
                            if (isset($item['max_quantity_per_order']) && $item['max_quantity_per_order'] !== null) {
                                $cartMaxQuantity = min($cartMaxQuantity, (int)$item['max_quantity_per_order']);
                            }
                            ?>
                            <div class="cart-item-row d-flex align-items-center flex-nowrap" style="border: 1px solid #e0e0e0; border-radius: 7px; padding: 7px 0 7px 8px; margin-bottom: 10px; background: #fff; gap: 8px;">
                                <div style="flex:0 0 56px; max-width:56px; min-width:40px;">
                                    <a href="product.php?slug=<?php echo urlencode($item['slug']); ?>">
                                        <?php
                                        $imgSrc = !empty($item['main_image']) ? './' . $item['main_image'] : './uploads/products/blank-img.webp';
                                        ?>
                                        <img src="<?php echo $imgSrc; ?>" onerror="this.onerror=null; this.src='./uploads/products/blank-img.webp';" alt="<?php echo htmlspecialchars($displayName); ?>" class="img-fluid" style="width:44px;height:44px;object-fit:cover;border-radius:5px;">
                                    </a>
                                </div>
                                <div class="cart-product-cell" style="flex:1 1 150px; min-width:120px; max-width:260px; font-size:0.97em; overflow:hidden;">
                                    <a class="cart-product-title" href="product.php?slug=<?php echo urlencode($item['slug']); ?>" title="<?php echo htmlspecialchars($displayName); ?>" style="display:block;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                        <?php echo htmlspecialchars($displayName); ?>
                                    </a>
                                    <?php echo $variationDetails; ?>
                                    <span class="cart-mobile-unit-line"><?php echo formatProductUnitLine($item); ?></span>
                                </div>
                                <div class="cart-unit-cell" style="flex:0 0 92px; min-width:82px; font-size:0.93em; color:#444; font-weight:600; text-align:center; white-space:nowrap;"><?php echo formatProductUnitLine($item); ?></div>
                                <div class="cart-mrp-cell" style="flex:0 0 90px; min-width:60px; font-size:0.93em; color:#888; text-align:center;"> <s><?php echo formatPrice($item['mrp'] * $priceMultiplier); ?></s> </div>
                                <div class="cart-you-pay-cell" style="flex:0 0 90px; min-width:60px; font-size:0.97em; color:#007bff; font-weight:500; text-align:center;"> <?php echo formatPrice($item['selling_price'] * $priceMultiplier); ?> </div>
                                <div class="cart-save-cell" style="flex:0 0 90px; min-width:60px; font-size:0.93em; color:#23a036; text-align:center;"> <?php echo formatPrice(max(0, $item['mrp'] - $item['selling_price']) * $priceMultiplier); ?> </div>
                                <div style="flex:0 0 80px; min-width:50px; text-align:center;">
                                    <div class="quantity-control d-inline-flex align-items-center justify-content-center">
                                        <button type="button" class="btn-qty btn-qty-minus" aria-label="Decrease quantity">-</button>
                                        <input type="number" class="quantity-input" value="<?php echo $item['quantity']; ?>" min="<?php echo $packageQuantity; ?>" step="<?php echo $packageQuantity; ?>" max="<?php echo $cartMaxQuantity; ?>" data-cart-id="<?php echo $item['id']; ?>" data-package-quantity="<?php echo $packageQuantity; ?>">
                                        <button type="button" class="btn-qty btn-qty-plus" aria-label="Increase quantity">+</button>
                                    </div>
                                </div>
                                <div class="cart-total-cell" style="flex:0 0 70px; min-width:50px; text-align:center; font-weight:600; font-size:1.01em;"> <?php echo formatPrice($item['selling_price'] * $priceMultiplier); ?> </div>
                                <div style="flex:0 0 36px; min-width:28px; text-align:center; flex-shrink:0;">
                                    <button class="btn btn-outline-danger btn-sm remove-item" data-cart-id="<?php echo $item['id']; ?>" title="Delete" style="padding: 4px 8px; font-size: 1.1rem;">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        <div id="cartPagination" class="mt-3 mb-md-4 mb-2"></div>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-4 col-lg-12">
                <!-- Mobile Remove All Button placed before Price Summary -->
                <div class="text-end d-md-none mb-2">
                    <button type="button" class="btn btn-outline-danger btn-sm" onclick="document.getElementById('removeAllItems').click()" title="Remove all items from cart">
                        <i class="fas fa-trash me-1"></i>Remove All
                    </button>
                </div>
                <div class="shopping-card">
                    <div class="card-header">
                        <h5 class="fw-bold">Price Summary</h5>
                    </div>
                    <div class="card-body">
                        <div class="floating-cart-summary-box" style="border:1px solid #cfd8dc;border-radius:8px;padding:16px 16px 8px 16px;background:#fff;box-shadow:0 2px 8px rgba(0,0,0,0.04);margin-bottom:10px;">
                                      <div class="d-flex justify-content-between mb-2"><span class="text-dark fw-bold">Total MRP</span><span class="cart-summary-total-mrp" style="font-weight:600;text-decoration:line-through;">₹<?php echo number_format($orderTotals['total_mrp'], 0); ?></span></div>
                                      <div class="d-flex justify-content-between mb-2"><span class="text-dark fw-bold">You Pay</span><span class="cart-summary-you-pay" style="font-weight:600;">₹<?php echo number_format($orderTotals['subtotal'], 0); ?></span></div>
                                                  <div class="d-flex justify-content-between mb-2"><span class="text-dark fw-bold">Savings</span><span class="cart-summary-savings fw-bold" style="color:#2e7d32;">₹<?php
                                $total_savings = 0;
                                foreach ($cartItems as $item) {
                                    $total_savings += max(0, $item['mrp'] - $item['selling_price']) * getCartItemPriceMultiplier($item);
                                }
                                echo number_format($total_savings, 0);
                            ?></span></div>
            <div class="d-flex justify-content-between mb-2"><span class="text-dark fw-bold">Delivery Charge <i class='bi bi-info-circle' title='Delivery charges may vary'></i></span><span class="text-danger fw-bold">+ Extra</span></div>
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
            if (typeof normalizeQuantityInputValue === 'function') {
                normalizeQuantityInputValue(this);
            }
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
                                    var priceMultiplier = getCartItemPriceMultiplier(item);
                                    // Find the cart row by cart id
                                    var row = document.querySelector('.cart-item-row input[data-cart-id="' + item.id + '"]').closest('.cart-item-row');
                                    if (row) {
                                        // Update MRP (total)
                                        var mrpCell = row.querySelector('.cart-mrp-cell');
                                        if (mrpCell) {
                                            mrpCell.innerHTML = '<s>' + formatPrice(item.mrp * priceMultiplier) + '</s>';
                                        }
                                        // Update You Pay (total)
                                        var youPayCell = row.querySelector('.cart-you-pay-cell');
                                        if (youPayCell) {
                                            youPayCell.textContent = formatPrice(item.selling_price * priceMultiplier);
                                        }
                                        // Update You Save
                                        var youSaveCell = row.querySelector('.cart-save-cell');
                                        if (youSaveCell) {
                                            youSaveCell.textContent = formatPrice(Math.max(0, item.mrp - item.selling_price) * priceMultiplier);
                                        }
                                        // Update Total
                                        var totalCell = row.querySelector('.cart-total-cell');
                                        if (totalCell) {
                                            totalCell.textContent = formatPrice(item.selling_price * priceMultiplier);
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
                    const limitMessage = data.message || '';
                    if (/maximum quantity|multiple of|available in stock|exceeds available stock/i.test(limitMessage) && typeof showEverythingB2CMaxQuantityPopup === 'function') {
                        showEverythingB2CMaxQuantityPopup(limitMessage);
                    } else if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Error: ' + limitMessage,
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
                showCloseButton: true,
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

function getCartItemPriceMultiplier(item) {
    const quantity = parseFloat(item && item.quantity) || 0;
    const packageQuantity = Math.max(1, parseInt(item && item.package_quantity, 10) || 1);
    return quantity / packageQuantity;
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
                  <div class="d-flex justify-content-between mb-2"><span class="text-dark fw-bold">Total MRP</span><span class="cart-summary-total-mrp" style="font-weight:600;text-decoration:line-through;">₹ ${parseFloat(totals.total_mrp || totals.subtotal).toLocaleString('en-IN', {minimumFractionDigits: 0, maximumFractionDigits: 0})}</span></div>
                  <div class="d-flex justify-content-between mb-2"><span class="text-dark fw-bold">You Pay</span><span class="cart-summary-you-pay" style="font-weight:600;">₹ ${parseFloat(totals.subtotal).toLocaleString('en-IN', {minimumFractionDigits: 0, maximumFractionDigits: 0})}</span></div>
                  <div class="d-flex justify-content-between mb-2"><span class="text-dark fw-bold">Savings</span><span class="cart-summary-savings fw-bold" style="color:#2e7d32;">₹ ${parseFloat(totals.total_savings).toLocaleString('en-IN', {minimumFractionDigits: 0, maximumFractionDigits: 0})}</span></div>
                  <div class="d-flex justify-content-between mb-2"><span class="text-dark fw-bold">Delivery Charge <i class='bi bi-info-circle' title='Delivery charges may vary'></i></span><span class="text-danger fw-bold">+ Extra</span></div>
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
        showCloseButton: true,
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

<!-- Cart Pagination Script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const itemsPerPage = 10;
    let currentPage = 1;
    
    function renderPagination() {
        const items = document.querySelectorAll('.cart-item-row');
        const totalItems = items.length;
        const totalPages = Math.ceil(totalItems / itemsPerPage);
        const paginationContainer = document.getElementById('cartPagination');
        
        if (!paginationContainer) return;
        
        if (totalPages <= 1) {
            paginationContainer.innerHTML = '';
            items.forEach(item => item.style.setProperty('display', '', ''));
            return;
        }
        
        let html = '<nav aria-label="Cart items pagination"><ul class="pagination justify-content-center mb-0">';
        
        // Previous Button
        if (currentPage > 1) {
            html += `<li class="page-item">
                        <a class="page-link" href="#" data-page="${currentPage - 1}">Previous</a>
                     </li>`;
        }
        
        for (let i = 1; i <= totalPages; i++) {
            html += `<li class="page-item ${currentPage === i ? 'active' : ''}">
                        <a class="page-link" href="#" data-page="${i}">${i}</a>
                     </li>`;
        }
        
        // Next Button
        if (currentPage < totalPages) {
            html += `<li class="page-item">
                        <a class="page-link" href="#" data-page="${currentPage + 1}">Next</a>
                     </li>`;
        }
        
        html += '</ul></nav>';
        paginationContainer.innerHTML = html;
        
        paginationContainer.querySelectorAll('.page-link').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const page = parseInt(this.getAttribute('data-page'));
                if (page >= 1 && page <= totalPages) {
                    currentPage = page;
                    updateItemVisibility();
                    renderPagination();
                    
                    // Smooth scroll to top of cart list
                    const cardHeader = document.querySelector('.shopping-card');
                    if (cardHeader) {
                        cardHeader.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }
                }
            });
        });
        
        updateItemVisibility();
    }
    
    function updateItemVisibility() {
        const items = document.querySelectorAll('.cart-item-row');
        const startIndex = (currentPage - 1) * itemsPerPage;
        const endIndex = startIndex + itemsPerPage;
        
        items.forEach((item, index) => {
            if (index >= startIndex && index < endIndex) {
                item.style.setProperty('display', '', ''); // remove inline display
            } else {
                item.style.setProperty('display', 'none', 'important');
            }
        });
    }
    
    // Initial render
    renderPagination();
});
</script> 
