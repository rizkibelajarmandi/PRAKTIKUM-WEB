-- Acara 26, Langkah 1: tambah kolom foto pada tabel mahasiswa.
-- Jalankan SEKALI di phpMyAdmin > db_workshop > tab SQL.
ALTER TABLE mahasiswa ADD COLUMN foto VARCHAR(255) DEFAULT NULL AFTER prodi;

-- Cek hasilnya
DESCRIBE mahasiswa;
