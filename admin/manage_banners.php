<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

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
        title VARCHAR(255) DEFAULT NULL,
        is_active TINYINT DEFAULT 1,
        order_index INT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}

// Processing Form Submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    
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
        $order_index = (int)($_POST['order_index'] ?? 0);
    
    // Directory where images will be saved
    $targetDir = "../uploads/banners/";
    if (!file_exists($targetDir)) {
        mkdir($targetDir, 0777, true);
    }
    
    // Check if an image was uploaded
    if (isset($_FILES["banner_image"]) && $_FILES["banner_image"]["error"] == 0) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        $fileType = $_FILES['banner_image']['type'];
        
        if (in_array($fileType, $allowedTypes)) {
            $fileName = uniqid() . '-' . basename($_FILES["banner_image"]["name"]);
            $targetFilePath = $targetDir . $fileName;
            
            if (move_uploaded_file($_FILES["banner_image"]["tmp_name"], $targetFilePath)) {
                $dbPath = "uploads/banners/" . $fileName; // Path relative to application root
                
                $stmt = $pdo->prepare("INSERT INTO banners (image_path, title, order_index) VALUES (?, ?, ?)");
                if ($stmt->execute([$dbPath, $title, $order_index])) {
                    $_SESSION['success_message'] = "Banner added successfully.";
                } else {
                    $_SESSION['error_message'] = "Failed to insert banner data.";
                }
            } else {
                $_SESSION['error_message'] = "Failed to upload image.";
            }
        } else {
            $_SESSION['error_message'] = "Only JPG, PNG, WEBP, and GIF files are allowed.";
        }
    } else {
        $_SESSION['error_message'] = "Please select an image to upload.";
    }
    header("Location: manage_banners.php");
    exit;
}
}

// Processing Deletion
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = $_GET['delete'];
    
    // Get image path to delete file
    $stmt = $pdo->prepare("SELECT image_path FROM banners WHERE id = ?");
    $stmt->execute([$id]);
    $banner = $stmt->fetch();
    
    if ($banner) {
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="assets/css/admin.css" rel="stylesheet">
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
                                            <th style="width: 100px;">Preview</th>
                                            <th style="width: 80px;">Order</th>
                                            <th>Title</th>
                                            <th>Status</th>
                                            <th>Date Added</th>
                                            <th style="width: 150px;">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($banners)): ?>
                                            <tr>
                                                <td colspan="6" class="text-center">No banners found. Replace homepage hardcoded images by adding a new banner here.</td>
                                            </tr>
                                        <?php else: ?>
                                            <?php foreach ($banners as $banner): ?>
                                                <tr>
                                                    <td>
                                                        <img src="../<?php echo htmlspecialchars($banner['image_path']); ?>" alt="Banner" class="img-fluid rounded" style="max-height: 50px;">
                                                    </td>
                                                    <td>
                                                        <input type="number" name="display_order[<?php echo $banner['id']; ?>]" value="<?php echo $banner['order_index']; ?>" class="form-control px-1" style="width: 60px;">
                                                    </td>
                                                    <td><?php echo htmlspecialchars($banner['title']); ?></td>
                                                    <td>
                                                        <a href="manage_banners.php?toggle=<?php echo $banner['id']; ?>" class="badge bg-<?php echo $banner['is_active'] ? 'success' : 'secondary'; ?>">
                                                            <?php echo $banner['is_active'] ? 'Active' : 'Inactive'; ?>
                                                        </a>
                                                    </td>
                                                    <td><?php echo date('M d, Y', strtotime($banner['created_at'])); ?></td>
                                                    <td>
                                                        <a href="manage_banners.php?toggle=<?php echo $banner['id']; ?>" class="btn btn-sm btn-<?php echo $banner['is_active'] ? 'warning' : 'success'; ?> me-1" title="<?php echo $banner['is_active'] ? 'Deactivate' : 'Activate'; ?>">
                                                            <i class="fas fa-<?php echo $banner['is_active'] ? 'ban' : 'check'; ?>"></i>
                                                        </a>
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

    <!-- Add Banner Modal -->
    <div class="modal fade" id="addBannerModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="manage_banners.php" method="POST" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title">Add New Banner</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="action" value="add">
                        
                        <div class="mb-3">
                            <label for="title" class="form-label">Banner Title (Optional)</label>
                            <input type="text" class="form-control" id="title" name="title">
                        </div>
                        
                        <div class="mb-3">
                            <label for="order_index" class="form-label">Display Order</label>
                            <input type="number" class="form-control" id="order_index" name="order_index" value="0">
                            <div class="form-text">Set the order (1, 2, 3). Lower numbers appear first.</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="banner_image" class="form-label">Upload Image <span class="text-danger">*</span></label>
                            <input type="file" class="form-control" id="banner_image" name="banner_image" accept="image/*" required>
                            <div class="form-text">Recommended size: 1920x600 pixels. Max size: 2MB.</div>
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
</body>
</html>
