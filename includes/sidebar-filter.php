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
  <!-- Mobile Filter Toggle Button -->
  <button class="filter-toggle-btn d-lg-none" id="sidebarFilterToggle">
    <span class="filter-icon-css"></span>
    <span>Filter</span>
  </button>

  <!-- Sidebar Filter Panel -->
  <div class="sidebar-filter-panel" id="sidebarFilterPanel">
    <div class="sidebar-filter-header">
      <h4>Filters</h4>
      <button class="filter-close-btn d-lg-none" id="sidebarFilterClose">
        <i class="bi bi-x-lg"></i>
      </button>
    </div>

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
          <select name="category" id="sidebarCategorySelect" class="form-control">
            <option value="">All Categories</option>
            <?php 
            // Function to render categories with subcategories as nested options
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
                        
                        // Recursively add subcategories
                        $output .= renderCategoriesWithSubcategories($categories, $cat['id'], $level + 1);
                    }
                }
                
                return $output;
            }
            
            echo renderCategoriesWithSubcategories($categories);
            ?>
          </select>
        </div>
      </div>

      <!-- Price Range Filter - COMMENTED OUT FOR NOW -->
      <!--
      <div class="filter-section">
        <h5>Price Range</h5>
        <div class="price-range-container">
          <div class="price-display">
            <div class="price-inputs">
              <div class="price-input-group">
                <label>Min</label>
                <div class="price-input-wrapper">
                  <span class="currency-symbol">₹</span>
                  <input type="number" id="minPriceInput" class="price-input" 
                         min="<?php echo $siteMinPrice; ?>" max="<?php echo $siteMaxPrice; ?>" 
                         value="<?php echo $currentMinPrice; ?>" step="10">
                </div>
              </div>
              <div class="price-input-group">
                <label>Max</label>
                <div class="price-input-wrapper">
                  <span class="currency-symbol">₹</span>
                  <input type="number" id="maxPriceInput" class="price-input" 
                         min="<?php echo $siteMinPrice; ?>" max="<?php echo $siteMaxPrice; ?>" 
                         value="<?php echo $currentMaxPrice; ?>" step="10">
                </div>
              </div>
            </div>
          </div>
          <div class="price-sliders">
            <div class="slider-track">
              <div class="slider-fill" id="sliderFill"></div>
            </div>
            <input type="range" id="minPriceSlider" name="min_price" 
                   min="<?php echo $siteMinPrice; ?>" max="<?php echo $siteMaxPrice; ?>" 
                   value="<?php echo $currentMinPrice; ?>" step="10">
            <input type="range" id="maxPriceSlider" name="max_price" 
                   min="<?php echo $siteMinPrice; ?>" max="<?php echo $siteMaxPrice; ?>" 
                   value="<?php echo $currentMaxPrice; ?>" step="10">
          </div>
          <div class="price-range-info">
            <span class="range-label">Range: ₹<?php echo number_format($siteMinPrice); ?> - ₹<?php echo number_format($siteMaxPrice); ?></span>
          </div>
        </div>
      </div>
      -->

      <!-- Sort Options - COMMENTED OUT FOR NOW -->
      <!--
      <div class="filter-section">
        <h5>Sort By</h5>
        <div class="form-group">
          <select name="sort" class="form-control">
            <option value="newest" <?php if ($currentSort == 'newest') echo 'selected'; ?>>Newest First</option>
            <option value="oldest" <?php if ($currentSort == 'oldest') echo 'selected'; ?>>Oldest First</option>
            <option value="price_low" <?php if ($currentSort == 'price_low') echo 'selected'; ?>>Price: Low to High</option>
            <option value="price_high" <?php if ($currentSort == 'price_high') echo 'selected'; ?>>Price: High to Low</option>
            <option value="name_asc" <?php if ($currentSort == 'name_asc') echo 'selected'; ?>>Name: A to Z</option>
            <option value="name_desc" <?php if ($currentSort == 'name_desc') echo 'selected'; ?>>Name: Z to A</option>
          </select>
        </div>
      </div>
      -->

      <!-- Filter Actions -->
      <div class="filter-actions">
        <button type="submit" class="filter-btn filter-btn-primary">
          Apply
        </button>
        <button type="button" class="filter-btn filter-btn-secondary" id="clearAllFilters">
          Clear All
        </button>
      </div>
    </form>
  </div>
</div>

<style>
/* Sidebar Filter Styles */
.sidebar-filter-container {
  position: relative;
}

.filter-toggle-btn {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 10px 16px;
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
  box-shadow: 0 2px 10px rgba(0,0,0,0.1);
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
  background: #99d052;
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
  background: #6c757d;
  color: white;
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
    top: 0;
    left: -100%;
    width: 100%;
    height: 100vh;
    z-index: 1050;
    border-radius: 0;
    transition: left 0.3s ease;
    overflow-y: auto;
  }

  .sidebar-filter-panel.show {
    left: 0;
  }

  .sidebar-filter-header {
    position: sticky;
    top: 0;
    background: white;
    z-index: 1;
  }
}

  @media (min-width: 992px) {
    .filter-toggle-btn {
      display: none;
    }
    
    .filter-close-btn {
      display: none;
    }
  }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
  // Mobile filter toggle
  const filterToggle = document.getElementById('sidebarFilterToggle');
  const filterPanel = document.getElementById('sidebarFilterPanel');
  const filterClose = document.getElementById('sidebarFilterClose');

  if (filterToggle && filterPanel) {
    filterToggle.addEventListener('click', function() {
      filterPanel.classList.add('show');
      document.body.style.overflow = 'hidden';
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
});
</script> 