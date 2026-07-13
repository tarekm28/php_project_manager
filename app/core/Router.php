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
        
        // Check query param first
        $uri = $_GET['route'] ?? null;
        
        if ($uri === null) {
            $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/';

            // Auto-detect script directory to strip it
            $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
            $scriptDir = dirname($scriptName);
            // Normalize slashes
            $scriptDir = str_replace('\\', '/', $scriptDir);
            
            if ($scriptDir !== '/' && !empty($scriptDir) && strpos($uri, $scriptDir) === 0) {
                $uri = substr($uri, strlen($scriptDir));
            }
        }

        if (empty($uri)) {
            $uri = '/';
        }
        
        if ($uri !== '/' && strpos($uri, '/') !== 0) {
            $uri = '/' . $uri;
        }

        // Strip trailing slash if it is not just '/'
        if ($uri !== '/' && substr($uri, -1) === '/') {
            $uri = rtrim($uri, '/');
        }

        // Normalize direct index.php requests to the root route
        if ($uri === '/index.php') {
            $uri = '/';
        }

        if (!isset($this->routes[$method][$uri])) {
            http_response_code(404);
            echo '404 Not Found';
            return;
        }

        [$controllerName, $methodName] = explode('@', $this->routes[$method][$uri], 2);
        $controllerFile = __DIR__ . '/../controller/' . $controllerName . '.php';

        if (!file_exists($controllerFile)) {
            throw new Exception("Controller not found: {$controllerName}");
        }

        require_once $controllerFile;

        $controller = new $controllerName();
        
        $controller->$methodName();
    }
}
