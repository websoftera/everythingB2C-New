<?php
session_start();
require_once 'includes/functions.php';

$pageTitle = 'Wishlist';

// Wishlist Pagination setup
$wishlistPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($wishlistPage < 1) $wishlistPage = 1;
$wishlistLimit = 15;
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
<div class="container-fluid mt-4 wishlist-container">
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
                                        <img src="<?php echo $item['main_image']; ?>" alt="<?php echo cleanProductName($item['name']); ?>">
                                    <?php else: ?>
                                        <img src="./uploads/products/blank-img.webp" alt="No image available">
                                    <?php endif; ?>
                                </a>
                                <?php if ($isOutOfStock): ?>
                                    <div class="out-of-stock">OUT OF STOCK</div>
                                <?php endif; ?>
                            </div>
                            <div class="product-details">
                                <a href="product.php?slug=<?php echo $item['slug']; ?>" class="product-title-link">
                                    <h3><?php echo strtoupper(cleanProductName($item['name'])); ?></h3>
                                </a>
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
<style>
/* Wishlist Page Layout Styles */
.products-container {
  padding: 20px 0;
}

/* Product Grid - Standardized to match category layout */
.products-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr); /* Default: 4 cards */
  gap: 20px;
  margin-bottom: 40px;
  margin-top:20px;
  width: 100%;
}

/* Product Card - Enhanced Hover & Radius Preservation */
.wishlist-container .products-grid .card.product-card {
  border-radius: 8px !important;
  overflow: hidden !important;
  transition: transform 0.3s ease, box-shadow 0.3s ease !important;
  border: 1px solid #eee !important;
  isolation: isolate !important; /* Forces stacking context for clean clipping */
}

.wishlist-container .products-grid .card.product-card:hover {
  transform: translateY(-5px) !important;
  box-shadow: 0 10px 20px rgba(0, 0, 0, 0.12) !important;
  border-radius: 8px !important; /* Absolute preservation */
  overflow: hidden !important;
}

/* Response Design - Standardized break points */
@media (max-width: 767px) {
  .products-grid {
    grid-template-columns: 1fr !important; /* Mobile: 1 card per row */
    gap: 15px;
  }
}

@media (min-width: 768px) and (max-width: 1199px) {
  .products-grid {
    grid-template-columns: repeat(3, 1fr) !important; /* Tablet: 3 cards per row */
    gap: 18px;
  }
}

@media (min-width: 1200px) {
  .products-grid {
    grid-template-columns: repeat(5, 1fr) !important; /* Desktop: 5 cards per row */
    gap: 20px !important;
  }
  .products-grid .card.product-card {
    min-width: 0 !important;
    max-width: 100% !important;
    margin: 0 !important;
  }
}

@media (min-width: 1400px) {
  .products-grid {
    grid-template-columns: repeat(5, 1fr) !important; /* Wide screens: 5 cards per row */
    gap: 20px !important;
  }
}

/* Container width standardization - Harmonized with category.php */
.container-fluid {
  max-width: 100% !important;
  overflow-x: hidden !important;
  padding-left: 15px !important;
  padding-right: 15px !important;
}

.row {
  margin-left: 0 !important;
  margin-right: 0 !important;
}

/* Discount banner consistency */
.wishlist-container .products-grid .card.product-card .discount-banner {
    background: var(--site-blue) !important;
    color: #fff !important;
    border-radius: 8px 8px 0 0 !important; /* Match card radius for clean corners */
    padding: 8px 0 !important;
    font-size: 11px !important;
    text-align: center !important;
    height: auto !important;
    min-height: unset !important;
    display: block !important;
}
</style>

<?php include 'includes/footer.php'; ?>