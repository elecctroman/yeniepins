<?php

namespace Core;

require_once __DIR__ . '/Env.php';
require_once __DIR__ . '/Router.php';
require_once __DIR__ . '/Session.php';
require_once __DIR__ . '/Security.php';
require_once __DIR__ . '/CSRF.php';
require_once __DIR__ . '/Cache.php';
require_once __DIR__ . '/Helpers.php';

use Exception;

class Bootstrap
{
    protected Router $router;

    public function __construct()
    {
        Env::load();
        date_default_timezone_set(config('timezone'));
        $this->router = new Router();
    }

    public function getRouter(): Router
    {
        return $this->router;
    }

    public function run(): void
    {
        try {
            $this->router->dispatch($_SERVER['REQUEST_URI'] ?? '/', $_SERVER['REQUEST_METHOD'] ?? 'GET');
        } catch (Exception $e) {
            http_response_code(500);
            echo 'Uygulama hatası: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
        }
    }
}
