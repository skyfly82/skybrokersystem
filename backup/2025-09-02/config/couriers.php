<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Courier
    |--------------------------------------------------------------------------
    |
    | This option controls the default courier service that will be used
    | when no specific courier is specified in requests.
    |
    */

    'default' => env('DEFAULT_COURIER', 'inpost'),

    /*
    |--------------------------------------------------------------------------
    | Courier Services Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may specify the configuration for each courier service
    | that your application supports.
    |
    */

    'services' => [

        'inpost' => [
            'name' => 'InPost',
            'enabled' => env('INPOST_ENABLED', true),
            'sandbox' => env('INPOST_SANDBOX', true),
            'api_url' => env('INPOST_API_URL', 'https://api-shipx-pl.easypack24.net'),
            'sandbox_api_url' => env('INPOST_SANDBOX_API_URL', 'https://sandbox-api-shipx-pl.easypack24.net'),
            'token' => env('INPOST_TOKEN'),
            'organization_id' => env('INPOST_ORGANIZATION_ID'),
            'services' => [
                'inpost_locker_standard' => [
                    'name' => 'Paczkomat Standard',
                    'delivery_time' => '24-48h',
                    'max_weight' => 25,
                    'max_dimensions' => [64, 38, 8], // cm
                ],
                'inpost_locker_express' => [
                    'name' => 'Paczkomat Express',
                    'delivery_time' => '24h',
                    'max_weight' => 25,
                    'max_dimensions' => [64, 38, 8],
                ],
                'inpost_courier_standard' => [
                    'name' => 'Kurier Standard',
                    'delivery_time' => '24-48h',
                    'max_weight' => 30,
                    'max_dimensions' => [100, 100, 100],
                ],
                'inpost_courier_express' => [
                    'name' => 'Kurier Express',
                    'delivery_time' => '24h',
                    'max_weight' => 30,
                    'max_dimensions' => [100, 100, 100],
                ],
            ],
            'pricing' => [
                'base_price' => 12.99,
                'weight_factor' => 2.50,
                'express_multiplier' => 1.5,
                'courier_multiplier' => 2.0,
            ],
        ],

        'dhl' => [
            'name' => 'DHL',
            'enabled' => env('DHL_ENABLED', false),
            'sandbox' => env('DHL_SANDBOX', true),
            'api_url' => env('DHL_API_URL', 'https://api.dhl.com'),
            'sandbox_api_url' => env('DHL_SANDBOX_API_URL', 'https://api-sandbox.dhl.com'),
            'username' => env('DHL_USERNAME'),
            'password' => env('DHL_PASSWORD'),
            'account_number' => env('DHL_ACCOUNT_NUMBER'),
            'services' => [
                'dhl_express_worldwide' => [
                    'name' => 'DHL Express Worldwide',
                    'delivery_time' => '1-3 dni',
                    'max_weight' => 70,
                    'international' => true,
                ],
                'dhl_express_poland' => [
                    'name' => 'DHL Express Polska',
                    'delivery_time' => '24h',
                    'max_weight' => 70,
                    'international' => false,
                ],
            ],
            'pricing' => [
                'base_price' => 45.00,
                'weight_factor' => 8.50,
                'international_multiplier' => 2.5,
            ],
        ],

        'dpd' => [
            'name' => 'DPD',
            'enabled' => env('DPD_ENABLED', false),
            'sandbox' => env('DPD_SANDBOX', true),
            'api_url' => env('DPD_API_URL', 'https://weblabel.dpd.com.pl'),
            'sandbox_api_url' => env('DPD_SANDBOX_API_URL', 'https://weblabel-sandbox.dpd.com.pl'),
            'login' => env('DPD_LOGIN'),
            'password' => env('DPD_PASSWORD'),
            'fid' => env('DPD_FID'),
            'services' => [
                'dpd_classic' => [
                    'name' => 'DPD Classic',
                    'delivery_time' => '24-48h',
                    'max_weight' => 31.5,
                ],
                'dpd_next_day' => [
                    'name' => 'DPD Next Day',
                    'delivery_time' => '24h',
                    'max_weight' => 31.5,
                ],
                'dpd_pickup' => [
                    'name' => 'DPD Pickup',
                    'delivery_time' => '24-48h',
                    'max_weight' => 31.5,
                    'pickup_points' => true,
                ],
            ],
            'pricing' => [
                'base_price' => 18.99,
                'weight_factor' => 3.50,
                'next_day_multiplier' => 1.8,
            ],
        ],

        'gls' => [
            'name' => 'GLS',
            'enabled' => env('GLS_ENABLED', false),
            'sandbox' => env('GLS_SANDBOX', true),
            'api_url' => env('GLS_API_URL', 'https://api.gls-group.eu'),
            'sandbox_api_url' => env('GLS_SANDBOX_API_URL', 'https://api-sandbox.gls-group.eu'),
            'username' => env('GLS_USERNAME'),
            'password' => env('GLS_PASSWORD'),
            'customer_id' => env('GLS_CUSTOMER_ID'),
            'services' => [
                'gls_business' => [
                    'name' => 'GLS Business Parcel',
                    'delivery_time' => '24-48h',
                    'max_weight' => 40,
                ],
                'gls_express' => [
                    'name' => 'GLS Express',
                    'delivery_time' => '24h',
                    'max_weight' => 40,
                ],
                'gls_parcelshop' => [
                    'name' => 'GLS ParcelShop',
                    'delivery_time' => '24-48h',
                    'max_weight' => 20,
                    'pickup_points' => true,
                ],
            ],
            'pricing' => [
                'base_price' => 16.50,
                'weight_factor' => 2.90,
                'express_multiplier' => 1.6,
            ],
        ],

        'meest' => [
            'name' => 'Meest',
            'enabled' => env('MEEST_ENABLED', false),
            'sandbox' => env('MEEST_SANDBOX', true),
            'api_url' => env('MEEST_API_URL', 'https://api.meest.com'),
            'sandbox_api_url' => env('MEEST_SANDBOX_API_URL', 'https://api-sandbox.meest.com'),
            'token' => env('MEEST_TOKEN'),
            'client_id' => env('MEEST_CLIENT_ID'),
            'services' => [
                'meest_standard' => [
                    'name' => 'Meest Standard',
                    'delivery_time' => '2-3 dni',
                    'max_weight' => 30,
                ],
                'meest_express' => [
                    'name' => 'Meest Express',
                    'delivery_time' => '24h',
                    'max_weight' => 30,
                ],
            ],
            'pricing' => [
                'base_price' => 22.00,
                'weight_factor' => 4.00,
                'express_multiplier' => 1.7,
            ],
        ],

        'fedex' => [
            'name' => 'FedEx',
            'enabled' => env('FEDEX_ENABLED', false),
            'sandbox' => env('FEDEX_SANDBOX', true),
            'api_url' => env('FEDEX_API_URL', 'https://apis.fedex.com'),
            'sandbox_api_url' => env('FEDEX_SANDBOX_API_URL', 'https://apis-sandbox.fedex.com'),
            'api_key' => env('FEDEX_API_KEY'),
            'secret_key' => env('FEDEX_SECRET_KEY'),
            'account_number' => env('FEDEX_ACCOUNT_NUMBER'),
            'meter_number' => env('FEDEX_METER_NUMBER'),
            'services' => [
                'fedex_international_priority' => [
                    'name' => 'FedEx International Priority',
                    'delivery_time' => '1-3 dni',
                    'max_weight' => 68,
                    'international' => true,
                ],
                'fedex_international_economy' => [
                    'name' => 'FedEx International Economy',
                    'delivery_time' => '2-5 dni',
                    'max_weight' => 68,
                    'international' => true,
                ],
            ],
            'pricing' => [
                'base_price' => 120.00,
                'weight_factor' => 15.00,
                'priority_multiplier' => 1.5,
            ],
        ],

        'ambro' => [
            'name' => 'Ambro',
            'enabled' => env('AMBRO_ENABLED', false),
            'sandbox' => env('AMBRO_SANDBOX', true),
            'api_url' => env('AMBRO_API_URL', 'https://api.ambro.pl'),
            'sandbox_api_url' => env('AMBRO_SANDBOX_API_URL', 'https://api-sandbox.ambro.pl'),
            'token' => env('AMBRO_TOKEN'),
            'services' => [
                'ambro_standard' => [
                    'name' => 'Ambro Standard',
                    'delivery_time' => '24-48h',
                    'max_weight' => 30,
                ],
                'ambro_express' => [
                    'name' => 'Ambro Express',
                    'delivery_time' => '24h',
                    'max_weight' => 30,
                ],
            ],
            'pricing' => [
                'base_price' => 19.99,
                'weight_factor' => 3.20,
                'express_multiplier' => 1.4,
            ],
        ],

        'packeta' => [
            'name' => 'Packeta',
            'enabled' => env('PACKETA_ENABLED', false),
            'sandbox' => env('PACKETA_SANDBOX', true),
            'api_url' => env('PACKETA_API_URL', 'https://www.zasilkovna.cz/api/rest'),
            'sandbox_api_url' => env('PACKETA_SANDBOX_API_URL', 'https://www.zasilkovna.cz/api/rest'),
            'api_key' => env('PACKETA_API_KEY'),
            'services' => [
                'packeta_pickup_point' => [
                    'name' => 'Packeta Pickup Point',
                    'delivery_time' => '2-3 dni',
                    'max_weight' => 50,
                    'pickup_points' => true,
                ],
                'packeta_home_delivery' => [
                    'name' => 'Packeta Home Delivery',
                    'delivery_time' => '24-48h',
                    'max_weight' => 50,
                ],
            ],
            'pricing' => [
                'base_price' => 14.90,
                'weight_factor' => 2.80,
                'home_delivery_multiplier' => 1.8,
            ],
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Global Settings
    |--------------------------------------------------------------------------
    */

    'settings' => [
        'timeout' => 30, // API request timeout in seconds
        'retry_attempts' => 3,
        'retry_delay' => 1000, // milliseconds
        'cache_ttl' => 3600, // Cache pickup points for 1 hour
        'webhook_timeout' => 10,
    ],

    /*
    |--------------------------------------------------------------------------
    | Webhook Configuration
    |--------------------------------------------------------------------------
    */

    'webhooks' => [
        'verify_signatures' => env('COURIER_WEBHOOKS_VERIFY_SIGNATURES', true),
        'allowed_ips' => [
            'inpost' => [
                '185.244.149.0/24',
                '185.244.150.0/24',
            ],
            'dhl' => [
                '194.153.237.0/24',
            ],
            'dpd' => [
                '213.172.56.0/24',
            ],
            // Add other courier IP ranges as needed
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Price Calculation
    |--------------------------------------------------------------------------
    */

    'pricing' => [
        'currency' => 'PLN',
        'tax_rate' => 0.23, // 23% VAT
        'margin' => 0.15, // 15% system margin
        'free_shipping_threshold' => 500.00, // Free shipping above this amount
        'insurance_rate' => 0.005, // 0.5% of declared value
        'cod_fee' => 5.00, // COD handling fee
        'express_fee' => 15.00, // Additional express fee
    ],

    /*
    |--------------------------------------------------------------------------
    | Supported Countries
    |--------------------------------------------------------------------------
    */

    'countries' => [
        'domestic' => ['PL'],
        'international' => [
            'DE', 'FR', 'IT', 'ES', 'NL', 'BE', 'AT', 'CZ', 'SK', 'HU',
            'LT', 'LV', 'EE', 'SI', 'HR', 'RO', 'BG', 'GB', 'IE', 'DK',
            'SE', 'FI', 'NO', 'CH', 'PT', 'GR', 'CY', 'MT', 'LU', 'US',
            'CA', 'AU', 'JP', 'CN', 'IN', 'BR', 'MX', 'AR', 'CL', 'CO',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Package Dimensions
    |--------------------------------------------------------------------------
    */

    'dimensions' => [
        'max_weight' => 70, // kg - global maximum
        'max_length' => 200, // cm
        'max_width' => 200, // cm
        'max_height' => 200, // cm
        'max_girth' => 400, // cm (length + 2*(width + height))

        'size_categories' => [
            'small' => [
                'max_weight' => 2,
                'max_dimensions' => [35, 25, 8],
                'description' => 'Małe przesyłki (dokumenty, akcesoria)',
            ],
            'medium' => [
                'max_weight' => 10,
                'max_dimensions' => [60, 40, 20],
                'description' => 'Średnie przesyłki (książki, elektronika)',
            ],
            'large' => [
                'max_weight' => 30,
                'max_dimensions' => [100, 60, 60],
                'description' => 'Duże przesyłki (odzież, sprzęt)',
            ],
            'extra_large' => [
                'max_weight' => 70,
                'max_dimensions' => [200, 200, 200],
                'description' => 'Bardzo duże przesyłki (meble, sprzęt AGD)',
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Service Levels
    |--------------------------------------------------------------------------
    */

    'service_levels' => [
        'standard' => [
            'name' => 'Standard',
            'delivery_time' => '24-48h',
            'price_multiplier' => 1.0,
            'tracking' => true,
            'insurance' => false,
        ],
        'express' => [
            'name' => 'Express',
            'delivery_time' => '24h',
            'price_multiplier' => 1.5,
            'tracking' => true,
            'insurance' => true,
        ],
        'same_day' => [
            'name' => 'Same Day',
            'delivery_time' => '4-8h',
            'price_multiplier' => 3.0,
            'tracking' => true,
            'insurance' => true,
            'available_cities' => ['Warszawa', 'Kraków', 'Gdańsk', 'Wrocław', 'Poznań'],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Additional Services
    |--------------------------------------------------------------------------
    */

    'additional_services' => [
        'cod' => [
            'name' => 'Pobranie (COD)',
            'description' => 'Płatność przy odbiorze',
            'fee' => 5.00,
            'max_amount' => 10000.00,
        ],
        'insurance' => [
            'name' => 'Ubezpieczenie',
            'description' => 'Ubezpieczenie przesyłki',
            'rate' => 0.005, // 0.5% wartości
            'min_fee' => 2.00,
            'max_coverage' => 50000.00,
        ],
        'saturday_delivery' => [
            'name' => 'Dostawa w sobotę',
            'description' => 'Dostawa w dni wolne od pracy',
            'fee' => 10.00,
        ],
        'evening_delivery' => [
            'name' => 'Dostawa wieczorna',
            'description' => 'Dostawa po godzinie 18:00',
            'fee' => 8.00,
        ],
        'fragile' => [
            'name' => 'Ostrożnie - kruche',
            'description' => 'Specjalna obsługa delikatnych przesyłek',
            'fee' => 5.00,
        ],
        'return_receipt' => [
            'name' => 'Zwrotne potwierdzenie odbioru',
            'description' => 'Potwierdzenie dostarczenia',
            'fee' => 3.00,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Business Hours
    |--------------------------------------------------------------------------
    */

    'business_hours' => [
        'pickup' => [
            'monday' => ['08:00', '18:00'],
            'tuesday' => ['08:00', '18:00'],
            'wednesday' => ['08:00', '18:00'],
            'thursday' => ['08:00', '18:00'],
            'friday' => ['08:00', '18:00'],
            'saturday' => ['09:00', '14:00'],
            'sunday' => null, // Closed
        ],
        'delivery' => [
            'monday' => ['08:00', '20:00'],
            'tuesday' => ['08:00', '20:00'],
            'wednesday' => ['08:00', '20:00'],
            'thursday' => ['08:00', '20:00'],
            'friday' => ['08:00', '20:00'],
            'saturday' => ['09:00', '16:00'],
            'sunday' => ['10:00', '16:00'],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Status Mapping
    |--------------------------------------------------------------------------
    */

    'status_mapping' => [
        'created' => 'Utworzona',
        'printed' => 'Wydrukowana',
        'dispatched' => 'Nadana',
        'in_transit' => 'W transporcie',
        'out_for_delivery' => 'W doręczeniu',
        'delivered' => 'Dostarczona',
        'returned' => 'Zwrócona',
        'cancelled' => 'Anulowana',
        'failed' => 'Błąd',
    ],

    /*
    |--------------------------------------------------------------------------
    | Notification Events
    |--------------------------------------------------------------------------
    */

    'notification_events' => [
        'shipment_created' => 'Przesyłka utworzona',
        'shipment_printed' => 'Etykieta wydrukowana',
        'shipment_dispatched' => 'Przesyłka nadana',
        'shipment_in_transit' => 'Przesyłka w transporcie',
        'shipment_out_for_delivery' => 'Przesyłka w doręczeniu',
        'shipment_delivered' => 'Przesyłka dostarczona',
        'shipment_returned' => 'Przesyłka zwrócona',
        'shipment_exception' => 'Problem z dostawą',
    ],

    /*
    |--------------------------------------------------------------------------
    | API Rate Limits
    |--------------------------------------------------------------------------
    */

    'rate_limits' => [
        'per_minute' => 60,
        'per_hour' => 1000,
        'per_day' => 10000,
        'burst_limit' => 10, // Max requests in burst
    ],

    /*
    |--------------------------------------------------------------------------
    | Logging Configuration
    |--------------------------------------------------------------------------
    */

    'logging' => [
        'enabled' => env('COURIER_LOGGING_ENABLED', true),
        'level' => env('COURIER_LOG_LEVEL', 'info'),
        'channels' => ['single', 'slack'],
        'sensitive_fields' => ['password', 'token', 'api_key'],
    ],

];
