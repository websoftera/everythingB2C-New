<?php
$pageTitle = 'Demo-site';
require_once 'includes/header.php';
require_once 'includes/delivery_popup_functions.php';

// Check if popup should be shown
$showPopup = shouldShowDeliveryPopup();
$popupSettings = getPopupSettings();

// Get data from database
$categories = getAllCategoriesWithRecursiveProductCount();
$featuredProducts = getFeaturedProducts(8);
$discountedProducts = getDiscountedProducts(8);

// Get user's wishlist for quick lookup
$wishlist_ids = [];
if (isLoggedIn()) {
    $wishlistItems = getWishlistItems($_SESSION['user_id']);
    foreach ($wishlistItems as $item) {
        $wishlist_ids[] = $item['product_id'];
    }
} else {
    // For guests, get wishlist from session
    $wishlistItems = getSessionWishlistItems();
    foreach ($wishlistItems as $item) {
        $wishlist_ids[] = $item['product_id'];
    }
}

// Debug: Log wishlist status
error_log("Wishlist debug - User logged in: " . (isLoggedIn() ? 'Yes' : 'No') . ", Wishlist items count: " . count($wishlist_ids) . ", Wishlist IDs: " . implode(',', $wishlist_ids));

// Filter to only main categories (parent_id is NULL)
$main_categories = array_filter($categories, function($cat) { return empty($cat['parent_id']); });
?>

<!-- Hero Section -->
<section class="hero-slider-section">
    <div id="heroCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel" data-bs-interval="1500">
        <div class="carousel-inner">
            <div class="carousel-item active hero-slide hero-slide-1">
                <div class="carousel-caption d-block text-start">
                    <!-- Optional caption content -->
                </div>
            </div>
            <div class="carousel-item hero-slide hero-slide-2">
                <div class="carousel-caption d-block text-end">
                    <!-- Optional caption content -->
                </div>
            </div>
            <div class="carousel-item hero-slide hero-slide-3">
                <div class="carousel-caption d-block text-end">
                    <!-- Optional caption content -->
                </div>
            </div>
        </div>
        <!-- Carousel Controls -->
        <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
        <!-- Carousel Indicators -->
        <div class="carousel-indicators">
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="1" aria-label="Slide 2"></button>
        </div>
    </div>
</section>

<!-- How We Work Section -->
    <div class="container hero-container">
        <h5 class="hero-button">HOW WE WORK</h5>
    </div>

<section>
    <div class="process-container">
        <div class="step">
            <img src="./asset/images/work-1.webp" alt="Online Shopping">
            <p>ONLINE SHOPPING</p>
        </div>
        <div class="arrow">
            <!-- <img src="./asset/images/work-progress.webp" alt="Arrow"> -->
        </div>
        <div class="step">
            <img src="./asset/images/work-2.webp" alt="Warehouse">
            <p>WAREHOUSE</p>
        </div>
        <div class="arrow">
            <!-- <img src="./asset/images/work-progress.webp" alt="Arrow"> -->
        </div>
        <div class="step">
            <img src="./asset/images/work-3.webp" alt="Shipping">
            <p>SHIPPING</p>
        </div>
        <div class="arrow">
            <!-- <img src="./asset/images/work-progress.webp" alt="Arrow"> -->
        </div>
        <div class="step">
            <img src="./asset/images/work-01.webp" alt="Home Delivery">
            <p>HOME DELIVERY</p>
        </div>
    </div>
</section>

<!-- Product Categories Section -->
<section class="popular-categories-section">
    <div class="categories-card">
        <div class="category-products-header">
            <h2 class="category-products-title">Product Categories</h2>
            <a href="categories.php" class="view-all-link">View All</a>
        </div>
        <div class="categories-slider-wrapper">
                         <button class="category-nav-btn prev-btn" aria-label="Scroll Left">&#8249;</button>
            <div class="categories-container" id="slider">
            <?php foreach ($main_categories as $category): ?>
                    <div class="category-item">
                    <a href="category.php?slug=<?php echo $category['slug']; ?>">
                            <div class="category-illustration">
                                <?php if (!empty($category['image']) && file_exists('./' . $category['image'])): ?>
                        <img src="./<?php echo $category['image']; ?>" alt="<?php echo $category['name']; ?>" />
                                <?php else: ?>
                                    <div class="category-placeholder">
                                        <i class="fas fa-box"></i>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <p class="category-label"><?php echo ucfirst($category['name']); ?></p>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
                         <button class="category-nav-btn next-btn" aria-label="Scroll Right">&#8250;</button>
        </div>
    </div>
</section>

<!-- Products Offering Discount Section -->
<section class="discounted-products-section">
    <div class="discounted-products-card">
        <div class="discounted-products-header">
            <h2 class="discounted-products-title">Products Offering Discount</h2>
            <a href="products.php?discounted=1" class="view-all-link">View All</a>
        </div>
        <div class="discounted-products-slider-wrapper">
            <button class="discounted-nav-btn prev-btn" aria-label="Scroll Left">
          <img src="asset/icons/blue_arrow.png" alt="Previous" style="width: 24px; height: 24px;">
        </button>
            <div class="discounted-products-container" id="discounted-slider">
        <?php 
        // Debug: Show how many discounted products we have
        echo '<!-- DEBUG: Found ' . count($discountedProducts) . ' discounted products -->';
        
        foreach ($discountedProducts as $product): 
            $inWishlist = in_array($product['id'], $wishlist_ids);
            $isOutOfStock = ($product['stock_quantity'] <= 0);
        ?>
            <div class="card product-card" data-id="prod-<?php echo $product['id']; ?>">
                <?php if ($product['is_discounted']): ?>
                    <div class="discount-banner">SAVE ₹<?php echo $product['mrp'] - $product['selling_price']; ?> (<?php echo $product['discount_percentage']; ?>% OFF)</div>
                <?php endif; ?>
                <div class="product-info">
                  <div class="wishlist">
                    <input type="checkbox" class="heart-checkbox" id="wishlist-checkbox-discounted-<?php echo $product['id']; ?>" data-product-id="<?php echo $product['id']; ?>" <?php if ($inWishlist) echo 'checked'; ?>>
                    <label for="wishlist-checkbox-discounted-<?php echo $product['id']; ?>" class="wishlist-label <?php echo $inWishlist ? 'wishlist-active' : ''; ?>">
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
        <?php endforeach; ?>
</div>
            <button class="discounted-nav-btn next-btn" aria-label="Scroll Right">
          <img src="asset/icons/blue_arrow.png" alt="Next" style="transform: rotate(180deg);width: 24px; height: 24px;">
        </button>
</div>
    </div>
</section>

<!-- Featured Products Section -->
<section class="featured-products-section">
    <div class="featured-products-card">
        <div class="featured-products-header">
            <h2 class="featured-products-title">Top 100 Products with Higher Discounts</h2>
            <a href="products.php?featured=1" class="view-all-link">View All</a>
        </div>
        <div class="featured-products-slider-wrapper">
            <button class="featured-nav-btn prev-btn" aria-label="Scroll Left">
          <img src="asset/icons/green_arrow.png" alt="Previous" style="width: 24px; height: 24px;">
        </button>
            <div class="featured-products-container" id="featured-slider">
        <?php 
        // Debug: Show how many featured products we have
        echo '<!-- DEBUG: Found ' . count($featuredProducts) . ' featured products -->';
        
        foreach ($featuredProducts as $product): 
            $inWishlist = in_array($product['id'], $wishlist_ids);
            $isOutOfStock = ($product['stock_quantity'] <= 0);
        ?>
            <div class="card product-card" data-id="prod-<?php echo $product['id']; ?>">
                <?php if ($product['is_discounted']): ?>
                    <div class="discount-banner">SAVE ₹<?php echo $product['mrp'] - $product['selling_price']; ?> (<?php echo $product['discount_percentage']; ?>% OFF)</div>
                <?php endif; ?>
                <div class="product-info">
                <div class="wishlist">
                    <input type="checkbox" class="heart-checkbox" id="wishlist-checkbox-featured-<?php echo $product['id']; ?>" data-product-id="<?php echo $product['id']; ?>" <?php if ($inWishlist) echo 'checked'; ?>>
                    <label for="wishlist-checkbox-featured-<?php echo $product['id']; ?>" class="wishlist-label <?php echo $inWishlist ? 'wishlist-active' : ''; ?>">
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
                            <button class="add-to-cart add-to-cart-btn" data-product-id="<?php echo $product['id']; ?>">ADD TO CART</button>
                        </div>
                    <?php endif; ?>
                </div>
                </div>
            </div>
        <?php endforeach; ?>
</div>
            <button class="featured-nav-btn next-btn" aria-label="Scroll Right">
          <img src="asset/icons/green_arrow.png" alt="Next" style="transform: rotate(180deg); width: 24px; height: 24px;">
        </button>
</div>
    </div>
</section>

<section class="service-section">
  <div class="features">
    
    <div class="feature green">
      <i class="fas fa-truck"></i>
      <div class="divider"></div>
      <span>Free Shipping</span>
    </div>

    <div class="feature yellow">
      <i class="fas fa-indian-rupee-sign"></i>
      <div class="divider"></div>
      <span>Lower Price</span>
    </div>

    <div class="feature blue">
      <i class="fas fa-mobile-alt"></i>
      <div class="divider"></div>
      <span>COD Service</span>
    </div>

    <div class="feature gray">
      <i class="fas fa-newspaper"></i>
      <div class="divider"></div>
      <span>Return Policy</span>
    </div>

  </div>
</section>

<!-- Include Footer -->
<?php include 'includes/footer.php'; ?>

<!-- Include your existing CSS and JS files -->
<link rel="stylesheet" href="./asset/style/style.css">

<style>
/* Popular Categories Section - Modern Design */
.popular-categories-section {
  padding: 20px;
  padding-top: 40px;
  background: #f5f5f5;
  margin-top: 0;
}

.categories-card {
  background: #fff;
  border-radius: 8px;
  border: 1px solid #e0e0e0;
  padding: 20px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  margin: 0 25px;
  position: relative;
}

.categories-title {
  font-size: 16px;
  font-weight: bold;
  color: #fff;
  margin: 0 0 20px 0;
  text-align: left;
}

.categories-slider-wrapper {
  display: flex;
  align-items: center;
  position: relative;
  overflow: visible;
  padding: 0 30px;
  margin: 0 -10px;
}

.categories-container {
  display: flex;
  gap: 16px;
  overflow-x: auto;
  scroll-behavior: smooth;
  padding: 10px 0;
  scroll-snap-type: x mandatory;
  -webkit-overflow-scrolling: touch;
  scrollbar-width: none;
  flex: 1;
}

.categories-container::-webkit-scrollbar {
  display: none;
}

.category-item {
  flex: 0 0 auto;
  width: 220px;
  min-width: 220px;
  scroll-snap-align: start;
  text-align: center;
}

.category-item a {
  text-decoration: none;
  color: inherit;
  display: block;
}

.category-illustration {
  width: 200px;
  height: 200px;
  margin: 0 auto 10px;
  background: #f8f9fa;
  border-radius: 8px;
  border: 1px solid #e0e0e0;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  transition: transform 0.2s ease, box-shadow 0.2s ease;
  overflow: hidden;
}

.category-illustration:hover {
  transform: translateY(-2px);
  box-shadow: 0 0 5px 5px var(--pay-light-green);
  border: 2px solid var(--dark-green);
}

.category-illustration img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  border-radius: 8px;
}

.category-placeholder {
  width: 100%;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #e9ecef;
  border-radius: 8px;
  color: #6c757d;
}

.category-placeholder i {
  font-size: 48px;
}

.category-label {
  font-size: 14px;
  font-weight: 600;
  color: #fff;
  margin: 0;
  line-height: 1.2;
  text-align: center;
}

.category-nav-btn {
  background: #ffffff;
  border: 1px solid #e0e0e0;
  border-radius: 50%;
  color: #16BAE4;
  font-size: 24px;
  font-weight: bold;
  cursor: pointer;
  width: 30px;
  height: 40px;
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  z-index: 50;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
  transition: all 0.3s ease;
  opacity: 1;
  pointer-events: auto;
  text-decoration: none;
  line-height: 1;
  user-select: none;
}

.category-nav-btn:hover {
  background: #ffffff;
  color: #16BAE4;
  border-color: #16BAE4;
  box-shadow: 0 4px 12px rgba(22, 186, 228, 0.3);
  transform: translateY(-50%) scale(1.05);
}

.category-nav-btn:active {
  transform: translateY(-50%) scale(0.95);
}

.category-nav-btn.prev-btn {
  left: -24px;
}

.category-nav-btn.next-btn {
  right: -24px;
}

/* Ensure arrows are always visible */
.category-nav-btn::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: #ffffff;
  z-index: -1;
}

/* Force arrow visibility */
.category-nav-btn {
  color: #16BAE4 !important;
  background: #ffffff !important;
  border: 1px solid #e0e0e0 !important;
}

/* Discounted Products Section - Modern Design */
.discounted-products-section {
  padding: 20px;
  background: none !important;
  margin: 0;
  overflow: visible;
}

.discounted-products-card {
  background: var(--light-blue); /* Light blue background as per image */
  border-radius: 8px;
  border: 1px solid #e0e0e0;
  padding: 20px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  margin: 0 25px;
  position: relative;
  overflow: visible;
}

.discounted-products-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
}

.category-products-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
}

.category-products-title {
  font-size: 16px;
  font-weight: bold;
  color: var(--dark-blue); /* Dark blue color as per image */
  margin: 0;
  padding-left: 8px;
}

.discounted-products-title {
  font-size: 16px;
  font-weight: bold;
  color: var(--dark-blue); /* Dark blue color as per image */
  margin: 0;
  padding-left: 8px;
}

.category-products-header .view-all-link {
  background: var(--light-gray) !important;
  background-color: var(--medium-gray) !important;
  color: var(--dark-grey) !important;
}

.view-all-link {
  text-decoration: none;
  color: var(--dark-grey); /* Dark gray color */
  font-weight: 600;
  font-size: 14px;
  background: #ffffff; /* White background for top section */
  padding: 8px 16px;
  border-radius: 6px;
  transition: color 0.2s ease;
}

.view-all-link:hover {
  color: var(--dark-grey);
}

/* Featured section View All button has different background */
.featured-products-header .view-all-link {
  background: var(--light-gray) !important; /* Light gray background as per image */
  background-color: var(--light-gray) !important;
  color: var(--dark-grey) !important;
}

/* Discounted section View All button has white background */
.discounted-products-header .view-all-link {
  background: #ffffff !important; /* White background as per image */
  background-color: #ffffff !important;
  color: var(--dark-grey) !important;
}

.discounted-products-slider-wrapper {
  display: flex;
  align-items: center;
  position: relative;
  overflow: visible;
  padding: 0 0px;
  margin: 0 auto;
  width: 100%;
  max-width: 1400px;
}

/* Desktop: Show 4 full cards + 0.5 card */
@media (min-width: 1200px) {
  .discounted-products-container {
    width: calc(4.5 * 220px + 4 * 16px) !important; /* 4.5 cards + gaps */
    max-width: calc(4.5 * 220px + 4 * 16px) !important;
  }
}

/* Large tablet: Show 3.5 cards */
@media (min-width: 992px) and (max-width: 1199px) {
  .discounted-products-container {
    width: calc(3.5 * 220px + 3 * 16px) !important;
    max-width: calc(3.5 * 220px + 3 * 16px) !important;
  }
}

/* Medium tablet: Show 2.5 cards */
@media (min-width: 768px) and (max-width: 991px) {
  .discounted-products-container {
    width: calc(2.5 * 220px + 2 * 16px) !important;
    max-width: calc(2.5 * 220px + 2 * 16px) !important;
  }
  .discounted-products-container .card.product-card {
    flex: 0 0 240px !important;
    width: 240px !important;
    min-width: 240px !important;
    max-width: 240px !important;
  }
  .discounted-products-container {
    width: calc(2.5 * 240px + 2 * 16px) !important;
    max-width: calc(2.5 * 240px + 2 * 16px) !important;
  }
}

/* Small tablet: Show 1.8 cards */
@media (min-width: 576px) and (max-width: 767px) {
  .discounted-products-container {
    width: calc(1.8 * 200px + 1 * 16px) !important;
    max-width: calc(1.8 * 200px + 1 * 16px) !important;
  }
  .discounted-products-container .card.product-card {
    flex: 0 0 200px !important;
    width: 200px !important;
    min-width: 200px !important;
    max-width: 200px !important;
  }
}

/* Mobile: Show 1.3 cards */
@media (max-width: 575px) {
  .discounted-products-container {
    width: calc(1.3 * 180px + 0.3 * 16px) !important;
    max-width: calc(1.3 * 180px + 0.3 * 16px) !important;
  }
  .discounted-products-container .card.product-card {
    flex: 0 0 180px !important;
    width: 180px !important;
    min-width: 180px !important;
    max-width: 180px !important;
  }
}

.discounted-products-container {
  display: flex !important;
  gap: 20px !important;
  overflow-x: auto !important;
  overflow-y: hidden !important;
  scroll-behavior: smooth !important;
  padding: 15px 10px !important;
  -webkit-overflow-scrolling: touch !important;
  scrollbar-width: none !important;
  flex: 1 !important;
  /* Ensure proper containment */
  position: relative !important;
  width: 100% !important;
  max-width: 100% !important;
  /* Force container to not wrap */
  flex-wrap: nowrap !important;
  /* Remove any text formatting */
  white-space: normal !important;
  /* Box model */
  box-sizing: border-box !important;
  /* Ensure container takes full width when not constrained by breakpoints */
  min-width: 100% !important;
}

/* Ensure product cards in discounted section maintain their width */
.discounted-products-container .card.product-card {
  flex: 0 0 240px !important;
  width: 240px !important;
  min-width: 240px !important;
  max-width: 240px !important;
  flex-shrink: 0 !important;
  flex-grow: 0 !important;
  margin: 0 !important;
  box-sizing: border-box !important;
  /* background: var(--light-blue) !important; */
  background: #fff !important; /* Light blue background as per image */
  border-radius: 8px !important;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1) !important;
  border: 1px solid var(--light-blue) !important;
  /* Override any global white background */
  background-image: none !important;
  /* Ensure border matches background */
  border-color: var(--light-blue) !important;
}

/* Discounted Products Section Specific Styles */
.discounted-products-slider-wrapper {
  position: relative !important;
  display: flex !important;
  align-items: center !important;
  width: 100% !important;
  max-width: 100% !important;
  margin: 0 auto !important;
  padding: 0 !important;
  box-sizing: border-box !important;
}

.featured-products-slider-wrapper {
  position: relative !important;
  display: flex !important;
  align-items: center !important;
  width: 100% !important;
  max-width: 100% !important;
  margin: 0 auto !important;
  padding: 0 !important;
  box-sizing: border-box !important;
}

.discounted-products-container .card.product-card .discount-banner {
  background: var(--site-blue) !important; /* Dark blue banner as per image */
  color: #fff !important;
  border-radius: 4px !important;
}

.discounted-products-container .card.product-card .price-btn.mrp {
  background: var(--mrp-light-blue) !important; /* Light blue background for MRP */
  color: var(--dark-blue) !important; /* Blue text */
}

.discounted-products-container .card.product-card .price-btn.pay {
  background: var(--pay-light-green) !important; /* Light green background for PAY */
  color: var(--dark-grey) !important; /* Dark gray text */
}

.discounted-products-container .card.product-card .add-to-cart-btn,
.discounted-products-container .card.product-card .add-to-cart {
  background: var(--cart-button) !important; /* Dark blue for top section */
  color: #ffffff !important;
}

.discounted-products-container .card.product-card .add-to-cart-btn:hover,
.discounted-products-container .card.product-card .add-to-cart:hover {
  background: var(--dark-blue) !important; /* Slightly lighter blue on hover */
}

/* Cart Added Highlight for Discounted Products Section */
.discounted-products-container .card.product-card .add-to-cart-btn.cart-added-highlight,
.discounted-products-container .card.product-card .add-to-cart.cart-added-highlight {
  border: 2.5px solid #ffd600 !important; /* Yellow border */
  background: #9fbe1b !important; /* Green background */
  color: #fff !important;
  box-shadow: 0 0 0 6px rgba(255,214,0,0.18), 0 2px 12px rgba(40,167,69,0.22) !important;
  transition: box-shadow 0.2s, border 0.2s, background 0.2s, color 0.2s !important;
  transform: scale(1.02) !important;
  position: relative !important;
  z-index: 999 !important;
}

.discounted-products-container .card.product-card .product-details {
  background-image: none !important;
}

.discounted-products-container .card.product-card .product-image {
  background-image: none !important;
}

.discounted-products-container::-webkit-scrollbar {
  display: none;
}

.product-info{
  padding: 5px 6px !important;
  background: #ffffff !important;
}

.discounted-nav-btn {
  background: #ffffff;
  border: none;
  color: var(--light-blue); /* Light blue color as per image */
  font-size: 14px;
  font-weight: bold;
  cursor: pointer;
  width: 30px;
  height: 40px;
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  z-index: 100;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
  transition: all 0.3s ease;
  opacity: 1;
  pointer-events: auto;
  text-decoration: none;
  line-height: 1;
  user-select: none;
}

.discounted-nav-btn:hover {
  background: #ffffff;
  color: var(--dark-blue); /* Darker blue on hover */
  border-color: var(--light-blue);
  box-shadow: 0 4px 12px rgba(206, 229, 239, 0.3);
  transform: translateY(-50%) scale(1.05);
}

.discounted-nav-btn:active {
  transform: translateY(-50%) scale(0.95);
}

.discounted-nav-btn.prev-btn {
  left: -35px;
  z-index: 10;
}

.discounted-nav-btn.next-btn {
  right: -35px;
  z-index: 10;
}

/* Ensure discounted arrows are always visible */
.discounted-nav-btn::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  /* border-radius: 50%; */
  background: #ffffff;
  z-index: -1;
}

/* Force discounted arrow visibility */
.discounted-nav-btn {
  color: var(--light-blue) !important; /* Light blue color as per image */
  background: #ffffff !important;
  border: 1px solid #e0e0e0 !important;
  cursor: pointer !important;
  pointer-events: auto !important;
  position: absolute !important;
  z-index: 100 !important;
  /* border-radius: 50% !important; */
  width: 22px !important;
  height: 22px !important;
  display: flex !important;
  align-items: center !important;
  justify-content: center !important;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1) !important;
  top: 50% !important;
  transform: translateY(-50%) !important;
}

.discounted-nav-btn.prev-btn {
  left: -30px !important;
}

.discounted-nav-btn.next-btn {
  right: -30px !important;
}

.featured-nav-btn.prev-btn {
  left: -30px !important;
}

.featured-nav-btn.next-btn {
  right: -30px !important;
}

/* Ensure perfect circular shape */
.discounted-nav-btn img,
.featured-nav-btn img {
  width: 12px !important;
  height: 22px !important;
  object-fit: contain !important;
  display: block !important;
}

/* Navigation button hover effects */
.discounted-nav-btn:hover,
.featured-nav-btn:hover {
  transform: translateY(-50%) scale(1.05) !important;
  box-shadow: 0 4px 12px rgba(0,0,0,0.15) !important;
}

/* Ensure discounted arrows are clickable */
.discounted-nav-btn:hover {
  cursor: pointer !important;
  pointer-events: auto !important;
  background: #f8f9fa !important;
  color: var(--dark-blue) !important; /* Darker blue on hover */
}

.discounted-nav-btn:active {
  cursor: pointer !important;
  pointer-events: auto !important;
  transform: translateY(-50%) scale(0.95) !important;
}

/* Additional debugging styles for discounted arrows */
.discounted-nav-btn::after {
  content: '';
  position: absolute;
  top: -5px;
  left: -5px;
  right: -5px;
  bottom: -5px;
  background: transparent;
  z-index: 99;
}

/* Mobile Responsive Design */
@media (max-width: 768px) {
  .popular-categories-section {
    padding: 15px;
    margin: 15px 0;
  }
  
  .categories-card {
    padding: 15px;
    border-radius: 6px;
  }
  
  .categories-title {
    font-size: 14px;
    margin-bottom: 15px;
  }
  
  .categories-container {
    gap: 12px;
    padding: 8px 0;
  }
  
  .category-item {
    width: 180px;
    min-width: 180px;
  }
  
  .category-illustration {
    width: 160px;
    height: 160px;
    margin-bottom: 8px;
  }
  
  .category-illustration img {
    width: 100%;
    height: 100%;
  }
  
  .category-placeholder {
    width: 100%;
    height: 100%;
  }
  
  .category-placeholder i {
    font-size: 40px;
  }
  
  .category-label {
    font-size: 12px;
  }
  
  .category-nav-btn {
    width: 44px;
    height: 44px;
    font-size: 20px;
    color: #16BAE4 !important;
    background: #ffffff !important;
    border: 1px solid #e0e0e0 !important;
  }
  
  .category-nav-btn.prev-btn {
    left: -22px;
  }
  
  .category-nav-btn.next-btn {
    right: -22px;
  }
}

@media (max-width: 480px) {
  .popular-categories-section {
    padding: 10px;
    margin: 10px 0;
  }
  
  .categories-card {
    padding: 12px;
  }
  
  .categories-title {
    font-size: 12px;
    margin-bottom: 12px;
  }
  
  .categories-container {
    gap: 10px;
  }
  
  .category-item {
    width: 160px;
    min-width: 160px;
  }
  
  .category-illustration {
    width: 140px;
    height: 140px;
  }
  
  .category-illustration img {
    width: 100%;
    height: 100%;
  }
  
  .category-placeholder {
    width: 100%;
    height: 100%;
  }
  
  .category-placeholder i {
    font-size: 36px;
  }
  
  .category-label {
    font-size: 10px;
  }
  
  .category-nav-btn {
    width: 40px;
    height: 40px;
    font-size: 18px;
    color: #16BAE4 !important;
    background: #ffffff !important;
    border: 1px solid #e0e0e0 !important;
  }
  
  .category-nav-btn.prev-btn {
    left: -20px;
  }
  
  .category-nav-btn.next-btn {
    right: -20px;
  }
  
  /* Discounted Products Mobile Responsive */
  .discounted-products-section {
    padding: 15px;
    margin: 15px 0;
  }
  
  .discounted-products-card {
    padding: 15px;
    border-radius: 6px;
    max-width: 100%;
  }
  
  .discounted-products-header {
    margin-bottom: 15px;
  }
  
  .discounted-products-title {
    font-size: 14px;
    padding-left: 8px;
  }
  
  .discounted-products-slider-wrapper {
    padding: 0;
    max-width: 100%;
  }
  
  .discounted-products-container .card.product-card {
    flex: 0 0 220px !important;
    width: 220px !important;
    min-width: 220px !important;
    max-width: 220px !important;
  }
  
  .discounted-nav-btn {
    width: 44px;
    height: 44px;
    font-size: 20px;
    color: var(--light-blue) !important; /* Light blue color as per image */
    background: #ffffff !important;
    border: 1px solid #e0e0e0 !important;
  }
  
  .discounted-nav-btn.prev-btn {
    left: 5px;
  }
  
  .discounted-nav-btn.next-btn {
    right: 5px;
  }
}

/* Tablet Responsive (768px - 1024px) */
@media (max-width: 1024px) and (min-width: 769px) {
  .discounted-products-section {
    padding: 18px;
    margin: 18px 0;
  }
  
  .discounted-products-card {
    padding: 18px;
    max-width: 100%;
  }
  
  .discounted-products-title {
    font-size: 18px;
  }
  
  .discounted-products-slider-wrapper {
    padding: 0;
    max-width: 100%;
  }
  
  .discounted-products-container .card.product-card {
    flex: 0 0 220px !important;
    width: 220px !important;
    min-width: 220px !important;
    max-width: 220px !important;
  }
  
  .discounted-nav-btn {
    width: 48px;
    height: 48px;
    font-size: 22px;
  }
}

/* Medium Mobile Responsive (481px - 768px) */
@media (max-width: 768px) and (min-width: 481px) {
  .discounted-products-section {
    padding: 16px;
    margin: 16px 0;
  }
  
  .discounted-products-card {
    padding: 16px;
    max-width: 100%;
  }
  
  .discounted-products-title {
    font-size: 16px;
  }
  
  .discounted-products-slider-wrapper {
    padding: 0;
    max-width: 100%;
  }
  
  .discounted-products-container .card.product-card {
    flex: 0 0 200px !important;
    width: 200px !important;
    min-width: 200px !important;
    max-width: 200px !important;
  }
  
  .discounted-nav-btn {
    width: 46px;
    height: 46px;
    font-size: 21px;
  }
}

/* Featured Products Section - Same Design as Discounted Products */
.featured-products-section {
  padding: 20px;
  padding-bottom: 45px;
  background-color: none !important; /* Light green background as per image */
  margin: 0;
  overflow: visible;
}

.featured-products-card {
  background: var(--light-green); /* Light green background as per image */
  border-radius: 8px;
  border: 1px solid #e0e0e0;
  padding: 20px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  margin: 0 25px;
  position: relative;
  overflow: visible;
}

.featured-products-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
}

.featured-products-title {
  font-size: 16px;
  font-weight: bold;
  color: var(--dark-grey); /* Dark gray color as per image */
  margin: 0;
  padding-left: 8px;
}

.featured-products-slider-wrapper {
  display: flex;
  align-items: center;
  position: relative;
  overflow: visible;
  padding: 0;
  margin: 0 auto;
  width: 100%;
  max-width: 1400px;
}

/* Desktop: Show 4 full cards + 0.5 card for featured products */
@media (min-width: 1200px) {
  .featured-products-container {
    width: calc(4.5 * 220px + 4 * 16px) !important; /* 4.5 cards + gaps */
    max-width: calc(4.5 * 220px + 4 * 16px) !important;
  }
}

/* Large tablet: Show 3.5 cards for featured products */
@media (min-width: 992px) and (max-width: 1199px) {
  .featured-products-container {
    width: calc(3.5 * 220px + 3 * 16px) !important;
    max-width: calc(3.5 * 220px + 3 * 16px) !important;
  }
}

/* Medium tablet: Show 2.5 cards for featured products */
@media (min-width: 768px) and (max-width: 991px) {
  .featured-products-container {
    width: calc(2.5 * 220px + 2 * 16px) !important;
    max-width: calc(2.5 * 220px + 2 * 16px) !important;
  }
  .featured-products-container .card.product-card {
    flex: 0 0 220px !important;
    width: 220px !important;
    min-width: 220px !important;
    max-width: 220px !important;
  }
  .featured-products-container {
    width: calc(2.5 * 220px + 2 * 16px) !important;
    max-width: calc(2.5 * 220px + 2 * 16px) !important;
  }
}

/* Small tablet: Show 1.8 cards for featured products */
@media (min-width: 576px) and (max-width: 767px) {
  .featured-products-container {
    width: calc(1.8 * 200px + 1 * 16px) !important;
    max-width: calc(1.8 * 200px + 1 * 16px) !important;
  }
  .featured-products-container .card.product-card {
    flex: 0 0 200px !important;
    width: 200px !important;
    min-width: 200px !important;
    max-width: 200px !important;
  }
}

/* Mobile: Show 1.3 cards for featured products */
@media (max-width: 575px) {
  .featured-products-container {
    width: calc(1.3 * 180px + 0.3 * 16px) !important;
    max-width: calc(1.3 * 180px + 0.3 * 16px) !important;
  }
  .featured-products-container .card.product-card {
    flex: 0 0 180px !important;
    width: 180px !important;
    min-width: 180px !important;
    max-width: 180px !important;
  }
}

.featured-products-container {
  display: flex !important;
  gap: 20px !important;
  overflow-x: auto !important;
  overflow-y: hidden !important;
  scroll-behavior: smooth !important;
  padding: 15px 10px !important;
  -webkit-overflow-scrolling: touch !important;
  scrollbar-width: none !important;
  flex: 1 !important;
  /* Ensure proper containment */
  position: relative !important;
  width: 100% !important;
  max-width: 100% !important;
  /* Force container to not wrap */
  flex-wrap: nowrap !important;
  /* Remove any text formatting */
  white-space: normal !important;
  /* Box model */
  box-sizing: border-box !important;
  /* Ensure container takes full width when not constrained by breakpoints */
  min-width: 100% !important;
}

/* Ensure product cards in featured section maintain their width */
.featured-products-container .card.product-card {
  flex: 0 0 240px !important;
  width: 240px !important;
  min-width: 240px !important;
  max-width: 240px !important;
  flex-shrink: 0 !important;
  flex-grow: 0 !important;
  margin: 0 !important;
  box-sizing: border-box !important;
  background: #fff !important; /* Light green background as per image */
  border-radius: 8px !important;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1) !important;
  border: 1px solid var(--light-green) !important;
  /* Override any global white background */
  background-image: none !important;
  /* Ensure border matches background */
  border-color: var(--light-green) !important;
}

/* Featured Products Section Specific Styles */
.featured-products-container .card.product-card .discount-banner {
  background: var(--dark-green) !important; /* Dark green banner as per image */
  color: #fff !important;
  border-radius: 4px !important;
}

.featured-products-container .card.product-card .price-btn.mrp {
  background: var(--mrp-light-blue) !important; /* Light blue background for MRP */
  color: var(--dark-blue) !important; /* Blue text */
}

.featured-products-container .card.product-card .price-btn.pay {
  background: var(--pay-light-green) !important; /* Light green background for PAY */
  color: var(--dark-grey) !important; /* Dark gray text */
}

.featured-products-container .card.product-card .add-to-cart-btn,
.featured-products-container .card.product-card .add-to-cart {
  background: var(--dark-grey) !important; /* Dark gray for bottom section */
  color: #ffffff !important;
}

.featured-products-container .card.product-card .add-to-cart-btn:hover,
.featured-products-container .card.product-card .add-to-cart:hover {
  background: #4b5563 !important; /* Slightly lighter gray on hover */
}

/* Cart Added Highlight for Featured Products Section */
.featured-products-container .card.product-card .add-to-cart-btn.cart-added-highlight,
.featured-products-container .card.product-card .add-to-cart.cart-added-highlight {
  border: 2.5px solid #ffd600 !important; /* Yellow border */
  background: #9fbe1b !important; /* Green background */
  color: #fff !important;
  box-shadow: 0 0 0 6px rgba(255,214,0,0.18), 0 2px 12px rgba(40,167,69,0.22) !important;
  transition: box-shadow 0.2s, border 0.2s, background 0.2s, color 0.2s !important;
  transform: scale(1.02) !important;
  position: relative !important;
  z-index: 999 !important;
}

.featured-products-container .card.product-card .product-details {
  background: none !important; /* Same as card background */
  background-image: none !important;
}

.featured-products-container .card.product-card .product-image {
  background: none !important;/* Same as card background */
  background-color: none !important;
  background-image: none !important;
}

.featured-products-container::-webkit-scrollbar {
  display: none;
}

.featured-nav-btn {
  background: #ffffff !important;
  border-radius: 50% !important;
  color: var(--light-green) !important; /* Light green color as per image */
  font-size: 24px !important;
  font-weight: bold !important;
  cursor: pointer !important;
  width: 50px !important;
  height: 50px !important;
  position: absolute !important;
  top: 50% !important;
  transform: translateY(-50%) !important;
  z-index: 1000 !important;
  display: flex !important;
  align-items: center !important;
  justify-content: center !important;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1) !important;
  transition: all 0.3s ease !important;
  opacity: 1 !important;
  pointer-events: auto !important;
  text-decoration: none !important;
  line-height: 1 !important;
  user-select: none !important;
  border: 1px solid #e0e0e0 !important;
}

.featured-nav-btn.prev-btn {
  left: -25px !important;
}

.featured-nav-btn.next-btn {
  right: -25px !important;
}

.featured-nav-btn:hover {
  background: #ffffff !important;
  color: var(--dark-green) !important; /* Darker green on hover */
  border-color: var(--light-green) !important;
  box-shadow: 0 4px 12px rgba(227, 242, 170, 0.3) !important;
  transform: translateY(-50%) scale(1.05) !important;
}

.featured-nav-btn:active {
  transform: translateY(-50%) scale(0.95) !important;
}

.featured-nav-btn.prev-btn {
  left: -35px !important;
  z-index: 1000 !important;
}

.featured-nav-btn.next-btn {
  right: -35px !important;
  z-index: 1000 !important;
}

/* Featured Products Mobile Responsive */
@media (max-width: 480px) {
  .featured-products-section {
    padding: 15px;
    margin: 15px 0;
  }
  
  .featured-products-card {
    padding: 15px;
    border-radius: 6px;
    max-width: 100%;
  }
  
  .featured-products-header {
    margin-bottom: 15px;
  }
  
  .featured-products-title {
    font-size: 14px;
  }
  
  .featured-products-slider-wrapper {
    padding: 0;
    max-width: 100%;
  }
  
  .featured-products-container .card.product-card {
    flex: 0 0 180px !important;
    width: 180px !important;
    min-width: 180px !important;
    max-width: 180px !important;
  }
  
  .featured-nav-btn {
    width: 44px !important;
    height: 44px !important;
    font-size: 20px !important;
    color: var(--light-green) !important; /* Light green color as per image */
    background: #ffffff !important;
    border: 2px solid var(--light-green) !important;
  }
  
  .featured-nav-btn.prev-btn {
    left: 5px !important;
  }
  
  .featured-nav-btn.next-btn {
    right: 5px !important;
  }
}

/* Featured Products Tablet Responsive (768px - 1024px) */
@media (max-width: 1024px) and (min-width: 769px) {
  .featured-products-section {
    padding: 18px;
    margin: 18px 0;
  }
  
  .featured-products-card {
    padding: 18px;
    max-width: 100%;
  }
  
  .featured-products-title {
    font-size: 18px;
  }
  
  .featured-products-slider-wrapper {
    padding: 0;
    max-width: 100%;
  }
  
  .featured-products-container .card.product-card {
    flex: 0 0 220px !important;
    width: 220px !important;
    min-width: 220px !important;
    max-width: 220px !important;
  }
  
  .featured-nav-btn {
    width: 48px !important;
    height: 48px !important;
    font-size: 22px !important;
    color: var(--light-green) !important; /* Light green color as per image */
  }
}

/* Featured Products Medium Mobile Responsive (481px - 768px) */
@media (max-width: 768px) and (min-width: 481px) {
  .featured-products-section {
    padding: 16px;
    margin: 16px 0;
  }
  
  .featured-products-card {
    padding: 16px;
    max-width: 100%;
  }
  
  .featured-products-title {
    font-size: 16px;
  }
  
  .featured-products-slider-wrapper {
    padding: 0;
    max-width: 100%;
  }
  
  .featured-products-container .card.product-card {
    flex: 0 0 200px !important;
    width: 200px !important;
    min-width: 200px !important;
    max-width: 200px !important;
  }
  
  .featured-nav-btn {
    width: 46px !important;
    height: 46px !important;
    font-size: 21px !important;
    color: var(--light-green) !important; /* Light green color as per image */
  }
}

/* Force Featured Arrow Visibility - Override any conflicts */
.featured-nav-btn,
.featured-nav-btn.prev-btn,
.featured-nav-btn.next-btn {
  visibility: visible !important;
  display: flex !important;
  opacity: 1 !important;
  pointer-events: auto !important;
  position: absolute !important;
  z-index: 9999 !important;
}

/* Ensure featured section containers don't hide arrows */
.featured-products-section,
.featured-products-card,
.featured-products-slider-wrapper {
  overflow: visible !important;
  position: relative !important;
}

/* Additional arrow styling to ensure visibility */
.featured-nav-btn::before {
  content: '';
  position: absolute !important;
  top: -2px !important;
  left: -2px !important;
  right: -2px !important;
  bottom: -2px !important;
  background: transparent !important;
  border-radius: 50% !important;
  z-index: -1 !important;
}

/* Hero Banner Carousel - Responsive Fix */
.hero-slider-section {
  width: 100% !important;
  overflow: hidden !important;
  position: relative !important;
}

/* Additional Responsive Container Fixes */
@media (max-width: 1200px) {
  .discounted-products-card,
  .featured-products-card {
    max-width: 95% !important;
  }
  
  .discounted-products-slider-wrapper,
  .featured-products-slider-wrapper {
    max-width: 95% !important;
  }
}

@media (max-width: 768px) {
  .discounted-products-card,
  .featured-products-card {
    max-width: 100% !important;
    margin: 0 10px !important;
  }
  
  .discounted-products-slider-wrapper,
  .featured-products-slider-wrapper {
    max-width: 100% !important;
    padding: 0 !important;
  }
}

@media (max-width: 480px) {
  .discounted-products-card,
  .featured-products-card {
    margin: 0 5px !important;
  }
  
  .discounted-products-slider-wrapper,
  .featured-products-slider-wrapper {
    padding: 0 !important;
  }
}

#heroCarousel {
  width: 100% !important;
  overflow: hidden !important;
}

#heroCarousel .carousel-inner {
  width: 100% !important;
  overflow: hidden !important;
}

#heroCarousel .carousel-item {
  width: 100% !important;
  display: none !important;
  position: relative !important;
}

#heroCarousel .carousel-item.active {
  display: block !important;
}

#heroCarousel .carousel-item img {
  width: 100% !important;
  height: auto !important;
  object-fit: cover !important;
}

/* Mobile-specific carousel fixes */
@media (max-width: 767.98px) {
  .hero-slider-section {
    overflow: hidden !important;
  }
  
  #heroCarousel {
    overflow: hidden !important;
  }
  
  #heroCarousel .carousel-inner {
    overflow: hidden !important;
  }
  
  #heroCarousel .carousel-item {
    width: 100% !important;
    display: none !important;
  }
  
  #heroCarousel .carousel-item.active {
    display: block !important;
  }
  
  /* Ensure only one slide is visible at a time */
  #heroCarousel .carousel-item:not(.active) {
    display: none !important;
    position: absolute !important;
    left: -100% !important;
  }
}

/* Tablet and desktop carousel fixes */
@media (min-width: 768px) {
  .hero-slider-section {
    overflow: hidden !important;
  }
  
  #heroCarousel {
    overflow: hidden !important;
  }
  
  #heroCarousel .carousel-inner {
    overflow: hidden !important;
  }
}

/* Featured Products - Now using same structure as other product sections */
#featured-products {
  display: flex !important;
  flex-wrap: nowrap !important;
  overflow-x: auto !important;
  scroll-behavior: smooth !important;
  gap: 15px !important;
  padding: 20px !important;
  -ms-overflow-style: none !important;
  scrollbar-width: none !important;
}

#featured-products::-webkit-scrollbar {
  display: none !important;
}

#featured-products .product-card {
  flex: 0 0 auto !important;
  width: 280px !important;
  min-width: 280px !important;
  max-width: 280px !important;
}

/* Featured Products - Image Display Fix */
#featured-products .product-card .product-image {
  width: 100% !important;
  height: 200px !important;
  overflow: hidden !important;
  position: relative !important;
  display: block !important;
}

#featured-products .product-card .product-image img {
  width: 100% !important;
  height: 100% !important;
  max-height: 155px !important;
  min-height: 155px !important;
  object-fit: cover !important;
  display: block !important;
  visibility: visible !important;
  opacity: 1 !important;
}

#featured-products .product-card .product-image a {
  display: block !important;
  width: 100% !important;
  height: 100% !important;
  text-decoration: none !important;
}

/* Ensure product cards are visible */
#featured-products .product-card {
  display: block !important;
  visibility: visible !important;
  opacity: 1 !important;
  width: 280px !important;
  min-width: 280px !important;
  max-width: 280px !important;
  margin: 0 10px !important;
}

/* Mobile responsive for featured products */
@media (max-width: 767.98px) {
  #featured-products .product-card {
    width: 250px !important;
    min-width: 250px !important;
    max-width: 250px !important;
  }
  
  #featured-products .product-card .product-image {
    height: 180px !important;
  }
}

/* Discounted Products - Same structure as Featured Products */
#discounted-products {
  display: flex !important;
  flex-wrap: nowrap !important;
  overflow-x: auto !important;
  scroll-behavior: smooth !important;
  gap: 15px !important;
  padding: 20px !important;
  -ms-overflow-style: none !important;
  scrollbar-width: none !important;
}

#discounted-products::-webkit-scrollbar {
  display: none !important;
}

#discounted-products .product-card {
  flex: 0 0 auto !important;
  width: 280px !important;
  min-width: 280px !important;
  max-width: 280px !important;
}

/* Mobile responsive for discounted products */
@media (max-width: 767.98px) {
  #discounted-products .product-card {
    width: 250px !important;
    min-width: 250px !important;
    max-width: 250px !important;
  }
}

/* Discounted Products - Image Display Fix */
#discounted-products .product-card .product-image {
  width: 100% !important;
  height: 200px !important;
  overflow: hidden !important;
  position: relative !important;
  display: block !important;
}

#discounted-products .product-card .product-image img {
  width: 100% !important;
  height: 100% !important;
  max-height: 155px !important;
  min-height: 155px !important;
  object-fit: cover !important;
  display: block !important;
  visibility: visible !important;
  opacity: 1 !important;
}

#discounted-products .product-card .product-image a {
  display: block !important;
  width: 100% !important;
  height: 100% !important;
  text-decoration: none !important;
}

/* Ensure product cards are visible */
#discounted-products .product-card {
  display: block !important;
  visibility: visible !important;
  opacity: 1 !important;
  width: 280px !important;
  min-width: 280px !important;
  max-width: 280px !important;
  margin: 0 10px !important;
}

/* Mobile responsive for discounted products */
@media (max-width: 767.98px) {
  #discounted-products .product-card {
    width: 250px !important;
    min-width: 250px !important;
    max-width: 250px !important;
  }
  
  #discounted-products .product-card .product-image {
    height: 180px !important;
  }
}

/* Carousel Controls - Mobile Responsive */
.carousel-control-prev,
.carousel-control-next {
  position: absolute !important;
  top: 50% !important;
  transform: translateY(-50%) !important;
  z-index: 10 !important;
  background: rgba(0, 0, 0, 0.3) !important;
  border: none !important;
  border-radius: 50% !important;
  width: 40px !important;
  height: 40px !important;
  display: flex !important;
  align-items: center !important;
  justify-content: center !important;
}

.carousel-control-prev {
  left: 10px !important;
}

.carousel-control-next {
  right: 10px !important;
}

.carousel-control-prev-icon,
.carousel-control-next-icon {
  width: 25px !important;
  height: 25px !important;
  filter: brightness(0) invert(1) !important;
}

/* Carousel Indicators */
.carousel-indicators {
  position: absolute !important;
  bottom: 20px !important;
  left: 50% !important;
  transform: translateX(-50%) !important;
  z-index: 10 !important;
  display: flex !important;
  gap: 8px !important;
}

.carousel-indicators button {
  width: 12px !important;
  height: 12px !important;
  border-radius: 50% !important;
  border: 2px solid rgba(255, 255, 255, 0.5) !important;
  background: transparent !important;
  cursor: pointer !important;
  transition: all 0.3s ease !important;
}

.carousel-indicators button.active {
  background: rgba(255, 255, 255, 0.8) !important;
  border-color: rgba(255, 255, 255, 0.8) !important;
}

/* Mobile-specific carousel control adjustments */
@media (max-width: 767.98px) {
  .carousel-control-prev,
  .carousel-control-next {
    width: 35px !important;
    height: 35px !important;
  }
  
  .carousel-control-prev-icon,
  .carousel-control-next-icon {
    width: 18px !important;
    height: 18px !important;
  }
  
  .carousel-indicators {
    bottom: 15px !important;
  }
  
  .carousel-indicators button {
    width: 10px !important;
    height: 10px !important;
  }
}
</style>

<script>
console.log('JavaScript loading...');
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM Content Loaded');
    // Hero Carousel functionality
    const heroCarousel = document.getElementById('heroCarousel');
    if (heroCarousel) {
        // Initialize Bootstrap carousel if not already initialized
        if (typeof bootstrap !== 'undefined' && bootstrap.Carousel) {
            const carousel = new bootstrap.Carousel(heroCarousel, {
                interval: 3000,
                wrap: true,
                keyboard: false,
                pause: 'hover',
                touch: true
            });
        }
        
        // Fallback carousel functionality for mobile
        const carouselItems = heroCarousel.querySelectorAll('.carousel-item');
        let currentIndex = 0;
        
        function showSlide(index) {
            carouselItems.forEach((item, i) => {
                item.classList.remove('active');
                if (i === index) {
                    item.classList.add('active');
                }
            });
        }
        
        function nextSlide() {
            currentIndex = (currentIndex + 1) % carouselItems.length;
            showSlide(currentIndex);
        }
        
        // Auto-advance slides
        setInterval(nextSlide, 3000);
    }
    
    // Category slider functionality - Simplified
    console.log('🔍 Looking for category slider elements...');
    const categoryContainer = document.getElementById('slider');
    const prevBtn = document.querySelector('.category-nav-btn.prev-btn');
    const nextBtn = document.querySelector('.category-nav-btn.next-btn');
    
    console.log('Category elements:', {
        container: categoryContainer ? 'FOUND' : 'NOT FOUND',
        prevBtn: prevBtn ? 'FOUND' : 'NOT FOUND',
        nextBtn: nextBtn ? 'FOUND' : 'NOT FOUND'
    });
    
    if (categoryContainer && prevBtn && nextBtn) {
        console.log('✅ All category slider elements found - Setting up slider...');
        
        // Simple scroll function
        const scrollAmount = 200; // Fixed scroll amount
        
        prevBtn.addEventListener('click', function(e) {
            e.preventDefault();
            console.log('⬅️ CATEGORY PREV button clicked');
            categoryContainer.scrollBy({
                left: -scrollAmount,
                behavior: 'smooth'
            });
            console.log('Category scrolled left by', scrollAmount, 'px');
        });
        
        nextBtn.addEventListener('click', function(e) {
            e.preventDefault();
            console.log('➡️ CATEGORY NEXT button clicked');
            categoryContainer.scrollBy({
                left: scrollAmount,
                behavior: 'smooth'
            });
            console.log('Category scrolled right by', scrollAmount, 'px');
        });
        
        console.log('✅ Category slider event listeners added');
        
    } else {
        console.error('❌ Category slider setup failed - missing elements');
        if (!categoryContainer) console.error('Missing: slider container');
        if (!prevBtn) console.error('Missing: category prev button');
        if (!nextBtn) console.error('Missing: category next button');
    }
    
    // Discounted Products slider functionality - SIMPLE & RELIABLE VERSION
    console.log('🔍 Setting up discounted products slider...');
    
    const discountedContainer = document.getElementById('discounted-slider');
    const discountedPrevBtn = document.querySelector('.discounted-nav-btn.prev-btn');
    const discountedNextBtn = document.querySelector('.discounted-nav-btn.next-btn');
    
    if (discountedContainer && discountedPrevBtn && discountedNextBtn) {
        console.log('✅ Discount slider elements found - initializing...');
        
        // Dynamic scroll amount based on screen size
        function getDiscountedScrollAmount() {
            const width = window.innerWidth;
            if (width <= 575) {
                return 196; // 180px card + 16px gap
            } else if (width <= 767) {
                return 216; // 200px card + 16px gap
            } else if (width <= 991) {
                return 236; // 220px card + 16px gap
            } else {
                return 236; // 220px card + 16px gap
            }
        }
        
        // Previous button
        discountedPrevBtn.addEventListener('click', function(e) {
            e.preventDefault();
            const scrollAmount = getDiscountedScrollAmount();
            console.log('⬅️ Scrolling left by', scrollAmount, 'px');
            discountedContainer.scrollLeft -= scrollAmount;
        });
        
        // Next button
        discountedNextBtn.addEventListener('click', function(e) {
            e.preventDefault();
            const scrollAmount = getDiscountedScrollAmount();
            console.log('➡️ Scrolling right by', scrollAmount, 'px');
            discountedContainer.scrollLeft += scrollAmount;
        });
        
        console.log('✅ Discount slider setup complete');
        
        // Simple diagnostic
        setTimeout(() => {
            console.log('📊 Slider info:', {
                containerWidth: discountedContainer.clientWidth,
                contentWidth: discountedContainer.scrollWidth,
                canScroll: discountedContainer.scrollWidth > discountedContainer.clientWidth,
                cardCount: discountedContainer.querySelectorAll('.product-card').length
            });
        }, 1000);
        
    } else {
        console.error('❌ Discount slider elements not found');
    }
    
    // Featured Products slider functionality - SAME AS DISCOUNT PRODUCTS
    console.log('🔍 Setting up featured products slider...');
    
    const featuredContainer = document.getElementById('featured-slider');
    const featuredPrevBtn = document.querySelector('.featured-nav-btn.prev-btn');
    const featuredNextBtn = document.querySelector('.featured-nav-btn.next-btn');
    
    console.log('Featured elements check:', {
        container: featuredContainer ? 'FOUND' : 'NOT FOUND',
        prevBtn: featuredPrevBtn ? 'FOUND' : 'NOT FOUND',
        nextBtn: featuredNextBtn ? 'FOUND' : 'NOT FOUND'
    });
    
    // Force make arrows visible
    if (featuredPrevBtn) {
        featuredPrevBtn.style.display = 'flex';
        featuredPrevBtn.style.visibility = 'visible';
        featuredPrevBtn.style.opacity = '1';
        featuredPrevBtn.style.zIndex = '9999';
        console.log('🔧 Forced prev arrow visibility');
    }
    
    if (featuredNextBtn) {
        featuredNextBtn.style.display = 'flex';
        featuredNextBtn.style.visibility = 'visible';
        featuredNextBtn.style.opacity = '1';
        featuredNextBtn.style.zIndex = '9999';
        console.log('🔧 Forced next arrow visibility');
    }
    
    if (featuredContainer && featuredPrevBtn && featuredNextBtn) {
        console.log('✅ Featured slider elements found - initializing...');
        
        // Dynamic scroll amount based on screen size
        function getFeaturedScrollAmount() {
            const width = window.innerWidth;
            if (width <= 575) {
                return 196; // 180px card + 16px gap
            } else if (width <= 767) {
                return 216; // 200px card + 16px gap
            } else if (width <= 991) {
                return 236; // 220px card + 16px gap
            } else {
                return 236; // 220px card + 16px gap
            }
        }
        
        // Previous button
        featuredPrevBtn.addEventListener('click', function(e) {
            e.preventDefault();
            const scrollAmount = getFeaturedScrollAmount();
            console.log('⬅️ Featured scrolling left by', scrollAmount, 'px');
            featuredContainer.scrollLeft -= scrollAmount;
        });
        
        // Next button
        featuredNextBtn.addEventListener('click', function(e) {
            e.preventDefault();
            const scrollAmount = getFeaturedScrollAmount();
            console.log('➡️ Featured scrolling right by', scrollAmount, 'px');
            featuredContainer.scrollLeft += scrollAmount;
        });
        
        console.log('✅ Featured slider setup complete');
        
        // Simple diagnostic
        setTimeout(() => {
            console.log('📊 Featured slider info:', {
                containerWidth: featuredContainer.clientWidth,
                contentWidth: featuredContainer.scrollWidth,
                canScroll: featuredContainer.scrollWidth > featuredContainer.clientWidth,
                cardCount: featuredContainer.querySelectorAll('.product-card').length
            });
        }, 1000);
        
    } else {
        console.error('❌ Featured slider elements not found');
    }
    
    // Handle window resize for responsive sliders
    window.addEventListener('resize', function() {
        console.log('🔄 Window resized - updating slider diagnostics...');
        
        // Update discounted slider info
        if (discountedContainer) {
            setTimeout(() => {
                console.log('📊 Updated Discount slider info:', {
                    containerWidth: discountedContainer.clientWidth,
                    contentWidth: discountedContainer.scrollWidth,
                    canScroll: discountedContainer.scrollWidth > discountedContainer.clientWidth,
                    cardCount: discountedContainer.querySelectorAll('.product-card').length,
                    screenWidth: window.innerWidth
                });
            }, 100);
        }
        
        // Update featured slider info
        if (featuredContainer) {
            setTimeout(() => {
                console.log('📊 Updated Featured slider info:', {
                    containerWidth: featuredContainer.clientWidth,
                    contentWidth: featuredContainer.scrollWidth,
                    canScroll: featuredContainer.scrollWidth > featuredContainer.clientWidth,
                    cardCount: featuredContainer.querySelectorAll('.product-card').length,
                    screenWidth: window.innerWidth
                });
            }, 100);
        }
    });
    
    // Product carousel functionality
    const productCarousels = document.querySelectorAll('.product-carousel-wrapper');
    
    productCarousels.forEach(carousel => {
        const productSection = carousel.querySelector('section');
        const prevBtn = carousel.querySelector('.nav-btn.prev');
        const nextBtn = carousel.querySelector('.nav-btn.next');
        
        if (productSection && prevBtn && nextBtn) {
            const scrollAmount = 400; // Adjust scroll amount as needed
            
            prevBtn.addEventListener('click', () => {
                productSection.scrollBy({
                    left: -scrollAmount,
                    behavior: 'smooth'
                });
            });
            
            nextBtn.addEventListener('click', () => {
                productSection.scrollBy({
                    left: scrollAmount,
                    behavior: 'smooth'
                });
            });
        }
    });
});
</script>

<!-- Delivery Availability Popup -->
<?php if ($showPopup && ($popupSettings['popup_enabled'] ?? '1') == '1'): ?>
<div id="deliveryPopup" class="delivery-popup-overlay">
    <div class="delivery-popup">
        <div class="delivery-popup-header">
            <div class="delivery-logo">
                <img src="asset/images/logo.webp" alt="Demo-site Logo" class="site-logo">
            </div>
            <button class="delivery-popup-close" onclick="closeDeliveryPopup()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <div class="delivery-popup-content">
            <div class="delivery-message">
                <?php echo htmlspecialchars($popupSettings['popup_message'] ?? 'We Deliver Orders In Maharashtra, Gujarat, Bangalore, And Hyderabad Only.'); ?>
            </div>
            <div class="delivery-instruction">
                <?php echo htmlspecialchars($popupSettings['popup_instruction'] ?? 'Please Enter Your Pincode To Check Delivery Availability.'); ?>
            </div>
            
            <div class="delivery-input-section">
                <div class="delivery-input-group">
                    <input type="text" id="pincodeInput" class="delivery-pincode-input" 
                           placeholder="Enter your pincode" maxlength="6" pattern="[0-9]{6}">
                    <button type="button" class="delivery-check-btn" onclick="checkPincode()">
                        Check
                    </button>
                </div>
                <button type="button" class="delivery-start-shopping-btn" onclick="startShopping()">
                    START SHOPPING
                </button>
            </div>
            
            <div id="deliveryResult" class="delivery-result" style="display: none;">
                <div class="delivery-result-message"></div>
            </div>
        </div>
    </div>
</div>

<style>
/* Delivery Popup Styles */
.delivery-popup-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.7);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 10000;
    backdrop-filter: blur(5px);
}

.delivery-popup {
    background: white;
    border-radius: 8px;
    padding: 20px;
    max-width: 380px;
    width: 90%;
    max-height: 80vh;
    overflow-y: auto;
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
    position: relative;
    padding-top: 40px;
}

.delivery-popup-header {
    text-align: center;
    margin-bottom: 20px;
    position: relative;
}

.delivery-logo {
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 8px;
}

.site-logo {
    height: 50px;
    width: auto;
    max-width: 250px;
}

.delivery-tagline {
    font-size: 11px;
    color: #9fbe1b;
    margin-top: 3px;
    font-weight: 500;
}

.delivery-popup-close {
    position: absolute;
    top: 8px;
    right: 8px;
    background: none;
    border: none;
    font-size: 16px;
    color: #999;
    cursor: pointer;
    padding: 4px;
    border-radius: 50%;
    transition: all 0.3s ease;
    width: 24px;
    height: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 10;
}

.delivery-popup-close:hover {
    background-color: #f5f5f5;
    color: #666;
}

.delivery-popup-content {
    text-align: center;
}

.delivery-message,
.delivery-instruction {
    font-size: 13px;
    font-weight: 600;
    color: #333;
    margin-bottom: 12px;
    line-height: 1.3;
}

.delivery-input-section {
    margin-bottom: 15px;
}

.delivery-input-group {
    display: flex;
    gap: 8px;
    margin-bottom: 12px;
}

.delivery-pincode-input {
    flex: 1;
    padding: 10px 12px;
    border: 1px solid #ddd;
    border-radius: 5px;
    font-size: 13px;
    outline: none;
    transition: border-color 0.3s ease;
}

.delivery-pincode-input:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 2px rgba(0, 123, 255, 0.25);
}

.delivery-check-btn {
    padding: 10px 16px;
    background-color: #9FBF1C;
    color: white;
    border: none;
    border-radius: 5px;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.delivery-check-btn:hover {
    background-color: #9ebf1c9d;
}

.delivery-start-shopping-btn {
    width: 100%;
    padding: 10px;
    background-color: #9FBF1C;
    color: white;
    border: none;
    border-radius: 6px;
    font-size: 14px;
    font-weight: bold;
    cursor: pointer;
    transition: background-color 0.3s ease;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.delivery-start-shopping-btn:hover {
    background-color: #9FBF1C;
}

.delivery-result {
    margin-top: 15px;
    padding: 12px;
    border-radius: 6px;
    font-weight: bold;
    text-align: center;
}

.delivery-result.success {
    background-color: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.delivery-result.error {
    background-color: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

.delivery-result-message {
    font-size: 13px;
}

/* Responsive Design */
@media (max-width: 768px) {
    .delivery-popup {
        padding: 15px;
        padding-top: 35px;
        margin: 15px;
        max-width: 350px;
    }
    
    .delivery-message,
    .delivery-instruction {
        font-size: 12px;
    }
    
    .delivery-input-group {
        flex-direction: column;
    }
    
    .delivery-check-btn {
        width: 100%;
    }
    
    .site-logo {
        height: 45px;
    }
    
    .delivery-popup-close {
        top: 6px;
        right: 6px;
        font-size: 14px;
        width: 20px;
        height: 20px;
    }
}

@media (max-width: 480px) {
    .delivery-popup {
        padding: 12px;
        padding-top: 30px;
        margin: 10px;
        max-width: 320px;
    }
    
    .delivery-message,
    .delivery-instruction {
        font-size: 11px;
    }
    
    .delivery-pincode-input {
        padding: 8px 10px;
        font-size: 12px;
    }
    
    .delivery-check-btn,
    .delivery-start-shopping-btn {
        padding: 10px;
        font-size: 13px;
    }
    
    .site-logo {
        height: 40px;
    }
    
    .delivery-popup-close {
        top: 5px;
        right: 5px;
        font-size: 12px;
        width: 18px;
        height: 18px;
    }
}
</style>

<script>
// Delivery Popup JavaScript
function closeDeliveryPopup() {
    document.getElementById('deliveryPopup').style.display = 'none';
    // Mark popup as shown in session
    fetch('ajax/mark_popup_shown.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({ action: 'mark_shown' })
    })
    .then(response => {
        if (!response.ok) {
            console.error('Failed to mark popup as shown');
            // Fallback to form submission if AJAX fails
            markPopupAsShownFallback();
        } else {
            console.log('Popup marked as shown successfully');
        }
    })
    .catch(error => {
        console.error('Error marking popup as shown:', error);
        // Fallback to form submission if AJAX fails
        markPopupAsShownFallback();
    });
}

function checkPincode() {
    const pincode = document.getElementById('pincodeInput').value.trim();
    const resultDiv = document.getElementById('deliveryResult');
    const resultMessage = resultDiv.querySelector('.delivery-result-message');
    
    if (!pincode || pincode.length !== 6 || !/^\d{6}$/.test(pincode)) {
        resultDiv.className = 'delivery-result error';
        resultMessage.textContent = 'Please enter a valid 6-digit pincode.';
        resultDiv.style.display = 'block';
        return;
    }
    
    // Show loading state
    const checkBtn = document.querySelector('.delivery-check-btn');
    const originalText = checkBtn.textContent;
    checkBtn.textContent = 'Checking...';
    checkBtn.disabled = true;
    
    fetch('ajax/check_pincode.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ pincode: pincode })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            resultDiv.className = `delivery-result ${data.status}`;
            resultMessage.textContent = data.message;
        } else {
            resultDiv.className = 'delivery-result error';
            resultMessage.textContent = data.message || 'An error occurred. Please try again.';
        }
        resultDiv.style.display = 'block';
    })
    .catch(error => {
        resultDiv.className = 'delivery-result error';
        resultMessage.textContent = 'Network error. Please try again.';
        resultDiv.style.display = 'block';
    })
    .finally(() => {
        checkBtn.textContent = originalText;
        checkBtn.disabled = false;
    });
}

function startShopping() {
    closeDeliveryPopup();
    // Optionally scroll to products section
    const productsSection = document.querySelector('.discounted-products-section');
    if (productsSection) {
        productsSection.scrollIntoView({ behavior: 'smooth' });
    }
}

// Alternative method to mark popup as shown using a simple form submission
function markPopupAsShownFallback() {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = 'ajax/mark_popup_shown.php';
    form.style.display = 'none';
    
    const input = document.createElement('input');
    input.type = 'hidden';
    input.name = 'mark_shown';
    input.value = '1';
    form.appendChild(input);
    
    document.body.appendChild(form);
    form.submit();
    document.body.removeChild(form);
}

// Handle Enter key in pincode input
document.addEventListener('DOMContentLoaded', function() {
    const pincodeInput = document.getElementById('pincodeInput');
    if (pincodeInput) {
        pincodeInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                checkPincode();
            }
        });
        
        // Only allow numbers
        pincodeInput.addEventListener('input', function(e) {
            this.value = this.value.replace(/[^0-9]/g, '');
        });
    }
    
    // Handle wishlist checkbox changes
    document.querySelectorAll('.heart-checkbox').forEach(function(checkbox) {
        checkbox.addEventListener('change', function() {
            const productId = this.getAttribute('data-product-id');
            const wishlistLabel = this.nextElementSibling;
            const isChecked = this.checked;
            
            // Update icon immediately for better UX
            if (isChecked) {
                wishlistLabel.classList.add('wishlist-active');
            } else {
                wishlistLabel.classList.remove('wishlist-active');
            }
            
            // Send AJAX request
            const url = isChecked ? 'ajax/add-to-wishlist.php' : 'ajax/remove-from-wishlist.php';
            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    product_id: productId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (!data.success) {
                    // Revert icon if request failed
                    if (isChecked) {
                        wishlistLabel.classList.remove('wishlist-active');
                        checkbox.checked = false;
                    } else {
                        wishlistLabel.classList.add('wishlist-active');
                        checkbox.checked = true;
                    }
                    console.error('Wishlist operation failed:', data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                // Revert icon on error
                if (isChecked) {
                    wishlistLabel.classList.remove('wishlist-active');
                    checkbox.checked = false;
                } else {
                    wishlistLabel.classList.add('wishlist-active');
                    checkbox.checked = true;
                }
            });
        });
    });
    
    // Force pink color on page load for wishlist items
    function applyWishlistStyling() {
        console.log('applyWishlistStyling function called');
        
        // Find all wishlist labels with wishlist-active class
        const activeWishlistLabels = document.querySelectorAll('.wishlist-label.wishlist-active');
        console.log('Found active wishlist labels:', activeWishlistLabels.length);
        
        activeWishlistLabels.forEach(function(label) {
            const heartIcon = label.querySelector('.heart-icon');
            if (heartIcon) {
                console.log('Applying pink color to heart icon');
                heartIcon.style.color = '#DE0085';
                heartIcon.style.webkitTextStroke = '2px #DE0085';
                heartIcon.style.textStroke = '2px #DE0085';
                heartIcon.style.filter = 'drop-shadow(0 2px 4px rgba(222, 0, 133, 0.3))';
            }
        });
        
        // Also check for checked checkboxes
        const checkedCheckboxes = document.querySelectorAll('.heart-checkbox:checked');
        console.log('Found checked checkboxes:', checkedCheckboxes.length);
        
        checkedCheckboxes.forEach(function(checkbox) {
            const label = checkbox.nextElementSibling;
            if (label && label.classList.contains('wishlist-label')) {
                const heartIcon = label.querySelector('.heart-icon');
                if (heartIcon) {
                    console.log('Applying pink color to heart icon from checkbox');
                    heartIcon.style.color = '#DE0085';
                    heartIcon.style.webkitTextStroke = '2px #DE0085';
                    heartIcon.style.textStroke = '2px #DE0085';
                    heartIcon.style.filter = 'drop-shadow(0 2px 4px rgba(222, 0, 133, 0.3))';
                }
            }
        });
    }
    
    // Apply styling immediately
    console.log('Applying wishlist styling...');
    applyWishlistStyling();
    
    // Also apply styling after a short delay to ensure all elements are loaded
    setTimeout(function() {
        console.log('Applying wishlist styling after 100ms...');
        applyWishlistStyling();
    }, 100);
    setTimeout(function() {
        console.log('Applying wishlist styling after 500ms...');
        applyWishlistStyling();
    }, 500);
});
</script>
<?php endif; ?>

<!-- Force wishlist styling on page load - always runs -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Wishlist styling script loaded');
    
    function forceWishlistStyling() {
        console.log('forceWishlistStyling function called');
        
        // Find all wishlist labels with wishlist-active class
        const activeWishlistLabels = document.querySelectorAll('.wishlist-label.wishlist-active');
        console.log('Found active wishlist labels:', activeWishlistLabels.length);
        
        activeWishlistLabels.forEach(function(label) {
            const heartIcon = label.querySelector('.heart-icon');
            if (heartIcon) {
                console.log('Applying pink color to heart icon');
                heartIcon.style.color = '#DE0085';
                heartIcon.style.webkitTextStroke = '2px #DE0085';
                heartIcon.style.textStroke = '2px #DE0085';
                heartIcon.style.filter = 'drop-shadow(0 2px 4px rgba(222, 0, 133, 0.3))';
            }
        });
        
        // Also check for checked checkboxes
        const checkedCheckboxes = document.querySelectorAll('.heart-checkbox:checked');
        console.log('Found checked checkboxes:', checkedCheckboxes.length);
        
        checkedCheckboxes.forEach(function(checkbox) {
            const label = checkbox.nextElementSibling;
            if (label && label.classList.contains('wishlist-label')) {
                const heartIcon = label.querySelector('.heart-icon');
                if (heartIcon) {
                    console.log('Applying pink color to heart icon from checkbox');
                    heartIcon.style.color = '#DE0085';
                    heartIcon.style.webkitTextStroke = '2px #DE0085';
                    heartIcon.style.textStroke = '2px #DE0085';
                    heartIcon.style.filter = 'drop-shadow(0 2px 4px rgba(222, 0, 133, 0.3))';
                }
            }
        });
        
        // Debug: Check all wishlist elements
        const allWishlistLabels = document.querySelectorAll('.wishlist-label');
        console.log('Total wishlist labels found:', allWishlistLabels.length);
        
        allWishlistLabels.forEach(function(label, index) {
            const checkbox = label.previousElementSibling;
            const isChecked = checkbox && checkbox.checked;
            const hasActiveClass = label.classList.contains('wishlist-active');
            console.log(`Wishlist label ${index}: checked=${isChecked}, active=${hasActiveClass}`);
        });
    }
    
    // Apply styling immediately
    console.log('Applying wishlist styling immediately...');
    forceWishlistStyling();
    
    // Also apply styling after delays to ensure all elements are loaded
    setTimeout(function() {
        console.log('Applying wishlist styling after 100ms...');
        forceWishlistStyling();
    }, 100);
    
    setTimeout(function() {
        console.log('Applying wishlist styling after 500ms...');
        forceWishlistStyling();
    }, 500);
    
    setTimeout(function() {
        console.log('Applying wishlist styling after 1000ms...');
        forceWishlistStyling();
    }, 1000);
    
    // Test function to manually add an item to wishlist
    window.testWishlist = function() {
        console.log('Testing wishlist functionality...');
        
        // Find the first wishlist checkbox and check it
        const firstCheckbox = document.querySelector('.heart-checkbox');
        if (firstCheckbox) {
            firstCheckbox.checked = true;
            const label = firstCheckbox.nextElementSibling;
            if (label) {
                label.classList.add('wishlist-active');
                const heartIcon = label.querySelector('.heart-icon');
                if (heartIcon) {
                    heartIcon.style.color = '#DE0085';
                    heartIcon.style.webkitTextStroke = '2px #DE0085';
                    heartIcon.style.textStroke = '2px #DE0085';
                    heartIcon.style.filter = 'drop-shadow(0 2px 4px rgba(222, 0, 133, 0.3))';
                    console.log('Manually applied pink color to first heart icon');
                }
            }
        }
    };
    
    // Add a test button to the page
    const testButton = document.createElement('button');
    testButton.textContent = 'Test Wishlist';
    testButton.style.cssText = 'position: fixed; top: 10px; right: 10px; z-index: 10000; background: red; color: white; padding: 10px; border: none; cursor: pointer;';
    testButton.onclick = window.testWishlist;
    document.body.appendChild(testButton);
});
</script>

</body>
</html> 