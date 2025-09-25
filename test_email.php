<?php
/**
 * Test Email Functionality
 * This script tests if the email configuration and functions work correctly
 */

require_once 'vendor/autoload.php';
require_once 'includes/email_functions.php';

// Test email configuration
echo "Testing Email Configuration...\n";

try {
    // Test sending a simple email
    $testEmail = "test@example.com"; // Replace with a real email for testing
    $subject = "EverythingB2C - Email Test";
    $body = "
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset='UTF-8'>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: #9fbe1b; color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }
            .content { background: #f9f9f9; padding: 20px; border-radius: 0 0 8px 8px; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>Email Test Successful!</h1>
                <p>EverythingB2C Email System</p>
            </div>
            <div class='content'>
                <p>This is a test email to verify that the email system is working correctly.</p>
                <p>If you receive this email, the configuration is successful!</p>
                <p><strong>Timestamp:</strong> " . date('Y-m-d H:i:s') . "</p>
            </div>
        </div>
    </body>
    </html>";
    
    $result = sendEmail($testEmail, $subject, $body);
    
    if ($result) {
        echo "✅ Email sent successfully to: $testEmail\n";
        echo "📧 Check the inbox for the test email.\n";
    } else {
        echo "❌ Failed to send email.\n";
        echo "📋 Check the error logs for details.\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "📋 Check the configuration in config/email.php\n";
}

echo "\nEmail test completed.\n";
?>
