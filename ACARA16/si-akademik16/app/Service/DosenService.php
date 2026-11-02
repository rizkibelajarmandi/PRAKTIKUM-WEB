<?php
require_once __DIR__ . '/../Models/Dosen.php';
require_once __DIR__ . '/../repository/DosenRepository.php';

class DosenService
{
    private $repo;

    public function __construct(DosenRepository $repo)
    {
        $this->repo = $repo;
    }

    public function getAll() { return $this->repo->getAll(); }
    public function getById($id) { return $this->repo->getById($id); }

    public function create(array $input): array
    {
        $errors = $this->validate($input);
        if (count($errors) > 0) {
            return ['success' => false, 'errors' => $errors];
        }

        $dosen = new Dosen('');
        $dosen->setNidn($input['nidn']);
        $dosen->setNama($input['nama']);
        $dosen->setBidangKeahlian($input['bidang_keahlian']);

        $this->repo->save($dosen);
        return ['success' => true];
    }

    public function update(array $input): array
    {
        $errors = $this->validate($input);
        if (count($errors) > 0) {
            return ['success' => false, 'errors' => $errors];
        }

        $dosen = $this->repo->getById($input['id']);
        if (!$dosen) {
            return ['success' => false, 'errors' => ['global' => 'Dosen tidak ditemukan']];
        }

        $dosen->setNidn($input['nidn']);
        $dosen->setNama($input['nama']);
        $dosen->setBidangKeahlian($input['bidang_keahlian']);

        $this->repo->update($dosen);
        return ['success' => true];
    }

    public function delete($id)
    {
        return $this->repo->delete($id);
    }

    private function validate(array $input): array
    {
        $errors = [];
        if (empty($input['nidn'])) $errors['nidn'] = 'NIDN wajib diisi.';
        if (empty($input['nama'])) $errors['nama'] = 'Nama wajib diisi.';
        return $errors;
    }
}
?>