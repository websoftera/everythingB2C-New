# EverythingB2C - Dynamic E-commerce Website

This is a dynamic e-commerce website built with PHP and MySQL, converted from a static HTML website.

## Features

- **Dynamic Product Management**: Products are fetched from database
- **Category Management**: Categories with product counts
- **User Authentication**: Login/Register system
- **Shopping Cart**: Add/remove items, update quantities
- **Wishlist**: Save products for later
- **Search Functionality**: Search products by name or description
- **Responsive Design**: Works on desktop and mobile
- **Product Details**: Detailed product pages with images
- **Related Products**: Show related products on product pages

## Database Structure

The website uses the following main tables:
- `categories` - Product categories
- `products` - Product information
- `product_images` - Multiple images per product
- `users` - User accounts
- `cart` - Shopping cart items
- `wishlist` - User wishlist items
- `orders` - Order information
- `order_items` - Order line items

## Setup Instructions

### 1. Database Setup

1. Create a MySQL database named `EverythingB2C`
2. Import the database schema from `database/schema.sql`
3. Update database credentials in `config/database.php` if needed

### 2. File Structure

Ensure your files are organized as follows:
```
EverythingB2C-New/
├── config/
│   └── database.php
├── includes/
│   ├── functions.php
│   ├── header.php
│   └── footer.php
├── ajax/
│   ├── add-to-cart.php
│   ├── add-to-wishlist.php
│   ├── remove-from-cart.php
│   ├── remove-from-wishlist.php
│   └── update-cart.php
├── database/
│   └── schema.sql
├── index.php
├── product.php
├── category.php
├── search.php
├── cart.php
├── wishlist.php
├── login.php
├── register.php
├── logout.php
└── [existing static files]
```

### 3. Server Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache/Nginx web server
- PDO extension enabled

### 4. Configuration

1. Update database connection details in `config/database.php`:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'your_username');
define('DB_PASS', 'your_password');
define('DB_NAME', 'EverythingB2C');
```

2. Ensure your web server is configured to serve PHP files

### 5. Testing

1. Access the website through your web server
2. Register a new account
3. Browse products and categories
4. Add items to cart and wishlist
5. Test search functionality

## Key Files Explained

### Core Files
- `index.php` - Dynamic homepage with featured and discounted products
- `product.php` - Individual product detail pages
- `category.php` - Category listing pages
- `search.php` - Product search results

### User Management
- `login.php` - User login
- `register.php` - User registration
- `logout.php` - User logout

### Shopping Features
- `cart.php` - Shopping cart management
- `wishlist.php` - Wishlist management

### AJAX Handlers
- `ajax/add-to-cart.php` - Add products to cart
- `ajax/add-to-wishlist.php` - Add products to wishlist
- `ajax/remove-from-cart.php` - Remove items from cart
- `ajax/remove-from-wishlist.php` - Remove items from wishlist
- `ajax/update-cart.php` - Update cart quantities

### Database Functions
- `includes/functions.php` - All database helper functions
- `config/database.php` - Database connection configuration

## Adding New Products

To add new products to the database:

1. Insert into `products` table with required fields
2. Add product images to `product_images` table
3. Update category product counts

Example SQL:
```sql
INSERT INTO products (name, slug, description, mrp, selling_price, category_id, main_image) 
VALUES ('Product Name', 'product-slug', 'Description', 100.00, 80.00, 1, 'path/to/image.jpg');
```

## Customization

- Modify CSS files in `asset/style/` for styling changes
- Update product images in the `asset/` directory
- Add new categories by inserting into the `categories` table
- Customize the header and footer in `includes/header.php` and `includes/footer.php`

## Security Features

- Password hashing using PHP's `password_hash()`
- SQL injection prevention with prepared statements
- Input sanitization
- Session-based authentication
- CSRF protection (basic)

## Browser Compatibility

- Chrome (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)
- Mobile browsers

## Support

For issues or questions, please check:
1. Database connection settings
2. File permissions
3. PHP error logs
4. Browser console for JavaScript errors

## License

This project is for educational and commercial use. 