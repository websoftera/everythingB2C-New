<?php
session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'domain' => '',
    'secure' => false,
    'httponly' => false,
    'samesite' => 'Lax'
]);
ob_start();
if (session_status() === PHP_SESSION_NONE) session_start();
require_once 'includes/functions.php';
require_once 'includes/gst_shipping_functions.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!isLoggedIn()) {
    $_SESSION['redirect_after_login'] = 'checkout.php';
    header('Location: login.php');
    exit;
}

$userId = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['set_default']) && isset($_POST['address_id'])) {
        $addressId = intval($_POST['address_id']);
        setDefaultAddress($userId, $addressId);
        header('Location: checkout.php');
        exit;
    }
    if (isset($_POST['add_address'])) {
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
    if (isset($_POST['edit_address'])) {
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
    if (isset($_POST['delete_address'])) {
        $addressId = intval($_POST['address_id']);
        deleteUserAddress($userId, $addressId);
        header('Location: checkout.php');
        exit;
    }
    // === PLACE ORDER HANDLER ===
    if (isset($_POST['place_order'])) {
        $selected_address_id = isset($_POST['selected_address']) ? intval($_POST['selected_address']) : 0;
        $payment_method = isset($_POST['payment_method']) ? $_POST['payment_method'] : '';
        $gst_number = isset($_POST['gst_number']) ? trim($_POST['gst_number']) : '';
        $upi_transaction_id = null;
        $upi_screenshot_path = null;
        if ($payment_method === 'direct_payment') {
            $upi_transaction_id = isset($_POST['upi_transaction_id']) ? trim($_POST['upi_transaction_id']) : null;
            // Handle file upload
            if (isset($_FILES['upi_screenshot']) && $_FILES['upi_screenshot']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = 'uploads/payments/';
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
                $ext = pathinfo($_FILES['upi_screenshot']['name'], PATHINFO_EXTENSION);
                $filename = 'upi_' . time() . '_' . rand(1000,9999) . '.' . $ext;
                $targetPath = $uploadDir . $filename;
                if (move_uploaded_file($_FILES['upi_screenshot']['tmp_name'], $targetPath)) {
                    $upi_screenshot_path = $targetPath;
                }
            }
        }
        if (!$selected_address_id || !$payment_method) {
            $error_message = 'Please select a delivery address and payment method.';
        } else {
            $cartItems = getCartItems($userId);
            if (empty($cartItems)) {
                $error_message = 'Your cart is empty.';
            } else {
                $result = createOrder($userId, $selected_address_id, $payment_method, $gst_number, null, false, $upi_transaction_id, $upi_screenshot_path);
                if ($result && !empty($result['success'])) {
                    $orderPlaced = true;
                    $placedOrderId = $result['order_id'];
                    if ($payment_method === 'razorpay') {
                        header('Location: process_payment.php?order_id=' . $placedOrderId);
                        exit;
                    }
                } else {
                    $error_message = isset($result['message']) ? $result['message'] : 'Order could not be placed.';
                }
            }
        }
    }
}

$pageTitle = 'Checkout';
include 'includes/header.php';

$addresses = getUserAddresses($userId);
$defaultAddress = getDefaultAddress($userId);
$cartItems = getCartItems($userId);

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
$mrp = 0; $savings = 0; $count = 0;
foreach ($cartItems as $item) {
    $mrp += $item['mrp'] * $item['quantity'];
    $savings += ($item['mrp'] - $item['selling_price']) * $item['quantity'];
    $count += $item['quantity'];
}
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
        <?php if (isset($result)): ?>
            <br><strong>Order Result Debug:</strong>
            <pre style="max-height:200px;overflow:auto;font-size:0.95em;"><?php echo htmlspecialchars(print_r($result, true)); ?></pre>
        <?php endif; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
</div>
<?php endif; ?>

<?php if ($orderTotals['total'] < 150): ?>
  <div class="alert alert-danger text-center" id="minOrderAlert">
    Minimum order amount is ₹150. Please add more items to your cart to proceed.
  </div>
<?php endif; ?>

<!-- === ADDRESS SECTION (OUTSIDE ORDER FORM, RESTORED) === -->
<div class="container my-5">
  <div class="row g-4">
    <!-- LEFT COLUMN: Address and GST (no form) -->
    <div class="col-lg-8">
      <div class="checkout-card mb-4">
        <div class="card-body">
          <h5 class="mb-3"><b>1</b> Select a delivery mode</h5>
          <div class="d-flex align-items-center mb-4">
            <input type="radio" checked disabled class="me-2">
            <span style="font-size:1.5rem; margin-right:8px;">🏠</span>
            <b>Home Delivery</b>
            <span class="badge bg-light text-primary ms-2">Flat ₹<?php echo $orderTotals['shipping_charge']; ?>.00</span>
          </div>
          <h6 class="mb-2">Saved addresses</h6>
          <?php if ($addresses): ?>
            <?php foreach ($addresses as $addr): ?>
              <div class="form-check mb-2">
                <input class="form-check-input address-radio" type="radio" name="selected_address_left" id="addr<?php echo $addr['id']; ?>" value="<?php echo $addr['id']; ?>" <?php if ($addr['is_default']) echo 'checked'; ?> required>
                <label class="form-check-label" for="addr<?php echo $addr['id']; ?>">
                  <b><?php echo htmlspecialchars($addr['name']); ?></b>, <?php echo htmlspecialchars($addr['address_line1']); ?><?php if ($addr['address_line2']) echo ', ' . htmlspecialchars($addr['address_line2']); ?>, <?php echo htmlspecialchars($addr['city']); ?>, <?php echo htmlspecialchars($addr['state']); ?>, <?php echo htmlspecialchars($addr['pincode']); ?>, Mob: <?php echo htmlspecialchars($addr['phone']); ?>
                </label>
                <div class="address-actions">
                  <?php if (!$addr['is_default']): ?>
                    <form method="post" style="display:inline;">
                      <input type="hidden" name="set_default" value="1">
                      <input type="hidden" name="address_id" value="<?php echo $addr['id']; ?>">
                      <button type="submit" class="btn btn-link btn-sm">Set as default</button>
                    </form>
                  <?php endif; ?>
                  <button type="button" class="btn btn-outline-primary btn-sm" onclick="editAddress(<?php echo $addr['id']; ?>)">
                    <i class="fas fa-edit"></i> Edit
                  </button>
                  <form method="post" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this address?');">
                    <input type="hidden" name="delete_address" value="<?php echo $addr['id']; ?>">
                    <button type="submit" class="btn btn-outline-danger btn-sm"><i class="fas fa-trash"></i> Delete</button>
                  </form>
                </div>
              </div>
            <?php endforeach; ?>
          <?php else: ?>
            <div class="alert alert-info">No saved addresses. Please add one below.</div>
          <?php endif; ?>
          <!-- Add New Address Form (collapsible, outside order form) -->
          <div class="mt-3 mb-2">
            <button type="button" class="btn btn-outline-success" data-bs-toggle="collapse" data-bs-target="#addAddressForm" aria-expanded="false" aria-controls="addAddressForm">
              + Add New Address
            </button>
          </div>
          <div class="collapse<?php if (!$addresses) echo ' show'; ?> mt-2" id="addAddressForm">
            <form method="post" id="addAddressFormReal">
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
          <!-- GST Number Input (NEW) -->
          <div class="mt-4 mb-2">
            <div class="row align-items-center">
              <div class="col-md-6">
                <label for="gst_number_left" class="form-label">GST Number (optional, for business invoice)</label>
                <input type="text" id="gst_number_left" class="form-control" maxlength="20" pattern="[0-9A-Z]{15}" title="Enter a valid 15-character GSTIN" value="<?php echo isset($_POST['gst_number']) ? htmlspecialchars($_POST['gst_number']) : (isset($_SESSION['gst_number']) ? htmlspecialchars($_SESSION['gst_number']) : ''); ?>" autocomplete="off">
                <small class="text-muted">If you want a business invoice, enter your 15-digit GSTIN here.</small>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- RIGHT COLUMN: Bill summary, payment, and Place Order (form) -->
    <div class="col-lg-4">
      <form id="checkoutForm" method="post" enctype="multipart/form-data">
        <!-- Hidden fields for address and GST -->
        <input type="hidden" name="place_order" value="1">
        <input type="hidden" name="selected_address" id="selected_address_hidden">
        <input type="hidden" name="gst_number" id="gst_number_hidden">
        <input type="hidden" name="upi_transaction_id" id="upi_transaction_id_hidden">
        <input type="hidden" name="user_upi_id" id="user_upi_id_hidden">
        <!-- UPI Screenshot file input (now inside form) -->
        <div id="upiScreenshotFormField" style="display:none;">
          <label for="upi_screenshot" class="form-label">Upload Payment Screenshot (optional)</label>
          <input type="file" class="form-control" id="upi_screenshot" name="upi_screenshot" accept="image/*">
        </div>
        <div class="checkout-card">
          <div class="card-body">
            <h5 class="mb-3">Bill Summary <span class="text-muted" style="font-size:1rem;">(<?php echo $count; ?> products)</span></h5>
            <!-- Per-product breakdown -->
            <!-- Removed per-product table as requested -->
            <div class="mb-2"></div>
            <div class="d-flex justify-content-between mb-2"><span>Total MRP</span><span>₹<?php echo number_format($mrp,2); ?></span></div>
            <div class="d-flex justify-content-between mb-2"><span>You Pay</span><span>₹<?php echo number_format($orderTotals['subtotal'],2); ?></span></div>
            <div class="d-flex justify-content-between mb-2"><span>Delivery charge</span><span id="cart-shipping">₹<?php echo $orderTotals['shipping_charge']; ?></span></div>
            <div class="d-flex justify-content-between mb-2"><span>Shipping Zone</span><span id="cart-shipping-zone"><?php echo htmlspecialchars($orderTotals['shipping_zone_name'] ?? ''); ?></span></div>
            <div class="d-flex justify-content-between mb-2 bg-light p-2 rounded"><span class="text-success"><b>Total Savings</b></span><span class="text-success">₹<?php echo number_format($savings,2); ?></span></div>
            <!-- GST Breakdown -->
            <div class="border-top pt-2 mt-2" id="gst-breakdown-section">
              <!-- This content will be fully replaced by JS on address change -->
              <small class="text-muted">TAX Breakdown:</small>
              <?php if ($orderTotals['igst_total'] > 0): ?>
                <div class="d-flex justify-content-between mb-1"><small>IGST (<?php echo number_format($orderTotals['igst_total'] > 0 ? ($orderTotals['igst_total'] / $orderTotals['subtotal']) * 100 : 0, 1); ?>%)</small><small>₹<?php echo number_format($orderTotals['igst_total'],2); ?></small></div>
              <?php elseif ($orderTotals['sgst_total'] > 0 || $orderTotals['cgst_total'] > 0): ?>
                <div class="d-flex justify-content-between mb-1"><small>SGST (<?php echo number_format($orderTotals['sgst_total'] > 0 ? ($orderTotals['sgst_total'] / $orderTotals['subtotal']) * 100 : 0, 1); ?>%)</small><small>₹<?php echo number_format($orderTotals['sgst_total'],2); ?></small></div>
                <div class="d-flex justify-content-between mb-1"><small>CGST (<?php echo number_format($orderTotals['cgst_total'] > 0 ? ($orderTotals['cgst_total'] / $orderTotals['subtotal']) * 100 : 0, 1); ?>%)</small><small>₹<?php echo number_format($orderTotals['cgst_total'],2); ?></small></div>
              <?php endif; ?>
              <div class="d-flex justify-content-between"><small><strong>Total TAX</strong></small><small><strong>₹<?php echo number_format($orderTotals['total_gst'],2); ?></strong></small></div>
            </div>
            <div class="d-flex justify-content-between mb-2 bg-primary bg-opacity-10 p-2 rounded"><span><b>Total Amount to Pay</b></span><span id="order-total-amount"><b>₹<?php echo number_format($orderTotals['total'],2); ?></b></span></div>
            <!-- Payment Method Section and Place Order Button -->
            <div class="checkout-card mt-3">
              <div class="card-body">
                <h6 class="mb-2">Select Payment Method</h6>
                <div class="form-check mb-2">
                  <input class="form-check-input" type="radio" name="payment_method" id="cod" value="cod" checked>
                  <label class="form-check-label" for="cod">
                    <i class="fas fa-money-bill-wave me-2"></i>
                    <strong>Cash on Delivery</strong>
                    <br><small class="text-muted">Pay after you get your order </small>
                  </label>
                </div>
                <!-- <div class="form-check mb-2">
                  <input class="form-check-input" type="radio" name="payment_method" id="razorpay" value="razorpay">
                  <label class="form-check-label" for="razorpay">
                    <i class="fas fa-credit-card me-2"></i>
                    <strong>Online Payment</strong>
                    <br><small class="text-muted">Pay via card, UPI or netbanking</small>
                  </label>
                </div> -->
                <div class="form-check mb-2">
                  <input class="form-check-input" type="radio" name="payment_method" id="direct_payment" value="direct_payment">
                  <label class="form-check-label" for="direct_payment">
                    <i class="fas fa-qrcode me-2"></i>
                    <strong>Direct Payment</strong>
                    <br><small class="text-muted">Pay via UPI app or QR code</small>
                  </label>
                </div>
                <div id="directPaymentSection" style="display:none; border:1px solid #e3e3e3; border-radius:8px; padding:16px; margin-bottom:16px; background:#f8f9fa;">
                  <div class="mb-2 text-center">
                    <a id="upiPaymentLink" href="#" class="btn btn-success" target="_blank">Pay via UPI App</a>
                    <div class="text-muted mt-1" style="font-size:0.95rem;">(This button works only on mobile UPI apps)</div>
                    <div id="upiQrCode" class="mt-3"></div>
                  </div>
                  <div class="mb-2">
                    <label for="user_upi_id" class="form-label">Your UPI ID</label>
                    <input type="text" class="form-control" id="user_upi_id" name="user_upi_id" placeholder="yourupi@bank" pattern="^[\w.-]+@[\w.-]+$" required>
                    <div class="invalid-feedback">Please enter a valid UPI ID (e.g., yourname@bank).</div>
                  </div>
                  <div class="mb-2 text-muted" style="font-size:0.97rem;">
                    <b>Instructions:</b><br>
                    1. Scan the QR code or click the payment link to pay the total amount.<br>
                    2. After payment, click <b>Continue</b> below.<br>
                    3. On the next step, enter your UPI transaction ID and (optionally) upload a payment screenshot.<br>
                    4. Your order will be placed as 'pending for confirmation' until payment is verified.<br>
                  </div>
                  <div class="d-grid">
                    <button type="button" id="directPaymentContinueBtn" class="btn btn-primary">Continue</button>
                  </div>
                </div>
                <!-- Direct Payment Step 2: Transaction ID and Screenshot (inside form, hidden by default) -->
                <div id="directPaymentDetailsSection" style="display:none; border:1px solid #e3e3e3; border-radius:8px; padding:16px; margin-bottom:16px; background:#f8f9fa; max-width:400px; margin-left:auto; margin-right:auto;">
                  <div class="mb-2">
                    <label for="upi_transaction_id" class="form-label">UPI Transaction ID</label>
                    <input type="text" class="form-control" id="upi_transaction_id" name="upi_transaction_id" required>
                  </div>
                  <div class="mb-2">
                    <label for="upi_screenshot" class="form-label">Upload Payment Screenshot (optional)</label>
                    <input type="file" class="form-control" id="upi_screenshot" name="upi_screenshot" accept="image/*">
                  </div>
                  <div class="d-grid mb-2">
                    <button type="button" id="directPaymentSubmitBtn" class="btn btn-success">Submit Payment Details</button>
                  </div>
                  <div id="directPaymentInfoMsg" class="text-success text-center mb-2" style="display:none;"></div>
                </div>
                <div id="directPaymentInfoMsg" class="text-success text-center mb-2" style="display:none;"></div>
                <div class="d-grid mb-3">
                  <button type="submit" name="place_order" id="placeOrderBtn" class="place-order-btn btn btn-primary w-100 mt-3" <?php if ($orderTotals['total'] < 150) echo 'disabled'; ?>>
                    <i class="fas fa-shopping-cart"></i> Place Order
                  </button>
                </div>
              </div>
            </div>
            <div class="text-center mt-3">
              <a href="cart.php" class="btn btn-outline-secondary">Back to Cart</a>
            </div>
          </div>
        </div>
      </form>
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

<!-- Bootstrap modal for direct payment confirmation -->
<div class="modal fade" id="directPaymentConfirmModal" tabindex="-1" aria-labelledby="directPaymentConfirmModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="directPaymentConfirmModalLabel">Confirm UPI Payment</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        Have you completed the payment via UPI? You will now be asked to enter your transaction ID.
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary" id="directPaymentConfirmYesBtn">Yes, Continue</button>
      </div>
    </div>
  </div>
</div>

<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrious/4.0.2/qrious.min.js"></script>
<script>
// Edit Address Modal logic (fills from PHP array, not AJAX)
function editAddress(addressId) {
  var addresses = <?php echo json_encode($addresses); ?>;
  var address = addresses.find(function(a) { return a.id == addressId; });
  if (!address) return;
  document.getElementById('edit_address_id').value = address.id;
  document.getElementById('edit_name').value = address.name;
  document.getElementById('edit_phone').value = address.phone;
  document.getElementById('edit_pincode').value = address.pincode;
  document.getElementById('edit_address_line1').value = address.address_line1;
  document.getElementById('edit_address_line2').value = address.address_line2 || '';
  document.getElementById('edit_city').value = address.city;
  document.getElementById('edit_state').value = address.state;
  document.getElementById('edit_is_default').checked = address.is_default == 1;
  var modal = new bootstrap.Modal(document.getElementById('editAddressModal'));
  modal.show();
}

// --- Ensure hidden field is always up to date with selected address ---
function updateSelectedAddressHidden() {
  var selectedAddress = document.querySelector('input[name="selected_address_left"]:checked');
  document.getElementById('selected_address_hidden').value = selectedAddress ? selectedAddress.value : '';
}
// Update on page load and whenever address is changed
updateSelectedAddressHidden();
document.querySelectorAll('input[name="selected_address_left"]').forEach(function(radio) {
  radio.addEventListener('change', updateSelectedAddressHidden);
});
// On form submit, update hidden field just before submit
  document.getElementById('checkoutForm').addEventListener('submit', function(e) {
    updateSelectedAddressHidden();
    var gstNumber = document.getElementById('gst_number_left').value;
    document.getElementById('gst_number_hidden').value = gstNumber;
    // --- Razorpay client-side flow ---
    var paymentMethod = document.querySelector('input[name="payment_method"]:checked').value;
    if (paymentMethod === 'razorpay') {
      e.preventDefault();
      // Gather form data
      var formData = new FormData(this);
      var data = {};
      formData.forEach((v, k) => { data[k] = v; });
      // Create Razorpay order via AJAX
      fetch('ajax/initiate_razorpay_checkout.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
      })
      .then(res => res.json())
      .then(function(resp) {
        if (resp.success) {
          var options = {
            key: resp.key_id,
            amount: resp.amount,
            currency: resp.currency,
            name: 'EverythingB2C',
            description: 'Order Payment',
            order_id: resp.razorpay_order_id,
            handler: function(paymentResp) {
              // On payment success, verify payment and create order
              fetch('ajax/verify_payment.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                  temp_order_id: resp.temp_order_id,
                  razorpay_payment_id: paymentResp.razorpay_payment_id,
                  razorpay_order_id: paymentResp.razorpay_order_id,
                  razorpay_signature: paymentResp.razorpay_signature
                })
              })
              .then(r => r.json())
              .then(function(vresp) {
                if (vresp.success) {
                  document.getElementById('thankYouModal').style.display = 'flex';
                  // Add event listener for close/redirect
                  var closeBtn = document.querySelector('#thankYouModal .popup-close');
                  if (closeBtn) {
                    closeBtn.onclick = function() { window.location.href = 'index.php'; };
                  }
                  // Also handle modal background click (optional)
                  document.getElementById('thankYouModal').onclick = function(e) {
                    if (e.target === this) window.location.href = 'index.php';
                  };
                } else {
                  alert('Payment verification failed: ' + (vresp.message || ''));
                  window.location.reload();
                }
              });
            },
            prefill: {
              name: resp.customer_name,
              email: resp.customer_email,
              contact: resp.customer_phone
            },
            theme: { color: '#3399cc' }
          };
          var rzp = new Razorpay(options);
          rzp.open();
        } else {
          alert('Error: ' + (resp.message || 'Could not initiate payment.'));
        }
      });
    }
  });

// --- Live update shipping zone/charge on address change ---
const addresses = <?php echo json_encode($addresses); ?>;
document.querySelectorAll('input[name="selected_address_left"]').forEach(function(radio) {
  radio.addEventListener('change', function() {
    const addrId = this.value;
    const addr = addresses.find(a => a.id == addrId);
    if (!addr) return;
    // Prepare data for AJAX
    const data = {
      delivery_state: addr.state,
      delivery_city: addr.city,
      delivery_pincode: addr.pincode
    };
    fetch('ajax/get-cart-summary.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(data)
    })
    .then(res => res.json())
    .then(function(resp) {
      if (resp && resp.success !== false && resp.totals) {
        // Update shipping charge
        document.getElementById('cart-shipping').textContent = '₹' + parseFloat(resp.totals.total_shipping).toFixed(2);
        // Update shipping zone
        document.getElementById('cart-shipping-zone').textContent = resp.totals.shipping_zone_name || '';
        // Update GST breakdown
        let gstHtml = '<small class="text-muted">TAX Breakdown:</small>';
        if (resp.totals.igst_total > 0) {
          gstHtml += `<div class="d-flex justify-content-between mb-1"><small>IGST (${((resp.totals.igst_total/resp.totals.subtotal)*100).toFixed(1)}%)</small><small>₹${parseFloat(resp.totals.igst_total).toFixed(2)}</small></div>`;
        } else if (resp.totals.sgst_total > 0 || resp.totals.cgst_total > 0) {
          gstHtml += `<div class="d-flex justify-content-between mb-1"><small>SGST (${((resp.totals.sgst_total/resp.totals.subtotal)*100).toFixed(1)}%)</small><small>₹${parseFloat(resp.totals.sgst_total).toFixed(2)}</small></div>`;
          gstHtml += `<div class="d-flex justify-content-between mb-1"><small>CGST (${((resp.totals.cgst_total/resp.totals.subtotal)*100).toFixed(1)}%)</small><small>₹${parseFloat(resp.totals.cgst_total).toFixed(2)}</small></div>`;
        }
        gstHtml += `<div class="d-flex justify-content-between"><small><strong>Total GST</strong></small><small><strong>₹${parseFloat(resp.totals.total_gst).toFixed(2)}</strong></small></div>`;
        document.getElementById('gst-breakdown-section').innerHTML = gstHtml;
        // Update total amount
        document.getElementById('order-total-amount').innerHTML = '<b>₹' + parseFloat(resp.totals.grand_total).toFixed(2) + '</b>';
      }
    });
  });
});

// Direct Payment (UPI/QR) logic
const paymentRadios = document.querySelectorAll('input[name="payment_method"]');
const directPaymentSection = document.getElementById('directPaymentSection');
const directPaymentDetailsSection = document.getElementById('directPaymentDetailsSection');
const upiPaymentLink = document.getElementById('upiPaymentLink');
// --- Use only the in-form elements ---
const checkoutForm = document.getElementById('checkoutForm');
const userUpiIdInput = checkoutForm.querySelector('#user_upi_id');
const continueBtn = checkoutForm.querySelector('#directPaymentContinueBtn');
const submitBtn = checkoutForm.querySelector('#directPaymentSubmitBtn');
const upiTransactionIdInput = checkoutForm.querySelector('#upi_transaction_id');
const upiScreenshotInput = checkoutForm.querySelector('#upi_screenshot');
const placeOrderBtn = checkoutForm.querySelector('#placeOrderBtn');
const orderTotal = <?php echo json_encode($orderTotals['total']); ?>;
const upiId = 'prakash.raje7@oksbi';

let directPaymentStepCompleted = false;

function showDirectPaymentStep() {
  directPaymentSection.style.display = '';
  directPaymentDetailsSection.style.display = 'none';
  placeOrderBtn.disabled = true;
  directPaymentStepCompleted = false;
}
function showDirectPaymentDetails() {
  directPaymentSection.style.display = 'none';
  directPaymentDetailsSection.style.display = '';
  placeOrderBtn.disabled = true;
  directPaymentStepCompleted = false;
}
function resetDirectPaymentStep() {
  directPaymentSection.style.display = 'none';
  directPaymentDetailsSection.style.display = 'none';
  placeOrderBtn.disabled = false;
  directPaymentStepCompleted = false;
  const infoMsg = checkoutForm.querySelector('#directPaymentInfoMsg');
  if (infoMsg) infoMsg.style.display = 'none';
  if (upiTransactionIdInput) upiTransactionIdInput.value = '';
  if (upiScreenshotInput) upiScreenshotInput.value = '';
}

paymentRadios.forEach(radio => {
  radio.addEventListener('change', function() {
    if (this.value === 'direct_payment') {
      showDirectPaymentStep();
    } else {
      resetDirectPaymentStep();
    }
  });
});

// Bootstrap modal for direct payment confirmation
const directPaymentConfirmModal = new bootstrap.Modal(document.getElementById('directPaymentConfirmModal'));

if (continueBtn) continueBtn.addEventListener('click', function() {
  if (!userUpiIdInput.value.match(/^[\w.-]+@[\w.-]+$/)) {
    userUpiIdInput.classList.add('is-invalid');
    userUpiIdInput.focus();
    return;
  } else {
    userUpiIdInput.classList.remove('is-invalid');
  }
  // Show Bootstrap modal instead of alert
  directPaymentConfirmModal.show();
  var yesBtn = document.getElementById('directPaymentConfirmYesBtn');
  yesBtn.onclick = function() {
    directPaymentConfirmModal.hide();
    showDirectPaymentDetails();
  };
});

if (submitBtn) submitBtn.addEventListener('click', function(e) {
  e.preventDefault();
  e.stopPropagation();
  if (!upiTransactionIdInput.value.trim()) {
    upiTransactionIdInput.classList.add('is-invalid');
    upiTransactionIdInput.focus();
    return;
  } else {
    upiTransactionIdInput.classList.remove('is-invalid');
  }
  directPaymentStepCompleted = true;
  placeOrderBtn.disabled = false;
  const infoMsg = checkoutForm.querySelector('#directPaymentInfoMsg');
  if (infoMsg) {
    infoMsg.textContent = 'Now you can click on Place Order to complete your order.';
    infoMsg.style.display = '';
  }
});

checkoutForm.addEventListener('submit', function(e) {
  var paymentMethod = checkoutForm.querySelector('input[name="payment_method"]:checked').value;
  if (paymentMethod === 'direct_payment') {
    if (!directPaymentStepCompleted) {
      e.preventDefault();
      showDirectPaymentStep();
      alert('Please complete the direct payment steps before placing the order.');
      return false;
    }
    if (!upiTransactionIdInput.value.trim()) {
      e.preventDefault();
      upiTransactionIdInput.classList.add('is-invalid');
      upiTransactionIdInput.focus();
      alert('Please enter your UPI transaction ID.');
      return false;
    }
    // Set hidden fields for backend
    document.getElementById('upi_transaction_id_hidden').value = upiTransactionIdInput.value.trim();
    document.getElementById('user_upi_id_hidden').value = userUpiIdInput.value.trim();
    placeOrderBtn.disabled = true;
  }
});
</script>

<!-- Thank You Modal (always present for JS to show) -->
<div id="thankYouModal" class="popup-overlay" style="display:none;">
  <div class="popup">
    <span class="popup-close" onclick="closeThankYouModal()">&times;</span>
    <i class="fas fa-check-circle text-success" style="font-size: 3rem;"></i>
    <h3 class="text-success mt-3">Thank You!</h3>
    <p class="lead">Your order has been placed successfully.</p>
    <div class="d-grid gap-2 mt-4">
      <a href="myaccount.php" class="btn btn-primary">Go to My Account</a>
      <a href="index.php" class="btn btn-outline-secondary">Continue Shopping</a>
    </div>
  </div>
</div>
<?php if (!empty($orderPlaced)): ?>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('thankYouModal').style.display = 'flex';
    var closeBtn = document.querySelector('#thankYouModal .popup-close');
    if (closeBtn) {
      closeBtn.onclick = function() { window.location.href = 'index.php'; };
    }
    document.getElementById('thankYouModal').onclick = function(e) {
      if (e.target === this) window.location.href = 'index.php';
    };
  });
</script>
<?php endif; ?>
<script>
function closeThankYouModal() {
  window.location.href = 'index.php';
}
// Prevent form resubmission on reload
if (window.history.replaceState) {
  window.history.replaceState(null, null, window.location.href);
}
</script>
<style>
.popup-overlay {
  position: fixed;
  top: 0; left: 0;
  width: 100%; height: 100%;
  background-color: rgba(0, 0, 0, 0.6);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 9999;
}
.popup {
  background: white;
  padding: 30px 24px 24px 24px;
  border-radius: 12px;
  text-align: center;
  width: 340px;
  box-shadow: 0 4px 20px rgba(0,0,0,0.3);
  position: relative;
}
.popup-close {
  position: absolute;
  top: 10px; right: 15px;
  cursor: pointer;
  font-size: 20px;
  font-weight: bold;
}
</style>
<?php include 'includes/footer.php'; ?> 