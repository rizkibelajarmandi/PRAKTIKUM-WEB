<?php
require_once __DIR__ . '/../Core/BaseController.php';
require_once __DIR__ . '/../Service/MahasiswaService.php';

class MahasiswaController extends BaseController
{
    private $service;

    public function __construct(MahasiswaService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $mahasiswa = $this->service->getAll();
        $this->view('mahasiswa/index', ['mahasiswa' => $mahasiswa]);
    }

    public function create()
    {
        $this->view('mahasiswa/create');
    }

    public function store()
    {
        $result = $this->service->create($_POST);

        if ($result['success']) {
            $_SESSION['flash'] = [
                'type' => 'success',
                'message' => 'Data mahasiswa berhasil ditambahkan.'
            ];
            $this->redirect('/mahasiswa');
        } else {
            // Kembalikan ke form dengan error dan input sebelumnya
            $this->view('mahasiswa/create', [
                'errors' => $result['errors'],
                'old' => $_POST
            ]);
        }
    }

    public function detail($nim)
    {
        $mahasiswa = $this->service->getByNim($nim);
        if (!$mahasiswa) {
            http_response_code(404);
            echo "Mahasiswa tidak ditemukan";
            return;
        }
        $this->view('mahasiswa/detail', ['mahasiswa' => $mahasiswa]);
    }

    public function edit($nim)
    {
        $mahasiswa = $this->service->getByNim($nim);
        if (!$mahasiswa) {
            http_response_code(404);
            echo "Mahasiswa tidak ditemukan";
            return;
        }
        $this->view('mahasiswa/edit', ['mahasiswa' => $mahasiswa]);
    }

    public function update()
    {
        $result = $this->service->update($_POST);

        if ($result['success']) {
            $_SESSION['flash'] = [
                'type' => 'success',
                'message' => 'Data mahasiswa berhasil diubah.'
            ];
            $this->redirect('/mahasiswa');
        } else {
            $mahasiswa = $this->service->getByNim($_POST['nim']);
            $this->view('mahasiswa/edit', [
                'mahasiswa' => $mahasiswa,
                'errors' => $result['errors'],
                'old' => $_POST
            ]);
        }
    }

    public function delete($nim)
    {
        $this->service->delete($nim);
        $_SESSION['flash'] = [
            'type' => 'success',
            'message' => 'Data mahasiswa berhasil dihapus.'
        ];
        $this->redirect('/mahasiswa');
    }
}
?>