<?php
// RESTful API Mahasiswa
//
//   GET    /api/mahasiswa        -> daftar semua mahasiswa
//   GET    /api/mahasiswa/{id}   -> detail satu mahasiswa
//   POST   /api/mahasiswa        -> tambah mahasiswa
//   PUT    /api/mahasiswa/{id}   -> ubah mahasiswa
//   DELETE /api/mahasiswa/{id}   -> hapus mahasiswa

class MahasiswaApiController
{
    private $service;

    public function __construct(MahasiswaService $service)
    {
        $this->service = $service;
    }

    // Titik masuk: dipanggil dari index.php untuk semua URL berawalan "api/"
    public function handle(string $url, string $method): void
    {
        if (!preg_match('#^api/mahasiswa(?:/([^/]+))?$#', $url, $m)) {
            $this->respond(404, false, 'Endpoint tidak ditemukan.');
        }

        $rawId = $m[1] ?? null;

        // Validasi format ID di path
        $id = null;
        if ($rawId !== null) {
            if (!ctype_digit($rawId) || (int) $rawId <= 0) {
                $this->respond(400, false, 'ID tidak valid. ID harus berupa angka bulat positif.');
            }
            $id = (int) $rawId;
        }

        // Method yang diizinkan per jenis endpoint
        $allowed = $id === null ? ['GET', 'POST'] : ['GET', 'PUT', 'DELETE'];
        if (!in_array($method, $allowed, true)) {
            header('Allow: ' . implode(', ', $allowed));
            $this->respond(405, false, 'Method ' . $method . ' tidak diizinkan untuk endpoint ini.');
        }

        try {
            switch ($method) {
                case 'GET':
                    $id === null ? $this->index() : $this->show($id);
                    break;
                case 'POST':
                    $this->store();
                    break;
                case 'PUT':
                    $this->update($id);
                    break;
                case 'DELETE':
                    $this->destroy($id);
                    break;
            }
        } catch (PDOException $e) {
            AppLogger::log($e, 'API Mahasiswa gagal');
            $this->respond(500, false, 'Terjadi kesalahan pada server.');
        }
    }

    // GET /api/mahasiswa
    private function index(): void
    {
        $data = array_map([$this, 'toArray'], $this->service->getAll());
        $this->respond(200, true, 'Data semua mahasiswa berhasil diambil.', $data);
    }

    // GET /api/mahasiswa/{id}
    private function show(int $id): void
    {
        $mhs = $this->service->getById($id);
        if (!$mhs) {
            $this->respond(404, false, 'Mahasiswa dengan ID ' . $id . ' tidak ditemukan.');
        }
        $this->respond(200, true, 'Detail data mahasiswa berhasil diambil.', $this->toArray($mhs));
    }

    // POST /api/mahasiswa
    private function store(): void
    {
        $input = $this->readJson();
        $result = $this->service->createApi($input);

        if (!$result['success']) {
            $this->fail($result);
        }

        $mhs = $result['data'];
        header('Location: ' . $this->baseApiUrl() . '/' . $mhs->getId());
        $this->respond(201, true, 'Data mahasiswa berhasil ditambahkan.', $this->toArray($mhs));
    }

    // PUT /api/mahasiswa/{id}
    private function update(int $id): void
    {
        $input = $this->readJson();
        $result = $this->service->updateApi($id, $input);

        if (!$result['success']) {
            $this->fail($result);
        }

        $this->respond(200, true, 'Data mahasiswa berhasil diubah.', $this->toArray($result['data']));
    }

    // DELETE /api/mahasiswa/{id}
    private function destroy(int $id): void
    {
        $result = $this->service->deleteApi($id);

        if (!$result['success']) {
            $this->fail($result);
        }

        $this->respond(200, true, 'Data mahasiswa berhasil dihapus.');
    }

    // ---------------------------------------------------------------- helpers

    // Baca body request sebagai JSON; hentikan dengan 400 jika kosong / rusak.
    private function readJson(): array
    {
        $raw = file_get_contents('php://input');

        if ($raw === false || trim($raw) === '') {
            $this->respond(400, false, 'Body request kosong. Kirim data dalam format JSON.');
        }

        $input = json_decode($raw, true);

        if (json_last_error() !== JSON_ERROR_NONE || !is_array($input)) {
            $this->respond(400, false, 'Format JSON tidak valid.');
        }

        return $input;
    }

    private function fail(array $result): void
    {
        $code = $result['code'];
        $errors = $result['errors'] ?? [];

        $messages = [
            404 => 'Mahasiswa tidak ditemukan.',
            409 => 'Data bentrok dengan data yang sudah ada.',
            422 => 'Validasi gagal.',
            500 => 'Terjadi kesalahan pada server.',
        ];

        $this->respond($code, false, $messages[$code] ?? 'Permintaan gagal.', null, $errors);
    }

    private function toArray($m): array
    {
        return [
            'id'       => (int) $m->getId(),
            'nim'      => $m->getNim(),
            'nama'     => $m->getNama(),
            'email'    => $m->getEmail(),
            'jurusan'  => $m->getJurusan(),
            'prodi_id' => (int) $m->getProdiId(),
            'angkatan' => (int) $m->getAngkatan(),
            'status'   => $m->getStatus(),
        ];
    }

    private function baseApiUrl(): string
    {
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $path = strtok($_SERVER['REQUEST_URI'] ?? '', '?');
        // buang "/{id}" bila ada, sisakan ".../api/mahasiswa"
        $path = preg_replace('#/api/mahasiswa(/.*)?$#', '/api/mahasiswa', $path);
        return $scheme . '://' . $host . $path;
    }

    private function respond(int $status, bool $success, string $message, $data = null, array $errors = []): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');

        $body = ['success' => $success, 'message' => $message];
        if ($data !== null) {
            $body['data'] = $data;
        }
        if ($errors) {
            $body['errors'] = $errors;
        }

        echo json_encode($body, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }
}
