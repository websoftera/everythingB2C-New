<?php
// Sidebar Filter Component
// This component provides a responsive sidebar filter for all pages

// Get current page filters
$currentCategory = isset($_GET['category']) ? intval($_GET['category']) : null;
$currentSubcategory = isset($_GET['subcategory']) ? intval($_GET['subcategory']) : null;
$currentMinPrice = isset($_GET['min_price']) ? floatval($_GET['min_price']) : 0;
$currentMaxPrice = isset($_GET['max_price']) ? floatval($_GET['max_price']) : 10000;
$currentSearch = isset($_GET['q']) ? $_GET['q'] : '';
$currentSort = isset($_GET['sort']) ? $_GET['sort'] : 'newest';

// Fixed price range for slider
$siteMinPrice = 0;
$siteMaxPrice = 10000;

// Only use fixed range if no specific prices are provided
if (!isset($_GET['min_price']) && !isset($_GET['max_price'])) {
    $currentMinPrice = $siteMinPrice;
    $currentMaxPrice = $siteMaxPrice;
}

// Get all categories for filter
$categories = getAllCategories();
$categoryTree = buildCategoryTree($categories);
?>

<!-- Sidebar Filter Component -->
<div class="sidebar-filter-container">

  <!-- Mobile Filter Toggle Buttons -->
  <div class="mobile-filter-toggles d-lg-none">
    <button class="filter-toggle-btn" id="sidebarFilterToggle" type="button" data-toggle="category">
      <i class="bi bi-funnel"></i> <span>Filter</span>
    </button>
    <button class="filter-toggle-btn" id="sidebarSortToggle" type="button" data-toggle="sort">
      <i class="bi bi-arrow-down-up"></i> <span>Sort</span>
    </button>
  </div>

  <!-- Sidebar Filter Panel -->
  <div class="sidebar-filter-panel" id="sidebarFilterPanel">
    <div class="sidebar-filter-header">
      <h4>Filters</h4>
      <button class="filter-close-btn d-lg-none" id="sidebarFilterClose">
        <i class="bi bi-x-lg"></i>
      </button>
    </div>

    <!-- Desktop View (Unchanged to maintain existing UI) -->
    <div class="desktop-filter-view d-none d-lg-block">
      <form method="get" id="sidebarFilterForm" class="sidebar-filter-form" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
        <!-- Preserve essential page parameters -->
        <?php if (isset($_GET['slug'])): ?>
          <input type="hidden" name="slug" value="<?php echo htmlspecialchars($_GET['slug']); ?>">
        <?php endif; ?>
        
        <!-- Search Filter -->
        <div class="filter-section">
          <h5>Search</h5>
          <div class="form-group">
            <input type="text" name="q" value="<?php echo htmlspecialchars($currentSearch); ?>" 
                   placeholder="Search products..." class="form-control">
          </div>
        </div>

        <!-- Category Filter -->
        <div class="filter-section">
          <h5>Categories</h5>
          <div class="form-group">
            <select name="category" id="sidebarCategorySelect" class="form-control" onchange="document.getElementById('sidebarFilterForm').dispatchEvent(new Event('submit'))">
              <option value="">All Categories</option>
              <?php 
              // Re-use current function if existing
              if (!function_exists('renderCategoriesWithSubcategories')) {
                  function renderCategoriesWithSubcategories($categories, $parentId = null, $level = 0) {
                      $output = '';
                      $indent = str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', $level);
                      foreach ($categories as $cat) {
                          if ($cat['parent_id'] == $parentId) {
                              $output .= '<option value="' . $cat['id'] . '"';
                              if (isset($_GET['category']) && $_GET['category'] == $cat['id']) {
                                  $output .= ' selected';
                              }
                              $output .= '>' . $indent . htmlspecialchars($cat['name']) . '</option>';
                              $output .= renderCategoriesWithSubcategories($categories, $cat['id'], $level + 1);
                          }
                      }
                      return $output;
                  }
              }
              echo renderCategoriesWithSubcategories($categories);
              ?>
            </select>
          </div>
        </div>

        <!-- Price Range Filter - COMMENTED OUT FOR DESKTOP -->
        <!--
        <div class="filter-section">
           ...
        </div>
        -->

        <!-- Option Actions -->
        <div class="filter-actions">
          <button type="submit" class="filter-btn filter-btn-primary" title="Apply Filters">
            <i class="fas fa-check"></i> Apply
          </button>
          <button type="button" class="filter-btn filter-btn-secondary" id="clearAllFilters" title="Clear All Filters">
            <i class="fas fa-times"></i> Clear All
          </button>
        </div>
      </form>
    </div>

    <!-- Mobile View (DMart Style - Tabs & Content Layout) -->
    <div class="mobile-filter-layout d-lg-none">
      <form method="get" id="mobileFilterForm" class="sidebar-filter-form" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
        <!-- Preserve essential page parameters -->
        <?php if (isset($_GET['slug'])): ?>
          <input type="hidden" name="slug" value="<?php echo htmlspecialchars($_GET['slug']); ?>">
        <?php endif; ?>
        
        <div class="mobile-filter-body">
          <!-- Left Navigation -->
          <div class="mobile-filter-side-nav">
            <button type="button" class="mob-nav-item active" data-target="mob-sec-category">Category</button>

            <button type="button" class="mob-nav-item" data-target="mob-sec-sort">Sort</button>
          </div>

          <!-- Right Content Pane -->
          <div class="mobile-filter-content">
            <!-- Search Inside Content Pane (Local List Filter) -->
            <div class="mob-filter-search-container">
              <div class="mob-search-wrapper">
                <input type="text" placeholder="Search Categories..." class="form-control mob-category-search">
                <button type="button" class="mob-search-clear" id="mobSearchClear">&times;</button>
              </div>
            </div>

            <!-- Category Pane -->
            <div class="mob-filter-pane active" id="mob-sec-category">
              <div class="mob-options-list">
                <?php 
                // Define helper for list on-the-fly or inside
                if (!function_exists('renderMobileCategoriesList')) {
                    function renderMobileCategoriesList($categories, $parentId = null, $level = 0) {
                        $output = '';
                        foreach ($categories as $cat) {
                            if ($cat['parent_id'] == $parentId) {
                                $checked = '';
                                if (isset($_GET['category'])) {
                                    if (is_array($_GET['category'])) {
                                        $checked = in_array($cat['id'], $_GET['category']) ? 'checked' : '';
                                    } else {
                                        $checked = ($_GET['category'] == $cat['id']) ? 'checked' : '';
                                    }
                                }
                                $indentStyle = 'padding-left: ' . ($level * 15 + 15) . 'px;';
                                
                                $output .= '<label class="mob-radio-item" style="' . $indentStyle . '">';
                                $output .= '<input type="checkbox" name="category[]" value="' . $cat['id'] . '" ' . $checked . '>';
                                $output .= '<span class="mob-radio-label">' . htmlspecialchars($cat['name']) . '</span>';
                                $output .= '<span class="mob-radio-circle"></span>';
                                $output .= '</label>';
                                
                                $output .= renderMobileCategoriesList($categories, $cat['id'], $level + 1);
                            }
                        }
                        return $output;
                    }
                }
                echo renderMobileCategoriesList($categories);
                ?>
              </div>
            </div>



            <!-- Sort Pane -->
            <div class="mob-filter-pane" id="mob-sec-sort">
              <div class="mob-options-list">
                <label class="mob-radio-item">
                  <input type="radio" name="sort" value="newest" <?php if ($currentSort == 'newest') echo 'checked'; ?>>
                  <span class="mob-radio-label">Newest First</span>
                  <span class="mob-radio-circle"></span>
                </label>
                <label class="mob-radio-item">
                  <input type="radio" name="sort" value="oldest" <?php if ($currentSort == 'oldest') echo 'checked'; ?>>
                  <span class="mob-radio-label">Oldest First</span>
                  <span class="mob-radio-circle"></span>
                </label>
                <label class="mob-radio-item">
                  <input type="radio" name="sort" value="price_low" <?php if ($currentSort == 'price_low') echo 'checked'; ?>>
                  <span class="mob-radio-label">Price: Low to High</span>
                  <span class="mob-radio-circle"></span>
                </label>
                <label class="mob-radio-item">
                  <input type="radio" name="sort" value="price_high" <?php if ($currentSort == 'price_high') echo 'checked'; ?>>
                  <span class="mob-radio-label">Price: High to Low</span>
                  <span class="mob-radio-circle"></span>
                </label>
              </div>
            </div>

          </div>
        </div>

        <!-- Fixed Footer Actions -->
        <div class="mob-filter-footer">
          <button type="button" class="mob-filter-foot-btn mob-clear-btn" id="mobileClearAllFilters">CLEAR ALL</button>
          <button type="submit" class="mob-filter-foot-btn mob-apply-btn">APPLY</button>
        </div>
      </form>
    </div>

  </div>
</div>

<style>
/* Sidebar Filter Styles */
.sidebar-filter-container {
  position: relative;
}

.filter-toggle-btn {
  width: auto !important; /* Allow button to shrink to content */
  align-self: flex-end !important; /* Push to right if in flex container */
  margin-left: auto !important; /* Push to right in block flow */
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 8px;
  padding: 10px 20px;
  background: white;
  color: #99d052;
  border: 1px solid #99d052;
  border-radius: 20px;
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
  margin-bottom: 15px;
  transition: all 0.2s ease;
}

.filter-toggle-btn .filter-icon-css {
  width: 16px;
  height: 12px;
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 16'%3E%3Cg fill='%2399d052'%3E%3Crect x='2' y='4' width='16' height='2' rx='1'/%3E%3Crect x='2' y='10' width='16' height='2' rx='1'/%3E%3Crect x='9' y='1' width='2' height='6' rx='1'/%3E%3Crect x='9' y='7' width='2' height='6' rx='1'/%3E%3C/g%3E%3C/svg%3E");
  background-repeat: no-repeat;
  background-size: contain;
  background-position: center;
}

.filter-toggle-btn:hover {
  background: #99d052;
  color: white;
}

.filter-toggle-btn:hover .filter-icon-css {
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 16'%3E%3Cg fill='white'%3E%3Crect x='2' y='4' width='16' height='2' rx='1'/%3E%3Crect x='2' y='10' width='16' height='2' rx='1'/%3E%3Crect x='9' y='1' width='2' height='6' rx='1'/%3E%3Crect x='9' y='7' width='2' height='6' rx='1'/%3E%3C/g%3E%3C/svg%3E");
}

.sidebar-filter-panel {
  background: white;
  border: 1px solid #e9ecef;
  border-radius: 8px;
  padding: 20px;
  box-shadow: none; /* Removed highlighting shadow per user request */
  margin-bottom: 20px;
}

.sidebar-filter-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
  padding-bottom: 15px;
  border-bottom: 1px solid #e9ecef;
}

.sidebar-filter-header h4 {
  margin: 0;
  font-size: 18px;
  font-weight: 600;
  color: #333;
}

  .filter-close-btn {
    background: none;
    border: none;
    font-size: 20px;
    color: #666;
    cursor: pointer;
    padding: 0;
    width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    transition: background-color 0.2s;
  }

  .filter-close-btn:hover {
    background: #f8f9fa;
    color: #333;
  }

.filter-section {
  margin-bottom: 25px;
}

.filter-section h5 {
  font-size: 14px;
  font-weight: 600;
  color: #333;
  margin-bottom: 10px;
}

.form-group {
  margin-bottom: 15px;
}

.form-control {
  width: 100%;
  padding: 8px 12px;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size: 14px;
  transition: border-color 0.2s;
}

.form-control:focus {
  outline: none;
  border-color: #007bff;
  box-shadow: 0 0 0 2px rgba(0, 123, 255, 0.1);
}

select.form-control {
  appearance: none;
  -webkit-appearance: none;
  -moz-appearance: none;
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='%23666' viewBox='0 0 16 16'%3E%3Cpath d='M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E");
  background-repeat: no-repeat;
  background-position: calc(100% - 12px) center;
  padding-right: 30px;
}

.price-range-container {
  padding: 10px 0;
}

.price-display {
  text-align: center;
  font-size: 14px;
  font-weight: 500;
  color: #333;
  margin-bottom: 15px;
}

.price-inputs {
  display: flex;
  justify-content: space-between;
  gap: 15px;
  margin-bottom: 20px;
}

.price-input-group {
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.price-input-group label {
  font-size: 12px;
  font-weight: 600;
  color: #333;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.price-input-wrapper {
  display: flex;
  align-items: center;
  border: 2px solid #e9ecef;
  border-radius: 8px;
  padding: 8px 12px;
  background-color: white;
  transition: all 0.2s ease;
  box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.price-input-wrapper:focus-within {
  border-color: #007bff;
  box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1);
}

.price-input {
  width: 100%;
  border: none;
  background: none;
  font-size: 16px;
  font-weight: 500;
  text-align: center;
  color: #333;
  outline: none;
}

.price-input:focus {
  outline: none;
}

.currency-symbol {
  font-size: 16px;
  font-weight: 500;
  color: #666;
  margin-right: 5px;
}

.price-sliders {
  position: relative;
  height: 40px;
}

.slider-track {
  position: absolute;
  top: 50%;
  left: 0;
  width: 100%;
  height: 6px;
  background: #e9ecef;
  border-radius: 3px;
  transform: translateY(-50%);
  z-index: 1;
}

.slider-fill {
  position: absolute;
  top: 50%;
  left: 0;
  height: 6px;
  background: #007bff;
  border-radius: 3px;
  transform: translateY(-50%);
  z-index: 2;
}

.price-sliders input[type="range"] {
  position: absolute;
  width: 100%;
  height: 6px;
  background: transparent;
  border-radius: 3px;
  outline: none;
  pointer-events: none;
  -webkit-appearance: none;
  z-index: 3;
}

.price-sliders input[type="range"]::-webkit-slider-thumb {
  -webkit-appearance: none;
  appearance: none;
  width: 20px;
  height: 20px;
  background: #007bff;
  border-radius: 50%;
  cursor: pointer;
  pointer-events: auto;
  box-shadow: 0 2px 6px rgba(0,0,0,0.3);
  border: 2px solid white;
  transition: all 0.2s ease;
}

.price-sliders input[type="range"]::-webkit-slider-thumb:hover {
  background: #0056b3;
  transform: scale(1.1);
  box-shadow: 0 4px 12px rgba(0,0,0,0.4);
}

.price-sliders input[type="range"]::-moz-range-thumb {
  width: 20px;
  height: 20px;
  background: #007bff;
  border-radius: 50%;
  cursor: pointer;
  border: 2px solid white;
  box-shadow: 0 2px 6px rgba(0,0,0,0.3);
  transition: all 0.2s ease;
}

.price-sliders input[type="range"]::-moz-range-thumb:hover {
  background: #0056b3;
  transform: scale(1.1);
  box-shadow: 0 4px 12px rgba(0,0,0,0.4);
}

.price-sliders input[type="range"]::-webkit-slider-track {
  background: transparent;
}

.price-sliders input[type="range"]::-moz-range-track {
  background: transparent;
}

#minPriceSlider {
  top: 0;
}

#maxPriceSlider {
  top: 0;
}

.price-range-info {
  text-align: center;
  font-size: 12px;
  color: #666;
  margin-top: 15px;
}

.range-label {
  background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
  padding: 8px 16px;
  border-radius: 20px;
  border: 1px solid #dee2e6;
  font-weight: 500;
  color: #495057;
  display: inline-block;
  box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.filter-actions {
  display: flex;
  gap: 10px;
  margin-top: 20px;
  padding-top: 20px;
  border-top: 1px solid #e9ecef;
}

.filter-btn {
  flex: 1;
  padding: 10px 16px;
  border: none;
  border-radius: 6px;
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 6px;
}

.filter-btn-primary {
  background: #9fbe1b;;
  color: white;
}

.filter-btn-primary:hover {
  background: #9fbe1b;
}

.filter-btn-secondary {
  background: white;
  color: #6c757d;
  border: 1px solid #6c757d;
}

.filter-btn-secondary:hover {
  background: #007bff;
  color: white;
  border-color: #007bff;
}

.filter-clear-btn {
  display: inline-block;
  padding: 10px 20px;
  background: #007bff;
  color: white;
  text-decoration: none;
  border-radius: 6px;
  font-size: 14px;
  font-weight: 500;
  transition: background-color 0.2s;
}

.filter-clear-btn:hover {
  background: #0056b3;
  color: white;
  text-decoration: none;
}

/* Responsive Design */
@media (max-width: 991.98px) {
  .sidebar-filter-panel {
    position: fixed;
    top: 15%; /* Top spacing for bottom-sheet effect */
    left: -100%;
    width: 100%;
    bottom: 0;
    height: auto; /* Takes remaining space to avoid cutting off bottom buttons */
    z-index: 1050;
    border-radius: 20px 20px 0 0; /* Rounded top corners */
    transition: left 0.3s ease;
    overflow-y: hidden;
    display: flex;
    flex-direction: column;
    padding: 0 !important;
    box-shadow: 0 -5px 25px rgba(0,0,0,0.15);
  }

  /* Support Backdrop for mobile bottom sheet offset view */
  .sidebar-filter-panel.show::before {
    content: '';
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 15%; /* Covers top buffer */
    background: rgba(0,0,0,0.5);
    z-index: -1;
  }

  .sidebar-filter-panel.show {
    left: 0;
  }

  .sidebar-filter-header {
    position: sticky;
    top: 0;
    background: #ffffff; /* White background for modal header */
    z-index: 10;
    padding: 15px 20px;
    border-bottom: 1px solid #e9ecef;
    margin-bottom: 0px !important;
    border-radius: 20px 20px 0 0 !important; /* Fix background corner overlap */
  }

  .sidebar-filter-header h4 {
    font-size: 22px !important; /* Slightly bigger */
    font-weight: 700 !important;
    color: #007bff !important; /* Blue Title */
  }

  /* Reduce space before filter buttons for mobile (2px after breadcrumbs) */
  .category-container, .shop-page-container, .search-page-container, .products-list-page-container, .wishlist-container {
    margin-top: 2px !important;
  }

  /* Side-by-Side Mobile Toggles Custom CSS */
  .mobile-filter-toggles {
    display: flex;
    gap: 12px;
    margin-bottom: 4px !important; /* 4px bottom space before cards */
    width: 100%;
    justify-content: flex-end; /* Align right */
  }

  .mobile-filter-toggles .filter-toggle-btn {
    flex: 0 0 auto; /* Compact width fit content */
    width: auto !important;
    margin-bottom: 0 !important;
    margin-left: 0 !important;
    border-radius: 20px;
    justify-content: center;
    padding: 6px 16px !important; /* Proper padding for pill button */
    height: 34px !important; /* Standard height */
    display: inline-flex;
    align-items: center;
    font-size: 14px;
  }

  /* Custom Mobile Filter Layout (DMart Style) */
  .mobile-filter-layout {
    display: flex !important; 
    flex-direction: column;
    flex: 1;
    overflow: hidden; /* Prevent overall wrapper scrolling */
  }

  .mobile-filter-layout form {
    display: flex;
    flex-direction: column;
    height: 100%;
  }

  .mobile-filter-body {
    display: flex;
    flex: 1;
    overflow: hidden; /* Individual scrolling in nav and content */
  }

  /* Left Navigation Node */
  .mobile-filter-side-nav {
    width: 120px;
    background-color: #f6f8fa;
    border-right: 1px solid #e9ecef;
    display: flex;
    flex-direction: column;
    overflow-y: auto;
  }

  .mob-nav-item {
    padding: 18px 15px;
    background: none;
    border: none;
    border-bottom: 1px solid #e9ecef;
    text-align: left;
    font-size: 14px;
    font-weight: 500;
    color: #444;
    cursor: pointer;
    border-left: 4px solid transparent;
    transition: all 0.2s;
  }

  .mob-nav-item.active {
    background-color: white;
    color: #9fbe1b; /* local matching shade */
    border-left-color: #9fbe1b;
    font-weight: 600;
  }

  /* Right Content Pane */
  .mobile-filter-content {
    flex: 1;
    background: #fff;
    display: flex;
    flex-direction: column;
    overflow: hidden;
  }

  .mob-filter-search-container {
    padding: 12px 15px;
    border-bottom: 1px solid #f0f0f0;
  }

  .mob-search-wrapper {
    position: relative;
    display: flex;
    align-items: center;
    width: 100%;
  }

  .mob-category-search {
    width: 100%;
    border-radius: 20px !important;
    padding: 8px 35px 8px 15px !important; /* Right padding for X */
    font-size: 14px;
    background-color: #f1f3f5;
    border: none !important;
  }

  .mob-search-clear {
    position: absolute;
    right: 12px;
    background: none;
    border: none;
    font-size: 20px;
    color: #888;
    cursor: pointer;
    padding: 0;
    line-height: 1;
    display: none; /* Hidden by default toggled in JS */
  }

  .mob-search-clear:hover {
    color: #333;
  }

  .mob-filter-pane {
    display: none;
    flex: 1;
    overflow-y: auto;
    padding: 15px;
  }

  .mob-filter-pane.active {
    display: block;
  }

  /* Option Lists on Mobile Pane */
  .mob-options-list {
    display: flex;
    flex-direction: column;
    gap: 2px;
  }

  .mob-radio-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 5px 10px !important; /* Compact Row spacing */
    border-bottom: 1px solid #f8f9fa;
    cursor: pointer;
    position: relative;
    margin-bottom: 0;
  }

  .mob-radio-item input[type="radio"],
  .mob-radio-item input[type="checkbox"] {
    position: absolute;
    opacity: 0;
    width: 0;
    height: 0;
  }

  .mob-radio-label {
    font-size: 15px;
    color: #333;
    flex: 1;
  }

  .mob-radio-circle {
    width: 20px;
    height: 20px;
    border: 2px solid #ccc;
    border-radius: 4px; /* Makes it look like a Checkbox (Square) */
    display: inline-block;
    position: relative;
    transition: all 0.2s;
  }

  .mob-radio-item input[type="radio"]:checked + .mob-radio-label,
  .mob-radio-item input[type="checkbox"]:checked + .mob-radio-label {
    color: #9fbe1b;
    font-weight: 500;
  }

  .mob-radio-item input[type="radio"]:checked ~ .mob-radio-circle,
  .mob-radio-item input[type="checkbox"]:checked ~ .mob-radio-circle {
    border-color: #9fbe1b;
    background-color: #9fbe1b; /* Green fill filled */
  }

  .mob-radio-item input[type="radio"]:checked ~ .mob-radio-circle::after,
  .mob-radio-item input[type="checkbox"]:checked ~ .mob-radio-circle::after {
    content: '\2713'; /* Tick checkmark symbol */
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    color: white;
    font-size: 12px;
    font-weight: bold;
    width: auto;
    height: auto;
    background: none;
    border-radius: 0;
  }


  /* Fixed Footer */
  .mob-filter-footer {
    display: flex;
    padding: 15px;
    border-top: 1px solid #eee;
    background: white;
    gap: 15px;
    position: sticky;
    bottom: 0;
    z-index: 10;
  }

  .mob-filter-foot-btn {
    flex: 1;
    padding: 12px;
    border: 1px solid #ccc;
    background: white;
    font-weight: 600;
    font-size: 14px;
    border-radius: 6px;
    cursor: pointer;
    text-align: center;
  }

  .mob-apply-btn {
    background: #ccc !important;
    color: #888 !important;
    border: none !important;
    pointer-events: none;
    transition: all 0.3s ease;
  }

  .mob-apply-btn.active-btn {
    background: #9fbe1b !important;
    color: white !important;
    pointer-events: auto;
  }

  .mob-clear-btn {
    color: #666;
    background: #fff;
  }

  /* Price styling enhancements for compactness */
  .mob-filter-pane .price-input-wrapper {
    padding: 6px 10px;
  }
}

@media (min-width: 992px) {
  /* Hide Mobile Close Button */
  .filter-close-btn {
    display: none;
  }

  /* Horizontal Filter Layout container */
  .sidebar-filter-panel {
    margin: 0 auto 25px auto;
    padding: 15px 20px;
    max-width: 800px; /* narrowed max-width for compact view */
    display: flex;
    justify-content: center; /* Center the form contents */
  }

  /* Hide Header label on desktop (Filters) */
  .sidebar-filter-header {
    display: none;
  }

  /* Transform the form into a horizontally aligned flex row */
  .sidebar-filter-form {
    display: flex;
    flex-direction: row;
    align-items: center;
    justify-content: center;
    gap: 20px; /* Increased gap for better separation */
    flex-wrap: nowrap;
    width: 100%;
  }

  /* Remove margins and titles from internal filter sections */
  .filter-section {
    margin-bottom: 0;
    display: flex;
    align-items: center;
    gap: 8px;
  }

  .filter-section h5 {
    margin-bottom: 0;
    white-space: nowrap;
    font-size: 13px; /* Slightly smaller text for compact layout */
  }

  .form-group {
    margin-bottom: 0;
    width: auto;
    min-width: 180px; /* Compact inputs slightly */
  }
  
  .form-control {
    height: 38px; /* Standardize height */
    padding: 6px 12px;
  }

  .filter-actions {
    margin-top: 0;
    padding-top: 0;
    border-top: none;
    display: flex;
    flex-direction: row;
    align-items: center;
    gap: 10px;
    height: 38px;
  }
  
  .filter-actions .filter-btn {
    height: 38px;
    padding: 0 16px;
    white-space: nowrap;
    display: inline-flex;
    align-items: center; /* keep icon and text vertically centered */
    justify-content: center;
    flex: none; /* Prevent buttons from stretching vertically or horizontally weirdly */
  }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const filterToggle = document.getElementById('sidebarFilterToggle');
  const sortToggle = document.getElementById('sidebarSortToggle');
  const filterPanel = document.getElementById('sidebarFilterPanel');
  const filterClose = document.getElementById('sidebarFilterClose');

  function openModalWithTab(tabTargetId) {
    if (filterPanel) {
      filterPanel.classList.add('show');
      document.body.style.overflow = 'hidden';

      // Activate corresponding mobile nav item
      const tabBtn = document.querySelector(`.mob-nav-item[data-target="${tabTargetId}"]`);
      if (tabBtn) {
        tabBtn.click();
      }
    }
  }

  if (filterToggle) {
    filterToggle.addEventListener('click', function() {
      openModalWithTab('mob-sec-category');
    });
  }

  if (sortToggle) {
    sortToggle.addEventListener('click', function() {
      openModalWithTab('mob-sec-sort');
    });
  }

  if (filterClose && filterPanel) {
    filterClose.addEventListener('click', function() {
      filterPanel.classList.remove('show');
      document.body.style.overflow = '';
    });
  }

  // Price range sliders
  const minPriceSlider = document.getElementById('minPriceSlider');
  const maxPriceSlider = document.getElementById('maxPriceSlider');
  const minPriceInput = document.getElementById('minPriceInput');
  const maxPriceInput = document.getElementById('maxPriceInput');
  const sliderFill = document.getElementById('sliderFill');

  if (minPriceSlider && maxPriceSlider && minPriceInput && maxPriceInput && sliderFill) {
    // Update sliderFill width based on min and max values
    function updateSliderFill() {
      const minVal = parseInt(minPriceInput.value);
      const maxVal = parseInt(maxPriceInput.value);
      const minRange = parseInt(minPriceSlider.min);
      const maxRange = parseInt(minPriceSlider.max);

      const fillWidth = ((maxVal - minVal) / (maxRange - minRange)) * 100;
      sliderFill.style.width = `${fillWidth}%`;
    }

    // Sync slider with input
    function syncSliderWithInput(slider, input) {
      slider.value = input.value;
      updateSliderFill();
    }

    // Sync input with slider
    function syncInputWithSlider(input, slider) {
      input.value = slider.value;
      updateSliderFill();
    }

    // Min price handlers
    minPriceSlider.addEventListener('input', function() {
      const value = this.value;
      minPriceInput.value = value;
      
      // Ensure min doesn't exceed max
      if (parseInt(value) > parseInt(maxPriceSlider.value)) {
        maxPriceSlider.value = value;
        maxPriceInput.value = value;
      }
      updateSliderFill();
    });

    minPriceInput.addEventListener('input', function() {
      const value = this.value;
      minPriceSlider.value = value;
      
      // Ensure min doesn't exceed max
      if (parseInt(value) > parseInt(maxPriceInput.value)) {
        maxPriceInput.value = value;
        maxPriceSlider.value = value;
      }
      updateSliderFill();
    });

    // Max price handlers
    maxPriceSlider.addEventListener('input', function() {
      const value = this.value;
      maxPriceInput.value = value;
      
      // Ensure max doesn't go below min
      if (parseInt(value) < parseInt(minPriceSlider.value)) {
        minPriceSlider.value = value;
        minPriceInput.value = value;
      }
      updateSliderFill();
    });

    maxPriceInput.addEventListener('input', function() {
      const value = this.value;
      maxPriceSlider.value = value;
      
      // Ensure max doesn't go below min
      if (parseInt(value) < parseInt(minPriceInput.value)) {
        minPriceInput.value = value;
        minPriceSlider.value = value;
      }
      updateSliderFill();
    });

    // Initial call to set the fill width
    updateSliderFill();
  }

  // Clear all filters
  const clearFiltersBtn = document.getElementById('clearAllFilters');
  if (clearFiltersBtn) {
    clearFiltersBtn.addEventListener('click', function() {
      const form = document.getElementById('sidebarFilterForm');
      if (form) {
        // Reset form
        form.reset();
        
        // Reset price displays
        if (minPriceInput) minPriceInput.value = '0';
        if (maxPriceInput) maxPriceInput.value = '10000';
        
        // Get current page URL and preserve essential parameters
        const currentUrl = window.location.pathname;
        const urlParams = new URLSearchParams(window.location.search);
        const slug = urlParams.get('slug');
        
        if (slug) {
          window.location.href = currentUrl + '?slug=' + slug;
        } else {
          window.location.href = currentUrl;
        }
      }
    });
  }

  // Handle form submission to maintain current page context
  const filterForm = document.getElementById('sidebarFilterForm');
  if (filterForm) {
    filterForm.addEventListener('submit', function(e) {
      e.preventDefault();
      
      // Get current page URL
      const currentUrl = window.location.pathname;
      
      // Create form data
      const formData = new FormData(this);
      const params = new URLSearchParams();
      
      // Preserve essential URL parameters
      const urlParams = new URLSearchParams(window.location.search);
      const slug = urlParams.get('slug');
      if (slug) {
        params.append('slug', slug);
      }
      
      // Add form data to params
      for (const [key, value] of formData.entries()) {
        if (key !== 'slug') { // Don't duplicate slug
          // Include empty values for category parameter to ensure "All Categories" works
          if (key === 'category' || value) {
            params.append(key, value);
          }
        }
      }
      
      // Build new URL
      const newUrl = currentUrl + (params.toString() ? '?' + params.toString() : '');
      
      // Navigate to new URL
      window.location.href = newUrl;
    });
  }

  // --- Mobile Local Search Filter (Pane Search) ---
  const mobSearchInput = document.querySelector('.mob-category-search');
  const mobSearchClear = document.getElementById('mobSearchClear');
  const mobRadioItems = document.querySelectorAll('#mob-sec-category .mob-radio-item');

  if (mobSearchInput) {
    mobSearchInput.addEventListener('input', function() {
      const val = this.value.toLowerCase().trim();
      
      if (mobSearchClear) {
        mobSearchClear.style.display = val.length > 0 ? 'block' : 'none';
      }

      mobRadioItems.forEach(item => {
        const labelNode = item.querySelector('.mob-radio-label');
        if (labelNode) {
          const txt = labelNode.textContent.toLowerCase();
          item.style.display = txt.includes(val) ? 'flex' : 'none';
        }
      });
    });
  }

  if (mobSearchClear) {
    mobSearchClear.addEventListener('click', function() {
      if (mobSearchInput) {
        mobSearchInput.value = '';
        mobSearchInput.dispatchEvent(new Event('input')); // Trigger visibility updates
      }
    });
  }

  // --- Mobile Filter Tabs & Actions Handler ---
  const mobNavItems = document.querySelectorAll('.mob-nav-item');
  const mobPanes = document.querySelectorAll('.mob-filter-pane');

  if (mobNavItems.length > 0 && mobPanes.length > 0) {
    mobNavItems.forEach(item => {
      item.addEventListener('click', function() {
        // Toggle Nav item active status
        mobNavItems.forEach(i => i.classList.remove('active'));
        this.classList.add('active');

        // Toggle Content Pane active status
        mobPanes.forEach(p => p.classList.remove('active'));
        const targetId = this.getAttribute('data-target');
        const targetPane = document.getElementById(targetId);
        if (targetPane) {
          targetPane.classList.add('active');
        }
      });
    });
  }

  // Mobile Clear All Filters
  const mobClearFiltersBtn = document.getElementById('mobileClearAllFilters');
  if (mobClearFiltersBtn) {
    mobClearFiltersBtn.addEventListener('click', function() {
      const mobForm = document.getElementById('mobileFilterForm');
      if (mobForm) {
        // Uncheck all radios and checkboxes
        mobForm.querySelectorAll('input[type="radio"], input[type="checkbox"]').forEach(r => r.checked = false);
        // Clear Search
        const searchIn = mobForm.querySelector('.mob-search-input');
        if (searchIn) searchIn.value = '';
        // Clear Price Inputs
        mobForm.querySelectorAll('.price-input').forEach(i => i.value = '');
        
        // Trigger submit to apply clear
        mobForm.dispatchEvent(new Event('submit'));
      }
    });
  }

  // Handle Mobile Form Submission
  const mobFilterForm = document.getElementById('mobileFilterForm');
  if (mobFilterForm) {
    mobFilterForm.addEventListener('submit', function(e) {
      e.preventDefault();
      
      const currentUrl = window.location.pathname;
      const formData = new FormData(this);
      const params = new URLSearchParams();
      
      const urlParams = new URLSearchParams(window.location.search);
      const slug = urlParams.get('slug');
      if (slug) params.append('slug', slug);

      for (const [key, value] of formData.entries()) {
        if (key !== 'slug' && value !== '') {
          // Add standard parameter or handle arrays if needed
          params.append(key, value);
        }
      }
      
      window.location.href = currentUrl + (params.toString() ? '?' + params.toString() : '');
    });
  }

  // --- Mobile Apply Button State Logic ---
  const applyBtn = document.querySelector('.mob-apply-btn');
  
  if (mobFilterForm && applyBtn) {
      // Store initial state
      const initialData = new FormData(mobFilterForm);
      const initialState = new URLSearchParams(initialData).toString();
      
      function updateApplyState() {
          const currentData = new FormData(mobFilterForm);
          const currentState = new URLSearchParams(currentData).toString();
          
          if (currentState !== initialState) {
              applyBtn.classList.add('active-btn');
              applyBtn.disabled = false;
          } else {
              applyBtn.classList.remove('active-btn');
              applyBtn.disabled = true;
          }
      }
      
      mobFilterForm.addEventListener('change', updateApplyState);
      mobFilterForm.addEventListener('input', updateApplyState);
      updateApplyState(); // Set initial state
  }
});
</script> 
<script src="asset/js/live-filter.js"></script>