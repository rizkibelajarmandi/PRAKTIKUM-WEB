<?php
class MahasiswaController
{
    // Folder tujuan upload. public/ berada DI DALAM app/ pada proyek ini,
    // jadi dari app/controllers/ cukup naik satu level ('/../public/uploads/').
    private const UPLOAD_DIR = __DIR__ . '/../public/uploads/';
    private const MAKS_UKURAN = 2 * 1024 * 1024; // 2 MB
    // ekstensi yang diizinkan => signature gambar yang sebenarnya
    private const TIPE_DIIZINKAN = [
        'jpg'  => IMAGETYPE_JPEG,
        'jpeg' => IMAGETYPE_JPEG,
        'png'  => IMAGETYPE_PNG,
    ];

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
        Auth::requireAdmin(); // Acara 25: hanya admin
        $data = $this->bacaInput();
        if (
            empty($data['nim']) || empty($data['nama']) ||
            empty($data['prodi'])
        ) {
            Response::json(400, false, 'Field nim, nama, dan prodi wajib diisi');
        }

        if ($this->model->nimExists((string) $data['nim'])) {
            Response::json(422, false, 'NIM sudah terdaftar');
        }

        // Acara 26: foto bersifat opsional; null jika tidak ada berkas dikirim
        $foto = $this->prosesFoto();
        if ($foto) {
            $data['foto'] = $foto;
        }

        $id = $this->model->create($data);
        Response::json(201, true, 'Data berhasil ditambahkan', ['id' => $id, 'foto' => $foto]);
    }

    public function update($id): void
    {
        Auth::requireAdmin(); // Acara 25: hanya admin
        if (!$id) {
            Response::json(400, false, 'Parameter id wajib diisi');
        }

        $data = $this->bacaInput();
        if (empty($data['nim']) || empty($data['nama']) || empty($data['prodi'])) {
            Response::json(400, false, 'Field nim, nama, dan prodi wajib diisi');
        }

        $lama = $this->model->find((int) $id);
        if (!$lama) {
            Response::json(404, false, 'Data tidak ditemukan');
        }

        if ($this->model->nimExists((string) $data['nim'], (int) $id)) {
            Response::json(422, false, 'NIM sudah terdaftar');
        }

        $fotoBaru = $this->prosesFoto();
        if ($fotoBaru) {
            $data['foto'] = $fotoBaru;
        }

        $this->model->update((int) $id, $data);

        // Tugas mandiri 1: hapus foto lama setelah data berhasil diperbarui dengan foto baru
        if ($fotoBaru) {
            $this->hapusBerkas($lama['foto'] ?? null);
        }

        Response::json(200, true, 'Data berhasil diperbarui');
    }

    public function destroy($id): void
    {
        Auth::requireAdmin(); // Acara 25: hanya admin
        if (!$id) {
            Response::json(400, false, 'Parameter id wajib diisi');
        }

        $mhs = $this->model->find((int) $id);
        if (!$mhs) {
            Response::json(404, false, 'Data tidak ditemukan');
        }

        $this->model->delete((int) $id);
        $this->hapusBerkas($mhs['foto'] ?? null); // jangan tinggalkan berkas yatim
        Response::json(200, true, 'Data berhasil dihapus');
    }

    /**
     * Membaca input request:
     * - multipart/form-data (form dengan berkas) -> $_POST
     * - selain itu (mis. JSON dari Postman)      -> php://input
     */
    private function bacaInput(): array
    {
        $tipe = $_SERVER['CONTENT_TYPE'] ?? '';
        if (stripos($tipe, 'multipart/form-data') !== false) {
            // Jika request melebihi post_max_size, PHP mengosongkan $_POST dan $_FILES sekaligus.
            if (empty($_POST) && empty($_FILES) && (int) ($_SERVER['CONTENT_LENGTH'] ?? 0) > 0) {
                Response::json(413, false, 'Ukuran request melebihi batas server');
            }
            return $_POST;
        }
        $data = json_decode(file_get_contents('php://input'), true);
        return is_array($data) ? $data : [];
    }

    /**
     * Memvalidasi dan menyimpan foto dari $_FILES['foto'].
     * Mengembalikan nama file baru, atau null jika tidak ada berkas yang dikirim.
     */
    private function prosesFoto(): ?string
    {
        // $_FILES['foto'] tetap ada walau tidak memilih berkas (error = UPLOAD_ERR_NO_FILE)
        if (!isset($_FILES['foto']) || $_FILES['foto']['error'] === UPLOAD_ERR_NO_FILE) {
            return null;
        }
        $file = $_FILES['foto'];

        if ($file['error'] === UPLOAD_ERR_INI_SIZE || $file['error'] === UPLOAD_ERR_FORM_SIZE) {
            Response::json(422, false, 'Ukuran berkas maksimal 2MB');
        }
        if ($file['error'] !== UPLOAD_ERR_OK) {
            Response::json(500, false, 'Upload gagal (kode ' . $file['error'] . ')');
        }
        if ($file['size'] > self::MAKS_UKURAN) {
            Response::json(422, false, 'Ukuran berkas maksimal 2MB');
        }

        // Jangan percaya ekstensi/$_FILES['type']: cek signature gambar yang sebenarnya
        $ekstensi = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $tipeAsli = @exif_imagetype($file['tmp_name']);
        if (!isset(self::TIPE_DIIZINKAN[$ekstensi]) || $tipeAsli !== self::TIPE_DIIZINKAN[$ekstensi]) {
            Response::json(422, false, 'Berkas harus berupa gambar JPG/PNG yang valid');
        }

        if (!is_dir(self::UPLOAD_DIR)) {
            mkdir(self::UPLOAD_DIR, 0755, true);
        }

        // Nama baru yang unik; nama asli dari pengguna tidak dipakai
        $namaBaru = uniqid('foto_', true) . '.' . $ekstensi;
        if (!move_uploaded_file($file['tmp_name'], self::UPLOAD_DIR . $namaBaru)) {
            Response::json(500, false, 'Gagal menyimpan berkas');
        }
        return $namaBaru;
    }

    private function hapusBerkas(?string $nama): void
    {
        if (!$nama) return;
        $path = self::UPLOAD_DIR . basename($nama); // basename: cegah path traversal
        if (is_file($path)) {
            unlink($path);
        }
    }
}
