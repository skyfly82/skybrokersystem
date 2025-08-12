<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Courier Configuration
    |--------------------------------------------------------------------------
    */
    
    'default_timeout' => env('COURIER_DEFAULT_TIMEOUT', 30),
    'default_retry_attempts' => env('COURIER_DEFAULT_RETRY_ATTEMPTS', 3),
    'default_retry_delay' => env('COURIER_DEFAULT_RETRY_DELAY', 5),
    
    /*
    |--------------------------------------------------------------------------
    | Courier Providers Configuration
    |--------------------------------------------------------------------------
    */
    
    'providers' => [
        'inpost' => [
            'name' => 'InPost',
            'enabled' => env('INPOST_ENABLED', true),
            'api_url' => env('INPOST_API_URL', 'https://api-shipx-pl.easypack24.net'),
            'token' => env('INPOST_TOKEN'),
            'organization_id' => env('INPOST_ORGANIZATION_ID'),
            'sandbox' => env('INPOST_SANDBOX', true),
            'timeout' => env('INPOST_TIMEOUT', 30),
            'services' => [
                'inpost_locker_standard' => 'Paczkomat Standard',
                'inpost_locker_express' => 'Paczkomat Express',
                'inpost_courier_standard' => 'Kurier Standard',
                'inpost_courier_express' => 'Kurier Express',
            ],
        ],
        
        'dhl' => [
            'name' => 'DHL',
            'enabled' => env('DHL_ENABLED', false),
            'api_url' => env('DHL_API_URL', 'https://api-test.dhl.com'),
            'api_key' => env('DHL_API_KEY'),
            'api_secret' => env('DHL_API_SECRET'),
            'sandbox' => env('DHL_SANDBOX', true),
            'timeout' => env('DHL_TIMEOUT', 30),
            'services' => [
                'dhl_domestic' => 'DHL Krajowa',
                'dhl_express' => 'DHL Express',
                'dhl_international' => 'DHL Międzynarodowa',
            ],
        ],
        
        'dpd' => [
            'name' => 'DPD',
            'enabled' => env('DPD_ENABLED', false),
            'api_url' => env('DPD_API_URL'),
            'username' => env('DPD_USERNAME'),
            'password' => env('DPD_PASSWORD'),
            'sandbox' => env('DPD_SANDBOX', true),
            'timeout' => env('DPD_TIMEOUT', 30),
            'services' => [
                'dpd_classic' => 'DPD Classic',
                'dpd_express' => 'DPD Express',
                'dpd_pickup' => 'DPD Pickup',
            ],
        ],
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Pricing Configuration
    |--------------------------------------------------------------------------
    */
    
    'pricing' => [
        'markup_percentage' => env('COURIER_MARKUP_PERCENTAGE', 15.0),
        'minimum_margin' => env('COURIER_MINIMUM_MARGIN', 2.00),
        'vat_rate' => env('COURIER_VAT_RATE', 23),
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Webhooks Configuration
    |--------------------------------------------------------------------------
    */
    
    'webhooks' => [
        'inpost' => [
            'url' => env('APP_URL') . '/api/webhooks/inpost',
            'secret' => env('INPOST_WEBHOOK_SECRET'),
        ],
        'dhl' => [
            'url' => env('APP_URL') . '/api/webhooks/dhl',
            'secret' => env('DHL_WEBHOOK_SECRET'),
        ],
        'dpd' => [
            'url' => env('APP_URL') . '/api/webhooks/dpd',
            'secret' => env('DPD_WEBHOOK_SECRET'),
        ],
    ],
];
