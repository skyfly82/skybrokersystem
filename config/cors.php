<?php

/**
 * Cel: Konfiguracja CORS dla bezpiecznego API
 * Moduł: Security
 * Odpowiedzialny: Claude-Code
 * Data: 2025-09-02
 */

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for cross-origin resource sharing
    | or "CORS". This determines what cross-origin operations may execute
    | in web browsers. You are free to adjust these settings as needed.
    |
    | To learn more: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
    |
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'],

    'allowed_origins' => [
        'http://localhost:3000',
        'http://localhost:3001', 
        'http://127.0.0.1:3000',
        'https://admin.skybrokersystem.com',
        'https://app.skybrokersystem.com',
    ],

    'allowed_origins_patterns' => [
        '/^https?:\/\/(\w+\.)?skybrokersystem\.com$/',
    ],

    'allowed_headers' => [
        'Accept',
        'Authorization', 
        'Content-Type',
        'X-Requested-With',
        'X-API-Key',
        'X-CSRF-Token',
        'Origin',
        'User-Agent',
        'DNT',
        'Cache-Control',
    ],

    'exposed_headers' => [
        'X-RateLimit-Limit',
        'X-RateLimit-Remaining', 
        'X-RateLimit-Reset',
    ],

    'max_age' => 86400, // 24 hours

    'supports_credentials' => true,

];
