<?php
/**
 * Email Functions
 * Handles sending email notifications for orders and status updates
 */

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/email.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

/**
 * Send email using PHPMailer
 */
function sendEmail($to, $subject, $body, $isHTML = true) {
    $emailConfig = include 'config/email.php';
    
    $mail = new PHPMailer(true);
    
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = $emailConfig['smtp']['host'];
        $mail->SMTPAuth = true;
        $mail->Username = $emailConfig['smtp']['username'];
        $mail->Password = $emailConfig['smtp']['password'];
        $mail->SMTPSecure = $emailConfig['smtp']['encryption'];
        $mail->Port = $emailConfig['smtp']['port'];
        
        // Recipients
        $mail->setFrom($emailConfig['smtp']['from_email'], $emailConfig['smtp']['from_name']);
        $mail->addAddress($to);
        
        // Content
        $mail->isHTML($isHTML);
        $mail->Subject = $subject;
        $mail->Body = $body;
        
        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Email sending failed: " . $mail->ErrorInfo);
        return false;
    }
}

/**
 * Send order placed notification to user
 */
function sendOrderPlacedUserNotification($userId, $orderId) {
    global $pdo;
    
    try {
        // Get user details
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$user || !$user['email']) {
            return false;
        }
        
        // Get order details
        $order = getOrderById($orderId, $userId);
        if (!$order) {
            return false;
        }
        
        // Get order items
        $orderItems = getOrderItems($orderId);
        
        $subject = "Order Confirmed - Order #{$orderId}";
        $body = generateOrderPlacedUserEmail($user, $order, $orderItems);
        
        return sendEmail($user['email'], $subject, $body);
        
    } catch (Exception $e) {
        error_log("Order placed user notification failed: " . $e->getMessage());
        return false;
    }
}

/**
 * Send order placed notification to admin
 */
function sendOrderPlacedAdminNotification($orderId) {
    global $pdo;
    
    try {
        // Get order details
        $stmt = $pdo->prepare("SELECT o.*, u.name, u.email, u.phone FROM orders o 
                               JOIN users u ON o.user_id = u.id WHERE o.id = ?");
        $stmt->execute([$orderId]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$order) {
            return false;
        }
        
        // Get order items
        $orderItems = getOrderItems($orderId);
        
        $emailConfig = include 'config/email.php';
        $subject = "New Order Received - Order #{$orderId}";
        $body = generateOrderPlacedAdminEmail($order, $orderItems);
        
        return sendEmail($emailConfig['admin_email'], $subject, $body);
        
    } catch (Exception $e) {
        error_log("Order placed admin notification failed: " . $e->getMessage());
        return false;
    }
}

/**
 * Send order status changed notification to user
 */
function sendOrderStatusChangedNotification($userId, $orderId, $newStatus, $oldStatus = null) {
    global $pdo;
    
    try {
        // Get user details
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$user || !$user['email']) {
            return false;
        }
        
        // Get order details
        $order = getOrderById($orderId, $userId);
        if (!$order) {
            return false;
        }
        
        $subject = "Order Status Updated - Order #{$orderId}";
        $body = generateOrderStatusChangedEmail($user, $order, $newStatus, $oldStatus);
        
        return sendEmail($user['email'], $subject, $body);
        
    } catch (Exception $e) {
        error_log("Order status changed notification failed: " . $e->getMessage());
        return false;
    }
}

/**
 * Generate HTML email body for order placed user notification
 */
function generateOrderPlacedUserEmail($user, $order, $orderItems) {
    $total = number_format($order['total_amount'], 2);
    $orderDate = date('F j, Y \a\t g:i A', strtotime($order['created_at']));
    
    $itemsHTML = '';
    foreach ($orderItems as $item) {
        $productName = $item['name'] ?? 'Unknown Product';
        $quantity = $item['quantity'] ?? 1;
        $price = $item['price'] ?? 0;
        $itemsHTML .= "
        <tr>
            <td style='padding: 10px; border-bottom: 1px solid #eee;'>{$productName}</td>
            <td style='padding: 10px; border-bottom: 1px solid #eee; text-align: center;'>{$quantity}</td>
            <td style='padding: 10px; border-bottom: 1px solid #eee; text-align: right;'>₹" . number_format($price, 2) . "</td>
            <td style='padding: 10px; border-bottom: 1px solid #eee; text-align: right;'>₹" . number_format($price * $quantity, 2) . "</td>
        </tr>";
    }
    
    return "
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset='UTF-8'>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: #9fbe1b; color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }
            .content { background: #f9f9f9; padding: 20px; border-radius: 0 0 8px 8px; }
            .order-details { background: white; padding: 15px; margin: 15px 0; border-radius: 5px; border-left: 4px solid #9fbe1b; }
            table { width: 100%; border-collapse: collapse; margin: 15px 0; }
            th { background: #9fbe1b; color: white; padding: 10px; text-align: left; }
            .total { font-size: 18px; font-weight: bold; color: #9fbe1b; }
            .footer { text-align: center; margin-top: 20px; color: #666; font-size: 14px; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>Order Confirmed!</h1>
                <p>Thank you for your order, " . ($user['name'] ?? 'Customer') . "!</p>
            </div>
            <div class='content'>
                <div class='order-details'>
                    <h3>Order Details</h3>
                    <p><strong>Order Number:</strong> #{$order['id']}</p>
                    <p><strong>Order Date:</strong> {$orderDate}</p>
                    <p><strong>Payment Method:</strong> " . ucfirst($order['payment_method']) . "</p>
                    <p><strong>Status:</strong> " . ucfirst($order['status']) . "</p>
                </div>
                
                <h3>Order Items</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Quantity</th>
                            <th>Price</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        {$itemsHTML}
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan='3' style='text-align: right; padding: 10px; font-weight: bold;'>Total Amount:</td>
                            <td style='text-align: right; padding: 10px; font-weight: bold;' class='total'>₹{$total}</td>
                        </tr>
                    </tfoot>
                </table>
                
                <p>We'll send you updates about your order via email and SMS. You can also track your order by visiting your account.</p>
                
                <div style='text-align: center; margin: 20px 0;'>
                    <a href='" . getBaseUrl() . "myaccount.php' style='background: #9fbe1b; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; display: inline-block;'>View My Account</a>
                </div>
            </div>
            <div class='footer'>
                <p>Thank you for shopping with EverythingB2C!</p>
                <p>If you have any questions, please contact us at info@everythingb2c.in</p>
            </div>
        </div>
    </body>
    </html>";
}

/**
 * Generate HTML email body for order placed admin notification
 */
function generateOrderPlacedAdminEmail($order, $orderItems) {
    $total = number_format($order['total_amount'], 2);
    $orderDate = date('F j, Y \a\t g:i A', strtotime($order['created_at']));
    
    $itemsHTML = '';
    foreach ($orderItems as $item) {
        $productName = $item['name'] ?? 'Unknown Product';
        $quantity = $item['quantity'] ?? 1;
        $price = $item['price'] ?? 0;
        $itemsHTML .= "
        <tr>
            <td style='padding: 10px; border-bottom: 1px solid #eee;'>{$productName}</td>
            <td style='padding: 10px; border-bottom: 1px solid #eee; text-align: center;'>{$quantity}</td>
            <td style='padding: 10px; border-bottom: 1px solid #eee; text-align: right;'>₹" . number_format($price, 2) . "</td>
            <td style='padding: 10px; border-bottom: 1px solid #eee; text-align: right;'>₹" . number_format($price * $quantity, 2) . "</td>
        </tr>";
    }
    
    return "
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset='UTF-8'>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: #dc3545; color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }
            .content { background: #f9f9f9; padding: 20px; border-radius: 0 0 8px 8px; }
            .order-details { background: white; padding: 15px; margin: 15px 0; border-radius: 5px; border-left: 4px solid #dc3545; }
            .customer-details { background: white; padding: 15px; margin: 15px 0; border-radius: 5px; border-left: 4px solid #17a2b8; }
            table { width: 100%; border-collapse: collapse; margin: 15px 0; }
            th { background: #dc3545; color: white; padding: 10px; text-align: left; }
            .total { font-size: 18px; font-weight: bold; color: #dc3545; }
            .footer { text-align: center; margin-top: 20px; color: #666; font-size: 14px; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>New Order Received!</h1>
                <p>Order #{$order['id']} has been placed</p>
            </div>
            <div class='content'>
                <div class='order-details'>
                    <h3>Order Information</h3>
                    <p><strong>Order Number:</strong> #{$order['id']}</p>
                    <p><strong>Order Date:</strong> {$orderDate}</p>
                    <p><strong>Payment Method:</strong> " . ucfirst($order['payment_method']) . "</p>
                    <p><strong>Status:</strong> " . ucfirst($order['status']) . "</p>
                    <p><strong>Total Amount:</strong> ₹{$total}</p>
                </div>
                
                <div class='customer-details'>
                    <h3>Customer Information</h3>
                    <p><strong>Name:</strong> " . ($order['name'] ?? 'N/A') . "</p>
                    <p><strong>Email:</strong> " . ($order['email'] ?? 'N/A') . "</p>
                    <p><strong>Phone:</strong> " . ($order['phone'] ?? 'N/A') . "</p>
                </div>
                
                <h3>Order Items</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Quantity</th>
                            <th>Price</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        {$itemsHTML}
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan='3' style='text-align: right; padding: 10px; font-weight: bold;'>Total Amount:</td>
                            <td style='text-align: right; padding: 10px; font-weight: bold;' class='total'>₹{$total}</td>
                        </tr>
                    </tfoot>
                </table>
                
                <div style='text-align: center; margin: 20px 0;'>
                    <a href='" . getBaseUrl() . "admin/orders.php' style='background: #dc3545; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; display: inline-block;'>View in Admin Panel</a>
                </div>
            </div>
            <div class='footer'>
                <p>Please process this order promptly.</p>
            </div>
        </div>
    </body>
    </html>";
}

/**
 * Generate HTML email body for order status changed notification
 */
function generateOrderStatusChangedEmail($user, $order, $newStatus, $oldStatus = null) {
    $orderDate = date('F j, Y \a\t g:i A', strtotime($order['created_at']));
    $statusChangeText = $oldStatus ? "changed from " . ucfirst($oldStatus) . " to " . ucfirst($newStatus) : "updated to " . ucfirst($newStatus);
    
    return "
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset='UTF-8'>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: #9fbe1b; color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }
            .content { background: #f9f9f9; padding: 20px; border-radius: 0 0 8px 8px; }
            .order-details { background: white; padding: 15px; margin: 15px 0; border-radius: 5px; border-left: 4px solid #9fbe1b; }
            .status-badge { background: #9fbe1b; color: white; padding: 8px 16px; border-radius: 20px; display: inline-block; font-weight: bold; }
            .footer { text-align: center; margin-top: 20px; color: #666; font-size: 14px; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>Order Status Updated</h1>
                <p>Your order status has been {$statusChangeText}</p>
            </div>
            <div class='content'>
                <div class='order-details'>
                    <h3>Order Details</h3>
                    <p><strong>Order Number:</strong> #{$order['id']}</p>
                    <p><strong>Order Date:</strong> {$orderDate}</p>
                    <p><strong>New Status:</strong> <span class='status-badge'>" . ucfirst($newStatus) . "</span></p>
                </div>
                
                <p>Your order #{$order['id']} status has been {$statusChangeText}. You can track your order by visiting your account.</p>
                
                <div style='text-align: center; margin: 20px 0;'>
                    <a href='" . getBaseUrl() . "track_order.php?tracking_id=" . urlencode($order['tracking_id']) . "' style='background: #9fbe1b; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; display: inline-block;'>Track Order</a>
                </div>
            </div>
            <div class='footer'>
                <p>Thank you for shopping with EverythingB2C!</p>
                <p>If you have any questions, please contact us at info@everythingb2c.in</p>
            </div>
        </div>
    </body>
    </html>";
}

/**
 * Get base URL for email links
 */
function getBaseUrl() {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $path = dirname($_SERVER['SCRIPT_NAME']);
    return $protocol . '://' . $host . $path . '/';
}
