<?php
/**
 * Router sinifi, istekleri URL kaliplari ve HTTP methodlarina gore yonlendirir.
 *
 * Bu sinif, GET ve POST gibi methodlar icin ayri rotalar saklar.
 * Kaliplarda dinamik segmentler desteklenir: 'konular/(?P<slug>[^/]+)' gibi.
 * Parametreler regexten alinir ve denetleyiciye gecilir.
 */
class Router {
    private array $routes = [
        'GET' => [],
        'POST' => [],
    ];

    /**
     * GET istekleri icin rota tanimlar.
     */
    public function get(string $pattern, callable $callable): void {
        $this->routes['GET'][] = ['pattern' => trim($pattern, '/'), 'callable' => $callable];
    }

    /**
     * POST istekleri icin rota tanimlar.
     */
    public function post(string $pattern, callable $callable): void {
        $this->routes['POST'][] = ['pattern' => trim($pattern, '/'), 'callable' => $callable];
    }

    /**
     * Istegi eslesen bir rotaya yonlendirir.
     *
     * @param Request $request
     * @return mixed|null
     */
    public function dispatch(Request $request) {
        $method = $request->method;
        $path   = $request->path;
        if (!isset($this->routes[$method])) {
            return null;
        }
        foreach ($this->routes[$method] as $route) {
            $regex = '@^' . $route['pattern'] . '$@D';
            $matches = [];
            if (preg_match($regex, $path, $matches)) {
                // Named capture groups are included; filtre sadece string keyler
                $params = [];
                foreach ($matches as $key => $val) {
                    if (!is_int($key)) {
                        $params[$key] = $val;
                    }
                }
                return ($route['callable'])($request, $params);
            }
        }
        return null;
    }
}
