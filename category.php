<?php
// Prevent caching
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

require_once 'includes/functions.php';

// Get category slug from URL
$slug = $_GET['slug'] ?? '';

if (empty($slug)) {
    header('Location: index.php');
    exit;
}

// Get category details
$category = getCategoryBySlug($slug);

if (!$category) {
    header('Location: index.php');
    exit;
}

$pageTitle = $category['name'];
require_once 'includes/header.php';

// Get products in this category
$products = getProductsByCategory($category['id']);

// Get user's wishlist for quick lookup
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

// Pagination
$itemsPerPage = 12;
$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$totalProducts = count($products);
$totalPages = ceil($totalProducts / $itemsPerPage);
$offset = ($currentPage - 1) * $itemsPerPage;
$products = array_slice($products, $offset, $itemsPerPage);
?>

<!-- Banner/Breadcrumb (skip homepage) -->
<!-- <div class="page-banner" style="background: url('asset/images/internalpage-bg.webp') center/cover no-repeat; min-height: 240px; display: flex; align-items: center;">
    <div class="container">
        <h2 style="color: #fff; font-size: 2rem; font-weight: bold; text-shadow: 0 2px 8px rgba(0,0,0,0.3); margin: 0; padding: 32px 0;">
            <?php echo htmlspecialchars($pageTitle); ?>
        </h2>
    </div>
</div> -->
<!-- Category Header -->
<section class="category-header">
    <div class="container">
        <h1><?php echo $category['name']; ?></h1>
        <!-- <p><?php echo $category['description']; ?></p>
        <p class="text-muted"><?php echo $totalProducts; ?> products found</p> -->
    </div>
</section>

<!-- Products Grid -->
<section class="products-section mt-5">
    <div class="container">
        <?php if (empty($products)): ?>
            <div class="text-center py-5">
                <h3>No products found in this category</h3>
                <p>Please check back later or browse other categories.</p>
                <a href="index.php" class="btn btn-primary">Back to Home</a>
            </div>
        <?php else: ?>
            <div class="row">
                <?php foreach ($products as $product): 
                    $inWishlist = in_array($product['id'], $wishlist_ids);
                    $isOutOfStock = ($product['stock_quantity'] <= 0);
                ?>
                    <div class="col-md-3 col-sm-6 mb-4">
                        <div class="card product-card" data-id="prod-<?php echo $product['id']; ?>">
                            <?php if ($product['is_discounted']): ?>
                                <div class="discount-banner">SAVE ₹<?php echo $product['mrp'] - $product['selling_price']; ?> (<?php echo $product['discount_percentage']; ?>% OFF)</div>
                            <?php endif; ?>
                            <div class="product-image">
                                <a href="product.php?slug=<?php echo $product['slug']; ?>">
                                    <img src="./<?php echo $product['main_image']; ?>" alt="<?php echo $product['name']; ?>" class="img-fluid">
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
                                        <div class="quantity-control d-inline-flex align-items-center">
                                            <button type="button" class="btn-qty btn-qty-minus" aria-label="Decrease quantity">-</button>
                                            <input type="number" class="quantity-input" value="1" min="1">
                                            <button type="button" class="btn-qty btn-qty-plus" aria-label="Increase quantity">+</button>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
                <nav aria-label="Product pagination">
                    <ul class="pagination justify-content-center">
                        <?php if ($currentPage > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="?slug=<?php echo $slug; ?>&page=<?php echo $currentPage - 1; ?>">Previous</a>
                            </li>
                        <?php endif; ?>
                        
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <li class="page-item <?php echo $i == $currentPage ? 'active' : ''; ?>">
                                <a class="page-link" href="?slug=<?php echo $slug; ?>&page=<?php echo $i; ?>"><?php echo $i; ?></a>
                            </li>
                        <?php endfor; ?>
                        
                        <?php if ($currentPage < $totalPages): ?>
                            <li class="page-item">
                                <a class="page-link" href="?slug=<?php echo $slug; ?>&page=<?php echo $currentPage + 1; ?>">Next</a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</section>

<!-- Include Footer -->
<?php include 'includes/footer.php'; ?>

<!-- Include CSS -->
<link rel="stylesheet" href="./asset/style/style.css">

<script>
// Remove any add-to-cart button JS logic here, rely on popup.js
</script>

</body>
</html> 