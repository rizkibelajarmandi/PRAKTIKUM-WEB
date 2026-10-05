<?php
namespace App\Models;

class Mahasiswa {
    public static function getAll() {
        return [
            ['nim' => '23001', 'nama' => 'Andi',  'prodi' => 'Teknik Informatika'],
            ['nim' => '23002', 'nama' => 'Budi',  'prodi' => 'Teknik Informatika'],
            ['nim' => '23003', 'nama' => 'Citra', 'prodi' => 'Sistem Informasi'],
            ['nim' => '23004', 'nama' => 'Ajes',  'prodi' => 'Teknik Informatika'],
            ['nim' => '23005', 'nama' => 'Maul',  'prodi' => 'Teknik Informatika'],
            ['nim' => '23006', 'nama' => 'Rizky', 'prodi' => 'Teknik Informatika'],
        ];
    }

    // Cari 1 mahasiswa berdasarkan NIM, null kalau tidak ketemu
    public static function findByNim($nim) {
        foreach (self::getAll() as $mhs) {
            if ($mhs['nim'] == $nim) {
                return $mhs;
            }
        }
        return null;
    }
}
?>
