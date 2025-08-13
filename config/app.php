<?php

return [
    // ... existing Laravel config ...
    
    /*
    |--------------------------------------------------------------------------
    | SkyBrokerSystem Configuration
    |--------------------------------------------------------------------------
    */
    
    'skybrokersystem' => [
        'version' => env('APP_VERSION', '6.0.0'),
        'name' => 'SkyBrokerSystem',
        'description' => 'System brokerski przesyłek kurierskich',
        'company' => [
            'name' => 'SkyBrokerSystem Sp. z o.o.',
            'address' => 'ul. Przykładowa 1, 00-000 Warszawa',
            'nip' => '1234567890',
            'phone' => '+48 123 456 789',
            'email' => 'kontakt@skybrokersystem.com',
            'website' => 'https://skybrokersystem.com',
        ],
        'support' => [
            'email' => env('SUPPORT_EMAIL', 'support@skybrokersystem.com'),
            'phone' => '+48 123 456 789',
            'hours' => 'Pon-Pt 8:00-18:00',
        ],
        'features' => [
            'advanced_reporting' => env('FEATURE_ADVANCED_REPORTING', true),
            'bulk_operations' => env('FEATURE_BULK_OPERATIONS', true),
            'api_v2' => env('FEATURE_API_V2', false),
            'multi_currency' => env('FEATURE_MULTI_CURRENCY', false),
        ],
        'limits' => [
            'max_shipments_per_day' => 1000,
            'max_users_per_customer' => 50,
            'max_api_calls_per_hour' => 1000,
            'max_file_upload_size' => '10MB',
        ],
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Additional Providers
    |--------------------------------------------------------------------------
    */
    
    'providers' => [
        // ... existing providers ...
        
        // SkyBrokerSystem Providers
       // App\Providers\CourierServiceProvider::class,
        //App\Providers\PaymentServiceProvider::class,
       // App\Providers\NotificationServiceProvider::class,
       // App\Providers\SmsServiceProvider::class,
    ],
];