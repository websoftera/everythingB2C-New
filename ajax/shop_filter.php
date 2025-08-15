<?php
require_once '../includes/functions.php';

$categories = getAllCategories();
$categoryTree = buildCategoryTree($categories);

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$selectedCategory = isset($_GET['category']) ? intval($_GET['category']) : null;
$selectedSubcategory = isset($_GET['subcategory']) ? intval($_GET['subcategory']) : null;
$minPrice = isset($_GET['min_price']) ? floatval($_GET['min_price']) : 0;
$maxPrice = isset($_GET['max_price']) ? floatval($_GET['max_price']) : 0;

$where = ['p.is_active = 1'];
$params = [];
if ($search !== '') {
    $where[] = '(p.name LIKE ? OR p.description LIKE ?)';
    $params[] = "%$search%";
    $params[] = "%$search%";
}
if ($selectedSubcategory) {
    $where[] = 'p.category_id = ?';
    $params[] = $selectedSubcategory;
} elseif ($selectedCategory) {
    $subcatIds = array_map(function($cat) { return $cat['id']; }, array_merge(
        isset($categoryTree[$selectedCategory]['children']) ? $categoryTree[$selectedCategory]['children'] : [],
        [ ['id' => $selectedCategory] ]
    ));
    $where[] = 'p.category_id IN (' . implode(',', array_fill(0, count($subcatIds), '?')) . ')';
    $params = array_merge($params, $subcatIds);
}
if ($minPrice > 0) {
    $where[] = 'p.selling_price >= ?';
    $params[] = $minPrice;
}
if ($maxPrice > 0) {
    $where[] = 'p.selling_price <= ?';
    $params[] = $maxPrice;
}
$sql = 'SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id';
if ($where) {
    $sql .= ' WHERE ' . implode(' AND ', $where);
}
$sql .= ' ORDER BY p.created_at DESC';
$pdo = $GLOBALS['pdo'];
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($products)) {
    echo '<div class="shop-page-no-products">No products found for selected filters.</div>';
    exit;
}
foreach ($products as $product): 
    $isOutOfStock = ($product['stock_quantity'] <= 0);
    $inWishlist = false;
    if (isLoggedIn()) {
        $inWishlist = isInWishlist($_SESSION['user_id'], $product['id']);
    } else {
        $inWishlist = in_array($product['id'], isset($_SESSION['wishlist']) ? $_SESSION['wishlist'] : []);
    }
?>
<div class="card product-card" data-id="prod-<?php echo $product['id']; ?>">
  <?php if ($product['is_discounted']): ?>
    <div class="discount-banner">SAVE ₹<?php echo $product['mrp'] - $product['selling_price']; ?> (<?php echo $product['discount_percentage']; ?>% OFF)</div>
  <?php endif; ?>
  <div class="product-info">
    <div class="wishlist">
      <input type="checkbox" class="heart-checkbox" id="wishlist-checkbox-ajax-<?php echo $product['id']; ?>" data-product-id="<?php echo $product['id']; ?>" <?php if ($inWishlist) echo 'checked'; ?>>
      <label for="wishlist-checkbox-ajax-<?php echo $product['id']; ?>" class="wishlist-label <?php echo $inWishlist ? 'wishlist-active' : ''; ?>">
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
          </div>
          <div class="cart-actions d-flex align-items-center gap-2">
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

<style>
/* AJAX Shop Filter - Matching Products Offering Discount Design */
.product-card {
  background: #fff !important;
  border-radius: 8px !important;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1) !important;
  border: 1px solid var(--light-blue) !important;
}

.product-info {
  padding: 5px 6px !important;
}

.product-card .product-image img {
  max-height: 155px !important;
  min-height: 155px !important;
}

.product-card .discount-banner {
  background: var(--site-blue) !important;
  color: #fff !important;
  border-radius: 4px !important;
}

.product-card .price-btn.mrp {
  background: var(--mrp-light-blue) !important;
  color: var(--dark-blue) !important;
}

.product-card .price-btn.pay {
  background: var(--pay-light-green) !important;
  color: var(--dark-grey) !important;
}

.product-card .add-to-cart-btn,
.product-card .add-to-cart {
  background: var(--cart-button) !important;
  color: #ffffff !important;
}

.product-card .add-to-cart-btn:hover,
.product-card .add-to-cart:hover {
  background: var(--dark-blue) !important;
}

.product-card .product-details {
  background-image: none !important;
}

.product-card .product-image {
  background-image: none !important;
}
</style> 