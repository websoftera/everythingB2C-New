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
                <a href="<?php echo $base_url; ?>about.php">About Us</a>
                <span class="separator">|</span>
                <a href="<?php echo $base_url; ?>returns.php">Returns & Refunds</a>
                <span class="separator">|</span>
                <a href="<?php echo $base_url; ?>privacy.php">Privacy Policy</a>
                <span class="separator">|</span>
                <a href="<?php echo $base_url; ?>faq.php">FAQ</a>
            </div>
        </div>
        <div class="footer-column social">
            <h3>Connect With Us</h3>
            <div class="footer-connect-inline">
                <span class="contact-info-inline">
                    <i class="fas fa-phone"></i> <a href="tel:+918780406230">+91 878 040 6230</a>
                </span>
                <span class="separator">|</span>
                <span class="contact-info-inline">
                    <i class="fas fa-envelope footer-email"></i> <a href="mailto:info@everythingb2c.in">info@everythingb2c.in</a>
                </span>
                <span class="separator">|</span>
                <div class="social-icons">
                    <a href="#"><i class="fab fa-facebook"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                    <a href="#"><i class="fab fa-linkedin"></i></a>
                </div>
            </div>
        </div>
    </div>
    <div class="footer-bottom">
        <p>© 2026 everythingb2c - All Rights Reserved. <a href="https://everythingb2c.in" target="_blank">everythingb2c</a></p>
    </div>
</footer>

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
        if (document.body.classList.contains('variant-drawer-open')) {
            backToTopBtn.style.setProperty('display', 'none', 'important');
            backToTopBtn.style.setProperty('opacity', '0', 'important');
            backToTopBtn.style.setProperty('visibility', 'hidden', 'important');
            return;
        }

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

<style>
.everythingb2c-quantity-limit-overlay {
    position: fixed;
    inset: 0;
    z-index: 30000;
    display: none;
    align-items: center;
    justify-content: center;
    padding: 16px;
    background: rgba(0, 0, 0, 0.48);
    font-family: 'Mulish', Arial, sans-serif;
}

.everythingb2c-quantity-limit-overlay.show {
    display: flex;
}

.everythingb2c-quantity-limit-modal {
    position: relative;
    width: min(390px, calc(100vw - 28px));
    min-height: 245px;
    border-radius: 8px;
    background: #fff;
    color: #3a3a3a;
    padding: 48px 28px 28px;
    text-align: center;
    box-shadow: 0 22px 60px rgba(0, 0, 0, 0.26);
}

.everythingb2c-quantity-limit-close {
    position: absolute;
    top: 10px;
    right: 14px;
    width: 24px;
    height: 24px;
    border: 0;
    background: transparent;
    color: #444;
    font-size: 28px;
    line-height: 22px;
    font-weight: 300;
    cursor: pointer;
}

.everythingb2c-quantity-limit-logo-wrap {
    display: flex;
    justify-content: center;
    align-items: center;
    margin-bottom: 30px;
}

.everythingb2c-quantity-limit-logo {
    max-width: 175px;
    max-height: 54px;
    object-fit: contain;
}

.everythingb2c-quantity-limit-title {
    margin: 0 0 12px;
    color: #3a3a3a;
    font-size: 21px;
    font-weight: 800;
    line-height: 1.2;
    letter-spacing: 0;
}

.everythingb2c-quantity-limit-message {
    margin: 0 auto;
    max-width: 315px;
    color: #6c6c6c;
    font-size: 14px;
    font-weight: 500;
    line-height: 1.35;
}

.quantity-control .btn-qty:disabled,
.quantity-control .btn-qty[aria-disabled="true"] {
    background: transparent !important;
    color: #333 !important;
    cursor: pointer !important;
    opacity: 1 !important;
}

@media (max-width: 480px) {
    .everythingb2c-quantity-limit-modal {
        min-height: 225px;
        padding: 44px 20px 26px;
    }

    .everythingb2c-quantity-limit-logo-wrap {
        margin-bottom: 26px;
    }

    .everythingb2c-quantity-limit-logo {
        max-width: 160px;
    }

    .everythingb2c-quantity-limit-title {
        font-size: 19px;
    }

    .everythingb2c-quantity-limit-message {
        font-size: 13px;
    }
}
</style>

<script>
// Global Quantity Control Logic
function getEverythingB2CLogoSrc() {
    return (window.BASE_URL || '') + 'asset/images/logo.webp';
}

function showEverythingB2CMaxQuantityPopup(message, title = 'Maximum quantity reached') {
    let overlay = document.getElementById('everythingb2cQuantityLimitOverlay');
    if (!overlay) {
        overlay = document.createElement('div');
        overlay.id = 'everythingb2cQuantityLimitOverlay';
        overlay.className = 'everythingb2c-quantity-limit-overlay';
        overlay.innerHTML = `
            <div class="everythingb2c-quantity-limit-modal" role="dialog" aria-modal="true" aria-labelledby="everythingb2cQuantityLimitTitle">
                <button type="button" class="everythingb2c-quantity-limit-close" aria-label="Close">&times;</button>
                <div class="everythingb2c-quantity-limit-logo-wrap">
                    <img class="everythingb2c-quantity-limit-logo" src="${getEverythingB2CLogoSrc()}" alt="everythingB2C">
                </div>
                <h2 id="everythingb2cQuantityLimitTitle" class="everythingb2c-quantity-limit-title"></h2>
                <p class="everythingb2c-quantity-limit-message"></p>
            </div>
        `;
        document.body.appendChild(overlay);

        overlay.querySelector('.everythingb2c-quantity-limit-close').addEventListener('click', function() {
            overlay.classList.remove('show');
        });
        overlay.addEventListener('click', function(e) {
            if (e.target === overlay) {
                overlay.classList.remove('show');
            }
        });
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                overlay.classList.remove('show');
            }
        });
    }

    overlay.querySelector('.everythingb2c-quantity-limit-title').textContent = title;
    overlay.querySelector('.everythingb2c-quantity-limit-message').textContent = message || 'Maximum quantity allowed for this product has been reached.';
    overlay.classList.add('show');
}

function isValidPackageQuantity(quantity, packageQuantity) {
    packageQuantity = Math.max(1, parseInt(packageQuantity, 10) || 1);
    quantity = parseInt(quantity, 10) || 0;
    return quantity > 0 && quantity % packageQuantity === 0;
}

function roundToNearestPackage(quantity, packageQuantity) {
    packageQuantity = Math.max(1, parseInt(packageQuantity, 10) || 1);
    quantity = parseInt(quantity, 10) || 0;
    return Math.floor(quantity / packageQuantity) * packageQuantity;
}

function normalizeQuantityInputValue(input) {
    if (!input) return 1;
    const packageQuantity = Math.max(1, parseInt(input.dataset.packageQuantity || input.getAttribute('step'), 10) || 1);
    const min = Math.max(packageQuantity, parseInt(input.getAttribute('min'), 10) || packageQuantity);
    const max = parseInt(input.getAttribute('max'), 10) || 99;
    let value = parseInt(input.value, 10) || min;
    value = Math.max(min, value);
    value = Math.min(max, value);
    value = roundToNearestPackage(value, packageQuantity);
    if (value < min) value = min;
    if (value > max) value = roundToNearestPackage(max, packageQuantity);
    if (value < min) value = min;
    input.value = value;
    input.dataset.packageQuantity = packageQuantity;
    input.setAttribute('step', packageQuantity);
    input.setAttribute('min', min);
    const control = input.closest('.quantity-control');
    if (control) {
        const minusButton = control.querySelector('.btn-qty-minus');
        const plusButton = control.querySelector('.btn-qty-plus');
        if (minusButton) {
            minusButton.disabled = false;
            minusButton.setAttribute('aria-disabled', value <= min ? 'true' : 'false');
        }
        if (plusButton) {
            plusButton.disabled = false;
            plusButton.setAttribute('aria-disabled', value >= max || max < min ? 'true' : 'false');
        }
    }
    return value;
}

document.addEventListener('click', function(e) {
    if (e.target.matches('.btn-qty-minus, .btn-qty-plus') || e.target.closest('.btn-qty-minus, .btn-qty-plus')) {
        const btn = e.target.matches('.btn-qty-minus, .btn-qty-plus') ? e.target : e.target.closest('.btn-qty-minus, .btn-qty-plus');
        const container = btn.closest('.quantity-control');
        if (!container) return;
        
        const input = container.querySelector('.quantity-input');
        if (!input) return;
        
        let value = normalizeQuantityInputValue(input);
        let min = parseInt(input.getAttribute('min')) || 1;
        let max = parseInt(input.getAttribute('max')) || 99;
        let step = Math.max(1, parseInt(input.dataset.packageQuantity || input.getAttribute('step'), 10) || 1);

        if (btn.classList.contains('btn-qty-minus')) {
            if (value > min) {
                input.value = Math.max(min, value - step);
            }
        } else {
            if (value < max) {
                input.value = Math.min(max, value + step);
            } else if (typeof showEverythingB2CMaxQuantityPopup === 'function') {
                showEverythingB2CMaxQuantityPopup('Maximum quantity allowed for this product is ' + max);
            }
        }
        normalizeQuantityInputValue(input);

        // Trigger change event for any other listeners
        input.dispatchEvent(new Event('change', { bubbles: true }));
    }
});

document.addEventListener('change', function(e) {
    if (e.target && e.target.matches('.quantity-input, .shop-page-quantity-input, input[name="quantity"], .product-quantity-input')) {
        normalizeQuantityInputValue(e.target);
    }
});

document.addEventListener('blur', function(e) {
    if (e.target && e.target.matches('.quantity-input, .shop-page-quantity-input, input[name="quantity"], .product-quantity-input')) {
        normalizeQuantityInputValue(e.target);
    }
}, true);

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
