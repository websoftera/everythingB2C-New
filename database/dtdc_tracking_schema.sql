-- DTDC Tracking Integration Schema
-- This file contains the necessary tables for DTDC API integration

-- DTDC Orders Table - Stores DTDC order information
CREATE TABLE IF NOT EXISTS dtdc_orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    dtdc_order_id VARCHAR(100) UNIQUE,
    dtdc_tracking_id VARCHAR(100) UNIQUE,
    dtdc_reference_number VARCHAR(100),
    status VARCHAR(50) DEFAULT 'PENDING',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    INDEX idx_order_id (order_id),
    INDEX idx_dtdc_tracking_id (dtdc_tracking_id)
);

-- DTDC Tracking Events Table - Stores detailed tracking history
CREATE TABLE IF NOT EXISTS dtdc_tracking_events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    dtdc_order_id INT NOT NULL,
    event_date DATETIME NOT NULL,
    event_location VARCHAR(255),
    event_status VARCHAR(100),
    event_description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (dtdc_order_id) REFERENCES dtdc_orders(id) ON DELETE CASCADE,
    INDEX idx_dtdc_order_id (dtdc_order_id),
    INDEX idx_event_date (event_date)
);

-- DTDC API Logs Table - For debugging and monitoring
CREATE TABLE IF NOT EXISTS dtdc_api_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT,
    action VARCHAR(50) NOT NULL,
    request_data JSON,
    response_data JSON,
    status VARCHAR(20) DEFAULT 'PENDING',
    error_message TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_order_id (order_id),
    INDEX idx_action (action),
    INDEX idx_created_at (created_at)
);

-- Update orders table to include DTDC tracking information
-- Add DTDC tracking ID to orders table
ALTER TABLE orders ADD COLUMN IF NOT EXISTS dtdc_tracking_id VARCHAR(100);
ALTER TABLE orders ADD COLUMN IF NOT EXISTS dtdc_order_id VARCHAR(100);
ALTER TABLE orders ADD COLUMN IF NOT EXISTS dtdc_enabled BOOLEAN DEFAULT FALSE;

-- Add indexes for better performance
ALTER TABLE orders ADD INDEX IF NOT EXISTS idx_dtdc_tracking_id (dtdc_tracking_id);
ALTER TABLE orders ADD INDEX IF NOT EXISTS idx_dtdc_order_id (dtdc_order_id);
