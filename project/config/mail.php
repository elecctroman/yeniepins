<?php

return [
    'driver' => 'smtp',
    'host' => env('MAIL_HOST', 'smtp.example.com'),
    'port' => env('MAIL_PORT', 587),
    'username' => env('MAIL_USERNAME', 'user@example.com'),
    'password' => env('MAIL_PASSWORD', 'secret'),
    'encryption' => env('MAIL_ENCRYPTION', 'tls'),
    'from' => [
        'address' => env('MAIL_FROM_ADDRESS', 'noreply@example.com'),
        'name' => env('MAIL_FROM_NAME', 'Dijital Market'),
    ],
];
