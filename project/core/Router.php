<?php

namespace Core;

class Router
{
    protected array $routes = [];

    public function __construct()
    {
        $this->routes = require __DIR__ . '/../config/routes.php';
    }

    public function dispatch(string $uri, string $method): void
    {
        $path = parse_url($uri, PHP_URL_PATH) ?? '/';
        $path = rtrim($path, '/') ?: '/';

        foreach ($this->routes as $route) {
            if ($route['method'] === $method && $route['path'] === $path) {
                $this->callHandler($route['handler']);
                return;
            }
        }

        http_response_code(404);
        echo 'Sayfa bulunamadı';
    }

    protected function callHandler(array $handler): void
    {
        [$controller, $action] = $handler;
        $controllerClass = '\\App\\Controllers\\' . $controller;
        if (!class_exists($controllerClass)) {
            require_once __DIR__ . '/../app/Controllers/' . $controller . '.php';
        }

        $controllerInstance = new $controllerClass();
        if (!method_exists($controllerInstance, $action)) {
            throw new \RuntimeException('Eylem bulunamadı: ' . $action);
        }

        $controllerInstance->$action();
    }
}
