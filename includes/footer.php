<!-- Footer -->
<footer class="desktop-footer">
    <div class="footer-background">
        <div class="footer-overlay"></div>
    </div>
    <div class="footer-container">
        <!-- <div class="footer-column">
            <h3>Quick Links</h3>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="products.php">All Products</a></li>
                <li><a href="about.php">About Us</a></li>
                <li><a href="contact.php">Contact</a></li>
                <li><a href="privacy.php">Privacy Policy</a></li>
            </ul>
        </div> -->
        <!-- <div class="footer-column">
            <h3>Categories</h3>
            <ul>
                <?php 
                $footerCategories = getAllCategories();
                foreach (array_slice($footerCategories, 0, 5) as $category): 
                ?>
                    <li><a href="category.php?slug=<?php echo $category['slug']; ?>"><?php echo $category['name']; ?></a></li>
                <?php endforeach; ?>
            </ul>
        </div> -->
        <div class="footer-column">
            <h3>Quick Links</h3>    
            <div class="footer-links-grid">
                <div class="footer-links-column">
                    <ul>
                        <li><a href="returns.php">Returns & Exchanges</a></li>
                        <li><a href="shipping.php">Privacy Policy</a></li>
                    </ul>
                </div>
                <div class="footer-links-column">
                    <ul>
                        <li><a href="about.php">About Us</a></li>
                        <li><a href="faq.php">FAQ</a></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="footer-column social">
            <h3>Connect With Us</h3>
            <div class="footer-connect-grid">
                <div class="footer-social-column">
                    <div class="social-icons">
                        <a href="#"><i class="fab fa-facebook"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-linkedin"></i></a>
                    </div>
                </div>
                <div class="footer-contact-column">
                    <div class="contact-info">
                        <p><i class="fas fa-phone"></i> <a href="tel:+918780406230">+91 878 040 6230</a></p>
                        <p><i class="fas fa-envelope footer-email"></i> <a href="mailto:info@everythingb2c.in">info@everythingb2c.in</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="footer-bottom">
        <p>© 2025 EverythingB2C - All Rights Reserved. <a href="https://www.websoftera.com" target="_blank">Websoftera</a></p>
    </div>
</footer>
</body>
</html> 