<?php
require_once __DIR__ . '/../Models/Dosen.php';

class DosenController
{
    public function index()
    {
        global $pdo;
        $model = new Dosen($pdo);
        $dosen = $model->getAll();
        require_once __DIR__ . '/../Views/dosen/index.php';
    }

    public function detail($id)
    {
        global $pdo;
        $model = new Dosen($pdo);
        $dosen = $model->getById($id);
        if (!$dosen) {
            http_response_code(404);
            echo "Dosen tidak ditemukan";
            return;
        }
        require_once __DIR__ . '/../Views/dosen/detail.php';
    }

    public function create()
    {
        require_once __DIR__ . '/../Views/dosen/create.php';
    }

    public function store()
    {
        global $pdo;
        $model = new Dosen($pdo);
        $model->create([
            'nidn' => $_POST['nidn'],
            'nama' => $_POST['nama'],
            'bidang_keahlian' => $_POST['bidang_keahlian']
        ]);
        header('Location: ' . BASE_URL . '/dosen');
        exit;
    }

    public function edit($id)
    {
        global $pdo;
        $model = new Dosen($pdo);
        $dosen = $model->getById($id);
        require_once __DIR__ . '/../Views/dosen/edit.php';
    }

    public function update()
    {
        global $pdo;
        $model = new Dosen($pdo);
        $model->update($_POST['id'], [
            'nidn' => $_POST['nidn'],
            'nama' => $_POST['nama'],
            'bidang_keahlian' => $_POST['bidang_keahlian']
        ]);
        header('Location: ' . BASE_URL . '/dosen');
        exit;
    }

    public function delete($id)
    {
        global $pdo;
        $model = new Dosen($pdo);
        $model->delete($id);
        header('Location: ' . BASE_URL . '/dosen');
        exit;
    }
}
