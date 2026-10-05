<?php
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/top_deals.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}
if (!canAccess('view_products')) {
    http_response_code(403);
    exit('Access denied.');
}

$pageTitle = 'Top Deals of the Week';
$_SESSION['top_deals_csrf'] = $_SESSION['top_deals_csrf'] ?? bin2hex(random_bytes(32));
$error = '';
$success = '';
$categories = getTopDealCategories($pdo, false);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (!hash_equals($_SESSION['top_deals_csrf'], (string)($_POST['csrf'] ?? ''))) {
            throw new RuntimeException('Your session expired. Refresh the page and try again.');
        }
        if (!ensureTopDealCategoryOrderSchema($pdo)) {
            throw new RuntimeException('Top Deals setup is not available. Check database permissions and reload this page.');
        }

        $allowedIds = array_map(static fn($row) => (int)$row['id'], $categories);
        $rawOrder = $_POST['category_order'] ?? [];
        $rawVisibleIds = $_POST['visible_category_ids'] ?? [];
        $rawOfferTexts = $_POST['offer_text'] ?? [];
        if (!is_array($rawOrder) || !is_array($rawVisibleIds) || !is_array($rawOfferTexts)) {
            throw new RuntimeException('The category selection is invalid. Refresh the page and try again.');
        }
        $submittedIds = array_map('intval', $rawOrder);
        $visibleIds = array_map('intval', $rawVisibleIds);
        if (count($submittedIds) !== count(array_unique($submittedIds))
            || count($submittedIds) !== count($allowedIds)
            || array_diff($submittedIds, $allowedIds)
            || array_diff($allowedIds, $submittedIds)
            || count($visibleIds) !== count(array_unique($visibleIds))
            || array_diff($visibleIds, $allowedIds)) {
            throw new RuntimeException('The category order changed. Refresh the page and try again.');
        }

        $pdo->beginTransaction();
        $saveOrder = $pdo->prepare('INSERT INTO top_deal_category_order (category_id, sort_order, is_visible, offer_text) VALUES (?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE sort_order = VALUES(sort_order), is_visible = VALUES(is_visible), offer_text = VALUES(offer_text)');
        foreach ($submittedIds as $position => $categoryId) {
            $offerText = trim((string)($rawOfferTexts[$categoryId] ?? ''));
            $offerText = $offerText === '' ? '30-20% OFF' : mb_substr($offerText, 0, 100, 'UTF-8');
            $saveOrder->execute([$categoryId, $position + 1, in_array($categoryId, $visibleIds, true) ? 1 : 0, $offerText]);
        }
        $pdo->commit();
        $success = 'Top Deals subcategory order saved.';
        $categories = getTopDealCategories($pdo, false);
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        $error = $e instanceof RuntimeException ? $e->getMessage() : 'Unable to save the Top Deals order. Please try again.';
        if (!($e instanceof RuntimeException)) {
            error_log('Top Deals order save failed: ' . $e->getMessage());
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8'); ?> - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="assets/css/admin.css" rel="stylesheet">
    <style>
        .top-deal-order-list { display: grid; gap: 10px; padding: 0; margin: 0; list-style: none; min-height: 8px; }
        [hidden] { display: none !important; }
        .top-deal-order-row { display: grid; grid-template-columns: 36px 76px minmax(0, 1fr) minmax(150px, 210px) auto; align-items: center; gap: 14px; padding: 10px 14px; border: 1px solid #dfe4ea; border-radius: 9px; background: #fff; }
        .top-deal-order-row.is-dragging { opacity: .45; }
        .top-deal-drag-handle { color: #788596; cursor: grab; text-align: center; }
        .top-deal-order-image { width: 72px; height: 60px; object-fit: contain; border: 1px solid #edf0f3; border-radius: 6px; background: #fff; }
        .top-deal-order-count { color: #586575; }
        .top-deal-offer-field label { display: block; margin-bottom: 3px; color: #586575; font-size: .75rem; font-weight: 600; }
        .top-deal-category-picker { max-height: 360px; overflow: auto; border: 1px solid #dee2e6; border-radius: 8px; padding: 12px; }
        .top-deal-category-option { display: flex; align-items: center; gap: 10px; padding: 7px 4px; }
        .top-deal-group-title { font-size: 1rem; font-weight: 600; margin: 20px 0 10px; }
        .top-deal-empty-list { color: #6c757d; padding: 12px 0; }
        @media (max-width: 575px) { .top-deal-order-row { grid-template-columns: 26px 58px minmax(0,1fr); gap: 9px; padding: 9px; } .top-deal-order-image { width: 56px; height: 52px; } .top-deal-order-count, .top-deal-offer-field { grid-column: 3; } }
    </style>
</head>
<body>
<div class="everythingb2c-admin-container">
    <?php include 'includes/sidebar.php'; ?>
    <div class="everythingb2c-main-content">
        <?php include 'includes/header.php'; ?>
        <main class="container-fluid p-3 p-lg-4">
            <?php if ($error): ?><div class="alert alert-danger" role="alert"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div><?php endif; ?>
            <?php if ($success): ?><div class="alert alert-success" role="status"><?php echo htmlspecialchars($success, ENT_QUOTES, 'UTF-8'); ?></div><?php endif; ?>
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3">
                <div><h1 class="h3 mb-1">Top Deals of the Week</h1><p class="text-muted mb-0">Choose subcategories, set their offer text, and drag them into homepage order.</p></div>
                <div class="d-flex flex-wrap gap-2">
                    <a class="btn btn-outline-primary" href="products.php"><i class="fas fa-box me-1"></i>Manage products</a>
                    <?php if (!empty($categories)): ?><button class="btn btn-primary" type="submit" form="topDealOrderForm"><i class="fas fa-save me-1"></i>Save Display Order</button><?php endif; ?>
                </div>
            </div>
            <div class="card shadow-sm"><div class="card-body">
                <?php if (empty($categories)): ?>
                    <div class="text-center py-5">
                        <h2 class="h5">No subcategories available</h2>
                        <p class="text-muted">Add subcategories in Categories. They will be available to choose here.</p>
                        <a class="btn btn-primary" href="categories.php">Open categories</a>
                    </div>
                <?php else: ?>
                    <form method="post" id="topDealOrderForm">
                        <input type="hidden" name="csrf" value="<?php echo htmlspecialchars($_SESSION['top_deals_csrf'], ENT_QUOTES, 'UTF-8'); ?>">
                        <div class="d-flex flex-wrap align-items-center gap-3 my-3">
                            <button type="button" class="btn btn-outline-primary" id="toggleTopDealCategoryPicker" aria-expanded="false" aria-controls="topDealCategoryPicker"><i class="fas fa-list-check me-1"></i>Select Categories</button>
                            <span class="text-muted" id="topDealSelectionSummary"></span>
                        </div>
                        <div class="top-deal-category-picker mb-4" id="topDealCategoryPicker" hidden>
                            <label class="form-label" for="topDealCategorySearch">Choose subcategories to show on the homepage</label>
                            <input class="form-control mb-2" id="topDealCategorySearch" type="search" placeholder="Search subcategories...">
                            <?php foreach ($categories as $category): ?>
                                <label class="top-deal-category-option" data-category-search="<?php echo htmlspecialchars(mb_strtolower($category['name'], 'UTF-8'), ENT_QUOTES, 'UTF-8'); ?>">
                                    <input class="form-check-input m-0" type="checkbox" name="visible_category_ids[]" value="<?php echo (int)$category['id']; ?>" <?php echo (int)$category['is_visible'] === 1 ? 'checked' : ''; ?>>
                                    <span><?php echo htmlspecialchars($category['name'], ENT_QUOTES, 'UTF-8'); ?></span>
                                    <span class="text-muted small">(<?php echo (int)$category['product_count']; ?> selected products)</span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                        <h2 class="top-deal-group-title">Selected for homepage <span class="text-muted fw-normal" id="topDealSelectedCount"></span></h2>
                        <ul class="top-deal-order-list" id="topDealSelectedList" aria-label="Selected subcategories">
                            <?php foreach ($categories as $category): ?>
                                <?php if ((int)$category['is_visible'] !== 1) continue; ?>
                                <?php $image = trim((string)($category['image'] ?? '')); $image = $image !== '' ? '../' . ltrim($image, './\\') : ''; ?>
                                <li class="top-deal-order-row" draggable="true" data-category-id="<?php echo (int)$category['id']; ?>">
                                    <span class="top-deal-drag-handle" aria-label="Drag to reorder" title="Drag to reorder"><i class="fas fa-grip-vertical"></i></span>
                                    <?php if ($image !== ''): ?><img class="top-deal-order-image" src="<?php echo htmlspecialchars($image, ENT_QUOTES, 'UTF-8'); ?>" alt="" loading="lazy" onerror="this.onerror=null;this.src='../uploads/products/blank-img.webp'"><?php else: ?><span class="top-deal-order-image d-flex align-items-center justify-content-center text-muted"><i class="fas fa-image"></i></span><?php endif; ?>
                                    <span class="fw-semibold"><?php echo htmlspecialchars($category['name'], ENT_QUOTES, 'UTF-8'); ?></span>
                                    <span class="top-deal-offer-field"><label for="offer-text-<?php echo (int)$category['id']; ?>">Offer text under image</label><input class="form-control form-control-sm" id="offer-text-<?php echo (int)$category['id']; ?>" name="offer_text[<?php echo (int)$category['id']; ?>]" value="<?php echo htmlspecialchars($category['offer_text'], ENT_QUOTES, 'UTF-8'); ?>" maxlength="100"></span>
                                    <span class="top-deal-order-count"><?php echo (int)$category['product_count']; ?> selected product<?php echo (int)$category['product_count'] === 1 ? '' : 's'; ?></span>
                                    <input type="hidden" name="category_order[]" value="<?php echo (int)$category['id']; ?>">
                                </li>
                            <?php endforeach; ?>
                        </ul>
                        <ul class="top-deal-order-list" id="topDealUnselectedList" aria-label="Unselected subcategories" hidden>
                            <?php foreach ($categories as $category): ?>
                                <?php if ((int)$category['is_visible'] === 1) continue; ?>
                                <?php $image = trim((string)($category['image'] ?? '')); $image = $image !== '' ? '../' . ltrim($image, './\\') : ''; ?>
                                <li class="top-deal-order-row" draggable="true" data-category-id="<?php echo (int)$category['id']; ?>">
                                    <span class="top-deal-drag-handle" aria-label="Drag to reorder" title="Drag to reorder"><i class="fas fa-grip-vertical"></i></span>
                                    <?php if ($image !== ''): ?><img class="top-deal-order-image" src="<?php echo htmlspecialchars($image, ENT_QUOTES, 'UTF-8'); ?>" alt="" loading="lazy" onerror="this.onerror=null;this.src='../uploads/products/blank-img.webp'"><?php else: ?><span class="top-deal-order-image d-flex align-items-center justify-content-center text-muted"><i class="fas fa-image"></i></span><?php endif; ?>
                                    <span class="fw-semibold"><?php echo htmlspecialchars($category['name'], ENT_QUOTES, 'UTF-8'); ?></span>
                                    <span class="top-deal-offer-field"><label for="offer-text-<?php echo (int)$category['id']; ?>">Offer text under image</label><input class="form-control form-control-sm" id="offer-text-<?php echo (int)$category['id']; ?>" name="offer_text[<?php echo (int)$category['id']; ?>]" value="<?php echo htmlspecialchars($category['offer_text'], ENT_QUOTES, 'UTF-8'); ?>" maxlength="100"></span>
                                    <span class="top-deal-order-count"><?php echo (int)$category['product_count']; ?> selected product<?php echo (int)$category['product_count'] === 1 ? '' : 's'; ?></span>
                                    <input type="hidden" name="category_order[]" value="<?php echo (int)$category['id']; ?>">
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </form>
                    <p class="small text-muted mt-3 mb-0">Only selected subcategories with active Top Deals products appear on the homepage. Newly added subcategories start unselected. Drag selected rows to set their display order, then save.</p>
                <?php endif; ?>
            </div></div>
        </main>
    </div>
</div>
<script>
(() => {
    const selectedList = document.getElementById('topDealSelectedList');
    const unselectedList = document.getElementById('topDealUnselectedList');
    const picker = document.getElementById('topDealCategoryPicker');
    if (!selectedList || !unselectedList || !picker) return;
    let dragged = null;
    const rows = () => [...selectedList.children, ...unselectedList.children];
    const checkboxes = [...picker.querySelectorAll('input[name="visible_category_ids[]"]')];
    const updateSummary = () => {
        const selected = checkboxes.filter(input => input.checked).length;
        document.getElementById('topDealSelectionSummary').textContent = `${selected} selected`;
        document.getElementById('topDealSelectedCount').textContent = `(${selected})`;
        selectedList.querySelector('.top-deal-empty-list')?.remove();
        if (!selectedList.children.length) selectedList.innerHTML = '<li class="top-deal-empty-list">No subcategories selected yet.</li>';
    };
    const rowFor = id => rows().find(row => row.dataset.categoryId === String(id));
    checkboxes.forEach(input => input.addEventListener('change', () => {
        const row = rowFor(input.value);
        if (row) (input.checked ? selectedList : unselectedList).appendChild(row);
        updateSummary();
    }));
    document.getElementById('toggleTopDealCategoryPicker')?.addEventListener('click', event => {
        picker.hidden = !picker.hidden;
        event.currentTarget.setAttribute('aria-expanded', String(!picker.hidden));
    });
    document.getElementById('topDealCategorySearch')?.addEventListener('input', event => {
        const query = event.currentTarget.value.trim().toLocaleLowerCase();
        picker.querySelectorAll('.top-deal-category-option').forEach(option => {
            option.hidden = !option.dataset.categorySearch.includes(query);
        });
    });
    const orderLists = [selectedList, unselectedList];
    orderLists.forEach(list => list.addEventListener('dragstart', event => {
        const row = event.target.closest('.top-deal-order-row');
        if (!row) return;
        dragged = row;
        row.classList.add('is-dragging');
        event.dataTransfer.effectAllowed = 'move';
    }));
    orderLists.forEach(list => list.addEventListener('dragend', () => {
        if (dragged) dragged.classList.remove('is-dragging');
        dragged = null;
    }));
    orderLists.forEach(list => list.addEventListener('dragover', event => {
        event.preventDefault();
        const target = event.target.closest('.top-deal-order-row');
        if (!dragged || dragged.parentElement !== list || !target || target === dragged) return;
        const bounds = target.getBoundingClientRect();
        list.insertBefore(dragged, event.clientY < bounds.top + bounds.height / 2 ? target : target.nextSibling);
    }));
    updateSummary();
})();
</script>
<script src="assets/js/admin.js"></script>
</body>
</html>
