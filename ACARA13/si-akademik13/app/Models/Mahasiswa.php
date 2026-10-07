<?php

class Mahasiswa {
    private $nim;
    private $nama;
    private $jurusan;
    private $prodi_id;
    private $angkatan;

    public function getNim() { return $this->nim; }
    public function setNim($nim) { $this->nim = $nim; }

    public function getNama() { return $this->nama; }
    public function setNama($nama) { $this->nama = $nama; }

    public function getJurusan() { return $this->jurusan; }
    public function setJurusan($jurusan) { $this->jurusan = empty($jurusan) ? '-' : $jurusan; }

    public function getProdiId() { return $this->prodi_id; }
    public function setProdiId($prodi_id) { $this->prodi_id = $prodi_id; }

    public function getAngkatan() { return $this->angkatan; }
    public function setAngkatan($angkatan) { $this->angkatan = $angkatan; }
}
?>