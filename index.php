<?php
$pageTitle = 'Home';
require_once 'includes/header.php';

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
}

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
            <img src="./asset/images/work-progress.webp" alt="Arrow">
        </div>
        <div class="step">
            <img src="./asset/images/work-2.webp" alt="Warehouse">
            <p>WAREHOUSE</p>
        </div>
        <div class="arrow">
            <img src="./asset/images/work-progress.webp" alt="Arrow">
        </div>
        <div class="step">
            <img src="./asset/images/work-3.webp" alt="Shipping">
            <p>SHIPPING</p>
        </div>
        <div class="arrow">
            <img src="./asset/images/work-progress.webp" alt="Arrow">
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
        <h2 class="categories-title">Popular Categories</h2>
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
            <a href="products.php?discounted=1" class="view-all-link">View All<span> &gt;</span></a>
        </div>
        <div class="discounted-products-slider-wrapper">
            <button class="discounted-nav-btn prev-btn" aria-label="Scroll Left">&#8249;</button>
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
                <div class="product-image">
                    <a href="product.php?slug=<?php echo $product['slug']; ?>">
                        <?php if (!empty($product['main_image']) && file_exists('./' . $product['main_image'])): ?>
                            <img src="./<?php echo $product['main_image']; ?>" alt="<?php echo cleanProductName($product['name']); ?>">
                        <?php else: ?>
                            <div style="background: #f8f9fa; height: 200px; display: flex; align-items: center; justify-content: center; border: 1px dashed #dee2e6;">
                                <small style="color: #6c757d;">Image not found: <?php echo $product['main_image'] ?? 'No image path'; ?></small>
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
                        <div class="wishlist">
                            <input type="checkbox" class="heart-checkbox" id="wishlist-checkbox-discounted-<?php echo $product['id']; ?>" data-product-id="<?php echo $product['id']; ?>" <?php if ($inWishlist) echo 'checked'; ?>>
                            <label for="wishlist-checkbox-discounted-<?php echo $product['id']; ?>" class="wishlist-label"><i class="fas fa-heart"></i></label>
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
        <?php endforeach; ?>
</div>
            <button class="discounted-nav-btn next-btn" aria-label="Scroll Right">&#8250;</button>
</div>
    </div>
</section>

<!-- Featured Products Section -->
<section class="featured-products-section">
    <div class="featured-products-card">
        <div class="featured-products-header">
            <h2 class="featured-products-title">Top 100 Products with Higher Discounts</h2>
            <a href="products.php?featured=1" class="view-all-link">View All<span> &gt;</span></a>
        </div>
        <div class="featured-products-slider-wrapper">
            <button class="featured-nav-btn prev-btn" aria-label="Scroll Left">&#8249;</button>
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
                <div class="product-image">
                    <a href="product.php?slug=<?php echo $product['slug']; ?>">
                        <?php if (!empty($product['main_image']) && file_exists('./' . $product['main_image'])): ?>
                            <img src="./<?php echo $product['main_image']; ?>" alt="<?php echo cleanProductName($product['name']); ?>">
                        <?php else: ?>
                            <div style="background: #f8f9fa; height: 200px; display: flex; align-items: center; justify-content: center; border: 1px dashed #dee2e6;">
                                <small style="color: #6c757d;">Image not found: <?php echo $product['main_image'] ?? 'No image path'; ?></small>
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
                        <div class="wishlist">
                            <input type="checkbox" class="heart-checkbox" id="wishlist-checkbox-featured-<?php echo $product['id']; ?>" data-product-id="<?php echo $product['id']; ?>" <?php if ($inWishlist) echo 'checked'; ?>>
                            <label for="wishlist-checkbox-featured-<?php echo $product['id']; ?>" class="wishlist-label"><i class="fas fa-heart"></i></label>
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
        <?php endforeach; ?>
</div>
            <button class="featured-nav-btn next-btn" aria-label="Scroll Right">&#8250;</button>
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
  color: #333;
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
  width: 160px;
  min-width: 160px;
  scroll-snap-align: start;
  text-align: center;
}

.category-item a {
  text-decoration: none;
  color: inherit;
  display: block;
}

.category-illustration {
  width: 140px;
  height: 140px;
  margin: 0 auto 8px;
  background: #f8f9fa;
  border-radius: 8px;
  border: 1px solid #e9ecef;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  transition: transform 0.2s ease, box-shadow 0.2s ease;
  overflow: hidden;
}

.category-illustration:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
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
  font-size: 12px;
  font-weight: 600;
  color: #333;
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
  background: #f5f5f5;
  margin: 0;
  overflow: visible;
}

.discounted-products-card {
  background: rgb(255, 216, 192);
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

.discounted-products-title {
  font-size: 16px;
  font-weight: bold;
  color: #333;
  margin: 0;
}

.view-all-link {
  text-decoration: none;
  color: #16BAE4;
  font-weight: 600;
  font-size: 14px;
  transition: color 0.2s ease;
}

.view-all-link:hover {
  color: #0d8ba8;
}

.discounted-products-slider-wrapper {
  display: flex;
  align-items: center;
  position: relative;
  overflow: visible;
  padding: 0 15px;
  margin: 0 auto;
  width: 100%;
  max-width: 1400px;
}

/* Desktop: Show 4 full cards + 0.5 card */
@media (min-width: 1200px) {
  .discounted-products-container {
    width: calc(4.5 * 280px + 4 * 12px) !important; /* 4.5 cards + gaps */
    max-width: calc(4.5 * 280px + 4 * 12px) !important;
  }
}

/* Large tablet: Show 3.5 cards */
@media (min-width: 992px) and (max-width: 1199px) {
  .discounted-products-container {
    width: calc(3.5 * 280px + 3 * 12px) !important;
    max-width: calc(3.5 * 280px + 3 * 12px) !important;
  }
}

/* Medium tablet: Show 2.5 cards */
@media (min-width: 768px) and (max-width: 991px) {
  .discounted-products-container {
    width: calc(2.5 * 280px + 2 * 12px) !important;
    max-width: calc(2.5 * 280px + 2 * 12px) !important;
  }
  .discounted-products-container .card.product-card {
    flex: 0 0 260px !important;
    width: 260px !important;
    min-width: 260px !important;
    max-width: 260px !important;
  }
  .discounted-products-container {
    width: calc(2.5 * 260px + 2 * 12px) !important;
    max-width: calc(2.5 * 260px + 2 * 12px) !important;
  }
}

/* Small tablet: Show 1.8 cards */
@media (min-width: 576px) and (max-width: 767px) {
  .discounted-products-container {
    width: calc(1.8 * 240px + 1 * 12px) !important;
    max-width: calc(1.8 * 240px + 1 * 12px) !important;
  }
  .discounted-products-container .card.product-card {
    flex: 0 0 240px !important;
    width: 240px !important;
    min-width: 240px !important;
    max-width: 240px !important;
  }
}

/* Mobile: Show 1.3 cards */
@media (max-width: 575px) {
  .discounted-products-container {
    width: calc(1.3 * 220px + 0.3 * 12px) !important;
    max-width: calc(1.3 * 220px + 0.3 * 12px) !important;
  }
  .discounted-products-container .card.product-card {
    flex: 0 0 220px !important;
    width: 220px !important;
    min-width: 220px !important;
    max-width: 220px !important;
  }
}

.discounted-products-container {
  display: flex !important;
  gap: 12px;
  overflow-x: auto !important;
  overflow-y: hidden !important;
  scroll-behavior: smooth;
  padding: 10px 5px;
  -webkit-overflow-scrolling: touch;
  scrollbar-width: none;
  flex: 1;
  /* Ensure proper containment */
  position: relative;
  width: 100%;
  max-width: 100%;
  /* Force container to not wrap */
  flex-wrap: nowrap !important;
  /* Remove any text formatting */
  white-space: normal !important;
  /* Box model */
  box-sizing: border-box;
  /* Ensure container takes full width when not constrained by breakpoints */
  min-width: 100%;
}

/* Ensure product cards in discounted section maintain their width */
.discounted-products-container .card.product-card {
  flex: 0 0 280px !important;
  width: 280px !important;
  min-width: 280px !important;
  max-width: 280px !important;
  flex-shrink: 0 !important;
  flex-grow: 0 !important;
  margin: 0 !important;
  box-sizing: border-box !important;
}

.discounted-products-container::-webkit-scrollbar {
  display: none;
}

.discounted-nav-btn {
  background: #ffffff;
  border: none;
  color: #16BAE4;
  font-size: 24px;
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
  color: #16BAE4;
  border-color: #16BAE4;
  box-shadow: 0 4px 12px rgba(22, 186, 228, 0.3);
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
  border-radius: 50%;
  background: #ffffff;
  z-index: -1;
}

/* Force discounted arrow visibility */
.discounted-nav-btn {
  color: #16BAE4 !important;
  background: #ffffff !important;
  border: 1px solid #e0e0e0 !important;
  cursor: pointer !important;
  pointer-events: auto !important;
  position: absolute !important;
  z-index: 100 !important;
}

/* Ensure discounted arrows are clickable */
.discounted-nav-btn:hover {
  cursor: pointer !important;
  pointer-events: auto !important;
  background: #f8f9fa !important;
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
    width: 140px;
    min-width: 140px;
  }
  
  .category-illustration {
    width: 120px;
    height: 120px;
    margin-bottom: 6px;
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
    font-size: 11px;
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
    width: 130px;
    min-width: 130px;
  }
  
  .category-illustration {
    width: 110px;
    height: 110px;
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
  }
  
  .discounted-products-slider-wrapper {
    padding: 0 50px;
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
    color: #16BAE4 !important;
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
    padding: 0 55px;
    max-width: 100%;
  }
  
  .discounted-products-container .card.product-card {
    flex: 0 0 280px !important;
    width: 280px !important;
    min-width: 280px !important;
    max-width: 280px !important;
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
    padding: 0 52px;
    max-width: 100%;
  }
  
  .discounted-products-container .card.product-card {
    flex: 0 0 240px !important;
    width: 240px !important;
    min-width: 240px !important;
    max-width: 240px !important;
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
  background-color: #f5f5f5;
  margin: 0;
  overflow: visible;
}

.featured-products-card {
  background: rgb(206, 229, 239);
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
  color: #333;
  margin: 0;
}

.featured-products-slider-wrapper {
  display: flex;
  align-items: center;
  position: relative;
  overflow: visible;
  padding: 0 15px;
  margin: 0 auto;
  width: 100%;
  max-width: 1400px;
}

/* Desktop: Show 4 full cards + 0.5 card for featured products */
@media (min-width: 1200px) {
  .featured-products-container {
    width: calc(4.5 * 280px + 4 * 12px) !important; /* 4.5 cards + gaps */
    max-width: calc(4.5 * 280px + 4 * 12px) !important;
  }
}

/* Large tablet: Show 3.5 cards for featured products */
@media (min-width: 992px) and (max-width: 1199px) {
  .featured-products-container {
    width: calc(3.5 * 280px + 3 * 12px) !important;
    max-width: calc(3.5 * 280px + 3 * 12px) !important;
  }
}

/* Medium tablet: Show 2.5 cards for featured products */
@media (min-width: 768px) and (max-width: 991px) {
  .featured-products-container {
    width: calc(2.5 * 280px + 2 * 12px) !important;
    max-width: calc(2.5 * 280px + 2 * 12px) !important;
  }
  .featured-products-container .card.product-card {
    flex: 0 0 260px !important;
    width: 260px !important;
    min-width: 260px !important;
    max-width: 260px !important;
  }
  .featured-products-container {
    width: calc(2.5 * 260px + 2 * 12px) !important;
    max-width: calc(2.5 * 260px + 2 * 12px) !important;
  }
}

/* Small tablet: Show 1.8 cards for featured products */
@media (min-width: 576px) and (max-width: 767px) {
  .featured-products-container {
    width: calc(1.8 * 240px + 1 * 12px) !important;
    max-width: calc(1.8 * 240px + 1 * 12px) !important;
  }
  .featured-products-container .card.product-card {
    flex: 0 0 240px !important;
    width: 240px !important;
    min-width: 240px !important;
    max-width: 240px !important;
  }
}

/* Mobile: Show 1.3 cards for featured products */
@media (max-width: 575px) {
  .featured-products-container {
    width: calc(1.3 * 220px + 0.3 * 12px) !important;
    max-width: calc(1.3 * 220px + 0.3 * 12px) !important;
  }
  .featured-products-container .card.product-card {
    flex: 0 0 220px !important;
    width: 220px !important;
    min-width: 220px !important;
    max-width: 220px !important;
  }
}

.featured-products-container {
  display: flex !important;
  gap: 12px;
  overflow-x: auto !important;
  overflow-y: hidden !important;
  scroll-behavior: smooth;
  padding: 10px 5px;
  -webkit-overflow-scrolling: touch;
  scrollbar-width: none;
  flex: 1;
  /* Ensure proper containment */
  position: relative;
  width: 100%;
  max-width: 100%;
  /* Force container to not wrap */
  flex-wrap: nowrap !important;
  /* Remove any text formatting */
  white-space: normal !important;
  /* Box model */
  box-sizing: border-box;
  /* Ensure container takes full width when not constrained by breakpoints */
  min-width: 100%;
}

/* Ensure product cards in featured section maintain their width */
.featured-products-container .card.product-card {
  flex: 0 0 280px !important;
  width: 280px !important;
  min-width: 280px !important;
  max-width: 280px !important;
  flex-shrink: 0 !important;
  flex-grow: 0 !important;
  margin: 0 !important;
  box-sizing: border-box !important;
}

.featured-products-container::-webkit-scrollbar {
  display: none;
}

.featured-nav-btn {
  background: #ffffff !important;
  /* border-radius: 50% !important; */
  color: #16BAE4 !important;
  font-size: 24px !important;
  font-weight: bold !important;
  cursor: pointer !important;
  width: 30px !important;
  height: 40px !important;
  position: absolute !important;
  top: 50% !important;
  transform: translateY(-50%) !important;
  z-index: 1000 !important;
  display: flex !important;
  align-items: center !important;
  justify-content: center !important;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2) !important;
  transition: all 0.3s ease !important;
  opacity: 1 !important;
  pointer-events: auto !important;
  text-decoration: none !important;
  line-height: 1 !important;
  user-select: none !important;
  border: none !important;
}

.featured-nav-btn:hover {
  background: #ffffff !important;
  color: #16BAE4 !important;
  border-color: #16BAE4 !important;
  box-shadow: 0 4px 12px rgba(22, 186, 228, 0.3) !important;
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
    padding: 0 50px;
    max-width: 100%;
  }
  
  .featured-products-container .card.product-card {
    flex: 0 0 220px !important;
    width: 220px !important;
    min-width: 220px !important;
    max-width: 220px !important;
  }
  
  .featured-nav-btn {
    width: 44px !important;
    height: 44px !important;
    font-size: 20px !important;
    color: #16BAE4 !important;
    background: #ffffff !important;
    border: 2px solid #16BAE4 !important;
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
    padding: 0 55px;
    max-width: 100%;
  }
  
  .featured-products-container .card.product-card {
    flex: 0 0 280px !important;
    width: 280px !important;
    min-width: 280px !important;
    max-width: 280px !important;
  }
  
  .featured-nav-btn {
    width: 48px !important;
    height: 48px !important;
    font-size: 22px !important;
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
    padding: 0 52px;
    max-width: 100%;
  }
  
  .featured-products-container .card.product-card {
    flex: 0 0 240px !important;
    width: 240px !important;
    min-width: 240px !important;
    max-width: 240px !important;
  }
  
  .featured-nav-btn {
    width: 46px !important;
    height: 46px !important;
    font-size: 21px !important;
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
    padding: 0 40px !important;
  }
}

@media (max-width: 480px) {
  .discounted-products-card,
  .featured-products-card {
    margin: 0 5px !important;
  }
  
  .discounted-products-slider-wrapper,
  .featured-products-slider-wrapper {
    padding: 0 35px !important;
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
  width: 20px !important;
  height: 20px !important;
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
                return 232; // 220px card + 12px gap
            } else if (width <= 767) {
                return 252; // 240px card + 12px gap
            } else if (width <= 991) {
                return 272; // 260px card + 12px gap
            } else {
                return 292; // 280px card + 12px gap
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
                return 232; // 220px card + 12px gap
            } else if (width <= 767) {
                return 252; // 240px card + 12px gap
            } else if (width <= 991) {
                return 272; // 260px card + 12px gap
            } else {
                return 292; // 280px card + 12px gap
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

</body>
</html> 