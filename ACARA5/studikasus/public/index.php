<?php

$routes = require __DIR__ . '/../routes/web.php';

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$baseDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));

if ($baseDir !== '/') {
    $uri = substr($uri, strlen($baseDir));
}
if ($uri === '') $uri = '/';

if (array_key_exists($uri, $routes)) {
    $controllerName = $routes[$uri][0];
    $methodName = $routes[$uri][1];

    require_once __DIR__ . '/../app/controllers/' . $controllerName . '.php';
    
    $controller = new $controllerName();
    $controller->$methodName();
} else {
    http_response_code(404);
    echo "404 - Halaman Tidak Ditemukan";
}