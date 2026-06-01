<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

$pageTitle = 'Product Attributes';

try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS product_attributes (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(150) NOT NULL,
        slug VARCHAR(170) NOT NULL UNIQUE,
        sort_order INT DEFAULT 0,
        is_active TINYINT(1) DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )");

    $pdo->exec("CREATE TABLE IF NOT EXISTS product_attribute_values (
        id INT AUTO_INCREMENT PRIMARY KEY,
        attribute_id INT NOT NULL,
        value VARCHAR(150) NOT NULL,
        sort_order INT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY unique_attribute_value (attribute_id, value),
        INDEX idx_attribute_values_attribute (attribute_id),
        CONSTRAINT fk_attribute_values_attribute
            FOREIGN KEY (attribute_id) REFERENCES product_attributes(id)
            ON DELETE CASCADE
    )");
} catch (PDOException $e) {
    die('Database error: ' . htmlspecialchars($e->getMessage()));
}

function attributeSlug($name) {
    $slug = strtolower(trim($name));
    $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
    $slug = trim($slug, '-');
    return $slug ?: 'attribute';
}

function uniqueAttributeSlug($pdo, $name, $ignoreId = null) {
    $base = attributeSlug($name);
    $slug = $base;
    $counter = 2;

    while (true) {
        if ($ignoreId) {
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM product_attributes WHERE slug = ? AND id != ?");
            $stmt->execute([$slug, $ignoreId]);
        } else {
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM product_attributes WHERE slug = ?");
            $stmt->execute([$slug]);
        }

        if ((int)$stmt->fetchColumn() === 0) {
            return $slug;
        }

        $slug = $base . '-' . $counter;
        $counter++;
    }
}

function splitAttributeValues($rawValues) {
    $items = preg_split('/[\r\n,]+/', $rawValues);
    $values = [];

    foreach ($items as $item) {
        $value = trim($item);
        if ($value !== '' && !in_array(strtolower($value), array_map('strtolower', $values), true)) {
            $values[] = $value;
        }
    }

    return $values;
}

function insertAttributeValues($pdo, $attributeId, $values) {
    if (empty($values)) {
        return;
    }

    $nextOrderStmt = $pdo->prepare("SELECT COALESCE(MAX(sort_order), 0) + 1 FROM product_attribute_values WHERE attribute_id = ?");
    $insertStmt = $pdo->prepare("INSERT IGNORE INTO product_attribute_values (attribute_id, value, sort_order) VALUES (?, ?, ?)");

    foreach ($values as $value) {
        $nextOrderStmt->execute([$attributeId]);
        $sortOrder = (int)$nextOrderStmt->fetchColumn();
        $insertStmt->execute([$attributeId, $value, $sortOrder]);
    }
}

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    $action = $_POST['action'] ?? '';

    try {
        if ($action === 'add_attribute') {
            $name = trim($_POST['attribute_name'] ?? '');
            $values = splitAttributeValues($_POST['attribute_values'] ?? '');

            if ($name === '') {
                $_SESSION['error_message'] = 'Attribute name is required.';
            } else {
                $nextOrder = (int)$pdo->query("SELECT COALESCE(MAX(sort_order), 0) + 1 FROM product_attributes")->fetchColumn();
                $stmt = $pdo->prepare("INSERT INTO product_attributes (name, slug, sort_order) VALUES (?, ?, ?)");
                $stmt->execute([$name, uniqueAttributeSlug($pdo, $name), $nextOrder]);
                insertAttributeValues($pdo, (int)$pdo->lastInsertId(), $values);
                $_SESSION['success_message'] = 'Attribute added successfully.';
            }
        }

        if ($action === 'edit_attribute') {
            $attributeId = (int)($_POST['attribute_id'] ?? 0);
            $name = trim($_POST['attribute_name'] ?? '');

            if ($attributeId <= 0 || $name === '') {
                $_SESSION['error_message'] = 'Attribute name is required.';
            } else {
                $stmt = $pdo->prepare("UPDATE product_attributes SET name = ?, slug = ? WHERE id = ?");
                $stmt->execute([$name, uniqueAttributeSlug($pdo, $name, $attributeId), $attributeId]);
                $_SESSION['success_message'] = 'Attribute updated successfully.';
            }
        }

        if ($action === 'delete_attribute') {
            $attributeId = (int)($_POST['attribute_id'] ?? 0);
            $stmt = $pdo->prepare("DELETE FROM product_attributes WHERE id = ?");
            $stmt->execute([$attributeId]);
            $_SESSION['success_message'] = 'Attribute deleted successfully.';
        }

        if ($action === 'add_value') {
            $attributeId = (int)($_POST['attribute_id'] ?? 0);
            $values = splitAttributeValues($_POST['attribute_value'] ?? '');

            if ($attributeId <= 0 || empty($values)) {
                $_SESSION['error_message'] = 'Please enter a value.';
            } else {
                insertAttributeValues($pdo, $attributeId, $values);
                $_SESSION['success_message'] = 'Value added successfully.';
            }
        }

        if ($action === 'delete_value') {
            $valueId = (int)($_POST['value_id'] ?? 0);
            $stmt = $pdo->prepare("DELETE FROM product_attribute_values WHERE id = ?");
            $stmt->execute([$valueId]);
            $_SESSION['success_message'] = 'Value removed successfully.';
        }
    } catch (PDOException $e) {
        $_SESSION['error_message'] = 'Database error: ' . $e->getMessage();
    }

    header('Location: product_attributes.php');
    exit;
}

$stmt = $pdo->query("
    SELECT a.*, COUNT(v.id) AS value_count
    FROM product_attributes a
    LEFT JOIN product_attribute_values v ON v.attribute_id = a.id
    GROUP BY a.id
    ORDER BY a.sort_order ASC, a.name ASC
");
$attributes = $stmt->fetchAll(PDO::FETCH_ASSOC);

$valuesStmt = $pdo->query("SELECT * FROM product_attribute_values ORDER BY sort_order ASC, value ASC");
$attributeValues = [];
foreach ($valuesStmt->fetchAll(PDO::FETCH_ASSOC) as $value) {
    $attributeValues[$value['attribute_id']][] = $value;
}
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
    <style>
        .attributes-page {
            background: #f6f7fb;
            color: #061426;
            min-height: calc(100vh - 72px);
        }

        .attributes-page h1 {
            font-size: 34px;
            font-weight: 500;
            margin-bottom: 4px;
        }

        .attributes-subtitle {
            color: #667085;
            font-size: 17px;
            margin: 0;
        }

        .attributes-add-btn {
            background: #c9283d;
            border: 0;
            border-radius: 10px;
            box-shadow: none;
            color: #fff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            height: 42px;
            min-height: 42px;
            min-width: 178px;
            font-size: 15px;
            font-weight: 600;
            line-height: 1;
            padding: 0 18px;
            white-space: nowrap;
        }

        .attributes-add-btn i {
            font-size: 15px;
            margin-right: 0 !important;
        }

        .attributes-add-btn:hover,
        .attributes-add-btn:focus {
            background: #b82235;
            color: #fff;
        }

        .attribute-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 30px;
            justify-content: start;
        }

        .attribute-card {
            background: #fff;
            border-radius: 14px;
            box-shadow: 0 12px 34px rgba(16, 24, 40, 0.06);
            min-height: 209px;
            padding: 20px;
            width: 100%;
        }

        .attribute-card-header {
            display: grid;
            grid-template-columns: 48px 1fr auto;
            align-items: center;
            gap: 12px;
            margin-bottom: 24px;
        }

        .attribute-icon {
            width: 46px;
            height: 46px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #eef5ff;
            border-radius: 10px;
            color: #3f83f8;
            font-size: 18px;
        }

        .attribute-name {
            font-size: 20px;
            font-weight: 700;
            margin: 0;
            word-break: break-word;
        }

        .attribute-actions {
            display: flex;
            gap: 10px;
        }

        .attribute-icon-btn {
            border: 0;
            background: transparent;
            color: #a9b4c0;
            font-size: 17px;
            line-height: 1;
            padding: 0;
        }

        .attribute-icon-btn:hover {
            color: #4f5f70;
        }

        .attribute-values {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 10px;
        }

        .attribute-chip,
        .attribute-add-value {
            min-height: 36px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border: 1px solid #e3e8ef;
            border-radius: 8px;
            background: #fbfcfe;
            color: #344054;
            font-size: 15px;
            font-weight: 500;
            padding: 7px 14px;
        }

        .attribute-chip button {
            border: 0;
            background: transparent;
            color: #9aa6b2;
            font-size: 18px;
            line-height: 1;
            padding: 0;
        }

        .attribute-add-value {
            border-style: dashed;
            background: #fff;
            color: #566579;
        }

        .attribute-empty {
            border: 1px dashed #cfd8e3;
            border-radius: 14px;
            color: #667085;
            padding: 42px;
            text-align: center;
        }

        .attribute-delete-popup {
            width: 390px !important;
            padding: 24px 26px 26px !important;
            border-radius: 8px !important;
        }

        .attribute-delete-popup .swal2-icon {
            width: 74px;
            height: 74px;
            margin: 8px auto 22px;
        }

        .attribute-delete-popup .swal2-title {
            font-size: 26px;
            font-weight: 700;
            margin-bottom: 12px;
        }

        .attribute-delete-popup .swal2-html-container {
            font-size: 16px;
            line-height: 1.35;
            margin: 0 0 22px;
        }

        .attribute-delete-popup .swal2-actions {
            margin-top: 0;
            gap: 8px;
        }

        .attribute-delete-popup .swal2-styled {
            border-radius: 4px !important;
            font-size: 15px !important;
            font-weight: 700 !important;
            padding: 11px 18px !important;
        }

        @media (max-width: 1200px) {
            .attribute-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 768px) {
            .attribute-grid {
                grid-template-columns: 1fr;
            }

            .attributes-page h1 {
                font-size: 28px;
            }
        }
    </style>
</head>
<body>
    <div class="everythingb2c-admin-container">
        <?php include 'includes/sidebar.php'; ?>

        <div class="everythingb2c-main-content">
            <?php include 'includes/header.php'; ?>

            <div class="everythingb2c-dashboard-content attributes-page">
                <div class="container-fluid">
                    <div class="d-flex justify-content-between align-items-start mb-4">
                        <div>
                            <h1>Product Attributes</h1>
                            <p class="attributes-subtitle">Manage filtering options like size, color, etc.</p>
                        </div>
                        <button type="button" class="attributes-add-btn" data-bs-toggle="modal" data-bs-target="#addAttributeModal">
                            <i class="fas fa-plus me-2"></i> Add Attribute
                        </button>
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

                    <?php if (empty($attributes)): ?>
                        <div class="attribute-empty">
                            <i class="fas fa-sliders-h fa-2x mb-3"></i>
                            <h5>No attributes found</h5>
                            <p class="mb-0">Add size, color, material, or any other product filter option.</p>
                        </div>
                    <?php else: ?>
                        <div class="attribute-grid">
                            <?php foreach ($attributes as $attribute): ?>
                                <div class="attribute-card">
                                    <div class="attribute-card-header">
                                        <span class="attribute-icon"><i class="fas fa-bookmark"></i></span>
                                        <h2 class="attribute-name"><?php echo htmlspecialchars($attribute['name']); ?></h2>
                                        <div class="attribute-actions">
                                            <button type="button"
                                                class="attribute-icon-btn edit-attribute-btn"
                                                title="Edit attribute"
                                                data-bs-toggle="modal"
                                                data-bs-target="#editAttributeModal"
                                                data-attribute-id="<?php echo $attribute['id']; ?>"
                                                data-attribute-name="<?php echo htmlspecialchars($attribute['name'], ENT_QUOTES); ?>">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <form method="POST"
                                                class="d-inline delete-attribute-form"
                                                data-attribute-name="<?php echo htmlspecialchars($attribute['name'], ENT_QUOTES); ?>">
                                                <input type="hidden" name="action" value="delete_attribute">
                                                <input type="hidden" name="attribute_id" value="<?php echo $attribute['id']; ?>">
                                                <button type="submit" class="attribute-icon-btn" title="Delete attribute">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>

                                    <div class="attribute-values">
                                        <?php foreach (($attributeValues[$attribute['id']] ?? []) as $value): ?>
                                            <form method="POST" class="attribute-chip m-0">
                                                <?php echo htmlspecialchars($value['value']); ?>
                                                <input type="hidden" name="action" value="delete_value">
                                                <input type="hidden" name="value_id" value="<?php echo $value['id']; ?>">
                                                <button type="submit" title="Remove value">&times;</button>
                                            </form>
                                        <?php endforeach; ?>
                                        <button type="button"
                                            class="attribute-add-value add-value-btn"
                                            data-bs-toggle="modal"
                                            data-bs-target="#addValueModal"
                                            data-attribute-id="<?php echo $attribute['id']; ?>"
                                            data-attribute-name="<?php echo htmlspecialchars($attribute['name'], ENT_QUOTES); ?>">
                                            + Add Value
                                        </button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="addAttributeModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title">Add Attribute</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="action" value="add_attribute">
                        <div class="mb-3">
                            <label class="form-label" for="attribute_name">Attribute Name *</label>
                            <input type="text" class="form-control" id="attribute_name" name="attribute_name" placeholder="Example: Safety Shoes Size" required>
                        </div>
                        <div class="mb-0">
                            <label class="form-label" for="attribute_values">Values</label>
                            <textarea class="form-control" id="attribute_values" name="attribute_values" rows="3" placeholder="Example: 6, 7, 8, 9, 10"></textarea>
                            <div class="form-text">Separate multiple values with commas or new lines.</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Attribute</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editAttributeModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Attribute</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="action" value="edit_attribute">
                        <input type="hidden" name="attribute_id" id="edit_attribute_id">
                        <label class="form-label" for="edit_attribute_name">Attribute Name *</label>
                        <input type="text" class="form-control" id="edit_attribute_name" name="attribute_name" required>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Attribute</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="addValueModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title">Add Value</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="action" value="add_value">
                        <input type="hidden" name="attribute_id" id="value_attribute_id">
                        <div class="mb-2 text-muted" id="value_attribute_label"></div>
                        <label class="form-label" for="attribute_value">Value *</label>
                        <input type="text" class="form-control" id="attribute_value" name="attribute_value" placeholder="Example: White" required>
                        <div class="form-text">You can add multiple values with commas.</div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Value</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="assets/js/admin.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.edit-attribute-btn').forEach(function (button) {
                button.addEventListener('click', function () {
                    document.getElementById('edit_attribute_id').value = button.dataset.attributeId;
                    document.getElementById('edit_attribute_name').value = button.dataset.attributeName || '';
                });
            });

            document.querySelectorAll('.add-value-btn').forEach(function (button) {
                button.addEventListener('click', function () {
                    document.getElementById('value_attribute_id').value = button.dataset.attributeId;
                    document.getElementById('value_attribute_label').textContent = button.dataset.attributeName || '';
                    document.getElementById('attribute_value').value = '';
                });
            });

            document.querySelectorAll('.delete-attribute-form').forEach(function (form) {
                form.addEventListener('submit', function (event) {
                    event.preventDefault();

                    Swal.fire({
                        title: 'Are you sure?',
                        text: 'Deleting this attribute will also delete all its values!',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#e62f49',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Yes, delete it!',
                        cancelButtonText: 'Cancel',
                        width: '390px',
                        customClass: {
                            popup: 'attribute-delete-popup'
                        }
                    }).then(function (result) {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });
        });
    </script>
</body>
</html>
