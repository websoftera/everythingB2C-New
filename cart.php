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
                    <div class="card-header">
                        <h5>Cart Items (<?php echo count($cartItems); ?>)</h5>
                    </div>
                    <div class="card-body">
                        <?php foreach ($cartItems as $item): ?>
                            <div class="row mb-3 align-items-center">
                                <div class="col-md-2">
                                    <img src="./<?php echo $item['main_image']; ?>" alt="<?php echo $item['name']; ?>" class="img-fluid">
                                </div>
                                <div class="col-md-4">
                                    <h6><?php echo $item['name']; ?></h6>
                                    <p class="text-muted">Price: <?php echo formatPrice($item['selling_price']); ?></p>
                                </div>
                                <div class="col-md-2">
                                    <div class="quantity-control d-inline-flex align-items-center">
                                        <button type="button" class="btn-qty btn-qty-minus" aria-label="Decrease quantity">-</button>
                                        <input type="number" class="form-control quantity-input" value="<?php echo $item['quantity']; ?>" min="1" data-cart-id="<?php echo $item['id']; ?>">
                                        <button type="button" class="btn-qty btn-qty-plus" aria-label="Increase quantity">+</button>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <strong><?php echo formatPrice($item['selling_price'] * $item['quantity']); ?></strong>
                                </div>
                                <div class="col-md-2">
                                    <button class="btn btn-danger btn-sm remove-item" data-cart-id="<?php echo $item['id']; ?>">Remove</button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="shopping-card">
                    <div class="card-header">
                        <h5>Order Summary</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal:</span>
                            <span id="cart-subtotal"><?php echo formatPrice($orderTotals['subtotal']); ?></span>
                        </div>
                        <!-- Delivery State Dropdown (for guests) -->
                        <?php if (!isLoggedIn()): ?>
                        <div class="d-flex justify-content-between mb-2 align-items-center">
                            <label for="delivery_state" style="margin-bottom:0;">Enter Delivery State for GST Calculation:</label>
                            <form method="post" style="margin-bottom:0;display:inline-block;">
                                <select name="delivery_state" id="delivery_state" class="form-control" style="max-width:180px;display:inline-block;">
                                    <?php foreach (getIndianStates() as $state): ?>
                                        <option value="<?php echo htmlspecialchars($state); ?>" <?php if ($delivery_state == $state) echo 'selected'; ?>><?php echo htmlspecialchars($state); ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <button type="submit" class="btn btn-primary btn-sm">Update</button>
                            </form>
                        </div>
                        <?php endif; ?>
                        <?php
                        // Calculate GST percent and type for display
                        $gst_percent = 0;
                        $gst_type_label = '';
                        $seller_state = 'Maharashtra'; // Seller's state for GST comparison
                        if (!empty($cartItems)) {
                            $total_gst_rate = 0;
                            $total_qty = 0;
                            $sgst_cgst_qty = 0;
                            $igst_qty = 0;
                            foreach ($cartItems as $item) {
                                if (isset($item['gst_rate'])) {
                                    $total_gst_rate += $item['gst_rate'] * $item['quantity'];
                                    $total_qty += $item['quantity'];
                                    // Determine GST type for this item (pass seller_state as billing_state)
                                    $gst_calc = calculateGST($item['selling_price'], $item['gst_rate'], $item['gst_type'], $delivery_state, $seller_state);
                                    if ($gst_calc['gst_type'] === 'sgst_cgst') {
                                        $sgst_cgst_qty += $item['quantity'];
                                    } else {
                                        $igst_qty += $item['quantity'];
                                    }
                                }
                            }
                            if ($total_qty > 0) {
                                $gst_percent = round($total_gst_rate / $total_qty, 1);
                                if ($sgst_cgst_qty > 0 && $igst_qty === 0) {
                                    $gst_type_label = 'GST (SGST+CGST)';
                                } elseif ($igst_qty > 0 && $sgst_cgst_qty === 0) {
                                    $gst_type_label = 'IGST';
                                } elseif ($sgst_cgst_qty > 0 && $igst_qty > 0) {
                                    $gst_type_label = 'GST/IGST';
                                }
                            }
                        }
                        ?>
                        <div class="d-flex justify-content-between mb-2">
                            <span><?php echo $gst_type_label ?: 'GST'; ?><?php if ($gst_percent > 0): ?> (<?php echo $gst_percent; ?>%)<?php endif; ?>:</span>
                            <span id="cart-gst"><?php echo formatPrice($orderTotals['gst_amount']); ?></span>
                        </div>
                        <?php if ($orderTotals['shipping_charge'] > 0): ?>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Delivery charge</span>
                            <span id="cart-shipping"><?php echo formatPrice($orderTotals['shipping_charge']); ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-2"><span>Shipping Zone</span><span><?php echo htmlspecialchars($orderTotals['shipping_zone_name'] ?? ''); ?></span></div>
                        <?php else: ?>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Shipping:</span>
                            <span id="cart-shipping">Free</span>
                        </div>
                        <?php endif; ?>
                        <hr>
                        <div class="d-flex justify-content-between mb-3">
                            <strong>Total:</strong>
                            <strong id="cart-grandtotal"><?php echo formatPrice($orderTotals['total']); ?></strong>
                        </div>
                        <div class="d-grid">
                            <a href="checkout.php" class="btn btn-primary">Proceed to Checkout</a>
                        </div>
                        <div class="text-center mt-3">
                            <a href="index.php" class="btn btn-outline-secondary">Continue Shopping</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
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
                    // Update order summary
                    fetch('ajax/get-cart-summary.php')
                        .then(res => res.json())
                        .then(summary => {
                            if (summary.success) {
                                const totals = summary.totals;
                                document.getElementById('cart-subtotal').textContent = formatPrice(totals.subtotal);
                                document.getElementById('cart-shipping').textContent = (totals.total_shipping > 0) ? formatPrice(totals.total_shipping) : 'Free';
                                document.getElementById('cart-gst').textContent = formatPrice(totals.total_gst);
                                document.getElementById('cart-grandtotal').textContent = isNaN(totals.grand_total) ? formatPrice(0) : formatPrice(totals.grand_total);
                            }
                        });
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
                    } else {
                        alert('Error: ' + data.message);
                    }
                });
            }
        });
    });
});

function formatPrice(amount) {
    if (isNaN(amount) || amount === null || amount === undefined) return '₹0.00';
    return '₹' + parseFloat(amount).toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2});
}
</script> 