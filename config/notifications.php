<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Notification Channel
    |--------------------------------------------------------------------------
    |
    | This option controls the default notification channel that will be used
    | when no specific channel is specified.
    |
    */

    'default_channel' => env('NOTIFICATION_DEFAULT_CHANNEL', 'database'),

    /*
    |--------------------------------------------------------------------------
    | Notification Channels Configuration
    |--------------------------------------------------------------------------
    */

    'channels' => [

        'database' => [
            'enabled' => true,
            'table' => 'notifications',
            'cleanup_after_days' => 90,
            'max_notifications_per_user' => 1000,
        ],

        'mail' => [
            'enabled' => env('MAIL_NOTIFICATIONS_ENABLED', true),
            'driver' => env('MAIL_MAILER', 'smtp'),
            'from' => [
                'address' => env('MAIL_FROM_ADDRESS', 'noreply@skybroker.pl'),
                'name' => env('MAIL_FROM_NAME', 'SkyBroker System'),
            ],
            'reply_to' => [
                'address' => env('MAIL_REPLY_TO_ADDRESS', 'support@skybroker.pl'),
                'name' => env('MAIL_REPLY_TO_NAME', 'SkyBroker Support'),
            ],
            'templates' => [
                'layout' => 'emails.layouts.default',
                'footer' => 'emails.partials.footer',
                'header' => 'emails.partials.header',
            ],
            'queue' => env('MAIL_NOTIFICATIONS_QUEUE', true),
            'queue_name' => 'emails',
            'max_retries' => 3,
            'retry_delay' => 300, // 5 minutes
        ],

        'sms' => [
            'enabled' => env('SMS_NOTIFICATIONS_ENABLED', false),
            'provider' => env('SMS_PROVIDER', 'smsapi'),
            'from' => env('SMS_FROM', 'SkyBroker'),
            'max_length' => 160,
            'unicode_support' => true,
            'queue' => env('SMS_NOTIFICATIONS_QUEUE', true),
            'queue_name' => 'sms',
            'max_retries' => 3,
            'retry_delay' => 180, // 3 minutes
            
            'providers' => [
                'smsapi' => [
                    'api_url' => 'https://api.smsapi.pl',
                    'token' => env('SMSAPI_TOKEN'),
                    'sender' => env('SMSAPI_SENDER', 'SkyBroker'),
                    'test_mode' => env('SMSAPI_TEST_MODE', true),
                ],
                'twilio' => [
                    'api_url' => 'https://api.twilio.com',
                    'account_sid' => env('TWILIO_ACCOUNT_SID'),
                    'auth_token' => env('TWILIO_AUTH_TOKEN'),
                    'from' => env('TWILIO_FROM'),
                ],
                'nexmo' => [
                    'api_url' => 'https://rest.nexmo.com',
                    'api_key' => env('NEXMO_API_KEY'),
                    'api_secret' => env('NEXMO_API_SECRET'),
                    'from' => env('NEXMO_FROM'),
                ],
            ],
        ],

        'slack' => [
            'enabled' => env('SLACK_NOTIFICATIONS_ENABLED', false),
            'webhook_url' => env('SLACK_WEBHOOK_URL'),
            'channel' => env('SLACK_CHANNEL', '#notifications'),
            'username' => env('SLACK_USERNAME', 'SkyBroker Bot'),
            'icon' => env('SLACK_ICON', ':package:'),
            'mention_channel' => false,
            'mention_users' => [],
        ],

        'discord' => [
            'enabled' => env('DISCORD_NOTIFICATIONS_ENABLED', false),
            'webhook_url' => env('DISCORD_WEBHOOK_URL'),
            'username' => env('DISCORD_USERNAME', 'SkyBroker'),
            'avatar_url' => env('DISCORD_AVATAR_URL'),
        ],

        'push' => [
            'enabled' => env('PUSH_NOTIFICATIONS_ENABLED', false),
            'provider' => env('PUSH_PROVIDER', 'firebase'),
            'providers' => [
                'firebase' => [
                    'server_key' => env('FIREBASE_SERVER_KEY'),
                    'sender_id' => env('FIREBASE_SENDER_ID'),
                    'api_url' => 'https://fcm.googleapis.com/fcm/send',
                ],
                'onesignal' => [
                    'app_id' => env('ONESIGNAL_APP_ID'),
                    'api_key' => env('ONESIGNAL_API_KEY'),
                    'api_url' => 'https://onesignal.com/api/v1/notifications',
                ],
            ],
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Notification Templates
    |--------------------------------------------------------------------------
    */

    'templates' => [

        'shipment_created' => [
            'name' => 'Przesyłka utworzona',
            'description' => 'Powiadomienie o utworzeniu nowej przesyłki',
            'channels' => ['mail', 'sms', 'database'],
            'priority' => 'normal',
            'variables' => [
                'tracking_number',
                'courier_name',
                'recipient_name',
                'company_name',
                'tracking_url',
            ],
            'templates' => [
                'mail' => [
                    'subject' => 'Przesyłka {{tracking_number}} została utworzona',
                    'view' => 'emails.customer.shipment-created',
                ],
                'sms' => [
                    'content' => 'Przesyłka {{tracking_number}} została utworzona przez {{courier_name}}. Śledź: {{tracking_url}}',
                ],
                'database' => [
                    'title' => 'Nowa przesyłka',
                    'message' => 'Przesyłka {{tracking_number}} została utworzona',
                ],
            ],
        ],

        'shipment_status_updated' => [
            'name' => 'Zmiana statusu przesyłki',
            'description' => 'Powiadomienie o zmianie statusu przesyłki',
            'channels' => ['mail', 'database'],
            'priority' => 'normal',
            'variables' => [
                'tracking_number',
                'status',
                'status_description',
                'courier_name',
                'company_name',
                'tracking_url',
            ],
            'templates' => [
                'mail' => [
                    'subject' => 'Status przesyłki {{tracking_number}} został zaktualizowany',
                    'view' => 'emails.customer.shipment-status-updated',
                ],
                'database' => [
                    'title' => 'Aktualizacja statusu',
                    'message' => 'Przesyłka {{tracking_number}}: {{status_description}}',
                ],
            ],
        ],

        'shipment_delivered' => [
            'name' => 'Przesyłka dostarczona',
            'description' => 'Powiadomienie o dostarczeniu przesyłki',
            'channels' => ['mail', 'sms', 'database'],
            'priority' => 'high',
            'variables' => [
                'tracking_number',
                'recipient_name',
                'delivery_date',
                'delivery_time',
                'company_name',
            ],
            'templates' => [
                'mail' => [
                    'subject' => 'Przesyłka {{tracking_number}} została dostarczona',
                    'view' => 'emails.customer.shipment-delivered',
                ],
                'sms' => [
                    'content' => 'Przesyłka {{tracking_number}} została dostarczona do {{recipient_name}}.',
                ],
                'database' => [
                    'title' => 'Przesyłka dostarczona',
                    'message' => 'Przesyłka {{tracking_number}} została pomyślnie dostarczona',
                ],
            ],
        ],

        'payment_completed' => [
            'name' => 'Płatność zakończona',
            'description' => 'Powiadomienie o zakończeniu płatności',
            'channels' => ['mail', 'database'],
            'priority' => 'high',
            'variables' => [
                'amount',
                'currency',
                'payment_method',
                'company_name',
                'new_balance',
                'transaction_id',
            ],
            'templates' => [
                'mail' => [
                    'subject' => 'Płatność {{amount}} {{currency}} została zrealizowana',
                    'view' => 'emails.customer.payment-completed',
                ],
                'database' => [
                    'title' => 'Płatność zrealizowana',
                    'message' => 'Płatność {{amount}} {{currency}} została pomyślnie zrealizowana',
                ],
            ],
        ],

        'payment_failed' => [
            'name' => 'Płatność nieudana',
            'description' => 'Powiadomienie o nieudanej płatności',
            'channels' => ['mail', 'sms', 'database'],
            'priority' => 'high',
            'variables' => [
                'amount',
                'currency',
                'payment_method',
                'failure_reason',
                'company_name',
                'retry_url',
            ],
            'templates' => [
                'mail' => [
                    'subject' => 'Płatność {{amount}} {{currency}} nie powiodła się',
                    'view' => 'emails.customer.payment-failed',
                ],
                'sms' => [
                    'content' => 'Płatność {{amount}} {{currency}} nie powiodła się. Spróbuj ponownie: {{retry_url}}',
                ],
                'database' => [
                    'title' => 'Płatność nieudana',
                    'message' => 'Płatność {{amount}} {{currency}} nie została zrealizowana',
                ],
            ],
        ],

        'low_balance' => [
            'name' => 'Niskie saldo',
            'description' => 'Ostrzeżenie o niskim saldzie konta',
            'channels' => ['mail', 'database'],
            'priority' => 'normal',
            'variables' => [
                'current_balance',
                'currency',
                'company_name',
                'topup_url',
                'threshold',
            ],
            'templates' => [
                'mail' => [
                    'subject' => 'Niskie saldo konta - {{current_balance}} {{currency}}',
                    'view' => 'emails.customer.low-balance',
                ],
                'database' => [
                    'title' => 'Niskie saldo',
                    'message' => 'Saldo konta wynosi {{current_balance}} {{currency}}',
                ],
            ],
        ],

        'customer_registered' => [
            'name' => 'Nowy klient zarejestrowany',
            'description' => 'Powiadomienie dla adminów o nowej rejestracji',
            'channels' => ['mail', 'slack', 'database'],
            'priority' => 'normal',
            'recipients' => 'admin',
            'variables' => [
                'company_name',
                'email',
                'registration_date',
                'approval_url',
            ],
            'templates' => [
                'mail' => [
                    'subject' => 'Nowy klient oczekuje na weryfikację: {{company_name}}',
                    'view' => 'emails.admin.customer-registered',
                ],
                'slack' => [
                    'content' => 'Nowy klient {{company_name}} ({{email}}) oczekuje na weryfikację.',
                ],
                'database' => [
                    'title' => 'Nowa rejestracja',
                    'message' => 'Klient {{company_name}} oczekuje na weryfikację',
                ],
            ],
        ],

        'customer_approved' => [
            'name' => 'Klient zatwierdzony',
            'description' => 'Powiadomienie o zatwierdzeniu konta klienta',
            'channels' => ['mail', 'database'],
            'priority' => 'high',
            'variables' => [
                'company_name',
                'login_url',
                'api_key',
                'support_email',
            ],
            'templates' => [
                'mail' => [
                    'subject' => 'Konto {{company_name}} zostało aktywowane',
                    'view' => 'emails.customer.account-approved',
                ],
                'database' => [
                    'title' => 'Konto aktywowane',
                    'message' => 'Twoje konto zostało zatwierdzone i aktywowane',
                ],
            ],
        ],

        'system_error' => [
            'name' => 'Błąd systemowy',
            'description' => 'Powiadomienie o błędach systemowych',
            'channels' => ['mail', 'slack'],
            'priority' => 'critical',
            'recipients' => 'admin',
            'variables' => [
                'error_message',
                'error_code',
                'timestamp',
                'user_id',
                'ip_address',
                'stack_trace',
            ],
            'templates' => [
                'mail' => [
                    'subject' => '[BŁĄD] {{error_code}}: {{error_message}}',
                    'view' => 'emails.admin.system-error',
                ],
                'slack' => [
                    'content' => ':warning: Błąd systemowy: {{error_message}} ({{error_code}})',
                ],
            ],
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Notification Events
    |--------------------------------------------------------------------------
    */

    'events' => [
        'shipment.created' => 'shipment_created',
        'shipment.status_updated' => 'shipment_status_updated',
        'shipment.delivered' => 'shipment_delivered',
        'payment.completed' => 'payment_completed',
        'payment.failed' => 'payment_failed',
        'customer.registered' => 'customer_registered',
        'customer.approved' => 'customer_approved',
        'customer.low_balance' => 'low_balance',
        'system.error' => 'system_error',
    ],

    /*
    |--------------------------------------------------------------------------
    | Priority Levels
    |--------------------------------------------------------------------------
    */

    'priorities' => [
        'low' => [
            'value' => 1,
            'color' => 'gray',
            'delay' => 300, // 5 minutes
        ],
        'normal' => [
            'value' => 2,
            'color' => 'blue',
            'delay' => 60, // 1 minute
        ],
        'high' => [
            'value' => 3,
            'color' => 'orange',
            'delay' => 10, // 10 seconds
        ],
        'critical' => [
            'value' => 4,
            'color' => 'red',
            'delay' => 0, // Immediate
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Queue Configuration
    |--------------------------------------------------------------------------
    */

    'queue' => [
        'enabled' => env('NOTIFICATION_QUEUE_ENABLED', true),
        'connection' => env('NOTIFICATION_QUEUE_CONNECTION', 'redis'),
        'queues' => [
            'high' => 'notifications-high',
            'normal' => 'notifications',
            'low' => 'notifications-low',
        ],
        'max_tries' => 3,
        'timeout' => 120,
        'retry_delay' => 300,
    ],

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting
    |--------------------------------------------------------------------------
    */

    'rate_limiting' => [
        'enabled' => env('NOTIFICATION_RATE_LIMITING_ENABLED', true),
        'limits' => [
            'email' => [
                'per_minute' => 10,
                'per_hour' => 100,
                'per_day' => 500,
            ],
            'sms' => [
                'per_minute' => 5,
                'per_hour' => 50,
                'per_day' => 100,
            ],
            'push' => [
                'per_minute' => 30,
                'per_hour' => 500,
                'per_day' => 2000,
            ],
        ],
        'throttle_duration' => 3600, // 1 hour
    ],

    /*
    |--------------------------------------------------------------------------
    | User Preferences
    |--------------------------------------------------------------------------
    */

    'user_preferences' => [
        'allow_opt_out' => true,
        'require_double_opt_in' => false,
        'default_preferences' => [
            'email' => [
                'shipment_created' => true,
                'shipment_delivered' => true,
                'payment_completed' => true,
                'payment_failed' => true,
                'low_balance' => true,
            ],
            'sms' => [
                'shipment_created' => false,
                'shipment_delivered' => true,
                'payment_completed' => false,
                'payment_failed' => true,
                'low_balance' => false,
            ],
            'push' => [
                'shipment_created' => true,
                'shipment_delivered' => true,
                'payment_completed' => true,
                'payment_failed' => true,
                'low_balance' => true,
            ],
        ],
        'mandatory_notifications' => [
            'customer_approved',
            'system_error',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Tracking and Analytics
    |--------------------------------------------------------------------------
    */

    'tracking' => [
        'enabled' => env('NOTIFICATION_TRACKING_ENABLED', true),
        'track_opens' => true,
        'track_clicks' => true,
        'track_deliveries' => true,
        'track_bounces' => true,
        'pixel_tracking' => true,
        'link_tracking' => true,
        'retention_days' => 90,
    ],

    /*
    |--------------------------------------------------------------------------
    | Cleanup Configuration
    |--------------------------------------------------------------------------
    */

    'cleanup' => [
        'enabled' => true,
        'schedule' => 'daily',
        'retention_days' => [
            'sent_notifications' => 90,
            'failed_notifications' => 30,
            'tracking_data' => 60,
        ],
        'batch_size' => 1000,
    ],

    /*
    |--------------------------------------------------------------------------
    | Development and Testing
    |--------------------------------------------------------------------------
    */

    'testing' => [
        'mail_trap' => env('NOTIFICATION_MAIL_TRAP', false),
        'mail_trap_email' => env('NOTIFICATION_MAIL_TRAP_EMAIL', 'test@example.com'),
        'sms_trap' => env('NOTIFICATION_SMS_TRAP', false),
        'sms_trap_number' => env('NOTIFICATION_SMS_TRAP_NUMBER', '+48123456789'),
        'log_all_notifications' => env('NOTIFICATION_LOG_ALL', false),
        'fake_delivery' => env('NOTIFICATION_FAKE_DELIVERY', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | Security Settings
    |--------------------------------------------------------------------------
    */

    'security' => [
        'encrypt_sensitive_data' => true,
        'sign_webhooks' => true,
        'webhook_secret' => env('NOTIFICATION_WEBHOOK_SECRET'),
        'allowed_domains' => [
            'skybroker.pl',
            'localhost',
        ],
        'content_security_policy' => [
            'allow_external_images' => false,
            'allow_external_links' => true,
            'sanitize_html' => true,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Monitoring and Alerts
    |--------------------------------------------------------------------------
    */

    'monitoring' => [
        'enabled' => env('NOTIFICATION_MONITORING_ENABLED', true),
        'failure_threshold' => 10, // Alert after 10 failures
        'success_rate_threshold' => 95, // Alert if success rate drops below 95%
        'delivery_time_threshold' => 300, // Alert if delivery takes longer than 5 minutes
        'alert_channels' => ['slack', 'mail'],
        'alert_recipients' => [
            env('NOTIFICATION_ALERT_EMAIL', 'admin@skybroker.pl'),
        ],
    ],

];