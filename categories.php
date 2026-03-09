<?php
$pageTitle = 'All Categories';
require_once 'includes/header.php';
require_once 'includes/functions.php';

// Get all categories with recursive product counts (including subcategories)
$all_categories_with_products = getAllCategoriesWithRecursiveProductCount();

// Get filter parameters for sidebar
$selectedCategory = isset($_GET['category']) ? $_GET['category'] : '';
$searchTerm = isset($_GET['q']) ? trim($_GET['q']) : '';

// By default, only show main parent categories (not subcategories)
if (empty($selectedCategory) && empty($searchTerm)) {
    $display_list = array_filter($all_categories_with_products, function($cat) { 
        return empty($cat['parent_id']); 
    });
} else {
    // If we're filtering by name or by a specific ID, we search against ALL categories including subcategories
    $display_list = $all_categories_with_products;
    
    // Filter categories based on search
    if (!empty($searchTerm)) {
        $display_list = array_filter($display_list, function($cat) use ($searchTerm) {
            return stripos($cat['name'], $searchTerm) !== false;
        });
    }

    // Filter by selected category if specified
    if (!empty($selectedCategory)) {
        $display_list = array_filter($display_list, function($cat) use ($selectedCategory) {
            return $cat['id'] == $selectedCategory;
        });
    }
}

// Pagination
$itemsPerPage = 12;
$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$totalCategories = count($display_list);
$totalPages = ceil($totalCategories / $itemsPerPage);
$offset = ($currentPage - 1) * $itemsPerPage;
$displayCategories = array_slice($display_list, $offset, $itemsPerPage);

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
<?php
$breadcrumbs = generateBreadcrumb($pageTitle);
echo renderBreadcrumb($breadcrumbs);
?>

<div class="container-fluid" style="padding-left: 5px !important; padding-right: 5px !important;">
    <div class="row">
        <!-- Top Filter (Desktop/Tablet) & Sidebar Filter (Mobile) -->
        <div class="col-12">
            <?php include 'includes/sidebar-filter.php'; ?>
        </div>

        <!-- Categories Section -->
        <div class="col-12">
            <div class="categories-container">
         <!-- Categories Header -->
                <div class="categories-header">
<!-- Top space preserved for card grid -->
       
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
    border-radius: 12px;
    padding: 0;
    box-shadow: 0 4px 15px rgba(0,0,0,0.06);
    transition: all 0.3s ease;
    border: 1px solid #9fbe1b;
    height: 100%;
    overflow: hidden;
    min-width: 0;
    display: flex;
    flex-direction: column;
}

.category-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.12);
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
    height: 180px;
    overflow: hidden;
    border-radius: 12px 12px 0 0;
    background-color: transparent;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px; /* Uniform boundary gap */
}

.category-image img {
    /*max-width: 100%;
    max-height: 100%;*/
    width: 200px;
    height: 160px;
    object-fit: contain;
    transition: transform 0.5s ease;
    display: block;
    background-color: transparent; /* Remove conflicting inner background */
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
    color: var(--dark-green);
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

/* Removed Sidebar Filter Styles - Now handled by includes/sidebar-filter.php */

/* Desktop: 4 cards per row */
@media (min-width: 1200px) {
    .categories-grid {
        grid-template-columns: repeat(5, 1fr);
    }
    
    /* Hide mobile filter button on desktop */
    .mobile-filter-button-container {
        display: none !important;
    }
}

/* Mobile: 1 card per row */
@media (max-width: 767px) {
    .categories-grid {
        grid-template-columns: 1fr !important;
        gap: 20px !important;
        padding: 0 15px;
    }
    
    .category-card {
        max-width: 100% !important;
        border-radius: 12px;
    }
    
    .category-image {
        height: 200px; /* Taller image block for single column */
        padding: 20px; /* Consistent inner padding */
        background: transparent;
    }
    
    .category-image img {
        object-fit: cover;
    }
    
    .category-details {
        padding: 15px;
    }
    
    .category-name {
        font-size: 1.1rem;
        line-height: 1.4;
    }
    
    .category-count {
        font-size: 0.9rem;
        margin-bottom: 15px;
    }
    
    .view-category-btn {
        padding: 10px 16px;
        font-size: 1rem;
        width: 100%; /* Make button full width on mobile */
    }

    /* Ensure proper container spacing */
    .container-fluid {
        padding-left: 15px;
        padding-right: 15px;
    }
}
</style>

<!-- Script removed as mobile filter logic is handled externally -->

<?php require_once 'includes/footer.php'; ?>
