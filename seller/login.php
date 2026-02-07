<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

// Redirect if already logged in
if (isset($_SESSION['seller_id'])) {
    header('Location: index.php');
    exit;
}

$error = '';
$register_error = '';
$register_success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // LOGIN LOGIC
    if (isset($_POST['login'])) {
        $email = trim($_POST['email']);
        $password = $_POST['password'];
        
        if (empty($email) || empty($password)) {
            $error = 'Please fill in all fields';
        } else {
            try {
                // Check if user exists and is an approved seller
                $stmt = $pdo->prepare("SELECT u.*, s.id as seller_id, s.business_name 
                                       FROM users u 
                                       JOIN sellers s ON u.id = s.user_id 
                                       WHERE u.email = ? 
                                       AND u.user_role = 'seller' 
                                       AND u.is_seller_approved = 1 
                                       AND s.is_active = 1");
                $stmt->execute([$email]);
                $seller = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($seller && password_verify($password, $seller['password'])) {
                    // Set seller session
                    $_SESSION['seller_id'] = $seller['seller_id'];
                    $_SESSION['seller_user_id'] = $seller['id'];
                    $_SESSION['seller_name'] = $seller['name'];
                    $_SESSION['seller_email'] = $seller['email'];
                    $_SESSION['seller_business_name'] = $seller['business_name'];
                    
                    // Log activity
                    require_once '../includes/seller_functions.php';
                    logSellerActivity($seller['seller_id'], 'login', 'Seller logged in');
                    
                    header('Location: index.php');
                    exit;
                } else {
                    $error = 'Invalid email or password, or your seller account is not approved/active';
                }
            } catch (Exception $e) {
                $error = 'Database error: ' . $e->getMessage();
            }
        }
    }
    
    // REGISTER LOGIC
    if (isset($_POST['register'])) {
        $first_name = sanitizeInput($_POST['first_name'] ?? '');
        $last_name = sanitizeInput($_POST['last_name'] ?? '');
        $email = sanitizeInput($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
        $business_name = sanitizeInput($_POST['business_name'] ?? '');
        $business_address = sanitizeInput($_POST['business_address'] ?? '');
        $phone = sanitizeInput($_POST['phone'] ?? '');
        $gst_number = sanitizeInput($_POST['gst_number'] ?? '');
        
        if (empty($first_name) || empty($email) || empty($password) || empty($business_name)) {
            $register_error = 'Please fill in all required fields';
        } elseif ($password !== $confirm_password) {
            $register_error = 'Passwords do not match';
        } elseif (strlen($password) < 6) {
            $register_error = 'Password must be at least 6 characters';
        } else {
            try {
                // Check if email already exists
                $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
                $stmt->execute([$email]);
                if ($stmt->fetch()) {
                    $register_error = 'Email already registered';
                } else {
                    // Hash password
                    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                    
                    // Create user account
                    $fullName = trim($first_name . ' ' . $last_name);
                    $stmt = $pdo->prepare("INSERT INTO users (name, email, password, phone, user_role, is_seller_approved) 
                                          VALUES (?, ?, ?, ?, 'seller', 0)");
                    if ($stmt->execute([$fullName, $email, $hashedPassword, $phone])) {
                        $userId = $pdo->lastInsertId();
                        
                        // Create seller account (pending approval)
                        $stmt = $pdo->prepare("INSERT INTO sellers (user_id, business_name, business_address, gst_number, is_active) 
                                              VALUES (?, ?, ?, ?, 0)");
                        if ($stmt->execute([$userId, $business_name, $business_address, $gst_number])) {
                            $register_success = 'Registration successful! Your account is pending admin approval. You will receive an email once approved.';
                        } else {
                            $register_error = 'Registration failed. Please try again.';
                        }
                    } else {
                        $register_error = 'Registration failed. Please try again.';
                    }
                }
            } catch (Exception $e) {
                $register_error = 'Database error: ' . $e->getMessage();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seller Login - EverythingB2C</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: #f8f9fa;
            min-height: 100vh;
            padding: 40px 0;
        }
        .seller-page {
            padding: 40px 0;
        }
        .form-container {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        .form-header {
            padding: 20px;
            border-bottom: 2px solid #ddd;
            font-weight: 600;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: white;
        }
        .login-header {
            background: linear-gradient(135deg, #9fbe1b 0%, #7a9615 100%);
        }
        .register-header {
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
        }
        .form-body {
            padding: 30px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-control {
            border-radius: 6px;
            border: 1px solid #e0e0e0;
            padding: 10px 12px;
            font-size: 14px;
        }
        .form-control:focus {
            border-color: #9fbe1b;
            box-shadow: 0 0 0 0.2rem rgba(159, 190, 27, 0.15);
        }
        .form-label {
            font-weight: 600;
            font-size: 13px;
            color: #333;
            margin-bottom: 8px;
        }
        .required {
            color: #dc3545;
        }
        .login-btn {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 6px;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .login-btn-login {
            background: linear-gradient(135deg, #9fbe1b 0%, #7a9615 100%);
            color: white;
        }
        .login-btn-login:hover {
            opacity: 0.9;
        }
        .login-btn-register {
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            color: white;
        }
        .login-btn-register:hover {
            opacity: 0.9;
        }
        .alert {
            border-radius: 6px;
            margin-bottom: 20px;
        }
        .back-link {
            text-align: center;
            margin-top: 20px;
        }
        .back-link a {
            color: #6c757d;
            text-decoration: none;
            font-size: 13px;
        }
        .back-link a:hover {
            color: #9fbe1b;
        }
        .form-section {
            display: none;
        }
        .form-section.active {
            display: block;
        }
        @media (max-width: 992px) {
            .register-form {
                margin-top: 30px;
            }
        }
    </style>
</head>
<body>
<div class="seller-page">
    <div class="container">
        <div class="row">
            <!-- Login Form -->
            <div class="col-lg-6">
                <div class="form-container">
                    <div class="form-header login-header">
                        <i class="fas fa-sign-in-alt me-2"></i>LOGIN FOR EXISTING SELLER
                    </div>
                    <div class="form-body">
                        <?php if ($error): ?>
                            <div class="alert alert-danger"><i class="fas fa-exclamation-circle me-2"></i><?php echo $error; ?></div>
                        <?php endif; ?>
                        <form method="POST">
                            <input type="hidden" name="login" value="1">
                            <div class="form-group">
                                <label for="login_email" class="form-label">Email Address <span class="required">*</span></label>
                                <input type="email" class="form-control" id="login_email" name="email" placeholder="seller@example.com" required>
                            </div>
                            <div class="form-group">
                                <label for="login_password" class="form-label">Password <span class="required">*</span></label>
                                <input type="password" class="form-control" id="login_password" name="password" placeholder="Enter your password" required>
                            </div>
                            <button type="submit" class="login-btn login-btn-login">
                                <i class="fas fa-sign-in-alt me-2"></i>Login to Seller Dashboard
                            </button>
                        </form>
                        <div class="back-link">
                            <a href="../index.php"><i class="fas fa-arrow-left me-1"></i>Back to Website</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Register Form -->
            <div class="col-lg-6 register-form">
                <div class="form-container">
                    <div class="form-header register-header">
                        <i class="fas fa-user-plus me-2"></i>REGISTER AS NEW SELLER
                    </div>
                    <div class="form-body">
                        <?php if ($register_error): ?>
                            <div class="alert alert-danger"><i class="fas fa-exclamation-circle me-2"></i><?php echo $register_error; ?></div>
                        <?php endif; ?>
                        <?php if ($register_success): ?>
                            <div class="alert alert-success"><i class="fas fa-check-circle me-2"></i><?php echo $register_success; ?></div>
                        <?php else: ?>
                        <form method="POST">
                            <input type="hidden" name="register" value="1">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="first_name" class="form-label">First Name <span class="required">*</span></label>
                                        <input type="text" class="form-control" id="first_name" name="first_name" placeholder="First Name" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="last_name" class="form-label">Last Name</label>
                                        <input type="text" class="form-control" id="last_name" name="last_name" placeholder="Last Name">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="reg_email" class="form-label">Email Address <span class="required">*</span></label>
                                <input type="email" class="form-control" id="reg_email" name="email" placeholder="your@email.com" required>
                            </div>
                            <div class="form-group">
                                <label for="business_name" class="form-label">Business Name <span class="required">*</span></label>
                                <input type="text" class="form-control" id="business_name" name="business_name" placeholder="Your Business Name" required>
                            </div>
                            <div class="form-group">
                                <label for="business_address" class="form-label">Business Address</label>
                                <input type="text" class="form-control" id="business_address" name="business_address" placeholder="Street Address">
                            </div>
                            <div class="form-group">
                                <label for="phone" class="form-label">Phone Number</label>
                                <input type="tel" class="form-control" id="phone" name="phone" placeholder="10-digit phone number">
                            </div>
                            <div class="form-group">
                                <label for="gst_number" class="form-label">GST Number</label>
                                <input type="text" class="form-control" id="gst_number" name="gst_number" placeholder="Your GST Number">
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="reg_password" class="form-label">Password <span class="required">*</span></label>
                                        <input type="password" class="form-control" id="reg_password" name="password" placeholder="Min 6 characters" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="confirm_password" class="form-label">Confirm Password <span class="required">*</span></label>
                                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Confirm Password" required>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="login-btn login-btn-register">
                                <i class="fas fa-user-plus me-2"></i>Register as Seller
                            </button>
                        </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Handle register parameter in URL
    document.addEventListener('DOMContentLoaded', function() {
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('register') === '1') {
            const registerForm = document.querySelector('.register-form');
            if (registerForm) {
                registerForm.scrollIntoView({ behavior: 'smooth' });
                registerForm.querySelector('input').focus();
            }
        }
    });
</script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
