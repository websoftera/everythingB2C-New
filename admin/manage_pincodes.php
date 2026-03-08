<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$pageTitle = 'Manage Pincodes';
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';
require_once 'includes/auth-check.php';
require_once '../includes/delivery_popup_functions.php';

// Check permission
checkAdminPermission('manage_pincodes');

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

// Get data - with error handling
$pincodes = [];
$settings = [];

try {
    $pincodes = getAllServiceablePincodes();
} catch (Exception $e) {
    $error = "Error loading pincodes: " . $e->getMessage();
    $pincodes = [];
}

try {
    $settings = getPopupSettings();
} catch (Exception $e) {
    $error = "Error loading settings: " . $e->getMessage();
    $settings = [];
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
    <link rel="stylesheet" href="css/admin-styles.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .container-main {
            max-width: 1400px;
            margin: 0 auto;
            padding: 30px 15px;
        }
        .pincode-card {
            background: white;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }
        .status-active {
            color: #28a745;
            font-weight: 600;
        }
        .status-inactive {
            color: #dc3545;
            font-weight: 600;
        }
        .alert {
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .card {
            border-radius: 8px;
            border: 1px solid #e0e0e0;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }
        .card-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid #e0e0e0;
            border-radius: 8px 8px 0 0 !important;
        }
        .card-header h5 {
            margin: 0;
            color: #333;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <?php require_once 'includes/header.php'; ?>
    
    <div class="container-main">
        <div class="row">
            <div class="col-md-12">
                <h2 class="mb-4"><i class="fas fa-map-marker-alt"></i> Manage Serviceable Pincodes</h2>
                
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
                    <!-- Add Pincodes Section -->
                    <div class="col-md-6 mb-4">
                        <div class="card">
                            <div class="card-header">
                                <h5><i class="fas fa-plus"></i> Add Serviceable Pincodes</h5>
                            </div>
                            <div class="card-body">
                                <form method="POST">
                                    <input type="hidden" name="action" value="add_pincodes">
                                    <div class="mb-3">
                                        <label for="pincodes" class="form-label">Pincodes (comma-separated) *</label>
                                        <textarea class="form-control" id="pincodes" name="pincodes" rows="4" 
                                                  placeholder="Enter pincodes separated by commas (e.g., 411001, 411002, 411003)" required></textarea>
                                        <div class="form-text">Enter 6-digit pincodes separated by commas</div>
                                    </div>
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="fas fa-plus"></i> Add Pincodes
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Popup Settings Section -->
                    <div class="col-md-6 mb-4">
                        <div class="card">
                            <div class="card-header">
                                <h5><i class="fas fa-cog"></i> Popup Settings</h5>
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
                                    <button type="submit" class="btn btn-success w-100">
                                        <i class="fas fa-save"></i> Save Settings
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Pincodes List -->
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-list"></i> Serviceable Pincodes (<?php echo count($pincodes); ?>)</h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($pincodes)): ?>
                            <div class="alert alert-info mb-0">
                                <i class="fas fa-info-circle"></i> No serviceable pincodes added yet.
                            </div>
                        <?php else: ?>
                            <div class="row">
                                <?php foreach ($pincodes as $pincode): ?>
                                    <div class="col-md-6 col-lg-4 mb-3">
                                        <div class="pincode-card">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <div class="flex-grow-1">
                                                    <h6 class="mb-1"><strong><?php echo htmlspecialchars($pincode['pincode']); ?></strong></h6>
                                                    <small class="text-muted">
                                                        <?php echo htmlspecialchars($pincode['city'] ?? 'N/A'); ?>, 
                                                        <?php echo htmlspecialchars($pincode['state'] ?? 'N/A'); ?>
                                                    </small>
                                                </div>
                                                <div class="ms-2">
                                                    <span class="badge <?php echo $pincode['is_active'] ? 'bg-success' : 'bg-secondary'; ?>">
                                                        <?php echo $pincode['is_active'] ? 'Active' : 'Inactive'; ?>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="d-flex gap-2 mt-3">
                                                <form method="POST" style="display: inline; flex: 1;" class="d-grid">
                                                    <input type="hidden" name="action" value="toggle_status">
                                                    <input type="hidden" name="pincode_id" value="<?php echo $pincode['id']; ?>">
                                                    <button type="submit" class="btn btn-sm <?php echo $pincode['is_active'] ? 'btn-success' : 'btn-secondary'; ?>">
                                                        <i class="fas <?php echo $pincode['is_active'] ? 'fa-check' : 'fa-times'; ?>"></i>
                                                        <?php echo $pincode['is_active'] ? 'Active' : 'Inactive'; ?>
                                                    </button>
                                                </form>
                                                <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this pincode?');">
                                                    <input type="hidden" name="action" value="delete_pincode">
                                                    <input type="hidden" name="pincode_id" value="<?php echo $pincode['id']; ?>">
                                                    <button type="submit" class="btn btn-sm btn-danger">
                                                        <i class="fas fa-trash"></i> Delete
                                                    </button>
                                                </form>
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
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
