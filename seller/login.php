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
            background: linear-gradient(135deg, #9fbe1b 0%, #7a9615 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
            overflow: hidden;
            width: 100%;
            max-width: 450px;
        }
        .login-header {
            background: linear-gradient(135deg, #9fbe1b 0%, #7a9615 100%);
            color: white;
            padding: 40px 30px;
            text-align: center;
        }
        .login-header i {
            font-size: 3rem;
            margin-bottom: 15px;
        }
        .login-body {
            padding: 40px;
        }
        .form-control {
            border-radius: 10px;
            border: 2px solid #e9ecef;
            padding: 12px 15px;
        }
        .form-control:focus {
            border-color: #9fbe1b;
            box-shadow: 0 0 0 0.2rem rgba(159, 190, 27, 0.25);
        }
        .btn-login {
            background: linear-gradient(135deg, #9fbe1b 0%, #7a9615 100%);
            border: none;
            border-radius: 10px;
            padding: 12px;
            font-weight: 600;
            width: 100%;
            color: white;
        }
        .btn-login:hover {
            background: linear-gradient(135deg, #8ba817 0%, #6a8513 100%);
            color: white;
        }
        .input-group-text {
            background: #f8f9fa;
            border: 2px solid #e9ecef;
            border-right: none;
        }
        .input-group .form-control {
            border-left: none;
        }
        .seller-badge {
            background: #9fbe1b;
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="login-header">
            <i class="fas fa-store"></i>
            <h3>Seller Login</h3>
            <p class="mb-0">EverythingB2C Seller Portal</p>
            <span class="seller-badge mt-2 d-inline-block">Partner Dashboard</span>
        </div>
        <div class="login-body">
            <?php if ($error): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="mb-3">
                    <label for="email" class="form-label">Email Address</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                        <input type="email" class="form-control" id="email" name="email" 
                               value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" 
                               placeholder="seller@example.com" required>
                    </div>
                </div>
                
                <div class="mb-4">
                    <label for="password" class="form-label">Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input type="password" class="form-control" id="password" name="password" 
                               placeholder="Enter your password" required>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary btn-login">
                    <i class="fas fa-sign-in-alt"></i> Login to Seller Dashboard
                </button>
            </form>
            
            <div class="text-center mt-4">
                <p class="text-muted mb-2">
                    <i class="fas fa-info-circle"></i> For sellers only
                </p>
                <a href="../index.php" class="text-muted">
                    <i class="fas fa-arrow-left"></i> Back to Website
                </a>
            </div>
            
            <hr class="my-3">
            
            <div class="text-center">
                <small class="text-muted">
                    Are you an admin? 
                    <a href="../admin/login.php" class="text-decoration-none" style="color: #9fbe1b;">
                        <strong>Admin Login</strong>
                    </a>
                </small>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
