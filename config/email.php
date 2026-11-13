<?php
/**
 * Email Configuration
 *
 * SMTP_PASSWORD must be defined by the web server environment.  Do not put
 * SMTP credentials in this file, .env files committed to the repository, or
 * browser-facing code.
 */

$smtpPassword = getenv('SMTP_PASSWORD');

// Apache's SetEnv directive is exposed through apache_getenv() on mod_php.
// PHP-FPM and CLI deployments use getenv() instead. Both remain server-only.
if (($smtpPassword === false || $smtpPassword === '') && function_exists('apache_getenv')) {
    $smtpPassword = apache_getenv('SMTP_PASSWORD');
}

if ($smtpPassword === false || $smtpPassword === '') {
    $smtpPassword = $_SERVER['SMTP_PASSWORD'] ?? '';
}

return [
    // Email links must point to the publicly reachable storefront.  Do not
    // derive this from the request host: orders placed from a local XAMPP
    // instance would otherwise send recipients to their own localhost.
    // EMAIL_BASE_URL can be set by a deployment to use another public domain.
    'public_base_url' => rtrim((string)(getenv('EMAIL_BASE_URL') ?: 'https://everythingb2c.in'), '/') . '/',

    'smtp' => [
        'host' => 'smtpout.secureserver.net',
        'port' => 465,
        'encryption' => 'ssl',
        'username' => 'info@everythingb2c.in',
        'password' => 'Nirmit@1101',
        'from_email' => 'info@everythingb2c.in',
        'from_name' => 'EverythingB2C'
    ],

    'admin_email' => 'info@everythingb2c.in',
    
    'templates' => [
        'order_placed_user' => 'emails/order_placed_user.php',
        'order_placed_admin' => 'emails/order_placed_admin.php',
        'order_status_changed' => 'emails/order_status_changed.php'
    ]
];

