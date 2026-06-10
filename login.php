<?php
session_start();
require_once 'includes/functions.php';

$pageTitle = 'My Account';

// Redirect if already logged in
if (isLoggedIn()) {
    header('Location: myaccount.php'); // Should probably go to an account dashboard
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
                // Merge guest cart into user cart
                mergeSessionCartToUserCart($user['id']);
                $redirect = isset($_SESSION['redirect_after_login']) ? $_SESSION['redirect_after_login'] : 'myaccount.php';
                unset($_SESSION['redirect_after_login']);
                header('Location: ' . $redirect);
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

        if (empty($first_name) || empty($last_name) || empty($email) || empty($phone) || empty($password) || empty($confirm_password)) {
            $register_error = 'Please fill in all required fields';
        } elseif (!preg_match('/^[A-Za-z][A-Za-z ]{1,49}$/', $first_name)) {
            $register_error = 'First name should contain only letters and spaces';
        } elseif (!preg_match('/^[A-Za-z][A-Za-z ]{1,49}$/', $last_name)) {
            $register_error = 'Last name should contain only letters and spaces';
        } elseif (!preg_match('/^[6-9][0-9]{9}$/', $phone)) {
            $register_error = 'Please enter a valid 10 digit mobile number';
        } elseif ($password !== $confirm_password) {
            $register_error = 'Passwords do not match';
        } elseif (!preg_match('/^(?=.*[A-Za-z])(?=.*\d)(?=.*[^A-Za-z\d]).{8,}$/', $password)) {
            $register_error = 'Password must be at least 8 characters and include a letter, number, and special character';
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

// Breadcrumb Navigation
$breadcrumbs = generateBreadcrumb($pageTitle);
echo renderBreadcrumb($breadcrumbs);
?>
<link rel="stylesheet" href="asset/style/login.css">
<div class="account-page">
    <div class="container">
        <!-- <h1 class="account-title">My Account</h1> -->
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
                                <input type="email" class="login-form-control" name="email" required>
                            </div>
                            <div class="form-group">
                                <label for="password">Password <span class="required">*</span></label>
                                <div class="position-relative">
                                    <input type="password" class="login-form-control" name="password" required style="padding-right: 40px;">
                                    <span class="toggle-password" style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); cursor: pointer;">
                                        <i class="far fa-eye text-primary"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="form-options">
                                <div class="remember-me">
                                    <input type="checkbox" id="remember_me">
                                    <label for="remember_me" style="font-weight: normal; font-size: 14px; margin-bottom:0;">Remember Me</label>
                                </div>
                                <button type="submit" class="login-btn login-btn-login">Log In</button>
                            </div>
                            <div class="forgot-password" style="margin-top: 15px;">
                                <a href="forgot_password.php">Forgot Password?</a>
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
                                        <input type="text" class="login-form-control" name="first_name" placeholder="First Name" pattern="[A-Za-z][A-Za-z ]{1,49}" title="Only letters and spaces are allowed" maxlength="50" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="last_name">Last Name <span class="required">*</span></label>
                                        <input type="text" class="login-form-control" name="last_name" placeholder="Last Name" pattern="[A-Za-z][A-Za-z ]{1,49}" title="Only letters and spaces are allowed" maxlength="50" required>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="phone">Mobile Number <span class="required">*</span></label>
                                <div class="mobile-number-group">
                                    <input type="text" class="login-form-control country-code" value="+91" readonly>
                                    <input type="tel" class="login-form-control" name="phone" placeholder="Mobile Number" pattern="[6-9][0-9]{9}" title="Enter a valid 10 digit mobile number" maxlength="10" inputmode="numeric" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="email">Email Address <span class="required">*</span></label>
                                <input type="email" class="login-form-control" name="email" placeholder="Email Address" required>
                            </div>
                            <div class="form-group">
                                <label for="password">Password <span class="required">*</span></label>
                                <div class="position-relative">
                                    <input type="password" class="login-form-control" name="password" placeholder="Password" pattern="(?=.*[A-Za-z])(?=.*[0-9])(?=.*[^A-Za-z0-9]).{8,}" title="Minimum 8 characters with at least one letter, one number, and one special character" minlength="8" required style="padding-right: 40px;">
                                    <span class="toggle-password" style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); cursor: pointer;">
                                        <i class="far fa-eye text-primary"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="confirm_password">Confirm Password <span class="required">*</span></label>
                                <div class="position-relative">
                                    <input type="password" class="login-form-control" name="confirm_password" placeholder="Confirm Password" minlength="8" required style="padding-right: 40px;">
                                    <span class="toggle-password" style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); cursor: pointer;">
                                        <i class="far fa-eye text-primary"></i>
                                    </span>
                                </div>
                            </div>
                            <button type="submit" class="login-btn login-btn-register">Register</button>
                        </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const togglePasswordButtons = document.querySelectorAll('.toggle-password');
    togglePasswordButtons.forEach(button => {
        button.addEventListener('click', function() {
            const input = this.previousElementSibling;
            const icon = this.querySelector('i');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
    });

    const registerForm = document.querySelector('.register-form form');
    if (registerForm) {
        const firstName = registerForm.querySelector('input[name="first_name"]');
        const lastName = registerForm.querySelector('input[name="last_name"]');
        const phone = registerForm.querySelector('input[name="phone"]');
        const password = registerForm.querySelector('input[name="password"]');
        const confirmPassword = registerForm.querySelector('input[name="confirm_password"]');

        [firstName, lastName].forEach(input => {
            if (!input) return;
            input.addEventListener('input', function() {
                this.value = this.value.replace(/[^A-Za-z ]/g, '').replace(/\s{2,}/g, ' ');
            });
        });

        if (phone) {
            phone.addEventListener('input', function() {
                this.value = this.value.replace(/\D/g, '').slice(0, 10);
            });
        }

        function validateConfirmPassword() {
            if (!password || !confirmPassword) return;
            confirmPassword.setCustomValidity(
                confirmPassword.value && password.value !== confirmPassword.value
                    ? 'Passwords do not match'
                    : ''
            );
        }

        if (password) password.addEventListener('input', validateConfirmPassword);
        if (confirmPassword) confirmPassword.addEventListener('input', validateConfirmPassword);
    }
});
</script>
<?php include 'includes/footer.php'; ?> 
