function showPopup() {
  var overlay = document.getElementById("popupOverlay");
  var form = document.getElementById("popupForm");
  if (overlay) overlay.style.display = "block";
  if (form) form.style.display = "block";
}

function closeLoginForm() {
  document.getElementById("popupOverlay").style.display = "none";
  document.getElementById("popupForm").style.display = "none";
}

function checkPincode() {
  const pin = document.getElementById("pin").value;
  const serviceablePins = ["400001", "500001", "560001", "380001"];

  if (serviceablePins.includes(pin)) {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            icon: 'success',
            title: 'Delivery Available',
            text: 'Delivery available in your area.',
            timer: 3000,
            showConfirmButton: false
        });
    } else {
        Swal.fire({
            icon: 'success',
            title: 'Delivery Available',
            text: 'Delivery available in your area.',
            timer: 3000,
            showConfirmButton: false
        });
    }
  } else {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            icon: 'error',
            title: 'Delivery Not Available',
            text: 'Sorry, we do not deliver in this area.',
            timer: 4000,
            showConfirmButton: false
        });
    } else {
        Swal.fire({
            icon: 'error',
            title: 'Delivery Not Available',
            text: 'Sorry, we do not deliver in this area.',
            timer: 4000,
            showConfirmButton: false
        });
    }
  }
}

// Show popup after 3 seconds (or 5 if you want)
window.addEventListener("load", () => {
  setTimeout(showPopup, 3000);
});
