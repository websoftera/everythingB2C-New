# Seller Login System - Complete Guide

## 🎯 Seller vs Admin Login

### **Two Separate Login Systems:**

| Feature | Admin Login | Seller Login |
|---------|------------|--------------|
| **URL** | `admin/login.php` | `seller/login.php` |
| **Database Table** | `admins` | `users` + `sellers` |
| **User Type** | Admin only | Sellers only |
| **Dashboard** | `admin/index.php` | `seller/index.php` |
| **Session Variable** | `$_SESSION['admin_id']` | `$_SESSION['seller_id']` |
| **Access** | Full system access | Limited to own data |

---

## 🔐 How Seller Login Works

### **Login Process:**

1. **Seller goes to:** `http://localhost/demo/seller/login.php`
2. **Enters credentials:**
   - Email (from `users` table)
   - Password (from `users` table)
3. **System checks:**
   - User exists in `users` table
   - `user_role = 'seller'`
   - `is_seller_approved = 1`
   - Seller account is active (`sellers.is_active = 1`)
4. **If valid:** Logs in and redirects to seller dashboard
5. **If invalid:** Shows error message

### **Session Variables Set:**
```php
$_SESSION['seller_id'] = $seller['seller_id'];           // ID from sellers table
$_SESSION['seller_user_id'] = $seller['id'];              // ID from users table
$_SESSION['seller_name'] = $seller['name'];               // User name
$_SESSION['seller_email'] = $seller['email'];             // User email
$_SESSION['seller_business_name'] = $seller['business_name']; // Business name
```

---

## 🚀 How to Assign a User as Seller

### **Method 1: Via Admin Panel (Recommended)**

1. **Login as Admin**
   - Go to `http://localhost/demo/admin/login.php`
   - Use admin credentials

2. **Create Seller**
   - Click "Manage Sellers" in sidebar
   - Click "Add New Seller" button
   - Select a customer user from dropdown
   - Fill in business details
   - Click "Create Seller"

3. **System Automatically:**
   - Updates `users.user_role = 'seller'`
   - Sets `users.is_seller_approved = 1`
   - Creates entry in `sellers` table
   - Creates permissions in `seller_permissions`
   - Initializes statistics

### **Method 2: Direct SQL (For Testing)**

```sql
-- Step 1: Find user you want to make a seller
SELECT id, name, email FROM users WHERE email = 'seller@example.com';

-- Step 2: Update user role
UPDATE users 
SET user_role = 'seller', 
    is_seller_approved = 1, 
    seller_approved_at = NOW(), 
    seller_approved_by = 1 
WHERE id = 2;  -- Replace with your user ID

-- Step 3: Create seller record
INSERT INTO sellers (user_id, business_name, commission_percentage) 
VALUES (2, 'Test Seller Store', 10.00);  -- Replace user_id with your user ID

-- Step 4: Get the seller_id
SET @seller_id = LAST_INSERT_ID();

-- Step 5: Create permissions
INSERT INTO seller_permissions (seller_id) VALUES (@seller_id);

-- Step 6: Create statistics
INSERT INTO seller_statistics (seller_id) VALUES (@seller_id);
```

---

## 🔑 Seller Login Credentials

### **For Testing:**

If you created a seller from a user with:
- **Email:** `user@example.com`
- **Password:** `password123` (or whatever their customer password was)

Then seller logs in with:
- **URL:** `http://localhost/demo/seller/login.php`
- **Email:** `user@example.com`
- **Password:** `password123` (same as their customer password)

---

## 📊 Seller Dashboard Features

After successful login, sellers see:

### **Dashboard (seller/index.php):**
- ✅ Total products count
- ✅ Active products count
- ✅ Pending approval count (with warning if any)
- ✅ Total orders count
- ✅ Revenue overview (total revenue, commission, earnings)
- ✅ Quick action buttons
- ✅ Business information summary

### **Sidebar Menu (Based on Permissions):**
- 🏠 **Dashboard** - Main dashboard
- 📦 **My Products** - View/edit own products (with pending count badge)
- ➕ **Add Product** - Add new products (requires approval)
- 🏷️ **My Categories** - Manage own categories
- 🛒 **My Orders** - View orders with seller products
- 📊 **Reports** - Sales and performance reports
- ⚙️ **Settings** - Update profile and business info

---

## 🎨 Seller Portal Design

### **Login Page:**
- Green gradient theme (`#9fbe1b`)
- "Seller Portal" branding
- "Partner Dashboard" badge
- Link to admin login at bottom
- Link back to main website

### **Dashboard:**
- Uses same admin CSS (`admin/assets/css/admin.css`)
- Green color scheme for seller branding
- Statistics cards
- Revenue breakdown
- Quick action buttons

---

## 🔒 Security Features

### **Authentication:**
- ✅ Password verification using `password_verify()`
- ✅ Checks user role is 'seller'
- ✅ Verifies seller is approved
- ✅ Checks seller account is active
- ✅ Session-based authentication

### **Authorization:**
- ✅ Permission-based menu (only shows allowed options)
- ✅ Seller can only see own data
- ✅ Cannot access admin panel
- ✅ Cannot see other sellers' data

### **Activity Logging:**
- ✅ Login events logged
- ✅ Activity tracked in `seller_activity_log`

---

## 🧪 Testing Seller Login

### **Test Steps:**

1. **Create a test seller via admin panel:**
   ```
   Admin → Manage Sellers → Add New Seller
   - Select user: Test User (user@example.com)
   - Business Name: Test Store
   - Click Create
   ```

2. **Get the user's credentials:**
   - Email: `user@example.com`
   - Password: (whatever password that user had)

3. **Test seller login:**
   ```
   Go to: http://localhost/demo/seller/login.php
   Enter email and password
   Click "Login to Seller Dashboard"
   ```

4. **Expected result:**
   - Redirected to `seller/index.php`
   - See seller dashboard with statistics
   - See sidebar with seller options
   - See business name in header

---

## 🐛 Troubleshooting

### **Issue 1: "Invalid email or password"**
**Cause:** User doesn't exist or wrong credentials

**Fix:**
```sql
-- Check if user exists
SELECT id, name, email, user_role, is_seller_approved 
FROM users WHERE email = 'your_seller_email@example.com';

-- Check if seller record exists
SELECT * FROM sellers WHERE user_id = 2; -- Replace with user ID
```

### **Issue 2: "Account not approved/active"**
**Cause:** Seller not approved or account inactive

**Fix:**
```sql
-- Approve seller
UPDATE users 
SET user_role = 'seller', is_seller_approved = 1 
WHERE id = 2;

-- Activate seller account
UPDATE sellers SET is_active = 1 WHERE user_id = 2;
```

### **Issue 3: Page redirects to login.php**
**Cause:** Session not set properly

**Fix:** Check that `seller_system_schema.sql` was imported and seller was created properly

### **Issue 4: Dashboard shows errors**
**Cause:** Missing database tables or permissions

**Fix:**
```sql
-- Check seller record
SELECT * FROM sellers WHERE user_id = 2;

-- Check permissions
SELECT * FROM seller_permissions WHERE seller_id = 1;

-- Check statistics
SELECT * FROM seller_statistics WHERE seller_id = 1;
```

---

## 📁 Files Created

### **Seller Login System:**
1. ✅ `seller/login.php` - Seller login page
2. ✅ `seller/logout.php` - Seller logout
3. ✅ `seller/index.php` - Seller dashboard
4. ✅ `seller/includes/sidebar.php` - Seller sidebar (permission-based)
5. ✅ `seller/includes/header.php` - Seller header

### **Still Need to Create:**
- `seller/products.php` - Product management
- `seller/add_product.php` - Add new product
- `seller/edit_product.php` - Edit product
- `seller/categories.php` - Category management
- `seller/orders.php` - Orders view
- `seller/reports.php` - Reports
- `seller/settings.php` - Settings

---

## 🎯 Quick Test

### **Create a Test Seller:**

```sql
-- Assuming you have a user with ID=2 and email='test@example.com'

-- 1. Make user a seller
UPDATE users SET user_role = 'seller', is_seller_approved = 1 WHERE id = 2;

-- 2. Create seller record
INSERT INTO sellers (user_id, business_name, commission_percentage) 
VALUES (2, 'My Test Store', 10.00);

-- 3. Create permissions
INSERT INTO seller_permissions (seller_id) VALUES (LAST_INSERT_ID());

-- 4. Create statistics
INSERT INTO seller_statistics (seller_id) VALUES (LAST_INSERT_ID());
```

### **Test Login:**
1. Go to: `http://localhost/demo/seller/login.php`
2. Email: `test@example.com` (the user's email)
3. Password: (the user's password - same as they use for customer login)
4. Click "Login to Seller Dashboard"
5. Should see seller dashboard with green theme

---

## 🌐 Login URLs

### **For Customers:**
```
http://localhost/demo/login.php
```

### **For Sellers:**
```
http://localhost/demo/seller/login.php
```

### **For Admins:**
```
http://localhost/demo/admin/login.php
```

---

## ✅ Complete Login Flow

```
Customer → login.php → Regular website
         ↓
    (Admin makes user a seller)
         ↓
Seller → seller/login.php → Seller dashboard
         ↓
    (Can manage products, view orders, see reports)
         ↓
    (Products need admin approval before going live)
         ↓
Admin → admin/login.php → Admin panel
         ↓
    (Approves seller products)
         ↓
    Products visible on website ✓
```

---

## 🔐 Password Information

**Important:** Sellers use the SAME password they had as customers!

- When you convert a customer to a seller, their password doesn't change
- They use their existing user email and password
- The system just checks for `user_role = 'seller'` instead of `'customer'`

---

**Status:** Seller login system fully implemented! ✅  
**Next:** Test login and create remaining seller dashboard pages as needed
