<?php
require_once __DIR__ . '/../Models/Mahasiswa.php';
require_once __DIR__ . '/../repository/MahasiswaRepository.php';

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
        $errors = $this->validate($input, true);
        if (count($errors) > 0) {
            return ['success' => false, 'errors' => $errors];
        }

        $mahasiswa = new Mahasiswa('');
        $mahasiswa->setNim($input['nim']);
        $mahasiswa->setNama($input['nama']);
        $mahasiswa->setJurusan($input['jurusan']);
        $mahasiswa->setProdiId($input['prodi_id']);
        $mahasiswa->setAngkatan($input['angkatan']);

        $this->repo->save($mahasiswa);
        return ['success' => true];
    }

    public function update(array $input): array
    {
        $errors = $this->validate($input, false);
        if (count($errors) > 0) {
            return ['success' => false, 'errors' => $errors];
        }

        $mahasiswa = $this->repo->getByNim($input['nim']);
        if (!$mahasiswa) {
            return ['success' => false, 'errors' => ['global' => 'Mahasiswa tidak ditemukan']];
        }

        $mahasiswa->setNama($input['nama']);
        $mahasiswa->setJurusan($input['jurusan']);
        $mahasiswa->setProdiId($input['prodi_id']);
        $mahasiswa->setAngkatan($input['angkatan']);

        $this->repo->update($mahasiswa);
        return ['success' => true];
    }

    public function delete($nim)
    {
        return $this->repo->delete($nim);
    }

    private function validate(array $input, bool $isCreate): array
    {
        $errors = [];

        if (empty($input['nim']) && $isCreate) {
            $errors['nim'] = 'NIM wajib diisi.';
        }
        if (empty($input['nama'])) {
            $errors['nama'] = 'Nama wajib diisi.';
        }
        if (empty($input['prodi_id'])) {
            $errors['prodi_id'] = 'Prodi ID wajib diisi.';
        }
        if (empty($input['angkatan'])) {
            $errors['angkatan'] = 'Angkatan wajib diisi.';
        }

        // Validasi NIM ganda (hanya saat create)
        if ($isCreate && !empty($input['nim']) && $this->repo->getByNim($input['nim'])) {
            $errors['nim'] = 'NIM sudah terdaftar.';
        }

        return $errors;
    }
}
?>