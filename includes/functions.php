<?php
require_once __DIR__ . '/../config/database.php';

// Function to get all categories
function getAllCategories() {
    global $pdo;
    $stmt = $pdo->query("SELECT * FROM categories ORDER BY name");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Function to get category by slug
function getCategoryBySlug($slug) {
    global $pdo;
    
    // Debug: Log what we're searching for
    error_log("DEBUG: Searching for category with slug: '" . $slug . "'");
    
    $stmt = $pdo->prepare("SELECT * FROM categories WHERE slug = ?");
    $stmt->execute([$slug]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Debug: Log what we found
    if ($result) {
        error_log("DEBUG: Found category - ID: " . $result['id'] . ", Name: " . $result['name'] . ", Slug: " . $result['slug']);
    } else {
        error_log("DEBUG: No category found for slug: '" . $slug . "'");
    }
    
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
    
    // Debug: Log what we're searching for
    error_log("DEBUG: Searching for products with category_id: " . $categoryId);
    
    $sql = "SELECT p.*, c.name as category_name FROM products p 
            LEFT JOIN categories c ON p.category_id = c.id 
            WHERE p.category_id = ? AND p.is_active = 1 
            ORDER BY p.created_at DESC";
    
    if ($limit) {
        $sql .= " LIMIT " . (int)$limit;
    }
    
    // Debug: Log the SQL query
    error_log("DEBUG: SQL Query: " . $sql);
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$categoryId]);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Debug: Log what we found
    error_log("DEBUG: Found " . count($result) . " products for category_id: " . $categoryId);
    
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
    $sql = "SELECT p.*, c.name as category_name, c.slug as category_slug FROM products p 
            LEFT JOIN categories c ON p.category_id = c.id 
            WHERE (p.name LIKE ? OR p.description LIKE ?) AND p.is_active = 1 
            ORDER BY p.name LIMIT " . (int)$limit;
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$searchTerm, $searchTerm]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Function to calculate discount percentage
function calculateDiscountPercentage($mrp, $sellingPrice) {
    if ($mrp <= 0) return 0;
    return round((($mrp - $sellingPrice) / $mrp) * 100);
}

// Function to format price
function formatPrice($price) {
    return '₹' . number_format($price, 2);
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
        // Update quantity
        $stmt = $pdo->prepare("UPDATE cart SET quantity = quantity + ? WHERE user_id = ? AND product_id = ?");
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
        $stmt = $pdo->prepare("SELECT c.*, p.name, p.selling_price, p.mrp, p.main_image, p.stock_quantity FROM cart c JOIN products p ON c.product_id = p.id WHERE c.user_id = ?");
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
    if (isset($_SESSION['cart'][$productId])) {
        $_SESSION['cart'][$productId] += $quantity;
    } else {
        $_SESSION['cart'][$productId] = $quantity;
    }
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
        $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$productId]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($product) {
            $product['quantity'] = $qty;
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

function getDefaultAddress($userId) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM addresses WHERE user_id = ? AND is_default = 1 LIMIT 1");
    $stmt->execute([$userId]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
?> 