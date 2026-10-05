<?php
namespace App\Controllers;

use App\Models\Mahasiswa;

class MahasiswaController {
    public function index() {
        $mahasiswa = Mahasiswa::getAll();

        require_once __DIR__ . '/../views/mahasiswa/index.php';
    }

    // GET /mahasiswa/detail?nim=23001
    public function detail() {
        $nim = $_GET['nim'] ?? '';
        $mahasiswa = Mahasiswa::findByNim($nim);

        if (!$mahasiswa) {
            http_response_code(404);
            echo "Mahasiswa dengan NIM '{$nim}' tidak ditemukan.";
            return;
        }

        require_once __DIR__ . '/../views/mahasiswa/detail.php';
    }
}
?>