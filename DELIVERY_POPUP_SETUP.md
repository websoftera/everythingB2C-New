# Delivery Availability Popup Setup Guide

## Overview
This functionality adds a delivery availability popup to the homepage that allows users to check if their pincode is serviceable. The popup appears once per browser session and can be managed through the admin panel.

## Features
- ✅ Popup appears on homepage only
- ✅ Session-based display (shows once per browser session)
- ✅ Admin can manage serviceable pincodes
- ✅ Admin can customize popup messages
- ✅ Responsive design for all devices
- ✅ Real-time pincode validation
- ✅ AJAX-based functionality

## Database Setup

### 1. Run the SQL Schema
Execute the following SQL file to create the required tables:
```sql
-- Run this file: database/delivery_popup_schema.sql
```

This will create:
- `serviceable_pincodes` table for storing serviceable pincodes
- `popup_settings` table for storing popup configuration

### 2. Default Settings
The schema includes default popup settings:
- Popup enabled: Yes
- Message: "We Deliver Orders In Maharashtra, Gujarat, Bangalore, And Hyderabad Only."
- Instruction: "Please Enter Your Pincode To Check Delivery Availability."
- Success message: "Great! We deliver to your area."
- Error message: "We are not providing service to this area."

## Files Created/Modified

### New Files:
1. `database/delivery_popup_schema.sql` - Database schema
2. `includes/delivery_popup_functions.php` - PHP functions
3. `ajax/check_pincode.php` - AJAX handler for pincode checking
4. `ajax/mark_popup_shown.php` - AJAX handler for marking popup as shown
5. `admin/manage_pincodes.php` - Admin panel for managing pincodes

### Modified Files:
1. `index.php` - Added popup HTML, CSS, and JavaScript
2. `admin/includes/sidebar.php` - Added pincode management link

## Admin Panel Usage

### Accessing Pincode Management
1. Login to admin panel
2. Navigate to "Manage Pincodes" in the sidebar
3. Add serviceable pincodes (comma-separated)
4. Customize popup messages
5. Enable/disable individual pincodes

### Adding Pincodes
- Enter pincodes separated by commas (e.g., 411001, 411002, 411003)
- Only 6-digit pincodes are accepted
- Invalid pincodes will show error messages

### Popup Settings
- **Enable/Disable Popup**: Toggle popup visibility
- **Popup Message**: Main message shown to users
- **Instruction Text**: Text prompting user to enter pincode
- **Service Available Message**: Message shown when pincode is serviceable
- **Service Unavailable Message**: Message shown when pincode is not serviceable

## User Experience

### Popup Behavior
1. Popup appears when user visits homepage for the first time in a session
2. User can enter their 6-digit pincode
3. System checks if pincode is serviceable
4. Shows appropriate success/error message
5. User can close popup or start shopping
6. Popup won't appear again in the same session

### Responsive Design
- **Desktop**: Full popup with horizontal layout
- **Tablet**: Adjusted spacing and font sizes
- **Mobile**: Vertical layout for better usability

## Technical Details

### Session Management
- Uses PHP sessions to track if popup has been shown
- Session persists until browser is closed
- Popup reappears when user opens website in new session

### AJAX Endpoints
- `ajax/check_pincode.php` - Validates pincode and returns result
- `ajax/mark_popup_shown.php` - Marks popup as shown in session

### Security Features
- Input validation for pincode format
- SQL injection prevention with prepared statements
- XSS prevention with htmlspecialchars
- CSRF protection through session validation

## Customization

### Styling
The popup styles are included in the `index.php` file. You can modify:
- Colors and fonts
- Popup size and positioning
- Button styles and hover effects
- Responsive breakpoints

### Functionality
Modify the JavaScript functions in `index.php` to:
- Change popup behavior
- Add additional validation
- Customize user interactions

## Troubleshooting

### Common Issues
1. **Popup not appearing**: Check if popup is enabled in admin settings
2. **Pincode not working**: Verify pincode is added to serviceable list
3. **Session issues**: Clear browser cache and cookies
4. **AJAX errors**: Check browser console for network errors

### Debug Mode
Add this to check popup status:
```php
// Add to index.php for debugging
echo "<!-- Debug: Show popup = " . ($showPopup ? 'true' : 'false') . " -->";
echo "<!-- Debug: Popup enabled = " . ($popupSettings['popup_enabled'] ?? 'not set') . " -->";
```

## Support
For issues or questions, check:
1. Browser console for JavaScript errors
2. PHP error logs for server-side issues
3. Database connection and table structure
4. File permissions and paths
