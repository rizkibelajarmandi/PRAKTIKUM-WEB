<?php
return [
    '/' => ['App\Controllers\HomeController', 'index', []],
    '/login' => ['App\Controllers\AuthController', 'loginForm', []],
    '/login/process' => ['App\Controllers\AuthController', 'login', []],
    '/logout' => ['App\Controllers\AuthController', 'logout', []],
    
    '/dashboard' => ['App\Controllers\HomeController', 'dashboard', ['App\Core\Middleware\AuthMiddleware']],
    '/mahasiswa' => ['App\Controllers\MahasiswaController', 'index', ['App\Core\Middleware\AuthMiddleware']],
];
?>