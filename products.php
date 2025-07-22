<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

// Get filter parameters
$discounted = isset($_GET['discounted']) ? (int)$_GET['discounted'] : 0;
$featured = isset($_GET['featured']) ? (int)$_GET['featured'] : 0;

// Set page title based on filter
if ($discounted) {
    $pageTitle = "Products Offering Discount";
    $products = getDiscountedProducts();
} elseif ($featured) {
    $pageTitle = "Featured Products";
    $products = getFeaturedProducts();
} else {
    $pageTitle = "All Products";
    $products = getAllProducts();
}

// Get user's wishlist for quick lookup
$wishlist_ids = [];
if (isLoggedIn()) {
    $wishlistItems = getWishlistItems($_SESSION['user_id']);
    foreach ($wishlistItems as $item) {
        $wishlist_ids[] = $item['product_id'];
    }
} else {
    $wishlistItems = getWishlistItems();
    foreach ($wishlistItems as $item) {
        $wishlist_ids[] = $item['product_id'];
    }
}

// Include header
include 'includes/header.php';

// Banner/Breadcrumb (skip homepage)
?>
<div class="page-banner" style="background: url('asset/images/internalpage-bg.webp') center/cover no-repeat; min-height: 240px; display: flex; align-items: center;">
    <div class="container">
        <h2 style="color: #fff; font-size: 2rem; font-weight: bold; text-shadow: 0 2px 8px rgba(0,0,0,0.3); margin: 0; padding: 32px 0;">
            <?php echo htmlspecialchars($pageTitle); ?>
        </h2>
    </div>
</div>
<?php
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <h1 class="mb-4"><?php echo $pageTitle; ?></h1>
            
            <?php if (empty($products)): ?>
                <div class="alert alert-info">
                    <p>No products found.</p>
                </div>
            <?php else: ?>
                <div class="row">
                    <?php foreach ($products as $product): 
                        $inWishlist = in_array($product['id'], $wishlist_ids);
                        $isOutOfStock = ($product['stock_quantity'] <= 0);
                    ?>
                        <div class="col-md-3 col-sm-6 mb-4">
                            <div class="card product-card" data-id="prod-<?php echo $product['id']; ?>">
                                <?php if ($product['is_discounted']): ?>
                                    <div class="discount-banner">SAVE ₹<?php echo $product['mrp'] - $product['selling_price']; ?> (<?php echo $product['discount_percentage']; ?>% OFF)</div>
                                <?php endif; ?>
                                <div class="product-image">
                                    <a href="product.php?slug=<?php echo $product['slug']; ?>">
                                        <img src="./<?php echo $product['main_image']; ?>" alt="<?php echo $product['name']; ?>" class="card-img-top">
                                    </a>
                                    <?php if ($isOutOfStock): ?>
                                        <div class="out-of-stock">OUT OF STOCK</div>
                                    <?php endif; ?>
                                </div>
                                <div class="product-details">
                                    <h3><?php echo strtoupper($product['name']); ?></h3>
                                    <div class="price-buttons">
                                        <div class="price-btn mrp">
                                            <span class="label">MRP</span>
                                            <span class="value"><?php echo formatPrice($product['mrp']); ?></span>
                                        </div>
                                        <div class="price-btn pay">
                                            <span class="label">PAY</span>
                                            <span class="value"><?php echo formatPrice($product['selling_price']); ?></span>
                                        </div>
                                        <div class="wishlist">
                                            <input type="checkbox" class="heart-checkbox" id="wishlist-checkbox-<?php echo $product['id']; ?>" data-product-id="<?php echo $product['id']; ?>" <?php if ($inWishlist) echo 'checked'; ?>>
                                            <label for="wishlist-checkbox-<?php echo $product['id']; ?>" class="wishlist-label"><i class="fas fa-heart"></i></label>
                                        </div>
                                    </div>
                                    <?php if ($isOutOfStock): ?>
                                        <a href="product.php?slug=<?php echo $product['slug']; ?>" class="read-more">READ MORE</a>
                                    <?php else: ?>
                                        <div class="cart-actions">
                                            <button class="add-to-cart add-to-cart-btn" data-product-id="<?php echo $product['id']; ?>">ADD TO CART</button>
                                            <div class="quantity-control d-inline-flex align-items-center">
                                                <button type="button" class="btn-qty btn-qty-minus" aria-label="Decrease quantity">-</button>
                                                <input type="number" class="quantity-input" value="1" min="1">
                                                <button type="button" class="btn-qty btn-qty-plus" aria-label="Increase quantity">+</button>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
// Remove any add-to-cart button JS logic here, rely on popup.js
</script>

<?php include 'includes/footer.php'; ?> 