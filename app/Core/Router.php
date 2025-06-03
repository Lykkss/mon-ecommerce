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
        // On transforme le pattern en expression rationnelle
        $this->routes[] = [
            'method'  => $method,
            'pattern' => '#^' . $pattern . '$#',
            'action'  => $action,
        ];
    }

    public function dispatch(string $uri, string $httpMethod): void
    {
        // On suppose que session_start() a déjà été appelé dans index.php
        $path = parse_url($uri, PHP_URL_PATH) ?: '/';

        // ◆◆ Protection automatique des routes /admin ◆◆
        if (str_starts_with($path, '/admin')) {
            if (empty($_SESSION['user_id'])
                || ($_SESSION['user_role'] ?? '') !== 'admin'
            ) {
                http_response_code(403);
                exit('403 – Accès refusé (Admin uniquement)');
            }
        }

        foreach ($this->routes as $route) {
            if ($httpMethod === $route['method']
             && preg_match($route['pattern'], $path, $matches)
            ) {
                // Conserver uniquement les paramètres nommés
                $params = array_filter(
                    $matches,
                    fn($key) => is_string($key),
                    ARRAY_FILTER_USE_KEY
                );

                $action = $route['action'];

                // 1) [Classe, méthode]
                if (is_array($action) && count($action) === 2) {
                    [$class, $method] = $action;

                // 2) "Controller@méthode"
                } elseif (is_string($action) && str_contains($action, '@')) {
                    [$ctrl, $method] = explode('@', $action, 2);
                    $class = "App\\Controllers\\{$ctrl}";

                // 3) Callable simple
                } else {
                    call_user_func_array($action, array_values($params));
                    return;
                }

                // Instanciation + appel de la méthode
                (new $class)->{$method}(...array_values($params));
                return;
            }
        }

        // Aucune route ne correspond → 404
        http_response_code(404);
        echo "404 – Page non trouvée";
    }
}
