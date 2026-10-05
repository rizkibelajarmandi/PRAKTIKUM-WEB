<?php

$routes = require __DIR__ . '/../routes/web.php';

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$baseDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));

if ($baseDir !== '/') {
    $uri = substr($uri, strlen($baseDir));
}
if ($uri === '') $uri = '/';

$matched = false;

foreach ($routes as $route => $handler) {

    if (strpos($route, '{') !== false) {
        
        $routeParts = explode('/', trim($route, '/'));
        
        $uriParts = explode('/', trim($uri, '/'));

        if (count($routeParts) === count($uriParts)) {
            $params = [];
            $isMatch = true;

            foreach ($routeParts as $index => $part) {
                if (strpos($part, '{') !== false) {

                    $params[] = $uriParts[$index]; 
                } elseif ($part !== $uriParts[$index]) {
                
                    $isMatch = false;
                    break;
                }
            }

            if ($isMatch) {
                $controllerName = $handler[0];
                $methodName = $handler[1];

                require_once __DIR__ . '/../app/controllers/' . $controllerName . '.php';
                $controller = new $controllerName();
                
                call_user_func_array([$controller, $methodName], $params);
                
                $matched = true;
                break;
            }
        }
    } else {
       
        if ($route === $uri) {
            $controllerName = $handler[0];
            $methodName = $handler[1];

            require_once __DIR__ . '/../app/controllers/' . $controllerName . '.php';
            $controller = new $controllerName();
            $controller->$methodName();
            
            $matched = true;
            break;
        }
    }
}

if (!$matched) {
    http_response_code(404);
    echo "404 - Halaman Tidak Ditemukan";
}