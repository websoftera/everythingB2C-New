<?php
require_once 'includes/functions.php';

// Get product slug from URL
$slug = $_GET['slug'] ?? '';

if (empty($slug)) {
    header('Location: index.php');
    exit;
}

// Get product details
$product = getProductBySlug($slug);

if (!$product) {
    header('Location: index.php');
    exit;
}

$pageTitle = $product['name'];
require_once 'includes/header.php';

// Get product images
$productImages = getProductImages($product['id']);

// Get related products
$relatedProducts = getRelatedProducts($product['id'], $product['category_id'], 4);

// Get full category path
$categoryPath = getCategoryPath($product['category_id']);

// Check if product is in wishlist
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
$inWishlist = in_array($product['id'], $wishlist_ids);
?>

<!-- Breadcrumb Navigation -->
<?php
$breadcrumbs = generateBreadcrumb($pageTitle, $categoryPath, $product['name']);
echo renderBreadcrumb($breadcrumbs);
?>

<link rel="stylesheet" href="./asset/style/style.css">

<!-- Product Detail Section -->
<div class="product-detail-card modern-card" data-id="prod-<?php echo $product['id']; ?>" data-product-id="<?php echo $product['id']; ?>">
    <div class="product-image-section position-relative">
        <?php if ($product['is_discounted']): ?>
            <div class="discount-banner1">SAVE ₹<?php echo $product['mrp'] - $product['selling_price']; ?> (<?php echo $product['discount_percentage']; ?>% OFF)</div>
        <?php endif; ?>
        <button class="zoom-icon-btn modern-zoom" id="zoomBtn" title="Zoom"><i class="fas fa-search-plus"></i></button>
        <div class="img-magnifier-container" id="mainImageContainer" style="position:relative;">
            <img id="mainImage" src="<?php echo $product['main_image']; ?>" alt="<?php echo cleanProductName($product['name']); ?>" data-index="0" style="width:100%;height:100%;object-fit:contain;border-radius:8px;display:block;" />
            <div id="magnifier" class="img-magnifier-glass" style="display:none;"></div>
        </div>
        <div class="thumbnail-row">
            <img class="thumbnail" src="<?php echo $product['main_image']; ?>" alt="<?php echo cleanProductName($product['name']); ?>">
            <?php foreach ($productImages as $image): ?>
                <?php if ($image['image_path'] !== $product['main_image']): ?>
                    <img class="thumbnail" src="<?php echo $image['image_path']; ?>" alt="<?php echo cleanProductName($product['name']); ?>">
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>
    <div class="product-info-section modern-info">
        <h2 class="title"><?php echo cleanProductName($product['name']); ?></h2>
        <p><strong>SKU:</strong> <?php echo htmlspecialchars($product['sku']); ?></p>
        <?php if (!empty($product['hsn'])): ?>
            <div class="product-hsn"><strong>HSN:</strong> <?php echo htmlspecialchars($product['hsn']); ?></div>
        <?php endif; ?>
        <div class="price-buttons1 modern-prices">
            <div class="price-btn mrp">
                <span class="label">MRP</span>
                <span class="value"><?php echo formatPrice($product['mrp']); ?></span>
            </div>
            <div class="price-btn pay">
                <span class="label">PAY</span>
                <span class="value"><?php echo formatPrice($product['selling_price']); ?></span>
            </div>
            <div class="wishlist">
                <input type="checkbox" class="heart-checkbox" id="wishlist-checkbox-main-<?php echo $product['id']; ?>" data-product-id="<?php echo $product['id']; ?>" <?php echo $inWishlist ? 'checked' : ''; ?>>
                <label for="wishlist-checkbox-main-<?php echo $product['id']; ?>" class="wishlist-label"><i class="fas fa-heart"></i></label>
            </div>
        </div>
        <?php if ($product['stock_quantity'] > 0): ?>
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
        <?php else: ?>
        <div class="out-of-stock-message">
            <button class="btn btn-secondary" disabled style="background-color: #6c757d; color: #fff; padding: 10px 20px; border: none; border-radius: 5px; cursor: not-allowed;">OUT OF STOCK</button>
        </div>
        <?php endif; ?>
        <p><strong>CATEGORY:</strong> 
            <?php foreach ($categoryPath as $i => $cat): ?>
                <a href="category.php?slug=<?php echo $cat['slug']; ?>"><?php echo htmlspecialchars($cat['name']); ?></a><?php if ($i < count($categoryPath) - 1) echo ' &raquo; '; ?>
            <?php endforeach; ?>
        </p>
        <div class="product-description modern-desc">
            <h4>Product Details</h4>
            <p><?php echo $product['description']; ?></p>
        </div>
        <?php if ($product['stock_quantity'] > 0): ?>
            <p class="text-success"><strong>Stock:</strong> <?php echo $product['stock_quantity']; ?> units available</p>
        <?php else: ?>
            <p class="text-danger"><strong>Out of Stock</strong></p>
        <?php endif; ?>
    </div>
</div>

<!-- Zoom Modal -->
<div id="zoomModal" class="zoom-modal">
    <span class="zoom-close" id="zoomClose">&times;</span>
    <img class="zoom-modal-content" id="zoomedImg" src="" alt="Zoomed Product Image">
    <button id="zoomPrev" class="zoom-nav-btn zoom-prev">&#8592;</button>
    <button id="zoomNext" class="zoom-nav-btn zoom-next">&#8594;</button>
</div>

<!-- Related Products Section -->
<section class="related-products-section">
    <div class="related-products-card">
        <div class="related-products-header">
            <h2 class="related-products-title">Related Products</h2>
        </div>
        <div class="related-products-slider-wrapper">
            <button class="related-nav-btn prev-btn" aria-label="Scroll Left">
                <img src="asset/icons/blue_arrow.png" alt="Previous" style="width: 24px; height: 24px;">
            </button>
            <div class="related-products-container" id="related-slider">
                <?php foreach ($relatedProducts as $relatedProduct): 
                    $inWishlist = in_array($relatedProduct['id'], $wishlist_ids);
                    $isOutOfStock = ($relatedProduct['stock_quantity'] <= 0);
                ?>
                    <div class="card product-card" data-id="prod-<?php echo $relatedProduct['id']; ?>">
                        <?php if ($relatedProduct['is_discounted']): ?>
                            <div class="discount-banner">SAVE ₹<?php echo $relatedProduct['mrp'] - $relatedProduct['selling_price']; ?> (<?php echo $relatedProduct['discount_percentage']; ?>% OFF)</div>
                        <?php endif; ?>
                        <div class="product-info">
                          <div class="wishlist">
                            <input type="checkbox" class="heart-checkbox" id="wishlist-checkbox-related-<?php echo $relatedProduct['id']; ?>" data-product-id="<?php echo $relatedProduct['id']; ?>" <?php if ($inWishlist) echo 'checked'; ?>>
                            <label for="wishlist-checkbox-related-<?php echo $relatedProduct['id']; ?>" class="wishlist-label <?php echo $inWishlist ? 'wishlist-active' : ''; ?>">
                                <span class="heart-icon">&#10084;</span>
                            </label>
                          </div>
                          <div class="product-image">
                              <a href="product.php?slug=<?php echo $relatedProduct['slug']; ?>">
                                  <?php if (!empty($relatedProduct['main_image'])): ?>
                                      <img src="<?php echo $relatedProduct['main_image']; ?>" alt="<?php echo cleanProductName($relatedProduct['name']); ?>">
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
                              <h3><?php echo strtoupper(cleanProductName($relatedProduct['name'])); ?></h3>
                              <div class="price-buttons">
                                  <div class="price-btn mrp">
                                      <span class="label">MRP</span>
                                      <span class="value"><?php echo formatPrice($relatedProduct['mrp']); ?></span>
                                  </div>
                                  <div class="price-btn pay">
                                      <span class="label">PAY</span>
                                      <span class="value"><?php echo formatPrice($relatedProduct['selling_price']); ?></span>
                                  </div>
                              </div>
                              <?php if ($isOutOfStock): ?>
                                  <a href="product.php?slug=<?php echo $relatedProduct['slug']; ?>" class="read-more">READ MORE</a>
                              <?php else: ?>
                                  <div class="cart-actions d-flex align-items-center gap-2 justify-content-center">
                                      <div class="quantity-control d-inline-flex align-items-center">
                                          <button type="button" class="btn-qty btn-qty-minus" aria-label="Decrease quantity">-</button>
                                          <input type="number" class="quantity-input" value="1" min="1" max="99" data-product-id="<?php echo $relatedProduct['id']; ?>">
                                          <button type="button" class="btn-qty btn-qty-plus" aria-label="Increase quantity">+</button>
                                      </div>
                                  </div>
                                  <div class="cart-actions d-flex align-items-center gap-2 justify-content-center">
                                      <button class="add-to-cart add-to-cart-btn" data-product-id="<?php echo $relatedProduct['id']; ?>">
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
            <button class="related-nav-btn next-btn" aria-label="Scroll Right">
                <img src="asset/icons/blue_arrow.png" alt="Next" style="transform: rotate(180deg);width: 24px; height: 24px;">
            </button>
        </div>
    </div>
</section>

<!-- Include Footer -->
<?php include 'includes/footer.php'; ?>

<style>
.modern-card {
    padding: 32px 24px;
    margin: 40px auto;
    max-width: 1100px;
    background: #fff;
    display: flex;
    flex-wrap: wrap;
    gap: 32px;
}
.product-image-section { flex: 1 1 350px; min-width: 320px; }
.product-info-section { flex: 2 1 400px; min-width: 320px; }
.modern-zoom { position: absolute; top: 16px; right: 16px; background: #fff; border: none; border-radius: 50%; width: 40px; height: 40px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); display: flex; align-items: center; justify-content: center; font-size: 1.3rem; color: #007bff; cursor: pointer; z-index: 2; transition: background 0.2s; }
.modern-zoom:hover { background: #f0f8ff; }
.img-magnifier-container { position: relative; display: block; width: 400px; height: 400px; max-width: 100%; background: #f8f9fa; border-radius: 8px; overflow: hidden; }
.img-magnifier-glass {
  display: none;
  position: absolute;
  border: 3px solid #007bff;
  border-radius: 50%;
  cursor: none;
  width: 320px;
  height: 320px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.15);
  background-repeat: no-repeat;
  background-size: 200% 200%;
  z-index: 10;
}
.thumbnail-row { display: flex; gap: 10px; margin-top: 16px; }
.thumbnail { width: 80px; height: 80px; object-fit: contain; border-radius: 8px; border: 2px solid transparent; cursor: pointer; transition: border 0.2s; background: #f8f9fa; }
.thumbnail.active, .thumbnail:hover { border: 2px solid #007bff; }
#mainImage { width: 100%; height: 100%; object-fit: contain; border-radius: 8px; display: block; }
.modern-info h2.title { text-align: left; font-size: 20px; font-weight: 700; margin-bottom: 12px; }
.modern-prices { margin-bottom: 18px; }
.modern-cart { margin-bottom: 18px; }
.modern-desc { margin-top: 18px; }
.product-hsn{margin-bottom: 10px;}

/* Product Detail Price Buttons - Matching Products Offering Discount */
.price-buttons1 {
  display: flex;
  gap: 8px;
  align-items: center;
  margin-bottom: 18px;
}

.price-buttons1 .price-btn {
  display: flex;
  flex-direction: row;
  align-items: center;
  justify-content: center;
  padding: 8px 12px;
  border-radius: 4px;
  border: none;
  font-size: 12px;
  font-weight: 600;
  min-width: 80px;
  white-space: nowrap;
  gap: 4px;
}

.price-buttons1 .price-btn.mrp {
  background: var(--mrp-light-blue) !important;
  color: var(--dark-blue) !important;
}

.price-buttons1 .price-btn.pay {
  background: var(--pay-light-green) !important;
  color: var(--dark-grey) !important;
}

.price-buttons1 .price-btn .label {
  font-size: 10px;
  font-weight: 700;
  text-transform: uppercase;
  white-space: nowrap;
  line-height: 1;
}

.price-buttons1 .price-btn .value {
  font-size: 14px;
  font-weight: 700;
  white-space: nowrap;
  line-height: 1;
}

/* Product Detail Cart Actions - One Row Layout */
.cart-actions {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-bottom: 18px;
  justify-content: flex-start;
}

.cart-actions .quantity-control {
  display: flex;
  align-items: center;
  gap: 4px;
}

.cart-actions .add-to-cart-btn {
  background: var(--cart-button);
  color: #ffffff;
  border: none;
  padding: 10px 20px;
  border-radius: 4px;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.2s;
  white-space: nowrap;
}

.cart-actions .add-to-cart-btn:hover {
  background: var(--dark-blue);
}

/* Zoom Modal Styles */
.zoom-modal { display: none; position: fixed; z-index: 9999; left: 0; top: 0; width: 100vw; height: 100vh; overflow: auto; background: rgba(0,0,0,0.7); align-items: center; justify-content: center; }
.zoom-modal-content { margin: auto; display: block; width: 600px; height: 600px; max-width: 90vw; max-height: 90vh; border-radius: 12px; box-shadow: 0 4px 32px rgba(0,0,0,0.25); background: #f8f9fa; object-fit: contain; }
.zoom-close { position: absolute; top: 10px; right: 10px; color: #fff; font-size: 2rem; font-weight: bold; cursor: pointer; z-index: 10001; text-shadow: 0 2px 8px rgba(0,0,0,0.5); background: rgba(0,0,0,0.5); border-radius: 50%; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; transition: background 0.2s; }
.zoom-nav-btn { position: absolute; top: 50%; transform: translateY(-50%); background: rgba(0,0,0,0.5); color: #fff; border: none; border-radius: 50%; width: 48px; height: 48px; font-size: 2rem; cursor: pointer; z-index: 10001; display: flex; align-items: center; justify-content: center; transition: background 0.2s; }
.zoom-prev { left: 40px; }
.zoom-next { right: 40px; }
.zoom-nav-btn:hover { background: #007bff; }

.qty-minus, .qty-plus, .btn-qty-minus, .btn-qty-plus {
  width: 36px;
  height: 36px;
  border: none;
  background: #f0f0f0;
  color: #007bff;
  font-size: 1.5rem;
  border-radius: 4px;
  margin: 0 4px;
  cursor: pointer;
  transition: background 0.2s;
  user-select: none;
  -webkit-user-select: none;
  -moz-user-select: none;
  -ms-user-select: none;
}
.qty-minus:hover, .qty-plus:hover, .btn-qty-minus:hover, .btn-qty-plus:hover {
  background: #e0eaff;
}
.qty-minus:active, .qty-plus:active, .btn-qty-minus:active, .btn-qty-plus:active {
  background: #d0e0ff;
}
.quantity-input {
  width: 60px;
  text-align: center;
  font-size: 1.1rem;
  margin: 0 4px;
  border: 1px solid #ddd;
  border-radius: 4px;
  height: 36px;
}

/* Related Products Section - Matching Products Offering Discount Design */
.related-products-section {
  padding: 20px;
  background:none !important;
  margin: 0;
  overflow: visible;
}

.related-products-card {
  background: var(--light-blue);
  border-radius: 8px;
  border: 1px solid #e0e0e0;
  padding: 20px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  margin: 0 25px;
  position: relative;
  overflow: visible;
}

.related-products-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
}

.related-products-title {
  font-size: 16px;
  font-weight: bold;
  color: var(--dark-blue);
  margin: 0;
  padding-left: 8px;
}

.related-products-slider-wrapper {
  display: flex;
  align-items: center;
  position: relative;
  overflow: visible;
  padding: 0 0px;
  margin: 0 auto;
  width: 100%;
  max-width: 1400px;
}

.related-products-container {
  display: flex;
  gap: 16px;
  overflow-x: auto;
  scroll-behavior: smooth;
  scrollbar-width: none;
  -ms-overflow-style: none;
  padding: 0;
  margin: 0;
}

.related-products-container::-webkit-scrollbar {
  display: none;
}

/* Navigation Buttons */
.related-nav-btn {
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  background: #ffffff;
  border: 1px solid #e0e0e0;
  border-radius: 50%;
  width: 40px;
  height: 40px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  z-index: 10;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  transition: all 0.3s ease;
}

.related-nav-btn:hover {
  background: #f8f9fa;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.related-nav-btn.prev-btn {
  left: -20px;
}

.related-nav-btn.next-btn {
  right: -20px;
}

/* Product Cards - Matching Products Offering Discount */
.related-products-container .product-card {
  background: #fff !important;
  border-radius: 8px !important;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1) !important;
  border: 1px solid var(--light-blue) !important;
  min-width: 220px;
  flex-shrink: 0;
}

.related-products-container .product-info {
  padding: 5px 6px !important;
}

.related-products-container .product-card .product-image img {
  max-height: 155px !important;
  min-height: 155px !important;
}

.related-products-container .product-card .discount-banner {
  background: var(--site-blue) !important;
  color: #fff !important;
  border-radius: 4px !important;
}

.related-products-container .product-card .price-btn.mrp {
  background: var(--mrp-light-blue) !important;
  color: var(--dark-blue) !important;
}

.related-products-container .product-card .price-btn.pay {
  background: var(--pay-light-green) !important;
  color: var(--dark-grey) !important;
}

.related-products-container .product-card .add-to-cart-btn,
.related-products-container .product-card .add-to-cart {
  background: var(--cart-button) !important;
  color: #ffffff !important;
}

.related-products-container .product-card .add-to-cart-btn:hover,
.related-products-container .product-card .add-to-cart:hover {
  background: var(--dark-blue) !important;
}

.related-products-container .product-card .product-details {
  background-image: none !important;
}

.related-products-container .product-card .product-image {
  background-image: none !important;
}

@media (max-width: 900px) { 
  .modern-card { flex-direction: column; padding: 18px 6px; } 
  .product-image-section, .product-info-section { min-width: 0; } 
  .img-magnifier-container { width: 100%; height: 350px; } 
  .zoom-modal-content { width: 500px; height: 500px; max-width: 98vw; max-height: 70vh; } 
  .zoom-prev { left: 10px; } 
  .zoom-next { right: 10px; } 
  .zoom-close { top: 5px; right: 5px; width: 35px; height: 35px; font-size: 1.5rem; } 
  .img-magnifier-glass { display: none !important; } 
}
@media (max-width: 600px) { 
  .img-magnifier-container { height: 300px; } 
  .thumbnail { width: 60px; height: 60px; } 
  .zoom-modal-content { width: 400px; height: 400px; } 
}
@media (max-width: 480px) { 
  .img-magnifier-container { height: 250px; } 
  .thumbnail { width: 50px; height: 50px; } 
  .zoom-modal-content { width: 350px; height: 350px; } 
}

/* Responsive Design for Related Products */
@media (max-width: 768px) {
  .related-products-section {
    padding: 15px;
  }
  
  .related-products-card {
    margin: 0 15px;
    padding: 15px;
  }
  
  .related-nav-btn {
    width: 35px;
    height: 35px;
  }
  
  .related-nav-btn.prev-btn {
    left: -15px;
  }
  
  .related-nav-btn.next-btn {
    right: -15px;
  }
}

@media (max-width: 576px) {
  .related-products-section {
    padding: 10px;
  }
  
  .related-products-card {
    margin: 0 10px;
    padding: 10px;
  }
  
  .related-nav-btn {
    width: 30px;
    height: 30px;
  }
  
  .related-nav-btn.prev-btn {
    left: -10px;
  }
  
  .related-nav-btn.next-btn {
    right: -10px;
  }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const thumbnails = document.querySelectorAll('.thumbnail');
    const mainImage = document.getElementById('mainImage');
    const mainImageContainer = document.getElementById('mainImageContainer');
    const magnifier = document.getElementById('magnifier');
    if (!mainImage || !mainImageContainer) return;
    
    // Mark the first thumbnail as active on load
    if (thumbnails.length > 0) {
        thumbnails[0].classList.add('active');
    }
    
    thumbnails.forEach((thumb, idx) => {
        thumb.addEventListener('click', function() {
            thumbnails.forEach(t => t.classList.remove('active'));
            mainImage.src = this.src;
            mainImage.setAttribute('data-index', idx);
            this.classList.add('active');
        });
    });
    
    // --- Magnifier effect ---
    function magnify(img, zoom) {
        let glass = magnifier;
        glass.style.backgroundImage = `url('${img.src}')`;
        glass.style.backgroundRepeat = 'no-repeat';
        glass.style.backgroundSize = (img.width * zoom) + 'px ' + (img.height * zoom) + 'px';
        let bw = 3;
        let w = glass.offsetWidth / 2;
        let h = glass.offsetHeight / 2;
        
        function moveMagnifier(e) {
            let pos = getCursorPos(e);
            let x = pos.x;
            let y = pos.y;
            if (x > img.width - (w / zoom)) { x = img.width - (w / zoom); }
            if (x < w / zoom) { x = w / zoom; }
            if (y > img.height - (h / zoom)) { y = img.height - (h / zoom); }
            if (y < h / zoom) { y = h / zoom; }
            glass.style.left = (x - w) + 'px';
            glass.style.top = (y - h) + 'px';
            glass.style.backgroundPosition = '-' + ((x * zoom) - w + bw) + 'px -' + ((y * zoom) - h + bw) + 'px';
        }
        
        function getCursorPos(e) {
            let a = img.getBoundingClientRect();
            let x = e.pageX - a.left - window.pageXOffset;
            let y = e.pageY - a.top - window.pageYOffset;
            return { x: x, y: y };
        }
        
        glass.onmousemove = moveMagnifier;
        img.onmousemove = moveMagnifier;
        glass.ontouchmove = moveMagnifier;
        img.ontouchmove = moveMagnifier;
    }
    
    mainImage.addEventListener('mouseenter', function() {
        magnifier.style.display = 'block';
        magnify(mainImage, 2);
    });
    
    mainImage.addEventListener('mouseleave', function() {
        magnifier.style.display = 'none';
    });
    
    mainImage.addEventListener('mousemove', function(e) {
        if (magnifier.style.display === 'block') {
            magnifier.onmousemove(e);
        }
    });
    
    // Update magnifier when image changes
    thumbnails.forEach((thumb) => {
        thumb.addEventListener('click', function() {
            if (magnifier.style.display === 'block') {
                magnify(mainImage, 2);
            }
        });
    });
    
    // --- Zoom Modal with navigation ---
    const zoomModal = document.getElementById('zoomModal');
    const zoomedImg = document.getElementById('zoomedImg');
    const zoomClose = document.getElementById('zoomClose');
    const zoomPrev = document.getElementById('zoomPrev');
    const zoomNext = document.getElementById('zoomNext');
    const productImages = Array.from(thumbnails).map(t => t.getAttribute('src'));
    let zoomIdx = 0;
    
    function showZoom(idx) {
        zoomedImg.src = productImages[idx];
        zoomIdx = idx;
        if (productImages.length > 1) {
            zoomPrev.style.display = 'flex';
            zoomNext.style.display = 'flex';
        } else {
            zoomPrev.style.display = 'none';
            zoomNext.style.display = 'none';
        }
    }
    
    document.getElementById('zoomBtn').onclick = function() {
        showZoom(parseInt(mainImage.getAttribute('data-index')) || 0);
        zoomModal.style.display = 'flex';
    };
    
    zoomClose.onclick = function() {
        zoomModal.style.display = 'none';
    };
    
    if (productImages.length > 1) {
        zoomPrev.onclick = function(e) {
            e.stopPropagation();
            zoomIdx = (zoomIdx - 1 + productImages.length) % productImages.length;
            showZoom(zoomIdx);
        };
        zoomNext.onclick = function(e) {
            e.stopPropagation();
            zoomIdx = (zoomIdx + 1) % productImages.length;
            showZoom(zoomIdx);
        };
    }
    
    zoomModal.onclick = function(e) {
        if (e.target === this) zoomModal.style.display = 'none';
    };
    
    // Related Products Slider
    const relatedSlider = document.getElementById('related-slider');
    const prevBtn = document.querySelector('.related-nav-btn.prev-btn');
    const nextBtn = document.querySelector('.related-nav-btn.next-btn');
    
    if (relatedSlider && prevBtn && nextBtn) {
        let scrollAmount = 0;
        const cardWidth = 220;
        const gap = 16;
        const scrollStep = cardWidth + gap;
        
        function updateScrollButtons() {
            const maxScroll = relatedSlider.scrollWidth - relatedSlider.clientWidth;
            prevBtn.style.display = scrollAmount <= 0 ? 'none' : 'flex';
            nextBtn.style.display = scrollAmount >= maxScroll ? 'none' : 'flex';
        }
        
        prevBtn.addEventListener('click', function() {
            scrollAmount = Math.max(0, scrollAmount - scrollStep);
            relatedSlider.scrollTo({
                left: scrollAmount,
                behavior: 'smooth'
            });
        });
        
        nextBtn.addEventListener('click', function() {
            const maxScroll = relatedSlider.scrollWidth - relatedSlider.clientWidth;
            scrollAmount = Math.min(maxScroll, scrollAmount + scrollStep);
            relatedSlider.scrollTo({
                left: scrollAmount,
                behavior: 'smooth'
            });
        });
        
        relatedSlider.addEventListener('scroll', function() {
            scrollAmount = relatedSlider.scrollLeft;
            updateScrollButtons();
        });
        
        updateScrollButtons();
        window.addEventListener('resize', updateScrollButtons);
    }
});
</script> 