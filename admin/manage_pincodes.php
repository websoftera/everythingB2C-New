<?php
session_start();
require_once '../includes/delivery_popup_functions.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

$message = '';
$error = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add_pincodes':
                $pincodesString = $_POST['pincodes'] ?? '';
                if (!empty($pincodesString)) {
                    $result = addServiceablePincodes($pincodesString);
                    if ($result['added'] > 0) {
                        $message = "Successfully added {$result['added']} pincode(s).";
                    }
                    if (!empty($result['errors'])) {
                        $error = "Errors: " . implode(', ', $result['errors']);
                    }
                }
                break;
                
            case 'update_settings':
                $settings = [
                    'popup_enabled' => $_POST['popup_enabled'] ?? '0',
                    'popup_message' => $_POST['popup_message'] ?? '',
                    'popup_instruction' => $_POST['popup_instruction'] ?? '',
                    'service_available_message' => $_POST['service_available_message'] ?? '',
                    'service_unavailable_message' => $_POST['service_unavailable_message'] ?? ''
                ];
                updatePopupSettings($settings);
                $message = "Settings updated successfully.";
                break;
                
            case 'delete_pincode':
                $id = $_POST['pincode_id'] ?? 0;
                if ($id > 0) {
                    deleteServiceablePincode($id);
                    $message = "Pincode deleted successfully.";
                }
                break;
                
            case 'toggle_status':
                $id = $_POST['pincode_id'] ?? 0;
                if ($id > 0) {
                    togglePincodeStatus($id);
                    $message = "Pincode status updated successfully.";
                }
                break;
        }
    }
}

// Get data
$pincodes = getAllServiceablePincodes();
$settings = getPopupSettings();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Serviceable Pincodes - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .sidebar {
            min-height: 100vh;
            background-color: #343a40;
        }
        .sidebar .nav-link {
            color: #fff;
        }
        .sidebar .nav-link:hover {
            background-color: #495057;
        }
        .main-content {
            padding: 20px;
        }
        .pincode-card {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
        }
        .status-active {
            color: #9fbe1b;
        }
        .status-inactive {
            color: #dc3545;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2 sidebar p-0">
                <div class="p-3">
                    <h4 class="text-white">Admin Panel</h4>
                </div>
                <nav class="nav flex-column">
                    <a class="nav-link" href="dashboard.php">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a>
                    <a class="nav-link active" href="manage_pincodes.php">
                        <i class="fas fa-map-marker-alt"></i> Manage Pincodes
                    </a>
                    <a class="nav-link" href="products.php">
                        <i class="fas fa-box"></i> Products
                    </a>
                    <a class="nav-link" href="orders.php">
                        <i class="fas fa-shopping-cart"></i> Orders
                    </a>
                    <a class="nav-link" href="logout.php">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </nav>
            </div>

            <!-- Main Content -->
            <div class="col-md-10 main-content">
                <h2 class="mb-4">Manage Serviceable Pincodes</h2>

                <?php if ($message): ?>
                    <div class="alert alert-success"><?php echo $message; ?></div>
                <?php endif; ?>

                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>

                <div class="row">
                    <!-- Add Pincodes -->
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5>Add Serviceable Pincodes</h5>
                            </div>
                            <div class="card-body">
                                <form method="POST">
                                    <input type="hidden" name="action" value="add_pincodes">
                                    <div class="mb-3">
                                        <label for="pincodes" class="form-label">Pincodes (comma-separated)</label>
                                        <textarea class="form-control" id="pincodes" name="pincodes" rows="4" 
                                                  placeholder="Enter pincodes separated by commas (e.g., 411001, 411002, 411003)" required></textarea>
                                        <div class="form-text">Enter 6-digit pincodes separated by commas</div>
                                    </div>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-plus"></i> Add Pincodes
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Popup Settings -->
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5>Popup Settings</h5>
                            </div>
                            <div class="card-body">
                                <form method="POST">
                                    <input type="hidden" name="action" value="update_settings">
                                    <div class="mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="popup_enabled" name="popup_enabled" value="1" 
                                                   <?php echo ($settings['popup_enabled'] ?? '1') == '1' ? 'checked' : ''; ?>>
                                            <label class="form-check-label" for="popup_enabled">
                                                Enable Delivery Popup
                                            </label>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="popup_message" class="form-label">Popup Message</label>
                                        <input type="text" class="form-control" id="popup_message" name="popup_message" 
                                               value="<?php echo htmlspecialchars($settings['popup_message'] ?? ''); ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="popup_instruction" class="form-label">Instruction Text</label>
                                        <input type="text" class="form-control" id="popup_instruction" name="popup_instruction" 
                                               value="<?php echo htmlspecialchars($settings['popup_instruction'] ?? ''); ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="service_available_message" class="form-label">Service Available Message</label>
                                        <input type="text" class="form-control" id="service_available_message" name="service_available_message" 
                                               value="<?php echo htmlspecialchars($settings['service_available_message'] ?? ''); ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="service_unavailable_message" class="form-label">Service Unavailable Message</label>
                                        <input type="text" class="form-control" id="service_unavailable_message" name="service_unavailable_message" 
                                               value="<?php echo htmlspecialchars($settings['service_unavailable_message'] ?? ''); ?>" required>
                                    </div>
                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-save"></i> Save Settings
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pincodes List -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h5>Serviceable Pincodes (<?php echo count($pincodes); ?>)</h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($pincodes)): ?>
                            <p class="text-muted">No serviceable pincodes added yet.</p>
                        <?php else: ?>
                            <div class="row">
                                <?php foreach ($pincodes as $pincode): ?>
                                    <div class="col-md-3 mb-3">
                                        <div class="pincode-card">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <h6 class="mb-1"><?php echo htmlspecialchars($pincode['pincode']); ?></h6>
                                                    <small class="text-muted">
                                                        <?php echo htmlspecialchars($pincode['city'] ?? 'N/A'); ?>, 
                                                        <?php echo htmlspecialchars($pincode['state'] ?? 'N/A'); ?>
                                                    </small>
                                                </div>
                                                <div class="d-flex gap-2">
                                                    <form method="POST" style="display: inline;">
                                                        <input type="hidden" name="action" value="toggle_status">
                                                        <input type="hidden" name="pincode_id" value="<?php echo $pincode['id']; ?>">
                                                        <button type="submit" class="btn btn-sm <?php echo $pincode['is_active'] ? 'btn-success' : 'btn-secondary'; ?>">
                                                            <i class="fas <?php echo $pincode['is_active'] ? 'fa-check' : 'fa-times'; ?>"></i>
                                                        </button>
                                                    </form>
                                                    <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this pincode?');">
                                                        <input type="hidden" name="action" value="delete_pincode">
                                                        <input type="hidden" name="pincode_id" value="<?php echo $pincode['id']; ?>">
                                                        <button type="submit" class="btn btn-sm btn-danger">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                            <div class="mt-2">
                                                <span class="badge <?php echo $pincode['is_active'] ? 'bg-success' : 'bg-secondary'; ?>">
                                                    <?php echo $pincode['is_active'] ? 'Active' : 'Inactive'; ?>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
