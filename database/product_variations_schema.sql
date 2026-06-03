-- Product variation data for attribute-based price, stock, and image overrides.

ALTER TABLE products ADD COLUMN has_variations TINYINT(1) DEFAULT 0;

CREATE TABLE IF NOT EXISTS product_variations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    variation_label VARCHAR(255) NOT NULL,
    attributes_json TEXT,
    mrp DECIMAL(10,2) NOT NULL DEFAULT 0,
    selling_price DECIMAL(10,2) NOT NULL DEFAULT 0,
    stock_quantity INT NOT NULL DEFAULT 0,
    image_path VARCHAR(255) DEFAULT NULL,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_product_variations_product (product_id),
    CONSTRAINT fk_product_variations_product
        FOREIGN KEY (product_id) REFERENCES products(id)
        ON DELETE CASCADE
);
