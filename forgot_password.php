<?php
session_start();
require_once 'includes/functions.php';

$pageTitle = 'Forgot Password';
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
                        FORGOT PASSWORD
                    </div>
                    <div class="form-body">
                        <div id="forgotPasswordMessage"></div>
                        <form id="forgotPasswordForm">
                            <p class="text-muted mb-4">Enter your email address and we'll send you a link to reset your password.</p>
                            <div class="form-group">
                                <label for="email">Email Address <span class="required">*</span></label>
                                <input type="email" class="login-form-control" name="email" id="email" required placeholder="Enter your registered email">
                            </div>
                            <div class="d-grid mt-4">
                                <button type="submit" class="login-btn login-btn-login" id="submitBtn" style="background-color: #7a9615;">Send Reset Link</button>
                            </div>
                            <div class="text-center mt-3">
                                <a href="login.php" class="text-muted"><i class="fas fa-arrow-left me-1"></i> Back to Login</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('forgotPasswordForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const email = document.getElementById('email').value;
    const submitBtn = document.getElementById('submitBtn');
    const messageDiv = document.getElementById('forgotPasswordMessage');
    
    // Disable button and show loading
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Sending...';
    
    fetch('ajax/forgot_password_process.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ email: email })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            messageDiv.innerHTML = `<div class="alert alert-success">${data.message}</div>`;
            document.getElementById('forgotPasswordForm').reset();
            // Optional: Hide form after success
            // document.getElementById('forgotPasswordForm').style.display = 'none';
        } else {
            messageDiv.innerHTML = `<div class="alert alert-danger">${data.message}</div>`;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        messageDiv.innerHTML = '<div class="alert alert-danger">An error occurred. Please try again later.</div>';
    })
    .finally(() => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = 'Send Reset Link';
    });
});
</script>

<?php include 'includes/footer.php'; ?>
