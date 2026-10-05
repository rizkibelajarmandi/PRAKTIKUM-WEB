<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login - Sistem Informasi Akademik</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <h1 class="text-center">POLITEKNIK NEGERI JEMBER</h1>
        <h1 class="text-center mb-4">Sistem Informasi Akademik</h1>

        <div class="card mx-auto" style="max-width: 400px;">
            <div class="card-body">
                <h4 class="card-title text-center mb-4">Login</h4>
                
                <!-- Menampilkan Flash Message Logout (Tugas Mandiri) -->
                <?php if (isset($_SESSION['flash'])): ?>
                    <div class="alert alert-info"><?= $_SESSION['flash']; unset($_SESSION['flash']); ?></div>
                <?php endif; ?>

                <!-- Menampilkan Error Login -->
                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
                <?php endif; ?>

                <form action="<?= BASE_URL ?>/login/process" method="POST">
                    <div class="mb-3">
                        <label>Username</label>
                        <input type="text" name="username" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Login</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>