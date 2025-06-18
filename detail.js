document.querySelectorAll('.product-detail-card').forEach(card => {
  const qtyInput = card.querySelector('.quantity-input');
  const payButton = card.querySelector('.pay');
  const plusBtn = card.querySelector('.qty-plus');  // your plus button
  const minusBtn = card.querySelector('.qty-minus'); // your minus button

  const unitPrice = parseFloat(payButton.getAttribute('data-pay')) || 0;

  function updatePayText() {
    let qty = parseInt(qtyInput.value);
    if (isNaN(qty) || qty < 1) qty = 1;
    qtyInput.value = qty;
    const total = unitPrice * qty;
    payButton.textContent = `PAY ₹${total.toFixed(2)}`;
  }

  plusBtn?.addEventListener('click', () => {
    qtyInput.value = (parseInt(qtyInput.value) || 1) + 1;
    updatePayText();
  });

  minusBtn?.addEventListener('click', () => {
    let qty = (parseInt(qtyInput.value) || 1) - 1;
    if (qty < 1) qty = 1;
    qtyInput.value = qty;
    updatePayText();
  });

  qtyInput.addEventListener('input', updatePayText);
  updatePayText();
});




  // --- cart js ---------------------------------------------------------------------
  function saveToCart(product) {
    const cart = JSON.parse(localStorage.getItem("cart")) || [];
    const existing = cart.find(item => item.id === product.id);

    if (existing) {
      existing.qty += product.qty;
    } else {
      cart.push(product);
    }

    localStorage.setItem("cart", JSON.stringify(cart));
  }

  // --- Add to Cart Logic
  function addProductToCart(btn) {
    const card = btn.closest(".product-detail-card");
    const title = card.querySelector(".title")?.innerText || "Untitled";
    const productId = title.replace(/\s+/g, "-").toLowerCase(); // or use custom data-id
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

    saveToCart(product);
    btn.textContent = "Added to Cart";
    btn.disabled = true;
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

  // --- Setup Events on Load
  document.addEventListener("DOMContentLoaded", () => {
    const addBtn = document.querySelector(".add-to-cart");
    if (addBtn) {
      addBtn.addEventListener("click", () => addProductToCart(addBtn));
    }
    initCartButtonState();
  });

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

  saveToCart(product);
  showCartModal(title); // ✅ SHOW POPUP HERE
  btn.textContent = "Added to Cart";
  btn.disabled = true;
}


// wishlist js ------------------------------------------------------------------------------------//

