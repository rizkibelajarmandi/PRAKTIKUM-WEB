-- Jalankan di phpMyAdmin bila tabel belum ada
CREATE DATABASE IF NOT EXISTS si_akademik;
USE si_akademik;

CREATE TABLE IF NOT EXISTS mahasiswa (
    nim VARCHAR(20) PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    jurusan VARCHAR(100) NOT NULL,
    prodi_id INT NOT NULL,
    angkatan INT NOT NULL
);

CREATE TABLE IF NOT EXISTS dosen (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nidn VARCHAR(20) NOT NULL,
    nama VARCHAR(100) NOT NULL,
    bidang_keahlian VARCHAR(100)
);
