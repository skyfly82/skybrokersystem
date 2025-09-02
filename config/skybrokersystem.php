<?php

return [

    /*
    |--------------------------------------------------------------------------
    | SkyBrokerSystem General Configuration
    |--------------------------------------------------------------------------
    |
    | General system configuration including version, build information,
    | and core system settings.
    |
    */

    'version' => env('APP_VERSION', '6.0.0'),
    'build' => env('APP_BUILD', 'dev'),
    'release_date' => '2024-01-15',
    'environment' => env('APP_ENV', 'local'),

    /*
    |--------------------------------------------------------------------------
    | System Settings
    |--------------------------------------------------------------------------
    |
    | Core system settings that control various aspects of the application
    | behavior and functionality.
    |
    */

    'settings' => [
        'default_timezone' => 'Europe/Warsaw',
        'default_currency' => 'PLN',
        'default_country' => 'PL',
        'date_format' => 'Y-m-d',
        'datetime_format' => 'Y-m-d H:i:s',
        'pagination_per_page' => 20,
        'max_upload_size' => 10, // MB
        'session_lifetime' => 120, // minutes
    ],

    /*
    |--------------------------------------------------------------------------
    | Customer Configuration
    |--------------------------------------------------------------------------
    |
    | Settings related to customer accounts, registration, and management.
    |
    */

    'customers' => [
        'auto_approval' => env('CUSTOMERS_AUTO_APPROVAL', false),
        'require_verification' => env('CUSTOMERS_REQUIRE_VERIFICATION', true),
        'default_credit_limit' => env('CUSTOMERS_DEFAULT_CREDIT_LIMIT', 0.00),
        'low_balance_threshold' => env('CUSTOMERS_LOW_BALANCE_THRESHOLD', 100.00),
        'api_key_prefix' => 'sk_',
        'api_key_length' => 48,
        'registration' => [
            'enabled' => env('CUSTOMER_REGISTRATION_ENABLED', true),
            'require_nip' => env('CUSTOMER_REGISTRATION_REQUIRE_NIP', true),
            'notification_email' => env('ADMIN_NOTIFICATION_EMAIL', 'admin@example.com'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Payment Configuration
    |--------------------------------------------------------------------------
    |
    | Payment system configuration including providers, limits, and settings.
    |
    */

    'payments' => [
        'enabled_providers' => [
            'simulation' => env('PAYMENT_SIMULATION_ENABLED', true),
            'paynow' => env('PAYMENT_PAYNOW_ENABLED', false),
            'stripe' => env('PAYMENT_STRIPE_ENABLED', false),
        ],
        'default_provider' => env('PAYMENT_DEFAULT_PROVIDER', 'simulation'),
        'currency' => 'PLN',
        'min_topup_amount' => env('PAYMENT_MIN_TOPUP', 10.00),
        'max_topup_amount' => env('PAYMENT_MAX_TOPUP', 10000.00),
        'payment_timeout' => 24, // hours
        'auto_complete_simulation_under' => 100.00, // PLN
        'vat_rate' => 0.23, // 23% VAT
    ],

    /*
    |--------------------------------------------------------------------------
    | Courier Services Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for integrated courier services and their settings.
    |
    */

    'couriers' => [
        'enabled_services' => [
            'inpost' => env('COURIER_INPOST_ENABLED', true),
            'dhl' => env('COURIER_DHL_ENABLED', false),
            'dpd' => env('COURIER_DPD_ENABLED', false),
            'gls' => env('COURIER_GLS_ENABLED', false),
            'meest' => env('COURIER_MEEST_ENABLED', false),
            'fedex' => env('COURIER_FEDEX_ENABLED', false),
            'ambro' => env('COURIER_AMBRO_ENABLED', false),
            'packeta' => env('COURIER_PACKETA_ENABLED', false),
        ],
        'default_service' => env('COURIER_DEFAULT_SERVICE', 'inpost'),
        'label_format' => env('COURIER_LABEL_FORMAT', 'pdf'),
        'label_size' => env('COURIER_LABEL_SIZE', 'A4'),
        'available_formats' => [
            'pdf' => [
                'name' => 'PDF',
                'sizes' => ['A4', 'A6'],
                'mime_type' => 'application/pdf',
                'extension' => 'pdf',
            ],
            'zpl' => [
                'name' => 'ZPL',
                'sizes' => [],
                'mime_type' => 'text/plain',
                'extension' => 'zpl',
            ],
        ],
        'tracking_update_frequency' => 60, // minutes
        'webhook_timeout' => 30, // seconds
        'api_timeout' => 60, // seconds
        'retry_attempts' => 3,
    ],

    /*
    |--------------------------------------------------------------------------
    | Notification Configuration
    |--------------------------------------------------------------------------
    |
    | Settings for email and SMS notifications.
    |
    */

    'notifications' => [
        'enabled_channels' => [
            'email' => env('NOTIFICATIONS_EMAIL_ENABLED', true),
            'sms' => env('NOTIFICATIONS_SMS_ENABLED', true),
            'database' => env('NOTIFICATIONS_DATABASE_ENABLED', true),
        ],
        'queue_notifications' => env('NOTIFICATIONS_QUEUE_ENABLED', true),
        'retry_failed_after' => 60, // minutes
        'max_retry_attempts' => 3,
        'templates' => [
            'cache_enabled' => true,
            'cache_ttl' => 3600, // seconds
        ],
        'default_from_email' => env('MAIL_FROM_ADDRESS', 'noreply@skybrokersystem.com'),
        'default_from_name' => env('MAIL_FROM_NAME', 'SkyBrokerSystem'),
        'sms' => [
            'sender_name' => env('SMS_SENDER_NAME', 'SkyBroker'),
            'max_length' => 160,
            'development_mode' => env('SMS_DEVELOPMENT_MODE', true),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | API Configuration
    |--------------------------------------------------------------------------
    |
    | REST API settings including rate limiting and authentication.
    |
    */

    'api' => [
        'version' => 'v1',
        'rate_limit' => [
            'enabled' => env('API_RATE_LIMIT_ENABLED', true),
            'requests_per_hour' => env('API_RATE_LIMIT_PER_HOUR', 1000),
            'requests_per_minute' => env('API_RATE_LIMIT_PER_MINUTE', 60),
        ],
        'authentication' => [
            'api_key_header' => 'X-API-Key',
            'token_ttl' => 3600, // seconds
        ],
        'response_format' => [
            'include_meta' => true,
            'include_timestamps' => true,
            'pretty_print' => env('API_PRETTY_PRINT', false),
        ],
        'documentation' => [
            'enabled' => env('API_DOCS_ENABLED', true),
            'url' => '/api/documentation',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Admin Panel Configuration
    |--------------------------------------------------------------------------
    |
    | Settings for the admin panel interface and functionality.
    |
    */

    'admin' => [
        'dashboard' => [
            'refresh_interval' => 30, // seconds
            'chart_days' => 30,
            'recent_items_count' => 10,
        ],
        'pagination' => [
            'per_page' => 25,
            'max_per_page' => 100,
        ],
        'exports' => [
            'enabled' => env('ADMIN_EXPORTS_ENABLED', true),
            'max_records' => 10000,
            'formats' => ['csv', 'xlsx', 'pdf'],
        ],
        'activity_log' => [
            'enabled' => env('ADMIN_ACTIVITY_LOG_ENABLED', true),
            'retention_days' => 90,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Customer Panel Configuration
    |--------------------------------------------------------------------------
    |
    | Settings for the customer panel interface.
    |
    */

    'customer_panel' => [
        'dashboard' => [
            'show_balance' => true,
            'show_statistics' => true,
            'chart_days' => 30,
        ],
        'shipments' => [
            'per_page' => 20,
            'allow_create' => true,
            'allow_cancel' => true,
            'show_pricing' => true,
        ],
        'payments' => [
            'show_history' => true,
            'allow_topup' => true,
            'show_invoices' => true,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Security Configuration
    |--------------------------------------------------------------------------
    |
    | Security-related settings for the application.
    |
    */

    'security' => [
        'password_requirements' => [
            'min_length' => 8,
            'require_uppercase' => true,
            'require_lowercase' => true,
            'require_numbers' => true,
            'require_symbols' => false,
        ],
        'session' => [
            'secure_cookies' => env('SESSION_SECURE_COOKIES', false),
            'same_site' => env('SESSION_SAME_SITE', 'lax'),
        ],
        'api_security' => [
            'require_https' => env('API_REQUIRE_HTTPS', false),
            'allowed_origins' => env('API_ALLOWED_ORIGINS', '*'),
            'webhook_signature_validation' => true,
        ],
        'file_uploads' => [
            'allowed_types' => ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'jpg', 'png'],
            'max_size' => 10240, // KB
            'virus_scan' => env('FILE_VIRUS_SCAN_ENABLED', false),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Caching Configuration
    |--------------------------------------------------------------------------
    |
    | Cache settings for various system components.
    |
    */

    'cache' => [
        'courier_services' => [
            'ttl' => 3600, // 1 hour
            'key_prefix' => 'courier_services',
        ],
        'pickup_points' => [
            'ttl' => 1800, // 30 minutes
            'key_prefix' => 'pickup_points',
        ],
        'price_calculations' => [
            'ttl' => 900, // 15 minutes
            'key_prefix' => 'price_calc',
        ],
        'tracking_data' => [
            'ttl' => 300, // 5 minutes
            'key_prefix' => 'tracking',
        ],
        'customer_stats' => [
            'ttl' => 600, // 10 minutes
            'key_prefix' => 'customer_stats',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Logging Configuration
    |--------------------------------------------------------------------------
    |
    | Logging settings for different system components.
    |
    */

    'logging' => [
        'channels' => [
            'courier_api' => 'courier',
            'payment_api' => 'payments',
            'notifications' => 'notifications',
            'webhooks' => 'webhooks',
        ],
        'levels' => [
            'courier_errors' => 'error',
            'payment_errors' => 'error',
            'api_requests' => env('LOG_API_REQUESTS', false) ? 'info' : 'debug',
            'webhooks' => 'info',
        ],
        'retention' => [
            'days' => env('LOG_RETENTION_DAYS', 30),
            'compress_after' => 7, // days
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Feature Flags
    |--------------------------------------------------------------------------
    |
    | Feature toggles for enabling/disabling functionality.
    |
    */

    'features' => [
        'customer_registration' => env('FEATURE_CUSTOMER_REGISTRATION', true),
        'api_access' => env('FEATURE_API_ACCESS', true),
        'sms_notifications' => env('FEATURE_SMS_NOTIFICATIONS', true),
        'email_notifications' => env('FEATURE_EMAIL_NOTIFICATIONS', true),
        'payment_processing' => env('FEATURE_PAYMENT_PROCESSING', true),
        'courier_integration' => env('FEATURE_COURIER_INTEGRATION', true),
        'webhook_processing' => env('FEATURE_WEBHOOK_PROCESSING', true),
        'bulk_operations' => env('FEATURE_BULK_OPERATIONS', true),
        'advanced_reporting' => env('FEATURE_ADVANCED_REPORTING', true),
        'multi_language' => env('FEATURE_MULTI_LANGUAGE', false),
        'maintenance_mode' => env('FEATURE_MAINTENANCE_MODE', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | Integration Settings
    |--------------------------------------------------------------------------
    |
    | Settings for third-party integrations and external services.
    |
    */

    'integrations' => [
        'analytics' => [
            'google_analytics_id' => env('GOOGLE_ANALYTICS_ID'),
            'hotjar_id' => env('HOTJAR_ID'),
        ],
        'monitoring' => [
            'sentry_dsn' => env('SENTRY_LARAVEL_DSN'),
            'newrelic_enabled' => env('NEW_RELIC_ENABLED', false),
        ],
        'external_apis' => [
            'timeout' => 30, // seconds
            'retry_attempts' => 3,
            'retry_delay' => 1000, // milliseconds
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Development & Testing
    |--------------------------------------------------------------------------
    |
    | Settings for development and testing environments.
    |
    */

    'development' => [
        'debug_toolbar' => env('APP_DEBUG', false),
        'query_logging' => env('DB_QUERY_LOGGING', false),
        'api_documentation' => env('API_DOCS_ENABLED', true),
        'test_data' => [
            'create_seeders' => env('CREATE_TEST_DATA', false),
            'customer_count' => 10,
            'shipment_count' => 100,
        ],
        'simulation' => [
            'payment_delay' => 2, // seconds
            'courier_delay' => 1, // seconds
            'webhook_delay' => 5, // seconds
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Backup & Maintenance
    |--------------------------------------------------------------------------
    |
    | Settings for system backup and maintenance procedures.
    |
    */

    'maintenance' => [
        'backup' => [
            'enabled' => env('BACKUP_ENABLED', true),
            'schedule' => env('BACKUP_SCHEDULE', 'daily'),
            'retention_days' => env('BACKUP_RETENTION_DAYS', 30),
            'include_uploads' => true,
        ],
        'cleanup' => [
            'old_notifications' => 90, // days
            'old_logs' => 30, // days
            'old_sessions' => 7, // days
            'temp_files' => 1, // days
        ],
        'health_checks' => [
            'enabled' => env('HEALTH_CHECKS_ENABLED', true),
            'interval' => 300, // seconds (5 minutes)
            'endpoints' => [
                'database',
                'cache',
                'storage',
                'courier_apis',
                'payment_apis',
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Localization
    |--------------------------------------------------------------------------
    |
    | Localization and multi-language support settings.
    |
    */

    'localization' => [
        'default_locale' => 'pl',
        'available_locales' => ['pl', 'en'],
        'fallback_locale' => 'en',
        'auto_detect' => env('AUTO_DETECT_LOCALE', false),
        'currency_formats' => [
            'PLN' => ['symbol' => 'zł', 'position' => 'after', 'decimals' => 2],
            'EUR' => ['symbol' => '€', 'position' => 'before', 'decimals' => 2],
            'USD' => ['symbol' => '$', 'position' => 'before', 'decimals' => 2],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Performance & Optimization
    |--------------------------------------------------------------------------
    |
    | Performance and optimization settings.
    |
    */

    'performance' => [
        'cache_views' => env('CACHE_VIEWS', true),
        'cache_routes' => env('CACHE_ROUTES', true),
        'cache_config' => env('CACHE_CONFIG', true),
        'optimize_images' => env('OPTIMIZE_IMAGES', true),
        'compress_responses' => env('COMPRESS_RESPONSES', true),
        'lazy_loading' => env('LAZY_LOADING', true),
        'cdn' => [
            'enabled' => env('CDN_ENABLED', false),
            'url' => env('CDN_URL'),
        ],
        'database' => [
            'query_cache' => env('DB_QUERY_CACHE', true),
            'connection_pooling' => env('DB_CONNECTION_POOLING', false),
        ],
    ],

];
