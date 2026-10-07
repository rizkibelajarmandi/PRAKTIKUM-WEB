<?php

class Mahasiswa {
    private $id;
    private $nim;
    private $nama;
    private $email;
    private $jurusan;
    private $prodi_id;
    private $angkatan;
    private $status;

    public function getId() { return $this->id; }
    
    public function getNim() { return $this->nim; }
    public function setNim($nim) { $this->nim = $nim; }

    public function getNama() { return $this->nama; }
    public function setNama($nama) { $this->nama = $nama; }

    public function getEmail() { return $this->email; }
    public function setEmail($email) { $this->email = $email; }

    public function getJurusan() { return $this->jurusan; }
    public function setJurusan($jurusan) { $this->jurusan = $jurusan; }

    public function getProdiId() { return $this->prodi_id; }
    public function setProdiId($prodi_id) { $this->prodi_id = $prodi_id; }

    public function getAngkatan() { return $this->angkatan; }
    public function setAngkatan($angkatan) { $this->angkatan = $angkatan; }

    public function getStatus() { return $this->status; }
    public function setStatus($status) { $this->status = $status; }
}
?>