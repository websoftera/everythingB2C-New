# Seller System Schema - Fixed for Your Database

## Issues Fixed
The original seller schema referenced columns that didn't exist in your database:
- ❌ `products.status` (doesn't exist - you use `is_active`)
- ❌ `categories.status` (doesn't exist)
- ❌ `users.first_name` and `users.last_name` (don't exist - you use single `name` column)

## What Was Changed

### 1. Products Table
**Original (Error):**
```sql
ALTER TABLE products 
ADD COLUMN IF NOT EXISTS is_approved TINYINT(1) DEFAULT 1 AFTER status;
```

**Fixed:**
```sql
ALTER TABLE products 
ADD COLUMN IF NOT EXISTS is_approved TINYINT(1) DEFAULT 1 AFTER is_active;
```

### 2. Categories Table
**Original (Error):**
```sql
ALTER TABLE categories 
ADD COLUMN IF NOT EXISTS is_approved TINYINT(1) DEFAULT 1 AFTER status;
```

**Fixed:**
```sql
ALTER TABLE categories 
ADD COLUMN IF NOT EXISTS is_approved TINYINT(1) DEFAULT 1 AFTER product_count;
```

### 3. Index Definitions
**Original (Error):**
```sql
CREATE INDEX IF NOT EXISTS idx_products_seller_approved ON products(seller_id, is_approved, status);
CREATE INDEX IF NOT EXISTS idx_categories_seller ON categories(seller_id, status);
```

**Fixed:**
```sql
CREATE INDEX IF NOT EXISTS idx_products_seller_approved ON products(seller_id, is_approved, is_active);
CREATE INDEX IF NOT EXISTS idx_categories_seller ON categories(seller_id);
```

### 4. Users Table References
**Original (Error):**
```sql
SELECT s.*, u.first_name, u.last_name, u.email FROM...
u.first_name, u.last_name -- Error: columns don't exist
```

**Fixed:**
```sql
SELECT s.*, u.name, u.email FROM...
u.name -- Single name column
```

### 5. View Definition
**Original (Error):**
```sql
u.first_name, u.last_name, u.email, u.phone
```

**Fixed:**
```sql
u.name as customer_name, u.email, u.phone
```

### 6. PHP Functions
**Original (Error):**
```php
SUM(CASE WHEN status = 'active' AND is_approved = 1 THEN 1 ELSE 0 END)
```

**Fixed:**
```php
SUM(CASE WHEN is_active = 1 AND is_approved = 1 THEN 1 ELSE 0 END)
```

## Files Updated

1. ✅ `database/seller_system_schema.sql` - Fixed ALTER TABLE statements, indexes, and view
2. ✅ `includes/seller_functions.php` - Fixed statistics calculation and user queries
3. ✅ `includes/email_functions.php` - Fixed user name references
4. ✅ `admin/manage_sellers.php` - Fixed user name displays
5. ✅ `SELLER_SYSTEM_QUICKSTART.md` - Updated documentation

## Your Database Structure

Based on your `database/schema.sql`:

### Products Table Has:
- `id`
- `name`
- `slug`
- `sku`
- `hsn`
- `description`
- `mrp`
- `selling_price`
- `discount_percentage`
- `category_id`
- `main_image`
- `stock_quantity`
- `is_active` ✅ (This is what we use, not `status`)
- `is_featured`
- `is_discounted`
- `created_at`

### Categories Table Has:
- `id`
- `name`
- `slug`
- `image`
- `description`
- `product_count` ✅ (We place new columns after this)
- `parent_id`
- `created_at`

### Users Table Has:
- `id`
- `name` ✅ (Single name column, not first_name/last_name)
- `email`
- `password`
- `phone`
- `address`
- `city`
- `state`
- `pincode`
- `is_active`
- `created_at`

## ✅ Ready to Import

The fixed `database/seller_system_schema.sql` file is now ready to import without errors!

### Import Command:
```bash
mysql -u root -p your_database < database/seller_system_schema.sql
```

### Or via phpMyAdmin:
1. Open phpMyAdmin
2. Select your database
3. Go to "Import" tab
4. Choose `database/seller_system_schema.sql`
5. Click "Go"

## Frontend Query Updates

When querying products on the frontend, use:
```php
// Show only active and approved products
SELECT * FROM products WHERE is_active = 1 AND is_approved = 1
```

Instead of:
```php
// Old (if you were using status)
SELECT * FROM products WHERE status = 'active'
```

## Testing After Import

Run these queries to verify the schema was imported correctly:

```sql
-- Check if seller columns were added to products
DESCRIBE products;

-- Check if seller columns were added to categories  
DESCRIBE categories;

-- Check if sellers table was created
DESCRIBE sellers;

-- Check if seller_permissions table was created
DESCRIBE seller_permissions;

-- Check if seller_statistics table was created
DESCRIBE seller_statistics;
```

## Expected Results

After successful import, you should see:

### In `products` table:
- `seller_id` INT NULL
- `is_approved` TINYINT(1) DEFAULT 1
- `approved_at` TIMESTAMP NULL
- `approved_by` INT NULL
- `rejection_reason` TEXT NULL

### In `categories` table:
- `seller_id` INT NULL
- `is_approved` TINYINT(1) DEFAULT 1

### In `users` table:
- `user_role` ENUM('customer', 'seller', 'admin') DEFAULT 'customer'
- `is_seller_approved` TINYINT(1) DEFAULT 0
- `seller_approved_at` TIMESTAMP NULL
- `seller_approved_by` INT NULL

## Next Steps

1. ✅ Import the fixed schema
2. ✅ Set your admin user role: `UPDATE users SET user_role = 'admin' WHERE id = 1;`
3. ✅ Update admin sidebar with seller management links
4. ✅ Test creating a seller
5. ✅ Test product approval workflow

---

**Status:** Fixed and ready to import! ✅
