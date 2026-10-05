<?php
class AuthController {
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $_SESSION['user'] = $_POST['username'];
            header('Location: ' . BASE_URL . '/index.php?url=dashboard');
            exit;
        }
        require_once __DIR__ . '/../Views/auth/login.php';
    }

    public function logout() {
        session_destroy();
        header('Location: ' . BASE_URL . '/index.php?url=login');
        exit;
    }
}
?>