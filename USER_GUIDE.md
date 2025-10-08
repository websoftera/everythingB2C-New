# EverythingB2C - Complete User Guide

## 📖 Table of Contents
1. [For Administrators](#for-administrators)
2. [For Sellers](#for-sellers)
3. [Quick Reference](#quick-reference)

---

# For Administrators

## 🔐 Admin Login
**URL:** `http://localhost/demo/admin/login.php`

**Steps:**
1. Open the admin login page
2. Enter your admin email and password
3. Click "Login"
4. You'll be redirected to the admin dashboard

---

## 👥 Managing Sellers

### How to Create a Seller

**Path:** Admin Dashboard → Manage Sellers → Add New Seller

**Steps:**
1. Click "Manage Sellers" in the left sidebar
2. Click "Add New Seller" button (top right)
3. Fill in the form:
   - **Select User:** Choose a customer from the dropdown
   - **Business Name:** Enter the seller's business name (e.g., "ABC Store")
   - **Business Type:** Select type (Sole Proprietorship, Partnership, etc.)
   - **GST Number:** Enter GST number if available
   - **PAN Number:** Enter PAN number
   - **Commission %:** Set commission rate (default: 10%)
   - **Business Address:** Enter full business address
   - **Contact Details:** Business email and phone
   - **Bank Details:** Account name, number, IFSC, bank name
4. Click "Create Seller"
5. ✅ Seller account is now active!

**What Happens:**
- User becomes a seller (can login at `seller/login.php`)
- Seller can now add products
- Seller appears in "Manage Sellers" list
- Seller can access seller dashboard

---

### How to View Seller Information

**Path:** Admin Dashboard → Manage Sellers

**You can see:**
- Business name and GST number
- Contact person name
- Email and phone
- Total products (with active count)
- Total orders
- Total revenue
- Account status (Active/Inactive)

**Actions Available:**
- 👁️ **View Details:** See complete seller information
- 📦 **View Products:** See all products from this seller
- 🛒 **View Orders:** See orders with this seller's products
- ✏️ **Edit:** Update seller information
- 🔴/🟢 **Activate/Deactivate:** Control seller account status

---

## ✅ Approving Seller Products

### Why Product Approval is Needed
When sellers add products, they must be approved by admin before appearing on the website. This ensures quality control.

**Path:** Admin Dashboard → Approve Products

**Steps to Approve:**
1. Click "Approve Products" in sidebar (shows pending count)
2. You'll see all pending products with:
   - Product image
   - Product details (name, SKU, HSN, category)
   - Seller name
   - Pricing (MRP, selling price)
   - Stock quantity
   - GST rate
   - Description
   - Date added
3. Review the product carefully
4. Click "Approve Product" button
   - Popup appears asking for confirmation
   - Click "Yes, Approve"
5. ✅ Product is now live on the website!

**Steps to Reject:**
1. Click "Reject Product" button
2. Popup appears with text box
3. Enter reason for rejection (e.g., "Images are not clear", "Price too high")
4. Click "Reject Product"
5. ✅ Seller will see rejection reason and can fix the product

**Best Practices:**
- ✅ Check product images are clear
- ✅ Verify pricing is reasonable
- ✅ Ensure description is accurate
- ✅ Confirm product category is correct
- ✅ Check for duplicate products

---

## 📦 Managing All Seller Products

**Path:** Admin Dashboard → All Seller Products

**Features:**
- **Filter by Seller:** See products from specific seller
- **Filter by Approval:** Show approved/pending products only
- **View All:** See all seller products in one place
- **Quick Actions:**
  - View/Edit product details
  - Approve pending products
  - View which seller owns the product

---

## 🛒 Viewing Seller Orders

**Path:** Admin Dashboard → Seller Orders

**What You See:**
- All orders containing products from sellers
- Customer information
- Number of seller items vs total items
- Order status and payment method
- Order date and time

**Filters:**
- Filter by specific seller
- View all seller orders together
- Track which products are from sellers

---

## 📊 Admin Dashboard Features

### Product Management
**Path:** Admin Dashboard → Products

- **View all products** (admin + seller products)
- **Add new products** (admin products)
- **Edit products**
- **Delete products**
- **Bulk actions:** Activate, deactivate, delete multiple products
- **Search and filter**
- **Export to CSV**
- **Import from CSV**

### Category Management
**Path:** Admin Dashboard → Categories

- **View all categories** (admin + seller categories)
- **Add new category** with image
- **Edit category**
- **Delete category** (if no products)
- **Create subcategories**

### Order Management
**Path:** Admin Dashboard → Orders

- **View all orders**
- **Update order status**
- **Add tracking information**
- **Set delivery date**
- **Filter and search orders**
- **Download invoices**
- **Track orders**

### Reports
**Path:** Admin Dashboard → Reports

- View sales statistics
- Top selling products
- Sales by category
- Daily sales charts
- Revenue tracking

---

# For Sellers

## 🔐 Seller Login
**URL:** `http://localhost/demo/seller/login.php`

**Steps:**
1. Open the seller login page
2. Enter your email (same as customer email)
3. Enter your password (same as customer password)
4. Click "Login to Seller Dashboard"
5. You'll see your seller dashboard

**Note:** Use the same login credentials you use as a customer. Admin will inform you when your seller account is activated.

---

## 🏠 Seller Dashboard

**What You See:**
- **Statistics Cards:**
  - Total Products
  - Active Products
  - Pending Approval (products waiting for admin approval)
  - Total Orders
  
- **Revenue Overview:**
  - Total Revenue from your sales
  - Commission Amount (platform fee)
  - Your Earnings (revenue minus commission)
  
- **Quick Actions:**
  - Manage Products
  - Add New Product
  - View Orders
  - View Reports
  
- **Business Information:**
  - Your business name
  - Business type and GST number
  - Contact details
  - Commission rate

---

## 📦 Managing Your Products

### How to Add a New Product

**Path:** Seller Dashboard → My Products → Add New Product

**Steps:**
1. Click "Add New Product" button
2. Fill in the product form:
   - **Product Name:** Enter product name
   - **SKU:** Enter unique SKU code
   - **Category:** Select from dropdown (includes admin categories)
   - **HSN Code:** Enter HSN code
   - **Description:** Write detailed product description
   - **MRP:** Enter maximum retail price
   - **Selling Price:** Enter your selling price
   - **Stock Quantity:** How many units you have
   - **Max Qty Per Order:** Maximum units customer can buy (optional)
   - **GST Rate:** Select GST rate (0%, 5%, 12%, 18%, 28%)
   - **Product Options:**
     - ✅ Active (product is active)
     - ✅ Featured (show in featured section)
     - ✅ Discounted (show in discounted section)
   - **Main Image:** Upload product image (recommended: 800x800px)
   - **Additional Images:** Upload more images (optional)
3. Click "Submit for Approval"
4. ⏳ Your product is now **pending approval**

**What Happens Next:**
- Admin reviews your product
- If approved ✅: Product goes live on website
- If rejected ❌: You'll see rejection reason and can fix it

---

### How to View Your Products

**Path:** Seller Dashboard → My Products

**What You See:**
- List of all your products
- Product images (thumbnails)
- Product name, SKU, category
- Pricing and stock
- Status (Active/Inactive)
- **Approval Status:**
  - 🟢 **Approved:** Live on website
  - 🟡 **Pending:** Waiting for admin approval
  - 🔴 **Rejected:** Needs fixes (hover to see reason)

**Search and Filter:**
- **Search:** Type product name or SKU
- **Filter by Category:** Select category
- **Filter by Status:** Active or Inactive
- **Filter by Approval:** Approved or Pending

**Actions:**
- ✏️ **Edit:** Update product details
- 👁️ **View:** See product on website
- 🗑️ **Delete:** Remove product (if you have permission)

---

### How to Edit a Product

**Path:** Seller Dashboard → My Products → Edit button

**Steps:**
1. Find the product in your list
2. Click the ✏️ (edit) button
3. View product details
4. (Full edit form coming soon)

**Note:** After editing, product may need admin re-approval.

---

### Bulk Actions

**Path:** Seller Dashboard → My Products

**How to Use:**
1. Select multiple products using checkboxes
2. Choose bulk action from dropdown:
   - **Activate:** Make products active
   - **Deactivate:** Make products inactive
   - **Delete:** Remove products (if permitted)
3. Click "Apply"
4. ✅ Action applied to all selected products

---

### Export Your Products

**Path:** Seller Dashboard → My Products → Export CSV

**Steps:**
1. Click "Export CSV" button (top right)
2. CSV file downloads automatically
3. Open in Excel/Google Sheets

**CSV Contains:**
- Product ID, Name, SKU, HSN
- Category, Description
- MRP, Selling Price, Discount %
- Stock Quantity, GST Rate
- Status and Approval Status
- Creation date

---

## 🏷️ Managing Your Categories

### How to Add a Category

**Path:** Seller Dashboard → My Categories → Add New Category

**Steps:**
1. Click "Add New Category" button
2. Fill in the form:
   - **Category Name:** Enter category name
   - **Description:** Describe the category
   - **Parent Category:** Select if this is a subcategory (optional)
   - **Category Image:** Upload image (recommended: 400x400px)
3. Click "Add Category"
4. ✅ Category created!

**Using Categories:**
- You can now assign products to this category
- Category helps organize your products
- Customers can filter by category

---

### Admin Categories vs Your Categories

**In the category list, you'll see two types:**

1. **Admin Categories** (Gray background, blue "Admin Category" badge):
   - Created by admin
   - You can USE them for your products
   - You CANNOT edit or delete them
   - Shows "Read-Only" badge

2. **Your Categories** (White background):
   - Created by you
   - You can edit and delete
   - Only you can use them

**Why This Helps:**
- Use admin categories for standard products
- Create your own for unique product lines
- Better organization for your store

---

### How to Edit a Category

**Path:** Seller Dashboard → My Categories → Edit button

**Steps:**
1. Find your category (not admin category)
2. Click ✏️ (edit) button
3. Update information
4. Upload new image if needed
5. Click "Update Category"
6. ✅ Category updated!

**Note:** You can only edit categories you created.

---

### How to Delete a Category

**Path:** Seller Dashboard → My Categories → Delete button

**Steps:**
1. Find your category
2. Click 🗑️ (delete) button
3. Confirm deletion
4. ✅ Category deleted!

**Important:** You cannot delete categories that have products assigned to them. Remove products from the category first.

---

## 🛒 Managing Your Orders

### How to View Orders

**Path:** Seller Dashboard → My Orders

**What You See:**
- All orders containing your products
- Order number and tracking ID
- Customer name and contact
- Total order amount
- Payment method
- Order status
- Order date

**Note:** You only see orders that include YOUR products. The order might also contain products from other sellers.

---

### How to Filter Orders

**Filters Available:**
1. **Search:** Order #, Tracking ID, Customer name/email
2. **Order Status:** Pending, Processing, Shipped, Delivered, etc.
3. **Payment Method:** COD, Razorpay, Direct Payment
4. **Date:** Today, Last 7 Days, This Month

**Steps:**
1. Use the filter boxes at the top
2. Click "Filter" button
3. Results update instantly
4. Click "Clear" to reset filters

---

### How to View Order Details

**Path:** My Orders → Eye Icon

**Steps:**
1. Find the order
2. Click 👁️ (view) button
3. Modal opens showing:
   - Order number and tracking ID
   - Customer information
   - Shipping address
   - **YOUR products** in this order (with images and details)
   - Your items subtotal and GST
   - Payment information
   - Order summary

**Important:** You only see YOUR products from the order, not items from other sellers.

---

### How to Update Order Status

**Path:** My Orders → Edit Icon

**Steps:**
1. Find the order
2. Click ✏️ (edit/update) button
3. Update Status modal opens
4. Fill in the details:
   - **Order Status:** Select new status (Processing, Shipped, Delivered, etc.)
   - **Estimated Delivery Date:** Set expected delivery date
   - **Status Description:** Add notes (optional)
   - **External Tracking ID:** Add courier tracking number (e.g., DTDC123456)
   - **External Tracking Link:** Add tracking URL
5. Click "Update Status"
6. ✅ Status updated!
7. 📧 Customer receives email notification automatically

**When to Update Status:**
- Order confirmed → **Processing**
- Packed and ready → **Shipped** (add tracking ID)
- Out for delivery → **Out for Delivery**
- Received by customer → **Delivered**

**Best Practices:**
- ✅ Always add tracking ID when shipping
- ✅ Keep customers informed with status updates
- ✅ Add notes for clarity
- ✅ Set realistic delivery dates

---

### How to Track Orders

**Path:** My Orders → Truck Icon

**Steps:**
1. Click 🚚 (truck) icon
2. Opens tracking page in new tab
3. Customer can also track using this

---

### How to Download Invoice

**Path:** My Orders → Invoice Icon

**Steps:**
1. Click 📄 (invoice) icon
2. PDF invoice downloads
3. Can be sent to customer if needed

---

### How to Export Orders

**Path:** My Orders → Export CSV

**Steps:**
1. Apply any filters you want (optional)
2. Click "Export CSV" button
3. CSV file downloads with filtered orders
4. Open in Excel for analysis

---

## 📊 Viewing Your Reports

**Path:** Seller Dashboard → Reports

**What You See:**
- **Total Products:** All your products
- **Total Orders:** Orders with your products
- **Total Revenue:** Your sales amount
- **Commission:** Platform fee on your sales
- **Your Earnings:** Revenue minus commission

**Coming Soon:**
- Sales charts
- Top selling products
- Monthly comparisons
- Category-wise sales

---

## ⚙️ Updating Your Settings

**Path:** Seller Dashboard → Settings

**What You See:**
- Business name and type
- GST and PAN numbers
- Contact information
- Bank details
- Commission rate
- Account status

**To Update:**
Currently, contact admin to update your business information.

---

# For Administrators - Advanced Features

## 📦 Product Approval Workflow

### The Approval Process

1. **Seller adds product** → Product saved as "Pending"
2. **Admin notification** → Pending count shows in sidebar
3. **Admin reviews** → Check quality, pricing, details
4. **Admin approves** → Product goes live on website
   OR
   **Admin rejects** → Seller sees reason and can fix

### Approval Best Practices

✅ **Check Image Quality:**
- Images should be clear and professional
- Correct product shown
- No watermarks or text overlays

✅ **Verify Product Information:**
- Correct category selected
- Description is accurate and detailed
- HSN code is correct

✅ **Check Pricing:**
- Selling price is not higher than MRP
- Price is competitive
- Discount percentage is reasonable

✅ **Stock Availability:**
- Seller has mentioned stock quantity
- Stock is realistic

**If Rejecting:**
- Be specific about the issue
- Provide actionable feedback
- E.g., "Please upload clearer product images" or "Description needs more details"

---

## 👁️ Monitoring Seller Performance

**Path:** Admin Dashboard → Manage Sellers

**Metrics to Track:**
- **Product Count:** How many products seller has
- **Active Products:** Live products on website
- **Total Orders:** Sales performance
- **Total Revenue:** Earning contribution
- **Commission:** Platform earnings from seller

**Actions:**
- **High Performers:** Consider reducing commission
- **Low Activity:** Reach out to support seller
- **Quality Issues:** Review pending products carefully
- **Inactive:** Consider deactivating if not using

---

## 🔍 Viewing Seller Orders

**Path:** Admin Dashboard → Seller Orders

**Purpose:** See all orders that include seller products

**Use Cases:**
- Monitor seller fulfillment
- Track overall seller sales
- Resolve customer issues
- Analyze seller performance

**Filters:**
- Filter by specific seller
- See order details
- View customer information

---

# Quick Reference

## 🌐 All URLs

### Admin:
- **Login:** `http://localhost/demo/admin/login.php`
- **Dashboard:** `http://localhost/demo/admin/index.php`
- **Manage Sellers:** `http://localhost/demo/admin/manage_sellers.php`
- **Approve Products:** `http://localhost/demo/admin/approve_products.php`
- **Seller Products:** `http://localhost/demo/admin/seller_products.php`
- **Seller Orders:** `http://localhost/demo/admin/seller_orders.php`
- **Products:** `http://localhost/demo/admin/products.php`
- **Categories:** `http://localhost/demo/admin/categories.php`
- **Orders:** `http://localhost/demo/admin/orders.php`
- **Reports:** `http://localhost/demo/admin/reports.php`

### Seller:
- **Login:** `http://localhost/demo/seller/login.php`
- **Dashboard:** `http://localhost/demo/seller/index.php`
- **My Products:** `http://localhost/demo/seller/products.php`
- **Add Product:** `http://localhost/demo/seller/add_product.php`
- **My Categories:** `http://localhost/demo/seller/categories.php`
- **My Orders:** `http://localhost/demo/seller/orders.php`
- **Reports:** `http://localhost/demo/seller/reports.php`
- **Settings:** `http://localhost/demo/seller/settings.php`

---

## 🎯 Common Tasks

### Admin Tasks:

| Task | Path | Time |
|------|------|------|
| Create new seller | Manage Sellers → Add New Seller | 2 min |
| Approve product | Approve Products → Approve | 1 min |
| Reject product | Approve Products → Reject | 1 min |
| View seller stats | Manage Sellers | Instant |
| Check pending products | Sidebar badge | Instant |

### Seller Tasks:

| Task | Path | Time |
|------|------|------|
| Add new product | My Products → Add New Product | 5 min |
| View product status | My Products | Instant |
| Update order status | My Orders → Edit | 2 min |
| Export products | My Products → Export CSV | Instant |
| Check revenue | Dashboard | Instant |

---

## 💡 Tips & Best Practices

### For Sellers:

1. **Product Photos:**
   - Use clear, high-quality images
   - 800x800px recommended
   - Show product from multiple angles

2. **Product Descriptions:**
   - Be detailed and accurate
   - Include key features
   - Mention dimensions/specifications

3. **Pricing:**
   - Keep selling price below MRP
   - Be competitive
   - Consider shipping costs

4. **Stock Management:**
   - Keep stock updated
   - Mark as inactive if out of stock
   - Update quantity regularly

5. **Order Fulfillment:**
   - Update status promptly
   - Add tracking IDs when shipping
   - Communicate with customers

### For Admin:

1. **Seller Onboarding:**
   - Collect complete business details
   - Verify GST/PAN documents
   - Set appropriate commission rate
   - Explain approval process

2. **Product Approval:**
   - Review within 24 hours
   - Give specific rejection reasons
   - Maintain quality standards
   - Be consistent with criteria

3. **Monitoring:**
   - Check pending products daily
   - Review seller performance monthly
   - Address quality issues promptly
   - Support good sellers

---

## 🆘 Common Questions

### For Sellers:

**Q: Why is my product not visible on the website?**
A: Check approval status in "My Products". If showing "Pending", wait for admin approval. If "Rejected", fix the issues mentioned and re-submit.

**Q: Can I use admin categories?**
A: Yes! Admin categories are available for you to use. You'll see them in gray with "Admin Category" badge. You cannot edit them, but you can assign your products to them.

**Q: How do I know when my product is approved?**
A: Check "My Products" page - status will change from "Pending" to "Approved". The product will also appear on the website.

**Q: Can I delete products?**
A: Only if admin has given you delete permission. Otherwise, you can deactivate products instead.

**Q: When do I get paid?**
A: Contact admin for payment schedules. Your earnings are shown in the dashboard (Revenue minus Commission).

### For Admin:

**Q: How do I know when sellers add products?**
A: The "Approve Products" menu item shows a red badge with the count of pending products.

**Q: Can sellers update order status?**
A: Yes, sellers can update order status for orders containing their products.

**Q: What if I want to remove a seller?**
A: You can deactivate the seller account from "Manage Sellers". Their products will remain but they cannot login.

**Q: Can multiple sellers sell the same product?**
A: Yes, each seller manages their own products independently.

---

## 📞 Support

For technical issues or questions:
- **Admin:** Check documentation files
- **Sellers:** Contact platform admin
- **Both:** Refer to this user guide

---

**Version:** 1.0  
**Last Updated:** October 2025  
**Platform:** EverythingB2C Multi-Vendor System
