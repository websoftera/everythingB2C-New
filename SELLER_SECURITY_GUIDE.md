# Seller System - Security & Data Isolation Guide

## 🔒 Security Principles

The seller system follows strict data isolation rules to ensure sellers can ONLY manage their own data and cannot access or modify admin-created content.

---

## 🛡️ Data Isolation Rules

### **Products:**
- ✅ Sellers can ONLY see products where `seller_id = their_seller_id`
- ✅ Sellers can ONLY edit/delete products where `seller_id = their_seller_id`
- ❌ Sellers CANNOT see admin products (`seller_id IS NULL`)
- ❌ Sellers CANNOT see other sellers' products
- ❌ Sellers CANNOT modify admin products

### **Categories:**
- ✅ Sellers can VIEW all categories (admin + own)
- ✅ Sellers can CREATE new categories (`seller_id = their_seller_id`)
- ✅ Sellers can EDIT only their own categories
- ✅ Sellers can DELETE only their own categories
- ✅ Sellers can USE admin categories for products
- ❌ Sellers CANNOT edit admin categories
- ❌ Sellers CANNOT delete admin categories
- ❌ Sellers CANNOT edit other sellers' categories

### **Orders:**
- ✅ Sellers can ONLY see orders containing their products
- ✅ Sellers can VIEW customer information
- ✅ Sellers can VIEW order status
- ❌ Sellers CANNOT update order status (admin only)
- ❌ Sellers CANNOT see orders without their products
- ❌ Sellers CANNOT see other sellers' orders

### **Reports:**
- ✅ Sellers can ONLY see statistics for their own products
- ✅ Sellers can ONLY see revenue from their products
- ❌ Sellers CANNOT see other sellers' statistics
- ❌ Sellers CANNOT see overall platform statistics

---

## 🔐 Security Implementation

### **1. Products Security**

#### Query Filter:
```php
// SECURITY: Always filter by seller_id
$where_conditions = ["p.seller_id = ?"];
$params = [$sellerId];
```

#### Bulk Actions:
```php
// SECURITY: Ensure seller_id check in WHERE clause
$stmt = $pdo->prepare("DELETE FROM products WHERE id IN ($placeholders) AND seller_id = ?");
$params = array_merge($selected_products, [$sellerId]);
$stmt->execute($params);
```

#### Edit/Delete Operations:
```php
// SECURITY: Verify product belongs to seller before any operation
$stmt = $pdo->prepare("SELECT id FROM products WHERE id = ? AND seller_id = ?");
$stmt->execute([$productId, $sellerId]);
if (!$stmt->fetch()) {
    $_SESSION['error_message'] = 'Access denied';
    exit;
}
```

---

### **2. Categories Security**

#### Display Logic:
```php
// Show all categories (admin + seller) for product assignment
$stmt = $pdo->prepare("SELECT c.* FROM categories c 
                       WHERE c.seller_id = ? OR c.seller_id IS NULL
                       ORDER BY c.name");
$stmt->execute([$sellerId]);
```

#### Edit/Delete Restrictions:
```php
// SECURITY: Only allow editing seller's own categories
$isSellerCategory = ($category['seller_id'] == $sellerId);
if ($isSellerCategory) {
    // Show edit/delete buttons
} else {
    // Show "Read-Only" badge
}
```

#### Database Checks:
```php
// SECURITY: Verify category belongs to seller
$stmt = $pdo->prepare("UPDATE categories SET ... WHERE id = ? AND seller_id = ?");
$stmt->execute([..., $categoryId, $sellerId]);
```

---

### **3. Orders Security**

#### Query Filter:
```sql
-- SECURITY: Only show orders with seller's products
SELECT DISTINCT o.* 
FROM orders o
JOIN order_items oi ON o.id = oi.order_id
JOIN products p ON oi.product_id = p.id
WHERE p.seller_id = ?  -- CRITICAL: Filter by seller_id
```

#### Order Items:
```php
// When showing order details, only show items from seller's products
$stmt = $pdo->prepare("SELECT oi.*, p.name 
                       FROM order_items oi
                       JOIN products p ON oi.product_id = p.id
                       WHERE oi.order_id = ? AND p.seller_id = ?");
$stmt->execute([$orderId, $sellerId]);
```

---

### **4. Statistics Security**

```php
// SECURITY: Only calculate stats for seller's data
$stmt = $pdo->prepare("SELECT COUNT(*) FROM products WHERE seller_id = ?");
$stmt->execute([$sellerId]);

$stmt = $pdo->prepare("SELECT SUM(total_amount) 
                       FROM orders o
                       JOIN order_items oi ON o.id = oi.order_id
                       JOIN products p ON oi.product_id = p.id
                       WHERE p.seller_id = ?");
$stmt->execute([$sellerId]);
```

---

## 🎨 Visual Indicators

### **Category List:**
- **Admin Categories:** Light gray background + "Admin Category" blue badge + "Read-Only" badge
- **Seller Categories:** White background + Edit/Delete buttons

### **Product List:**
- **Seller Products:** Full edit/delete capabilities
- **Approval Status:** Green (Approved) / Yellow (Pending) / Red (Rejected with reason)

---

## ✅ Security Checklist

All seller pages include these security measures:

### Products (`seller/products.php`):
- [x] Filter: `WHERE p.seller_id = ?`
- [x] Bulk actions: `AND seller_id = ?`
- [x] Delete: Checks `seller_id = ?`
- [x] Export: Only seller's products

### Categories (`seller/categories.php`):
- [x] Shows admin categories as read-only
- [x] Edit: `WHERE id = ? AND seller_id = ?`
- [x] Delete: `WHERE id = ? AND seller_id = ?`
- [x] Create: Sets `seller_id` automatically

### Add Product (`seller/add_product.php`):
- [x] Sets `seller_id` on insert
- [x] Sets `is_approved = 0` (requires approval)
- [x] Logs activity with `seller_id`

### Edit Product (`seller/edit_product.php`):
- [x] Verifies `seller_id` before showing
- [x] Update: `WHERE id = ? AND seller_id = ?`
- [x] Re-approval required after edit

### Delete Product (`seller/delete_product.php`):
- [x] Checks permission first
- [x] Verifies `seller_id` ownership
- [x] Delete: `WHERE id = ? AND seller_id = ?`

### Orders (`seller/orders.php`):
- [x] JOIN filters by `p.seller_id`
- [x] Only shows orders with seller products
- [x] Cannot modify order status

### Reports (`seller/reports.php`):
- [x] Statistics filtered by `seller_id`
- [x] Revenue only from seller products
- [x] Commission calculated on seller sales only

---

## 🚨 Prevented Actions

Sellers CANNOT:
- ❌ See admin products
- ❌ Edit admin products
- ❌ Delete admin products
- ❌ Edit admin categories
- ❌ Delete admin categories
- ❌ See other sellers' products
- ❌ Edit other sellers' data
- ❌ See orders without their products
- ❌ Update order status
- ❌ Access admin panel
- ❌ See platform-wide statistics
- ❌ Import products (admin only)

---

## 🎯 Example Security Scenarios

### Scenario 1: Seller Tries to Edit Admin Category
```
1. Seller clicks edit on admin category
2. Modal doesn't appear (button doesn't exist)
3. If they try direct URL manipulation:
   - Security check: WHERE id = ? AND seller_id = ?
   - Result: 0 rows updated
   - Error: "Category not found or access denied"
```

### Scenario 2: Seller Tries to Delete Other Seller's Product
```
1. Seller manipulates URL: delete_product.php?id=999
2. Security check: SELECT ... WHERE id = 999 AND seller_id = ?
3. Result: No match found
4. Error: "Product not found or access denied"
5. Redirect to products.php
```

### Scenario 3: Seller Tries Bulk Delete on Mixed Products
```
1. Seller somehow selects products with different seller_ids
2. Bulk delete query: DELETE FROM products WHERE id IN (...) AND seller_id = ?
3. Result: Only their own products deleted
4. Other products remain untouched
```

---

## 📊 Database-Level Security

### Foreign Keys:
```sql
-- Products linked to seller
ALTER TABLE products 
ADD CONSTRAINT fk_products_seller 
FOREIGN KEY (seller_id) REFERENCES sellers(id) ON DELETE SET NULL;

-- If seller deleted, products become admin products (seller_id = NULL)
```

### Indexes:
```sql
-- Fast seller_id lookups
CREATE INDEX idx_seller_id ON products(seller_id);
CREATE INDEX idx_products_seller_approved ON products(seller_id, is_approved, is_active);
```

---

## 🧪 Testing Security

### Test Cases:

1. **Test Product Isolation:**
```sql
-- Create products for different sellers
INSERT INTO products (seller_id, name, ...) VALUES (1, 'Seller 1 Product', ...);
INSERT INTO products (seller_id, name, ...) VALUES (2, 'Seller 2 Product', ...);
INSERT INTO products (seller_id, name, ...) VALUES (NULL, 'Admin Product', ...);

-- Login as Seller 1
-- Verify: Only sees "Seller 1 Product"
-- Verify: Cannot edit "Seller 2 Product" or "Admin Product"
```

2. **Test Category Isolation:**
```sql
-- Create categories
INSERT INTO categories (seller_id, name) VALUES (1, 'Seller 1 Category');
INSERT INTO categories (seller_id, name) VALUES (NULL, 'Admin Category');

-- Login as Seller 1  
-- Verify: Sees both categories
-- Verify: Can edit only "Seller 1 Category"
-- Verify: "Admin Category" shows as read-only
```

3. **Test Order Filtering:**
```sql
-- Create orders with different seller products
-- Login as Seller 1
-- Verify: Only sees orders containing Seller 1's products
-- Verify: Doesn't see orders with only other sellers' products
```

---

## ✅ Security Features Implemented

- ✅ Session-based authentication
- ✅ Seller ID verification on all operations
- ✅ SQL injection protection (prepared statements)
- ✅ XSS protection (htmlspecialchars)
- ✅ Permission checks before sensitive operations
- ✅ Activity logging for audit trail
- ✅ Read-only vs editable indicators
- ✅ Error messages don't reveal system details
- ✅ Automatic seller_id assignment on creates
- ✅ seller_id verification on updates/deletes

---

## 📋 Summary

**What Sellers CAN Do:**
- ✅ Manage their own products (CRUD)
- ✅ Manage their own categories (CRUD)
- ✅ View admin categories (read-only) and use them for products
- ✅ View orders containing their products
- ✅ View their own statistics
- ✅ Export their own data

**What Sellers CANNOT Do:**
- ❌ Access any admin data
- ❌ Modify any admin data
- ❌ Access other sellers' data
- ❌ Bypass approval workflow
- ❌ Update order status
- ❌ See platform-wide data

**Visual Indicators:**
- 🔵 Blue "Admin Category" badge = Read-only
- 🟢 Green "Approved" badge = Live on website
- 🟡 Yellow "Pending" badge = Awaiting approval
- 🔴 Red "Rejected" badge = Needs fixes
- 🔒 "Read-Only" badge = Cannot edit/delete

---

**The seller system is now secure with complete data isolation!** 🔒✅
