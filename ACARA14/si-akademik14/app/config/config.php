<?php
require_once __DIR__ . '/../Core/AppLogger.php';

$host = 'localhost';
$dbname = 'si_akademik';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    AppLogger::log($e, 'Koneksi database gagal');
    http_response_code(503);
    exit('Layanan sedang mengalami gangguan. Silakan coba lagi nanti.');
}

define('BASE_URL', '/PRAKTIKUM-WEB/ACARA14/si-akademik14/app/public');
?>