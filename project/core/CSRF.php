<?php

namespace Core;

class CSRF
{
    protected const TOKEN_KEY = '_csrf_token';

    public static function token(): string
    {
        Session::start();
        $token = Session::get(self::TOKEN_KEY);
        if (!$token) {
            $token = Security::generateToken();
            Session::set(self::TOKEN_KEY, $token);
        }
        return $token;
    }

    public static function validate(?string $token): bool
    {
        Session::start();
        $stored = Session::get(self::TOKEN_KEY);
        return $token !== null && is_string($stored) && hash_equals($stored, $token);
    }

    public static function regenerate(): void
    {
        Session::set(self::TOKEN_KEY, Security::generateToken());
    }
}
