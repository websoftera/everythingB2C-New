<?php
require_once 'config/database.php';
require_once 'includes/functions.php';
require_once 'includes/footer_pages.php';

if (renderManagedFooterPageForLegacyPath($pdo, 'about.php')) {
    exit;
}

// Include header
include 'includes/header.php';

// Breadcrumb Navigation
$breadcrumbs = generateBreadcrumb('About Us');
echo renderBreadcrumb($breadcrumbs);
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-lg-10 mx-auto">
            <div class="about-us-content">
                <h1 class="page-title">About everythingb2c</h1>
                
                <div class="about-section">
                    <p class="intro-text">
                        Welcome to everythingb2c.in - your one-stop destination for all your business and personal needs. We are committed to providing high-quality products at competitive prices with exceptional customer service.
                    </p>
                </div>

                <div class="about-section">
                    <h2>Our Story</h2>
                    <p>
                        <strong>EverythingB2C is a local eCommerce marketplace created to connect local businesses with local customers.</strong> We are starting our journey in <strong>Vadodara</strong> with a simple objective: to help local businesses sell online while making it easier for customers to discover and purchase products from businesses around them.
                    </p>
                    <p>
                        At the same time, EverythingB2C helps customers get products at <strong>competitive prices</strong> by allowing them to compare prices from multiple local sellers. Since our operations are managed locally, sellers can benefit from lower platform fees, reduced packaging costs, and optimized logistics. This creates a <strong>win-win situation for both sellers and buyers.</strong>
                    </p>
                    <p>
                        <strong>EverythingB2C provides the marketplace, technology, and pickup &amp; delivery support</strong>, allowing local sellers to focus on their products and customers while we take care of order fulfilment and delivery.
                    </p>
                    <p><strong>Local Sellers. Local Buyers. Better Savings. Faster Delivery.</strong></p>
                    <p><strong>An eCommerce startup founded by technocrats and IIM Calcutta alumni.</strong></p>
                </div>

                <div class="about-section">
                    <h2>Our Mission</h2>
                    <p>
                        To provide businesses and consumers with access to a comprehensive range of high-quality products at competitive prices, delivered with exceptional service and reliability.
                    </p>
                </div>

                <div class="about-section">
                    <h2>Our Vision</h2>
                    <p>
                        To become India's leading B2C platform, known for quality, reliability, and customer satisfaction, while continuously expanding our product range and improving our services.
                    </p>
                </div>

                <div class="about-section">
                    <h2>What We Offer</h2>
                    <div class="features-grid">
                        <div class="feature-item">
                            <h3>Wide Product Range</h3>
                            <p>From office stationery to personal care products, cleaning supplies to home essentials - we have everything you need under one roof.</p>
                        </div>
                        <div class="feature-item">
                            <h3>Quality Assurance</h3>
                            <p>Every product in our catalog is carefully selected and tested to ensure it meets our high quality standards.</p>
                        </div>
                        <div class="feature-item">
                            <h3>Competitive Pricing</h3>
                            <p>We offer the best prices in the market without compromising on quality, helping you save on your purchases.</p>
                        </div>
                        <div class="feature-item">
                            <h3>Fast Delivery</h3>
                            <p>Quick and reliable delivery across India with real-time tracking and secure packaging.</p>
                        </div>
                        <div class="feature-item">
                            <h3>Customer Support</h3>
                            <p>Our dedicated customer support team is always ready to help you with any queries or concerns.</p>
                        </div>
                        <div class="feature-item">
                            <h3>Secure Shopping</h3>
                            <p>Safe and secure payment options with SSL encryption to protect your personal and financial information.</p>
                        </div>
                    </div>
                </div>

                <div class="about-section">
                    <h2>Our Product Categories</h2>
                    <ul class="category-list">
                        <li><strong>Office Stationery:</strong> A complete range of office supplies for your everyday business needs.</li>
                        <li><strong>School Stationery:</strong> Essential stationery and supplies for students, teachers, and classrooms.</li>
                        <li><strong>Industrial Safety Products:</strong> Protective equipment and safety essentials for workplaces and industry.</li>
                        <li><strong>Packaging Material:</strong> Reliable packaging supplies for storing, protecting, and shipping products.</li>
                        <li><strong>Personal Care:</strong> Everyday personal care products for hygiene and wellness.</li>
                        <li><strong>Home &amp; Garden:</strong> Useful products to care for and improve your home and outdoor spaces.</li>
                    </ul>
                </div>

                <div class="about-section">
                    <h2>Why Choose everythingb2c?</h2>
                    <div class="why-choose-us">
                        <div class="reason-item">
                            <h3>For Buyers</h3>
                            <ul>
                                <li>Shop from local sellers</li>
                                <li>Competitive prices</li>
                                <li>Same-day delivery*</li>
                                <li>Easy returns</li>
                            </ul>
                        </div>
                        <div class="reason-item">
                            <h3>For Sellers</h3>
                            <ul>
                                <li>Reach local customers</li>
                                <li>EverythingB2C pickup</li>
                                <li>Easy online selling</li>
                                <li>Introductory 0% platform fee*</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="about-section">
                    <h2>Contact Information</h2>
                    <div class="contact-details">
                        <p><strong>Email:</strong> <a href="mailto:info@everythingb2c.in">info@everythingb2c.in</a></p>
                        <p><strong>Phone:</strong> <a href="tel:+916355837347">+91 63558 37347</a></p>
                        <p><strong>Business Hours:</strong> Monday to Saturday, 9:00 AM - 9:00 PM</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* About Us Page Styles */
.about-us-content {
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

.about-section {
    margin-bottom: 40px;
}

.about-section h2 {
    color: #2c539f;
    font-size: 24px;
    font-weight: 600;
    margin-bottom: 30px;
    border-left: 4px solid var(--site-blue);
    padding-left: 15px;
    text-align: left;
}

.intro-text {
    font-size: 18px;
    line-height: 1.6;
    color: #555;
    margin-bottom: 30px;
    text-align: center;
    font-style: italic;
}

.about-section p {
    font-size: 15px;
    line-height: 1.7;
    color: #666;
    margin-bottom: 15px;
}

.features-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 25px;
    margin-top: 20px;
}

.feature-item {
    background: #f8f9fa;
    padding: 25px;
    border-radius: 8px;
    border-left: 4px solid var(--site-blue);
    transition: transform 0.2s ease;
}

.feature-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.feature-item h3 {
    color: var(--dark-blue);
    font-size: 18px;
    font-weight: 600;
    margin-bottom: 10px;
}

.feature-item p {
    color: #666;
    font-size: 15px;
    line-height: 1.7;
    margin: 0;
}

.about-us-content .category-list {
    list-style: none;
    padding-left: 0;
}

.about-us-content .category-list li {
    font-size: 15px;
    line-height: 1.7;
    color: #666;
    margin-bottom: 12px;
    padding-left: 25px;
    position: relative;
}

@media (max-width: 767px) {
    .about-us-content .category-list li {
        font-size: 12px; /* adjust as needed */
    }
}

.about-us-content .category-list li::before {
    content: "•";
    color: var(--site-blue);
    font-weight: bold;
    position: absolute;
    left: 0;
    top: 0;
}

.category-list li strong {
    color: #333;
    font-weight: 600;
}

.why-choose-us {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 20px;
    margin-top: 20px;
}

.reason-item {
    background: #fff;
    padding: 20px;
    border-radius: 6px;
    border: 1px solid #e9ecef;
    text-align: center;
}

.reason-item h3 {
    color: var(--dark-blue);
    font-size: 16px;
    font-weight: 600;
    margin-bottom: 10px;
}

.reason-item ul {
    list-style: none;
    padding: 0;
    margin: 0;
    text-align: left;
}

.reason-item li {
    color: #666;
    font-size: 15px;
    line-height: 1.7;
    margin: 0 0 8px;
}

.reason-item li::before {
    content: "✓";
    color: var(--site-blue);
    font-weight: 700;
    margin-right: 9px;
}

.contact-details {
    background: #f8f9fa;
    padding: 25px;
    border-radius: 6px;
    border-left: 4px solid var(--site-blue);
}

.contact-details p {
    margin-bottom: 10px;
    font-size: 15px;
}

.contact-details a {
    color: var(--site-blue);
    text-decoration: none;
    font-weight: 500;
}

.contact-details a:hover {
    color: var(--dark-blue);
    text-decoration: underline;
}

/* Responsive Design */
@media (max-width: 768px) {
    .about-us-content {
        padding: 25px 20px;
        margin: 0 10px 30px 10px;
    }
    
    .page-title {
        font-size: 26px;
    }
    
    .about-section h2 {
        font-size: 15px !important;
    }
    
    .about-section p {
        font-size: 12px !important;
    }
    
    .intro-text {
        font-size: 16px;
    }
    
    .features-grid {
        grid-template-columns: 1fr;
        gap: 20px;
    }
    
    .why-choose-us {
        grid-template-columns: 1fr;
        gap: 15px;
    }
}

@media (max-width: 480px) {
    .about-us-content {
        padding: 20px 15px;
    }
    
    .page-title {
        font-size: 22px;
    }
    
    .about-section h2 {
        font-size: 18px;
    }
}
</style>

<?php include 'includes/footer.php'; ?>
