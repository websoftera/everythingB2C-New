<?php
// Prevent caching
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

require_once 'config/database.php';
require_once 'includes/functions.php';

// Get category from slug
$slug = isset($_GET['slug']) ? $_GET['slug'] : '';
$pdo = $GLOBALS['pdo'];

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

// Get category details
$stmt = $pdo->prepare('SELECT * FROM categories WHERE slug = ?');
$stmt->execute([$slug]);
$category = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$category) {
    header('Location: index.php');
    exit;
}

$pageTitle = $category['name'];
require_once 'includes/header.php';

// Get category path for breadcrumb
$categoryPath = getCategoryPath($category['id']);

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
} elseif ($selectedCategory === '') {
    // "All Categories" selected - show all products (no category filter)
    // Don't add any category filter condition
} else {
    // Default to current page category and its descendants
    $categoryIds = getAllDescendantCategoryIdsRecursive($pdo, $category['id']);
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
$sql = "SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE " . implode(' AND ', $whereConditions);

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

global $pdo;
$stmt = $pdo->prepare($sql);
$stmt->execute($params);

$allProducts = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get total count BEFORE pagination
$totalProducts = count($allProducts);

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
$totalPages = ceil($totalProducts / $itemsPerPage);
$offset = ($currentPage - 1) * $itemsPerPage;
$products = array_slice($allProducts, $offset, $itemsPerPage);

// Helper function to build pagination URLs with all filter parameters
function buildPaginationUrl($slug, $page, $params = []) {
    $url = "?slug=" . urlencode($slug) . "&page=" . $page;
    
    // Preserve all filter parameters
    if (isset($params['q']) && $params['q'] !== '') {
        $url .= "&q=" . urlencode($params['q']);
    }
    
    // Always include category parameter (even if empty for "All Categories")
    if (isset($params['category'])) {
        $url .= "&category=" . urlencode($params['category']);
    }
    
    if (isset($params['subcategory']) && $params['subcategory'] !== '') {
        $url .= "&subcategory=" . urlencode($params['subcategory']);
    }
    
    if (isset($params['min_price']) && $params['min_price'] !== '') {
        $url .= "&min_price=" . urlencode($params['min_price']);
    }
    
    if (isset($params['max_price']) && $params['max_price'] !== '') {
        $url .= "&max_price=" . urlencode($params['max_price']);
    }
    
    if (isset($params['sort']) && $params['sort'] !== '') {
        $url .= "&sort=" . urlencode($params['sort']);
    }
    
    return $url;
}
?>

<!-- Breadcrumb Navigation -->
<?php
$breadcrumbs = generateBreadcrumb($pageTitle, $categoryPath);
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
        <!-- Category Header -->
        <!-- <div class="category-header">
          <h1><?php echo $category['name']; ?></h1>
          <div class="results-count">
            <?php echo $totalProducts; ?> product<?php echo $totalProducts != 1 ? 's' : ''; ?> found
          </div>
        </div> -->
        
        <!-- Products Grid -->
        <div class="products-grid">
          <?php if (empty($products)): ?>
            <div class="no-products">
              <div class="no-products-icon">
                <i class="bi bi-search"></i>
              </div>
              <h3>No products found</h3>
              <p>Try adjusting your filters or search terms.</p>
              <a href="category.php?slug=<?php echo $category['slug']; ?>" class="filter-clear-btn">Clear All Filters</a>
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
                        $inWishlist = in_array($product['id'], $wishlist_ids);
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
        
        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
          <nav aria-label="Product pagination">
            <ul class="pagination justify-content-center">
              <?php if ($currentPage > 1): ?>
                <li class="page-item">
                  <a class="page-link" href="<?php echo buildPaginationUrl($slug, $currentPage - 1, $_GET); ?>">Previous</a>
                </li>
              <?php endif; ?>
              
              <?php for ($i = max(1, $currentPage - 2); $i <= min($totalPages, $currentPage + 2); $i++): ?>
                <li class="page-item <?php echo $i == $currentPage ? 'active' : ''; ?>">
                  <a class="page-link" href="<?php echo buildPaginationUrl($slug, $i, $_GET); ?>"><?php echo $i; ?></a>
                </li>
              <?php endfor; ?>
              
              <?php if ($currentPage < $totalPages): ?>
                <li class="page-item">
                  <a class="page-link" href="<?php echo buildPaginationUrl($slug, $currentPage + 1, $_GET); ?>">Next</a>
                </li>
              <?php endif; ?>
            </ul>
          </nav>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<style>
/* Category Page Layout Styles */
.products-container {
  padding: 20px 0;
}

.category-header {
  margin-bottom: 30px;
  padding-bottom: 15px;
  border-bottom: 1px solid #e9ecef;
}

.category-header h1 {
  margin: 0 0 10px 0;
  font-size: 28px;
  font-weight: 600;
  color: #333;
}

.results-count {
  font-size: 14px;
  color: #666;
}

/* Product Grid - Truly Responsive */
.products-grid {
  display: grid;
  grid-template-columns: 1fr; /* Mobile: 1 card per row */
  gap: 15px;
  margin-bottom: 40px;
  width: 100%;
  max-width: 100%;
}

/* Product Card - Responsive Design */
.product-card {
  width: 100%;
  max-width: 100%;
  min-width: 0;
  overflow: hidden;
  word-wrap: break-word;
  word-break: break-word;
  box-sizing: border-box;
  margin: 0;
  padding: 0;
  display: flex;
  flex-direction: column;
}

.product-card .product-image {
  width: 100%;
  height: auto;
  overflow: hidden;
  flex-shrink: 0;
}

.product-card .product-image img {
  width: 100%;
  height: auto;
  object-fit: cover;
}

.product-card .product-details {
  padding: 10px;
  width: 100%;
  box-sizing: border-box;
  min-width: 0;
  flex: 1;
  display: flex;
  flex-direction: column;
}

.product-card .product-details h3 {

  font-size: 14px;
  line-height: 1.3;
  margin-bottom: 8px;
  word-wrap: break-word;
  overflow-wrap: break-word;
  hyphens: auto;
  max-width: 100%;
  flex-shrink: 0;
}

.product-card .price-buttons {
  display: flex;
  flex-wrap: wrap;
  gap: 5px;
  margin-bottom: 8px;
  width: 100%;
  min-width: 0;
  flex-shrink: 0;
}

.product-card .price-buttons button {
  font-size: 11px;
  padding: 4px 6px;
  white-space: nowrap;
  flex-shrink: 0;
  min-width: fit-content;
  max-width: 100%;
}

.product-card .cart-actions {
  display: flex;
  flex-direction: column;
  gap: 8px;
  width: 100%;
  flex-shrink: 0;
}

.product-card .quantity-control {
  display: flex;
  align-items: center;
  gap: 5px;
  width: 100%;
  justify-content: center;
}

.product-card .quantity-input {
  width: 50px;
  text-align: center;
  font-size: 12px;
  flex-shrink: 0;
}

.product-card .add-to-cart-btn {
  width: 100%;
  font-size: 12px;
  padding: 6px 8px;
  white-space: nowrap;
  min-width: 0;
  max-width: 100%;
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

/* Responsive Design - Progressive Enhancement */
@media (min-width: 576px) {
  .products-grid {
    grid-template-columns: repeat(2, 1fr); /* Tablet: 2 cards per row */
    gap: 15px;
  }
}

@media (min-width: 768px) {
  .products-grid {
    grid-template-columns: repeat(2, 1fr); /* Small Desktop: 2 cards per row */
    gap: 18px;
  }
}

@media (min-width: 992px) {
  .products-grid {
    grid-template-columns: repeat(3, 1fr); /* Desktop: 3 cards per row */
    gap: 20px;
  }
}

@media (min-width: 1200px) {
  .products-grid {
    grid-template-columns: repeat(4, 1fr); /* Large Desktop: 4 cards per row */
    gap: 20px;
  }
}

/* Mobile-specific fixes - Ensure cards fit properly */
@media (max-width: 575.98px) {
  .products-grid {
    gap: 10px;
    padding: 0 5px;
  }
  
  .product-card {
    min-width: 0;
    max-width: 100%;
    width: 100%;
  }
  
  .product-card .product-details {
    padding: 8px;
    min-width: 0;
    width: 100%;
  }
  
  .product-card .product-details h3 {

    font-size: 12px;
    margin-bottom: 6px;
    max-width: 100%;
    width: 100%;
  }
  
  .product-card .price-buttons {
    gap: 3px;
    margin-bottom: 6px;
    flex-wrap: wrap;
    justify-content: flex-start;
    width: 100%;
  }
  
  .product-card .price-buttons button {
    font-size: 10px;
    padding: 3px 4px;
    flex-shrink: 0;
    min-width: fit-content;
    max-width: 100%;
  }
  
  .product-card .add-to-cart-btn {
    font-size: 11px;
    padding: 5px 6px;
    width: 100%;
    min-width: 0;
    max-width: 100%;
  }
  
  .product-card .cart-actions {
    width: 100%;
    min-width: 0;
  }
  
  .product-card .quantity-control {
    width: 100%;
    justify-content: center;
  }
}

/* Tablet adjustments */
@media (min-width: 576px) and (max-width: 991.98px) {
  .product-card .product-details h3 {

    font-size: 13px;
  }
  
  .product-card .price-buttons button {
    font-size: 10px;
    padding: 3px 4px;
  }
  
  .product-card .add-to-cart-btn {
    font-size: 11px;
    padding: 5px 6px;
  }
}

/* Desktop adjustments */
@media (min-width: 992px) {
  .product-card .product-details h3 {

    font-size: 14px;
  }
  
  .product-card .price-buttons button {
    font-size: 11px;
    padding: 4px 6px;
  }
  
  .product-card .add-to-cart-btn {
    font-size: 12px;
    padding: 6px 8px;
  }
}

/* Container and Layout Fixes */
.container-fluid {
  max-width: 100%;
  overflow-x: hidden;
  padding-left: 15px;
  padding-right: 15px;
}

.row {
  margin-left: 0;
  margin-right: 0;
}

.col-lg-9 {
  padding-left: 15px;
  padding-right: 15px;
  max-width: 100%;
  overflow-x: hidden;
}

/* Additional mobile fixes */
@media (max-width: 575.98px) {
  .container-fluid {
    padding-left: 5px;
    padding-right: 5px;
  }
  
  .col-lg-9 {
    padding-left: 0;
    padding-right: 0;
  }
  
  /* Ensure no horizontal scroll */
  body {
    overflow-x: hidden;
  }
  
  /* Force single column layout on very small screens */
  .products-grid {
    grid-template-columns: 1fr !important;
    gap: 10px;
    width: 100%;
  }
  
  /* Ensure cards don't overflow */
  .product-card {
    width: 100% !important;
    max-width: 100% !important;
    min-width: 0 !important;
  }
}
</style>

<?php include 'includes/footer.php'; ?> 