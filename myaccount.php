<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once 'includes/functions.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$userId = $_SESSION['user_id'];
$user = getCurrentUser();
$pageTitle = 'My Account';
include 'includes/header.php';

$userOrders = getUserOrders($userId, 5);
$userAddresses = getUserAddresses($userId);
$wishlistItems = getWishlistItems($userId);

if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: index.php');
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
    header('Location: myaccount.php#addresses');
    exit;
}

if (isset($_POST['update_info'])) {
    $newName = sanitizeInput($_POST['name']);
    $newPhone = sanitizeInput($_POST['phone']);
    $stmt = $pdo->prepare("UPDATE users SET name = ?, phone = ? WHERE id = ?");
    $stmt->execute([$newName, $newPhone, $userId]);
    header('Location: myaccount.php#profile');
    exit;
}
if (isset($_POST['change_password'])) {
    $currentPassword = $_POST['current_password'];
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];
    $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $userRow = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($userRow && password_verify($currentPassword, $userRow['password'])) {
        if ($newPassword === $confirmPassword && strlen($newPassword) >= 6) {
            $hashed = password_hash($newPassword, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
            $stmt->execute([$hashed, $userId]);
            $password_message = 'Password updated successfully!';
        } else {
            $password_message = 'Passwords do not match or are too short (min 6 chars).';
        }
    } else {
        $password_message = 'Current password is incorrect.';
    }
}
?>

<style>

/* === Banner === */
.account-banner {
  background: url('asset/images/internalpage-bg.webp') center/cover no-repeat;
  min-height: 240px;
  display: flex;
  align-items: center;
}

.account-banner h2 {
  color: #fff;
  font-size: 2rem;
  font-weight: bold;
  text-shadow: 0 2px 8px rgba(0,0,0,0.3);
  margin: 0;
  padding: 32px 0;
}

/* === Wrapper === */
.account-wrapper {
  display: flex;
  flex-direction: column; /* Mobile first */
  gap: 24px;
  max-width: 1200px;
  margin: 40px auto;
  padding: 0 15px;
}

.account-sidebar {
  width: 100%;
  max-width: 100%;
  margin-bottom: 24px;
}

.account-content {
  width: 100%;
  max-width: 100%;
}

@media (min-width: 992px) {
  .account-wrapper {
    flex-direction: row;
    align-items: flex-start;
  }
  .account-sidebar {
    width: 250px;
    max-width: 250px;
    flex-shrink: 0;
    margin-bottom: 0;
  }
  .account-content {
    flex: 1 1 0%;
    max-width: calc(100% - 250px);
    width: auto;
  }
}

/* === Sidebar Card === */
.account-card {
  background: #fff;
  border: 1px solid #ddd;
  border-radius: 6px;
  padding: 20px;
  margin-bottom: 20px;
}

.account-card h5 {
  margin-bottom: 15px;
}

/* === Sidebar Menu === */
.account-menu {
  display: flex;
  flex-direction: column;
}

.account-menu-item {
  padding: 12px 15px;
  border-bottom: 1px solid #eee;
  color: #333;
  text-decoration: none;
  display: flex;
  align-items: center;
  gap: 8px;
}

.account-menu-item:hover {
  background: #f5f5f5;
}

.account-menu-item.active {
  background: #007bff;
  color: #fff;
}

.account-menu-item.logout {
  color: red;
}

/* === Stats Cards === */
.account-stats {
  display: flex;
  flex-wrap: wrap;
  gap: 20px;
  margin: 20px 0;
}

.account-stat-card {
  flex: 1 1 30%;
  text-align: center;
  padding: 20px;
  border-radius: 6px;
  color: #fff;
}

.account-stat-card.primary { background: #007bff; }
.account-stat-card.success { background: #28a745; }
.account-stat-card.warning { background: #ffc107; color: #000; }

/* === Orders === */
.account-order-card {
  display: flex;
  justify-content: space-between;
  align-items: center;
  border-top: 1px solid #eee;
  padding: 15px 0;
}

.account-order-card h6 {
  margin: 0 0 5px;
}

.account-order-card p {
  margin: 0;
  color: #555;
  font-size: 0.9rem;
}

.status {
  padding: 4px 10px;
  border-radius: 3px;
  font-size: 0.8rem;
  color: #fff;
}

.status.delivered { background: #28a745; }
.status.cancelled { background: #dc3545; }
.status.primary { background: #007bff; }

/* === Empty States === */
.account-empty {
  text-align: center;
  padding: 40px 0;
}

.account-empty i {
  font-size: 3rem;
  color: #999;
  margin-bottom: 10px;
}

/* === Addresses === */
.account-addresses {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 20px;
}

.account-address-card {
  position: relative;
  border: 1px solid #ddd;
  border-radius: 6px;
  padding: 15px;
}

.default-badge {
  position: absolute;
  top: 10px; right: 10px;
  background: #007bff;
  color: #fff;
  padding: 3px 8px;
  font-size: 0.75rem;
  border-radius: 3px;
}

.address-actions {
  margin-top: 10px;
}

.address-actions .btn {
  margin-right: 5px;
  margin-top: 5px;
}

/* === Wishlist === */
.account-wishlist {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
  gap: 20px;
}

.account-wishlist-item {
  text-align: center;
}

.account-wishlist-item img {
  width: 100%;
  height: 180px;
  object-fit: cover;
  border-radius: 4px;
}

.account-wishlist-item h6 {
  margin: 10px 0 5px;
}

.account-wishlist-item p {
  margin: 0 0 10px;
}

/* === Profile === */
.account-profile {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
  gap: 20px;
}

.account-profile p {
  margin: 5px 0;
}

/* === Buttons === */
.btn {
  display: inline-block;
  background: #007bff;
  color: #fff;
  padding: 8px 15px;
  text-decoration: none;
  border-radius: 4px;
  font-size: 0.9rem;
}

.btn:hover {
  background: #0056b3;
}

.btn.small {
  padding: 5px 10px;
  font-size: 0.8rem;
}

.btn.danger {
  background: #dc3545;
}

.btn.danger:hover {
  background: #b02a37;
}

.custom-container {
  width: 100%;
  max-width: 1200px; /* You can adjust this as needed */
  margin-left: auto;
  margin-right: auto;
  padding-left: 15px;
  padding-right: 15px;
  box-sizing: border-box;
}



</style>

<div class="account-banner">
    <div class="custom-container">
        <h2>My Account</h2>
    </div>
</div>

<div class="custom-container account-wrapper">
    <div class="account-sidebar">
        <div class="account-card">
            <h5>Account Menu</h5>
            <div class="account-menu">
                <a href="#dashboard" class="account-menu-item active" onclick="showSection('dashboard')">
                    <i class="fas fa-home"></i> Dashboard
                </a>
                <a href="#orders" class="account-menu-item" onclick="showSection('orders')">
                    <i class="fas fa-shopping-bag"></i> My Orders
                </a>
                <a href="#addresses" class="account-menu-item" onclick="showSection('addresses')">
                    <i class="fas fa-map-marker-alt"></i> Addresses
                </a>
                <a href="#wishlist" class="account-menu-item" onclick="showSection('wishlist')">
                    <i class="fas fa-heart"></i> Wishlist
                </a>
                <a href="#profile" class="account-menu-item" onclick="showSection('profile')">
                    <i class="fas fa-user"></i> Account Info
                </a>
                <a href="?logout=1" class="account-menu-item logout" onclick="return confirm('Are you sure you want to logout?')">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </div>
    </div>
    <div class="account-content">
        <!-- Dashboard -->
        <div id="dashboard" class="account-section">
            <div class="account-card">
                <h3>Welcome back, <?php echo htmlspecialchars($user['name']); ?>!</h3>
                <div class="account-stats">
                    <div class="account-stat-card primary">
                        <h4><?php echo count($userOrders); ?></h4>
                        <p>Total Orders</p>
                    </div>
                    <div class="account-stat-card success">
                        <h4><?php echo count($userAddresses); ?></h4>
                        <p>Saved Addresses</p>
                    </div>
                    <div class="account-stat-card warning">
                        <h4><?php echo count($wishlistItems); ?></h4>
                        <p>Wishlist Items</p>
                    </div>
                </div>

                <h5>Recent Orders</h5>
                <?php if (!empty($userOrders)): ?>
                    <?php foreach (array_slice($userOrders, 0, 3) as $order): ?>
                        <div class="account-order-card">
                            <div>
                                <h6>Order #<?php echo htmlspecialchars($order['tracking_id'] ?? $order['order_number']); ?></h6>
                                <p>₹<?php echo number_format($order['total_amount'], 2); ?> • <?php echo date('M d, Y', strtotime($order['created_at'])); ?></p>
                            </div>
                            <div>
                                <span class="status <?php echo $order['status']; ?>">
                                    <?php echo ucfirst($order['status']); ?>
                                </span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="account-empty">
                        <i class="fas fa-shopping-bag"></i>
                        <h5>No orders yet</h5>
                        <p>Start shopping to see your orders here</p>
                        <a href="index.php" class="btn">Start Shopping</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Orders -->
        <div id="orders" class="account-section" style="display: none;">
            <div class="account-card">
                <h3>My Orders</h3>
                <?php if (!empty($userOrders)): ?>
                    <?php foreach ($userOrders as $order): ?>
                        <div class="account-order-card" style="flex-direction: column; align-items: stretch;">
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <div>
                                    <h6>Order #<?php echo htmlspecialchars($order['tracking_id'] ?? $order['order_number']); ?></h6>
                                    <p>₹<?php echo number_format($order['total_amount'], 2); ?> • <?php echo date('M d, Y', strtotime($order['created_at'])); ?></p>
                                    <p>Payment: <strong><?php echo strtoupper($order['payment_method'] ?? 'N/A'); ?></strong> • Status: <strong><?php echo ucfirst($order['payment_status'] ?? 'N/A'); ?></strong></p>
                                    <p>Order Status: <span class="badge" style="background-color: <?php echo $order['color'] ?? '#007bff'; ?>; color: #fff;"><?php echo $order['status_name'] ?? ucfirst($order['status']); ?></span></p>
                                </div>
                                <div>
                                    <a href="track_order.php?order_id=<?php echo $order['id']; ?>" class="btn small">Track Order</a>
                                    <a href="download_invoice.php?order_id=<?php echo $order['id']; ?>" class="btn small btn-success" target="_blank">Download Invoice</a>
                                </div>
                            </div>
                            <!-- Product Details for this order -->
                            <?php $orderItems = getOrderItems($order['id']); ?>
                            <div style="margin-top: 10px;">
                                <table class="table table-sm table-bordered" style="background: #fafbfc;">
                                    <thead>
                                        <tr>
                                            <th>Product</th>
                                            <th>SKU</th>
                                            <th>Qty</th>
                                            <th>Price</th>
                                            <th>Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($orderItems as $item): ?>
                                            <tr>
                                                <td>
                                                    <img src="<?php echo htmlspecialchars($item['main_image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" style="height:32px;width:32px;object-fit:cover;margin-right:8px;">
                                                    <a href="product.php?slug=<?php echo htmlspecialchars($item['slug']); ?>" target="_blank"><?php echo htmlspecialchars($item['name']); ?></a>
                                                </td>
                                                <td><?php echo htmlspecialchars($item['product_id']); ?></td>
                                                <td><?php echo $item['quantity']; ?></td>
                                                <td>₹<?php echo number_format($item['price'], 2); ?></td>
                                                <td>₹<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="account-empty">
                        <i class="fas fa-shopping-bag"></i>
                        <h5>No orders yet</h5>
                        <p>Start shopping to see your orders here</p>
                        <a href="index.php" class="btn">Start Shopping</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Addresses -->
        <div id="addresses" class="account-section" style="display: none;">
            <div class="account-card">
                <h3>My Addresses</h3>
                <!-- Add New Address Form -->
                <form method="post" style="margin-bottom: 24px;">
                    <input type="hidden" name="add_address" value="1">
                    <div class="row g-2">
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
                            <button type="submit" class="btn btn-success">Add Address</button>
                        </div>
                    </div>
                </form>
                <?php if (!empty($userAddresses)): ?>
                    <div class="account-addresses">
                        <?php foreach ($userAddresses as $address): ?>
                            <div class="account-address-card">
                                <?php if ($address['is_default']): ?>
                                    <span class="default-badge">Default</span>
                                <?php endif; ?>
                                <h6><?php echo htmlspecialchars($address['name']); ?></h6>
                                <p><?php echo htmlspecialchars($address['phone']); ?></p>
                                <p><?php echo htmlspecialchars($address['address_line1']); ?></p>
                                <?php if ($address['address_line2']): ?>
                                    <p><?php echo htmlspecialchars($address['address_line2']); ?></p>
                                <?php endif; ?>
                                <p><?php echo htmlspecialchars($address['city'] . ', ' . $address['state'] . ' - ' . $address['pincode']); ?></p>
                                <div class="address-actions">
                                    <?php if (!$address['is_default']): ?>
                                        <a href="checkout.php?set_default=<?php echo $address['id']; ?>" class="btn small">Set as Default</a>
                                    <?php endif; ?>
                                    <a href="checkout.php?edit_address=<?php echo $address['id']; ?>" class="btn small">Edit</a>
                                    <a href="checkout.php?delete_address=<?php echo $address['id']; ?>" class="btn small danger" onclick="return confirm('Delete this address?')">Delete</a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="account-empty">
                        <i class="fas fa-map-marker-alt"></i>
                        <h5>No addresses saved</h5>
                        <p>Add an address to make checkout easier</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Wishlist -->
        <div id="wishlist" class="account-section" style="display: none;">
            <div class="account-card">
                <h3>My Wishlist</h3>
                <?php if (!empty($wishlistItems)): ?>
                    <div class="account-wishlist">
                        <?php foreach ($wishlistItems as $item): ?>
                            <div class="account-wishlist-item">
                                <img src="<?php echo htmlspecialchars($item['main_image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
                                <h6><?php echo htmlspecialchars($item['name']); ?></h6>
                                <p>₹<?php echo number_format($item['selling_price'], 2); ?></p>
                                <a href="product.php?slug=<?php echo $item['slug']; ?>" class="btn small">View</a>
                                <a href="ajax/remove-from-wishlist.php?product_id=<?php echo $item['id']; ?>" class="btn small danger" onclick="return confirm('Remove from wishlist?')">Remove</a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="account-empty">
                        <i class="fas fa-heart"></i>
                        <h5>Your wishlist is empty</h5>
                        <p>Start adding products to your wishlist</p>
                        <a href="index.php" class="btn">Start Shopping</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Profile -->
        <div id="profile" class="account-section" style="display: none;">
            <div class="account-card">
                <h3>Account Information</h3>
                <div class="account-profile">
                    <div>
                        <h5>Personal Info</h5>
                        <form method="post" style="margin-bottom: 20px;">
                            <input type="hidden" name="update_info" value="1">
                            <div class="mb-2">
                                <label for="name" class="form-label"><strong>Name:</strong></label>
                                <input type="text" class="form-control" name="name" id="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
                            </div>
                            <div class="mb-2">
                                <label for="phone" class="form-label"><strong>Phone:</strong></label>
                                <input type="text" class="form-control" name="phone" id="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
                            </div>
                            <button type="submit" class="btn btn-primary">Update Info</button>
                        </form>
                        <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
                        <p><strong>Member since:</strong> <?php echo date('M d, Y', strtotime($user['created_at'])); ?></p>
                    </div>
                    <div>
                        <h5>Change Password</h5>
                        <?php if (isset($password_message)): ?>
                            <div class="alert alert-info"><?php echo htmlspecialchars($password_message); ?></div>
                        <?php endif; ?>
                        <form method="post">
                            <input type="hidden" name="change_password" value="1">
                            <div class="mb-2">
                                <label for="current_password" class="form-label">Current Password</label>
                                <input type="password" class="form-control" name="current_password" id="current_password" required>
                            </div>
                            <div class="mb-2">
                                <label for="new_password" class="form-label">New Password</label>
                                <input type="password" class="form-control" name="new_password" id="new_password" required>
                            </div>
                            <div class="mb-2">
                                <label for="confirm_password" class="form-label">Confirm New Password</label>
                                <input type="password" class="form-control" name="confirm_password" id="confirm_password" required>
                            </div>
                            <button type="submit" class="btn btn-warning">Change Password</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
function showSection(id) {
    document.querySelectorAll('.account-section').forEach(s => s.style.display = 'none');
    document.getElementById(id).style.display = 'block';
    document.querySelectorAll('.account-menu-item').forEach(i => i.classList.remove('active'));
    document.querySelector(`[href="#${id}"]`).classList.add('active');
}
document.addEventListener('DOMContentLoaded', () => {
    const hash = window.location.hash.substring(1);
    if (hash) showSection(hash);
});
</script>

<?php include 'includes/footer.php'; ?>
