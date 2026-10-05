<?php
// app/Repository/MahasiswaRepository.php

class MahasiswaRepository {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getAll() {
        $stmt = $this->pdo->query("SELECT * FROM mahasiswa ORDER BY nama ASC");
        return $stmt->fetchAll(PDO::FETCH_CLASS, 'Mahasiswa');
    }

    public function getByNim($nim) {
        $stmt = $this->pdo->prepare("SELECT * FROM mahasiswa WHERE nim = :nim");
        $stmt->execute(['nim' => $nim]);
        $stmt->setFetchMode(PDO::FETCH_CLASS, 'Mahasiswa');
        return $stmt->fetch();
    }

    public function save($mahasiswa) {
        $stmt = $this->pdo->prepare("INSERT INTO mahasiswa (nim, nama, jurusan, prodi_id, angkatan) VALUES (:nim, :nama, :jurusan, :prodi_id, :angkatan)");
        return $stmt->execute([
            'nim'      => $mahasiswa->getNim(),
            'nama'     => $mahasiswa->getNama(),
            'jurusan'  => $mahasiswa->getJurusan(),
            'prodi_id' => $mahasiswa->getProdiId(),
            'angkatan' => $mahasiswa->getAngkatan()
        ]);
    }

    public function update($mahasiswa) {
        $stmt = $this->pdo->prepare("UPDATE mahasiswa SET nama = :nama, jurusan = :jurusan, prodi_id = :prodi_id, angkatan = :angkatan WHERE nim = :nim");
        return $stmt->execute([
            'nim'      => $mahasiswa->getNim(),
            'nama'     => $mahasiswa->getNama(),
            'jurusan'  => $mahasiswa->getJurusan(),
            'prodi_id' => $mahasiswa->getProdiId(),
            'angkatan' => $mahasiswa->getAngkatan()
        ]);
    }

    public function delete($nim) {
        $stmt = $this->pdo->prepare("DELETE FROM mahasiswa WHERE nim = :nim");
        return $stmt->execute(['nim' => $nim]);
    }
}
?>