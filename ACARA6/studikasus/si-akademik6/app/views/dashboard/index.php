<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5" style="max-width: 700px;">
        <h1 class="text-center">POLITEKNIK NEGERI JEMBER</h1>
        <h1 class="text-center mb-4">Sistem Informasi Akademik</h1>

        <div style="border: 1px solid #ddd; padding: 20px; border-radius: 4px;">

            <?php if (isset($_SESSION['flash'])): ?>
                <div class="alert alert-success"><?= $_SESSION['flash']; unset($_SESSION['flash']); ?></div>
            <?php endif; ?>

            <!-- <p class="mb-3">Selamat datang, <?= htmlspecialchars($_SESSION['name']); ?>.</p> -->

            <p class="mb-1"><strong>Menu:</strong></p>
            <ul>
                <li><a href="<?= BASE_URL ?>/mahasiswa">Mahasiswa</a></li>
                <li><a href="<?= BASE_URL ?>/logout">Logout</a></li>
            </ul>
        </div>
    </div>
</body>
</html>