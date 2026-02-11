<?php
$pageTitle = 'EverythingB2C';
$pageCss = ['asset/style/home.css'];
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
        // Standardized CSS handles visibility and z-index via !important
        prevBtn.style.display = 'flex';
        prevBtn.style.visibility = 'visible';
        nextBtn.style.display = 'flex';
        nextBtn.style.visibility = 'visible';

        const scrollAmount = 300; // Fixed scroll amount
        
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
        // Standardized CSS handles visibility and z-index via !important
        discountedPrevBtn.style.display = 'flex';
        discountedPrevBtn.style.visibility = 'visible';
        discountedNextBtn.style.display = 'flex';
        discountedNextBtn.style.visibility = 'visible';

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
    
    
    if (featuredContainer && featuredPrevBtn && featuredNextBtn) {
        // Standardized CSS handles visibility and z-index via !important
        featuredPrevBtn.style.display = 'flex';
        featuredPrevBtn.style.visibility = 'visible';
        featuredNextBtn.style.display = 'flex';
        featuredNextBtn.style.visibility = 'visible';
        
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
