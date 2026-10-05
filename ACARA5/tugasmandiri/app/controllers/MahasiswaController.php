<?php
class MahasiswaController {
    public function index() {
        echo "Ini adalah Halaman Daftar Mahasiswa (Tugas Mandiri)";
    }

     public function create() {
        echo "Ini adalah Halaman Tambah Mahasiswa (Create)";
    }

    public function show($id) {
        echo "Menampilkan detail Mahasiswa dengan ID: " . htmlspecialchars($id);
    }
}