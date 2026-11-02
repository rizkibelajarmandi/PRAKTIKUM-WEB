<?php
require_once __DIR__ . '/../config/config.php';

$url = rtrim($_GET['url'] ?? '', '/');

// ===== RESTful API (tanpa session & tanpa redirect login) =====
if ($url === 'api' || strpos($url, 'api/') === 0) {
    header('Content-Type: application/json; charset=utf-8');
    require_once __DIR__ . '/../Core/BaseModel.php';
    require_once __DIR__ . '/../repository/MahasiswaRepository.php';
    require_once __DIR__ . '/../Service/MahasiswaService.php';
    require_once __DIR__ . '/../Controller/MahasiswaApiController.php';

    $api = new MahasiswaApiController(new MahasiswaService(new MahasiswaRepository($pdo)));
    $api->handle($url, $_SERVER['REQUEST_METHOD']);
    exit;
}

// ===== Aplikasi web biasa =====
session_start();

try {
    require_once __DIR__ . '/../routes/web.php';
} catch (PDOException $e) {
    AppLogger::log($e, 'Permintaan database gagal');
    http_response_code(503);
    echo 'Layanan sedang mengalami gangguan. Silakan coba lagi nanti.';
}
?>
