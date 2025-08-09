<?php
require_once __DIR__ . '/../config/database.php';

// Function to get cart summary for header display
function getCartSummary() {
    $total_items = 0;
    $total_amount = 0;
    
    if (isset($_SESSION['user_id'])) {
        // User is logged in, get cart from database
        $cartItems = getCartItems($_SESSION['user_id']);
        foreach ($cartItems as $item) {
            $total_items += $item['quantity'];
            $total_amount += ($item['selling_price'] * $item['quantity']);
        }
    } else {
        // User is not logged in, get cart from session
        $sessionCart = getSessionCartItems();
        foreach ($sessionCart as $item) {
            $total_items += $item['quantity'];
            $total_amount += ($item['selling_price'] * $item['quantity']);
        }
    }
    
    return [
        'total_items' => $total_items,
        'total_amount' => $total_amount
    ];
}

// Function to get all categories
function getAllCategories() {
    global $pdo;
    $stmt = $pdo->query("SELECT * FROM categories ORDER BY name");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Function to get all categories with real-time product counts
function getAllCategoriesWithProductCount() {
    global $pdo;
    $stmt = $pdo->query("SELECT c.*, COUNT(p.id) as product_count 
                         FROM categories c 
                         LEFT JOIN products p ON c.id = p.category_id AND p.is_active = 1 
                         GROUP BY c.id 
                         ORDER BY c.name");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Function to get all categories with recursive product counts (including subcategories)
function getAllCategoriesWithRecursiveProductCount() {
    global $pdo;
    
    // First, get all categories
    $stmt = $pdo->query("SELECT * FROM categories ORDER BY name");
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Create a map of category IDs to their recursive product counts
    $categoryProductCounts = [];
    
    foreach ($categories as $category) {
        $categoryProductCounts[$category['id']] = getRecursiveProductCount($category['id']);
    }
    
    // Update the categories array with recursive counts
    foreach ($categories as &$category) {
        $category['product_count'] = $categoryProductCounts[$category['id']];
    }
    
    return $categories;
}

// Helper function to recursively count products in a category and all its subcategories
function getRecursiveProductCount($categoryId) {
    global $pdo;
    
    // Get direct products in this category
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM products WHERE category_id = ? AND is_active = 1");
    $stmt->execute([$categoryId]);
    $directCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Get all subcategories
    $stmt = $pdo->prepare("SELECT id FROM categories WHERE parent_id = ?");
    $stmt->execute([$categoryId]);
    $subcategories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Recursively count products in subcategories
    $subcategoryCount = 0;
    foreach ($subcategories as $subcategory) {
        $subcategoryCount += getRecursiveProductCount($subcategory['id']);
    }
    
    return $directCount + $subcategoryCount;
}

// Function to get parent categories only (categories with no parent or parent_id is null)
function getParentCategories() {
    global $pdo;
    $stmt = $pdo->query("SELECT * FROM categories WHERE parent_id IS NULL ORDER BY name");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Function to get subcategories by parent category ID
function getSubcategoriesByParentId($parentId) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM categories WHERE parent_id = ? ORDER BY name");
    $stmt->execute([$parentId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Function to get category with its parent information
function getCategoryWithParent($categoryId) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT c.*, p.name as parent_name FROM categories c 
                          LEFT JOIN categories p ON c.parent_id = p.id 
                          WHERE c.id = ?");
    $stmt->execute([$categoryId]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Function to get category by slug
function getCategoryBySlug($slug) {
    global $pdo;
    
    $stmt = $pdo->prepare("SELECT * FROM categories WHERE slug = ?");
    $stmt->execute([$slug]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    return $result;
}

// Function to get product by ID
function getProductById($id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT p.*, c.name as category_name, c.slug as category_slug 
                          FROM products p 
                          LEFT JOIN categories c ON p.category_id = c.id 
                          WHERE p.id = ? AND p.is_active = 1");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Function to get products by category
function getProductsByCategory($categoryId, $limit = null) {
    global $pdo;
    
    $sql = "SELECT p.*, c.name as category_name FROM products p 
            LEFT JOIN categories c ON p.category_id = c.id 
            WHERE p.category_id = ? AND p.is_active = 1 
            ORDER BY p.created_at DESC";
    
    if ($limit) {
        $sql .= " LIMIT " . (int)$limit;
    }
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$categoryId]);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    return $result;
}

// Function to get featured products
function getFeaturedProducts($limit = 8) {
    global $pdo;
    $sql = "SELECT p.*, c.name as category_name FROM products p 
            LEFT JOIN categories c ON p.category_id = c.id 
            WHERE p.is_featured = 1 AND p.is_active = 1 
            ORDER BY p.created_at DESC LIMIT " . (int)$limit;
    $stmt = $pdo->query($sql);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Function to get discounted products
function getDiscountedProducts($limit = 8) {
    global $pdo;
    $sql = "SELECT p.*, c.name as category_name FROM products p 
            LEFT JOIN categories c ON p.category_id = c.id 
            WHERE p.is_discounted = 1 AND p.is_active = 1 
            ORDER BY p.discount_percentage DESC LIMIT " . (int)$limit;
    $stmt = $pdo->query($sql);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Function to get all products
function getAllProducts($limit = null) {
    global $pdo;
    $sql = "SELECT p.*, c.name as category_name FROM products p 
            LEFT JOIN categories c ON p.category_id = c.id 
            WHERE p.is_active = 1 
            ORDER BY p.created_at DESC";
    
    if ($limit) {
        $sql .= " LIMIT " . (int)$limit;
    }
    
    $stmt = $pdo->query($sql);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Function to get product by slug
function getProductBySlug($slug) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT p.*, c.name as category_name, c.slug as category_slug 
                          FROM products p 
                          LEFT JOIN categories c ON p.category_id = c.id 
                          WHERE p.slug = ? AND p.is_active = 1");
    $stmt->execute([$slug]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Function to get product images
function getProductImages($productId) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM product_images WHERE product_id = ? ORDER BY sort_order, is_main DESC");
    $stmt->execute([$productId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Function to get related products
function getRelatedProducts($productId, $categoryId, $limit = 4) {
    global $pdo;
    $sql = "SELECT p.*, c.name as category_name FROM products p 
            LEFT JOIN categories c ON p.category_id = c.id 
            WHERE p.category_id = ? AND p.id != ? AND p.is_active = 1 
            ORDER BY RAND() LIMIT " . (int)$limit;
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$categoryId, $productId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Function to search products
function searchProducts($query, $limit = 20) {
    global $pdo;
    $searchTerm = "%$query%";
    
    // Enhanced search query that includes category names and their parent/grandparent categories
    $sql = "SELECT p.*, c.name as category_name, c.slug as category_slug FROM products p 
            LEFT JOIN categories c ON p.category_id = c.id 
            WHERE (p.name LIKE ? OR p.description LIKE ? OR c.name LIKE ? OR EXISTS (
                SELECT 1 FROM categories parent 
                WHERE parent.id = c.parent_id AND parent.name LIKE ?
            ) OR EXISTS (
                SELECT 1 FROM categories grandparent 
                JOIN categories parent ON parent.parent_id = grandparent.id 
                WHERE parent.id = c.parent_id AND grandparent.name LIKE ?
            )) AND p.is_active = 1 
            ORDER BY p.name LIMIT " . (int)$limit;
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Function to calculate discount percentage
function calculateDiscountPercentage($mrp, $sellingPrice) {
    if ($mrp <= 0) return 0;
    return round((($mrp - $sellingPrice) / $mrp) * 100);
}

// Function to clean product name (remove HTML entities and tags)
function cleanProductName($name) {
    // Decode HTML entities first
    $cleaned = html_entity_decode($name, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    // Remove any remaining HTML tags
    $cleaned = strip_tags($cleaned);
    // Trim whitespace
    return trim($cleaned);
}

// Function to format price
function formatPrice($price) {
    return '₹ ' . number_format($price, 0, '.', '');
}

// Function to check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Function to get current user
function getCurrentUser() {
    if (!isLoggedIn()) return null;
    
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Function to add to cart
function addToCart($userId, $productId, $quantity = 1) {
    global $pdo;
    
    // Check if product already in cart
    $stmt = $pdo->prepare("SELECT * FROM cart WHERE user_id = ? AND product_id = ?");
    $stmt->execute([$userId, $productId]);
    $existing = $stmt->fetch();
    
    if ($existing) {
        // Update quantity (replace, don't add)
        $stmt = $pdo->prepare("UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ?");
        return $stmt->execute([$quantity, $userId, $productId]);
    } else {
        // Add new item
        $stmt = $pdo->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)");
        return $stmt->execute([$userId, $productId, $quantity]);
    }
}

// Function to get cart items
function getCartItems($userId = null) {
    if (isLoggedIn() && $userId) {
        // DB cart
        global $pdo;
        $stmt = $pdo->prepare("SELECT c.*, p.name, p.selling_price, p.mrp, p.main_image, p.slug, p.stock_quantity, p.gst_type, p.gst_rate, p.shipping_charge, p.hsn FROM cart c JOIN products p ON c.product_id = p.id WHERE c.user_id = ?");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        // Session cart
        return getSessionCartItems();
    }
}

// Function to add to wishlist
function addToWishlist($userId, $productId) {
    global $pdo;
    
    if (isInWishlist($userId, $productId)) {
        return false; // Already in wishlist
    }
    
    $stmt = $pdo->prepare("INSERT INTO wishlist (user_id, product_id) VALUES (?, ?)");
    return $stmt->execute([$userId, $productId]);
}

// Function to remove from wishlist
function removeFromWishlist($userId, $productId) {
    global $pdo;
    $stmt = $pdo->prepare("DELETE FROM wishlist WHERE user_id = ? AND product_id = ?");
    return $stmt->execute([$userId, $productId]);
}

// Function to get wishlist items
function getWishlistItems($userId = null) {
    if (isLoggedIn() && $userId) {
        // DB wishlist
        global $pdo;
        $stmt = $pdo->prepare("SELECT w.*, p.name, p.selling_price, p.mrp, p.main_image, p.slug FROM wishlist w JOIN products p ON w.product_id = p.id WHERE w.user_id = ?");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        // Session wishlist
        return getSessionWishlistItems();
    }
}

// Function to check if product is in wishlist
function isInWishlist($userId, $productId) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM wishlist WHERE user_id = ? AND product_id = ?");
    $stmt->execute([$userId, $productId]);
    return $stmt->fetch() ? true : false;
}

// Function to sanitize input
function sanitizeInput($input) {
    return htmlspecialchars(strip_tags(trim($input)));
}

// Function to generate random string
function generateRandomString($length = 10) {
    return substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);
}

// --- SESSION-BASED CART & WISHLIST FOR GUESTS ---
function addToSessionCart($productId, $quantity = 1) {
    if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
    // Always set the quantity (replace, don't add)
    $_SESSION['cart'][$productId] = $quantity;
    return true;
}

function removeFromSessionCart($productId) {
    if (isset($_SESSION['cart'][$productId])) {
        unset($_SESSION['cart'][$productId]);
        return true;
    }
    return false;
}

function getSessionCartItems() {
    global $pdo;
    $items = [];
    if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) return $items;
    foreach ($_SESSION['cart'] as $productId => $qty) {
        $stmt = $pdo->prepare("SELECT id, name, selling_price, mrp, main_image, slug, gst_type, gst_rate, shipping_charge, hsn FROM products WHERE id = ?");
        $stmt->execute([$productId]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($product) {
            $product['quantity'] = $qty;
            $product['product_id'] = $product['id'];
            $items[] = $product;
        }
    }
    return $items;
}

function addToSessionWishlist($productId) {
    if (!isset($_SESSION['wishlist'])) $_SESSION['wishlist'] = [];
    if (!in_array($productId, $_SESSION['wishlist'])) {
        $_SESSION['wishlist'][] = $productId;
        return true;
    }
    return false;
}

function removeFromSessionWishlist($productId) {
    if (isset($_SESSION['wishlist'])) {
        $key = array_search($productId, $_SESSION['wishlist']);
        if ($key !== false) {
            unset($_SESSION['wishlist'][$key]);
            $_SESSION['wishlist'] = array_values($_SESSION['wishlist']);
            return true;
        }
    }
    return false;
}

function getSessionWishlistItems() {
    global $pdo;
    $items = [];
    if (!isset($_SESSION['wishlist']) || empty($_SESSION['wishlist'])) return $items;
    foreach ($_SESSION['wishlist'] as $productId) {
        $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$productId]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($product) {
            $product['product_id'] = $product['id'];
            $items[] = $product;
        }
    }
    return $items;
}

// --- ADDRESS MANAGEMENT ---
function getUserAddresses($userId) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM addresses WHERE user_id = ? ORDER BY is_default DESC, created_at DESC");
    $stmt->execute([$userId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function addUserAddress($userId, $data) {
    global $pdo;
    $sql = "INSERT INTO addresses (user_id, name, phone, pincode, address_line1, address_line2, city, state, is_default) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([
        $userId,
        $data['name'],
        $data['phone'],
        $data['pincode'],
        $data['address_line1'],
        $data['address_line2'],
        $data['city'],
        $data['state'],
        !empty($data['is_default']) ? 1 : 0
    ]);
}

function setDefaultAddress($userId, $addressId) {
    global $pdo;
    // Unset all
    $pdo->prepare("UPDATE addresses SET is_default = 0 WHERE user_id = ?")->execute([$userId]);
    // Set one
    $pdo->prepare("UPDATE addresses SET is_default = 1 WHERE user_id = ? AND id = ?")->execute([$userId, $addressId]);
}

function updateUserAddress($userId, $addressId, $data) {
    global $pdo;
    $sql = "UPDATE addresses SET name = ?, phone = ?, pincode = ?, address_line1 = ?, address_line2 = ?, city = ?, state = ?, is_default = ? WHERE user_id = ? AND id = ?";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([
        $data['name'],
        $data['phone'],
        $data['pincode'],
        $data['address_line1'],
        $data['address_line2'],
        $data['city'],
        $data['state'],
        !empty($data['is_default']) ? 1 : 0,
        $userId,
        $addressId
    ]);
}

function deleteUserAddress($userId, $addressId) {
    global $pdo;
    $stmt = $pdo->prepare("DELETE FROM addresses WHERE user_id = ? AND id = ?");
    return $stmt->execute([$userId, $addressId]);
}

function getDefaultAddress($userId) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM addresses WHERE user_id = ? AND is_default = 1 LIMIT 1");
    $stmt->execute([$userId]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// GST Calculation Functions
function calculateGSTAmount($price, $gstRate) {
    return ($price * $gstRate) / 100;
}

function getGSTBreakdown($price, $gstType, $gstRate) {
    $gstAmount = calculateGSTAmount($price, $gstRate);
    
    if ($gstType === 'sgst_cgst') {
        $sgstRate = $cgstRate = $gstRate / 2;
        return [
            'total_gst' => $gstAmount,
            'sgst_rate' => $sgstRate,
            'cgst_rate' => $cgstRate,
            'sgst_amount' => $gstAmount / 2,
            'cgst_amount' => $gstAmount / 2,
            'igst_amount' => 0
        ];
    } else {
        return [
            'total_gst' => $gstAmount,
            'sgst_rate' => 0,
            'cgst_rate' => 0,
            'sgst_amount' => 0,
            'cgst_amount' => 0,
            'igst_amount' => $gstAmount
        ];
    }
}

function calculateCartTotals($cartItems) {
    $subtotal = 0;
    $totalGST = 0;
    $totalShipping = 0;
    $sgstTotal = 0;
    $cgstTotal = 0;
    $igstTotal = 0;
    
    foreach ($cartItems as $item) {
        $itemTotal = $item['selling_price'] * $item['quantity'];
        $subtotal += $itemTotal;
        
        // Calculate GST for this item
        $gstBreakdown = getGSTBreakdown($itemTotal, $item['gst_type'], $item['gst_rate']);
        $totalGST += $gstBreakdown['total_gst'];
        $sgstTotal += $gstBreakdown['sgst_amount'];
        $cgstTotal += $gstBreakdown['cgst_amount'];
        $igstTotal += $gstBreakdown['igst_amount'];
        
        // Add shipping charge if exists
        if ($item['shipping_charge'] !== null) {
            $totalShipping += $item['shipping_charge'];
        }
    }
    
    $grandTotal = $subtotal + $totalGST + $totalShipping;
    
    return [
        'subtotal' => $subtotal,
        'total_gst' => $totalGST,
        'sgst_total' => $sgstTotal,
        'cgst_total' => $cgstTotal,
        'igst_total' => $igstTotal,
        'total_shipping' => $totalShipping,
        'grand_total' => $grandTotal
    ];
}

// Order Management Functions
function generateTrackingId() {
    global $pdo;
    do {
        $trackingId = 'EverythingB2C' . str_pad(mt_rand(1, 99999999), 8, '0', STR_PAD_LEFT);
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM orders WHERE tracking_id = ?");
        $stmt->execute([$trackingId]);
    } while ($stmt->fetchColumn() > 0);
    return $trackingId;
}

function generateOrderNumber() {
    global $pdo;
    do {
        $orderNumber = 'ORDER' . date('Ymd') . str_pad(mt_rand(0, 9999), 4, '0', STR_PAD_LEFT);
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM orders WHERE order_number = ?");
        $stmt->execute([$orderNumber]);
    } while ($stmt->fetchColumn() > 0);
    return $orderNumber;
}

function createOrder($userId, $addressId, $paymentMethod, $gstNumber = null, $companyName = null, $isBusinessPurchase = false, $upiTransactionId = null, $upiScreenshot = null) {
    global $pdo;
    
    try {
        $pdo->beginTransaction();
        
        // Get cart items and address
        $cartItems = getCartItems($userId);
        if (empty($cartItems)) {
            throw new Exception('Cart is empty');
        }
        $stmt = $pdo->prepare("SELECT * FROM addresses WHERE id = ? AND user_id = ?");
        $stmt->execute([$addressId, $userId]);
        $address = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$address) {
            throw new Exception('Invalid address');
        }
        // Use the same calculation as checkout page
        require_once __DIR__ . '/gst_shipping_functions.php';
        $totals = calculateOrderTotal(
            $cartItems,
            $address['state'],
            $address['city'],
            $address['pincode']
        );
        $trackingId = generateTrackingId();
        $orderNumber = generateOrderNumber();
        // --- Direct Payment: add UPI fields if provided ---
        $columns = "user_id, address_id, order_number, tracking_id, total_amount, subtotal, gst_amount, shipping_charge, payment_method, gst_number, company_name, is_business_purchase, order_status_id, payment_status";
        $placeholders = "?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1, 'pending'";
        $values = [
            $userId,
            $addressId,
            $orderNumber,
            $trackingId,
            $totals['total'],
            $totals['subtotal'],
            $totals['gst_amount'],
            $totals['shipping_charge'],
            $paymentMethod,
            $gstNumber,
            $companyName,
            $isBusinessPurchase ? 1 : 0
        ];
        if ($paymentMethod === 'direct_payment') {
            $columns .= ", upi_transaction_id, upi_screenshot";
            $placeholders .= ", ?, ?";
            $values[] = $upiTransactionId;
            $values[] = $upiScreenshot;
        }
        $stmt = $pdo->prepare("INSERT INTO orders ($columns) VALUES ($placeholders)");
        $stmt->execute($values);
        $orderId = $pdo->lastInsertId();
        // Add order items (use GST logic from gst_shipping_functions.php)
        $seller_state = 'Maharashtra';
        foreach ($cartItems as $item) {
            $item_price = isset($item['selling_price']) ? $item['selling_price'] : 0;
            $item_total = $item_price * $item['quantity'];
            $gst_calc = function_exists('calculateGST')
                ? calculateGST($item_price, $item['gst_rate'], $item['gst_type'], $address['state'], $seller_state)
                : getGSTBreakdown($item_total, $item['gst_type'], $item['gst_rate']);
            $item_gst = $gst_calc['total_gst'] * $item['quantity'];
            $stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, hsn, quantity, price, unit_price, gst_rate, gst_amount, mrp, selling_price) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $orderId,
                $item['product_id'],
                $item['hsn'],
                $item['quantity'],
                $item_total,
                $item_price,
                $item['gst_rate'],
                $item_gst,
                $item['mrp'],
                $item['selling_price']
            ]);
        }
        // Add initial status history
        addOrderStatusHistory($orderId, 1, 'Order placed successfully', 'system');
        // Clear cart
        $stmt = $pdo->prepare("DELETE FROM cart WHERE user_id = ?");
        $stmt->execute([$userId]);
        $pdo->commit();
        return ['success' => true, 'order_id' => $orderId, 'tracking_id' => $trackingId, 'order_number' => $orderNumber];
    } catch (Exception $e) {
        $pdo->rollBack();
        return ['success' => false, 'message' => $e->getMessage()];
    }
}

function addOrderStatusHistory($orderId, $statusId, $description = null, $updatedBy = 'system') {
    global $pdo;
    $stmt = $pdo->prepare("INSERT INTO order_status_history (order_id, order_status_id, status_description, updated_by) VALUES (?, ?, ?, ?)");
    return $stmt->execute([$orderId, $statusId, $description, $updatedBy]);
}

function updateOrderStatus($orderId, $statusId, $description = null, $externalTrackingId = null, $externalTrackingLink = null, $estimatedDeliveryDate = null) {
    global $pdo;
    
    try {
        $pdo->beginTransaction();
        
        // Update order status
        $stmt = $pdo->prepare("UPDATE orders SET order_status_id = ?, status_description = ?, external_tracking_id = ?, external_tracking_link = ?, estimated_delivery_date = ? WHERE id = ?");
        $stmt->execute([$statusId, $description, $externalTrackingId, $externalTrackingLink, $estimatedDeliveryDate, $orderId]);
        
        // Add to status history
        addOrderStatusHistory($orderId, $statusId, $description, 'admin');
        
        $pdo->commit();
        return true;
        
    } catch (Exception $e) {
        $pdo->rollBack();
        return false;
    }
}

function getOrderById($orderId, $userId = null) {
    global $pdo;
    $sql = "SELECT o.*, os.name as status_name, os.color as status_color, os.description as status_description,
                   a.name as address_name, a.phone as address_phone, a.address_line1, a.address_line2, a.city, a.state, a.pincode
            FROM orders o 
            LEFT JOIN order_statuses os ON o.order_status_id = os.id
            LEFT JOIN addresses a ON o.address_id = a.id
            WHERE o.id = ?";
    
    if ($userId) {
        $sql .= " AND o.user_id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$orderId, $userId]);
    } else {
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$orderId]);
    }
    
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function getOrderItems($orderId) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT oi.*, p.name, p.main_image, p.slug, p.sku, oi.hsn 
                          FROM order_items oi 
                          JOIN products p ON oi.product_id = p.id 
                          WHERE oi.order_id = ?");
    $stmt->execute([$orderId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getOrderStatusHistory($orderId) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT osh.*, os.name as status_name, os.color as status_color 
                          FROM order_status_history osh 
                          JOIN order_statuses os ON osh.order_status_id = os.id 
                          WHERE osh.order_id = ? 
                          ORDER BY osh.created_at DESC");
    $stmt->execute([$orderId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getUserOrders($userId, $limit = null) {
    global $pdo;
    $sql = "SELECT o.*, os.name as status_name, os.color as status_color, a.state as state FROM orders o 
            LEFT JOIN order_statuses os ON o.order_status_id = os.id 
            LEFT JOIN addresses a ON o.address_id = a.id 
            WHERE o.user_id = ? 
            ORDER BY o.created_at DESC";
    if ($limit) {
        $sql .= " LIMIT " . intval($limit);
    }
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$userId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getAllOrderStatuses() {
    global $pdo;
    $stmt = $pdo->query("SELECT * FROM order_statuses ORDER BY sort_order, name");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function createCustomOrderStatus($name, $description, $color = '#007bff') {
    global $pdo;
    $stmt = $pdo->prepare("INSERT INTO order_statuses (name, description, color, is_system, sort_order) VALUES (?, ?, ?, FALSE, (SELECT COALESCE(MAX(sort_order), 0) + 1 FROM order_statuses s))");
    return $stmt->execute([$name, $description, $color]);
}

function updateOrderStatusRecord($statusId, $name, $description, $color) {
    global $pdo;
    $stmt = $pdo->prepare("UPDATE order_statuses SET name = ?, description = ?, color = ? WHERE id = ? AND is_system = FALSE");
    return $stmt->execute([$name, $description, $color, $statusId]);
}

function deleteCustomOrderStatus($statusId) {
    global $pdo;
    // Check if status is in use
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM orders WHERE order_status_id = ?");
    $stmt->execute([$statusId]);
    if ($stmt->fetchColumn() > 0) {
        return false; // Status is in use
    }
    
    $stmt = $pdo->prepare("DELETE FROM order_statuses WHERE id = ? AND is_system = FALSE");
    return $stmt->execute([$statusId]);
}

// Payment Functions
function createRazorpayOrder($amount, $currency = 'INR') {
    // This would integrate with Razorpay API
    // For now, return a mock response
    return [
        'id' => 'order_' . uniqid(),
        'amount' => $amount * 100, // Razorpay expects amount in paise
        'currency' => $currency
    ];
}

function verifyRazorpayPayment($razorpayOrderId, $razorpayPaymentId, $razorpaySignature) {
    // This would verify the payment with Razorpay
    // For now, return true
    return true;
}

function updatePaymentStatus($orderId, $paymentStatus, $razorpayOrderId = null, $razorpayPaymentId = null) {
    global $pdo;
    $stmt = $pdo->prepare("UPDATE orders SET payment_status = ?, razorpay_order_id = ?, razorpay_payment_id = ? WHERE id = ?");
    return $stmt->execute([$paymentStatus, $razorpayOrderId, $razorpayPaymentId, $orderId]);
}

// Helper function to build a nested category tree with level
function buildCategoryTree(array $categories, $parentId = null, $level = 0) {
    $branch = [];
    foreach ($categories as $category) {
        if ($category['parent_id'] == $parentId) {
            $category['level'] = $level;
            $children = buildCategoryTree($categories, $category['id'], $level + 1);
            if ($children) {
                $category['children'] = $children;
            } else {
                $category['children'] = [];
            }
            $branch[] = $category;
        }
    }
    return $branch;
}

// Helper function to get the full category path (main > sub)
function getCategoryPath($categoryId, $categories = null) {
    global $pdo;
    if ($categories === null) {
        $categories = getAllCategories();
    }
    $catMap = [];
    foreach ($categories as $cat) {
        $catMap[$cat['id']] = $cat;
    }
    $path = [];
    $current = $categoryId;
    while ($current && isset($catMap[$current])) {
        array_unshift($path, $catMap[$current]);
        $current = $catMap[$current]['parent_id'];
    }
    return $path;
}

// Generate breadcrumb navigation
function generateBreadcrumb($pageTitle, $categoryPath = null, $productName = null) {
    $breadcrumbs = [];
    
    // Always start with Home
    $breadcrumbs[] = [
        'title' => 'Home',
        'url' => 'index.php',
        'active' => false
    ];
    
    // Add category path if provided
    if ($categoryPath && is_array($categoryPath)) {
        foreach ($categoryPath as $category) {
            $breadcrumbs[] = [
                'title' => $category['name'],
                'url' => 'category.php?slug=' . $category['slug'],
                'active' => false
            ];
        }
    }
    
    // Add current page/product - avoid repetition
    if ($productName) {
        // Check if the product name is already in the breadcrumb (from category path)
        $lastCategoryName = null;
        if ($categoryPath && !empty($categoryPath)) {
            $lastCategoryName = end($categoryPath)['name'];
        }
        
        // Only add product name if it's different from the last category
        if ($productName !== $lastCategoryName) {
            $breadcrumbs[] = [
                'title' => $productName,
                'url' => '#',
                'active' => true
            ];
        } else {
            // Mark the last category as active instead
            if (!empty($breadcrumbs)) {
                $breadcrumbs[count($breadcrumbs) - 1]['active'] = true;
            }
        }
    } else {
        // Check if page title is already in breadcrumb (from category path)
        $lastCategoryName = null;
        if ($categoryPath && !empty($categoryPath)) {
            $lastCategoryName = end($categoryPath)['name'];
        }
        
        // Only add page title if it's different from the last category
        if ($pageTitle !== $lastCategoryName) {
            $breadcrumbs[] = [
                'title' => $pageTitle,
                'url' => '#',
                'active' => true
            ];
        } else {
            // Mark the last category as active instead
            if (!empty($breadcrumbs)) {
                $breadcrumbs[count($breadcrumbs) - 1]['active'] = true;
            }
        }
    }
    
    return $breadcrumbs;
}

// Render breadcrumb HTML
function renderBreadcrumb($breadcrumbs) {
    $html = '<nav aria-label="breadcrumb" class="breadcrumb-nav">';
    $html .= '<ol class="breadcrumb">';
    
    foreach ($breadcrumbs as $index => $crumb) {
        $isLast = $index === count($breadcrumbs) - 1;
        
        if ($isLast || $crumb['active']) {
            $html .= '<li class="breadcrumb-item active" aria-current="page">';
            $html .= htmlspecialchars($crumb['title']);
            $html .= '</li>';
        } else {
            $html .= '<li class="breadcrumb-item">';
            $html .= '<a href="' . htmlspecialchars($crumb['url']) . '">';
            $html .= htmlspecialchars($crumb['title']);
            $html .= '</a>';
            $html .= '</li>';
        }
    }
    
    $html .= '</ol>';
    $html .= '</nav>';
    
    return $html;
}

// Merge session cart into user cart after login
function mergeSessionCartToUserCart($userId) {
    if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) return;
    foreach ($_SESSION['cart'] as $productId => $qty) {
        addToCart($userId, $productId, $qty);
    }
    clearSessionCart();
}

// Clear session cart
function clearSessionCart() {
    unset($_SESSION['cart']);
    return true; // Always return true since unset() doesn't return a boolean
}

// Clear user's database cart
function clearUserCart($userId) {
    global $pdo;
    $stmt = $pdo->prepare("DELETE FROM cart WHERE user_id = ?");
    return $stmt->execute([$userId]);
}
?> 