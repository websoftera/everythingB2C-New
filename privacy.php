<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

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

<style>
/* Privacy Policy Page Styles */
.privacy-policy-content {
    background: #fff;
    padding: 40px;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    margin-bottom: 40px;
}

.page-title {
    color: #333;
    font-size: 32px;
    font-weight: 700;
    margin-bottom: 30px;
    text-align: center;
    border-bottom: 3px solid var(--site-blue);
    padding-bottom: 15px;
}

.privacy-section {
    margin-bottom: 35px;
}

.privacy-section h2 {
    color: var(--dark-blue);
    font-size: 24px;
    font-weight: 600;
    margin-bottom: 15px;
    border-left: 4px solid var(--site-blue);
    padding-left: 15px;
}

.intro-text {
    font-size: 16px;
    line-height: 1.6;
    color: #555;
    margin-bottom: 25px;
    text-align: center;
    font-style: italic;
}

.privacy-section p {
    font-size: 15px;
    line-height: 1.7;
    color: #666;
    margin-bottom: 15px;
}

.privacy-list {
    list-style: none;
    padding-left: 0;
}

.privacy-list li {
    font-size: 15px;
    line-height: 1.7;
    color: #666;
    margin-bottom: 12px;
    padding-left: 25px;
    position: relative;
}

.privacy-list li::before {
    content: "•";
    color: var(--site-blue);
    font-weight: bold;
    position: absolute;
    left: 0;
    top: 0;
}

.privacy-list li strong {
    color: #333;
    font-weight: 600;
}

.contact-info {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 6px;
    border-left: 4px solid var(--site-blue);
    margin-top: 15px;
}

.contact-info a {
    color: var(--site-blue);
    text-decoration: none;
    font-weight: 500;
}

.contact-info a:hover {
    color: var(--dark-blue);
    text-decoration: underline;
}

/* Responsive Design */
@media (max-width: 768px) {
    .privacy-policy-content {
        padding: 25px 20px;
        margin: 0 10px 30px 10px;
    }
    
    .page-title {
        font-size: 26px;
    }
    
    .privacy-section h2 {
        font-size: 20px;
    }
    
    .intro-text {
        font-size: 15px;
    }
    
    .privacy-section p,
    .privacy-list li {
        font-size: 14px;
    }
}

@media (max-width: 480px) {
    .privacy-policy-content {
        padding: 20px 15px;
    }
    
    .page-title {
        font-size: 22px;
    }
    
    .privacy-section h2 {
        font-size: 18px;
    }
}
</style>

<?php include 'includes/footer.php'; ?>
