<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/product_reviews.php';

header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    header('Allow: GET');
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed.']);
    exit;
}

$productId = filter_input(INPUT_GET, 'product_id', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
if (!$productId) {
    http_response_code(400);
    echo json_encode(['error' => 'A valid product_id is required.']);
    exit;
}

try {
    $product = $pdo->prepare('SELECT id FROM products WHERE id = ? LIMIT 1');
    $product->execute([$productId]);
    if (!$product->fetchColumn()) {
        http_response_code(404);
        echo json_encode(['error' => 'Product not found.']);
        exit;
    }

    $page = max(1, (int)($_GET['page'] ?? 1));
    $perPage = max(1, min(100, (int)($_GET['per_page'] ?? 20)));
    $summary = getPublicProductReviewSummary($pdo, (int)$productId);
    $reviews = getPublicProductReviews($pdo, (int)$productId, $perPage, ($page - 1) * $perPage);
    echo json_encode([
        'average_rating' => $summary['average_rating'],
        'total_reviews' => $summary['total_reviews'],
        'page' => $page,
        'per_page' => $perPage,
        'has_more' => $page * $perPage < $summary['total_reviews'],
        'reviews' => $reviews,
    ], JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
} catch (Throwable $e) {
    error_log('Public product reviews API failed: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Unable to load product reviews.']);
}

