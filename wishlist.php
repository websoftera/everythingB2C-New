<?php
session_start();
require_once 'includes/functions.php';

$pageTitle = 'Wishlist';

// Get wishlist items for both guests and logged-in users
if (isLoggedIn()) {
    $wishlistItems = getWishlistItems($_SESSION['user_id']);
} else {
    $wishlistItems = getWishlistItems();
}

require_once 'includes/header.php';

// Breadcrumb Navigation
$breadcrumbs = generateBreadcrumb($pageTitle);
echo renderBreadcrumb($breadcrumbs);
?>

<!-- Banner/Breadcrumb (skip homepage) -->
<?php
?>
<link rel="stylesheet" href="./asset/style/style.css">
<div class="container mt-4 wishlist-container">
    <!-- <h1>My Wishlist</h1> -->
    
    <?php if (empty($wishlistItems)): ?>
        <div class="text-center py-5">
            <h3>Your wishlist is empty</h3>
            <p>Add some products to your wishlist to save them for later.</p>
            <a href="index.php" class="btn btn-primary">Start Shopping</a>
        </div>
    <?php else: ?>
        <div class="row">
            <?php foreach ($wishlistItems as $item): ?>
                <div class="col-md-3 col-sm-6 mb-4">
                    <div class="card product-card" data-id="prod-<?php echo $item['product_id']; ?>">
                        <?php 
                        $discount = $item['mrp'] - $item['selling_price'];
                        $discountPercentage = calculateDiscountPercentage($item['mrp'], $item['selling_price']);
                        $isOutOfStock = ($item['stock_quantity'] <= 0);
                        if ($discount > 0): 
                        ?>
                            <div class="discount-banner">SAVE ₹<?php echo $discount; ?> (<?php echo $discountPercentage; ?>% OFF)</div>
                        <?php endif; ?>
                        <div class="product-info">
                            <div class="wishlist">
                                <input type="checkbox" class="heart-checkbox" id="wishlist-checkbox-<?php echo $item['product_id']; ?>" data-product-id="<?php echo $item['product_id']; ?>" checked>
                                <label for="wishlist-checkbox-<?php echo $item['product_id']; ?>" class="wishlist-label wishlist-active">
                                    <span class="heart-icon">&#10084;</span>
                                </label>
                            </div>
                            <div class="product-image">
                                <a href="product.php?slug=<?php echo $item['slug']; ?>">
                                    <img src="./<?php echo $item['main_image']; ?>" alt="<?php echo cleanProductName($item['name']); ?>">
                                </a>
                                <?php if ($isOutOfStock): ?>
                                    <div class="out-of-stock">OUT OF STOCK</div>
                                <?php endif; ?>
                            </div>
                            <div class="product-details">
                                <h3><?php echo strtoupper(cleanProductName($item['name'])); ?></h3>
                                <div class="price-buttons">
                                    <div class="price-btn mrp">
                                        <span class="label">MRP</span>
                                        <span class="value"><?php echo formatPrice($item['mrp']); ?></span>
                                    </div>
                                    <div class="price-btn pay">
                                        <span class="label">PAY</span>
                                        <span class="value"><?php echo formatPrice($item['selling_price']); ?></span>
                                    </div>
                                </div>
                                <?php if ($isOutOfStock): ?>
                                    <a href="product.php?slug=<?php echo $item['slug']; ?>" class="read-more">READ MORE</a>
                                <?php else: ?>
                                    <div class="cart-actions d-flex align-items-center gap-2">
                                        <div class="quantity-control d-inline-flex align-items-center">
                                            <button type="button" class="btn-qty btn-qty-minus" aria-label="Decrease quantity">-</button>
                                            <input type="number" class="quantity-input" value="1" min="1" max="99" data-product-id="<?php echo $item['product_id']; ?>">
                                            <button type="button" class="btn-qty btn-qty-plus" aria-label="Increase quantity">+</button>
                                        </div>
                                    </div>
                                    <div class="cart-actions d-flex align-items-center gap-2">
                                        <button class="add-to-cart add-to-cart-btn" data-product-id="<?php echo $item['product_id']; ?>">
                                            <i class="fas fa-shopping-cart" style="margin-right: 6px; transform: scaleX(-1); font-size: 18px;"></i>
                                            ADD TO CART
                                        </button>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?> 