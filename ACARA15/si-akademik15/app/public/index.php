<?php
session_start();
require_once __DIR__ . '/../config/config.php';

try {
    require_once __DIR__ . '/../routes/web.php';
} catch (PDOException $e) {
    AppLogger::log($e, 'Permintaan database gagal');
    http_response_code(503);
    echo 'Layanan sedang mengalami gangguan. Silakan coba lagi nanti.';
}
?>