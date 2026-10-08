<?php
require_once __DIR__ . '/../Core/BaseController.php';

class AuthController extends BaseController
{
    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';

            // Kredensial statis (karena belum ada tabel users)
            $valid_username = 'admin';
            $valid_password = '12345';

            // Cek apakah username DAN password cocok
            if ($username === $valid_username && $password === $valid_password) {
                $_SESSION['user'] = $username;
                $this->redirect('/dashboard');
            } else {
                // Jika salah, set flash message error
                $_SESSION['flash'] = [
                    'type' => 'danger',
                    'message' => 'Username atau password salah.'
                ];
                $this->redirect('/login'); // Redirect balik ke login
            }
        }

        // Tampilkan halaman login (jika method GET)
        $this->view('auth/login');
    }

    public function logout()
    {
        session_destroy();
        $this->redirect('/login');
    }
}
?>