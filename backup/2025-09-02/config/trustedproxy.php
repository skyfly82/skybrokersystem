<?php

return [
    'proxies' => '*',
    'headers' => [
        'forwarded' => 'FORWARDED',
        'x_forwarded_for' => 'X_FORWARDED_FOR',
        'x_forwarded_host' => 'X_FORWARDED_HOST',
        'x_forwarded_port' => 'X_FORWARDED_PORT',
        'x_forwarded_proto' => 'X_FORWARDED_PROTO',
    ],
];
