CREATE DATABASE IF NOT EXISTS si_akademik;
USE si_akademik;

CREATE TABLE IF NOT EXISTS mahasiswa (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nim VARCHAR(20) NOT NULL UNIQUE,
    nama VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL,
    jurusan VARCHAR(100) NOT NULL,
    prodi_id INT NOT NULL,
    angkatan INT NOT NULL,
    status ENUM('aktif', 'cuti', 'lulus') NOT NULL DEFAULT 'aktif'
);

SET @add_mahasiswa_email = IF(
    (SELECT COUNT(*) FROM information_schema.COLUMNS
     WHERE TABLE_SCHEMA = DATABASE()
       AND TABLE_NAME = 'mahasiswa'
       AND COLUMN_NAME = 'email') = 0,
    'ALTER TABLE mahasiswa ADD COLUMN email VARCHAR(255) NOT NULL DEFAULT '''' AFTER nama',
    'SELECT 1'
);
PREPARE add_mahasiswa_email_stmt FROM @add_mahasiswa_email;
EXECUTE add_mahasiswa_email_stmt;
DEALLOCATE PREPARE add_mahasiswa_email_stmt;

SET @add_mahasiswa_status = IF(
    (SELECT COUNT(*) FROM information_schema.COLUMNS
     WHERE TABLE_SCHEMA = DATABASE()
       AND TABLE_NAME = 'mahasiswa'
       AND COLUMN_NAME = 'status') = 0,
    'ALTER TABLE mahasiswa ADD COLUMN status ENUM(''aktif'', ''cuti'', ''lulus'') NOT NULL DEFAULT ''aktif''',
    'SELECT 1'
);
PREPARE add_mahasiswa_status_stmt FROM @add_mahasiswa_status;
EXECUTE add_mahasiswa_status_stmt;
DEALLOCATE PREPARE add_mahasiswa_status_stmt;

-- ACARA 16 (RESTful API): endpoint memakai {id}, maka tabel mahasiswa butuh kolom id.
-- Aman dijalankan ulang; untuk tabel lama (PK = nim) akan dimigrasi, nim tetap UNIQUE.
SET @add_mahasiswa_id = IF(
    (SELECT COUNT(*) FROM information_schema.COLUMNS
     WHERE TABLE_SCHEMA = DATABASE()
       AND TABLE_NAME = 'mahasiswa'
       AND COLUMN_NAME = 'id') = 0,
    'ALTER TABLE mahasiswa DROP PRIMARY KEY, ADD COLUMN id INT AUTO_INCREMENT PRIMARY KEY FIRST, ADD UNIQUE KEY uq_mahasiswa_nim (nim)',
    'SELECT 1'
);
PREPARE add_mahasiswa_id_stmt FROM @add_mahasiswa_id;
EXECUTE add_mahasiswa_id_stmt;
DEALLOCATE PREPARE add_mahasiswa_id_stmt;

CREATE TABLE IF NOT EXISTS dosen (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nidn VARCHAR(20) NOT NULL,
    nama VARCHAR(100) NOT NULL,
    bidang_keahlian VARCHAR(100)
);
