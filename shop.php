<?php
require_once 'config/database.php';
require_once 'includes/functions.php';
require_once 'includes/header.php';

// Function to get all descendant category IDs recursively
function getAllDescendantCategoryIdsRecursive($pdo, $parentId) {
    $descendants = [$parentId];
    
    $stmt = $pdo->prepare('SELECT id FROM categories WHERE parent_id = ?');
    $stmt->execute([$parentId]);
    $children = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    foreach ($children as $childId) {
        $descendants = array_merge($descendants, getAllDescendantCategoryIdsRecursive($pdo, $childId));
    }
    
    return $descendants;
}

$pdo = $GLOBALS['pdo'];

// Fetch all categories and build tree
$categories = getAllCategories();
$categoryTree = buildCategoryTree($categories);

// Get filter parameters
$selectedCategory = isset($_GET['category']) ? $_GET['category'] : null;
$selectedSubcategory = isset($_GET['subcategory']) ? intval($_GET['subcategory']) : null;
$minPrice = isset($_GET['min_price']) ? floatval($_GET['min_price']) : 0;
$maxPrice = isset($_GET['max_price']) ? floatval($_GET['max_price']) : 10000;
$searchTerm = isset($_GET['q']) ? trim($_GET['q']) : '';
$sortBy = isset($_GET['sort']) ? $_GET['sort'] : 'newest';

// Fix: Handle empty string category parameter properly
if (isset($_GET['category']) && $_GET['category'] === '') {
    $selectedCategory = '';
}

// Get site-wide price range for comparison
$priceStmt = $pdo->query('SELECT MIN(selling_price) as min_price, MAX(selling_price) as max_price FROM products WHERE is_active = 1');
$priceRow = $priceStmt->fetch(PDO::FETCH_ASSOC);
$siteMinPrice = $priceRow['min_price'] ?: 0;
$siteMaxPrice = $priceRow['max_price'] ?: 10000;

// Build the WHERE clause
$whereConditions = ['p.is_active = 1'];
$params = [];

// Category filtering - if a category is selected, include all its descendants
if ($selectedCategory !== null && $selectedCategory !== '') {
    // Get all descendant category IDs for the selected category
    $categoryIds = getAllDescendantCategoryIdsRecursive($pdo, intval($selectedCategory));
    $placeholders = str_repeat('?,', count($categoryIds) - 1) . '?';
    $whereConditions[] = "p.category_id IN ($placeholders)";
    $params = array_merge($params, $categoryIds);
}

// Price filter
if ($minPrice > 0) {
    $whereConditions[] = 'p.selling_price >= ?';
    $params[] = $minPrice;
}
if ($maxPrice < 10000) {
    $whereConditions[] = 'p.selling_price <= ?';
    $params[] = $maxPrice;
}

// Search filter
if (!empty($searchTerm)) {
    $whereConditions[] = '(p.name LIKE ? OR p.description LIKE ?)';
    $params[] = '%' . $searchTerm . '%';
    $params[] = '%' . $searchTerm . '%';
}

// Build SQL query
$sql = 'SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id';
if ($whereConditions) {
    $sql .= ' WHERE ' . implode(' AND ', $whereConditions);
}

// Add sorting
switch ($sortBy) {
    case 'oldest':
        $sql .= ' ORDER BY p.created_at ASC';
        break;
    case 'price_low':
        $sql .= ' ORDER BY p.selling_price ASC';
        break;
    case 'price_high':
        $sql .= ' ORDER BY p.selling_price DESC';
        break;
    case 'name_asc':
        $sql .= ' ORDER BY p.name ASC';
        break;
    case 'name_desc':
        $sql .= ' ORDER BY p.name DESC';
        break;
    default:
        $sql .= ' ORDER BY p.created_at DESC';
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Set page title for breadcrumb
$pageTitle = 'Shop';
$breadcrumbs = generateBreadcrumb($pageTitle);
echo renderBreadcrumb($breadcrumbs);
?>

<div class="container-fluid">
  <div class="row">
    <!-- Sidebar Filter -->
    <div class="col-lg-3 col-md-4">
      <?php include 'includes/sidebar-filter.php'; ?>
    </div>
    
    <!-- Products Section -->
    <div class="col-lg-9 col-md-8">
      <div class="products-container">
        <!-- Results Header -->
        <div class="results-header">
          <h2>Products</h2>
          <div class="results-count">
            <?php echo count($products); ?> product<?php echo count($products) != 1 ? 's' : ''; ?> found
          </div>
        </div>
        
        <!-- Products Grid -->
        <div class="products-grid">
          <?php if (empty($products)): ?>
            <div class="no-products">
              <div class="no-products-icon">
                <i class="bi bi-search"></i>
              </div>
              <h3>No products found</h3>
              <p>Try adjusting your filters or search terms.</p>
              <a href="shop.php" class="filter-clear-btn">Clear All Filters</a>
            </div>
          <?php else: ?>
            <?php foreach ($products as $product): 
              $isOutOfStock = ($product['stock_quantity'] <= 0);
            ?>
              <div class="card product-card" data-id="prod-<?php echo $product['id']; ?>">
                <?php if ($product['is_discounted']): ?>
                  <div class="discount-banner">SAVE ₹<?php echo $product['mrp'] - $product['selling_price']; ?> (<?php echo $product['discount_percentage']; ?>% OFF)</div>
                <?php endif; ?>
                <div class="product-image">
                    <a href="product.php?slug=<?php echo $product['slug']; ?>">
                        <img src="./<?php echo $product['main_image']; ?>" alt="<?php echo cleanProductName($product['name']); ?>">
                    </a>
                    <?php if ($isOutOfStock): ?>
                        <div class="out-of-stock">OUT OF STOCK</div>
                    <?php endif; ?>
                </div>
                <div class="product-details">
                    <h3><?php echo strtoupper(cleanProductName($product['name'])); ?></h3>
                  <div class="price-buttons">
                    <button class="mrp"><span class="label">MRP</span> <span class="value">₹<?php echo number_format($product['mrp'],0); ?></span></button>
                    <button class="pay"><span class="label">PAY</span> <span class="value">₹<?php echo number_format($product['selling_price'],0); ?></span></button>
                    <label class="wishlist">
                      <input type="checkbox" class="heart-checkbox" id="wishlist-checkbox-<?php echo $product['id']; ?>" data-product-id="<?php echo $product['id']; ?>" <?php 
                        $inWishlist = false;
                        if (isLoggedIn()) {
                            $inWishlist = isInWishlist($_SESSION['user_id'], $product['id']);
                        } else {
                            $inWishlist = in_array($product['id'], isset($_SESSION['wishlist']) ? $_SESSION['wishlist'] : []);
                        }
                        echo $inWishlist ? 'checked' : '';
                      ?>>
                      <span class="heart-icon">&#10084;</span>
                    </label>
                  </div>
                  <?php if ($isOutOfStock): ?>
                    <a href="product.php?slug=<?php echo $product['slug']; ?>" class="read-more">READ MORE</a>
                  <?php else: ?>
                    <div class="cart-actions">
                      <div class="quantity-control">
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
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
/* Shop Page Layout Styles */
.products-container {
  padding: 20px 0;
}

.results-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 30px;
  padding-bottom: 15px;
  border-bottom: 1px solid #e9ecef;
}

.results-header h2 {
  margin: 0;
  font-size: 24px;
  font-weight: 600;
  color: #333;
}

.results-count {
  font-size: 14px;
  color: #666;
}

.products-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
  gap: 20px;
  margin-bottom: 40px;
}

.no-products {
  text-align: center;
  padding: 60px 20px;
  grid-column: 1 / -1;
}

.no-products-icon {
  font-size: 48px;
  color: #ccc;
  margin-bottom: 20px;
}

.no-products h3 {
  font-size: 20px;
  color: #333;
  margin-bottom: 10px;
}

.no-products p {
  color: #666;
  margin-bottom: 20px;
}

/* Responsive Design */
@media (max-width: 991.98px) {
  .results-header {
    flex-direction: column;
    align-items: flex-start;
    gap: 10px;
  }
  
  .products-grid {
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 15px;
  }
}

@media (max-width: 767.98px) {
  .products-grid {
    grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
    gap: 10px;
  }
}
</style>

<?php include 'includes/footer.php'; ?> 