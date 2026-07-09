<?php
return [
    'host' => env('REDIS_HOST', '127.0.0.1'),
    'port' => (int) env('REDIS_PORT', 6379),
    'password' => env('REDIS_PASSWORD', ''),
    'select' => (int) env('REDIS_SELECT', 0),
    'timeout' => 2.0,
    'prefix' => 'sunshine_ai:',
];

