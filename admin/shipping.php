<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';
require_once '../includes/gst_shipping_functions.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

$pageTitle = 'Shipping Management';
$success_message = '';
$error_message = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add_zone':
                $name = trim($_POST['name']);
                $description = trim($_POST['description']);
                
                if (empty($name)) {
                    $error_message = 'Zone name is required.';
                } else {
                    try {
                        $stmt = $pdo->prepare("INSERT INTO shipping_zones (name, description) VALUES (?, ?)");
                        $stmt->execute([$name, $description]);
                        header('Location: shipping.php?success=add_zone'); exit;
                    } catch (Exception $e) {
                        $error_message = 'Error adding shipping zone: ' . $e->getMessage();
                    }
                }
                break;
                
            case 'add_location':
                $zone_id = intval($_POST['zone_id']);
                $location_type = $_POST['location_type'];
                $location_value = trim($_POST['location_value']);
                
                if (empty($location_value)) {
                    $error_message = 'Location value is required.';
                } else {
                    try {
                        $stmt = $pdo->prepare("INSERT INTO shipping_zone_locations (zone_id, location_type, location_value) VALUES (?, ?, ?)");
                        $stmt->execute([$zone_id, $location_type, $location_value]);
                        header('Location: shipping.php?success=add_location'); exit;
                    } catch (Exception $e) {
                        $error_message = 'Error adding location: ' . $e->getMessage();
                    }
                }
                break;
                
            case 'add_charge':
                $zone_id = intval($_POST['zone_id']);
                $charge_type = $_POST['charge_type'];
                $charge_value = floatval($_POST['charge_value']);
                $min_order_amount = floatval($_POST['min_order_amount']);
                $max_order_amount = !empty($_POST['max_order_amount']) ? floatval($_POST['max_order_amount']) : null;
                
                if ($charge_value < 0) {
                    $error_message = 'Charge value cannot be negative.';
                } else {
                    try {
                        $stmt = $pdo->prepare("INSERT INTO shipping_charges (zone_id, charge_type, charge_value, min_order_amount, max_order_amount) VALUES (?, ?, ?, ?, ?)");
                        $stmt->execute([$zone_id, $charge_type, $charge_value, $min_order_amount, $max_order_amount]);
                        header('Location: shipping.php?success=add_charge'); exit;
                    } catch (Exception $e) {
                        $error_message = 'Error adding shipping charge: ' . $e->getMessage();
                    }
                }
                break;
                
            case 'delete_zone':
                $zone_id = intval($_POST['zone_id']);
                try {
                    $pdo->beginTransaction();
                    
                    // Delete shipping charges
                    $stmt = $pdo->prepare("DELETE FROM shipping_charges WHERE zone_id = ?");
                    $stmt->execute([$zone_id]);
                    
                    // Delete zone locations
                    $stmt = $pdo->prepare("DELETE FROM shipping_zone_locations WHERE zone_id = ?");
                    $stmt->execute([$zone_id]);
                    
                    // Delete zone
                    $stmt = $pdo->prepare("DELETE FROM shipping_zones WHERE id = ?");
                    $stmt->execute([$zone_id]);
                    
                    $pdo->commit();
                    header('Location: shipping.php?success=delete_zone'); exit;
                } catch (Exception $e) {
                    $pdo->rollBack();
                    $error_message = 'Error deleting shipping zone: ' . $e->getMessage();
                }
                break;

            case 'edit_zone':
                $zone_id = intval($_POST['zone_id']);
                $name = trim($_POST['name']);
                $description = trim($_POST['description']);
                if (empty($name)) {
                    $error_message = 'Zone name is required.';
                } else {
                    try {
                        $stmt = $pdo->prepare("UPDATE shipping_zones SET name = ?, description = ? WHERE id = ?");
                        $stmt->execute([$name, $description, $zone_id]);
                        header('Location: shipping.php?success=edit_zone'); exit;
                    } catch (Exception $e) {
                        $error_message = 'Error updating shipping zone: ' . $e->getMessage();
                    }
                }
                break;

            case 'edit_charge':
                $charge_id = intval($_POST['charge_id']);
                $charge_type = $_POST['charge_type'];
                $charge_value = floatval($_POST['charge_value']);
                $min_order_amount = floatval($_POST['min_order_amount']);
                $max_order_amount = !empty($_POST['max_order_amount']) ? floatval($_POST['max_order_amount']) : null;
                try {
                    $stmt = $pdo->prepare("UPDATE shipping_charges SET charge_type = ?, charge_value = ?, min_order_amount = ?, max_order_amount = ? WHERE id = ?");
                    $stmt->execute([$charge_type, $charge_value, $min_order_amount, $max_order_amount, $charge_id]);
                    header('Location: shipping.php?success=edit_charge'); exit;
                } catch (Exception $e) {
                    $error_message = 'Error updating shipping charge: ' . $e->getMessage();
                }
                break;

            case 'delete_charge':
                $charge_id = intval($_POST['charge_id']);
                try {
                    $stmt = $pdo->prepare("DELETE FROM shipping_charges WHERE id = ?");
                    $stmt->execute([$charge_id]);
                    header('Location: shipping.php?success=delete_charge'); exit;
                } catch (Exception $e) {
                    $error_message = 'Error deleting shipping charge: ' . $e->getMessage();
                }
                break;
        }
    }
}

// Get shipping zones
$zones = getAllShippingZones();

// Get Indian states and cities
$states = getIndianStates();
$cities = getCommonCities();

if (isset($_GET['success'])) {
    switch ($_GET['success']) {
        case 'add_zone': $success_message = 'Shipping zone added successfully!'; break;
        case 'edit_zone': $success_message = 'Shipping zone updated successfully!'; break;
        case 'delete_zone': $success_message = 'Shipping zone deleted successfully!'; break;
        case 'add_location': $success_message = 'Location added to zone successfully!'; break;
        case 'add_charge': $success_message = 'Shipping charge added successfully!'; break;
        case 'edit_charge': $success_message = 'Shipping charge updated successfully!'; break;
        case 'delete_charge': $success_message = 'Shipping charge deleted successfully!'; break;
    }
}
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

            <!-- Shipping Content -->
            <div class="dashboard-content">
                <div class="container-fluid">
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center">
                                <h1 class="h3 mb-0">Shipping Management</h1>
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addZoneModal">
                                    <i class="fas fa-plus"></i> Add Shipping Zone
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

                    <!-- Shipping Zones -->
                    <div class="row">
                        <?php foreach ($zones as $zone): ?>
                            <div class="col-md-6 col-lg-4 mb-4">
                                <div class="card h-100">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <h5 class="mb-0"><?php echo htmlspecialchars($zone['name']); ?></h5>
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li><a class="dropdown-item" href="#" onclick="addLocation(<?php echo $zone['id']; ?>)">
                                                    <i class="fas fa-map-marker-alt"></i> Add Location
                                                </a></li>
                                                <li><a class="dropdown-item" href="#" onclick="addCharge(<?php echo $zone['id']; ?>)">
                                                    <i class="fas fa-plus"></i> Add Charge
                                                </a></li>
                                                <li><a class="dropdown-item" href="#" onclick="editZone(<?php echo $zone['id']; ?>, '<?php echo htmlspecialchars(addslashes($zone['name'])); ?>', '<?php echo htmlspecialchars(addslashes($zone['description'])); ?>')"><i class="fas fa-edit"></i> Edit Zone</a></li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li><a class="dropdown-item text-danger" href="#" onclick="deleteZone(<?php echo $zone['id']; ?>, '<?php echo htmlspecialchars($zone['name']); ?>')">
                                                    <i class="fas fa-trash"></i> Delete Zone
                                                </a></li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <p class="text-muted"><?php echo htmlspecialchars($zone['description']); ?></p>
                                        
                                        <!-- Zone Locations -->
                                        <h6>Locations:</h6>
                                        <?php 
                                        $locations = getShippingZoneLocations($zone['id']);
                                        if (empty($locations)): ?>
                                            <p class="text-muted small">No locations added</p>
                                        <?php else: ?>
                                            <div class="mb-3">
                                                <?php foreach ($locations as $location): ?>
                                                    <span class="badge bg-light text-dark me-1 mb-1">
                                                        <?php echo ucfirst($location['location_type']); ?>: <?php echo htmlspecialchars($location['location_value']); ?>
                                                    </span>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <!-- Zone Charges -->
                                        <h6>Charges:</h6>
                                        <?php 
                                        $charges = getShippingCharges($zone['id']);
                                        if (empty($charges)): ?>
                                            <p class="text-muted small">No charges configured</p>
                                        <?php else: ?>
                                            <div class="table-responsive">
                                                <table class="table table-sm">
                                                    <thead>
                                                        <tr>
                                                            <th>Type</th>
                                                            <th>Value</th>
                                                            <th>Min Order</th>
                                                            <th>Actions</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php foreach ($charges as $charge): ?>
                                                            <tr>
                                                                <td><?php echo ucfirst($charge['charge_type']); ?></td>
                                                                <td>
                                                                    <?php if ($charge['charge_type'] === 'percentage'): ?>
                                                                        <?php echo $charge['charge_value']; ?>%
                                                                    <?php else: ?>
                                                                        ₹<?php echo $charge['charge_value']; ?>
                                                                    <?php endif; ?>
                                                                </td>
                                                                <td>₹<?php echo $charge['min_order_amount']; ?></td>
                                                                <td>
                                                                    <button class="btn btn-sm btn-outline-primary me-1" onclick="editCharge(<?php echo $charge['id']; ?>, '<?php echo htmlspecialchars($charge['charge_type']); ?>', <?php echo $charge['charge_value']; ?>, <?php echo $charge['min_order_amount']; ?>, <?php echo $charge['max_order_amount']; ?>)"><i class="fas fa-edit"></i></button>
                                                                    <form method="post" style="display:inline;" onsubmit="return confirm('Delete this charge?');">
                                                                        <input type="hidden" name="action" value="delete_charge">
                                                                        <input type="hidden" name="charge_id" value="<?php echo $charge['id']; ?>">
                                                                        <button type="submit" class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                                                                    </form>
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
                        <?php endforeach; ?>
                    </div>

                    <?php if (empty($zones)): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-shipping-fast fa-3x text-muted mb-3"></i>
                            <h5>No shipping zones configured</h5>
                            <p class="text-muted">Add your first shipping zone to get started.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Zone Modal -->
    <div class="modal fade" id="addZoneModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Shipping Zone</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="add_zone">
                        
                        <div class="mb-3">
                            <label for="name" class="form-label">Zone Name *</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Zone</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Add Location Modal -->
    <div class="modal fade" id="addLocationModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Location to Zone</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="add_location">
                        <input type="hidden" name="zone_id" id="location_zone_id">
                        
                        <div class="mb-3">
                            <label for="location_type" class="form-label">Location Type *</label>
                            <select class="form-control" id="location_type" name="location_type" required>
                                <option value="">Select Type</option>
                                <option value="country">Country</option>
                                <option value="state">State</option>
                                <option value="city">City</option>
                                <option value="pincode">Pincode</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="location_value" class="form-label">Location Value *</label>
                            <input type="text" class="form-control" id="location_value" name="location_value" required>
                            <div class="form-text">Enter the specific location (e.g., "Maharashtra" for state, "Mumbai" for city)</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Location</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Add Charge Modal -->
    <div class="modal fade" id="addChargeModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Shipping Charge</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="add_charge">
                        <input type="hidden" name="zone_id" id="charge_zone_id">
                        
                        <div class="mb-3">
                            <label for="charge_type" class="form-label">Charge Type *</label>
                            <select class="form-control" id="charge_type" name="charge_type" required>
                                <option value="">Select Type</option>
                                <option value="fixed">Fixed Amount</option>
                                <option value="percentage">Percentage of Order</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="charge_value" class="form-label">Charge Value *</label>
                            <input type="number" class="form-control" id="charge_value" name="charge_value" step="0.01" min="0" required>
                            <div class="form-text">Enter amount in ₹ for fixed, or percentage (e.g., 5 for 5%)</div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="min_order_amount" class="form-label">Minimum Order Amount</label>
                                    <input type="number" class="form-control" id="min_order_amount" name="min_order_amount" step="0.01" min="0" value="0">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="max_order_amount" class="form-label">Maximum Order Amount</label>
                                    <input type="number" class="form-control" id="max_order_amount" name="max_order_amount" step="0.01" min="0">
                                    <div class="form-text">Leave empty for no maximum</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Charge</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Zone Modal -->
    <div class="modal fade" id="editZoneModal" tabindex="-1" aria-labelledby="editZoneModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="post" id="editZoneForm">
                    <input type="hidden" name="action" value="edit_zone">
                    <input type="hidden" name="zone_id" id="edit_zone_id">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editZoneModalLabel">Edit Shipping Zone</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="edit_zone_name" class="form-label">Zone Name</label>
                            <input type="text" class="form-control" id="edit_zone_name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_zone_description" class="form-label">Description</label>
                            <textarea class="form-control" id="edit_zone_description" name="description" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Zone Form -->
    <form method="POST" id="deleteZoneForm" style="display: none;">
        <input type="hidden" name="action" value="delete_zone">
        <input type="hidden" name="zone_id" id="delete_zone_id">
    </form>

    <!-- Edit Charge Modal -->
    <div class="modal fade" id="editChargeModal" tabindex="-1" aria-labelledby="editChargeModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="post" id="editChargeForm">
                    <input type="hidden" name="action" value="edit_charge">
                    <input type="hidden" name="charge_id" id="edit_charge_id">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editChargeModalLabel">Edit Shipping Charge</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="edit_charge_type" class="form-label">Charge Type</label>
                            <select class="form-control" id="edit_charge_type" name="charge_type" required>
                                <option value="fixed">Fixed</option>
                                <option value="percentage">Percentage</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="edit_charge_value" class="form-label">Charge Value</label>
                            <input type="number" class="form-control" id="edit_charge_value" name="charge_value" step="0.01" min="0" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_min_order_amount" class="form-label">Min Order Amount</label>
                            <input type="number" class="form-control" id="edit_min_order_amount" name="min_order_amount" step="0.01" min="0">
                        </div>
                        <div class="mb-3">
                            <label for="edit_max_order_amount" class="form-label">Max Order Amount</label>
                            <input type="number" class="form-control" id="edit_max_order_amount" name="max_order_amount" step="0.01" min="0">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/admin.js"></script>
    <script>
        function addLocation(zoneId) {
            document.getElementById('location_zone_id').value = zoneId;
            new bootstrap.Modal(document.getElementById('addLocationModal')).show();
        }

        function addCharge(zoneId) {
            document.getElementById('charge_zone_id').value = zoneId;
            new bootstrap.Modal(document.getElementById('addChargeModal')).show();
        }

        function editZone(id, name, description) {
            document.getElementById('edit_zone_id').value = id;
            document.getElementById('edit_zone_name').value = name;
            document.getElementById('edit_zone_description').value = description;
            var modal = new bootstrap.Modal(document.getElementById('editZoneModal'));
            modal.show();
        }

        function deleteZone(zoneId, zoneName) {
            if (confirm(`Are you sure you want to delete the shipping zone "${zoneName}"? This will also delete all associated locations and charges.`)) {
                document.getElementById('delete_zone_id').value = zoneId;
                document.getElementById('deleteZoneForm').submit();
            }
        }

        function editCharge(id, type, value, min, max) {
            document.getElementById('edit_charge_id').value = id;
            document.getElementById('edit_charge_type').value = type;
            document.getElementById('edit_charge_value').value = value;
            document.getElementById('edit_min_order_amount').value = min;
            document.getElementById('edit_max_order_amount').value = max || '';
            var modal = new bootstrap.Modal(document.getElementById('editChargeModal'));
            modal.show();
        }
    </script>
</body>
</html> 