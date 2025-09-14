# DTDC API Credentials Setup Guide

## Current Configuration Status

✅ **You have been provided with:**
- **Tracking API Credentials** (for live tracking)
- **Customer Portal Access** (for account management)

## Your Current Credentials

### Tracking API (Already Configured)
```
Username: PL3537_trk_json
Password: wafBo
Token: bbb8196c734d8487983936199e880072
```

### Customer Portal Access
```
URL: https://customer.dtdc.in/login
Customer Code: PL3537
Password: Abc@123456
```

## How to Get Complete API Configuration

### Step 1: Login to DTDC Customer Portal

1. **Go to**: https://customer.dtdc.in/login
2. **Login with**:
   - Customer Code: `PL3537`
   - Password: `Abc@123456`

### Step 2: Navigate to API Section

Once logged in, look for these sections in the portal:

1. **API Documentation** or **Developer Section**
2. **API Credentials** or **Integration Settings**
3. **Webhook Configuration**
4. **Service Codes** or **Product Codes**

### Step 3: Get Complete API Details

From the customer portal, you need to find:

#### A. API Endpoints
```
Base URL: https://apis.dtdc.in/apis/v1 (already updated)
```

#### B. Service Codes
```
Product Codes (for different services):
- DOM (Domestic)
- SURFACE (Surface)
- AIR (Air)
- EXPRESS (Express)

Service Types:
- PPD (Prepaid)
- COD (Cash on Delivery)
```

#### C. Additional API Credentials
You may need separate credentials for:
- **Order Creation API**
- **Label Generation API**
- **Pincode Check API**
- **Order Cancellation API**

### Step 4: Test API Endpoints

Use the provided tracking credentials to test the tracking API:

#### Test Tracking API
```bash
curl -X POST https://apis.dtdc.in/apis/v1/tracking \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer bbb8196c734d8487983936199e880072" \
  -d '{
    "tracking_id": "YOUR_TEST_TRACKING_ID",
    "username": "PL3537_trk_json",
    "password": "wafBo",
    "token": "bbb8196c734d8487983936199e880072"
  }'
```

### Step 5: Contact DTDC Support

If you cannot find complete API details in the portal:

1. **Email DTDC Support**: support@dtdc.in
2. **Phone**: 1800-123-4567
3. **Request**:
   - Complete API documentation
   - All API endpoints for your account
   - Service codes and product codes
   - Webhook configuration details
   - Sample API requests and responses

### Step 6: Update Configuration

Once you have complete details, update these files:

#### A. Update `config/dtdc_config.php`
```php
'api' => [
    'base_url' => 'https://apis.dtdc.in/apis/v1',
    'username' => 'PL3537_trk_json',
    'password' => 'wafBo',
    'api_key' => 'bbb8196c734d8487983936199e880072',
    
    // Update these with actual endpoints from DTDC
    'endpoints' => [
        'tracking' => '/tracking',
        'pincode_search' => '/pincode/check',
        'order_upload' => '/order/create',
        'order_cancel' => '/order/cancel',
        'shipping_label' => '/label/generate'
    ],
],

// Update service codes
'defaults' => [
    'service_type' => 'SURFACE', // or AIR, EXPRESS
    'payment_mode' => 'PPD', // or COD
    'product_code' => 'DOM',
    'sub_product_code' => 'SURFACE'
],
```

#### B. Test the Integration
```bash
php test_dtdc_integration.php
```

## What You Need to Find in Customer Portal

### 1. API Documentation Section
- Complete API endpoint URLs
- Request/response formats
- Authentication methods
- Error codes and messages

### 2. Service Configuration
- Available service types (Surface, Air, Express)
- Product codes for different services
- Payment modes (PPD, COD)
- Weight limits and pricing

### 3. Account Settings
- Pickup address configuration
- Billing settings
- Service area restrictions
- Rate cards

### 4. Integration Settings
- Webhook URLs for status updates
- API rate limits
- Testing vs production environments
- Sandbox credentials (if available)

## Testing Your Current Setup

### Quick Test
1. **Run the test script**:
   ```bash
   php test_dtdc_integration.php
   ```

2. **Check if tracking works**:
   - Go to your admin panel
   - Find an order with a DTDC tracking ID
   - Click "View DTDC Tracking"

### Expected Issues
Since you only have tracking API credentials, you might face issues with:
- ❌ Order creation (needs separate credentials)
- ❌ Label generation (needs separate credentials)
- ❌ Pincode checking (needs separate credentials)
- ✅ Tracking should work (you have these credentials)

## Next Steps

1. **Login to DTDC Customer Portal** and explore the API section
2. **Contact DTDC Support** for complete API documentation
3. **Test tracking functionality** with your current credentials
4. **Request additional API access** for order creation and label generation
5. **Update configuration** once you have complete details

## Support Contacts

**DTDC Technical Support**:
- Email: support@dtdc.in
- Phone: 1800-123-4567
- Portal: https://customer.dtdc.in/login

**Your Account Details**:
- Customer Code: PL3537
- Username: PL3537_trk_json
- Portal Password: Abc@123456

## Current Status

✅ **Tracking API**: Configured and ready to test  
⏳ **Order Creation API**: Needs additional credentials  
⏳ **Label Generation API**: Needs additional credentials  
⏳ **Pincode Check API**: Needs additional credentials  

The tracking functionality should work with your current credentials. For full integration, you'll need to get the complete API documentation from DTDC.
