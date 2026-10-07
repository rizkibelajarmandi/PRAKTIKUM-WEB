<?php
// app/Controller/MahasiswaController.php
// Controller hanya mengatur alur; semua akses database lewat MahasiswaRepository.

require_once __DIR__ . '/../Core/BaseController.php';
require_once __DIR__ . '/../Models/Mahasiswa.php';
require_once __DIR__ . '/../repository/MahasiswaRepository.php';

class MahasiswaController extends BaseController
{
    private $repo;

    public function __construct(MahasiswaRepository $repo)
    {
        $this->repo = $repo;
    }

    public function index()
    {
        $mahasiswa = $this->repo->getAll();
        $this->view('mahasiswa/index', ['mahasiswa' => $mahasiswa]);
    }

    public function create()
    {
        $this->view('mahasiswa/create');
    }

    public function store()
    {
        $mahasiswa = new Mahasiswa('');
        $mahasiswa->setNim($_POST['nim']);
        $mahasiswa->setNama($_POST['nama']);
        $mahasiswa->setJurusan($_POST['jurusan']);
        $mahasiswa->setProdiId($_POST['prodi_id']);
        $mahasiswa->setAngkatan($_POST['angkatan']);

        $this->repo->save($mahasiswa);
        $this->redirect('/mahasiswa');
    }

    public function detail($nim)
    {
        $mahasiswa = $this->repo->getByNim($nim);
        if (!$mahasiswa) {
            http_response_code(404);
            echo "Mahasiswa tidak ditemukan";
            return;
        }
        $this->view('mahasiswa/detail', ['mahasiswa' => $mahasiswa]);
    }

    public function edit($nim)
    {
        $mahasiswa = $this->repo->getByNim($nim);
        if (!$mahasiswa) {
            http_response_code(404);
            echo "Mahasiswa tidak ditemukan";
            return;
        }
        $this->view('mahasiswa/edit', ['mahasiswa' => $mahasiswa]);
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
        $this->redirect('/mahasiswa');
    }

    public function delete($nim)
    {
        $this->repo->delete($nim);
        $this->redirect('/mahasiswa');
    }
}
