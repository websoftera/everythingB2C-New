<?php
$pageTitle = 'Manage Roles';
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';
require_once 'includes/auth-check.php';

// Check permission
checkAdminPermission('manage_roles');

$message = '';
$error = '';

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'add_role') {
        $name = trim($_POST['role_name'] ?? '');
        $description = trim($_POST['role_description'] ?? '');
        
        if (empty($name)) {
            $error = 'Role name is required';
        } else {
            if (addRole($name, $description)) {
                $message = 'Role added successfully!';
            } else {
                $error = 'Failed to add role. Role name may already exist.';
            }
        }
    } elseif ($action === 'update_role') {
        $roleId = intval($_POST['role_id'] ?? 0);
        $name = trim($_POST['role_name'] ?? '');
        $description = trim($_POST['role_description'] ?? '');
        
        if (empty($name)) {
            $error = 'Role name is required';
        } elseif (updateRole($roleId, $name, $description)) {
            $message = 'Role updated successfully!';
        } else {
            $error = 'Failed to update role';
        }
    } elseif ($action === 'delete_role') {
        $roleId = intval($_POST['role_id'] ?? 0);
        if (deleteRole($roleId)) {
            $message = 'Role deleted successfully!';
        } else {
            $error = 'Cannot delete this role. It may be a system role or have admins assigned.';
        }
    } elseif ($action === 'update_permissions') {
        $roleId = intval($_POST['role_id'] ?? 0);
        $selectedPermissions = $_POST['permissions'] ?? [];
        
        try {
            // Remove all existing permissions
            $stmt = $pdo->prepare("DELETE FROM role_permissions WHERE role_id = ?");
            $stmt->execute([$roleId]);
            
            // Add selected permissions
            foreach ($selectedPermissions as $permissionId) {
                addPermissionToRole($roleId, intval($permissionId));
            }
            
            $message = 'Permissions updated successfully!';
        } catch (Exception $e) {
            $error = 'Failed to update permissions: ' . $e->getMessage();
        }
    }
}

// Get all roles
$roles = getAllRoles();
$allPermissionsGrouped = getAllPermissionsGrouped();

// Get selected role for editing
$selectedRole = null;
$selectedRolePermissions = [];
if (isset($_GET['edit'])) {
    $roleId = intval($_GET['edit']);
    $stmt = $pdo->prepare("SELECT * FROM roles WHERE id = ?");
    $stmt->execute([$roleId]);
    $selectedRole = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($selectedRole) {
        $selectedRolePermissions = getRolePermissions($roleId);
        $selectedRolePermissions = array_column($selectedRolePermissions, 'id');
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - EverythingB2C Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .everythingb2c-admin-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px 30px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .everythingb2c-header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1400px;
            margin: 0 auto;
        }
        .everythingb2c-header-title {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }
        .container-main {
            max-width: 1400px;
            margin: 0 auto;
            padding: 30px 15px;
        }
        .role-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            border-left: 4px solid #667eea;
        }
        .role-card.system {
            border-left-color: #28a745;
        }
        .badge-system {
            background-color: #28a745;
        }
        .btn-sm {
            padding: 5px 10px;
            font-size: 12px;
        }
        .permission-group {
            margin-bottom: 20px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 8px;
        }
        .permission-group-title {
            font-weight: 600;
            margin-bottom: 10px;
            color: #667eea;
        }
        .form-check {
            margin-bottom: 8px;
        }
        .alert {
            border-radius: 8px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <?php require_once 'includes/header.php'; ?>
    
    <div class="container-main">
        <div class="row">
            <div class="col-md-12">
                <h2 class="mb-4"><i class="fas fa-shield-alt"></i> Manage Roles & Permissions</h2>
                
                <?php if ($message): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($message); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                
                <?php if ($error): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                
                <div class="row">
                    <div class="col-md-8">
                        <h4 class="mb-3">All Roles</h4>
                        <?php foreach ($roles as $role): ?>
                            <div class="role-card <?php echo $role['is_system_role'] ? 'system' : ''; ?>">
                                <div class="row align-items-center">
                                    <div class="col-md-6">
                                        <h5 class="mb-2">
                                            <?php echo htmlspecialchars($role['name']); ?>
                                            <?php if ($role['is_system_role']): ?>
                                                <span class="badge badge-system">System Role</span>
                                            <?php endif; ?>
                                        </h5>
                                        <p class="text-muted small mb-0"><?php echo htmlspecialchars($role['description']); ?></p>
                                        <small class="text-secondary">
                                            <i class="fas fa-users"></i> <?php echo $role['admin_count']; ?> admin(s)
                                        </small>
                                    </div>
                                    <div class="col-md-6 text-end">
                                        <a href="?edit=<?php echo $role['id']; ?>#permissions" class="btn btn-sm btn-primary">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <?php if (!$role['is_system_role'] && $role['admin_count'] == 0): ?>
                                            <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure?');">
                                                <input type="hidden" name="action" value="delete_role">
                                                <input type="hidden" name="role_id" value="<?php echo $role['id']; ?>">
                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    <i class="fas fa-trash"></i> Delete
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <div class="col-md-4">
                        <h4 class="mb-3">Add New Role</h4>
                        <div class="card">
                            <div class="card-body">
                                <form method="POST">
                                    <input type="hidden" name="action" value="add_role">
                                    <div class="mb-3">
                                        <label for="role_name" class="form-label">Role Name *</label>
                                        <input type="text" class="form-control" id="role_name" name="role_name" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="role_description" class="form-label">Description</label>
                                        <textarea class="form-control" id="role_description" name="role_description" rows="3"></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="fas fa-plus"></i> Add Role
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                
                <?php if ($selectedRole): ?>
                    <hr class="my-5">
                    <div id="permissions">
                        <h4 class="mb-3">Manage Permissions for: <strong><?php echo htmlspecialchars($selectedRole['name']); ?></strong></h4>
                        
                        <form method="POST" class="card">
                            <div class="card-body">
                                <input type="hidden" name="action" value="update_permissions">
                                <input type="hidden" name="role_id" value="<?php echo $selectedRole['id']; ?>">
                                
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <div class="card">
                                            <div class="card-header bg-light">
                                                <strong>Role Details</strong>
                                            </div>
                                            <div class="card-body">
                                                <form method="POST" class="mb-3">
                                                    <input type="hidden" name="action" value="update_role">
                                                    <input type="hidden" name="role_id" value="<?php echo $selectedRole['id']; ?>">
                                                    <div class="mb-3">
                                                        <label for="edit_role_name" class="form-label">Role Name</label>
                                                        <input type="text" class="form-control" id="edit_role_name" name="role_name" value="<?php echo htmlspecialchars($selectedRole['name']); ?>" <?php echo $selectedRole['is_system_role'] ? 'disabled' : ''; ?> required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="edit_role_description" class="form-label">Description</label>
                                                        <textarea class="form-control" id="edit_role_description" name="role_description" rows="3" <?php echo $selectedRole['is_system_role'] ? 'disabled' : ''; ?>><?php echo htmlspecialchars($selectedRole['description']); ?></textarea>
                                                    </div>
                                                    <?php if (!$selectedRole['is_system_role']): ?>
                                                        <button type="submit" class="btn btn-sm btn-success w-100">
                                                            <i class="fas fa-save"></i> Update Role
                                                        </button>
                                                    <?php else: ?>
                                                        <p class="text-muted small">System roles cannot be edited</p>
                                                    <?php endif; ?>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="card">
                                            <div class="card-header bg-light">
                                                <strong>Assign Permissions</strong>
                                            </div>
                                            <div class="card-body" style="max-height: 500px; overflow-y: auto;">
                                                <?php foreach ($allPermissionsGrouped as $category => $permissions): ?>
                                                    <div class="permission-group">
                                                        <div class="permission-group-title"><?php echo htmlspecialchars($category); ?></div>
                                                        <?php foreach ($permissions as $permission): ?>
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox" name="permissions[]" 
                                                                    id="perm_<?php echo $permission['id']; ?>" 
                                                                    value="<?php echo $permission['id']; ?>"
                                                                    <?php echo in_array($permission['id'], $selectedRolePermissions) ? 'checked' : ''; ?>>
                                                                <label class="form-check-label" for="perm_<?php echo $permission['id']; ?>">
                                                                    <?php echo htmlspecialchars($permission['name']); ?>
                                                                    <small class="text-muted d-block"><?php echo htmlspecialchars($permission['description']); ?></small>
                                                                </label>
                                                            </div>
                                                        <?php endforeach; ?>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                            <div class="card-footer">
                                                <button type="submit" class="btn btn-primary w-100">
                                                    <i class="fas fa-save"></i> Save Permissions
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <?php require_once 'includes/footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
