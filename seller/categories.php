<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';
require_once '../includes/seller_functions.php';

if (!isset($_SESSION['seller_id'])) {
    header('Location: login.php');
    exit;
}

// Helper functions
function createSlug($string) {
    $slug = strtolower(trim($string));
    $slug = preg_replace('/[^a-z0-9-]/', '-', $slug);
    $slug = preg_replace('/-+/', '-', $slug);
    return trim($slug, '-');
}

function uploadImage($file, $folder) {
    $upload_dir = "../uploads/$folder/";
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    
    $allowed_types = ['image/jpeg', 'image/png', 'image/webp', 'image/jpg'];
    if (!in_array($file['type'], $allowed_types)) {
        return false;
    }
    
    $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid() . '.' . $file_extension;
    $filepath = $upload_dir . $filename;
    
    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        return "uploads/$folder/" . $filename;
    }
    
    return false;
}

$sellerId = $_SESSION['seller_id'];
$pageTitle = 'My Categories';
$success_message = '';
$error_message = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
                $name = trim($_POST['name']);
                $slug = createSlug($name);
                $description = trim($_POST['description']);
                $parent_id = isset($_POST['parent_id']) && $_POST['parent_id'] !== '' ? intval($_POST['parent_id']) : null;
                
                if (empty($name)) {
                    $_SESSION['error_message'] = 'Category name is required.';
                } else {
                    try {
                        $pdo->beginTransaction();
                        
                        // Insert category with seller_id
                        $stmt = $pdo->prepare("INSERT INTO categories (seller_id, name, slug, description, parent_id) VALUES (?, ?, ?, ?, ?)");
                        $stmt->execute([$sellerId, $name, $slug, $description, $parent_id]);
                        $category_id = $pdo->lastInsertId();
                        
                        // Handle image upload
                        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                            $image_path = uploadImage($_FILES['image'], 'categories');
                            if ($image_path) {
                                $stmt = $pdo->prepare("UPDATE categories SET image = ? WHERE id = ?");
                                $stmt->execute([$image_path, $category_id]);
                            }
                        }
                        
                        $pdo->commit();
                        $_SESSION['success_message'] = 'Category added successfully!';
                        logSellerActivity($sellerId, 'category_added', "Added category: {$name}");
                    } catch (Exception $e) {
                        $pdo->rollBack();
                        $_SESSION['error_message'] = 'Error adding category: ' . $e->getMessage();
                    }
                }
                header('Location: categories.php');
                exit;
                break;
                
            case 'edit':
                $id = intval($_POST['id']);
                $name = trim($_POST['name']);
                $slug = createSlug($name);
                $description = trim($_POST['description']);
                $parent_id = isset($_POST['parent_id']) && $_POST['parent_id'] !== '' ? intval($_POST['parent_id']) : null;
                
                // Verify category belongs to seller
                $stmt = $pdo->prepare("SELECT id FROM categories WHERE id = ? AND seller_id = ?");
                $stmt->execute([$id, $sellerId]);
                if (!$stmt->fetch()) {
                    $_SESSION['error_message'] = 'Category not found or access denied.';
                    header('Location: categories.php');
                    exit;
                }
                
                if (empty($name)) {
                    $_SESSION['error_message'] = 'Category name is required.';
                } else {
                    try {
                        $pdo->beginTransaction();
                        
                        // Update category
                        $stmt = $pdo->prepare("UPDATE categories SET name = ?, slug = ?, description = ?, parent_id = ? WHERE id = ? AND seller_id = ?");
                        $stmt->execute([$name, $slug, $description, $parent_id, $id, $sellerId]);
                        
                        // Handle image upload
                        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                            $image_path = uploadImage($_FILES['image'], 'categories');
                            if ($image_path) {
                                $stmt = $pdo->prepare("UPDATE categories SET image = ? WHERE id = ?");
                                $stmt->execute([$image_path, $id]);
                            }
                        }
                        
                        $pdo->commit();
                        $_SESSION['success_message'] = 'Category updated successfully!';
                        logSellerActivity($sellerId, 'category_updated', "Updated category: {$name} (ID: {$id})");
                    } catch (Exception $e) {
                        $pdo->rollBack();
                        $_SESSION['error_message'] = 'Error updating category: ' . $e->getMessage();
                    }
                }
                header('Location: categories.php');
                exit;
                break;
                
            case 'delete':
                $id = intval($_POST['id']);
                
                // Verify category belongs to seller
                $stmt = $pdo->prepare("SELECT name FROM categories WHERE id = ? AND seller_id = ?");
                $stmt->execute([$id, $sellerId]);
                $category = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if (!$category) {
                    $_SESSION['error_message'] = 'Category not found or access denied.';
                    header('Location: categories.php');
                    exit;
                }
                
                try {
                    // Check if category has products
                    $stmt = $pdo->prepare("SELECT COUNT(*) FROM products WHERE category_id = ?");
                    $stmt->execute([$id]);
                    $product_count = $stmt->fetchColumn();
                    
                    if ($product_count > 0) {
                        $_SESSION['error_message'] = 'Cannot delete category with existing products.';
                    } else {
                        $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ? AND seller_id = ?");
                        $stmt->execute([$id, $sellerId]);
                        $_SESSION['success_message'] = 'Category deleted successfully!';
                        logSellerActivity($sellerId, 'category_deleted', "Deleted category: {$category['name']} (ID: {$id})");
                    }
                } catch (Exception $e) {
                    $_SESSION['error_message'] = 'Error deleting category: ' . $e->getMessage();
                }
                header('Location: categories.php');
                exit;
                break;
        }
    }
}

// SECURITY: Get only seller's own categories (seller_id = $sellerId)
// Admin categories (seller_id = NULL) can be viewed but not edited
$stmt = $pdo->prepare("SELECT c.*, COUNT(p.id) as product_count,
                       CASE WHEN c.seller_id IS NULL THEN 1 ELSE 0 END as is_admin_category
                       FROM categories c 
                       LEFT JOIN products p ON c.id = p.category_id AND p.seller_id = ?
                       WHERE c.seller_id = ? OR c.seller_id IS NULL
                       GROUP BY c.id 
                       ORDER BY is_admin_category ASC, c.name ASC");
$stmt->execute([$sellerId, $sellerId]);
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get seller categories only (for editing and parent selection)
$sellerCategories = array_filter($categories, function($cat) use ($sellerId) {
    return $cat['seller_id'] == $sellerId;
});

$categoryTree = buildCategoryTree($categories);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - EverythingB2C</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="../admin/assets/css/admin.css" rel="stylesheet">
</head>
<body>
    <div class="everythingb2c-admin-container">
        <?php include 'includes/sidebar.php'; ?>
        <div class="everythingb2c-main-content">
            <?php include 'includes/header.php'; ?>
            <div class="everythingb2c-dashboard-content">
                <div class="container-fluid">
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center">
                                <h1 class="h3 mb-0">My Categories</h1>
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                                    <i class="fas fa-plus"></i> Add New Category
                                </button>
                            </div>
                        </div>
                    </div>

                    <?php if (isset($_SESSION['success_message'])): ?>
                        <div class="alert alert-success alert-dismissible fade show">
                            <?php echo htmlspecialchars($_SESSION['success_message']); unset($_SESSION['success_message']); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['error_message'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show">
                            <?php echo htmlspecialchars($_SESSION['error_message']); unset($_SESSION['error_message']); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>Note:</strong> Admin categories (marked with blue badge) are read-only. You can use them for your products but cannot edit or delete them. You can only manage categories you've created.
                    </div>

                    <div class="card shadow">
                        <div class="card-header">
                            <h5 class="mb-0">Categories (<?php echo count($sellerCategories); ?>)</h5>
                        </div>
                        <div class="card-body">
                            <?php if (empty($sellerCategories)): ?>
                                <div class="text-center py-4">
                                    <i class="fas fa-tags fa-3x text-muted mb-3"></i>
                                    <h5>No categories found</h5>
                                    <p class="text-muted">Add your first category to organize your products.</p>
                                </div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Image</th>
                                                <th>Name</th>
                                                <th>Slug</th>
                                                <th>Description</th>
                                                <th>Products</th>
                                                <th>Created</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php 
                                            function displayCategories($categories, $sellerId, $level = 0) {
                                                foreach ($categories as $category) {
                                                    $isAdminCategory = ($category['seller_id'] === null || $category['seller_id'] == 0);
                                                    $isSellerCategory = ($category['seller_id'] == $sellerId);
                                                    
                                                    // Show all categories but only allow editing seller's own
                                                    echo '<tr' . ($isAdminCategory ? ' style="background-color:#f8f9fa;"' : '') . '>';
                                                    echo '<td>';
                                                    if ($category['image']) {
                                                        // Handle image path
                                                        $catImagePath = $category['image'];
                                                        if (strpos($catImagePath, 'uploads/') === 0) {
                                                            $catImagePath = '../' . $catImagePath;
                                                        } elseif (strpos($catImagePath, '/') !== 0 && strpos($catImagePath, 'http') !== 0) {
                                                            $catImagePath = '../uploads/' . $catImagePath;
                                                        } else {
                                                            $catImagePath = '../' . $catImagePath;
                                                        }
                                                        echo '<img src="' . htmlspecialchars($catImagePath) . '" alt="Category" style="width:50px;height:50px;object-fit:cover;border-radius:4px;">';
                                                    } else {
                                                        echo '<div style="width:50px;height:50px;background:#f0f0f0;display:flex;align-items:center;justify-content:center;border-radius:4px;"><i class="fas fa-image text-muted"></i></div>';
                                                    }
                                                    echo '</td>';
                                                    echo '<td>';
                                                    echo '<strong style="padding-left: ' . ($level * 20) . 'px">';
                                                    echo htmlspecialchars($category['name']);
                                                    echo '</strong>';
                                                    if ($category['parent_id']) {
                                                        echo ' <span class="badge bg-secondary">Sub-category</span>';
                                                    } else {
                                                        echo ' <span class="badge bg-primary">Main Category</span>';
                                                    }
                                                    // Mark admin categories
                                                    if ($isAdminCategory) {
                                                        echo ' <span class="badge bg-info">Admin Category</span>';
                                                    }
                                                    echo '</td>';
                                                    echo '<td><code>' . htmlspecialchars($category['slug']) . '</code></td>';
                                                    echo '<td>' . htmlspecialchars(substr($category['description'] ?? '', 0, 50)) . (strlen($category['description'] ?? '') > 50 ? '...' : '') . '</td>';
                                                    echo '<td><span class="badge bg-primary">' . ($category['product_count'] ?? 0) . '</span></td>';
                                                    echo '<td>' . date('M d, Y', strtotime($category['created_at'])) . '</td>';
                                                    echo '<td>';
                                                    
                                                    // SECURITY: Only show edit/delete for seller's own categories
                                                    if ($isSellerCategory) {
                                                        echo '<button type="button" class="btn btn-warning btn-sm" onclick=\'editCategory(' . htmlspecialchars(json_encode($category)) . ')\' title="Edit"><i class="fas fa-edit"></i></button> ';
                                                        echo '<button type="button" class="btn btn-danger btn-sm" onclick="deleteCategory(' . $category['id'] . ', \'' . htmlspecialchars($category['name'], ENT_QUOTES) . '\')" title="Delete"><i class="fas fa-trash"></i></button>';
                                                    } else {
                                                        echo '<span class="badge bg-secondary">Read-Only</span>';
                                                    }
                                                    echo '</td>';
                                                    echo '</tr>';
                                                    
                                                    if (!empty($category['children'])) {
                                                        displayCategories($category['children'], $sellerId, $level + 1);
                                                    }
                                                }
                                            }
                                            
                                            displayCategories($categoryTree, $sellerId);
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Category Modal -->
    <div class="modal fade" id="addCategoryModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" enctype="multipart/form-data">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="add">
                        
                        <div class="mb-3">
                            <label class="form-label">Category Name *</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description" rows="3"></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Parent Category (Optional)</label>
                            <select class="form-control" name="parent_id">
                                <option value="">None (Main Category)</option>
                                <?php foreach ($sellerCategories as $cat): ?>
                                    <?php if (!$cat['parent_id']): // Only show main categories as parents ?>
                                        <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Category Image</label>
                            <input type="file" class="form-control" name="image" accept="image/*">
                            <small class="text-muted">Recommended: 400x400px</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Category</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Category Modal -->
    <div class="modal fade" id="editCategoryModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" enctype="multipart/form-data">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="edit">
                        <input type="hidden" name="id" id="edit_id">
                        
                        <div class="mb-3">
                            <label class="form-label">Category Name *</label>
                            <input type="text" class="form-control" name="name" id="edit_name" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description" id="edit_description" rows="3"></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Parent Category (Optional)</label>
                            <select class="form-control" name="parent_id" id="edit_parent_id">
                                <option value="">None (Main Category)</option>
                                <?php foreach ($sellerCategories as $cat): ?>
                                    <?php if (!$cat['parent_id']): ?>
                                        <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Category Image</label>
                            <div id="current_image_preview" class="mb-2"></div>
                            <input type="file" class="form-control" name="image" accept="image/*">
                            <small class="text-muted">Upload new image to replace current one</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Category</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Form -->
    <form method="POST" id="deleteForm" style="display: none;">
        <input type="hidden" name="action" value="delete">
        <input type="hidden" name="id" id="delete_id">
    </form>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../admin/assets/js/admin.js"></script>
    <script>
    function editCategory(category) {
        document.getElementById('edit_id').value = category.id;
        document.getElementById('edit_name').value = category.name;
        document.getElementById('edit_description').value = category.description || '';
        document.getElementById('edit_parent_id').value = category.parent_id || '';
        
        // Show current image
        const preview = document.getElementById('current_image_preview');
        if (category.image) {
            let imgPath = category.image;
            if (imgPath.indexOf('uploads/') === 0) {
                imgPath = '../' + imgPath;
            } else if (imgPath.indexOf('/') !== 0 && imgPath.indexOf('http') !== 0) {
                imgPath = '../uploads/' + imgPath;
            } else {
                imgPath = '../' + imgPath;
            }
            preview.innerHTML = '<img src="' + imgPath + '" style="max-width:200px;max-height:100px;border-radius:4px;">';
        } else {
            preview.innerHTML = '<p class="text-muted"><i class="fas fa-image"></i> No image</p>';
        }
        
        const modal = new bootstrap.Modal(document.getElementById('editCategoryModal'));
        modal.show();
    }

    function deleteCategory(id, name) {
        Swal.fire({
            title: 'Delete Category?',
            html: `Are you sure you want to delete category "<strong>${name}</strong>"?<br><br><small class="text-muted">Note: You cannot delete categories that have products.</small>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, Delete',
            cancelButtonText: 'Cancel',
            width: '380px',
            customClass: {
                popup: 'swal-with-logo'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete_id').value = id;
                document.getElementById('deleteForm').submit();
            }
        });
    }
    
    </script>
</body>
</html>