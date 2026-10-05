<?php
namespace App\Core;

class Router {
    public function run($routes) {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $scriptName = dirname($_SERVER['SCRIPT_NAME']);
        
        if ($scriptName !== '/') {
            $uri = substr($uri, strlen($scriptName));
        }
        if ($uri === '') $uri = '/';

        foreach ($routes as $route => $handler) {
            if ($route === $uri) {
                $controllerName = $handler[0];
                $methodName = $handler[1];
                $middlewares = $handler[2] ?? [];

                foreach ($middlewares as $mw) {
                    $instance = new $mw();
                    $instance->handle();
                }

                if (class_exists($controllerName)) {
                    $controller = new $controllerName();
                    if (method_exists($controller, $methodName)) {
                        $controller->$methodName();
                        return;
                    }
                }
            }
        }
        
        http_response_code(404);
        echo "404 - Halaman Tidak Ditemukan";
    }
}
?>