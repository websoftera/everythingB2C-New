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
    $allCategoryIds = [];
    if (is_array($selectedCategory)) {
        foreach ($selectedCategory as $catId) {
            $allCategoryIds = array_merge($allCategoryIds, getAllDescendantCategoryIdsRecursive($pdo, intval($catId)));
        }
    } else {
        $allCategoryIds = getAllDescendantCategoryIdsRecursive($pdo, intval($selectedCategory));
    }
    $categoryIds = array_unique($allCategoryIds);
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

<div class="container-fluid mt-4 shop-page-container">
  <div class="row">
    <!-- Top Filter (Desktop/Tablet) & Sidebar Filter (Mobile) -->
    <div class="col-12">
      <?php include 'includes/sidebar-filter.php'; ?>
    </div>
    
    <!-- Products Section -->
    <div class="col-12">
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
              <a href="shop.php" class="filter-clear-btn">Clear All</a>
            </div>
          <?php else: ?>
            <?php foreach ($products as $product): 
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
                  <?php else: ?>
                      <div class="discount-banner" style="visibility: hidden;">&nbsp;</div>
                  <?php endif; ?>
                <div class="product-info">
                  <div class="product-image">
                      <a href="product.php?slug=<?php echo $product['slug']; ?>">
                          <?php if (!empty($product['main_image'])): ?>
                              <img src="<?php echo $product['main_image']; ?>" alt="<?php echo cleanProductName($product['name']); ?>">
                          <?php else: ?>
                            <img src="./uploads/products/blank-img.webp" alt="No image available">
                        <?php endif; ?>
                      </a>
                      <?php if ($isOutOfStock): ?>
                          <div class="out-of-stock">OUT OF STOCK</div>
                      <?php endif; ?>
                  </div>
                  <div class="product-details">
                      <a href="product.php?slug=<?php echo $product['slug']; ?>" class="product-title-link">
                          <h3><?php echo strtoupper(cleanProductName($product['name'])); ?></h3>
                      </a>
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
                            <input type="checkbox" class="heart-checkbox" id="wishlist-checkbox-shop-<?php echo $product['id']; ?>" data-product-id="<?php echo $product['id']; ?>" <?php if ($inWishlist) echo 'checked'; ?>>
                            <label for="wishlist-checkbox-shop-<?php echo $product['id']; ?>" class="wishlist-label <?php echo $inWishlist ? 'wishlist-active' : ''; ?>">
                                <span class="heart-icon">&#10084;</span>
                            </label>
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
                            <button class="add-to-cart add-to-cart-btn" data-product-id="<?php echo $product['id']; ?>">
                                <i class="fas fa-shopping-cart"></i>
                                ADD TO CART
                            </button>
                        </div>
                      <?php endif; ?>
                  </div>
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

/* Standard Grid - 5 columns on desktop */
.products-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr); /* Default: 4 cards */
  gap: 20px;
  margin-bottom: 40px;
  width: 100%;
}

/* Product Card - Enhanced Hover & Radius Preservation */
.shop-page-container .products-grid .card.product-card {
  border-radius: 8px !important;
  overflow: hidden !important;
  transition: transform 0.3s ease, box-shadow 0.3s ease !important;
  border: 1px solid #eee !important;
  isolation: isolate !important; /* Forces stacking context for clean clipping */
}

.shop-page-container .products-grid .card.product-card:hover {
  transform: translateY(-5px) !important;
  box-shadow: 0 10px 20px rgba(0, 0, 0, 0.12) !important;
  border-radius: 8px !important; /* Force preservation */
  overflow: hidden !important;
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

/* Standardized responsive break points */
@media (max-width: 767px) {
  .products-grid {
    grid-template-columns: 1fr !important; /* Mobile: 1 card per row */
    gap: 15px;
  }
}

@media (min-width: 768px) and (max-width: 1199px) {
  .products-grid {
    grid-template-columns: repeat(3, 1fr) !important; /* Tablet: 3 cards per row */
    gap: 18px;
  }
}

@media (min-width: 1200px) {
  .products-grid {
    grid-template-columns: repeat(5, 1fr) !important; /* Desktop: 5 cards per row */
    gap: 20px !important;
  }
  .products-grid .card.product-card {
    min-width: 0 !important;
    max-width: 100% !important;
    margin: 0 !important;
  }
}

@media (min-width: 1400px) {
  .products-grid {
    grid-template-columns: repeat(5, 1fr) !important; /* Wide screens: 5 cards per row */
    gap: 20px !important;
  }
}

/* Container width standardization - Harmonized with category.php */
.container-fluid {
  max-width: 100% !important;
  overflow-x: hidden !important;
  padding-left: 15px !important;
  padding-right: 15px !important;
}

.row {
  margin-left: 0 !important;
  margin-right: 0 !important;
}

/* Discount banner consistency */
.shop-page-container .products-grid .card.product-card .discount-banner {
    background: var(--site-blue) !important;
    color: #fff !important;
    border-radius: 8px 8px 0 0 !important; /* Match card radius for clean corners */
    padding: 8px 0 !important;
    font-size: 11px !important;
    text-align: center !important;
    height: auto !important;
    min-height: unset !important;
    display: block !important;
}
</style>

<?php include 'includes/footer.php'; ?> 