<?php
ob_start(); // Start output buffering to prevent accidental output before JSON
if (session_status() === PHP_SESSION_NONE) session_start();
require_once 'includes/functions.php';
require_once 'includes/gst_shipping_functions.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Force login
if (!isLoggedIn()) {
    if (
        isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
        strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'
    ) {
        ob_clean();
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Session expired. Please log in again.']);
        exit;
    } else {
        $_SESSION['redirect_after_login'] = 'checkout.php';
        header('Location: login.php');
        exit;
    }
}

$userId = $_SESSION['user_id'];

// Handle new address form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_address'])) {
    $data = [
        'name' => sanitizeInput($_POST['name']),
        'phone' => sanitizeInput($_POST['phone']),
        'pincode' => sanitizeInput($_POST['pincode']),
        'address_line1' => sanitizeInput($_POST['address_line1']),
        'address_line2' => sanitizeInput($_POST['address_line2']),
        'city' => sanitizeInput($_POST['city']),
        'state' => sanitizeInput($_POST['state']),
        'is_default' => isset($_POST['is_default']) ? 1 : 0
    ];
    addUserAddress($userId, $data);
    if ($data['is_default']) setDefaultAddress($userId, $pdo->lastInsertId());
    header('Location: checkout.php');
    exit;
}

// Handle edit address form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_address'])) {
    $addressId = intval($_POST['address_id']);
    $data = [
        'name' => sanitizeInput($_POST['name']),
        'phone' => sanitizeInput($_POST['phone']),
        'pincode' => sanitizeInput($_POST['pincode']),
        'address_line1' => sanitizeInput($_POST['address_line1']),
        'address_line2' => sanitizeInput($_POST['address_line2']),
        'city' => sanitizeInput($_POST['city']),
        'state' => sanitizeInput($_POST['state']),
        'is_default' => isset($_POST['is_default']) ? 1 : 0
    ];
    updateUserAddress($userId, $addressId, $data);
    if ($data['is_default']) setDefaultAddress($userId, $addressId);
    header('Location: checkout.php');
    exit;
}

// Handle delete address
if (isset($_GET['delete_address'])) {
    $addressId = intval($_GET['delete_address']);
    deleteUserAddress($userId, $addressId);
    header('Location: checkout.php');
    exit;
}

// Handle order placement
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_order'])) {
    file_put_contents('debug_checkout.log', date('Y-m-d H:i:s') . "\n" . print_r($_POST, true) . "\n\n", FILE_APPEND);
    $addressId = intval($_POST['selected_address']);
    $paymentMethod = $_POST['payment_method'];
    $gstNumber = !empty($_POST['gst_number']) ? sanitizeInput($_POST['gst_number']) : null;
    $companyName = !empty($_POST['company_name']) ? sanitizeInput($_POST['company_name']) : null;
    $isBusinessPurchase = isset($_POST['is_business']) ? true : false;
    $cartItems = getCartItems($userId);
    if (!$addressId) {
        $error_message = 'Please select a delivery address.';
    } else {
        $result = createOrder($userId, $addressId, $paymentMethod, $gstNumber, $companyName, $isBusinessPurchase);
        if ($result['success']) {
            $order = getOrderById($result['order_id']);
            if (
                isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
                strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'
            ) {
                // AJAX request: return JSON for Razorpay
                // Clean output buffer and suppress errors
                $debug_output = ob_get_contents();
                if (!empty($debug_output)) {
                    file_put_contents(__DIR__ . '/ajax_debug.log', date('Y-m-d H:i:s') . "\n" . $debug_output . "\n\n", FILE_APPEND);
                }
                ob_clean();
                error_reporting(0);
                ini_set('display_errors', 0);
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => true,
                    'order_id' => $order['id'],
                    'amount' => (int)round($order['total_amount'] * 100), // in paise
                    'tracking_id' => $order['tracking_id'] ?? $order['order_number'],
                    'customer_name' => $user['name'],
                    'customer_email' => $user['email'],
                    'customer_phone' => $user['phone'],
                ]);
                exit;
            } else {
                if ($paymentMethod === 'razorpay') {
                    $_SESSION['pending_order_id'] = $result['order_id'];
                    header('Location: process_payment.php?order_id=' . $result['order_id']);
                    exit;
                } else {
                    header('Location: order_success.php?order_id=' . $result['order_id']);
                    exit;
                }
            }
        } else {
            if (
                isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
                strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'
            ) {
                $debug_output = ob_get_contents();
                if (!empty($debug_output)) {
                    file_put_contents(__DIR__ . '/ajax_debug.log', date('Y-m-d H:i:s') . "\n" . $debug_output . "\n\n", FILE_APPEND);
                }
                ob_clean();
                error_reporting(0);
                ini_set('display_errors', 0);
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => $result['message']]);
                exit;
            } else {
                $error_message = 'Error creating order: ' . $result['message'];
            }
        }
    }
}

// Handle set default address
if (isset($_GET['set_default'])) {
    setDefaultAddress($userId, (int)$_GET['set_default']);
    header('Location: checkout.php');
    exit;
}

$pageTitle = 'Checkout';
include 'includes/header.php';

$addresses = getUserAddresses($userId);
$defaultAddress = getDefaultAddress($userId);
$cartItems = getCartItems($userId);

// Use selected address or default for GST calculation
$selectedAddress = $defaultAddress;
if (isset($_POST['selected_address'])) {
    foreach ($addresses as $addr) {
        if ($addr['id'] == $_POST['selected_address']) {
            $selectedAddress = $addr;
            break;
        }
    }
}
$delivery_state = $selectedAddress['state'] ?? 'Maharashtra';
$delivery_city = $selectedAddress['city'] ?? null;
$delivery_pincode = $selectedAddress['pincode'] ?? null;
$orderTotals = calculateOrderTotal($cartItems, $delivery_state, $delivery_city, $delivery_pincode);
// Patch: Add sgst_total, cgst_total, igst_total, total_gst for template compatibility
$orderTotals['sgst_total'] = 0;
$orderTotals['cgst_total'] = 0;
$orderTotals['igst_total'] = 0;
$orderTotals['total_gst'] = 0;
if (!empty($orderTotals['gst_breakdown'])) {
    foreach ($orderTotals['gst_breakdown'] as $gst) {
        $orderTotals['sgst_total'] += $gst['sgst'];
        $orderTotals['cgst_total'] += $gst['cgst'];
        $orderTotals['igst_total'] += $gst['igst'];
        $orderTotals['total_gst'] += $gst['gst_amount'];
    }
}
$mrp = 0; $savings = 0; $delivery = 49; $count = 0;
foreach ($cartItems as $item) {
    $mrp += $item['mrp'] * $item['quantity'];
    $savings += ($item['mrp'] - $item['selling_price']) * $item['quantity'];
    $count += $item['quantity'];
}
if ($orderTotals['total'] >= 999) {
    $delivery = 0;
}
$pay = $orderTotals['total'] + $delivery;
?>

<style>
.address-actions {
    margin-top: 8px;
    padding-top: 8px;
    border-top: 1px solid #eee;
}

.address-actions .btn {
    margin-right: 5px;
    font-size: 0.8rem;
    padding: 2px 8px;
}

.form-check-label {
    cursor: pointer;
    padding: 8px;
    border-radius: 4px;
    transition: background-color 0.2s;
    border:1px solid #000;
}

.form-check-label:hover {
    background-color: #f8f9fa;
}

.form-check-input:checked + .form-check-label {
    background-color: #e3f2fd;
    border: 1px solid #2196f3;
}

.modal-dialog {
    max-width: 600px;
}

.btn-outline-primary {
    border-color: #007bff;
    color: #007bff;
}

.btn-outline-primary:hover {
    background-color: #007bff;
    border-color: #007bff;
    color: white;
}

.btn-outline-danger {
    border-color: #dc3545;
    color: #dc3545;
}

.btn-outline-danger:hover {
    background-color: #dc3545;
    border-color: #dc3545;
    color: white;
}
</style>

<div class="page-banner" style="background: url('asset/images/internalpage-bg.webp') center/cover no-repeat; min-height: 240px; display: flex; align-items: center;">
    <div class="container">
        <h2 style="color: #fff; font-size: 2rem; font-weight: bold; text-shadow: 0 2px 8px rgba(0,0,0,0.3); margin: 0; padding: 32px 0;">
            Checkout
        </h2>
    </div>
</div>

<?php if (isset($error_message)): ?>
<div class="container mt-3">
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-triangle me-2"></i>
        <?php echo htmlspecialchars($error_message); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
</div>
<?php endif; ?>

<?php if (isset($_GET['error'])): ?>
<div class="container mt-3">
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-triangle me-2"></i>
        <?php 
        $error = $_GET['error'];
        switch($error) {
            case 'invalid_payment':
                echo 'Invalid payment information. Please try again.';
                break;
            case 'payment_failed':
                echo 'Payment failed. Please try again or choose a different payment method.';
                break;
            case 'payment_error':
                echo 'An error occurred during payment processing. Please try again.';
                break;
            default:
                echo 'An error occurred. Please try again.';
        }
        ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
</div>
<?php endif; ?>

<?php if ($orderTotals['total'] < 150): ?>
  <div class="alert alert-danger text-center" id="minOrderAlert">
    Minimum order amount is ₹150. Please add more items to your cart to proceed.
  </div>
<?php endif; ?>

<div class="container my-5">
  <div class="row g-4">
    <div class="col-lg-8">
      <form method="post" id="checkoutForm">
        <input type="hidden" name="place_order" value="1">
        <div class="checkout-card mb-4">
          <div class="card-body">
            <h5 class="mb-3"><b>1</b> Select a delivery mode</h5>
            <div class="d-flex align-items-center mb-4">
              <input type="radio" checked disabled class="me-2">
              <span style="font-size:1.5rem; margin-right:8px;">🏠</span>
              <b>Home Delivery</b>
              <span class="badge bg-light text-primary ms-2">Flat ₹<?php echo $delivery; ?>.00</span>
            </div>
            <h6 class="mb-2">Saved addresses</h6>
            <?php if ($addresses): ?>
              <?php foreach ($addresses as $addr): ?>
                <div class="form-check mb-2">
                  <input class="form-check-input" type="radio" name="selected_address" id="addr<?php echo $addr['id']; ?>" value="<?php echo $addr['id']; ?>" <?php if ($addr['is_default']) echo 'checked'; ?> required>
                  <label class="form-check-label" for="addr<?php echo $addr['id']; ?>">
                    <b><?php echo htmlspecialchars($addr['name']); ?></b>, <?php echo htmlspecialchars($addr['address_line1']); ?><?php if ($addr['address_line2']) echo ', ' . htmlspecialchars($addr['address_line2']); ?>, <?php echo htmlspecialchars($addr['city']); ?>, <?php echo htmlspecialchars($addr['state']); ?>, <?php echo htmlspecialchars($addr['pincode']); ?>, Mob: <?php echo htmlspecialchars($addr['phone']); ?>
                  </label>
                  <div class="address-actions">
                    <?php if (!$addr['is_default']): ?>
                      <a href="?set_default=<?php echo $addr['id']; ?>" class="btn btn-link btn-sm">Set as default</a>
                    <?php endif; ?>
                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="editAddress(<?php echo $addr['id']; ?>)">
                      <i class="fas fa-edit"></i> Edit
                    </button>
                    <a href="?delete_address=<?php echo $addr['id']; ?>" class="btn btn-outline-danger btn-sm" onclick="return confirm('Are you sure you want to delete this address?')">
                      <i class="fas fa-trash"></i> Delete
                    </a>
                  </div>
                </div>
              <?php endforeach; ?>
              <!-- Add New Address Toggle Button -->
              <button type="button" class="btn btn-outline-success mt-3 mb-2" data-bs-toggle="collapse" data-bs-target="#addAddressForm" aria-expanded="false" aria-controls="addAddressForm">
                + Add New Address
              </button>
            <?php endif; ?>
            <?php if (!$addresses): ?>
              <div class="alert alert-info">No saved addresses. Please add one below.</div>
            <?php endif; ?>
            <hr>
          </div>
        </div>
        <!-- GST Information Section -->
        <div class="checkout-card mb-4">
          <div class="card-body">
            <h5 class="mb-3"><b>2</b> GST Information (Optional)</h5>
            <div class="row g-3">
              <div class="col-md-6">
                <label for="gst_number" class="form-label">GST Number</label>
                <input type="text" class="form-control" id="gst_number" name="gst_number" placeholder="Enter GST Number (Optional)" pattern="[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}" title="Please enter a valid GST number">
                <div class="form-text">Format: 22AAAAA0000A1Z5</div>
              </div>
              <div class="col-md-6">
                <label for="company_name" class="form-label">Company Name</label>
                <input type="text" class="form-control" id="company_name" name="company_name" placeholder="Enter Company Name (Optional)">
              </div>
              <div class="col-12">
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" id="is_business" name="is_business">
                  <label class="form-check-label" for="is_business">
                    This is a business purchase
                  </label>
                </div>
              </div>
            </div>
          </div>
        </div>
      </form>
      <!-- Add New Address Form (completely outside main form) -->
      <div class="collapse<?php if (!$addresses) echo ' show'; ?>" id="addAddressForm">
        <form method="post">
          <input type="hidden" name="add_address" value="1">
          <div class="row g-2 mt-2">
            <div class="col-md-6">
              <input type="text" name="name" class="form-control" placeholder="Full Name" required>
            </div>
            <div class="col-md-6">
              <input type="text" name="phone" class="form-control" placeholder="Phone Number" required>
            </div>
            <div class="col-md-4">
              <input type="text" name="pincode" class="form-control" placeholder="PIN Code" required>
            </div>
            <div class="col-md-8">
              <input type="text" name="address_line1" class="form-control" placeholder="Address Line 1" required>
            </div>
            <div class="col-md-12">
              <input type="text" name="address_line2" class="form-control" placeholder="Address Line 2 (optional)">
            </div>
            <div class="col-md-6">
              <input type="text" name="city" class="form-control" placeholder="City" required>
            </div>
            <div class="col-md-6">
              <input type="text" name="state" class="form-control" placeholder="State" required>
            </div>
            <div class="col-12">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="is_default" id="is_default">
                <label class="form-check-label" for="is_default">Set as default address</label>
              </div>
            </div>
            <div class="col-12">
              <button type="submit" class="btn btn-success">Save Address</button>
            </div>
          </div>
        </form>
      </div>
    </div>
    <div class="col-lg-4">
      <div class="checkout-card">
        <div class="card-body">
          <h5 class="mb-3">Bill Summary <span class="text-muted" style="font-size:1rem;">(<?php echo $count; ?> products)</span></h5>
          <div class="d-flex justify-content-between mb-2"><span>MRP</span><span>₹<?php echo number_format($mrp,2); ?></span></div>
          <div class="d-flex justify-content-between mb-2"><span>Subtotal</span><span>₹<?php echo number_format($orderTotals['subtotal'],2); ?></span></div>
          <div class="d-flex justify-content-between mb-2"><span>Delivery charge</span><span>₹<?php echo $orderTotals['shipping_charge']; ?></span></div>
          <div class="d-flex justify-content-between mb-2 bg-light p-2 rounded"><span class="text-success"><b>Total Savings</b></span><span class="text-success">₹<?php echo number_format($savings,2); ?></span></div>
          <!-- GST Breakdown -->
          <div class="border-top pt-2 mt-2">
            <small class="text-muted">GST Breakdown:</small>
            <?php if ($orderTotals['sgst_total'] > 0 || $orderTotals['cgst_total'] > 0): ?>
              <div class="d-flex justify-content-between mb-1"><small>SGST (<?php echo number_format($orderTotals['sgst_total'] > 0 ? ($orderTotals['sgst_total'] / $orderTotals['subtotal']) * 100 : 0, 1); ?>%)</small><small>₹<?php echo number_format($orderTotals['sgst_total'],2); ?></small></div>
              <div class="d-flex justify-content-between mb-1"><small>CGST (<?php echo number_format($orderTotals['cgst_total'] > 0 ? ($orderTotals['cgst_total'] / $orderTotals['subtotal']) * 100 : 0, 1); ?>%)</small><small>₹<?php echo number_format($orderTotals['cgst_total'],2); ?></small></div>
            <?php endif; ?>
            <?php if ($orderTotals['igst_total'] > 0): ?>
              <div class="d-flex justify-content-between mb-1"><small>IGST (<?php echo number_format($orderTotals['igst_total'] > 0 ? ($orderTotals['igst_total'] / $orderTotals['subtotal']) * 100 : 0, 1); ?>%)</small><small>₹<?php echo number_format($orderTotals['igst_total'],2); ?></small></div>
            <?php endif; ?>
            <div class="d-flex justify-content-between"><small><strong>Total GST</strong></small><small><strong>₹<?php echo number_format($orderTotals['total_gst'],2); ?></strong></small></div>
          </div>
          <div class="d-flex justify-content-between mb-2 bg-primary bg-opacity-10 p-2 rounded"><span><b>Total Amount to Pay</b></span><span id="order-total-amount"><b>₹<?php echo number_format($orderTotals['total'],2); ?></b></span></div>
          <!-- Payment Method Section -->
          <div class="checkout-card mb-4">
            <div class="card-body">
              <h6 class="mb-2">Select Payment Method</h6>
              <div class="form-check mb-2">
                <input class="form-check-input" type="radio" name="payment_method" id="cod" value="cod" checked form="checkoutForm">
                <label class="form-check-label" for="cod">
                  <i class="fas fa-money-bill-wave me-2"></i>
                  <strong>Cash on Delivery</strong>
                  <br><small class="text-muted">Pay after you get your order </small>
                </label>
              </div>
              <div class="form-check mb-2">
                <input class="form-check-input" type="radio" name="payment_method" id="razorpay" value="razorpay" form="checkoutForm">
                <label class="form-check-label" for="razorpay">
                  <i class="fas fa-credit-card me-2"></i>
                  <strong>Online Payment</strong>
                  <br><small class="text-muted">Pay via card, UPI or netbanking</small>
                </label>
              </div>
            </div>
          </div>
          <!-- Place Order button -->
          <div class="d-grid mb-3">
            <button type="submit" class="place-order-btn btn btn-primary w-100 mt-3" form="checkoutForm" <?php if ($orderTotals['total'] < 150) echo 'disabled'; ?>>
              <i class="fas fa-shopping-cart"></i> Place Order
            </button>
          </div>
          <div class="text-center mt-3">
            <a href="cart.php" class="btn btn-outline-secondary">Back to Cart</a>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Edit Address Modal -->
    <div class="modal fade" id="editAddressModal" tabindex="-1" aria-labelledby="editAddressModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="editAddressModalLabel">Edit Address</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <form method="post" id="editAddressForm">
            <input type="hidden" name="edit_address" value="1">
            <input type="hidden" name="address_id" id="edit_address_id">
            <div class="modal-body">
              <div class="row g-3">
                <div class="col-md-6">
                  <label for="edit_name" class="form-label">Full Name *</label>
                  <input type="text" name="name" id="edit_name" class="form-control" required>
                </div>
                <div class="col-md-6">
                  <label for="edit_phone" class="form-label">Phone Number *</label>
                  <input type="text" name="phone" id="edit_phone" class="form-control" required>
                </div>
                <div class="col-md-4">
                  <label for="edit_pincode" class="form-label">PIN Code *</label>
                  <input type="text" name="pincode" id="edit_pincode" class="form-control" required>
                </div>
                <div class="col-md-8">
                  <label for="edit_address_line1" class="form-label">Address Line 1 *</label>
                  <input type="text" name="address_line1" id="edit_address_line1" class="form-control" required>
                </div>
                <div class="col-md-12">
                  <label for="edit_address_line2" class="form-label">Address Line 2 (optional)</label>
                  <input type="text" name="address_line2" id="edit_address_line2" class="form-control">
                </div>
                <div class="col-md-6">
                  <label for="edit_city" class="form-label">City *</label>
                  <input type="text" name="city" id="edit_city" class="form-control" required>
                </div>
                <div class="col-md-6">
                  <label for="edit_state" class="form-label">State *</label>
                  <input type="text" name="state" id="edit_state" class="form-control" required>
                </div>
                <div class="col-12">
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="is_default" id="edit_is_default">
                    <label class="form-check-label" for="edit_is_default">Set as default address</label>
                  </div>
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
              <button type="submit" class="btn btn-primary">Update Address</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Thank You Modal -->
<div class="modal fade" id="thankYouModal" tabindex="-1" aria-labelledby="thankYouModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content text-center">
      <div class="modal-header border-0">
        <h5 class="modal-title w-100" id="thankYouModalLabel">Thank You!</h5>
      </div>
      <div class="modal-body">
        <i class="fas fa-check-circle text-success" style="font-size: 3rem;"></i>
        <p class="lead mt-3">Your order has been placed successfully.</p>
        <div class="d-grid gap-2 mt-4">
          <a href="myaccount.php" class="btn btn-primary">My Account</a>
          <a href="index.php" class="btn btn-outline-secondary">Continue Shopping</a>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include 'includes/footer.php'; ?>

<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // GST Number validation
    const gstInput = document.getElementById('gst_number');
    const companyInput = document.getElementById('company_name');
    const businessCheckbox = document.getElementById('is_business');
    const checkoutForm = document.getElementById('checkoutForm');
    const placeOrderBtn = document.querySelector('button[form="checkoutForm"]');

    // GST number format validation
    gstInput.addEventListener('input', function() {
        const value = this.value.toUpperCase();
        this.value = value;
        const gstPattern = /^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}$/;
        if (value.length > 0 && !gstPattern.test(value)) {
            this.setCustomValidity('Please enter a valid GST number in format: 22AAAAA0000A1Z5');
        } else {
            this.setCustomValidity('');
        }
    });
    businessCheckbox.addEventListener('change', function() {
        if (this.checked) {
            gstInput.required = true;
            companyInput.required = true;
            gstInput.style.borderColor = '#dc3545';
            companyInput.style.borderColor = '#dc3545';
        } else {
            gstInput.required = false;
            companyInput.required = false;
            gstInput.style.borderColor = '';
            companyInput.style.borderColor = '';
        }
    });

    // Universal AJAX order placement for both COD and Razorpay
    checkoutForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const paymentMethod = document.querySelector('input[name="payment_method"]:checked').value;
        // Gather form data
        const formData = new FormData(checkoutForm);
        formData.append('place_order', '1');
        // AJAX to create order
        fetch('checkout.php', {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.order_id) {
                if (paymentMethod === 'razorpay' && data.amount) {
                    // Create Razorpay order
                    fetch('ajax/create_razorpay_order.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ order_id: data.order_id, amount: data.amount })
                    })
                    .then(response => response.json())
                    .then(rzpData => {
                        if (rzpData.success) {
                            const options = {
                                key: rzpData.key_id,
                                amount: rzpData.amount,
                                currency: rzpData.currency,
                                name: 'EverythingB2C',
                                description: 'Order #' + data.tracking_id,
                                order_id: rzpData.razorpay_order_id,
                                handler: function(response) {
                                    // Payment successful, verify
                                    fetch('ajax/verify_payment.php?order_id=' + data.order_id + '&payment_id=' + response.razorpay_payment_id + '&signature=' + response.razorpay_signature)
                                        .then(res => {
                                            // Show thank you modal
                                            const thankYouModal = new bootstrap.Modal(document.getElementById('thankYouModal'));
                                            thankYouModal.show();
                                        })
                                        .catch(() => {
                                            alert('Payment verified, but an error occurred. Please check your order in My Account.');
                                        });
                                },
                                prefill: {
                                    name: data.customer_name || '',
                                    email: data.customer_email || '',
                                    contact: data.customer_phone || ''
                                },
                                theme: { color: '#3399cc' }
                            };
                            const rzp = new Razorpay(options);
                            rzp.open();
                        } else {
                            alert('Error creating Razorpay order: ' + rzpData.message);
                        }
                    });
                } else {
                    // COD or other payment method: show thank you modal
                    const thankYouModal = new bootstrap.Modal(document.getElementById('thankYouModal'));
                    thankYouModal.show();
                }
            } else {
                alert('Error creating order: ' + (data.message || 'Unknown error'));
                // Optionally fallback to redirect
                // window.location.href = 'order_success.php?order_id=' + (data.order_id || '');
            }
        })
        .catch(error => {
            alert('Error processing order: ' + error);
        });
    });

    // Address editing functionality
    function editAddress(addressId) {
        // Fetch address data via AJAX
        fetch(`ajax/get_address.php?id=${addressId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const address = data.address;
                    
                    // Populate the edit form
                    document.getElementById('edit_address_id').value = address.id;
                    document.getElementById('edit_name').value = address.name;
                    document.getElementById('edit_phone').value = address.phone;
                    document.getElementById('edit_pincode').value = address.pincode;
                    document.getElementById('edit_address_line1').value = address.address_line1;
                    document.getElementById('edit_address_line2').value = address.address_line2 || '';
                    document.getElementById('edit_city').value = address.city;
                    document.getElementById('edit_state').value = address.state;
                    document.getElementById('edit_is_default').checked = address.is_default == 1;
                    
                    // Show the modal
                    const modal = new bootstrap.Modal(document.getElementById('editAddressModal'));
                    modal.show();
                } else {
                    alert('Error loading address: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error loading address data');
            });
    }

    // Minimum order JS check (in case of dynamic total updates in the future)
    const minOrderAlert = document.getElementById('minOrderAlert');
    function checkMinOrder() {
        const total = parseFloat(document.getElementById('order-total-amount')?.textContent?.replace(/[^\d.]/g, '') || '0');
        if (total < 150) {
            placeOrderBtn.disabled = true;
            if (minOrderAlert) minOrderAlert.style.display = '';
        } else {
            placeOrderBtn.disabled = false;
            if (minOrderAlert) minOrderAlert.style.display = 'none';
        }
    }
    checkMinOrder();
    // If you have dynamic total updates, call checkMinOrder() after update
});
</script> 