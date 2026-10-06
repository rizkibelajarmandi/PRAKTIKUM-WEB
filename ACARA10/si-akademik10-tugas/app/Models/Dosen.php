<?php
// app/Models/Dosen.php
// Entity Dosen: hanya menyimpan data + validasi. Query ada di DosenRepository.

class Dosen
{
    private $id;
    private $nidn;
    private $nama;
    private $bidang_keahlian;

    public function getId() {
        return $this->id;
    }

    public function getNidn() {
        return $this->nidn;
    }

    public function setNidn($nidn) {
        if (empty($nidn)) {
            throw new Exception("NIDN tidak boleh kosong!");
        }
        $this->nidn = $nidn;
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

    public function getBidangKeahlian() {
        return $this->bidang_keahlian;
    }

    public function setBidangKeahlian($bidang) {
        $this->bidang_keahlian = empty($bidang) ? '-' : $bidang;
    }
}
