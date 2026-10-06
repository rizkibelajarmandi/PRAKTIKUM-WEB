<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Informasi Akademik</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <h1 class="text-center">POLITEKNIK NEGERI JEMBER</h1>
        <h2 class="text-center mb-4">Sistem Informasi Akademik</h2>
        
        <div class="mx-auto" style="max-width: 400px;">
            <div style="border: 1px solid #ddd; padding: 20px; border-radius: 4px; background: white;">
                <h3 class="text-center mb-3">Login</h3>
                <form method="post" action="<?= BASE_URL ?>/login">
                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" name="username" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Login</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>