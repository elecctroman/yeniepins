<?php

use Core\Env;
use Core\Security;

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

if (!function_exists('str_slug')) {
    function str_slug(string $value): string
    {
        $value = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $value);
        $value = preg_replace('~[^\pL\d]+~u', '-', $value ?? '');
        $value = trim($value ?? '', '-');
        $value = preg_replace('~-+~', '-', $value ?? '');
        return strtolower($value ?? '');
    }
}

if (!function_exists('money_fmt')) {
    function money_fmt(float $amount): string
    {
        return number_format($amount, 2, ',', '.');
    }
}

if (!function_exists('now_tr')) {
    function now_tr(string $format = 'Y-m-d H:i:s'): string
    {
        $tz = new DateTimeZone(config('timezone', 'Europe/Istanbul'));
        return (new DateTime('now', $tz))->format($format);
    }
}

if (!function_exists('csrf_field')) {
    function csrf_field(): string
    {
        $token = Core\CSRF::token();
        return '<input type="hidden" name="_csrf" value="' . Security::escape($token) . '">';
    }
}

if (!function_exists('old')) {
    function old(string $key, mixed $default = null): mixed
    {
        return Core\Session::get('_old_input.' . $key, $default);
    }
}
