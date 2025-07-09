<?php
$pageTitle = 'Home';
require_once 'includes/header.php';

// Get data from database
$categories = getAllCategories();
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
                        <div class="cart-actions">
                            <button class="add-to-cart add-to-cart-btn" data-product-id="<?php echo $product['id']; ?>">ADD TO CART</button>
                            <input type="number" class="quantity-input" value="1" min="1">
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
    <h5 class="header-title">Featured Products</h5>
    <a href="products.php?featured=1"><button class="P-Button">View All<span> &gt;</span></button></a>
</section>

<div class="product-carousel-wrapper">
    <button class="nav-btn prev">&#10094;</button>
    <section id="featured-products" style="display: flex; flex-wrap: nowrap; overflow-x: scroll; gap: 20px; padding: 20px; max-width: 100%; scroll-behavior: smooth; -ms-overflow-style: none; scrollbar-width: none; background-color: #fff; box-shadow: 0 0 8px rgba(28, 27, 27, 0.1);">
        <?php foreach ($featuredProducts as $product): 
            $inWishlist = in_array($product['id'], $wishlist_ids);
        ?>
            <div class="card" data-id="prod-<?php echo $product['id']; ?>" style="flex: 0 0 20%; min-width: 200px; max-width: 250px; margin: 0; float: none; display: block;">
                <?php if ($product['is_discounted']): ?>
                    <div class="discount-banner">SAVE ₹<?php echo $product['mrp'] - $product['selling_price']; ?> (<?php echo $product['discount_percentage']; ?>% OFF)</div>
                <?php endif; ?>
                <div class="product-image">
                    <a href="product.php?slug=<?php echo $product['slug']; ?>">
                        <img src="./<?php echo $product['main_image']; ?>" alt="<?php echo $product['name']; ?>">
                    </a>
                </div>
                <div class="product-details">
                    <h3><?php echo $product['name']; ?></h3>
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
                    <div class="cart-actions">
                        <button class="add-to-cart add-to-cart-btn" data-product-id="<?php echo $product['id']; ?>">Add to Cart</button>
                        <input type="number" class="quantity-input" value="1" min="1">
                    </div>
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

<script>
document.addEventListener('DOMContentLoaded', function() {
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