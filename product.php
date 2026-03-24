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

$pageTitle = strip_tags($product['name']);
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
<div class="container-fluid" style="padding: 0 15px;">
    <?php
    $breadcrumbs = generateBreadcrumb(strip_tags($pageTitle), $categoryPath, strip_tags($product['name']));
    echo renderBreadcrumb($breadcrumbs);
    ?>
</div>

<div class="product-page-container">
    <link rel="stylesheet" href="asset/style/product-detail.css">
    <style>
        /* Modern Card base fixes */
        .product-page-container .modern-card {
            padding: 0 !important;
            overflow: hidden !important;
        }

        /* Responsive Discount Banner Styling */
        .product-page-container .discount-banner-detail {
            background: #0c79e7 !important;
            color: #fff !important;
            padding: 12px 20px !important;
            font-weight: 700 !important;
            font-size: 14px !important;
            display: block !important;
            text-align: center !important;
            text-transform: uppercase !important;
            margin-left: -24px !important;
            margin-right: -24px !important;
            margin-bottom: 20px !important;
            width: calc(100% + 48px) !important;
            border-radius: 0 !important;
            position: relative;
            z-index: 5;
        }
        
        /* Ensure top of banner touches the top of the card */
        .product-page-container .product-image-section {
            padding-top: 0 !important;
            padding-left: 24px !important;
            padding-right: 24px !important;
            padding-bottom: 32px !important;
        }

        /* Move zoom icon down to avoid collision with banner */
        .product-page-container .modern-zoom {
            top: 65px !important;
        }

        /* Mobile specific adjustments */
        @media (max-width: 900px) {
            .product-page-container .modern-card {
                margin: 2px 0 !important;
                padding: 4px 10px !important;
            }

            .product-page-container .product-image-section {
                padding-bottom: 2px !important; /* Remove gap after main image section */
            }

            .product-page-container .thumbnail-row {
                margin-top: 4px !important; /* Space between main img & gallery */
            }

            .product-page-container .product-info-section {
                padding: 12px !important; /* Standardized uniform padding for interior nodes border safety */
            }

            .product-page-container .modern-info h2.title {
                margin-top: 2px !important;
                margin-bottom: 4px !important; /* Product title top bottom space */
                font-size: 18px !important;
            }

            .product-page-container .sku-row, 
            .product-page-container .product-hsn {
                margin-bottom: 4px !important;
            }

            /* Set exact manual layouts for the two rows */
            .product-page-container .price-buttons1,
            .product-page-container .cart-actions {
                display: flex !important;
                flex-direction: row !important;
                flex-wrap: nowrap !important;
                justify-content: flex-start !important;
                gap: 0 !important;
                width: 100% !important;
            }

            .product-page-container .price-buttons1 {
                margin-bottom: 10px !important;
            }

            .product-page-container .cart-actions {
                margin-bottom: 10px !important;
            }

            /* Exact Math: 50% - 24px for identical parts to allow 12px right gap */
            .product-page-container .price-buttons1 .price-btn,
            .product-page-container .cart-actions .quantity-control {
                flex: 0 0 calc(50% - 24px) !important;
                min-width: calc(50% - 24px) !important;
                max-width: calc(50% - 24px) !important;
                width: calc(50% - 24px) !important;
                height: 32px !important;
                min-height: 32px !important;
                max-height: 32px !important;
                padding: 0 2px !important;
                margin: 0 !important;
                margin-right: 5px !important; /* Exact 5px gap */
                display: flex !important;
                align-items: center !important;
                justify-content: center !important;
                box-sizing: border-box !important;
                overflow: hidden !important;
                white-space: nowrap !important;
            }

            /* Wishlist specific exact layout */
            .product-page-container .price-buttons1 .wishlist {
                flex: 0 0 26px !important;
                width: 26px !important;
                min-width: 26px !important;
                max-width: 26px !important;
                margin: 0 !important;
                margin-left: auto !important; /* Push to right */
                display: flex !important;
                justify-content: flex-end !important;
                align-items: center !important;
            }
            .product-page-container .price-buttons1 .wishlist-label {
                font-size: 22px !important;
                color: #DE0085 !important;
            }

            /* Quantity Inner Buttons exact layout */
            .product-page-container .product-info-section .quantity-control .btn-qty {
                height: 100% !important;
                flex: 0 0 20px !important;
                width: 20px !important;
                min-width: 20px !important;
                max-width: 20px !important;
                padding: 0 !important;
            }
            .product-page-container .product-info-section .quantity-control .quantity-input {
                height: 100% !important;
                flex: 1 1 auto !important;
                width: 100% !important;
                max-width: none !important;
                min-width: 0 !important;
            }

            /* Add to Cart exact layout to fill gap perfectly */
            .product-page-container .product-info-section .cart-actions .add-to-cart-btn {
                flex: 1 1 auto !important;
                min-width: 0 !important;
                height: 32px !important;
                min-height: 32px !important;
                max-height: 32px !important;
                margin: 0 !important;
                margin-right: 12px !important; /* Gap from the absolute right edge */
                padding: 0 !important;
            }

            .product-page-container .product-info-section p.text-success,
            .product-page-container .product-info-section p.text-danger {
                margin-bottom: 2px !important; /* Space after stock */
            }

            .product-page-container .product-description {
                margin-top: 5px !important;
            }
        }

        /* Typography Standardisation */
        .product-page-container .sku-row, 
        .product-page-container .product-hsn {
            font-size: 14px !important;
            color: #333 !important;
            margin-bottom: 8px !important;
        }
        .product-page-container .sku-row strong, 
        .product-page-container .product-hsn strong {
            font-weight: 700 !important;
            color: #333 !important;
        }
        .product-page-container .product-description h4 {
            font-size: 18px !important;
            font-weight: 700 !important;
            color: #333 !important;
            margin-bottom: 12px !important;
            text-align: left !important;
        }
    </style>

    <!-- Product Detail Section -->
    <div class="product-detail-card modern-card" data-id="prod-<?php echo $product['id']; ?>" data-product-id="<?php echo $product['id']; ?>">
        <div class="product-image-section position-relative">
            <?php if ($product['is_discounted']): ?>
                <div class="discount-banner-detail">SAVE ₹<?php echo $product['mrp'] - $product['selling_price']; ?> (<?php echo $product['discount_percentage']; ?>% OFF)</div>
            <?php endif; ?>
            <button class="zoom-icon-btn modern-zoom" id="zoomBtn" title="Zoom"><i class="fas fa-search-plus"></i></button>
            <div class="img-magnifier-container" id="mainImageContainer" style="position:relative;">
                <?php if (!empty($product['main_image'])): ?>
                    <img id="mainImage" src="<?php echo $product['main_image']; ?>" alt="<?php echo cleanProductName($product['name']); ?>" data-index="0" style="width:100%;height:100%;object-fit:contain;border-radius:8px;display:block;" />
                <?php else: ?>
                    <img id="mainImage" src="./uploads/products/blank-img.webp" alt="No image available" data-index="0" style="width:100%;height:100%;object-fit:contain;border-radius:8px;display:block;" />
                <?php endif; ?>
                <div id="magnifier" class="img-magnifier-glass" style="display:none;"></div>
            </div>
            <div class="thumbnail-row">
                <?php if (!empty($product['main_image'])): ?>
                    <img class="thumbnail" src="<?php echo $product['main_image']; ?>" alt="<?php echo cleanProductName($product['name']); ?>">
                <?php else: ?>
                    <img class="thumbnail" src="./uploads/products/blank-img.webp" alt="No image available">
                <?php endif; ?>
                <?php foreach ($productImages as $image): ?>
                    <?php if ($image['image_path'] !== $product['main_image']): ?>
                        <img class="thumbnail" src="<?php echo $image['image_path']; ?>" alt="<?php echo cleanProductName($product['name']); ?>">
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="product-info-section modern-info">
            <h2 class="title"><?php echo cleanProductName($product['name']); ?></h2>
            <p class="sku-row"><strong>SKU:</strong> <?php echo htmlspecialchars($product['sku']); ?></p>
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
                    <label for="wishlist-checkbox-main-<?php echo $product['id']; ?>" class="wishlist-label <?php echo $inWishlist ? 'wishlist-active' : ''; ?>">
                        <i class="bi <?php echo $inWishlist ? 'bi-heart-fill' : 'bi-heart'; ?> header-wishlist-icon"></i>
                    </label>
                </div>
            </div>
            <?php if ($product['stock_quantity'] > 0): ?>
            <div class="cart-action-btns cart-actions d-flex align-items-center gap-2">
                <div class="quantity-control d-inline-flex align-items-center">
                    <button type="button" class="btn-qty btn-qty-minus" aria-label="Decrease quantity">-</button>
                    <input type="number" class="quantity-input" value="1" min="1" max="99" data-product-id="<?php echo $product['id']; ?>">
                    <button type="button" class="btn-qty btn-qty-plus" aria-label="Increase quantity">+</button>
                </div>
                                            <button class="add-to-cart add-to-cart-btn" data-product-id="<?php echo $product['id']; ?>">
                                    <i class="fas fa-shopping-cart"></i>
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
                    <img src="asset/icons/blue_arrow.png" alt="Previous">
                </button>
                <div class="related-products-container" id="related-slider">
                    <?php foreach ($relatedProducts as $relatedProduct): 
                        $inWishlist = in_array($relatedProduct['id'], $wishlist_ids);
                        $isOutOfStock = ($relatedProduct['stock_quantity'] <= 0);
                    ?>
                        <div class="card product-card" data-id="prod-<?php echo $relatedProduct['id']; ?>">
                            <?php if ($relatedProduct['is_discounted']): ?>
                                <div class="discount-banner">SAVE ₹<?php echo $relatedProduct['mrp'] - $relatedProduct['selling_price']; ?> (<?php echo $relatedProduct['discount_percentage']; ?>% OFF)</div>
                            <?php else: ?>
                                <div class="discount-banner" style="visibility: hidden;">&nbsp;</div>
                            <?php endif; ?>
                            
                            <div class="product-info">
                                <div class="product-image">
                                    <a href="product.php?slug=<?php echo $relatedProduct['slug']; ?>">
                                        <?php if (!empty($relatedProduct['main_image'])): ?>
                                            <img src="<?php echo $relatedProduct['main_image']; ?>" alt="<?php echo cleanProductName($relatedProduct['name']); ?>">
                                        <?php else: ?>
                                            <img src="./uploads/products/blank-img.webp" alt="No image available">
                                        <?php endif; ?>
                                    </a>
                                    <?php if ($isOutOfStock): ?>
                                        <div class="out-of-stock">OUT OF STOCK</div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="product-details">
                                    <a href="product.php?slug=<?php echo $relatedProduct['slug']; ?>" class="product-title-link">
                                        <h3><?php echo cleanProductName($relatedProduct['name']); ?></h3>
                                    </a>
                                    
                                    <div class="price-buttons">
                                        <div class="price-btn mrp">
                                            <span class="label">MRP</span>
                                            <span class="value"><?php echo formatPrice($relatedProduct['mrp']); ?></span>
                                        </div>
                                        <div class="price-btn pay">
                                            <span class="label">PAY</span>
                                            <span class="value"><?php echo formatPrice($relatedProduct['selling_price']); ?></span>
                                        </div>
                                        <div class="wishlist">
                                            <input type="checkbox" class="heart-checkbox" id="wishlist-checkbox-related-<?php echo $relatedProduct['id']; ?>" data-product-id="<?php echo $relatedProduct['id']; ?>" <?php if ($inWishlist) echo 'checked'; ?>>
                                            <label for="wishlist-checkbox-related-<?php echo $relatedProduct['id']; ?>" class="wishlist-label <?php echo $inWishlist ? 'wishlist-active' : ''; ?>">
                                                <i class="bi <?php echo $inWishlist ? 'bi-heart-fill' : 'bi-heart'; ?> header-wishlist-icon"></i>
                                            </label>
                                        </div>
                                    </div>
                                    
                                    <?php if ($isOutOfStock): ?>
                                        <a href="product.php?slug=<?php echo $relatedProduct['slug']; ?>" class="read-more">READ MORE</a>
                                    <?php else: ?>
                                        <div class="cart-actions d-flex align-items-center">
                                            <div class="quantity-control d-inline-flex align-items-center">
                                                <button type="button" class="btn-qty btn-qty-minus" aria-label="Decrease quantity">-</button>
                                                <input type="number" class="quantity-input" value="1" min="1" max="99" data-product-id="<?php echo $relatedProduct['id']; ?>">
                                                <button type="button" class="btn-qty btn-qty-plus" aria-label="Increase quantity">+</button>
                                            </div>
                                            <button class="add-to-cart add-to-cart-btn" data-product-id="<?php echo $relatedProduct['id']; ?>">
                                                <i class="fas fa-shopping-cart"></i>
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
                    <img src="asset/icons/blue_arrow.png" alt="Next" style="transform: rotate(180deg);">
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

<?php include 'includes/footer.php'; ?> 