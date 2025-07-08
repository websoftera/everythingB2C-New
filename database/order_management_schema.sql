-- Order Management Schema
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

-- Insert default system order statuses
INSERT INTO order_statuses (name, description, color, is_system, sort_order) VALUES
('Pending', 'Order has been placed and is awaiting confirmation', '#ffc107', TRUE, 1),
('Processing', 'Order is being processed and prepared for shipping', '#17a2b8', TRUE, 2),
('Shipped', 'Order has been shipped from our warehouse', '#28a745', TRUE, 3),
('In Transit', 'Order is in transit to delivery location', '#6f42c1', TRUE, 4),
('Out for Delivery', 'Order is out for delivery to your address', '#fd7e14', TRUE, 5),
('Delivered', 'Order has been successfully delivered', '#20c997', TRUE, 6),
('Canceled', 'Order has been canceled', '#dc3545', TRUE, 7);

-- Update orders table to include tracking and payment information
ALTER TABLE orders ADD COLUMN IF NOT EXISTS tracking_id VARCHAR(50) UNIQUE;
ALTER TABLE orders ADD COLUMN IF NOT EXISTS external_tracking_id VARCHAR(100);
ALTER TABLE orders ADD COLUMN IF NOT EXISTS external_tracking_link TEXT;
ALTER TABLE orders ADD COLUMN IF NOT EXISTS order_status_id INT DEFAULT 1;
ALTER TABLE orders ADD COLUMN IF NOT EXISTS payment_method ENUM('cod', 'razorpay') DEFAULT 'cod';
ALTER TABLE orders ADD COLUMN IF NOT EXISTS payment_status ENUM('pending', 'paid', 'failed', 'refunded') DEFAULT 'pending';
ALTER TABLE orders ADD COLUMN IF NOT EXISTS razorpay_order_id VARCHAR(100);
ALTER TABLE orders ADD COLUMN IF NOT EXISTS razorpay_payment_id VARCHAR(100);
ALTER TABLE orders ADD COLUMN IF NOT EXISTS gst_number VARCHAR(20);
ALTER TABLE orders ADD COLUMN IF NOT EXISTS company_name VARCHAR(100);
ALTER TABLE orders ADD COLUMN IF NOT EXISTS is_business_purchase BOOLEAN DEFAULT FALSE;
ALTER TABLE orders ADD COLUMN IF NOT EXISTS status_description TEXT;
ALTER TABLE orders ADD COLUMN IF NOT EXISTS estimated_delivery_date DATE;
ALTER TABLE orders ADD COLUMN IF NOT EXISTS actual_delivery_date DATE;

-- Add foreign key for order status
ALTER TABLE orders ADD CONSTRAINT fk_orders_status 
FOREIGN KEY (order_status_id) REFERENCES order_statuses(id) ON DELETE RESTRICT;

-- Order Status History Table
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

-- Payment Transactions Table
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

-- Generate tracking IDs for existing orders
UPDATE orders SET tracking_id = CONCAT('Everythingb2c', LPAD(id, 8, '0')) WHERE tracking_id IS NULL;

-- Create index for better performance
CREATE INDEX idx_orders_tracking_id ON orders(tracking_id);
CREATE INDEX idx_orders_status ON orders(order_status_id);
CREATE INDEX idx_orders_payment_status ON orders(payment_status);
CREATE INDEX idx_order_status_history_order ON order_status_history(order_id); 