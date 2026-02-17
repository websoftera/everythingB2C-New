
document.addEventListener('DOMContentLoaded', function () {
    const mainNavbar = document.querySelector('.navbar.sticky-top');
    if (mainNavbar) {
        const setNavbarHeight = () => {
            const navbarHeight = mainNavbar.offsetHeight;
            document.body.style.setProperty('--main-navbar-height', `${navbarHeight}px`);
        };

        // Set height on load
        setNavbarHeight();

        // Update height on window resize
        window.addEventListener('resize', setNavbarHeight);

        // Use a MutationObserver to detect changes that might affect height
        const observer = new MutationObserver(setNavbarHeight);
        observer.observe(mainNavbar, {
            childList: true,
            subtree: true,
            attributes: true
        });
    }
});
