-- Delivery Popup and Serviceable Pincodes Schema

-- Table to store serviceable pincodes
CREATE TABLE serviceable_pincodes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    pincode VARCHAR(10) NOT NULL UNIQUE,
    city VARCHAR(100),
    state VARCHAR(100),
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Table to store popup settings
CREATE TABLE popup_settings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    setting_key VARCHAR(50) NOT NULL UNIQUE,
    setting_value TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert default popup settings
INSERT INTO popup_settings (setting_key, setting_value) VALUES
('popup_enabled', '1'),
('popup_title', 'Check Delivery Availability'),
('popup_message', 'We Deliver Orders In Maharashtra, Gujarat, Bangalore, And Hyderabad Only.'),
('popup_instruction', 'Please Enter Your Pincode To Check Delivery Availability.'),
('service_available_message', 'Great! We deliver to your area.'),
('service_unavailable_message', 'We are not providing service to this area.');

-- Add indexes for better performance
CREATE INDEX idx_serviceable_pincodes_pincode ON serviceable_pincodes(pincode);
CREATE INDEX idx_serviceable_pincodes_active ON serviceable_pincodes(is_active);
