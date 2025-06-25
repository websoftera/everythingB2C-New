// popup/searchbar.js
(function() {
  const searchInput = document.getElementById('headerSearchInput');
  const searchBtn = document.getElementById('headerSearchBtn');
  const resultsPopup = document.getElementById('headerSearchResultsPopup');
  let selectedCategory = 'all';
  let debounceTimeout = null;

  // Handle category dropdown selection (desktop & mobile)
  document.querySelectorAll('.category-option').forEach(option => {
    option.addEventListener('click', function(e) {
      e.preventDefault();
      const catSlug = this.getAttribute('data-category');
      selectedCategory = catSlug;
      // Update dropdown button text
      if (this.closest('.dropdown-desktop')) {
        document.getElementById('selectedCategoryDesktop').textContent = this.textContent;
        document.getElementById('categoryDropdownDesktop').setAttribute('data-selected-category', catSlug);
      } else {
        document.getElementById('selectedCategoryMobile').textContent = this.textContent;
        document.getElementById('categoryDropdownMobile').setAttribute('data-selected-category', catSlug);
      }
      // If not 'all', redirect to category page
      if (catSlug !== 'all') {
        window.location.href = 'category.php?slug=' + encodeURIComponent(catSlug);
      }
    });
  });

  // Debounced AJAX search
  function doSearch() {
    const query = searchInput.value.trim();
    if (!query) {
      resultsPopup.style.display = 'none';
      resultsPopup.innerHTML = '';
      return;
    }
    fetch('ajax/search-products.php?query=' + encodeURIComponent(query))
      .then(res => res.json())
      .then(data => {
        if (data.success && data.results.length > 0) {
          resultsPopup.innerHTML = data.results.map(product => `
            <div class="search-result-item d-flex align-items-center p-2 border-bottom" style="cursor:pointer; background:#fff;" data-slug="${product.slug}">
              <img src="./${product.image}" alt="${product.name}" style="width:40px;height:40px;object-fit:cover;border-radius:4px;margin-right:10px;">
              <div>
                <div style="font-weight:600;">${product.name}</div>
                <div style="font-size:12px;color:#888;">${product.category}</div>
                <div style="font-size:13px;color:#16BAE4;">₹${product.price} <span style="text-decoration:line-through;color:#aaa;font-size:11px;">₹${product.mrp}</span> ${product.discount > 0 ? `<span style='color:#e74c3c;'>(${product.discount}% OFF)</span>` : ''}</div>
              </div>
            </div>
          `).join('');
          resultsPopup.style.display = 'block';
        } else {
          resultsPopup.innerHTML = '<div class="p-2 bg-white text-muted">No results found.</div>';
          resultsPopup.style.display = 'block';
        }
      })
      .catch(() => {
        resultsPopup.innerHTML = '<div class="p-2 bg-white text-danger">Error searching.</div>';
        resultsPopup.style.display = 'block';
      });
  }

  searchInput.addEventListener('input', function() {
    clearTimeout(debounceTimeout);
    debounceTimeout = setTimeout(doSearch, 300);
  });

  // Search on button click
  searchBtn.addEventListener('click', doSearch);

  // Navigate to product page on result click
  resultsPopup.addEventListener('click', function(e) {
    const item = e.target.closest('.search-result-item');
    if (item) {
      window.location.href = 'product.php?slug=' + encodeURIComponent(item.getAttribute('data-slug'));
    }
  });

  // Hide popup on outside click or escape
  document.addEventListener('mousedown', function(e) {
    if (!resultsPopup.contains(e.target) && e.target !== searchInput) {
      resultsPopup.style.display = 'none';
    }
  });
  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
      resultsPopup.style.display = 'none';
    }
  });
})(); 