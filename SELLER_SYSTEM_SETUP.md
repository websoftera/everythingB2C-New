# Seller/Vendor Management System - Complete Setup Guide

## Overview
This comprehensive seller system allows EverythingB2C to support multiple sellers/vendors. Each seller can manage their own products, categories, orders, and view reports, while admin maintains full control over all sellers and their data.

## Features Implemented

### ✅ Admin Features
- Convert any customer user to a seller
- Manage seller accounts and permissions
- Approve/reject seller products before they go live
- View all seller products, categories, and orders
- Monitor seller performance and statistics
- Set commission rates per seller
- Activate/deactivate seller accounts

### ✅ Seller Features
- Manage own products (add, edit, view)
- Manage own categories
- View orders containing their products
- View sales reports and statistics
- Update own settings
- Product approval workflow (products need admin approval)

### ✅ Product Approval System
- Seller products require admin approval
- Admin can approve or reject with reason
- Approval history tracking
- Email notifications (future enhancement)

## Database Structure

### New Tables Created
1. **sellers** - Seller business information
2. **seller_permissions** - Granular permissions per seller
3. **seller_statistics** - Cached seller stats for quick access
4. **seller_activity_log** - Audit trail of seller actions
5. **seller_product_approval_history** - Product approval tracking

### Modified Tables
1. **users** - Added `user_role`, `is_seller_approved` fields
2. **products** - Added `seller_id`, `is_approved`, `approved_at` fields
3. **categories** - Added `seller_id`, `is_approved` fields
4. **orders** - Added `seller_id` field

## Installation Steps

### 1. Run Database Schema
```bash
# Execute the seller system schema
mysql -u root -p your_database < database/seller_system_schema.sql
```

This will:
- Add necessary columns to existing tables
- Create new seller-related tables
- Set up indexes for performance
- Create helpful views

### 2. Update Admin User Role
```sql
-- Make your admin user an admin (replace 1 with your admin user ID)
UPDATE users SET user_role = 'admin' WHERE id = 1;
```

### 3. Add Files to Your Project

#### Backend Files Created:
- `includes/seller_functions.php` - Core seller functions
- `database/seller_system_schema.sql` - Database schema

#### Admin Files to Create:
- `admin/manage_sellers.php` - Main seller management (CREATED)
- `admin/seller_details.php` - View/edit seller details (TO CREATE)
- `admin/seller_products.php` - View seller products (TO CREATE)
- `admin/seller_orders.php` - View seller orders (TO CREATE)
- `admin/approve_products.php` - Approve pending products (TO CREATE)

#### Seller Dashboard Files to Create:
- `seller/index.php` - Seller dashboard
- `seller/products.php` - Seller product management
- `seller/add_product.php` - Add new product
- `seller/edit_product.php` - Edit product
- `seller/categories.php` - Manage categories
- `seller/orders.php` - View orders
- `seller/reports.php` - View reports
- `seller/settings.php` - Seller settings
- `seller/login.php` - Seller login page
- `seller/includes/header.php` - Seller header
- `seller/includes/sidebar.php` - Seller sidebar
- `seller/includes/footer.php` - Seller footer

### 4. Update Existing Files

#### Add to `admin/includes/sidebar.php`:
```php
<li class="nav-item">
    <a class="nav-link" href="manage_sellers.php">
        <i class="fas fa-users"></i>
        <span>Manage Sellers</span>
    </a>
</li>
<li class="nav-item">
    <a class="nav-link" href="approve_products.php">
        <i class="fas fa-check-circle"></i>
        <span>Approve Products</span>
        <?php
        // Show count of pending products
        $stmt = $pdo->query("SELECT COUNT(*) FROM products WHERE is_approved = 0 AND seller_id IS NOT NULL");
        $pendingCount = $stmt->fetchColumn();
        if ($pendingCount > 0) {
            echo "<span class='badge badge-danger badge-counter'>{$pendingCount}</span>";
        }
        ?>
    </a>
</li>
<li class="nav-item">
    <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseSellers">
        <i class="fas fa-store"></i>
        <span>Sellers Overview</span>
    </a>
    <div id="collapseSellers" class="collapse">
        <div class="bg-white py-2 collapse-inner rounded">
            <a class="collapse-item" href="seller_products.php">All Seller Products</a>
            <a class="collapse-item" href="seller_orders.php">All Seller Orders</a>
            <a class="collapse-item" href="seller_reports.php">Seller Reports</a>
        </div>
    </div>
</li>
```

#### Update `admin/login.php`:
```php
// After successful login, check user role
$stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
$stmt->execute([$username]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user && $user['user_role'] === 'admin') {
    $_SESSION['admin_logged_in'] = true;
    $_SESSION['admin_id'] = $user['id'];
    $_SESSION['admin_name'] = $user['first_name'];
    header('Location: index.php');
    exit;
}
```

#### Update main site `products.php`:
```php
// Only show approved products on frontend
$sql = "SELECT * FROM products WHERE status = 'active' AND is_approved = 1";
```

## User Roles and Permissions

### Admin Role
- Full access to all features
- Can create, edit, delete anything
- Manage sellers
- Approve/reject seller products
- View all seller data

### Seller Role
- Limited to own data only
- Products management (with approval workflow)
- Categories management
- View orders containing their products
- View own reports and statistics
- Cannot delete products (configurable)
- Cannot see other sellers' data

### Customer Role
- Regular shopping functionality
- No admin access

## Seller Permissions

Granular permissions that admin can configure per seller:

| Permission | Description | Default |
|-----------|-------------|---------|
| can_manage_products | View/edit products | Yes |
| can_manage_categories | Manage categories | Yes |
| can_view_orders | View orders | Yes |
| can_view_reports | View reports | Yes |
| can_update_settings | Update profile | Yes |
| can_add_products | Add new products | Yes |
| can_edit_products | Edit existing products | Yes |
| can_delete_products | Delete products | No |
| max_products | Maximum product limit | 100 |

## Product Approval Workflow

### For Sellers:
1. Seller adds/edits a product
2. Product is saved with `is_approved = 0`
3. Product NOT visible on frontend
4. Seller sees "Pending Approval" status
5. Seller waits for admin approval

### For Admin:
1. Admin sees pending products count in sidebar
2. Admin goes to "Approve Products" page
3. Admin reviews product details
4. Admin approves or rejects with reason
5. Product becomes visible (if approved)
6. Rejection reason shown to seller

## API Endpoints / AJAX Handlers

### Admin AJAX:
- `admin/ajax/approve_product.php` - Approve a product
- `admin/ajax/reject_product.php` - Reject a product
- `admin/ajax/update_seller_permissions.php` - Update permissions
- `admin/ajax/get_seller_stats.php` - Get seller statistics

### Seller AJAX:
- `seller/ajax/save_product.php` - Save product (pending approval)
- `seller/ajax/update_product.php` - Update product (requires re-approval)
- `seller/ajax/get_order_items.php` - Get order items for seller

## Security Considerations

### Authentication
- Separate login for sellers
- Role-based access control
- Session management

### Authorization
- Check seller_id on all queries
- Verify permissions before actions
- Prevent cross-seller data access

### Data Validation
- Validate all inputs
- Sanitize data before storage
- Check file uploads for products

## Commission System

### How It Works:
1. Admin sets commission percentage per seller
2. Commission calculated on order total
3. Tracked in `seller_statistics` table
4. Fields:
   - `total_revenue` - Total sales
   - `commission_paid` - Already paid commission
   - `pending_commission` - Unpaid commission

### Commission Calculation:
```php
$commission_amount = ($total_revenue * $commission_percentage) / 100;
```

## Reports and Analytics

### Seller Dashboard Shows:
- Total products (active, pending, rejected)
- Total orders
- Total revenue
- Pending commission
- Recent orders
- Top selling products
- Sales by month/category

### Admin Dashboard Shows:
- All sellers overview
- Total seller products
- Total seller orders
- Commission summary
- Pending approvals
- Seller performance comparison

## Email Notifications (Future Enhancement)

Recommended notifications:
1. Seller account created
2. Product approved
3. Product rejected (with reason)
4. New order with seller products
5. Order status updates
6. Commission payments

## Testing Checklist

### Admin Testing:
- [ ] Create a new seller from customer user
- [ ] Edit seller details
- [ ] Activate/deactivate seller
- [ ] Set seller permissions
- [ ] Approve seller product
- [ ] Reject seller product
- [ ] View seller orders
- [ ] View seller statistics

### Seller Testing:
- [ ] Login as seller
- [ ] View dashboard
- [ ] Add new product (check pending status)
- [ ] Edit existing product
- [ ] View orders
- [ ] View reports
- [ ] Update settings

### Frontend Testing:
- [ ] Seller products not visible until approved
- [ ] Approved products visible normally
- [ ] Orders work with seller products
- [ ] Cart works with mixed products (admin + seller)

## Troubleshooting

### Common Issues:

**1. Seller products not showing on frontend**
- Check `is_approved = 1` in product query
- Verify `status = 'active'`
- Check seller account is active

**2. Permission errors**
- Verify seller_permissions table has entry
- Check user_role is 'seller'
- Verify is_seller_approved is 1

**3. Orders not showing for seller**
- Check product has correct seller_id
- Verify order_items linked properly
- Check SQL joins in getSellerOrders()

## Future Enhancements

1. **Multi-vendor shipping** - Separate shipping per seller
2. **Seller subscriptions** - Monthly/yearly seller plans
3. **Advanced commission** - Category-based commission rates
4. **Seller messaging** - Admin to seller communication
5. **Product reviews** - Seller-specific reviews
6. **Seller ratings** - Customer ratings for sellers
7. **Bulk operations** - Bulk approve/reject products
8. **Advanced reports** - More detailed analytics
9. **Seller payout** - Automated commission payouts
10. **Mobile app** - Seller mobile application

## Support and Maintenance

### Regular Maintenance:
1. Update seller statistics daily
2. Archive old seller activity logs
3. Monitor pending approvals
4. Review seller performance
5. Check for inactive sellers

### Database Optimization:
```sql
-- Optimize seller tables monthly
OPTIMIZE TABLE sellers, seller_statistics, seller_product_approval_history;

-- Update all seller statistics
-- Run this via cron job daily
CALL update_all_seller_statistics();
```

## Contact and Support

For questions or issues with the seller system:
- Check documentation first
- Review error logs
- Test with sample data
- Contact developer if needed

---

**Version:** 1.0.0  
**Last Updated:** January 2025  
**Compatibility:** EverythingB2C Platform
