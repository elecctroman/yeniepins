<?php

namespace Core;

class Security
{
    public static function sanitize(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }

    public static function sanitizeArray(array $data): array
    {
        return array_map(fn($value) => is_array($value) ? self::sanitizeArray($value) : self::sanitize((string)$value), $data);
    }

    public static function generateToken(int $length = 32): string
    {
        return bin2hex(random_bytes($length));
    }
}
