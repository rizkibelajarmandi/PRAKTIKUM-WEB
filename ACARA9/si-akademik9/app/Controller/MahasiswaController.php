<?php
// app/Controllers/MahasiswaController.php

require_once __DIR__ . '/../Models/Mahasiswa.php';
require_once __DIR__ . '/../repository/MahasiswaRepository.php';

class MahasiswaController
{
    private $repo;

    public function __construct(MahasiswaRepository $repo)
    {
        $this->repo = $repo;
    }

    public function index()
    {
        $mahasiswa = $this->repo->getAll();
        require_once __DIR__ . '/../Views/mahasiswa/index.php';
    }

    public function create()
    {
        require_once __DIR__ . '/../Views/mahasiswa/create.php';
    }

    public function store()
    {
        $mahasiswa = new Mahasiswa();
        $mahasiswa->setNim($_POST['nim']);
        $mahasiswa->setNama($_POST['nama']);
        $mahasiswa->setJurusan($_POST['jurusan']);
        $mahasiswa->setProdiId($_POST['prodi_id']);
        $mahasiswa->setAngkatan($_POST['angkatan']);

        $this->repo->save($mahasiswa);

        header('Location: ' . BASE_URL . '/mahasiswa');
        exit;
    }

    public function detail($nim)
    {
        $mahasiswa = $this->repo->getByNim($nim);
        if (!$mahasiswa) {
            http_response_code(404);
            echo "Mahasiswa tidak ditemukan";
            return;
        }
        require_once __DIR__ . '/../Views/mahasiswa/detail.php';
    }

    public function edit($nim)
    {
        $mahasiswa = $this->repo->getByNim($nim);
        if (!$mahasiswa) {
            http_response_code(404);
            echo "Mahasiswa tidak ditemukan";
            return;
        }

        require_once __DIR__ . '/../Views/mahasiswa/edit.php';
    }

    public function update()
    {
        $mahasiswa = $this->repo->getByNim($_POST['nim']);
        if (!$mahasiswa) {
            http_response_code(404);
            echo "Mahasiswa tidak ditemukan";
            return;
        }

        $mahasiswa->setNama($_POST['nama']);
        $mahasiswa->setJurusan($_POST['jurusan']);
        $mahasiswa->setProdiId($_POST['prodi_id']);
        $mahasiswa->setAngkatan($_POST['angkatan']);

        $this->repo->update($mahasiswa);

        header('Location: ' . BASE_URL . '/mahasiswa');
        exit;
    }

    public function delete($nim)
    {
        $this->repo->delete($nim);
        header('Location: ' . BASE_URL . '/mahasiswa');
        exit;
    }
}
?>