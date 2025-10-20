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

    public static function validate(?string $token = null): bool
    {
        Session::start();
        $stored = Session::get(self::TOKEN_KEY);
        $provided = $token
            ?? ($_POST['_csrf'] ?? null)
            ?? ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? null);

        return $provided !== null
            && is_string($stored)
            && hash_equals($stored, (string)$provided);
    }

    public static function regenerate(): void
    {
        Session::set(self::TOKEN_KEY, Security::generateToken());
    }
}
