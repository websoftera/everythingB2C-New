<?php
session_start();
header('Content-Type: application/json');

require_once '../includes/functions.php';

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);
$token = isset($input['token']) ? $input['token'] : '';
$password = isset($input['password']) ? $input['password'] : '';

if (empty($token) || empty($password)) {
    echo json_encode(['success' => false, 'message' => 'All fields are required.']);
    exit;
}

if (strlen($password) < 6) {
    echo json_encode(['success' => false, 'message' => 'Password must be at least 6 characters long.']);
    exit;
}

try {
    global $pdo;
    
    // Check if token is valid and not expired
    $stmt = $pdo->prepare("SELECT id FROM users WHERE reset_token = ? AND reset_token_expiry > NOW() AND is_active = 1");
    $stmt->execute([$token]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        // Update password and clear token
        $updateStmt = $pdo->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_token_expiry = NULL WHERE id = ?");
        
        if ($updateStmt->execute([$hashedPassword, $user['id']])) {
            echo json_encode(['success' => true, 'message' => 'Your password has been successfully updated. Redirecting to login...']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update password. Please try again.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid or expired token.']);
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()]);
}
?>
