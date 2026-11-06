<?php
/**
 * CLI-only authenticated SMTP connectivity test.
 * Run: SMTP_PASSWORD='...' php tests/smtp_connectivity.php
 */
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require_once __DIR__ . '/../vendor/autoload.php';
$config = require __DIR__ . '/../config/email.php';

if (empty($config['smtp']['password'])) {
    fwrite(STDERR, "SMTP_PASSWORD is not configured in this process.\n");
    exit(2);
}

$mail = new PHPMailer\PHPMailer\PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->Host = $config['smtp']['host'];
    $mail->Port = (int)$config['smtp']['port'];
    $mail->SMTPSecure = $config['smtp']['encryption'];
    $mail->SMTPAuth = true;
    $mail->Username = $config['smtp']['username'];
    $mail->Password = $config['smtp']['password'];
    $mail->Timeout = 15;
    $mail->SMTPDebug = 0;
    $mail->smtpConnect();
    $mail->getSMTPInstance()->quit();
    echo "Authenticated SMTP connection succeeded.\n";
} catch (Throwable $e) {
    fwrite(STDERR, "SMTP connection failed. Check the server environment and mail host settings.\n");
    exit(1);
}
