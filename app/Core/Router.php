<?php
namespace App\Core;

class Router
{
    private $routes = [];
    public function get($pattern, $action)  { $this->add('GET', $pattern, $action); }
    public function post($pattern, $action) { $this->add('POST', $pattern, $action); }

    private function add($method, $pattern, $action)
    {
        $this->routes[] = [
            'method'  => $method,
            'pattern' => "#^{$pattern}$#",
            'action'  => $action
        ];
    }

    public function dispatch($uri, $method)
    {
        $path = parse_url($uri, PHP_URL_PATH);
        foreach ($this->routes as $route) {
            if ($method === $route['method'] && preg_match($route['pattern'], $path, $matches)) {
                list($controller, $method) = explode('@', $route['action']);
                $controller = "App\\Controllers\\{$controller}";
                $params = array_filter($matches, 'is_numeric', ARRAY_FILTER_USE_KEY);
                return (new $controller)->{$method}(...array_values($params));
            }
        }
        header("HTTP/1.0 404 Not Found");
        echo "Page non trouvée";
    }
}
