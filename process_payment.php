<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once 'includes/functions.php';

// Force login
if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$userId = $_SESSION['user_id'];
$orderId = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;

// Verify order belongs to user
$order = getOrderById($orderId);
if (!$order || $order['user_id'] != $userId) {
    header('Location: cart.php');
    exit;
}

// Check if order is pending payment
if ($order['payment_status'] !== 'pending') {
    header('Location: order_success.php?order_id=' . $orderId);
    exit;
}

$pageTitle = 'Payment - Razorpay';
include 'includes/header.php';
?>

<style>
.payment-container {
    max-width: 600px;
    margin: 0 auto;
    padding: 20px;
}

.payment-card {
    background: white;
    border-radius: 10px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    padding: 30px;
    margin-bottom: 20px;
}

.payment-header {
    text-align: center;
    margin-bottom: 30px;
}

.payment-header h2 {
    color: #333;
    margin-bottom: 10px;
}

.payment-header .amount {
    font-size: 2rem;
    font-weight: bold;
    color: #28a745;
}

.order-details {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 8px;
    margin-bottom: 30px;
}

.order-details h5 {
    margin-bottom: 15px;
    color: #333;
}

.detail-row {
    display: flex;
    justify-content: space-between;
    margin-bottom: 8px;
    padding: 5px 0;
    border-bottom: 1px solid #eee;
}

.detail-row:last-child {
    border-bottom: none;
    font-weight: bold;
    font-size: 1.1rem;
}

.razorpay-button {
    background: #3399cc;
    color: white;
    border: none;
    padding: 15px 30px;
    border-radius: 5px;
    font-size: 1.1rem;
    font-weight: bold;
    cursor: pointer;
    width: 100%;
    transition: background-color 0.3s;
}

.razorpay-button:hover {
    background: #2980b9;
}

.loading {
    display: none;
    text-align: center;
    margin-top: 20px;
}

.loading .spinner {
    border: 4px solid #f3f3f3;
    border-top: 4px solid #3399cc;
    border-radius: 50%;
    width: 40px;
    height: 40px;
    animation: spin 1s linear infinite;
    margin: 0 auto 10px;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.back-button {
    background: #6c757d;
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 5px;
    text-decoration: none;
    display: inline-block;
    margin-top: 15px;
    transition: background-color 0.3s;
}

.back-button:hover {
    background: #5a6268;
    color: white;
    text-decoration: none;
}
</style>

<div class="page-banner" style="background: url('asset/images/internalpage-bg.webp') center/cover no-repeat; min-height: 240px; display: flex; align-items: center;">
    <div class="container">
        <h2 style="color: #fff; font-size: 2rem; font-weight: bold; text-shadow: 0 2px 8px rgba(0,0,0,0.3); margin: 0; padding: 32px 0;">
            Secure Payment
        </h2>
    </div>
</div>

<div class="container my-5">
    <div class="payment-container">
        <div class="payment-card">
            <div class="payment-header">
                <h2>Complete Your Payment</h2>
                <div class="amount">₹<?php echo number_format($order['total_amount'], 2); ?></div>
                <p class="text-muted">Order #<?php echo $order['tracking_id']; ?></p>
            </div>

            <div class="order-details">
                <h5>Order Summary</h5>
                <div class="detail-row">
                    <span>Subtotal:</span>
                    <span>₹<?php echo number_format($order['subtotal'], 2); ?></span>
                </div>
                <?php if ($order['shipping_charge'] > 0): ?>
                <div class="detail-row">
                    <span>Delivery Charge:</span>
                    <span>₹<?php echo number_format($order['shipping_charge'], 2); ?></span>
                </div>
                <?php endif; ?>
                <?php if ($order['gst_amount'] > 0): ?>
                <div class="detail-row">
                    <span>GST:</span>
                    <span>₹<?php echo number_format($order['gst_amount'], 2); ?></span>
                </div>
                <?php endif; ?>
                <div class="detail-row">
                    <span>Total Amount:</span>
                    <span>₹<?php echo number_format($order['total_amount'], 2); ?></span>
                </div>
            </div>

            <button id="rzp-button" class="razorpay-button">
                <i class="fas fa-credit-card me-2"></i>
                Pay Securely with Razorpay
            </button>

            <div class="loading" id="loading">
                <div class="spinner"></div>
                <p>Processing payment...</p>
            </div>

            <div class="text-center">
                <a href="checkout.php" class="back-button">
                    <i class="fas fa-arrow-left me-2"></i>
                    Back to Checkout
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Razorpay Script -->
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const rzpButton = document.getElementById('rzp-button');
    const loading = document.getElementById('loading');
    
    rzpButton.addEventListener('click', function() {
        // Show loading
        rzpButton.style.display = 'none';
        loading.style.display = 'block';
        
        // Create Razorpay order
        fetch('ajax/create_razorpay_order.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                order_id: <?php echo $orderId; ?>,
                amount: <?php echo $order['total_amount'] * 100; ?> // Razorpay expects amount in paise
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Initialize Razorpay
                const options = {
                    key: data.key_id, // Your Razorpay Key ID
                    amount: data.amount,
                    currency: data.currency,
                    name: 'EverythingB2C',
                    description: 'Order #<?php echo $order['tracking_id']; ?>',
                    order_id: data.razorpay_order_id,
                    handler: function(response) {
                        // Payment successful
                        window.location.href = 'ajax/verify_payment.php?order_id=<?php echo $orderId; ?>&payment_id=' + response.razorpay_payment_id + '&signature=' + response.razorpay_signature;
                    },
                    prefill: {
                        name: '<?php echo htmlspecialchars($order['customer_name']); ?>',
                        email: '<?php echo htmlspecialchars($order['customer_email']); ?>',
                        contact: '<?php echo htmlspecialchars($order['customer_phone']); ?>'
                    },
                    theme: {
                        color: '#3399cc'
                    }
                };
                
                const rzp = new Razorpay(options);
                rzp.open();
            } else {
                alert('Error creating payment order: ' + data.message);
                rzpButton.style.display = 'block';
                loading.style.display = 'none';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error processing payment. Please try again.');
            rzpButton.style.display = 'block';
            loading.style.display = 'none';
        });
    });
});
</script>

<?php include 'includes/footer.php'; ?>
