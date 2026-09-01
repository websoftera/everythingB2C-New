-- Control which parent-menu path displays a product from a shared subcategory.
-- Safe to run repeatedly during deployment.

CREATE TABLE IF NOT EXISTS product_category_parent_visibility (
    product_id INT NOT NULL,
    category_id INT NOT NULL,
    parent_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (product_id, category_id, parent_id),
    INDEX idx_product_parent_visibility_category (category_id, parent_id),
    INDEX idx_product_parent_visibility_product (product_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
