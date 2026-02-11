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
$pageCss = ['asset/style/category-page.css'];
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

// Use buildPaginationUrl function from functions.php
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
              <a href="category.php?slug=<?php echo $category['slug']; ?>" class="filter-clear-btn">Clear All</a>
            </div>
          <?php else: ?>
            <?php foreach ($products as $product): 
              $isOutOfStock = ($product['stock_quantity'] <= 0);
              $inWishlist = in_array($product['id'], $wishlist_ids);
            ?>
              <div class="card product-card" data-id="prod-<?php echo $product['id']; ?>">
                <?php if ($product['is_discounted']): ?>
                  <div class="discount-banner">SAVE ₹<?php echo $product['mrp'] - $product['selling_price']; ?> (<?php echo $product['discount_percentage']; ?>% OFF)</div>
                <?php endif; ?>
                <div class="product-info">
                  <div class="wishlist">
                    <input type="checkbox" class="heart-checkbox" id="wishlist-checkbox-category-<?php echo $product['id']; ?>" data-product-id="<?php echo $product['id']; ?>" <?php if ($inWishlist) echo 'checked'; ?>>
                    <label for="wishlist-checkbox-category-<?php echo $product['id']; ?>" class="wishlist-label <?php echo $inWishlist ? 'wishlist-active' : ''; ?>">
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
                                <i class="fas fa-shopping-cart" style="margin-right: 2px; transform: scaleX(-1); font-size: 11px;"></i>
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
        
        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
          <nav aria-label="Product pagination">
            <ul class="pagination justify-content-center">
              <?php if ($currentPage > 1): ?>
                <li class="page-item">
                  <a class="page-link" href="<?php echo buildPaginationUrl('category', $currentPage - 1, $_GET); ?>">Previous</a>
                </li>
              <?php endif; ?>
              
              <?php for ($i = max(1, $currentPage - 2); $i <= min($totalPages, $currentPage + 2); $i++): ?>
                <li class="page-item <?php echo $i == $currentPage ? 'active' : ''; ?>">
                  <a class="page-link" href="<?php echo buildPaginationUrl('category', $i, $_GET); ?>"><?php echo $i; ?></a>
                </li>
              <?php endfor; ?>
              
              <?php if ($currentPage < $totalPages): ?>
                <li class="page-item">
                  <a class="page-link" href="<?php echo buildPaginationUrl('category', $currentPage + 1, $_GET); ?>">Next</a>
                </li>
              <?php endif; ?>
            </ul>
          </nav>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>


<?php include 'includes/back_to_top_button.php'; ?>
<?php include 'includes/footer.php'; ?> 