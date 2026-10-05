<?php

require_once __DIR__ . '/../Controller/AuthController.php';
require_once __DIR__ . '/../Controller/DosenController.php';
require_once __DIR__ . '/../Controller/MahasiswaController.php';
require_once __DIR__ . '/../repository/MahasiswaRepository.php';
require_once __DIR__ . '/../../Middleware/AuthMiddleware.php';

$url = $_GET['url'] ?? 'login';
$url = rtrim($url, '/');

$mahasiswaRepo = new MahasiswaRepository($pdo);

$mahasiswaController = new MahasiswaController($mahasiswaRepo);

$authController  = new AuthController();
$dosenController = new DosenController();

if ($url === 'login') {
    $authController->login();
} elseif ($url === 'logout') {
    $authController->logout();
} elseif ($url === 'dashboard') {
    AuthMiddleware::handle();
    require_once __DIR__ . '/../Views/dashboard/index.php';
} elseif ($url === 'dosen') {
    AuthMiddleware::handle();
    $dosenController->index();
} elseif ($url === 'dosen/create') {
    AuthMiddleware::handle();
    $dosenController->create();
} elseif ($url === 'dosen/store' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    AuthMiddleware::handle();
    $dosenController->store();
} elseif ($url === 'dosen/edit' && isset($_GET['id'])) {
    AuthMiddleware::handle();
    $dosenController->edit($_GET['id']);
} elseif ($url === 'dosen/update' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    AuthMiddleware::handle();
    $dosenController->update();
} elseif ($url === 'dosen/delete' && isset($_GET['id'])) {
    AuthMiddleware::handle();
    $dosenController->delete($_GET['id']);
} elseif ($url === 'dosen/detail' && isset($_GET['id'])) {
    AuthMiddleware::handle();
    $dosenController->detail($_GET['id']);
} elseif ($url === 'mahasiswa') {
    AuthMiddleware::handle();
    $mahasiswaController->index();
} elseif ($url === 'mahasiswa/create') {
    AuthMiddleware::handle();
    $mahasiswaController->create();
} elseif ($url === 'mahasiswa/store' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    AuthMiddleware::handle();
    $mahasiswaController->store();
} elseif ($url === 'mahasiswa/edit' && isset($_GET['nim'])) {
    AuthMiddleware::handle();
    $mahasiswaController->edit($_GET['nim']);
} elseif ($url === 'mahasiswa/update' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    AuthMiddleware::handle();
    $mahasiswaController->update();
} elseif ($url === 'mahasiswa/delete' && isset($_GET['nim'])) {
    AuthMiddleware::handle();
    $mahasiswaController->delete($_GET['nim']);
} elseif ($url === 'mahasiswa/detail' && isset($_GET['nim'])) {
    AuthMiddleware::handle();
    $mahasiswaController->detail($_GET['nim']);
} else {
    http_response_code(404);
    echo "404 - Halaman tidak ditemukan";
}
?>