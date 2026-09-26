INSERT INTO popup_settings (setting_key, setting_value) VALUES
('popup_message', 'We Deliver Orders in Vadodara Only.'),
('popup_instruction', 'Please Enter PIN Code to Check Delivery Availability.'),
('service_available_message', 'We Provide Delivery to Your Area'),
('service_unavailable_message', 'We Don’t Provide Delivery to Your Area')
ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value);
