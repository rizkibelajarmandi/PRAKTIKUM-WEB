<?php
require_once __DIR__ . '/../Core/BaseController.php';

class AuthController extends BaseController
{
    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $_SESSION['user'] = $_POST['username'];
            $this->redirect('/dashboard');
        }
        $this->view('auth/login');
    }

    public function logout()
    {
        session_destroy();
        $this->redirect('/login');
    }
}
