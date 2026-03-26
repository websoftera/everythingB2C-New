<?php
session_start();
require_once 'includes/functions.php';

$token = isset($_GET['token']) ? $_GET['token'] : '';
$error = '';
$user_id = null;

if (empty($token)) {
    $error = 'Invalid or missing reset token.';
} else {
    try {
        global $pdo;
        $stmt = $pdo->prepare("SELECT id FROM users WHERE reset_token = ? AND reset_token_expiry > NOW() AND is_active = 1");
        $stmt->execute([$token]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$user) {
            $error = 'The reset link is invalid or has expired.';
        } else {
            $user_id = $user['id'];
        }
    } catch (Exception $e) {
        $error = 'A database error occurred.';
    }
}

$pageTitle = 'Reset Password';
require_once 'includes/header.php';

// Breadcrumb Navigation
$breadcrumbs = generateBreadcrumb($pageTitle);
echo renderBreadcrumb($breadcrumbs);
?>
<link rel="stylesheet" href="asset/style/login.css">
<div class="account-page">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="form-container">
                    <div class="form-header login-header" style="background-color: #8dbd43;">
                        RESET PASSWORD
                    </div>
                    <div class="form-body">
                        <div id="resetPasswordMessage">
                            <?php if ($error): ?>
                                <div class="alert alert-danger"><?php echo $error; ?></div>
                            <?php endif; ?>
                        </div>
                        
                        <?php if (!$error): ?>
                        <form id="resetPasswordForm">
                            <input type="hidden" name="token" id="token" value="<?php echo htmlspecialchars($token); ?>">
                            <p class="text-muted mb-4">Please enter your new password below.</p>
                            
                            <div class="form-group">
                                <label for="password">New Password <span class="required">*</span></label>
                                <div class="position-relative">
                                    <input type="password" class="login-form-control" name="password" id="password" required style="padding-right: 40px;">
                                    <span class="toggle-password" style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); cursor: pointer;">
                                        <i class="far fa-eye text-primary"></i>
                                    </span>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="confirm_password">Confirm New Password <span class="required">*</span></label>
                                <div class="position-relative">
                                    <input type="password" class="login-form-control" name="confirm_password" id="confirm_password" required style="padding-right: 40px;">
                                    <span class="toggle-password" style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); cursor: pointer;">
                                        <i class="far fa-eye text-primary"></i>
                                    </span>
                                </div>
                            </div>
                            
                            <div class="d-grid mt-4">
                                <button type="submit" class="login-btn login-btn-login" id="submitBtn" style="background-color: #7a9615;">Update Password</button>
                            </div>
                        </form>
                        <?php else: ?>
                            <div class="text-center mt-3">
                                <a href="forgot_password.php" class="btn btn-outline-secondary">Request New Link</a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Password Toggle
    const toggleButtons = document.querySelectorAll('.toggle-password');
    toggleButtons.forEach(btn => {
        btn.onclick = function() {
            const input = this.previousElementSibling;
            const icon = this.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        };
    });

    const resetForm = document.getElementById('resetPasswordForm');
    if (resetForm) {
        resetForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            const token = document.getElementById('token').value;
            const submitBtn = document.getElementById('submitBtn');
            const messageDiv = document.getElementById('resetPasswordMessage');
            
            if (password !== confirmPassword) {
                messageDiv.innerHTML = '<div class="alert alert-danger">Passwords do not match!</div>';
                return;
            }
            
            if (password.length < 6) {
                messageDiv.innerHTML = '<div class="alert alert-danger">Password must be at least 6 characters long.</div>';
                return;
            }
            
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Updating...';
            
            fetch('ajax/reset_password_process.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ 
                    token: token,
                    password: password
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    messageDiv.innerHTML = `<div class="alert alert-success">${data.message}</div>`;
                    resetForm.style.display = 'none';
                    setTimeout(() => {
                        window.location.href = 'login.php';
                    }, 3000);
                } else {
                    messageDiv.innerHTML = `<div class="alert alert-danger">${data.message}</div>`;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                messageDiv.innerHTML = '<div class="alert alert-danger">An error occurred. Please try again.</div>';
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = 'Update Password';
            });
        });
    }
});
</script>

<?php include 'includes/footer.php'; ?>
