<?php
$pageTitle = 'EverythingB2C';
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



// Filter to only main categories (parent_id is NULL)
$main_categories = array_filter($categories, function($cat) { return empty($cat['parent_id']); });
?>

<!-- Hero Section -->
<section class="hero-slider-section">
    <div id="heroCarousel" class="custom-carousel">
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img src="asset/images/b1.webp" alt="Banner 1" class="carousel-image">
                <div class="carousel-caption d-block text-start">
                    <!-- Optional caption content -->
                </div>
            </div>
            <div class="carousel-item">
                <img src="asset/images/b2.webp" alt="Banner 2" class="carousel-image">
                <div class="carousel-caption d-block text-end">
                    <!-- Optional caption content -->
                </div>
            </div>
            <div class="carousel-item">
                <img src="asset/images/b3.webp" alt="Banner 3" class="carousel-image">
                <div class="carousel-caption d-block text-end">
                    <!-- Optional caption content -->
                </div>
            </div>
        </div>
        <!-- Carousel Controls -->
        <button class="carousel-control-prev" type="button" aria-label="Previous">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" aria-label="Next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
        <!-- Carousel Indicators -->
        <div class="carousel-indicators">
            <button type="button" class="indicator active" data-slide="0" aria-current="true" aria-label="Slide 1"></button>
            <button type="button" class="indicator" data-slide="1" aria-label="Slide 2"></button>
            <button type="button" class="indicator" data-slide="2" aria-label="Slide 3"></button>
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
            <img src="./asset/images/work-4.webp" alt="Home Delivery">
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
            <button class="category-nav-btn prev-btn" aria-label="Scroll Left">
                <img src="asset/icons/blue_arrow.png" alt="Previous" style="width: 20px; height: 10px;">
            </button>
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
            <button class="category-nav-btn next-btn" aria-label="Scroll Right">
                <img src="asset/icons/blue_arrow.png" alt="Next" style="transform: rotate(180deg); width: 20px; height: 10px;">
            </button>
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
                            <button class="add-to-cart add-to-cart-btn" data-product-id="<?php echo $product['id']; ?>">
                                <i class="fas fa-shopping-cart" style="margin-right: 6px; transform: scaleX(-1); font-size: 18px;"></i>
                                ADD TO CART
                            </button>
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
                            <button class="add-to-cart add-to-cart-btn" data-product-id="<?php echo $product['id']; ?>">
                                <i class="fas fa-shopping-cart" style="margin-right: 6px; transform: scaleX(-1); font-size: 18px;"></i>
                                ADD TO CART
                            </button>
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
  <div class="container">
    <div class="service-cards">
      
      <div class="service-card free-shipping">
        <div class="service-inner">
          <div class="service-icon">
            <img src="asset/icons/free_shipping.png" alt="Free Shipping">
          </div>
          <div class="service-divider"></div>
          <div class="service-text">
            <div class="service-title">FREE</div>
            <div class="service-subtitle">Shipping</div>
          </div>
        </div>
      </div>

      <div class="service-card lower-price">
        <div class="service-inner">
          <div class="service-icon">
            <img src="asset/icons/lower_price_icon.png" alt="Lower Price">
          </div>
          <div class="service-divider"></div>
          <div class="service-text">
            <div class="service-title">LOWER</div>
            <div class="service-subtitle">Price</div>
          </div>
        </div>
      </div>

      <div class="service-card cod-service">
        <div class="service-inner">
          <div class="service-icon">
            <img src="asset/icons/COD.png" alt="COD Service">
          </div>
          <div class="service-divider"></div>
          <div class="service-text">
            <div class="service-title">COD</div>
            <div class="service-subtitle">Services</div>
          </div>
        </div>
      </div>

      <div class="service-card return-policy">
        <div class="service-inner">
          <div class="service-icon">
            <img src="asset/icons/return_policy.png" alt="Return Policy">
          </div>
          <div class="service-divider"></div>
          <div class="service-text">
            <div class="service-title">RETURN</div>
            <div class="service-subtitle">Policy</div>
          </div>
        </div>
      </div>

    </div>
  </div>
</section>

<!-- Include Footer -->

<!-- Back to Top Button -->
<button onclick="topFunction()" id="backToTopBtn" title="Go to top" aria-label="Scroll to top" style="display: none !important; position: fixed !important; bottom: 30px !important; right: 30px !important; z-index: 99999 !important; background: linear-gradient(135deg, #9ACD32, #7cb342) !important; color: white !important; border: none !important; border-radius: 4px !important; width: 30px !important; height: 30px !important; cursor: pointer !important; box-shadow: 0 4px 12px rgba(154, 205, 50, 0.3) !important; align-items: center !important; justify-content: center !important; font-size: 18px !important; opacity: 0 !important; visibility: hidden !important;">
  <i class="fas fa-chevron-up"></i>
</button>

<script>
// Scroll-to-top functionality
document.addEventListener('DOMContentLoaded', function() {
    const backToTopBtn = document.getElementById('backToTopBtn');
    
    if (!backToTopBtn) return;
    
    // Function to show/hide button based on scroll
    function toggleScrollButton() {
        const scrollTop1 = window.pageYOffset;
        const scrollTop2 = document.documentElement.scrollTop;
        const scrollTop3 = document.body.scrollTop;
        const scrollTop4 = window.scrollY;
        
        const scrollTop = scrollTop1 || scrollTop2 || scrollTop3 || scrollTop4 || 0;
        const showThreshold = 300;
        
        if (scrollTop > showThreshold) {
            backToTopBtn.style.setProperty('display', 'flex', 'important');
            backToTopBtn.style.setProperty('opacity', '1', 'important');
            backToTopBtn.style.setProperty('visibility', 'visible', 'important');
        } else {
            backToTopBtn.style.setProperty('display', 'none', 'important');
            backToTopBtn.style.setProperty('opacity', '0', 'important');
            backToTopBtn.style.setProperty('visibility', 'hidden', 'important');
        }
    }
    
    // Function to scroll to top
    function scrollToTop(e) {
        if (e) {
            e.preventDefault();
            e.stopPropagation();
        }
        
        function smoothScrollToTop() {
            const startPosition = document.body.scrollTop || window.pageYOffset || document.documentElement.scrollTop;
            const targetPosition = 0;
            const distance = targetPosition - startPosition;
            const duration = 1500;
            let start = null;
            
            function animation(currentTime) {
                if (start === null) start = currentTime;
                const timeElapsed = currentTime - start;
                const run = easeInOutCubic(timeElapsed, startPosition, distance, duration);
                
                window.scrollTo(0, run);
                document.body.scrollTop = run;
                document.documentElement.scrollTop = run;
                
                if (timeElapsed < duration) requestAnimationFrame(animation);
            }
            
            function easeInOutCubic(t, b, c, d) {
                t /= d / 2;
                if (t < 1) return c / 2 * t * t * t + b;
                t -= 2;
                return c / 2 * (t * t * t + 2) + b;
            }
            
            requestAnimationFrame(animation);
        }
        
        if ('scrollBehavior' in document.documentElement.style) {
            smoothScrollToTop();
        } else {
            const currentScrollPos = document.body.scrollTop || window.scrollY;
            const scrollStep = -currentScrollPos / (1500 / 16);
            const scrollInterval = setInterval(function() {
                const currentPos = document.body.scrollTop || window.scrollY;
                if (currentPos > 0) {
                    window.scrollBy(0, scrollStep);
                    document.body.scrollTop += scrollStep;
                } else {
                    clearInterval(scrollInterval);
                }
            }, 16);
        }
    }
    
    // Add event listeners
    window.addEventListener('scroll', function() {
        requestAnimationFrame(toggleScrollButton);
    }, { passive: false });
    
    document.addEventListener('scroll', function() {
        requestAnimationFrame(toggleScrollButton);
    }, { passive: false });
    
    document.body.addEventListener('scroll', function() {
        requestAnimationFrame(toggleScrollButton);
    }, { passive: false });
    
    window.addEventListener('wheel', function() {
        setTimeout(toggleScrollButton, 10);
    }, { passive: false });
    
    // Remove any existing event listeners and add a clean one
    backToTopBtn.removeEventListener('click', scrollToTop);
    backToTopBtn.addEventListener('click', scrollToTop);
    
    // Mobile positioning function for backToTopBtn
    function updateBackToTopPosition() {
        if (window.innerWidth <= 480) {
            // Extra small mobile
            backToTopBtn.style.setProperty('left', '15px', 'important');
            backToTopBtn.style.setProperty('right', 'auto', 'important');
            backToTopBtn.style.setProperty('bottom', '15px', 'important');
            backToTopBtn.style.setProperty('width', '40px', 'important');
            backToTopBtn.style.setProperty('height', '40px', 'important');
            backToTopBtn.style.setProperty('font-size', '14px', 'important');
            backToTopBtn.style.setProperty('padding', '10px', 'important');
        } else if (window.innerWidth <= 768) {
            // Mobile/tablet
            backToTopBtn.style.setProperty('left', '20px', 'important');
            backToTopBtn.style.setProperty('right', 'auto', 'important');
            backToTopBtn.style.setProperty('bottom', '20px', 'important');
            backToTopBtn.style.setProperty('width', '45px', 'important');
            backToTopBtn.style.setProperty('height', '45px', 'important');
            backToTopBtn.style.setProperty('font-size', '16px', 'important');
            backToTopBtn.style.setProperty('padding', '12px', 'important');
        } else {
            // Desktop
            backToTopBtn.style.setProperty('left', 'auto', 'important');
            backToTopBtn.style.setProperty('right', '30px', 'important');
            backToTopBtn.style.setProperty('bottom', '30px', 'important');
            backToTopBtn.style.setProperty('width', '50px', 'important');
            backToTopBtn.style.setProperty('height', '50px', 'important');
            backToTopBtn.style.setProperty('font-size', '18px', 'important');
            backToTopBtn.style.setProperty('padding', '15px', 'important');
        }
    }
    
    // Update position on load and resize
    updateBackToTopPosition();
    window.addEventListener('resize', updateBackToTopPosition);
    
    // Also update when scroll event triggers
    window.addEventListener('scroll', function() {
        setTimeout(updateBackToTopPosition, 10);
    });
    
    // Initialize button state
    toggleScrollButton();
});

// Global function for onclick attribute
function topFunction(e) {
    if (e) {
        e.preventDefault();
        e.stopPropagation();
    }
    
    function smoothScrollToTop() {
        const startPosition = document.body.scrollTop || window.pageYOffset || document.documentElement.scrollTop;
        const targetPosition = 0;
        const distance = targetPosition - startPosition;
        const duration = 1500;
        let start = null;
        
        function animation(currentTime) {
            if (start === null) start = currentTime;
            const timeElapsed = currentTime - start;
            const run = easeInOutCubic(timeElapsed, startPosition, distance, duration);
            
            window.scrollTo(0, run);
            document.body.scrollTop = run;
            document.documentElement.scrollTop = run;
            
            if (timeElapsed < duration) requestAnimationFrame(animation);
        }
        
        function easeInOutCubic(t, b, c, d) {
            t /= d / 2;
            if (t < 1) return c / 2 * t * t * t + b;
            t -= 2;
            return c / 2 * (t * t * t + 2) + b;
        }
        
        requestAnimationFrame(animation);
    }
    
    try {
        smoothScrollToTop();
    } catch (error) {
        window.scrollTo(0, 0);
        document.body.scrollTop = 0;
        document.documentElement.scrollTop = 0;
    }
}
</script>

<?php include 'includes/footer.php'; ?>

<!-- Include your existing CSS and JS files -->
<link rel="stylesheet" href="./asset/style/style.css">

<style>
/* Service Section - Redesigned */
.service-section {
  padding: 40px 0;
  background: #eee;
  border-radius: 4px;
}

.service-cards {
  width: 100%;
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 20px;
  justify-content: center;
  align-items: center;
}

.service-card {
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 8px;
  border-radius: 8px;
  width: 100%;
  height: 100px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.15);
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.service-inner {
  display: flex;
  align-items: center;
  padding: 20px 25px;
  border-radius: 6px;
  width: 100%;
  height: 100%;
  border: 2px solid rgba(255, 255, 255, 0.3);
  min-width: 0;
  overflow: hidden;
}

.service-card:hover {
  transform: translateY(-3px);
  box-shadow: 0 8px 20px rgba(0,0,0,0.2);
}

.service-icon {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 70px;
  height: 70px;
  margin-right: 15px;
}

.service-icon img {
  width: 45px;
  height: 45px;
  filter: brightness(0) invert(1);
  object-fit: contain;
}

/* Larger icons for Free Shipping and Return Policy */
.service-card.free-shipping .service-icon img,
.service-card.return-policy .service-icon img {
  width: 50px;
  height: 50px;
}

.service-divider {
  width: 1px;
  height: 45px;
  background: rgba(255, 255, 255, 0.4);
  margin: 0 15px;
}

.service-text {
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  flex: 1;
  min-width: 0;
  overflow: hidden;
}

.service-title {
  font-size: 18px;
  font-weight: 700;
  color: #ffffff;
  line-height: 1.1;
  margin-bottom: 4px;
  letter-spacing: 0.3px;
  word-wrap: break-word;
  overflow-wrap: break-word;
  hyphens: auto;
}

.service-subtitle {
  font-size: 14px;
  font-weight: 400;
  color: #ffffff;
  line-height: 1.1;
  letter-spacing: 0.2px;
  word-wrap: break-word;
  overflow-wrap: break-word;
  hyphens: auto;
}

/* Service Card Colors */
.service-card.free-shipping {
  background: #F7434C;
}

.service-card.lower-price {
  background: #FBC51B;
}

.service-card.cod-service {
  background: #9FBE1B;
}

.service-card.return-policy {
  background: #3079F1;
}

/* Specific adjustments for Return Policy card */
.service-card.return-policy .service-title {
  font-size: 16px;
  letter-spacing: 0.2px;
}

.service-card.return-policy .service-subtitle {
  font-size: 12px;
  letter-spacing: 0.1px;
}

/* Responsive Design */
@media (max-width: 1200px) {
  .service-cards {
    grid-template-columns: repeat(4, 1fr);
    gap: 15px;
  }
  
  .service-card {
    width: 100%;
    height: 90px;
    padding: 6px;
    min-width: 0;
  }
  
  .service-inner {
    padding: 18px 22px;
  }
  
  .service-icon {
    width: 60px;
    height: 60px;
    margin-right: 12px;
  }
  
  .service-icon img {
    width: 40px;
    height: 40px;
  }
  
  /* Larger icons for Free Shipping and Return Policy */
  .service-card.free-shipping .service-icon img,
  .service-card.return-policy .service-icon img {
    width: 45px;
    height: 45px;
  }
  
  .service-divider {
    height: 45px;
    margin: 0 16px;
  }
}

@media (max-width: 768px) {
  .service-section {
    padding: 30px 0;
    margin-left: 7px !important;
    margin-right: 7px !important;
  }
  
  .service-cards {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 12px;
    justify-content: center;
  }
  
  .service-card {
    width: 100%;
    max-width: none;
    height: 80px;
    padding: 6px;
    min-width: 0;
  }
  
  .service-inner {
    padding: 16px 18px;
  }
  
  .service-icon {
    width: 50px;
    height: 50px;
    margin-right: 10px;
  }
  
  .service-icon img {
    width: 35px;
    height: 35px;
  }
  
  /* Larger icons for Free Shipping and Return Policy */
  .service-card.free-shipping .service-icon img,
  .service-card.return-policy .service-icon img {
    width: 40px;
    height: 40px;
  }
  
  .service-divider {
    height: 40px;
    margin: 0 12px;
  }
  
  .service-title {
    font-size: 16px;
  }
  
  .service-subtitle {
    font-size: 12px;
  }
  
  /* Return Policy specific adjustments for mobile */
  .service-card.return-policy .service-title {
    font-size: 14px;
  }
  
  .service-card.return-policy .service-subtitle {
    font-size: 10px;
  }
}

@media (max-width: 480px) {
  .service-cards {
    grid-template-columns: repeat(2, 1fr);
    gap: 8px;
  }
  
  .service-card {
    height: 70px;
    padding: 5px;
    min-width: 0;
  }
  
  .service-inner {
    padding: 14px 16px;
  }
  
  .service-icon {
    width: 45px;
    height: 45px;
    margin-right: 8px;
  }
  
  .service-icon img {
    width: 30px;
    height: 30px;
  }
  
  /* Larger icons for Free Shipping and Return Policy */
  .service-card.free-shipping .service-icon img,
  .service-card.return-policy .service-icon img {
    width: 35px;
    height: 35px;
  }
  
  .service-divider {
    height: 35px;
    margin: 0 10px;
  }
  
  .service-title {
    font-size: 14px;
  }
  
  .service-subtitle {
    font-size: 11px;
  }
  
  /* Return Policy specific adjustments for small mobile */
  .service-card.return-policy .service-title {
    font-size: 12px;
  }
  
  .service-card.return-policy .service-subtitle {
    font-size: 9px;
  }
}

/* Very small mobile devices */
@media (max-width: 360px) {
  .service-section {
    padding: 20px 0;
    margin-left: 5px !important;
    margin-right: 5px !important;
  }
  
  .service-cards {
    grid-template-columns: repeat(2, 1fr);
    gap: 6px;
  }
  
  .service-card {
    height: 65px;
    padding: 4px;
    min-width: 0;
  }
  
  .service-inner {
    padding: 12px 14px;
  }
  
  .service-icon {
    width: 45px;
    height: 45px;
    margin-right: 8px;
  }
  
  .service-icon img {
    width: 30px;
    height: 30px;
  }
  
  /* Larger icons for Free Shipping and Return Policy */
  .service-card.free-shipping .service-icon img,
  .service-card.return-policy .service-icon img {
    width: 35px;
    height: 35px;
  }
  
  .service-divider {
    height: 30px;
    margin: 0 8px;
  }
  
  .service-title {
    font-size: 12px;
  }
  
  .service-subtitle {
    font-size: 10px;
  }
}

/* Extra small mobile devices */
@media (max-width: 320px) {
  .service-section {
    padding: 15px 0;
    margin-left: 3px !important;
    margin-right: 3px !important;
  }
  
  .service-cards {
    grid-template-columns: repeat(2, 1fr);
    gap: 4px;
  }
  
  .service-card {
    height: 60px;
    padding: 3px;
    min-width: 0;
  }
  
  .service-inner {
    padding: 10px 11px;
  }
  
  .service-icon {
    width: 40px;
    height: 40px;
    margin-right: 6px;
  }
  
  .service-icon img {
    width: 25px;
    height: 25px;
  }
  
  /* Larger icons for Free Shipping and Return Policy */
  .service-card.free-shipping .service-icon img,
  .service-card.return-policy .service-icon img {
    width: 30px;
    height: 30px;
  }
  
  .service-divider {
    height: 25px;
    margin: 0 6px;
  }
  
  .service-title {
    font-size: 12px;
  }
  
  .service-subtitle {
    font-size: 10px;
  }
}

/* Popular Categories Section - Modern Design */
.popular-categories-section {
  padding: 20px;
  background: #f5f5f5;
  margin-top: 0;
}

.categories-card {
  background: #fff;
  border-radius: 8px;
  border: 1px solid #e0e0e0;
  padding: 20px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  position: relative;
}

/* Responsive adjustments for categories card */
@media (max-width: 767px) {
  .categories-card {
    padding: 15px;
  }
}

@media (max-width: 480px) {
  .categories-card {
    padding: 10px;
  }
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
  padding: 0 8px;
  margin: 0 -10px;
}

/* Responsive adjustments for categories slider wrapper */
@media (max-width: 767px) {
  .categories-slider-wrapper {
    padding: 0 8px;
    margin: 0 -5px;
  }
}

@media (max-width: 480px) {
  .categories-slider-wrapper {
    padding: 0 8px;
    margin: 0;
  }
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
  align-items: flex-start;
  flex-wrap: nowrap;
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
  /* margin-right: 8px; */
  box-sizing: border-box;
  overflow: hidden;
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
  max-width: 100%;
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
  color: #6c757d;
  margin: 0;
  line-height: 1.2;
  text-align: center;
}

.category-nav-btn {
  background: #ffffff;
  border: none;
  color: var(--light-blue);
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

.category-nav-btn:hover {
  background: #ffffff;
  color: var(--dark-blue);
  border-color: var(--light-blue);
  box-shadow: 0 4px 12px rgba(206, 229, 239, 0.3);
  transform: translateY(-50%) scale(1.05);
}

.category-nav-btn:active {
  transform: translateY(-50%) scale(0.95);
}

.category-nav-btn.prev-btn {
  left: 10px;
  z-index: 10;
}

.category-nav-btn.next-btn {
  right: 10px;
  z-index: 10;
}

/* Responsive Categories Section */
/* Desktop: Show as many categories as possible (current behavior) */
@media (min-width: 1200px) {
  .categories-container {
    gap: 16px;
  }
  
  .category-item {
    width: 220px;
    min-width: 220px;
  }
  
  .category-illustration {
    width: 200px;
    height: 200px;
  }
  
  .category-label {
    font-size: 14px;
  }
}

/* Tablet: Show at least 5 categories */
@media (min-width: 768px) and (max-width: 1199px) {
  .categories-container {
    gap: 6px !important;
    padding: 0 10px !important;
  }
  
  .category-item {
    width: 140px !important;
    min-width: 140px !important;
    /* margin-right: 4px !important; */
    flex-shrink: 0 !important;
  }
  
  .category-illustration {
    width: 120px !important;
    height: 120px !important;
    margin: 0 auto 6px !important;
  }
  
  .category-label {
    font-size: 11px !important;
    font-weight: 600 !important;
  }
  
  /* Ensure 5 categories are visible */
  .categories-slider-wrapper {
    padding: 0 8px !important;
  }
}

/* Mobile: Show at least 4 categories */
@media (max-width: 767px) {
  .categories-container {
    gap: 4px !important;
    padding: 8px 0px !important;
    overflow-x: auto !important;
    scroll-behavior: smooth !important;
  }
  
  .category-item {
    width: 120px !important;
    min-width: 120px !important;
    /* margin-right: 4px !important; */
    flex-shrink: 0 !important;
  }
  
  .category-illustration {
    width: 100px !important;
    height: 100px !important;
    margin: 0 auto 6px !important;
  }
  
  .category-label {
    font-size: 10px !important;
    font-weight: 600 !important;
    line-height: 1.1 !important;
    word-wrap: break-word !important;
    max-width: 100% !important;
  }
  
  /* Ensure 4 categories are visible */
  .categories-slider-wrapper {
    padding: 0 8px !important;
    position: relative !important;
  }
  
  /* Adjust navigation buttons for mobile */
  .category-nav-btn,
  .discounted-nav-btn,
  .featured-nav-btn {
    width: 20px !important;
    height: 20px !important;
    top: 50% !important;
    transform: translateY(-50%) !important;
    background: #ffffff !important;
    border: 1px solid #e0e0e0 !important;
    box-shadow: 0 2px 6px rgba(0,0,0,0.1) !important;
    z-index: 100 !important;
  }
  
  .category-nav-btn.prev-btn,
  .discounted-nav-btn.prev-btn,
  .featured-nav-btn.prev-btn {
    left: 5px !important;
  }
  
  .category-nav-btn.next-btn,
  .discounted-nav-btn.next-btn,
  .featured-nav-btn.next-btn {
    right: 5px !important;
  }
  
  /* Adjust navigation button images for mobile */
  .category-nav-btn img,
  .discounted-nav-btn img,
  .featured-nav-btn img {
    width: 10px !important;
    height: 10px !important;
  }
}

/* Small Mobile: Optimize for very small screens */
@media (max-width: 480px) {
  .categories-container {
    gap: 3px !important;
    padding: 8px 0px !important;
  }
  
  .category-item {
    width: 100px !important;
    min-width: 100px !important;
    /* margin-right: 3px !important; */
    flex-shrink: 0 !important;
  }
  
  .category-illustration {
    width: 80px !important;
    height: 80px !important;
    margin: 0 auto 5px !important;
  }
  
  .category-label {
    font-size: 9px !important;
    font-weight: 600 !important;
    word-wrap: break-word !important;
    max-width: 100% !important;
    line-height: 1.1 !important;
  }
  
  .categories-slider-wrapper {
    padding: 0 8px !important;
    position: relative !important;
  }
  
  .category-nav-btn,
  .discounted-nav-btn,
  .featured-nav-btn {
    width: 18px !important;
    height: 18px !important;
    top: 50% !important;
    transform: translateY(-50%) !important;
    background: #ffffff !important;
    border: 1px solid #e0e0e0 !important;
    box-shadow: 0 2px 6px rgba(0,0,0,0.1) !important;
    z-index: 100 !important;
  }
  
  .category-nav-btn.prev-btn,
  .discounted-nav-btn.prev-btn,
  .featured-nav-btn.prev-btn {
    left: 3px !important;
  }
  
  .category-nav-btn.next-btn,
  .discounted-nav-btn.next-btn,
  .featured-nav-btn.next-btn {
    right: 3px !important;
  }
  
  /* Adjust navigation button images for small mobile */
  .category-nav-btn img,
  .discounted-nav-btn img,
  .featured-nav-btn img {
    width: 10px !important;
    height: 10px !important;
  }
}

/* Extra small screens - ensure no overlapping */
@media (max-width: 360px) {
  .categories-container {
    gap: 2px !important;
    padding: 8px 0px !important;
  }
  
  .category-item {
    width: 85px !important;
    min-width: 85px !important;
    /* margin-right: 2px !important; */
    flex-shrink: 0 !important;
  }
  
  .category-illustration {
    width: 70px !important;
    height: 70px !important;
    margin: 0 auto 4px !important;
  }
  
  .category-label {
    font-size: 8px !important;
    font-weight: 600 !important;
  }
  
  .categories-slider-wrapper {
    padding: 0 8px !important;
  }
  
  .category-nav-btn,
  .discounted-nav-btn,
  .featured-nav-btn {
    width: 16px !important;
    height: 16px !important;
    top: 50% !important;
    transform: translateY(-50%) !important;
    background: #ffffff !important;
    border: 1px solid #e0e0e0 !important;
    box-shadow: 0 2px 6px rgba(0,0,0,0.1) !important;
    z-index: 100 !important;
  }
  
  .category-nav-btn.prev-btn,
  .discounted-nav-btn.prev-btn,
  .featured-nav-btn.prev-btn {
    left: 2px !important;
  }
  
  .category-nav-btn.next-btn,
  .discounted-nav-btn.next-btn,
  .featured-nav-btn.next-btn {
    right: 2px !important;
  }
  
  /* Adjust navigation button images for ultra small mobile */
  .category-nav-btn img,
  .discounted-nav-btn img,
  .featured-nav-btn img {
    width: 8px !important;
    height: 8px !important;
  }
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
  color: var(--light-blue) !important;
  background: #ffffff !important;
  border: 1px solid #e0e0e0 !important;
  cursor: pointer !important;
  pointer-events: auto !important;
  position: absolute !important;
  z-index: 100 !important;
  width: 22px !important;
  height: 22px !important;
  display: flex !important;
  align-items: center !important;
  justify-content: center !important;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1) !important;
  top: 50% !important;
  transform: translateY(-50%) !important;
}

/* Mobile override for navigation buttons */
@media (max-width: 768px) {
  .category-nav-btn.prev-btn,
  .discounted-nav-btn.prev-btn,
  .featured-nav-btn.prev-btn {
    left: 5px !important;
  }
  
  .category-nav-btn.next-btn,
  .discounted-nav-btn.next-btn,
  .featured-nav-btn.next-btn {
    right: 5px !important;
  }
  
  /* Force override any conflicting styles */
  .category-nav-btn,
  .discounted-nav-btn,
  .featured-nav-btn {
    position: absolute !important;
    z-index: 1000 !important;
    background: #ffffff !important;
    border: 1px solid #e0e0e0 !important;
    box-shadow: 0 2px 6px rgba(0,0,0,0.1) !important;
  }
  
  /* Specific overrides for discounted products section */
  .discounted-products-slider-wrapper .discounted-nav-btn.prev-btn {
    left: 5px !important;
  }
  
  .discounted-products-slider-wrapper .discounted-nav-btn.next-btn {
    right: 5px !important;
  }
  
  /* Specific overrides for featured products section */
  .featured-products-slider-wrapper .featured-nav-btn.prev-btn {
    left: 5px !important;
  }
  
  .featured-products-slider-wrapper .featured-nav-btn.next-btn {
    right: 5px !important;
  }
}

@media (max-width: 480px) {
  .category-nav-btn.prev-btn,
  .discounted-nav-btn.prev-btn,
  .featured-nav-btn.prev-btn {
    left: 3px !important;
  }
  
  .category-nav-btn.next-btn,
  .discounted-nav-btn.next-btn,
  .featured-nav-btn.next-btn {
    right: 3px !important;
  }
  
  /* Specific overrides for discounted products section */
  .discounted-products-slider-wrapper .discounted-nav-btn.prev-btn {
    left: 3px !important;
  }
  
  .discounted-products-slider-wrapper .discounted-nav-btn.next-btn {
    right: 3px !important;
  }
  
  /* Specific overrides for featured products section */
  .featured-products-slider-wrapper .featured-nav-btn.prev-btn {
    left: 3px !important;
  }
  
  .featured-products-slider-wrapper .featured-nav-btn.next-btn {
    right: 3px !important;
  }
}

@media (max-width: 360px) {
  .category-nav-btn.prev-btn,
  .discounted-nav-btn.prev-btn,
  .featured-nav-btn.prev-btn {
    left: 2px !important;
  }
  
  .category-nav-btn.next-btn,
  .discounted-nav-btn.next-btn,
  .featured-nav-btn.next-btn {
    right: 2px !important;
  }
  
  /* Specific overrides for discounted products section */
  .discounted-products-slider-wrapper .discounted-nav-btn.prev-btn {
    left: 2px !important;
  }
  
  .discounted-products-slider-wrapper .discounted-nav-btn.next-btn {
    right: 2px !important;
  }
  
  /* Specific overrides for featured products section */
  .featured-products-slider-wrapper .featured-nav-btn.prev-btn {
    left: 2px !important;
  }
  
  .featured-products-slider-wrapper .featured-nav-btn.next-btn {
    right: 2px !important;
  }
}

/* Desktop styles */
@media (min-width: 769px) {
.category-nav-btn.prev-btn {
  left: 10px !important;
}

.category-nav-btn.next-btn {
  right: 10px !important;
  }
}
@media (max-width: 768px){
.featured-products-section {
  background: none !important;
  margin: 0px 0px !important;
}

.service-section{
  margin: 0px 7px !important;
}
.category-nav-btn {
  width: 18px !important;
  height: 18px !important;
}
}
/* Additional mobile overrides with higher specificity */
@media (max-width: 768px) {
  /* Fix featured products container overflow */
  .featured-products-section {
    overflow: hidden !important;
    padding: 20px 0px !important;
    padding-top: 10px !important;
    padding-bottom: 20px !important;
  }
  
  .featured-products-card {
    overflow: hidden !important;
    margin: 0 10px !important;
    padding: 7px !important;
    padding-bottom: 0 !important;
    padding-left: 0px !important;
  }
  
  .featured-products-slider-wrapper {
    overflow: hidden !important;
    width: 100% !important;
    max-width: 100% !important;
  }
  
  .featured-products-container {
    width: 100% !important;
    max-width: 100% !important;
    overflow-x: auto !important;
    overflow-y: hidden !important;
    padding: 15px 5px !important;
    gap: 10px !important;
  }
  
  .featured-products-container .card.product-card {
    flex-shrink: 0 !important;
    margin-right: 10px !important;
  }
  
  .featured-products-container .card.product-card:last-child {
    margin-right: 15px !important;
  }
  
  /* Force override for discounted products section */
  .discounted-products-slider-wrapper .discounted-nav-btn {
    position: absolute !important;
    z-index: 1000 !important;
    background: #ffffff !important;
    border: 1px solid #e0e0e0 !important;
    box-shadow: 0 2px 6px rgba(0,0,0,0.1) !important;
    width: 20px !important;
    height: 20px !important;
  }
  
  .discounted-products-slider-wrapper .discounted-nav-btn.prev-btn {
    left: 5px !important;
  }
  
  .discounted-products-slider-wrapper .discounted-nav-btn.next-btn {
    right: 5px !important;
  }
  
  .discounted-products-slider-wrapper .discounted-nav-btn img {
    width: 10px !important;
    height: 10px !important;
  }
  
  /* Force override for featured products section */
  .featured-products-slider-wrapper .featured-nav-btn {
    position: absolute !important;
    z-index: 1000 !important;
    background: #ffffff !important;
    border: 1px solid #e0e0e0 !important;
    box-shadow: 0 2px 6px rgba(0,0,0,0.1) !important;
    width: 20px !important;
    height: 20px !important;
  }
  
  .featured-products-slider-wrapper .featured-nav-btn.prev-btn {
    left: 5px !important;
  }
  
  .featured-products-slider-wrapper .featured-nav-btn.next-btn {
    right: 5px !important;
  }
  
  .featured-products-slider-wrapper .featured-nav-btn img {
    width: 12px !important;
    height: 12px !important;
  }
}

@media (max-width: 480px) {
  .discounted-products-slider-wrapper .discounted-nav-btn,
  .featured-products-slider-wrapper .featured-nav-btn {
    width: 18px !important;
    height: 18px !important;
  }
  
  .discounted-products-slider-wrapper .discounted-nav-btn.prev-btn,
  .featured-products-slider-wrapper .featured-nav-btn.prev-btn {
    left: 3px !important;
  }
  
  .discounted-products-slider-wrapper .discounted-nav-btn.next-btn,
  .featured-products-slider-wrapper .featured-nav-btn.next-btn {
    right: 3px !important;
  }
  
  .discounted-products-slider-wrapper .discounted-nav-btn img,
  .featured-products-slider-wrapper .featured-nav-btn img {
    width: 10px !important;
    height: 10px !important;
  }
}

@media (max-width: 360px) {
  .discounted-products-slider-wrapper .discounted-nav-btn,
  .featured-products-slider-wrapper .featured-nav-btn {
    width: 16px !important;
    height: 16px !important;
  }
  
  .discounted-products-slider-wrapper .discounted-nav-btn.prev-btn,
  .featured-products-slider-wrapper .featured-nav-btn.prev-btn {
    left: 2px !important;
  }
  
  .discounted-products-slider-wrapper .discounted-nav-btn.next-btn,
  .featured-products-slider-wrapper .featured-nav-btn.next-btn {
    right: 2px !important;
  }
  
  .discounted-products-slider-wrapper .discounted-nav-btn img,
  .featured-products-slider-wrapper .featured-nav-btn img {
    width: 8px !important;
    height: 8px !important;
  }
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
  margin: 0 0px;
  position: relative;
  overflow: visible;
}

.discounted-products-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.category-products-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2px;
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
  background: none !important;
  background-color: none !important;
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
  background: none !important; /* Light gray background as per image */
  background-color: none !important;
  color: var(--dark-grey) !important;
}

@media (max-width: 575px) {
  .view-all-link {
    padding: 6px 12px;
    font-size: 10px;
    padding: 8px 3px;
    min-width: 45px;
  }
}

/* Discounted section View All button has white background */
.discounted-products-header .view-all-link {
  background: none !important; /* White background as per image */
  background-color: none !important;
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
    /* min-width: 180px !important; */
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
  background: #fff !important; /* White background */
  border-radius: 8px !important;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1) !important;
  border: 1px solid var(--light-green) !important;
  /* Override any global white background */
  background-image: none !important;
  /* Ensure border matches background */
  border-color: var(--light-green) !important;
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

.featured-products-container::-webkit-scrollbar {
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
  width: 20px;
  height: 20px;
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

/* Desktop styles for discounted navigation */
@media (min-width: 769px) {
.discounted-nav-btn.prev-btn {
  left: -35px;
  z-index: 10;
}

.discounted-nav-btn.next-btn {
  right: -35px;
  z-index: 10;
  }
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
}

.featured-nav-btn {
  background: #ffffff;
  border: none;
  color: var(--light-green); /* Light green color as per image */
  font-size: 14px;
  font-weight: bold;
  cursor: pointer;
  width: 20px;
  height: 20px;
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

.featured-nav-btn:hover {
  background: #ffffff;
  color: var(--dark-green); /* Darker green on hover */
  border-color: var(--light-green);
  box-shadow: 0 4px 12px rgba(206, 229, 239, 0.3);
  transform: translateY(-50%) scale(1.05);
}

.featured-nav-btn:active {
  transform: translateY(-50%) scale(0.95);
}

/* Desktop styles for featured navigation */
@media (min-width: 769px) {
  .featured-nav-btn.prev-btn {
    left: 0px;
    z-index: 10;
  }
  
  .featured-nav-btn.next-btn {
    right: 0px;
    z-index: 10;
  }
}

/* Ensure featured arrows are always visible */
.featured-nav-btn::before {
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

/* Force featured arrow visibility */
.featured-nav-btn {
  color: var(--light-green) !important; /* Light green color as per image */
  background: #ffffff !important;
  border: 1px solid #e0e0e0 !important;
  cursor: pointer !important;
  pointer-events: auto !important;
  position: absolute !important;
  z-index: 100 !important;
  /* border-radius: 50% !important; */
  width: 20px !important;
  height: 20px !important;
  display: flex !important;
  align-items: center !important;
  justify-content: center !important;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1) !important;
  top: 50% !important;
  transform: translateY(-50%) !important;
}

.discounted-nav-btn.prev-btn {
  left: 0px !important;
}

.discounted-nav-btn.next-btn {
  right: 0px !important;
}

.featured-nav-btn.prev-btn {
  left: 0px !important;
}

.featured-nav-btn.next-btn {
  right: 0px !important;
}

/* Ensure perfect circular shape */
.discounted-nav-btn img,
.featured-nav-btn img {
  width: 10px !important;
  height: 10px !important;
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
    width: 50px;
    min-width: 50px;
  }
  
  .category-illustration {
    width: 80px;
    height: 80px;
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
    font-size: 8px;
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
    left: 0px;
  }
  
  .category-nav-btn.next-btn {
    right: 0px;
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
    width: 50px;
    min-width: 50px;
  }
  
  .category-illustration {
    width: 80px;
    height: 80px;
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
    font-size: 8px;
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
    left: 0px;
  }
  
  .category-nav-btn.next-btn {
    right: 0px;
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
  
  /* .discounted-products-header {
    margin-bottom: 15px;
  } */
  
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
    width: 20px;
    height: 20px;
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
    width: 20px;
    height: 20px;
    font-size: 16px;
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
    width: 20px;
    height: 20px;
    font-size: 16px;
  }
}

/* Featured Products Section - Same Design as Discounted Products */
.featured-products-section {
  padding: 20px 0px;
  padding-top: 10px !important;
  padding-bottom: 20px !important;
  background: none !important;
  margin: 0;
  overflow: hidden;
}

.featured-products-card {
  background: var(--light-green); /* Light green background as per image */
  border-radius: 8px;
  border: 1px solid #e0e0e0;
  padding: 20px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  margin: 0 0px;
  position: relative;
  overflow: hidden;
}

.featured-products-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
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
  overflow: hidden;
  padding: 0 0px;
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
    flex: 0 0 240px !important;
    width: 240px !important;
    min-width: 240px !important;
    max-width: 240px !important;
  }
  .featured-products-container {
    width: calc(2.5 * 240px + 2 * 16px) !important;
    max-width: calc(2.5 * 240px + 2 * 16px) !important;
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
  .featured-products-section {
    overflow: hidden !important;
    padding: 20px 0px !important;
    padding-top: 10px !important;
    padding-bottom: 20px !important;
  }
  
  .featured-products-card {
    overflow: hidden !important;
    margin: 0 10px !important;
    padding: 15px !important;
    padding-bottom: 0 !important;
    padding-left: 0px !important;
  }
  
  .featured-products-slider-wrapper {
    overflow: hidden !important;
    width: 100% !important;
    max-width: 100% !important;
  }
  
  .featured-products-container {
    width: 100% !important;
    max-width: 100% !important;
    overflow-x: auto !important;
    overflow-y: hidden !important;
    padding: 15px 5px !important;
    gap: 10px !important;
  }
  
  .featured-products-container .card.product-card {
    flex: 0 0 180px !important;
    width: 180px !important;
    min-width: 180px !important;
    max-width: 180px !important;
    margin-right: 10px !important;
    flex-shrink: 0 !important;
  }
  
  /* Ensure last card doesn't overflow */
  .featured-products-container .card.product-card:last-child {
    margin-right: 15px !important;
  }
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
  background: #ffffff;
  border: none;
  color: var(--light-green); /* Light green color as per image */
  font-size: 14px;
  font-weight: bold;
  cursor: pointer;
  width: 20px;
  height: 20px;
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

/* Desktop styles for featured navigation */
@media (min-width: 769px) {
.featured-nav-btn.prev-btn {
  left: 0px !important;
}

.featured-nav-btn.next-btn {
  right: 0px !important;
  }
}

.featured-nav-btn:hover {
  background: #ffffff;
  color: var(--dark-green); /* Darker green on hover */
  border-color: var(--light-green);
  box-shadow: 0 4px 12px rgba(227, 242, 170, 0.3);
  transform: translateY(-50%) scale(1.05);
}

.featured-nav-btn:active {
  transform: translateY(-50%) scale(0.95);
}

.featured-nav-btn.prev-btn {
  left: 0px;
  z-index: 10;
}

.featured-nav-btn.next-btn {
  right: 0px;
  z-index: 10;
}

/* Featured Products Mobile Responsive */
@media (max-width: 480px) {
  .featured-products-section {
    padding: 15px 0px;
    margin: 15px 0;
    padding-top: 10px !important;
    padding-bottom: 20px !important;
  }
  
  .featured-products-card {
    padding: 15px;
    padding-bottom: 0 !important;
    padding-left: 0px !important;
    border-radius: 6px;
    max-width: 100%;
  }
  
  /* .featured-products-header {
    margin-bottom: 15px;
  } */
  
  .featured-products-title {
    font-size: 14px;
    padding-left: 8px;
  }
  
  .featured-products-slider-wrapper {
    padding: 0;
    max-width: 100%;
  }
  
  .featured-products-container .card.product-card {
    flex: 0 0 180px !important;
    width: 180px !important;
    /* min-width: 180px !important; */
    max-width: 180px !important;
  }
  
  .featured-nav-btn {
    width: 20px !important;
    height: 20px !important;
    font-size: 20px !important;
    color: var(--light-green) !important;
    background: #ffffff !important;
    border: 1px solid #e0e0e0 !important;
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
    padding: 18px 0px;
    margin: 18px 0;
  }
  
  .featured-products-card {
    padding: 18px;
    padding-bottom: 0 !important;
    max-width: 100%;
    padding-left: 0px !important;
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
    width: 20px !important;
    height: 20px !important;
    font-size: 22px !important;
    color: var(--light-green) !important;
    background: #ffffff !important;
    border: 1px solid #e0e0e0 !important;
  }
}

/* Featured Products Medium Mobile Responsive (481px - 768px) */
@media (max-width: 768px) and (min-width: 481px) {
  .featured-products-section {
    padding: 16px 0px;
    padding-top: 10px !important;
    padding-bottom: 20px !important;
    margin: 16px 0;
  }
  
  .featured-products-card {
    padding: 16px;
    max-width: 100%;
    padding-left: 0px !important;
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
    width: 20px !important;
    height: 20px !important;
    font-size: 12px !important;
    color: var(--light-green) !important;
    background: #ffffff !important;
    border: 1px solid #e0e0e0 !important;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1) !important;
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

/* Force featured arrow visibility - matching discounted products */
.featured-nav-btn {
  color: var(--light-green) !important;
  background: #ffffff !important;
  border: 1px solid #e0e0e0 !important;
  cursor: pointer !important;
  pointer-events: auto !important;
  position: absolute !important;
  z-index: 100 !important;
  width: 20px !important;
  height: 20px !important;
  display: flex !important;
  align-items: center !important;
  justify-content: center !important;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1) !important;
  top: 50% !important;
  transform: translateY(-50%) !important;
}

.featured-nav-btn.prev-btn {
  left: 0px !important;
}

.featured-nav-btn.next-btn {
  right: 0px !important;
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

/* Custom Hero Banner Carousel - Ultra Smooth Transitions */
.hero-slider-section {
  width: 100% !important;
  overflow: hidden !important;
  position: relative !important;
  margin: 0 !important;
  padding: 0 !important;
  line-height: 0 !important;
}

/* Custom carousel container */
.custom-carousel {
  position: relative !important;
  width: 100% !important;
  height: auto !important;
  min-height: 300px !important;
  max-height: 400px !important;
  overflow: hidden !important;
  background-color: transparent !important;
  margin: 0 !important;
  padding: 0 !important;
}

.custom-carousel .carousel-inner {
  position: relative !important;
  width: 100% !important;
  height: auto !important;
  overflow: hidden !important;
  margin: 0 !important;
  padding: 0 !important;
  line-height: 0 !important;
  font-size: 0 !important;
}

/* Custom carousel items with smooth transitions */
.custom-carousel .carousel-item {
  position: absolute !important;
  top: 0 !important;
  left: 0 !important;
  width: 100% !important;
  height: auto !important;
  opacity: 0 !important;
  visibility: hidden !important;
  transition: opacity 1.5s ease-in-out, visibility 1.5s ease-in-out !important;
  display: block !important;
  overflow: hidden !important;
  margin: 0 !important;
  padding: 0 !important;
  line-height: 0 !important;
  font-size: 0 !important;
  background: transparent !important;
}

.custom-carousel .carousel-item.active {
  opacity: 1 !important;
  visibility: visible !important;
  z-index: 2 !important;
}

.custom-carousel .carousel-item:not(.active) {
  z-index: 1 !important;
}

/* Carousel images with smooth transitions */
.custom-carousel .carousel-image {
  width: 100% !important;
  height: auto !important;
  max-height: 400px !important;
  object-fit: cover !important;
  object-position: center !important;
  transition: transform 1.5s ease-in-out !important;
  will-change: transform !important;
  backface-visibility: hidden !important;
  -webkit-backface-visibility: hidden !important;
  transform: translateZ(0) !important;
  -webkit-transform: translateZ(0) !important;
  margin: 0 !important;
  padding: 0 !important;
  display: block !important;
  vertical-align: top !important;
}

.custom-carousel .carousel-item.active .carousel-image {
  transform: scale(1) translateZ(0) !important;
  -webkit-transform: scale(1) translateZ(0) !important;
}

.custom-carousel .carousel-item:not(.active) .carousel-image {
  transform: scale(1.05) translateZ(0) !important;
  -webkit-transform: scale(1.05) translateZ(0) !important;
}

/* Ensure smooth transitions */
.custom-carousel .carousel-item {
  will-change: opacity, visibility !important;
  backface-visibility: hidden !important;
  -webkit-backface-visibility: hidden !important;
}

/* Enhanced carousel controls */
.custom-carousel .carousel-control-prev,
.custom-carousel .carousel-control-next {
  position: absolute !important;
  top: 50% !important;
  transform: translateY(-50%) !important;
  background: rgba(0, 0, 0, 0.3) !important;
  border: none !important;
  border-radius: 4px !important;
  width: 30px !important;
  height: 30px !important;
  display: flex !important;
  align-items: center !important;
  justify-content: center !important;
  cursor: pointer !important;
  transition: all 0.3s ease !important;
  z-index: 10 !important;
  color: white !important;
  opacity: 1 !important;
  visibility: visible !important;
  pointer-events: auto !important;
}

.custom-carousel .carousel-control-prev:hover,
.custom-carousel .carousel-control-next:hover {
  background: rgba(0, 0, 0, 0.6) !important;
  transform: translateY(-50%) scale(1.1) !important;
}

.custom-carousel .carousel-control-prev {
  left: 20px !important;
  top: 50% !important;
  transform: translateY(-50%) !important;
}

.custom-carousel .carousel-control-next {
  right: 20px !important;
  top: 50% !important;
  transform: translateY(-50%) !important;
}

.custom-carousel .carousel-control-prev-icon,
.custom-carousel .carousel-control-next-icon {
  width: 15px !important;
  height: 15px !important;
  background-size: 100% 100% !important;
  background-repeat: no-repeat !important;
  display: block !important;
  opacity: 1 !important;
  visibility: visible !important;
}

.custom-carousel .carousel-control-prev-icon {
  background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='%23fff' viewBox='0 0 8 8'%3e%3cpath d='M5.25 0l-4 4 4 4 1.5-1.5-2.5-2.5 2.5-2.5-1.5-1.5z'/%3e%3c/svg%3e") !important;
}

.custom-carousel .carousel-control-next-icon {
  background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='%23fff' viewBox='0 0 8 8'%3e%3cpath d='M2.75 0l-1.5 1.5 2.5 2.5-2.5 2.5 1.5 1.5 4-4-4-4z'/%3e%3c/svg%3e") !important;
}

/* Enhanced carousel indicators */
.custom-carousel .carousel-indicators {
  position: absolute !important;
  bottom: 20px !important;
  left: 50% !important;
  transform: translateX(-50%) !important;
  display: flex !important;
  gap: 8px !important;
  z-index: 10 !important;
}

.custom-carousel .carousel-indicators .indicator {
  width: 12px !important;
  height: 12px !important;
  border-radius: 50% !important;
  background: rgba(255, 255, 255, 0.5) !important;
  border: 2px solid rgba(255, 255, 255, 0.3) !important;
  cursor: pointer !important;
  transition: all 0.3s ease !important;
  padding: 0 !important;
}

.custom-carousel .carousel-indicators .indicator.active {
  background: rgba(255, 255, 255, 0.9) !important;
  border-color: rgba(255, 255, 255, 0.9) !important;
  transform: scale(1.2) !important;
}

.custom-carousel .carousel-indicators .indicator:hover {
  background: rgba(255, 255, 255, 0.7) !important;
  transform: scale(1.1) !important;
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

/* Ensure carousel controls are always visible and touch-friendly */
.custom-carousel .carousel-control-prev,
.custom-carousel .carousel-control-next {
  -webkit-tap-highlight-color: transparent !important;
  -webkit-touch-callout: none !important;
  -webkit-user-select: none !important;
  -khtml-user-select: none !important;
  -moz-user-select: none !important;
  -ms-user-select: none !important;
  user-select: none !important;
  touch-action: manipulation !important;
}

/* Force arrow visibility on all devices */
.custom-carousel .carousel-control-prev,
.custom-carousel .carousel-control-next {
  opacity: 1 !important;
  visibility: visible !important;
  pointer-events: auto !important;
  display: flex !important;
  align-items: center !important;
  justify-content: center !important;
  z-index: 100 !important;
  position: absolute !important;
  top: 50% !important;
  transform: translateY(-50%) !important;
}

.custom-carousel .carousel-control-prev-icon,
.custom-carousel .carousel-control-next-icon {
  opacity: 1 !important;
  visibility: visible !important;
  display: block !important;
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

  .featured-products-container
 {
  margin: 0 6px !important;
  }
}

@media (max-width: 480px) {
  .discounted-products-card,
  .featured-products-card {
    margin: 0 7px !important;
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

/* Mobile-specific carousel fixes with smooth transitions */
@media (max-width: 767.98px) {
  .hero-slider-section {
    overflow: hidden !important;
    width: 100% !important;
    margin: 0 !important;
    padding: 0 !important;
    line-height: 0 !important;
  }
  
  .custom-carousel {
    height: auto !important;
    min-height: 0 !important;
    max-height: none !important;
    overflow: hidden !important;
    margin: 0 !important;
    padding: 0 !important;
    width: 100% !important;
    line-height: 0 !important;
  }
  
  .custom-carousel .carousel-inner {
    height: auto !important;
    min-height: 0 !important;
    max-height: none !important;
  }
  
  .custom-carousel .carousel-item {
    height: auto !important;
    min-height: 0 !important;
    max-height: none !important;
  }
  
  .custom-carousel .carousel-image {
    height: auto !important;
    max-height: 250px !important;
  }
  
  .custom-carousel .carousel-inner {
    overflow: hidden !important;
    width: 100% !important;
    height: 100% !important;
  }
  
  .custom-carousel .carousel-item {
    transition: opacity 1.2s ease-in-out, visibility 1.2s ease-in-out !important;
    width: 100% !important;
    height: 100% !important;
  }
  
  .custom-carousel .carousel-image {
    transition: transform 1.2s ease-in-out !important;
    width: 100% !important;
    height: 100% !important;
    object-fit: cover !important;
    object-position: center !important;
  }
  
  /* Enhanced mobile carousel controls */
  .custom-carousel .carousel-control-prev,
  .custom-carousel .carousel-control-next {
    width: 44px !important;
    height: 44px !important;
    background: rgba(0, 0, 0, 0.4) !important;
    border-radius: 25% !important;
    opacity: 1 !important;
    visibility: visible !important;
    pointer-events: auto !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
  }
  
  .custom-carousel .carousel-control-prev {
    left: 10px !important;
    top: 50% !important;
    transform: translateY(-50%) !important;
  }
  
  .custom-carousel .carousel-control-next {
    right: 10px !important;
    top: 50% !important;
    transform: translateY(-50%) !important;
  }
  
  .custom-carousel .carousel-control-prev-icon,
  .custom-carousel .carousel-control-next-icon {
    width: 18px !important;
    height: 18px !important;
    opacity: 1 !important;
    visibility: visible !important;
    display: block !important;
  }
  
  /* Enhanced mobile carousel indicators */
  .custom-carousel .carousel-indicators {
    bottom: 15px !important;
  }
  
  .custom-carousel .carousel-indicators .indicator {
    width: 10px !important;
    height: 10px !important;
  }
}

/* Extra small mobile devices */
@media (max-width: 480px) {
  .hero-slider-section {
    overflow: hidden !important;
    width: 100% !important;
    margin: 0 !important;
    padding: 0 !important;
    line-height: 0 !important;
  }
  
  .custom-carousel {
    height: auto !important;
    min-height: 0 !important;
    max-height: none !important;
    overflow: hidden !important;
    margin: 0 !important;
    padding: 0 !important;
    width: 100% !important;
    line-height: 0 !important;
  }
  
  .custom-carousel .carousel-inner {
    height: auto !important;
    min-height: 0 !important;
    max-height: none !important;
  }
  
  .custom-carousel .carousel-item {
    height: auto !important;
    min-height: 0 !important;
    max-height: none !important;
  }
  
  .custom-carousel .carousel-image {
    height: auto !important;
    max-height: 200px !important;
  }
  
  .custom-carousel .carousel-inner {
    overflow: hidden !important;
    width: 100% !important;
    height: 100% !important;
  }
  
  .custom-carousel .carousel-item {
    transition: opacity 1s ease-in-out, visibility 1s ease-in-out !important;
    width: 100% !important;
    height: 100% !important;
  }
  
  .custom-carousel .carousel-image {
    transition: transform 1s ease-in-out !important;
    width: 100% !important;
    height: 100% !important;
    object-fit: cover !important;
    object-position: center !important;
  }
  
  /* Smaller mobile carousel controls */
  .custom-carousel .carousel-control-prev,
  .custom-carousel .carousel-control-next {
    width: 30px !important;
    height: 30px !important;
    background: rgba(0, 0, 0, 0.5) !important;
    border-radius: 25% !important;
    opacity: 1 !important;
    visibility: visible !important;
    pointer-events: auto !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
  }
  
  .custom-carousel .carousel-control-prev {
    left: 8px !important;
    top: 50% !important;
    transform: translateY(-50%) !important;
  }
  
  .custom-carousel .carousel-control-next {
    right: 8px !important;
    top: 50% !important;
    transform: translateY(-50%) !important;
  }
  
  .custom-carousel .carousel-control-prev-icon,
  .custom-carousel .carousel-control-next-icon {
    width: 15px !important;
    height: 15px !important;
    opacity: 1 !important;
    visibility: visible !important;
    display: block !important;
  }
  
  /* Smaller mobile carousel indicators */
  .custom-carousel .carousel-indicators {
    bottom: 10px !important;
  }
  
  .custom-carousel .carousel-indicators .indicator {
    width: 8px !important;
    height: 8px !important;
  }
}

/* Very small mobile devices */
@media (max-width: 320px) {
  .hero-slider-section {
    overflow: hidden !important;
    width: 100% !important;
    margin: 0 !important;
    padding: 0 !important;
    line-height: 0 !important;
  }
  
  .custom-carousel {
    height: auto !important;
    min-height: 0 !important;
    max-height: none !important;
    overflow: hidden !important;
    margin: 0 !important;
    padding: 0 !important;
    width: 100% !important;
    line-height: 0 !important;
  }
  
  .custom-carousel .carousel-inner {
    height: auto !important;
    min-height: 0 !important;
    max-height: none !important;
  }
  
  .custom-carousel .carousel-item {
    height: auto !important;
    min-height: 0 !important;
    max-height: none !important;
  }
  
  .custom-carousel .carousel-image {
    height: auto !important;
    max-height: 180px !important;
  }
  
  .custom-carousel .carousel-inner {
    overflow: hidden !important;
    width: 100% !important;
    height: 100% !important;
  }
  
  .custom-carousel .carousel-item {
    transition: opacity 0.8s ease-in-out, visibility 0.8s ease-in-out !important;
    width: 100% !important;
    height: 100% !important;
  }
  
  .custom-carousel .carousel-image {
    transition: transform 0.8s ease-in-out !important;
    width: 100% !important;
    height: 100% !important;
    object-fit: cover !important;
    object-position: center !important;
  }
  
  /* Very small mobile carousel controls */
  .custom-carousel .carousel-control-prev,
  .custom-carousel .carousel-control-next {
    width: 36px !important;
    height: 36px !important;
    background: rgba(0, 0, 0, 0.6) !important;
    border-radius: 25% !important;
    opacity: 1 !important;
    visibility: visible !important;
    pointer-events: auto !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
  }
  
  .custom-carousel .carousel-control-prev {
    left: 5px !important;
    top: 50% !important;
    transform: translateY(-50%) !important;
  }
  
  .custom-carousel .carousel-control-next {
    right: 5px !important;
    top: 50% !important;
    transform: translateY(-50%) !important;
  }
  
  .custom-carousel .carousel-control-prev-icon,
  .custom-carousel .carousel-control-next-icon {
    width: 14px !important;
    height: 14px !important;
    opacity: 1 !important;
    visibility: visible !important;
    display: block !important;
  }
  
  /* Very small mobile carousel indicators */
  .custom-carousel .carousel-indicators {
    bottom: 8px !important;
  }
  
  .custom-carousel .carousel-indicators .indicator {
    width: 6px !important;
    height: 6px !important;
  }
}

/* Tablet and desktop carousel fixes with smooth transitions */
@media (min-width: 768px) {
  .hero-slider-section {
    overflow: hidden !important;
  }
  
  .custom-carousel {
    overflow: hidden !important;
  }
  
  .custom-carousel .carousel-inner {
    overflow: hidden !important;
  }
  
  /* Enhanced desktop carousel transitions */
  .custom-carousel .carousel-item {
    transition: opacity 1.5s ease-in-out, visibility 1.5s ease-in-out !important;
  }
  
  .custom-carousel .carousel-image {
    transition: transform 1.5s ease-in-out !important;
  }
  
  /* Enhanced desktop carousel controls */
  .custom-carousel .carousel-control-prev,
  .custom-carousel .carousel-control-next {
    width: 30px !important;
    height: 30px !important;
    background: rgba(0, 0, 0, 0.2) !important;
    opacity: 1 !important;
    visibility: visible !important;
    pointer-events: auto !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    top: 50% !important;
    transform: translateY(-50%) !important;
  }
  
  .custom-carousel .carousel-control-prev:hover,
  .custom-carousel .carousel-control-next:hover {
    background: rgba(0, 0, 0, 0.5) !important;
    transform: translateY(-50%) scale(1.15) !important;
  }
  
  .custom-carousel .carousel-control-prev-icon,
  .custom-carousel .carousel-control-next-icon {
    width: 15px !important;
    height: 15px !important;
    opacity: 1 !important;
    visibility: visible !important;
    display: block !important;
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
  top: 25% !important;
  transform: translateY(-50%) !important;
  z-index: 10 !important;
  background: rgba(0, 0, 0, 0.3) !important;
  border: none !important;
  border-radius: 50% !important;
  width: 30px !important;
  height: 30px !important;
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
  width: 15px !important;
  height: 15px !important;
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
  margin-left: 0% !important;
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
document.addEventListener('DOMContentLoaded', function() {
    // Custom Hero Carousel functionality with ultra-smooth transitions
    const heroCarousel = document.getElementById('heroCarousel');
    if (heroCarousel) {
        const carouselItems = heroCarousel.querySelectorAll('.carousel-item');
        const indicators = heroCarousel.querySelectorAll('.indicator');
        const prevBtn = heroCarousel.querySelector('.carousel-control-prev');
        const nextBtn = heroCarousel.querySelector('.carousel-control-next');
        const images = heroCarousel.querySelectorAll('.carousel-image');
        
        let currentIndex = 0;
        let isTransitioning = false;
        let autoPlayInterval;
        let imagesLoaded = 0;
        
        // Preload all images for smooth transitions
        function preloadImages() {
            images.forEach((img, index) => {
                const newImg = new Image();
                newImg.onload = () => {
                    imagesLoaded++;
                    if (imagesLoaded === images.length) {
                        // All images loaded, start carousel
                        initializeCarousel();
                    }
                };
                newImg.onerror = () => {
                    imagesLoaded++;
                    if (imagesLoaded === images.length) {
                        // All images processed, start carousel
                        initializeCarousel();
                    }
                };
                newImg.src = img.src;
            });
        }
        
        function initializeCarousel() {
            // Initialize first slide
            carouselItems[0].classList.add('active');
            indicators[0].classList.add('active');
            
            // Start auto-play
            startAutoPlay();
        }
        
        function showSlide(index) {
            if (isTransitioning || index === currentIndex) return;
            isTransitioning = true;
            
            const currentItem = carouselItems[currentIndex];
            const nextItem = carouselItems[index];
            const currentIndicator = indicators[currentIndex];
            const nextIndicator = indicators[index];
            
            // Update indicators
            currentIndicator.classList.remove('active');
            nextIndicator.classList.add('active');
            
            // Smooth transition between slides
            currentItem.classList.remove('active');
            nextItem.classList.add('active');
            
            currentIndex = index;
            
            // Reset transition state after animation
            setTimeout(() => {
                isTransitioning = false;
            }, 1500);
        }
        
        function nextSlide() {
            const nextIndex = (currentIndex + 1) % carouselItems.length;
            showSlide(nextIndex);
        }
        
        function prevSlide() {
            const prevIndex = (currentIndex - 1 + carouselItems.length) % carouselItems.length;
            showSlide(prevIndex);
        }
        
        function startAutoPlay() {
            autoPlayInterval = setInterval(nextSlide, 5000);
        }
        
        function stopAutoPlay() {
            if (autoPlayInterval) {
                clearInterval(autoPlayInterval);
            }
        }
        
        // Event listeners for manual navigation
        if (prevBtn) {
            prevBtn.addEventListener('click', (e) => {
                e.preventDefault();
                stopAutoPlay();
                prevSlide();
                startAutoPlay();
            });
        }
        
        if (nextBtn) {
            nextBtn.addEventListener('click', (e) => {
                e.preventDefault();
                stopAutoPlay();
                nextSlide();
                startAutoPlay();
            });
        }
        
        // Event listeners for indicators
        indicators.forEach((indicator, index) => {
            indicator.addEventListener('click', () => {
                stopAutoPlay();
                showSlide(index);
                startAutoPlay();
            });
        });
        
        // Pause auto-play on hover
        heroCarousel.addEventListener('mouseenter', stopAutoPlay);
        heroCarousel.addEventListener('mouseleave', startAutoPlay);
        
        // Touch/swipe support for mobile
        let touchStartX = 0;
        let touchEndX = 0;
        
        heroCarousel.addEventListener('touchstart', (e) => {
            touchStartX = e.changedTouches[0].screenX;
        });
        
        heroCarousel.addEventListener('touchend', (e) => {
            touchEndX = e.changedTouches[0].screenX;
            handleSwipe();
        });
        
        function handleSwipe() {
            const swipeThreshold = 50;
            const diff = touchStartX - touchEndX;
            
            if (Math.abs(diff) > swipeThreshold) {
                if (diff > 0) {
                    // Swipe left - next slide
                    stopAutoPlay();
                    nextSlide();
                    startAutoPlay();
                } else {
                    // Swipe right - previous slide
                    stopAutoPlay();
                    prevSlide();
                    startAutoPlay();
                }
            }
        }
        
        // Start preloading images
        preloadImages();
    }
    
    // Category slider functionality - Simplified
    const categoryContainer = document.getElementById('slider');
    const prevBtn = document.querySelector('.category-nav-btn.prev-btn');
    const nextBtn = document.querySelector('.category-nav-btn.next-btn');
    
    if (categoryContainer && prevBtn && nextBtn) {
        // Simple scroll function
        const scrollAmount = 200; // Fixed scroll amount
        
        prevBtn.addEventListener('click', function(e) {
            e.preventDefault();
            categoryContainer.scrollBy({
                left: -scrollAmount,
                behavior: 'smooth'
            });
        });
        
        nextBtn.addEventListener('click', function(e) {
            e.preventDefault();
            categoryContainer.scrollBy({
                left: scrollAmount,
                behavior: 'smooth'
            });
        });
    }
    
    // Discounted Products slider functionality - SIMPLE & RELIABLE VERSION
    
    
    const discountedContainer = document.getElementById('discounted-slider');
    const discountedPrevBtn = document.querySelector('.discounted-nav-btn.prev-btn');
    const discountedNextBtn = document.querySelector('.discounted-nav-btn.next-btn');
    
    if (discountedContainer && discountedPrevBtn && discountedNextBtn) {

        
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
            discountedContainer.scrollLeft -= scrollAmount;
        });
        
        // Next button
        discountedNextBtn.addEventListener('click', function(e) {
            e.preventDefault();
            const scrollAmount = getDiscountedScrollAmount();
            discountedContainer.scrollLeft += scrollAmount;
        });
    }
    
    // Featured Products slider functionality - SAME AS DISCOUNT PRODUCTS
    const featuredContainer = document.getElementById('featured-slider');
    const featuredPrevBtn = document.querySelector('.featured-nav-btn.prev-btn');
    const featuredNextBtn = document.querySelector('.featured-nav-btn.next-btn');
    
    // Force make arrows visible
    if (featuredPrevBtn) {
        featuredPrevBtn.style.display = 'flex';
        featuredPrevBtn.style.visibility = 'visible';
        featuredPrevBtn.style.opacity = '1';
        featuredPrevBtn.style.zIndex = '9999';
    }
    
    if (featuredNextBtn) {
        featuredNextBtn.style.display = 'flex';
        featuredNextBtn.style.visibility = 'visible';
        featuredNextBtn.style.opacity = '1';
        featuredNextBtn.style.zIndex = '9999';
    }
    
    if (featuredContainer && featuredPrevBtn && featuredNextBtn) {
        
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
            featuredContainer.scrollLeft -= scrollAmount;
        });
        
        // Next button
        featuredNextBtn.addEventListener('click', function(e) {
            e.preventDefault();
            const scrollAmount = getFeaturedScrollAmount();
            featuredContainer.scrollLeft += scrollAmount;
        });
    }
    

    
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
                <img src="asset/images/logo.webp" alt="EverythingB2C Logo" class="site-logo">
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

.featured-products-section {
  background: none !important;
  /* margin: 0px 18px !important; */
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
        
        // Find all wishlist labels with wishlist-active class
        const activeWishlistLabels = document.querySelectorAll('.wishlist-label.wishlist-active');
        
        activeWishlistLabels.forEach(function(label) {
            const heartIcon = label.querySelector('.heart-icon');
            if (heartIcon) {
                heartIcon.style.color = '#DE0085';
                heartIcon.style.webkitTextStroke = '2px #DE0085';
                heartIcon.style.textStroke = '2px #DE0085';
                heartIcon.style.filter = 'drop-shadow(0 2px 4px rgba(222, 0, 133, 0.3))';
            }
        });
        
        // Also check for checked checkboxes
        const checkedCheckboxes = document.querySelectorAll('.heart-checkbox:checked');
        
        checkedCheckboxes.forEach(function(checkbox) {
            const label = checkbox.nextElementSibling;
            if (label && label.classList.contains('wishlist-label')) {
                const heartIcon = label.querySelector('.heart-icon');
                if (heartIcon) {
                    heartIcon.style.color = '#DE0085';
                    heartIcon.style.webkitTextStroke = '2px #DE0085';
                    heartIcon.style.textStroke = '2px #DE0085';
                    heartIcon.style.filter = 'drop-shadow(0 2px 4px rgba(222, 0, 133, 0.3))';
                }
            }
        });
    }
    
    // Apply styling immediately
    applyWishlistStyling();
    
    // Also apply styling after a short delay to ensure all elements are loaded
    setTimeout(function() {
        applyWishlistStyling();
    }, 100);
    setTimeout(function() {
        applyWishlistStyling();
    }, 500);
});
</script>
<?php endif; ?>

<!-- Force wishlist styling on page load - always runs -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    
    function forceWishlistStyling() {
        
        // Find all wishlist labels with wishlist-active class
        const activeWishlistLabels = document.querySelectorAll('.wishlist-label.wishlist-active');
        
        activeWishlistLabels.forEach(function(label) {
            const heartIcon = label.querySelector('.heart-icon');
            if (heartIcon) {
                heartIcon.style.color = '#DE0085';
                heartIcon.style.webkitTextStroke = '2px #DE0085';
                heartIcon.style.textStroke = '2px #DE0085';
                heartIcon.style.filter = 'drop-shadow(0 2px 4px rgba(222, 0, 133, 0.3))';
            }
        });
        
        // Also check for checked checkboxes
        const checkedCheckboxes = document.querySelectorAll('.heart-checkbox:checked');
        
        checkedCheckboxes.forEach(function(checkbox) {
            const label = checkbox.nextElementSibling;
            if (label && label.classList.contains('wishlist-label')) {
                const heartIcon = label.querySelector('.heart-icon');
                if (heartIcon) {
                    heartIcon.style.color = '#DE0085';
                    heartIcon.style.webkitTextStroke = '2px #DE0085';
                    heartIcon.style.textStroke = '2px #DE0085';
                    heartIcon.style.filter = 'drop-shadow(0 2px 4px rgba(222, 0, 133, 0.3))';
                }
            }
        });
        
        // Check all wishlist elements
        const allWishlistLabels = document.querySelectorAll('.wishlist-label');
        
        allWishlistLabels.forEach(function(label, index) {
            const checkbox = label.previousElementSibling;
            const isChecked = checkbox && checkbox.checked;
            const hasActiveClass = label.classList.contains('wishlist-active');
        });
    }
    
    // Apply styling immediately
    forceWishlistStyling();
    
    // Also apply styling after delays to ensure all elements are loaded
    setTimeout(function() {
        forceWishlistStyling();
    }, 100);
    
    setTimeout(function() {
        forceWishlistStyling();
    }, 500);
    
    setTimeout(function() {
        forceWishlistStyling();
    }, 1000);
});
</script>

</body>
</html> 