<?php
$pageTitle = 'Manage Admin Users';
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';
require_once 'includes/auth-check.php';

// Check permission
checkAdminPermission('manage_admins');

$message = '';
$error = '';

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'add_admin') {
        $name = trim($_POST['admin_name'] ?? '');
        $email = trim($_POST['admin_email'] ?? '');
        $password = trim($_POST['admin_password'] ?? '');
        $roleId = intval($_POST['admin_role'] ?? 0);
        
        if (empty($name) || empty($email) || empty($password) || $roleId === 0) {
            $error = 'All fields are required';
        } elseif (strlen($password) < 6) {
            $error = 'Password must be at least 6 characters';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Invalid email format';
        } else {
            if (addAdmin($name, $email, $password, $roleId)) {
                $message = 'Admin user added successfully!';
            } else {
                $error = 'Failed to add admin. Email may already exist.';
            }
        }
    } elseif ($action === 'update_admin') {
        $adminId = intval($_POST['admin_id'] ?? 0);
        $name = trim($_POST['admin_name'] ?? '');
        $email = trim($_POST['admin_email'] ?? '');
        $password = trim($_POST['admin_password'] ?? '');
        $roleId = intval($_POST['admin_role'] ?? 0);
        
        if (empty($name) || empty($email) || $roleId === 0) {
            $error = 'Name, email, and role are required';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Invalid email format';
        } elseif ($password && strlen($password) < 6) {
            $error = 'Password must be at least 6 characters';
        } else {
            if (updateAdmin($adminId, $name, $email, $roleId, $password ?: null)) {
                $message = 'Admin user updated successfully!';
            } else {
                $error = 'Failed to update admin';
            }
        }
    } elseif ($action === 'deactivate_admin') {
        $adminId = intval($_POST['admin_id'] ?? 0);
        if ($adminId !== $_SESSION['admin_id']) {
            if (deactivateAdmin($adminId)) {
                $message = 'Admin user deactivated successfully!';
            } else {
                $error = 'Failed to deactivate admin';
            }
        } else {
            $error = 'You cannot deactivate your own account';
        }
    } elseif ($action === 'activate_admin') {
        $adminId = intval($_POST['admin_id'] ?? 0);
        if (activateAdmin($adminId)) {
            $message = 'Admin user activated successfully!';
        } else {
            $error = 'Failed to activate admin';
        }
    }
}

// Get all admins and roles
$admins = getAllAdmins();
$roles = getAllRoles();

// Get selected admin for editing
$selectedAdmin = null;
if (isset($_GET['edit'])) {
    $adminId = intval($_GET['edit']);
    $selectedAdmin = getAdminById($adminId);
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
    <link href="assets/css/admin.css" rel="stylesheet">
    <style>
        .admin-row {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 15px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            border-left: 4px solid #667eea;
        }
        .admin-row.inactive {
            opacity: 0.7;
            border-left-color: #dc3545;
        }
        .badge-role {
            background-color: #667eea;
            color: white;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 12px;
        }
        .form-section {
            margin-bottom: 30px;
        }
        .status-badge {
            padding: 5px 12px;
            border-radius: 5px;
            font-size: 12px;
            font-weight: 500;
        }
        .status-badge.active {
            background-color: #d4edda;
            color: #155724;
        }
        .status-badge.inactive {
            background-color: #f8d7da;
            color: #721c24;
        }
    </style>
</head>
<body>
    <div class="everythingb2c-admin-container">
        <!-- Sidebar -->
        <?php require_once 'includes/sidebar.php'; ?>

        <!-- Main Content -->
        <div class="everythingb2c-main-content">
            <!-- Header -->
            <?php require_once 'includes/header.php'; ?>

            <!-- Page Content -->
            <div class="everythingb2c-dashboard-content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-12">
                <h2 class="mb-4"><i class="fas fa-users-cog"></i> Manage Admin Users</h2>
                
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
                        <div class="form-section">
                            <h4 class="mb-3">All Admin Users</h4>
                            <?php foreach ($admins as $admin): ?>
                                <div class="admin-row <?php echo !$admin['is_active'] ? 'inactive' : ''; ?>">
                                    <div class="row align-items-center">
                                        <div class="col-md-8">
                                            <h5 class="mb-2">
                                                <i class="fas fa-user-shield"></i> <?php echo htmlspecialchars($admin['name']); ?>
                                            </h5>
                                            <p class="text-muted small mb-2">
                                                <i class="fas fa-envelope"></i> <?php echo htmlspecialchars($admin['email']); ?>
                                            </p>
                                            <p class="small mb-0">
                                                <span class="badge-role"><?php echo htmlspecialchars($admin['role_name']); ?></span>
                                                <span class="status-badge <?php echo $admin['is_active'] ? 'active' : 'inactive'; ?>">
                                                    <?php echo $admin['is_active'] ? 'Active' : 'Inactive'; ?>
                                                </span>
                                            </p>
                                            <p class="text-secondary small mt-2 mb-0">
                                                Last login: <?php echo $admin['last_login'] ? date('M d, Y H:i', strtotime($admin['last_login'])) : 'Never'; ?>
                                            </p>
                                        </div>
                                        <div class="col-md-4 text-end">
                                            <a href="?edit=<?php echo $admin['id']; ?>#edit-form" class="btn btn-sm btn-primary">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                            <?php if ($admin['id'] !== $_SESSION['admin_id']): ?>
                                                <form method="POST" style="display: inline;">
                                                    <input type="hidden" name="action" value="<?php echo $admin['is_active'] ? 'deactivate_admin' : 'activate_admin'; ?>">
                                                    <input type="hidden" name="admin_id" value="<?php echo $admin['id']; ?>">
                                                    <button type="submit" class="btn btn-sm <?php echo $admin['is_active'] ? 'btn-warning' : 'btn-success'; ?>">
                                                        <i class="fas fa-<?php echo $admin['is_active'] ? 'lock' : 'unlock'; ?>"></i>
                                                        <?php echo $admin['is_active'] ? 'Deactivate' : 'Activate'; ?>
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div id="edit-form">
                            <?php if ($selectedAdmin): ?>
                                <h4 class="mb-3">Edit Admin User</h4>
                                <div class="card">
                                    <div class="card-body">
                                        <form method="POST">
                                            <input type="hidden" name="action" value="update_admin">
                                            <input type="hidden" name="admin_id" value="<?php echo $selectedAdmin['id']; ?>">
                                            
                                            <div class="mb-3">
                                                <label for="admin_name" class="form-label">Name*</label>
                                                <input type="text" class="form-control" id="admin_name" name="admin_name" value="<?php echo htmlspecialchars($selectedAdmin['name']); ?>" required>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label for="admin_email" class="form-label">Email *</label>
                                                <input type="email" class="form-control" id="admin_email" name="admin_email" value="<?php echo htmlspecialchars($selectedAdmin['email']); ?>" required>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label for="admin_password" class="form-label">Password (leave empty to keep current)</label>
                                                <input type="password" class="form-control" id="admin_password" name="admin_password">
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label for="admin_role" class="form-label">Role *</label>
                                                <select class="form-control" id="admin_role" name="admin_role" required>
                                                    <option value="">Select Role</option>
                                                    <?php foreach ($roles as $role): ?>
                                                        <option value="<?php echo $role['id']; ?>" 
                                                            <?php echo $selectedAdmin['role_id'] == $role['id'] ? 'selected' : ''; ?>>
                                                            <?php echo htmlspecialchars($role['name']); ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            
                                            <button type="submit" class="btn btn-primary w-100">
                                                <i class="fas fa-save"></i> Update Admin
                                            </button>
                                            <a href="manage_admins.php" class="btn btn-secondary w-100 mt-2">
                                                <i class="fas fa-times"></i> Cancel
                                            </a>
                                        </form>
                                    </div>
                                </div>
                            <?php else: ?>
                                <h4 class="mb-3">Add New Admin User</h4>
                                <div class="card">
                                    <div class="card-body">
                                        <form method="POST">
                                            <input type="hidden" name="action" value="add_admin">
                                            
                                            <div class="mb-3">
                                                <label for="admin_name" class="form-label">Name *</label>
                                                <input type="text" class="form-control" id="admin_name" name="admin_name" required>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label for="admin_email" class="form-label">Email *</label>
                                                <input type="email" class="form-control" id="admin_email" name="admin_email" required>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label for="admin_password" class="form-label">Password *</label>
                                                <input type="password" class="form-control" id="admin_password" name="admin_password" required>
                                                <small class="text-muted">Min 6 characters</small>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label for="admin_role" class="form-label">Role *</label>
                                                <select class="form-control" id="admin_role" name="admin_role" required>
                                                    <option value="">Select Role</option>
                                                    <?php foreach ($roles as $role): ?>
                                                        <option value="<?php echo $role['id']; ?>">
                                                            <?php echo htmlspecialchars($role['name']); ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            
                                            <button type="submit" class="btn btn-success w-100">
                                                <i class="fas fa-plus"></i> Add Admin User
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
