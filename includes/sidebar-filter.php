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
                  <span class="currency-symbol">â‚¹</span>
                  <input type="number" id="minPriceInput" class="price-input" 
                         min="<?php echo $siteMinPrice; ?>" max="<?php echo $siteMaxPrice; ?>" 
                         value="<?php echo $currentMinPrice; ?>" step="10">
                </div>
              </div>
              <div class="price-input-group">
                <label>Max</label>
                <div class="price-input-wrapper">
                  <span class="currency-symbol">â‚¹</span>
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
            <span class="range-label">Range: â‚¹<?php echo number_format($siteMinPrice); ?> - â‚¹<?php echo number_format($siteMaxPrice); ?></span>
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
