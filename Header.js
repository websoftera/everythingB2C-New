// Load Header HTML
window.addEventListener('DOMContentLoaded', () => {
  const baseUrl = window.BASE_URL || '';
  fetch(baseUrl + 'Header.html')
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
  const baseUrl = window.BASE_URL || '';
  fetch(baseUrl + 'popup.html')
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
// ========== wishlist ================================================================================================================================


function initWishlistFeature() {
  document.querySelectorAll('.heart-checkbox').forEach(checkbox => {
    checkbox.addEventListener('change', function () {
      const productId = this.getAttribute('data-product-id');
      const productName = this.closest('.card')?.querySelector('h3')?.innerText.trim() || 'Product';

      // Wishlist functionality is now handled by individual pages via AJAX
      // This prevents conflicts with server-side wishlist management
    });
  });
}

// Initialize event listeners when DOM loads
document.addEventListener('DOMContentLoaded', () => {
  // Wishlist functionality is now handled by individual pages
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

// Initialize event listeners when DOM loads
document.addEventListener('DOMContentLoaded', () => {
  // Cart functionality is now handled by individual pages
});

// drop down menu js ---------------------->
// Example using JS



// zoom effect js of deatil card 👇👇 ----------------------------------------------------------------------//


function activateZoom() {
  magnify("mainImage", 2); // Zoom level
  document.querySelector(".zoom-btn").style.display = "none";
}

function magnify(imgID, zoom) {
  let img = document.getElementById(imgID);
  if (!img || document.querySelector(".img-magnifier-glass")) return;

  let glass = document.createElement("DIV");
  glass.setAttribute("class", "img-magnifier-glass");

  let closeBtn = document.createElement("div");
  closeBtn.innerHTML = "&times;";
  closeBtn.classList.add("zoom-close-btn");
  closeBtn.onclick = () => {
    glass.remove();
    document.querySelector(".zoom-btn").style.display = "block";
  };
  glass.appendChild(closeBtn);

  img.parentElement.appendChild(glass);

  glass.style.backgroundImage = `url('${img.src}')`;
  glass.style.backgroundRepeat = "no-repeat";
  glass.style.backgroundSize = `${img.width * zoom}px ${img.height * zoom}px`;
  glass.style.display = "block";

  const w = glass.offsetWidth / 2;
  const h = glass.offsetHeight / 2;

  function moveMagnifier(e) {
    e.preventDefault();
    const pos = getCursorPos(e);
    let x = pos.x;
    let y = pos.y;

    if (x > img.width - w / zoom) x = img.width - w / zoom;
    if (x < w / zoom) x = w / zoom;
    if (y > img.height - h / zoom) y = img.height - h / zoom;
    if (y < h / zoom) y = h / zoom;

    glass.style.left = (x - w) + "px";
    glass.style.top = (y - h) + "px";
    glass.style.backgroundPosition = `-${(x * zoom - w)}px -${(y * zoom - h)}px`;
  }

  function getCursorPos(e) {
    const a = img.getBoundingClientRect();
    return {
      x: e.clientX - a.left,
      y: e.clientY - a.top
    };
  }

  img.addEventListener("mousemove", moveMagnifier);
  glass.addEventListener("mousemove", moveMagnifier);
}



const mainImage = document.getElementById('mainImage');
const thumbnails = document.querySelectorAll('.thumbnail');

thumbnails.forEach(thumb => {
  thumb.addEventListener('click', () => {
    // Swap src between clicked thumbnail and main image
    const tempSrc = mainImage.src;
    mainImage.src = thumb.src;
    thumb.src = tempSrc;
  });
});



window.addEventListener('DOMContentLoaded', () => {
  const baseUrl = window.BASE_URL || '';
  fetch(baseUrl + 'Header.html')
    .then(res => res.text())
    .then(data => {
      document.getElementById('header-0').innerHTML = data;
      initHeaderFeatures();
    });

  fetch(baseUrl + 'Footer.html')
    .then(res => res.text())
    .then(data => {
      document.getElementById('footer-0').innerHTML = data;
    });
});




document.addEventListener('DOMContentLoaded', () => {

  // Correct all PAY buttons dynamically
  document.querySelectorAll('.pay').forEach(btn => {
    let amount = parseFloat(btn.getAttribute('data-pay')) || 0;
    btn.textContent = `PAY \u20B9${amount.toFixed(2)}`;
  });

  // Correct all MRP buttons dynamically (if similar structure)
  document.querySelectorAll('.mrp').forEach(btn => {
    let amount = parseFloat(btn.getAttribute('data-mrp')) || 0;
    btn.textContent = `MRP \u20B9${amount.toFixed(2)}`;
  });

});






