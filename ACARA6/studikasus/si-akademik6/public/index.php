<?php
session_start();

spl_autoload_register(function ($class) {
    $file = __DIR__ . '/../' . str_replace('\\', '/', $class) . '.php';
    if (file_exists($file)) {
        require $file;
    }
});

require_once __DIR__ . '/../config/app.php';

$routes = require_once __DIR__ . '/../routes/web.php';

use App\Core\Router;
$router = new Router();
$router->run($routes);
?>