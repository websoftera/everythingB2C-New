-- Seller/Vendor Management System Schema
-- This schema adds multi-vendor functionality to EverythingB2C

-- Add seller role to users table
ALTER TABLE users 
ADD COLUMN IF NOT EXISTS user_role ENUM('customer', 'seller', 'admin') DEFAULT 'customer' AFTER password,
ADD COLUMN IF NOT EXISTS is_seller_approved TINYINT(1) DEFAULT 0 AFTER user_role,
ADD COLUMN IF NOT EXISTS seller_approved_at TIMESTAMP NULL AFTER is_seller_approved,
ADD COLUMN IF NOT EXISTS seller_approved_by INT NULL AFTER seller_approved_at;

-- Create sellers table for additional seller information
CREATE TABLE IF NOT EXISTS sellers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    business_name VARCHAR(255) NOT NULL,
    business_type VARCHAR(100) DEFAULT NULL,
    gst_number VARCHAR(50) DEFAULT NULL,
    pan_number VARCHAR(20) DEFAULT NULL,
    business_address TEXT DEFAULT NULL,
    business_email VARCHAR(255) DEFAULT NULL,
    business_phone VARCHAR(20) DEFAULT NULL,
    bank_account_name VARCHAR(255) DEFAULT NULL,
    bank_account_number VARCHAR(50) DEFAULT NULL,
    bank_ifsc_code VARCHAR(20) DEFAULT NULL,
    bank_name VARCHAR(255) DEFAULT NULL,
    commission_percentage DECIMAL(5,2) DEFAULT 10.00,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_seller (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Add seller_id to products table
ALTER TABLE products 
ADD COLUMN IF NOT EXISTS seller_id INT NULL AFTER id,
ADD COLUMN IF NOT EXISTS is_approved TINYINT(1) DEFAULT 1 AFTER is_active,
ADD COLUMN IF NOT EXISTS approved_at TIMESTAMP NULL AFTER is_approved,
ADD COLUMN IF NOT EXISTS approved_by INT NULL AFTER approved_at,
ADD COLUMN IF NOT EXISTS rejection_reason TEXT NULL AFTER approved_by;

-- Add index for seller products
ALTER TABLE products ADD INDEX IF NOT EXISTS idx_seller_id (seller_id);
ALTER TABLE products ADD INDEX IF NOT EXISTS idx_is_approved (is_approved);

-- Add seller foreign key (if products already exist, seller_id will be NULL for admin products)
-- Drop constraint if it exists first
SET @constraint_exists = (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS 
                          WHERE CONSTRAINT_SCHEMA = DATABASE() 
                          AND TABLE_NAME = 'products' 
                          AND CONSTRAINT_NAME = 'fk_products_seller');
SET @sql = IF(@constraint_exists > 0, 'ALTER TABLE products DROP FOREIGN KEY fk_products_seller', 'SELECT "Constraint does not exist"');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Now add the constraint
ALTER TABLE products 
ADD CONSTRAINT fk_products_seller 
FOREIGN KEY (seller_id) REFERENCES sellers(id) ON DELETE SET NULL;

-- Add seller_id to categories table
ALTER TABLE categories 
ADD COLUMN IF NOT EXISTS seller_id INT NULL AFTER id,
ADD COLUMN IF NOT EXISTS is_approved TINYINT(1) DEFAULT 1 AFTER product_count;

-- Add index for seller categories
ALTER TABLE categories ADD INDEX IF NOT EXISTS idx_seller_id (seller_id);

-- Add seller foreign key to categories
-- Drop constraint if it exists first
SET @constraint_exists = (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS 
                          WHERE CONSTRAINT_SCHEMA = DATABASE() 
                          AND TABLE_NAME = 'categories' 
                          AND CONSTRAINT_NAME = 'fk_categories_seller');
SET @sql = IF(@constraint_exists > 0, 'ALTER TABLE categories DROP FOREIGN KEY fk_categories_seller', 'SELECT "Constraint does not exist"');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Now add the constraint
ALTER TABLE categories 
ADD CONSTRAINT fk_categories_seller 
FOREIGN KEY (seller_id) REFERENCES sellers(id) ON DELETE SET NULL;

-- Create seller_product_approval_history table
CREATE TABLE IF NOT EXISTS seller_product_approval_history (
    id INT PRIMARY KEY AUTO_INCREMENT,
    product_id INT NOT NULL,
    seller_id INT NOT NULL,
    status ENUM('pending', 'approved', 'rejected') NOT NULL,
    action_by INT NOT NULL,
    action_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    comments TEXT DEFAULT NULL,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (seller_id) REFERENCES sellers(id) ON DELETE CASCADE,
    FOREIGN KEY (action_by) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create seller_permissions table
CREATE TABLE IF NOT EXISTS seller_permissions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    seller_id INT NOT NULL,
    can_manage_products TINYINT(1) DEFAULT 1,
    can_manage_categories TINYINT(1) DEFAULT 1,
    can_view_orders TINYINT(1) DEFAULT 1,
    can_view_reports TINYINT(1) DEFAULT 1,
    can_update_settings TINYINT(1) DEFAULT 1,
    can_add_products TINYINT(1) DEFAULT 1,
    can_edit_products TINYINT(1) DEFAULT 1,
    can_delete_products TINYINT(1) DEFAULT 0,
    max_products INT DEFAULT 100,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (seller_id) REFERENCES sellers(id) ON DELETE CASCADE,
    UNIQUE KEY unique_seller_permissions (seller_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create seller_activity_log table
CREATE TABLE IF NOT EXISTS seller_activity_log (
    id INT PRIMARY KEY AUTO_INCREMENT,
    seller_id INT NOT NULL,
    activity_type VARCHAR(100) NOT NULL,
    activity_description TEXT NOT NULL,
    ip_address VARCHAR(45) DEFAULT NULL,
    user_agent TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (seller_id) REFERENCES sellers(id) ON DELETE CASCADE,
    INDEX idx_seller_activity (seller_id, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create seller_statistics table for quick access to seller stats
CREATE TABLE IF NOT EXISTS seller_statistics (
    id INT PRIMARY KEY AUTO_INCREMENT,
    seller_id INT NOT NULL,
    total_products INT DEFAULT 0,
    active_products INT DEFAULT 0,
    pending_approval_products INT DEFAULT 0,
    total_orders INT DEFAULT 0,
    total_revenue DECIMAL(10,2) DEFAULT 0.00,
    commission_paid DECIMAL(10,2) DEFAULT 0.00,
    pending_commission DECIMAL(10,2) DEFAULT 0.00,
    last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (seller_id) REFERENCES sellers(id) ON DELETE CASCADE,
    UNIQUE KEY unique_seller_stats (seller_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Add seller_id to orders table to track which seller fulfilled the order
ALTER TABLE orders 
ADD COLUMN IF NOT EXISTS seller_id INT NULL AFTER user_id;

-- Add index for seller orders
ALTER TABLE orders ADD INDEX IF NOT EXISTS idx_seller_orders (seller_id);

-- Note: We'll use order_items to determine which seller fulfilled each item
-- since an order might have items from multiple sellers

-- Create seller_order_items view for easy seller order tracking
CREATE OR REPLACE VIEW seller_order_items AS
SELECT 
    oi.id,
    oi.order_id,
    o.order_number,
    o.tracking_id,
    o.created_at as order_date,
    o.total_amount as order_total,
    o.order_status_id,
    os.name as order_status,
    oi.product_id,
    p.name as product_name,
    p.seller_id,
    s.business_name as seller_name,
    oi.quantity,
    oi.price,
    oi.unit_price,
    oi.gst_amount,
    (oi.price * oi.quantity) as item_total,
    u.name as customer_name,
    u.email,
    u.phone
FROM order_items oi
JOIN products p ON oi.product_id = p.id
LEFT JOIN sellers s ON p.seller_id = s.id
JOIN orders o ON oi.order_id = o.id
LEFT JOIN order_statuses os ON o.order_status_id = os.id
JOIN users u ON o.user_id = u.id;

-- Insert default permissions for future sellers
-- This will be used as a template when creating new sellers

-- Create indexes for better performance
CREATE INDEX IF NOT EXISTS idx_products_seller_approved ON products(seller_id, is_approved, is_active);
CREATE INDEX IF NOT EXISTS idx_categories_seller ON categories(seller_id);

-- Insert sample admin user role update (if you have an admin user with id=1)
-- UPDATE users SET user_role = 'admin' WHERE id = 1;

-- End of Seller System Schema
