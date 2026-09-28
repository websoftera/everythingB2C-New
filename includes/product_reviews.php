<?php

const PRODUCT_REVIEW_STATUSES = ['pending', 'approved', 'rejected', 'spam'];

function productReviewsSchemaReady(PDO $pdo): bool {
    try {
        $pdo->query('SELECT 1 FROM product_reviews LIMIT 0');
        return true;
    } catch (PDOException $e) {
        $driverCode = $e->errorInfo[1] ?? null;
        if ($e->getCode() === '42S02' || (int)$driverCode === 1146) {
            error_log('Product reviews table is missing; run database/migrate_product_reviews.php up.');
            return false;
        }
        throw $e;
    }
}

/**
 * Apply the idempotent reviews migration on the first request after deployment.
 * This is used by the product page because this project has no checked-in
 * deployment pipeline or post-deploy hook. Fail closed and let the page render
 * without reviews if the hosting database user cannot apply DDL.
 */
function ensureProductReviewsSchema(PDO $pdo): bool {
    if (productReviewsSchemaReady($pdo)) {
        return true;
    }

    try {
        require_once __DIR__ . '/../database/product_reviews_migration.php';
        migrateProductReviewsUp($pdo);
        return productReviewsSchemaReady($pdo);
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        error_log('Automatic product reviews migration failed: ' . $e->getMessage());
        return false;
    }
}

function getProductReviewCsrfToken(): string {
    if (empty($_SESSION['product_reviews_csrf'])) {
        $_SESSION['product_reviews_csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['product_reviews_csrf'];
}

function verifyProductReviewCsrfToken(?string $token): bool {
    return is_string($token) && isset($_SESSION['product_reviews_csrf']) && hash_equals($_SESSION['product_reviews_csrf'], $token);
}

function normalizeProductReviewInput(array $input): array {
    $productId = filter_var($input['product_id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    $customerId = filter_var($input['customer_id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    $rating = filter_var($input['rating'] ?? null, FILTER_VALIDATE_INT);
    $status = strtolower(trim((string)($input['status'] ?? 'pending')));
    $sellerCode = trim(strip_tags((string)($input['seller_code'] ?? '')));
    $title = trim(strip_tags((string)($input['review_title'] ?? '')));
    $content = trim(strip_tags((string)($input['review_content'] ?? '')));

    if (!$productId) throw new InvalidArgumentException('Select a valid product.');
    if (!$customerId) throw new InvalidArgumentException('Select a valid customer.');
    if ($rating === false || $rating < 1 || $rating > 5) throw new InvalidArgumentException('Rating must be between 1 and 5.');
    if (!in_array($status, PRODUCT_REVIEW_STATUSES, true)) throw new InvalidArgumentException('Select a valid review status.');
    if ($title === '' || mb_strlen($title) > 200) throw new InvalidArgumentException('Review title is required and must be 200 characters or fewer.');
    if ($content === '' || mb_strlen($content) > 10000) throw new InvalidArgumentException('Review content is required and must be 10,000 characters or fewer.');
    if ($sellerCode !== '' && !preg_match('/^[A-Za-z0-9][A-Za-z0-9_-]{0,39}$/', $sellerCode)) {
        throw new InvalidArgumentException('Seller Code can contain letters, numbers, hyphens, and underscores (up to 40 characters).');
    }
    if ($status === 'approved' && $sellerCode === '') {
        throw new InvalidArgumentException('Seller Code is required before a review can be published.');
    }

    return [
        'product_id' => (int)$productId,
        'customer_id' => (int)$customerId,
        'rating' => (int)$rating,
        'review_title' => $title,
        'review_content' => $content,
        'seller_code' => $sellerCode === '' ? null : $sellerCode,
        'is_verified' => !empty($input['is_verified']) ? 1 : 0,
        'status' => $status,
    ];
}

function getProductReviewStats(PDO $pdo): array {
    $row = $pdo->query("SELECT COUNT(*) AS total,
        COALESCE(SUM(status = 'pending'), 0) AS pending,
        COALESCE(SUM(status = 'approved'), 0) AS approved,
        COALESCE(SUM(status = 'rejected'), 0) AS rejected,
        COALESCE(SUM(status = 'spam'), 0) AS spam
        FROM product_reviews")->fetch(PDO::FETCH_ASSOC);
    return array_map('intval', $row ?: ['total' => 0, 'pending' => 0, 'approved' => 0, 'rejected' => 0, 'spam' => 0]);
}

function listAdminProductReviews(PDO $pdo, array $filters, int $limit, int $offset): array {
    $where = [];
    $params = [];
    $search = trim((string)($filters['search'] ?? ''));
    if ($search !== '') {
        $where[] = '(p.name LIKE ? OR u.name LIKE ? OR r.review_title LIKE ? OR r.review_content LIKE ? OR r.seller_code LIKE ?)';
        $pattern = '%' . $search . '%';
        array_push($params, $pattern, $pattern, $pattern, $pattern, $pattern);
    }
    if (in_array($filters['status'] ?? '', PRODUCT_REVIEW_STATUSES, true)) {
        $where[] = 'r.status = ?';
        $params[] = $filters['status'];
    }
    if (isset($filters['rating']) && ctype_digit((string)$filters['rating']) && (int)$filters['rating'] >= 1 && (int)$filters['rating'] <= 5) {
        $where[] = 'r.rating = ?';
        $params[] = (int)$filters['rating'];
    }
    if (isset($filters['verified']) && in_array((string)$filters['verified'], ['0', '1'], true)) {
        $where[] = 'r.is_verified = ?';
        $params[] = (int)$filters['verified'];
    }
    if (isset($filters['product_id']) && ctype_digit((string)$filters['product_id']) && (int)$filters['product_id'] > 0) {
        $where[] = 'r.product_id = ?';
        $params[] = (int)$filters['product_id'];
    }
    foreach (['date_from' => '>=', 'date_to' => '<='] as $key => $operator) {
        $date = (string)($filters[$key] ?? '');
        $parsed = DateTime::createFromFormat('!Y-m-d', $date);
        if ($parsed && $parsed->format('Y-m-d') === $date) {
            $where[] = 'DATE(r.created_at) ' . $operator . ' ?';
            $params[] = $date;
        }
    }
    $sqlWhere = $where ? ' WHERE ' . implode(' AND ', $where) : '';
    $count = $pdo->prepare('SELECT COUNT(*) FROM product_reviews r LEFT JOIN products p ON p.id = r.product_id LEFT JOIN users u ON u.id = r.customer_id' . $sqlWhere);
    $count->execute($params);

    $stmt = $pdo->prepare('SELECT r.id, r.product_id, r.customer_id, r.seller_code, r.rating, r.review_title, r.review_content,
        r.is_verified, r.status, r.created_at, r.updated_at, r.approved_at, p.name AS product_name, p.slug AS product_slug, u.name AS customer_name
        FROM product_reviews r
        LEFT JOIN products p ON p.id = r.product_id
        LEFT JOIN users u ON u.id = r.customer_id' . $sqlWhere . '
        ORDER BY r.created_at DESC, r.id DESC LIMIT ? OFFSET ?');
    foreach ($params as $index => $value) $stmt->bindValue($index + 1, $value);
    $stmt->bindValue(count($params) + 1, max(1, min(100, $limit)), PDO::PARAM_INT);
    $stmt->bindValue(count($params) + 2, max(0, $offset), PDO::PARAM_INT);
    $stmt->execute();
    return ['total' => (int)$count->fetchColumn(), 'reviews' => $stmt->fetchAll(PDO::FETCH_ASSOC)];
}

function getAdminProductReview(PDO $pdo, int $id): ?array {
    $stmt = $pdo->prepare('SELECT r.*, p.name AS product_name, u.name AS customer_name
        FROM product_reviews r LEFT JOIN products p ON p.id = r.product_id LEFT JOIN users u ON u.id = r.customer_id
        WHERE r.id = ? LIMIT 1');
    $stmt->execute([$id]);
    $review = $stmt->fetch(PDO::FETCH_ASSOC);
    return $review ?: null;
}

function saveAdminProductReview(PDO $pdo, array $input, ?int $reviewId = null): int {
    $review = normalizeProductReviewInput($input);
    $productCheck = $pdo->prepare('SELECT 1 FROM products WHERE id = ? LIMIT 1');
    $productCheck->execute([$review['product_id']]);
    if (!$productCheck->fetchColumn()) throw new InvalidArgumentException('Select an existing product.');
    $customerCheck = $pdo->prepare("SELECT 1 FROM users WHERE id = ? AND user_role = 'customer' LIMIT 1");
    $customerCheck->execute([$review['customer_id']]);
    if (!$customerCheck->fetchColumn()) throw new InvalidArgumentException('Select an existing customer account.');
    if ($reviewId) {
        if (!getAdminProductReview($pdo, $reviewId)) throw new RuntimeException('Review not found.');
        $stmt = $pdo->prepare('UPDATE product_reviews SET product_id = ?, customer_id = ?, seller_code = ?, rating = ?, review_title = ?, review_content = ?, is_verified = ?, status = ?, approved_at = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?');
        $stmt->execute([
            $review['product_id'], $review['customer_id'], $review['seller_code'], $review['rating'], $review['review_title'],
            $review['review_content'], $review['is_verified'], $review['status'], $review['status'] === 'approved' ? date('Y-m-d H:i:s') : null, $reviewId,
        ]);
        return $reviewId;
    }
    $stmt = $pdo->prepare('INSERT INTO product_reviews (product_id, customer_id, seller_code, rating, review_title, review_content, is_verified, status, approved_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');
    $stmt->execute([
        $review['product_id'], $review['customer_id'], $review['seller_code'], $review['rating'], $review['review_title'],
        $review['review_content'], $review['is_verified'], $review['status'], $review['status'] === 'approved' ? date('Y-m-d H:i:s') : null,
    ]);
    return (int)$pdo->lastInsertId();
}

function setAdminProductReviewStatus(PDO $pdo, int $reviewId, string $status): void {
    if (!in_array($status, PRODUCT_REVIEW_STATUSES, true)) throw new InvalidArgumentException('Select a valid review status.');
    $review = getAdminProductReview($pdo, $reviewId);
    if (!$review) throw new RuntimeException('Review not found.');
    if ($status === 'approved' && trim((string)$review['seller_code']) === '') {
        throw new InvalidArgumentException('Seller Code is required before a review can be published.');
    }
    $stmt = $pdo->prepare('UPDATE product_reviews SET status = ?, approved_at = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?');
    $stmt->execute([$status, $status === 'approved' ? date('Y-m-d H:i:s') : null, $reviewId]);
}

function deleteAdminProductReview(PDO $pdo, int $reviewId): void {
    $stmt = $pdo->prepare('DELETE FROM product_reviews WHERE id = ?');
    $stmt->execute([$reviewId]);
}

function getPublicProductReviewSummary(PDO $pdo, int $productId): array {
    $stmt = $pdo->prepare("SELECT COUNT(*) AS total_reviews, COALESCE(AVG(rating), 0) AS average_rating
        FROM product_reviews WHERE product_id = ? AND status = 'approved' AND seller_code IS NOT NULL AND seller_code <> ''");
    $stmt->execute([$productId]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
    return ['total_reviews' => (int)($row['total_reviews'] ?? 0), 'average_rating' => round((float)($row['average_rating'] ?? 0), 1)];
}

function getPublicProductReviews(PDO $pdo, int $productId, int $limit = 20, int $offset = 0): array {
    $stmt = $pdo->prepare("SELECT rating, review_title, review_content, seller_code, is_verified, DATE_FORMAT(created_at, '%Y-%m-%d') AS created_at
        FROM product_reviews
        WHERE product_id = ? AND status = 'approved' AND seller_code IS NOT NULL AND seller_code <> ''
        ORDER BY created_at DESC, id DESC LIMIT ? OFFSET ?");
    $stmt->bindValue(1, $productId, PDO::PARAM_INT);
    $stmt->bindValue(2, max(1, min(100, $limit)), PDO::PARAM_INT);
    $stmt->bindValue(3, max(0, $offset), PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
