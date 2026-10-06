<?php 
/** @var array $dosen */

?>


<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Dosen</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <h1 class="text-center">POLITEKNIK NEGERI JEMBER</h1>
    <h2 class="text-center mb-4">Sistem Informasi Akademik</h2>

    <div style="border: 1px solid #ddd; padding: 20px; border-radius: 4px; background: white;">
        <h3>Detail Dosen</h3>
        <hr>
        <p><strong>NIDN :</strong> <?= htmlspecialchars($dosen['nidn']) ?></p>
        <p><strong>Nama :</strong> <?= htmlspecialchars($dosen['nama']) ?></p>
        <p><strong>Bidang Keahlian :</strong> <?= htmlspecialchars($dosen['bidang_keahlian']) ?></p>

        <a href="<?= BASE_URL ?>/dosen" class="btn btn-secondary" style="background-color: red; border-color: red;">Kembali</a>
    </div>
</div>
</body>
</html>