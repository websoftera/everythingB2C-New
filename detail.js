console.log('detail.js loaded');
document.querySelectorAll('.product-detail-card').forEach(card => {
  console.log('Found product-detail-card:', card);
  const qtyInput = card.querySelector('.quantity-input');
  const payButton = card.querySelector('.pay');
  const addBtn = card.querySelector('.add-to-cart');
  console.log('Found add to cart button:', addBtn);

  const unitPrice = parseFloat(payButton.getAttribute('data-pay')) || 0;

  function updatePayText() {
    let qty = parseInt(qtyInput.value);
    if (isNaN(qty) || qty < 1) qty = 1;
    qtyInput.value = qty;
    const total = unitPrice * qty;
    payButton.textContent = `PAY ₹${total.toFixed(2)}`;
  }

  qtyInput.addEventListener('input', updatePayText);
  updatePayText();

  // --- Fix: Disable add-to-cart button if already in cart (AJAX cart) ---
  function checkAjaxCartButtonState() {
    // Use the numeric product ID from data-product-id
    const productId = addBtn?.dataset.productId;
    if (!productId) return;
    // Check if the button is already disabled (e.g., after click)
    if (addBtn.disabled) return;
    // Optionally, you could fetch the cart count or state from the server via AJAX
    // For now, always enable the button (let server handle duplicates)
    addBtn.disabled = false;
    addBtn.textContent = 'ADD TO CART';
  }
  checkAjaxCartButtonState();
});

// Remove or comment out all localStorage cart logic and add-to-cart logic below:
/*
// --- cart js ---------------------------------------------------------------------
function saveToCart(product) {
  const cart = JSON.parse(localStorage.getItem("cart")) || [];
  const existing = cart.find(item => item.id === product.id);

  if (existing) {
    existing.qty = product.qty;
  } else {
    cart.push(product);
  }

  localStorage.setItem("cart", JSON.stringify(cart));
}

// --- Check if already in cart
function initCartButtonState() {
  const title = document.querySelector(".product-detail-card .title")?.innerText || "";
  const productId = title.replace(/\s+/g, "-").toLowerCase();
  const cart = JSON.parse(localStorage.getItem("cart")) || [];

  const exists = cart.find(item => item.id === productId);
  if (exists) {
    const btn = document.querySelector(".add-to-cart");
    if (btn) {
      btn.textContent = "Added to Cart";
      btn.disabled = true;
    }
  }
}

function showCartModal(productName = 'Product') {
  const modal = document.getElementById('cart-modal');
  const message = document.getElementById('cart-message');
  const icon = document.getElementById('cart-icon');

  if (!modal || !message || !icon) return;

  icon.textContent = '✅';
  message.textContent = `${productName} added to cart`;

  modal.style.display = 'flex';

  // Auto close after 2.5 seconds
  setTimeout(() => {
    modal.style.display = 'none';
  }, 2500);
}

function closeCartModal() {
  const modal = document.getElementById('cart-modal');
  if (modal) modal.style.display = 'none';
}

function addProductToCart(btn) {
  const card = btn.closest(".product-detail-card");
  const title = card.querySelector(".title")?.innerText || "Untitled";
  const productId = title.replace(/\s+/g, "-").toLowerCase();
  const image = card.querySelector("#mainImage")?.getAttribute("src") || "";
  const quantity = parseInt(card.querySelector(".quantity-input")?.value, 10) || 1;
  const payPrice = parseFloat(card.querySelector(".pay")?.dataset.pay) || 0;
  const mrpPrice = parseFloat(card.querySelector(".mrp")?.dataset.mrp) || 0;

  const product = {
    id: productId,
    title,
    image,
    qty: quantity,
    pay: payPrice,
    mrp: mrpPrice
  };

  showCartModal(title); // ✅ SHOW POPUP HERE
  btn.textContent = "Added to Cart";
  btn.disabled = true;
}
*/

// --- Setup Events on Load (only for price/qty UI, not cart) ---
document.addEventListener("DOMContentLoaded", () => {
  // No add-to-cart logic here; handled by popup.js
});

// wishlist js ------------------------------------------------------------------------------------//

