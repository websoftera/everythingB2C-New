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
<div class="product-page-container">
    <?php
    $breadcrumbs = generateBreadcrumb($pageTitle, $categoryPath, $product['name']);
    echo renderBreadcrumb($breadcrumbs);
    ?>

    <link rel="stylesheet" href="asset/style/product-detail.css">

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
                                      <div class="cart-actions d-flex align-items-center gap-2">
                                          <div class="quantity-control d-inline-flex align-items-center">
                                              <button type="button" class="btn-qty btn-qty-minus" aria-label="Decrease quantity">-</button>
                                              <input type="number" class="quantity-input" value="1" min="1" max="99" data-product-id="<?php echo $relatedProduct['id']; ?>">
                                              <button type="button" class="btn-qty btn-qty-plus" aria-label="Increase quantity">+</button>
                                          </div>
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
</div>

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