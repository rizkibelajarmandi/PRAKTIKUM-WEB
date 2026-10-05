<?php

require_once __DIR__ . '/../Models/mahasiswa.php';

class MahasiswaController
{
    public function index()
    {
        $model = new Mahasiswa();

        $data['mahasiswa'] = $model->getAll();

        require_once __DIR__ . '/../Views/mahasiswa/index.php';
    }

    public function detail()
    {
        $nim = $_GET['nim'] ?? null;

        $model = new Mahasiswa();

        $data['mahasiswa'] = $model->getByNim($nim);

        require_once __DIR__ . '/../Views/mahasiswa/detail.php';
    }
}