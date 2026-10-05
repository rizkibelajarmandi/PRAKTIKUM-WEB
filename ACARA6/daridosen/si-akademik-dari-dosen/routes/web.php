<?php
return [
    '/' => ['App\Controllers\HomeController', 'index', []],
    '/login' => ['App\Controllers\AuthController', 'loginForm', []],
    '/login/process' => ['App\Controllers\AuthController', 'login', []],
    '/logout' => ['App\Controllers\AuthController', 'logout', []],
    
    // Rute yang dilindungi Middleware (Sesuai Langkah 5 BKPM)
    '/dashboard' => ['App\Controllers\HomeController', 'dashboard', ['App\Core\Middleware\AuthMiddleware']],
    '/mahasiswa' => ['App\Controllers\MahasiswaController', 'index', ['App\Core\Middleware\AuthMiddleware']],
    '/mahasiswa/detail' => ['App\Controllers\MahasiswaController', 'detail', ['App\Core\Middleware\AuthMiddleware']],
    '/dosen' => ['App\Controllers\DosenController', 'index', ['App\Core\Middleware\AuthMiddleware']],
    '/dosen/detail' => ['App\Controllers\DosenController', 'detail', ['App\Core\Middleware\AuthMiddleware']],
];
?>