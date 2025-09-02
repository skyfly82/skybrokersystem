<?php

return [
    'tiles' => [
        'url' => env('MAP_TILES_URL', 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png'),
        'attribution' => env('MAP_TILES_ATTRIBUTION', '&copy; OpenStreetMap contributors'),
        'max_zoom' => 19,
    ],

    'api' => [
        'header' => 'X-API-Key',
        'default_scopes' => ['map.read'],
        'rate_limit_per_minute' => env('MAP_API_RATE_PER_MINUTE', 120),
        'rate_limit_per_day' => env('MAP_API_RATE_PER_DAY', 10000),
        'cache_ttl' => 300, // seconds
        'max_limit' => 500,
        'default_limit' => 100,
    ],
];
