<?php

namespace Core;

use RuntimeException;

class Bootstrap
{
    protected Router $router;

    public function __construct()
    {
        $this->registerAutoloaders();
        require_once __DIR__ . '/Helpers.php';

        $this->configurePhp();
        Logger::register();
        $this->ensureWritableDirectories();
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
        $env = config('env', 'production');
        if ($env === 'production') {
            error_reporting(E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED);
            ini_set('display_errors', '0');
        } else {
            error_reporting(E_ALL);
            ini_set('display_errors', '1');
        }

        $timezone = config('timezone', 'Europe/Istanbul');
        date_default_timezone_set($timezone);
    }

    protected function ensureWritableDirectories(): void
    {
        $paths = [
            __DIR__ . '/../storage/logs' => 'Günlük klasörü',
            __DIR__ . '/../storage/cache' => 'Önbellek klasörü',
            __DIR__ . '/../storage/uploads/csv' => 'CSV yükleme klasörü',
        ];

        foreach ($paths as $path => $label) {
            if (!is_dir($path)) {
                mkdir($path, 0775, true);
            }

            if (!is_writable($path)) {
                @chmod($path, 0775);
            }

            if (!is_writable($path)) {
                Logger::warning('{label} yazılabilir değil.', ['label' => $label, 'path' => $path]);
            }
        }
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
            if ($this->isInMaintenance($uri)) {
                $this->renderMaintenance();
                return;
            }

            $this->router->dispatch($uri, $method);
        } catch (RuntimeException $e) {
            http_response_code(404);
            Logger::warning('404 bulunamadı: {message}', ['message' => $e->getMessage(), 'uri' => $_SERVER['REQUEST_URI'] ?? '/']);
            echo Security::escape($e->getMessage());
        } catch (\Throwable $e) {
            http_response_code(500);
            Logger::error('Uygulama hatası: {message}', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            echo 'Uygulama hatası: ' . Security::escape($e->getMessage());
        }
    }

    protected function isInMaintenance(string $uri): bool
    {
        if (!setting_bool('bakim_modu', false)) {
            return false;
        }

        $path = parse_url($uri, PHP_URL_PATH) ?? '/';
        if (str_starts_with($path, '/admin')) {
            return false;
        }

        Session::start();
        $role = Session::get('user_role');
        if (in_array($role, ['admin', 'editor', 'support'], true)) {
            return false;
        }

        return true;
    }

    protected function renderMaintenance(): void
    {
        http_response_code(503);
        $view = new View();
        echo $view->render('maintenance', [
            'title' => 'Bakımdayız',
            'message' => setting('bakim_mesaj', 'Kısa bir bakım çalışması gerçekleştiriyoruz. Lütfen daha sonra tekrar deneyin.'),
        ]);
    }
}
