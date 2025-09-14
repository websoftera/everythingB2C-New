<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

// Get filter parameters
$selectedCategory = isset($_GET['category']) ? $_GET['category'] : null;
$selectedSubcategory = isset($_GET['subcategory']) ? intval($_GET['subcategory']) : null;
$minPrice = isset($_GET['min_price']) ? floatval($_GET['min_price']) : 0;
$maxPrice = isset($_GET['max_price']) ? floatval($_GET['max_price']) : 10000;
$searchTerm = isset($_GET['q']) ? trim($_GET['q']) : '';
$sortBy = isset($_GET['sort']) ? $_GET['sort'] : 'newest';
$discounted = isset($_GET['discounted']) ? (int)$_GET['discounted'] : 0;
$featured = isset($_GET['featured']) ? (int)$_GET['featured'] : 0;

// Fix: Handle empty string category parameter properly
if (isset($_GET['category']) && $_GET['category'] === '') {
    $selectedCategory = '';
}

// Get site-wide price range for comparison
$pdo = $GLOBALS['pdo'];
$priceStmt = $pdo->query('SELECT MIN(selling_price) as min_price, MAX(selling_price) as max_price FROM products WHERE is_active = 1');
$priceRow = $priceStmt->fetch(PDO::FETCH_ASSOC);
$siteMinPrice = $priceRow['min_price'] ?: 0;
$siteMaxPrice = $priceRow['max_price'] ?: 10000;

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

// Discounted/Featured filters
if ($discounted) {
    $whereConditions[] = 'p.is_discounted = 1';
    $pageTitle = "Products Offering Discount";
} elseif ($featured) {
    $whereConditions[] = 'p.is_featured = 1';
    $pageTitle = "Featured Products";
} else {
    $pageTitle = "All Products";
}

// Build the complete query
$whereClause = implode(' AND ', $whereConditions);

// Count total products for pagination
$countQuery = "SELECT COUNT(*) FROM products p WHERE $whereClause";
$countStmt = $pdo->prepare($countQuery);
$countStmt->execute($params);
$totalProducts = $countStmt->fetchColumn();

// Pagination
$productsPerPage = 12;
$currentPage = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$totalPages = ceil($totalProducts / $productsPerPage);
$offset = ($currentPage - 1) * $productsPerPage;

// Build ORDER BY clause
$orderBy = 'p.created_at DESC'; // Default: newest first
switch ($sortBy) {
    case 'oldest':
        $orderBy = 'p.created_at ASC';
        break;
    case 'price_low':
        $orderBy = 'p.selling_price ASC';
        break;
    case 'price_high':
        $orderBy = 'p.selling_price DESC';
        break;
    case 'name_asc':
        $orderBy = 'p.name ASC';
        break;
    case 'name_desc':
        $orderBy = 'p.name DESC';
        break;
}

// Get products with filters and pagination
$query = "SELECT p.*, c.name as category_name 
          FROM products p 
          LEFT JOIN categories c ON p.category_id = c.id 
          WHERE $whereClause 
          ORDER BY $orderBy 
          LIMIT $productsPerPage OFFSET $offset";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

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

// Include header
include 'includes/header.php';

// Breadcrumb Navigation
$breadcrumbs = generateBreadcrumb($pageTitle);
echo renderBreadcrumb($breadcrumbs);
?>
<link rel="stylesheet" href="./asset/style/style.css">
<div class="container mt-4">
  <div class="row">
    <!-- Sidebar Filter -->
    <div class="col-lg-3 col-md-4">
      <?php include 'includes/sidebar-filter.php'; ?>
    </div>
    
    <!-- Products Section -->
    <div class="col-lg-9 col-md-8">
      <div class="products-container">
        <!-- Products Header -->
        <div class="products-header">
          <h1><?php echo $pageTitle; ?></h1>
          <div class="results-count">
            <?php echo $totalProducts; ?> product<?php echo $totalProducts != 1 ? 's' : ''; ?> found
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
              <a href="products.php" class="filter-clear-btn">Clear Filter</a>
            </div>
          <?php else: ?>
            <?php foreach ($products as $product): 
              $inWishlist = in_array($product['id'], $wishlist_ids);
              $isOutOfStock = ($product['stock_quantity'] <= 0);
            ?>
              <div class="card product-card" data-id="prod-<?php echo $product['id']; ?>" data-product-id="<?php echo $product['id']; ?>">
                <?php if ($product['is_discounted']): ?>
                  <div class="discount-banner">SAVE ₹<?php echo $product['mrp'] - $product['selling_price']; ?> (<?php echo $product['discount_percentage']; ?>% OFF)</div>
                <?php endif; ?>
                <div class="product-info">
                  <div class="wishlist">
                    <input type="checkbox" class="heart-checkbox" id="wishlist-checkbox-products-<?php echo $product['id']; ?>" data-product-id="<?php echo $product['id']; ?>" <?php if ($inWishlist) echo 'checked'; ?>>
                    <label for="wishlist-checkbox-products-<?php echo $product['id']; ?>" class="wishlist-label <?php echo $inWishlist ? 'wishlist-active' : ''; ?>">
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
          <?php endif; ?>
        </div>
        
        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
          <nav aria-label="Product pagination">
            <ul class="pagination justify-content-center">
              <?php if ($currentPage > 1): ?>
                <li class="page-item">
                  <a class="page-link" href="<?php echo buildPaginationUrl('products', $currentPage - 1, $_GET); ?>">Previous</a>
                </li>
              <?php endif; ?>
              
              <?php for ($i = max(1, $currentPage - 2); $i <= min($totalPages, $currentPage + 2); $i++): ?>
                <li class="page-item <?php echo $i == $currentPage ? 'active' : ''; ?>">
                  <a class="page-link" href="<?php echo buildPaginationUrl('products', $i, $_GET); ?>"><?php echo $i; ?></a>
                </li>
              <?php endfor; ?>
              
              <?php if ($currentPage < $totalPages): ?>
                <li class="page-item">
                  <a class="page-link" href="<?php echo buildPaginationUrl('products', $currentPage + 1, $_GET); ?>">Next</a>
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
/* Products Page Layout Styles */
.products-container {
  padding: 20px 0;
}

.products-header {
  margin-bottom: 30px;
  padding-bottom: 15px;
  border-bottom: 1px solid #e9ecef;
}

.products-header h1 {
  margin: 0 0 10px 0;
  font-size: 28px;
  font-weight: 600;
  color: #333;
}

.results-count {
  font-size: 14px;
  color: #666;
}

/* Product Grid - Responsive */
.products-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr); /* Desktop: 4 cards per row */
  gap: 20px;
  margin-bottom: 40px;
  width: 100%;
  max-width: 100%;
}

/* Product Card - Matching Products Offering Discount Design */
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
  background: #fff !important;
  border-radius: 8px !important;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1) !important;
  border: 1px solid var(--light-blue) !important;
}

.product-info {
  padding: 5px 6px !important;
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
  max-height: 155px;
  min-height: 155px;
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

/* No Products Found */
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
  margin-bottom: 10px;
  color: #333;
}

.no-products p {
  color: #666;
  margin-bottom: 20px;
}

/* Responsive Design - Progressive Enhancement */
@media (max-width: 767px) {
  .products-grid {
    grid-template-columns: 1fr; /* Mobile: 1 card per row */
    gap: 15px;
  }
  
  .products-header h1 {
    font-size: 24px;
  }
}

@media (min-width: 768px) and (max-width: 1199px) {
  .products-grid {
    grid-template-columns: repeat(3, 1fr); /* Tablet: 3 cards per row */
    gap: 18px;
  }
}

@media (min-width: 1200px) {
  .products-grid {
    grid-template-columns: repeat(4, 1fr); /* Desktop: 4 cards per row */
    gap: 20px;
  }
}

/* Mobile-specific fixes - Ensure cards fit properly */
@media (max-width: 767px) {
  .products-grid {
    gap: 12px;
    padding: 0 10px;
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
    margin-bottom: 2px;
    max-width: 100%;
    width: 100%;
  }
  
  .product-card .price-buttons {
    gap: 3px;
    flex-wrap: wrap;
    justify-content: flex-start;
    width: 100%;
  }
  
  .product-card .price-btn {
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
    justify-content: space-between;
    align-items: center;
  }
  
  .product-card .quantity-control {
    flex-shrink: 0;
    margin-right: 10px;
  }
  
  .product-card .add-to-cart-btn {
    flex: 1;
    min-width: 0;
    max-width: none;
  }
}

/* Tablet adjustments */
@media (min-width: 768px) and (max-width: 1199px) {
  .product-card .product-details h3 {
    font-size: 13px;
  }
  
  .product-card .price-btn {
    font-size: 10px;
    padding: 3px 4px;
  }
  
  .product-card .add-to-cart-btn {
    font-size: 11px;
    padding: 5px 6px;
  }
}

/* Desktop adjustments */
@media (min-width: 1200px) {
  .product-card .product-details h3 {
    font-size: 14px;
  }
  
  .product-card .price-btn {
    font-size: 11px;
    padding: 4px 6px;
  }
  
  .product-card .add-to-cart-btn {
    font-size: 12px;
    padding: 6px 8px;
  }
}

/* Container and Layout Fixes */
.container {
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
@media (max-width: 767px) {
  .container {
    padding-left: 10px;
    padding-right: 10px;
  }
  
  .col-lg-9 {
    padding-left: 0;
    padding-right: 0;
  }
  
  /* Ensure no horizontal scroll */
  body {
    overflow-x: hidden;
  }
  
  /* Force 1 column layout on mobile */
  .products-grid {
    grid-template-columns: 1fr !important;
    gap: 12px;
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

<script>
// Remove any add-to-cart button JS logic here, rely on popup.js
</script>

<?php include 'includes/footer.php'; ?> 