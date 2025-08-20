<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Mailer
    |--------------------------------------------------------------------------
    */
    
    'default' => env('MAIL_MAILER', 'smtp'),
    
    /*
    |--------------------------------------------------------------------------
    | Mailer Configurations
    |--------------------------------------------------------------------------
    */
    
    'mailers' => [
        'smtp' => [
            'transport' => 'smtp',
            'host' => env('MAIL_HOST', 'mailpit'),
            'port' => env('MAIL_PORT', 1025),
            'encryption' => env('MAIL_ENCRYPTION', 'tls'),
            'username' => env('MAIL_USERNAME'),
            'password' => env('MAIL_PASSWORD'),
            'timeout' => null,
            'local_domain' => env('MAIL_EHLO_DOMAIN'),
        ],
        
        'mailpit' => [
            'transport' => 'smtp',
            'host' => env('MAILPIT_HOST', 'mailpit'),
            'port' => env('MAILPIT_PORT', 1025),
            'encryption' => null,
            'username' => null,
            'password' => null,
        ],
        
        'ses' => [
            'transport' => 'ses',
        ],
        
        'mailgun' => [
            'transport' => 'mailgun',
            'client' => [
                'timeout' => 5,
            ],
        ],
        
        'postmark' => [
            'transport' => 'postmark',
            'message_stream_id' => null,
            'client' => [
                'timeout' => 5,
            ],
        ],
        
        'sendmail' => [
            'transport' => 'sendmail',
            'path' => env('MAIL_SENDMAIL_PATH', '/usr/sbin/sendmail -bs -i'),
        ],
        
        'log' => [
            'transport' => 'log',
            'channel' => env('MAIL_LOG_CHANNEL'),
        ],
        
        'array' => [
            'transport' => 'array',
        ],
        
        'failover' => [
            'transport' => 'failover',
            'mailers' => [
                'smtp',
                'log',
            ],
        ],
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Global "From" Address
    |--------------------------------------------------------------------------
    */
    
    'from' => [
        'address' => env('MAIL_FROM_ADDRESS', 'noreply@skybrokersystem.com'),
        'name' => env('MAIL_FROM_NAME', 'SkyBrokerSystem'),
    ],
    
    /*
    |--------------------------------------------------------------------------
    | SkyBrokerSystem Email Configuration
    |--------------------------------------------------------------------------
    */
    
    'skybrokersystem' => [
        'admin_email' => env('ADMIN_EMAIL', 'admin@skybrokersystem.com'),
        'support_email' => env('SUPPORT_EMAIL', 'support@skybrokersystem.com'),
        'noreply_email' => env('NOREPLY_EMAIL', 'noreply@skybrokersystem.com'),
        
        'templates' => [
            'customer' => [
                'shipment_created' => 'emails.customer.shipment-created',
                'shipment_delivered' => 'emails.customer.shipment-delivered',
                'payment_completed' => 'emails.customer.payment-completed',
                'payment_failed' => 'emails.customer.payment-failed',
                'account_approved' => 'emails.customer.account-approved',
                'low_balance' => 'emails.customer.low-balance',
            ],
            'admin' => [
                'customer_registered' => 'emails.admin.customer-registered',
                'daily_report' => 'emails.admin.daily-report',
                'system_alert' => 'emails.admin.system-alert',
            ],
        ],
        
        'queue' => [
            'enabled' => env('MAIL_QUEUE_ENABLED', true),
            'connection' => env('MAIL_QUEUE_CONNECTION', 'redis'),
            'queue' => env('MAIL_QUEUE_NAME', 'emails'),
        ],
    ],
];