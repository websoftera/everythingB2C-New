-- Order Management Schema (Fixed Version)
-- This file contains all the necessary tables for order management, tracking, and payment integration

-- Custom Order Statuses Table
CREATE TABLE IF NOT EXISTS order_statuses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    color VARCHAR(20) DEFAULT '#007bff',
    is_system BOOLEAN DEFAULT FALSE,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert default system order statuses (ignore duplicates)
INSERT IGNORE INTO order_statuses (name, description, color, is_system, sort_order) VALUES
('Pending', 'Order has been placed and is awaiting confirmation', '#ffc107', TRUE, 1),
('Processing', 'Order is being processed and prepared for shipping', '#17a2b8', TRUE, 2),
('Shipped', 'Order has been shipped from our warehouse', '#28a745', TRUE, 3),
('In Transit', 'Order is in transit to delivery location', '#6f42c1', TRUE, 4),
('Out for Delivery', 'Order is out for delivery to your address', '#fd7e14', TRUE, 5),
('Delivered', 'Order has been successfully delivered', '#20c997', TRUE, 6),
('Canceled', 'Order has been canceled', '#dc3545', TRUE, 7);

-- Update orders table to include tracking and payment information
-- Add columns one by one to avoid errors

-- Check and add tracking_id column
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = 'EverythingB2C' AND TABLE_NAME = 'orders' AND COLUMN_NAME = 'tracking_id') = 0,
    'ALTER TABLE orders ADD COLUMN tracking_id VARCHAR(50) UNIQUE',
    'SELECT "tracking_id column already exists" as message'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Check and add external_tracking_id column
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = 'EverythingB2C' AND TABLE_NAME = 'orders' AND COLUMN_NAME = 'external_tracking_id') = 0,
    'ALTER TABLE orders ADD COLUMN external_tracking_id VARCHAR(100)',
    'SELECT "external_tracking_id column already exists" as message'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Check and add external_tracking_link column
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = 'EverythingB2C' AND TABLE_NAME = 'orders' AND COLUMN_NAME = 'external_tracking_link') = 0,
    'ALTER TABLE orders ADD COLUMN external_tracking_link TEXT',
    'SELECT "external_tracking_link column already exists" as message'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Check and add order_status_id column
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = 'EverythingB2C' AND TABLE_NAME = 'orders' AND COLUMN_NAME = 'order_status_id') = 0,
    'ALTER TABLE orders ADD COLUMN order_status_id INT DEFAULT 1',
    'SELECT "order_status_id column already exists" as message'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Check and add payment_method column
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = 'EverythingB2C' AND TABLE_NAME = 'orders' AND COLUMN_NAME = 'payment_method') = 0,
    'ALTER TABLE orders ADD COLUMN payment_method ENUM("cod", "razorpay") DEFAULT "cod"',
    'SELECT "payment_method column already exists" as message'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Check and add payment_status column
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = 'EverythingB2C' AND TABLE_NAME = 'orders' AND COLUMN_NAME = 'payment_status') = 0,
    'ALTER TABLE orders ADD COLUMN payment_status ENUM("pending", "paid", "failed", "refunded") DEFAULT "pending"',
    'SELECT "payment_status column already exists" as message'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Check and add razorpay_order_id column
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = 'EverythingB2C' AND TABLE_NAME = 'orders' AND COLUMN_NAME = 'razorpay_order_id') = 0,
    'ALTER TABLE orders ADD COLUMN razorpay_order_id VARCHAR(100)',
    'SELECT "razorpay_order_id column already exists" as message'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Check and add razorpay_payment_id column
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = 'EverythingB2C' AND TABLE_NAME = 'orders' AND COLUMN_NAME = 'razorpay_payment_id') = 0,
    'ALTER TABLE orders ADD COLUMN razorpay_payment_id VARCHAR(100)',
    'SELECT "razorpay_payment_id column already exists" as message'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Check and add gst_number column
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = 'EverythingB2C' AND TABLE_NAME = 'orders' AND COLUMN_NAME = 'gst_number') = 0,
    'ALTER TABLE orders ADD COLUMN gst_number VARCHAR(20)',
    'SELECT "gst_number column already exists" as message'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Check and add company_name column
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = 'EverythingB2C' AND TABLE_NAME = 'orders' AND COLUMN_NAME = 'company_name') = 0,
    'ALTER TABLE orders ADD COLUMN company_name VARCHAR(100)',
    'SELECT "company_name column already exists" as message'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Check and add is_business_purchase column
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = 'EverythingB2C' AND TABLE_NAME = 'orders' AND COLUMN_NAME = 'is_business_purchase') = 0,
    'ALTER TABLE orders ADD COLUMN is_business_purchase BOOLEAN DEFAULT FALSE',
    'SELECT "is_business_purchase column already exists" as message'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Check and add status_description column
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = 'EverythingB2C' AND TABLE_NAME = 'orders' AND COLUMN_NAME = 'status_description') = 0,
    'ALTER TABLE orders ADD COLUMN status_description TEXT',
    'SELECT "status_description column already exists" as message'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Check and add estimated_delivery_date column
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = 'EverythingB2C' AND TABLE_NAME = 'orders' AND COLUMN_NAME = 'estimated_delivery_date') = 0,
    'ALTER TABLE orders ADD COLUMN estimated_delivery_date DATE',
    'SELECT "estimated_delivery_date column already exists" as message'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Check and add actual_delivery_date column
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = 'EverythingB2C' AND TABLE_NAME = 'orders' AND COLUMN_NAME = 'actual_delivery_date') = 0,
    'ALTER TABLE orders ADD COLUMN actual_delivery_date DATE',
    'SELECT "actual_delivery_date column already exists" as message'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add foreign key constraint (only if it doesn't exist)
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = 'EverythingB2C' AND TABLE_NAME = 'orders' AND CONSTRAINT_NAME = 'fk_orders_status') = 0,
    'ALTER TABLE orders ADD CONSTRAINT fk_orders_status FOREIGN KEY (order_status_id) REFERENCES order_statuses(id) ON DELETE RESTRICT',
    'SELECT "fk_orders_status constraint already exists" as message'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Create order_status_history table
CREATE TABLE IF NOT EXISTS order_status_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    order_status_id INT NOT NULL,
    status_description TEXT,
    updated_by ENUM('admin', 'system', 'user') DEFAULT 'system',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (order_status_id) REFERENCES order_statuses(id) ON DELETE RESTRICT
);

-- Create payment_transactions table
CREATE TABLE IF NOT EXISTS payment_transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    payment_method ENUM('cod', 'razorpay') NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    currency VARCHAR(3) DEFAULT 'INR',
    status ENUM('pending', 'success', 'failed', 'refunded') DEFAULT 'pending',
    razorpay_order_id VARCHAR(100),
    razorpay_payment_id VARCHAR(100),
    razorpay_signature VARCHAR(255),
    transaction_data JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
);

-- Generate tracking IDs for existing orders (only if tracking_id is NULL)
UPDATE orders SET tracking_id = CONCAT('EverythingB2C', LPAD(id, 8, '0')) WHERE tracking_id IS NULL;

-- Create indexes (ignore if they exist)
CREATE INDEX IF NOT EXISTS idx_orders_tracking_id ON orders(tracking_id);
CREATE INDEX IF NOT EXISTS idx_orders_status ON orders(order_status_id);
CREATE INDEX IF NOT EXISTS idx_orders_payment_status ON orders(payment_status);
CREATE INDEX IF NOT EXISTS idx_order_status_history_order ON order_status_history(order_id); 