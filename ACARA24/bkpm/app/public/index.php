<?php
// Acara 24 - Header CORS dipasang satu kali di Front Controller,
// sehingga berlaku untuk semua resource (mahasiswa, login, register, dst).
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Preflight: browser mengirim OPTIONS sebelum POST/PUT/DELETE (atau request
// ber-header Authorization) dari origin lain. Jawab 204 dan hentikan di sini,
// supaya tidak jatuh ke Router (yang akan membalas 405).
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'OPTIONS') {
    http_response_code(204);
    exit;
}

// Catatan: folder public/ berada DI DALAM app/ pada proyek ini,
// jadi path ke autoload.php tetap '../../app/core/autoload.php'.
require_once __DIR__ . '/../../app/core/autoload.php';
(new Router())->dispatch();
