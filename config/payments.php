<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Payment Provider
    |--------------------------------------------------------------------------
    |
    | This option controls the default payment provider that will be used
    | when no specific provider is specified in payment requests.
    |
    */

    'default_provider' => env('PAYMENT_DEFAULT_PROVIDER', 'simulation'),
    'default_card_provider' => env('PAYMENT_DEFAULT_CARD_PROVIDER', 'paynow'),
    'default_bank_provider' => env('PAYMENT_DEFAULT_BANK_PROVIDER', 'paynow'),

    /*
    |--------------------------------------------------------------------------
    | Payment Providers Configuration
    |--------------------------------------------------------------------------
    */

    'providers' => [

        'simulation' => [
            'name' => 'Simulation Provider',
            'description' => 'Provider for testing and development',
            'enabled' => env('PAYMENT_SIMULATION_ENABLED', true),
            'auto_complete_threshold' => 100.00, // Auto-complete payments under this amount
            'delay_seconds' => 5, // Simulate processing delay
        ],

        'paynow' => [
            'name' => 'PayNow',
            'description' => 'PayNow payment gateway',
            'enabled' => env('PAYNOW_ENABLED', false),
            'sandbox' => env('PAYNOW_SANDBOX', true),
            'api_url' => env('PAYNOW_API_URL', 'https://api.paynow.pl'),
            'sandbox_api_url' => env('PAYNOW_SANDBOX_API_URL', 'https://api.sandbox.paynow.pl'),
            'api_key' => env('PAYNOW_API_KEY'),
            'signature_key' => env('PAYNOW_SIGNATURE_KEY'),
            'supported_methods' => [
                'card' => 'Karta płatnicza',
                'blik' => 'BLIK',
                'bank_transfer' => 'Przelew bankowy',
                'apple_pay' => 'Apple Pay',
                'google_pay' => 'Google Pay',
            ],
            'currencies' => ['PLN', 'EUR', 'USD'],
            'min_amount' => 1.00,
            'max_amount' => 100000.00,
            'fee_percentage' => 1.9, // 1.9% commission
            'fee_fixed' => 0.25, // 0.25 PLN fixed fee
        ],

        'stripe' => [
            'name' => 'Stripe',
            'description' => 'Stripe payment processor',
            'enabled' => env('STRIPE_ENABLED', false),
            'sandbox' => env('STRIPE_SANDBOX', true),
            'publishable_key' => env('STRIPE_PUBLISHABLE_KEY'),
            'secret_key' => env('STRIPE_SECRET_KEY'),
            'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),
            'supported_methods' => [
                'card' => 'Credit/Debit Card',
                'paypal' => 'PayPal',
                'sepa_debit' => 'SEPA Direct Debit',
                'ideal' => 'iDEAL',
                'p24' => 'Przelewy24',
            ],
            'currencies' => ['PLN', 'EUR', 'USD', 'GBP'],
            'min_amount' => 0.50,
            'max_amount' => 999999.99,
            'fee_percentage' => 2.9, // 2.9% commission
            'fee_fixed' => 0.30, // 0.30 EUR fixed fee
        ],

        'przelewy24' => [
            'name' => 'Przelewy24',
            'description' => 'Polish payment gateway',
            'enabled' => env('P24_ENABLED', false),
            'sandbox' => env('P24_SANDBOX', true),
            'api_url' => env('P24_API_URL', 'https://secure.przelewy24.pl'),
            'sandbox_api_url' => env('P24_SANDBOX_API_URL', 'https://sandbox.przelewy24.pl'),
            'merchant_id' => env('P24_MERCHANT_ID'),
            'pos_id' => env('P24_POS_ID'),
            'crc' => env('P24_CRC'),
            'api_key' => env('P24_API_KEY'),
            'supported_methods' => [
                'card' => 'Karta płatnicza',
                'blik' => 'BLIK',
                'bank_transfer' => 'Przelew bankowy',
                'paypal' => 'PayPal',
            ],
            'currencies' => ['PLN', 'EUR'],
            'min_amount' => 1.00,
            'max_amount' => 50000.00,
            'fee_percentage' => 1.9,
            'fee_fixed' => 0.25,
        ],

        'tpay' => [
            'name' => 'Tpay',
            'description' => 'Tpay payment system',
            'enabled' => env('TPAY_ENABLED', false),
            'sandbox' => env('TPAY_SANDBOX', true),
            'api_url' => env('TPAY_API_URL', 'https://secure.tpay.com'),
            'sandbox_api_url' => env('TPAY_SANDBOX_API_URL', 'https://secure.sandbox.tpay.com'),
            'merchant_id' => env('TPAY_MERCHANT_ID'),
            'security_code' => env('TPAY_SECURITY_CODE'),
            'api_key' => env('TPAY_API_KEY'),
            'api_password' => env('TPAY_API_PASSWORD'),
            'supported_methods' => [
                'card' => 'Karta płatnicza',
                'blik' => 'BLIK',
                'bank_transfer' => 'Przelew bankowy',
            ],
            'currencies' => ['PLN'],
            'min_amount' => 1.00,
            'max_amount' => 20000.00,
            'fee_percentage' => 1.8,
            'fee_fixed' => 0.20,
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Payment Methods Configuration
    |--------------------------------------------------------------------------
    */

    'methods' => [
        'card' => [
            'name' => 'Karta płatnicza',
            'icon' => 'credit-card',
            'description' => 'Visa, Mastercard, American Express',
            'instant' => true,
            'fee_percentage' => 1.9,
            'supported_currencies' => ['PLN', 'EUR', 'USD'],
        ],
        'blik' => [
            'name' => 'BLIK',
            'icon' => 'mobile',
            'description' => 'Płatność mobilna BLIK',
            'instant' => true,
            'fee_percentage' => 1.2,
            'supported_currencies' => ['PLN'],
            'availability' => 'poland_only',
        ],
        'bank_transfer' => [
            'name' => 'Przelew bankowy',
            'icon' => 'bank',
            'description' => 'Przelew tradycyjny lub online',
            'instant' => false,
            'processing_time' => '1-3 dni robocze',
            'fee_percentage' => 0.5,
            'supported_currencies' => ['PLN', 'EUR'],
        ],
        'paypal' => [
            'name' => 'PayPal',
            'icon' => 'paypal',
            'description' => 'Płatność przez PayPal',
            'instant' => true,
            'fee_percentage' => 3.4,
            'fee_fixed' => 0.35,
            'supported_currencies' => ['PLN', 'EUR', 'USD', 'GBP'],
        ],
        'apple_pay' => [
            'name' => 'Apple Pay',
            'icon' => 'mobile',
            'description' => 'Płatność Apple Pay',
            'instant' => true,
            'fee_percentage' => 1.9,
            'supported_currencies' => ['PLN', 'EUR', 'USD'],
            'device_required' => 'ios',
        ],
        'google_pay' => [
            'name' => 'Google Pay',
            'icon' => 'mobile',
            'description' => 'Płatność Google Pay',
            'instant' => true,
            'fee_percentage' => 1.9,
            'supported_currencies' => ['PLN', 'EUR', 'USD'],
            'device_required' => 'android',
        ],
        'simulation' => [
            'name' => 'Symulacja płatności',
            'icon' => 'test-tube',
            'description' => 'Tylko do testów - nie pobiera prawdziwych pieniędzy',
            'instant' => true,
            'fee_percentage' => 0.0,
            'supported_currencies' => ['PLN', 'EUR', 'USD'],
            'development_only' => true,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Currencies Configuration
    |--------------------------------------------------------------------------
    */

    'currencies' => [
        'default' => 'PLN',
        'supported' => [
            'PLN' => [
                'name' => 'Polski Złoty',
                'symbol' => 'zł',
                'decimal_places' => 2,
                'position' => 'after', // before or after amount
            ],
            'EUR' => [
                'name' => 'Euro',
                'symbol' => '€',
                'decimal_places' => 2,
                'position' => 'before',
            ],
            'USD' => [
                'name' => 'US Dollar',
                'symbol' => '$',
                'decimal_places' => 2,
                'position' => 'before',
            ],
            'GBP' => [
                'name' => 'British Pound',
                'symbol' => '£',
                'decimal_places' => 2,
                'position' => 'before',
            ],
        ],
        'exchange_rates_api' => env('EXCHANGE_RATES_API', 'https://api.exchangerate-api.com/v4/latest/'),
        'exchange_rates_cache_ttl' => 3600, // 1 hour
    ],

    /*
    |--------------------------------------------------------------------------
    | Transaction Limits
    |--------------------------------------------------------------------------
    */

    'limits' => [
        'min_amount' => 0.01,
        'max_amount' => 100000.00,
        'daily_limit' => 50000.00,
        'monthly_limit' => 500000.00,
        'per_transaction_limit' => [
            'card' => 25000.00,
            'blik' => 5000.00,
            'bank_transfer' => 100000.00,
            'paypal' => 15000.00,
        ],
        'free_transactions_per_month' => 5,
        'max_refund_percentage' => 100,
        'refund_time_limit_days' => 90,
    ],

    /*
    |--------------------------------------------------------------------------
    | Security Settings
    |--------------------------------------------------------------------------
    */

    'security' => [
        'encryption_key' => env('PAYMENT_ENCRYPTION_KEY'),
        'webhook_signature_validation' => env('PAYMENT_WEBHOOK_VALIDATE_SIGNATURE', true),
        'require_3d_secure' => env('PAYMENT_REQUIRE_3DS', false),
        '3d_secure_threshold' => 100.00, // Require 3DS for amounts above this
        'fraud_detection' => env('PAYMENT_FRAUD_DETECTION', true),
        'max_failed_attempts' => 3,
        'lockout_time_minutes' => 15,
        'allowed_ips' => [
            // Payment provider IPs for webhooks
            'paynow' => [
                '185.69.153.0/24',
                '185.69.154.0/24',
            ],
            'stripe' => [
                '3.18.12.63',
                '3.130.192.231',
                '13.235.14.237',
                // Add more Stripe IPs
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Webhook Configuration
    |--------------------------------------------------------------------------
    */

    'webhooks' => [
        'timeout' => 30, // seconds
        'retry_attempts' => 3,
        'retry_delay' => 5, // seconds
        'verify_ssl' => env('PAYMENT_WEBHOOK_VERIFY_SSL', true),
        'secret_header' => 'X-Webhook-Signature',
        'events' => [
            'payment.completed',
            'payment.failed',
            'payment.cancelled',
            'payment.refunded',
            'payment.partially_refunded',
            'payment.chargeback',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Notification Settings
    |--------------------------------------------------------------------------
    */

    'notifications' => [
        'send_payment_confirmations' => true,
        'send_payment_failures' => true,
        'send_refund_notifications' => true,
        'admin_notification_threshold' => 10000.00, // Notify admin for large transactions
        'failed_payment_admin_notification' => true,
        'templates' => [
            'payment_confirmation' => 'emails.payment.confirmation',
            'payment_failure' => 'emails.payment.failure',
            'refund_processed' => 'emails.payment.refund',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Fees and Commissions
    |--------------------------------------------------------------------------
    */

    'fees' => [
        'calculation_method' => 'percentage_plus_fixed', // percentage_only, fixed_only, percentage_plus_fixed
        'include_in_total' => false, // Whether to include fees in payment amount
        'fee_bearer' => 'customer', // customer, merchant, split
        'minimum_fee' => 0.10,
        'maximum_fee' => 50.00,
        'refund_fees' => false, // Whether to refund fees on refund
    ],

    /*
    |--------------------------------------------------------------------------
    | Reporting and Analytics
    |--------------------------------------------------------------------------
    */

    'reporting' => [
        'daily_reports' => true,
        'weekly_reports' => true,
        'monthly_reports' => true,
        'real_time_monitoring' => true,
        'export_formats' => ['csv', 'xlsx', 'pdf'],
        'report_recipients' => [
            env('PAYMENT_REPORT_EMAIL', 'finance@company.com'),
        ],
        'metrics' => [
            'success_rate',
            'average_transaction_value',
            'total_volume',
            'fees_collected',
            'refund_rate',
            'chargeback_rate',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Cache Configuration
    |--------------------------------------------------------------------------
    */

    'cache' => [
        'ttl' => 3600, // 1 hour
        'keys' => [
            'exchange_rates' => 'payment:exchange_rates',
            'provider_status' => 'payment:provider_status',
            'fee_calculations' => 'payment:fee_calc',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Development and Testing
    |--------------------------------------------------------------------------
    */

    'testing' => [
        'test_cards' => [
            'visa_success' => '4242424242424242',
            'visa_decline' => '4000000000000002',
            'mastercard_success' => '5555555555554444',
            'amex_success' => '378282246310005',
        ],
        'test_amounts' => [
            'success' => [1.00, 10.00, 100.00],
            'decline' => [0.05, 0.84, 4.00],
            'error' => [0.01, 0.99],
        ],
        'simulate_delays' => env('PAYMENT_SIMULATE_DELAYS', false),
        'force_3d_secure' => env('PAYMENT_FORCE_3DS_TESTING', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | Logging and Monitoring
    |--------------------------------------------------------------------------
    */

    'logging' => [
        'enabled' => env('PAYMENT_LOGGING_ENABLED', true),
        'level' => env('PAYMENT_LOG_LEVEL', 'info'),
        'channels' => ['payment', 'slack'],
        'log_sensitive_data' => env('PAYMENT_LOG_SENSITIVE', false),
        'sensitive_fields' => [
            'card_number',
            'cvv',
            'password',
            'api_key',
            'secret_key',
        ],
    ],

];