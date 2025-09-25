<?php
/**
 * Email Configuration
 * SMTP settings for sending email notifications
 */

return [
    'smtp' => [
        'host' => 'smtp.gmail.com',
        'port' => 587,
        'encryption' => 'tls',
        'username' => 'help.websoftera@gmail.com',
        'password' => 'sxxe qwel jemx alze',
        'from_email' => 'help.websoftera@gmail.com',
        'from_name' => 'EverythingB2C'
    ],
    
    'admin_email' => 'info@everythingb2c.in',
    
    'templates' => [
        'order_placed_user' => 'emails/order_placed_user.php',
        'order_placed_admin' => 'emails/order_placed_admin.php',
        'order_status_changed' => 'emails/order_status_changed.php'
    ]
];
