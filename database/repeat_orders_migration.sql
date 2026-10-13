-- Run this once as part of the production deployment.
ALTER TABLE orders ADD COLUMN reordered_from_order_id INT NULL;
ALTER TABLE order_items ADD COLUMN variation_id INT NULL;
ALTER TABLE order_items ADD COLUMN selected_attributes TEXT NULL;
CREATE INDEX idx_orders_reordered_from ON orders (reordered_from_order_id);
