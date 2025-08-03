<?php
$pageTitle = 'Home';
require_once 'includes/header.php';

// Get data from database
$categories = getAllCategoriesWithProductCount();
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
<!-- <section class="header0">
    <h5>Product Categories</h5>
</section> -->
<section class="header0">
    <h5 class="header-title">Product Categories</h5>
    <a href=""><button class="P-Button"><span> &gt;</span></button></a>
</section>

<section class="category-section">
    <div class="category-wrapper">
        <button class="nav-btn prev-btn" aria-label="Scroll Left">&#10094;</button>
        <div class="category-container" id="slider">
            <?php foreach ($main_categories as $category): ?>
                <div class="category-card">
                    <a href="category.php?slug=<?php echo $category['slug']; ?>">
                        <img src="./<?php echo $category['image']; ?>" alt="<?php echo $category['name']; ?>" />
                        <p><?php echo strtoupper($category['name']); ?> <span>(<?php echo $category['product_count']; ?>)</span></p>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
        <button class="nav-btn next-btn" aria-label="Scroll Right">&#10095;</button>
    </div>
</section>

<!-- Products Offering Discount Section -->
<section class="header0">
    <h5 class="header-title">Products Offering Discount</h5>
    <a href="products.php?discounted=1"><button class="P-Button">View All<span> &gt;</span></button></a>
</section>

<div class="product-carousel-wrapper">
    <button class="nav-btn prev">&#10094;</button>
    <section id="product">
        <?php foreach ($discountedProducts as $product): 
            $inWishlist = in_array($product['id'], $wishlist_ids);
            $isOutOfStock = ($product['stock_quantity'] <= 0);
        ?>
            <div class="card product-card" data-id="prod-<?php echo $product['id']; ?>">
                <?php if ($product['is_discounted']): ?>
                    <div class="discount-banner">SAVE ₹<?php echo $product['mrp'] - $product['selling_price']; ?> (<?php echo $product['discount_percentage']; ?>% OFF)</div>
                <?php endif; ?>
                <div class="product-image">
                    <a href="product.php?slug=<?php echo $product['slug']; ?>">
                        <img src="./<?php echo $product['main_image']; ?>" alt="<?php echo $product['name']; ?>">
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
                        <div class="cart-actions d-flex align-items-center gap-2">
                            <div class="quantity-control d-inline-flex align-items-center">
                                <button type="button" class="btn-qty btn-qty-minus" aria-label="Decrease quantity">-</button>
                                <input type="number" class="quantity-input" value="1" min="1" max="99" data-product-id="<?php echo $product['id']; ?>">
                                <button type="button" class="btn-qty btn-qty-plus" aria-label="Increase quantity">+</button>
                            </div>
                            <button class="add-to-cart add-to-cart-btn" data-product-id="<?php echo $product['id']; ?>">Add to Cart</button>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </section>
    <button class="nav-btn next">&#10095;</button>
</div>

<!-- Featured Products Section -->
<section class="header0">
    <h5 class="header-title">Top 100 Products with Higher Discounts</h5>
    <a href="products.php?featured=1"><button class="P-Button">View All<span> &gt;</span></button></a>
</section>

<div class="product-carousel-wrapper">
    <button class="nav-btn prev">&#10094;</button>
    <section id="featured-products">
        <?php foreach ($featuredProducts as $product): 
            $inWishlist = in_array($product['id'], $wishlist_ids);
            $isOutOfStock = ($product['stock_quantity'] <= 0);
        ?>
            <div class="card product-card" data-id="prod-<?php echo $product['id']; ?>">
                <?php if ($product['is_discounted']): ?>
                    <div class="discount-banner">SAVE ₹<?php echo $product['mrp'] - $product['selling_price']; ?> (<?php echo $product['discount_percentage']; ?>% OFF)</div>
                <?php endif; ?>
                <div class="product-image">
                    <a href="product.php?slug=<?php echo $product['slug']; ?>">
                        <img src="./<?php echo $product['main_image']; ?>" alt="<?php echo $product['name']; ?>">
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
    </section>
    <button class="nav-btn next">&#10095;</button>
</div>

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
/* Hero Banner Carousel - Responsive Fix */
.hero-slider-section {
  width: 100% !important;
  overflow: hidden !important;
  position: relative !important;
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

/* Mobile responsive for featured products */
@media (max-width: 767.98px) {
  #featured-products .product-card {
    width: 250px !important;
    min-width: 250px !important;
    max-width: 250px !important;
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
document.addEventListener('DOMContentLoaded', function() {
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
    
    // Category slider functionality
    const categoryContainer = document.getElementById('slider');
    const prevBtn = document.querySelector('.prev-btn');
    const nextBtn = document.querySelector('.next-btn');
    
    if (categoryContainer && prevBtn && nextBtn) {
        const scrollAmount = 300; // Adjust scroll amount as needed
        
        prevBtn.addEventListener('click', () => {
            categoryContainer.scrollBy({
                left: -scrollAmount,
                behavior: 'smooth'
            });
        });
        
        nextBtn.addEventListener('click', () => {
            categoryContainer.scrollBy({
                left: scrollAmount,
                behavior: 'smooth'
            });
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

</body>
</html> 