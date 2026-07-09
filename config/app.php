<?php
return [
    'app_debug' => env('APP_DEBUG', false),
    'default_timezone' => 'Asia/Shanghai',
    'exception_handle' => \app\ExceptionHandle::class,
    'show_error_msg' => env('APP_DEBUG', false),
];

