# RBAC (Role-Based Access Control) Implementation Guide

## Overview
This document explains the Role-Based Access Control (RBAC) system implemented for the EverythingB2C admin dashboard. This system allows administrators to:

- Create custom roles with specific sets of permissions
- Assign roles to admin users
- Control access to admin pages based on user permissions
- Manage admin users and their roles from the admin dashboard

## Database Setup

### 1. Run the RBAC Migration
Before using the RBAC system, you need to run the database migration SQL file:

```sql
-- File: database/rbac_schema.sql
-- Execute this SQL file in your database to create the necessary tables
```

**Steps:**
1. Open your database management tool (phpMyAdmin, MySQL Workbench, etc.)
2. Navigate to your EverythingB2C database: `u141519101_everythingb2c1`
3. Open and run the SQL file: [database/rbac_schema.sql](database/rbac_schema.sql)

This will create:
- `roles` table - Stores all available roles
- `permissions` table - Stores all available permissions
- `role_permissions` table - Maps permissions to roles
- System roles (Super Admin, Admin, Manager, Editor)
- 30+ permissions across different categories
- Default permission assignments for each role

## System Roles

The system comes with 4 pre-configured roles:

### 1. **Super Admin**
- Full access to all features
- Can manage roles, permissions, and admin users
- Cannot be modified or deleted

### 2. **Admin**
- Full access to most features
- Cannot manage roles, permissions, or admin users
- Can manage products, categories, orders, users, sellers, shipping, etc.

### 3. **Manager**
- Limited access for managing products and orders
- Permissions:
  - View Dashboard
  - View/Edit Products
  - View Orders & Edit Orders
  - View Users
  - View Sellers & Manage Seller Products

### 4. **Editor**
- Limited to content editing
- Permissions:
  - View Dashboard
  - View/Add/Edit Products
  - View/Add/Edit Categories

## Available Permissions

Permissions are organized by category:

### Dashboard
- `view_dashboard` - View admin dashboard

### Products
- `view_products` - View products list
- `add_product` - Add new products
- `edit_product` - Edit existing products
- `delete_product` - Delete products
- `manage_product_approval` - Approve/reject seller products

### Categories
- `view_categories` - View categories
- `add_category` - Add new categories
- `edit_category` - Edit categories
- `delete_category` - Delete categories

### Orders
- `view_orders` - View orders
- `edit_order` - Edit orders
- `delete_order` - Delete orders

### Users
- `view_users` - View users list
- `add_user` - Add new users
- `edit_user` - Edit user details
- `delete_user` - Delete users

### Sellers
- `view_sellers` - View sellers
- `add_seller` - Add new sellers
- `edit_seller` - Edit seller details
- `delete_seller` - Delete sellers
- `manage_seller_products` - Manage seller products

### Shipping
- `view_shipping` - View shipping settings
- `edit_shipping` - Edit shipping settings
- `manage_pincodes` - Manage pincodes

### Reports
- `view_reports` - View reports
- `export_reports` - Export reports

### Admin Management
- `manage_admins` - Add/edit/delete admin users
- `manage_roles` - Create and manage roles
- `manage_permissions` - Manage permissions

### Settings
- `view_settings` - View settings
- `edit_settings` - Edit settings

## Usage

### 1. Manage Roles

**URL:** `/admin/manage_roles.php`

**Features:**
- View all existing roles
- Add new custom roles
- Edit role details (name and description)
- Assign/revoke permissions for each role
- Delete custom roles (only if no admins are assigned)

**Required Permission:** `manage_roles`

### 2. Manage Admin Users

**URL:** `/admin/manage_admins.php`

**Features:**
- View all admin users
- Add new admin users with assigned roles
- Edit admin user details and roles
- Change admin passwords
- Activate/deactivate admin accounts
- View admin's last login time

**Required Permission:** `manage_admins`

### 3. Protecting Admin Pages

To protect an admin page with permission checks, add this line at the top of your PHP file:

```php
<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';
require_once 'includes/auth-check.php';

// Check for a specific permission
checkAdminPermission('view_products');

// Or check for multiple permissions (requires ANY one)
checkAdminPermission(['view_products', 'edit_product']);

// Or check for multiple permissions (requires ALL)
checkAdminPermission(['view_products', 'edit_product'], true);
```

### 4. Checking Permissions in Code

#### Method 1: Check Permission and Redirect
```php
require_once 'includes/auth-check.php';

// Redirects to permission-denied page if user doesn't have permission
checkAdminPermission('edit_product');

// Or use a more specific function
requirePermission('edit_product');

// Or require multiple permissions
requireAllPermissions(['edit_product', 'delete_product']);
```

#### Method 2: Check Permission Without Redirect
```php
require_once '../includes/functions.php';

if (hasPermission('edit_product')) {
    // User has permission
} else {
    // User does not have permission
}

// Check if user has ANY of the given permissions
if (hasAnyPermission(['edit_product', 'delete_product'])) {
    // User has at least one permission
}

// Check if user has ALL of the given permissions
if (hasAllPermissions(['edit_product', 'delete_product'])) {
    // User has all permissions
}
```

#### Method 3: Conditional Display in Templates
```php
<?php if (canAccess('edit_product')): ?>
    <button>Edit Product</button>
<?php endif; ?>
```

## Adding Permission Checks to Existing Pages

### Example: Protecting the Products Page

**File:** `admin/products.php`

```php
<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';
require_once 'includes/auth-check.php';

// Check if user has permission to view products
checkAdminPermission('view_products');

// Rest of the page code...
?>
```

### Example: Conditional Actions Based on Permissions

**File:** `admin/products.php`

```php
<?php
// ... at the top of file after includes ...
checkAdminPermission('view_products');

// Later in the code:
?>

<!-- Show Edit button only if user has edit_product permission -->
<?php if (canAccess('edit_product')): ?>
    <a href="edit_product.php?id=<?php echo $product['id']; ?>" class="btn btn-primary">
        <i class="fas fa-edit"></i> Edit
    </a>
<?php endif; ?>

<!-- Show Delete button only if user has delete_product permission -->
<?php if (canAccess('delete_product')): ?>
    <form method="POST" style="display: inline;" onsubmit="return confirm('Delete?');">
        <input type="hidden" name="action" value="delete">
        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
        <button type="submit" class="btn btn-danger">
            <i class="fas fa-trash"></i> Delete
        </button>
    </form>
<?php endif; ?>
```

## Recommended Security Setup

### 1. Create a Content Editor Role
```
Name: Content Editor
Description: Can manage products and categories
Permissions:
- view_dashboard
- view_products
- add_product
- edit_product
- view_categories
- add_category
- edit_category
```

### 2. Add a Content Editor Admin User
- Name: John Doe
- Email: john@example.com
- Password: secure123
- Role: Content Editor

### 3. Protect Content Pages
Add to the top of:
- `admin/products.php`: `checkAdminPermission('view_products');`
- `admin/add_product.php`: `checkAdminPermission('add_product');`
- `admin/edit_product.php`: `checkAdminPermission('edit_product');`
- `admin/categories.php`: `checkAdminPermission('view_categories');`

## API Reference

### Session Variables (Set on Login)
```php
$_SESSION['admin_id']          // Admin user ID
$_SESSION['admin_name']        // Admin name
$_SESSION['admin_email']       // Admin email
$_SESSION['admin_role_id']     // Role ID
$_SESSION['admin_role_name']   // Role name
$_SESSION['admin_permissions'] // Array of permission codes
```

### Available Functions

#### Permission Checking
- `hasPermission($code, $adminId)` - Check if admin has permission
- `hasAnyPermission($codes, $adminId)` - Check if admin has any of the permissions
- `hasAllPermissions($codes, $adminId)` - Check if admin has all permissions
- `canAccess($code)` - Check current admin's permission (for templates)
- `checkAdminPermission($codes, $requireAll)` - Check permission or redirect
- `requirePermission($code)` - Require single permission
- `requireAllPermissions($codes)` - Require all permissions

#### Role Management
- `getAllRoles()` - Get all active roles
- `addRole($name, $description)` - Create new role
- `updateRole($roleId, $name, $description)` - Update role
- `deleteRole($roleId)` - Delete role (only if no admins)
- `getRolePermissions($roleId)` - Get all permissions for a role

#### Permission Management
- `getAllPermissionsGrouped()` - Get permissions grouped by category
- `addPermissionToRole($roleId, $permissionId)` - Assign permission to role
- `removePermissionFromRole($roleId, $permissionId)` - Remove permission from role

#### Admin Management
- `getAllAdmins()` - Get all admin users
- `getAdminById($adminId)` - Get specific admin
- `getAdminRole($adminId)` - Get admin's role
- `addAdmin($name, $email, $password, $roleId)` - Create admin user
- `updateAdmin($adminId, $name, $email, $roleId, $password)` - Update admin
- `activateAdmin($adminId)` - Activate admin
- `deactivateAdmin($adminId)` - Deactivate admin
- `getCurrentAdmin()` - Get current logged-in admin

## Troubleshooting

### Admin Can't Access Page
1. Check if the admin has the required permission
2. Go to Manage Admin Users and verify the role assigned
3. Go to Manage Roles and verify the permission is assigned to the role

### Can't Create Custom Role
- Super Admin and system roles cannot be modified
- Only admins with `manage_roles` permission can create roles

### Can't Delete Role
- Database constraint: Role has admin users assigned
- Database constraint: Role is a system role
- Solution: First reassign all admins to a different role before deleting

### Permission Denied Page
- User doesn't have permission for that page
- This is working as intended for security
- Contact Super Admin to assign required permissions

## Security Notes

1. **Session-based:** Permissions are stored in user session for performance
2. **Database verification:** Permission checks query database each time a page loads
3. **Protected admin pages:** All admin management pages require `manage_admins` or `manage_roles` permissions
4. **Password hashing:** Admin passwords are hashed using bcrypt (PASSWORD_BCRYPT)
5. **Active status:** Only active admins can log in
6. **System roles:** Cannot be modified or deleted to ensure system integrity

## Migration from Old System

If you had admin users before the RBAC system:

1. Run the `rbac_schema.sql` migration
2. All existing admins will be assigned to "Super Admin" role automatically
3. Create new roles and permissions as needed
4. Reassign admins to appropriate roles
5. Update admin pages with permission checks gradually

## Quick Start Checklist

- [ ] Run database migration (rbac_schema.sql)
- [ ] Log in and verify you can access Manage Roles page
- [ ] Create custom roles as needed
- [ ] Create admin users with assigned roles
- [ ] Add permission checks to existing admin pages
- [ ] Test with different user roles to ensure proper access control
- [ ] Train team on permission system

## Support

For issues or questions:
1. Check the Troubleshooting section above
2. Verify database migration was completed
3. Check browser console for JavaScript errors
4. Review server logs for database errors
5. Ensure all files are properly uploaded

---

**Created:** 2026-03-09
**Version:** 1.0
**Status:** Production Ready
