<?php

namespace Core;

class Env
{
    protected static bool $loaded = false;

    public static function load(string $path = null): void
    {
        if (self::$loaded) {
            return;
        }

        $path ??= __DIR__ . '/../.env.php';
        if (!file_exists($path)) {
            $path = __DIR__ . '/../.env.example.php';
        }

        $variables = include $path;
        if (is_array($variables)) {
            foreach ($variables as $key => $value) {
                $_ENV[$key] = $value;
            }
        }

        self::$loaded = true;
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        return $_ENV[$key] ?? $default;
    }
}
