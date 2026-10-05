<?php
$username = $_SESSION['user'] ?? 'User';
$appName = defined('APP_NAME') ? APP_NAME : 'Sistem Informasi Akademik';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - <?= htmlspecialchars($appName) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <!-- Bagian Header (Harus di tengah) -->
        <h1 class="text-center">POLITEKNIK NEGERI JEMBER</h1>
        <h2 class="text-center mb-4"><?= htmlspecialchars($appName) ?></h2>

        <!-- Card Utama -->
        <div style="border: 1px solid #ddd; padding: 20px; border-radius: 4px; background: white;">
            <h3>Selamat datang, <?= htmlspecialchars($username) ?></h3>
            <hr>

            <p class="mt-3 mb-2"><strong>Menu:</strong></p>
            <ul>
                <li><a href="<?= BASE_URL ?>/index.php?url=mahasiswa">Mahasiswa</a></li>
                <li><a href="<?= BASE_URL ?>/index.php?url=dosen">Dosen</a></li>
                <li><a href="<?= BASE_URL ?>/index.php?url=logout">Logout</a></li>
            </ul>
        </div>
    </div>
</body>
</html>