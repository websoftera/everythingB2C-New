# RBAC System Architecture & Flow

## System Overview

```
┌─────────────────────────────────────────────────────────────────┐
│                    EverythingB2C Admin Panel                     │
└─────────────────────────────────────────────────────────────────┘
                              │
                              ▼
                    ┌──────────────────┐
                    │  Admin Login     │
                    │  Page            │
                    └────────┬─────────┘
                             │
                    ┌────────▼──────────┐
                    │ Verify Email &    │
                    │ Password          │
                    └────────┬──────────┘
                             │
                    ┌────────▼──────────────────────────┐
                    │ Load from Database:               │
                    │ - Admin info                      │
                    │ - Role (role_id)                  │
                    │ - Permissions (from role_perm)    │
                    └────────┬──────────────────────────┘
                             │
                    ┌────────▼────────────────────────────┐
                    │ Store in Session:                   │
                    │ - admin_id                          │
                    │ - admin_name                        │
                    │ - admin_role_id                     │
                    │ - admin_permissions (array)         │
                    └────────┬────────────────────────────┘
                             │
                    ┌────────▼──────────────┐
                    │ Redirect to           │
                    │ Dashboard (index.php) │
                    └────────┬──────────────┘
                             │
                    ┌────────▼──────────┐
                    │ Sidebar Renders   │
                    │ with Permission   │
                    │ Checks            │
                    └────────┬──────────┘
                             │
                    ┌────────▼────────────────────────┐
                    │ Only show menu items user       │
                    │ has permission for              │
                    └────────┬────────────────────────┘
                             │
        ┌────────────────────┼────────────────────────┐
        │                    │                        │
        ▼                    ▼                        ▼
   ┌─────────┐         ┌──────────┐        ┌────────────────┐
   │Products │         │ Orders   │        │Manage Roles    │
   │(if perm)│         │(if perm) │        │Admin Users     │
   └────┬────┘         └────┬─────┘        │(if perm)       │
        │                   │               └────────────────┘
        │                   │                      │
        ▼                   ▼                      ▼
    ┌────────────────────────────────────────────────┐
    │ Page loads & checks permission again           │
    │ using checkAdminPermission()                   │
    └────┬──────────────────────────────────────────┘
         │
      ┌──┴──┐
      │     │
   NO │     │ YES
      ▼     ▼
  Redirect  Page
  to     Renders
  403

```

## Database Schema Relationships

```
┌──────────────────┐
│    admins        │
├──────────────────┤
│ id (PK)          │
│ name             │
│ email (UNIQUE)   │
│ password         │
│ role_id (FK)     │ ──┐
│ is_active        │   │
│ created_at       │   │
│ updated_at       │   │
│ last_login       │   │
└──────────────────┘   │
                       │
                       │  1 to Many
                       │
                       ▼
            ┌──────────────────┐
            │      roles       │
            ├──────────────────┤
            │ id (PK)          │
            │ name (UNIQUE)    │
            │ description      │
            │ is_system_role   │
            │ is_active        │
            │ created_at       │
            │ updated_at       │
            └────────┬─────────┘
                     │
                     │  1 to Many
                     │
                     ▼
        ┌────────────────────────────┐
        │   role_permissions         │
        ├────────────────────────────┤
        │ id (PK)                    │
        │ role_id (FK)               │
        │ permission_id (FK)         │
        │ UNIQUE(role_id, perm_id)   │
        │ created_at                 │
        └────────────────┬───────────┘
                         │
                         │  Many to Many
                         │
                         ▼
            ┌────────────────────────┐
            │   permissions          │
            ├────────────────────────┤
            │ id (PK)                │
            │ code (UNIQUE)          │
            │ name                   │
            │ description            │
            │ category               │
            │ is_active              │
            │ created_at             │
            │ updated_at             │
            └────────────────────────┘
```

## Permission Checking Flow

```
User Requests Page
       │
       ▼
Check SessionPermissions
Cached?
       │
    ┌──┴──┐
    │     │
   YES   NO
    │     │
    │     ▼
    │  Query Database:
    │  Fetch Role → Permissions
    │     │
    │     ▼
    │  Store in Session
    │     │
    └──┬──┘
       │
       ▼
Compare Requested Permission
with Session Permissions
       │
    ┌──┴──────┐
    │          │
  MATCH    NO MATCH
    │          │
    ▼          ▼
  ALLOW     DENY
  Page      Redirect
  Render    to 403
```

## Role Permission Mapping Example

```
┌─────────────────────────────────────────────────┐
│           Role: Content Editor                  │
├─────────────────────────────────────────────────┤
│                                                 │
│  ALLOWED Permissions:                          │
│  ✓ view_dashboard                              │
│  ✓ view_products                               │
│  ✓ add_product                                 │
│  ✓ edit_product                                │
│  ✓ view_categories                             │
│  ✓ add_category                                │
│  ✓ edit_category                               │
│                                                 │
│  BLOCKED Permissions:                          │
│  ✗ delete_product                              │
│  ✗ delete_category                             │
│  ✗ view_orders                                 │
│  ✗ manage_admins                               │
│  ✗ manage_roles                                │
│                                                 │
└─────────────────────────────────────────────────┘
```

## Permission Categories Hierarchy

```
PERMISSIONS (30+)
│
├── Dashboard (1)
│   └── view_dashboard
│
├── Products (5)
│   ├── view_products
│   ├── add_product
│   ├── edit_product
│   ├── delete_product
│   └── manage_product_approval
│
├── Categories (4)
│   ├── view_categories
│   ├── add_category
│   ├── edit_category
│   └── delete_category
│
├── Orders (3)
│   ├── view_orders
│   ├── edit_order
│   └── delete_order
│
├── Users (4)
│   ├── view_users
│   ├── add_user
│   ├── edit_user
│   └── delete_user
│
├── Sellers (5)
│   ├── view_sellers
│   ├── add_seller
│   ├── edit_seller
│   ├── delete_seller
│   └── manage_seller_products
│
├── Shipping (3)
│   ├── view_shipping
│   ├── edit_shipping
│   └── manage_pincodes
│
├── Reports (2)
│   ├── view_reports
│   └── export_reports
│
├── Admin (3)
│   ├── manage_admins
│   ├── manage_roles
│   └── manage_permissions
│
└── Settings (2)
    ├── view_settings
    └── edit_settings
```

## System Roles Access Matrix

```
┌──────────────────┬──────────┬──────┬─────────┬────────┐
│ Feature          │ Super    │ Admin│ Manager │ Editor │
│                  │ Admin    │      │         │        │
├──────────────────┼──────────┼──────┼─────────┼────────┤
│ Dashboard        │ ✓✓✓      │ ✓✓✓  │ ✓       │ ✓      │
│ Products (View)  │ ✓✓✓      │ ✓✓✓  │ ✓       │ ✓      │
│ Products (Edit)  │ ✓✓✓      │ ✓✓✓  │         │ ✓      │
│ Products (Add)   │ ✓✓✓      │ ✓✓✓  │         │ ✓      │
│ Products (Del)   │ ✓✓✓      │ ✓✓✓  │         │        │
│ Categories       │ ✓✓✓      │ ✓✓✓  │         │ ✓      │
│ Orders           │ ✓✓✓      │ ✓✓✓  │ ✓       │        │
│ Users            │ ✓✓✓      │ ✓✓✓  │ ✓       │        │
│ Shipping         │ ✓✓✓      │ ✓✓✓  │         │        │
│ Sellers          │ ✓✓✓      │ ✓✓✓  │ ✓       │        │
│ Reports          │ ✓✓✓      │ ✓✓✓  │         │        │
│ Manage Roles     │ ✓✓✓      │      │         │        │
│ Manage Admins    │ ✓✓✓      │      │         │        │
│ Settings         │ ✓✓✓      │ ✓✓✓  │         │        │
└──────────────────┴──────────┴──────┴─────────┴────────┘

Legend: ✓ = Has permission
```

## Code Flow: Protecting a Page

```
user_request.php
    │
    ▼
<?php
session_start();
require '../config/database.php';
require '../includes/functions.php';
require 'includes/auth-check.php';
    │
    ▼
checkAdminPermission('view_products');
    │
    ▼
    ┌─────────────────────────────────┐
    │ Is admin logged in?             │
    │ $_SESSION['admin_id'] exists?   │
    └──────────┬──────────────────────┘
               │
            NO │ YES
               │  │
          REDIRECT
          to login  │
                    ▼
            ┌────────────────────────────┐
            │ Get permissions:           │
            │ From $_SESSION or Database │
            └──────────┬─────────────────┘
                       │
                       ▼
            ┌────────────────────────────────────┐
            │ Check: Is 'view_products'          │
            │ in permissions array?              │
            └──────────┬───────────────────────┘
                       │
                ┌──────┴──────┐
                │             │
              NO              YES
                │             │
        ┌───────▼────────┐    │
        │ Header redirect│    │
        │ to permission- │    │
        │ denied.php     │    │
        │ Exit           │    │
        └────────────────┘    │
                              ▼
                      ┌──────────────────────┐
                      │ Continue Rendering   │
                      │ Page Normally        │
                      └──────────────────────┘
```

## Sidebar Rendering Logic

```
Sidebar.php Loads
    │
    ▼
┌─────────────────────────┐
│ Check Dashboard Perm    │
│ Always Shown            │
└──────────┬──────────────┘
           │
           ▼
┌─────────────────────────┐
│ canAccess('view_        │
│ products') ?            │
└──────┬──────────┬───────┘
       │ YES      │ NO
       ▼          ▼
    SHOW      HIDE
   Products   Menu Item
   Menu Item
       │
       └─────────┬────────────┐
                 │            │
                 ▼            ▼
         Check next      Skip to
         permission      next item
```

## Complete Request Lifecycle Diagram

```
1. INITIAL REQUEST
   ├─ User clicks Products link
   └─ Browser requests: /admin/products.php

2. PAGE LOADS
   ├─ Session starts
   ├─ Database connection opens
   ├─ Functions loaded
   └─ Auth-check middleware loaded

3. PERMISSION CHECK
   ├─ checkAdminPermission('view_products') called
   ├─ Check if $_SESSION['admin_id'] exists
   ├─ Get admin's permissions
   └─ Verify 'view_products' in permissions

4. DECISION POINT
   ├─ IF permission denied:
   │  └─ Redirect to permission-denied.php
   │     └─ Display 403 error
   └─ IF permission granted:
      └─ Continue page rendering

5. PAGE RENDERING
   ├─ Sidebar renders
   │  ├─ Checks each menu item permission
   │  └─ Only shows accessible items
   ├─ Content area renders
   │  ├─ Product list displays
   │  ├─ Edit buttons show IF edit_product permission
   │  └─ Delete buttons show IF delete_product permission
   └─ Footer renders

6. USER INTERACTION
   ├─ User clicks "Edit Product"
   ├─ Browser requests: /admin/edit_product.php?id=5
   └─ Go back to step 2 (repeats for each request)
```

---

This RBAC system provides **layered security**:
1. **Login layer** - Only authenticated admins
2. **Session layer** - Permissions cached
3. **Page layer** - Permissions checked on each page
4. **Component layer** - Buttons/features hidden based on permissions

Each layer is independent and can fail securely.
