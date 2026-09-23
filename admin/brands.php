<?php
session_start();
require_once '../includes/functions.php';
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}
if (!canAccess('view_products')) {
    http_response_code(403);
    exit('Access denied.');
}
$pageTitle = 'Brands';
$_SESSION['brand_csrf'] = $_SESSION['brand_csrf'] ?? bin2hex(random_bytes(32));
$error = '';
$name = '';
$ready = brandsSchemaReady($pdo);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (!hash_equals($_SESSION['brand_csrf'], (string)($_POST['csrf'] ?? ''))) throw new RuntimeException('Please refresh the page and try again.');
        if (!canAccess('add_product')) throw new RuntimeException('You do not have permission to create brands.');
        if (!$ready) throw new RuntimeException('Please run the brands database migration first.');
        $name = trim((string)($_POST['name'] ?? ''));
        if ($name === '' || mb_strlen($name) > 150) throw new RuntimeException('Enter a brand name of 1 to 150 characters.');
        $duplicate = $pdo->prepare('SELECT id FROM brands WHERE name = ?');
        $duplicate->execute([$name]);
        if ($duplicate->fetchColumn()) throw new RuntimeException('A brand with this name already exists.');
        $imagePath = null;
        $upload = $_FILES['image'] ?? null;
        if ($upload && $upload['error'] !== UPLOAD_ERR_NO_FILE) {
            if ($upload['error'] !== UPLOAD_ERR_OK || $upload['size'] > 5 * 1024 * 1024) throw new RuntimeException('Upload a valid image up to 5 MB.');
            $info = @getimagesize($upload['tmp_name']);
            $types = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
            if (!$info || !isset($types[$info['mime']])) throw new RuntimeException('Use a JPG, PNG or WebP image.');
            $dir = __DIR__ . '/../uploads/brands';
            if (!is_dir($dir) && !mkdir($dir, 0755, true)) throw new RuntimeException('Unable to create image directory.');
            $imagePath = 'uploads/brands/' . bin2hex(random_bytes(16)) . '.' . $types[$info['mime']];
            if (!move_uploaded_file($upload['tmp_name'], __DIR__ . '/../' . $imagePath)) throw new RuntimeException('Unable to save image.');
        }
        try {
            $stmt = $pdo->prepare('INSERT INTO brands (name, image) VALUES (?, ?)');
            $stmt->execute([$name, $imagePath]);
        } catch (Throwable $e) {
            if ($imagePath) unlink(__DIR__ . '/../' . $imagePath);
            throw $e;
        }
        $_SESSION['brand_success'] = 'Brand created successfully.';
        header('Location: brands.php');
        exit;
    } catch (PDOException $e) {
        error_log('Brand creation failed: ' . $e->getMessage());
        $error = $e->getCode() === '23000' ? 'A brand with this name already exists.' : 'Unable to save brand. Please try again.';
    } catch (RuntimeException $e) {
        $error = $e->getMessage();
    }
}
$brands = getBrands($pdo);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Brands - Admin</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
<link href="assets/css/admin.css" rel="stylesheet">
</head>
<body>
<div class="everythingb2c-admin-container">
<?php include 'includes/sidebar.php'; ?>
<div class="everythingb2c-main-content">
<?php include 'includes/header.php'; ?>
<div class="container-fluid p-4">
<?php if (!$ready): ?><div class="alert alert-warning">Run database/migrate_brands.php from the command line to enable brands.</div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
<?php if (isset($_SESSION['brand_success'])): ?><div class="alert alert-success"><?php echo htmlspecialchars($_SESSION['brand_success']); unset($_SESSION['brand_success']); ?></div><?php endif; ?>
<?php if ($ready && canAccess('add_product')): ?>
<div class="card mb-4"><div class="card-body">
<h5>Add Brand</h5>
<form method="post" enctype="multipart/form-data">
<input type="hidden" name="csrf" value="<?php echo htmlspecialchars($_SESSION['brand_csrf']); ?>">
<div class="mb-3"><label for="brandName" class="form-label">Brand name *</label><input id="brandName" name="name" class="form-control" maxlength="150" required value="<?php echo htmlspecialchars($name); ?>"></div>
<div class="mb-3"><label for="brandImage" class="form-label">Image (optional)</label><input id="brandImage" type="file" name="image" accept="image/jpeg,image/png,image/webp" class="form-control"><small class="text-muted">JPG, PNG or WebP, up to 5 MB.</small></div>
<button class="btn btn-primary" type="submit">Save Brand</button>
</form></div></div>
<?php endif; ?>
<div class="card"><div class="card-body"><h5>Brands (<?php echo count($brands); ?>)</h5>
<div class="table-responsive"><table class="table align-middle"><thead><tr><th>Image</th><th>Name</th></tr></thead><tbody>
<?php foreach ($brands as $brand): ?><tr><td><?php if ($brand['image']): ?><img src="../<?php echo htmlspecialchars($brand['image']); ?>" alt="<?php echo htmlspecialchars($brand['name']); ?>" width="64" height="64" style="object-fit:contain"><?php else: ?><span class="text-muted">No image</span><?php endif; ?></td><td><?php echo htmlspecialchars($brand['name']); ?></td></tr><?php endforeach; ?>
<?php if (!$brands): ?><tr><td colspan="2" class="text-muted">No brands created yet.</td></tr><?php endif; ?>
</tbody></table></div></div></div>
</div></div></div>
<script src="assets/js/admin.js"></script>
</body></html>
