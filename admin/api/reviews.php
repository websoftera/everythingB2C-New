<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');
require_once __DIR__ . '/../includes/auth-check.php';
checkAdminPermission('manage_reviews');
require_once __DIR__ . '/../../includes/product_reviews.php';

function reviewApiRespond(array $payload, int $status = 200): void {
    http_response_code($status);
    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
    exit;
}

try {
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $action = $_GET['action'] ?? 'list';
        if ($action === 'get') {
            $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
            $review = $id ? getAdminProductReview($pdo, (int)$id) : null;
            if (!$review) reviewApiRespond(['error' => 'Review not found.'], 404);
            reviewApiRespond(['review' => $review]);
        }
        $page = max(1, (int)($_GET['page'] ?? 1));
        $perPage = max(1, min(100, (int)($_GET['per_page'] ?? 25)));
        $filters = [
            'search' => $_GET['search'] ?? '', 'status' => $_GET['status'] ?? '', 'rating' => $_GET['rating'] ?? '',
            'verified' => $_GET['verified'] ?? '', 'product_id' => $_GET['product_id'] ?? '',
            'date_from' => $_GET['date_from'] ?? '', 'date_to' => $_GET['date_to'] ?? '',
        ];
        $result = listAdminProductReviews($pdo, $filters, $perPage, ($page - 1) * $perPage);
        reviewApiRespond(['stats' => getProductReviewStats($pdo), 'total' => $result['total'], 'page' => $page, 'reviews' => $result['reviews']]);
    }

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Allow: GET, POST');
        reviewApiRespond(['error' => 'Method not allowed.'], 405);
    }

    $contentType = strtolower((string)($_SERVER['CONTENT_TYPE'] ?? ''));
    if (strpos($contentType, 'application/json') !== false) {
        $input = json_decode(file_get_contents('php://input'), true);
        if (!is_array($input)) reviewApiRespond(['error' => 'Invalid JSON request.'], 400);
    } else {
        $input = $_POST;
    }
    $csrfToken = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? ($input['_csrf'] ?? null);
    if (!verifyProductReviewCsrfToken(is_string($csrfToken) ? $csrfToken : null)) {
        reviewApiRespond(['error' => 'Your session token expired. Refresh the page and try again.'], 403);
    }

    $action = (string)($input['action'] ?? '');
    if ($action === 'create' || $action === 'update') {
        $id = filter_var($input['id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        if ($action === 'update' && !$id) reviewApiRespond(['error' => 'A valid review id is required.'], 400);
        $savedId = saveAdminProductReview($pdo, $input, $action === 'update' ? (int)$id : null);
        reviewApiRespond(['success' => true, 'id' => $savedId, 'message' => $action === 'create' ? 'Review created successfully.' : 'Review updated successfully.']);
    }

    if ($action === 'status' || $action === 'approve' || $action === 'reject' || $action === 'spam') {
        $id = filter_var($input['id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        if (!$id) reviewApiRespond(['error' => 'A valid review id is required.'], 400);
        $status = $action === 'status' ? (string)($input['status'] ?? '') : ['approve' => 'approved', 'reject' => 'rejected', 'spam' => 'spam'][$action];
        setAdminProductReviewStatus($pdo, (int)$id, $status);
        reviewApiRespond(['success' => true, 'message' => 'Review status updated successfully.']);
    }

    if ($action === 'verified') {
        $id = filter_var($input['id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        if (!$id || !getAdminProductReview($pdo, (int)$id)) reviewApiRespond(['error' => 'Review not found.'], 404);
        $stmt = $pdo->prepare('UPDATE product_reviews SET is_verified = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?');
        $stmt->execute([!empty($input['is_verified']) ? 1 : 0, (int)$id]);
        reviewApiRespond(['success' => true, 'message' => 'Verified purchase setting updated.']);
    }

    if ($action === 'delete') {
        $id = filter_var($input['id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        if (!$id) reviewApiRespond(['error' => 'A valid review id is required.'], 400);
        deleteAdminProductReview($pdo, (int)$id);
        reviewApiRespond(['success' => true, 'message' => 'Review deleted successfully.']);
    }

    if ($action === 'bulk') {
        $ids = array_values(array_unique(array_filter(array_map('intval', (array)($input['ids'] ?? [])), static function ($id) { return $id > 0; })));
        if (!$ids || count($ids) > 500) reviewApiRespond(['error' => 'Select between 1 and 500 reviews.'], 400);
        $bulkAction = (string)($input['bulk_action'] ?? '');
        if (!in_array($bulkAction, ['approved', 'rejected', 'spam', 'delete'], true)) reviewApiRespond(['error' => 'Select a valid bulk action.'], 400);
        $pdo->beginTransaction();
        foreach ($ids as $id) {
            if ($bulkAction === 'delete') deleteAdminProductReview($pdo, $id);
            else setAdminProductReviewStatus($pdo, $id, $bulkAction);
        }
        $pdo->commit();
        reviewApiRespond(['success' => true, 'message' => count($ids) . ' reviews updated successfully.']);
    }

    reviewApiRespond(['error' => 'Unknown review action.'], 400);
} catch (InvalidArgumentException $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    reviewApiRespond(['error' => $e->getMessage()], 422);
} catch (Throwable $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    error_log('Admin product reviews API failed: ' . $e->getMessage());
    reviewApiRespond(['error' => 'Unable to process the review request.'], 500);
}

