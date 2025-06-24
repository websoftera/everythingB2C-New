-- Create database
CREATE DATABASE IF NOT EXISTS everythingb2c;
USE everythingb2c;

-- Categories table
CREATE TABLE categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) UNIQUE NOT NULL,
    image VARCHAR(255),
    description TEXT,
    product_count INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Products table
CREATE TABLE products (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    description TEXT,
    mrp DECIMAL(10,2) NOT NULL,
    selling_price DECIMAL(10,2) NOT NULL,
    discount_percentage INT DEFAULT 0,
    category_id INT,
    main_image VARCHAR(255),
    stock_quantity INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    is_featured BOOLEAN DEFAULT FALSE,
    is_discounted BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
);

-- Product images table (for multiple images per product)
CREATE TABLE product_images (
    id INT PRIMARY KEY AUTO_INCREMENT,
    product_id INT,
    image_path VARCHAR(255) NOT NULL,
    is_main BOOLEAN DEFAULT FALSE,
    sort_order INT DEFAULT 0,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- Users table
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    address TEXT,
    city VARCHAR(50),
    state VARCHAR(50),
    pincode VARCHAR(10),
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Cart table
CREATE TABLE cart (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    product_id INT,
    quantity INT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- Wishlist table
CREATE TABLE wishlist (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    product_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- Orders table
CREATE TABLE orders (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    order_number VARCHAR(50) UNIQUE NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    status ENUM('pending', 'confirmed', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
    shipping_address TEXT,
    payment_method VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Order items table
CREATE TABLE order_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_id INT,
    product_id INT,
    quantity INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- Addresses table for multiple user addresses
CREATE TABLE IF NOT EXISTS addresses (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    pincode VARCHAR(10) NOT NULL,
    address_line1 VARCHAR(255) NOT NULL,
    address_line2 VARCHAR(255),
    city VARCHAR(50) NOT NULL,
    state VARCHAR(50) NOT NULL,
    is_default BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Insert sample categories
INSERT INTO categories (name, slug, image, description) VALUES
('Kitchen', 'kitchen', 'Home page/Product Categories images/KITCHEN.webp', 'Kitchen essentials and supplies'),
('Office Stationery', 'office-stationery', 'img/image_2.webp', 'Office and school stationery items'),
('Cleaning & Household', 'cleaning-household', 'img/image_4.webp', 'Cleaning and household products'),
('Personal Care', 'personal-care', 'img/image_1.webp', 'Personal care and hygiene products'),
('Diapers & Wipes', 'diapers-wipes', 'img/image_2.webp', 'Baby care products'),
('Home & Garden', 'home-garden', 'img/image_4.webp', 'Home and garden supplies'),
('Other', 'other', 'img/image_1.webp', 'Other miscellaneous products');

-- Insert sample products
INSERT INTO products (name, slug, description, mrp, selling_price, discount_percentage, category_id, main_image, stock_quantity, is_featured, is_discounted) VALUES
('JK Sparkle', 'jk-sparkle', 'JK Sparkle Lorem Ipsum is simply dummy text of the printing and typesetting industry.', 400.00, 200.00, 50, 7, 'asset/Products Offering Discount/JK Sparkle.webp', 100, TRUE, TRUE),
('JK LEDGER Paper 80 GSM | 500 Sheets', 'jk-ledger-paper-80-gsm', 'High quality ledger paper for office use', 450.00, 410.00, 9, 2, 'Home page/Products Offering Discount Images/P1.webp', 50, TRUE, TRUE),
('JK COPIER A4 Size Paper', 'jk-copier-a4-size', 'Premium A4 size copier paper', 310.00, 290.00, 6, 2, 'Home page/Products Offering Discount Images/P3.webp', 75, FALSE, TRUE),
('JK Cedar A4 Size Paper', 'jk-cedar-a4-size', 'Cedar brand A4 size paper', 450.00, 318.00, 29, 2, 'Home page/Products Offering Discount Images/P4.webp', 30, FALSE, TRUE),
('JK Easy A4 Size Paper', 'jk-easy-a4-size', 'Easy brand A4 size paper', 290.00, 280.00, 3, 2, 'Home page/Products Offering Discount Images/P5.webp', 60, FALSE, TRUE),
('Drain It Drain Cleaner Powder', 'drain-it-drain-cleaner', 'Effective drain cleaning powder', 150.00, 120.00, 20, 3, 'asset/household and cleaning/page1/Drain It Drain Cleaner Powder.webp', 40, FALSE, TRUE),
('Duster Big Heavy', 'duster-big-heavy', 'Heavy duty duster for cleaning', 80.00, 65.00, 19, 3, 'asset/household and cleaning/page1/Duster Big Heavy.webp', 25, FALSE, TRUE),
('Floor Duster', 'floor-duster', 'Professional floor duster', 120.00, 95.00, 21, 3, 'asset/household and cleaning/page1/Floor Duster.webp', 35, FALSE, TRUE),
('Disposable Gloves Plastic', 'disposable-gloves-plastic', 'Plastic disposable gloves', 200.00, 160.00, 20, 1, 'asset/kitchen/Disposable Gloves Plastic 80 Pcs.-2nd.webp', 80, FALSE, TRUE),
('Exo Anti-Bacterial Dishwash Bar', 'exo-anti-bacterial-dishwash', 'Anti-bacterial dishwashing bar', 45.00, 35.00, 22, 1, 'asset/kitchen/Exo Anti-Bacterial Dishwash Bar.webp', 100, FALSE, TRUE);

-- Insert product images
INSERT INTO product_images (product_id, image_path, is_main, sort_order) VALUES
(1, 'asset/Products Offering Discount/JK Sparkle.webp', TRUE, 1),
(1, 'asset/Products Offering Discount/JK Sparkle-2nd.webp', FALSE, 2),
(1, 'asset/Products Offering Discount/JK Sparkle-3rd.webp', FALSE, 3),
(1, 'asset/Products Offering Discount/JK Sparkle-4th.webp', FALSE, 4),
(2, 'Home page/Products Offering Discount Images/P1.webp', TRUE, 1),
(3, 'Home page/Products Offering Discount Images/P3.webp', TRUE, 1),
(4, 'Home page/Products Offering Discount Images/P4.webp', TRUE, 1),
(5, 'Home page/Products Offering Discount Images/P5.webp', TRUE, 1);

-- Update category product counts
UPDATE categories c SET product_count = (
    SELECT COUNT(*) FROM products p WHERE p.category_id = c.id
); 