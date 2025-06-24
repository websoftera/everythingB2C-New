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
<div class="product-detail-card" data-id="prod-<?php echo $product['id']; ?>">
    <div class="product-image-section position-relative">
        <?php if ($product['is_discounted']): ?>
            <div class="discount-banner1">SAVE ₹<?php echo $product['mrp'] - $product['selling_price']; ?> (<?php echo $product['discount_percentage']; ?>% OFF)</div>
        <?php endif; ?>
        <button class="zoom-icon-btn" onclick="activateZoom()">🔍</button>
        <div class="img-magnifier-container">
            <img id="mainImage" src="./<?php echo $product['main_image']; ?>" alt="<?php echo $product['name']; ?>" />
        </div>
        <?php if (count($productImages) > 1): ?>
            <div class="thumbnail-row">
                <?php foreach ($productImages as $image): ?>
                    <img class="thumbnail" src="./<?php echo $image['image_path']; ?>" alt="<?php echo $product['name']; ?>">
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="product-info-section">
        <h2 class="title"><?php echo $product['name']; ?></h2>
        <div class="price-buttons1">
            <button class="mrp" data-mrp="<?php echo $product['mrp']; ?>">MRP <?php echo formatPrice($product['mrp']); ?></button>
            <button class="pay" data-pay="<?php echo $product['selling_price']; ?>">PAY <?php echo formatPrice($product['selling_price']); ?></button>
            <div class="wishlist">
                <input type="checkbox" class="heart-checkbox" id="wishlist-checkbox-main-<?php echo $product['id']; ?>" data-product-id="<?php echo $product['id']; ?>" <?php echo $inWishlist ? 'checked' : ''; ?>>
                <label for="wishlist-checkbox-main-<?php echo $product['id']; ?>" class="wishlist-label"><i class="fas fa-heart"></i></label>
            </div>
        </div>
        <div class="cart-controls">
            <input type="number" class="quantity-input" value="1" min="1">
            <button class="add-to-cart add-to-cart-btn" data-product-id="<?php echo $product['id']; ?>">ADD TO CART</button>
        </div>
        <p><strong>CATEGORY:</strong> <a href="category.php?slug=<?php echo $product['category_slug']; ?>"><?php echo $product['category_name']; ?></a></p>
        <div class="product-description">
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

<!-- Related Products Section -->
<section class="header1">
    <h5>Related Products</h5>
</section>

<div class="products">
    <?php foreach ($relatedProducts as $relatedProduct): 
        $inWishlist = in_array($relatedProduct['id'], $wishlist_ids);
    ?>
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
    <?php endforeach; ?>
</div>

<!-- Include Footer -->
<?php include 'includes/footer.php'; ?>

<!-- Include CSS and JS -->
<link rel="stylesheet" href="deatil.css">
<script src="detail.js" defer></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Thumbnail click functionality
    const thumbnails = document.querySelectorAll('.thumbnail');
    const mainImage = document.getElementById('mainImage');
    
    thumbnails.forEach(thumb => {
        thumb.addEventListener('click', function() {
            mainImage.src = this.src;
            thumbnails.forEach(t => t.classList.remove('active'));
            this.classList.add('active');
        });
    });
});
</script> 