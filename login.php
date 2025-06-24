<?php
session_start();
require_once 'includes/functions.php';

$pageTitle = 'My Account';

// Redirect if already logged in
if (isLoggedIn()) {
    header('Location: my-account.php'); // Should probably go to an account dashboard
    exit;
}

$login_error = '';
$register_error = '';
$register_success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // LOGIN LOGIC
    if (isset($_POST['login'])) {
        $email = sanitizeInput($_POST['email']);
        $password = $_POST['password'];
        
        if (empty($email) || empty($password)) {
            $login_error = 'Please fill in all fields';
        } else {
            global $pdo;
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND is_active = 1");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                
                header('Location: index.php');
                exit;
            } else {
                $login_error = 'Invalid email or password';
            }
        }
    }

    // REGISTER LOGIC
    if (isset($_POST['register'])) {
        $first_name = sanitizeInput($_POST['first_name']);
        $last_name = sanitizeInput($_POST['last_name']);
        $email = sanitizeInput($_POST['email']);
        $phone = sanitizeInput($_POST['phone']);
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];

        $full_name = trim($first_name . ' ' . $last_name);

        if (empty($full_name) || empty($email) || empty($password)) {
            $register_error = 'Please fill in all required fields';
        } elseif ($password !== $confirm_password) {
            $register_error = 'Passwords do not match';
        } elseif (strlen($password) < 6) {
            $register_error = 'Password must be at least 6 characters long';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $register_error = 'Invalid email format';
        }
        else {
            global $pdo;
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $register_error = 'Email already registered';
            } else {
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO users (name, email, password, phone) VALUES (?, ?, ?, ?)");
                
                if ($stmt->execute([$full_name, $email, $hashedPassword, $phone])) {
                    // In a real app: mail($email, 'Your New Password', 'Your password is: ' . $password);
                    $register_success = 'Registration successful! You can now log in.';
                } else {
                    $register_error = 'Registration failed. Please try again.';
                }
            }
        }
    }
}

require_once 'includes/header.php';
?>

<link rel="stylesheet" href="asset/style/login.css">

<div class="account-page">
    <div class="container">
        <h1 class="account-title">My Account</h1>
        <div class="row">
            <!-- Login Form -->
            <div class="col-lg-6">
                <div class="form-container">
                    <div class="form-header login-header">
                        LOGIN FOR EXISTING CUSTOMER
                    </div>
                    <div class="form-body">
                        <?php if ($login_error): ?>
                            <div class="alert alert-danger"><?php echo $login_error; ?></div>
                        <?php endif; ?>
                        <form method="POST">
                            <input type="hidden" name="login" value="1">
                            <div class="form-group">
                                <label for="email">Email Address <span class="required">*</span></label>
                                <input type="email" class="form-control" name="email" required>
                            </div>
                            <div class="form-group">
                                <label for="password">Password <span class="required">*</span></label>
                                <input type="password" class="form-control" name="password" required>
                            </div>
                            
                            <div class="form-options">
                                <div class="remember-me">
                                    <input type="checkbox" id="remember_me">
                                    <label for="remember_me" style="font-weight: normal; font-size: 14px; margin-bottom:0;">Remember Me</label>
                                </div>
                                <button type="submit" class="btn btn-login">Log In</button>
                            </div>
                             <div class="forgot-password" style="margin-top: 15px;">
                                    <a href="#">Forgot Password?</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Register Form -->
            <div class="col-lg-6 register-form">
                <div class="form-container">
                    <div class="form-header register-header">
                        REGISTER FOR NEW CUSTOMER
                    </div>
                    <div class="form-body">
                        <?php if ($register_error): ?>
                            <div class="alert alert-danger"><?php echo $register_error; ?></div>
                        <?php endif; ?>
                        <?php if ($register_success): ?>
                            <div class="alert alert-success"><?php echo $register_success; ?></div>
                        <?php else: ?>
                        <form method="POST">
                            <input type="hidden" name="register" value="1">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="first_name">First Name <span class="required">*</span></label>
                                        <input type="text" class="form-control" name="first_name" placeholder="First Name" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="last_name">Last Name <span class="required">*</span></label>
                                        <input type="text" class="form-control" name="last_name" placeholder="Last Name" required>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="phone">Mobile Number <span class="required">*</span></label>
                                <div class="mobile-number-group">
                                    <input type="text" class="form-control country-code" value="+91" readonly>
                                    <input type="tel" class="form-control" name="phone" placeholder="Mobile Number" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="email">Email Address <span class="required">*</span></label>
                                <input type="email" class="form-control" name="email" placeholder="Email Address" required>
                            </div>
                            <div class="form-group">
                                <label for="password">Password <span class="required">*</span></label>
                                <input type="password" class="form-control" name="password" placeholder="Password" required>
                            </div>
                            <div class="form-group">
                                <label for="confirm_password">Confirm Password <span class="required">*</span></label>
                                <input type="password" class="form-control" name="confirm_password" placeholder="Confirm Password" required>
                            </div>
                            <button type="submit" class="btn btn-register">Register</button>
                        </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?> 