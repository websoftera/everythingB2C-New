<?php
if (PHP_SAPI !== 'cli') { http_response_code(403); exit('CLI only.'); }
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/product_reviews.php';

function review_test_assert(bool $condition, string $message): void {
    if (!$condition) throw new RuntimeException($message);
}

$productId = (int)$pdo->query('SELECT id FROM products ORDER BY id LIMIT 1')->fetchColumn();
$customerId = (int)$pdo->query("SELECT id FROM users WHERE user_role = 'customer' ORDER BY id LIMIT 1")->fetchColumn();
if (!$productId || !$customerId) { fwrite(STDERR, "Need at least one product and customer for integration checks.\n"); exit(2); }

$pdo->beginTransaction();
try {
    $newReview = [
        'product_id' => $productId, 'customer_id' => $customerId, 'rating' => 5,
        'review_title' => 'Review system integration check', 'review_content' => 'Private data stays hidden <script>alert(1)</script>',
        'seller_code' => '', 'is_verified' => 1, 'status' => 'pending',
    ];
    $id = saveAdminProductReview($pdo, $newReview);
    review_test_assert(!getPublicProductReviews($pdo, $productId), 'Pending reviews must not appear publicly.');
    $pendingStats = getProductReviewStats($pdo);
    review_test_assert($pendingStats['pending'] >= 1, 'Pending count should include the inserted review.');

    $failedApproval = false;
    try { setAdminProductReviewStatus($pdo, $id, 'approved'); } catch (InvalidArgumentException $e) { $failedApproval = true; }
    review_test_assert($failedApproval, 'Approval without Seller Code must be rejected.');

    $newReview['seller_code'] = 'SELL-1025';
    $newReview['status'] = 'approved';
    saveAdminProductReview($pdo, $newReview, $id);
    $public = getPublicProductReviews($pdo, $productId);
    $found = null;
    foreach ($public as $item) if ($item['review_title'] === $newReview['review_title']) $found = $item;
    review_test_assert($found !== null, 'Approved review should be visible publicly.');
    review_test_assert(array_keys($found) === ['rating','review_title','review_content','seller_code','is_verified','created_at'], 'Public response must contain only approved safe review fields.');
    review_test_assert($found['seller_code'] === 'SELL-1025' && (int)$found['is_verified'] === 1, 'Seller Code and verified flag should be returned.');
    review_test_assert(strpos($found['review_content'], '<script>') === false, 'Review HTML must be stripped before storage.');

    $search = listAdminProductReviews($pdo, ['search' => 'SELL-1025'], 25, 0);
    review_test_assert($search['total'] >= 1, 'Seller Code search should find the review.');
    $summary = getPublicProductReviewSummary($pdo, $productId);
    review_test_assert($summary['total_reviews'] >= 1 && $summary['average_rating'] > 0, 'Approved review statistics should be calculated.');

    foreach (['spam','rejected','pending'] as $status) {
        setAdminProductReviewStatus($pdo, $id, $status);
        $publicTitles = array_column(getPublicProductReviews($pdo, $productId), 'review_title');
        review_test_assert(!in_array($newReview['review_title'], $publicTitles, true), ucfirst($status) . ' review must not appear publicly.');
    }
    echo "Product review integration checks passed.\n";
} finally {
    $pdo->rollBack();
}
