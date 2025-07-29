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
?>
<div class="shop-page-product-card" data-id="prod-<?php echo $product['id']; ?>">
  <?php if ($product['is_discounted']): ?>
    <div class="shop-page-discount-banner">SAVE ₹<?php echo $product['mrp'] - $product['selling_price']; ?> (<?php echo $product['discount_percentage']; ?>% OFF)</div>
  <?php endif; ?>
  <div class="shop-page-product-image">
    <a href="product.php?slug=<?php echo $product['slug']; ?>">
      <img src="./<?php echo $product['main_image']; ?>" alt="<?php echo $product['name']; ?>">
    </a>
    <?php if ($isOutOfStock): ?>
      <div class="shop-page-out-of-stock">OUT OF STOCK</div>
    <?php endif; ?>
  </div>
  <div class="shop-page-product-details">
    <h3><?php echo strtoupper($product['name']); ?></h3>
    <div class="shop-page-price-buttons">
      <div class="shop-page-price-btn mrp">
        <span class="label">MRP</span>
        <span class="value">₹<?php echo number_format($product['mrp'],2); ?></span>
      </div>
      <div class="shop-page-price-btn pay">
        <span class="label">PAY</span>
        <span class="value">₹<?php echo number_format($product['selling_price'],2); ?></span>
      </div>
      <div class="shop-page-wishlist">
        <input type="checkbox" class="shop-page-heart-checkbox" id="wishlist-checkbox-<?php echo $product['id']; ?>" data-product-id="<?php echo $product['id']; ?>">
        <label for="wishlist-checkbox-<?php echo $product['id']; ?>" class="shop-page-wishlist-label"><i class="fas fa-heart"></i></label>
      </div>
    </div>
    <?php if ($isOutOfStock): ?>
      <a href="product.php?slug=<?php echo $product['slug']; ?>" class="shop-page-read-more">READ MORE</a>
    <?php else: ?>
      <div class="shop-page-cart-actions">
        <button class="shop-page-add-to-cart-btn" data-product-id="<?php echo $product['id']; ?>">ADD TO CART</button>
        <input type="number" class="shop-page-quantity-input" value="1" min="1" max="99" >
      </div>
    <?php endif; ?>
  </div>
</div>
<?php endforeach; ?> 