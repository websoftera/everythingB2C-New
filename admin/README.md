# Demo-site Demo-site

A comprehensive Demo-site for managing the Demo-site e-commerce website. This Demo-site provides complete control over products, categories, orders, users, and analytics.

## Features

### 🏠 Dashboard
- Overview statistics (products, categories, users, orders)
- Recent orders display
- Low stock product alerts
- Quick access to all management functions

### 📦 Product Management
- Add, edit, and delete products
- Bulk operations (activate, deactivate, delete)
- Product image management
- Category assignment
- Stock management
- Featured and discounted product flags
- Search and filter functionality

### 🏷️ Category Management
- Create, edit, and delete categories
- Category slug generation
- Product count tracking
- Category image management

### 🛒 Order Management
- View all orders with detailed information
- Order status updates (pending, confirmed, shipped, delivered, cancelled)
- Order items display
- Customer information
- Order printing functionality
- Advanced filtering and search

### 👥 User Management
- View all registered users
- User status management (activate/deactivate)
- User order history
- User address management
- User statistics and analytics

### 📊 Reports & Analytics
- Sales analytics with charts
- Top selling products
- Sales by category
- Date range filtering
- Export functionality
- Revenue tracking

### ⚙️ Settings
- Admin profile management
- Password change functionality
- Site settings configuration
- System information display

## Installation

### 1. Database Setup
First, run the admin schema to create the admin table and indexes:

```sql
-- Run the admin schema
SOURCE database/admin_schema.sql;
```

### 2. Default Admin Account
The system comes with a default admin account:
- **Email:** admin@Demo-site.com
- **Password:** admin123

**Important:** Change the default password immediately after first login!

### 3. File Structure
Ensure the following directory structure exists:
```
admin/
├── index.php              # Dashboard
├── login.php              # Admin login
├── logout.php             # Logout
├── products.php           # Product management
├── add_product.php        # Add new product
├── edit_product.php       # Edit product
├── categories.php         # Category management
├── orders.php             # Order management
├── users.php              # User management
├── reports.php            # Reports & analytics
├── settings.php           # Settings
├── includes/
│   ├── sidebar.php        # Navigation sidebar
│   └── header.php         # Admin header
├── assets/
│   ├── css/
│   │   └── admin.css      # Admin styles
│   └── js/
│       └── admin.js       # Admin JavaScript
└── ajax/
    ├── get_order_items.php
    └── get_user_details.php
```

### 4. Upload Directory
Create an uploads directory for product images:
```bash
mkdir uploads
mkdir uploads/products
chmod 755 uploads
chmod 755 uploads/products
```

## Usage

### Accessing the Demo-site
1. Navigate to `your-domain.com/admin/`
2. Login with the default credentials
3. Change the default password in Settings

### Managing Products
1. Go to **Products** in the sidebar
2. Use the search and filter options to find products
3. Click **Add New Product** to create a new product
4. Use the action buttons to edit, view, or delete products
5. Use bulk actions for multiple products

### Managing Orders
1. Go to **Orders** in the sidebar
2. View order details by clicking the eye icon
3. Update order status using the dropdown
4. Print orders using the print icon
5. Filter orders by date, status, or search terms

### Managing Users
1. Go to **Users** in the sidebar
2. View user details by clicking the eye icon
3. Activate/deactivate users as needed
4. Delete users (only if they have no orders)

### Viewing Reports
1. Go to **Reports** in the sidebar
2. Select date range for analytics
3. View sales charts and statistics
4. Export reports as needed

## Security Features

- **Session-based authentication**
- **Password hashing** using PHP's password_hash()
- **SQL injection protection** with prepared statements
- **XSS protection** with htmlspecialchars()
- **CSRF protection** (implement in forms)
- **Role-based access control** (admin roles)

## Customization

### Adding New Admin Users
```sql
INSERT INTO admins (name, email, password, role) VALUES 
('Admin Name', 'admin@example.com', '$2y$10$hashedpassword', 'admin');
```

### Modifying Admin Roles
The system supports three roles:
- `super_admin` - Full access
- `admin` - Standard admin access
- `manager` - Limited access (implement restrictions)

### Customizing Styles
Edit `assets/css/admin.css` to customize the Demo-site appearance.

### Adding New Features
1. Create new PHP files in the admin directory
2. Add navigation links in `includes/sidebar.php`
3. Follow the existing code structure and patterns
4. Implement proper security checks

## Troubleshooting

### Common Issues

**1. Admin login not working**
- Check database connection
- Verify admin table exists
- Ensure password is properly hashed

**2. Images not uploading**
- Check uploads directory permissions
- Verify PHP upload settings
- Check file size limits

**3. Charts not displaying**
- Ensure Chart.js is loaded
- Check browser console for JavaScript errors
- Verify data is being passed correctly

**4. AJAX requests failing**
- Check file paths
- Verify session is active
- Check browser network tab for errors

### Performance Optimization

1. **Database Indexes**: Ensure all necessary indexes are created
2. **Image Optimization**: Compress product images before upload
3. **Caching**: Implement caching for frequently accessed data
4. **Pagination**: Use pagination for large datasets

## Support

For support and questions:
1. Check the troubleshooting section
2. Review the code comments
3. Check browser console for errors
4. Verify database connectivity

## Version History

- **v1.0.0** - Initial release with basic CRUD operations
- Features: Dashboard, Product Management, Order Management, User Management, Reports

## License

This Demo-site is part of the Demo-site e-commerce system.

---

**Note:** Always keep your admin credentials secure and regularly update passwords. Consider implementing additional security measures like two-factor authentication for production use. 