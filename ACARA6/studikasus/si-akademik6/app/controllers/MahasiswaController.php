<?php
namespace App\Controllers;

class MahasiswaController {
    public function index() {
        
        $mahasiswa = [
            ['nim' => '23001', 'nama' => 'Andi', 'prodi' => 'Teknik Informatika'],
            ['nim' => '23002', 'nama' => 'Budi', 'prodi' => 'Teknik Informatika'],
        ];
        
        require_once __DIR__ . '/../Views/mahasiswa/index.php';
    }
}
?>