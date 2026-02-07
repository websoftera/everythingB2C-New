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
        updateBackToTopPosition();
    });
});
</script>
