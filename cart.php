<?php
session_start();
require_once 'includes/functions.php';

$pageTitle = 'Shopping Cart';

// Get cart items for both guests and logged-in users
if (isLoggedIn()) {
    $cartItems = getCartItems($_SESSION['user_id']);
} else {
    $cartItems = getCartItems();
}
$totalAmount = 0;

foreach ($cartItems as $item) {
    $totalAmount += $item['selling_price'] * $item['quantity'];
}

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
    <h1>Shopping Cart</h1>
    
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
                                    <input type="number" class="form-control quantity-input" value="<?php echo $item['quantity']; ?>" min="1" data-cart-id="<?php echo $item['id']; ?>">
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
                            <span><?php echo formatPrice($totalAmount); ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Shipping:</span>
                            <span>Free</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-3">
                            <strong>Total:</strong>
                            <strong><?php echo formatPrice($totalAmount); ?></strong>
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
        input.addEventListener('change', function() {
            const cartId = this.getAttribute('data-cart-id');
            const quantity = this.value;
            
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
                    location.reload();
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
</script> 