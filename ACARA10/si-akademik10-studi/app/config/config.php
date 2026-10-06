<?php
$host = 'localhost';
$dbname = 'si_akademik';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Koneksi database gagal: " . $e->getMessage());
}

define('BASE_URL', '/ACARA10/si-akademik10-studi/app/public');
?>  