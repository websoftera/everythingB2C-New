<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';
require_once '../includes/seller_application.php';

// Redirect if already logged in
if (isset($_SESSION['seller_id'])) {
    header('Location: index.php');
    exit;
}

$error = '';
$applicationError = '';
$applicationSuccess = $_SESSION['seller_application_flash'] ?? '';
unset($_SESSION['seller_application_flash']);
$_SESSION['seller_application_csrf'] = $_SESSION['seller_application_csrf'] ?? bin2hex(random_bytes(32));
$sellerCategories = getParentCategories();
$sellerApplicationSchemaReady = ensureSellerApplicationsSchema($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['seller_application'])) {
    $uploadedDocuments = [];
    try {
        if (!$sellerApplicationSchemaReady) {
            throw new RuntimeException('Registration is temporarily unavailable. Please contact us at 6355837347.');
        }
        if (!hash_equals($_SESSION['seller_application_csrf'], (string)($_POST['seller_application_csrf'] ?? ''))) {
            throw new RuntimeException('Your session expired. Refresh the page and try again.');
        }
        if (trim((string)($_POST['website'] ?? '')) !== '') {
            throw new RuntimeException('Unable to submit this application.');
        }

        $firmType = trim((string)($_POST['firm_type'] ?? ''));
        $personName = trim((string)($_POST['person_name'] ?? ''));
        $mobile = preg_replace('/\D+/', '', (string)($_POST['mobile'] ?? ''));
        $businessName = trim((string)($_POST['business_name'] ?? ''));
        $businessEmail = strtolower(trim((string)($_POST['business_email'] ?? '')));
        $businessAddress = trim((string)($_POST['business_address'] ?? ''));
        $categoryId = (int)($_POST['business_category'] ?? 0);
        $panNumber = strtoupper(preg_replace('/\s+/', '', (string)($_POST['pan_number'] ?? '')));
        $gstin = strtoupper(preg_replace('/\s+/', '', (string)($_POST['gstin'] ?? '')));
        $validFirmTypes = ['Proprietorship', 'Partnership', 'Limited Company'];
        $categoryById = [];
        foreach ($sellerCategories as $category) {
            $categoryById[(int)$category['id']] = (string)$category['name'];
        }

        if (!in_array($firmType, $validFirmTypes, true)) {
            throw new RuntimeException('Please select a valid firm type.');
        }
        if ($personName === '' || mb_strlen($personName) > 150) {
            throw new RuntimeException('Please enter the contact person name (up to 150 characters).');
        }
        if (!preg_match('/^[6-9][0-9]{9}$/', $mobile)) {
            throw new RuntimeException('Please enter a valid 10-digit mobile number.');
        }
        if ($businessName === '' || mb_strlen($businessName) > 180) {
            throw new RuntimeException('Please enter the business name (up to 180 characters).');
        }
        if (!filter_var($businessEmail, FILTER_VALIDATE_EMAIL) || strlen($businessEmail) > 190) {
            throw new RuntimeException('Please enter a valid email address.');
        }
        if (!isset($categoryById[$categoryId])) {
            throw new RuntimeException('Please select a business category.');
        }
        if (mb_strlen($businessAddress) > 2000) {
            throw new RuntimeException('Business address must be 2,000 characters or fewer.');
        }
        if ($panNumber !== '' && !preg_match('/^[A-Z]{5}[0-9]{4}[A-Z]$/', $panNumber)) {
            throw new RuntimeException('Please enter a valid PAN number.');
        }
        if ($gstin !== '' && !preg_match('/^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z][1-9A-Z]Z[0-9A-Z]$/', $gstin)) {
            throw new RuntimeException('Please enter a valid GSTIN.');
        }

        $uploadDirectory = dirname(__DIR__) . '/private_storage/seller_applications';
        foreach (['gstin_document', 'pan_document', 'aadhaar_document'] as $documentKey) {
            $uploadedDocuments[$documentKey] = storeSellerApplicationDocument($_FILES[$documentKey] ?? [], $documentKey, $uploadDirectory);
        }

        $insert = $pdo->prepare("INSERT INTO seller_registration_applications
            (firm_type, person_name, mobile, business_name, email, business_address, business_category, pan_number, gstin, gstin_document, pan_document, aadhaar_document)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $insert->execute([
            $firmType, $personName, $mobile, $businessName, $businessEmail,
            $businessAddress !== '' ? $businessAddress : null, $categoryById[$categoryId],
            $panNumber !== '' ? $panNumber : null, $gstin !== '' ? $gstin : null,
            $uploadedDocuments['gstin_document'], $uploadedDocuments['pan_document'], $uploadedDocuments['aadhaar_document'],
        ]);
        $applicationId = (int)$pdo->lastInsertId();

        $emailSent = false;
        try {
            require_once '../vendor/autoload.php';
            $emailConfig = require '../config/email.php';
            $mailer = new \PHPMailer\PHPMailer\PHPMailer(true);
            $mailer->isSMTP();
            $mailer->Host = $emailConfig['smtp']['host'];
            $mailer->SMTPAuth = true;
            $mailer->Username = $emailConfig['smtp']['username'];
            $mailer->Password = $emailConfig['smtp']['password'];
            $mailer->SMTPSecure = $emailConfig['smtp']['encryption'];
            $mailer->Port = (int)$emailConfig['smtp']['port'];
            $mailer->CharSet = 'UTF-8';
            $mailer->setFrom($emailConfig['smtp']['from_email'], $emailConfig['smtp']['from_name']);
            $mailer->addAddress('info@everythingb2c.in');
            $mailer->addReplyTo($businessEmail, $personName);
            $mailer->isHTML(true);
            $mailer->Subject = 'New seller registration application #' . $applicationId;
            $escape = static fn($value) => htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
            $mailer->Body = '<h2>New seller registration application</h2>'
                . '<p><strong>Application ID:</strong> ' . $applicationId . '</p>'
                . '<p><strong>Firm:</strong> ' . $escape($firmType) . '<br>'
                . '<strong>Contact person:</strong> ' . $escape($personName) . '<br>'
                . '<strong>Mobile:</strong> ' . $escape($mobile) . '<br>'
                . '<strong>Business:</strong> ' . $escape($businessName) . '<br>'
                . '<strong>Email:</strong> ' . $escape($businessEmail) . '<br>'
                . '<strong>Address:</strong> ' . nl2br($escape($businessAddress)) . '<br>'
                . '<strong>Category:</strong> ' . $escape($categoryById[$categoryId]) . '<br>'
                . '<strong>PAN:</strong> ' . $escape($panNumber ?: 'Not provided') . '<br>'
                . '<strong>GSTIN:</strong> ' . $escape($gstin ?: 'Not provided') . '</p>';
            foreach ($uploadedDocuments as $documentKey => $relativePath) {
                if ($relativePath !== null) {
                    $mailer->addAttachment(dirname(__DIR__) . '/' . $relativePath, ucwords(str_replace('_', ' ', $documentKey)) . '.' . pathinfo($relativePath, PATHINFO_EXTENSION));
                }
            }
            $mailer->send();
            $emailSent = true;
        } catch (Throwable $mailError) {
            error_log('Seller application email failed for #' . $applicationId . ': ' . $mailError->getMessage());
        }

        $_SESSION['seller_application_flash'] = $emailSent
            ? 'Thank you. Your seller application was submitted. Our team will contact you soon.'
            : 'Your application was saved, but the email notification could not be sent. Please call 6355837347 and mention application #' . $applicationId . '.';
        header('Location: login.php#seller-application');
        exit;
    } catch (Throwable $applicationErrorException) {
        foreach ($uploadedDocuments as $relativePath) {
            if ($relativePath !== null) {
                $savedFile = dirname(__DIR__) . '/' . $relativePath;
                if (is_file($savedFile)) {
                    unlink($savedFile);
                }
            }
        }
        $applicationError = $applicationErrorException instanceof RuntimeException
            ? $applicationErrorException->getMessage()
            : 'Unable to submit your application right now. Please try again or call 6355837347.';
        if (!($applicationErrorException instanceof RuntimeException)) {
            error_log('Seller application submission failed: ' . $applicationErrorException->getMessage());
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
    .seller-application-form .form-group { margin-bottom: 16px; }
    .seller-application-form label { display: block; margin-bottom: 6px; font-weight: 600; color: #263238; }
    .seller-application-form .form-control,
    .seller-application-form .form-select { min-height: 46px; border: 1px solid #ced4da; border-radius: 7px; padding: 10px 12px; }
    .seller-application-form .input-group { flex-wrap: nowrap; }
    .seller-application-form .input-group-text { min-height: 46px; padding: 10px 14px; color: #263238; background: #fff; border: 1px solid #ced4da; border-right: 0; border-radius: 7px 0 0 7px; }
    .seller-application-form .input-group .form-control { min-width: 0; border-radius: 0 7px 7px 0; }
    .seller-application-form textarea.form-control { min-height: 100px; }
    .seller-application-form .document-note { color: #697586; font-size: 13px; margin-top: 4px; }
    .seller-application-form .seller-submit { min-height: 48px; padding: 10px 28px; border: 0; border-radius: 7px; color: #fff; background: #0d8ac1; font-weight: 700; }
    .seller-application-form .seller-submit:hover { background: #0876a7; }
    .seller-application-form .required { color: #dc3545; }
    .seller-application-form .honeypot { position: absolute; left: -10000px; width: 1px; height: 1px; overflow: hidden; }
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
                            <input type="hidden" name="login" value="1">
                            <div class="form-group">
                                <label for="email">Email Address <span class="required">*</span></label>
                                <input type="email" class="login-form-control" name="email" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
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
                                <button type="submit" class="login-btn login-btn-login" style="background-color: #7a9615;">Log In</button>
                            </div>
                            <div class="forgot-password" style="margin-top: 15px;">
                                <a href="../forgot_password.php">Forgot Password?</a>
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
                    <div class="form-body seller-info-content" id="seller-application">
                        <h4>Register your business</h4>
                        <p>Complete this form to apply to sell on EverythingB2C. Our team will review your details and contact you.</p>
                        <?php if ($applicationError): ?><div class="alert alert-danger" role="alert"><?php echo htmlspecialchars($applicationError, ENT_QUOTES, 'UTF-8'); ?></div><?php endif; ?>
                        <?php if ($applicationSuccess): ?><div class="alert alert-success" role="status"><?php echo htmlspecialchars($applicationSuccess, ENT_QUOTES, 'UTF-8'); ?></div><?php endif; ?>
                        <?php if (!$sellerApplicationSchemaReady): ?><div class="alert alert-warning" role="alert">The seller application form is temporarily unavailable. Please call <a href="tel:+916355837347">6355837347</a>.</div><?php endif; ?>
                        <form class="seller-application-form" method="post" enctype="multipart/form-data" autocomplete="on">
                            <input type="hidden" name="seller_application" value="1">
                            <input type="hidden" name="seller_application_csrf" value="<?php echo htmlspecialchars($_SESSION['seller_application_csrf'], ENT_QUOTES, 'UTF-8'); ?>">
                            <div class="honeypot" aria-hidden="true"><label for="seller-website">Leave this field empty</label><input id="seller-website" type="text" name="website" tabindex="-1" autocomplete="off"></div>
                            <div class="row">
                                <div class="col-md-6"><div class="form-group"><label for="firm-type">Firm <span class="required">*</span></label><select class="form-select" id="firm-type" name="firm_type" required><option value="">Select firm type</option><option>Proprietorship</option><option>Partnership</option><option>Limited Company</option></select></div></div>
                                <div class="col-md-6"><div class="form-group"><label for="person-name">Person Name <span class="required">*</span></label><input class="form-control" id="person-name" name="person_name" type="text" maxlength="150" autocomplete="name" required></div></div>
                                <div class="col-md-6"><div class="form-group"><label for="seller-mobile">Mobile <span class="required">*</span></label><div class="input-group"><span class="input-group-text">+91</span><input class="form-control" id="seller-mobile" name="mobile" type="tel" inputmode="numeric" pattern="[6-9][0-9]{9}" maxlength="10" autocomplete="tel-national" required></div></div></div>
                                <div class="col-md-6"><div class="form-group"><label for="business-name">Business Name <span class="required">*</span></label><input class="form-control" id="business-name" name="business_name" type="text" maxlength="180" autocomplete="organization" required></div></div>
                                <div class="col-md-6"><div class="form-group"><label for="business-email">Email <span class="required">*</span></label><input class="form-control" id="business-email" name="business_email" type="email" maxlength="190" autocomplete="email" required></div></div>
                                <div class="col-md-6"><div class="form-group"><label for="business-category">Business Category <span class="required">*</span></label><select class="form-select" id="business-category" name="business_category" required><option value="">Select a category</option><?php foreach ($sellerCategories as $category): ?><option value="<?php echo (int)$category['id']; ?>"><?php echo htmlspecialchars($category['name'], ENT_QUOTES, 'UTF-8'); ?></option><?php endforeach; ?></select></div></div>
                                <div class="col-12"><div class="form-group"><label for="business-address">Business Address</label><textarea class="form-control" id="business-address" name="business_address" maxlength="2000" autocomplete="street-address"></textarea></div></div>
                                <div class="col-md-6"><div class="form-group"><label for="pan-number">PAN No.</label><input class="form-control" id="pan-number" name="pan_number" type="text" maxlength="10" pattern="[A-Za-z]{5}[0-9]{4}[A-Za-z]" title="Enter a valid 10-character PAN" autocomplete="off"></div></div>
                                <div class="col-md-6"><div class="form-group"><label for="gstin">GSTIN</label><input class="form-control" id="gstin" name="gstin" type="text" maxlength="15" pattern="[0-9]{2}[A-Za-z]{5}[0-9]{4}[A-Za-z][1-9A-Za-z]Z[0-9A-Za-z]" title="Enter a valid 15-character GSTIN" autocomplete="off"></div></div>
                                <div class="col-md-4"><div class="form-group"><label for="gstin-document">GSTIN Document</label><input class="form-control" id="gstin-document" name="gstin_document" type="file" accept=".pdf,.jpg,.jpeg,.png"><div class="document-note">PDF, JPG or PNG; maximum 5 MB.</div></div></div>
                                <div class="col-md-4"><div class="form-group"><label for="pan-document">PAN Document</label><input class="form-control" id="pan-document" name="pan_document" type="file" accept=".pdf,.jpg,.jpeg,.png"><div class="document-note">PDF, JPG or PNG; maximum 5 MB.</div></div></div>
                                <div class="col-md-4"><div class="form-group"><label for="aadhaar-document">Aadhaar Card</label><input class="form-control" id="aadhaar-document" name="aadhaar_document" type="file" accept=".pdf,.jpg,.jpeg,.png"><div class="document-note">PDF, JPG or PNG; maximum 5 MB.</div></div></div>
                            </div>
                            <button class="seller-submit" type="submit" <?php echo $sellerApplicationSchemaReady ? '' : 'disabled'; ?>><i class="fas fa-paper-plane me-2"></i>Submit Application</button>
                        </form>
                        <p class="mt-3 mb-0">Need help? Contact us at <a href="tel:+916355837347">6355837347</a> or <a href="mailto:info@everythingb2c.in">info@everythingb2c.in</a>.</p>
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
});
</script>

<?php include '../includes/footer.php'; ?>
