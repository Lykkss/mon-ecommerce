<?php
namespace App\Core;

class Router
{
    private array $routes = [];

    public function get(string $pattern, string|array|callable $action): void
    {
        $this->add('GET', $pattern, $action);
    }

    public function post(string $pattern, string|array|callable $action): void
    {
        $this->add('POST', $pattern, $action);
    }

    private function add(string $method, string $pattern, string|array|callable $action): void
    {
        $this->routes[] = [
            'method'  => $method,
            'pattern' => '#^' . $pattern . '$#',
            'action'  => $action,
        ];
    }

    public function dispatch(string $uri, string $httpMethod): void
    {
        $path = parse_url($uri, PHP_URL_PATH) ?: '/';

        foreach ($this->routes as $route) {
            if ($httpMethod === $route['method'] && preg_match($route['pattern'], $path, $matches)) {
                // on ne garde que les clés nommées
                $params = array_filter(
                    $matches,
                    fn($key) => is_string($key),
                    ARRAY_FILTER_USE_KEY
                );

                $action = $route['action'];

                // 1) Action sous forme de [Classe, 'méthode']
                if (is_array($action) && count($action) === 2) {
                    [$class, $method] = $action;

                // 2) Action sous forme de "Controller@méthode"
                } elseif (is_string($action) && strpos($action, '@') !== false) {
                    [$controllerName, $method] = explode('@', $action, 2);
                    $class = "App\\Controllers\\{$controllerName}";

                // 3) Sinon, on considère que c'est un callable ou une closure
                } else {
                    call_user_func_array($action, array_values($params));
                    return;
                }

                // Appel du contrôleur
                (new $class)->{$method}(...array_values($params));
                return;
            }
        }

        // Aucune route ne correspond
        http_response_code(404);
        echo "Page non trouvée";
    }
}
