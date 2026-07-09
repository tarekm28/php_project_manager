<?php

class Router
{
    private array $routes = [];

    public function get(string $path, string $action): void
    {
        $this->routes['GET'][$path] = $action;
    }

    public function post(string $path, string $action): void
    {
        $this->routes['POST'][$path] = $action;
    }

    public function dispatch(): void
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/';

        if (!isset($this->routes[$method][$uri])) {
            http_response_code(404);
            echo '404 Not Found';
            return;
        }

        [$controllerName, $methodName] = explode('@', $this->routes[$method][$uri], 2);
        $controllerFileName = strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $controllerName));
        $controllerFile = __DIR__ . '/../controller/' . $controllerFileName . '.php';

        if (!file_exists($controllerFile)) {
            throw new Exception("Controller not found: {$controllerName}");
        }

        require_once $controllerFile;

        $controller = new $controllerName();
        
        $controller->$methodName();
    }
}
