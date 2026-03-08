-- RBAC (Role-Based Access Control) Schema

-- Create roles table
CREATE TABLE IF NOT EXISTS roles (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) UNIQUE NOT NULL,
    description TEXT,
    is_system_role BOOLEAN DEFAULT FALSE,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create permissions table
CREATE TABLE IF NOT EXISTS permissions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    code VARCHAR(100) UNIQUE NOT NULL,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    category VARCHAR(50),
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create role_permissions junction table
CREATE TABLE IF NOT EXISTS role_permissions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    role_id INT NOT NULL,
    permission_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_role_permission (role_id, permission_id),
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE,
    FOREIGN KEY (permission_id) REFERENCES permissions(id) ON DELETE CASCADE
);

-- Modify admins table to use role_id instead of role enum
ALTER TABLE admins ADD COLUMN role_id INT AFTER password;
ALTER TABLE admins ADD CONSTRAINT fk_admins_role FOREIGN KEY (role_id) REFERENCES roles(id);

-- Insert default system roles
INSERT INTO roles (name, description, is_system_role) VALUES 
('Super Admin', 'Full access to all features', TRUE),
('Admin', 'Full access to most features', TRUE),
('Manager', 'Limited access to manage products and orders', TRUE),
('Editor', 'Can edit products and categories', TRUE);

-- Insert permissions
INSERT INTO permissions (code, name, category, description) VALUES
-- Dashboard
('view_dashboard', 'View Dashboard', 'Dashboard', 'Can view admin dashboard'),

-- Products
('view_products', 'View Products', 'Products', 'Can view products list'),
('add_product', 'Add Product', 'Products', 'Can add new products'),
('edit_product', 'Edit Product', 'Products', 'Can edit existing products'),
('delete_product', 'Delete Product', 'Products', 'Can delete products'),
('manage_product_approval', 'Manage Product Approval', 'Products', 'Can approve/reject seller products'),

-- Categories
('view_categories', 'View Categories', 'Categories', 'Can view categories'),
('add_category', 'Add Category', 'Categories', 'Can add new categories'),
('edit_category', 'Edit Category', 'Categories', 'Can edit categories'),
('delete_category', 'Delete Category', 'Categories', 'Can delete categories'),

-- Orders
('view_orders', 'View Orders', 'Orders', 'Can view orders'),
('edit_order', 'Edit Order', 'Orders', 'Can edit orders'),
('delete_order', 'Delete Order', 'Orders', 'Can delete orders'),

-- Users
('view_users', 'View Users', 'Users', 'Can view users list'),
('add_user', 'Add User', 'Users', 'Can add new users'),
('edit_user', 'Edit User', 'Users', 'Can edit user details'),
('delete_user', 'Delete User', 'Users', 'Can delete users'),

-- Sellers
('view_sellers', 'View Sellers', 'Sellers', 'Can view sellers'),
('add_seller', 'Add Seller', 'Sellers', 'Can add new sellers'),
('edit_seller', 'Edit Seller', 'Sellers', 'Can edit seller details'),
('delete_seller', 'Delete Seller', 'Sellers', 'Can delete sellers'),
('manage_seller_products', 'Manage Seller Products', 'Sellers', 'Can manage seller products'),

-- Shipping
('view_shipping', 'View Shipping', 'Shipping', 'Can view shipping settings'),
('edit_shipping', 'Edit Shipping', 'Shipping', 'Can edit shipping settings'),
('manage_pincodes', 'Manage Pincodes', 'Shipping', 'Can manage pincodes'),

-- Reports
('view_reports', 'View Reports', 'Reports', 'Can view reports'),
('export_reports', 'Export Reports', 'Reports', 'Can export reports'),

-- Admin Management
('manage_admins', 'Manage Admin Users', 'Admin', 'Can add/edit/delete admin users'),
('manage_roles', 'Manage Roles', 'Admin', 'Can create and manage roles'),
('manage_permissions', 'Manage Permissions', 'Admin', 'Can manage permissions'),

-- Settings
('view_settings', 'View Settings', 'Settings', 'Can view settings'),
('edit_settings', 'Edit Settings', 'Settings', 'Can edit settings');

-- Assign permissions to Super Admin role (all permissions)
INSERT INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id FROM roles r, permissions p
WHERE r.name = 'Super Admin';

-- Assign permissions to Admin role (all except admin management)
INSERT INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id FROM roles r, permissions p
WHERE r.name = 'Admin' AND p.code NOT IN ('manage_admins', 'manage_roles', 'manage_permissions');

-- Assign permissions to Manager role (limited)
INSERT INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id FROM roles r, permissions p
WHERE r.name = 'Manager' AND p.code IN (
    'view_dashboard', 'view_products', 'edit_product', 'view_orders', 
    'edit_order', 'view_users', 'view_sellers', 'manage_seller_products'
);

-- Assign permissions to Editor role
INSERT INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id FROM roles r, permissions p
WHERE r.name = 'Editor' AND p.code IN (
    'view_dashboard', 'view_products', 'add_product', 'edit_product', 
    'view_categories', 'add_category', 'edit_category'
);

-- Update existing admin to Super Admin role
UPDATE admins SET role_id = (SELECT id FROM roles WHERE name = 'Super Admin') WHERE name = 'Super Admin' LIMIT 1;
