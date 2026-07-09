<?php
return [
    'dashscope' => [
        'api_key' => env('DASHSCOPE_API_KEY', ''),
        'endpoint' => env('DASHSCOPE_ENDPOINT', 'https://dashscope.aliyuncs.com/compatible-mode/v1/chat/completions'),
        'model' => env('DASHSCOPE_MODEL', 'qwen-plus'),
        'timeout' => 15,
        'retry' => 2,
    ],
    'sensitive_words' => ['赌博', '诈骗', '洗钱', '暴力', '套现', '伪造保单'],
    'rate_limit' => [
        'window_seconds' => 60,
        'max_requests' => 10,
    ],
];

