// Load Header HTML
window.addEventListener('DOMContentLoaded', () => {
  fetch('Header.html')
    .then(res => res.text())
    .then(data => {
      document.getElementById('header-0').innerHTML = data;
      initHeaderFeatures(); // After header loads, run all scripts
    });
});

function initHeaderFeatures() {
  // ========== Category Scroll Buttons ==========
  const container = document.querySelector('.category-container');
  const nextBtn1 = document.querySelector('.mobile-next-btn');
  const prevBtn1 = document.querySelector('.mobile-prev-btn');
  if (container && nextBtn1 && prevBtn1) {
    nextBtn1.addEventListener('click', () => {
      container.scrollBy({ left: container.offsetWidth, behavior: 'smooth' });
    });
    prevBtn1.addEventListener('click', () => {
      container.scrollBy({ left: -container.offsetWidth, behavior: 'smooth' });
    });
  }

 // ========== Price Update on Quantity Change ==========
document.querySelectorAll('.card').forEach(card => {
  const payButton = card.querySelector('.pay');
  const qtyInput = card.querySelector('.quantity-input');

  if (payButton && qtyInput) {
    const unitPrice = parseFloat(payButton.getAttribute('data-pay')) || 0;

    function updatePayText() {
      let quantity = parseInt(qtyInput.value);
      if (isNaN(quantity) || quantity < 1) quantity = 1;

      const total = unitPrice * quantity;
      payButton.textContent = `PAY ₹${total.toFixed(2)}`;
    }

    qtyInput.addEventListener('input', updatePayText);
    updatePayText(); // Run once on load
  }
});


  // ========== Mobile Auto Scroll ==============================================================================================================
  const scrollContainer = document.querySelector(".scroll-wrapper");
  if (window.innerWidth < 992 && scrollContainer) {
    const clone = scrollContainer.innerHTML;
    scrollContainer.innerHTML += clone;
    let scrollPos = 0;
    const scrollStep = 150;
    setInterval(() => {
      scrollPos += scrollStep;
      scrollContainer.scrollTo({ left: scrollPos, behavior: "smooth" });
      if (scrollPos >= scrollContainer.scrollWidth / 2) {
        scrollContainer.scrollTo({ left: 0, behavior: "auto" });
        scrollPos = 0;
      }
    }, 2000);
  }

  // ========== Load Popup ================================================================================================================================
  fetch('/popup.html')
    .then(response => response.text())
    .then(html => {
      document.body.insertAdjacentHTML('beforeend', html);
      setTimeout(() => {
        document.getElementById("popupForm").style.display = "block";
        document.getElementById("popupOverlay").style.display = "block";
      }, 5000);
      window.closeLoginForm = function () {
        document.getElementById("popupForm").style.display = "none";
        document.getElementById("popupOverlay").style.display = "none";
      };
    })
    .catch(error => console.error('Error loading popup:', error));
}

function initWishlistFeature() {
  const wishlist = JSON.parse(localStorage.getItem('wishlist')) || [];

  // Set initial heart state on page load
  document.querySelectorAll('.heart-checkbox').forEach(cb => {
    const card = cb.closest('.card');
    const productId = card.getAttribute('data-id');
    const inWishlist = wishlist.find(item => item.id === productId);

    cb.checked = !!inWishlist;

    const label = cb.nextElementSibling;
    if (label) label.textContent = inWishlist ? '❤️' : '🤍';
  });

  // Handle checkbox change
  document.querySelectorAll('.heart-checkbox').forEach(cb => {
    cb.addEventListener('change', function () {
      const card = this.closest('.card');
      const productId = card.getAttribute('data-id');
      const productName = card.querySelector('h3')?.innerText || 'Product';
      const product = {
        id: productId,
        html: card.outerHTML
      };

      let wishlist = JSON.parse(localStorage.getItem('wishlist')) || [];

      if (this.checked) {
        // Add to wishlist if not already
        if (!wishlist.find(item => item.id === productId)) {
          wishlist.push(product);
        }
        showWishlistModal(productName, 'added');
      } else {
        // Remove from wishlist
        wishlist = wishlist.filter(item => item.id !== productId);
        showWishlistModal(productName, 'removed');
      }

      localStorage.setItem('wishlist', JSON.stringify(wishlist));

      // Toggle heart icon
      const label = this.nextElementSibling;
      if (label) label.textContent = this.checked ? '❤️' : '🤍';
    });
  });
}

// ✨ Wishlist modal logic
function showWishlistModal(productName, type = 'added') {
  const modal = document.getElementById('wishlist-modal');
  const message = document.getElementById('wishlist-message');
  const icon = document.getElementById('wishlist-icon');

  if (type === 'added') {
    icon.textContent = '❤️';
    message.textContent = `${productName} added to your wishlist!`;
  } else {
    icon.textContent = '💔';
    message.textContent = `${productName} removed from your wishlist.`;
  }

  modal.style.display = 'flex'; // Show modal
  setTimeout(() => {
    modal.style.display = 'none'; // Auto-hide after 2.5s
  }, 2500);
}

// Close modal manually
function closeWishlistModal() {
  document.getElementById('wishlist-modal').style.display = 'none';
}

// Run after DOM is ready
document.addEventListener('DOMContentLoaded', () => {
  initWishlistFeature();
});

// cart js ---------------------------------------------------------------------------------------------------------------->//

  
  
  // Show modal with product add/update status
  function showCartModal(productName, type = 'added') {
    const modal = document.getElementById('cart-modal');
    const message = document.getElementById('cart-message');
    const icon = document.getElementById('cart-icon');
    if (!modal || !message || !icon) return;

    if (type === 'added') {
      icon.textContent = '✅';
      message.textContent = `${productName} added to cart`;
    } else if (type === 'exists') {
      icon.textContent = '⚠️';
      message.textContent = `${productName} is already in cart, quantity updated`;
    }

    modal.style.display = 'flex';

    setTimeout(() => {
      modal.style.display = 'none';
    }, 2000);
  }

  // Close modal manually
  function closeCartModal() {
    const modal = document.getElementById('cart-modal');
    if (modal) modal.style.display = 'none';
  }

  // Handle adding product to cart
  function handleAddToCart(button) {
    const card = button.closest('.card');
    if (!card) return;

    const productName = card.querySelector('h3')?.innerText.trim() || 'Item';
    const productId = card.getAttribute('data-id');
    const imgSrc = card.querySelector('img')?.getAttribute('src') || '';
    const qtyInput = card.querySelector('.quantity-input');
    const payBtn = card.querySelector('.pay');    // The pay button element
    const mrpBtn = card.querySelector('.mrp');    // The mrp button element

    // Safely parse quantity, minimum 1
    let qty = parseInt(qtyInput?.value, 10);
    if (isNaN(qty) || qty < 1) qty = 1;

    // Safely parse pay and mrp prices from data attributes
    // IMPORTANT: you must add data-pay and data-mrp attributes on these buttons!
    const pay = parseFloat(payBtn?.dataset.pay) || 0;
    const mrp = parseFloat(mrpBtn?.dataset.mrp) || 0;

    // Create product object
    const product = {
      id: productId,
      title: productName,
      image: imgSrc,
      qty: qty,
      pay: pay,
      mrp: mrp
    };

    // Load cart from localStorage or initialize empty
    let cart = JSON.parse(localStorage.getItem('cart')) || [];
    const existingIndex = cart.findIndex(item => item.id === productId);

    if (existingIndex !== -1) {
      // If product exists, increase quantity
      cart[existingIndex].qty += qty;
      showCartModal(productName, 'exists');
    } else {
      // Add new product to cart
      cart.push(product);
      showCartModal(productName, 'added');
    }

    // Save updated cart
    localStorage.setItem('cart', JSON.stringify(cart));
  }

  // Render cart items inside a container element
  function renderCart() {
    const cart = JSON.parse(localStorage.getItem('cart')) || [];
    const container = document.getElementById('cart-container');
    if (!container) return;

    container.innerHTML = cart.map(item => `
      <div class="card" data-id="${item.id}">
        <img src="${item.image}" alt="${item.title}" />
        <h3>${item.title}</h3>
        <p>MRP: ₹${item.mrp}</p>
        <p>Pay: ₹${item.pay}</p>
        <p>Quantity: ${item.qty}</p>
      </div>
    `).join('');
  }

  // Initialize event listeners when DOM loads
  document.addEventListener('DOMContentLoaded', () => {
    // Add click listeners to all "Add to Cart" buttons
    document.querySelectorAll('.add-to-cart').forEach(btn => {
      btn.addEventListener('click', () => handleAddToCart(btn));
    });

    renderCart();
  });