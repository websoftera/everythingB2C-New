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
        // Base URL for DTDC API - Updated with actual DTDC API endpoint
        'base_url' => 'https://apis.dtdc.in/apis',
        
        // API Authentication - Updated with your exact credentials
        'username' => 'PL3537_trk_json',
        'password' => 'wafBo',
        'api_key' => 'bbb8196c734d8487983936199e880072',
        
        // API Endpoints - Updated with actual DTDC API endpoints
        'endpoints' => [
            'tracking' => '/tracking',
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
