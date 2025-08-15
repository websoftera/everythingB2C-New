<?php
$pageTitle = 'All Categories';
require_once 'includes/header.php';
require_once 'includes/functions.php';

// Get all categories with product counts
$categories = getAllCategoriesWithProductCount();
$main_categories = array_filter($categories, function($cat) { return empty($cat['parent_id']); });

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
        <div class="col-lg-3 col-md-4">
            <div class="sidebar-filter-container">
                <!-- Mobile Filter Toggle Button -->
                <button class="filter-toggle-btn d-lg-none" id="sidebarFilterToggle">
                    <i class="bi bi-funnel"></i>
                    <span>Filters</span>
                </button>

                <!-- Sidebar Filter Panel -->
                <div class="sidebar-filter-panel" id="sidebarFilterPanel">
                    <div class="sidebar-filter-header">
                        <h4>Filters</h4>
                        <button class="filter-close-btn d-lg-none" id="sidebarFilterClose">
                            <i class="bi bi-x-lg"></i>
                        </button>
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
                                <i class="bi bi-search"></i> Apply Filters
                            </button>
                            <a href="categories.php" class="btn btn-outline-secondary filter-clear-btn">
                                <i class="bi bi-x-circle"></i> Clear All
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Categories Section -->
        <div class="col-lg-9 col-md-8">
            <div class="categories-container">
                <!-- Categories Header -->
                <div class="categories-header">
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
    grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
    gap: 16px;
    margin-bottom: 20px;
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
}

.category-image {
    width: 100%;
    height: 160px;
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
    padding: 12px;
    background-color: #9fbe1b;
}

.category-name {
    font-size: 1rem;
    font-weight: 600;
    color: #ffffff;
    margin-bottom: 6px;
    text-align: center;
}

.category-name a {
    text-decoration: none;
    color: inherit;
    transition: color 0.3s ease;
}

.category-name a:hover {
    color: #ffffff;
    opacity: 0.8;
}

.category-count {
    color: #ffffff;
    font-weight: 500;
    text-align: center;
    margin-bottom: 12px;
    font-size: 0.85rem;
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
    background: #ffffff;
    color: #9fbe1b;
    border: 2px solid #ffffff;
    padding: 6px 12px;
    border-radius: 6px;
    font-weight: 600;
    transition: all 0.3s ease;
    font-size: 0.85rem;
}

.view-category-btn:hover {
    background: transparent;
    color: #ffffff;
    border-color: #ffffff;
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

/* Sidebar Filter Styles (matching existing site) */
.sidebar-filter-container {
    position: sticky;
    top: 20px;
    margin-top: 20px;
}

.filter-toggle-btn {
    width: 100%;
    background: var(--site-blue);
    color: white;
    border: none;
    padding: 12px 20px;
    border-radius: 8px;
    font-weight: 500;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
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

.filter-close-btn {
    background: none;
    border: none;
    font-size: 1.2rem;
    color: var(--dark-gray);
    cursor: pointer;
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
}

/* Mobile Responsive */
@media (max-width: 991px) {
    .sidebar-filter-panel {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        z-index: 1050;
        border-radius: 0;
        overflow-y: auto;
    }
    
    .sidebar-filter-panel.show {
        display: block;
    }
}

@media (max-width: 768px) {
    .categories-title {
        font-size: 2rem;
    }
    
    .categories-grid {
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        gap: 12px;
    }
    
    .category-image {
        height: 140px;
    }
    
    .category-details {
        padding: 10px;
    }
    
    .category-name {
        font-size: 0.95rem;
    }
}

@media (max-width: 480px) {
    .categories-grid {
        grid-template-columns: 1fr;
        gap: 10px;
    }
    
    .category-image {
        height: 120px;
    }
    
    .category-details {
        padding: 8px;
    }
    
    .category-name {
        font-size: 0.9rem;
    }
    
    .view-category-btn {
        padding: 5px 10px;
        font-size: 0.8rem;
    }
}
</style>

<script>
// Mobile sidebar filter toggle functionality
document.addEventListener('DOMContentLoaded', function() {
    const filterToggle = document.getElementById('sidebarFilterToggle');
    const filterPanel = document.getElementById('sidebarFilterPanel');
    const filterClose = document.getElementById('sidebarFilterClose');

    if (filterToggle && filterPanel) {
        filterToggle.addEventListener('click', function() {
            filterPanel.classList.add('show');
        });
    }

    if (filterClose && filterPanel) {
        filterClose.addEventListener('click', function() {
            filterPanel.classList.remove('show');
        });
    }

    // Close filter panel when clicking outside
    document.addEventListener('click', function(e) {
        if (filterPanel && filterPanel.classList.contains('show')) {
            if (!filterPanel.contains(e.target) && !filterToggle.contains(e.target)) {
                filterPanel.classList.remove('show');
            }
        }
    });
});
</script>

<?php require_once 'includes/footer.php'; ?>
