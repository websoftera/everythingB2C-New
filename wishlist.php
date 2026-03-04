<?php
session_start();
require_once 'includes/functions.php';

$pageTitle = 'Wishlist';

// Wishlist Pagination setup
$wishlistPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($wishlistPage < 1) $wishlistPage = 1;
$wishlistLimit = 12;
$wishlistOffset = ($wishlistPage - 1) * $wishlistLimit;

// Get total items for pagination
$userId = isLoggedIn() ? $_SESSION['user_id'] : null;
$totalWishlistItems = getWishlistItems($userId, 'count');
$totalWishlistPages = ceil($totalWishlistItems / $wishlistLimit);

// Ensure current page doesn't exceed total pages
if ($wishlistPage > $totalWishlistPages && $totalWishlistPages > 0) {
    $wishlistPage = $totalWishlistPages;
    $wishlistOffset = ($wishlistPage - 1) * $wishlistLimit;
}

// Get wishlist items for both guests and logged-in users with limit/offset
if (isLoggedIn()) {
    $wishlistItems = getWishlistItems($_SESSION['user_id'], $wishlistLimit, $wishlistOffset);
} else {
    $wishlistItems = getWishlistItems(null, $wishlistLimit, $wishlistOffset);
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
        <div class="products-grid wishlist-grid">
            <?php foreach ($wishlistItems as $item): ?>
                <div class="card product-card" data-id="prod-<?php echo $item['product_id']; ?>">
                        <?php 
                        $isOutOfStock = ($item['stock_quantity'] <= 0);
                        if ($item['is_discounted']): ?>
                            <div class="discount-banner">SAVE ₹<?php echo $item['mrp'] - $item['selling_price']; ?> (<?php echo $item['discount_percentage']; ?>% OFF)</div>
                        <?php else: ?>
                            <div class="discount-banner" style="visibility: hidden;">&nbsp;</div>
                        <?php endif; ?>
                        <div class="product-info">
                            <div class="product-image">
                                <a href="product.php?slug=<?php echo $item['slug']; ?>">
                                    <?php if (!empty($item['main_image'])): ?>
                                        <img src="./<?php echo $item['main_image']; ?>" alt="<?php echo cleanProductName($item['name']); ?>" onerror="this.onerror=null; this.closest('.product-image').classList.add('no-image'); this.remove();">
                                    <?php else: ?>
                                        <img src="./uploads/products/blank-img.webp" alt="No image available">
                                    <?php endif; ?>
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
                                    <div class="wishlist">
                                        <input type="checkbox" class="heart-checkbox" id="wishlist-checkbox-<?php echo $item['product_id']; ?>" data-product-id="<?php echo $item['product_id']; ?>" checked>
                                        <label for="wishlist-checkbox-<?php echo $item['product_id']; ?>" class="wishlist-label wishlist-active">
                                            <span class="heart-icon">&#10084;</span>
                                        </label>
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
                                        <button class="add-to-cart add-to-cart-btn" data-product-id="<?php echo $item['product_id']; ?>">
                                            <i class="fas fa-shopping-cart"></i>
                                            ADD TO CART
                                        </button>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
            <?php endforeach; ?>
        </div>
        
        <!-- Pagination for Main Wishlist -->
        <?php if ($totalWishlistPages > 1): ?>
            <nav aria-label="Wishlist pagination" class="mt-4">
                <ul class="pagination justify-content-center">
                    <?php if ($wishlistPage > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?php echo $wishlistPage - 1; ?>">Previous</a>
                        </li>
                    <?php else: ?>
                        <li class="page-item">
                            <a class="page-link" href="javascript:void(0)" style="cursor: default;">Previous</a>
                        </li>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $totalWishlistPages; $i++): ?>
                        <li class="page-item <?php echo ($i === $wishlistPage) ? 'active' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                        </li>
                    <?php endfor; ?>

                    <?php if ($wishlistPage < $totalWishlistPages): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?php echo $wishlistPage + 1; ?>">Next</a>
                        </li>
                    <?php else: ?>
                        <li class="page-item">
                            <a class="page-link" href="javascript:void(0)" style="cursor: default;">Next</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
        <?php endif; ?>
        
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?> 