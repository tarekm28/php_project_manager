<?php

class Router
{
    private array $routes = [];

    public function get(string $path, string $action, array $middleware = []): void
    {
        $this->routes['GET'][$path] = ['action' => $action, 'middleware' => $middleware];
    }

    public function post(string $path, string $action, array $middleware = []): void
    {
        $this->routes['POST'][$path] = ['action' => $action, 'middleware' => $middleware];
    }

    public function put(string $path, string $action, array $middleware = []): void
    {
        $this->routes['PUT'][$path] = ['action' => $action, 'middleware' => $middleware];
    }

    public function patch(string $path, string $action, array $middleware = []): void
    {
        $this->routes['PATCH'][$path] = ['action' => $action, 'middleware' => $middleware];
    }

    public function delete(string $path, string $action, array $middleware = []): void
    {
        $this->routes['DELETE'][$path] = ['action' => $action, 'middleware' => $middleware];
    }

    public function dispatch(): void
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $uri = $_GET['route'] ?? parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/';

        if (is_string($uri)) {
            $parsedUri = parse_url($uri);
            if (isset($parsedUri['path'])) {
                $uri = $parsedUri['path'];
            }
            if (isset($parsedUri['query'])) {
                parse_str($parsedUri['query'], $queryParams);
                foreach ($queryParams as $key => $value) {
                    $_GET[$key] = $value;
                }
            }
        }
        
        $scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
        if ($scriptDir !== '/' && !empty($scriptDir) && strpos($uri, $scriptDir) === 0) {
            $uri = substr($uri, strlen($scriptDir));
        }
        if (empty($uri) || $uri === '/index.php') $uri = '/';

        if (!isset($this->routes[$method][$uri])) {
            Response::json(['error' => 'Not found'], 404);
            return;
        }

        $route = $this->routes[$method][$uri];

        foreach ($route['middleware'] as $middlewareClass) {
            $middlewareClass::handle();
        }

        [$controllerName, $methodName] = explode('@', $route['action'], 2);
        $controllerClass = $controllerName;

        $controllerFile = __DIR__ . '/../controller/' . $controllerName . '.php';
        if (file_exists($controllerFile)) {
            require_once $controllerFile;
        }

        if (!class_exists($controllerClass)) {
            Response::json(['error' => 'Controller not found'], 500);
            return;
        }

        $controller = new $controllerClass();
        $controller->$methodName();
    }
}