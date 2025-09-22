<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

// Include header
include 'includes/header.php';

// Breadcrumb Navigation
$breadcrumbs = generateBreadcrumb('Return and Refund Policy');
echo renderBreadcrumb($breadcrumbs);
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="returns-policy-content">
                <h1 class="page-title">Return and Refund Policy</h1>
                
                <div class="policy-section">
                    <p class="intro-text">
                        At EverythingB2C.in, customer satisfaction is our priority. If you are not entirely satisfied with your purchase, we're here to help.
                    </p>
                </div>

                <div class="policy-section">
                    <h2>Return and Refund Policy</h2>
                    <p>Products purchased from our site can be returned for a refund within 7 days from the date of the invoice provided:</p>
                    <ul class="policy-list">
                        <li>The product has not been used and has not been altered in any manner</li>
                        <li>The product is intact and in saleable condition, and</li>
                        <li>The product is accompanied by the original invoice of purchase.</li>
                    </ul>
                    <p>The products marked as non-refundable and purchased in bulk quantities cannot be returned by the customers.</p>
                </div>

                <div class="policy-section">
                    <h2>Please Note:</h2>
                    <ul class="policy-list">
                        <li>Products can only be returned for a refund. We don't provide an exchange of products.</li>
                        <li>In the event of any quality related complaint or feedback about a product, the customer should contact the consumer care number or the consumer care cell of the manufacturer/ marketer of the product. Details of the consumer care cell of the Manufacturer or Marketer will be on the packaging of the product.</li>
                        <li>EverythingB2C.in will refund customers through the same payment mode that was used to purchase the product online.</li>
                        <li>EverythingB2C.in shall not be held liable either directly or indirectly for the quality of any Product. Products covered under Manufacturer's warranty/guarantee cannot be returned to EverythingB2C, as the same is covered by the after-sale service offered by the manufacturer of the Product. EverythingB2C.in will not be directly or indirectly liable for goods covered under the manufacturer's warranty/guarantee.</li>
                        <li>In the event of any disputes in this regard, the same shall be referred to courts of competent jurisdiction at Pune.</li>
                        <li>EverythingB2C.in reserves the right to alter or modify any of the terms and conditions of this Policy without assigning any reason or providing intimation whatsoever. EverythingB2C's decision on the above would be final and the customer shall abide by the same unconditionally.</li>
                    </ul>
                </div>

                <div class="policy-section">
                    <h2>Consent</h2>
                    <p>By accessing this website, you consent to the terms and conditions of the Policy.</p>
                </div>

                <div class="policy-section">
                    <h2>Cancellation Policy</h2>
                    <p>Orders placed on EverythingB2C can be cancelled under the following conditions:</p>
                    <ul class="policy-list">
                        <li>The cancellation request is made before the order is dispatched from our warehouse.</li>
                        <li>Once an order has been shipped, it cannot be cancelled. In such cases, customers may choose to return the product in accordance with our Return and Refund Policy after receiving it.</li>
                    </ul>
                </div>

                <div class="policy-section">
                    <h2>Upon successful cancellation:</h2>
                    <ul class="policy-list">
                        <li>A full refund will be processed to the original method of payment</li>
                        <li>Refunds will be initiated within 5–7 business days from the date of confirmation</li>
                    </ul>
                </div>

                <div class="policy-section">
                    <h2>Please Note:</h2>
                    <p>EverythingB2C reserves the right to cancel an order under the following circumstances:</p>
                    <ul class="policy-list">
                        <li>The product is out of stock or unavailable</li>
                        <li>There are issues with payment confirmation</li>
                        <li>The shipping address is incomplete or non-serviceable</li>
                        <li>The order is flagged as suspicious or potentially fraudulent</li>
                    </ul>
                    <p>EverythingB2C.in reserves the right to modify this Policy at any time without prior notice.</p>
                </div>

                <div class="policy-section">
                    <h2>Contact Us</h2>
                    <p>For any questions or concerns regarding this Policy, please contact us at:</p>
                    <p class="contact-info">
                        <strong>Email:</strong> <a href="mailto:info@everythingb2c.in">info@everythingb2c.in</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Returns Policy Page Styles */
.returns-policy-content {
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

.policy-section {
    margin-bottom: 35px;
}

.policy-section h2 {
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

.policy-section p {
    font-size: 15px;
    line-height: 1.7;
    color: #666;
    margin-bottom: 15px;
}

.policy-list {
    list-style: none;
    padding-left: 0;
}

.policy-list li {
    font-size: 15px;
    line-height: 1.7;
    color: #666;
    margin-bottom: 12px;
    padding-left: 25px;
    position: relative;
}

.policy-list li::before {
    content: "•";
    color: var(--site-blue);
    font-weight: bold;
    position: absolute;
    left: 0;
    top: 0;
}

.policy-list li strong {
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
    .returns-policy-content {
        padding: 25px 20px;
        margin: 0 10px 30px 10px;
    }
    
    .page-title {
        font-size: 26px;
    }
    
    .policy-section h2 {
        font-size: 20px;
    }
    
    .intro-text {
        font-size: 15px;
    }
    
    .policy-section p,
    .policy-list li {
        font-size: 14px;
    }
}

@media (max-width: 480px) {
    .returns-policy-content {
        padding: 20px 15px;
    }
    
    .page-title {
        font-size: 22px;
    }
    
    .policy-section h2 {
        font-size: 18px;
    }
}
</style>

<?php include 'includes/footer.php'; ?>
