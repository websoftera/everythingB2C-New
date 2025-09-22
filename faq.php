<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

// Include header
include 'includes/header.php';

// Breadcrumb Navigation
$breadcrumbs = generateBreadcrumb('Frequently Asked Questions');
echo renderBreadcrumb($breadcrumbs);
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="faq-content">
                <h1 class="page-title">Frequently Asked Questions</h1>
                
                <div class="faq-intro">
                    <p class="intro-text">
                        Find answers to the most commonly asked questions about EverythingB2C.in. If you don't find what you're looking for, feel free to contact us.
                    </p>
                </div>

                <div class="faq-section">
                    <h2>General Questions</h2>
                    
                    <div class="faq-item">
                        <h3 class="faq-question">What is EverythingB2C.in?</h3>
                        <div class="faq-answer">
                            <p>EverythingB2C.in is a comprehensive B2C platform offering a wide range of products including office stationery, personal care items, cleaning supplies, home essentials, and more. We provide quality products at competitive prices with reliable delivery across India.</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <h3 class="faq-question">How do I place an order?</h3>
                        <div class="faq-answer">
                            <p>Placing an order is simple:</p>
                            <ol>
                                <li>Browse our product categories and select the items you need</li>
                                <li>Add products to your cart</li>
                                <li>Review your cart and proceed to checkout</li>
                                <li>Enter your shipping details and payment information</li>
                                <li>Confirm your order</li>
                            </ol>
                        </div>
                    </div>

                    <div class="faq-item">
                        <h3 class="faq-question">What payment methods do you accept?</h3>
                        <div class="faq-answer">
                            <p>We accept various payment methods including:</p>
                            <ul>
                                <li>Credit/Debit Cards (Visa, MasterCard, RuPay)</li>
                                <li>Net Banking</li>
                                <li>UPI Payments</li>
                                <li>Digital Wallets</li>
                                <li>Cash on Delivery (COD) - subject to availability</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="faq-section">
                    <h2>Shipping & Delivery</h2>
                    
                    <div class="faq-item">
                        <h3 class="faq-question">How long does delivery take?</h3>
                        <div class="faq-answer">
                            <p>Delivery times vary by location:</p>
                            <ul>
                                <li><strong>Metro cities:</strong> 2-3 business days</li>
                                <li><strong>Tier 2 cities:</strong> 3-5 business days</li>
                                <li><strong>Other locations:</strong> 5-7 business days</li>
                            </ul>
                            <p>Delivery times may be extended during festivals or peak seasons.</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <h3 class="faq-question">Do you deliver to all locations in India?</h3>
                        <div class="faq-answer">
                            <p>Yes, we deliver to most locations across India. However, delivery to remote areas may take longer or may not be available. You can check delivery availability by entering your pincode during checkout.</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <h3 class="faq-question">Can I track my order?</h3>
                        <div class="faq-answer">
                            <p>Yes, once your order is dispatched, you will receive a tracking number via SMS and email. You can use this tracking number to monitor your order's progress on our website or the courier company's website.</p>
                        </div>
                    </div>
                </div>

                <div class="faq-section">
                    <h2>Returns & Refunds</h2>
                    
                    <div class="faq-item">
                        <h3 class="faq-question">What is your return policy?</h3>
                        <div class="faq-answer">
                            <p>We offer a 7-day return policy from the date of invoice. Products can be returned if:</p>
                            <ul>
                                <li>The product has not been used or altered</li>
                                <li>The product is in original condition with tags</li>
                                <li>You have the original invoice</li>
                            </ul>
                            <p>Please note that we only provide refunds, not exchanges.</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <h3 class="faq-question">How long does it take to process a refund?</h3>
                        <div class="faq-answer">
                            <p>Refunds are typically processed within 5-7 business days after we receive and verify the returned product. The refund will be credited to the same payment method used for the original purchase.</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <h3 class="faq-question">Can I cancel my order?</h3>
                        <div class="faq-answer">
                            <p>You can cancel your order before it is dispatched from our warehouse. Once the order has been shipped, you cannot cancel it, but you can return the product after receiving it according to our return policy.</p>
                        </div>
                    </div>
                </div>

                <div class="faq-section">
                    <h2>Account & Security</h2>
                    
                    <div class="faq-item">
                        <h3 class="faq-question">Do I need to create an account to shop?</h3>
                        <div class="faq-answer">
                            <p>While you can browse our products without an account, creating an account allows you to:</p>
                            <ul>
                                <li>Save your shipping addresses</li>
                                <li>Track your order history</li>
                                <li>Save items to your wishlist</li>
                                <li>Faster checkout process</li>
                            </ul>
                        </div>
                    </div>

                    <div class="faq-item">
                        <h3 class="faq-question">Is my personal information secure?</h3>
                        <div class="faq-answer">
                            <p>Yes, we take your privacy and security seriously. We use SSL encryption to protect your personal and payment information. We never share your personal details with third parties without your consent.</p>
                        </div>
                    </div>
                </div>

                <div class="faq-section">
                    <h2>Product Information</h2>
                    
                    <div class="faq-item">
                        <h3 class="faq-question">Are the product images accurate?</h3>
                        <div class="faq-answer">
                            <p>We strive to provide accurate product images and descriptions. However, actual colors may vary slightly due to monitor settings. If you have any concerns about a product, please contact our customer support before placing your order.</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <h3 class="faq-question">What if a product is out of stock?</h3>
                        <div class="faq-answer">
                            <p>If a product is out of stock, it will be marked as "Out of Stock" on the product page. You can sign up for notifications to be alerted when the product becomes available again.</p>
                        </div>
                    </div>

                    <div class="faq-item">
                        <h3 class="faq-question">Do you offer bulk discounts?</h3>
                        <div class="faq-answer">
                            <p>Yes, we offer special pricing for bulk orders. Please contact our sales team at info@everythingb2c.in for bulk pricing and custom quotes.</p>
                        </div>
                    </div>
                </div>

                <div class="faq-section">
                    <h2>Customer Support</h2>
                    
                    <div class="faq-item">
                        <h3 class="faq-question">How can I contact customer support?</h3>
                        <div class="faq-answer">
                            <p>You can reach our customer support team through:</p>
                            <ul>
                                <li><strong>Email:</strong> info@everythingb2c.in</li>
                                <li><strong>Phone:</strong> +91 878 040 6230</li>
                                <li><strong>Business Hours:</strong> Monday to Saturday, 9:00 AM - 6:00 PM</li>
                            </ul>
                        </div>
                    </div>

                    <div class="faq-item">
                        <h3 class="faq-question">What if I have a complaint about product quality?</h3>
                        <div class="faq-answer">
                            <p>For quality-related complaints, please contact the manufacturer's customer care number provided on the product packaging. For issues with our service or delivery, please contact our customer support team.</p>
                        </div>
                    </div>
                </div>

                <div class="contact-section">
                    <h2>Still Have Questions?</h2>
                    <p>If you couldn't find the answer to your question, please don't hesitate to contact us. Our customer support team is here to help!</p>
                    <div class="contact-info">
                        <p><strong>Email:</strong> <a href="mailto:info@everythingb2c.in">info@everythingb2c.in</a></p>
                        <p><strong>Phone:</strong> <a href="tel:+918780406230">+91 878 040 6230</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* FAQ Page Styles */
.faq-content {
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

.faq-intro {
    margin-bottom: 40px;
}

.intro-text {
    font-size: 16px;
    line-height: 1.6;
    color: #555;
    text-align: center;
    font-style: italic;
}

.faq-section {
    margin-bottom: 40px;
}

.faq-section h2 {
    color: var(--dark-blue);
    font-size: 24px;
    font-weight: 600;
    margin-bottom: 25px;
    border-left: 4px solid var(--site-blue);
    padding-left: 15px;
}

.faq-item {
    margin-bottom: 20px;
    border: 1px solid #e9ecef;
    border-radius: 6px;
    overflow: hidden;
}

.faq-question {
    background: #f8f9fa;
    color: var(--dark-blue);
    font-size: 16px;
    font-weight: 600;
    padding: 15px 20px;
    margin: 0;
    cursor: pointer;
    border-bottom: 1px solid #e9ecef;
    transition: background-color 0.2s ease;
}

.faq-question:hover {
    background: #e9ecef;
}

.faq-answer {
    padding: 20px;
    background: #fff;
}

.faq-answer p {
    font-size: 15px;
    line-height: 1.7;
    color: #666;
    margin-bottom: 15px;
}

.faq-answer p:last-child {
    margin-bottom: 0;
}

.faq-answer ul,
.faq-answer ol {
    margin: 15px 0;
    padding-left: 25px;
}

.faq-answer li {
    font-size: 15px;
    line-height: 1.7;
    color: #666;
    margin-bottom: 8px;
}

.faq-answer strong {
    color: #333;
    font-weight: 600;
}

.contact-section {
    background: #f8f9fa;
    padding: 30px;
    border-radius: 8px;
    border-left: 4px solid var(--site-blue);
    text-align: center;
    margin-top: 40px;
}

.contact-section h2 {
    color: var(--dark-blue);
    font-size: 22px;
    font-weight: 600;
    margin-bottom: 15px;
    border: none;
    padding: 0;
}

.contact-section p {
    font-size: 15px;
    line-height: 1.6;
    color: #666;
    margin-bottom: 20px;
}

.contact-info {
    margin-top: 20px;
}

.contact-info p {
    margin-bottom: 10px;
    font-size: 15px;
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
    .faq-content {
        padding: 25px 20px;
        margin: 0 10px 30px 10px;
    }
    
    .page-title {
        font-size: 26px;
    }
    
    .faq-section h2 {
        font-size: 20px;
    }
    
    .faq-question {
        font-size: 15px;
        padding: 12px 15px;
    }
    
    .faq-answer {
        padding: 15px;
    }
    
    .faq-answer p,
    .faq-answer li {
        font-size: 14px;
    }
}

@media (max-width: 480px) {
    .faq-content {
        padding: 20px 15px;
    }
    
    .page-title {
        font-size: 22px;
    }
    
    .faq-section h2 {
        font-size: 18px;
    }
}
</style>

<?php include 'includes/footer.php'; ?>
