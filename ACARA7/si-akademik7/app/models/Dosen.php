<?php
namespace App\Models;

class Dosen {
    public static function getAll() {
        return [
            ['nip' => '001', 'nama' => 'Bu Puji',  'prodi' => 'Teknik Informatika'],
            ['nip' => '002', 'nama' => 'Bu Ulfa',  'prodi' => 'Sistem Informasi'],
            ['nip' => '003', 'nama' => 'Pak Fikri', 'prodi' => 'Konsep Jaringan Komputer'],
            ['nip' => '004', 'nama' => 'Pak Radit', 'prodi' => 'Workshop Mobile Advance'],
        ];
    }

    public static function findByNip($nip) {
        foreach (self::getAll() as $d) {
            if ($d['nip'] == $nip) {
                return $d;
            }
        }
        return null;
    }
}
?>
