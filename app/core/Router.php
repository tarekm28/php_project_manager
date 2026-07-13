<?php
namespace App\Core;

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
        $controllerClass = "App\\Controllers\\{$controllerName}";

        if (!class_exists($controllerClass)) {
            Response::json(['error' => 'Controller not found'], 500);
            return;
        }

        $controller = new $controllerClass();
        $controller->$methodName();
    }
}