<?php
/**
 * DTDC API Configuration
 * 
 * This file contains the configuration for DTDC API integration.
 * Update these values with your actual DTDC API credentials.
 */

return [
    // DTDC API Configuration
    'api' => [
        // Base URL for DTDC API - Official production endpoint
        'base_url' => 'https://blktracksvc.dtdc.com/dtdc-api',
        
        // API Authentication - Updated with your exact credentials
        'username' => 'PL3537_trk_json',
        'password' => 'wafBo',
        'api_key' => 'bbb8196c734d8487983936199e880072',
        'token' => '', // Will be obtained dynamically
        'customer_code' => 'PL3537',
        'customer_password' => 'Abc@123456',
        
        // API Endpoints - Official DTDC API endpoints from documentation
        'endpoints' => [
            'authenticate' => '/api/dtdc/authenticate',
            'tracking' => '/rest/JSONCnTrk/getTrackDetails',
            'tracking_xml' => '/rest/XMLCnTrk/getDetails',
            'pincode_search' => '/pincode/check',
            'order_upload' => '/order/create',
            'order_cancel' => '/order/cancel',
            'shipping_label' => '/label/generate'
        ],
        
        // API Settings
        'timeout' => 30, // seconds
        'retry_attempts' => 3,
        'retry_delay' => 2, // seconds
    ],
    
    // DTDC Service Settings
    'service' => [
        'enabled' => true, // Set to false to disable DTDC integration
        'auto_tracking' => true, // Automatically fetch tracking updates
        'cache_duration' => 300, // Cache tracking data for 5 minutes
        'webhook_enabled' => false, // Enable webhook for real-time updates
        'webhook_secret' => 'your_webhook_secret_key'
    ],
    
    // Order Status Mapping
    'status_mapping' => [
        'PICKED_UP' => 'Processing',
        'IN_TRANSIT' => 'In Transit', 
        'OUT_FOR_DELIVERY' => 'Out for Delivery',
        'DELIVERED' => 'Delivered',
        'FAILED_DELIVERY' => 'Failed Delivery',
        'RETURNED' => 'Returned',
        'CANCELLED' => 'Canceled'
    ],
    
    // Default Settings
    'defaults' => [
        'service_type' => 'SURFACE',
        'payment_mode' => 'PPD', // Prepaid
        'product_code' => 'DOM',
        'sub_product_code' => 'SURFACE'
    ]
];
