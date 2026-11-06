<?php
require_once 'config/database.php';
require_once 'includes/functions.php';
require_once 'includes/email_functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$_SESSION['contact_csrf'] = $_SESSION['contact_csrf'] ?? bin2hex(random_bytes(32));
$_SESSION['contact_form_started_at'] = $_SESSION['contact_form_started_at'] ?? time();

$success = $_SESSION['contact_flash_success'] ?? '';
unset($_SESSION['contact_flash_success']);
$error = '';
$values = ['name' => '', 'email' => '', 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $values = [
        'name' => trim((string)($_POST['name'] ?? '')),
        'email' => strtolower(trim((string)($_POST['email'] ?? ''))),
        'message' => trim((string)($_POST['message'] ?? '')),
    ];
    $submittedAt = time();

    if (!hash_equals($_SESSION['contact_csrf'], (string)($_POST['csrf_token'] ?? ''))) {
        $error = 'Your session has expired. Please refresh the page and try again.';
    } elseif (trim((string)($_POST['website'] ?? '')) !== '') {
        // Honeypot: accept silently so automated submitters receive no signal.
        $success = 'Thank you for contacting us. We will get back to you shortly.';
    } elseif ($submittedAt - (int)$_SESSION['contact_form_started_at'] < 3) {
        $error = 'Please take a moment to complete the form before submitting.';
    } elseif (isset($_SESSION['contact_last_submission']) && $submittedAt - (int)$_SESSION['contact_last_submission'] < 60) {
        $error = 'Please wait a minute before sending another enquiry.';
    } elseif ($values['name'] === '' || mb_strlen($values['name']) > 100) {
        $error = 'Please enter your name (up to 100 characters).';
    } elseif (!filter_var($values['email'], FILTER_VALIDATE_EMAIL) || strlen($values['email']) > 190) {
        $error = 'Please enter a valid email address.';
    } elseif ($values['message'] === '' || mb_strlen($values['message']) > 3000) {
        $error = 'Please enter a message between 1 and 3,000 characters.';
    } elseif (sendContactEnquiry($values['name'], $values['email'], $values['message'])) {
        $_SESSION['contact_last_submission'] = $submittedAt;
        $_SESSION['contact_csrf'] = bin2hex(random_bytes(32));
        $_SESSION['contact_form_started_at'] = $submittedAt;
        $_SESSION['contact_flash_success'] = 'Thank you! Your enquiry has been sent successfully. We will get back to you shortly.';
        header('Location: contact.php?sent=1');
        exit;
    } else {
        $error = 'We could not send your enquiry right now. Please try again later or contact us by phone.';
    }
}

$pageTitle = 'Contact Us';
include 'includes/header.php';
echo renderBreadcrumb(generateBreadcrumb($pageTitle));
?>

<main class="container my-4 my-md-5">
    <div class="row justify-content-center">
        <div class="col-lg-9 col-xl-8">
            <section class="card shadow-sm border-0 contact-card">
                <div class="card-body p-4 p-md-5">
                    <h1 class="h2 text-center mb-3">Contact Us</h1>
                    <p class="text-muted text-center mb-4">Send us your enquiry and our team will get back to you shortly.</p>

                    <?php if ($success !== ''): ?>
                        <div class="alert alert-success" role="status"><?php echo htmlspecialchars($success, ENT_QUOTES, 'UTF-8'); ?></div>
                    <?php endif; ?>
                    <?php if ($error !== ''): ?>
                        <div class="alert alert-danger" role="alert"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
                    <?php endif; ?>

                    <form method="post" action="contact.php" novalidate>
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['contact_csrf'], ENT_QUOTES, 'UTF-8'); ?>">
                        <div class="visually-hidden" aria-hidden="true">
                            <label for="website">Website</label>
                            <input type="text" id="website" name="website" tabindex="-1" autocomplete="off">
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label" for="name">Name</label>
                                <input class="form-control" id="name" name="name" type="text" maxlength="100" autocomplete="name" required value="<?php echo htmlspecialchars($values['name'], ENT_QUOTES, 'UTF-8'); ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="email">Email address</label>
                                <input class="form-control" id="email" name="email" type="email" maxlength="190" autocomplete="email" required value="<?php echo htmlspecialchars($values['email'], ENT_QUOTES, 'UTF-8'); ?>">
                            </div>
                            <div class="col-12">
                                <label class="form-label" for="message">Message</label>
                                <textarea class="form-control" id="message" name="message" rows="7" maxlength="3000" required><?php echo htmlspecialchars($values['message'], ENT_QUOTES, 'UTF-8'); ?></textarea>
                            </div>
                            <div class="col-12 text-center">
                                <button class="btn btn-primary px-4" type="submit">Send Enquiry</button>
                            </div>
                        </div>
                    </form>
                </div>
            </section>
        </div>
    </div>
</main>

<style>
.contact-card { border-radius: 10px; }
.contact-card h1 { color: var(--site-blue, #0087be); font-weight: 700; }
.contact-card .form-control:focus { border-color: var(--site-blue, #0087be); box-shadow: 0 0 0 .2rem rgba(0, 135, 190, .18); }
</style>

<?php include 'includes/footer.php'; ?>
