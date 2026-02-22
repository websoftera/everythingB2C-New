<?php
session_start();
require_once '../config/database.php';

// Redirect if already logged in
if (isset($_SESSION['seller_id'])) {
    header('Location: index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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

$pageTitle = 'Seller Login';
$base_url = '../';
require_once '../includes/header.php';

// Breadcrumb Navigation
$breadcrumbs = generateBreadcrumb($pageTitle);
echo renderBreadcrumb($breadcrumbs);
?>
<link rel="stylesheet" href="../asset/style/login.css">
<style>
    .seller-info-content h4 {
        color: #333;
        margin-bottom: 20px;
        font-weight: 600;
    }
    .seller-info-content p {
        color: #666;
        line-height: 1.6;
        margin-bottom: 15px;
    }
    .seller-info-content ul {
        padding-left: 20px;
        margin-bottom: 25px;
    }
    .seller-info-content ul li {
        margin-bottom: 10px;
        color: #555;
    }
    .btn-admin-login {
        display: inline-block;
        margin-top: 20px;
        color: #7a9615;
        text-decoration: none;
        font-size: 14px;
        font-weight: 600;
    }
    .btn-admin-login:hover {
        text-decoration: underline;
    }
</style>

<div class="account-page">
    <div class="container">
        <div class="row">
            <!-- Login Form -->
            <div class="col-lg-6">
                <div class="form-container">
                    <div class="form-header login-header" style="background-color: #8dbd43;">
                        LOGIN FOR EXISTING SELLER
                    </div>
                    <div class="form-body">
                        <?php if ($error): ?>
                            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                        <?php endif; ?>
                        <form method="POST">
                            <div class="form-group">
                                <label for="email">Email Address <span class="required">*</span></label>
                                <input type="email" class="login-form-control" name="email" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="password">Password <span class="required">*</span></label>
                                <input type="password" class="login-form-control" name="password" required>
                            </div>
                            <div class="form-options">
                                <div class="remember-me">
                                    <input type="checkbox" id="remember_me">
                                    <label for="remember_me" style="font-weight: normal; font-size: 14px; margin-bottom:0;">Remember Me</label>
                                </div>
                                <button type="submit" class="login-btn login-btn-login" style="background-color: #7a9615;">Log In</button>
                            </div>
                            <div class="forgot-password" style="margin-top: 15px;">
                                <a href="#">Forgot Password?</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Information Column -->
            <div class="col-lg-6 register-form">
                <div class="form-container">
                    <div class="form-header register-header" style="background-color: #0d8ac1;">
                        BECOME A SELLER
                    </div>
                    <div class="form-body seller-info-content">
                        <h4>Start Selling on EverythingB2C</h4>
                        <p>Join our growing community of sellers and reach thousands of customers every day. We provide the tools you need to manage your business efficiently.</p>
                        
                        <ul>
                            <li><i class="fas fa-check-circle text-success me-2"></i> Low commission rates</li>
                            <li><i class="fas fa-check-circle text-success me-2"></i> Easy product management</li>
                            <li><i class="fas fa-check-circle text-success me-2"></i> Advanced sales reports</li>
                            <li><i class="fas fa-check-circle text-success me-2"></i> Secure and fast payments</li>
                        </ul>
                        
                        <p><strong>How to register?</strong></p>
                        <p>Currently, seller registration is handled by our administration team. Please contact us at <a href="mailto:info@everythingb2c.in">info@everythingb2c.in</a> or call us at <a href="tel:+918780406230">+91 878 040 6230</a> to upgrade your customer account to a seller account.</p>
                        
                        <div class="text-center">
                            <a href="../index.php" class="btn btn-outline-secondary mt-3">
                                <i class="fas fa-arrow-left me-1"></i> Back to Website
                            </a>
                        </div>
                        
                        <hr class="mt-4">
                        <div class="text-center">
                            <a href="../admin/login.php" class="btn-admin-login">
                                <i class="fas fa-user-shield me-1"></i> Admin Portal Login
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
