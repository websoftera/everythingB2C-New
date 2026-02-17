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
        <p>© 2025 EverythingB2C - All Rights Reserved. <a href="https://www.websoftera.com" target="_blank">Websoftera</a></p>
    </div>
</footer>

<style>
/* Desktop Footer Inline Layout */
@media (min-width: 1025px) {
  .footer-links-inline {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 8px;
  }
  
  .footer-links-inline a {
    color: #ccc;
    text-decoration: none;
    font-size: 14px;
    transition: color 0.2s ease;
  }
  
  .footer-links-inline a:hover {
    color: white;
    text-decoration: underline;
  }
  
  .footer-connect-inline {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 8px;
  }
  
  .footer-connect-inline .social-icons {
    display: flex;
    align-items: center;
    gap: 8px;
  }
  
  .footer-connect-inline .social-icons a {
    color: #ccc;
    text-decoration: none;
    font-size: 16px;
    transition: color 0.2s ease;
  }
  
  .footer-connect-inline .social-icons a:hover {
    color: white;
  }
  
  .footer-connect-inline .contact-info-inline {
    color: #ccc;
    font-size: 14px;
  }
  
  .footer-connect-inline .contact-info-inline a {
    color: #ccc;
    text-decoration: none;
    transition: color 0.3s ease;
  }
  
  .footer-connect-inline .contact-info-inline a:hover {
    color: #ffffff;
    text-decoration: underline;
  }
  
  .separator {
    color: #666;
    font-size: 14px;
    margin: 0 4px;
  }
}

/* Mobile Layout - Keep original stacked layout */
@media (max-width: 1024px) {
  .footer-links-inline {
    display: block;
  }
  
  .footer-links-inline a {
    display: block;
    color: #ccc;
    text-decoration: none;
    font-size: 14px;
    margin-bottom: 8px;
    transition: color 0.2s ease;
  }
  
  .footer-links-inline a:hover {
    color: white;
    text-decoration: underline;
  }
  
  .footer-links-inline .separator {
    display: none;
  }
  
  .footer-connect-inline {
    display: block;
  }
  
  .footer-connect-inline .separator {
    display: none;
  }
  
  .footer-connect-inline .contact-info-inline {
    display: block;
    color: #ccc;
    font-size: 14px;
    margin-bottom: 8px;
  }
  
  .footer-connect-inline .contact-info-inline a {
    color: #ccc;
    text-decoration: none;
    transition: color 0.3s ease;
  }
  
  .footer-connect-inline .contact-info-inline a:hover {
    color: #ffffff;
    text-decoration: underline;
  }
  
  .footer-connect-inline .social-icons {
    display: flex;
    gap: 12px;
    margin-bottom: 15px;
  }
  
  .footer-connect-inline .social-icons a {
    color: #ccc;
    font-size: 16px;
    transition: color 0.2s ease;
  }
  
  .footer-connect-inline .social-icons a:hover {
    color: white;
  }
}
</style>

<script>
// Global product image fallback: swap in a sample image if any product img fails to load
(function() {
  const fallbackSrc = 'https://via.placeholder.com/240x155?text=Product';
  document.addEventListener('error', function (evt) {
    const el = evt.target;
    if (el && el.tagName === 'IMG' && !el.dataset.fallbackApplied) {
      el.dataset.fallbackApplied = '1';
      el.src = fallbackSrc;
    }
  }, true);
})();
</script>

<!-- Bootstrap Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 