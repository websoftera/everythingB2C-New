<?php
session_start();
require_once '../../config/database.php';
require_once '../../includes/functions.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

// Check if parent_id is provided
if (!isset($_GET['parent_id']) || empty($_GET['parent_id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Parent ID is required']);
    exit;
}

$parentId = intval($_GET['parent_id']);

try {
    $subcategories = getSubcategoriesByParentId($parentId);
    echo json_encode(['success' => true, 'subcategories' => $subcategories]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?> 