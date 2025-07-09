<?php
require_once 'includes/functions.php';
require_once 'includes/header.php';

// Fetch all categories and build tree
$categories = getAllCategories();
$categoryTree = buildCategoryTree($categories);

// Get selected filters
$selectedCategory = isset($_GET['category']) ? intval($_GET['category']) : null;
$selectedSubcategory = isset($_GET['subcategory']) ? intval($_GET['subcategory']) : null;
$maxPrice = isset($_GET['max_price']) ? floatval($_GET['max_price']) : 0;

// Build product query
$where = ['p.is_active = 1'];
$params = [];
if ($selectedSubcategory) {
    $where[] = 'p.category_id = ?';
    $params[] = $selectedSubcategory;
} elseif ($selectedCategory) {
    // Get all subcategories for this category
    $subcatIds = array_map(function($cat) { return $cat['id']; }, array_merge(
        isset($categoryTree[$selectedCategory]['children']) ? $categoryTree[$selectedCategory]['children'] : [],
        [ ['id' => $selectedCategory] ]
    ));
    $where[] = 'p.category_id IN (' . implode(',', array_fill(0, count($subcatIds), '?')) . ')';
    $params = array_merge($params, $subcatIds);
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

// Get min/max price for slider
$priceStmt = $pdo->query('SELECT MIN(selling_price) as min_price, MAX(selling_price) as max_price FROM products WHERE is_active = 1');
$priceRow = $priceStmt->fetch(PDO::FETCH_ASSOC);
$siteMinPrice = 0;
$siteMaxPrice = 10000;
$maxPriceValue = $maxPrice ?: $siteMaxPrice;
?>
<link rel="stylesheet" href="asset/style/shop.css">

<div class="container shop-page-container">
  <!-- Static Filter Bar at Top (Desktop Only) -->
  <div class="shop-page-filter-bar d-none d-md-block">
    <form method="get" id="filterFormDesktop">
      <div class="shop-page-filter-content">
        <div class="shop-page-search-box">
          <input type="text" id="shopSearchInputDesktop" name="search" placeholder="Search products..." autocomplete="off">
        </div>
        <div class="shop-page-category-filter">
          <select name="category" id="categorySelectDesktop">
            <option value="">All Categories</option>
            <?php foreach ($categoryTree as $cat): ?>
              <option value="<?php echo $cat['id']; ?>" <?php if ($selectedCategory == $cat['id']) echo 'selected'; ?>>
                <?php echo htmlspecialchars($cat['name']); ?>
              </option>
              <?php if (!empty($cat['children'])): ?>
                <?php foreach ($cat['children'] as $subcat): ?>
                  <option value="<?php echo $subcat['id']; ?>" <?php if ($selectedSubcategory == $subcat['id']) echo 'selected'; ?>>
                    &nbsp;&nbsp;— <?php echo htmlspecialchars($subcat['name']); ?>
                  </option>
                <?php endforeach; ?>
              <?php endif; ?>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="shop-page-price-range" style="flex-direction:column;align-items:flex-start;min-width:220px;">
          <div style="font-size:13px;margin-bottom:2px;">
            Price: ₹0 - ₹<span id="maxPriceDisplayDesktop"><?php echo $maxPriceValue; ?></span>
          </div>
          <div style="width:100%;display:flex;align-items:center;gap:8px;">
            <input type="range" id="maxPriceRangeDesktop" name="max_price" min="<?php echo $siteMinPrice; ?>" max="<?php echo $siteMaxPrice; ?>" value="<?php echo $maxPriceValue; ?>" step="1">
          </div>
        </div>
        <button type="submit" class="shop-page-filter-btn">Apply Filters</button>
        <button type="button" id="clearFilterBtnDesktop" class="shop-page-filter-btn" style="background:#eee;color:#333;margin-left:8px;">Clear Filter</button>
      </div>
    </form>
  </div>

  <!-- Mobile Filter Button -->
  <button class="shop-page-mobile-filter-btn d-md-none" id="openMobileFilter" aria-label="Show Filters">
    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="display:block;margin:auto;">
      <path d="M3 5h18M6 10h12M10 15h4" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
  </button>

  <!-- Mobile Filter Panel (Bottom Sheet) -->
  <div class="shop-page-mobile-filter-panel" id="mobileFilterPanel">
    <button class="close-btn" id="closeMobileFilter" aria-label="Close">&times;</button>
    <form method="get" id="filterFormMobile">
      <div class="shop-page-filter-content" style="flex-direction:column;gap:18px;">
        <div class="shop-page-search-box">
          <input type="text" id="shopSearchInputMobile" name="search" placeholder="Search products..." autocomplete="off">
        </div>
        <div class="shop-page-category-filter">
          <select name="category" id="categorySelectMobile">
            <option value="">All Categories</option>
            <?php foreach ($categoryTree as $cat): ?>
              <option value="<?php echo $cat['id']; ?>" <?php if ($selectedCategory == $cat['id']) echo 'selected'; ?>>
                <?php echo htmlspecialchars($cat['name']); ?>
              </option>
              <?php if (!empty($cat['children'])): ?>
                <?php foreach ($cat['children'] as $subcat): ?>
                  <option value="<?php echo $subcat['id']; ?>" <?php if ($selectedSubcategory == $subcat['id']) echo 'selected'; ?>>
                    &nbsp;&nbsp;— <?php echo htmlspecialchars($subcat['name']); ?>
                  </option>
                <?php endforeach; ?>
              <?php endif; ?>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="shop-page-price-range" style="flex-direction:column;align-items:flex-start;min-width:220px;">
          <div style="font-size:13px;margin-bottom:2px;">
            Price: ₹0 - ₹<span id="maxPriceDisplayMobile"><?php echo $maxPriceValue; ?></span>
          </div>
          <div style="width:100%;display:flex;align-items:center;gap:8px;">
            <input type="range" id="maxPriceRangeMobile" name="max_price" min="<?php echo $siteMinPrice; ?>" max="<?php echo $siteMaxPrice; ?>" value="<?php echo $maxPriceValue; ?>" step="1">
          </div>
        </div>
        <button type="submit" class="shop-page-filter-btn">Apply Filters</button>
        <button type="button" id="clearFilterBtnMobile" class="shop-page-filter-btn" style="background:#eee;color:#333;margin-left:8px;">Clear Filter</button>
      </div>
    </form>
  </div>

  <!-- Products Section -->
  <section class="shop-page-products">
    <div class="shop-page-product-grid">
      <?php if (empty($products)): ?>
        <div class="shop-page-no-products">No products found for selected filters.</div>
      <?php else: ?>
        <?php foreach ($products as $product): 
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
                  <input type="number" class="shop-page-quantity-input" value="1" min="1">
                </div>
              <?php endif; ?>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </section>
</div>

<script>
// --- Desktop Filter Logic ---
const maxPriceRangeDesktop = document.getElementById('maxPriceRangeDesktop');
const maxPriceDisplayDesktop = document.getElementById('maxPriceDisplayDesktop');
if (maxPriceRangeDesktop) {
  maxPriceRangeDesktop.addEventListener('input', function() {
    maxPriceDisplayDesktop.textContent = maxPriceRangeDesktop.value;
  });
}
const searchInputDesktop = document.getElementById('shopSearchInputDesktop');
const categorySelectDesktop = document.getElementById('categorySelectDesktop');
const filterFormDesktop = document.getElementById('filterFormDesktop');
const clearFilterBtnDesktop = document.getElementById('clearFilterBtnDesktop');

// --- Mobile Filter Logic ---
const openMobileFilter = document.getElementById('openMobileFilter');
const closeMobileFilter = document.getElementById('closeMobileFilter');
const mobileFilterPanel = document.getElementById('mobileFilterPanel');
const maxPriceRangeMobile = document.getElementById('maxPriceRangeMobile');
const maxPriceDisplayMobile = document.getElementById('maxPriceDisplayMobile');
const searchInputMobile = document.getElementById('shopSearchInputMobile');
const categorySelectMobile = document.getElementById('categorySelectMobile');
const filterFormMobile = document.getElementById('filterFormMobile');
const clearFilterBtnMobile = document.getElementById('clearFilterBtnMobile');

if (maxPriceRangeMobile) {
  maxPriceRangeMobile.addEventListener('input', function() {
    maxPriceDisplayMobile.textContent = maxPriceRangeMobile.value;
  });
}
if (openMobileFilter && mobileFilterPanel) {
  openMobileFilter.addEventListener('click', function() {
    mobileFilterPanel.classList.add('show');
    document.body.style.overflow = 'hidden';
  });
}
if (closeMobileFilter && mobileFilterPanel) {
  closeMobileFilter.addEventListener('click', function() {
    mobileFilterPanel.classList.remove('show');
    document.body.style.overflow = '';
  });
}

// --- AJAX search and price filter (shared) ---
const productGrid = document.querySelector('.shop-page-product-grid');
let searchTimeout = null;

function fetchProductsAJAX(form) {
  const formData = new FormData(form);
  const params = new URLSearchParams();
  for (const [key, value] of formData.entries()) {
    if (value) params.append(key, value);
  }
  productGrid.innerHTML = '<div class="shop-page-loading"><span class="spinner-border spinner-border-sm"></span> Loading...</div>';
  fetch('ajax/shop_filter.php?' + params.toString())
    .then(res => res.text())
    .then(html => {
      productGrid.innerHTML = html;
      if (mobileFilterPanel) {
        mobileFilterPanel.classList.remove('show');
        document.body.style.overflow = '';
      }
    });
}

if (searchInputDesktop) {
  searchInputDesktop.addEventListener('input', function() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => fetchProductsAJAX(filterFormDesktop), 350);
  });
}
if (categorySelectDesktop && maxPriceRangeDesktop) {
  [categorySelectDesktop, maxPriceRangeDesktop].forEach(input => {
    input.addEventListener('change', function() {
      fetchProductsAJAX(filterFormDesktop);
    });
  });
}
if (filterFormDesktop) {
  filterFormDesktop.addEventListener('submit', function(e) {
    e.preventDefault();
    fetchProductsAJAX(filterFormDesktop);
  });
}
if (clearFilterBtnDesktop) {
  clearFilterBtnDesktop.addEventListener('click', function() {
    searchInputDesktop.value = '';
    categorySelectDesktop.selectedIndex = 0;
    maxPriceRangeDesktop.value = <?php echo $siteMaxPrice; ?>;
    maxPriceDisplayDesktop.textContent = <?php echo $siteMaxPrice; ?>;
    fetchProductsAJAX(filterFormDesktop);
  });
}

// Mobile filter events
if (searchInputMobile) {
  searchInputMobile.addEventListener('input', function() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => fetchProductsAJAX(filterFormMobile), 350);
  });
}
if (categorySelectMobile && maxPriceRangeMobile) {
  [categorySelectMobile, maxPriceRangeMobile].forEach(input => {
    input.addEventListener('change', function() {
      fetchProductsAJAX(filterFormMobile);
    });
  });
}
if (filterFormMobile) {
  filterFormMobile.addEventListener('submit', function(e) {
    e.preventDefault();
    fetchProductsAJAX(filterFormMobile);
  });
}
if (clearFilterBtnMobile) {
  clearFilterBtnMobile.addEventListener('click', function() {
    searchInputMobile.value = '';
    categorySelectMobile.selectedIndex = 0;
    maxPriceRangeMobile.value = <?php echo $siteMaxPrice; ?>;
    maxPriceDisplayMobile.textContent = <?php echo $siteMaxPrice; ?>;
    fetchProductsAJAX(filterFormMobile);
  });
}
</script>

<?php include 'includes/footer.php'; ?> 