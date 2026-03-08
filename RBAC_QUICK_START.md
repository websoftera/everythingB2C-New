# RBAC Quick Start Guide

## What is RBAC?

**Role-Based Access Control (RBAC)** is a security system that:
- Restricts which admin pages each staff member can access
- Allows Super Admin to create custom roles (like "Editor", "Manager", etc.)
- Assigns specific permissions to each role
- Prevents unauthorized access to sensitive admin pages

## Step 1: Set Up the Database (Required)

1. Open **phpMyAdmin** in your hosting control panel
2. Select your database: `u141519101_everythingb2c1`
3. Click **Import**
4. Select the file: `database/rbac_schema.sql`
5. Click **Go/Import**
6. ✅ You should see a success message

## Step 2: Verify Installation

1. Go to: `admin/index.php`
2. Log in with your Super Admin account
   - Email: `admin@EverythingB2C.com`
   - Password: `admin123`
3. In the left sidebar, you should now see:
   - "Manage Roles" (new!)
   - "Admin Users" (new!)

## Step 3: Create Roles

### Example: Create an "Editor" Role

1. Click **Manage Roles** in the sidebar
2. In the "Add New Role" section:
   - **Role Name:** `Content Editor`
   - **Description:** `Can manage products and categories`
3. Click **Add Role**
4. Find your new role and click **Edit**
5. In the "Manage Permissions" section on the right:
   - Check these permissions:
     - Dashboard → View Dashboard
     - Products → View Products
     - Products → Add Product
     - Products → Edit Product
     - Categories → View Categories
     - Categories → Add Category
     - Categories → Edit Category
6. Click **Save Permissions**

## Step 4: Create Admin Users

1. Click **Admin Users** in the sidebar
2. In the "Add New Admin User" section:
   - **Name:** `John Doe`
   - **Email:** `john@example.com`
   - **Password:** Your secure password
   - **Role:** `Content Editor`
3. Click **Add Admin User**

## Step 5: Secure Existing Pages (Optional)

Add permission checks to existing admin pages:

### Example: Protect products.php

Open file: `admin/products.php`

At the very top (after `<?php`), add these lines:

```php
<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';
require_once 'includes/auth-check.php';

// Add this line to require view_products permission
checkAdminPermission('view_products');

// Rest of the existing code below...
```

### Which Pages to Protect

Map permissions to pages:

| Permission | File |
|-----------|------|
| `view_products` | `admin/products.php` |
| `add_product` | `admin/add_product.php` |
| `edit_product` | `admin/edit_product.php` |
| `view_categories` | `admin/categories.php` |
| `view_orders` | `admin/orders.php` |
| `view_users` | `admin/users.php` |
| `view_sellers` | `admin/manage_sellers.php` |
| `manage_product_approval` | `admin/approve_products.php` |
| `view_shipping` | `admin/shipping.php` |
| `manage_pincodes` | `admin/manage_pincodes.php` |
| `view_reports` | `admin/reports.php` |
| `view_settings` | `admin/settings.php` |

## Default Roles and Permissions

### Super Admin
✅ Full access to everything
✅ Can create roles
✅ Can manage admin users
✅ Cannot be deleted

### Admin
✅ Full access except admin management
✅ Cannot create roles
❌ Cannot manage admin users
✅ Can manage products, orders, etc.

### Manager
⚠️ Limited access:
- View Dashboard
- View/Edit Products
- View Orders
- View Users
- Manage Sellers

### Editor
⚠️ Very limited:
- View Dashboard
- View/Add/Edit Products
- View/Add/Edit Categories

## Testing Your Setup

### Test 1: Log in as Different Roles
1. Create a test admin with "Editor" role
2. Log in as that user
3. Verify they CANNOT see:
   - Orders menu item
   - Users menu item
   - Shipping menu item
4. Verify they CAN see:
   - Products menu item
   - Categories menu item

### Test 2: Test Page Protection
1. While logged in as "Editor", try to manually access:
   `admin/orders.php`
2. You should be redirected to **Permission Denied** page

### Test 3: Test Add/Edit Permissions
1. As "Editor", verify you can only see buttons for actions you have permission for:
   - You see "Edit" button (have `edit_product`)
   - You don't see "Delete" button (don't have `delete_product`)

## Common Issues & Solutions

### Issue: "Admin Users" menu doesn't show up
**Solution:**
- Check if migration ran successfully
- Verify you're logged in as Super Admin
- Try logging out and back in

### Issue: "Permission Denied" message
**Solution:**
- This is WORKING correctly - the user doesn't have permission
- Contact Super Admin to assign the permission
- Go to Manage Roles → Edit the user's role → Add the permission

### Issue: Can't delete a role
**Solution:**
- The role might have admin users assigned to it
- First, reassign all admins to a different role
- Then you can delete the empty role

### Issue: Admin can see all sidebar items despite not having permissions
**Solution:**
- Sidebar shows items based on session permissions
- Log out completely and log back in
- Permissions are cached in the session for performance

## Next Steps

1. ✅ Complete the setup above
2. ✅ Create roles for your team
3. ✅ Create admin users for each team member
4. ✅ Add permission checks to existing pages (optional, for security)
5. ✅ Test with different user accounts
6. ✅ Train your team on the new system

## Advanced: Custom Permissions

Want to add new permissions? 

1. Edit `database/rbac_schema.sql`
2. Add new permission to the `INSERT INTO permissions` section
3. Re-run migration
4. Or manually insert in database:

```sql
INSERT INTO permissions (code, name, category, description)
VALUES ('delete_order', 'Delete Order', 'Orders', 'Can delete orders');
```

Then assign to roles in "Manage Roles" page.

## Summary

| File | Purpose | How to Use |
|------|---------|-----------|
| `admin/manage_roles.php` | Create/edit roles and permissions | Super Admin only |
| `admin/manage_admins.php` | Add/manage admin users | Super Admin only |
| `admin/includes/auth-check.php` | Protection for pages | Add to admin pages |
| `database/rbac_schema.sql` | Database setup | Run once |
| `includes/functions.php` | Permission functions | Auto-included |

---

**Need more details?** See [RBAC_IMPLEMENTATION_GUIDE.md](RBAC_IMPLEMENTATION_GUIDE.md)

**Version:** 1.0 | **Last Updated:** March 2026
