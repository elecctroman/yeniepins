<?php

use Core\Env;

if (!function_exists('config')) {
    function config(?string $key = null, mixed $default = null): mixed
    {
        static $config;
        if ($config === null) {
            $config = require __DIR__ . '/../config/config.php';
        }

        if ($key === null) {
            return $config;
        }

        return $config[$key] ?? $default;
    }
}

if (!function_exists('env')) {
    function env(string $key, mixed $default = null): mixed
    {
        return Env::get($key, $default);
    }
}

if (!function_exists('asset')) {
    function asset(string $path): string
    {
        return rtrim(config('base_url'), '/') . '/public/assets/' . ltrim($path, '/');
    }
}
