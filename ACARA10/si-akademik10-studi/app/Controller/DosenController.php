<?php
require_once __DIR__ . '/../Core/BaseController.php';
require_once __DIR__ . '/../Models/Dosen.php';

class DosenController extends BaseController
{
    private $model;

    public function __construct(Dosen $model)
    {
        $this->model = $model;
    }

    public function index()
    {
        $dosen = $this->model->getAll();
        $this->view('dosen/index', ['dosen' => $dosen]);
    }

    public function detail($id)
    {
        $dosen = $this->model->getById($id);
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
        $this->model->create([
            'nidn' => $_POST['nidn'],
            'nama' => $_POST['nama'],
            'bidang_keahlian' => $_POST['bidang_keahlian']
        ]);
        $this->redirect('/dosen');
    }

    public function edit($id)
    {
        $dosen = $this->model->getById($id);
        $this->view('dosen/edit', ['dosen' => $dosen]);
    }

    public function update()
    {
        $this->model->update($_POST['id'], [
            'nidn' => $_POST['nidn'],
            'nama' => $_POST['nama'],
            'bidang_keahlian' => $_POST['bidang_keahlian']
        ]);
        $this->redirect('/dosen');
    }

    public function delete($id)
    {
        $this->model->delete($id);
        $this->redirect('/dosen');
    }
}
