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
                <a href="<?php echo $base_url; ?>returns.php">Returns & Refunds</a>
                <span class="separator">|</span>
                <a href="<?php echo $base_url; ?>privacy.php">Privacy Policy</a>
                <span class="separator">|</span>
                <a href="<?php echo $base_url; ?>about.php">About Us</a>
                <span class="separator">|</span>
                <a href="<?php echo $base_url; ?>faq.php">FAQ</a>
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
<!-- Back to Top Button -->
<button onclick="topFunction()" id="backToTopBtn" title="Go to top" aria-label="Scroll to top" style="display: none !important; position: fixed !important; bottom: 30px !important; right: 30px !important; z-index: 99999 !important; background: linear-gradient(135deg, #9ACD32, #7cb342) !important; color: white !important; border: none !important; border-radius: 4px !important; width: 30px !important; height: 30px !important; cursor: pointer !important; box-shadow: 0 4px 12px rgba(154, 205, 50, 0.3) !important; align-items: center !important; justify-content: center !important; font-size: 18px !important; opacity: 0 !important; visibility: hidden !important;">
  <i class="fas fa-chevron-up"></i>
</button>

<script>
// Scroll-to-top functionality
document.addEventListener('DOMContentLoaded', function() {
    const backToTopBtn = document.getElementById('backToTopBtn');
    
    if (!backToTopBtn) return;
    
    // Function to show/hide button based on scroll
    function toggleScrollButton() {
        const scrollTop1 = window.pageYOffset;
        const scrollTop2 = document.documentElement.scrollTop;
        const scrollTop3 = document.body.scrollTop;
        const scrollTop4 = window.scrollY;
        
        const scrollTop = scrollTop1 || scrollTop2 || scrollTop3 || scrollTop4 || 0;
        const showThreshold = 300;
        
        if (scrollTop > showThreshold) {
            backToTopBtn.style.setProperty('display', 'flex', 'important');
            backToTopBtn.style.setProperty('opacity', '1', 'important');
            backToTopBtn.style.setProperty('visibility', 'visible', 'important');
        } else {
            backToTopBtn.style.setProperty('display', 'none', 'important');
            backToTopBtn.style.setProperty('opacity', '0', 'important');
            backToTopBtn.style.setProperty('visibility', 'hidden', 'important');
        }
    }
    
    // Function to scroll to top
    function scrollToTop(e) {
        if (e) {
            e.preventDefault();
            e.stopPropagation();
        }
        
        function smoothScrollToTop() {
            const startPosition = document.body.scrollTop || window.pageYOffset || document.documentElement.scrollTop;
            const targetPosition = 0;
            const distance = targetPosition - startPosition;
            const duration = 1500;
            let start = null;
            
            function animation(currentTime) {
                if (start === null) start = currentTime;
                const timeElapsed = currentTime - start;
                const run = easeInOutCubic(timeElapsed, startPosition, distance, duration);
                
                window.scrollTo(0, run);
                document.body.scrollTop = run;
                document.documentElement.scrollTop = run;
                
                if (timeElapsed < duration) requestAnimationFrame(animation);
            }
            
            function easeInOutCubic(t, b, c, d) {
                t /= d / 2;
                if (t < 1) return c / 2 * t * t * t + b;
                t -= 2;
                return c / 2 * (t * t * t + 2) + b;
            }
            
            requestAnimationFrame(animation);
        }
        
        if ('scrollBehavior' in document.documentElement.style) {
            smoothScrollToTop();
        } else {
            const currentScrollPos = document.body.scrollTop || window.scrollY;
            const scrollStep = -currentScrollPos / (1500 / 16);
            const scrollInterval = setInterval(function() {
                const currentPos = document.body.scrollTop || window.scrollY;
                if (currentPos > 0) {
                    window.scrollBy(0, scrollStep);
                    document.body.scrollTop += scrollStep;
                } else {
                    clearInterval(scrollInterval);
                }
            }, 16);
        }
    }
    
    // Add event listeners
    window.addEventListener('scroll', function() {
        requestAnimationFrame(toggleScrollButton);
    }, { passive: false });
    
    document.addEventListener('scroll', function() {
        requestAnimationFrame(toggleScrollButton);
    }, { passive: false });
    
    document.body.addEventListener('scroll', function() {
        requestAnimationFrame(toggleScrollButton);
    }, { passive: false });
    
    window.addEventListener('wheel', function() {
        setTimeout(toggleScrollButton, 10);
    }, { passive: false });
    
    // Remove any existing event listeners and add a clean one
    backToTopBtn.removeEventListener('click', scrollToTop);
    backToTopBtn.addEventListener('click', scrollToTop);
    
    // Mobile positioning function for backToTopBtn
    function updateBackToTopPosition() {
        if (window.innerWidth <= 480) {
            // Extra small mobile
            backToTopBtn.style.setProperty('left', '15px', 'important');
            backToTopBtn.style.setProperty('right', 'auto', 'important');
            backToTopBtn.style.setProperty('bottom', '15px', 'important');
            backToTopBtn.style.setProperty('width', '40px', 'important');
            backToTopBtn.style.setProperty('height', '40px', 'important');
            backToTopBtn.style.setProperty('font-size', '14px', 'important');
            backToTopBtn.style.setProperty('padding', '10px', 'important');
        } else if (window.innerWidth <= 768) {
            // Mobile/tablet
            backToTopBtn.style.setProperty('left', '20px', 'important');
            backToTopBtn.style.setProperty('right', 'auto', 'important');
            backToTopBtn.style.setProperty('bottom', '20px', 'important');
            backToTopBtn.style.setProperty('width', '45px', 'important');
            backToTopBtn.style.setProperty('height', '45px', 'important');
            backToTopBtn.style.setProperty('font-size', '16px', 'important');
            backToTopBtn.style.setProperty('padding', '12px', 'important');
        } else {
            // Desktop
            backToTopBtn.style.setProperty('left', 'auto', 'important');
            backToTopBtn.style.setProperty('right', '30px', 'important');
            backToTopBtn.style.setProperty('bottom', '30px', 'important');
            backToTopBtn.style.setProperty('width', '50px', 'important');
            backToTopBtn.style.setProperty('height', '50px', 'important');
            backToTopBtn.style.setProperty('font-size', '18px', 'important');
            backToTopBtn.style.setProperty('padding', '15px', 'important');
        }
    }
    
    // Update position on load and resize
    updateBackToTopPosition();
    window.addEventListener('resize', updateBackToTopPosition);
    
    // Also update when scroll event triggers
    window.addEventListener('scroll', function() {
        setTimeout(updateBackToTopPosition, 10);
    });
    
    // Initialize button state
    toggleScrollButton();
});

// Global function for onclick attribute
function topFunction(e) {
    if (e) {
        e.preventDefault();
        e.stopPropagation();
    }
    
    function smoothScrollToTop() {
        const startPosition = document.body.scrollTop || window.pageYOffset || document.documentElement.scrollTop;
        const targetPosition = 0;
        const distance = targetPosition - startPosition;
        const duration = 1500;
        let start = null;
        
        function animation(currentTime) {
            if (start === null) start = currentTime;
            const timeElapsed = currentTime - start;
            const run = easeInOutCubic(timeElapsed, startPosition, distance, duration);
            
            window.scrollTo(0, run);
            document.body.scrollTop = run;
            document.documentElement.scrollTop = run;
            
            if (timeElapsed < duration) requestAnimationFrame(animation);
        }
        
        function easeInOutCubic(t, b, c, d) {
            t /= d / 2;
            if (t < 1) return c / 2 * t * t * t + b;
            t -= 2;
            return c / 2 * (t * t * t + 2) + b;
        }
        
        requestAnimationFrame(animation);
    }
    
    try {
        smoothScrollToTop();
    } catch (error) {
        window.scrollTo(0, 0);
        document.body.scrollTop = 0;
        document.documentElement.scrollTop = 0;
    }
}
</script>

<script>
// Global Quantity Control Logic
document.addEventListener('click', function(e) {
    if (e.target.matches('.btn-qty-minus, .btn-qty-plus') || e.target.closest('.btn-qty-minus, .btn-qty-plus')) {
        const btn = e.target.matches('.btn-qty-minus, .btn-qty-plus') ? e.target : e.target.closest('.btn-qty-minus, .btn-qty-plus');
        const container = btn.closest('.quantity-control');
        if (!container) return;
        
        const input = container.querySelector('.quantity-input');
        if (!input) return;
        
        let value = parseInt(input.value) || 1;
        let min = parseInt(input.getAttribute('min')) || 1;
        let max = parseInt(input.getAttribute('max')) || 99;
        
        if (btn.classList.contains('btn-qty-minus')) {
            if (value > min) {
                input.value = value - 1;
            }
        } else {
            if (value < max) {
                input.value = value + 1;
            }
        }
        
        // Trigger change event for any other listeners
        input.dispatchEvent(new Event('change', { bubbles: true }));
    }
});

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