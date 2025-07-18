<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

$pageTitle = 'Categories Management';
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
                        
                        // Insert category
                        $stmt = $pdo->prepare("INSERT INTO categories (name, slug, description, parent_id) VALUES (?, ?, ?, ?)");
                        $stmt->execute([$name, $slug, $description, $parent_id]);
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
                
                if (empty($name)) {
                    $_SESSION['error_message'] = 'Category name is required.';
                } else {
                    try {
                        $pdo->beginTransaction();
                        
                        // Update category
                        $stmt = $pdo->prepare("UPDATE categories SET name = ?, slug = ?, description = ?, parent_id = ? WHERE id = ?");
                        $stmt->execute([$name, $slug, $description, $parent_id, $id]);
                        
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
                try {
                    // Check if category has products
                    $stmt = $pdo->prepare("SELECT COUNT(*) FROM products WHERE category_id = ?");
                    $stmt->execute([$id]);
                    $product_count = $stmt->fetchColumn();
                    
                    if ($product_count > 0) {
                        $_SESSION['error_message'] = 'Cannot delete category with existing products.';
                    } else {
                        $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
                        $stmt->execute([$id]);
                        $_SESSION['success_message'] = 'Category deleted successfully!';
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

// Get categories with product counts
$stmt = $pdo->query("SELECT c.*, COUNT(p.id) as product_count 
                     FROM categories c 
                     LEFT JOIN products p ON c.id = p.category_id 
                     GROUP BY c.id 
                     ORDER BY c.name");
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch all categories for parent selection (excluding self in edit)
$allCategories = $pdo->query("SELECT id, name FROM categories ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);

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
    
    $allowed_types = ['image/jpeg', 'image/png', 'image/webp'];
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

$categoryTree = buildCategoryTree($categories);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - EverythingB2C</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/admin.css" rel="stylesheet">
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <?php include 'includes/sidebar.php'; ?>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Header -->
            <?php include 'includes/header.php'; ?>

            <!-- Categories Content -->
            <div class="dashboard-content">
                <div class="container-fluid">
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center">
                                <h1 class="h3 mb-0">Categories Management</h1>
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                                    <i class="fas fa-plus"></i> Add New Category
                                </button>
                            </div>
                        </div>
                    </div>

                    <?php if ($success_message): ?>
                        <div class="alert alert-success"><?php echo htmlspecialchars($success_message); ?></div>
                    <?php endif; ?>

                    <?php if ($error_message): ?>
                        <div class="alert alert-danger"><?php echo htmlspecialchars($error_message); ?></div>
                    <?php endif; ?>

                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Categories (<?php echo count($categories); ?>)</h5>
                        </div>
                        <div class="card-body">
                            <?php if (empty($categories)): ?>
                                <div class="text-center py-4">
                                    <i class="fas fa-tags fa-3x text-muted mb-3"></i>
                                    <h5>No categories found</h5>
                                    <p class="text-muted">Add your first category to get started.</p>
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
                                            <?php foreach ($categoryTree as $category): ?>
                                                <tr>
                                                    <td>
                                                        <?php if ($category['image']): ?>
                                                            <img src="../<?php echo $category['image']; ?>" 
                                                                 alt="<?php echo htmlspecialchars($category['name']); ?>"
                                                                 class="img-preview">
                                                        <?php else: ?>
                                                            <div class="img-preview bg-light d-flex align-items-center justify-content-center">
                                                                <i class="fas fa-image text-muted"></i>
                                                            </div>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <strong style="padding-left: <?php echo $category['level'] * 20; ?>px">
                                                            <?php echo htmlspecialchars($category['name']); ?>
                                                        </strong>
                                                        <?php if ($category['parent_id']): ?>
                                                            <span class="badge bg-secondary ms-2">Sub-category</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <code><?php echo htmlspecialchars($category['slug']); ?></code>
                                                    </td>
                                                    <td>
                                                        <?php echo htmlspecialchars($category['description'] ?? ''); ?>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-primary"><?php echo $category['product_count']; ?></span>
                                                    </td>
                                                    <td><?php echo date('M d, Y', strtotime($category['created_at'])); ?></td>
                                                    <td>
                                                        <div class="action-buttons">
                                                            <button type="button" class="btn btn-warning btn-sm" 
                                                                    onclick="editCategory(<?php echo htmlspecialchars(json_encode($category)); ?>)" title="Edit">
                                                                <i class="fas fa-edit"></i>
                                                            </button>
                                                            <button type="button" class="btn btn-danger btn-sm" 
                                                                    onclick="deleteCategory(<?php echo $category['id']; ?>, '<?php echo htmlspecialchars($category['name']); ?>')" title="Delete">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
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
                            <label for="name" class="form-label">Category Name *</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="parent_id" class="form-label">Parent Category</label>
                            <select class="form-control" id="parent_id" name="parent_id">
                                <option value="">None (Main Category)</option>
                                <?php foreach ($allCategories as $cat): ?>
                                    <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="image" class="form-label">Category Image</label>
                            <input type="file" class="form-control" id="image" name="image" accept="image/*">
                            <div class="form-text">Upload an image for this category (optional)</div>
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
                            <label for="edit_name" class="form-label">Category Name *</label>
                            <input type="text" class="form-control" id="edit_name" name="name" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="edit_parent_id" class="form-label">Parent Category</label>
                            <select class="form-control" id="edit_parent_id" name="parent_id">
                                <option value="">None (Main Category)</option>
                                <?php foreach ($allCategories as $cat): ?>
                                    <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="edit_description" class="form-label">Description</label>
                            <textarea class="form-control" id="edit_description" name="description" rows="3"></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="edit_image" class="form-label">Category Image</label>
                            <input type="file" class="form-control" id="edit_image" name="image" accept="image/*">
                            <div class="form-text">Upload a new image to replace the current one (optional)</div>
                            <div id="current_image_preview" class="mt-2"></div>
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

    <!-- Delete Category Form -->
    <form method="POST" id="deleteCategoryForm" style="display: none;">
        <input type="hidden" name="action" value="delete">
        <input type="hidden" name="id" id="delete_id">
    </form>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/admin.js"></script>
    <script>
        function editCategory(category) {
            document.getElementById('edit_id').value = category.id;
            document.getElementById('edit_name').value = category.name;
            document.getElementById('edit_description').value = category.description || '';
            document.getElementById('edit_parent_id').value = category.parent_id || '';
            
            // Show current image preview
            const previewDiv = document.getElementById('current_image_preview');
            if (category.image) {
                previewDiv.innerHTML = `
                    <strong>Current Image:</strong><br>
                    <img src="../${category.image}" alt="${category.name}" class="img-preview" style="max-width: 100px;">
                `;
            } else {
                previewDiv.innerHTML = '<em>No image currently set</em>';
            }
            
            new bootstrap.Modal(document.getElementById('editCategoryModal')).show();
        }

        function deleteCategory(id, name) {
            if (confirm(`Are you sure you want to delete the category "${name}"? This action cannot be undone.`)) {
                document.getElementById('delete_id').value = id;
                document.getElementById('deleteCategoryForm').submit();
            }
        }
    </script>
</body>
</html> 