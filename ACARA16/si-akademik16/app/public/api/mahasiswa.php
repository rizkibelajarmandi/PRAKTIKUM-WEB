<?php
// 1. Set header agar output berupa JSON
header('Content-Type: application/json; charset=utf-8');

// 2. Panggil Config & Service yang sudah ada 
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../repository/MahasiswaRepository.php';
require_once __DIR__ . '/../../Service/MahasiswaService.php';

// 3. Inisialisasi Service
try {
    $repo = new MahasiswaRepository($pdo);
    $service = new MahasiswaService($repo);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Koneksi database gagal']);
    exit;
}

// 4. Format Response Standard
$response = [
    'success' => false,
    'message' => 'Endpoint tidak ditemukan',
    'data' => null
];

$method = $_SERVER['REQUEST_METHOD'];

// 5. Logika API
if ($method === 'GET') {
    if (isset($_GET['nim'])) {
        // --- GET Detail (Berdasarkan NIM) ---
        $nim = $_GET['nim'];
        $mahasiswa = $service->getByNim($nim);
        
        if ($mahasiswa) {
            $response['success'] = true;
            $response['message'] = 'Detail data mahasiswa berhasil diambil';
            $response['data'] = [
                'nim' => $mahasiswa->getNim(),
                'nama' => $mahasiswa->getNama(),
                'email' => $mahasiswa->getEmail(),
                'jurusan' => $mahasiswa->getJurusan(),
                'prodi_id' => $mahasiswa->getProdiId(),
                'angkatan' => $mahasiswa->getAngkatan(),
                'status' => $mahasiswa->getStatus()
            ];
        } else {
            http_response_code(404);
            $response['message'] = 'Mahasiswa tidak ditemukan';
        }
    } else {
        // --- GET All (Semua Mahasiswa) ---
        $listMahasiswa = $service->getAll();
        $data = [];
        
        foreach ($listMahasiswa as $mhs) {
            $data[] = [
                'nim' => $mhs->getNim(),
                'nama' => $mhs->getNama(),
                'email' => $mhs->getEmail(),
                'jurusan' => $mhs->getJurusan(),
                'prodi_id' => $mhs->getProdiId(),
                'angkatan' => $mhs->getAngkatan(),
                'status' => $mhs->getStatus()
            ];
        }
        
        $response['success'] = true;
        $response['message'] = 'Data semua mahasiswa berhasil diambil';
        $response['data'] = $data;
    }
} elseif ($method === 'POST') {
    // --- POST (Tambah Mahasiswa via JSON) ---
    $inputJSON = file_get_contents('php://input');
    $input = json_decode($inputJSON, true);

    if ($input === null) {
        http_response_code(400);
        $response['message'] = 'Format JSON tidak valid';
    } else {
        $result = $service->create($input);
        
        if ($result['success']) {
            http_response_code(201); // Created
            $response['success'] = true;
            $response['message'] = 'Data mahasiswa berhasil ditambahkan';
        } else {
            http_response_code(422); // Unprocessable Entity (Validasi Gagal)
            $response['message'] = 'Validasi gagal';
            $response['errors'] = $result['errors']; // Tampilkan error dari Service
        }
    }
} else {
    http_response_code(405); // Method Not Allowed
    $response['message'] = 'Method HTTP tidak diizinkan';
}

// 6. Cetak Output JSON
echo json_encode($response, JSON_PRETTY_PRINT);
?>