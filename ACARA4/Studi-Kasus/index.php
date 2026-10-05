<?php

require_once __DIR__ . '/app/Models/Mahasiswa.php';

use App\Models\Mahasiswa;

$daftarMahasiswa = [
    new Mahasiswa('2401001', 'Budi Santoso', 'Teknik Informatika'),
    new Mahasiswa('2302045', 'Siti Aminah', 'Sistem Informasi'),
    new Mahasiswa('2205077', 'Andi Wijaya', 'Manajemen Informatika'),
    new Mahasiswa('2401099', 'Rina Kartika', 'Teknik Informatika'),
];

$content = __DIR__ . '/app/Views/mahasiswa/index.php';

require __DIR__ . '/app/Views/layouts/main.php';
