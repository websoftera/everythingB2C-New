<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

// Include header
$pageCss = ['asset/style/about.css'];
include 'includes/header.php';

// Breadcrumb Navigation
$breadcrumbs = generateBreadcrumb('About Us');
echo renderBreadcrumb($breadcrumbs);
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-lg-10 mx-auto">
            <div class="about-us-content">
                <h1 class="page-title">About EverythingB2C</h1>
                
                <div class="about-section">
                    <p class="intro-text">
                        Welcome to EverythingB2C.in - your one-stop destination for all your business and personal needs. We are committed to providing high-quality products at competitive prices with exceptional customer service.
                    </p>
                </div>

                <div class="about-section">
                    <h2>Our Story</h2>
                    <p>
                        EverythingB2C.in was founded with a simple yet powerful vision: to make quality products accessible to everyone. We understand that businesses and individuals need reliable suppliers who can deliver consistent quality, competitive pricing, and excellent service.
                    </p>
                    <p>
                        Starting as a small venture, we have grown into a trusted name in the B2C marketplace, serving thousands of satisfied customers across India. Our journey has been marked by continuous innovation, customer-centric approach, and unwavering commitment to quality.
                    </p>
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
                        <li><strong>Office Stationery:</strong> Complete range of office supplies for all your business needs</li>
                        <li><strong>Personal Care:</strong> Quality personal care products for daily hygiene and wellness</li>
                        <li><strong>Cleaning & Household:</strong> Effective cleaning solutions and household essentials</li>
                        <li><strong>Diapers & Wipes:</strong> Safe and comfortable baby care products</li>
                        <li><strong>Home & Garden:</strong> Products to beautify and maintain your living spaces</li>
                        <li><strong>Kitchen Essentials:</strong> Everything you need for your kitchen and cooking needs</li>
                    </ul>
                </div>

                <div class="about-section">
                    <h2>Why Choose EverythingB2C?</h2>
                    <div class="why-choose-us">
                        <div class="reason-item">
                            <h3>Trusted by Thousands</h3>
                            <p>Join thousands of satisfied customers who trust us for their regular purchases.</p>
                        </div>
                        <div class="reason-item">
                            <h3>Quality Guarantee</h3>
                            <p>We stand behind every product we sell with our quality guarantee and return policy.</p>
                        </div>
                        <div class="reason-item">
                            <h3>Easy Returns</h3>
                            <p>Hassle-free return policy within 7 days if you're not completely satisfied.</p>
                        </div>
                        <div class="reason-item">
                            <h3>Regular Updates</h3>
                            <p>We continuously add new products and categories to serve your evolving needs.</p>
                        </div>
                    </div>
                </div>

                <div class="about-section">
                    <h2>Contact Information</h2>
                    <div class="contact-details">
                        <p><strong>Email:</strong> <a href="mailto:info@everythingb2c.in">info@everythingb2c.in</a></p>
                        <p><strong>Phone:</strong> <a href="tel:+918780406230">+91 878 040 6230</a></p>
                        <p><strong>Business Hours:</strong> Monday to Saturday, 9:00 AM - 6:00 PM</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<?php include 'includes/back_to_top_button.php'; ?>
<?php include 'includes/footer.php'; ?>
