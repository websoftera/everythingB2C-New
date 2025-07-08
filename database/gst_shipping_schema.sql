-- GST and Shipping Charges Database Schema Updates
-- Run this after the main schema.sql

-- Add GST columns to products table
ALTER TABLE products 
ADD COLUMN gst_type ENUM('sgst_cgst', 'igst') NOT NULL DEFAULT 'sgst_cgst' AFTER discount_percentage,
ADD COLUMN gst_rate DECIMAL(5,2) NOT NULL DEFAULT 18.00 AFTER gst_type;

-- Add per-product shipping charge (in rupees, optional)
ALTER TABLE products ADD COLUMN shipping_charge DECIMAL(10,2) DEFAULT NULL AFTER gst_rate;

-- Create shipping zones table
CREATE TABLE shipping_zones (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create shipping zone locations table (for cities, states, countries)
CREATE TABLE shipping_zone_locations (
    id INT PRIMARY KEY AUTO_INCREMENT,
    zone_id INT NOT NULL,
    location_type ENUM('country', 'state', 'city', 'pincode') NOT NULL,
    location_value VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (zone_id) REFERENCES shipping_zones(id) ON DELETE CASCADE,
    UNIQUE KEY unique_location (zone_id, location_type, location_value)
);

-- Create shipping charges table
CREATE TABLE shipping_charges (
    id INT PRIMARY KEY AUTO_INCREMENT,
    zone_id INT NOT NULL,
    charge_type ENUM('fixed', 'percentage') NOT NULL DEFAULT 'fixed',
    charge_value DECIMAL(10,2) NOT NULL,
    min_order_amount DECIMAL(10,2) DEFAULT 0.00,
    max_order_amount DECIMAL(10,2) DEFAULT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (zone_id) REFERENCES shipping_zones(id) ON DELETE CASCADE
);

-- Update orders table to include GST and shipping details
ALTER TABLE orders 
ADD COLUMN subtotal DECIMAL(10,2) NOT NULL DEFAULT 0.00 AFTER total_amount,
ADD COLUMN gst_amount DECIMAL(10,2) NOT NULL DEFAULT 0.00 AFTER subtotal,
ADD COLUMN shipping_charge DECIMAL(10,2) NOT NULL DEFAULT 0.00 AFTER gst_amount,
ADD COLUMN shipping_zone_id INT AFTER shipping_charge,
ADD COLUMN billing_state VARCHAR(50) AFTER shipping_zone_id,
ADD COLUMN billing_city VARCHAR(50) AFTER billing_state,
ADD COLUMN billing_pincode VARCHAR(10) AFTER billing_city,
ADD FOREIGN KEY (shipping_zone_id) REFERENCES shipping_zones(id) ON DELETE SET NULL;

-- Update order_items table to include GST details
ALTER TABLE order_items 
ADD COLUMN unit_price DECIMAL(10,2) NOT NULL DEFAULT 0.00 AFTER price,
ADD COLUMN gst_rate DECIMAL(5,2) NOT NULL DEFAULT 0.00 AFTER unit_price,
ADD COLUMN gst_amount DECIMAL(10,2) NOT NULL DEFAULT 0.00 AFTER gst_rate;

-- Insert default shipping zones
INSERT INTO shipping_zones (name, description) VALUES
('Same State', 'Deliveries within the same state (SGST + CGST)'),
('Different State', 'Deliveries to different states (IGST)'),
('Free Shipping Zone', 'Areas with free shipping'),
('Premium Zone', 'Areas with premium shipping charges');

-- Insert sample shipping zone locations
INSERT INTO shipping_zone_locations (zone_id, location_type, location_value) VALUES
-- Same State Zone (assuming Maharashtra as base state)
(1, 'state', 'Maharashtra'),

-- Different State Zone (other states)
(2, 'state', 'Delhi'),
(2, 'state', 'Karnataka'),
(2, 'state', 'Tamil Nadu'),
(2, 'state', 'Gujarat'),
(2, 'state', 'Rajasthan'),
(2, 'state', 'Uttar Pradesh'),
(2, 'state', 'West Bengal'),
(2, 'state', 'Telangana'),
(2, 'state', 'Andhra Pradesh'),
(2, 'state', 'Kerala'),

-- Free Shipping Zone (specific cities)
(3, 'city', 'Mumbai'),
(3, 'city', 'Pune'),
(3, 'city', 'Nagpur'),

-- Premium Zone (remote areas)
(4, 'city', 'Srinagar'),
(4, 'city', 'Leh'),
(4, 'city', 'Port Blair');

-- Insert sample shipping charges
INSERT INTO shipping_charges (zone_id, charge_type, charge_value, min_order_amount) VALUES
(1, 'fixed', 50.00, 0.00),      -- Same state: ₹50 fixed
(2, 'fixed', 100.00, 0.00),     -- Different state: ₹100 fixed
(3, 'fixed', 0.00, 500.00),     -- Free shipping: ₹0 for orders above ₹500
(4, 'fixed', 200.00, 0.00);     -- Premium zone: ₹200 fixed

-- Update existing products with default GST rates
UPDATE products SET gst_rate = 18.00 WHERE gst_rate = 0.00;

-- Add indexes for better performance
CREATE INDEX idx_shipping_zone_locations ON shipping_zone_locations(location_type, location_value);
CREATE INDEX idx_shipping_charges_zone ON shipping_charges(zone_id, is_active);
CREATE INDEX idx_orders_shipping ON orders(shipping_zone_id, billing_state, billing_city); 