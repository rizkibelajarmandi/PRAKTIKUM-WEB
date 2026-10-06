<?php
// app/Controller/DosenController.php
// Controller hanya mengatur alur; semua akses database lewat DosenRepository.

require_once __DIR__ . '/../Core/BaseController.php';
require_once __DIR__ . '/../Models/Dosen.php';
require_once __DIR__ . '/../repository/DosenRepository.php';

class DosenController extends BaseController
{
    private $repo;

    public function __construct(DosenRepository $repo)
    {
        $this->repo = $repo;
    }

    public function index()
    {
        $dosen = $this->repo->getAll();
        $this->view('dosen/index', ['dosen' => $dosen]);
    }

    public function detail($id)
    {
        $dosen = $this->repo->getById($id);
        if (!$dosen) {
            http_response_code(404);
            echo "Dosen tidak ditemukan";
            return;
        }
        $this->view('dosen/detail', ['dosen' => $dosen]);
    }

    public function create()
    {
        $this->view('dosen/create');
    }

    public function store()
    {
        $dosen = new Dosen();
        $dosen->setNidn($_POST['nidn']);
        $dosen->setNama($_POST['nama']);
        $dosen->setBidangKeahlian($_POST['bidang_keahlian']);

        $this->repo->save($dosen);
        $this->redirect('/dosen');
    }

    public function edit($id)
    {
        $dosen = $this->repo->getById($id);
        if (!$dosen) {
            http_response_code(404);
            echo "Dosen tidak ditemukan";
            return;
        }
        $this->view('dosen/edit', ['dosen' => $dosen]);
    }

    public function update()
    {
        $dosen = $this->repo->getById($_POST['id']);
        if (!$dosen) {
            http_response_code(404);
            echo "Dosen tidak ditemukan";
            return;
        }

        $dosen->setNidn($_POST['nidn']);
        $dosen->setNama($_POST['nama']);
        $dosen->setBidangKeahlian($_POST['bidang_keahlian']);

        $this->repo->update($dosen);
        $this->redirect('/dosen');
    }

    public function delete($id)
    {
        $this->repo->delete($id);
        $this->redirect('/dosen');
    }
}
