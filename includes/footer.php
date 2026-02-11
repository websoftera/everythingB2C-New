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
            <div class="footer-links-inline">
                <a href="returns.php">Returns & Refunds</a>
                <span class="separator">|</span>
                <a href="privacy.php">Privacy Policy</a>
                <span class="separator">|</span>
                <a href="about.php">About Us</a>
                <span class="separator">|</span>
                <a href="faq.php">FAQ</a>
            </div>
        </div>
        <div class="footer-column social">
            <h3>Connect With Us</h3>
            <div class="footer-connect-inline">
                <div class="social-icons">
                    <a href="#"><i class="fab fa-facebook"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                    <a href="#"><i class="fab fa-linkedin"></i></a>
                </div>
                <span class="separator">|</span>
                <span class="contact-info-inline">
                    <i class="fas fa-phone"></i> <a href="tel:+918780406230">+91 878 040 6230</a>
                </span>
                <span class="separator">|</span>
                <span class="contact-info-inline">
                    <i class="fas fa-envelope footer-email"></i> <a href="mailto:info@everythingb2c.in">info@everythingb2c.in</a>
                </span>
            </div>
        </div>
    </div>
    <div class="footer-bottom">
        <p>Â© 2025 EverythingB2C - All Rights Reserved. <a href="https://www.websoftera.com" target="_blank">Websoftera</a></p>
    </div>
</footer>

</body>
</html>
