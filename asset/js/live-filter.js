document.addEventListener('DOMContentLoaded', function () {
    const filterForms = document.querySelectorAll('.sidebar-filter-form, #mobileFilterForm');
    const productsContainer = document.querySelector('.products-container'); // The wrapper for grid + header + pagination
    const productsGrid = document.querySelector('.products-grid');
    const resultsCount = document.querySelector('.results-count');
    const paginationNav = document.querySelector('nav[aria-label="Product pagination"]'); // Select pagination if it exists

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

        if (productsGrid) productsGrid.style.opacity = '0.5';

        fetch(url)
            .then(response => response.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');

                const newProductsGrid = doc.querySelector('.products-grid');
                if (productsGrid && newProductsGrid) {
                    productsGrid.innerHTML = newProductsGrid.innerHTML;
                    productsGrid.className = newProductsGrid.className;
                } else if (productsGrid && !newProductsGrid) {
                    const newContainer = doc.querySelector('.products-container');
                    if (newContainer && productsContainer) productsContainer.innerHTML = newContainer.innerHTML;
                }

                const newResultsCount = doc.querySelector('.results-count');
                if (resultsCount && newResultsCount) {
                    resultsCount.innerHTML = newResultsCount.innerHTML;
                }

                const newProductsContainer = doc.querySelector('.products-container');
                if (productsContainer && newProductsContainer) {
                    productsContainer.innerHTML = newProductsContainer.innerHTML;
                }

                window.history.pushState({}, '', url);
            })
            .catch(err => console.error('Filter fetch error:', err))
            .finally(() => {
                isFetching = false;
                if (productsGrid) productsGrid.style.opacity = '1';
            });
    }

    // Build URL from any form
    function getFilterUrl(form) {
        const formData = new FormData(form);
        const params = new URLSearchParams();

        for (const [key, value] of formData.entries()) {
            if (value !== '' || key === 'category') {
                params.append(key, value);
            }
        }

        return window.location.pathname + '?' + params.toString();
    }

    // Bind Event Listeners to EACH form
    filterForms.forEach(form => {
        const searchInput = form.querySelector('input[name="q"], .mob-search-input');
        const categorySelect = form.querySelector('select[name="category"]');
        const categoryCheckboxes = form.querySelectorAll('input[name="category[]"][type="checkbox"]');
        const sortRadios = form.querySelectorAll('input[name="sort"][type="radio"]');
        const minPriceIn = form.querySelector('#minPriceInput');
        const maxPriceIn = form.querySelector('#maxPriceInput');

        const triggerFilter = debounce(function () {
            const url = getFilterUrl(form);
            fetchResults(url);
        }, 500);

        // 1. Search (Debounced)
        if (searchInput) {
            searchInput.addEventListener('input', triggerFilter);
            searchInput.addEventListener('keypress', function (e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    fetchResults(getFilterUrl(form));
                }
            });
        }

        // 2. Category Select (Desktop)
        if (categorySelect) {
            categorySelect.addEventListener('change', function () {
                fetchResults(getFilterUrl(form));
            });
        }

        // 3. Category Checkboxes (Mobile)
        if (categoryCheckboxes.length > 0) {
            categoryCheckboxes.forEach(checkbox => {
                // For live-filtering on select (optional, usually footer does it, but ajax updates standard UX)
                checkbox.addEventListener('change', triggerFilter);
            });
        }

        // 4. Sort Radios (Mobile)
        if (sortRadios.length > 0) {
            sortRadios.forEach(radio => {
                radio.addEventListener('change', triggerFilter);
            });
        }

        // 5. Price Inputs (Debounced)
        if (minPriceIn) minPriceIn.addEventListener('input', triggerFilter);
        if (maxPriceIn) maxPriceIn.addEventListener('input', triggerFilter);
    });

    // 6. Pagination Links (Delegation)
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

    window.addEventListener('popstate', function () {
        fetchResults(window.location.href);
    });

});


