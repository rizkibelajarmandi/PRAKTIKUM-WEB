<?php
// app/repository/DosenRepository.php
// Seluruh query database untuk Dosen ada di sini (Repository Pattern).

require_once __DIR__ . '/../Core/BaseModel.php';

class DosenRepository extends BaseModel
{
    public function getAll()
    {
        $stmt = $this->pdo->query("SELECT * FROM dosen ORDER BY nama ASC");
        return $stmt->fetchAll(PDO::FETCH_CLASS, 'Dosen');
    }

    public function getById($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM dosen WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $stmt->setFetchMode(PDO::FETCH_CLASS, 'Dosen');
        return $stmt->fetch();
    }

    public function save($dosen)
    {
        $stmt = $this->pdo->prepare("INSERT INTO dosen (nidn, nama, bidang_keahlian) VALUES (:nidn, :nama, :bidang_keahlian)");
        return $stmt->execute([
            'nidn'            => $dosen->getNidn(),
            'nama'            => $dosen->getNama(),
            'bidang_keahlian' => $dosen->getBidangKeahlian()
        ]);
    }

    public function update($dosen)
    {
        $stmt = $this->pdo->prepare("UPDATE dosen SET nidn = :nidn, nama = :nama, bidang_keahlian = :bidang_keahlian WHERE id = :id");
        return $stmt->execute([
            'id'              => $dosen->getId(),
            'nidn'            => $dosen->getNidn(),
            'nama'            => $dosen->getNama(),
            'bidang_keahlian' => $dosen->getBidangKeahlian()
        ]);
    }

    public function delete($id)
    {
        $stmt = $this->pdo->prepare("DELETE FROM dosen WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
}
