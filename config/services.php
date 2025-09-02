<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'freshdesk' => [
        'api_url' => env('FRESHDESK_API_URL'),
        'api_key' => env('FRESHDESK_API_KEY'),
        'webhook_secret' => env('FRESHDESK_WEBHOOK_SECRET'),
        'enabled' => env('FRESHDESK_ENABLED', false),
    ],

    'freshcaller' => [
        'api_url' => env('FRESHCALLER_API_URL'),
        'api_key' => env('FRESHCALLER_API_KEY'),
        'webhook_secret' => env('FRESHCALLER_WEBHOOK_SECRET'),
        'enabled' => env('FRESHCALLER_ENABLED', false),
    ],

];
