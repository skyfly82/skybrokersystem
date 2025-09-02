<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default SMS Provider
    |--------------------------------------------------------------------------
    */

    'default' => env('SMS_PROVIDER', 'log'),

    /*
    |--------------------------------------------------------------------------
    | SMS Providers Configuration
    |--------------------------------------------------------------------------
    */

    'providers' => [
        'log' => [
            'driver' => 'log',
            'channel' => env('SMS_LOG_CHANNEL', 'sms'),
        ],

        'smsapi' => [
            'driver' => 'smsapi',
            'api_url' => env('SMSAPI_URL', 'https://api.smsapi.pl'),
            'api_token' => env('SMSAPI_TOKEN'),
            'sender' => env('SMSAPI_SENDER', 'SkyBroker'),
            'test_mode' => env('SMSAPI_TEST_MODE', true),
        ],

        'twilio' => [
            'driver' => 'twilio',
            'account_sid' => env('TWILIO_ACCOUNT_SID'),
            'auth_token' => env('TWILIO_AUTH_TOKEN'),
            'from' => env('TWILIO_FROM'),
        ],

        'vonage' => [
            'driver' => 'vonage',
            'api_key' => env('VONAGE_API_KEY'),
            'api_secret' => env('VONAGE_API_SECRET'),
            'from' => env('VONAGE_FROM', 'SkyBroker'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | SMS Settings
    |--------------------------------------------------------------------------
    */

    'settings' => [
        'max_length' => env('SMS_MAX_LENGTH', 160),
        'sender' => env('SMS_SENDER', 'SkyBroker'),
        'queue' => env('SMS_QUEUE', 'sms'),
        'retry_attempts' => env('SMS_RETRY_ATTEMPTS', 3),
        'retry_delay' => env('SMS_RETRY_DELAY', 60), // seconds
    ],

    /*
    |--------------------------------------------------------------------------
    | SMS Templates
    |--------------------------------------------------------------------------
    */

    'templates' => [
        'shipment_delivered' => 'Przesyłka {tracking_number} została dostarczona. Sprawdź szczegóły: {url}',
        'payment_failed' => 'Płatność nie powiodła się. Kwota: {amount} PLN. Sprawdź: {url}',
        'low_balance' => 'Niskie saldo: {balance} PLN. Doładuj konto: {url}',
        'account_approved' => 'Twoje konto zostało zatwierdzone! Zaloguj się: {url}',
        'verification_code' => 'Twój kod weryfikacyjny: {code}',
    ],

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting
    |--------------------------------------------------------------------------
    */

    'rate_limits' => [
        'per_minute' => env('SMS_RATE_LIMIT_PER_MINUTE', 10),
        'per_hour' => env('SMS_RATE_LIMIT_PER_HOUR', 100),
        'per_day' => env('SMS_RATE_LIMIT_PER_DAY', 500),
    ],
];
