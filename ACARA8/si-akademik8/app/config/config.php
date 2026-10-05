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

// Sesuaikan dengan nama folder Anda di htdocs
define('BASE_URL', 'TUGASBESOK/ACARA8-fixed/ACARA8/si-akademik8/app/public');
?>