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

function formatEmailAmount($amount) {
    $amount = (float)$amount;
    $decimals = abs($amount - round($amount)) < 0.005 ? 0 : 2;
    return number_format($amount, $decimals);
}

function getEmailOrderItemAmounts(array $item) {
    if (function_exists('getOrderItemDisplayAmounts')) {
        return getOrderItemDisplayAmounts($item);
    }

    $packageQuantity = max(1, (int)($item['package_quantity'] ?? 1));
    $priceMultiplier = max(0, (float)($item['quantity'] ?? 1)) / $packageQuantity;
    $unitPrice = isset($item['unit_price']) ? (float)$item['unit_price'] : (float)($item['price'] ?? 0);
    $lineTotal = isset($item['price']) ? (float)$item['price'] : ($unitPrice * $priceMultiplier);

    return [
        'unit_price' => $unitPrice,
        'line_total' => $lineTotal
    ];
}

function getEmailOrderItemDisplayQuantity(array $item) {
    if (function_exists('getOrderItemDisplayQuantity') && function_exists('formatDisplayQuantity')) {
        return formatDisplayQuantity(getOrderItemDisplayQuantity($item));
    }

    $packageQuantity = max(1, (int)($item['package_quantity'] ?? 1));
    $quantity = max(0, (float)($item['quantity'] ?? 1)) / $packageQuantity;
    return abs($quantity - round($quantity)) < 0.0001
        ? (string)(int)round($quantity)
        : rtrim(rtrim(number_format($quantity, 2, '.', ''), '0'), '.');
}

/**
 * Send email using PHPMailer
 */
function sendEmail($to, $subject, $body, $isHTML = true) {
    $emailConfig = include __DIR__ . '/../config/email.php';

    if (empty($emailConfig['smtp']['password'])) {
        error_log('Email sending failed: SMTP_PASSWORD is not configured.');
        return false;
    }
    
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
        // Explicitly declare UTF-8 so currency and other non-ASCII characters
        // are not decoded as ISO-8859-1 by email clients.
        $mail->CharSet = 'UTF-8';
        $mail->Encoding = 'base64';
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
 * Send a contact-form enquiry to the site inbox.
 * The visitor is deliberately a Reply-To address, never the SMTP sender.
 */
function sendContactEnquiry(string $name, string $visitorEmail, string $message): bool {
    $emailConfig = include __DIR__ . '/../config/email.php';

    if (empty($emailConfig['smtp']['password'])) {
        error_log('Contact form email failed: SMTP_PASSWORD is not configured.');
        return false;
    }

    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = $emailConfig['smtp']['host'];
        $mail->SMTPAuth = true;
        $mail->Username = $emailConfig['smtp']['username'];
        $mail->Password = $emailConfig['smtp']['password'];
        $mail->SMTPSecure = $emailConfig['smtp']['encryption'];
        $mail->Port = (int)$emailConfig['smtp']['port'];
        $mail->CharSet = 'UTF-8';
        $mail->Timeout = 15;

        $mail->setFrom($emailConfig['smtp']['from_email'], $emailConfig['smtp']['from_name']);
        $mail->addAddress($emailConfig['admin_email'], $emailConfig['smtp']['from_name']);
        $mail->addReplyTo($visitorEmail, $name);
        $mail->isHTML(true);
        $mail->Subject = 'New website enquiry from ' . $name;

        $escape = static fn(string $value): string => htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $safeMessage = nl2br($escape($message));
        $mail->Body = '<h2>New website enquiry</h2>'
            . '<p><strong>Name:</strong> ' . $escape($name) . '<br>'
            . '<strong>Email:</strong> ' . $escape($visitorEmail) . '</p>'
            . '<p><strong>Message:</strong><br>' . $safeMessage . '</p>';
        $mail->AltBody = "New website enquiry\n\nName: {$name}\nEmail: {$visitorEmail}\n\nMessage:\n{$message}";
        $mail->send();

        return true;
    } catch (Exception $e) {
        error_log('Contact form email failed: ' . $mail->ErrorInfo);
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
        
        $emailConfig = include __DIR__ . '/../config/email.php';
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
    // HTML entity keeps the rupee sign reliable in all email clients.
    $currency = '&#8377;';
    $total = formatEmailAmount($order['total_amount']);
    $productTotal = formatEmailAmount($order['subtotal'] ?? 0);
    $shippingCharge = formatEmailAmount($order['shipping_charge'] ?? 0);
    $hasShippingCharge = (float)($order['shipping_charge'] ?? 0) > 0;
    $orderDate = date('F j, Y \a\t g:i A', strtotime($order['created_at']));
    
    $itemsHTML = '';
    foreach ($orderItems as $item) {
        $productName = cleanProductName($item['name'] ?? 'Unknown Product');
        $quantity = getEmailOrderItemDisplayQuantity($item);
        $amounts = getEmailOrderItemAmounts($item);
        $itemsHTML .= "
        <tr>
            <td style='padding: 11px 8px; border-bottom: 1px solid #e9ecef; color: #343a40; word-break: break-word;'>{$productName}</td>
            <td style='padding: 11px 6px; border-bottom: 1px solid #e9ecef; text-align: center; color: #343a40;'>{$quantity}</td>
            <td style='padding: 11px 6px; border-bottom: 1px solid #e9ecef; text-align: right; color: #343a40; white-space: nowrap;'>{$currency}" . formatEmailAmount($amounts['unit_price']) . "</td>
            <td style='padding: 11px 8px; border-bottom: 1px solid #e9ecef; text-align: right; color: #343a40; font-weight: 600; white-space: nowrap;'>{$currency}" . formatEmailAmount($amounts['line_total']) . "</td>
        </tr>";
    }
    
    return "
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset='UTF-8'>
        <style>
            body { margin: 0; padding: 0; background: #f4f6f8; font-family: Arial, sans-serif; line-height: 1.5; color: #343a40; }
            .container { max-width: 640px; margin: 0 auto; padding: 24px 12px; }
            .header { background: #9fbe1b; color: #ffffff; padding: 28px 24px; text-align: center; border-radius: 10px 10px 0 0; }
            .header h1 { margin: 0 0 6px; font-size: 24px; line-height: 1.25; }
            .header p { margin: 0; font-size: 15px; opacity: 0.95; }
            .content { background: #ffffff; padding: 24px; border-radius: 0 0 10px 10px; }
            .order-details { background: #f8f9fa; padding: 16px; margin: 16px 0; border-radius: 7px; border-left: 4px solid #9fbe1b; }
            .order-details h3, .section-title { margin: 0 0 10px; color: #212529; font-size: 17px; }
            .order-details p { margin: 6px 0; font-size: 14px; }
            table { width: 100%; border-collapse: collapse; margin: 12px 0 20px; font-size: 14px; }
            th { background: #9fbe1b; color: #ffffff; padding: 11px 8px; text-align: left; font-size: 12px; }
            .total { font-size: 18px; font-weight: bold; color: #7a9500; }
            .footer { text-align: center; margin-top: 18px; color: #6c757d; font-size: 13px; }
            @media only screen and (max-width: 480px) { .container { padding: 0; } .header, .content { border-radius: 0; } .content { padding: 16px; } table { font-size: 12px; } th { font-size: 11px; padding: 9px 5px; } }
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
                
                <h3 class='section-title'>Order Items</h3>
                <table>
                    <thead>
                        <tr>
                            <th style='width: 46%;'>Product</th>
                            <th style='width: 14%; text-align: center;'>Qty.</th>
                            <th style='width: 20%; text-align: right;'>Price</th>
                            <th style='width: 20%; text-align: right;'>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        {$itemsHTML}
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan='3' style='text-align: right; padding: 11px 8px; font-weight: 600; border-top: 1px solid #dee2e6;'>Product Total:</td>
                            <td style='text-align: right; padding: 11px 8px; font-weight: 600; border-top: 1px solid #dee2e6; white-space: nowrap;'>{$currency}{$productTotal}</td>
                        </tr>" .
                        ($hasShippingCharge ? "
                        <tr>
                            <td colspan='3' style='text-align: right; padding: 11px 8px; font-weight: 600;'>Shipping Charges:</td>
                            <td style='text-align: right; padding: 11px 8px; font-weight: 600; white-space: nowrap;'>{$currency}{$shippingCharge}</td>
                        </tr>" : "") . "
                        <tr>
                            <td colspan='3' style='text-align: right; padding: 14px 8px; font-weight: bold; border-top: 2px solid #9fbe1b;'>Total Amount:</td>
                            <td style='text-align: right; padding: 14px 8px; font-weight: bold; border-top: 2px solid #9fbe1b; white-space: nowrap;' class='total'>{$currency}{$total}</td>
                        </tr>
                    </tfoot>
                </table>
                
                <p>We'll send you updates about your order via email and SMS. You can also track your order by visiting your account.</p>
                
                <div style='text-align: center; margin: 24px 0 4px;'>
                    <a href='" . getBaseUrl() . "myaccount.php' style='background: #9fbe1b; color: #ffffff; padding: 13px 24px; text-decoration: none; border-radius: 5px; display: inline-block; font-size: 14px; font-weight: bold;'>View My Account</a>
                </div>
            </div>
            <div class='footer'>
                <p>Thank you for shopping with everythingb2c!</p>
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
    // Use an HTML entity rather than a literal symbol. This remains reliable in
    // clients that alter or ignore a message's character encoding declaration.
    $currency = '&#8377;';
    $total = formatEmailAmount($order['total_amount']);
    $productTotal = formatEmailAmount($order['subtotal'] ?? 0);
    $shippingCharge = formatEmailAmount($order['shipping_charge'] ?? 0);
    $hasShippingCharge = (float)($order['shipping_charge'] ?? 0) > 0;
    $orderDate = date('F j, Y \a\t g:i A', strtotime($order['created_at']));
    
    $itemsHTML = '';
    foreach ($orderItems as $item) {
        $productName = cleanProductName($item['name'] ?? 'Unknown Product');
        $quantity = getEmailOrderItemDisplayQuantity($item);
        $amounts = getEmailOrderItemAmounts($item);
        $itemsHTML .= "
        <tr>
            <td style='padding: 11px 8px; border-bottom: 1px solid #e9ecef; color: #343a40; word-break: break-word;'>{$productName}</td>
            <td style='padding: 11px 6px; border-bottom: 1px solid #e9ecef; text-align: center; color: #343a40;'>{$quantity}</td>
            <td style='padding: 11px 6px; border-bottom: 1px solid #e9ecef; text-align: right; color: #343a40; white-space: nowrap;'>{$currency}" . formatEmailAmount($amounts['unit_price']) . "</td>
            <td style='padding: 11px 8px; border-bottom: 1px solid #e9ecef; text-align: right; color: #343a40; font-weight: 600; white-space: nowrap;'>{$currency}" . formatEmailAmount($amounts['line_total']) . "</td>
        </tr>";
    }
    
    return "
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset='UTF-8'>
        <style>
            body { margin: 0; padding: 0; background: #f4f6f8; font-family: Arial, sans-serif; line-height: 1.5; color: #343a40; }
            .container { max-width: 640px; margin: 0 auto; padding: 24px 12px; }
            .header { background: #dc3545; color: #ffffff; padding: 28px 24px; text-align: center; border-radius: 10px 10px 0 0; }
            .header h1 { margin: 0 0 6px; font-size: 24px; line-height: 1.25; }
            .header p { margin: 0; font-size: 15px; opacity: 0.95; }
            .content { background: #ffffff; padding: 24px; border-radius: 0 0 10px 10px; }
            .order-details, .customer-details { background: #f8f9fa; padding: 16px; margin: 16px 0; border-radius: 7px; }
            .order-details { border-left: 4px solid #dc3545; }
            .customer-details { border-left: 4px solid #17a2b8; }
            .order-details h3, .customer-details h3, .section-title { margin: 0 0 10px; color: #212529; font-size: 17px; }
            .order-details p, .customer-details p { margin: 6px 0; font-size: 14px; }
            table { width: 100%; border-collapse: collapse; margin: 12px 0 20px; font-size: 14px; }
            th { background: #dc3545; color: #ffffff; padding: 11px 8px; text-align: left; font-size: 12px; }
            .total { font-size: 18px; font-weight: bold; color: #c82333; }
            .footer { text-align: center; margin-top: 18px; color: #6c757d; font-size: 13px; }
            @media only screen and (max-width: 480px) { .container { padding: 0; } .header, .content { border-radius: 0; } .content { padding: 16px; } table { font-size: 12px; } th { font-size: 11px; padding: 9px 5px; } }
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
                    <p><strong>Total Amount:</strong> {$currency}{$total}</p>
                </div>
                
                <div class='customer-details'>
                    <h3>Customer Information</h3>
                    <p><strong>Name:</strong> " . ($order['name'] ?? 'N/A') . "</p>
                    <p><strong>Email:</strong> " . ($order['email'] ?? 'N/A') . "</p>
                    <p><strong>Phone:</strong> " . ($order['phone'] ?? 'N/A') . "</p>
                </div>
                
                <h3 class='section-title'>Order Items</h3>
                <table>
                    <thead>
                        <tr>
                            <th style='width: 46%;'>Product</th>
                            <th style='width: 14%; text-align: center;'>Qty.</th>
                            <th style='width: 20%; text-align: right;'>Price</th>
                            <th style='width: 20%; text-align: right;'>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        {$itemsHTML}
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan='3' style='text-align: right; padding: 11px 8px; font-weight: 600; border-top: 1px solid #dee2e6;'>Product Total:</td>
                            <td style='text-align: right; padding: 11px 8px; font-weight: 600; border-top: 1px solid #dee2e6; white-space: nowrap;'>{$currency}{$productTotal}</td>
                        </tr>" .
                        ($hasShippingCharge ? "
                        <tr>
                            <td colspan='3' style='text-align: right; padding: 11px 8px; font-weight: 600;'>Shipping Charges:</td>
                            <td style='text-align: right; padding: 11px 8px; font-weight: 600; white-space: nowrap;'>{$currency}{$shippingCharge}</td>
                        </tr>" : "") . "
                        <tr>
                            <td colspan='3' style='text-align: right; padding: 14px 8px; font-weight: bold; border-top: 2px solid #dc3545;'>Total Amount:</td>
                            <td style='text-align: right; padding: 14px 8px; font-weight: bold; border-top: 2px solid #dc3545; white-space: nowrap;' class='total'>{$currency}{$total}</td>
                        </tr>
                    </tfoot>
                </table>
                
                <div style='text-align: center; margin: 24px 0 4px;'>
                    <a href='" . getBaseUrl() . "admin/orders.php' style='background: #dc3545; color: #ffffff; padding: 13px 24px; text-decoration: none; border-radius: 5px; display: inline-block; font-size: 14px; font-weight: bold;'>View in Admin Panel</a>
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
                <p>Thank you for shopping with everythingb2c!</p>
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
