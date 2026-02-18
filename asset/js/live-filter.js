/**
 * Live Filter Script
 * Handles real-time search and filtering without page reloads.
 */

document.addEventListener('DOMContentLoaded', function () {
    const filterForm = document.getElementById('sidebarFilterForm');
    const productsContainer = document.querySelector('.products-container'); // The wrapper for grid + header + pagination
    const productsGrid = document.querySelector('.products-grid');
    const resultsCount = document.querySelector('.results-count');
    const paginationNav = document.querySelector('nav[aria-label="Product pagination"]'); // Select pagination if it exists

    // Select inputs
    const searchInput = filterForm.querySelector('input[name="q"]');
    const categorySelect = filterForm.querySelector('select[name="category"]');
    const minPriceInput = document.getElementById('minPriceInput');
    const maxPriceInput = document.getElementById('maxPriceInput');
    const minPriceSlider = document.getElementById('minPriceSlider'); // If uncommented later
    const maxPriceSlider = document.getElementById('maxPriceSlider'); // If uncommented later

    let debounceTimer;
    let isFetching = false;

    // Helper: Debounce function
    function debounce(func, delay) {
        return function (...args) {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => func.apply(this, args), delay);
        };
    }

    // Main fetch function
    function fetchResults(url) {
        if (isFetching) return;
        isFetching = true;

        // Add loading state
        if (productsGrid) productsGrid.style.opacity = '0.5';

        fetch(url)
            .then(response => response.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');

                // Update Products Grid
                const newProductsGrid = doc.querySelector('.products-grid');
                if (productsGrid && newProductsGrid) {
                    productsGrid.innerHTML = newProductsGrid.innerHTML;
                    // Copy classes if they changed (e.g., responsive columns)
                    productsGrid.className = newProductsGrid.className;
                } else if (productsGrid && !newProductsGrid) {
                    // Handle case where no products found might return different structure? 
                    // Usually it returns a 'no-products' div inside grid or instead of grid.
                    // Let's check container.
                    const newContainer = doc.querySelector('.products-container');
                    if (newContainer) productsContainer.innerHTML = newContainer.innerHTML;
                }

                // Update Result Count
                const newResultsCount = doc.querySelector('.results-count');
                if (resultsCount && newResultsCount) {
                    resultsCount.innerHTML = newResultsCount.innerHTML;
                }

                // Update Pagination
                // We need to adhere to the DOM structure. 
                // pagination is usually inside .products-container but after .products-grid
                // Let's rely on replacing the whole products-container content? 
                // Replacing whole container is safer but might break Sidebar if sidebar is inside?
                // Sidebar is outside .products-container (col-lg-3 vs col-lg-9). All good.

                const newProductsContainer = doc.querySelector('.products-container');
                if (productsContainer && newProductsContainer) {
                    productsContainer.innerHTML = newProductsContainer.innerHTML;

                    // Re-bind popup.js or any content listeners if needed
                    // For now, product cards rely on simple links or inline JS (which we shouldn't use but is there)
                    // If 'popup.js' is external, it delegates events usually, or needs re-init.
                    // Assuming existing 'add-to-cart' logic is handled via delegation or simple hrefs for now.
                    // Verification step will catch if "Add to Cart" breaks.
                }

                // Update Browser History
                window.history.pushState({}, '', url);

                // Scroll to top of results if needed
                // productsContainer.scrollIntoView({ behavior: 'smooth' });

            })
            .catch(err => console.error('Filter fetch error:', err))
            .finally(() => {
                isFetching = false;
                if (productsGrid) productsGrid.style.opacity = '1';
                // Remove loading state if we added a spinner
            });
    }

    // Build URL from form data
    function getFilterUrl() {
        const formData = new FormData(filterForm);
        const params = new URLSearchParams();

        // Preserve existing URL params that might be hidden or not in form (like 'slug' which IS in form)
        // But FormData gets them from the form inputs.

        for (const [key, value] of formData.entries()) {
            // Only add parameter if it has a value, OR if it's 'category' which might be empty string for "All"
            // But usually empty string in URL means "All" or "No filter".
            // Backend handles empty category as 'All'.
            if (value !== '' || key === 'category') {
                params.append(key, value);
            }
        }

        return window.location.pathname + '?' + params.toString();
    }

    // Event Listeners

    // 1. Search Input (Debounced)
    if (searchInput) {
        searchInput.addEventListener('input', debounce(function () {
            const url = getFilterUrl();
            fetchResults(url);
        }, 500)); // 500ms delay

        searchInput.addEventListener('keypress', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault(); // Prevent form submit
                const url = getFilterUrl();
                fetchResults(url);
            }
        });
    }

    // 2. Category Select (Immediate)
    if (categorySelect) {
        categorySelect.addEventListener('change', function () {
            const url = getFilterUrl();
            fetchResults(url);
        });
    }

    // 3. Price Inputs (Debounced)
    // Only if they exist and are uncommented
    const handlePriceChange = debounce(function () {
        const url = getFilterUrl();
        fetchResults(url);
    }, 500);

    if (minPriceInput) minPriceInput.addEventListener('input', handlePriceChange);
    if (maxPriceInput) maxPriceInput.addEventListener('input', handlePriceChange);
    // Range sliders trigger 'input' continuously and 'change' on release
    if (minPriceSlider) minPriceSlider.addEventListener('change', handlePriceChange);
    if (maxPriceSlider) maxPriceSlider.addEventListener('change', handlePriceChange);

    // 4. Pagination Links (Delegation)
    // Because pagination is replaced via AJAX, we need delegation on the container
    if (productsContainer) {
        productsContainer.addEventListener('click', function (e) {
            const link = e.target.closest('.page-link');
            if (link) {
                e.preventDefault();
                const url = link.getAttribute('href');
                if (url && url !== '#') {
                    fetchResults(url);
                }
            }
        });
    }

    // Popstate (Browser Back/Forward)
    window.addEventListener('popstate', function () {
        // Just reload for simplicity to ensure correct state, 
        // OR fetch the current location.href
        fetchResults(window.location.href);
    });

});
