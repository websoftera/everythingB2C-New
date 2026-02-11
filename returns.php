<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

$pageCss = ['asset/style/returns.css'];

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


<?php include 'includes/back_to_top_button.php'; ?>
<?php include 'includes/footer.php'; ?>
