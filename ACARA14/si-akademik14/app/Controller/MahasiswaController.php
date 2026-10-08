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
        try {
            $mahasiswa = $this->service->getAll();
            $this->view('mahasiswa/index', ['mahasiswa' => $mahasiswa]);
        } catch (Exception $e) {
            http_response_code(500);
            echo "Terjadi kesalahan saat mengambil data mahasiswa.";
        }
    }

    public function create()
    {
        try {
            $this->view('mahasiswa/create');
        } catch (Exception $e) {
            http_response_code(500);
            echo "Terjadi kesalahan saat membuka form.";
        }
    }

    public function store()
    {
        try {
            $result = $this->service->create($_POST);

            if ($result['success']) {
                $_SESSION['flash'] = [
                    'type' => 'success',
                    'message' => 'Data mahasiswa berhasil ditambahkan.'
                ];

                $this->redirect('/mahasiswa');
            } else {
                $this->view('mahasiswa/create', [
                    'errors' => $result['errors'],
                    'old' => $_POST
                ]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo "Terjadi kesalahan saat menambahkan data mahasiswa.";
        }
    }

    public function detail($nim)
    {
        try {
            $mahasiswa = $this->service->getByNim($nim);

            if (!$mahasiswa) {
                http_response_code(404);
                echo "Mahasiswa tidak ditemukan";
                return;
            }

            $this->view('mahasiswa/detail', [
                'mahasiswa' => $mahasiswa
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo "Terjadi kesalahan saat mengambil detail mahasiswa.";
        }
    }

    public function edit($nim)
    {
        try {
            $mahasiswa = $this->service->getByNim($nim);

            if (!$mahasiswa) {
                http_response_code(404);
                echo "Mahasiswa tidak ditemukan";
                return;
            }

            $this->view('mahasiswa/edit', [
                'mahasiswa' => $mahasiswa
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo "Terjadi kesalahan saat membuka form edit.";
        }
    }

    public function update()
    {
        try {
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
        } catch (Exception $e) {
            http_response_code(500);
            echo "Terjadi kesalahan saat mengubah data mahasiswa.";
        }
    }

    public function delete($nim)
    {
        try {
            if ($this->service->delete($nim)) {
                $_SESSION['flash'] = [
                    'type' => 'success',
                    'message' => 'Data mahasiswa berhasil dihapus.'
                ];
            } else {
                $_SESSION['flash'] = [
                    'type' => 'danger',
                    'message' => 'Data gagal dihapus. Silakan coba lagi.'
                ];
            }

            $this->redirect('/mahasiswa');
        } catch (Exception $e) {
            $_SESSION['flash'] = [
                'type' => 'danger',
                'message' => 'Terjadi kesalahan saat menghapus data.'
            ];

            $this->redirect('/mahasiswa');
        }
    }
}
?>

