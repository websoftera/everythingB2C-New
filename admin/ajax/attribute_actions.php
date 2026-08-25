<?php
session_start();
require_once '../../config/database.php';
require_once '../../includes/functions.php';

header('Content-Type: application/json');

if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit;
}

function adminAttributeSlug($name) {
    $slug = strtolower(trim((string)$name));
    $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
    return trim($slug, '-') ?: 'attribute';
}

function uniqueAdminAttributeSlug(PDO $pdo, $name, $excludeId = null) {
    $base = adminAttributeSlug($name);
    $slug = $base;
    $counter = 2;

    do {
        if ($excludeId) {
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM product_attributes WHERE slug = ? AND id != ?");
            $stmt->execute([$slug, $excludeId]);
        } else {
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM product_attributes WHERE slug = ?");
            $stmt->execute([$slug]);
        }

        if ((int)$stmt->fetchColumn() === 0) {
            return $slug;
        }

        $slug = $base . '-' . $counter++;
    } while (true);
}

function splitAdminAttributeValues($valueString) {
    return array_values(array_unique(array_filter(array_map('trim', explode(',', (string)$valueString)))));
}

$action = $_POST['action'] ?? '';

try {
    switch ($action) {
        case 'add_attribute':
            $name = normalizeProductAttributeName(sanitizeInput($_POST['name'] ?? ''));
            if ($name === '') {
                echo json_encode(['success' => false, 'message' => 'Attribute name is required.']);
                exit;
            }

            $nextOrder = (int)$pdo->query("SELECT COALESCE(MAX(sort_order), 0) + 1 FROM product_attributes")->fetchColumn();
            $stmt = $pdo->prepare("INSERT INTO product_attributes (name, slug, sort_order) VALUES (?, ?, ?)");
            $stmt->execute([$name, uniqueAdminAttributeSlug($pdo, $name), $nextOrder]);
            echo json_encode(['success' => true, 'id' => $pdo->lastInsertId(), 'name' => $name]);
            break;

        case 'edit_attribute':
            $id = (int)($_POST['id'] ?? 0);
            $name = normalizeProductAttributeName(sanitizeInput($_POST['name'] ?? ''));
            if ($id <= 0 || $name === '') {
                echo json_encode(['success' => false, 'message' => 'Invalid data provided.']);
                exit;
            }

            $stmt = $pdo->prepare("UPDATE product_attributes SET name = ?, slug = ? WHERE id = ?");
            $stmt->execute([$name, uniqueAdminAttributeSlug($pdo, $name, $id), $id]);
            echo json_encode(['success' => true]);
            break;

        case 'delete_attribute':
            $id = (int)($_POST['id'] ?? 0);
            if ($id <= 0) {
                echo json_encode(['success' => false, 'message' => 'Invalid attribute ID.']);
                exit;
            }

            $stmt = $pdo->prepare("DELETE FROM product_attributes WHERE id = ?");
            $stmt->execute([$id]);
            echo json_encode(['success' => true]);
            break;

        case 'add_value':
            $attributeId = (int)($_POST['attribute_id'] ?? 0);
            $values = splitAdminAttributeValues($_POST['value'] ?? '');
            if ($attributeId <= 0 || empty($values)) {
                echo json_encode(['success' => false, 'message' => 'Invalid data provided.']);
                exit;
            }

            $nextOrderStmt = $pdo->prepare("SELECT COALESCE(MAX(sort_order), 0) + 1 FROM product_attribute_values WHERE attribute_id = ?");
            $insertStmt = $pdo->prepare("INSERT IGNORE INTO product_attribute_values (attribute_id, value, sort_order) VALUES (?, ?, ?)");
            $newValues = [];
            foreach ($values as $value) {
                $nextOrderStmt->execute([$attributeId]);
                $sortOrder = (int)$nextOrderStmt->fetchColumn();
                $insertStmt->execute([$attributeId, $value, $sortOrder]);

                if ($insertStmt->rowCount() > 0) {
                    $newValues[] = ['id' => $pdo->lastInsertId(), 'value' => $value];
                }
            }

            echo json_encode(['success' => true, 'values' => $newValues]);
            break;

        case 'delete_value':
            $id = (int)($_POST['id'] ?? 0);
            if ($id <= 0) {
                echo json_encode(['success' => false, 'message' => 'Invalid value ID.']);
                exit;
            }

            $stmt = $pdo->prepare("DELETE FROM product_attribute_values WHERE id = ?");
            $stmt->execute([$id]);
            echo json_encode(['success' => true]);
            break;

        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action.']);
            break;
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
