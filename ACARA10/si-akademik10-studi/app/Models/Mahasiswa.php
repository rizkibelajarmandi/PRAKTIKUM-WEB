<?php

class Mahasiswa {
    private $nim;
    private $nama;
    private $jurusan;
    private $prodi_id;
    private $angkatan;

    public function getNim() {
        return $this->nim;
    }

    public function setNim($nim) {
        if (empty($nim)) {
            throw new Exception("NIM tidak boleh kosong!");
        }
        $this->nim = $nim;
    }

    public function getNama() {
        return $this->nama;
    }

    public function setNama($nama) {
        if (empty($nama)) {
            throw new Exception("Nama tidak boleh kosong!");
        }
        $this->nama = $nama;
    }

    public function getJurusan() {
        return $this->jurusan;
    }

    public function setJurusan($jurusan) {
        $this->jurusan = empty($jurusan) ? '-' : $jurusan;
    }

    public function getProdiId() {
        return $this->prodi_id;
    }

    public function setProdiId($prodi_id) {
        if (empty($prodi_id)) {
            throw new Exception("Prodi ID tidak boleh kosong!");
        }
        $this->prodi_id = $prodi_id;
    }

    public function getAngkatan() {
        return $this->angkatan;
    }

    public function setAngkatan($angkatan) {
        if (empty($angkatan)) {
            throw new Exception("Angkatan tidak boleh kosong!");
        }
        $this->angkatan = $angkatan;
    }
}
?>