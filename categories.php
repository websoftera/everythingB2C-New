<?php
$pageTitle = 'All Categories';
require_once 'includes/header.php';
require_once 'includes/functions.php';

// Reuse the header's active-product counts, including descendant assignments.
$all_categories_with_products = array_filter($categories, function ($cat) {
    return (int)($cat['product_count'] ?? 0) > 0;
});

// Get filter parameters for sidebar
$selectedCategory = isset($_GET['category']) ? $_GET['category'] : '';
$searchTerm = isset($_GET['q']) ? trim($_GET['q']) : '';

// By default, only show main parent categories (not subcategories)
if (empty($selectedCategory) && empty($searchTerm)) {
    $display_list = $categoryTree;
}
else {
    // If we're filtering by name or by a specific ID, we search against ALL categories including subcategories
    $display_list = $all_categories_with_products;

    // Filter categories based on search
    if (!empty($searchTerm)) {
        $display_list = array_filter($display_list, function ($cat) use ($searchTerm) {
            return stripos($cat['name'], $searchTerm) !== false;
        });
    }

    // Filter by selected category if specified
    if (!empty($selectedCategory)) {
        $display_list = array_filter($display_list, function ($cat) use ($selectedCategory) {
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
function buildCategoriesPaginationUrl($page, $params = [])
{
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
                    <?php
else: ?>
                        <?php foreach ($displayCategories as $category): ?>
                            <div class="card category-card" data-id="cat-<?php echo $category['id']; ?>">
                                <div class="category-info">
                                    <a class="category-image-link" href="category.php?slug=<?php echo urlencode($category['slug']); ?>">
                                        <div class="category-image">
                                            <?php if (!empty($category['image']) && file_exists('./' . $category['image'])): ?>
                                                <img src="./<?php echo $category['image']; ?>" alt="<?php echo $category['name']; ?>" />
                                            <?php
        else: ?>
                                                <div class="category-placeholder">
                                                    <i class="fas fa-box"></i>
                                                </div>
                                            <?php
        endif; ?>
                                        </div>
                                    </a>
                                    <div class="category-details">
                                        <h3 class="category-name"><a href="category.php?slug=<?php echo urlencode($category['slug']); ?>"><?php echo ucfirst($category['name']); ?></a></h3>
                                        <div class="category-count"><i class="bi bi-box"></i><?php echo (int)($category['product_count'] ?? 0); ?> product<?php echo (int)($category['product_count'] ?? 0) !== 1 ? 's' : ''; ?></div>
                                        <div class="category-actions"><a href="category.php?slug=<?php echo urlencode($category['slug']); ?>" class="btn view-category-btn"><i class="bi bi-eye"></i> View Products</a></div>
                                    </div>
                                </div>
                            </div>
                        <?php
    endforeach; ?>
                    <?php
endif; ?>
                </div>

                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                    <nav aria-label="Categories pagination">
                        <ul class="pagination justify-content-center">
                            <?php if ($currentPage > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="<?php echo buildCategoriesPaginationUrl($currentPage - 1, $_GET); ?>">Previous</a>
                                </li>
                            <?php
    endif; ?>

                            <?php for ($i = max(1, $currentPage - 2); $i <= min($totalPages, $currentPage + 2); $i++): ?>
                                <li class="page-item <?php echo $i == $currentPage ? 'active' : ''; ?>">
                                    <a class="page-link" href="<?php echo buildCategoriesPaginationUrl($i, $_GET); ?>"><?php echo $i; ?></a>
                                </li>
                            <?php
    endfor; ?>

                            <?php if ($currentPage < $totalPages): ?>
                                <li class="page-item">
                                    <a class="page-link" href="<?php echo buildCategoriesPaginationUrl($currentPage + 1, $_GET); ?>">Next</a>
                                </li>
                            <?php
    endif; ?>
                        </ul>
                    </nav>
                <?php
endif; ?>
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
    padding: 24px 0 12px;
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
    column-gap: 10px;
    row-gap: 20px;
    margin-bottom: 20px;
    width: 100%;
}

.category-card {
    background: #ffffff;
    border-radius: 9px;
    box-sizing: border-box;
    padding: 16px 32px 20px;
    padding-bottom: 20px !important;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    transition: all 0.3s ease;
    border: 1px solid #e0e0e0;
    height: 100%;
    overflow: hidden;
    min-width: 0;
    max-width: none !important;
    width: 100% !important;
    display: block;
    margin: 0 !important;
}

.category-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.12);
    border-color: var(--primary-color);
}

.category-info {
    display: flex;
    flex-direction: column;
    height: 100%;
    min-width: 0;
}

.category-image-link {
    display: block;
    text-decoration: none;
}

.category-image {
    width: 100%;
    aspect-ratio: 4 / 3;
    box-sizing: border-box;
    overflow: hidden;
    border-radius: 10px;
    background-color: transparent;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
}

.category-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.5s ease;
    display: block;
    border-radius: 10px;
}

.category-card:hover .category-image img {
    transform: scale(1.04);
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
    border-radius: 10px;
}

.category-details {
    flex: 1;
    display: flex;
    flex-direction: column;
    padding: 8px 0 0;
    background-color: #fff;
}

.category-name {
    font-size: 14px;
    font-weight: 600;
    color: #495057;
    margin: 0;
    text-align: center;
}

.category-name a {
    color: inherit;
    text-decoration: none;
}

.category-name a:hover {
    color: var(--primary-color);
}

.category-count {
    color: var(--dark-grey);
    font-size: 0.85rem;
    font-weight: 500;
    margin: 5px 0 10px;
    text-align: center;
}

.category-count i {
    margin-right: 5px;
}

.category-actions {
    margin-top: auto;
    text-align: center;
}

.view-category-btn {
    background: var(--dark-green);
    border: 1px solid var(--dark-green);
    border-radius: 6px;
    color: #fff;
    font-size: 0.8rem;
    font-weight: 600;
    padding: 5px 10px;
}

.view-category-btn:hover {
    background: var(--light-green);
    border-color: var(--light-green);
    color: #fff;
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

/* Desktop: five full-width cards per row with no unused column space. */
@media (min-width: 1200px) {
    .categories-grid {
        grid-template-columns: repeat(5, minmax(0, 1fr));
    }

    /* Hide mobile filter button on desktop */
    .mobile-filter-button-container {
        display: none !important;
    }
}

/* Mobile: 1 card per row */
@media (max-width: 767px) {
    .categories-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
        gap: 12px !important;
        padding: 0;
        padding-top:20px;
    }

    .category-card {
        max-width: 100% !important;
        border-radius: 9px;
        padding: 10px 10px 20px;
    }

    .category-image {
        aspect-ratio: 1 / 1;
        padding: 20px;
    }

    .category-image img {
        object-fit: cover;
    }

    .category-details {
        padding: 8px 0 0;
    }

    .category-name {
        font-size: 0.9rem;
        line-height: 1.25;
    }

    .category-count {
        font-size: 0.75rem;
        margin: 4px 0 8px;
    }

    .view-category-btn {
        font-size: 0.75rem;
        padding: 5px 8px;
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
