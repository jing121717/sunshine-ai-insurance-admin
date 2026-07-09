<?php
return [
    'default' => 'mysql',
    'connections' => [
        'mysql' => [
            'type' => 'mysql',
            'hostname' => env('DB_HOST', '127.0.0.1'),
            'database' => env('DB_DATABASE', 'sunshine_insurance_ai'),
            'username' => env('DB_USERNAME', 'root'),
            'password' => env('DB_PASSWORD', ''),
            'hostport' => env('DB_PORT', '3306'),
            'charset' => 'utf8mb4',
            'prefix' => '',
            'debug' => env('APP_DEBUG', false),
            'deploy' => 0,
            'rw_separate' => false,
            'fields_strict' => true,
            'break_reconnect' => true,
            'trigger_sql' => env('APP_DEBUG', true),
        ],
    ],
];

