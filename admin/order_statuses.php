<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

if (!canAccess('view_orders')) {
    header('Location: permission-denied.php');
    exit;
}

$pageTitle = 'Manage Order Statuses';
$success_message = $_SESSION['success_message'] ?? '';
$error_message = $_SESSION['error_message'] ?? '';
unset($_SESSION['success_message'], $_SESSION['error_message']);

function ensureOrderStatusesTable(PDO $pdo) {
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS order_statuses (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            description TEXT,
            color VARCHAR(20) DEFAULT '#007bff',
            is_system BOOLEAN DEFAULT FALSE,
            sort_order INT DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");
    ensureDefaultOrderStatuses();
}

ensureOrderStatusesTable($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    try {
        if ($action === 'create') {
            $name = sanitizeInput($_POST['name'] ?? '');
            $description = sanitizeInput($_POST['description'] ?? '');
            $color = sanitizeInput($_POST['color'] ?? '#007bff');

            if ($name === '') {
                throw new Exception('Status name is required.');
            }

            if (!preg_match('/^#[0-9A-Fa-f]{6}$/', $color)) {
                $color = '#007bff';
            }

            if (!createCustomOrderStatus($name, $description, $color)) {
                throw new Exception('Status already exists or could not be created.');
            }

            $_SESSION['success_message'] = 'Order status added successfully.';
        } elseif ($action === 'update') {
            $statusId = (int)($_POST['status_id'] ?? 0);
            $name = sanitizeInput($_POST['name'] ?? '');
            $description = sanitizeInput($_POST['description'] ?? '');
            $color = sanitizeInput($_POST['color'] ?? '#007bff');

            if ($statusId <= 0 || $name === '') {
                throw new Exception('Valid status details are required.');
            }

            if (!preg_match('/^#[0-9A-Fa-f]{6}$/', $color)) {
                $color = '#007bff';
            }

            if (!updateOrderStatusRecord($statusId, $name, $description, $color)) {
                throw new Exception('Only custom statuses can be updated.');
            }

            $_SESSION['success_message'] = 'Order status updated successfully.';
        } elseif ($action === 'delete') {
            $statusId = (int)($_POST['status_id'] ?? 0);
            if ($statusId <= 0 || !deleteCustomOrderStatus($statusId)) {
                throw new Exception('Only unused custom statuses can be deleted.');
            }

            $_SESSION['success_message'] = 'Order status deleted successfully.';
        }
    } catch (Exception $e) {
        $_SESSION['error_message'] = $e->getMessage();
    }

    header('Location: order_statuses.php');
    exit;
}

$statuses = getAllOrderStatuses();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?> - EverythingB2C</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="assets/css/admin.css" rel="stylesheet">
</head>
<body>
<div class="everythingb2c-admin-container">
    <?php include 'includes/sidebar.php'; ?>

    <div class="everythingb2c-main-content">
        <?php include 'includes/header.php'; ?>

        <div class="everythingb2c-dashboard-content">
            <div class="container-fluid">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="h3 mb-0">Manage Order Statuses</h1>
                    <a href="orders.php" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Orders
                    </a>
                </div>

                <?php if ($success_message): ?>
                    <div class="alert alert-success"><?php echo htmlspecialchars($success_message); ?></div>
                <?php endif; ?>
                <?php if ($error_message): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($error_message); ?></div>
                <?php endif; ?>

                <div class="row">
                    <div class="col-lg-8 mb-4">
                        <div class="card">
                            <div class="card-header bg-white">
                                <h5 class="mb-0">Current Statuses</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table align-middle">
                                        <thead>
                                        <tr>
                                            <th>Status</th>
                                            <th>Description</th>
                                            <th>Type</th>
                                            <th style="width:220px;">Actions</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php foreach ($statuses as $status): ?>
                                            <tr>
                                                <td>
                                                    <span class="badge" style="background-color: <?php echo htmlspecialchars($status['color']); ?>;">
                                                        <?php echo htmlspecialchars($status['name']); ?>
                                                    </span>
                                                </td>
                                                <td><?php echo htmlspecialchars($status['description'] ?? ''); ?></td>
                                                <td>
                                                    <?php if ((int)$status['is_system'] === 1): ?>
                                                        <span class="badge bg-secondary">System</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-primary">Custom</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if ((int)$status['is_system'] !== 1): ?>
                                                        <button type="button" class="btn btn-sm btn-outline-primary"
                                                                onclick='editStatus(<?php echo json_encode($status, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT); ?>)'>
                                                            <i class="fas fa-edit"></i> Edit
                                                        </button>
                                                        <form method="POST" class="d-inline" onsubmit="return confirm('Delete this custom status?');">
                                                            <input type="hidden" name="action" value="delete">
                                                            <input type="hidden" name="status_id" value="<?php echo (int)$status['id']; ?>">
                                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    <?php else: ?>
                                                        <span class="text-muted small">Protected</span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4 mb-4">
                        <div class="card">
                            <div class="card-header bg-white">
                                <h5 class="mb-0" id="statusFormTitle">Add Custom Status</h5>
                            </div>
                            <div class="card-body">
                                <form method="POST" id="statusForm">
                                    <input type="hidden" name="action" id="statusAction" value="create">
                                    <input type="hidden" name="status_id" id="statusId" value="">

                                    <div class="mb-3">
                                        <label class="form-label">Status Name *</label>
                                        <input type="text" class="form-control" name="name" id="statusName" required>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Description</label>
                                        <textarea class="form-control" name="description" id="statusDescription" rows="3"></textarea>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Badge Color</label>
                                        <input type="color" class="form-control form-control-color" name="color" id="statusColor" value="#007bff">
                                    </div>

                                    <div class="d-grid gap-2">
                                        <button type="submit" class="btn btn-primary" id="statusSubmit">
                                            <i class="fas fa-plus"></i> Add Status
                                        </button>
                                        <button type="button" class="btn btn-outline-secondary d-none" id="cancelEdit" onclick="resetStatusForm()">
                                            Cancel Edit
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function editStatus(status) {
    document.getElementById('statusFormTitle').textContent = 'Edit Custom Status';
    document.getElementById('statusAction').value = 'update';
    document.getElementById('statusId').value = status.id || '';
    document.getElementById('statusName').value = status.name || '';
    document.getElementById('statusDescription').value = status.description || '';
    document.getElementById('statusColor').value = status.color || '#007bff';
    document.getElementById('statusSubmit').innerHTML = '<i class="fas fa-save"></i> Update Status';
    document.getElementById('cancelEdit').classList.remove('d-none');
}

function resetStatusForm() {
    document.getElementById('statusForm').reset();
    document.getElementById('statusFormTitle').textContent = 'Add Custom Status';
    document.getElementById('statusAction').value = 'create';
    document.getElementById('statusId').value = '';
    document.getElementById('statusColor').value = '#007bff';
    document.getElementById('statusSubmit').innerHTML = '<i class="fas fa-plus"></i> Add Status';
    document.getElementById('cancelEdit').classList.add('d-none');
}
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/admin.js"></script>
</body>
</html>
