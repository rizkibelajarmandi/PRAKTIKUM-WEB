<?php
require_once __DIR__ . '/../Core/BaseModel.php';

class MahasiswaRepository extends BaseModel
{
    public function getAll()
    {
        $stmt = $this->pdo->query(
            "SELECT id, nim, nama, email, jurusan, prodi_id, angkatan, status
             FROM mahasiswa ORDER BY nama ASC"
        );
        return $stmt->fetchAll(PDO::FETCH_CLASS, 'Mahasiswa');
    }

    public function getByNim($nim)
    {
        $stmt = $this->pdo->prepare(
            "SELECT id, nim, nama, email, jurusan, prodi_id, angkatan, status
             FROM mahasiswa WHERE nim = :nim"
        );
        $stmt->execute(['nim' => $nim]);
        $stmt->setFetchMode(PDO::FETCH_CLASS, 'Mahasiswa');
        return $stmt->fetch();
    }

    public function save($mahasiswa)
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO mahasiswa (nim, nama, email, jurusan, prodi_id, angkatan, status) 
             VALUES (:nim, :nama, :email, :jurusan, :prodi_id, :angkatan, :status)"
        );
        return $stmt->execute([
            'nim'      => $mahasiswa->getNim(),
            'nama'     => $mahasiswa->getNama(),
            'email'    => $mahasiswa->getEmail(),
            'jurusan'  => $mahasiswa->getJurusan(),
            'prodi_id' => $mahasiswa->getProdiId(),
            'angkatan' => $mahasiswa->getAngkatan(),
            'status'   => $mahasiswa->getStatus()
        ]);
    }

    public function update($mahasiswa)
    {
        $stmt = $this->pdo->prepare(
            "UPDATE mahasiswa 
             SET nama = :nama, email = :email, jurusan = :jurusan, 
                 prodi_id = :prodi_id, angkatan = :angkatan, status = :status 
             WHERE nim = :nim"
        );
        return $stmt->execute([
            'nim'      => $mahasiswa->getNim(),
            'nama'     => $mahasiswa->getNama(),
            'email'    => $mahasiswa->getEmail(),
            'jurusan'  => $mahasiswa->getJurusan(),
            'prodi_id' => $mahasiswa->getProdiId(),
            'angkatan' => $mahasiswa->getAngkatan(),
            'status'   => $mahasiswa->getStatus()
        ]);
    }

    public function delete($nim)
    {
        $stmt = $this->pdo->prepare("DELETE FROM mahasiswa WHERE nim = :nim");
        return $stmt->execute(['nim' => $nim]);
    }

    // ===== Dipakai oleh RESTful API (berbasis id) =====

    public function getById(int $id)
    {
        $stmt = $this->pdo->prepare(
            "SELECT id, nim, nama, email, jurusan, prodi_id, angkatan, status
             FROM mahasiswa WHERE id = :id"
        );
        $stmt->execute(['id' => $id]);
        $stmt->setFetchMode(PDO::FETCH_CLASS, 'Mahasiswa');
        return $stmt->fetch();
    }

    public function lastInsertId(): int
    {
        return (int) $this->pdo->lastInsertId();
    }

    // true jika NIM sudah dipakai mahasiswa lain (selain $exceptId)
    public function nimExists(string $nim, ?int $exceptId = null): bool
    {
        $sql = "SELECT COUNT(*) FROM mahasiswa WHERE nim = :nim";
        $params = ['nim' => $nim];
        if ($exceptId !== null) {
            $sql .= " AND id <> :id";
            $params['id'] = $exceptId;
        }
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn() > 0;
    }

    public function updateById(int $id, $mahasiswa)
    {
        $stmt = $this->pdo->prepare(
            "UPDATE mahasiswa
             SET nim = :nim, nama = :nama, email = :email, jurusan = :jurusan,
                 prodi_id = :prodi_id, angkatan = :angkatan, status = :status
             WHERE id = :id"
        );
        return $stmt->execute([
            'id'       => $id,
            'nim'      => $mahasiswa->getNim(),
            'nama'     => $mahasiswa->getNama(),
            'email'    => $mahasiswa->getEmail(),
            'jurusan'  => $mahasiswa->getJurusan(),
            'prodi_id' => $mahasiswa->getProdiId(),
            'angkatan' => $mahasiswa->getAngkatan(),
            'status'   => $mahasiswa->getStatus()
        ]);
    }

    public function deleteById(int $id)
    {
        $stmt = $this->pdo->prepare("DELETE FROM mahasiswa WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->rowCount() > 0;
    }
}
?>
