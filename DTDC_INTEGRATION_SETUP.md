# DTDC API Integration Setup Guide

This guide will help you set up the DTDC API integration for live tracking in your EverythingB2C platform.

## Overview

The DTDC integration provides:
- **Live Tracking**: Real-time shipment tracking using DTDC API
- **Order Management**: Create DTDC orders directly from admin panel
- **Shipping Labels**: Generate shipping labels for DTDC shipments
- **Automatic Status Updates**: Sync DTDC tracking events with your order status system

## Prerequisites

1. **DTDC API Credentials**: You need to obtain API credentials from DTDC
2. **Database Access**: Ensure your database supports the new DTDC tables
3. **PHP cURL**: Required for API communication
4. **File Permissions**: Ensure the cache directory is writable

## Installation Steps

### 1. Database Setup

Run the following SQL script to create the necessary tables:

```sql
-- Execute the DTDC tracking schema
SOURCE database/dtdc_tracking_schema.sql;
```

Or manually run the SQL commands from `database/dtdc_tracking_schema.sql`.

### 2. Configuration Setup

1. **Update DTDC Configuration**:
   - Open `config/dtdc_config.php`
   - Update the following with your actual DTDC API credentials:
     ```php
     'username' => 'your_dtdc_username',
     'password' => 'your_dtdc_password',
     'api_key' => 'your_dtdc_api_key',
     'base_url' => 'https://api.dtdc.com/api/v1', // Update with actual DTDC API URL
     ```

2. **Enable DTDC Service**:
   ```php
   'service' => [
       'enabled' => true, // Set to true to enable DTDC integration
       'auto_tracking' => true,
       'cache_duration' => 300, // 5 minutes cache
   ],
   ```

### 3. File Permissions

Create and set permissions for the cache directory:

```bash
mkdir -p cache
chmod 755 cache
```

### 4. Test the Integration

1. **Test API Connection**:
   - Go to Admin Panel → Orders
   - Find an order and click "Create DTDC Order" button
   - Check if the order is created successfully in DTDC

2. **Test Tracking**:
   - After creating a DTDC order, click "View DTDC Tracking"
   - Verify that tracking events are displayed correctly

## Usage Guide

### For Administrators

#### Creating DTDC Orders

1. Go to **Admin Panel → Orders**
2. Find the order you want to ship
3. Click the **"Create DTDC Order"** button
4. The system will:
   - Create an order in DTDC system
   - Generate a DTDC tracking ID
   - Update your order with DTDC information

#### Managing DTDC Orders

Once a DTDC order is created, you'll see additional buttons:

- **🔄 Refresh DTDC Tracking**: Updates tracking data from DTDC
- **🚚 View DTDC Tracking**: Shows detailed tracking events
- **🏷️ Generate Shipping Label**: Creates a shipping label

#### Monitoring API Calls

All DTDC API calls are logged in the `dtdc_api_logs` table for debugging and monitoring.

### For Customers

#### Live Tracking Experience

When customers track their orders:

1. **DTDC Enabled Orders**: Show live tracking with real-time updates
2. **Regular Orders**: Show admin-managed status updates
3. **Hybrid Approach**: DTDC tracking events are integrated with your order status timeline

#### Features Available to Customers

- **Real-time Status Updates**: Live tracking from DTDC
- **Location Tracking**: Current shipment location
- **Delivery Estimates**: Estimated delivery dates
- **Event Timeline**: Complete tracking history

## Configuration Options

### DTDC API Settings

```php
'api' => [
    'base_url' => 'https://api.dtdc.com/api/v1',
    'timeout' => 30, // seconds
    'retry_attempts' => 3,
    'retry_delay' => 2, // seconds
],
```

### Service Settings

```php
'service' => [
    'enabled' => true, // Enable/disable DTDC integration
    'auto_tracking' => true, // Automatically fetch updates
    'cache_duration' => 300, // Cache duration in seconds
],
```

### Status Mapping

```php
'status_mapping' => [
    'PICKED_UP' => 'Processing',
    'IN_TRANSIT' => 'In Transit', 
    'OUT_FOR_DELIVERY' => 'Out for Delivery',
    'DELIVERED' => 'Delivered',
    'FAILED_DELIVERY' => 'Failed Delivery',
    'RETURNED' => 'Returned',
    'CANCELLED' => 'Canceled'
],
```

## Troubleshooting

### Common Issues

1. **API Connection Failed**
   - Check your DTDC API credentials
   - Verify the API base URL is correct
   - Ensure your server can make outbound HTTPS requests

2. **Tracking Data Not Loading**
   - Check if the DTDC tracking ID exists
   - Verify the order has been created in DTDC system
   - Check the `dtdc_api_logs` table for error messages

3. **Cache Issues**
   - Ensure the `cache` directory is writable
   - Clear cache files if needed: `rm cache/dtdc_*.json`

### Debug Mode

Enable debug logging by checking the `dtdc_api_logs` table:

```sql
SELECT * FROM dtdc_api_logs ORDER BY created_at DESC LIMIT 10;
```

### API Response Issues

If you're getting unexpected API responses:

1. Check the actual DTDC API documentation
2. Update the `prepareOrderData()` method in `includes/dtdc_api.php`
3. Modify the `parseTrackingResponse()` method for your API response format

## Security Considerations

1. **API Credentials**: Keep your DTDC API credentials secure
2. **HTTPS Only**: Ensure all API calls use HTTPS
3. **Input Validation**: All user inputs are validated before API calls
4. **Rate Limiting**: Consider implementing rate limiting for API calls

## Performance Optimization

1. **Caching**: Tracking data is cached for 5 minutes to reduce API calls
2. **Batch Operations**: Consider batching multiple API calls when possible
3. **Error Handling**: Graceful fallback to admin-managed statuses if DTDC is unavailable

## Support

For issues with this integration:

1. Check the error logs in `dtdc_api_logs` table
2. Verify your DTDC API credentials and endpoints
3. Ensure all database tables are created correctly
4. Check file permissions for the cache directory

## API Documentation Reference

Based on the DTDC API documents you provided, the integration supports:

- **Tracking API**: Real-time shipment tracking
- **Order Upload API**: Creating new shipments
- **Order Cancellation API**: Canceling shipments
- **Shipping Label API**: Generating shipping labels
- **Pincode Search API**: Checking serviceable areas

Update the API endpoints and data formats in `config/dtdc_config.php` and `includes/dtdc_api.php` according to the actual DTDC API documentation you received.
