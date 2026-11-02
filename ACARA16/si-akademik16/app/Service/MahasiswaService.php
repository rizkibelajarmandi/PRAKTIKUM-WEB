<?php
require_once __DIR__ . '/../Models/Mahasiswa.php';
require_once __DIR__ . '/../repository/MahasiswaRepository.php';
require_once __DIR__ . '/../Core/AppLogger.php';

class MahasiswaService
{
    private $repo;

    public function __construct(MahasiswaRepository $repo)
    {
        $this->repo = $repo;
    }

    public function getAll() { return $this->repo->getAll(); }
    public function getByNim($nim) { return $this->repo->getByNim($nim); }

    public function create(array $input): array
    {
        try {
            $errors = $this->validate($input, true);
            if (count($errors) > 0) {
                return ['success' => false, 'errors' => $errors];
            }

            $mahasiswa = new Mahasiswa('');
            $mahasiswa->setNim($input['nim']);
            $mahasiswa->setNama($input['nama']);
            $mahasiswa->setEmail($input['email']);
            $mahasiswa->setJurusan($input['jurusan']);
            $mahasiswa->setProdiId($input['prodi_id']);
            $mahasiswa->setAngkatan($input['angkatan']);
            $mahasiswa->setStatus('aktif');

            $this->repo->save($mahasiswa);
            return ['success' => true];

        } catch (PDOException $e) {
            AppLogger::log($e, 'CREATE Mahasiswa gagal');
            return [
                'success' => false,
                'errors'  => ['global' => 'Data gagal disimpan. Silakan coba lagi.']
            ];
        }
    }

    public function update(array $input): array
    {
        try {
            $errors = $this->validate($input, false);
            if (count($errors) > 0) {
                return ['success' => false, 'errors' => $errors];
            }

            $mahasiswa = $this->repo->getByNim($input['nim']);
            if (!$mahasiswa) {
                return ['success' => false, 'errors' => ['global' => 'Mahasiswa tidak ditemukan']];
            }

            $mahasiswa->setNama($input['nama']);
            $mahasiswa->setEmail($input['email']);
            $mahasiswa->setJurusan($input['jurusan']);
            $mahasiswa->setProdiId($input['prodi_id']);
            $mahasiswa->setAngkatan($input['angkatan']);
            $mahasiswa->setStatus($input['status'] ?? 'aktif');

            $this->repo->update($mahasiswa);
            return ['success' => true];

        } catch (PDOException $e) {
            AppLogger::log($e, 'UPDATE Mahasiswa gagal');
            return [
                'success' => false,
                'errors'  => ['global' => 'Data gagal diubah. Silakan coba lagi.']
            ];
        }
    }

    public function delete($nim)
    {
        try {
            return $this->repo->delete($nim);
        } catch (PDOException $e) {
            AppLogger::log($e, 'DELETE Mahasiswa gagal');
            return false;
        }
    }

    // =====================================================================
    // RESTful API (berbasis id). Setiap method mengembalikan array:
    //   ['success' => bool, 'code' => HTTP status, 'errors' => [...]|'data' => Mahasiswa]
    // =====================================================================

    public function getById(int $id)
    {
        return $this->repo->getById($id);
    }

    public function createApi(array $input): array
    {
        try {
            $check = $this->validateApi($input, null);
            if ($check['errors']) {
                return ['success' => false, 'code' => $check['conflict'] && count($check['errors']) === 1 ? 409 : 422, 'errors' => $check['errors']];
            }

            $m = new Mahasiswa();
            $this->fillFromInput($m, $check['clean'], 'aktif');
            $this->repo->save($m);

            return ['success' => true, 'code' => 201, 'data' => $this->repo->getById($this->repo->lastInsertId())];
        } catch (PDOException $e) {
            AppLogger::log($e, 'API CREATE Mahasiswa gagal');
            return ['success' => false, 'code' => 500, 'errors' => ['global' => 'Data gagal disimpan.']];
        }
    }

    public function updateApi(int $id, array $input): array
    {
        try {
            $m = $this->repo->getById($id);
            if (!$m) {
                return ['success' => false, 'code' => 404, 'errors' => ['id' => 'Mahasiswa dengan ID ' . $id . ' tidak ditemukan.']];
            }

            $check = $this->validateApi($input, $id);
            if ($check['errors']) {
                return ['success' => false, 'code' => $check['conflict'] && count($check['errors']) === 1 ? 409 : 422, 'errors' => $check['errors']];
            }

            $this->fillFromInput($m, $check['clean'], $m->getStatus() ?: 'aktif');
            $this->repo->updateById($id, $m);

            return ['success' => true, 'code' => 200, 'data' => $this->repo->getById($id)];
        } catch (PDOException $e) {
            AppLogger::log($e, 'API UPDATE Mahasiswa gagal');
            return ['success' => false, 'code' => 500, 'errors' => ['global' => 'Data gagal diubah.']];
        }
    }

    public function deleteApi(int $id): array
    {
        try {
            if (!$this->repo->deleteById($id)) {
                return ['success' => false, 'code' => 404, 'errors' => ['id' => 'Mahasiswa dengan ID ' . $id . ' tidak ditemukan.']];
            }
            return ['success' => true, 'code' => 200];
        } catch (PDOException $e) {
            AppLogger::log($e, 'API DELETE Mahasiswa gagal');
            return ['success' => false, 'code' => 500, 'errors' => ['global' => 'Data gagal dihapus.']];
        }
    }

    private function fillFromInput(Mahasiswa $m, array $d, string $defaultStatus): void
    {
        $m->setNim($d['nim']);
        $m->setNama($d['nama']);
        $m->setEmail($d['email']);
        $m->setJurusan($d['jurusan']);
        $m->setProdiId($d['prodi_id']);
        $m->setAngkatan($d['angkatan']);
        $m->setStatus($d['status'] ?? $defaultStatus);
    }

    // Validasi payload API. Semua field wajib ada (POST dan PUT = replace penuh).
    private function validateApi(array $input, ?int $ignoreId): array
    {
        $errors = [];
        $conflict = false;
        $clean = [];

        // Field teks wajib
        $labels = ['nim' => 'NIM', 'nama' => 'Nama', 'email' => 'Email', 'jurusan' => 'Jurusan'];
        foreach ($labels as $key => $label) {
            $val = $input[$key] ?? null;
            if (!is_string($val) && !is_int($val)) {
                $val = '';
            }
            $val = trim((string) $val);
            if ($val === '') {
                $errors[$key] = $label . ' wajib diisi.';
            } else {
                $clean[$key] = $val;
            }
        }

        if (isset($clean['nim']) && strlen($clean['nim']) > 20) {
            $errors['nim'] = 'NIM maksimal 20 karakter.';
        }
        if (isset($clean['email']) && !filter_var($clean['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Format email tidak valid.';
        }

        // Field angka wajib
        foreach (['prodi_id' => 'Prodi ID', 'angkatan' => 'Angkatan'] as $key => $label) {
            $val = $input[$key] ?? null;
            if ($val === null || $val === '') {
                $errors[$key] = $label . ' wajib diisi.';
            } elseif ((!is_int($val) && !(is_string($val) && ctype_digit($val))) || (int) $val <= 0) {
                $errors[$key] = $label . ' harus berupa angka bulat positif.';
            } else {
                $clean[$key] = (int) $val;
            }
        }
        if (isset($clean['angkatan']) && ($clean['angkatan'] < 1900 || $clean['angkatan'] > 2100)) {
            $errors['angkatan'] = 'Angkatan harus 4 digit tahun (1900-2100).';
        }

        // Status opsional
        if (array_key_exists('status', $input)) {
            if (!in_array($input['status'], ['aktif', 'cuti', 'lulus'], true)) {
                $errors['status'] = 'Status harus salah satu dari: aktif, cuti, lulus.';
            } else {
                $clean['status'] = $input['status'];
            }
        }

        // NIM unik
        if (!isset($errors['nim']) && isset($clean['nim']) && $this->repo->nimExists($clean['nim'], $ignoreId)) {
            $errors['nim'] = 'NIM sudah terdaftar.';
            $conflict = true;
        }

        return ['errors' => $errors, 'conflict' => $conflict, 'clean' => $clean];
    }

    private function validate(array $input, bool $isCreate): array
    {
        $errors = [];

        if ($isCreate && empty($input['nim'])) {
            $errors['nim'] = 'NIM wajib diisi.';
        }
        if (empty($input['nama'])) {
            $errors['nama'] = 'Nama wajib diisi.';
        }
        
        if (empty($input['email'])) {
            $errors['email'] = 'Email wajib diisi.';
        } elseif (!filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Format email tidak valid.';
        }

        if (empty($input['jurusan'])) {
            $errors['jurusan'] = 'Jurusan wajib diisi.';
        }
        if (empty($input['prodi_id'])) {
            $errors['prodi_id'] = 'Prodi ID wajib diisi.';
        }
        if (empty($input['angkatan'])) {
            $errors['angkatan'] = 'Angkatan wajib diisi.';
        }
        if (!$isCreate && isset($input['status']) && !in_array($input['status'], ['aktif', 'cuti', 'lulus'], true)) {
            $errors['status'] = 'Status mahasiswa tidak valid.';
        }

        if ($isCreate && !empty($input['nim']) && $this->repo->getByNim($input['nim'])) {
            $errors['nim'] = 'NIM sudah terdaftar.';
        }

        return $errors;
    }
}
?>