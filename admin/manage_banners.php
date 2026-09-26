<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';
require_once '../includes/banner_button.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

// Ensure the setup is complete (create banners table)
try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS banners (
        id INT AUTO_INCREMENT PRIMARY KEY,
        image_path VARCHAR(255) NOT NULL,
        mobile_image_path VARCHAR(255) DEFAULT NULL,
        title VARCHAR(255) DEFAULT NULL,
        is_active TINYINT DEFAULT 1,
        order_index INT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    if (!$pdo->query("SHOW COLUMNS FROM banners LIKE 'mobile_image_path'")->fetch()) {
        $pdo->exec("ALTER TABLE banners ADD COLUMN mobile_image_path VARCHAR(255) DEFAULT NULL AFTER image_path");
    }
    if (!$pdo->query("SHOW COLUMNS FROM banners LIKE 'button_config'")->fetch()) {
        $pdo->exec("ALTER TABLE banners ADD COLUMN button_config TEXT DEFAULT NULL");
    }
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}

function uploadBannerImage($fileInputName, &$errorMessage)
{
    $targetDir = "../uploads/banners/";
    if (!file_exists($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    if (!isset($_FILES[$fileInputName]) || $_FILES[$fileInputName]["error"] !== 0) {
        $errorMessage = "Please select an image to upload.";
        return null;
    }

    $allowedTypes = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp', 'image/gif' => 'gif'];
    $fileType = (new finfo(FILEINFO_MIME_TYPE))->file($_FILES[$fileInputName]['tmp_name']);

    if (!isset($allowedTypes[$fileType]) || !getimagesize($_FILES[$fileInputName]['tmp_name'])) {
        $errorMessage = "Only JPG, PNG, WEBP, and GIF files are allowed.";
        return null;
    }

    if ($_FILES[$fileInputName]['size'] > 2 * 1024 * 1024) {
        $errorMessage = 'Each banner image must be 2MB or smaller.';
        return null;
    }
    $fileName = bin2hex(random_bytes(16)) . '.' . $allowedTypes[$fileType];
    $targetFilePath = $targetDir . $fileName;

    if (!move_uploaded_file($_FILES[$fileInputName]["tmp_name"], $targetFilePath)) {
        $errorMessage = "Failed to upload image.";
        return null;
    }

    return "uploads/banners/" . $fileName;
}

// Processing Form Submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if (in_array($_POST['action'], ['add', 'edit'], true)) {
        try { $buttonConfig = json_encode(validateBannerButton($_POST['button'] ?? []), JSON_UNESCAPED_SLASHES); }
        catch (InvalidArgumentException $e) {
            $_SESSION['error_message'] = $e->getMessage();
            header('Location: manage_banners.php');
            exit;
        }
    }

    if ($_POST['action'] === 'update_order') {
        if (isset($_POST['display_order']) && is_array($_POST['display_order'])) {
            $stmt = $pdo->prepare("UPDATE banners SET order_index = ? WHERE id = ?");
            foreach ($_POST['display_order'] as $id => $order) {
                $stmt->execute([(int)$order, (int)$id]);
            }
            $_SESSION['success_message'] = "Banner display orders updated.";
        }
        header("Location: manage_banners.php");
        exit;
    }

    if ($_POST['action'] === 'add') {
        $title = sanitizeInput($_POST['title'] ?? '');
        $nextOrderStmt = $pdo->query("SELECT COALESCE(MAX(order_index), 0) + 1 FROM banners");
        $order_index = (int)$nextOrderStmt->fetchColumn();

        $uploadError = '';
        $dbPath = uploadBannerImage('banner_image', $uploadError);

        if ($dbPath) {
            $mobilePath = null;
            if (isset($_FILES['mobile_banner_image']) && $_FILES['mobile_banner_image']['error'] !== UPLOAD_ERR_NO_FILE) {
                $mobilePath = uploadBannerImage('mobile_banner_image', $uploadError);
                if (!$mobilePath) {
                    unlink('../' . $dbPath);
                    $_SESSION['error_message'] = $uploadError;
                    header('Location: manage_banners.php');
                    exit;
                }
            }
            $stmt = $pdo->prepare("INSERT INTO banners (image_path, mobile_image_path, title, order_index, button_config) VALUES (?, ?, ?, ?, ?)");
            if ($stmt->execute([$dbPath, $mobilePath, $title, $order_index, $buttonConfig])) {
                $_SESSION['success_message'] = "Banner added successfully.";
            } else {
                $_SESSION['error_message'] = "Failed to insert banner data.";
            }
        } else {
            $_SESSION['error_message'] = $uploadError;
        }

        header("Location: manage_banners.php");
        exit;
    }

    if ($_POST['action'] === 'edit') {
        $id = (int)($_POST['banner_id'] ?? 0);
        $title = sanitizeInput($_POST['edit_title'] ?? '');

        $stmt = $pdo->prepare("SELECT image_path, mobile_image_path FROM banners WHERE id = ?");
        $stmt->execute([$id]);
        $banner = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$banner) {
            $_SESSION['error_message'] = "Banner not found.";
            header("Location: manage_banners.php");
            exit;
        }

        $imagePath = $banner['image_path'];
        if (isset($_FILES['edit_banner_image']) && $_FILES['edit_banner_image']['error'] !== UPLOAD_ERR_NO_FILE) {
            $uploadError = '';
            $newImagePath = uploadBannerImage('edit_banner_image', $uploadError);

            if (!$newImagePath) {
                $_SESSION['error_message'] = $uploadError;
                header("Location: manage_banners.php");
                exit;
            }

            $imagePath = $newImagePath;
        }

        $mobilePath = !empty($_POST['remove_mobile_image']) ? null : $banner['mobile_image_path'];
        if (isset($_FILES['edit_mobile_banner_image']) && $_FILES['edit_mobile_banner_image']['error'] !== UPLOAD_ERR_NO_FILE) {
            $uploadError = '';
            $mobilePath = uploadBannerImage('edit_mobile_banner_image', $uploadError);
            if (!$mobilePath) {
                if ($imagePath !== $banner['image_path']) unlink('../' . $imagePath);
                $_SESSION['error_message'] = $uploadError;
                header('Location: manage_banners.php');
                exit;
            }
        }
        $stmt = $pdo->prepare("UPDATE banners SET image_path = ?, mobile_image_path = ?, title = ?, button_config = ? WHERE id = ?");
        if ($stmt->execute([$imagePath, $mobilePath, $title, $buttonConfig, $id])) {
            foreach (['image_path' => $imagePath, 'mobile_image_path' => $mobilePath] as $key => $newPath) {
                if (!empty($banner[$key]) && $banner[$key] !== $newPath && is_file('../' . $banner[$key])) unlink('../' . $banner[$key]);
            }
            $_SESSION['success_message'] = "Banner updated successfully.";
        } else {
            $_SESSION['error_message'] = "Failed to update banner.";
        }

        header("Location: manage_banners.php");
        exit;
    }
}

// Processing Deletion
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = $_GET['delete'];

    // Get image path to delete file
    $stmt = $pdo->prepare("SELECT image_path, mobile_image_path FROM banners WHERE id = ?");
    $stmt->execute([$id]);
    $banner = $stmt->fetch();

    if ($banner) {
        if (!empty($banner['mobile_image_path']) && is_file('../' . $banner['mobile_image_path'])) {
            unlink('../' . $banner['mobile_image_path']);
        }
        $filePath = "../" . $banner['image_path'];
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        $delStmt = $pdo->prepare("DELETE FROM banners WHERE id = ?");
        $delStmt->execute([$id]);
        $_SESSION['success_message'] = "Banner deleted successfully.";
    }
    header("Location: manage_banners.php");
    exit;
}

// Processing Active Status Toggle
if (isset($_GET['toggle']) && is_numeric($_GET['toggle'])) {
    $id = $_GET['toggle'];
    $stmt = $pdo->prepare("UPDATE banners SET is_active = NOT is_active WHERE id = ?");
    $stmt->execute([$id]);
    $_SESSION['success_message'] = "Banner status updated.";
    header("Location: manage_banners.php");
    exit;
}

// Fetch all banners
$stmt = $pdo->query("SELECT * FROM banners ORDER BY order_index ASC, id DESC");
$banners = $stmt->fetchAll(PDO::FETCH_ASSOC);

$pageTitle = 'Manage Banners';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - EverythingB2C</title>

    <link rel="icon" href="../sitelogo.png" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="assets/css/admin.css" rel="stylesheet">
    <style>
        .banner-button-fields .banner-button-main-fields > *,
        .banner-button-fields .banner-button-placement-fields > * { min-width: 0; }
        .banner-button-fields .form-control { width: 100%; min-width: 0; }
        .banner-button-fields .banner-button-placement-fields > label { font-size: .9rem; }
        #editBannerModal .modal-dialog,
        #addBannerModal .modal-dialog { max-width: 900px; }
        #editBannerModal .modal-content,
        #addBannerModal .modal-content { max-height: calc(100vh - 2rem); }
        #editBannerModal .modal-body,
        #addBannerModal .modal-body { overflow-y: auto; }
        #addBannerModal .modal-footer { position: sticky; bottom: 0; z-index: 2; background: #fff; }
        #editBannerModal .modal-footer { position: sticky; bottom: 0; z-index: 2; background: #fff; }
        #editBannerModal .banner-button-fields { padding: .65rem !important; margin-bottom: .75rem !important; }
        #addBannerModal .banner-button-fields { padding: .65rem !important; margin-bottom: .75rem !important; }
        #editBannerModal .banner-button-fields .form-text { margin-top: .35rem; }
        #addBannerModal .banner-button-fields .form-text { margin-top: .35rem; }
        #editBannerModal .banner-button-preview,
        #addBannerModal .banner-button-preview { display: none !important; }
        #editBannerModal .form-control { padding-top: .35rem; padding-bottom: .35rem; }
        #addBannerModal .form-control { padding-top: .35rem; padding-bottom: .35rem; }
        #editBannerModal .mb-3 { margin-bottom: .75rem !important; }
        #addBannerModal .mb-3 { margin-bottom: .75rem !important; }
        #editBannerModal .banner-image-previews img { display: block; width: 100%; height: 96px; object-fit: contain; background: #f6f7f8; }
        #addBannerModal .banner-image-previews img { display: block; width: 100%; height: 96px; object-fit: contain; background: #f6f7f8; }
        #editBannerModal .banner-image-previews img[hidden],
        #addBannerModal .banner-image-previews img[hidden] { display: none !important; }
        #editBannerModal #edit_mobile_banner_fallback[hidden] { display: none !important; }
        @media (max-width: 575.98px) {
            #editBannerModal .modal-dialog { margin: .5rem; }
            #addBannerModal .modal-dialog { margin: .5rem; }
            #editBannerModal .banner-image-previews img { height: 76px; }
            #addBannerModal .banner-image-previews img { height: 76px; }
        }
        .banner-drag-handle {
            cursor: move;
            color: #6c757d;
        }

        .banner-row.dragging {
            opacity: 0.55;
        }

        .banner-row.drag-over {
            outline: 2px dashed #0d6efd;
            outline-offset: -4px;
        }

        .banner-status-badge,
        .banner-status-badge:hover,
        .banner-status-badge:focus {
            color: #fff !important;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="everythingb2c-admin-container">
        <!-- Sidebar -->
        <?php include 'includes/sidebar.php'; ?>

        <!-- Main Content -->
        <div class="everythingb2c-main-content">
            <!-- Header -->
            <?php include 'includes/header.php'; ?>

            <!-- Dashboard Content -->
            <div class="everythingb2c-dashboard-content">
                <div class="container-fluid">
                    <div class="row mb-4">
                        <div class="col-12 d-flex justify-content-between align-items-center">
                            <h1 class="h3 mb-0 text-gray-800">Manage Banners</h1>
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addBannerModal">
                                <i class="fas fa-plus"></i> Add New Banner
                            </button>
                        </div>
                    </div>

                    <!-- Flash Messages -->
                    <?php if (isset($_SESSION['success_message'])): ?>
                        <div class="alert alert-success alert-dismissible fade show">
                            <?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    <?php if (isset($_SESSION['error_message'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show">
                            <?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Banner List</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <form action="manage_banners.php" method="POST">
                                    <input type="hidden" name="action" value="update_order">
                                <table class="table table-bordered" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th style="width: 48px;">Move</th>
                                            <th style="width: 100px;">Preview</th>
                                            <th>Mobile Preview</th>
                                            <th>Title</th>
                                            <th>Status</th>
                                            <th>Date Added</th>
                                            <th style="width: 190px;">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="bannerTableBody">
                                        <?php if (empty($banners)): ?>
                                            <tr>
                                                <td colspan="7" class="text-center">No banners found. Replace homepage hardcoded images by adding a new banner here.</td>
                                            </tr>
                                        <?php else: ?>
                                            <?php foreach ($banners as $banner): ?>
                                                <tr class="banner-row" draggable="true" data-banner-id="<?php echo $banner['id']; ?>">
                                                    <td class="text-center align-middle">
                                                        <span class="banner-drag-handle" title="Drag to change position">
                                                            <i class="fas fa-grip-vertical"></i>
                                                        </span>
                                                        <input type="hidden" name="display_order[<?php echo $banner['id']; ?>]" value="<?php echo $banner['order_index']; ?>" class="banner-order-input">
                                                    </td>
                                                    <td>
                                                        <img src="../<?php echo htmlspecialchars($banner['image_path']); ?>" alt="Banner" class="img-fluid rounded" style="max-height: 50px;">
                                                    </td>
                                                    <td>
                                                        <?php if (!empty($banner['mobile_image_path'])): ?>
                                                            <img src="../<?php echo htmlspecialchars($banner['mobile_image_path']); ?>" alt="Mobile banner" style="max-width: 110px; max-height: 70px;">
                                                        <?php else: ?><small class="text-muted">Uses desktop image</small><?php endif; ?>
                                                    </td>
                                                    <td><?php echo htmlspecialchars($banner['title']); ?></td>
                                                    <td>
                                                        <a href="manage_banners.php?toggle=<?php echo $banner['id']; ?>" class="badge banner-status-badge bg-<?php echo $banner['is_active'] ? 'success' : 'secondary'; ?>">
                                                            <?php echo $banner['is_active'] ? 'Active' : 'Inactive'; ?>
                                                        </a>
                                                    </td>
                                                    <td><?php echo date('M d, Y', strtotime($banner['created_at'])); ?></td>
                                                    <td>
                                                        <a href="manage_banners.php?toggle=<?php echo $banner['id']; ?>" class="btn btn-sm btn-<?php echo $banner['is_active'] ? 'warning' : 'success'; ?> me-1" title="<?php echo $banner['is_active'] ? 'Deactivate' : 'Activate'; ?>">
                                                            <i class="fas fa-<?php echo $banner['is_active'] ? 'ban' : 'check'; ?>"></i>
                                                        </a>
                                                        <button type="button"
                                                            class="btn btn-sm btn-primary me-1 edit-banner-btn"
                                                            title="Edit"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#editBannerModal"
                                                            data-banner-id="<?php echo $banner['id']; ?>"
                                                            data-banner-title="<?php echo htmlspecialchars($banner['title'], ENT_QUOTES); ?>"
                                                            data-banner-image="../<?php echo htmlspecialchars($banner['image_path'], ENT_QUOTES); ?>"
                                                            data-banner-mobile="<?php echo !empty($banner['mobile_image_path']) ? '../' . htmlspecialchars($banner['mobile_image_path'], ENT_QUOTES) : ''; ?>"
                                                            data-banner-button="<?php echo htmlspecialchars(json_encode(getBannerButton($banner['button_config'] ?? null)), ENT_QUOTES, 'UTF-8'); ?>">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        <a href="manage_banners.php?delete=<?php echo $banner['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this banner?');" title="Delete">
                                                            <i class="fas fa-trash"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                                <?php if (!empty($banners)): ?>
                                    <div class="mt-2 mb-2 text-end">
                                        <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Save Display Order</button>
                                    </div>
                                <?php endif; ?>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Banner Modal -->
    <div class="modal fade" id="editBannerModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <form action="manage_banners.php" method="POST" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Banner</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="action" value="edit">
                        <input type="hidden" name="banner_id" id="edit_banner_id">
                        <?php include 'includes/banner-button-fields.php'; ?>

                        <div class="mb-3">
                            <label for="edit_title" class="form-label">Banner Title (Optional)</label>
                            <input type="text" class="form-control" id="edit_title" name="edit_title">
                        </div>

                        <div class="row g-2 mb-3 banner-image-previews">
                            <div class="col-6">
                                <label class="form-label">Desktop Image</label>
                                <img src="" alt="Current desktop banner" id="edit_banner_preview" class="rounded border">
                                <label for="edit_banner_image" class="form-label mt-2 mb-1">Replace desktop image</label>
                                <input type="file" class="form-control" id="edit_banner_image" name="edit_banner_image" accept="image/*">
                                <div class="form-text">Leave empty to keep the current image.</div>
                            </div>
                            <div class="col-6">
                                <label class="form-label">Mobile Image</label>
                                <img id="edit_mobile_banner_preview" alt="Current mobile banner" class="rounded border" hidden>
                                <div id="edit_mobile_banner_fallback" class="border rounded d-flex align-items-center justify-content-center text-muted small" style="height:96px;background:#f6f7f8;">Uses desktop image</div>
                                <label for="edit_mobile_banner_image" class="form-label mt-2 mb-1">Replace mobile image</label>
                                <input type="file" class="form-control" id="edit_mobile_banner_image" name="edit_mobile_banner_image" accept="image/jpeg,image/png,image/webp,image/gif">
                                <div class="form-text">Leave empty to keep the current image.</div>
                                <div class="form-check mt-2">
                                    <input type="checkbox" class="form-check-input" id="remove_mobile_image" name="remove_mobile_image" value="1">
                                    <label class="form-check-label" for="remove_mobile_image">Use desktop image instead</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Banner</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Add Banner Modal -->
    <div class="modal fade" id="addBannerModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <form action="manage_banners.php" method="POST" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title">Add New Banner</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="action" value="add">
                        <?php include 'includes/banner-button-fields.php'; ?>

                        <div class="mb-3">
                            <label for="title" class="form-label">Banner Title (Optional)</label>
                            <input type="text" class="form-control" id="title" name="title">
                        </div>

                        <div class="row g-2 mb-3 banner-image-previews">
                            <div class="col-6">
                                <label class="form-label" for="banner_image">Desktop Image <span class="text-danger">*</span></label>
                                <img id="add_banner_preview" class="rounded border mb-2" alt="Desktop banner preview" hidden>
                                <input type="file" class="form-control" id="banner_image" name="banner_image" accept="image/*" required>
                                <div class="form-text">Required. Recommended: 1920 × 600px, max 2MB.</div>
                            </div>
                            <div class="col-6">
                                <label class="form-label" for="mobile_banner_image">Mobile Image (Optional)</label>
                                <img id="add_mobile_banner_preview" class="rounded border mb-2" alt="Mobile banner preview" hidden>
                                <input type="file" class="form-control" id="mobile_banner_image" name="mobile_banner_image" accept="image/jpeg,image/png,image/webp,image/gif">
                                <div class="form-text">Optional. Recommended: 800 × 370px, max 2MB. Without one, mobile uses the desktop image.</div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Banner</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/admin.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            function updateButtonPreview(form, imageSrc) {
                var preview = form.querySelector('.banner-button-preview');
                if (imageSrc) { preview.querySelector('img').src = imageSrc; preview.hidden = false; }
                var button = preview.querySelector('span');
                button.style.display = form.elements['button[enabled]'].checked ? 'flex' : 'none';
                button.textContent = form.elements['button[text]'].value;
                ['left', 'top', 'width', 'height'].forEach(function (key) { button.style[key] = form.elements['button[' + key + ']'].value + '%'; });
            }
            document.querySelectorAll('.banner-button-fields').forEach(function (fields) {
                var form = fields.closest('form');
                fields.addEventListener('input', function () { updateButtonPreview(form); });
                form.querySelector('input[name="banner_image"], input[name="edit_banner_image"]').addEventListener('change', function () {
                    if (!this.files[0]) return;
                    var reader = new FileReader();
                    reader.onload = function () { updateButtonPreview(form, reader.result); };
                    reader.readAsDataURL(this.files[0]);
                });
            });
            [
                ['banner_image', 'add_banner_preview'],
                ['mobile_banner_image', 'add_mobile_banner_preview']
            ].forEach(function (previewPair) {
                var input = document.getElementById(previewPair[0]);
                var preview = document.getElementById(previewPair[1]);
                if (!input || !preview) return;
                input.addEventListener('change', function () {
                    if (!this.files[0]) { preview.removeAttribute('src'); preview.hidden = true; return; }
                    var reader = new FileReader();
                    reader.onload = function () { preview.src = reader.result; preview.hidden = false; };
                    reader.readAsDataURL(this.files[0]);
                });
            });
            var tableBody = document.getElementById('bannerTableBody');
            var draggedRow = null;

            function refreshOrderInputs() {
                if (!tableBody) {
                    return;
                }

                tableBody.querySelectorAll('.banner-row').forEach(function (row, index) {
                    var input = row.querySelector('.banner-order-input');
                    if (input) {
                        input.value = index + 1;
                    }
                });
            }

            if (tableBody) {
                tableBody.querySelectorAll('.banner-row').forEach(function (row) {
                    row.addEventListener('dragstart', function () {
                        draggedRow = row;
                        row.classList.add('dragging');
                    });

                    row.addEventListener('dragend', function () {
                        row.classList.remove('dragging');
                        tableBody.querySelectorAll('.drag-over').forEach(function (item) {
                            item.classList.remove('drag-over');
                        });
                        draggedRow = null;
                        refreshOrderInputs();
                    });

                    row.addEventListener('dragover', function (event) {
                        event.preventDefault();
                        if (row !== draggedRow) {
                            row.classList.add('drag-over');
                        }
                    });

                    row.addEventListener('dragleave', function () {
                        row.classList.remove('drag-over');
                    });

                    row.addEventListener('drop', function (event) {
                        event.preventDefault();
                        row.classList.remove('drag-over');

                        if (!draggedRow || draggedRow === row) {
                            return;
                        }

                        var rows = Array.from(tableBody.querySelectorAll('.banner-row'));
                        var draggedIndex = rows.indexOf(draggedRow);
                        var targetIndex = rows.indexOf(row);

                        if (draggedIndex < targetIndex) {
                            row.after(draggedRow);
                        } else {
                            row.before(draggedRow);
                        }

                        refreshOrderInputs();
                    });
                });

            }

            document.querySelectorAll('.edit-banner-btn').forEach(function (button) {
                button.addEventListener('click', function () {
                    document.getElementById('edit_banner_id').value = button.dataset.bannerId;
                    document.getElementById('edit_title').value = button.dataset.bannerTitle || '';
                    document.getElementById('edit_banner_preview').src = button.dataset.bannerImage || '';
                    document.getElementById('edit_banner_image').value = '';
                    var editForm = document.getElementById('edit_banner_id').form;
                    var settings = JSON.parse(button.dataset.bannerButton);
                    Object.keys(settings).forEach(function (key) {
                        var field = editForm.elements['button[' + key + ']'];
                        if (key === 'enabled') field.checked = settings[key]; else field.value = settings[key];
                    });
                    updateButtonPreview(editForm, button.dataset.bannerImage);
                    document.getElementById('edit_mobile_banner_image').value = '';
                    document.getElementById('remove_mobile_image').checked = false;
                    var mobilePreview = document.getElementById('edit_mobile_banner_preview');
                    var mobileFallback = document.getElementById('edit_mobile_banner_fallback');
                    mobilePreview.hidden = !button.dataset.bannerMobile;
                    mobileFallback.hidden = !!button.dataset.bannerMobile;
                    if (button.dataset.bannerMobile) mobilePreview.src = button.dataset.bannerMobile;
                    else mobilePreview.removeAttribute('src');
                });
            });
        });
    </script>
</body>
</html>
