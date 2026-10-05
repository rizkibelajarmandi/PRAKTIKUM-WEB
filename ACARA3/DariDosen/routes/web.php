<?php

require_once __DIR__ . '/../app/Controller/MahasiswaController.php';
require_once __DIR__ . '/../app/Controller/DosenController.php';

$url = $_GET['url'] ?? 'mahasiswa';

$url = trim($url, '/');

switch ($url) {

    // route ke halaman mahasiswa
    case 'mahasiswa':
        $controller = new MahasiswaController();
        $controller->index();
        break;

    // route ke detail mahasiswa
    case 'mahasiswa/detail':
        $controller = new MahasiswaController();
        $controller->detail();
        break;

    // route ke halaman dosen
    case 'dosen':
        $controller = new DosenController();
        $controller->index();
        break;

    // ini adalah jika url tidak ditemukan
    default:
        http_response_code(404);

        echo "<h1>404</h1>";
        echo "<p>Halaman tidak ditemukan.</p>";
        break;
}