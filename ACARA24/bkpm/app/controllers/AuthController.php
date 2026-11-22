<?php
class AuthController
{
    private User $model;
    public function __construct()
    {
        $this->model = new User();
    }
    public function register(): void
    {
        $data = json_decode(file_get_contents('php://input'), true);
        if (
            empty($data['username']) || empty($data['email']) ||
            empty($data['password'])
        ) {
            Response::json(400, false, 'Username, email, dan password wajib
diisi');
        }
        $data['password'] = password_hash(
            $data['password'],
            PASSWORD_DEFAULT
        );
        try {
            $id = $this->model->create($data);
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                Response::json(409, false, 'Username atau email sudah terdaftar');
            }
            Response::json(500, false, 'Terjadi kesalahan pada server');
        }

        Response::json(201, true, 'Registrasi berhasil', ['id' => $id]);
    }
    public function login(): void
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $user = $this->model->findByUsername($data['username'] ?? '');
        if (!$user || !password_verify(
            $data['password'] ?? '',
            $user['password']
        )) {
            Response::json(401, false, 'Username atau password salah');
        }
        $token = Auth::generateToken(['id' => $user['id'], 'role' =>
        $user['role']]);
        Response::json(200, true, 'Login berhasil', ['token' => $token, 'role'
        => $user['role']]);
    }
}
