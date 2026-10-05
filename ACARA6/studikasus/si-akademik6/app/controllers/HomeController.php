<?php
namespace App\Controllers;

class HomeController {
    public function index() {
        header('Location: ' . BASE_URL . '/login');
        exit;
    }

    public function dashboard() {
        require_once __DIR__ . '/../Views/dashboard/index.php';
    }
}
?>