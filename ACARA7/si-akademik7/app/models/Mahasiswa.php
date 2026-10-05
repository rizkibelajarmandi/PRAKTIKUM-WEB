<?php
namespace App\Models;

use App\Core\Model;
use PDO;

class Mahasiswa extends Model {
    
    public static function all() {
        $instance = new self();
        $stmt = $instance->db->query("
            SELECT m.nim, m.nama, m.status, p.nama AS prodi 
            FROM mahasiswa m 
            LEFT JOIN prodi p ON m.prodi_id = p.id
        ");
        return $stmt->fetchAll();
    }

    public static function findByNim($nim) {
        $instance = new self();
        $stmt = $instance->db->prepare("
            SELECT m.nim, m.nama, m.status, p.nama AS prodi 
            FROM mahasiswa m 
            LEFT JOIN prodi p ON m.prodi_id = p.id 
            WHERE m.nim = :nim
        ");
        $stmt->execute(['nim' => $nim]);
        return $stmt->fetch();
    }
}