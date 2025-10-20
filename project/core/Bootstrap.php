<?php

namespace Core;

use RuntimeException;

class Bootstrap
{
    protected Router $router;

    public function __construct()
    {
        $this->registerAutoloaders();
        Env::load();

        require_once __DIR__ . '/Helpers.php';

        $this->configurePhp();
        $this->router = new Router();
    }

    protected function registerAutoloaders(): void
    {
        spl_autoload_register(function (string $class): void {
            $prefixes = [
                'Core\\' => __DIR__ . '/',
                'App\\' => __DIR__ . '/../app/',
            ];

            foreach ($prefixes as $prefix => $baseDir) {
                $len = strlen($prefix);
                if (strncmp($class, $prefix, $len) !== 0) {
                    continue;
                }
                $relativeClass = substr($class, $len);
                $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';
                if (file_exists($file)) {
                    require_once $file;
                }
            }
        });
    }

    protected function configurePhp(): void
    {
        $env = env('APP_ENV', 'production');
        if ($env === 'production') {
            error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);
            ini_set('display_errors', '0');
        } else {
            error_reporting(E_ALL);
            ini_set('display_errors', '1');
        }

        $timezone = config('timezone', 'Europe/Istanbul');
        date_default_timezone_set($timezone);
    }

    public function getRouter(): Router
    {
        return $this->router;
    }

    public function run(): void
    {
        try {
            $uri = $_SERVER['REQUEST_URI'] ?? '/';
            $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
            $this->router->dispatch($uri, $method);
        } catch (RuntimeException $e) {
            http_response_code(404);
            echo Security::escape($e->getMessage());
        } catch (\Throwable $e) {
            http_response_code(500);
            echo 'Uygulama hatası: ' . Security::escape($e->getMessage());
        }
    }
}
