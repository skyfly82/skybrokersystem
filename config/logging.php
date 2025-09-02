<?php

return [
    // ... existing Laravel config ...

    'channels' => [
        // ... existing channels ...

        'sms' => [
            'driver' => 'single',
            'path' => storage_path('logs/sms.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'days' => 14,
        ],

        'payments' => [
            'driver' => 'single',
            'path' => storage_path('logs/payments.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'days' => 30,
        ],

        'couriers' => [
            'driver' => 'single',
            'path' => storage_path('logs/couriers.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'days' => 14,
        ],

        'webhooks' => [
            'driver' => 'single',
            'path' => storage_path('logs/webhooks.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'days' => 30,
        ],

        'api' => [
            'driver' => 'single',
            'path' => storage_path('logs/api.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'days' => 7,
        ],

        'security' => [
            'driver' => 'single',
            'path' => storage_path('logs/security.log'),
            'level' => 'warning',
            'days' => 90,
        ],
    ],
];
