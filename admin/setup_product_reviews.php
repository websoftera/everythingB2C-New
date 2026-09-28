<?php
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/includes/auth-check.php';
checkAdminPermission('manage_roles');
require_once __DIR__ . '/../database/product_reviews_migration.php';

$pageTitle = 'Initialize Product Reviews';
$csrfKey = 'product_reviews_migration_csrf';
if (empty($_SESSION[$csrfKey])) {
    $_SESSION[$csrfKey] = bin2hex(random_bytes(32));
}
$csrf = $_SESSION[$csrfKey];
$error = '';
$success = $_SESSION['product_reviews_migration_success'] ?? '';
unset($_SESSION['product_reviews_migration_success']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $submittedToken = (string)($_POST['csrf'] ?? '');
    if (!hash_equals($csrf, $submittedToken)) {
        $error = 'Your session expired. Refresh this page and try again.';
    } else {
        try {
            migrateProductReviewsUp($pdo);
            $pdo->query('SELECT 1 FROM product_reviews LIMIT 0');
            $_SESSION['product_reviews_migration_success'] = 'Product Reviews is ready. You can now open Reviews from the admin menu.';
            header('Location: setup_product_reviews.php');
            exit;
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            error_log('Browser product reviews migration failed: ' . $e->getMessage());
            $error = 'Setup could not complete. Check the server PHP error log or contact your hosting administrator.';
        }
    }
}

$isReady = false;
try {
    $pdo->query('SELECT 1 FROM product_reviews LIMIT 0');
    $isReady = true;
} catch (Throwable $e) {
    $isReady = false;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8'); ?> - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="assets/css/admin.css" rel="stylesheet">
</head>
<body>
<div class="everythingb2c-admin-container">
    <?php include __DIR__ . '/includes/sidebar.php'; ?>
    <div class="everythingb2c-main-content">
        <?php include __DIR__ . '/includes/header.php'; ?>
        <main class="container-fluid p-3 p-lg-4">
            <div class="card shadow-sm" style="max-width:780px">
                <div class="card-body p-4">
                    <h1 class="h4 mb-3">Initialize Product Reviews</h1>
                    <p>This one-time setup creates the review database table and enables the Reviews permission for administrator roles. It runs against the database configured for this website.</p>
                    <?php if ($success): ?><div class="alert alert-success" role="status"><?php echo htmlspecialchars($success, ENT_QUOTES, 'UTF-8'); ?></div><?php endif; ?>
                    <?php if ($error): ?><div class="alert alert-danger" role="alert"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div><?php endif; ?>
                    <?php if ($isReady): ?>
                        <div class="alert alert-success"><i class="fas fa-check-circle me-2"></i>The product review table is available.</div>
                        <p class="text-muted">You can safely run setup again if the Reviews menu permission was not installed. Existing reviews are preserved.</p>
                    <?php else: ?>
                        <div class="alert alert-warning"><i class="fas fa-circle-exclamation me-2"></i>The product review table is not available yet. Run setup to create it.</div>
                    <?php endif; ?>
                    <form method="post" onsubmit="return confirm('Run the Product Reviews database setup now? Existing review data will be preserved.');">
                        <input type="hidden" name="csrf" value="<?php echo htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8'); ?>">
                        <button class="btn btn-primary" type="submit"><i class="fas fa-database me-2"></i><?php echo $isReady ? 'Run Setup / Repair Permissions' : 'Run Product Reviews Setup'; ?></button>
                        <?php if ($isReady): ?><a class="btn btn-outline-secondary ms-2" href="reviews.php">Open Reviews</a><?php endif; ?>
                    </form>
                </div>
            </div>
        </main>
    </div>
</div>
<script src="assets/js/admin.js"></script>
</body>
</html>
