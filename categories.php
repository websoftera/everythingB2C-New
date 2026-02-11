<?php
$pageTitle = 'All Categories';
$pageCss = ['asset/style/categories.css'];
require_once 'includes/header.php';
require_once 'includes/functions.php';

// Get all categories with recursive product counts (including subcategories)
$categories = getAllCategoriesWithRecursiveProductCount();
$main_categories = array_filter($categories, function($cat) { return empty($cat['parent_id']); });

// Debug: Let's verify the counting is working correctly
// Uncomment the following lines to debug category counts
/*
echo "<div style='background: #f0f0f0; padding: 10px; margin: 10px; border: 1px solid #ccc;'>";
echo "<h4>Debug: Category Product Counts</h4>";
foreach ($main_categories as $cat) {
    echo "<p><strong>{$cat['name']}</strong>: {$cat['product_count']} products</p>";
}
echo "</div>";
*/

// Get filter parameters for sidebar
$selectedCategory = isset($_GET['category']) ? $_GET['category'] : '';
$searchTerm = isset($_GET['q']) ? trim($_GET['q']) : '';

// Filter categories based on search
if (!empty($searchTerm)) {
    $main_categories = array_filter($main_categories, function($cat) use ($searchTerm) {
        return stripos($cat['name'], $searchTerm) !== false;
    });
}

// Filter by selected category if specified
if (!empty($selectedCategory)) {
    $main_categories = array_filter($main_categories, function($cat) use ($selectedCategory) {
        return $cat['id'] == $selectedCategory;
    });
}

// Pagination
$itemsPerPage = 12;
$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$totalCategories = count($main_categories);
$totalPages = ceil($totalCategories / $itemsPerPage);
$offset = ($currentPage - 1) * $itemsPerPage;
$displayCategories = array_slice($main_categories, $offset, $itemsPerPage);

// Helper function to build pagination URLs
function buildCategoriesPaginationUrl($page, $params = []) {
    $url = "?page=" . $page;
    
    if (isset($params['q']) && $params['q'] !== '') {
        $url .= "&q=" . urlencode($params['q']);
    }
    
    if (isset($params['category']) && $params['category'] !== '') {
        $url .= "&category=" . urlencode($params['category']);
    }
    
    return $url;
}
?>

<!-- Breadcrumb Navigation -->
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Categories</li>
                </ol>
            </nav>
        </div>
    </div>
</div>

<!-- Spacing after breadcrumb -->
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div style="height: 20px;"></div>
        </div>
    </div>
</div>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar Filter -->
        <div class="col-lg-3 col-md-4 d-none d-lg-block">
            <div class="sidebar-filter-container">
                <!-- Sidebar Filter Panel -->
                <div class="sidebar-filter-panel" id="sidebarFilterPanel">
                    <div class="sidebar-filter-header">
                        <h4>Filters</h4>
                    </div>

                    <form method="get" id="sidebarFilterForm" class="sidebar-filter-form">
                        <!-- Search Filter -->
                        <div class="filter-section">
                            <h5>Search Categories</h5>
                            <div class="form-group">
                                <input type="text" name="q" value="<?php echo htmlspecialchars($searchTerm); ?>" 
                                       placeholder="Search categories..." class="form-control">
                            </div>
                        </div>

                        <!-- Category Filter -->
                        <div class="filter-section">
                            <h5>Filter by Category</h5>
                            <div class="form-group">
                                <select name="category" class="form-control">
                                    <option value="">All Categories</option>
                                    <?php 
                                    $allCategories = getAllCategories();
                                    foreach ($allCategories as $cat) {
                                        if (empty($cat['parent_id'])) {
                                            echo '<option value="' . $cat['id'] . '"';
                                            if ($selectedCategory == $cat['id']) {
                                                echo ' selected';
                                            }
                                            echo '>' . htmlspecialchars($cat['name']) . '</option>';
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>

                        <!-- Filter Actions -->
                        <div class="filter-actions">
                            <button type="submit" class="btn btn-primary filter-apply-btn">
                                Apply
                            </button>
                            <a href="categories.php" class="btn btn-outline-secondary filter-clear-btn">
                                Clear All
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Categories Section -->
        <div class="col-lg-9 col-md-8 col-12">
            <!-- Mobile Filter Panel -->
            <div class="mobile-filter-panel d-lg-none" id="mobileFilterPanel">
                <div class="mobile-filter-overlay" id="mobileFilterOverlay"></div>
                <div class="mobile-filter-content">
                    <div class="mobile-filter-header">
                        <h4>Filters</h4>
                        <button class="mobile-filter-close" id="mobileFilterClose">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>

                    <form method="get" id="mobileFilterForm" class="mobile-filter-form">
                        <!-- Search Filter -->
                        <div class="filter-section">
                            <h5>Search Categories</h5>
                            <div class="form-group">
                                <input type="text" name="q" value="<?php echo htmlspecialchars($searchTerm); ?>" 
                                       placeholder="Search categories..." class="form-control">
                            </div>
                        </div>

                        <!-- Category Filter -->
                        <div class="filter-section">
                            <h5>Filter by Category</h5>
                            <div class="form-group">
                                <select name="category" class="form-control">
                                    <option value="">All Categories</option>
                                    <?php 
                                    $allCategories = getAllCategories();
                                    foreach ($allCategories as $cat) {
                                        if (empty($cat['parent_id'])) {
                                            echo '<option value="' . $cat['id'] . '"';
                                            if ($selectedCategory == $cat['id']) {
                                                echo ' selected';
                                            }
                                            echo '>' . htmlspecialchars($cat['name']) . '</option>';
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>

                        <!-- Filter Actions -->
                        <div class="filter-actions">
                            <button type="submit" class="btn btn-primary filter-apply-btn">
                                Apply
                            </button>
                            <a href="categories.php" class="btn btn-outline-secondary filter-clear-btn">
                                Clear All
                            </a>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="categories-container">
         <!-- Categories Header -->
                <div class="categories-header">
                                    <!-- Mobile Filter Button - Always Visible on Mobile -->
<div class="mobile-filter-button-container d-lg-none">
    <button class="mobile-filter-btn" id="mobileFilterBtn">
        <span class="filter-icon-css"></span>
        <span>Filter</span>
    </button>
</div>
       
                </div>

                <!-- Categories Grid -->
                <div class="categories-grid">
                    <?php if (empty($displayCategories)): ?>
                        <div class="no-categories">
                            <div class="no-categories-icon">
                                <i class="bi bi-folder-x"></i>
                            </div>
                            <h3>No categories found</h3>
                            <p>Try adjusting your search terms or filters.</p>
                            <a href="categories.php" class="filter-clear-btn">Clear All Filters</a>
                        </div>
                    <?php else: ?>
                        <?php foreach ($displayCategories as $category): ?>
                            <div class="card category-card" data-id="cat-<?php echo $category['id']; ?>">
                                <div class="category-info">
                                    <div class="category-image">
                                        <a href="category.php?slug=<?php echo $category['slug']; ?>">
                                            <?php if (!empty($category['image']) && file_exists('./' . $category['image'])): ?>
                                                <img src="./<?php echo $category['image']; ?>" alt="<?php echo $category['name']; ?>" />
                                            <?php else: ?>
                                                <div class="category-placeholder">
                                                    <i class="fas fa-box"></i>
                                                </div>
                                            <?php endif; ?>
                                        </a>
                                    </div>
                                    <div class="category-details">
                                        <h3 class="category-name">
                                            <a href="category.php?slug=<?php echo $category['slug']; ?>">
                                                <?php echo ucfirst($category['name']); ?>
                                            </a>
                                        </h3>
                                        <?php if (isset($category['product_count']) && $category['product_count'] > 0): ?>
                                            <div class="category-count">
                                                <i class="bi bi-box"></i>
                                                <?php echo $category['product_count']; ?> product<?php echo $category['product_count'] != 1 ? 's' : ''; ?>
                                            </div>
                                        <?php endif; ?>
                                        <div class="category-actions">
                                            <a href="category.php?slug=<?php echo $category['slug']; ?>" class="btn btn-primary view-category-btn">
                                                <i class="bi bi-eye"></i> View Products
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                    <nav aria-label="Categories pagination">
                        <ul class="pagination justify-content-center">
                            <?php if ($currentPage > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="<?php echo buildCategoriesPaginationUrl($currentPage - 1, $_GET); ?>">Previous</a>
                                </li>
                            <?php endif; ?>
                            
                            <?php for ($i = max(1, $currentPage - 2); $i <= min($totalPages, $currentPage + 2); $i++): ?>
                                <li class="page-item <?php echo $i == $currentPage ? 'active' : ''; ?>">
                                    <a class="page-link" href="<?php echo buildCategoriesPaginationUrl($i, $_GET); ?>"><?php echo $i; ?></a>
                                </li>
                            <?php endfor; ?>
                            
                            <?php if ($currentPage < $totalPages): ?>
                                <li class="page-item">
                                    <a class="page-link" href="<?php echo buildCategoriesPaginationUrl($currentPage + 1, $_GET); ?>">Next</a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>


<script>
// Mobile filter functionality
document.addEventListener('DOMContentLoaded', function() {
    const mobileFilterBtn = document.getElementById('mobileFilterBtn');
    const mobileFilterPanel = document.getElementById('mobileFilterPanel');
    const mobileFilterClose = document.getElementById('mobileFilterClose');
    const mobileFilterOverlay = document.getElementById('mobileFilterOverlay');

    // Function to open mobile filter panel
    function openMobileFilterPanel() {
        if (mobileFilterPanel) {
            mobileFilterPanel.classList.add('show');
            document.body.style.overflow = 'hidden'; // Prevent background scrolling
        }
    }

    // Function to close mobile filter panel
    function closeMobileFilterPanel() {
        if (mobileFilterPanel) {
            mobileFilterPanel.classList.remove('show');
            document.body.style.overflow = ''; // Restore scrolling
        }
    }

    // Event listeners
    if (mobileFilterBtn) {
        mobileFilterBtn.addEventListener('click', openMobileFilterPanel);
    }

    if (mobileFilterClose) {
        mobileFilterClose.addEventListener('click', closeMobileFilterPanel);
    }

    if (mobileFilterOverlay) {
        mobileFilterOverlay.addEventListener('click', closeMobileFilterPanel);
    }

    // Close mobile filter panel on escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && mobileFilterPanel && mobileFilterPanel.classList.contains('show')) {
            closeMobileFilterPanel();
        }
    });
});
</script>

<?php require_once 'includes/footer.php'; ?>
