<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'includes/functions.php';
require_once 'includes/product_reviews.php';

// Get product slug from URL
$slug = $_GET['slug'] ?? '';

if (empty($slug)) {
    header('Location: index.php');
    exit;
}

// Get product details
$product = getProductBySlug($slug);

if (!$product) {
    header('Location: index.php');
    exit;
}

// Deployments on this host do not have a post-deploy command hook. Apply the
// idempotent review migration on the first product request after deployment.
$reviewsAvailable = ensureProductReviewsSchema($pdo);

$reviewNotice = $_SESSION['product_review_notice'] ?? '';
unset($_SESSION['product_review_notice']);
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST' && ($_POST['action'] ?? '') === 'submit_product_review') {
    try {
        if (!isLoggedIn()) {
            $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'] . '#write-product-review';
            header('Location: login.php');
            exit;
        }
        if (!verifyProductReviewCsrfToken($_POST['review_csrf'] ?? null)) {
            throw new RuntimeException('Your session expired. Refresh the page and try again.');
        }
        if (!$reviewsAvailable) {
            throw new RuntimeException('Reviews are temporarily unavailable while the review system is being set up. Please try again later.');
        }
        $customerId = (int)$_SESSION['user_id'];
        $customerCheck = $pdo->prepare("SELECT 1 FROM users WHERE id = ? AND user_role = 'customer' AND is_active = 1 LIMIT 1");
        $customerCheck->execute([$customerId]);
        if (!$customerCheck->fetchColumn()) {
            throw new RuntimeException('Please sign in with a customer account to submit a review.');
        }
        $existingReview = $pdo->prepare('SELECT id FROM product_reviews WHERE product_id = ? AND customer_id = ? LIMIT 1');
        $existingReview->execute([(int)$product['id'], $customerId]);
        if ($existingReview->fetchColumn()) {
            throw new RuntimeException('You have already submitted a review for this product.');
        }

        saveAdminProductReview($pdo, [
            'product_id' => (int)$product['id'],
            'customer_id' => $customerId,
            'rating' => $_POST['rating'] ?? null,
            'review_title' => $_POST['review_title'] ?? '',
            'review_content' => $_POST['review_content'] ?? '',
            'seller_code' => '',
            'is_verified' => 0,
            'status' => 'pending',
        ]);
        $_SESSION['product_review_notice'] = 'Thank you. Your review was submitted and is awaiting approval.';
    } catch (Throwable $e) {
        $_SESSION['product_review_notice'] = $e instanceof InvalidArgumentException || $e instanceof RuntimeException
            ? $e->getMessage()
            : 'Unable to submit your review right now. Please try again.';
        if (!($e instanceof InvalidArgumentException || $e instanceof RuntimeException)) {
            error_log('Customer product review submission failed: ' . $e->getMessage());
        }
    }
    header('Location: product.php?slug=' . rawurlencode((string)$product['slug']) . '#write-product-review');
    exit;
}

$reviewSummary = $reviewsAvailable
    ? getPublicProductReviewSummary($pdo, (int)$product['id'])
    : ['total_reviews' => 0, 'average_rating' => 0.0];
$productReviews = $reviewsAvailable ? getPublicProductReviews($pdo, (int)$product['id'], 20) : [];
$customerReview = null;
$canSubmitProductReview = false;
if ($reviewsAvailable && isLoggedIn()) {
    $reviewAccount = $pdo->prepare("SELECT 1 FROM users WHERE id = ? AND user_role = 'customer' AND is_active = 1 LIMIT 1");
    $reviewAccount->execute([(int)$_SESSION['user_id']]);
    $canSubmitProductReview = (bool)$reviewAccount->fetchColumn();
    if ($canSubmitProductReview) {
        $customerReviewStmt = $pdo->prepare('SELECT status FROM product_reviews WHERE product_id = ? AND customer_id = ? LIMIT 1');
        $customerReviewStmt->execute([(int)$product['id'], (int)$_SESSION['user_id']]);
        $customerReview = $customerReviewStmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }
}

$pageTitle = strip_tags($product['name']);
$pageStyles = ['asset/style/product-detail.css'];
require_once 'includes/header.php';

// Get product images
$productImages = getProductImages($product['id']);
$variationData = getProductVariationData($product['id']);
$product = applyDisplayVariationPrice($product);

// Get related products
$relatedProducts = getRelatedProducts($product['id'], $product['category_id'], 20);

// Get full category path
$categoryPath = getCategoryPath($product['category_id']);

// Check if product is in wishlist
$wishlist_ids = [];
if (isLoggedIn()) {
    $wishlistItems = getWishlistItems($_SESSION['user_id']);
    foreach ($wishlistItems as $item) {
        $wishlist_ids[] = $item['product_id'];
    }
} else {
    $wishlistItems = getWishlistItems();
    foreach ($wishlistItems as $item) {
        $wishlist_ids[] = $item['product_id'];
    }
}
$inWishlist = in_array($product['id'], $wishlist_ids);
?>

<!-- Breadcrumb Navigation -->
<div class="container-fluid product-page-breadcrumb" style="padding: 0 15px;">
    <?php
    $breadcrumbs = generateBreadcrumb(strip_tags($pageTitle), $categoryPath, strip_tags($product['name']));
    echo renderBreadcrumb($breadcrumbs);
    ?>
</div>

<div class="product-page-container">
    <style>
        /* Modern Card base fixes */
        .product-page-container {
            --product-detail-blue: #0c79e7;
        }

        .product-page-container .product-back-row {
            max-width: 1460px;
            margin: 12px auto 18px;
            padding: 0 18px;
            display: flex;
            justify-content: flex-end;
        }

        .product-page-container .product-back-btn {
            border: 0;
            background: var(--product-detail-blue);
            color: #fff;
            padding: 8px 18px;
            border-radius: 50px;
            text-decoration: none !important;
            font-weight: 700;
            font-size: 12px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
            box-shadow: 0 10px 20px rgba(12, 121, 231, 0.16);
            cursor: pointer;
        }

        .product-page-container .product-back-icon {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            border: 1px solid rgba(255, 255, 255, 0.65);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex: 0 0 24px;
        }

        .product-page-container .product-back-icon svg {
            width: 16px;
            height: 16px;
            display: block;
            stroke: #fff;
            stroke-width: 2;
            stroke-linecap: round;
            stroke-linejoin: round;
            fill: none;
        }

        .product-page-container .product-back-btn:hover,
        .product-page-container .product-back-btn:focus {
            background: var(--product-detail-blue);
            color: #fff;
            transform: translateY(-1px);
            box-shadow: 0 12px 22px rgba(12, 121, 231, 0.22);
        }

        .product-page-container .detail-variant-section {
            margin-top: 18px;
        }

        .product-page-container .detail-variant-group {
            margin-bottom: 16px;
        }

        .product-page-container .detail-variant-group h5 {
            margin: 0 0 10px;
            color: #243041;
            font-size: 14px;
            font-weight: 800;
        }

        .product-page-container .detail-variant-options {
            display: flex;
            flex-wrap: wrap;
            gap: 9px;
        }

        .product-page-container .detail-variant-option {
            min-width: 50px;
            min-height: 34px;
            border: 1px solid #d8e0ea;
            border-radius: 8px;
            background: #fff;
            color: #243041;
            font-size: 13px;
            font-weight: 800;
            padding: 6px 12px;
        }

        .product-page-container .detail-variant-option.active {
            border-color: #d8e0ea;
            background: var(--site-blue, #0c79e7);
            color: #fff;
            box-shadow: none;
        }

        .product-page-container .detail-variant-option:hover:not(.active):not(:disabled):not(.is-unavailable) {
            border-color: #0c79e7;
            color: #0c79e7;
        }

        .product-page-container .detail-variant-option:disabled,
        .product-page-container .detail-variant-option.is-unavailable {
            color: #9aa4b2;
            background: #f3f5f8;
            border-color: #e1e6ee;
            cursor: not-allowed;
            opacity: 0.75;
        }

        .product-page-container .detail-variant-note {
            color: #7c86a0;
            font-size: 14px;
            font-weight: 600;
        }

        .product-page-container .modern-card {
            padding: 0 !important;
            overflow: hidden !important;
        }

        /* Responsive Discount Banner Styling */
        .product-page-container .discount-banner-detail {
            background: #0c79e7 !important;
            color: #fff !important;
            padding: 12px 20px !important;
            font-weight: 700 !important;
            font-size: 14px !important;
            display: block !important;
            text-align: center !important;
            text-transform: uppercase !important;
            margin-left: -24px !important;
            margin-right: -24px !important;
            margin-bottom: 20px !important;
            width: calc(100% + 48px) !important;
            border-radius: 0 !important;
            position: relative;
            z-index: 5;
        }
        
        /* Ensure top of banner touches the top of the card */
        .product-page-container .product-image-section {
            padding-top: 0 !important;
            padding-left: 24px !important;
            padding-right: 24px !important;
            padding-bottom: 32px !important;
        }

        /* Move zoom icon down to avoid collision with banner */
        .product-page-container .modern-zoom {
            top: 65px !important;
        }

        /* Mobile specific adjustments */
        @media (max-width: 900px) {
            .product-page-container .product-back-row {
                margin: 10px auto 12px;
                padding: 0 12px;
                justify-content: flex-end;
            }

            .product-page-container .product-back-btn {
                padding: 8px 18px;
                border-radius: 50px;
                font-size: 12px;
            }

            .product-page-container .modern-card {
                margin: 2px 0 !important;
                padding: 4px 10px !important;
            }

            .product-page-container .product-image-section {
                padding-bottom: 2px !important; /* Remove gap after main image section */
            }

            .product-page-container .thumbnail-row {
                margin-top: 4px !important; /* Space between main img & gallery */
            }

            .product-page-container .product-info-section {
                padding: 12px !important; /* Standardized uniform padding for interior nodes border safety */
            }

            .product-page-container .modern-info h2.title {
                margin-top: 2px !important;
                margin-bottom: 4px !important; /* Product title top bottom space */
                font-size: 18px !important;
            }

            .product-page-container .sku-row, 
            .product-page-container .product-hsn {
                margin-bottom: 4px !important;
            }

            /* Set exact manual layouts for the two rows */
            .product-page-container .price-buttons1,
            .product-page-container .cart-actions {
                display: flex !important;
                flex-direction: row !important;
                flex-wrap: nowrap !important;
                justify-content: flex-start !important;
                gap: 0 !important;
                width: 100% !important;
            }

            .product-page-container .price-buttons1 {
                margin-bottom: 10px !important;
            }

            .product-page-container .cart-actions {
                margin-bottom: 10px !important;
            }

            /* Exact Math: 50% - 24px for identical parts to allow 12px right gap */
            .product-page-container .price-buttons1 .price-btn,
            .product-page-container .cart-actions .quantity-control {
                flex: 0 0 calc(50% - 24px) !important;
                min-width: calc(50% - 24px) !important;
                max-width: calc(50% - 24px) !important;
                width: calc(50% - 24px) !important;
                height: 32px !important;
                min-height: 32px !important;
                max-height: 32px !important;
                padding: 0 2px !important;
                margin: 0 !important;
                margin-right: 5px !important; /* Exact 5px gap */
                display: flex !important;
                align-items: center !important;
                justify-content: center !important;
                box-sizing: border-box !important;
                overflow: hidden !important;
                white-space: nowrap !important;
            }

            /* Wishlist specific exact layout */
            .product-page-container .price-buttons1 .wishlist {
                flex: 0 0 26px !important;
                width: 26px !important;
                min-width: 26px !important;
                max-width: 26px !important;
                margin: 0 !important;
                margin-left: auto !important; /* Push to right */
                display: flex !important;
                justify-content: flex-end !important;
                align-items: center !important;
            }
            .product-page-container .price-buttons1 .wishlist-label {
                font-size: 22px !important;
                color: #DE0085 !important;
            }

            .product-page-container .price-buttons1 .detail-unit-line {
                flex: 0 0 auto !important;
                width: auto !important;
                margin: 0 0 0 8px !important;
                align-self: center !important;
                text-align: left !important;
            }

            .product-page-container .detail-variant-group h5 {
                font-size: 15px !important;
            }

            /* Quantity Inner Buttons exact layout */
            .product-page-container .product-info-section .quantity-control .btn-qty {
                height: 100% !important;
                flex: 0 0 20px !important;
                width: 20px !important;
                min-width: 20px !important;
                max-width: 20px !important;
                padding: 0 !important;
            }
            .product-page-container .product-info-section .quantity-control .quantity-input {
                height: 100% !important;
                flex: 1 1 auto !important;
                width: 100% !important;
                max-width: none !important;
                min-width: 0 !important;
            }

            /* Add to Cart exact layout to fill gap perfectly */
            .product-page-container .product-info-section .cart-actions .add-to-cart-btn {
                flex: 1 1 auto !important;
                min-width: 0 !important;
                height: 32px !important;
                min-height: 32px !important;
                max-height: 32px !important;
                margin: 0 !important;
                margin-right: 12px !important; /* Gap from the absolute right edge */
                padding: 0 !important;
            }

            .product-page-container .product-info-section p.text-success,
            .product-page-container .product-info-section p.text-danger {
                margin-bottom: 2px !important; /* Space after stock */
            }

            .product-page-container .product-description {
                margin-top: 5px !important;
            }
        }

        .product-page-container .price-buttons1 .detail-unit-line {
            flex: 0 0 auto !important;
            width: auto !important;
            margin: 0 0 0 8px !important;
            align-self: center !important;
            text-align: left !important;
        }

        .product-page-container .mobile-detail-unit-line {
            display: none !important;
        }

        .product-page-container .price-buttons1.modern-prices .price-btn .label,
        .product-page-container .price-buttons1.modern-prices .price-btn .value {
            color: #000 !important;
            font-size: 13px !important;
            font-weight: 800 !important;
        }

        @media (min-width: 901px) {
            .product-page-container .price-buttons1.modern-prices {
                display: inline-flex !important;
                max-width: none !important;
                width: fit-content !important;
                gap: 10px !important;
                align-items: center !important;
                margin-bottom: 1px !important;
            }

            .product-page-container .price-buttons1.modern-prices .price-btn.mrp,
            .product-page-container .price-buttons1.modern-prices .price-btn.pay {
                flex: 0 0 112px !important;
                width: 112px !important;
                min-width: 112px !important;
                max-width: 112px !important;
                height: 32px !important;
                min-height: 32px !important;
                max-height: 32px !important;
                padding: 0 12px !important;
                margin: 0 !important;
                border-radius: 4px !important;
                display: flex !important;
                align-items: center !important;
                justify-content: center !important;
            }

            .product-page-container .price-buttons1.modern-prices .price-btn .label,
            .product-page-container .price-buttons1.modern-prices .price-btn .value {
                color: inherit !important;
                font-size: 12px !important;
                font-weight: 700 !important;
            }

            .product-page-container .price-buttons1.modern-prices .wishlist {
                flex: 0 0 24px !important;
                width: 24px !important;
                min-width: 24px !important;
                max-width: 24px !important;
                margin: 0 !important;
                height: 32px !important;
                display: inline-flex !important;
                align-items: center !important;
                justify-content: center !important;
            }

            .product-page-container .price-buttons1.modern-prices .wishlist-label {
                height: 14px !important;
                line-height: 14px !important;
                display: inline-flex !important;
                align-items: center !important;
                justify-content: center !important;
                margin: 0 !important;
                padding: 0 !important;
            }

            .product-page-container .price-buttons1.modern-prices .wishlist-label .header-wishlist-icon {
                display: block !important;
                line-height: 14px !important;
                height: 14px !important;
                margin: 0 !important;
                padding: 0 !important;
            }

            .product-page-container .price-buttons1.modern-prices .detail-unit-line {
                margin-left: 0 !important;
                font-family: 'Mulish', sans-serif !important;
                color: #273444 !important;
                font-size: 12px !important;
                font-weight: 500 !important;
                line-height: 14px !important;
                height: 32px !important;
                display: inline-flex !important;
                align-items: center !important;
            }

            .product-page-container .price-buttons1 .product-detail-unit-price {
                display: inline-flex !important;
                align-items: center !important;
                flex: 0 0 auto !important;
                order: 4 !important;
                color: #273444 !important;
                font-family: 'Mulish', sans-serif !important;
                font-size: 12px !important;
                font-weight: 500 !important;
                line-height: 14px !important;
                margin-left: -4px !important;
                padding: 0 !important;
                height: 32px !important;
                align-self: center !important;
                white-space: nowrap !important;
            }

            .product-page-container .product-info-section .cart-actions {
                display: flex !important;
                max-width: none !important;
                width: fit-content !important;
                gap: 10px !important;
                align-items: center !important;
                margin-bottom: 18px !important;
                clear: both !important;
            }

            .product-page-container .product-info-section .cart-actions .quantity-control {
                flex: 0 0 112px !important;
                width: 112px !important;
                min-width: 112px !important;
                max-width: 112px !important;
                height: 30px !important;
                min-height: 30px !important;
                max-height: 30px !important;
                margin: 0 !important;
            }

            .product-page-container .product-info-section .cart-actions .add-to-cart-btn {
                flex: 0 0 142px !important;
                width: 142px !important;
                min-width: 142px !important;
                max-width: 142px !important;
                height: 30px !important;
                min-height: 30px !important;
                max-height: 30px !important;
                margin: 0 !important;
                font-size: 11px !important;
                letter-spacing: 0.5px !important;
                border-radius: 3px !important;
                gap: 5px !important;
                padding: 0 8px !important;
                box-sizing: border-box !important;
                white-space: nowrap !important;
                overflow: hidden !important;
                text-overflow: ellipsis !important;
            }

            .product-page-container .product-info-section .quantity-control .quantity-input {
                flex: 1 !important;
                width: 100% !important;
                min-width: 0 !important;
                max-width: none !important;
                height: 30px !important;
                font-size: 14px !important;
                font-weight: bold !important;
                text-align: center !important;
                border: none !important;
                background: #fff !important;
                color: #333 !important;
                padding: 0 !important;
                margin: 0 !important;
                -webkit-appearance: none !important;
                -moz-appearance: textfield !important;
            }

            .product-page-container .product-info-section .quantity-control .btn-qty {
                font-size: 13px !important;
                font-weight: 400 !important;
            }
        }

        /* Typography Standardisation */
        .product-page-container .sku-row, 
        .product-page-container .product-hsn {
            font-size: 14px !important;
            color: #333 !important;
            margin-bottom: 8px !important;
        }
        .product-page-container .sku-row strong, 
        .product-page-container .product-hsn strong {
            font-weight: 700 !important;
            color: #333 !important;
        }
        .product-page-container .product-description h4 {
            font-size: 18px !important;
            font-weight: 700 !important;
            color: #333 !important;
            margin-bottom: 12px !important;
            text-align: left !important;
        }

        @media (max-width: 900px) {
            .product-page-container .sku-row,
            .product-page-container .product-hsn {
                display: inline-block !important;
                margin: 0 12px 4px 0 !important;
                font-size: 14px !important;
                color: #333 !important;
                line-height: 18px !important;
            }

            .product-page-container .mobile-detail-unit-line {
                display: block !important;
                margin: 0 0 4px 0 !important;
                color: #333 !important;
                font-size: 13px !important;
                font-weight: 400 !important;
                line-height: 18px !important;
                text-align: left !important;
                white-space: nowrap !important;
            }

            .product-page-container .mobile-detail-unit-line strong {
                font-weight: 700 !important;
                color: #333 !important;
            }

            .product-page-container .price-buttons1.modern-prices .detail-unit-line {
                display: none !important;
            }

            .product-page-container .price-buttons1.modern-prices .price-btn .label,
            .product-page-container .price-buttons1.modern-prices .price-btn .value {
                font-size: 12px !important;
                font-weight: 700 !important;
            }

            .product-page-container .product-info-section .quantity-control .btn-qty {
                flex: 0 0 30px !important;
                width: 30px !important;
                min-width: 30px !important;
                max-width: 30px !important;
                height: 32px !important;
                min-height: 32px !important;
                max-height: 32px !important;
                font-size: 14px !important;
                font-weight: 700 !important;
            }
        }

        @media (min-width: 992px) {
            .product-page-container .related-products-slider-wrapper {
                max-width: 1188px !important;
                margin-left: auto !important;
                margin-right: auto !important;
                padding-left: 34px !important;
                padding-right: 34px !important;
                justify-content: center !important;
            }

            .product-page-container .related-products-container {
                width: 1120px !important;
                max-width: 1120px !important;
                gap: 10px !important;
                justify-content: flex-start !important;
                overflow-x: auto !important;
            }

            .product-page-container .related-products-container .card.product-card {
                display: flex !important;
                flex-direction: column !important;
                align-items: stretch !important;
                flex: 0 0 240px !important;
                width: 100% !important;
                min-width: 20px !important;
                max-width: 240px !important;
                border: 1px solid #ddd !important;
                text-align: center !important;
                border-radius: 8px !important;
                background: #fff !important;
                cursor: pointer !important;
                margin: 0 0 15px 0 !important;
                overflow: hidden !important;
                position: relative !important;
                padding: 0 !important;
                box-sizing: border-box !important;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.12) !important;
                transition: transform 0.3s ease, box-shadow 0.3s ease !important;
            }
        }
    </style>

    <?php
    $fallbackBackUrl = !empty($categoryPath)
        ? 'category.php?slug=' . urlencode($categoryPath[count($categoryPath) - 1]['slug'])
        : 'products.php';
    ?>
    <div class="product-back-row">
        <button type="button" class="product-back-btn" id="productBackBtn" data-fallback="<?php echo htmlspecialchars($fallbackBackUrl); ?>">
            <span>Back</span>
            <span class="product-back-icon" aria-hidden="true">
                <svg viewBox="0 0 20 20" focusable="false">
                    <path d="M8.5 5 3.5 10l5 5"></path>
                    <path d="M4 10h12"></path>
                </svg>
            </span>
        </button>
    </div>

    <!-- Product Detail Section -->
    <div class="product-detail-card modern-card" data-id="prod-<?php echo $product['id']; ?>" data-product-id="<?php echo $product['id']; ?>" data-base-mrp="<?php echo htmlspecialchars((string)$product['mrp']); ?>" data-base-pay="<?php echo htmlspecialchars((string)$product['selling_price']); ?>">
        <div class="product-image-section position-relative">
            <?php echo renderProductDiscountBanner($product, 'discount-banner-detail', false); ?>
            <button class="zoom-icon-btn modern-zoom" id="zoomBtn" title="Zoom"><i class="fas fa-search-plus"></i></button>
            <div class="img-magnifier-container" id="mainImageContainer" style="position:relative;">
                <?php if (!empty($product['main_image'])): ?>
                    <img id="mainImage" src="<?php echo $product['main_image']; ?>" alt="<?php echo cleanProductName($product['name']); ?>" data-index="0" fetchpriority="high" decoding="async" style="width:100%;height:100%;object-fit:contain;border-radius:8px;display:block;" />
                <?php else: ?>
                    <img id="mainImage" src="./uploads/products/blank-img.webp" alt="No image available" data-index="0" fetchpriority="high" decoding="async" style="width:100%;height:100%;object-fit:contain;border-radius:8px;display:block;" />
                <?php endif; ?>
                <div id="magnifier" class="img-magnifier-glass" style="display:none;"></div>
            </div>
            <div class="thumbnail-row">
                <?php if (!empty($product['main_image'])): ?>
                    <img class="thumbnail" src="<?php echo $product['main_image']; ?>" alt="<?php echo cleanProductName($product['name']); ?>" loading="lazy" decoding="async">
                <?php else: ?>
                    <img class="thumbnail" src="./uploads/products/blank-img.webp" alt="No image available" loading="lazy" decoding="async">
                <?php endif; ?>
                <?php foreach ($productImages as $image): ?>
                    <?php if ($image['image_path'] !== $product['main_image']): ?>
                        <img class="thumbnail" src="<?php echo $image['image_path']; ?>" alt="<?php echo cleanProductName($product['name']); ?>" loading="lazy" decoding="async">
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="product-info-section modern-info">
            <h2 class="title"><?php echo cleanProductName($product['name']); ?></h2>
            <p class="sku-row"><strong>SKU:</strong> <?php echo htmlspecialchars($product['sku']); ?></p>
            <?php if (!empty($product['hsn'])): ?>
                <div class="product-hsn"><strong>HSN:</strong> <?php echo htmlspecialchars($product['hsn']); ?></div>
            <?php endif; ?>
            <?php if ($variationData['has_variations']): ?>
                <div class="detail-variant-section" id="detailVariantSection">
                    <?php foreach ($variationData['attributes'] as $attribute): ?>
                        <div class="detail-variant-group" data-attribute-id="<?php echo (int)$attribute['id']; ?>">
                            <h5>Select <?php echo htmlspecialchars($attribute['name']); ?></h5>
                            <div class="detail-variant-options">
                                <?php foreach ($attribute['values'] as $index => $value): ?>
                                    <button type="button"
                                            class="detail-variant-option"
                                            data-attribute-id="<?php echo (int)$attribute['id']; ?>"
                                            data-value-id="<?php echo (int)$value['id']; ?>">
                                        <?php echo htmlspecialchars($value['value']); ?>
                                    </button>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    <p class="detail-variant-note">Showing details for selected options.</p>
                </div>
            <?php endif; ?>
            <div class="mobile-detail-unit-line"><strong>Unit:</strong> <span id="mobileDetailUnitLine"><?php echo formatProductUnitLine($product, true); ?></span></div>
            <div class="price-buttons1 modern-prices">
                <div class="price-btn mrp">
                    <span class="label">MRP</span>
                    <span class="value" id="detailMrpValue"><?php echo formatPrice($product['mrp']); ?></span>
                </div>
                <div class="price-btn pay">
                    <span class="label">PAY</span>
                    <span class="value" id="detailPayValue"><?php echo formatPrice($product['selling_price']); ?></span>
                </div>
                <div class="wishlist">
                    <input type="checkbox" class="heart-checkbox" id="wishlist-checkbox-main-<?php echo $product['id']; ?>" data-product-id="<?php echo $product['id']; ?>" <?php echo $inWishlist ? 'checked' : ''; ?>>
                    <label for="wishlist-checkbox-main-<?php echo $product['id']; ?>" class="wishlist-label <?php echo $inWishlist ? 'wishlist-active' : ''; ?>">
                        <i class="bi <?php echo $inWishlist ? 'bi-heart-fill' : 'bi-heart'; ?> header-wishlist-icon"></i>
                    </label>
                </div>
                <div class="product-unit-line detail-unit-line product-detail-unit-price" id="detailUnitLine"><?php echo formatProductUnitLine($product, true); ?></div>
            </div>
            <?php if ($product['stock_quantity'] > 0): ?>
                <div class="cart-action-btns cart-actions d-flex align-items-center gap-2">
                <div class="quantity-control d-inline-flex align-items-center">
                    <button type="button" class="btn-qty btn-qty-minus" aria-label="Decrease quantity">-</button>
                    <?php
                    $packageQuantity = normalizePackageQuantity($product['package_quantity'] ?? 1);
                    $maxQuantity = getProductOrderMaxQuantity($product);
                    ?>
                    <input type="number" class="quantity-input" value="<?php echo $packageQuantity; ?>" min="<?php echo $packageQuantity; ?>" step="<?php echo $packageQuantity; ?>" max="<?php echo $maxQuantity; ?>" data-product-id="<?php echo $product['id']; ?>" data-package-quantity="<?php echo $packageQuantity; ?>" <?php echo $variationData['has_variations'] ? 'data-requires-variation="1"' : ''; ?>>
                    <button type="button" class="btn-qty btn-qty-plus" aria-label="Increase quantity">+</button>
                </div>
                                            <button class="add-to-cart add-to-cart-btn" data-product-id="<?php echo $product['id']; ?>" id="detailAddToCartBtn" data-variation-id="" <?php echo $variationData['has_variations'] ? 'data-requires-variation="1"' : ''; ?>>
                                    <i class="fas fa-shopping-cart"></i>
                                    ADD TO CART
                                </button>
            </div>
            <?php else: ?>
            <div class="out-of-stock-message">
                <button class="btn btn-secondary" disabled style="background-color: #6c757d; color: #fff; padding: 10px 20px; border: none; border-radius: 5px; cursor: not-allowed;">OUT OF STOCK</button>
            </div>
            <?php endif; ?>
            <p><strong>CATEGORY:</strong> 
                <?php foreach ($categoryPath as $i => $cat): ?>
                    <a href="category.php?slug=<?php echo $cat['slug']; ?>"><?php echo htmlspecialchars($cat['name']); ?></a><?php if ($i < count($categoryPath) - 1) echo ' &raquo; '; ?>
                <?php endforeach; ?>
            </p>
            <div class="product-description modern-desc">
                <h4>Product Details</h4>
                <p><?php echo $product['description']; ?></p>
            </div>
            <?php if ($product['stock_quantity'] > 0): ?>
                <p class="text-success" id="detailStockText"><strong>Stock:</strong> <?php echo (int)($product['display_base_stock_quantity'] ?? $product['stock_quantity']); ?> units available</p>
            <?php else: ?>
                <p class="text-danger"><strong>Out of Stock</strong></p>
            <?php endif; ?>
        </div>
    </div>

    <section class="product-reviews" aria-labelledby="product-reviews-title">
        <div class="product-reviews-heading">
            <div><h2 id="product-reviews-title">Customer Reviews</h2><p>What customers say about this product</p></div>
            <div class="product-reviews-summary">
                <strong><?php echo number_format($reviewSummary['average_rating'], 1); ?>/5</strong>
                <span class="review-stars" aria-label="Average rating <?php echo htmlspecialchars((string)$reviewSummary['average_rating']); ?> out of 5"><?php echo str_repeat('★', (int)round($reviewSummary['average_rating'])) . str_repeat('☆', 5 - (int)round($reviewSummary['average_rating'])); ?></span>
                <small>Based on <?php echo (int)$reviewSummary['total_reviews']; ?> <?php echo $reviewSummary['total_reviews'] === 1 ? 'review' : 'reviews'; ?></small>
            </div>
        </div>
        <?php if (!$reviewsAvailable): ?>
            <div class="product-reviews-empty">Customer reviews are temporarily unavailable.</div>
        <?php elseif ($productReviews): ?>
            <div class="product-review-carousel" data-review-carousel aria-label="Customer review carousel">
              <div class="product-review-viewport">
                <div class="product-review-track">
                <?php foreach ($productReviews as $review): ?>
                    <article class="product-review-card">
                        <div class="review-card-rating" aria-label="<?php echo (int)$review['rating']; ?> out of 5 stars"><?php echo str_repeat('★', (int)$review['rating']) . str_repeat('☆', 5 - (int)$review['rating']); ?></div>
                        <p><?php echo nl2br(htmlspecialchars($review['review_content'], ENT_QUOTES, 'UTF-8')); ?></p>
                        <div class="product-review-meta"><strong class="seller-review-code">Seller Code: <?php echo htmlspecialchars($review['seller_code'], ENT_QUOTES, 'UTF-8'); ?></strong>
                            <?php if (!empty($review['is_verified'])): ?><span class="verified-review"><i class="fas fa-check-circle"></i> Verified Purchase</span><?php endif; ?>
                            <time datetime="<?php echo htmlspecialchars($review['created_at'], ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars(date('M j, Y', strtotime($review['created_at'])), ENT_QUOTES, 'UTF-8'); ?></time>
                        </div>
                    </article>
                <?php endforeach; ?>
                </div>
              </div>
              <div class="product-review-carousel-controls">
                <button type="button" class="review-carousel-arrow" data-review-prev aria-label="Previous reviews">&#8592;</button>
                <span data-review-page aria-live="polite"></span>
                <button type="button" class="review-carousel-arrow" data-review-next aria-label="Next reviews">&#8594;</button>
              </div>
            </div>
        <?php else: ?><div class="product-reviews-empty">No approved reviews yet.</div><?php endif; ?>

        <div class="write-product-review" id="write-product-review">
            <h3>Write a Review</h3>
            <?php if ($reviewNotice): ?><div class="review-submit-notice" role="status"><?php echo htmlspecialchars($reviewNotice, ENT_QUOTES, 'UTF-8'); ?></div><?php endif; ?>
            <?php if (!$reviewsAvailable): ?>
                <p class="product-review-login-note">Review submissions are temporarily unavailable.</p>
            <?php elseif ($customerReview): ?>
                <p class="product-review-login-note">You have already submitted a review for this product. Its current status is <strong><?php echo htmlspecialchars(ucfirst($customerReview['status']), ENT_QUOTES, 'UTF-8'); ?></strong>.</p>
            <?php elseif (!isLoggedIn()): ?>
                <p class="product-review-login-note"><a href="login.php?review_product=<?php echo rawurlencode((string)$product['slug']); ?>">Sign in</a> to your customer account to submit a review.</p>
            <?php elseif (!$canSubmitProductReview): ?>
                <p class="product-review-login-note">Reviews can be submitted from a customer account.</p>
            <?php else: ?>
                <form method="post" action="product.php?slug=<?php echo rawurlencode((string)$product['slug']); ?>#write-product-review" class="product-review-form">
                    <input type="hidden" name="action" value="submit_product_review">
                    <input type="hidden" name="review_csrf" value="<?php echo htmlspecialchars(getProductReviewCsrfToken(), ENT_QUOTES, 'UTF-8'); ?>">
                    <label>Rating <select name="rating" required><option value="">Select rating</option><?php for ($rating = 5; $rating >= 1; $rating--): ?><option value="<?php echo $rating; ?>"><?php echo $rating; ?> star<?php echo $rating > 1 ? 's' : ''; ?></option><?php endfor; ?></select></label>
                    <label>Review title <input type="text" name="review_title" maxlength="200" required placeholder="Summarize your experience"></label>
                    <label>Your review <textarea name="review_content" rows="4" maxlength="10000" required placeholder="What did you think about this product?"></textarea></label>
                    <p class="product-review-login-note">Your review will be checked by our team before it appears. Seller Code and Verified Purchase are managed by an admin.</p>
                    <button type="submit">Submit Review</button>
                </form>
            <?php endif; ?>
        </div>
    </section>

    <style>
      .product-reviews{max-width:1460px;margin:30px auto;padding:24px;background:#fff;border:1px solid #e6eaf0;border-radius:12px;box-shadow:0 8px 28px rgba(25,45,75,.05)}
      .product-reviews-heading{display:flex;justify-content:space-between;align-items:center;gap:20px;padding:0 0 18px;border-bottom:1px solid #edf0f4}
      .product-reviews-heading>div:first-child{flex:1;min-width:0;margin:0;padding:0;text-align:left!important}
      .product-reviews-heading h2{display:block;width:auto;margin:0!important;padding:0!important;text-align:left!important;color:#202b3c;font-size:23px;font-weight:700}.product-reviews-heading p{display:block;width:auto;margin:5px 0 0!important;padding:0!important;text-align:left!important;color:#6b7280}
      .product-reviews-summary{display:grid;text-align:right;gap:2px}.product-reviews-summary strong{font-size:25px;color:#202b3c}.product-reviews-summary small{color:#687385}.review-stars,.review-card-rating{color:#efa900;letter-spacing:2px}
      .product-review-carousel{padding-top:18px}.product-review-viewport{overflow:hidden;touch-action:pan-y}.product-review-track{display:flex;gap:14px;transition:transform .45s ease;will-change:transform}.product-review-card{flex:0 0 calc(50% - 7px);min-width:0;padding:18px;border:1px solid #e8ecf1;border-radius:10px;background:#fff}
      .review-card-rating{font-size:17px}.product-review-card>p{margin:0;color:#4c5665;line-height:1.6;overflow-wrap:anywhere}.product-review-meta{display:flex;align-items:center;flex-wrap:wrap;gap:12px;margin-top:15px;padding-top:12px;border-top:1px solid #f0f2f5;color:#667085;font-size:13px}.product-review-meta time,.seller-review-code{font-weight:700;color:#455164}.verified-review{color:#188449;font-weight:600}.product-reviews-empty{padding:24px 0;color:#697586}
      .product-review-carousel-controls{display:flex;justify-content:center;align-items:center;gap:14px;margin-top:16px;color:#667085;font-size:13px}.review-carousel-arrow{width:34px;height:34px;border:1px solid #d5dce5;border-radius:50%;background:#fff;color:#344054;cursor:pointer}.review-carousel-arrow:hover{border-color:#0c79e7;color:#0c79e7}.review-carousel-arrow:focus-visible{outline:2px solid #0c79e7;outline-offset:2px}
      .product-review-carousel.single-review-page .product-review-carousel-controls{display:none}
      .write-product-review{margin-top:22px;padding-top:20px;border-top:1px solid #edf0f4}.write-product-review h3{margin:0 0 12px;font-size:19px;color:#263244}.product-review-form{display:grid;gap:12px;max-width:760px}.product-review-form label{display:grid;gap:6px;font-weight:600;color:#344054}.product-review-form input,.product-review-form textarea,.product-review-form select{width:100%;padding:10px 12px;border:1px solid #d0d5dd;border-radius:7px;font:inherit;font-weight:400}.product-review-form button{justify-self:start;border:0;border-radius:7px;background:#0c79e7;color:white;padding:10px 18px;font-weight:700;cursor:pointer}.product-review-form button:hover{background:#0868c8}.review-submit-notice{max-width:760px;padding:10px 13px;border-radius:7px;background:#eaf5ff;color:#155b91;margin-bottom:12px}.product-review-login-note{color:#687385;margin:0 0 12px}
      @media(max-width:700px){.product-reviews{box-sizing:border-box;width:auto;/* margin:20px 12px; */padding:12px 10px !important}.product-reviews-heading{align-items:flex-start;padding:0 12px 18px}.product-reviews-heading>div{padding:0}.product-review-carousel{padding:18px 0 0}.product-reviews-heading h2{font-size:20px}.product-review-card{flex-basis:100%;padding:12px}.product-reviews-empty{padding-left:12px;padding-right:12px}.write-product-review{padding:12px}.product-review-meta{gap:8px 12px}}
      @media(prefers-reduced-motion:reduce){.product-review-track{transition:none}}
    </style>

    <script>
    (function () {
      const carousel = document.querySelector('[data-review-carousel]');
      if (!carousel) return;
      const viewport = carousel.querySelector('.product-review-viewport');
      const track = carousel.querySelector('.product-review-track');
      const cards = Array.from(track.children);
      const pageLabel = carousel.querySelector('[data-review-page]');
      const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
      let index = 0;
      let visible = 2;
      let timer = null;
      let touchStartX = null;
      let paused = false;
      const getVisible = () => window.matchMedia('(max-width: 700px)').matches ? 1 : 2;
      const lastPageStart = () => Math.max(0, Math.ceil(cards.length / visible) - 1) * visible;
      function render() {
        visible = getVisible();
        const lastStart = lastPageStart();
        if (index > lastStart) index = 0;
        const card = cards[0];
        if (!card) return;
        const gap = parseFloat(getComputedStyle(track).columnGap) || 0;
        track.style.transform = 'translateX(-' + (index * (card.getBoundingClientRect().width + gap)) + 'px)';
        pageLabel.textContent = (Math.floor(index / visible) + 1) + ' / ' + (lastStart / visible + 1);
        carousel.classList.toggle('single-review-page', lastStart === 0);
      }
      function advance(direction) {
        const lastStart = lastPageStart();
        if (!lastStart) return;
        index += direction * visible;
        if (index > lastStart) index = 0;
        if (index < 0) index = lastStart;
        render();
      }
      function stopAutoScroll() { if (timer) { clearInterval(timer); timer = null; } }
      function startAutoScroll() {
        stopAutoScroll();
        if (reducedMotion || lastPageStart() === 0) return;
        timer = setInterval(function () { if (!paused && !document.hidden) advance(1); }, 5000);
      }
      carousel.querySelector('[data-review-prev]').addEventListener('click', function () { advance(-1); });
      carousel.querySelector('[data-review-next]').addEventListener('click', function () { advance(1); });
      carousel.addEventListener('mouseenter', function () { paused = true; });
      carousel.addEventListener('mouseleave', function () { paused = false; });
      carousel.addEventListener('focusin', function () { paused = true; });
      carousel.addEventListener('focusout', function (event) { if (!carousel.contains(event.relatedTarget)) paused = false; });
      viewport.addEventListener('touchstart', function (event) { touchStartX = event.changedTouches[0].clientX; }, { passive: true });
      viewport.addEventListener('touchend', function (event) {
        if (touchStartX === null) return;
        const swipe = event.changedTouches[0].clientX - touchStartX;
        if (Math.abs(swipe) > 45) advance(swipe < 0 ? 1 : -1);
        touchStartX = null;
      }, { passive: true });
      window.addEventListener('resize', function () { render(); startAutoScroll(); });
      render();
      startAutoScroll();
    })();
    </script>

    <!-- Zoom Modal -->
    <div id="zoomModal" class="zoom-modal">
        <span class="zoom-close" id="zoomClose">&times;</span>
        <img class="zoom-modal-content" id="zoomedImg" src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///ywAAAAAAQABAAACAUwAOw==" alt="Zoomed Product Image">
        <button id="zoomPrev" class="zoom-nav-btn zoom-prev">&#8592;</button>
        <button id="zoomNext" class="zoom-nav-btn zoom-next">&#8594;</button>
    </div>

    <!-- Related Products Section -->
    <section class="related-products-section">
        <div class="related-products-card">
            <div class="related-products-header">
                <h2 class="related-products-title">Related Products</h2>
            </div>
            <div class="related-products-slider-wrapper">
                <button class="related-nav-btn prev-btn" aria-label="Scroll Left">
                    <img src="asset/icons/blue_arrow.png" alt="Previous" loading="lazy" decoding="async">
                </button>
                <div class="related-products-container" id="related-slider">
                    <?php foreach ($relatedProducts as $relatedProduct): 
                        $inWishlist = in_array($relatedProduct['id'], $wishlist_ids);
                        $isOutOfStock = ($relatedProduct['stock_quantity'] <= 0);
                    ?>
                        <div class="card product-card" data-id="prod-<?php echo $relatedProduct['id']; ?>">
                            <?php echo renderProductDiscountBanner($relatedProduct); ?>
                            
                            <div class="product-info">
                                <div class="product-image">
                                    <a href="product.php?slug=<?php echo $relatedProduct['slug']; ?>">
                                        <?php if (!empty($relatedProduct['main_image'])): ?>
                                            <img src="<?php echo $relatedProduct['main_image']; ?>" alt="<?php echo cleanProductName($relatedProduct['name']); ?>" loading="lazy" decoding="async">
                                        <?php else: ?>
                                            <img src="./uploads/products/blank-img.webp" alt="No image available" loading="lazy" decoding="async">
                                        <?php endif; ?>
                                    </a>
                                    <?php if ($isOutOfStock): ?>
                                        <div class="out-of-stock">OUT OF STOCK</div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="product-details">
                                    <div class="product-unit-line"><?php echo formatProductUnitLine($relatedProduct, true); ?></div>
                                    <a href="product.php?slug=<?php echo $relatedProduct['slug']; ?>" class="product-title-link">
                                        <h3><?php echo cleanProductName($relatedProduct['name']); ?></h3>
                                    </a>

                                    <div class="price-buttons">
                                        <div class="price-btn mrp">
                                            <span class="label">MRP</span>
                                            <span class="value"><?php echo formatPrice($relatedProduct['mrp']); ?></span>
                                        </div>
                                        <div class="price-btn pay">
                                            <span class="label">PAY</span>
                                            <span class="value"><?php echo formatPrice($relatedProduct['selling_price']); ?></span>
                                        </div>
                                        <div class="wishlist">
                                            <input type="checkbox" class="heart-checkbox" id="wishlist-checkbox-related-<?php echo $relatedProduct['id']; ?>" data-product-id="<?php echo $relatedProduct['id']; ?>" <?php if ($inWishlist) echo 'checked'; ?>>
                                            <label for="wishlist-checkbox-related-<?php echo $relatedProduct['id']; ?>" class="wishlist-label <?php echo $inWishlist ? 'wishlist-active' : ''; ?>">
                                                <i class="bi <?php echo $inWishlist ? 'bi-heart-fill' : 'bi-heart'; ?> header-wishlist-icon"></i>
                                            </label>
                                        </div>
                                    </div>
                                    
                                    <?php if ($isOutOfStock): ?>
                                        <a href="product.php?slug=<?php echo $relatedProduct['slug']; ?>" class="read-more">READ MORE</a>
                                    <?php else: ?>
                                        <div class="cart-actions d-flex align-items-center">
                                            <div class="quantity-control d-inline-flex align-items-center">
                                                <button type="button" class="btn-qty btn-qty-minus" aria-label="Decrease quantity">-</button>
                                                <?php
                                                $relatedPackageQuantity = normalizePackageQuantity($relatedProduct['package_quantity'] ?? 1);
                                                $relatedMaxQuantity = getProductOrderMaxQuantity($relatedProduct);
                                                ?>
                                                <input type="number" class="quantity-input" value="<?php echo $relatedPackageQuantity; ?>" min="<?php echo $relatedPackageQuantity; ?>" step="<?php echo $relatedPackageQuantity; ?>" max="<?php echo $relatedMaxQuantity; ?>" data-product-id="<?php echo $relatedProduct['id']; ?>" data-package-quantity="<?php echo $relatedPackageQuantity; ?>">
                                                <button type="button" class="btn-qty btn-qty-plus" aria-label="Increase quantity">+</button>
                                            </div>
                                            <button class="add-to-cart add-to-cart-btn" data-product-id="<?php echo $relatedProduct['id']; ?>">
                                                <i class="fas fa-shopping-cart"></i>
                                                ADD TO CART
                                            </button>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <button class="related-nav-btn next-btn" aria-label="Scroll Right">
                    <img src="asset/icons/blue_arrow.png" alt="Next" loading="lazy" decoding="async" style="transform: rotate(180deg);">
                </button>
            </div>
        </div>
    </section>
</div>

<script>
window.productDetailVariations = <?php echo json_encode($variationData); ?>;

window.updateDisplayedPriceForQuantity = function(input, priceSource) {
    if (!input) return;

    const card = input.closest('.product-detail-card.modern-card');
    if (!card) return;

    const mrpValue = document.getElementById('detailMrpValue');
    const payValue = document.getElementById('detailPayValue');
    const detailDiscountBanner = document.querySelector('.discount-banner-detail');

    if (priceSource) {
        card.dataset.baseMrp = Number(priceSource.mrp || 0);
        card.dataset.basePay = Number(priceSource.selling_price || 0);
    }

    const qty = typeof normalizeQuantityInputValue === 'function'
        ? normalizeQuantityInputValue(input)
        : Math.max(1, parseInt(input.value, 10) || 1);
    const packageQuantity = Math.max(1, parseInt(input.dataset.packageQuantity || input.getAttribute('step'), 10) || 1);
    const packageMultiplier = Math.max(1, qty / packageQuantity);
    const unitMrp = Number(card.dataset.baseMrp || 0);
    const unitPay = Number(card.dataset.basePay || 0);
    const totalMrp = unitMrp * packageMultiplier;
    const totalPay = unitPay * packageMultiplier;

    function formatDetailPrice(value) {
        return '\u20b9 ' + Math.round(Number(value || 0)).toLocaleString('en-IN');
    }

    if (mrpValue) mrpValue.textContent = formatDetailPrice(totalMrp);
    if (payValue) payValue.textContent = formatDetailPrice(totalPay);

    if (!detailDiscountBanner) return;

    if (unitMrp > 0 && unitPay > 0 && unitMrp > unitPay) {
        const saveAmount = Math.round((unitMrp - unitPay) * packageMultiplier);
        const discountPercent = Math.round(((unitMrp - unitPay) / unitMrp) * 100);
        detailDiscountBanner.textContent = 'SAVE \u20b9' + saveAmount.toLocaleString('en-IN') + ' (' + discountPercent + '% OFF)';
        detailDiscountBanner.style.display = '';
    } else {
        detailDiscountBanner.style.display = 'none';
    }
};

document.addEventListener('DOMContentLoaded', function() {
    const detailQuantityInput = document.querySelector('.product-detail-card.modern-card .quantity-input');
    if (detailQuantityInput) {
        const refreshDetailPrices = function() {
            window.updateDisplayedPriceForQuantity(detailQuantityInput);
        };
        detailQuantityInput.addEventListener('input', refreshDetailPrices);
        detailQuantityInput.addEventListener('change', refreshDetailPrices);
        refreshDetailPrices();
    }

    const detailVariationData = window.productDetailVariations || { has_variations: false, attributes: [], variations: [] };
    if (detailVariationData.has_variations && detailVariationData.variations.length) {
        const selectedValues = {};
        const attributeGroups = detailVariationData.attributes || [];
        const variations = detailVariationData.variations || [];

        const mrpValue = document.getElementById('detailMrpValue');
        const payValue = document.getElementById('detailPayValue');
        const detailUnitLine = document.getElementById('detailUnitLine');
        const mobileDetailUnitLine = document.getElementById('mobileDetailUnitLine');
        const stockText = document.getElementById('detailStockText');
        const addToCartBtn = document.getElementById('detailAddToCartBtn');
        const detailMainImage = document.getElementById('mainImage');
        const detailDiscountBanner = document.querySelector('.discount-banner-detail');
        const quantityInput = document.querySelector('.product-detail-card .quantity-input');
        const productUnitPrice = <?php echo json_encode(getProductUnitPrice($product)); ?>;
        const hasProductUnitPrice = <?php echo json_encode((float)($product['pay_per_unit'] ?? 0) > 0); ?>;
        const packageQuantity = <?php echo (int)normalizePackageQuantity($product['package_quantity'] ?? 1); ?>;
        const productMaxPackages = <?php echo (int)(($product['max_quantity_per_order'] ?? 0) ?: 0); ?>;

        function formatPriceValue(value) {
            return '₹ ' + Number(value || 0).toLocaleString('en-IN', { maximumFractionDigits: 0 });
        }

        function formatUnitLine(value) {
            const unitPrice = Number(value || 0).toLocaleString('en-IN', {
                minimumFractionDigits: 0,
                maximumFractionDigits: 2
            });
            return '(₹ ' + unitPrice + ' / <?php echo addslashes(getProductUnitLabel($product)); ?>)';
        }

        function getVariationUnitPrice(variation) {
            return hasProductUnitPrice ? productUnitPrice : variation.selling_price;
        }

        function variationMatchesFilters(variation, filters) {
            return Object.entries(filters).every(([attributeId, valueId]) => {
                return String(variation.attribute_value_ids?.[attributeId]) === String(valueId);
            });
        }

        function exactSelectedVariation() {
            if (attributeGroups.some(group => !selectedValues[String(group.id)])) {
                return null;
            }

            return variations.find(variation => {
                return attributeGroups.every(group => {
                    return String(variation.attribute_value_ids?.[group.id]) === String(selectedValues[String(group.id)]);
                });
            }) || null;
        }

        function getSelectionWithOption(attributeId, valueId) {
            const filters = {};
            const targetIndex = attributeGroups.findIndex(group => String(group.id) === String(attributeId));
            attributeGroups.forEach((group, index) => {
                const groupId = String(group.id);
                if (index < targetIndex && selectedValues[groupId]) {
                    filters[groupId] = selectedValues[groupId];
                }
            });
            filters[String(attributeId)] = String(valueId);
            return filters;
        }

        function isValueAvailable(attributeId, valueId) {
            const filters = getSelectionWithOption(attributeId, valueId);
            return variations.some(variation => Number(variation.stock_quantity) > 0 && variationMatchesFilters(variation, filters));
        }

        function clearUnavailableSelectedValues(changedAttributeId) {
            const changedIndex = attributeGroups.findIndex(group => String(group.id) === String(changedAttributeId));
            attributeGroups.forEach((group, index) => {
                const attributeId = String(group.id);
                if (index <= changedIndex || !selectedValues[attributeId]) {
                    return;
                }

                if (!isValueAvailable(attributeId, selectedValues[attributeId])) {
                    delete selectedValues[attributeId];
                }
            });
        }

        function selectedAttributePayload() {
            const payload = {};
            attributeGroups.forEach(group => {
                const valueId = selectedValues[String(group.id)];
                const value = (group.values || []).find(item => String(item.id) === String(valueId));
                if (value) {
                    payload[group.name] = [value.value];
                }
            });
            return payload;
        }

        function updateDetailDiscountBanner(variation) {
            if (!detailDiscountBanner) return;

            const mrp = Number(variation.mrp || 0);
            const sellingPrice = Number(variation.selling_price || 0);
            if (mrp > 0 && sellingPrice > 0 && mrp > sellingPrice) {
                const saveAmount = Math.round(mrp - sellingPrice);
                const discountPercent = Math.round(((mrp - sellingPrice) / mrp) * 100);
                detailDiscountBanner.textContent = 'SAVE \u20b9' + saveAmount.toLocaleString('en-IN') + ' (' + discountPercent + '% OFF)';
                detailDiscountBanner.style.display = '';
            } else {
                detailDiscountBanner.style.display = 'none';
            }
        }

        function syncOptionButtons() {
            document.querySelectorAll('.detail-variant-option').forEach(button => {
                const attributeId = button.dataset.attributeId;
                const valueId = button.dataset.valueId;
                const available = isValueAvailable(attributeId, valueId);
                button.classList.toggle('active', String(selectedValues[String(attributeId)] || '') === String(valueId));
                button.classList.toggle('is-unavailable', !available);
                button.disabled = !available;
            });
        }

        function syncCartButtonSelection() {
            if (!addToCartBtn) return;
            addToCartBtn.dataset.selectedValueIds = JSON.stringify(selectedValues);
            addToCartBtn.setAttribute('data-selected-value-ids', JSON.stringify(selectedValues));
            addToCartBtn.dataset.selectedAttributes = JSON.stringify(selectedAttributePayload());
        }

        function applySelectedVariation() {
            const selectedVariation = exactSelectedVariation();
            syncOptionButtons();
            syncCartButtonSelection();

            if (!selectedVariation) {
                if (addToCartBtn) {
                    addToCartBtn.dataset.variationId = '';
                    addToCartBtn.setAttribute('data-variation-id', '');
                    addToCartBtn.disabled = false;
                }
                if (quantityInput) {
                    quantityInput.dataset.variationId = '';
                }
                return;
            }

            if (mrpValue) mrpValue.textContent = formatPriceValue(selectedVariation.mrp);
            if (payValue) payValue.textContent = formatPriceValue(selectedVariation.selling_price);
            updateDetailDiscountBanner(selectedVariation);
            if (detailUnitLine) detailUnitLine.textContent = formatUnitLine(getVariationUnitPrice(selectedVariation));
            if (mobileDetailUnitLine) mobileDetailUnitLine.textContent = formatUnitLine(getVariationUnitPrice(selectedVariation));
            if (detailMainImage && selectedVariation.image_path) {
                detailMainImage.src = selectedVariation.image_path;
            }

            const variationStockQuantity = Math.max(0, parseInt(selectedVariation.stock_quantity, 10) || 0);
            if (stockText) {
                stockText.className = variationStockQuantity > 0 ? 'text-success' : 'text-danger';
                stockText.innerHTML = variationStockQuantity > 0
                    ? '<strong>Stock:</strong> ' + variationStockQuantity + ' units available'
                    : '<strong>Out of Stock</strong>';
            }
            if (addToCartBtn) {
                addToCartBtn.dataset.variationId = selectedVariation.id;
                addToCartBtn.setAttribute('data-variation-id', selectedVariation.id);
                addToCartBtn.dataset.selectedAttributes = JSON.stringify(selectedAttributePayload());
                addToCartBtn.disabled = variationStockQuantity <= 0;
            }
            if (quantityInput) {
                const maxPackages = productMaxPackages > 0 ? Math.min(productMaxPackages, variationStockQuantity) : variationStockQuantity;
                quantityInput.max = Math.max(packageQuantity, maxPackages * packageQuantity);
                quantityInput.dataset.variationId = selectedVariation.id;
                if (typeof normalizeQuantityInputValue === 'function') {
                    normalizeQuantityInputValue(quantityInput);
                }
            }
            if (window.updateDisplayedPriceForQuantity && quantityInput) {
                window.updateDisplayedPriceForQuantity(quantityInput, selectedVariation);
            }
        }

        document.querySelectorAll('.detail-variant-option').forEach(button => {
            button.addEventListener('click', function() {
                if (this.disabled) return;
                const attributeId = String(this.dataset.attributeId);
                const valueId = String(this.dataset.valueId);
                if (String(selectedValues[attributeId] || '') === valueId) {
                    delete selectedValues[attributeId];
                } else {
                    selectedValues[attributeId] = valueId;
                }
                clearUnavailableSelectedValues(this.dataset.attributeId);
                applySelectedVariation();
            });
        });

        window.getProductDetailVariantSelection = function(productId) {
            if (String(productId) !== String(<?php echo (int)$product['id']; ?>)) {
                return {};
            }
            return Object.assign({}, selectedValues);
        };

        window.applyProductDetailVariantSelection = function(productId, nextSelectedValues) {
            if (String(productId) !== String(<?php echo (int)$product['id']; ?>)) {
                return;
            }

            Object.keys(selectedValues).forEach(attributeId => delete selectedValues[attributeId]);
            Object.entries(nextSelectedValues || {}).forEach(([attributeId, valueId]) => {
                if (valueId !== null && valueId !== undefined && valueId !== '') {
                    selectedValues[String(attributeId)] = String(valueId);
                }
            });
            applySelectedVariation();
        };

        applySelectedVariation();
    }

    const productBackBtn = document.getElementById('productBackBtn');
    if (productBackBtn) {
        productBackBtn.addEventListener('click', function() {
            if (window.history.length > 1 && document.referrer) {
                window.history.back();
            } else {
                window.location.href = this.dataset.fallback || 'products.php';
            }
        });
    }

    const thumbnails = document.querySelectorAll('.thumbnail');
    const mainImage = document.getElementById('mainImage');
    const mainImageContainer = document.getElementById('mainImageContainer');
    const magnifier = document.getElementById('magnifier');
    if (!mainImage || !mainImageContainer) return;
    
    // Mark the first thumbnail as active on load
    if (thumbnails.length > 0) {
        thumbnails[0].classList.add('active');
    }
    
    thumbnails.forEach((thumb, idx) => {
        thumb.addEventListener('click', function() {
            thumbnails.forEach(t => t.classList.remove('active'));
            mainImage.src = this.src;
            mainImage.setAttribute('data-index', idx);
            this.classList.add('active');
        });
    });
    
    // --- Magnifier effect ---
    function magnify(img, zoom) {
        let glass = magnifier;
        glass.style.backgroundImage = `url('${img.src}')`;
        glass.style.backgroundRepeat = 'no-repeat';
        glass.style.backgroundSize = (img.width * zoom) + 'px ' + (img.height * zoom) + 'px';
        let bw = 3;
        let w = glass.offsetWidth / 2;
        let h = glass.offsetHeight / 2;
        
        function moveMagnifier(e) {
            let pos = getCursorPos(e);
            let x = pos.x;
            let y = pos.y;
            if (x > img.width - (w / zoom)) { x = img.width - (w / zoom); }
            if (x < w / zoom) { x = w / zoom; }
            if (y > img.height - (h / zoom)) { y = img.height - (h / zoom); }
            if (y < h / zoom) { y = h / zoom; }
            glass.style.left = (x - w) + 'px';
            glass.style.top = (y - h) + 'px';
            glass.style.backgroundPosition = '-' + ((x * zoom) - w + bw) + 'px -' + ((y * zoom) - h + bw) + 'px';
        }
        
        function getCursorPos(e) {
            let a = img.getBoundingClientRect();
            let x = e.pageX - a.left - window.pageXOffset;
            let y = e.pageY - a.top - window.pageYOffset;
            return { x: x, y: y };
        }
        
        glass.onmousemove = moveMagnifier;
        img.onmousemove = moveMagnifier;
        glass.ontouchmove = moveMagnifier;
        img.ontouchmove = moveMagnifier;
    }
    
    mainImage.addEventListener('mouseenter', function() {
        magnifier.style.display = 'block';
        magnify(mainImage, 2);
    });
    
    mainImage.addEventListener('mouseleave', function() {
        magnifier.style.display = 'none';
    });
    
    mainImage.addEventListener('mousemove', function(e) {
        if (magnifier.style.display === 'block') {
            magnifier.onmousemove(e);
        }
    });
    
    // Update magnifier when image changes
    thumbnails.forEach((thumb) => {
        thumb.addEventListener('click', function() {
            if (magnifier.style.display === 'block') {
                magnify(mainImage, 2);
            }
        });
    });
    
    // --- Zoom Modal with navigation ---
    const zoomModal = document.getElementById('zoomModal');
    const zoomedImg = document.getElementById('zoomedImg');
    const zoomClose = document.getElementById('zoomClose');
    const zoomPrev = document.getElementById('zoomPrev');
    const zoomNext = document.getElementById('zoomNext');
    const productImages = Array.from(thumbnails).map(t => t.getAttribute('src'));
    let zoomIdx = 0;
    
    function showZoom(idx) {
        zoomedImg.src = productImages[idx];
        zoomIdx = idx;
        if (productImages.length > 1) {
            zoomPrev.style.display = 'flex';
            zoomNext.style.display = 'flex';
        } else {
            zoomPrev.style.display = 'none';
            zoomNext.style.display = 'none';
        }
    }
    
    document.getElementById('zoomBtn').onclick = function() {
        showZoom(parseInt(mainImage.getAttribute('data-index')) || 0);
        zoomModal.style.display = 'flex';
    };
    
    zoomClose.onclick = function() {
        zoomModal.style.display = 'none';
    };
    
    if (productImages.length > 1) {
        zoomPrev.onclick = function(e) {
            e.stopPropagation();
            zoomIdx = (zoomIdx - 1 + productImages.length) % productImages.length;
            showZoom(zoomIdx);
        };
        zoomNext.onclick = function(e) {
            e.stopPropagation();
            zoomIdx = (zoomIdx + 1) % productImages.length;
            showZoom(zoomIdx);
        };
    }
    
    zoomModal.onclick = function(e) {
        if (e.target === this) zoomModal.style.display = 'none';
    };
    
    // Related Products Slider
    const relatedSlider = document.getElementById('related-slider');
    const prevBtn = document.querySelector('.related-nav-btn.prev-btn');
    const nextBtn = document.querySelector('.related-nav-btn.next-btn');
    
    if (relatedSlider && prevBtn && nextBtn) {
        let scrollAmount = relatedSlider.scrollLeft;

        function getScrollStep() {
            const firstCard = relatedSlider.querySelector('.product-card');
            if (!firstCard) {
                return relatedSlider.clientWidth;
            }

            const cardRect = firstCard.getBoundingClientRect();
            const styles = window.getComputedStyle(relatedSlider);
            const gap = parseFloat(styles.columnGap || styles.gap || 0) || 0;
            return Math.max(1, Math.round(cardRect.width + gap));
        }
        
        function updateScrollButtons() {
            const maxScroll = relatedSlider.scrollWidth - relatedSlider.clientWidth;
            scrollAmount = relatedSlider.scrollLeft;
            const hasOverflow = maxScroll > 1;
            prevBtn.style.display = hasOverflow ? 'flex' : 'none';
            nextBtn.style.display = hasOverflow ? 'flex' : 'none';
        }
        
        prevBtn.addEventListener('click', function() {
            scrollAmount = Math.max(0, relatedSlider.scrollLeft - getScrollStep());
            relatedSlider.scrollTo({
                left: scrollAmount,
                behavior: 'smooth'
            });
        });
        
        nextBtn.addEventListener('click', function() {
            const maxScroll = relatedSlider.scrollWidth - relatedSlider.clientWidth;
            scrollAmount = Math.min(maxScroll, relatedSlider.scrollLeft + getScrollStep());
            relatedSlider.scrollTo({
                left: scrollAmount,
                behavior: 'smooth'
            });
        });
        
        relatedSlider.addEventListener('scroll', function() {
            scrollAmount = relatedSlider.scrollLeft;
            updateScrollButtons();
        });
        
        updateScrollButtons();
        window.addEventListener('resize', updateScrollButtons);
    }
});
</script> 

<?php include 'includes/footer.php'; ?> 
