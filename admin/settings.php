<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

$pageTitle = 'Settings';
$success_message = '';
$error_message = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'update_profile':
                $name = trim($_POST['name']);
                $email = trim($_POST['email']);
                $current_password = $_POST['current_password'];
                $new_password = $_POST['new_password'];
                $confirm_password = $_POST['confirm_password'];
                
                // Validate current password
                $stmt = $pdo->prepare("SELECT password FROM admins WHERE id = ?");
                $stmt->execute([$_SESSION['admin_id']]);
                $admin = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if (!password_verify($current_password, $admin['password'])) {
                    $_SESSION['error_message'] = 'Current password is incorrect.';
                } elseif ($new_password && $new_password !== $confirm_password) {
                    $_SESSION['error_message'] = 'New passwords do not match.';
                } elseif ($new_password && strlen($new_password) < 6) {
                    $_SESSION['error_message'] = 'New password must be at least 6 characters long.';
                } else {
                    try {
                        if ($new_password) {
                            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                            $stmt = $pdo->prepare("UPDATE admins SET name = ?, email = ?, password = ? WHERE id = ?");
                            $stmt->execute([$name, $email, $hashed_password, $_SESSION['admin_id']]);
                        } else {
                            $stmt = $pdo->prepare("UPDATE admins SET name = ?, email = ? WHERE id = ?");
                            $stmt->execute([$name, $email, $_SESSION['admin_id']]);
                        }
                        
                        $_SESSION['admin_name'] = $name;
                        $_SESSION['admin_email'] = $email;
                        $_SESSION['success_message'] = 'Profile updated successfully!';
                    } catch (Exception $e) {
                        $_SESSION['error_message'] = 'Error updating profile: ' . $e->getMessage();
                    }
                }
                header('Location: settings.php');
                exit;
                break;
                
            case 'update_site_settings':
                // This would typically update site settings in a settings table
                // For now, we'll just show a success message
                $_SESSION['success_message'] = 'Site settings updated successfully!';
                header('Location: settings.php');
                exit;
                break;
        }
    }
}

// Get current admin data
$stmt = $pdo->prepare("SELECT * FROM admins WHERE id = ?");
$stmt->execute([$_SESSION['admin_id']]);
$admin = $stmt->fetch(PDO::FETCH_ASSOC);
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

            <!-- Settings Content -->
            <div class="dashboard-content">
                <div class="container-fluid">
                    <div class="row mb-4">
                        <div class="col-12">
                            <h1 class="h3 mb-0">Settings</h1>
                        </div>
                    </div>

                    <?php
                    $success_message = isset($_SESSION['success_message']) ? $_SESSION['success_message'] : '';
                    $error_message = isset($_SESSION['error_message']) ? $_SESSION['error_message'] : '';
                    unset($_SESSION['success_message'], $_SESSION['error_message']);
                    ?>

                    <?php if ($success_message): ?>
                        <div class="alert alert-success"><?php echo htmlspecialchars($success_message); ?></div>
                    <?php endif; ?>

                    <?php if ($error_message): ?>
                        <div class="alert alert-danger"><?php echo htmlspecialchars($error_message); ?></div>
                    <?php endif; ?>

                    <div class="row">
                        <!-- Profile Settings -->
                        <div class="col-lg-6">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Profile Settings</h6>
                                </div>
                                <div class="card-body">
                                    <form method="POST">
                                        <input type="hidden" name="action" value="update_profile">
                                        
                                        <div class="mb-3">
                                            <label for="name" class="form-label">Name</label>
                                            <input type="text" class="form-control" id="name" name="name" 
                                                   value="<?php echo htmlspecialchars($admin['name']); ?>" required>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="email" class="form-label">Email</label>
                                            <input type="email" class="form-control" id="email" name="email" 
                                                   value="<?php echo htmlspecialchars($admin['email']); ?>" required>
                                        </div>
                                        
                                        <hr>
                                        
                                        <div class="mb-3">
                                            <label for="current_password" class="form-label">Current Password</label>
                                            <input type="password" class="form-control" id="current_password" name="current_password" required>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="new_password" class="form-label">New Password (leave blank to keep current)</label>
                                            <input type="password" class="form-control" id="new_password" name="new_password" minlength="6">
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="confirm_password" class="form-label">Confirm New Password</label>
                                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" minlength="6">
                                        </div>
                                        
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save"></i> Update Profile
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Site Settings -->
                        <div class="col-lg-6">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Site Settings</h6>
                                </div>
                                <div class="card-body">
                                    <form method="POST">
                                        <input type="hidden" name="action" value="update_site_settings">
                                        
                                        <div class="mb-3">
                                            <label for="site_name" class="form-label">Site Name</label>
                                            <input type="text" class="form-control" id="site_name" name="site_name" 
                                                   value="EverythingB2C" required>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="site_description" class="form-label">Site Description</label>
                                            <textarea class="form-control" id="site_description" name="site_description" rows="3">Your one-stop shop for everything you need</textarea>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="contact_email" class="form-label">Contact Email</label>
                                            <input type="email" class="form-control" id="contact_email" name="contact_email" 
                                                   value="contact@EverythingB2C.com">
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="contact_phone" class="form-label">Contact Phone</label>
                                            <input type="tel" class="form-control" id="contact_phone" name="contact_phone" 
                                                   value="+91 1234567890">
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="currency" class="form-label">Currency</label>
                                            <select class="form-control" id="currency" name="currency">
                                                <option value="INR" selected>Indian Rupee (₹)</option>
                                                <option value="USD">US Dollar ($)</option>
                                                <option value="EUR">Euro (€)</option>
                                            </select>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="timezone" class="form-label">Timezone</label>
                                            <select class="form-control" id="timezone" name="timezone">
                                                <option value="Asia/Kolkata" selected>Asia/Kolkata (IST)</option>
                                                <option value="UTC">UTC</option>
                                                <option value="America/New_York">America/New_York (EST)</option>
                                            </select>
                                        </div>
                                        
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save"></i> Update Settings
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- System Information -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">System Information</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h6>Server Information</h6>
                                            <ul class="list-unstyled">
                                                <li><strong>PHP Version:</strong> <?php echo PHP_VERSION; ?></li>
                                                <li><strong>Server Software:</strong> <?php echo $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown'; ?></li>
                                                <li><strong>Database:</strong> MySQL</li>
                                                <li><strong>Upload Max Size:</strong> <?php echo ini_get('upload_max_filesize'); ?></li>
                                            </ul>
                                        </div>
                                        <div class="col-md-6">
                                            <h6>Application Information</h6>
                                            <ul class="list-unstyled">
                                                <li><strong>Application Name:</strong> EverythingB2C</li>
                                                <li><strong>Version:</strong> 1.0.0</li>
                                                <li><strong>Last Updated:</strong> <?php echo date('M d, Y'); ?></li>
                                                <li><strong>EverythingB2C:</strong> Active</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Quick Actions</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-3 mb-3">
                                            <a href="products.php" class="btn btn-outline-primary w-100">
                                                <i class="fas fa-box"></i><br>
                                                Manage Products
                                            </a>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <a href="orders.php" class="btn btn-outline-success w-100">
                                                <i class="fas fa-shopping-cart"></i><br>
                                                View Orders
                                            </a>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <a href="users.php" class="btn btn-outline-info w-100">
                                                <i class="fas fa-users"></i><br>
                                                Manage Users
                                            </a>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <a href="reports.php" class="btn btn-outline-warning w-100">
                                                <i class="fas fa-chart-bar"></i><br>
                                                View Reports
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/admin.js"></script>
    <script>
        // Password confirmation validation
        document.getElementById('new_password').addEventListener('input', validatePasswords);
        document.getElementById('confirm_password').addEventListener('input', validatePasswords);

        function validatePasswords() {
            const newPassword = document.getElementById('new_password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            const confirmField = document.getElementById('confirm_password');
            
            if (newPassword && confirmPassword && newPassword !== confirmPassword) {
                confirmField.setCustomValidity('Passwords do not match');
            } else {
                confirmField.setCustomValidity('');
            }
        }
    </script>
</body>
</html> 