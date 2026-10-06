<?php
// app/Core/BaseController.php
// parent class semua controller: menyediakan method view() dan redirect()
// agar tidak perlu copy-paste di setiap controller

class BaseController
{
    protected function view($path, $data = [])
    {
        $file = __DIR__ . '/../Views/' . $path . '.php';

        if (!file_exists($file)) {
            http_response_code(500);
            echo "View tidak ditemukan: " . htmlspecialchars($path);
            return;
        }

        extract($data);  
        require $file;
    }

    protected function redirect($path)
    {
        header('Location: ' . BASE_URL . $path);
        exit;
    }
}
