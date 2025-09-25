# Email Notifications Setup

## Overview
This system automatically sends email notifications for order-related events:
- **Order Placement**: Users and admins receive notifications when a new order is placed
- **Order Status Updates**: Users receive notifications when their order status changes

## Configuration

### Email Settings
The email configuration is stored in `config/email.php`:

```php
'smtp' => [
    'host' => 'smtp.gmail.com',
    'port' => 587,
    'encryption' => 'tls',
    'username' => 'help.websoftera@gmail.com',
    'password' => 'sxxe qwel jemx alze',
    'from_email' => 'help.websoftera@gmail.com',
    'from_name' => 'EverythingB2C'
],
'admin_email' => 'info@everythingb2c.in'
```

### Dependencies
- **PHPMailer**: Installed via Composer for robust email sending
- **SMTP**: Uses Gmail SMTP for reliable delivery

## Email Templates

### 1. Order Placed - User Notification
- **Recipient**: Customer who placed the order
- **Subject**: "Order Confirmed - Order #[ORDER_ID]"
- **Content**: Order details, items, total amount, next steps
- **Design**: Green theme with EverythingB2C branding

### 2. Order Placed - Admin Notification
- **Recipient**: info@everythingb2c.in
- **Subject**: "New Order Received - Order #[ORDER_ID]"
- **Content**: Order details, customer information, items, total
- **Design**: Red theme for admin attention

### 3. Order Status Changed - User Notification
- **Recipient**: Customer
- **Subject**: "Order Status Updated - Order #[ORDER_ID]"
- **Content**: Status change details, tracking information
- **Design**: Green theme with status badge

## Integration Points

### Order Placement (`includes/functions.php`)
```php
// In createOrder() function
try {
    require_once __DIR__ . '/email_functions.php';
    
    // Send notification to user
    sendOrderPlacedUserNotification($userId, $orderId);
    
    // Send notification to admin
    sendOrderPlacedAdminNotification($orderId);
    
} catch (Exception $emailError) {
    // Log email error but don't fail the order
    error_log("Email notification failed for order {$orderId}: " . $emailError->getMessage());
}
```

### Order Status Updates (`includes/functions.php`)
```php
// In updateOrderStatus() function
if ($oldStatusId != $statusId) {
    try {
        require_once __DIR__ . '/email_functions.php';
        
        // Send notification to user
        sendOrderStatusChangedNotification($userId, $orderId, $newStatusName, $oldStatusName);
        
    } catch (Exception $emailError) {
        // Log email error but don't fail the status update
        error_log("Email notification failed for order status update {$orderId}: " . $emailError->getMessage());
    }
}
```

## Features

### ✅ Automatic Notifications
- Orders placed → User + Admin emails
- Status changed → User email
- Error handling → Logs failures without breaking order flow

### ✅ Professional Design
- HTML emails with responsive design
- EverythingB2C branding and colors
- Clear, actionable content

### ✅ Robust Error Handling
- Email failures don't affect order processing
- Detailed error logging for debugging
- Graceful degradation

### ✅ Security
- SMTP authentication
- App password for Gmail (more secure than regular password)
- Input validation and sanitization

## Testing

### Test Email System
Run the test script to verify email configuration:

```bash
php test_email.php
```

### Manual Testing
1. Place a test order
2. Check customer email inbox
3. Check admin email inbox (info@everythingb2c.in)
4. Update order status in admin panel
5. Check customer email for status update

## Email Content Examples

### User Order Confirmation
```
Subject: Order Confirmed - Order #12345

Hello John,

Thank you for your order! Your order #12345 has been placed successfully.

Order Details:
- Order Date: January 15, 2024 at 2:30 PM
- Payment Method: Razorpay
- Status: Pending

Items:
- Product A x2 - ₹500.00
- Product B x1 - ₹300.00

Total Amount: ₹800.00

We'll send you updates about your order via email and SMS.
[View My Account Button]
```

### Admin New Order Alert
```
Subject: New Order Received - Order #12345

New Order Alert!

Order #12345 has been placed by:
- Customer: John Doe
- Email: john@example.com
- Phone: +91 9876543210

Order Total: ₹800.00
Payment Method: Razorpay

[View in Admin Panel Button]
```

## Troubleshooting

### Common Issues
1. **Emails not sending**: Check SMTP credentials and Gmail app password
2. **Emails going to spam**: Configure SPF/DKIM records for domain
3. **PHP errors**: Ensure PHPMailer is properly installed via Composer

### Error Logs
Check PHP error logs for email-related errors:
- `error_log()` entries for email failures
- SMTP connection issues
- Template rendering errors

## Future Enhancements
- SMS notifications integration
- Email templates customization via admin panel
- Batch email processing for multiple orders
- Email analytics and tracking
