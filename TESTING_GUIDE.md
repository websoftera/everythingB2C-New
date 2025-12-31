# Testing Guide - Product Rejection System Fix

## Pre-Test Setup

1. Ensure you have test seller and admin accounts
2. Have a browser with developer tools open (for checking database)
3. Prepare test product data

---

## Test Case 1: Seller Edits Rejected Product

### Steps:
1. **Login as Admin**
   - Navigate to `/admin/approve_products.php`

2. **View Pending Products**
   - Verify you see pending seller products

3. **Reject a Product**
   - Click on a product
   - Scroll to rejection section
   - Enter rejection reason: "Product description is too vague"
   - Click "Reject Product"

4. **Verify Admin Dashboard**
   - Go to `/admin/products.php`
   - Find the rejected product
   - **Verify:** 
     - Approval column shows red "❌ Rejected" badge
     - Reason preview visible: "Product description..."
     - Seller name visible in new Seller column

5. **Verify Seller Dashboard**
   - Login as the seller
   - Go to `/seller/index.php`
   - **Verify:**
     - Rejected products count shows 1
     - Red alert box displays with rejection reason
     - "Edit & Resubmit" button visible for product

6. **Seller Edits Product**
   - Click "Edit & Resubmit" button
   - **Verify:**
     - Red alert shows: "Product Rejected! - Rejection Reason: ..."
     - All product fields pre-filled
     - Image displays
     - Button says "Update & Resubmit for Approval"

7. **Make Changes & Resubmit**
   - Edit description field (make it more detailed)
   - Click "Update & Resubmit for Approval"
   - **Verify:** Success message shows "Product updated and resubmitted for approval!"

8. **Verify Product Back to Pending**
   - Admin dashboard: Product now shows "⏳ Pending" not "❌ Rejected"
   - Rejection reason should be GONE (cleared)
   - Seller dashboard: Red alert disappears, pending count incremented

---

## Test Case 2: Seller Name in Invoice

### Steps:
1. **Create Multi-Seller Order**
   - Add 2-3 products from different sellers to cart
   - Checkout and complete payment

2. **View Invoice**
   - Go to My Account > Orders
   - Click "Download Invoice"
   - **Verify PDF shows:**
     - Each product row includes Seller column
     - Different sellers visible if multiple sellers in order
     - Seller name clearly labeled

3. **Print Invoice**
   - Print to PDF or paper
   - **Verify:** Packing team can clearly see which seller owns which product

4. **Alternative: View Order HTML**
   - View HTML source before PDF generation
   - Check table includes seller name column

---

## Test Case 3: Seller Name in My Account

### Steps:
1. **Login as Customer**
   - Navigate to `/myaccount.php`
   - Click on "My Orders" tab

2. **View Order Details**
   - Click on an order to expand
   - **Verify:**
     - Product table has "Seller" column
     - Each product shows its seller business name
     - "EverythingB2C" shows for admin products

3. **Multiple Sellers in Order**
   - View order with products from multiple sellers
   - **Verify:** Each product shows correct seller name

---

## Test Case 4: Admin Dashboard Status Display

### Steps:
1. **Login as Admin**
   - Go to `/admin/products.php`

2. **View Products Table**
   - **Verify new columns exist:**
     - Seller column (shows business name)
     - Approval column (shows status)

3. **Check Approval Statuses**
   - Approved products: Green "✅ Approved" badge
   - Rejected products: Red "❌ Rejected" with reason preview
   - Pending products: Yellow "⏳ Pending"

4. **Hover Over Rejection Reason**
   - Hover over reason preview
   - **Verify:** Full reason visible in tooltip

5. **Test Seller Products View**
   - Go to `/admin/seller_products.php`
   - Select a seller
   - **Verify:**
     - Approval column shows correct status
     - Rejection reasons visible
     - Seller contact info displayed

6. **Test Rejection Filter**
   - Filter dropdown: Select "Rejected"
   - **Verify:** Only rejected products display
   - Change to "Pending"
   - **Verify:** Only pending products display (not rejected)

---

## Test Case 5: Admin Seller Products Page

### Steps:
1. **Login as Admin**
   - Go to `/admin/seller_products.php`

2. **Test Filters**
   - Filter by Seller
   - Filter by Approval Status:
     - "All Products" - shows everything
     - "Approved Only" - shows only approved
     - "Pending Approval" - shows only pending (not rejected!)
     - "Rejected Only" - shows only rejected

3. **Verify Rejection Display**
   - For rejected products, verify:
     - Red badge with "❌ Rejected"
     - Rejection reason visible (preview)
     - Full reason on hover

---

## Test Case 6: Rejected Products Flow

### Complete Flow Test:
```
Step 1: Seller submits product "Test Item"
  └─> Database: is_approved=0, rejection_reason=NULL

Step 2: Admin goes to approve_products.php
  └─> Sees "Test Item" in Pending list

Step 3: Admin clicks Reject with reason "Needs better images"
  └─> Database: is_approved=0, rejection_reason="Needs better images"
  └─> Activity log: product_rejected entry created

Step 4: Seller dashboard shows rejection alert
  └─> "Products Rejected: 1"
  └─> Red alert with product name and reason
  └─> "Edit & Resubmit" button visible

Step 5: Seller clicks Edit & Resubmit
  └─> Form opens with rejection reason displayed
  └─> All fields pre-populated
  └─> Button text: "Update & Resubmit for Approval"

Step 6: Seller updates description and images, submits
  └─> Database: is_approved=0, rejection_reason=NULL (cleared!)
  └─> Activity log: product_resubmitted entry created

Step 7: Admin dashboard shows "Pending" again
  └─> Not "Rejected" anymore
  └─> Ready for re-review

Step 8: Admin can approve or reject again
  └─> Cycle repeats if needed
```

---

## Test Case 7: Data Integrity

### Verify No Data Loss:
1. **Check Database Directly**
   ```sql
   SELECT id, name, seller_id, is_approved, rejection_reason 
   FROM products 
   WHERE id = [test_product_id];
   ```
   - Verify seller_id preserved
   - Verify rejection_reason stored correctly
   - Verify is_approved changed appropriately

2. **Check Invoice Generation**
   - Download invoice for multi-seller order
   - Verify all products appear
   - Verify seller names correct
   - Verify amounts correct

3. **Check Activity Log**
   - Verify product_rejected entries created
   - Verify product_resubmitted entries created
   - Verify seller_id linked to entries

---

## Test Case 8: Edge Cases

### Edge Case 1: Admin Product (No Seller)
- Create product as admin (seller_id = NULL)
- **Verify:** Invoice shows "EverythingB2C" for seller name
- No errors in database queries

### Edge Case 2: Seller Profile Missing
- Create product with seller_id pointing to deleted/inactive seller
- **Verify:** Invoice shows "EverythingB2C" (default)
- No NULL values displayed

### Edge Case 3: Bulk Operations
- Select multiple products including rejected ones
- **Verify:** Can still perform bulk actions
- Rejection status not affected by bulk operations

### Edge Case 4: Re-reject After Resubmit
- Reject product
- Seller resubmits
- Admin rejects again with different reason
- **Verify:**
  - New rejection reason overwrites old one
  - Both entries in approval history
  - Seller sees new reason on dashboard

---

## Verification Checklist

### Seller Functionality:
- [ ] Can view rejection reason on dashboard
- [ ] Can click "Edit & Resubmit"
- [ ] Form pre-fills all product data
- [ ] Can upload new images
- [ ] Can modify all fields
- [ ] Can resubmit successfully
- [ ] Rejection reason cleared after resubmit
- [ ] Back to "Pending" status after resubmit

### Admin Functionality:
- [ ] Approval status shows correctly (Approved/Rejected/Pending)
- [ ] Rejection reason displayed
- [ ] Can filter by rejection status
- [ ] Seller name visible in products table
- [ ] Can see both pending and rejected products
- [ ] Rejection history preserved

### Display Functionality:
- [ ] Seller name in invoice PDF
- [ ] Seller name in My Account orders
- [ ] Seller name in order management
- [ ] No broken links or missing data
- [ ] Proper formatting in all displays

### Database Integrity:
- [ ] rejection_reason stored correctly
- [ ] is_approved values correct
- [ ] seller_id preserved
- [ ] No orphaned records
- [ ] Activity log entries created

---

## Quick Test Commands (for developers)

### Check Rejected Products:
```sql
SELECT * FROM products WHERE is_approved = 0 AND rejection_reason IS NOT NULL;
```

### Check Pending Products:
```sql
SELECT * FROM products WHERE is_approved = 0 AND rejection_reason IS NULL;
```

### Check Approval History:
```sql
SELECT * FROM seller_product_approval_history 
WHERE product_id = [id] 
ORDER BY action_date DESC;
```

### Check Activity Log:
```sql
SELECT * FROM seller_activity_log 
WHERE activity_type IN ('product_rejected', 'product_resubmitted')
ORDER BY created_at DESC;
```

---

## Browser Console Debugging

### Check Network Requests:
1. Open DevTools (F12)
2. Go to Network tab
3. Submit product/rejection
4. Verify 200 status code on POST requests

### Check Local Data:
1. Open DevTools (F12)
2. Go to Application/Storage tab
3. Check Session Storage for seller_id
4. Verify user role preserved

---

## Performance Testing

### Load Testing:
- Load 100+ rejected products
- Verify dashboard alert displays quickly
- Verify filter operations < 1 second

### Concurrent Testing:
- Admin rejecting product while seller views dashboard
- Multiple sellers resubmitting simultaneously
- Verify no data conflicts

---

## Success Criteria

✅ **All tests pass** when:
1. Sellers can edit and resubmit rejected products
2. Rejection status clearly distinguishes from pending
3. Admin can easily filter and view rejected products
4. Seller names display correctly in all places
5. No database errors or data loss
6. No broken functionality
7. All backward compatible

---

**Testing Date:** ___________
**Tested By:** ___________
**Status:** ⏳ Pending / ✅ Passed / ❌ Failed

**Notes:**
_____________________________________________________________________
_____________________________________________________________________

