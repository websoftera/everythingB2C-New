# Seller System Schema - Troubleshooting Guide

## Common Import Errors & Solutions

### Error 1: Unknown column 'status' in 'products'
```
#1054 - Unknown column 'status' in 'products'
```

**Cause:** Your database uses `is_active` instead of `status`.

**Solution:** ✅ **FIXED** - The schema now uses `is_active` column.

---

### Error 2: Unknown column 'first_name' in 'field list'
```
#1054 - Unknown column 'u.first_name' in 'field list'
```

**Cause:** Your users table has `name` instead of `first_name` and `last_name`.

**Solution:** ✅ **FIXED** - The schema now uses `name` column.

---

### Error 3: Duplicate key constraint
```
#1005 - Can't create table (errno: 121 "Duplicate key on write or update")
```

**Cause:** You've run the schema before and the foreign key constraints already exist.

**Solution:** ✅ **FIXED** - The schema now drops existing constraints before creating new ones.

**Alternative:** If still having issues, manually drop constraints:
```sql
-- Drop existing foreign keys
ALTER TABLE products DROP FOREIGN KEY IF EXISTS fk_products_seller;
ALTER TABLE categories DROP FOREIGN KEY IF EXISTS fk_categories_seller;

-- Then run the schema again
```

---

### Error 4: Table already exists
```
#1050 - Table 'sellers' already exists
```

**Cause:** The table was created in a previous partial import.

**Solution:** The schema uses `CREATE TABLE IF NOT EXISTS`, so this shouldn't happen. If it does:

**Option 1: Skip table creation** (if data exists)
- Comment out the CREATE TABLE statement
- Re-run only the ALTER TABLE statements

**Option 2: Drop and recreate** (if no important data)
```sql
DROP TABLE IF EXISTS seller_activity_log;
DROP TABLE IF EXISTS seller_product_approval_history;
DROP TABLE IF EXISTS seller_statistics;
DROP TABLE IF EXISTS seller_permissions;
DROP TABLE IF EXISTS sellers;

-- Then run the schema again
```

---

### Error 5: Cannot add foreign key constraint
```
#1215 - Cannot add foreign key constraint
```

**Cause:** The referenced table doesn't exist yet, or there's data mismatch.

**Solutions:**

1. **Check table creation order:**
   - `sellers` table must exist before creating foreign keys
   - Run in order: Create sellers table → Create other tables → Add foreign keys

2. **Check existing data:**
```sql
-- Check if there are any invalid seller_id values in products
SELECT COUNT(*) FROM products 
WHERE seller_id IS NOT NULL 
AND seller_id NOT IN (SELECT id FROM sellers);
```

3. **Temporarily allow NULL:**
```sql
-- Set invalid seller_id to NULL
UPDATE products SET seller_id = NULL 
WHERE seller_id IS NOT NULL 
AND seller_id NOT IN (SELECT id FROM sellers);
```

---

### Error 6: Column already exists
```
#1060 - Duplicate column name 'seller_id'
```

**Cause:** Column was added in previous partial import.

**Solution:** The schema uses `ADD COLUMN IF NOT EXISTS`, but MySQL versions < 8.0.26 don't support this.

**For MySQL < 8.0.26:**
```sql
-- Check if column exists before adding
SELECT COUNT(*) FROM information_schema.COLUMNS 
WHERE TABLE_SCHEMA = 'your_database_name' 
AND TABLE_NAME = 'products' 
AND COLUMN_NAME = 'seller_id';

-- If it returns 0, add the column
-- If it returns 1, skip this ALTER TABLE statement
```

**Alternative: Drop and re-add:**
```sql
ALTER TABLE products DROP COLUMN IF EXISTS seller_id;
ALTER TABLE products DROP COLUMN IF EXISTS is_approved;
ALTER TABLE products DROP COLUMN IF EXISTS approved_at;
ALTER TABLE products DROP COLUMN IF EXISTS approved_by;
ALTER TABLE products DROP COLUMN IF EXISTS rejection_reason;

-- Then run the schema again
```

---

## Pre-Import Checklist

Before importing, verify:

- [ ] You have a backup of your database
- [ ] MySQL version is compatible (5.7+ recommended)
- [ ] You have sufficient privileges (CREATE, ALTER, DROP)
- [ ] No other transactions are running
- [ ] The database name is correct

## Import Methods

### Method 1: Command Line (Recommended)
```bash
mysql -u root -p your_database < database/seller_system_schema.sql
```

**Pros:**
- Shows detailed error messages
- Faster import
- Better for large schemas

### Method 2: phpMyAdmin
1. Open phpMyAdmin
2. Select your database
3. Go to "Import" tab
4. Choose `seller_system_schema.sql`
5. Set "Format" to "SQL"
6. Click "Go"

**Pros:**
- User-friendly interface
- Good for beginners
- Visual progress indicator

### Method 3: Partial Import
If full import fails, import in stages:

```sql
-- Stage 1: Create sellers table
CREATE TABLE IF NOT EXISTS sellers (...);

-- Stage 2: Add columns to existing tables
ALTER TABLE users ADD COLUMN...
ALTER TABLE products ADD COLUMN...

-- Stage 3: Add indexes
ALTER TABLE products ADD INDEX...

-- Stage 4: Add foreign keys
ALTER TABLE products ADD CONSTRAINT...

-- Stage 5: Create other tables
CREATE TABLE IF NOT EXISTS seller_permissions...
```

---

## Verification After Import

Run these queries to verify successful import:

```sql
-- 1. Check if seller columns were added to products
SHOW COLUMNS FROM products LIKE '%seller%';
SHOW COLUMNS FROM products LIKE '%approved%';

-- Expected output:
-- seller_id, is_approved, approved_at, approved_by, rejection_reason

-- 2. Check if seller columns were added to categories
SHOW COLUMNS FROM categories LIKE '%seller%';

-- Expected output:
-- seller_id, is_approved

-- 3. Check if user_role was added to users
SHOW COLUMNS FROM users LIKE '%role%';

-- Expected output:
-- user_role, is_seller_approved, seller_approved_at, seller_approved_by

-- 4. Check if seller tables were created
SHOW TABLES LIKE 'seller%';

-- Expected output:
-- sellers
-- seller_activity_log
-- seller_permissions
-- seller_product_approval_history
-- seller_statistics

-- 5. Check foreign keys
SELECT 
    CONSTRAINT_NAME, 
    TABLE_NAME, 
    REFERENCED_TABLE_NAME
FROM information_schema.KEY_COLUMN_USAGE
WHERE CONSTRAINT_SCHEMA = DATABASE()
AND (CONSTRAINT_NAME LIKE 'fk_products_seller' OR CONSTRAINT_NAME LIKE 'fk_categories_seller');

-- Expected output:
-- fk_products_seller | products | sellers
-- fk_categories_seller | categories | sellers

-- 6. Check indexes
SHOW INDEX FROM products WHERE Key_name LIKE '%seller%';
SHOW INDEX FROM products WHERE Key_name LIKE '%approved%';

-- Expected output:
-- idx_seller_id, idx_is_approved, idx_products_seller_approved

-- 7. Verify view was created
SHOW FULL TABLES WHERE Table_type = 'VIEW';

-- Expected output should include:
-- seller_order_items | VIEW
```

---

## Clean Uninstall (If Needed)

To completely remove the seller system:

```sql
-- Drop views
DROP VIEW IF EXISTS seller_order_items;

-- Drop tables (in reverse order of dependencies)
DROP TABLE IF EXISTS seller_activity_log;
DROP TABLE IF EXISTS seller_product_approval_history;
DROP TABLE IF EXISTS seller_statistics;
DROP TABLE IF EXISTS seller_permissions;
DROP TABLE IF EXISTS sellers;

-- Remove foreign keys
ALTER TABLE products DROP FOREIGN KEY IF EXISTS fk_products_seller;
ALTER TABLE categories DROP FOREIGN KEY IF EXISTS fk_categories_seller;

-- Remove indexes
ALTER TABLE products DROP INDEX IF EXISTS idx_seller_id;
ALTER TABLE products DROP INDEX IF EXISTS idx_is_approved;
ALTER TABLE products DROP INDEX IF EXISTS idx_products_seller_approved;
ALTER TABLE categories DROP INDEX IF EXISTS idx_seller_id;
ALTER TABLE categories DROP INDEX IF EXISTS idx_categories_seller;
ALTER TABLE orders DROP INDEX IF EXISTS idx_seller_orders;

-- Remove columns from products
ALTER TABLE products 
DROP COLUMN IF EXISTS seller_id,
DROP COLUMN IF EXISTS is_approved,
DROP COLUMN IF EXISTS approved_at,
DROP COLUMN IF EXISTS approved_by,
DROP COLUMN IF EXISTS rejection_reason;

-- Remove columns from categories
ALTER TABLE categories 
DROP COLUMN IF EXISTS seller_id,
DROP COLUMN IF EXISTS is_approved;

-- Remove columns from users
ALTER TABLE users 
DROP COLUMN IF EXISTS user_role,
DROP COLUMN IF EXISTS is_seller_approved,
DROP COLUMN IF EXISTS seller_approved_at,
DROP COLUMN IF EXISTS seller_approved_by;

-- Remove columns from orders
ALTER TABLE orders 
DROP COLUMN IF EXISTS seller_id;
```

---

## Getting Help

If you're still experiencing issues:

1. **Check MySQL error log:**
   - Linux: `/var/log/mysql/error.log`
   - Windows: `C:\xampp\mysql\data\*.err`
   - XAMPP: Check `mysql_error.log` in xampp/mysql/data/

2. **Verify MySQL version:**
```sql
SELECT VERSION();
```

3. **Check privileges:**
```sql
SHOW GRANTS FOR CURRENT_USER();
```

4. **Export current schema for comparison:**
```bash
mysqldump -u root -p --no-data your_database > current_schema.sql
```

5. **Test on a copy database first:**
```sql
CREATE DATABASE test_everythingb2c;
USE test_everythingb2c;
-- Import your existing structure
-- Then run seller_system_schema.sql
```

---

## Success Indicators

You'll know the import was successful when:

- ✅ No error messages during import
- ✅ All verification queries return expected results
- ✅ You can access `admin/manage_sellers.php` without errors
- ✅ You can set user_role to 'admin' and 'seller'
- ✅ You can create a test seller
- ✅ New columns appear in product/category tables

---

**Last Updated:** After fixing duplicate key constraint error  
**Schema Version:** 1.0.2 (Compatible with your database structure)
