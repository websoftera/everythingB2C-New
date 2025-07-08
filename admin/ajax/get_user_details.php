<?php
session_start();
require_once '../../config/database.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    http_response_code(403);
    exit('Unauthorized');
}

$user_id = intval($_GET['id'] ?? 0);

if (!$user_id) {
    echo '<p class="text-muted">Invalid user ID.</p>';
    exit;
}

try {
    // Get user details
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user) {
        echo '<p class="text-muted">User not found.</p>';
        exit;
    }
    
    // Get user's order statistics
    $stmt = $pdo->prepare("SELECT 
        COUNT(*) as total_orders,
        SUM(total_amount) as total_spent,
        AVG(total_amount) as avg_order_value,
        MAX(created_at) as last_order_date
        FROM orders 
        WHERE user_id = ? AND status != 'cancelled'");
    $stmt->execute([$user_id]);
    $order_stats = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Get user's addresses
    $stmt = $pdo->prepare("SELECT * FROM addresses WHERE user_id = ? ORDER BY is_default DESC");
    $stmt->execute([$user_id]);
    $addresses = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo '<div class="row">';
    
    // User Information
    echo '<div class="col-md-6">';
    echo '<h6>User Information</h6>';
    echo '<table class="table table-sm">';
    echo '<tr><td><strong>Name:</strong></td><td>' . htmlspecialchars($user['name']) . '</td></tr>';
    echo '<tr><td><strong>Email:</strong></td><td>' . htmlspecialchars($user['email']) . '</td></tr>';
    echo '<tr><td><strong>Phone:</strong></td><td>' . htmlspecialchars($user['phone'] ?? 'Not provided') . '</td></tr>';
    echo '<tr><td><strong>Status:</strong></td><td>';
    echo '<span class="badge bg-' . ($user['is_active'] ? 'success' : 'secondary') . '">';
    echo $user['is_active'] ? 'Active' : 'Inactive';
    echo '</span></td></tr>';
    echo '<tr><td><strong>Joined:</strong></td><td>' . date('M d, Y', strtotime($user['created_at'])) . '</td></tr>';
    echo '</table>';
    echo '</div>';
    
    // Order Statistics
    echo '<div class="col-md-6">';
    echo '<h6>Order Statistics</h6>';
    echo '<table class="table table-sm">';
    echo '<tr><td><strong>Total Orders:</strong></td><td>' . number_format($order_stats['total_orders']) . '</td></tr>';
    echo '<tr><td><strong>Total Spent:</strong></td><td>₹' . number_format($order_stats['total_spent'] ?? 0, 2) . '</td></tr>';
    echo '<tr><td><strong>Avg Order Value:</strong></td><td>₹' . number_format($order_stats['avg_order_value'] ?? 0, 2) . '</td></tr>';
    if ($order_stats['last_order_date']) {
        echo '<tr><td><strong>Last Order:</strong></td><td>' . date('M d, Y', strtotime($order_stats['last_order_date'])) . '</td></tr>';
    }
    echo '</table>';
    echo '</div>';
    
    echo '</div>';
    
    // Addresses
    if (!empty($addresses)) {
        echo '<hr>';
        echo '<h6>Addresses</h6>';
        echo '<div class="row">';
        foreach ($addresses as $address) {
            echo '<div class="col-md-6 mb-3">';
            echo '<div class="card">';
            echo '<div class="card-body">';
            if ($address['is_default']) {
                echo '<span class="badge bg-primary mb-2">Default Address</span>';
            }
            echo '<strong>' . htmlspecialchars($address['name']) . '</strong><br>';
            echo htmlspecialchars($address['phone']) . '<br>';
            echo htmlspecialchars($address['address_line1']) . '<br>';
            if ($address['address_line2']) {
                echo htmlspecialchars($address['address_line2']) . '<br>';
            }
            echo htmlspecialchars($address['city']) . ', ' . htmlspecialchars($address['state']) . ' - ' . htmlspecialchars($address['pincode']);
            echo '</div>';
            echo '</div>';
            echo '</div>';
        }
        echo '</div>';
    }
    
    // Recent Orders
    $stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC LIMIT 5");
    $stmt->execute([$user_id]);
    $recent_orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (!empty($recent_orders)) {
        echo '<hr>';
        echo '<h6>Recent Orders</h6>';
        echo '<div class="table-responsive">';
        echo '<table class="table table-sm">';
        echo '<thead><tr><th>Order #</th><th>Amount</th><th>Status</th><th>Date</th></tr></thead>';
        echo '<tbody>';
        foreach ($recent_orders as $order) {
            echo '<tr>';
            echo '<td>' . htmlspecialchars($order['order_number']) . '</td>';
            echo '<td>₹' . number_format($order['total_amount'], 2) . '</td>';
            echo '<td><span class="badge bg-' . getStatusColor($order['status']) . '">' . ucfirst($order['status']) . '</span></td>';
            echo '<td>' . date('M d, Y', strtotime($order['created_at'])) . '</td>';
            echo '</tr>';
        }
        echo '</tbody>';
        echo '</table>';
        echo '</div>';
    }
    
} catch (Exception $e) {
    echo '<p class="text-danger">Error loading user details: ' . htmlspecialchars($e->getMessage()) . '</p>';
}

function getStatusColor($status) {
    switch ($status) {
        case 'pending': return 'warning';
        case 'confirmed': return 'info';
        case 'shipped': return 'primary';
        case 'delivered': return 'success';
        case 'cancelled': return 'danger';
        default: return 'secondary';
    }
}
?> 