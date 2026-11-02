<?php
// app/Core/BaseController.php
// Parent class semua Controller: menyediakan view() dan redirect()
// agar tidak perlu copy-paste di setiap Controller.

class BaseController
{
    /**
     * Menampilkan file view dan mengirim data ke dalamnya.
     * Contoh: $this->view('mahasiswa/index', ['mahasiswa' => $data]);
     */
    protected function view($path, $data = [])
    {
        $file = __DIR__ . '/../Views/' . $path . '.php';

        if (!file_exists($file)) {
            http_response_code(500);
            echo "View tidak ditemukan: " . htmlspecialchars($path);
            return;
        }

        extract($data);   // ['mahasiswa' => ...] menjadi variabel $mahasiswa
        require $file;
    }

    /**
     * Mengarahkan browser ke URL lain di dalam aplikasi.
     * Contoh: $this->redirect('/mahasiswa');
     */
    protected function redirect($path)
    {
        header('Location: ' . BASE_URL . $path);
        exit;
    }
}
