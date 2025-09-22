<?php
$pageTitle = 'All Categories';
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

<style>
/* Mobile Filter Button - Always Visible on Mobile */
.mobile-filter-button-container {
    position: relative;
    z-index: 9999;
    display: none;
    animation: float 3s ease-in-out infinite;
}

@keyframes float {
    0% { transform: translateY(0px); }
    50% { transform: translateY(-5px); }
    100% { transform: translateY(0px); }
}

/* Show mobile filter button only on mobile */
@media (max-width: 991px) {
    .mobile-filter-button-container {
        display: block !important;
    }
}

.mobile-filter-btn {
    background: var(--dark-green);
    color: white;
    border: none;
    padding: 14px 22px;
    border-radius: 25px;
    font-weight: 600;
    font-size: 14px;
    box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
    display: flex;
    align-items: center;
    gap: 10px;
    cursor: pointer;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.mobile-filter-btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
    transition: left 0.5s;
}

.mobile-filter-btn:hover::before {
    left: 100%;
}

.mobile-filter-btn:hover {
    background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.6);
}

.mobile-filter-btn:active {
    transform: translateY(-1px);
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.5);
}

.mobile-filter-btn .filter-icon-css {
    width: 18px;
    height: 14px;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 16'%3E%3Cg fill='white'%3E%3Crect x='2' y='4' width='16' height='2' rx='1'/%3E%3Crect x='2' y='10' width='16' height='2' rx='1'/%3E%3Crect x='9' y='1' width='2' height='6' rx='1'/%3E%3Crect x='9' y='7' width='2' height='6' rx='1'/%3E%3C/g%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-size: contain;
    background-position: center;
}

@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.1); }
    100% { transform: scale(1); }
}

/* Categories Page Styles */
.categories-container {
    padding: 20px 0 10px 0;
}

.categories-header {
    margin-bottom: 10px;
    text-align: center;
}

.categories-title {
    color: var(--dark-grey);
    font-weight: 700;
    font-size: 2.5rem;
    margin-bottom: 10px;
}

.results-count {
    color: var(--site-blue);
    font-weight: 500;
    font-size: 1.1rem;
}

.categories-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 16px;
    margin-bottom: 20px;
    width: 100%;
}

.category-card {
    background: #ffffff;
    border-radius: 8px;
    padding: 0;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
    border: 2px solid #9fbe1b;
    height: 100%;
    overflow: hidden;
    min-width: 0;
}

.category-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 20px rgba(0,0,0,0.15);
    border-color: #9fbe1b;
}

.category-info {
    display: flex;
    flex-direction: column;
    height: 100%;
    min-width: 0;
}

.category-image {
    width: 100%;
    height: 140px;
    overflow: hidden;
    border-radius: 6px 6px 0 0;
    background-color: #f8f9fa;
    display: flex;
    align-items: center;
    justify-content: center;
}

.category-image img {
    width: 100%;
    height: 100%;
    object-fit: contain;
    display: block;
    border-radius: 0;
    background-color: #f8f9fa;
}

.category-placeholder {
    width: 100%;
    height: 100%;
    background: var(--light-green);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--dark-grey);
    font-size: 2rem;
    border-radius: 6px 6px 0 0;
}

.category-details {
    flex: 1;
    display: flex;
    flex-direction: column;
    padding: 10px;
    background-color: #fff;
}

.category-name {
    font-size: 0.9rem;
    font-weight: 600;
    color: var(--dark-green);
    margin-bottom: 6px;
    text-align: center;
}

.category-name a {
    text-decoration: none;
    color: var(--dark-green);
    transition: color 0.3s ease;
}

.category-name a:hover {
    color: var(--light-green);
    opacity: 0.8;
}

.category-count {
    color: var(--dark-grey);
    font-weight: 500;
    text-align: center;
    margin-bottom: 10px;
    font-size: 0.8rem;
}

.category-count i {
    margin-right: 5px;
}

.category-actions {
    margin-top: auto;
    text-align: center;
    padding-bottom: 0;
}

.view-category-btn {
    background: var(--dark-green);
    color: #fff;
    border: 2px solid var(--light-green);
    padding: 5px 10px;
    border-radius: 6px;
    font-weight: 600;
    transition: all 0.3s ease;
    font-size: 0.8rem;
}

.view-category-btn:hover {
    background: transparent;
    color: #fff;
    border-color: var(--light-green);
    transform: translateY(-2px);
}

.no-categories {
    grid-column: 1 / -1;
    text-align: center;
    padding: 60px 20px;
}

.no-categories-icon {
    font-size: 4rem;
    color: var(--medium-gray);
    margin-bottom: 20px;
}

.no-categories h3 {
    color: var(--dark-grey);
    margin-bottom: 10px;
}

.no-categories p {
    color: var(--dark-gray);
    margin-bottom: 20px;
}

/* Sidebar Filter Styles */
.sidebar-filter-container {
    position: sticky;
    top: 20px;
    margin-top: 20px;
}

.sidebar-filter-panel {
    background: white;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    border: 1px solid #e0e0e0;
}

.sidebar-filter-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 1px solid #e0e0e0;
}

.sidebar-filter-header h4 {
    margin: 0;
    color: var(--dark-grey);
    font-weight: 600;
}

.filter-section {
    margin-bottom: 25px;
}

.filter-section h5 {
    color: var(--dark-grey);
    font-weight: 600;
    margin-bottom: 12px;
    font-size: 1rem;
}

.filter-actions {
    display: flex;
    flex-direction: column;
    gap: 10px;
    margin-top: 30px;
}

.filter-apply-btn,
.filter-clear-btn {
    width: 100%;
    padding: 12px;
    border-radius: 8px;
    font-weight: 500;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    text-decoration: none;
    background: var(--dark-green) !important;
    border-radius: 4px !important;
    color: white !important;
}

/* Mobile Filter Panel */
.mobile-filter-panel {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    z-index: 10000;
}

.mobile-filter-panel.show {
    display: block;
}

.mobile-filter-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.5);
}

.mobile-filter-content {
    position: absolute;
    top: 0;
    left: 0;
    width: 80%;
    max-width: 350px;
    height: 100%;
    background: white;
    overflow-y: auto;
    padding: 20px;
    box-shadow: 2px 0 10px rgba(0,0,0,0.3);
}

.mobile-filter-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 1px solid #e0e0e0;
}

.mobile-filter-header h4 {
    margin: 0;
    color: var(--dark-grey);
    font-weight: 600;
}

.mobile-filter-close {
    background: none;
    border: none;
    font-size: 1.5rem;
    color: var(--dark-gray);
    cursor: pointer;
    padding: 0;
    width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.mobile-filter-form {
    padding: 0;
}

/* Desktop: 4 cards per row */
@media (min-width: 1200px) {
    .categories-grid {
        grid-template-columns: repeat(4, 1fr);
    }
    
    /* Hide mobile filter button on desktop */
    .mobile-filter-button-container {
        display: none !important;
    }
}

/* Force 2 columns on mobile */
@media (max-width: 767px) {
    .categories-grid {
        grid-template-columns: repeat(2, 1fr) !important;
        gap: 10px !important;
    }
}

/* Tablets: 3 cards per row */
@media (max-width: 1199px) and (min-width: 768px) {
    .categories-grid {
        grid-template-columns: repeat(3, 1fr);
        gap: 12px;
    }
    
    /* Hide mobile filter button on tablets */
    .mobile-filter-button-container {
        display: none !important;
    }
}

/* Mobile: 2 cards per row */
@media (max-width: 767px) {
    .categories-grid {
        grid-template-columns: repeat(2, 1fr) !important;
        gap: 10px;
        padding: 0 10px;
    }
    
    .category-image {
        height: 100px;
    }
    
    .category-details {
        padding: 6px;
    }
    
    .category-name {
        font-size: 0.8rem;
        line-height: 1.2;
    }
    
    .category-count {
        font-size: 0.7rem;
        margin-bottom: 8px;
    }
    
    .view-category-btn {
        padding: 3px 6px;
        font-size: 0.7rem;
        white-space: nowrap;
    }
    
    /* Show mobile filter button */
    .mobile-filter-button-container {
        display: block !important;
    }
    
    /* Add top margin to prevent overlap with filter button */
    .categories-container {
        margin-top: 10px;
        padding-left: 0;
        padding-right: 0;
    }
    
    /* Ensure proper container spacing */
    .container-fluid {
        padding-left: 10px;
        padding-right: 10px;
    }

    .category-card {
        max-width: 200px !important;
    }
}

/* Small mobile: 2 cards per row with smaller sizes */
@media (max-width: 480px) {
    .categories-grid {
        grid-template-columns: repeat(2, 1fr) !important;
        gap: 6px;
        padding: 0 5px;
    }
    
    .category-image {
        height: 80px;
    }
    
    .category-details {
        padding: 4px;
    }
    
    .category-name {
        font-size: 0.7rem;
        line-height: 1.1;
    }
    
    .category-count {
        font-size: 0.6rem;
        margin-bottom: 6px;
    }
    
    .view-category-btn {
        padding: 2px 4px;
        font-size: 0.6rem;
        white-space: nowrap;
    }
    
    /* Show mobile filter button */
    .mobile-filter-button-container {
        display: block !important;
    }
    
    /* Smaller filter button on very small screens */
    .mobile-filter-btn {
        padding: 12px 18px;
        font-size: 12px;
        border-radius: 20px;
    }
    
    .mobile-filter-btn span {
        display: inline; /* Keep text visible */
        font-size: 11px;
    }
    
    .mobile-filter-btn i {
        font-size: 14px;
    }
    
    /* Add top margin to prevent overlap with filter button */
    .categories-container {
        margin-top: 10px;
        padding-left: 0;
        padding-right: 0;
    }
    
    /* Ensure proper container spacing */
    .container-fluid {
        padding-left: 5px;
        padding-right: 5px;
    }
}
</style>

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
