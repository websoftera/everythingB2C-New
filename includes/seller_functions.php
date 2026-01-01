<?php
/**
 * Seller Management Functions
 * Functions to handle seller operations, permissions, and data management
 */

/**
 * Check if user is a seller
 */
function isSeller($userId = null) {
    global $pdo;
    
    if ($userId === null && isset($_SESSION['user_id'])) {
        $userId = $_SESSION['user_id'];
    }
    
    if (!$userId) {
        return false;
    }
    
    $stmt = $pdo->prepare("SELECT user_role, is_seller_approved FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    return $user && $user['user_role'] === 'seller' && $user['is_seller_approved'] == 1;
}

/**
 * Check if user is admin
 */
function isAdmin($userId = null) {
    global $pdo;
    
    if ($userId === null && isset($_SESSION['user_id'])) {
        $userId = $_SESSION['user_id'];
    }
    
    if (!$userId) {
        return false;
    }
    
    $stmt = $pdo->prepare("SELECT user_role FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    return $user && $user['user_role'] === 'admin';
}

/**
 * Get seller ID from user ID
 */
function getSellerIdByUserId($userId) {
    global $pdo;
    
    $stmt = $pdo->prepare("SELECT id FROM sellers WHERE user_id = ?");
    $stmt->execute([$userId]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    return $result ? $result['id'] : null;
}

/**
 * Create a new seller
 */
function createSeller($userId, $businessName, $businessData = []) {
    global $pdo;
    
    try {
        $pdo->beginTransaction();
        
        // Update user role
        $stmt = $pdo->prepare("UPDATE users SET user_role = 'seller', is_seller_approved = 1, 
                               seller_approved_at = NOW(), seller_approved_by = ? WHERE id = ?");
        $stmt->execute([$_SESSION['user_id'] ?? 1, $userId]);
        
        // Create seller record
        $stmt = $pdo->prepare("INSERT INTO sellers (user_id, business_name, business_type, gst_number, 
                               pan_number, business_address, business_email, business_phone, 
                               bank_account_name, bank_account_number, bank_ifsc_code, bank_name, 
                               commission_percentage) 
                               VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $userId,
            $businessName,
            $businessData['business_type'] ?? null,
            $businessData['gst_number'] ?? null,
            $businessData['pan_number'] ?? null,
            $businessData['business_address'] ?? null,
            $businessData['business_email'] ?? null,
            $businessData['business_phone'] ?? null,
            $businessData['bank_account_name'] ?? null,
            $businessData['bank_account_number'] ?? null,
            $businessData['bank_ifsc_code'] ?? null,
            $businessData['bank_name'] ?? null,
            $businessData['commission_percentage'] ?? 10.00
        ]);
        
        $sellerId = $pdo->lastInsertId();
        
        // Create default permissions
        $stmt = $pdo->prepare("INSERT INTO seller_permissions (seller_id) VALUES (?)");
        $stmt->execute([$sellerId]);
        
        // Initialize statistics
        $stmt = $pdo->prepare("INSERT INTO seller_statistics (seller_id) VALUES (?)");
        $stmt->execute([$sellerId]);
        
        // Log activity
        logSellerActivity($sellerId, 'seller_created', 'Seller account created');
        
        $pdo->commit();
        return ['success' => true, 'seller_id' => $sellerId];
        
    } catch (Exception $e) {
        $pdo->rollBack();
        return ['success' => false, 'message' => $e->getMessage()];
    }
}

/**
 * Get seller details
 */
function getSellerDetails($sellerId) {
    global $pdo;
    
    $stmt = $pdo->prepare("SELECT s.*, u.name, u.email, u.phone, 
                           u.is_seller_approved, u.seller_approved_at
                           FROM sellers s
                           JOIN users u ON s.user_id = u.id
                           WHERE s.id = ?");
    $stmt->execute([$sellerId]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

/**
 * Get all sellers
 */
function getAllSellers($filters = []) {
    global $pdo;
    
    $sql = "SELECT s.*, u.name, u.email, u.phone, 
            u.is_seller_approved, u.seller_approved_at,
            ss.total_products, ss.active_products, ss.total_orders, ss.total_revenue
            FROM sellers s
            JOIN users u ON s.user_id = u.id
            LEFT JOIN seller_statistics ss ON s.id = ss.seller_id
            WHERE 1=1";
    
    $params = [];
    
    if (isset($filters['is_active'])) {
        $sql .= " AND s.is_active = ?";
        $params[] = $filters['is_active'];
    }
    
    if (isset($filters['is_approved'])) {
        $sql .= " AND u.is_seller_approved = ?";
        $params[] = $filters['is_approved'];
    }
    
    $sql .= " ORDER BY s.created_at DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Update seller status
 */
function updateSellerStatus($sellerId, $isActive) {
    global $pdo;
    
    $stmt = $pdo->prepare("UPDATE sellers SET is_active = ? WHERE id = ?");
    $success = $stmt->execute([$isActive, $sellerId]);
    
    if ($success) {
        $action = $isActive ? 'activated' : 'deactivated';
        logSellerActivity($sellerId, 'status_changed', "Seller account {$action}");
    }
    
    return $success;
}

/**
 * Get seller permissions
 */
function getSellerPermissions($sellerId) {
    global $pdo;
    
    $stmt = $pdo->prepare("SELECT * FROM seller_permissions WHERE seller_id = ?");
    $stmt->execute([$sellerId]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

/**
 * Update seller permissions
 */
function updateSellerPermissions($sellerId, $permissions) {
    global $pdo;
    
    $stmt = $pdo->prepare("UPDATE seller_permissions SET 
                           can_manage_products = ?,
                           can_manage_categories = ?,
                           can_view_orders = ?,
                           can_view_reports = ?,
                           can_update_settings = ?,
                           can_add_products = ?,
                           can_edit_products = ?,
                           can_delete_products = ?,
                           max_products = ?
                           WHERE seller_id = ?");
    
    return $stmt->execute([
        $permissions['can_manage_products'] ?? 1,
        $permissions['can_manage_categories'] ?? 1,
        $permissions['can_view_orders'] ?? 1,
        $permissions['can_view_reports'] ?? 1,
        $permissions['can_update_settings'] ?? 1,
        $permissions['can_add_products'] ?? 1,
        $permissions['can_edit_products'] ?? 1,
        $permissions['can_delete_products'] ?? 0,
        $permissions['max_products'] ?? 100,
        $sellerId
    ]);
}

/**
 * Check if seller has permission
 */
function sellerHasPermission($sellerId, $permission) {
    $permissions = getSellerPermissions($sellerId);
    return isset($permissions[$permission]) && $permissions[$permission] == 1;
}

/**
 * Get seller products
 */
function getSellerProducts($sellerId, $includeUnapproved = false) {
    global $pdo;
    
    $sql = "SELECT p.*, c.name as category_name 
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            WHERE p.seller_id = ?";
    
    if (!$includeUnapproved) {
        $sql .= " AND p.is_approved = 1";
    }
    
    $sql .= " ORDER BY p.created_at DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$sellerId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Get seller categories
 */
function getSellerCategories($sellerId) {
    global $pdo;
    
    $stmt = $pdo->prepare("SELECT * FROM categories WHERE seller_id = ? ORDER BY name ASC");
    $stmt->execute([$sellerId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Get seller orders
 */
function getSellerOrders($sellerId, $filters = []) {
    global $pdo;
    
    $sql = "SELECT DISTINCT o.*, os.name as status_name, os.color as status_color,
            u.name as customer_name, u.email, u.phone
            FROM orders o
            JOIN order_items oi ON o.id = oi.order_id
            JOIN products p ON oi.product_id = p.id
            JOIN order_statuses os ON o.order_status_id = os.id
            JOIN users u ON o.user_id = u.id
            WHERE p.seller_id = ?";
    
    $params = [$sellerId];
    
    if (isset($filters['status'])) {
        $sql .= " AND o.order_status_id = ?";
        $params[] = $filters['status'];
    }
    
    if (isset($filters['date_from'])) {
        $sql .= " AND DATE(o.created_at) >= ?";
        $params[] = $filters['date_from'];
    }
    
    if (isset($filters['date_to'])) {
        $sql .= " AND DATE(o.created_at) <= ?";
        $params[] = $filters['date_to'];
    }
    
    $sql .= " ORDER BY o.created_at DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Get seller statistics
 */
function getSellerStatistics($sellerId) {
    global $pdo;
    
    $stmt = $pdo->prepare("SELECT * FROM seller_statistics WHERE seller_id = ?");
    $stmt->execute([$sellerId]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

/**
 * Update seller statistics
 */
function updateSellerStatistics($sellerId) {
    global $pdo;
    
    try {
        // Count products
        $stmt = $pdo->prepare("SELECT 
                               COUNT(*) as total_products,
                               SUM(CASE WHEN is_active = 1 AND is_approved = 1 THEN 1 ELSE 0 END) as active_products,
                               SUM(CASE WHEN is_approved = 0 THEN 1 ELSE 0 END) as pending_approval_products
                               FROM products WHERE seller_id = ?");
        $stmt->execute([$sellerId]);
        $productStats = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Calculate revenue and orders
        // Note: oi.price is total price for the line item, oi.unit_price is per unit
        $stmt = $pdo->prepare("SELECT 
                               COUNT(DISTINCT o.id) as total_orders,
                               COALESCE(SUM(oi.price), 0) as total_revenue
                               FROM orders o
                               JOIN order_items oi ON o.id = oi.order_id
                               JOIN products p ON oi.product_id = p.id
                               WHERE p.seller_id = ?");
        $stmt->execute([$sellerId]);
        $orderStats = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Get commission info
        $stmt = $pdo->prepare("SELECT commission_percentage FROM sellers WHERE id = ?");
        $stmt->execute([$sellerId]);
        $seller = $stmt->fetch(PDO::FETCH_ASSOC);
        $commission = $seller['commission_percentage'] ?? 10;
        
        $totalRevenue = $orderStats['total_revenue'];
        $commissionAmount = ($totalRevenue * $commission) / 100;
        
        // Update or insert statistics
        $stmt = $pdo->prepare("INSERT INTO seller_statistics 
                               (seller_id, total_products, active_products, pending_approval_products, 
                                total_orders, total_revenue, pending_commission)
                               VALUES (?, ?, ?, ?, ?, ?, ?)
                               ON DUPLICATE KEY UPDATE
                               total_products = VALUES(total_products),
                               active_products = VALUES(active_products),
                               pending_approval_products = VALUES(pending_approval_products),
                               total_orders = VALUES(total_orders),
                               total_revenue = VALUES(total_revenue),
                               pending_commission = VALUES(pending_commission)");
        
        return $stmt->execute([
            $sellerId,
            $productStats['total_products'],
            $productStats['active_products'],
            $productStats['pending_approval_products'],
            $orderStats['total_orders'],
            $totalRevenue,
            $commissionAmount
        ]);
        
    } catch (Exception $e) {
        error_log("Error updating seller statistics: " . $e->getMessage());
        return false;
    }
}

/**
 * Log seller activity
 */
function logSellerActivity($sellerId, $activityType, $description) {
    global $pdo;
    
    $ipAddress = $_SERVER['REMOTE_ADDR'] ?? null;
    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? null;
    
    $stmt = $pdo->prepare("INSERT INTO seller_activity_log 
                           (seller_id, activity_type, activity_description, ip_address, user_agent)
                           VALUES (?, ?, ?, ?, ?)");
    
    return $stmt->execute([$sellerId, $activityType, $description, $ipAddress, $userAgent]);
}

/**
 * Approve product
 */
function approveProduct($productId, $adminId) {
    global $pdo;
    
    try {
        $pdo->beginTransaction();
        
        // Get product and seller info
        $stmt = $pdo->prepare("SELECT seller_id FROM products WHERE id = ?");
        $stmt->execute([$productId]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$product) {
            throw new Exception('Product not found');
        }
        
        // Update product status
        $stmt = $pdo->prepare("UPDATE products SET is_approved = 1, approved_at = NOW(), 
                               approved_by = ? WHERE id = ?");
        $stmt->execute([$adminId, $productId]);
        
        // Log approval history
        if ($product['seller_id']) {
            $stmt = $pdo->prepare("INSERT INTO seller_product_approval_history 
                                   (product_id, seller_id, status, action_by, comments)
                                   VALUES (?, ?, 'approved', ?, 'Product approved by admin')");
            $stmt->execute([$productId, $product['seller_id'], $adminId]);
            
            // Log activity
            logSellerActivity($product['seller_id'], 'product_approved', "Product ID {$productId} approved");
            
            // Update seller statistics
            updateSellerStatistics($product['seller_id']);
        }
        
        $pdo->commit();
        return ['success' => true];
        
    } catch (Exception $e) {
        $pdo->rollBack();
        return ['success' => false, 'message' => $e->getMessage()];
    }
}

/**
 * Reject product
 */
function rejectProduct($productId, $adminId, $reason) {
    global $pdo;
    
    try {
        $pdo->beginTransaction();
        
        // Get product and seller info
        $stmt = $pdo->prepare("SELECT seller_id, rejection_reason FROM products WHERE id = ?");
        $stmt->execute([$productId]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$product) {
            throw new Exception('Product not found');
        }
        
        // Prevent re-rejecting an already rejected product
        // Admin must wait for seller to resubmit before rejecting again
        if ($product['rejection_reason']) {
            throw new Exception('This product is already rejected. Please wait for the seller to resubmit before rejecting again.');
        }
        
        // Update product status
        $stmt = $pdo->prepare("UPDATE products SET is_approved = 0, rejection_reason = ? WHERE id = ?");
        $stmt->execute([$reason, $productId]);
        
        // Log rejection history
        if ($product['seller_id']) {
            $stmt = $pdo->prepare("INSERT INTO seller_product_approval_history 
                                   (product_id, seller_id, status, action_by, comments)
                                   VALUES (?, ?, 'rejected', ?, ?)");
            $stmt->execute([$productId, $product['seller_id'], $adminId, $reason]);
            
            // Log activity
            logSellerActivity($product['seller_id'], 'product_rejected', "Product ID {$productId} rejected: {$reason}");
            
            // Update seller statistics
            updateSellerStatistics($product['seller_id']);
        }
        
        $pdo->commit();
        return ['success' => true];
        
    } catch (Exception $e) {
        $pdo->rollBack();
        return ['success' => false, 'message' => $e->getMessage()];
    }
}

/**
 * Get pending approval products
 */
function getPendingApprovalProducts() {
    global $pdo;
    
    $stmt = $pdo->prepare("SELECT p.*, c.name as category_name, s.business_name as seller_name, 
                           s.id as seller_id
                           FROM products p
                           LEFT JOIN categories c ON p.category_id = c.id
                           LEFT JOIN sellers s ON p.seller_id = s.id
                           WHERE p.is_approved = 0 AND p.seller_id IS NOT NULL
                           ORDER BY p.created_at DESC");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Get seller dashboard data
 */
function getSellerDashboardData($sellerId) {
    $stats = getSellerStatistics($sellerId);
    $recentOrders = getSellerOrders($sellerId, ['limit' => 5]);
    $pendingProducts = getSellerProducts($sellerId, true);
    $permissions = getSellerPermissions($sellerId);
    
    return [
        'statistics' => $stats,
        'recent_orders' => $recentOrders,
        'pending_products' => array_filter($pendingProducts, function($p) {
            return $p['is_approved'] == 0 && !$p['rejection_reason'];
        }),
        'rejected_products' => array_filter($pendingProducts, function($p) {
            return $p['is_approved'] == 0 && $p['rejection_reason'];
        }),
        'permissions' => $permissions
    ];
}

/**
 * Get rejected products for seller
 */
function getRejectedProducts($sellerId) {
    global $pdo;
    
    $stmt = $pdo->prepare("SELECT p.*, c.name as category_name 
                           FROM products p
                           LEFT JOIN categories c ON p.category_id = c.id
                           WHERE p.seller_id = ? AND p.is_approved = 0 AND p.rejection_reason IS NOT NULL
                           ORDER BY p.updated_at DESC");
    $stmt->execute([$sellerId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
