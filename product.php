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

<!-- Banner/Breadcrumb (skip homepage) -->
<div class="page-banner" style="background: url('asset/images/internalpage-bg.webp') center/cover no-repeat; min-height: 240px; display: flex; align-items: center;">
    <div class="container">
        <h2 style="color: #fff; font-size: 2rem; font-weight: bold; text-shadow: 0 2px 8px rgba(0,0,0,0.3); margin: 0; padding: 32px 0;">
            <?php echo htmlspecialchars($pageTitle); ?>
        </h2>
    </div>
</div>

<!-- Product Detail Section -->
<div class="product-detail-card modern-card" data-id="prod-<?php echo $product['id']; ?>">
    <div class="product-image-section position-relative">
        <?php if ($product['is_discounted']): ?>
            <div class="discount-banner1">SAVE ₹<?php echo $product['mrp'] - $product['selling_price']; ?> (<?php echo $product['discount_percentage']; ?>% OFF)</div>
        <?php endif; ?>
        <button class="zoom-icon-btn modern-zoom" id="zoomBtn" title="Zoom"><i class="fas fa-search-plus"></i></button>
        <div class="img-magnifier-container" id="mainImageContainer" style="position:relative;">
            <img id="mainImage" src="<?php echo $product['main_image']; ?>" alt="<?php echo $product['name']; ?>" data-index="0" style="width:100%;max-width:400px;" />
            <div id="magnifier" class="img-magnifier-glass" style="display:none;"></div>
        </div>
        <div class="thumbnail-row">
            <img class="thumbnail" src="<?php echo $product['main_image']; ?>" alt="<?php echo $product['name']; ?>">
            <?php foreach ($productImages as $image): ?>
                <?php if ($image['image_path'] !== $product['main_image']): ?>
                    <img class="thumbnail" src="<?php echo $image['image_path']; ?>" alt="<?php echo $product['name']; ?>">
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>
    <div class="product-info-section modern-info">
        <h2 class="title"><?php echo $product['name']; ?></h2>
        <p><strong>SKU:</strong> <?php echo htmlspecialchars($product['sku']); ?></p>
        <?php if (!empty($product['hsn'])): ?>
            <div class="product-hsn"><strong>HSN:</strong> <?php echo htmlspecialchars($product['hsn']); ?></div>
        <?php endif; ?>
        <div class="price-buttons1 modern-prices">
            <button class="mrp" data-mrp="<?php echo $product['mrp']; ?>">MRP <?php echo formatPrice($product['mrp']); ?></button>
            <button class="pay" data-pay="<?php echo $product['selling_price']; ?>">PAY <?php echo formatPrice($product['selling_price']); ?></button>
            <div class="wishlist">
                <input type="checkbox" class="heart-checkbox" id="wishlist-checkbox-main-<?php echo $product['id']; ?>" data-product-id="<?php echo $product['id']; ?>" <?php echo $inWishlist ? 'checked' : ''; ?>>
                <label for="wishlist-checkbox-main-<?php echo $product['id']; ?>" class="wishlist-label"><i class="fas fa-heart"></i></label>
            </div>
        </div>
        <div class="cart-controls modern-cart">
            <div class="quantity-control d-inline-flex align-items-center">
                <button type="button" class="btn-qty btn-qty-minus" aria-label="Decrease quantity">-</button>
                <input type="number" class="quantity-input" value="1" min="1">
                <button type="button" class="btn-qty btn-qty-plus" aria-label="Increase quantity">+</button>
            </div>
            <button class="add-to-cart add-to-cart-btn" data-product-id="<?php echo $product['id']; ?>">ADD TO CART</button>
        </div>
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
<style>
.product-detail-main-card { background: #fff; border-radius: 18px; box-shadow: 0 4px 24px rgba(22,186,228,0.08); padding: 32px 24px; max-width: 1100px; margin: 0 auto; }
.main-product-image { border-radius: 12px; box-shadow: 0 2px 12px rgba(34,52,89,0.07); background: #fff; }
.thumbnail.active, .thumbnail:hover { border: 2px solid var(--primary-color) !important; }
@media (max-width: 900px) { .product-detail-main-card { padding: 16px 4px; } }
@media (max-width: 600px) { .product-detail-main-card { padding: 6px 0; } }
</style>
<!-- Zoom Modal and existing scripts/styles remain unchanged -->

<!-- Zoom Modal -->
<div id="zoomModal" class="zoom-modal">
    <span class="zoom-close" id="zoomClose">&times;</span>
    <img class="zoom-modal-content" id="zoomedImg" src="" alt="Zoomed Product Image">
    <button id="zoomPrev" class="zoom-nav-btn zoom-prev">&#8592;</button>
    <button id="zoomNext" class="zoom-nav-btn zoom-next">&#8594;</button>
</div>

<style>
.modern-card {
    /* box-shadow: 0 4px 24px rgba(0,0,0,0.10); */
    /* border-radius: 16px; */
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
.img-magnifier-container { position: relative; display: flex; }
.img-magnifier-glass {
  display: none;
  position: absolute;
  border: 3px solid #007bff;
  border-radius: 50%;
  cursor: none;
  /*set the size of the magnifier glass:*/
  width: 120px;
  height: 120px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.15);
  background-repeat: no-repeat;
  background-size: 200% 200%;
  z-index: 10;
}
.thumbnail-row { display: flex; gap: 10px; margin-top: 16px; }
.thumbnail { width: 60px; height: 60px; object-fit: cover; border-radius: 8px; border: 2px solid transparent; cursor: pointer; transition: border 0.2s; }
.thumbnail.active, .thumbnail:hover { border: 2px solid #007bff; }
.modern-info h2.title { text-align: left; font-size: 20px; font-weight: 700; margin-bottom: 12px; }
.modern-prices { margin-bottom: 18px; }
.modern-cart { margin-bottom: 18px; }
.modern-desc { margin-top: 18px; }
.product-hsn{margin-bottom: 10px;}
/* Zoom Modal Styles */
.zoom-modal { display: none; position: fixed; z-index: 9999; left: 0; top: 0; width: 100vw; height: 100vh; overflow: auto; background: rgba(0,0,0,0.7); align-items: center; justify-content: center; }
.zoom-modal-content { margin: auto; display: block; max-width: 90vw; max-height: 90vh; border-radius: 12px; box-shadow: 0 4px 32px rgba(0,0,0,0.25); background: #fff; }
.zoom-close { position: absolute; top: 40px; right: 60px; color: #fff; font-size: 2.5rem; font-weight: bold; cursor: pointer; z-index: 10001; text-shadow: 0 2px 8px rgba(0,0,0,0.5); }
.zoom-nav-btn { position: absolute; top: 50%; transform: translateY(-50%); background: rgba(0,0,0,0.5); color: #fff; border: none; border-radius: 50%; width: 48px; height: 48px; font-size: 2rem; cursor: pointer; z-index: 10001; display: flex; align-items: center; justify-content: center; transition: background 0.2s; }
.zoom-prev { left: 40px; }
.zoom-next { right: 40px; }
.zoom-nav-btn:hover { background: #007bff; }
@media (max-width: 900px) { .modern-card { flex-direction: column; padding: 18px 6px; } .product-image-section, .product-info-section { min-width: 0; } .zoom-modal-content { max-width: 98vw; max-height: 70vh; } .zoom-prev { left: 10px; } .zoom-next { right: 10px; } .img-magnifier-glass { display: none !important; } }
.qty-minus, .qty-plus {
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
}
.qty-minus:hover, .qty-plus:hover {
  background: #e0eaff;
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
});
</script>

<!-- Swiper CSS for slider -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.css" />

<!-- Related Products Section -->
<section class="header1">
    <h5>Related Products</h5>
</section>

<div class="swiper related-swiper">
    <div class="swiper-wrapper">
        <?php foreach ($relatedProducts as $relatedProduct): 
            $inWishlist = in_array($relatedProduct['id'], $wishlist_ids);
        ?>
            <div class="swiper-slide">
                <div class="card" data-id="prod-<?php echo $relatedProduct['id']; ?>">
                    <?php if ($relatedProduct['is_discounted']): ?>
                        <div class="discount-banner">SAVE ₹<?php echo $relatedProduct['mrp'] - $relatedProduct['selling_price']; ?> (<?php echo $relatedProduct['discount_percentage']; ?>% OFF)</div>
                    <?php endif; ?>
                    <div class="product-image">
                        <a href="product.php?slug=<?php echo $relatedProduct['slug']; ?>">
                            <img src="./<?php echo $relatedProduct['main_image']; ?>" alt="<?php echo $relatedProduct['name']; ?>">
                        </a>
                    </div>
                    <div class="product-details">
                        <h3><?php echo $relatedProduct['name']; ?></h3>
                        <div class="price-buttons">
                            <button class="mrp" data-mrp="<?php echo $relatedProduct['mrp']; ?>">MRP <?php echo formatPrice($relatedProduct['mrp']); ?></button>
                            <button class="pay" data-pay="<?php echo $relatedProduct['selling_price']; ?>">PAY <?php echo formatPrice($relatedProduct['selling_price']); ?></button>
                            <div class="wishlist">
                                <input type="checkbox" class="heart-checkbox" id="wishlist-checkbox-related-<?php echo $relatedProduct['id']; ?>" data-product-id="<?php echo $relatedProduct['id']; ?>" <?php if ($inWishlist) echo 'checked'; ?>>
                                <label for="wishlist-checkbox-related-<?php echo $relatedProduct['id']; ?>" class="wishlist-label"><i class="fas fa-heart"></i></label>
                            </div>
                        </div>
                        <div class="cart-actions">
                            <button class="add-to-cart add-to-cart-btn" data-product-id="<?php echo $relatedProduct['id']; ?>">ADD TO CART</button>
                            <input type="number" class="quantity-input" value="1" min="1">
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <!-- Add Arrows -->
    <div class="swiper-button-next"></div>
    <div class="swiper-button-prev"></div>
</div>

<!-- Swiper JS for slider -->
<script src="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    new Swiper('.related-swiper', {
        slidesPerView: 1,
        spaceBetween: 20,
        navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev',
        },
        breakpoints: {
            600: { slidesPerView: 2 },
            900: { slidesPerView: 3 },
            1200: { slidesPerView: 4 }
        },
        loop: true,
        grabCursor: true,
    });
});
</script>

<!-- Include Footer -->
<?php include 'includes/footer.php'; ?>

<!-- Include CSS and JS -->
<link rel="stylesheet" href="deatil.css">
<script src="detail.js" defer></script> 