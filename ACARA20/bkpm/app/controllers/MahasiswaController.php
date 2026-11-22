<?php
class MahasiswaController
{
    private Mahasiswa $model;
    public function __construct()
    {
        $this->model = new Mahasiswa();
    }
    public function index(): void
    {
        $id = $_GET['id'] ?? null;
        if ($id) {
            $data = $this->model->find((int) $id);
            if (!$data) Response::json(404, false, 'Data tidak ditemukan');
            Response::json(200, true, 'Data berhasil diambil', $data);
        }
        Response::json(200, true, 'Data berhasil diambil', $this->model->getAll());
    }
    public function store(): void
    {
        Auth::requireAuth(); //tambahan 
        $data = json_decode(file_get_contents('php://input'), true);
        if (
            empty($data['nim']) || empty($data['nama']) ||
            empty($data['prodi'])
        ) {
            Response::json(400, false, 'Field nim, nama, dan prodi wajib diisi');
        }
        $id = $this->model->create($data);
        Response::json(201, true, 'Data berhasil ditambahkan', ['id' =>
        $id]);
    }
    public function update($id): void
    {
        Auth::requireAuth();
        if (!$id) {
            Response::json(400, false, 'Parameter id wajib diisi');
        }

        $data = json_decode(file_get_contents('php://input'), true);
        if (empty($data['nim']) || empty($data['nama']) || empty($data['prodi'])) {
            Response::json(400, false, 'Field nim, nama, dan prodi wajib diisi');
        }

        if (!$this->model->find((int) $id)) {
            Response::json(404, false, 'Data tidak ditemukan');
        }

        $this->model->update((int) $id, $data);
        Response::json(200, true, 'Data berhasil diperbarui');
    }

    public function destroy($id): void
    {
        Auth::requireAuth();
        if (!$id) {
            Response::json(400, false, 'Parameter id wajib diisi');
        }

        if (!$this->model->find((int) $id)) {
            Response::json(404, false, 'Data tidak ditemukan');
        }

        $this->model->delete((int) $id);
        Response::json(200, true, 'Data berhasil dihapus');
    }
}

