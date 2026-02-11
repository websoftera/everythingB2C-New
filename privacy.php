<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

$pageCss = ['asset/style/privacy-page.css'];

// Include header
include 'includes/header.php';

// Breadcrumb Navigation
$breadcrumbs = generateBreadcrumb('Privacy Policy');
echo renderBreadcrumb($breadcrumbs);
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="privacy-policy-content">
                <h1 class="page-title">Privacy Policy</h1>
                
                <div class="privacy-section">
                    <p class="intro-text">
                        At everythingb2c.in, we are committed to safeguarding your privacy. This Privacy Policy outlines how we collect, use, disclose, and protect your personal information when you interact with our website and services.
                    </p>
                </div>

                <div class="privacy-section">
                    <h2>Information We Collect</h2>
                    <p>We may collect the following types of information:</p>
                    <ul class="privacy-list">
                        <li><strong>Personal Information:</strong> Name, email address, phone number, shipping and billing addresses, and payment details.</li>
                        <li><strong>Device Information:</strong> IP address, browser type, operating system, and other technical details.</li>
                        <li><strong>Usage Data:</strong> Pages visited, time spent on the site, and other analytical data.</li>
                        <li><strong>Transaction Information:</strong> Details of purchases and order history.</li>
                    </ul>
                </div>

                <div class="privacy-section">
                    <h2>How We Use Your Information</h2>
                    <p>Your information is used to:</p>
                    <ul class="privacy-list">
                        <li>Process and fulfill orders.</li>
                        <li>Communicate order updates and respond to inquiries.</li>
                        <li>Enhance our website and services.</li>
                        <li>Send promotional materials, with your consent.</li>
                        <li>Prevent fraudulent activities and ensure security.</li>
                    </ul>
                </div>

                <div class="privacy-section">
                    <h2>Sharing Your Information</h2>
                    <p>We do not sell or rent your personal information. However, we may share it with:</p>
                    <ul class="privacy-list">
                        <li><strong>Service Providers:</strong> Such as payment gateways, shipping partners, and marketing platforms, strictly for operational purposes.</li>
                        <li><strong>Legal Obligations:</strong> If required by law or to protect our rights.</li>
                    </ul>
                </div>

                <div class="privacy-section">
                    <h2>Amendment to the Policy</h2>
                    <p>
                        EverythingB2C.in reserves the right to change the Policy to its business requirements. We will post those changes on this site as and when modified. Such changes shall be effective immediately upon posting.
                    </p>
                </div>

                <div class="privacy-section">
                    <h2>Contact Us</h2>
                    <p>For any questions or concerns regarding this Privacy Policy, please contact us at:</p>
                    <p class="contact-info">
                        <strong>Email:</strong> <a href="mailto:info@everythingb2c.in">info@everythingb2c.in</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>


<?php include 'includes/back_to_top_button.php'; ?>
<?php include 'includes/footer.php'; ?>
