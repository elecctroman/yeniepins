<?php

namespace Core;

use RuntimeException;

class Router
{
    protected array $routes = [];

    public function __construct()
    {
        $this->loadRoutes();
    }

    protected function loadRoutes(): void
    {
        $definitions = require __DIR__ . '/../config/routes.php';
        foreach ($definitions as $route) {
            $this->addRoute($route);
        }
    }

    protected function addRoute(array $route): void
    {
        $methods = array_map('strtoupper', (array)($route['method'] ?? $route['methods'] ?? 'GET'));
        $path = $route['path'] ?? '/';
        $handler = $route['handler'] ?? null;
        if ($handler === null) {
            throw new RuntimeException('Rota için handler tanımsız: ' . $path);
        }

        $this->routes[] = [
            'methods' => $methods,
            'path' => $path,
            'regex' => $this->compilePath($path),
            'handler' => $handler,
            'middleware' => $route['middleware'] ?? [],
        ];
    }

    protected function compilePath(string $path): string
    {
        $pattern = preg_replace('#\{([a-zA-Z0-9_]+)\}#', '(?P<$1>[^/]+)', $path);
        $pattern = rtrim($pattern, '/');
        if ($pattern === '') {
            $pattern = '/';
        }

        return '#^' . $pattern . '$#u';
    }

    public function dispatch(string $uri, string $method): void
    {
        $path = parse_url($uri, PHP_URL_PATH) ?? '/';
        $path = rtrim($path, '/');
        if ($path === '') {
            $path = '/';
        }
        $method = strtoupper($method);

        foreach ($this->routes as $route) {
            if (!in_array($method, $route['methods'], true)) {
                continue;
            }

            if (preg_match($route['regex'], $path, $matches)) {
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                $this->runMiddleware($route['middleware'], $params);
                $this->callHandler($route['handler'], $params);
                return;
            }
        }

        throw new RuntimeException('Sayfa bulunamadı');
    }

    protected function runMiddleware(array $middlewares, array $params): void
    {
        foreach ($middlewares as $middleware) {
            $class = $middleware['class'] ?? null;
            if (!$class || !class_exists($class)) {
                continue;
            }

            $instance = new $class();
            $arguments = $middleware['params'] ?? [];
            if (method_exists($instance, 'handle')) {
                $instance->handle($params, $arguments);
            }
        }
    }

    protected function callHandler(array|string $handler, array $params): void
    {
        if (is_string($handler)) {
            [$controller, $action] = explode('@', $handler);
        } else {
            [$controller, $action] = $handler;
        }

        if (!str_contains($controller, '\\')) {
            $controllerClass = '\\App\\Controllers\\' . $controller;
        } else {
            $normalized = '\\' . ltrim($controller, '\\');
            if (str_starts_with($normalized, '\\App\\Controllers\\')) {
                $controllerClass = $normalized;
            } else {
                $controllerClass = '\\App\\Controllers\\' . ltrim($controller, '\\');
            }
        }

        if (!class_exists($controllerClass)) {
            throw new RuntimeException('Controller bulunamadı: ' . $controllerClass);
        }

        $controllerInstance = new $controllerClass();
        if (!method_exists($controllerInstance, $action)) {
            throw new RuntimeException('Eylem bulunamadı: ' . $action);
        }

        $controllerInstance->$action($params);
    }
}
