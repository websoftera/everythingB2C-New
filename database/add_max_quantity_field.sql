-- Add max_quantity_per_order field to products table
-- This field will restrict the maximum number of items that can be purchased in one order

ALTER TABLE products ADD COLUMN max_quantity_per_order INT DEFAULT NULL;

-- Add comment to explain the field
ALTER TABLE products MODIFY COLUMN max_quantity_per_order INT DEFAULT NULL COMMENT 'Maximum quantity allowed per order. NULL means no limit.';

-- Add index for better performance
CREATE INDEX idx_products_max_quantity ON products(max_quantity_per_order); 