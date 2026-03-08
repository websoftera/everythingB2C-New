<?php
session_start();
require_once '../config/database.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Access Denied - EverythingB2C Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .error-container {
            text-align: center;
            color: white;
        }
        .error-code {
            font-size: 120px;
            font-weight: bold;
            margin: 0;
            opacity: 0.8;
        }
        .error-message {
            font-size: 32px;
            margin: 20px 0;
        }
        .error-description {
            font-size: 16px;
            margin: 20px 0 30px;
            opacity: 0.9;
        }
        .btn-back {
            background: white;
            color: #667eea;
            padding: 12px 30px;
            font-size: 16px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-back:hover {
            background: #f0f0f0;
            color: #667eea;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <p class="error-code">403</p>
        <h1 class="error-message">Access Denied</h1>
        <p class="error-description">
            <?php if (isset($_SESSION['admin_id'])): ?>
                You don't have permission to access this page.
            <?php else: ?>
                You must be logged in to access this page.
            <?php endif; ?>
        </p>
        <a href="index.php" class="btn-back">
            <i class="fas fa-arrow-left"></i> Back to Dashboard
        </a>
    </div>
</body>
</html>
