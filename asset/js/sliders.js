/**
 * Global Slider Functionality for EverythingB2C
 * Handles Category, Subcategory, and Product Sliders
 */
document.addEventListener('DOMContentLoaded', function () {
    // 1. Category/Subcategory Slider
    // Works for both Home page (#slider) and Category page (#category-slider)
    const categorySliders = document.querySelectorAll('.categories-container');

    categorySliders.forEach(container => {
        const wrapper = container.closest('.categories-slider-wrapper');
        if (!wrapper) return;

        const prevBtn = wrapper.querySelector('.prev-btn');
        const nextBtn = wrapper.querySelector('.next-btn');

        if (prevBtn && nextBtn) {
            const scrollAmount = 250;

            prevBtn.addEventListener('click', function (e) {
                e.preventDefault();
                container.scrollBy({
                    left: -scrollAmount,
                    behavior: 'smooth'
                });
            });

            nextBtn.addEventListener('click', function (e) {
                e.preventDefault();
                container.scrollBy({
                    left: scrollAmount,
                    behavior: 'smooth'
                });
            });
        }
    });

    // 2. Product Sliders (Discounted & Featured)
    const productSliders = [
        { containerId: 'discounted-slider', prevClass: '.discounted-nav-btn.prev-btn', nextClass: '.discounted-nav-btn.next-btn' },
        { containerId: 'featured-slider', prevClass: '.featured-nav-btn.prev-btn', nextClass: '.featured-nav-btn.next-btn' }
    ];

    productSliders.forEach(slider => {
        const container = document.getElementById(slider.containerId);
        const prevBtn = document.querySelector(slider.prevClass);
        const nextBtn = document.querySelector(slider.nextClass);

        if (container && prevBtn && nextBtn) {
            const getScrollAmount = () => {
                const width = window.innerWidth;
                if (width <= 575) return 196;
                if (width <= 767) return 216;
                return 236;
            };

            prevBtn.addEventListener('click', function (e) {
                e.preventDefault();
                container.scrollLeft -= getScrollAmount();
            });

            nextBtn.addEventListener('click', function (e) {
                e.preventDefault();
                container.scrollLeft += getScrollAmount();
            });
        }
    });
});
