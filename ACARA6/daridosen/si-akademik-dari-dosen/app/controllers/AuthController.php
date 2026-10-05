<?php
namespace App\Controllers;

class AuthController {
    
    public function loginForm() {
        require_once __DIR__ . '/../Views/auth/login.php';
    }

    public function login() {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';

        // Simulasi login hardcode (Sesuai Langkah 4 BKPM)
        if ($username === 'admin' && $password === '12345') {
            $_SESSION['logged_in'] = true;
            $_SESSION['name'] = 'Admin';
            
            // TUGAS MANDIRI: Set Flash Message
            $_SESSION['flash'] = "Selamat datang, Admin";
            
            header('Location: ' . BASE_URL . '/dashboard');
            exit;
        } else {
            $_SESSION['error'] = "Username atau password salah.";
            header('Location: ' . BASE_URL . '/login');
            exit;
        }
    }

    public function logout() {
        session_unset();
        session_destroy();
        session_start(); // Mulai session baru untuk flash message
        
        // TUGAS MANDIRI: Set Flash Message Logout
        $_SESSION['flash'] = "Anda telah logout";
        
        header('Location: ' . BASE_URL . '/login');
        exit;
    }
}
?>