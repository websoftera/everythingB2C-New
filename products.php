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

// Breadcrumb Navigation
$breadcrumbs = generateBreadcrumb($pageTitle);
echo renderBreadcrumb($breadcrumbs);
?>

<!-- Banner/Breadcrumb (skip homepage) -->
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
                                <div class="product-info">
                                  <div class="wishlist">
                                    <input type="checkbox" class="heart-checkbox" id="wishlist-checkbox-products-<?php echo $product['id']; ?>" data-product-id="<?php echo $product['id']; ?>" <?php if ($inWishlist) echo 'checked'; ?>>
                                    <label for="wishlist-checkbox-products-<?php echo $product['id']; ?>" class="wishlist-label <?php echo $inWishlist ? 'wishlist-active' : ''; ?>">
                                        <span class="heart-icon">&#10084;</span>
                                    </label>
                                  </div>
                                  <div class="product-image">
                                      <a href="product.php?slug=<?php echo $product['slug']; ?>">
                                          <?php if (!empty($product['main_image'])): ?>
                                              <img src="<?php echo $product['main_image']; ?>" alt="<?php echo cleanProductName($product['name']); ?>">
                                          <?php else: ?>
                                              <div style="background: #f8f9fa; height: 155px; display: flex; align-items: center; justify-content: center; border: 1px dashed #dee2e6;">
                                                  <small style="color: #6c757d;">No image available</small>
                                              </div>
                                          <?php endif; ?>
                                      </a>
                                      <?php if ($isOutOfStock): ?>
                                          <div class="out-of-stock">OUT OF STOCK</div>
                                      <?php endif; ?>
                                  </div>
                                  <div class="product-details">
                                      <h3><?php echo strtoupper(cleanProductName($product['name'])); ?></h3>
                                      <div class="price-buttons">
                                          <div class="price-btn mrp">
                                              <span class="label">MRP</span>
                                              <span class="value"><?php echo formatPrice($product['mrp']); ?></span>
                                          </div>
                                          <div class="price-btn pay">
                                              <span class="label">PAY</span>
                                              <span class="value"><?php echo formatPrice($product['selling_price']); ?></span>
                                          </div>
                                      </div>
                                      <?php if ($isOutOfStock): ?>
                                          <a href="product.php?slug=<?php echo $product['slug']; ?>" class="read-more">READ MORE</a>
                                      <?php else: ?>
                                        <div class="cart-actions d-flex align-items-center gap-2">
                                            <div class="quantity-control d-inline-flex align-items-center">
                                                <button type="button" class="btn-qty btn-qty-minus" aria-label="Decrease quantity">-</button>
                                                <input type="number" class="quantity-input" value="1" min="1" max="99" data-product-id="<?php echo $product['id']; ?>">
                                                <button type="button" class="btn-qty btn-qty-plus" aria-label="Increase quantity">+</button>
                                            </div>
                                        </div>
                                        <div class="cart-actions d-flex align-items-center gap-2">
                                            <button class="add-to-cart add-to-cart-btn" data-product-id="<?php echo $product['id']; ?>">ADD TO CART</button>
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
    </div>
</div>

<style>
/* Products Page - Matching Products Offering Discount Design */
.product-card {
  background: #fff !important;
  border-radius: 8px !important;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1) !important;
  border: 1px solid var(--light-blue) !important;
}

.product-info {
  padding: 5px 6px !important;
}

.product-card .product-image img {
  max-height: 155px !important;
  min-height: 155px !important;
}

.product-card .discount-banner {
  background: var(--site-blue) !important;
  color: #fff !important;
  border-radius: 4px !important;
}

.product-card .price-btn.mrp {
  background: var(--mrp-light-blue) !important;
  color: var(--dark-blue) !important;
}

.product-card .price-btn.pay {
  background: var(--pay-light-green) !important;
  color: var(--dark-grey) !important;
}

.product-card .add-to-cart-btn,
.product-card .add-to-cart {
  background: var(--cart-button) !important;
  color: #ffffff !important;
}

.product-card .add-to-cart-btn:hover,
.product-card .add-to-cart:hover {
  background: var(--dark-blue) !important;
}

.product-card .product-details {
  background-image: none !important;
}

.product-card .product-image {
  background-image: none !important;
}
</style>

<script>
// Remove any add-to-cart button JS logic here, rely on popup.js
</script>

<?php include 'includes/footer.php'; ?> 