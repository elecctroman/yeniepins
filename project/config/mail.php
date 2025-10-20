<?php

if (!class_exists('MailerStub')) {
    class MailerStub
    {
        public static function send(string $to, string $subject, string $message): void
        {
            $logPath = __DIR__ . '/../storage/logs/mail.log';
            $directory = dirname($logPath);
            if (!is_dir($directory)) {
                mkdir($directory, 0775, true);
            }
            $entry = sprintf("[%s] To: %s | Subject: %s | %s\n", date('Y-m-d H:i:s'), $to, $subject, $message);
            file_put_contents($logPath, $entry, FILE_APPEND | LOCK_EX);
        }
    }
}

return [
    'driver' => 'smtp',
    'host' => 'smtp.example.com',
    'port' => 587,
    'username' => 'user@example.com',
    'password' => 'secret',
    'encryption' => 'tls',
    'from' => [
        'address' => 'noreply@example.com',
        'name' => 'Dijital Market',
    ],
    'log_path' => __DIR__ . '/../storage/logs/mail.log',
];
