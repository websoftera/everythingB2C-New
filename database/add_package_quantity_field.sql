-- Add package_quantity field to products table.
-- Customers can only buy product quantities in multiples of this value.

ALTER TABLE products ADD COLUMN package_quantity INT DEFAULT 1 AFTER stock_quantity;
