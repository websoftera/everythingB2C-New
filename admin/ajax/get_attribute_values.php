<?php
session_start();
require_once '../../config/database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$attributeId = isset($_GET['attribute_id']) ? (int)$_GET['attribute_id'] : 0;
if ($attributeId <= 0) {
    echo json_encode([]);
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT id, value FROM product_attribute_values WHERE attribute_id = ? ORDER BY sort_order ASC, value ASC, id ASC");
    $stmt->execute([$attributeId]);
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
