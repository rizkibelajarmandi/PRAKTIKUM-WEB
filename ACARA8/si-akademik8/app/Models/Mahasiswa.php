<?php
class Mahasiswa {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getAll() {
        $stmt = $this->pdo->query("SELECT * FROM mahasiswa ORDER BY nama ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getByNim($nim) {
        $stmt = $this->pdo->prepare("SELECT * FROM mahasiswa WHERE nim = :nim");
        $stmt->execute(['nim' => $nim]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>