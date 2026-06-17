CREATE TABLE IF NOT EXISTS quotations (
    id INT PRIMARY KEY AUTO_INCREMENT,
    quotation_no VARCHAR(60) UNIQUE NOT NULL,
    quotation_date DATE NOT NULL,
    customer_name VARCHAR(150) NOT NULL,
    mobile_no VARCHAR(30),
    eway_bill_no VARCHAR(80),
    eway_bill_date DATE NULL,
    buyer_po_no VARCHAR(80),
    buyer_po_date DATE NULL,
    payment_terms VARCHAR(100) DEFAULT 'Manual',
    payment_date DATE NULL,
    transporter VARCHAR(150),
    lr_no VARCHAR(80),
    bill_to_name VARCHAR(150),
    bill_to_gstin VARCHAR(30),
    bill_to_mobile VARCHAR(30),
    bill_to_address TEXT,
    ship_to_name VARCHAR(150),
    ship_to_gstin VARCHAR(30),
    ship_to_mobile VARCHAR(30),
    ship_to_address TEXT,
    taxable_total DECIMAL(12,2) NOT NULL DEFAULT 0,
    cgst_total DECIMAL(12,2) NOT NULL DEFAULT 0,
    sgst_total DECIMAL(12,2) NOT NULL DEFAULT 0,
    grand_total DECIMAL(12,2) NOT NULL DEFAULT 0,
    created_by INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS quotation_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    quotation_id INT NOT NULL,
    product_id INT NULL,
    product_name VARCHAR(255) NOT NULL,
    product_image VARCHAR(255),
    hsn VARCHAR(40),
    unit VARCHAR(30) DEFAULT 'No.',
    gst_rate DECIMAL(5,2) NOT NULL DEFAULT 0,
    item_mrp DECIMAL(12,2) NOT NULL DEFAULT 0,
    item_price DECIMAL(12,2) NOT NULL DEFAULT 0,
    quantity DECIMAL(10,2) NOT NULL DEFAULT 1,
    taxable_value DECIMAL(12,2) NOT NULL DEFAULT 0,
    cgst_amount DECIMAL(12,2) NOT NULL DEFAULT 0,
    sgst_amount DECIMAL(12,2) NOT NULL DEFAULT 0,
    total_price DECIMAL(12,2) NOT NULL DEFAULT 0,
    sort_order INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_quotation_items_quotation_id (quotation_id),
    CONSTRAINT fk_quotation_items_quotation
        FOREIGN KEY (quotation_id) REFERENCES quotations(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT IGNORE INTO permissions (code, name, category, description)
VALUES ('manage_quotations', 'Manage Quotations', 'Orders', 'Can create and download quotations');

INSERT IGNORE INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id
FROM roles r
JOIN permissions p ON p.code = 'manage_quotations'
WHERE r.name IN ('Super Admin', 'Admin');
