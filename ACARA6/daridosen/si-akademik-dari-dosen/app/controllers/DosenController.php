<?php
namespace App\Controllers;

use App\Models\Dosen;

class DosenController {
    public function index() {
        $dosen = Dosen::getAll();

        require_once __DIR__ . '/../views/dosen/index.php';
    }

    // GET /dosen/detail?nip=001
    public function detail() {
        $nip = $_GET['nip'] ?? '';
        $dosen = Dosen::findByNip($nip);

        if (!$dosen) {
            http_response_code(404);
            echo "Dosen dengan NIP '{$nip}' tidak ditemukan.";
            return;
        }

        require_once __DIR__ . '/../views/dosen/detail.php';
    }
}
?>
