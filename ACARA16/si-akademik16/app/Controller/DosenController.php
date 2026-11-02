<?php
require_once __DIR__ . '/../Core/BaseController.php';
require_once __DIR__ . '/../Service/DosenService.php';

class DosenController extends BaseController
{
    private $service;

    public function __construct(DosenService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $dosen = $this->service->getAll();
        $this->view('dosen/index', ['dosen' => $dosen]);
    }

    public function detail($id)
    {
        $dosen = $this->service->getById($id);
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
        $result = $this->service->create($_POST);

        if ($result['success']) {
            $_SESSION['flash'] = [
                'type' => 'success',
                'message' => 'Data dosen berhasil ditambahkan.'
            ];
            $this->redirect('/dosen');
        } else {
            $this->view('dosen/create', [
                'errors' => $result['errors'],
                'old' => $_POST
            ]);
        }
    }

    public function edit($id)
    {
        $dosen = $this->service->getById($id);
        if (!$dosen) {
            http_response_code(404);
            echo "Dosen tidak ditemukan";
            return;
        }
        $this->view('dosen/edit', ['dosen' => $dosen]);
    }

    public function update()
    {
        $result = $this->service->update($_POST);

        if ($result['success']) {
            $_SESSION['flash'] = [
                'type' => 'success',
                'message' => 'Data dosen berhasil diubah.'
            ];
            $this->redirect('/dosen');
        } else {
            $dosen = $this->service->getById($_POST['id']);
            $this->view('dosen/edit', [
                'dosen' => $dosen,
                'errors' => $result['errors'],
                'old' => $_POST
            ]);
        }
    }

    public function delete($id)
    {
        $this->service->delete($id);
        $_SESSION['flash'] = [
            'type' => 'success',
            'message' => 'Data dosen berhasil dihapus.'
        ];
        $this->redirect('/dosen');
    }
}
?>