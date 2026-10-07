CREATE DATABASE IF NOT EXISTS si_akademik;
USE si_akademik;

CREATE TABLE IF NOT EXISTS mahasiswa (
    nim VARCHAR(20) PRIMARY KEY,
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

CREATE TABLE IF NOT EXISTS dosen (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nidn VARCHAR(20) NOT NULL,
    nama VARCHAR(100) NOT NULL,
    bidang_keahlian VARCHAR(100)
);
