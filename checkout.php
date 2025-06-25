<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once 'includes/functions.php';

// Force login
if (!isLoggedIn()) {
    $_SESSION['redirect_after_login'] = 'checkout.php';
    header('Location: login.php');
    exit;
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

// Calculate bill summary
$mrp = 0; $total = 0; $savings = 0; $delivery = 49; $count = 0;
foreach ($cartItems as $item) {
    $mrp += $item['mrp'] * $item['quantity'];
    $total += $item['selling_price'] * $item['quantity'];
    $savings += ($item['mrp'] - $item['selling_price']) * $item['quantity'];
    $count += $item['quantity'];
}
if ($total >= 999) $delivery = 0;
$pay = $total + $delivery;
?>
<div class="page-banner" style="background: url('asset/images/internalpage-bg.webp') center/cover no-repeat; min-height: 240px; display: flex; align-items: center;">
    <div class="container">
        <h2 style="color: #fff; font-size: 2rem; font-weight: bold; text-shadow: 0 2px 8px rgba(0,0,0,0.3); margin: 0; padding: 32px 0;">
            Checkout
        </h2>
    </div>
</div>
<div class="container my-5">
  <div class="row g-4">
    <div class="col-lg-8">
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
          <form method="post">
            <?php if ($addresses): ?>
              <?php foreach ($addresses as $addr): ?>
                <div class="form-check mb-2">
                  <input class="form-check-input" type="radio" name="selected_address" id="addr<?php echo $addr['id']; ?>" value="<?php echo $addr['id']; ?>" <?php if ($addr['is_default']) echo 'checked'; ?> onclick="window.location='?set_default=<?php echo $addr['id']; ?>'">
                  <label class="form-check-label" for="addr<?php echo $addr['id']; ?>">
                    <b><?php echo htmlspecialchars($addr['name']); ?></b>, <?php echo htmlspecialchars($addr['address_line1']); ?><?php if ($addr['address_line2']) echo ', ' . htmlspecialchars($addr['address_line2']); ?>, <?php echo htmlspecialchars($addr['city']); ?>, <?php echo htmlspecialchars($addr['state']); ?>, <?php echo htmlspecialchars($addr['pincode']); ?>, Mob: <?php echo htmlspecialchars($addr['phone']); ?>
                  </label>
                  <?php if (!$addr['is_default']): ?>
                    <a href="?set_default=<?php echo $addr['id']; ?>" class="btn btn-link btn-sm">Set as default</a>
                  <?php endif; ?>
                </div>
              <?php endforeach; ?>
            <?php else: ?>
              <div class="alert alert-info">No saved addresses. Please add one below.</div>
            <?php endif; ?>
          </form>
          <hr>
          <h6 class="mb-2">+ Add New Address</h6>
          <form method="post" class="row g-2">
            <input type="hidden" name="add_address" value="1">
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
          </form>
        </div>
      </div>
    </div>
    <div class="col-lg-4">
      <div class="checkout-card">
        <div class="card-body">
          <h5 class="mb-3">Bill Summary <span class="text-muted" style="font-size:1rem;">(<?php echo $count; ?> products)</span></h5>
          <div class="d-flex justify-content-between mb-2"><span>MRP</span><span>₹<?php echo number_format($mrp,2); ?></span></div>
          <div class="d-flex justify-content-between mb-2"><span>Delivery charge</span><span>₹<?php echo $delivery; ?></span></div>
          <div class="d-flex justify-content-between mb-2 bg-light p-2 rounded"><span class="text-success"><b>Total Savings</b></span><span class="text-success">₹<?php echo number_format($savings,2); ?></span></div>
          <div class="d-flex justify-content-between mb-2 bg-primary bg-opacity-10 p-2 rounded"><span><b>Total Amount to Pay</b></span><span><b>₹<?php echo number_format($pay,2); ?></b></span></div>
          <button class="btn btn-secondary w-100 mt-3" disabled>PAY ON DELIVERY (COD)</button>
        </div>
      </div>
    </div>
  </div>
</div>
<?php include 'includes/footer.php'; ?> 