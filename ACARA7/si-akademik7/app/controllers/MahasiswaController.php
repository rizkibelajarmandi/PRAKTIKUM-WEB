<?php
namespace App\Controllers;

use App\Models\Mahasiswa;

class MahasiswaController {
    public function index() {
        // Memanggil method all() dari model
        $mahasiswa = Mahasiswa::all(); 
        
        // Path yang benar (1x naik ke folder app, lalu masuk views)
        require_once __DIR__ . '/../views/mahasiswa/index.php';
    }

    public function detail() {
        $nim = $_GET['nim'] ?? '';
        $mahasiswa = Mahasiswa::findByNim($nim);

        if (!$mahasiswa) {
            http_response_code(404);
            echo "Mahasiswa dengan NIM '$nim' tidak ditemukan.";
            return;
        }

        // INI YANG DIPERBAIKI (Sebelumnya ../../views, sekarang jadi ../views)
        require_once __DIR__ . '/../views/mahasiswa/detail.php';
    }
}