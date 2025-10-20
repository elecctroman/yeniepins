<?php

namespace Core;

use DateTime;
use DateTimeZone;

class Logger
{
    protected static ?Logger $instance = null;
    protected string $logFile;

    public function __construct(?string $logFile = null)
    {
        $this->logFile = $logFile ?? __DIR__ . '/../storage/logs/app.log';
        $directory = dirname($this->logFile);
        if (!is_dir($directory)) {
            mkdir($directory, 0775, true);
        }
    }

    public static function instance(): Logger
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public static function register(): void
    {
        $logger = self::instance();

        set_exception_handler(static function (\Throwable $throwable) use ($logger): void {
            $logger->error('Beklenmeyen istisna: {message}', [
                'message' => $throwable->getMessage(),
                'file' => $throwable->getFile(),
                'line' => $throwable->getLine(),
                'trace' => $throwable->getTraceAsString(),
            ]);

            http_response_code(500);
            echo 'Beklenmeyen bir hata oluştu.';
        });

        set_error_handler(static function (int $severity, string $message, string $file, int $line) use ($logger): bool {
            $logger->error('PHP hatası: {message}', [
                'message' => $message,
                'file' => $file,
                'line' => $line,
                'severity' => $severity,
            ]);

            return false;
        });

        register_shutdown_function(static function () use ($logger): void {
            $error = error_get_last();
            if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR], true)) {
                $logger->error('Fatal hata: {message}', [
                    'message' => $error['message'] ?? 'Bilinmeyen',
                    'file' => $error['file'] ?? 'unknown',
                    'line' => $error['line'] ?? 0,
                ]);
            }
        });
    }

    public static function debug(string $message, array $context = []): void
    {
        self::instance()->write('DEBUG', $message, $context);
    }

    public static function info(string $message, array $context = []): void
    {
        self::instance()->write('INFO', $message, $context);
    }

    public static function warning(string $message, array $context = []): void
    {
        self::instance()->write('WARNING', $message, $context);
    }

    public static function error(string $message, array $context = []): void
    {
        self::instance()->write('ERROR', $message, $context);
    }

    protected function write(string $level, string $message, array $context = []): void
    {
        $entry = $this->format($level, $message, $context);
        $result = @file_put_contents($this->logFile, $entry . PHP_EOL, FILE_APPEND | LOCK_EX);
        if ($result === false) {
            error_log($entry);
        }
    }

    protected function format(string $level, string $message, array $context): string
    {
        $timestamp = (new DateTime('now', new DateTimeZone(config('timezone', 'Europe/Istanbul'))))->format('Y-m-d H:i:s');
        $interpolated = $this->interpolate($message, $context);
        $contextJson = $context ? json_encode($context, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : '';

        return sprintf('[%s] %s: %s %s', $timestamp, strtoupper($level), $interpolated, $contextJson);
    }

    protected function interpolate(string $message, array $context): string
    {
        $replace = [];
        foreach ($context as $key => $value) {
            if (!is_array($value) && !is_object($value)) {
                $replace['{' . $key . '}'] = (string)$value;
            }
        }

        return strtr($message, $replace);
    }
}
