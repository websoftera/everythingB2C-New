<?php
/**
 * delivery_popup.php
 * Renders the delivery availability check modal using the EXACT design from index.php.
 */

if (!function_exists('shouldShowDeliveryPopup')) {
    require_once __DIR__ . '/delivery_popup_functions.php';
}

$showPopup = shouldShowDeliveryPopup();
$popupSettings = getPopupSettings();
$popupEnabled = $popupSettings['popup_enabled'] ?? '1';

if ($popupEnabled !== '1') {
    return; // Don't render if disabled
}
?>

<!-- Delivery Availability Popup -->
<div id="deliveryPopup" class="delivery-popup-overlay" style="display: none;">
    <div class="delivery-popup">
        <div class="delivery-popup-header">
            <div class="delivery-logo">
                <img src="<?php echo $base_url; ?>asset/images/logo.webp" alt="everythingb2c logo" class="site-logo">
            </div>
            <button class="delivery-popup-close" onclick="closeDeliveryPopup()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <div class="delivery-popup-content">
            <div class="delivery-message">
                <?php echo htmlspecialchars($popupSettings['popup_message'] ?? 'We Deliver Orders In Maharashtra, Gujarat, Bangalore, And Hyderabad Only.'); ?>
            </div>
            <div class="delivery-instruction">
                <?php echo htmlspecialchars($popupSettings['popup_instruction'] ?? 'Please Enter Your Pincode To Check Delivery Availability.'); ?>
            </div>
            
            <div class="delivery-input-section">
                <div class="delivery-input-group">
                    <input type="text" id="pincodeInput" class="delivery-pincode-input" 
                           placeholder="Enter your pincode" maxlength="6" pattern="[0-9]{6}">
                    <button type="button" class="delivery-check-btn" onclick="checkDeliveryPincode()">
                        Check
                    </button>
                </div>
                <button type="button" class="delivery-start-shopping-btn" onclick="startShopping()">
                    START SHOPPING
                </button>
            </div>
            
            <div id="deliveryResult" class="delivery-result" style="display: none;">
                <div class="delivery-result-message"></div>
            </div>
        </div>
    </div>
</div>

<style>
/* Global Popup Font */
.delivery-popup-overlay * {
    font-family: 'Mulish', sans-serif !important;
}

/* Delivery Popup Styles */
.delivery-popup-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.7);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 10000;
    backdrop-filter: blur(5px);
}

.delivery-popup {
    background: white;
    border-radius: 8px;
    padding: 20px;
    padding-top: 40px;
    max-width: 380px;
    width: 90%;
    max-height: 80vh;
    overflow-y: auto;
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
    position: relative;
}

.delivery-popup-header {
    text-align: center;
    margin-bottom: 20px;
    position: relative;
}

.delivery-logo {
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 8px;
}

.site-logo {
    height: 50px;
    width: auto;
    max-width: 250px;
}

.delivery-popup-close {
    position: absolute;
    top: 8px;
    right: 8px;
    background: none;
    border: none;
    font-size: 16px;
    color: #999;
    cursor: pointer;
    padding: 4px;
    border-radius: 50%;
    transition: all 0.3s ease;
    width: 24px;
    height: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 10;
}

.delivery-popup-close:hover {
    background-color: #f5f5f5;
    color: #666;
}

.delivery-popup-content {
    text-align: center;
}

.delivery-message {
    font-size: 13px;
    font-weight: 600;
    color: #9fbe1b; /* Match user screenshot color */
    margin-bottom: 12px;
    line-height: 1.3;
}

.delivery-instruction {
    font-size: 13px;
    font-weight: 600;
    color: #333;
    margin-bottom: 12px;
    line-height: 1.3;
}

.delivery-input-section {
    margin-bottom: 15px;
}

.delivery-input-group {
    display: flex;
    gap: 8px;
    margin-bottom: 12px;
}

.delivery-pincode-input {
    flex: 1;
    padding: 10px 12px;
    border: 1px solid #ddd;
    border-radius: 5px;
    font-size: 13px;
    outline: none;
    transition: border-color 0.3s ease;
}

.delivery-pincode-input:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 2px rgba(0, 123, 255, 0.25);
}

.delivery-check-btn {
    padding: 10px 16px;
    background-color: #9FBF1C;
    color: white;
    border: none;
    border-radius: 5px;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.delivery-check-btn:hover {
    background-color: #9ebf1c9d;
}

.delivery-start-shopping-btn {
    width: 100%;
    padding: 10px;
    background-color: #9FBF1C;
    color: white;
    border: none;
    border-radius: 6px;
    font-size: 14px;
    font-weight: bold;
    cursor: pointer;
    transition: background-color 0.3s ease;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.delivery-start-shopping-btn:hover {
    background-color: #9FBF1C;
}

.delivery-result {
    margin-top: 15px;
    padding: 12px;
    border-radius: 6px;
    font-weight: bold;
    text-align: center;
}

.delivery-result-message {
    font-size: 13px;
}

/* Match original colors for result types */
.delivery-result.success {
    background-color: #d4edda !important;
    color: #155724 !important;
}

.delivery-result.error {
    background-color: #f8d7da !important;
    color: #721c24 !important;
}

@media (max-width: 768px) {
    .delivery-popup { padding: 15px; padding-top: 35px; max-width: 350px; }
    .delivery-input-group { flex-direction: column; }
    .delivery-check-btn { width: 100%; }
}
</style>

<script>
function closeDeliveryPopup() {
    document.getElementById('deliveryPopup').style.display = 'none';
    fetch('ajax/mark_popup_shown.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ action: 'mark_shown' })
    }).catch(console.error);
}

function checkDeliveryPincode() {
    const input = document.getElementById('pincodeInput');
    const pincode = input.value.trim();
    const resultDiv = document.getElementById('deliveryResult');
    const resultMessage = resultDiv.querySelector('.delivery-result-message');
    
    if (!pincode || pincode.length !== 6 || !/^\d{6}$/.test(pincode)) {
        resultDiv.className = 'delivery-result error';
        resultMessage.textContent = 'Please enter a valid 6-digit pincode.';
        resultDiv.style.display = 'block';
        return;
    }
    
    const checkBtn = document.querySelector('.delivery-check-btn');
    const originalText = checkBtn.textContent;
    checkBtn.textContent = 'Checking...';
    checkBtn.disabled = true;
    
    fetch('ajax/check_pincode.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ pincode: pincode })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            resultDiv.className = `delivery-result ${data.status}`;
            resultMessage.textContent = data.message;
        } else {
            resultDiv.className = 'delivery-result error';
            resultMessage.textContent = data.message || 'An error occurred.';
        }
        resultDiv.style.display = 'block';
    })
    .catch(error => {
        resultDiv.className = 'delivery-result error';
        resultMessage.textContent = 'Network error.';
        resultDiv.style.display = 'block';
    })
    .finally(() => {
        checkBtn.textContent = originalText;
        checkBtn.disabled = false;
    });
}

function startShopping() {
    closeDeliveryPopup();
}

document.addEventListener('DOMContentLoaded', function() {
    const pincodeInput = document.getElementById('pincodeInput');
    if (pincodeInput) {
        pincodeInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') checkDeliveryPincode();
        });
        pincodeInput.addEventListener('input', function(e) {
            this.value = this.value.replace(/[^0-9]/g, '');
        });
    }

    // Auto-open logic (only if session says so)
    <?php if ($showPopup): ?>
    const popup = document.getElementById('deliveryPopup');
    if (popup) popup.style.display = 'flex';
    <?php endif; ?>

    // Handle open_pincode param
    if (new URLSearchParams(window.location.search).get('open_pincode') === '1') {
        const popup = document.getElementById('deliveryPopup');
        if (popup) popup.style.display = 'flex';
        history.replaceState(null, '', window.location.pathname);
    }
});
</script>
