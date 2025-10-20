<?php

if (!class_exists('MailerStub')) {
    class MailerStub
    {
        public static function send(string $to, string $subject, string $message): void
        {
            $logPath = __DIR__ . '/../storage/logs/mail.log';
            $entry = sprintf("[%s] To: %s | Subject: %s | %s\n", date('Y-m-d H:i:s'), $to, $subject, $message);
            file_put_contents($logPath, $entry, FILE_APPEND);
        }
    }
}

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
    'log_path' => __DIR__ . '/../storage/logs/mail.log',
];
