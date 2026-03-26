<?php
session_start();
header('Content-Type: application/json');

require_once '../includes/functions.php';
require_once '../includes/email_functions.php';

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);
$email = isset($input['email']) ? trim($input['email']) : '';

if (empty($email)) {
    echo json_encode(['success' => false, 'message' => 'Please enter your email address.']);
    exit;
}

try {
    global $pdo;
    
    // Check if user exists
    $stmt = $pdo->prepare("SELECT id, name FROM users WHERE email = ? AND is_active = 1");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user) {
        // Generate a unique token
        $token = bin2hex(random_bytes(32));
        $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));
        
        // Save token to database
        $updateStmt = $pdo->prepare("UPDATE users SET reset_token = ?, reset_token_expiry = ? WHERE id = ?");
        $updateStmt->execute([$token, $expiry, $user['id']]);
        
        // Prepare email
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'];
        // Get the base path correctly (assuming we are in /ajax/ and reset_password.php is in /)
        $scriptPath = dirname(dirname($_SERVER['SCRIPT_NAME']));
        if ($scriptPath === DIRECTORY_SEPARATOR || $scriptPath === '\\') $scriptPath = '';
        
        $resetLink = $protocol . '://' . $host . $scriptPath . '/reset_password.php?token=' . $token;
        
        $subject = "Password Reset Request - EverythingB2C";
        $body = "
        <!DOCTYPE html>
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #8dbd43; color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }
                .content { background: #f9f9f9; padding: 20px; border-radius: 0 0 8px 8px; border: 1px solid #eee; }
                .button { background: #7a9615; color: white; padding: 12px 25px; text-decoration: none; border-radius: 5px; display: inline-block; font-weight: bold; margin: 20px 0; }
                .footer { text-align: center; margin-top: 20px; color: #666; font-size: 12px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h2>EverythingB2C</h2>
                </div>
                <div class='content'>
                    <p>Hello " . htmlspecialchars($user['name']) . ",</p>
                    <p>We received a request to reset your password. Click the button below to set a new password:</p>
                    <div style='text-align: center;'>
                        <a href='{$resetLink}' class='button' style='color: white;'>Reset Password</a>
                    </div>
                    <p>This link will expire in 1 hour.</p>
                    <p>If you didn't request a password reset, you can safely ignore this email.</p>
                </div>
                <div class='footer'>
                    <p>&copy; " . date('Y') . " EverythingB2C. All rights reserved.</p>
                </div>
            </div>
        </body>
        </html>";
        
        if (sendEmail($email, $subject, $body)) {
            echo json_encode(['success' => true, 'message' => 'A password reset link has been sent to your email address.']);
        } else {
            // For development/troubleshooting, we might want to see the error, 
            // but for production a generic message is better.
            echo json_encode(['success' => false, 'message' => 'Failed to send reset email. Please try again later.']);
        }
    } else {
        // Security best practice: don't reveal if user exists, 
        // but often for UX we say "If the email exists, a link has been sent".
        // I'll provide a slightly more helpful message as requested "fix this button issue".
        echo json_encode(['success' => true, 'message' => 'If this email is registered, you will receive a reset link shortly.']);
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()]);
}
?>
