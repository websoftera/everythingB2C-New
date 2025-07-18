console.log('pop.js loaded');
function showPopup() {
  console.log('showPopup called from pop.js');
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
    alert("Delivery available in your area.");
  } else {
    alert("Sorry, we do not deliver in this area.");
  }
}

// Show popup after 3 seconds (or 5 if you want)
window.addEventListener("load", () => {
  setTimeout(showPopup, 3000);
});
