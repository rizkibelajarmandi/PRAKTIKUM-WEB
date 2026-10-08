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