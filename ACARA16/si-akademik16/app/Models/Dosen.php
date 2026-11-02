<?php

class Dosen
{
    private $id;
    private $nidn;
    private $nama;
    private $bidang_keahlian;

    public function getId() { return $this->id; }
    
    public function getNidn() { return $this->nidn; }
    public function setNidn($nidn) { $this->nidn = $nidn; }

    public function getNama() { return $this->nama; }
    public function setNama($nama) { $this->nama = $nama; }

    public function getBidangKeahlian() { return $this->bidang_keahlian; }
    public function setBidangKeahlian($bidang) { $this->bidang_keahlian = empty($bidang) ? '-' : $bidang; }
}
?>