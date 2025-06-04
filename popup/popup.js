// popup.js
function showPopup() {
  document.getElementById("popupOverlay").style.display = "block";
  document.getElementById("popupForm").style.display = "block";
}

function closeLoginForm() {
  document.getElementById("popupOverlay").style.display = "none";
  document.getElementById("popupForm").style.display = "none";
}

function checkPincode() {
  const pin = document.getElementById("pin").value;
  const serviceablePins = ["400001", "500001", "560001", "380001"]; // example pins

  if (serviceablePins.includes(pin)) {
    alert("Delivery available in your area.");
  } else {
    alert("Sorry, we do not deliver in this area.");
  }
}

// Show popup 5 seconds after page load
window.addEventListener("load", () => {
  setTimeout(showPopup, 3000);
});
