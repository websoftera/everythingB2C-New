<!-- Footer -->
<footer class="desktop-footer">
    <div class="footer-background">
        <div class="footer-overlay"></div>
    </div>
    <div class="footer-container">
        <div class="footer-column">
            <h3>Quick Links</h3>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="products.php">All Products</a></li>
                <li><a href="about.php">About Us</a></li>
                <li><a href="contact.php">Contact</a></li>
            </ul>
        </div>
        <div class="footer-column">
            <h3>Categories</h3>
            <ul>
                <?php 
                $footerCategories = getAllCategories();
                foreach (array_slice($footerCategories, 0, 5) as $category): 
                ?>
                    <li><a href="category.php?slug=<?php echo $category['slug']; ?>"><?php echo $category['name']; ?></a></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <div class="footer-column">
            <h3>Customer Service</h3>
            <ul>
                <li><a href="Customer-Support.html">Customer Support</a></li>
                <li><a href="shipping.php">Shipping Info</a></li>
                <li><a href="returns.php">Returns & Exchanges</a></li>
                <li><a href="faq.php">FAQ</a></li>
            </ul>
        </div>
        <div class="footer-column social">
            <h3>Connect With Us</h3>
            <div class="icons">
                <a href="#"><i class="fab fa-facebook"></i></a>
                <a href="#"><i class="fab fa-twitter"></i></a>
                <a href="#"><i class="fab fa-instagram"></i></a>
                <a href="#"><i class="fab fa-linkedin"></i></a>
            </div>
            <div class="contact-info mt-3">
                <p><i class="fas fa-phone"></i> +91 1234567890</p>
                <p><i class="fas fa-envelope footer-email"></i> info@EverythingB2C.com</p>
            </div>
        </div>
    </div>
    <div class="footer-bottom">
        <p>&copy; 2024 EverythingB2C. All rights reserved. | 
        <a href="privacy.php">Privacy Policy</a> | 
        <a href="terms.php">Terms of Service</a></p>
    </div>
</footer>
</body>
</html> 