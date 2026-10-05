<?php
require_once __DIR__ . '/../Models/Mahasiswa.php';

class MahasiswaController
{
    public function index()
    {
        global $pdo;
        $model = new Mahasiswa($pdo);
        $mahasiswa = $model->getAll();
        require_once __DIR__ . '/../Views/mahasiswa/index.php';
    }

    public function detail($nim)
    {
        global $pdo;
        $model = new Mahasiswa($pdo);
        $mahasiswa = $model->getByNim($nim);
        if (!$mahasiswa) {
            http_response_code(404);
            echo "Mahasiswa tidak ditemukan";
            return;
        }
        require_once __DIR__ . '/../Views/mahasiswa/detail.php';
    }
}
